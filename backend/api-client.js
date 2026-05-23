// Configuration API
const API_BASE = 'http://localhost/PharmaLocal/backend/api';

function getToastContainer() {
  let container = document.getElementById('toast-container');
  if (!container) {
    container = document.createElement('div');
    container.id = 'toast-container';
    container.className = 'position-fixed top-0 end-0 p-3';
    container.style.zIndex = '1080';
    (document.body || document.documentElement).appendChild(container);
  }
  return container;
}

function showToast(type, message, delay = 5000) {
  const toastContainer = getToastContainer();
  const toastEl = document.createElement('div');
  toastEl.className = `toast align-items-center text-bg-${type} border-0 mb-2`;
  toastEl.role = 'alert';
  toastEl.setAttribute('aria-live', 'assertive');
  toastEl.setAttribute('aria-atomic', 'true');
  toastEl.innerHTML = `
    <div class="d-flex">
      <div class="toast-body">${String(message).replace(/\n/g, '<br>')}</div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
  `;
  toastContainer.appendChild(toastEl);
  const toast = new bootstrap.Toast(toastEl, { autohide: true, delay });
  toast.show();
  toastEl.addEventListener('hidden.bs.toast', () => toastEl.remove());
  return toast;
}

// ============ AUTHENTIFICATION ============

// Inscription
function register(nomData) {
    return fetch(`${API_BASE}/auth.php`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            action: 'register',
            nom: nomData.nom,
            prenom: nomData.prenom,
            email: nomData.email,
            telephone: nomData.telephone,
            password: nomData.password,
            passwordConfirm: nomData.passwordConfirm
        })
    }).then(response => response.json());
}

// Connexion
function login(email, password) {
    return fetch(`${API_BASE}/auth.php`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            action: 'login',
            email: email,
            password: password
        })
    }).then(response => response.json());
}

// Déconnexion
function logout() {
    return fetch(`${API_BASE}/auth.php`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'logout' })
    }).then(response => response.json());
}

// Vérifier la session
function checkSession() {
    return fetch(`${API_BASE}/auth.php?action=check`, {
        method: 'GET',
        credentials: 'include'
    }).then(response => response.json());
}

// ============ UTILISATEUR ============

// Obtenir le profil
function getProfile() {
    return fetch(`${API_BASE}/users.php?action=getProfile`, {
        method: 'GET',
        credentials: 'include'
    }).then(response => response.json());
}

// Mettre à jour le profil
function updateProfile(userData) {
    return fetch(`${API_BASE}/users.php`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            action: 'updateProfile',
            nom: userData.nom,
            prenom: userData.prenom,
            telephone: userData.telephone,
            adresse: userData.adresse
        }),
        credentials: 'include'
    }).then(response => response.json());
}

// Changer le mot de passe
function changePassword(oldPassword, newPassword, confirmPassword) {
    return fetch(`${API_BASE}/users.php`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            action: 'changePassword',
            oldPassword: oldPassword,
            newPassword: newPassword,
            confirmPassword: confirmPassword
        }),
        credentials: 'include'
    }).then(response => response.json());
}

// ============ RÉSERVATIONS (AUTHENTIFICATION REQUISE) ============

// Créer une réservation
function createReservation(pharmacieId, medicamentId, quantite) {
    return fetch(`${API_BASE}/reservations.php`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            action: 'create',
            pharmacie_id: pharmacieId,
            medicament_id: medicamentId,
            quantite: quantite
        }),
        credentials: 'include'
    }).then(response => response.json());
}

// Obtenir mes réservations
function getMyReservations() {
    return fetch(`${API_BASE}/reservations.php?action=getMyReservations`, {
        method: 'GET',
        credentials: 'include'
    }).then(response => response.json());
}

// Annuler une réservation
function cancelReservation(reservationId) {
    return fetch(`${API_BASE}/reservations.php`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            action: 'cancel',
            reservation_id: reservationId
        }),
        credentials: 'include'
    }).then(response => response.json());
}

// ============ UTILITAIRES ============

// Vérifier si l'utilisateur est connecté avant de faire une action
async function requireLogin(callback) {
    const session = await checkSession();
    if (!session.loggedIn) {
        showToast('warning', 'Vous devez être connecté pour accéder à cette fonctionnalité');
        setTimeout(() => {
            window.location.href = '/PharmaLocal/frontend/users/clients/connexion.html';
        }, 900);
        return;
    }
    if (callback) callback(session.user);
}

// Exemple d'utilisation pour l'inscription
document.getElementById('btnRegister')?.addEventListener('click', async () => {
    const result = await register({
        nom: document.getElementById('nom').value,
        prenom: document.getElementById('prenom').value,
        email: document.getElementById('email').value,
        telephone: document.getElementById('telephone').value,
        password: document.getElementById('password').value,
        passwordConfirm: document.getElementById('passwordConfirm').value
    });

    if (result.success) {
        showToast('success', 'Inscription réussie ! Vous pouvez vous connecter.');
        setTimeout(() => {
            window.location.href = '/PharmaLocal/frontend/users/clients/connexion.html';
        }, 900);
    } else {
        showToast('danger', 'Erreur : ' + result.error);
    }
});

// Exemple d'utilisation pour la connexion
document.getElementById('btnLogin')?.addEventListener('click', async () => {
    const result = await login(
        document.getElementById('email').value,
        document.getElementById('password').value
    );

    if (result.success) {
        showToast('success', 'Connexion réussie !');
        localStorage.setItem('user_id', result.user.id);
        localStorage.setItem('user_email', result.user.email);
        setTimeout(() => {
            window.location.href = '/PharmaLocal/frontend/users/clients/profil.html';
        }, 900);
    } else {
        showToast('danger', 'Erreur : ' + result.error);
    }
});

// Exemple pour les réservations
document.getElementById('btnReserver')?.addEventListener('click', async () => {
    await requireLogin(async (user) => {
        const result = await createReservation(
            document.getElementById('pharmacie_id').value,
            document.getElementById('medicament_id').value,
            document.getElementById('quantite').value
        );

        if (result.success) {
            showToast('success', 'Réservation créée avec succès !');
            loadMyReservations();
        } else {
            showToast('danger', 'Erreur : ' + result.error);
        }
    });
});

// Charger les réservations
async function loadMyReservations() {
    const result = await getMyReservations();
    if (result.success) {
        const reservationsHtml = result.reservations.map(r => `
            <div class="reservation-item">
                <h4>${r.medicament}</h4>
                <p>Pharmacie: ${r.pharmacie}</p>
                <p>Quantité: ${r.quantite}</p>
                <p>Statut: <strong>${r.statut}</strong></p>
                <p>Date: ${new Date(r.date_reservation).toLocaleDateString('fr-FR')}</p>
                ${r.statut === 'en attente' ? 
                    `<button onclick="annulerReservation(${r.id})">Annuler</button>` : ''}
            </div>
        `).join('');
        document.getElementById('reservations').innerHTML = reservationsHtml || '<p>Aucune réservation</p>';
    }
}

// Annuler une réservation
async function annulerReservation(reservationId) {
    if (!confirm('Êtes-vous sûr de vouloir annuler cette réservation ?')) return;
    const result = await cancelReservation(reservationId);
    if (result.success) {
        showToast('success', 'Réservation annulée');
        loadMyReservations();
    } else {
        showToast('danger', 'Erreur : ' + result.error);
    }
}

// Charger les réservations au chargement de la page
document.addEventListener('DOMContentLoaded', () => {
    checkSession().then(session => {
        if (session.loggedIn) {
            document.getElementById('userEmail').textContent = session.user.email;
        }
    });
});
