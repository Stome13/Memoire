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
  <title>Réservations - Pharmacien | PharmaGarde</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link rel="stylesheet" href="./css/variables.css" />
  <link rel="stylesheet" href="css/dashboard-styles.css" />
</head>
<body>
  <?php include __DIR__ . '/../../includes/nav-pharmacien.php'; ?>

      <div class="dashboard-header">
        <h1 class="page-title">
          <i class="fas fa-handshake me-2"></i>Réservations
        </h1>
      </div>

      <div class="table-responsive">
        <table class="table table-striped">
          <thead>
            <tr>
              <th>Client</th>
              <th>Médicament</th>
              <th>Quantité</th>
              <th>Date</th>
              <th>Statut</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="reservationsTable">
            <tr><td colspan="6" class="text-center">Chargement des réservations...</td></tr>
          </tbody>
        </table>
      </div>
    </main>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      loadReservations();
    });

    function loadReservations() {
      fetch('/PharmaLocal/backend/api/reservations.php?action=listByPharmacy', {
        credentials: 'include'
      })
      .then(response => response.json())
      .then(data => {
        const tbody = document.getElementById('reservationsTable');
        
        if (data.success && data.reservations && data.reservations.length > 0) {
          tbody.innerHTML = data.reservations.map(res => `
            <tr>
              <td>${res.client_prenom} ${res.client_nom}</td>
              <td>${res.medicament}${res.dosage ? ' (' + res.dosage + ')' : ''}</td>
              <td>${res.quantite}</td>
              <td>${new Date(res.date_reservation).toLocaleDateString('fr-FR')}</td>
              <td>
                <span class="badge bg-${
                  res.statut === 'confirmée' ? 'success' : 
                  res.statut === 'prête' ? 'info' : 
                  res.statut === 'retirée' ? 'secondary' : 
                  res.statut === 'annulée' ? 'danger' : 'warning'
                }">
                  ${res.statut}
                </span>
              </td>
              <td>
                ${res.statut === 'en attente' ? '<button class="btn btn-sm btn-success" onclick="confirmReservation(' + res.id + ')">Confirmer</button>' : ''}
                ${res.statut === 'confirmée' ? '<button class="btn btn-sm btn-info" onclick="markReady(' + res.id + ')">Prête</button>' : ''}
              </td>
            </tr>
          `).join('');
        } else {
          tbody.innerHTML = '<tr><td colspan="6" class="text-center">Aucune réservation trouvée</td></tr>';
        }
      })
      .catch(error => {
        console.error('Erreur:', error);
        document.getElementById('reservationsTable').innerHTML = '<tr><td colspan="6" class="text-center text-danger">Erreur de chargement</td></tr>';
      });
    }

    function confirmReservation(id) {
      alert('Confirmation non implémentée');
    }

    function markReady(id) {
      alert('Marquage prêt non implémenté');
    }
  </script>
</body>
</html>
