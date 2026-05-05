// ========================================
// SYSTÈME D'AUTHENTIFICATION - API PHP
// ========================================

// ✅ ULTRA SIMPLE : Calculer le dossier racine du projet
// Exemple: URL = "http://localhost/PharmaLocal/frontend/..."
// Résultat: APP_BASE = "/PharmaLocal"
const pathSegments = window.location.pathname.split('/').filter(x => x);
const APP_BASE = pathSegments.length > 0 ? '/' + pathSegments[0] : '';
const API_BASE = window.location.origin + APP_BASE + '/backend/api';

console.log('[AUTH] APP_BASE:', APP_BASE); // Debug
console.log('[AUTH] API_BASE:', API_BASE); // Debug

class AuthManager {
  constructor() {
    this.currentUserKey = 'pharmaLocal_currentUser';
    this.justLoggedIn = false; // Flag pour éviter d'effacer localStorage immédiatement après login
  }

  // Initialiser la session au chargement
  async initializeSession() {
    // ⚠️ NE PAS vider localStorage si on vient de se connecter
    if (this.justLoggedIn) {
      console.log('[AUTH] Just logged in, skipping session check');
      return;
    }

    const session = await this.checkSession();
    if (session.loggedIn) {
      localStorage.setItem(this.currentUserKey, JSON.stringify(session.user));
    } else {
      localStorage.removeItem(this.currentUserKey);
    }
  }

  // Vérifier la session actuelle
  async checkSession() {
    try {
      const response = await fetch(`${API_BASE}/auth.php?action=check`, {
        method: 'GET',
        credentials: 'include'
      });
      return await response.json();
    } catch (error) {
      console.error('Erreur vérification session:', error);
      return { loggedIn: false };
    }
  }

  // Obtenir l'utilisateur actuellement connecté
  getCurrentUser() {
    const currentUser = localStorage.getItem(this.currentUserKey);
    return currentUser ? JSON.parse(currentUser) : null;
  }

  // Vérifier si un utilisateur est connecté
  isLoggedIn() {
    return this.getCurrentUser() !== null;
  }

  // Chercher un utilisateur par email (vérification côté serveur lors de l'inscription)
  async findUserByEmail(email) {
    // Cette vérification se fait côté serveur maintenant
    return false;
  }

