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
  <title>Médicaments - Pharmacien | PharmaGarde</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link rel="stylesheet" href="../css/variables.css" />
  <link rel="stylesheet" href="../css/styles.css" />
  <link rel="stylesheet" href="css/dashboard-styles.css" />
</head>
<body>
  <?php include __DIR__ . '/../../includes/nav-pharmacien.php'; ?>

      <div class="dashboard-header">
        <h1 class="page-title">
          <i class="fas fa-pills me-2"></i>Médicaments
        </h1>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMedicineModal">
          <i class="fas fa-plus me-2"></i>Ajouter un médicament
        </button>
      </div>

      <div class="table-responsive mt-4">
        <table class="table table-striped" id="medicinesTable">
          <thead>
            <tr>
              <th>Nom</th>
              <th>Dosage</th>
              <th>Stock</th>
              <th>Prix</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="medicinesTableBody">
            <tr><td colspan="5" class="text-center">Chargement des médicaments...</td></tr>
          </tbody>
        </table>
      </div>
    </main>
  </div>

  <!-- Modal Ajouter Médicament -->
  <div class="modal fade" id="addMedicineModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Ajouter un médicament</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form id="addMedicineForm">
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label">Nom</label>
              <input type="text" class="form-control" name="nom" required />
            </div>
            <div class="mb-3">
              <label class="form-label">Dosage</label>
              <input type="text" class="form-control" name="dosage" />
            </div>
            <div class="mb-3">
              <label class="form-label">Catégorie</label>
              <input type="text" class="form-control" name="categorie" />
            </div>
            <div class="mb-3">
              <label class="form-label">Prix</label>
              <input type="number" class="form-control" name="prix" step="0.01" />
            </div>
            <div class="mb-3">
              <label class="form-label">Description</label>
              <textarea class="form-control" name="description" rows="3"></textarea>
            </div>
            <div class="mb-3">
              <label class="form-label">Quantité en stock</label>
              <input type="number" class="form-control" name="quantite" value="0" min="0" />
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
            <button type="submit" class="btn btn-primary">Enregistrer</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Charger les médicaments
    async function loadMedicines() {
      try {
        const response = await fetch('/PharmaLocal/backend/api/medicaments.php?action=list');
        const result = await response.json();
        
        if (result.success) {
          const tbody = document.getElementById('medicinesTableBody');
          
          if (result.data.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" class="text-center">Aucun médicament trouvé</td></tr>';
            return;
          }

          tbody.innerHTML = result.data.map(med => `
            <tr>
              <td>${med.nom}</td>
              <td>${med.dosage || '-'}</td>
              <td>
                <span class="badge bg-${med.quantite > 0 ? 'success' : 'danger'}">
                  ${med.quantite ?? 0}
                </span>
              </td>
              <td>${parseFloat(med.prix).toFixed(2)} FCFA</td>
              <td>
                <button class="btn btn-sm btn-primary">Éditer</button>
              </td>
            </tr>
          `).join('');
        } else {
          document.getElementById('medicinesTableBody').innerHTML = '<tr><td colspan="5" class="text-center text-danger">Erreur: ' + result.error + '</td></tr>';
        }
      } catch (error) {
        console.error('Erreur chargement:', error);
        document.getElementById('medicinesTableBody').innerHTML = '<tr><td colspan="5" class="text-center text-danger">Erreur de connexion</td></tr>';
      }
    }

    // Ajouter un médicament
    async function handleAddMedicine(event) {
      event.preventDefault();
      
      const form = document.getElementById('addMedicineForm');
      const formData = new FormData(form);
      formData.append('action', 'create');

      try {
        const response = await fetch('/PharmaLocal/backend/api/medicaments.php', {
          method: 'POST',
          body: formData
        });
        const result = await response.json();

        if (result.success) {
          // Fermer le modal
          const modal = bootstrap.Modal.getInstance(document.getElementById('addMedicineModal'));
          if (modal) modal.hide();
          
          // Réinitialiser le formulaire
          form.reset();
          
          // Recharger les médicaments
          loadMedicines();
          
          // Afficher message de succès
          showAlert('success', result.message || 'Médicament ajouté avec succès');
        } else {
          showAlert('danger', result.error || 'Erreur lors de l\'ajout');
        }
      } catch (error) {
        console.error('Erreur:', error);
        showAlert('danger', 'Erreur de connexion: ' + error.message);
      }
    }

    // Afficher une alerte
    function showAlert(type, message) {
      const alertHTML = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert" style="position: fixed; top: 20px; right: 20px; z-index: 9999; max-width: 400px;">
          ${message}
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      `;
      document.body.insertAdjacentHTML('beforeend', alertHTML);
      
      setTimeout(() => {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(a => a.remove());
      }, 5000);
    }

    // Initialiser au chargement
    document.addEventListener('DOMContentLoaded', () => {
      loadMedicines();
      
      // Gérer la soumission du formulaire
      const form = document.getElementById('addMedicineForm');
      if (form) {
        form.addEventListener('submit', handleAddMedicine);
      }
    });
  </script>
