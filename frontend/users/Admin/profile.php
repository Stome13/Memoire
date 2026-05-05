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
  <title>Profil admin - PharmaLocal</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="css/admin-styles.css" />
</head>
<body>
  <?php include __DIR__ . '/../../includes/nav-admin.php'; ?>

  <main class="container">
    <div class="row justify-content-center">
      <div class="col-lg-8">
        <div class="card">
          <div class="card-body p-4">
            <h2 class="mb-4">Mon Profil</h2>
            
            <div class="row mb-4">
              <div class="col-md-6">
                <p><strong>Prénom:</strong> <?php echo escape($currentUser['prenom']); ?></p>
              </div>
              <div class="col-md-6">
                <p><strong>Nom:</strong> <?php echo escape($currentUser['nom']); ?></p>
              </div>
            </div>

            <div class="mb-4">
              <p><strong>Email:</strong> <?php echo escape($currentUser['email']); ?></p>
            </div>

            <div class="mb-4">
              <p><strong>Rôle:</strong> <span class="badge bg-primary"><?php echo escape($currentUser['role']); ?></span></p>
            </div>

            <hr />

            <h5 class="mb-3">Changer le mot de passe</h5>
            <form id="changePasswordForm">
              <div class="mb-3">
                <label for="oldPassword" class="form-label">Ancien mot de passe</label>
                <input type="password" class="form-control" id="oldPassword" required />
              </div>
              <div class="mb-3">
                <label for="newPassword" class="form-label">Nouveau mot de passe</label>
                <input type="password" class="form-control" id="newPassword" required />
              </div>
              <div class="mb-3">
                <label for="confirmPassword" class="form-label">Confirmer le mot de passe</label>
                <input type="password" class="form-control" id="confirmPassword" required />
              </div>
              <button type="submit" class="btn btn-primary">Changer le mot de passe</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    document.getElementById('changePasswordForm').addEventListener('submit', async function(e) {
      e.preventDefault();

      const oldPassword = document.getElementById('oldPassword').value;
      const newPassword = document.getElementById('newPassword').value;
      const confirmPassword = document.getElementById('confirmPassword').value;

      if (newPassword !== confirmPassword) {
        alert('Les mots de passe ne correspondent pas');
        return;
      }

      try {
        const response = await fetch('/PharmaLocal/backend/api/users.php', {
          method: 'POST',
          credentials: 'include',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: new URLSearchParams({
            action: 'changePassword',
            oldPassword: oldPassword,
            newPassword: newPassword
          })
        });

        const result = await response.json();

        if (result.success) {
          alert('Mot de passe changé avec succès');
          document.getElementById('changePasswordForm').reset();
        } else {
          alert(result.error || 'Erreur lors du changement');
        }
      } catch (error) {
        console.error('Erreur:', error);
        alert('Erreur lors du changement de mot de passe');
      }
    });
  </script>
</body>
</html>
