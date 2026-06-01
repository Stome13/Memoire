// ========================================
// PHARMACIEN STOCKS - JavaScript
// Load and display inventory from API
// ========================================

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

let STOCKS_DATA = [];
let PHARMACIE_ID = null;

// Initialize on page load
document.addEventListener('DOMContentLoaded', function () {
  loadStocks();
});

// Load Stocks from API
async function loadStocks() {
  try {
    const response = await fetch('/PharmaLocal/backend/api/stocks.php?action=current');
    const result = await response.json();
    
    if (result.success) {
      STOCKS_DATA = result.data;
      PHARMACIE_ID = result.pharmacie_id;
      
      if (STOCKS_DATA.length === 0) {
        displayMessage('Aucun stock enregistré pour votre pharmacie');
      } else {
        displayStocks();
      }
    } else {
      displayMessage('Erreur: ' + (result.message || 'Impossible de charger les stocks'));
    }
  } catch (error) {
    console.error('Erreur chargement stocks:', error);
    displayMessage('Erreur de chargement: ' + error.message);
  }
}

// Display Stocks in Table
function displayStocks() {
  const tbody = document.querySelector('table tbody');
  if (!tbody) return;

  tbody.innerHTML = STOCKS_DATA.map(stock => `
    <tr>
      <td><strong>${stock.nom}</strong></td>
      <td><input type="number" class="form-control form-control-sm" value="${stock.quantite}" min="0" onchange="updateStock(${stock.medicament_id}, this.value)"></td>
      <td>50</td>
      <td>
        <span class="badge bg-${stock.quantite > 50 ? 'success' : stock.quantite > 0 ? 'warning' : 'danger'}">
          ${stock.quantite > 50 ? 'OK' : stock.quantite > 0 ? 'ALERTE' : 'RUPTURE'}
        </span>
      </td>
      <td>
        <button class="btn btn-sm btn-success" onclick="increaseStock(${stock.medicament_id}, 10)" title="Ajouter 10">
          <i class="fas fa-plus"></i>
        </button>
        <button class="btn btn-sm btn-danger" onclick="decreaseStock(${stock.medicament_id}, 10)" title="Retirer 10">
          <i class="fas fa-minus"></i>
        </button>
      </td>
    </tr>
  `).join('');

  // Update alerts count
  const lowStockCount = STOCKS_DATA.filter(stock => stock.quantite < 50).length;
  updateAlertCount(lowStockCount);
}

// Update Alert Count
function updateAlertCount(count) {
  const alertDiv = document.querySelector('.alert-warning');
  if (alertDiv) {
    alertDiv.innerHTML = `
      <i class="fas fa-exclamation-triangle me-2"></i>
      <strong>Alertes stock faible:</strong> ${count} médicament(s) avec stock faible (< 50 unités)
    `;
  }
}

// Update Stock
async function updateStock(medicamentId, newQuantite) {
  try {
    const response = await fetch('/PharmaLocal/backend/api/stocks.php?action=create', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: new URLSearchParams({
        pharmacie_id: PHARMACIE_ID,
        medicament_id: medicamentId,
        quantite: newQuantite
      })
    });
    const result = await response.json();
    
    if (result.success) {
      loadStocks();
    } else {
      showToast('danger', 'Erreur: ' + result.error);
      loadStocks(); // Recharger pour corriger l'affichage
    }
  } catch (error) {
    console.error('Erreur mise à jour stock:', error);
    showToast('danger', 'Erreur: ' + error.message);
  }
}

// Increase Stock
async function increaseStock(medicamentId, amount) {
  try {
    const response = await fetch('/PharmaLocal/backend/api/stocks.php?action=increase', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: new URLSearchParams({
        pharmacie_id: PHARMACIE_ID,
        medicament_id: medicamentId,
        quantite: amount
      })
    });
    const result = await response.json();
    
    if (result.success) {
      loadStocks(); // Recharger
    } else {
      showToast('danger', 'Erreur: ' + result.error);
    }
  } catch (error) {
    console.error('Erreur augmentation stock:', error);
    showToast('danger', 'Erreur: ' + error.message);
  }
}

// Decrease Stock
async function decreaseStock(medicamentId, amount) {
  try {
    const response = await fetch('/PharmaLocal/backend/api/stocks.php?action=decrease', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: new URLSearchParams({
        pharmacie_id: PHARMACIE_ID,
        medicament_id: medicamentId,
        quantite: amount
      })
    });
    const result = await response.json();
    
    if (result.success) {
      loadStocks(); // Recharger
    } else {
      showToast('danger', 'Erreur: ' + result.error);
    }
  } catch (error) {
    console.error('Erreur réduction stock:', error);
    showToast('danger', 'Erreur: ' + error.message);
  }
}

// Display Message
function displayMessage(message) {
  const tbody = document.querySelector('table tbody');
  if (tbody) {
    tbody.innerHTML = `<tr><td colspan="5" class="text-center text-muted">${message}</td></tr>`;
  }
}