  // Créer un nouvel utilisateur (inscription)
  async register(userData) {
    try {
      const response = await fetch(`${API_BASE}/auth.php`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'include',
        body: JSON.stringify({
          action: 'register',
          nom: userData.lastName?.trim() || '',
          prenom: userData.firstName?.trim() || '',
          email: userData.email?.toLowerCase().trim() || '',
          telephone: userData.phone || '',
          password: userData.password || '',
          passwordConfirm: userData.password || ''
        })
      });

      let data;
      try {
        data = await response.json();
      } catch (parseError) {
        console.error('Impossible de parser la réponse JSON:', parseError);
        return { success: false, message: `Erreur serveur (${response.status})` };
      }

      if (!response.ok) {
        return { success: false, message: data.error || `Erreur serveur (${response.status})` };
      }

      if (data.success) {
        localStorage.setItem(this.currentUserKey, JSON.stringify(data.user));
        return { success: true, message: 'Inscription réussie!', user: data.user };
      } else {
        return { success: false, message: data.error || 'Erreur lors de l\'inscription' };
      }
    } catch (error) {
      console.error('Erreur inscription:', error);
      return { success: false, message: 'Erreur de connexion au serveur: ' + error.message };
    }
  }

  // Connexion utilisateur
  async login(email, password) {
    try {
      const response = await fetch(`${API_BASE}/auth.php`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'include',
        body: JSON.stringify({
          action: 'login',
          email: email.toLowerCase().trim(),
          password: password
        })
      });

      let data;
      try {
        data = await response.json();
      } catch (parseError) {
        console.error('Impossible de parser la réponse JSON:', parseError);
        throw new Error(`Réponse non JSON (${response.status})`);
      }

      console.log('Login response:', data);

      if (!response.ok) {
        return { success: false, message: data.error || `Erreur serveur (${response.status})` };
      }

      if (data.success) {
        localStorage.setItem(this.currentUserKey, JSON.stringify(data.user));
        console.log('[AUTH] Stored currentUser after login:', localStorage.getItem(this.currentUserKey));
        this.justLoggedIn = true; // Éviter que initializeSession() vide le localStorage
        return { success: true, message: 'Connexion réussie!', user: data.user };
      } else {
        return { success: false, message: data.error || 'Email ou mot de passe incorrect' };
      }
    } catch (error) {
      console.error('Erreur connexion:', error);
      return { success: false, message: 'Erreur de connexion au serveur: ' + error.message };
    }
  }

  // Déconnexion
  async logout() {
    try {
      await fetch(`${API_BASE}/auth.php`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'include',
        body: JSON.stringify({ action: 'logout' })
      });

      localStorage.removeItem(this.currentUserKey);
      return { success: true, message: 'Vous avez été déconnecté.' };
    } catch (error) {
      console.error('Erreur déconnexion:', error);
      localStorage.removeItem(this.currentUserKey);
      return { success: true, message: 'Déconnexion effectuée' };
    }
  }

  // Mettre à jour le profil utilisateur
  async updateProfile(userId, updatedData) {
    try {
      const response = await fetch(`${API_BASE}/users.php`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'include',
        body: JSON.stringify({
          action: 'updateProfile',
          nom: updatedData.lastName || updatedData.nom || '',
          prenom: updatedData.firstName || updatedData.prenom || '',
          telephone: updatedData.phone || updatedData.telephone || '',
          adresse: updatedData.address || updatedData.adresse || ''
        })
      });

      const data = await response.json();

      if (data.success) {
        // Mettre à jour la session locale
        const user = this.getCurrentUser();
        const updatedUser = { ...user, ...data.user };
        localStorage.setItem(this.currentUserKey, JSON.stringify(updatedUser));
        return { success: true, message: data.message, user: updatedUser };
      } else {
        return { success: false, message: data.error || 'Erreur lors de la mise à jour' };
      }
    } catch (error) {
      console.error('Erreur mise à jour profil:', error);
      return { success: false, message: 'Erreur de connexion au serveur' };
    }
  }

  // Ajouter une réservation
  async addReservation(userId, reservation) {
    try {
      const response = await fetch(`${API_BASE}/reservations.php`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'include',
        body: JSON.stringify({
          action: 'create',
          pharmacie_id: reservation.pharmacie_id || reservation.pharmacyId,
          medicament_id: reservation.medicament_id || reservation.medicamentId,
          quantite: reservation.quantite || reservation.quantity || 1
        })
      });

      const data = await response.json();

      if (data.success) {
        return { success: true, message: data.message, reservation: data };
      } else {
        return { success: false, message: data.error || 'Erreur lors de la réservation' };
      }
    } catch (error) {
      console.error('Erreur réservation:', error);
      return { success: false, message: 'Erreur de connexion au serveur' };
    }
  }

  // Obtenir les réservations de l'utilisateur
  async getUserReservations(userId) {
    try {
      const response = await fetch(`${API_BASE}/reservations.php?action=getMyReservations`, {
        method: 'GET',
        credentials: 'include'
      });

      const data = await response.json();
      return data.success ? data.reservations || [] : [];
    } catch (error) {
      console.error('Erreur récupération réservations:', error);
      return [];
    }
  }

  // Annuler une réservation
  async cancelReservation(userId, reservationId) {
    try {
      const response = await fetch(`${API_BASE}/reservations.php`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'include',
        body: JSON.stringify({
          action: 'cancel',
          reservation_id: reservationId
        })
      });

      const data = await response.json();

      if (data.success) {
        return { success: true, message: data.message };
      } else {
        return { success: false, message: data.error || 'Erreur lors de l\'annulation' };
      }
    } catch (error) {
      console.error('Erreur annulation réservation:', error);
      return { success: false, message: 'Erreur de connexion au serveur' };
    }
  }

  // Obtenir le profil utilisateur
  async getProfile() {
    try {
      const response = await fetch(`${API_BASE}/users.php?action=getProfile`, {
        method: 'GET',
        credentials: 'include'
      });

      const data = await response.json();
      return data.success ? data.user : null;
    } catch (error) {
      console.error('Erreur récupération profil:', error);
      return null;
    }
  }


  // Changer le mot de passe
  changePassword(userId, oldPassword, newPassword) {
    const users = this.getAllUsers();
    const user = users.find(u => u.id === userId);

    if (!user) {
      return { success: false, message: 'Utilisateur non trouvé.' };
    }

    if (user.password !== this.hashPassword(oldPassword)) {
      return { success: false, message: 'Ancien mot de passe incorrect.' };
    }

    user.password = this.hashPassword(newPassword);
    localStorage.setItem(this.storageKey, JSON.stringify(users));

    return { success: true, message: 'Mot de passe changé avec succès!' };
  }
}

// Créer une instance globale
const authManager = new AuthManager();

// Fonction utilitaire pour vérifier si l'utilisateur est connecté
function requireLogin() {
  if (!authManager.isLoggedIn()) {
    showAlert('danger', 'Vous devez être connecté pour accéder à cette page.');
    setTimeout(() => {
      window.location.href = 'connexion.html';
    }, 1500);
    return false;
  }
  return true;
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
