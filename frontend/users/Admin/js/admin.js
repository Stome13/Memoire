// ✅ ULTRA SIMPLE : Même logique que auth.js
const pathSegments = window.location.pathname.split('/').filter(x => x);
const APP_BASE = pathSegments.length > 0 ? '/' + pathSegments[0] : '';

let BASE_URL = window.location.origin + APP_BASE;
let API_BASE = `${BASE_URL}/backend/api`;
const CLIENT_LOGIN_URL = `${BASE_URL}/frontend/users/clients/connexion.html`;

console.log('[ADMIN.JS] APP_BASE:', APP_BASE);
console.log('[ADMIN.JS] API_BASE:', API_BASE);
console.log('[ADMIN.JS] CLIENT_LOGIN_URL:', CLIENT_LOGIN_URL);

async function adminApiFetch(url, options = {}) {
  const init = Object.assign({
    credentials: 'include',
    headers: { 'Content-Type': 'application/json' }
  }, options);

  const response = await fetch(url, init);
  const text = await response.text();
  let data = null;

  try {
    data = JSON.parse(text);
  } catch (error) {
    throw new Error(`Réponse JSON invalide: ${error.message}`);
  }

  if (!response.ok) {
    throw new Error(data.error || `Erreur API (${response.status})`);
  }

  return data;
}

function showAdminAlert(message, type = 'success') {
  const alertContainer = document.getElementById('adminAlert') || document.body;
  const alertDiv = document.createElement('div');
  alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
  alertDiv.role = 'alert';
  alertDiv.innerHTML = `${message}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>`;

  if (alertContainer === document.body) {
    document.body.prepend(alertDiv);
  } else {
    alertContainer.appendChild(alertDiv);
  }

  setTimeout(() => alertDiv.remove(), 6000);
}

async function loadDashboard() {
  try {
    const stats = await adminApiFetch(`${API_BASE}/users.php?action=stats`, { method: 'GET' });
    document.getElementById('dashboardUsersCount').textContent = stats.stats.totalUsers;
    document.getElementById('dashboardPharmaciesCount').textContent = stats.stats.totalPharmacies;
  } catch (error) {
    console.error('Impossible de charger les statistiques:', error);
    showAdminAlert(error.message, 'warning');
  }

  try {
    const reservations = await adminApiFetch(`${API_BASE}/reservations.php?action=listAll`, { method: 'GET' });
    document.getElementById('dashboardReservationsCount').textContent = reservations.reservations.length;
  } catch (error) {
    console.warn('Impossible de charger le nombre de réservations:', error);
  }
}

async function loadUsers() {
  const tbody = document.getElementById('usersTableBody');
  try {
    const result = await adminApiFetch(`${API_BASE}/users.php?action=list`, { method: 'GET' });
    tbody.innerHTML = result.users.map(user => `
      <tr>
        <td>${user.id}</td>
        <td>${user.nom} ${user.prenom}</td>
        <td>${user.email}</td>
        <td>${user.role}</td>
        <td><span class="badge bg-${user.role === 'admin' ? 'primary' : user.role === 'pharmacie' ? 'success' : 'secondary'}">Actif</span></td>
        <td><button class="btn btn-sm btn-outline-secondary">Modifier</button></td>
      </tr>
    `).join('');
  } catch (error) {
    console.error('Impossible de charger les utilisateurs:', error);
    tbody.innerHTML = `<tr><td colspan="6" class="text-center text-danger">Erreur de chargement des utilisateurs</td></tr>`;
  }
}

async function loadPharmacies() {
  const tbody = document.getElementById('pharmaciesTableBody');
  try {
    const result = await adminApiFetch(`${API_BASE}/pharmacies.php?action=list`, { method: 'GET' });
    tbody.innerHTML = result.pharmacies.map(pharmacie => `
      <tr>
        <td>${pharmacie.nom}</td>
        <td>${pharmacie.adresse || ''}</td>
        <td>${pharmacie.telephone || ''}</td>
        <td><span class="badge bg-success">Active</span></td>
        <td><button class="btn btn-sm btn-outline-secondary">Modifier</button></td>
      </tr>
    `).join('');
  } catch (error) {
    console.error('Impossible de charger les pharmacies:', error);
    tbody.innerHTML = `<tr><td colspan="5" class="text-center text-danger">Erreur de chargement des pharmacies</td></tr>`;
  }
}

