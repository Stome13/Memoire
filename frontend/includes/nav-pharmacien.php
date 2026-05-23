<?php
// Navigation pour les pages Pharmaciens
if (!function_exists('getCurrentUser')) {
    require_once __DIR__ . '/helpers.php';
}
if (!class_exists('Database')) {
    require_once __DIR__ . '/../../backend/includes/db.php';
}

$currentPage = basename($_SERVER['PHP_SELF'], '.php');
$currentUser = $currentUser ?? getCurrentUser();

// Récupérer la pharmacie du pharmacien si pas déjà disponible
$pharmacy = $pharmacy ?? null;
if (!$pharmacy && $currentUser) {
    try {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM pharmacies WHERE pharmacien_id = ?");
        $stmt->execute([$currentUser['id']]);
        $pharmacy = $stmt->fetch();
    } catch (Exception $e) {
        error_log("Erreur récupération pharmacie: " . $e->getMessage());
    }
}
?>
<!-- Navigation Top -->
<nav class="navbar navbar-expand-lg navbar-light pharmacien-navbar sticky-top">
  <div class="container">
    <a class="navbar-brand" href="../clients/index.php">
      <img src="../../images/logo.png" alt="PharmaGarde" class="logo-nav me-2" style="height: 40px; display: inline-block;">
      PharmaGarde
    </a>
    
    <!-- Menu Toggle Button pour mobile - À droite -->
    <button class="btn btn-outline-primary d-lg-none ms-auto" type="button" data-bs-toggle="offcanvas" data-bs-target="#pharmacienSidebar" aria-controls="pharmacienSidebar" title="Menu">
      <i class="fas fa-bars"></i>
    </button>
    
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <a class="nav-link" href="profil.php">
            <i class="fas fa-user-circle me-1"></i>Profil
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="?action=logout">
            <i class="fas fa-sign-out-alt me-1"></i>Déconnexion
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<!-- Offcanvas Sidebar pour mobile -->
<div class="offcanvas offcanvas-start" tabindex="-1" id="pharmacienSidebar" aria-labelledby="pharmacienSidebarLabel">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title" id="pharmacienSidebarLabel">
      <i class="fas fa-pills me-2"></i>Menu
    </h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body p-0">
    <!-- Profile Section in Offcanvas -->
    <div class="sidebar-profile">
      <div class="profile-avatar">
        <i class="fas fa-user-md"></i>
      </div>
      <div class="profile-info">
        <h6><?php echo $currentUser['prenom'] ?? 'Pharmacien'; ?></h6>
        <small><?php echo $currentUser['nom'] ?? 'Nom'; ?></small>
        <?php if ($pharmacy): ?>
          <div style="margin-top: 8px; padding-top: 8px; border-top: 1px solid rgba(255,255,255,0.2); font-size: 0.8rem; color: rgba(255,255,255,0.8);">
            <i class="fas fa-hospital-alt me-1"></i><?php echo htmlspecialchars($pharmacy['nom']); ?>
          </div>
        <?php endif; ?>
      </div>
    </div>
    
    <nav class="sidebar-nav-mobile">
      <div class="nav-section">
        <span class="section-title">NAVIGATION</span>
        
        <a href="dashboard.php" class="sidebar-link <?php echo $currentPage === 'dashboard' ? 'active' : ''; ?>" data-page="dashboard">
          <div class="link-icon">
            <i class="fas fa-chart-line"></i>
          </div>
          <span class="link-text">Tableau de bord</span>
        </a>
        
        <a href="medicaments.php" class="sidebar-link <?php echo $currentPage === 'medicaments' ? 'active' : ''; ?>" data-page="medicaments">
          <div class="link-icon">
            <i class="fas fa-pills"></i>
          </div>
          <span class="link-text">Médicaments</span>
        </a>
        
        <a href="stocks.php" class="sidebar-link <?php echo $currentPage === 'stocks' ? 'active' : ''; ?>" data-page="stocks">
          <div class="link-icon">
            <i class="fas fa-warehouse"></i>
          </div>
          <span class="link-text">Stocks</span>
        </a>
      </div>

      <div class="nav-section">
        <span class="section-title">GESTION</span>
        
        <a href="reservations.php" class="sidebar-link <?php echo $currentPage === 'reservations' ? 'active' : ''; ?>" data-page="reservations">
          <div class="link-icon">
            <i class="fas fa-handshake"></i>
          </div>
          <span class="link-text">Réservations</span>
        </a>
        
        <a href="garde.php" class="sidebar-link <?php echo $currentPage === 'garde' ? 'active' : ''; ?>" data-page="garde">
          <div class="link-icon">
            <i class="fas fa-shield-alt"></i>
          </div>
          <span class="link-text">Pharmacies de garde</span>
        </a>
      </div>

      <div class="nav-section">
        <span class="section-title">COMPTE</span>
        
        <a href="profil.php" class="sidebar-link <?php echo $currentPage === 'profil' ? 'active' : ''; ?>" data-page="profil">
          <div class="link-icon">
            <i class="fas fa-cog"></i>
          </div>
          <span class="link-text">Paramètres</span>
        </a>
        
        <div class="nav-divider"></div>
        
        <a href="profil.php" class="sidebar-link">
          <div class="link-icon">
            <i class="fas fa-user-circle"></i>
          </div>
          <span class="link-text">Profil</span>
        </a>
        
        <a href="?action=logout" class="sidebar-link logout-link">
          <div class="link-icon">
            <i class="fas fa-sign-out-alt"></i>
          </div>
          <span class="link-text">Déconnexion</span>
        </a>
      </div>
    </nav>
  </div>
