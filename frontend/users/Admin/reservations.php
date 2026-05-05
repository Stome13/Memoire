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
  <title>Gestion des réservations - PharmaLocal</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="css/admin-styles.css" />
</head>
<body data-admin-page="reservations">
  <?php include __DIR__ . '/../../includes/nav-admin.php'; ?>

  <main class="container">
    <div class="page-header">
      <h1>Gestion des réservations</h1>
      <p>Liste de toutes les réservations en attente et confirmées.</p>
    </div>

    <div class="content-wrapper">
      <div class="table-responsive">
        <table class="table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Client</th>
              <th>Pharmacie</th>
              <th>Médicament</th>
              <th>Date</th>
              <th>Statut</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="reservationsTableBody">
            <tr><td colspan="7" class="text-center">Chargement...</td></tr>
          </tbody>
        </table>
      </div>
    </div>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      loadReservations();
    });

    function loadReservations() {
      fetch('/PharmaLocal/backend/api/reservations.php?action=listAll', {
        credentials: 'include'
      })
      .then(response => response.json())
      .then(data => {
        const tbody = document.getElementById('reservationsTableBody');
        if (data.success && data.reservations && data.reservations.length > 0) {
          tbody.innerHTML = data.reservations.map(res => `
            <tr>
              <td>${res.id}</td>
              <td>${res.client_name || 'N/A'}</td>
              <td>${res.pharmacy_name || 'N/A'}</td>
              <td>${res.medicine_name || 'N/A'}</td>
              <td>${res.created_at || 'N/A'}</td>
              <td><span class="badge bg-info">${res.status || 'pending'}</span></td>
              <td>
                <button class="btn btn-sm btn-warning">Modifier</button>
              </td>
            </tr>
          `).join('');
        } else {
          tbody.innerHTML = '<tr><td colspan="7" class="text-center">Aucune réservation trouvée</td></tr>';
        }
      })
      .catch(error => console.error('Erreur:', error));
    }
  </script>
</body>
</html>
