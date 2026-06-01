<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/helpers.php';
require_once __DIR__ . '/../../../backend/includes/db.php';

requireRole('pharmacie');
$currentUser = getCurrentUser();

// Récupérer la pharmacie du pharmacien
$pharmacy = null;
$stats = [
    'total_medicines' => 0,
    'pending_reservations' => 0,
    'low_stock_alerts' => 0,
    'revenue_today' => 0
];
$recentReservations = [];

try {
    $db = Database::getInstance()->getConnection();
    
    // 1. Récupérer la pharmacie du pharmacien
    $stmt = $db->prepare("SELECT * FROM pharmacies WHERE pharmacien_id = ?");
    $stmt->execute([$currentUser['id']]);
    $pharmacy = $stmt->fetch();
    
    if ($pharmacy) {
        $pharmacyId = $pharmacy['id'];
        
        // 2. Total médicaments en stock
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM stocks WHERE pharmacie_id = ? AND quantite > 0");
        $stmt->execute([$pharmacyId]);
        $result = $stmt->fetch();
        $stats['total_medicines'] = $result['count'] ?? 0;
        
        // 3. Réservations en attente
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM reservations WHERE pharmacie_id = ? AND statut = 'en attente'");
        $stmt->execute([$pharmacyId]);
        $result = $stmt->fetch();
        $stats['pending_reservations'] = $result['count'] ?? 0;
        
        // 4. Stocks faibles (< 5)
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM stocks WHERE pharmacie_id = ? AND quantite > 0 AND quantite < 5");
        $stmt->execute([$pharmacyId]);
        $result = $stmt->fetch();
        $stats['low_stock_alerts'] = $result['count'] ?? 0;
        
        // 5. Réservations récentes
        $stmt = $db->prepare("
            SELECT r.*, u.nom, u.prenom, m.nom as med_nom, m.dosage
            FROM reservations r
            JOIN users u ON r.user_id = u.id
            JOIN medicaments m ON r.medicament_id = m.id
            WHERE r.pharmacie_id = ?
            ORDER BY r.date_reservation DESC
            LIMIT 10
        ");
        $stmt->execute([$pharmacyId]);
        $recentReservations = $stmt->fetchAll();
    }
} catch (Exception $e) {
    error_log("Erreur dashboard pharmacien: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Tableau de Bord - Pharmacien | PharmaGarde</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link rel="stylesheet" href="./css/variables.css" />
  <link rel="stylesheet" href="./css/styles.css" />
  <link rel="stylesheet" href="css/dashboard-styles.css" />
</head>
<body>
  <?php include __DIR__ . '/../../includes/nav-pharmacien.php'; ?>

      <!-- Dashboard Header -->
      <div class="dashboard-header">
        <h1 class="page-title">
          <i class="fas fa-chart-line me-2"></i>Tableau de Bord
        </h1>
      </div>

      <!-- Stats Cards -->
      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-icon bg-primary">
            <i class="fas fa-pills"></i>
          </div>
          <div class="stat-info">
            <h3><?php echo $stats['total_medicines']; ?></h3>
            <p class="stat-label">Médicaments</p>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-icon bg-warning">
            <i class="fas fa-shopping-cart"></i>
          </div>
          <div class="stat-info">
            <h3><?php echo $stats['pending_reservations']; ?></h3>
            <p class="stat-label">Réservations en attente</p>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-icon bg-danger">
            <i class="fas fa-exclamation-triangle"></i>
          </div>
          <div class="stat-info">
            <h3><?php echo $stats['low_stock_alerts']; ?></h3>
            <p class="stat-label">Alertes stock faible</p>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-icon bg-success">
            <i class="fas fa-check-circle"></i>
          </div>
          <div class="stat-info">
            <h3><?php echo $stats['revenue_today']; ?></h3>
            <p class="stat-label">Revenu jour (FCFA)</p>
          </div>
        </div>
      </div>

      <!-- Recent Reservations -->
      <section class="mt-5">
        <h2>Réservations récentes</h2>
        <div class="table-responsive">
          <table class="table table-striped">
            <thead>
              <tr>
                <th>Client</th>
                <th>Médicament</th>
                <th>Quantité</th>
                <th>Date</th>
                <th>Statut</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($recentReservations)): ?>
                <tr>
                  <td colspan="5" class="text-center text-muted">Aucune réservation</td>
                </tr>
              <?php else: ?>
                <?php foreach ($recentReservations as $res): ?>
                  <tr>
                    <td><?php echo escape($res['prenom'] . ' ' . $res['nom']); ?></td>
                    <td><?php echo escape($res['med_nom']); ?></td>
                    <td><?php echo $res['quantite']; ?></td>
                    <td><?php echo date('d/m/Y', strtotime($res['date_reservation'])); ?></td>
                    <td>
                      <span class="badge bg-<?php 
                        echo $res['statut'] === 'en attente' ? 'warning' : 
                             ($res['statut'] === 'confirmée' ? 'success' : 
                              ($res['statut'] === 'annulée' ? 'danger' : 'info'));
                      ?>">
                        <?php echo ucfirst($res['statut']); ?>
                      </span>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </section>

    </main>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="js/sidebar-toggle.js"></script>
  <script src="js/dashboard.js"></script>
</body>
</html>
