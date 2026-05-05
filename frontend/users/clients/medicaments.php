<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/helpers.php';

// Récupérer les données utilisateur si connecté
$currentUser = isLoggedIn() ? getCurrentUser() : null;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Medicaments - PharmaGarde</title>
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
        <h1 class="fw-bold mb-3">Rechercher un médicament</h1>
        <p class="text-muted">Vérifiez la disponibilité et les prix</p>
      </div>
    </section>

    <!-- Search Section -->
    <section class="py-5">
      <div class="container-lg">
        <div class="search-form p-5 mb-5">
          <div class="row g-3 align-items-end">
            <div class="col-md-8">
              <label for="med-search" class="form-label fw-bold">Nom du médicament</label>
              <input type="text" class="form-control form-control-lg" id="med-search" placeholder="Ex: Paracétamol, Amoxicilline..." />
            </div>
            <div class="col-md-4">
              <button class="btn btn-primary btn-lg w-100" id="search-btn-med">
                <i class="fas fa-search me-2"></i>Rechercher
              </button>
            </div>
          </div>
        </div>

        <!-- Results -->
        <div id="medicamentos-results">
          <!-- Les résultats seront générés par JavaScript -->
        </div>
      </div>
    </section>

    <!-- Popular Medicines -->
    <section class="py-5 bg-light">
      <div class="container-lg">
        <h2 class="fw-bold mb-5">Médicaments populaires</h2>
        <div class="row gy-4" id="popularMedicines">
          <!-- Les cartes seront générées par JavaScript -->
        </div>
      </div>
    </section>
  </main>

  <?php include __DIR__ . '/../../includes/footer.php'; ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="js/auth.js"></script>
  <script src="js/script.js"></script>
  <script src="js/medicaments.js"></script>
  <script src="js/reservation.js"></script>
</body>
</html>
