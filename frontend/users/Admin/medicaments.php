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
  <title>Gestion des médicaments - PharmaLocal</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="css/admin-styles.css" />
</head>
<body data-admin-page="medicaments">
  <?php include __DIR__ . '/../../includes/nav-admin.php'; ?>

  <main class="container">
    <div class="page-header d-flex justify-content-between align-items-center flex-wrap">
      <div>
        <h1>Gestion des médicaments</h1>
        <p>Liste des médicaments disponibles.</p>
      </div>
      <div class="d-flex gap-2 align-items-center mt-3 mt-md-0">
        <div>
          <label for="pharmacyFilter" class="form-label mb-0">Filtrer par pharmacie</label>
          <select id="pharmacyFilter" class="form-select form-select-sm">
            <option value="0">Toutes les pharmacies</option>
          </select>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMedicamentModal">Ajouter un médicament</button>
      </div>
    </div>

    <div id="alert-container"></div>

    <div class="content-wrapper">
      <div class="table-responsive">
        <table class="table">
          <thead>
            <tr>
              <th>Nom</th>
              <th>Dosage</th>
              <th>Catégorie</th>
              <th>Stock</th>
              <th>Prix</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="medicamentsTableBody">
            <tr><td colspan="6" class="text-center">Chargement...</td></tr>
          </tbody>
        </table>
      </div>
    </div>
  </main>

  <!-- Modal Ajouter Médicament -->
  <div class="modal fade" id="addMedicamentModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Ajouter un médicament</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <form id="addMedicamentForm">
            <div class="mb-3">
              <label for="medNom" class="form-label">Nom du médicament</label>
              <input type="text" class="form-control" id="medNom" name="nom" required>
            </div>
            <div class="mb-3">
              <label for="medDosage" class="form-label">Dosage</label>
              <input type="text" class="form-control" id="medDosage" name="dosage" placeholder="ex: 500mg">
            </div>
            <div class="mb-3">
              <label for="medCategorie" class="form-label">Catégorie</label>
              <input type="text" class="form-control" id="medCategorie" name="categorie" placeholder="ex: Antibiotique">
            </div>
            <div class="mb-3">
              <label for="medPrix" class="form-label">Prix (FCFA)</label>
              <input type="number" class="form-control" id="medPrix" name="prix" step="0.01" min="0">
            </div>
            <div class="mb-3">
              <label for="medDescription" class="form-label">Description</label>
              <textarea class="form-control" id="medDescription" name="description" rows="3"></textarea>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
          <button type="button" class="btn btn-primary" id="btnAddMedicament">Ajouter</button>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Charger les médicaments
    async function loadMedicaments(pharmacyId = 0) {
      try {
        let url = '/PharmaLocal/backend/api/medicaments.php?action=list';
        if (pharmacyId > 0) {
          url += '&pharmacy_id=' + pharmacyId;
        }

        const response = await fetch(url);
        const result = await response.json();
        const tbody = document.getElementById('medicamentsTableBody');

        if (result.success) {
          if (result.data.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center">Aucun médicament trouvé</td></tr>';
            return;
          }

          tbody.innerHTML = result.data.map(med => `
            <tr>
              <td>${med.nom}</td>
              <td>${med.dosage || '-'}</td>
              <td>${med.categorie || '-'}</td>
              <td>${med.quantite !== undefined ? med.quantite : 0}</td>
              <td>${parseFloat(med.prix).toFixed(2)} FCFA</td>
              <td>
                <button class="btn btn-sm btn-danger" onclick="deleteMedicament(${med.id})">Supprimer</button>
              </td>
            </tr>
          `).join('');
        } else {
          tbody.innerHTML = '<tr><td colspan="6" class="text-center text-danger">Erreur lors du chargement</td></tr>';
        }
      } catch (error) {
        console.error('Erreur:', error);
        document.getElementById('medicamentsTableBody').innerHTML = '<tr><td colspan="6" class="text-center text-danger">Erreur de connexion</td></tr>';
      }
    }

    async function loadPharmacies() {
      try {
        const response = await fetch('/PharmaLocal/backend/api/pharmacies.php?action=list');
        const result = await response.json();

        if (!result.success) {
          return;
        }

        const pharmacies = result.pharmacies || result.data || [];
        const filter = document.getElementById('pharmacyFilter');
        filter.innerHTML = '<option value="0">Toutes les pharmacies</option>' + pharmacies.map(ph => `
          <option value="${ph.id}">${ph.nom}</option>
        `).join('');

        filter.addEventListener('change', () => {
          loadMedicaments(parseInt(filter.value, 10));
        });
      } catch (error) {
        console.error('Erreur:', error);
      }
    }

    // Ajouter un médicament
    document.getElementById('btnAddMedicament').addEventListener('click', async () => {
      const form = document.getElementById('addMedicamentForm');
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
          bootstrap.Modal.getInstance(document.getElementById('addMedicamentModal')).hide();
          // Réinitialiser le formulaire
          form.reset();
          // Recharger les médicaments
          loadMedicaments();
          // Afficher le message
          showAlert('success', result.message);
        } else {
          showAlert('danger', result.error || 'Erreur lors de l\'ajout');
        }
      } catch (error) {
        console.error('Erreur:', error);
        showAlert('danger', 'Erreur de connexion');
      }
    });

    // Supprimer un médicament
    async function deleteMedicament(id) {
      if (!confirm('Êtes-vous sûr de vouloir supprimer ce médicament?')) return;

      try {
        const response = await fetch('/PharmaLocal/backend/api/medicaments.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: 'action=delete&id=' + id
        });
        const result = await response.json();

        if (result.success) {
          loadMedicaments();
          showAlert('success', result.message);
        } else {
          showAlert('danger', result.error || 'Erreur lors de la suppression');
        }
      } catch (error) {
        console.error('Erreur:', error);
        showAlert('danger', 'Erreur de connexion');
      }
    }

    // Afficher une alerte
    function showAlert(type, message) {
      const alertHTML = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
          ${message}
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      `;
      const container = document.getElementById('alert-container');
      container.innerHTML = alertHTML;
      
      // Auto-fermer après 5 secondes
      setTimeout(() => {
        container.innerHTML = '';
      }, 5000);
    }

    // Charger les pharmacies et les médicaments au chargement
    loadPharmacies().finally(() => loadMedicaments());
  </script>
</body>
</html>
