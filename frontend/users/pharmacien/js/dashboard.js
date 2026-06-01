// ========================================
// PHARMACIEN DASHBOARD - JavaScript
// Load data from API instead of mock data
// ========================================

let MEDICINES_DATA = [];

// Initialize on page load
document.addEventListener('DOMContentLoaded', function () {
  initializeDashboard();
});

// Initialize Dashboard
async function initializeDashboard() {
  // Load medicines from API
  await loadMedicinesFromAPI();
  
  // Load dashboard stats if on dashboard page
  if (document.getElementById('total-medicines')) {
    loadDashboardStats();
  }
}

// Load Medicines from API
async function loadMedicinesFromAPI() {
  try {
    const response = await fetch('/PharmaLocal/backend/api/medicaments.php?action=list');
    const result = await response.json();
    
    if (result.success && result.data) {
      MEDICINES_DATA = result.data;
    }
  } catch (error) {
    console.error('Erreur chargement médicaments:', error);
  }
}

// Load Dashboard Stats
function loadDashboardStats() {
  // Total medicines
  const totalMedicines = MEDICINES_DATA.length;
  document.getElementById('total-medicines').textContent = totalMedicines;

  // Pending reservations (mock)
  document.getElementById('pending-reservations').textContent = '0';

  // Low stock alerts (mock)
  document.getElementById('low-stock-alerts').textContent = '0';

  // Revenue (mock)
  document.getElementById('revenue-today').textContent = '0 €';

  // Load recent reservations
  loadRecentReservations();
}

// Load Recent Reservations
function loadRecentReservations() {
  const container = document.getElementById('recentReservations');
  if (!container || !container.querySelector('tbody')) return;

  container.querySelector('tbody').innerHTML = 
    <tr>
      <td colspan="5" class="text-center text-muted">Aucune réservation récente</td>
    </tr>
  ;
}
