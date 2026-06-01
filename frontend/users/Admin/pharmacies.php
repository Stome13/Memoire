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
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
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
              <th>Numéro IFU</th>
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
                <label for="pharma-ifu" class="form-label">Numéro IFU <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="pharma-ifu" name="ifu" required />
              </div>
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="pharma-ville" class="form-label">Ville</label>
                <input type="text" class="form-control" id="pharma-ville" name="ville" />
              </div>
              <div class="col-md-6 mb-3">
                <label for="pharma-email" class="form-label">Email</label>
                <input type="email" class="form-control" id="pharma-email" name="email" />
              </div>
            </div>

            <div class="row">
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

  <!-- Modal Éditer Pharmacie -->
  <div class="modal fade" id="editPharmacyModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Modifier une pharmacie</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form id="editPharmacyForm">
          <input type="hidden" id="edit-pharma-id" name="id" />
          <div class="modal-body">
            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="edit-pharma-nom" class="form-label">Nom <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="edit-pharma-nom" name="nom" required />
              </div>
              <div class="col-md-6 mb-3">
                <label for="edit-pharma-ifu" class="form-label">Numéro IFU <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="edit-pharma-ifu" name="ifu" required />
              </div>
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="edit-pharma-ville" class="form-label">Ville</label>
                <input type="text" class="form-control" id="edit-pharma-ville" name="ville" />
              </div>
              <div class="col-md-6 mb-3">
                <label for="edit-pharma-email" class="form-label">Email</label>
                <input type="email" class="form-control" id="edit-pharma-email" name="email" />
              </div>
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="edit-pharma-telephone" class="form-label">Téléphone</label>
                <input type="tel" class="form-control" id="edit-pharma-telephone" name="telephone" />
              </div>
            </div>

            <div class="mb-3">
              <label for="edit-pharma-adresse" class="form-label">Adresse <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="edit-pharma-adresse" name="adresse" required />
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="edit-pharma-ouverture" class="form-label">Heure d'ouverture</label>
                <input type="time" class="form-control" id="edit-pharma-ouverture" name="horaire_ouverture" />
              </div>
              <div class="col-md-6 mb-3">
                <label for="edit-pharma-fermeture" class="form-label">Heure de fermeture</label>
                <input type="time" class="form-control" id="edit-pharma-fermeture" name="horaire_fermeture" />
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
            <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="/PharmaLocal/frontend/users/js/toast-helper.js"></script>
  <script src="js/admin.js"></script>
  <script>
    let allPharmacies = [];

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

      // Gérer la soumission du formulaire d'édition
      const editPharmacyForm = document.getElementById('editPharmacyForm');
      if (editPharmacyForm) {
        editPharmacyForm.addEventListener('submit', function(e) {
          e.preventDefault();
          handleEditPharmacy();
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
          allPharmacies = data.pharmacies;
          tbody.innerHTML = data.pharmacies.map(pharma => `
            <tr>
              <td>${pharma.nom}</td>
              <td>${pharma.ifu || '-'}</td>
              <td>${pharma.adresse}</td>
              <td>${pharma.ville || '-'}</td>
              <td>${pharma.telephone || '-'}</td>
              <td>${pharma.pharmacien_nom || '-'}</td>
              <td><span class="badge bg-success">Actif</span></td>
              <td>
                <div class="btn-group" role="group">
                  <button class="btn btn-sm btn-warning" onclick="editPharmacy(${pharma.id})" title="Modifier" data-bs-toggle="tooltip">
                    <i class="fas fa-edit"></i>
                  </button>
                  <button class="btn btn-sm btn-danger" onclick="deletePharmacy(${pharma.id})" title="Supprimer" data-bs-toggle="tooltip">
                    <i class="fas fa-trash"></i>
                  </button>
                </div>
              </td>
            </tr>
          `).join('');
        } else {
          tbody.innerHTML = '<tr><td colspan="8" class="text-center">Aucune pharmacie trouvée</td></tr>';
        }
      })
      .catch(error => {
        console.error('Erreur:', error);
        document.getElementById('pharmaciesTableBody').innerHTML = '<tr><td colspan="8" class="text-center text-danger">Erreur de chargement</td></tr>';
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
      const pharma = allPharmacies.find(p => p.id === id);
      if (!pharma) {
        showAlert('danger', 'Pharmacie non trouvée');
        return;
      }

      document.getElementById('edit-pharma-id').value = pharma.id;
      document.getElementById('edit-pharma-nom').value = pharma.nom || '';
      document.getElementById('edit-pharma-ifu').value = pharma.ifu || '';
      document.getElementById('edit-pharma-adresse').value = pharma.adresse || '';
      document.getElementById('edit-pharma-ville').value = pharma.ville || '';
      document.getElementById('edit-pharma-email').value = pharma.email || '';
      document.getElementById('edit-pharma-telephone').value = pharma.telephone || '';
      document.getElementById('edit-pharma-ouverture').value = pharma.horaire_ouverture || '';
      document.getElementById('edit-pharma-fermeture').value = pharma.horaire_fermeture || '';

      const modal = new bootstrap.Modal(document.getElementById('editPharmacyModal'));
      modal.show();
    }

    function deletePharmacy(id) {
      if (confirm('Êtes-vous sûr de vouloir supprimer cette pharmacie ?')) {
        showAlert('danger', 'Suppression non implémentée', 'warning');
      }
    }

    function showAlert(type, message) {
      showToast(type, message, 5000);
    }
  </script>
</body>
</html>
