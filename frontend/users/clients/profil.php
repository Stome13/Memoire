<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/helpers.php';

// Vérifier que l'utilisateur est connecté
requireLogin();

// Récupérer les données utilisateur
$currentUser = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Mon Profil - PharmaGarde</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link rel="stylesheet" href="css/variables.css" />
  <link rel="stylesheet" href="css/styles.css" />
  <style>
    .profile-header {
      background: linear-gradient(135deg, var(--primary) 0%, #0056b3 100%);
      color: white;
      padding: 40px 0;
      margin-bottom: 30px;
    }

    .profile-avatar {
      width: 150px;
      height: 150px;
      border-radius: 50%;
      background: white;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 60px;
      margin: 0 auto 20px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    }

    .profile-info h2 {
      margin-bottom: 10px;
    }

    .profile-info p {
      margin: 5px 0;
      opacity: 0.9;
    }

    .nav-tabs {
      border-bottom: 2px solid var(--border-color);
      margin-bottom: 30px;
    }

    .nav-tabs .nav-link {
      color: var(--text-light);
      border: none;
      border-bottom: 3px solid transparent;
      padding: 12px 24px;
      transition: all 0.3s ease;
    }

    .nav-tabs .nav-link:hover {
      color: var(--primary);
      border-bottom-color: var(--primary);
    }

    .nav-tabs .nav-link.active {
      color: var(--primary);
      border-bottom-color: var(--primary);
      background: transparent;
    }

    .profile-card {
      border: 1px solid var(--border-color);
      border-radius: var(--border-radius);
      padding: 30px;
      margin-bottom: 20px;
      box-shadow: var(--shadow-sm);
    }

    .form-section {
      margin-bottom: 30px;
    }

    .form-section h5 {
      color: var(--text-dark);
      margin-bottom: 20px;
      padding-bottom: 10px;
      border-bottom: 2px solid var(--primary);
    }

    .form-group {
      margin-bottom: 15px;
    }

    .reservation-card {
      border: 1px solid var(--border-color);
      border-radius: var(--border-radius);
      padding: 20px;
      margin-bottom: 15px;
      transition: all 0.3s ease;
    }

    .reservation-card:hover {
      box-shadow: var(--shadow-md);
    }

    .reservation-status {
      display: inline-block;
      padding: 5px 12px;
      border-radius: 20px;
      font-size: 0.85rem;
      font-weight: 600;
    }

    .status-pending {
      background-color: #fff3cd;
      color: #856404;
    }

    .status-confirmed {
      background-color: #d4edda;
      color: #155724;
    }

    .status-cancelled {
      background-color: #f8d7da;
      color: #721c24;
    }

    .status-picked {
      background-color: #d1ecf1;
      color: #0c5460;
    }

    .btn-action {
      font-size: 0.85rem;
      padding: 6px 12px;
    }

    .empty-state {
      text-align: center;
      padding: 40px 20px;
      color: var(--text-light);
    }

    .empty-state i {
      font-size: 48px;
      margin-bottom: 15px;
      opacity: 0.5;
    }
  </style>
</head>
<body>
  <!-- Navigation -->
  <?php include __DIR__ . '/../../includes/nav-client.php'; ?>

  <main>
    <!-- Profile Header -->
    <section class="profile-header">
      <div class="container-lg">
        <div class="profile-avatar">
          <i class="fas fa-user"></i>
        </div>
        <div class="profile-info text-center">
          <h2><?php echo escape($currentUser['prenom'] . ' ' . $currentUser['nom']); ?></h2>
          <p><?php echo escape($currentUser['email']); ?></p>
          <div id="profileStatus" class="mt-3"></div>
        </div>
      </div>
    </section>

    <!-- Profile Content -->
    <section class="py-5">
      <div class="container-lg">
        <div class="row">
          <div class="col-lg-3">
            <!-- Profile Menu -->
            <div class="list-group sticky-top" style="top: 20px;">
              <button class="list-group-item list-group-item-action active" data-bs-toggle="list" href="#profile-tab">
                <i class="fas fa-user me-2"></i>Informations Personnelles
              </button>
              <button class="list-group-item list-group-item-action" data-bs-toggle="list" href="#reservations-tab">
                <i class="fas fa-calendar-check me-2"></i>Mes Réservations
                <span id="reservationCount" class="badge bg-primary ms-auto">0</span>
              </button>
              <button class="list-group-item list-group-item-action" data-bs-toggle="list" href="#security-tab">
                <i class="fas fa-lock me-2"></i>Sécurité
              </button>
              <button class="list-group-item list-group-item-action" data-bs-toggle="list" href="#health-tab">
                <i class="fas fa-heartbeat me-2"></i>Informations Santé
              </button>
            </div>
          </div>

          <div class="col-lg-9">
            <!-- Alert Container -->
            <div id="alertContainer"></div>

            <!-- Profile Tab -->
            <div class="profile-card" id="profile-tab">
              <div class="d-flex justify-content-between align-items-center mb-4">
                <h4><i class="fas fa-user me-2"></i>Informations Personnelles</h4>
                <button class="btn btn-primary btn-sm" id="editProfileBtn">
                  <i class="fas fa-edit me-1"></i>Modifier
                </button>
              </div>

              <!-- Display Mode -->
              <div id="profileDisplay">
                <div class="row">
                  <div class="col-md-6">
                    <p><strong>Prénom:</strong> <span id="displayFirstName"><?php echo escape($currentUser['prenom']); ?></span></p>
                    <p><strong>Nom:</strong> <span id="displayLastName"><?php echo escape($currentUser['nom']); ?></span></p>
                    <p><strong>Email:</strong> <span id="displayEmail"><?php echo escape($currentUser['email']); ?></span></p>
                    <p><strong>Téléphone:</strong> <span id="displayPhone">-</span></p>
                  </div>
                  <div class="col-md-6">
                    <p><strong>Ville:</strong> <span id="displayCity">-</span></p>
                    <p><strong>Adresse:</strong> <span id="displayAddress">-</span></p>
                    <p><strong>Date de naissance:</strong> <span id="displayDateOfBirth">-</span></p>
                  </div>
                </div>
              </div>

              <!-- Edit Mode -->
              <form id="editProfileForm" class="d-none needs-validation" novalidate>
                <div class="row">
                  <div class="col-md-6 form-group">
                    <label for="editFirstName" class="form-label fw-bold">Prénom</label>
                    <input type="text" class="form-control" id="editFirstName" value="<?php echo escape($currentUser['prenom']); ?>" required />
                  </div>
                  <div class="col-md-6 form-group">
                    <label for="editLastName" class="form-label fw-bold">Nom</label>
                    <input type="text" class="form-control" id="editLastName" value="<?php echo escape($currentUser['nom']); ?>" required />
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-6 form-group">
                    <label for="editPhone" class="form-label fw-bold">Téléphone</label>
                    <input type="tel" class="form-control" id="editPhone" />
                  </div>
                  <div class="col-md-6 form-group">
                    <label for="editCity" class="form-label fw-bold">Ville</label>
                    <input type="text" class="form-control" id="editCity" />
                  </div>
                </div>

                <div class="form-group">
                  <label for="editAddress" class="form-label fw-bold">Adresse</label>
                  <input type="text" class="form-control" id="editAddress" />
                </div>

                <div class="form-group">
                  <label for="editDateOfBirth" class="form-label fw-bold">Date de naissance</label>
                  <input type="date" class="form-control" id="editDateOfBirth" />
                </div>

                <div class="d-flex gap-2">
                  <button type="submit" class="btn btn-success">Enregistrer les modifications</button>
                  <button type="button" class="btn btn-secondary" id="cancelEditBtn">Annuler</button>
                </div>
              </form>
            </div>

            <!-- Reservations Tab -->
            <div class="profile-card d-none" id="reservations-tab">
              <h4><i class="fas fa-calendar-check me-2"></i>Mes Réservations</h4>
              <div id="reservationsList"></div>
            </div>

            <!-- Security Tab -->
            <div class="profile-card d-none" id="security-tab">
              <h4><i class="fas fa-lock me-2"></i>Sécurité</h4>

              <div class="form-section">
                <h5>Changer mot de passe</h5>
                <form id="changePasswordForm" class="needs-validation" novalidate>
                  <div class="form-group">
                    <label for="oldPassword" class="form-label fw-bold">Ancien mot de passe</label>
                    <input type="password" class="form-control" id="oldPassword" required />
                  </div>
                  <div class="form-group">
                    <label for="newPassword" class="form-label fw-bold">Nouveau mot de passe</label>
                    <input type="password" class="form-control" id="newPassword" required />
                  </div>
                  <div class="form-group">
                    <label for="confirmPassword" class="form-label fw-bold">Confirmer le mot de passe</label>
                    <input type="password" class="form-control" id="confirmPassword" required />
                  </div>
                  <button type="submit" class="btn btn-primary">Changer le mot de passe</button>
                </form>
              </div>

              <hr />

              <div class="form-section">
                <h5>Compte</h5>
                <p class="text-muted">Zone de danger - Actions irréversibles</p>
                <button class="btn btn-danger" id="deleteAccountBtn">
                  <i class="fas fa-trash me-1"></i>Supprimer mon compte
                </button>
              </div>
            </div>

            <!-- Health Tab -->
            <div class="profile-card d-none" id="health-tab">
              <div class="d-flex justify-content-between align-items-center mb-4">
                <h4><i class="fas fa-heartbeat me-2"></i>Informations Santé</h4>
                <button class="btn btn-primary btn-sm" id="editHealthBtn">
                  <i class="fas fa-edit me-1"></i>Modifier
                </button>
              </div>

              <!-- Display Mode -->
              <div id="healthDisplay">
                <div class="alert alert-info">
                  <i class="fas fa-info-circle me-2"></i>
                  <strong>Allergies:</strong> <span id="displayAllergies">Non renseignées</span>
                </div>
              </div>

              <!-- Edit Mode -->
              <form id="editHealthForm" class="d-none needs-validation" novalidate>
                <div class="form-group">
                  <label for="editAllergies" class="form-label fw-bold">Allergies (séparées par des virgules)</label>
                  <textarea class="form-control" id="editAllergies" rows="4" placeholder="Ex: Pénicilline, Arachides..."></textarea>
                </div>
                <div class="d-flex gap-2">
                  <button type="submit" class="btn btn-success">Enregistrer</button>
                  <button type="button" class="btn btn-secondary" id="cancelHealthBtn">Annuler</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>

  <?php include __DIR__ . '/../../includes/footer.php'; ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    const API_BASE = '/PharmaLocal/backend/api';
    const currentUser = <?php echo json_encode($currentUser); ?>;

    // Gestion du profil - Édition
    document.getElementById('editProfileBtn').addEventListener('click', function() {
      document.getElementById('profileDisplay').classList.add('d-none');
      document.getElementById('editProfileForm').classList.remove('d-none');
    });

    document.getElementById('cancelEditBtn').addEventListener('click', function() {
      document.getElementById('editProfileForm').classList.add('d-none');
      document.getElementById('profileDisplay').classList.remove('d-none');
    });

    document.getElementById('editProfileForm').addEventListener('submit', async function(e) {
      e.preventDefault();
      
      const data = {
        action: 'updateProfile',
        prenom: document.getElementById('editFirstName').value,
        nom: document.getElementById('editLastName').value,
        telephone: document.getElementById('editPhone').value,
        ville: document.getElementById('editCity').value,
        adresse: document.getElementById('editAddress').value,
        dateOfBirth: document.getElementById('editDateOfBirth').value
      };

      try {
        const response = await fetch(`${API_BASE}/users.php`, {
          method: 'POST',
          credentials: 'include',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: new URLSearchParams(data)
        });

        const result = await response.json();

        if (result.success) {
          showAlert('success', 'Profil mis à jour avec succès');
          // Mettre à jour l'affichage
          document.getElementById('displayFirstName').textContent = data.prenom;
          document.getElementById('displayLastName').textContent = data.nom;
          document.getElementById('displayPhone').textContent = data.telephone || '-';
          document.getElementById('displayCity').textContent = data.ville || '-';
          document.getElementById('displayAddress').textContent = data.adresse || '-';
          document.getElementById('displayDateOfBirth').textContent = data.dateOfBirth || '-';

          setTimeout(() => {
            document.getElementById('editProfileForm').classList.add('d-none');
            document.getElementById('profileDisplay').classList.remove('d-none');
          }, 1500);
        } else {
          showAlert('danger', result.error || 'Erreur lors de la mise à jour');
        }
      } catch (error) {
        console.error('Erreur:', error);
        showAlert('danger', 'Erreur lors de la mise à jour');
      }
    });

    // Gestion de la santé
    document.getElementById('editHealthBtn').addEventListener('click', function() {
      document.getElementById('healthDisplay').classList.add('d-none');
      document.getElementById('editHealthForm').classList.remove('d-none');
    });

    document.getElementById('cancelHealthBtn').addEventListener('click', function() {
      document.getElementById('editHealthForm').classList.add('d-none');
      document.getElementById('healthDisplay').classList.remove('d-none');
    });

    document.getElementById('editHealthForm').addEventListener('submit', async function(e) {
      e.preventDefault();
      
      const allergies = document.getElementById('editAllergies').value;
      
      try {
        const response = await fetch(`${API_BASE}/users.php`, {
          method: 'POST',
          credentials: 'include',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: new URLSearchParams({
            action: 'updateHealth',
            allergies: allergies
          })
        });

        const result = await response.json();

        if (result.success) {
          showAlert('success', 'Informations de santé mises à jour');
          document.getElementById('displayAllergies').textContent = allergies || 'Non renseignées';
          
          setTimeout(() => {
            document.getElementById('editHealthForm').classList.add('d-none');
            document.getElementById('healthDisplay').classList.remove('d-none');
          }, 1500);
        } else {
          showAlert('danger', result.error || 'Erreur lors de la mise à jour');
        }
      } catch (error) {
        console.error('Erreur:', error);
        showAlert('danger', 'Erreur lors de la mise à jour');
      }
    });

    // Changer mot de passe
    document.getElementById('changePasswordForm').addEventListener('submit', async function(e) {
      e.preventDefault();
      
      const newPassword = document.getElementById('newPassword').value;
      const confirmPassword = document.getElementById('confirmPassword').value;

      if (newPassword !== confirmPassword) {
        showAlert('danger', 'Les mots de passe ne correspondent pas');
        return;
      }

      try {
        const response = await fetch(`${API_BASE}/users.php`, {
          method: 'POST',
          credentials: 'include',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: new URLSearchParams({
            action: 'changePassword',
            oldPassword: document.getElementById('oldPassword').value,
            newPassword: newPassword
          })
        });

        const result = await response.json();

        if (result.success) {
          showAlert('success', 'Mot de passe changé avec succès');
          document.getElementById('changePasswordForm').reset();
        } else {
          showAlert('danger', result.error || 'Erreur lors du changement de mot de passe');
        }
      } catch (error) {
        console.error('Erreur:', error);
        showAlert('danger', 'Erreur lors du changement de mot de passe');
      }
    });

    function showAlert(type, message) {
      const container = document.getElementById('alertContainer');
      const alertDiv = document.createElement('div');
      alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
      alertDiv.setAttribute('role', 'alert');
      alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      `;
      container.appendChild(alertDiv);
    }
  </script>
</body>
</html>
