<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: /PharmaLocal/frontend/users/clients/connexion.php");
    exit;
}

$user_email = $_SESSION['user_email'] ?? '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin - PharmaGarde</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>
<body>
  <div class="d-flex">
    <!-- Sidebar -->
    <aside class="bg-dark text-white p-4" style="width: 250px; min-height: 100vh;">
      <div class="mb-5">
        <h5 class="fw-bold">Admin</h5>
        <small class="text-muted"><?php echo htmlspecialchars($user_email); ?></small>
      </div>
      <nav class="nav flex-column">
        <a class="nav-link text-white active" href="index.php">
          <i class="fas fa-chart-line me-2"></i>Tableau de bord
        </a>
        <a class="nav-link text-white-50" href="users.php">
          <i class="fas fa-users me-2"></i>Utilisateurs
        </a>
        <a class="nav-link text-white-50" href="pharmacies.php">
          <i class="fas fa-hospital me-2"></i>Pharmacies
        </a>
        <a class="nav-link text-white-50" href="medicaments.php">
          <i class="fas fa-pills me-2"></i>Médicaments
        </a>
        <a class="nav-link text-white-50" href="reservations.php">
          <i class="fas fa-handshake me-2"></i>Réservations
        </a>
        <a class="nav-link text-white-50" href="settings.php">
          <i class="fas fa-cog me-2"></i>Paramètres
        </a>
      </nav>
    </aside>

    <!-- Main Content -->
    <main class="flex-grow-1 p-4">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Tableau de Bord Admin</h1>
        <button class="btn btn-danger" onclick="logoutAdmin()">
          <i class="fas fa-sign-out-alt me-2"></i>Déconnexion
        </button>
      </div>

      <div class="row g-4 mb-4">
        <div class="col-md-3">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">
                <i class="fas fa-users me-2"></i>Utilisateurs
              </h5>
              <p class="card-text"><span class="fs-3 fw-bold">254</span></p>
              <small class="text-muted">+12 ce mois</small>
            </div>
          </div>
        </div>

        <div class="col-md-3">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">
                <i class="fas fa-hospital me-2"></i>Pharmacies
              </h5>
              <p class="card-text"><span class="fs-3 fw-bold">18</span></p>
              <small class="text-muted">Actives</small>
            </div>
          </div>
        </div>

        <div class="col-md-3">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">
                <i class="fas fa-handshake me-2"></i>Réservations
              </h5>
              <p class="card-text"><span class="fs-3 fw-bold">145</span></p>
              <small class="text-muted">Cette semaine</small>
            </div>
          </div>
        </div>

        <div class="col-md-3">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">
                <i class="fas fa-pills me-2"></i>Médicaments
              </h5>
              <p class="card-text"><span class="fs-3 fw-bold">892</span></p>
              <small class="text-muted">En stock</small>
            </div>
          </div>
        </div>
      </div>

      <div class="row g-4">
        <div class="col-md-6">
          <div class="card">
            <div class="card-header">
              <h5 class="mb-0">Activité Récente</h5>
            </div>
            <div class="card-body">
              <ul class="list-unstyled">
                <li class="mb-2"><strong>2024-01-16 14:30</strong> - Nouvel utilisateur inscrit: Jean Dupont</li>
                <li class="mb-2"><strong>2024-01-16 12:15</strong> - Pharmacie Centrale offline</li>
                <li class="mb-2"><strong>2024-01-16 10:45</strong> - 5 réservations confirmées</li>
                <li class="mb-2"><strong>2024-01-15 18:20</strong> - Mise à jour de stock pour Paracétamol</li>
              </ul>
            </div>
          </div>
        </div>

        <div class="col-md-6">
          <div class="card">
            <div class="card-header">
              <h5 class="mb-0">Statistiques</h5>
            </div>
            <div class="card-body">
              <div class="mb-3">
                <div class="d-flex justify-content-between mb-1">
                  <small>Utilisateurs actifs</small>
                  <small>78%</small>
                </div>
                <div class="progress">
                  <div class="progress-bar" style="width: 78%"></div>
                </div>
              </div>
              <div class="mb-3">
                <div class="d-flex justify-content-between mb-1">
                  <small>Pharmacies en ligne</small>
                  <small>94%</small>
                </div>
                <div class="progress">
                  <div class="progress-bar bg-success" style="width: 94%"></div>
                </div>
              </div>
              <div class="mb-3">
                <div class="d-flex justify-content-between mb-1">
                  <small>Réservations complétées</small>
                  <small>92%</small>
                </div>
                <div class="progress">
                  <div class="progress-bar bg-info" style="width: 92%"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    function logoutAdmin() {
      if (confirm('Êtes-vous sûr de vouloir vous déconnecter?')) {
        localStorage.removeItem('pharmaLocal_currentUser');
        fetch('/PharmaLocal/backend/api/auth.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          credentials: 'include',
          body: JSON.stringify({ action: 'logout' })
        }).catch(err => console.log('API logout failed:', err));
        
        window.location.href = '/PharmaLocal/frontend/users/clients/connexion.php';
      }
    }
  </script>
</body>
</html>
