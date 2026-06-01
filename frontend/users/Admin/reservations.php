<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/helpers.php';
require_once __DIR__ . '/../../../backend/includes/db.php';

requireRole('admin');

// Récupérer les réservations de la base de données
$reservations = [];
try {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("
        SELECT r.id, 
               CONCAT(u.prenom, ' ', u.nom) AS client_name, 
               p.nom AS pharmacy_name, 
               m.nom AS medicine_name, 
               r.quantite,
               r.statut, 
               r.date_reservation
        FROM reservations r
        LEFT JOIN users u ON r.user_id = u.id
        LEFT JOIN medicaments m ON r.medicament_id = m.id
        LEFT JOIN pharmacies p ON r.pharmacie_id = p.id
        ORDER BY r.date_reservation DESC
    ");
    $stmt->execute();
    $reservations = $stmt->fetchAll();
} catch (Exception $e) {
    error_log("Erreur lors de la récupération des réservations: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Gestion des réservations - PharmaLocal</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="css/admin-styles.css" />
</head>
<body data-admin-page="reservations">
  <?php include __DIR__ . '/../../includes/nav-admin.php'; ?>

  <main class="container">
    <div class="page-header">
      <h1>Gestion des réservations</h1>
      <p>Liste de toutes les réservations en attente et confirmées.</p>
    </div>

    <div class="content-wrapper">
      <div class="table-responsive">
        <table class="table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Client</th>
              <th>Pharmacie</th>
              <th>Médicament</th>
              <th>Quantité</th>
              <th>Date</th>
              <th>Statut</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (count($reservations) > 0): ?>
              <?php foreach ($reservations as $res): ?>
                <tr>
                  <td><?php echo htmlspecialchars($res['id']); ?></td>
                  <td><?php echo htmlspecialchars($res['client_name'] ?? 'N/A'); ?></td>
                  <td><?php echo htmlspecialchars($res['pharmacy_name'] ?? 'N/A'); ?></td>
                  <td><?php echo htmlspecialchars($res['medicine_name'] ?? 'N/A'); ?></td>
                  <td><?php echo htmlspecialchars($res['quantite'] ?? 'N/A'); ?></td>
                  <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($res['date_reservation'] ?? 'now'))); ?></td>
                  <td>
                    <span class="badge bg-<?php 
                      echo ($res['statut'] === 'confirmée' ? 'success' : 
                           ($res['statut'] === 'annulée' ? 'danger' : 'info')); 
                    ?>">
                      <?php echo ucfirst(htmlspecialchars($res['statut'] ?? 'pending')); ?>
                    </span>
                  </td>
                  <td>
                    <button class="btn btn-sm btn-warning">Modifier</button>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="8" class="text-center text-muted">Aucune réservation trouvée</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
