// ========================================
// GESTION DE LA PAGE PROFIL UTILISATEUR - API PHP
// ========================================

let currentUser = null;

document.addEventListener('DOMContentLoaded', async function () {
  // Initialiser et vérifier la connexion
  if (!authManager.isLoggedIn()) {
    window.location.href = 'connexion.html';
    return;
  }

  await loadProfileData();
  await loadReservations();
  setupEventListeners();
});

// Charger les données du profil depuis l'API
async function loadProfileData() {
  try {
    const profile = await authManager.getProfile();
    
    if (!profile) {
      showAlert('danger', 'Impossible de charger les données du profil');
      return;
    }

    currentUser = profile;

    // Header
    document.getElementById('userName').textContent = `${profile.prenom} ${profile.nom}`;
    document.getElementById('userEmail').textContent = profile.email;
    document.getElementById('memberDate').textContent = 'Membre de PharmaLocal';

    // Infos personnelles (mode affichage)
    document.getElementById('displayFirstName').textContent = profile.prenom || '-';
    document.getElementById('displayLastName').textContent = profile.nom || '-';
    document.getElementById('displayEmail').textContent = profile.email || '-';
    document.getElementById('displayPhone').textContent = profile.telephone || '-';
    document.getElementById('displayAddress').textContent = profile.adresse || '-';

    // Pré-remplir le formulaire d'édition
    document.getElementById('editFirstName').value = profile.prenom || '';
    document.getElementById('editLastName').value = profile.nom || '';
    document.getElementById('editPhone').value = profile.telephone || '';
    document.getElementById('editAddress').value = profile.adresse || '';
  } catch (error) {
    console.error('Erreur chargement profil:', error);
    showAlert('danger', 'Erreur lors du chargement du profil');
  }
}

// Charger les réservations depuis l'API
async function loadReservations() {
  try {
    const reservations = await authManager.getUserReservations(currentUser.id);
    const reservationsList = document.getElementById('reservationsList');

    if (!reservationsList) return;

    document.getElementById('reservationCount').textContent = reservations.length;

    if (reservations.length === 0) {
      reservationsList.innerHTML = `
        <div class="empty-state">
          <i class="fas fa-calendar-times"></i>
          <h5>Aucune réservation</h5>
          <p>Vous n'avez pas encore effectué de réservation.</p>
          <a href="medicaments.html" class="btn btn-primary mt-3">Chercher un médicament</a>
        </div>
      `;
      return;
    }

    reservationsList.innerHTML = reservations.map(r => `
      <div class="reservation-card">
        <div class="d-flex justify-content-between align-items-start mb-3">
          <div>
            <h5 class="mb-1">${r.medicament}</h5>
            <p class="text-muted mb-2">
              <i class="fas fa-hospital me-2"></i>${r.pharmacie}
            </p>
          </div>
          <span class="reservation-status status-${r.statut}">
            ${getStatusLabel(r.statut)}
          </span>
        </div>

        <div class="row text-sm mb-3">
          <div class="col-md-6">
            <p class="mb-1"><small><strong>Dosage:</strong> ${r.dosage || '-'}</small></p>
            <p class="mb-1"><small><strong>Quantité:</strong> ${r.quantite}</small></p>
          </div>
          <div class="col-md-6">
            <p class="mb-1"><small><strong>Téléphone:</strong> ${r.telephone}</small></p>
            <p class="mb-1"><small><strong>Date:</strong> ${new Date(r.date_reservation).toLocaleDateString('fr-FR')}</small></p>
          </div>
        </div>

        <div class="d-flex gap-2">
          ${r.statut === 'en attente' || r.statut === 'pending' ? `
            <button class="btn btn-danger btn-sm btn-action" onclick="cancelReservation(${r.id})">
              <i class="fas fa-times me-1"></i>Annuler
            </button>
          ` : ''}
        </div>
      </div>
    `).join('');
  } catch (error) {
    console.error('Erreur chargement réservations:', error);
  }
}

// Obtenir le label du statut
function getStatusLabel(status) {
  const labels = {
    'en attente': 'En attente',
    'confirmée': 'Confirmée',
    'prête': 'Prête',
    'retirée': 'Retirée',
    'annulée': 'Annulée',
    'pending': 'En attente',
    'confirmed': 'Confirmée',
    'ready': 'Prête',
    'picked_up': 'Retirée',
    'cancelled': 'Annulée'
  };
  return labels[status] || status;
}

