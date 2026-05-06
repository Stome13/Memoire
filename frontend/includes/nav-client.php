<?php
// Navigation pour les pages Clients
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
$currentUser = $currentUser ?? null; // Assurer que $currentUser est défini
?>
<!-- Navigation -->
<nav class="navbar navbar-expand-lg navbar-light">
  <div class="container-lg">
    <a class="navbar-brand" href="index.php"> <i class="fas fa-hospital me-2"></i> PharmaGarde</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <a class="nav-link <?php echo $currentPage === 'index' ? 'active' : ''; ?>" href="index.php">Accueil</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?php echo $currentPage === 'pharmacies' ? 'active' : ''; ?>" href="pharmacies.php">Pharmacies</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?php echo $currentPage === 'medicaments' ? 'active' : ''; ?>" href="medicaments.php">Médicaments</a>
        </li>
        <li class="nav-item">
          <a class="nav-link cart-badge" href="#" onclick="openCartModal(); return false;">
            <i class="fas fa-shopping-cart"></i>
            <span id="cart-badge" style="display: none;">0</span>
          </a>
        </li>
        <li class="nav-item" id="authNav">
          <?php if ($currentUser): ?>
            <div class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                <i class="fas fa-user-circle me-1"></i><?php echo escape($currentUser['prenom']); ?>
              </a>
              <ul class="dropdown-menu" aria-labelledby="userDropdown">
                <li><a class="dropdown-item" href="profil.php"><i class="fas fa-user me-2"></i>Mon Profil</a></li>
                <li><a class="dropdown-item" href="profil.php?tab=reservations"><i class="fas fa-calendar-check me-2"></i>Mes Réservations</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-danger" href="?action=logout"><i class="fas fa-sign-out-alt me-2"></i>Déconnexion</a></li>
              </ul>
            </div>
          <?php else: ?>
            <a class="nav-link btn btn-primary text-white ms-2" href="connexion.php">
              <i class="fas fa-sign-in-alt me-1"></i>Connexion
            </a>
          <?php endif; ?>
        </li>
      </ul>
    </div>
  </div>
</nav>

<script src="/PharmaLocal/frontend/users/clients/js/medicaments.js"></script>
<script>
  // Initialiser le badge du panier au chargement de chaque page
  document.addEventListener('DOMContentLoaded', function() {
    if (typeof updateCartBadge === 'function') {
      updateCartBadge();
    }
  });
</script>