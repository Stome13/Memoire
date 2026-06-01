<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/helpers.php';
require_once __DIR__ . '/../../../backend/includes/db.php';
requireRole('pharmacie');
$currentUser = getCurrentUser();

// Récupérer la pharmacie du pharmacien
$pharmacy = null;
try {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("SELECT * FROM pharmacies WHERE pharmacien_id = ?");
    $stmt->execute([$currentUser['id']]);
    $pharmacy = $stmt->fetch();
} catch (Exception $e) {
    error_log("Erreur récupération pharmacie: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Profil - Pharmacien | PharmaGarde</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link rel="stylesheet" href="./css/variables.css" />
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

          <div class="card mb-4">
            <div class="card-body">
              <h5 class="card-title">
                <i class="fas fa-hospital-alt me-2"></i>Informations de la pharmacie
              </h5>
              <?php if ($pharmacy): ?>
                <div class="row">
                  <div class="col-md-6">
                    <p><strong>Nom:</strong> <?php echo escape($pharmacy['nom'] ?? '-'); ?></p>
                    <p><strong>Adresse:</strong> <?php echo escape($pharmacy['adresse'] ?? '-'); ?></p>
                    <p><strong>Téléphone:</strong> <?php echo escape($pharmacy['telephone'] ?? '-'); ?></p>
                  </div>
                  <div class="col-md-6">
                    <p><strong>Ville:</strong> <?php echo escape($pharmacy['ville'] ?? '-'); ?></p>
                    <p><strong>IFU:</strong> <?php echo escape($pharmacy['ifu'] ?? '-'); ?></p>
                    <p><strong>Email:</strong> <?php echo escape($pharmacy['email'] ?? '-'); ?></p>
                  </div>
                </div>
              <?php else: ?>
                <p class="text-muted"><i class="fas fa-exclamation-circle me-2"></i>Aucune pharmacie associée à ce compte.</p>
              <?php endif; ?>
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
  <script src="js/sidebar-toggle.js"></script>
</body>
</html>