async function registerPharmacy(event) {
  event.preventDefault();

  const name = document.getElementById('pharmacyName').value.trim();
  const address = document.getElementById('pharmacyAddress').value.trim();
  const telephone = document.getElementById('pharmacyPhone').value.trim();
  const email = document.getElementById('pharmacyEmail').value.trim();
  const manager = document.getElementById('pharmacyManager').value.trim();
  const password = document.getElementById('pharmacyPassword').value;
  const passwordConfirm = document.getElementById('pharmacyPasswordConfirm').value;
  const hours = document.getElementById('pharmacyHours').value.trim();

  if (!name || !email || !password || !passwordConfirm) {
    showAdminAlert('Le nom, l’email et le mot de passe de la pharmacie sont requis.', 'danger');
    return;
  }

  if (password !== passwordConfirm) {
    showAdminAlert('Les mots de passe ne correspondent pas.', 'danger');
    return;
  }

  const hourParts = hours.split('-').map(part => part.trim());
  const openingHours = hourParts[0] || '';
  const closingHours = hourParts[1] || '';

  try {
    const result = await adminApiFetch(`${API_BASE}/pharmacies.php?action=register`, {
      method: 'POST',
      body: JSON.stringify({
        name,
        address,
        telephone,
        email,
        opening_hours: openingHours,
        closing_hours: closingHours,
        manager,
        password,
        passwordConfirm
      })
    });

    showAdminAlert(result.message, 'success');
    document.getElementById('registerPharmacyForm').reset();
    loadPharmacies();
  } catch (error) {
    console.error('Erreur inscription pharmacie:', error);
    showAdminAlert(error.message, 'danger');
  }
}

async function loadReservations() {
  const tbody = document.getElementById('reservationsTableBody');
  try {
    const result = await adminApiFetch(`${API_BASE}/reservations.php?action=listAll`, { method: 'GET' });
    tbody.innerHTML = result.reservations.map(reservation => `
      <tr>
        <td>#${reservation.id}</td>
        <td>${reservation.client_email}</td>
        <td>${reservation.pharmacie}</td>
        <td>${reservation.medicament}</td>
        <td><span class="badge bg-${reservation.statut === 'confirmée' ? 'success' : reservation.statut === 'annulée' ? 'danger' : 'warning'}">${reservation.statut}</span></td>
        <td><button class="btn btn-sm btn-outline-secondary">Voir</button></td>
      </tr>
    `).join('');
  } catch (error) {
    console.error('Impossible de charger les réservations:', error);
    tbody.innerHTML = `<tr><td colspan="6" class="text-center text-danger">Erreur de chargement des réservations</td></tr>`;
  }
}

async function loadProfile() {
  try {
    const result = await adminApiFetch(`${API_BASE}/users.php?action=getProfile`, { method: 'GET' });
    const user = result.user;
    document.getElementById('profileName').textContent = `${user.nom} ${user.prenom}`;
    document.getElementById('profileEmail').textContent = user.email;
    document.getElementById('profileRole').textContent = user.role;
    document.getElementById('adminFirstName').value = user.prenom || '';
    document.getElementById('adminLastName').value = user.nom || '';
    document.getElementById('adminEmail').value = user.email || '';
    document.getElementById('adminPhone').value = user.telephone || '';
  } catch (error) {
    console.error('Impossible de charger le profil:', error);
    showAdminAlert('Impossible de charger le profil.', 'danger');
  }
}

async function saveProfile(event) {
  event.preventDefault();
  try {
    const result = await adminApiFetch(`${API_BASE}/users.php`, {
      method: 'POST',
      body: JSON.stringify({
        action: 'updateProfile',
        nom: document.getElementById('adminLastName').value.trim(),
        prenom: document.getElementById('adminFirstName').value.trim(),
        telephone: document.getElementById('adminPhone').value.trim(),
        adresse: ''
      })
    });

    showAdminAlert(result.message, 'success');
    loadProfile();
  } catch (error) {
    console.error('Erreur mise à jour profil:', error);
    showAdminAlert(error.message, 'danger');
  }
}

async function handleLogout() {
  // ✅ Vider le localStorage pour l'admin
  localStorage.removeItem('pharmaLocal_currentUser');
  localStorage.removeItem('pharmaLocal_rememberMe');
  
  try {
    await adminApiFetch(`${API_BASE}/auth.php`, {
      method: 'POST',
      body: JSON.stringify({ action: 'logout' })
    });
  } catch (error) {
    console.warn('Logout API failed, redirection anyway.');
  }
  // ✅ Rediriger vers la page de connexion
  window.location.href = CLIENT_LOGIN_URL;
}

window.addEventListener('DOMContentLoaded', () => {
  const page = document.body.dataset.adminPage;

  if (page === 'dashboard') {
    loadDashboard();
  }

  if (page === 'users') {
    loadUsers();
  }

  if (page === 'pharmacies') {
    const pharmacyForm = document.getElementById('registerPharmacyForm');
    pharmacyForm?.addEventListener('submit', registerPharmacy);
    loadPharmacies();
  }

  if (page === 'reservations') {
    loadReservations();
  }

  if (page === 'profile') {
    loadProfile();
    const profileForm = document.querySelector('main form');
    profileForm?.addEventListener('submit', saveProfile);
  }

  if (page === 'logout') {
    document.getElementById('confirmLogout')?.addEventListener('click', handleLogout);
  }
});
