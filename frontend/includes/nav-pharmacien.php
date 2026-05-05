<?php
// Navigation pour les pages Pharmaciens
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
$currentUser = $currentUser ?? null; // Assurer que $currentUser est défini
?>
<!-- Navigation Top -->
<nav class="navbar navbar-expand-lg navbar-light pharmacien-navbar">
  <div class="container-fluid">
    <a class="navbar-brand" href="../clients/index.php">
      <i class="fas fa-hospital me-2"></i>PharmaGarde
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <a class="nav-link" href="profil.php">
            <i class="fas fa-user-circle me-1"></i>Profil
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link btn" href="?action=logout">
            <i class="fas fa-sign-out-alt me-1"></i>Déconnexion
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<!-- Sidebar & Main Content -->
<div class="dashboard-container">
  <!-- Sidebar Navigation -->
  <aside class="sidebar">
    <div class="sidebar-brand">
      <i class="fas fa-pills me-2"></i>
      <span>Gestion Pharmacien</span>
    </div>

    <nav class="sidebar-nav">
      <a href="dashboard.php" class="sidebar-link <?php echo $currentPage === 'dashboard' ? 'active' : ''; ?>" data-page="dashboard">
        <i class="fas fa-chart-line me-2"></i>
        <span>Tableau de bord</span>
      </a>
      <a href="medicaments.php" class="sidebar-link <?php echo $currentPage === 'medicaments' ? 'active' : ''; ?>" data-page="medicaments">
        <i class="fas fa-pills me-2"></i>
        <span>Médicaments</span>
      </a>
      <a href="stocks.php" class="sidebar-link <?php echo $currentPage === 'stocks' ? 'active' : ''; ?>" data-page="stocks">
        <i class="fas fa-warehouse me-2"></i>
        <span>Gestion des stocks</span>
      </a>
      <a href="reservations.php" class="sidebar-link <?php echo $currentPage === 'reservations' ? 'active' : ''; ?>" data-page="reservations">
        <i class="fas fa-handshake me-2"></i>
        <span>Réservations</span>
      </a>
      <a href="garde.php" class="sidebar-link <?php echo $currentPage === 'garde' ? 'active' : ''; ?>" data-page="garde">
        <i class="fas fa-shield-alt me-2"></i>
        <span>Pharmacies de garde</span>
      </a>
      <a href="profil.php" class="sidebar-link <?php echo $currentPage === 'profil' ? 'active' : ''; ?>" data-page="profil">
        <i class="fas fa-cog me-2"></i>
        <span>Profil & Paramètres</span>
      </a>
    </nav>
  </aside>

  <!-- Main Content Area -->
  <main class="main-content">