<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/helpers.php';
requireRole('pharmacie');
$currentUser = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Tableau de Bord - Pharmacien | PharmaGarde</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link rel="stylesheet" href="../css/variables.css" />
  <link rel="stylesheet" href="../css/styles.css" />
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
            <h3 id="total-medicines">0</h3>
            <p class="stat-label">Médicaments</p>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-icon bg-warning">
            <i class="fas fa-shopping-cart"></i>
          </div>
          <div class="stat-info">
            <h3 id="pending-reservations">0</h3>
            <p class="stat-label">Réservations en attente</p>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-icon bg-danger">
            <i class="fas fa-exclamation-triangle"></i>
          </div>
          <div class="stat-info">
            <h3 id="low-stock-alerts">0</h3>
            <p class="stat-label">Alertes stock faible</p>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-icon bg-success">
            <i class="fas fa-check-circle"></i>
          </div>
          <div class="stat-info">
            <h3 id="revenue-today">0</h3>
            <p class="stat-label">Revenu jour (FCFA)</p>
          </div>
        </div>
      </div>

      <!-- Recent Reservations -->
      <section class="mt-5">
        <h2>Réservations récentes</h2>
        <div class="table-responsive">
          <table class="table table-striped" id="recentReservations">
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
              <tr><td colspan="5" class="text-center">Chargement...</td></tr>
            </tbody>
          </table>
        </div>
      </section>

    </main>
  </div>
</div>
</body>
</html>
    </main>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="js/dashboard.js"></script>
</body>
</html>