</div>

<!-- Sidebar & Main Content (desktop uniquement) -->
<div class="dashboard-container">
  <!-- Sidebar Navigation (affichée uniquement sur desktop) -->
  <aside class="sidebar d-none d-lg-block">
    <!-- Profile Section -->
    <div class="sidebar-profile">
      <div class="profile-avatar">
        <i class="fas fa-user-md"></i>
      </div>
      <div class="profile-info">
        <h6><?php echo $currentUser['prenom'] ?? 'Pharmacien'; ?></h6>
        <small><?php echo $currentUser['nom'] ?? 'Nom'; ?></small>
        <?php if ($pharmacy): ?>
          <div style="margin-top: 8px; padding-top: 8px; border-top: 1px solid rgba(255,255,255,0.2); font-size: 0.8rem; color: rgba(21, 20, 20, 0.8);">
            <i class="fas fa-hospital-alt me-1"></i><?php echo htmlspecialchars($pharmacy['nom']); ?>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Navigation Links -->
    <nav class="sidebar-nav">
      <div class="nav-section">
        <span class="section-title">NAVIGATION</span>
        
        <a href="dashboard.php" class="sidebar-link <?php echo $currentPage === 'dashboard' ? 'active' : ''; ?>" data-page="dashboard">
          <div class="link-icon">
            <i class="fas fa-chart-line"></i>
          </div>
          <span class="link-text">Tableau de bord</span>
        </a>
        
        <a href="medicaments.php" class="sidebar-link <?php echo $currentPage === 'medicaments' ? 'active' : ''; ?>" data-page="medicaments">
          <div class="link-icon">
            <i class="fas fa-pills"></i>
          </div>
          <span class="link-text">Médicaments</span>
        </a>
        
        <a href="stocks.php" class="sidebar-link <?php echo $currentPage === 'stocks' ? 'active' : ''; ?>" data-page="stocks">
          <div class="link-icon">
            <i class="fas fa-warehouse"></i>
          </div>
          <span class="link-text">Stocks</span>
        </a>
      </div>

      <div class="nav-section">
        <span class="section-title">GESTION</span>
        
        <a href="reservations.php" class="sidebar-link <?php echo $currentPage === 'reservations' ? 'active' : ''; ?>" data-page="reservations">
          <div class="link-icon">
            <i class="fas fa-handshake"></i>
          </div>
          <span class="link-text">Réservations</span>
        </a>
        
        <a href="garde.php" class="sidebar-link <?php echo $currentPage === 'garde' ? 'active' : ''; ?>" data-page="garde">
          <div class="link-icon">
            <i class="fas fa-shield-alt"></i>
          </div>
          <span class="link-text">Pharmacies garde</span>
        </a>
      </div>

      <div class="nav-section">
        <span class="section-title">COMPTE</span>
        
        <a href="profil.php" class="sidebar-link <?php echo $currentPage === 'profil' ? 'active' : ''; ?>" data-page="profil">
          <div class="link-icon">
            <i class="fas fa-cog"></i>
          </div>
          <span class="link-text">Paramètres</span>
        </a>
      </div>
    </nav>

    <!-- Sidebar Footer -->
    <div class="sidebar-footer">
      <a href="?action=logout" class="logout-btn">
        <i class="fas fa-sign-out-alt me-2"></i>
        Déconnexion
      </a>
    </div>
  </aside>

  <!-- Main Content Area -->
  <main class="main-content">