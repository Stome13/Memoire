<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/helpers.php';
requireRole('admin');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Paramètres - PharmaLocal</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="css/admin-styles.css" />
</head>
<body>
  <?php include __DIR__ . '/../../includes/nav-admin.php'; ?>

  <main class="container">
    <div class="page-header">
      <h1>Paramètres du système</h1>
      <p>Configurez les paramètres généraux de PharmaLocal.</p>
    </div>

    <div class="row mt-5 g-4">
      <div class="col-lg-6">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Informations générales</h5>
            <form>
              <div class="mb-3">
                <label for="siteName" class="form-label">Nom du site</label>
                <input type="text" class="form-control" id="siteName" value="PharmaLocal" />
              </div>
              <div class="mb-3">
                <label for="siteUrl" class="form-label">URL du site</label>
                <input type="url" class="form-control" id="siteUrl" value="http://localhost/PharmaLocal" />
              </div>
              <button type="submit" class="btn btn-primary">Enregistrer</button>
            </form>
          </div>
        </div>
      </div>

      <div class="col-lg-6">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Email</h5>
            <form>
              <div class="mb-3">
                <label for="contactEmail" class="form-label">Email de contact</label>
                <input type="email" class="form-control" id="contactEmail" value="contact@pharmaguard.bj" />
              </div>
              <div class="mb-3">
                <label for="supportEmail" class="form-label">Email support</label>
                <input type="email" class="form-control" id="supportEmail" value="support@pharmaguard.bj" />
              </div>
              <button type="submit" class="btn btn-primary">Enregistrer</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