// Annuler une réservation
async function cancelReservation(reservationId) {
  if (confirm('Êtes-vous sûr de vouloir annuler cette réservation?')) {
    try {
      const result = await authManager.cancelReservation(currentUser.id, reservationId);

      if (result.success) {
        showAlert('success', result.message);
        await loadReservations();
      } else {
        showAlert('danger', result.message);
      }
    } catch (error) {
      console.error('Erreur annulation:', error);
      showAlert('danger', 'Erreur lors de l\'annulation');
    }
  }
}

// Configuration des événements
function setupEventListeners() {
  // Logout
  const logoutBtn = document.getElementById('logoutBtn');
  if (logoutBtn) {
    logoutBtn.addEventListener('click', async function () {
      if (confirm('Êtes-vous sûr de vouloir vous déconnecter?')) {
        try {
          await authManager.logout();
          showAlert('success', 'Vous avez été déconnecté.');
          setTimeout(() => {
            window.location.href = 'index.html';
          }, 1000);
        } catch (error) {
          console.error('Erreur déconnexion:', error);
        }
      }
    });
  }

  // Edit Profile
  const editProfileBtn = document.getElementById('editProfileBtn');
  const profileDisplay = document.getElementById('profileDisplay');
  const editProfileForm = document.getElementById('editProfileForm');

  if (editProfileBtn) {
    editProfileBtn.addEventListener('click', function () {
      profileDisplay.classList.add('d-none');
      editProfileForm.classList.remove('d-none');
    });
  }

  const cancelEditBtn = document.getElementById('cancelEditBtn');
  if (cancelEditBtn) {
    cancelEditBtn.addEventListener('click', function () {
      profileDisplay.classList.remove('d-none');
      editProfileForm.classList.add('d-none');
    });
  }

  // Save Profile Changes
  if (editProfileForm) {
    editProfileForm.addEventListener('submit', async function (e) {
      e.preventDefault();

      const updatedData = {
        firstName: document.getElementById('editFirstName').value,
        lastName: document.getElementById('editLastName').value,
        phone: document.getElementById('editPhone').value,
        address: document.getElementById('editAddress').value
      };

      try {
        const result = await authManager.updateProfile(currentUser.id, updatedData);

        if (result.success) {
          showAlert('success', result.message);
          await loadProfileData();
          profileDisplay.classList.remove('d-none');
          editProfileForm.classList.add('d-none');
        } else {
          showAlert('danger', result.message);
        }
      } catch (error) {
        console.error('Erreur mise à jour profil:', error);
        showAlert('danger', 'Erreur lors de la mise à jour');
      }
    });
  }

  // Change Password
  const changePasswordForm = document.getElementById('changePasswordForm');
  if (changePasswordForm) {
    changePasswordForm.addEventListener('submit', async function (e) {
      e.preventDefault();

      const oldPassword = document.getElementById('oldPassword').value;
      const newPassword = document.getElementById('newPassword').value;
      const confirmPassword = document.getElementById('confirmPassword').value;

      if (!oldPassword || !newPassword || !confirmPassword) {
        showAlert('danger', 'Tous les champs sont requis');
        return;
      }

      if (newPassword !== confirmPassword) {
        showAlert('danger', 'Les mots de passe ne correspondent pas.');
        return;
      }

      if (newPassword.length < 8) {
        showAlert('danger', 'Le nouveau mot de passe doit contenir au moins 8 caractères.');
        return;
      }

      try {
        // Implémenter changePassword dans authManager pour utiliser l'API
        showAlert('info', 'Fonction en cours d\'implémentation. Veuillez contacter le support.');
      } catch (error) {
        console.error('Erreur changement mot de passe:', error);
        showAlert('danger', 'Erreur lors du changement de mot de passe');
      }
    });
  }
}

// Fonction utilitaire pour afficher les alertes
function showAlert(type, message) {
  const alertDiv = document.createElement('div');
  alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
  alertDiv.role = 'alert';
  alertDiv.innerHTML = `
    ${message}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  `;

  const container = document.querySelector('main') || document.body;
  container.insertBefore(alertDiv, container.firstChild);

  setTimeout(() => {
    alertDiv.remove();
  }, 5000);
}


