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
  <title>Profil - Pharmacien | PharmaGarde</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link rel="stylesheet" href="../css/variables.css" />
  <link rel="stylesheet" href="css/dashboard-styles.css" />
</head>
<body>
  <?php include __DIR__ . '/../../includes/nav-pharmacien.php'; ?>

      <div class="dashboard-header">
        <h1 class="page-title">
          <i class="fas fa-user-md me-2"></i>Profil & Paramètres
        </h1>
      </div>

      <div class="row">
        <div class="col-lg-8">
          <div class="card mb-4">
            <div class="card-body">
              <h5 class="card-title">Informations personnelles</h5>
              <p><strong>Nom:</strong> <?php echo escape($currentUser['nom']); ?></p>
              <p><strong>Prénom:</strong> <?php echo escape($currentUser['prenom']); ?></p>
              <p><strong>Email:</strong> <?php echo escape($currentUser['email']); ?></p>
              <button class="btn btn-primary btn-sm">Modifier</button>
            </div>
          </div>

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Sécurité</h5>
              <form id="changePasswordForm">
                <div class="mb-3">
                  <label class="form-label">Ancien mot de passe</label>
                  <input type="password" class="form-control" required />
                </div>
                <div class="mb-3">
                  <label class="form-label">Nouveau mot de passe</label>
                  <input type="password" class="form-control" required />
                </div>
                <div class="mb-3">
                  <label class="form-label">Confirmer mot de passe</label>
                  <input type="password" class="form-control" required />
                </div>
                <button type="submit" class="btn btn-primary">Changer le mot de passe</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
