// Pharmacy management system - Authentication and role verification

// ═══════════════════════════════════════════════════════════════════════════
// 📋 AUTHENTICATION CHECK
// ═══════════════════════════════════════════════════════════════════════════

document.addEventListener('DOMContentLoaded', function () {
  // Get current user from localStorage
  const currentUserStr = localStorage.getItem('pharmaLocal_currentUser');
  
  // If no user or wrong role, redirect to login
  if (!currentUserStr) {
    redirectToLogin('Session expired. Please log in again.');
    return;
  }
  
  try {
    const currentUser = JSON.parse(currentUserStr);
    
    // Only allow users with role 'pharmacie'
    if (!currentUser.role || currentUser.role !== 'pharmacie') {
      redirectToLogin('Access denied. This page is for pharmacy staff only.');
      return;
    }
    
    // User is authenticated and has correct role
    console.log('Pharmacy user authenticated:', currentUser.email);
  } catch (error) {
    console.error('Error parsing user data:', error);
    redirectToLogin('Authentication error. Please log in again.');
  }
});

/**
 * Redirect to login page with optional message
 */
function redirectToLogin(message) {
  if (message) {
    sessionStorage.setItem('redirectMessage', message);
  }
  // ✅ ULTRA SIMPLE : Même logique que auth.js
  const pathSegments = window.location.pathname.split('/').filter(x => x);
  const APP_BASE = pathSegments.length > 0 ? '/' + pathSegments[0] : '';
  const BASE_URL = window.location.origin + APP_BASE;
  window.location.href = BASE_URL + '/frontend/users/clients/connexion.html';
}

/**
 * Pharmacy API fetch wrapper with authentication
 */
async function pharmacyApiFetch(endpoint, options = {}) {
  // ✅ ULTRA SIMPLE : Même logique que auth.js
  const pathSegments = window.location.pathname.split('/').filter(x => x);
  const APP_BASE = pathSegments.length > 0 ? '/' + pathSegments[0] : '';
  const API_BASE = window.location.origin + APP_BASE + '/backend/api';
  
  const url = `${API_BASE}/${endpoint}`;
  const currentUserStr = localStorage.getItem('pharmaLocal_currentUser');
  
  if (!currentUserStr) {
    throw new Error('Not authenticated');
  }
  
  const currentUser = JSON.parse(currentUserStr);
  
  // Prepare headers with authentication
  const headers = {
    'Content-Type': 'application/json',
    ...options.headers
  };
  
  // Add user ID and role to request
  if (options.method !== 'GET') {
    headers['X-User-ID'] = currentUser.id || '';
    headers['X-User-Role'] = currentUser.role || '';
  }
  
  try {
    const response = await fetch(url, {
      ...options,
      headers
    });
    
    // Check for authentication errors
    if (response.status === 401 || response.status === 403) {
      redirectToLogin('Session expired or access denied.');
      return;
    }
    
    if (!response.ok) {
      const errorData = await response.json().catch(() => ({}));
      throw new Error(errorData.message || `HTTP Error ${response.status}`);
    }
    
    // Parse JSON response
    const data = await response.json();
    return data;
  } catch (error) {
    console.error('API Error:', error);
    throw error;
  }
}

/**
 * Logout user and redirect to login
 */
function logoutPharmacy() {
  // ✅ CONFIRM EXPLICITE - Si l'utilisateur clique "Annulé", rien ne se passe
  if (!confirm('Êtes-vous sûr de vouloir vous déconnecter?')) {
    console.log('[logoutPharmacy] Logout cancelled by user');
    return; // ⚠️ IMPORTANT: Retourner immédiatement si l'utilisateur clique Annulé
  }
  
  console.log('[logoutPharmacy] Logout confirmed, clearing data...');
  
  // ✅ Vider localStorage
  localStorage.removeItem('pharmaLocal_currentUser');
  localStorage.removeItem('pharmaLocal_rememberMe');
  sessionStorage.removeItem('redirectMessage');
  
  console.log('[logoutPharmacy] localStorage cleared');
  
  // Appeler l'API logout EN ARRIÈRE-PLAN (asynchrone, sans attendre)
  const pathSegments = window.location.pathname.split('/').filter(x => x);
  const APP_BASE = pathSegments.length > 0 ? '/' + pathSegments[0] : '';
  
  fetch(window.location.origin + APP_BASE + '/backend/api/auth.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    credentials: 'include',
    body: JSON.stringify({ action: 'logout' })
  }).catch(err => {
    console.log('[logoutPharmacy] API logout failed (non-critical):', err);
  });
  
  // ✅ Redirection DIRECTE et SIMPLE
  // On est dans domain/PharmaLocal/frontend/users/pharmacien/
  // Accès relatif: ../../clients/connexion.html
  console.log('[logoutPharmacy] Redirecting to login page...');
  window.location.href = '../../clients/connexion.html';
}

// Export functions for global use
window.pharmacyApiFetch = pharmacyApiFetch;
window.logoutPharmacy = logoutPharmacy;
