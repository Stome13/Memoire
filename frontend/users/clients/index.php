<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/helpers.php';

// Locale française pour les dates
setlocale(LC_TIME, 'fr_FR.UTF-8', 'french');

// Récupérer les données utilisateur si connecté
$currentUser = isLoggedIn() ? getCurrentUser() : null;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>PharmaGarde - Localisez les pharmacies de garde</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link rel="stylesheet" href="css/variables.css" />
  <link rel="stylesheet" href="css/styles.css" />
</head>
<body>
  <!-- Navigation -->
  <?php include __DIR__ . '/../../includes/nav-client.php'; ?>

  <main>
    <!-- Hero Section -->
    <section class="hero-section-new">
      <div class="hero-bg"></div>
      <div class="container-lg position-relative" style="z-index: 2;">
        <!-- Badge Top -->
        <div class="text-center mb-4">
          <span class="hero-badge">
            <i class="fas fa-hospital-alt me-2"></i>
            Pharmacies de garde • Porto-Novo
          </span>
        </div>

        <!-- Titre -->
        <div class="text-center mb-5">
          <h1 class="hero-title">
            Trouvez votre pharmacie,<br />vérifiez vos médicaments
          </h1>
        </div>

        <!-- Description -->
        <div class="text-center mb-5">
          <p class="hero-subtitle">
            Localisez les pharmacies de garde les plus proches, vérifiez la<br />
            disponibilité de vos médicaments et réservez en quelques clics.
          </p>
        </div>

        <!-- Barre de Recherche -->
        <div class="search-container mb-5">
          <div class="search-box">
            <input 
              type="text" 
              class="search-input" 
              id="search-med" 
              placeholder="Rechercher un médicament..." 
              aria-label="Rechercher un médicament"
            />
            <button class="search-btn" type="button" id="search-btn" aria-label="Bouton rechercher">
              Rechercher
            </button>
          </div>
        </div>

        <!-- Features Bottom -->
        <div class="hero-features">
          <div class="hero-feature">
            <i class="fas fa-map-marker-alt"></i>
            <span>GPS de localisation</span>
          </div>
          <div class="hero-feature">
            <i class="fas fa-clock"></i>
            <span>Pharmacies ouvertes 24h/24</span>
          </div>
          <div class="hero-feature">
            <i class="fas fa-lock"></i>
            <span>Réservation sécurisée</span>
          </div>
        </div>
      </div>
    </section>

    <!-- Pharmacies de Garde -->
    <section class="py-5">
      <div class="container-lg">
        <div class="mb-5">
          <h2 class="fw-bold mb-1">Pharmacies de garde</h2>
          <p class="text-muted">Pharmacies ouvertes 24h/24 ce week-end</p>
        </div>
        <div class="row gy-4" id="pharmaciesList" role="region" aria-label="Pharmacies de garde">
          <?php
          require_once __DIR__ . '/../../../backend/includes/db.php';
          $db = Database::getInstance()->getConnection();
          $today = date('Y-m-d');
          $stmt = $db->query('SELECT * FROM pharmacies');
          $pharmacies = $stmt->fetchAll();
          foreach ($pharmacies as $pharmacie):
            // Vérifier si la pharmacie est de garde aujourd'hui
            $stmtGarde = $db->prepare('SELECT * FROM gardes WHERE pharmacie_id = ? AND date_garde = ?');
            $stmtGarde->execute([$pharmacie['id'], $today]);
            $gardeToday = $stmtGarde->fetch();
            // Prochaines gardes
            $stmtNextGarde = $db->prepare('SELECT * FROM gardes WHERE pharmacie_id = ? AND date_garde >= ? ORDER BY date_garde ASC, heure_debut ASC');
            $stmtNextGarde->execute([$pharmacie['id'], $today]);
            $gardes = $stmtNextGarde->fetchAll();
          ?>
          <div class="col-md-6 col-lg-4">
            <div class="pharmacy-card">
              <?php if ($gardeToday): ?>
              <span class="pharmacy-badge">
                <i class="fas fa-shield-alt"></i>De Garde
              </span>
              <?php endif; ?>
              <div class="pharmacy-info">
                <h5 class="pharmacy-name"><?php echo htmlspecialchars($pharmacie['nom']); ?></h5>
                <div class="pharmacy-detail">
                  <i class="fas fa-map-marker-alt"></i>
                  <span><?php echo htmlspecialchars($pharmacie['adresse']); ?></span>
                </div>
                <div class="pharmacy-detail">
                  <i class="fas fa-phone"></i>
                  <a href="tel:<?php echo htmlspecialchars($pharmacie['telephone']); ?>"><?php echo htmlspecialchars($pharmacie['telephone']); ?></a>
                </div>
                <?php if ($gardeToday): ?>
                <div class="pharmacy-detail">
                  <i class="fas fa-clock"></i>
                  <span>24h/24 (Garde)</span>
                </div>                <div class="pharmacy-rating">
                  <div class="stars">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star-half-alt"></i>
                  </div>
                  <span class="score">4.5</span>
                  <span class="text-muted ms-3" style="font-size: 0.85rem;">0.8 km</span>
                </div>
                <?php endif; ?>

                <p class="pharmacy-insurance">Mutuelle de santé, Assurance entreprise, ...</p>
                <?php if ($gardes && isset($gardes[0])): ?>
                <div class="pharmacy-detail" style="color: var(--danger);">
                  <i class="fas fa-hourglass-half"></i>
                  <span>Prochaine garde: <?php echo ucfirst(strftime('%A %d/%m', strtotime($gardes[0]['date_garde']))); ?> jusqu'à <?php echo substr($gardes[0]['heure_fin'],0,5); ?></span>
                </div>
                <?php endif; ?>
                <div class="pharmacy-actions">
                  <a href="pharmacies.php" class="btn btn-primary">Voir détails</a>
                  <a href="#" class="btn btn-outline-primary">Produits</a>
                </div>
                <div class="d-flex gap-2 mt-2">
                  <button class="btn btn-sm btn-outline-danger flex-fill" data-bs-toggle="modal" data-bs-target="#gardeModal<?php echo $pharmacie['id']; ?>">
                    <i class="fas fa-calendar-alt me-1"></i>Voir jours de garde
                  </button>
                  <a href="https://www.google.com/maps/search/<?php echo urlencode($pharmacie['nom'] . ' ' . $pharmacie['adresse']); ?>" target="_blank" class="btn btn-sm btn-outline-secondary flex-fill">
                    <i class="fas fa-map-marked-alt me-1"></i>Localisation
                  </a>
                </div>
              </div>
            </div>
          </div>

          <!-- Modal Jours de Garde -->
          <div class="modal fade" id="gardeModal<?php echo $pharmacie['id']; ?>" tabindex="-1" aria-labelledby="gardeModalLabel<?php echo $pharmacie['id']; ?>" aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="gardeModalLabel<?php echo $pharmacie['id']; ?>">Jours de garde - <?php echo htmlspecialchars($pharmacie['nom']); ?></h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <?php if ($gardes): ?>
                    <ul class="list-group">
                      <?php foreach ($gardes as $garde): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                          <span><strong><?php echo ucfirst(strftime('%A %d/%m/%Y', strtotime($garde['date_garde']))); ?></strong></span>
                          <span class="badge bg-primary rounded-pill"><?php echo substr($garde['heure_debut'],0,5); ?> - <?php echo substr($garde['heure_fin'],0,5); ?></span>
                        </li>
                      <?php endforeach; ?>
                    </ul>
                  <?php else: ?>
                    <p class="text-muted text-center">Aucune garde à venir.</p>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
        <div class="text-center mt-5">
          <a href="pharmacies.php" class="btn btn-outline-primary btn-lg">Voir toutes les pharmacies</a>
        </div>
      </div>
    </section>

    <!-- Features Section -->
    <section class="feature-section">
      <div class="container-lg">
        <div class="text-center mb-5">
          <h2 class="fw-bold mb-3">Tout ce dont vous avez besoin</h2>
          <p class="text-muted fs-5">Un système complet pour les patients et les pharmaciens</p>
        </div>
        <div class="row gy-4">
          <div class="col-md-6 col-lg-4">
            <div class="feature-card">
              <div class="feature-icon">
                <i class="fas fa-map-location-dot"></i>
              </div>
              <h5>Localisation GPS</h5>
              <p>Trouvez les pharmacies de garde les plus proches de votre position en temps réel.</p>
            </div>
          </div>
          <div class="col-md-6 col-lg-4">
            <div class="feature-card">
              <div class="feature-icon">
                <i class="fas fa-pills"></i>
              </div>
              <h5>Recherche de médicaments</h5>
              <p>Vérifiez la disponibilité et le prix des médicaments avant de vous déplacer.</p>
            </div>
          </div>
          <div class="col-md-6 col-lg-4">
            <div class="feature-card">
              <div class="feature-icon">
                <i class="fas fa-calendar-check"></i>
              </div>
              <h5>Réservation en ligne</h5>
              <p>Réservez vos médicaments et récupérez-les directement à la pharmacie.</p>
            </div>
          </div>
          <div class="col-md-6 col-lg-4">
            <div class="feature-card">
              <div class="feature-icon">
                <i class="fas fa-bell"></i>
              </div>
              <h5>Notifications</h5>
              <p>Recevez des alertes sur vos réservations et les pharmacies de garde.</p>
            </div>
          </div>
          <div class="col-md-6 col-lg-4">
            <div class="feature-card">
              <div class="feature-icon">
                <i class="fas fa-boxes"></i>
              </div>
              <h5>Gestion de stock</h5>
              <p>Les pharmaciens gèrent leurs stocks et détectent les ruptures automatiquement.</p>
            </div>
          </div>
          <div class="col-md-6 col-lg-4">
            <div class="feature-card">
              <div class="feature-icon">
                <i class="fas fa-credit-card"></i>
              </div>
              <h5>Paiement sécurisé</h5>
              <p>Payez en ligne en toute sécurité ou réglez sur place.</p>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>

  <?php include __DIR__ . '/../../includes/footer.php'; ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="js/auth.js"></script>
  <script src="js/script.js"></script>
  <script src="js/pharmacies.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      // Les pharmacies seront chargées via JS depuis l'API
      <?php if ($currentUser): ?>
        localStorage.setItem('pharmaGarde_currentUser', <?php echo json_encode($currentUser); ?>);
      <?php endif; ?>
    });
  </script>
</body>
</html>
