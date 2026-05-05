<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/helpers.php';
requireRole('admin');
$currentUser = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Gestion des utilisateurs - PharmaLocal</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="css/admin-styles.css" />
</head>
<body data-admin-page="users">
  <?php include __DIR__ . '/../../includes/nav-admin.php'; ?>

  <main class="container">
    <div class="page-header d-flex justify-content-between align-items-center">
      <div>
        <h1>Gestion des utilisateurs</h1>
        <p>Liste des comptes clients et administrateurs.</p>
      </div>
      <button class="btn btn-primary">Créer un utilisateur</button>
    </div>

    <div class="content-wrapper">
      <div class="table-responsive">
        <table class="table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Nom</th>
              <th>Email</th>
              <th>Rôle</th>
              <th>Statut</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="usersTableBody">
            <tr><td colspan="6" class="text-center">Chargement des utilisateurs...</td></tr>
          </tbody>
        </table>
      </div>
    </div>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="js/admin.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      loadUsers();
    });

    function loadUsers() {
      fetch('/PharmaLocal/backend/api/users.php?action=list', {
        credentials: 'include'
      })
      .then(response => response.json())
      .then(data => {
        const tbody = document.getElementById('usersTableBody');
        if (data.success && data.users && data.users.length > 0) {
          tbody.innerHTML = data.users.map(user => `
            <tr>
              <td>${user.id}</td>
              <td>${user.prenom} ${user.nom}</td>
              <td>${user.email}</td>
              <td><span class="badge bg-info">${user.role}</span></td>
              <td><span class="badge bg-success">Actif</span></td>
              <td>
                <button class="btn btn-sm btn-warning" onclick="editUser(${user.id})">Modifier</button>
                <button class="btn btn-sm btn-danger" onclick="deleteUser(${user.id})">Supprimer</button>
              </td>
            </tr>
          `).join('');
        } else {
          tbody.innerHTML = '<tr><td colspan="6" class="text-center">Aucun utilisateur trouvé</td></tr>';
        }
      })
      .catch(error => console.error('Erreur:', error));
    }

    function editUser(id) {
      alert('Modification de l\'utilisateur ' + id);
    }

    function deleteUser(id) {
      if(confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur?')) {
        alert('Suppression de l\'utilisateur ' + id);
      }
    }
  </script>
</body>
</html>
