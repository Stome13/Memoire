<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/helpers.php';

// Vérifier que c'est un admin
requireRole('admin');

$currentUser = getCurrentUser();

// Récupérer les stats
$stats = [
    'users' => 0,
    'pharmacies' => 0,
    'reservations' => 0
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Tableau de bord admin - PharmaGarde</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="css/admin-styles.css" />
</head>
<body data-admin-page="dashboard">
  <?php include __DIR__ . '/../../includes/nav-admin.php'; ?>

  <main class="container">
    <div class="page-header d-flex justify-content-between align-items-center">
      <div>
        <h1>Tableau de bord admin</h1>
        <p>Vue d'ensemble des performances du système.</p>
      </div>
      <a class="btn btn-primary" href="pharmacies.php">Ajouter une pharmacie</a>
    </div>

    <div class="row g-4">
      <div class="col-md-4">
        <div class="card">
          <div class="card-body text-center">
            <h5 class="card-title">Utilisateurs</h5>
            <p class="card-text text-muted">
              <span id="dashboardUsersCount" style="font-size: 1.8rem; color: var(--primary); font-weight: 700;"><?php echo $stats['users']; ?></span>
            </p>
            <p class="text-muted mb-0">utilisateurs inscrits</p>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card">
          <div class="card-body text-center">
            <h5 class="card-title">Pharmacies</h5>
            <p class="card-text text-muted">
              <span id="dashboardPharmaciesCount" style="font-size: 1.8rem; color: var(--primary); font-weight: 700;"><?php echo $stats['pharmacies']; ?></span>
            </p>
            <p class="text-muted mb-0">pharmacies actives</p>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card">
          <div class="card-body text-center">
            <h5 class="card-title">Réservations</h5>
            <p class="card-text text-muted">
              <span id="dashboardReservationsCount" style="font-size: 1.8rem; color: var(--primary); font-weight: 700;"><?php echo $stats['reservations']; ?></span>
            </p>
            <p class="text-muted mb-0">réservations en attente</p>
          </div>
        </div>
      </div>
    </div>

    <section class="mt-5">
      <h2>Activité récente</h2>
      <div class="content-wrapper">
        <div class="table-responsive">
          <table class="table">
            <thead>
              <tr>
                <th>Date</th>
                <th>Événement</th>
                <th>Détails</th>
              </tr>
            </thead>
            <tbody>
              <tr><td><?php echo date('d/m/Y'); ?></td><td>Système actif</td><td>PharmaLocal en fonctionnement normal</td></tr>
            </tbody>
          </table>
        </div>
      </div>
    </section>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="js/admin.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Charger les stats depuis l'API
      loadDashboardStats();
    });

    function loadDashboardStats() {
      fetch('/PharmaLocal/backend/api/users.php?action=stats', {
        credentials: 'include'
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          document.getElementById('dashboardUsersCount').textContent = data.users || 0;
          document.getElementById('dashboardPharmaciesCount').textContent = data.pharmacies || 0;
          document.getElementById('dashboardReservationsCount').textContent = data.reservations || 0;
        }
      })
      .catch(error => console.error('Erreur:', error));
    }
  </script>
</body>
</html>
