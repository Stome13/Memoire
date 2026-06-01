<?php
// Navigation pour les pages Admin
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>
<nav class="navbar navbar-expand-lg">
  <div class="container-fluid">
    <a class="navbar-brand" href="dashboard.php"><img src="../../images/logo.png" alt="PharmaGarde" class="logo-nav me-2" style="height: 40px; display: inline-block;"> PharmaGarde Admin</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNav" aria-controls="adminNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-center" id="adminNav">
      <ul class="navbar-nav mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link <?php echo $currentPage === 'dashboard' ? 'active' : ''; ?>" href="dashboard.php">Tableau de bord</a></li>
        <li class="nav-item"><a class="nav-link <?php echo $currentPage === 'users' ? 'active' : ''; ?>" href="users.php">Utilisateurs</a></li>
        <li class="nav-item"><a class="nav-link <?php echo $currentPage === 'pharmacies' ? 'active' : ''; ?>" href="pharmacies.php">Pharmacies</a></li>
        <li class="nav-item"><a class="nav-link <?php echo $currentPage === 'medicaments' ? 'active' : ''; ?>" href="medicaments.php">Médicaments</a></li>
        <li class="nav-item"><a class="nav-link <?php echo $currentPage === 'reservations' ? 'active' : ''; ?>" href="reservations.php">Réservations</a></li>
        <li class="nav-item"><a class="nav-link <?php echo $currentPage === 'settings' ? 'active' : ''; ?>" href="settings.php">Paramètres</a></li>
        <li class="nav-item"><a class="nav-link <?php echo $currentPage === 'profile' ? 'active' : ''; ?>" href="profile.php">Profil</a></li>
      </ul>
    </div>
    <div class="navbar-nav">
      <a class="btn btn-outline-primary logout-btn" href="#" onclick="showLogoutModal(); return false;">Déconnexion</a>
    </div>
  </div>
</nav>

<script src="../Admin/js/logout.js"></script>

<!-- Modal de confirmation de déconnexion -->
<div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="logoutModalLabel">Confirmer la déconnexion</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>Êtes-vous sûr de vouloir vous déconnecter ?</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
        <a href="?action=logout" class="btn btn-danger">Se déconnecter</a>
      </div>
    </div>
  </div>
</div>