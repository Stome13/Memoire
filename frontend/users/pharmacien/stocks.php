<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/helpers.php';
requireRole('pharmacie');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Gestion des stocks - Pharmacien | PharmaGarde</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link rel="stylesheet" href="../css/variables.css" />
  <link rel="stylesheet" href="css/dashboard-styles.css" />
</head>
<body>
  <?php include __DIR__ . '/../../includes/nav-pharmacien.php'; ?>

      <div class="dashboard-header">
        <h1 class="page-title">
          <i class="fas fa-warehouse me-2"></i>Gestion des stocks
        </h1>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#importStockModal">
          <i class="fas fa-upload me-2"></i>Importer CSV
        </button>
      </div>

      <div class="alert alert-warning mt-4">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <strong>Alertes stock faible:</strong> 3 médicaments sous le seuil minimum
      </div>

      <div class="table-responsive">
        <table class="table table-striped">
          <thead>
            <tr>
              <th>Médicament</th>
              <th>Stock actuel</th>
              <th>Stock minimum</th>
              <th>Statut</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr><td colspan="5" class="text-center">Chargement des stocks...</td></tr>
          </tbody>
        </table>
      </div>
    </main>
  </div>

  <!-- Modal Import CSV -->
  <div class="modal fade" id="importStockModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Importer les stocks (CSV)</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form id="importStockForm">
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label">Fichier CSV</label>
              <input type="file" class="form-control" accept=".csv" required />
            </div>
            <small class="text-muted">Format: nom_medicament,quantite,prix</small>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
            <button type="submit" class="btn btn-primary">Importer</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="js/stocks.js"></script>
</body>
</html>
