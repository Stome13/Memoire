// Configuration API
const API_BASE = 'http://localhost/PharmaLocal/backend/api';

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
        alert('Vous devez être connecté pour accéder à cette fonctionnalité');
        window.location.href = '/PharmaLocal/frontend/users/clients/connexion.html';
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
        alert('Inscription réussie ! Vous pouvez vous connecter.');
        window.location.href = '/PharmaLocal/frontend/users/clients/connexion.html';
    } else {
        alert('Erreur : ' + result.error);
    }
});

// Exemple d'utilisation pour la connexion
document.getElementById('btnLogin')?.addEventListener('click', async () => {
    const result = await login(
        document.getElementById('email').value,
        document.getElementById('password').value
    );

    if (result.success) {
        alert('Connexion réussie !');
        localStorage.setItem('user_id', result.user.id);
        localStorage.setItem('user_email', result.user.email);
        window.location.href = '/PharmaLocal/frontend/users/clients/profil.html';
    } else {
        alert('Erreur : ' + result.error);
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
            alert('Réservation créée avec succès !');
            loadMyReservations();
        } else {
            alert('Erreur : ' + result.error);
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
        alert('Réservation annulée');
        loadMyReservations();
    } else {
        alert('Erreur : ' + result.error);
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
