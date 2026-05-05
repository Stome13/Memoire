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
  <title>Gestion des pharmacies - PharmaLocal</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="css/admin-styles.css" />
</head>
<body data-admin-page="pharmacies">
  <?php include __DIR__ . '/../../includes/nav-admin.php'; ?>

  <main class="container">
    <div class="page-header d-flex justify-content-between align-items-center">
      <div>
        <h1>Gestion des pharmacies</h1>
        <p>Liste et enregistrement des pharmacies partenaires.</p>
      </div>
      <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPharmacyModal">Ajouter une pharmacie</button>
    </div>

    <div class="content-wrapper">
      <div class="table-responsive">
        <table class="table">
          <thead>
            <tr>
              <th>Nom</th>
              <th>Adresse</th>
              <th>Ville</th>
              <th>Téléphone</th>
              <th>Pharmacien</th>
              <th>Statut</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="pharmaciesTableBody">
            <tr><td colspan="7" class="text-center">Chargement des pharmacies...</td></tr>
          </tbody>
        </table>
      </div>
    </div>
  </main>

  <!-- Modal Ajouter Pharmacie -->
  <div class="modal fade" id="addPharmacyModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Ajouter une pharmacie</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form id="addPharmacyForm">
          <div class="modal-body">
            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="pharma-nom" class="form-label">Nom <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="pharma-nom" name="nom" required />
              </div>
              <div class="col-md-6 mb-3">
                <label for="pharma-ville" class="form-label">Ville</label>
                <input type="text" class="form-control" id="pharma-ville" name="ville" />
              </div>
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="pharma-email" class="form-label">Email</label>
                <input type="email" class="form-control" id="pharma-email" name="email" />
              </div>
              <div class="col-md-6 mb-3">
                <label for="pharma-telephone" class="form-label">Téléphone</label>
                <input type="tel" class="form-control" id="pharma-telephone" name="telephone" />
              </div>
            </div>

            <div class="mb-3">
              <label for="pharma-adresse" class="form-label">Adresse <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="pharma-adresse" name="adresse" required />
            </div>

            <hr />
            <h6 class="mb-3">Compte pharmacien</h6>
            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="pharmacien-nom" class="form-label">Nom du pharmacien <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="pharmacien-nom" name="pharmacien_nom" required />
              </div>
              <div class="col-md-6 mb-3">
                <label for="pharmacien-prenom" class="form-label">Prénom du pharmacien <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="pharmacien-prenom" name="pharmacien_prenom" required />
              </div>
            </div>
            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="pharmacien-email" class="form-label">Email pharmacien <span class="text-danger">*</span></label>
                <input type="email" class="form-control" id="pharmacien-email" name="pharmacien_email" required />
              </div>
              <div class="col-md-6 mb-3">
                <label for="pharmacien-telephone" class="form-label">Téléphone pharmacien</label>
                <input type="tel" class="form-control" id="pharmacien-telephone" name="pharmacien_telephone" />
              </div>
            </div>
            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="pharmacien-password" class="form-label">Mot de passe pharmacien <span class="text-danger">*</span></label>
                <input type="password" class="form-control" id="pharmacien-password" name="pharmacien_password" required />
              </div>
              <div class="col-md-6 mb-3">
                <label for="pharmacien-password-confirm" class="form-label">Confirmer le mot de passe <span class="text-danger">*</span></label>
                <input type="password" class="form-control" id="pharmacien-password-confirm" name="pharmacien_password_confirm" required />
              </div>
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="pharma-ouverture" class="form-label">Heure d'ouverture</label>
                <input type="time" class="form-control" id="pharma-ouverture" name="horaire_ouverture" />
              </div>
              <div class="col-md-6 mb-3">
                <label for="pharma-fermeture" class="form-label">Heure de fermeture</label>
                <input type="time" class="form-control" id="pharma-fermeture" name="horaire_fermeture" />
              </div>
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
  <script src="js/admin.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      loadPharmacies();

      // Gérer la soumission du formulaire d'ajout
      const addPharmacyForm = document.getElementById('addPharmacyForm');
      if (addPharmacyForm) {
        addPharmacyForm.addEventListener('submit', function(e) {
          e.preventDefault();
          handleAddPharmacy();
        });
      }
    });

    function loadPharmacies() {
      fetch('/PharmaLocal/backend/api/pharmacies.php?action=list', {
        credentials: 'include'
      })
      .then(response => response.json())
      .then(data => {
        const tbody = document.getElementById('pharmaciesTableBody');
        if (data.success && data.pharmacies && data.pharmacies.length > 0) {
          tbody.innerHTML = data.pharmacies.map(pharma => `
            <tr>
              <td>${pharma.nom}</td>
              <td>${pharma.adresse}</td>
              <td>${pharma.ville || '-'}</td>
              <td>${pharma.telephone || '-'}</td>
              <td>${pharma.pharmacien_nom || '-'}</td>
              <td><span class="badge bg-success">Actif</span></td>
              <td>
                <button class="btn btn-sm btn-warning" onclick="editPharmacy(${pharma.id})">Modifier</button>
                <button class="btn btn-sm btn-danger" onclick="deletePharmacy(${pharma.id})">Supprimer</button>
              </td>
            </tr>
          `).join('');
        } else {
          tbody.innerHTML = '<tr><td colspan="7" class="text-center">Aucune pharmacie trouvée</td></tr>';
        }
      })
      .catch(error => {
        console.error('Erreur:', error);
        document.getElementById('pharmaciesTableBody').innerHTML = '<tr><td colspan="7" class="text-center text-danger">Erreur de chargement</td></tr>';
      });
    }

    function handleAddPharmacy() {
      const form = document.getElementById('addPharmacyForm');
      const formData = new FormData(form);
      formData.append('action', 'create');

      fetch('/PharmaLocal/backend/api/pharmacies.php', {
        method: 'POST',
        body: formData,
        credentials: 'include'
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          // Fermer le modal
          const modal = bootstrap.Modal.getInstance(document.getElementById('addPharmacyModal'));
          if (modal) modal.hide();
          
          // Réinitialiser le formulaire
          form.reset();
          
          // Recharger les pharmacies
          loadPharmacies();
          
          // Afficher message de succès
          showAlert('success', data.message || 'Pharmacie ajoutée avec succès');
        } else {
          showAlert('danger', data.error || 'Erreur lors de l\'ajout');
        }
      })
      .catch(error => {
        console.error('Erreur:', error);
        showAlert('danger', 'Erreur de connexion: ' + error.message);
      });
    }

    function editPharmacy(id) {
      alert('Modification non implémentée');
    }

    function deletePharmacy(id) {
      if (confirm('Êtes-vous sûr de vouloir supprimer cette pharmacie ?')) {
        alert('Suppression non implémentée');
      }
    }

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
  </script>
</body>
</html>
