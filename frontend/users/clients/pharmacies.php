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
  <title>Pharmacies de Garde - PharmaGarde</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link rel="stylesheet" href="css/variables.css" />
  <link rel="stylesheet" href="css/styles.css" />
</head>
<body>
  <!-- Navigation -->
  <?php include __DIR__ . '/../../includes/nav-client.php'; ?>

  <main>
    <!-- Header Section -->
    <section class="bg-light py-5">
      <div class="container-lg">
        <h1 class="fw-bold mb-3">Toutes les pharmacies</h1>
        <p class="text-muted">Trouvez les pharmacies de garde disponibles</p>
      </div>
    </section>

    <!-- Filters -->
    <section class="py-4 border-bottom">
      <div class="container-lg">
        <div class="row g-3 align-items-end">
          <div class="col-md-4">
            <label for="city-filter" class="form-label fw-bold">Ville</label>
            <select class="form-select" id="city-filter">
              <option value="">Toutes les villes</option>
              <option value="Cotonou">Cotonou</option>
              <option value="Porto-Novo">Porto-Novo</option>
              <option value="Abomey-Calavi">Abomey-Calavi</option>
            </select>
          </div>
        </div>
      </div>
    </section>

    <!-- Pharmacies List dynamique -->
    <section class="py-5">
      <div class="container-lg">
        <div class="row gy-4">
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
                </div>
                <?php endif; ?>
                <p class="pharmacy-insurance">Mutuelle de santé, Assurance entreprise, ...</p>
                <?php if ($gardes && isset($gardes[0])): ?>
                <div class="pharmacy-detail" style="color: var(--success);">
                  <i class="fas fa-hourglass-half"></i>
                  <span>Prochaine garde: <?php echo ucfirst(strftime('%A %d/%m', strtotime($gardes[0]['date_garde']))); ?> de <?php echo substr($gardes[0]['heure_debut'],0,5); ?> à <?php echo substr($gardes[0]['heure_fin'],0,5); ?></span>
                </div>
                <?php endif; ?>
                <div class="d-flex gap-2 mt-2">
                  <button class="btn btn-sm btn-success flex-fill" data-bs-toggle="modal" data-bs-target="#gardeModal<?php echo $pharmacie['id']; ?>">
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
      </div>
    </section>
  </main>

  <?php include __DIR__ . '/../../includes/footer.php'; ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="js/auth.js"></script>
  <script src="js/script.js"></script>
  <script src="js/pharmacies.js"></script>
  <script src="js/reservation.js"></script>
</body>
</html>
