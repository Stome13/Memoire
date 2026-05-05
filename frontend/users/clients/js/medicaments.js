// ========================================
// GESTION DES MÉDICAMENTS
// ========================================

// Variables globales (déclarées dans script.js)
// Nous allons les utiliser et les mettre à jour depuis l'API

/**
 * Charger les médicaments depuis l'API
 */
async function loadMedicinesFromAPI() {
  try {
    const response = await fetch('/PharmaLocal/backend/api/medicaments.php?action=list');
    const result = await response.json();
    
    if (result.success && result.data && Array.isArray(result.data)) {
      // Mettre à jour la variable globale MEDICINES_DATA
      MEDICINES_DATA.length = 0; // Vider le tableau
      
      result.data.forEach(med => {
        MEDICINES_DATA.push({
          id: med.id,
          name: med.nom,
          dosage: med.dosage,
          description: med.description,
          price: med.prix || '0',
          category: med.categorie,
          quantity: med.quantite || 0,
          inStock: (med.quantite || 0) > 0,
          pharmacies: []
        });
      });
      
      // Charger les pharmacies si nécessaire
      await loadPharmaciesFromAPI();
      
      // Charger les médicaments populaires
      loadPopularMedicines();
    } else {
      console.warn('Erreur API médicaments ou aucune donnée:', result.error);
      // Utiliser les données mock existantes
      loadPopularMedicines();
    }
  } catch (error) {
    console.error('Erreur chargement médicaments:', error);
    // Utiliser les données mock existantes
    loadPopularMedicines();
  }
}

/**
 * Charger les pharmacies depuis l'API
 */
async function loadPharmaciesFromAPI() {
  try {
    const response = await fetch('/PharmaLocal/backend/api/pharmacies.php?action=list');
    const result = await response.json();
    
    if (result.success && (result.data || result.pharmacies)) {
      const data = result.data || result.pharmacies;
      
      // Mettre à jour la variable globale PHARMACIES_DATA
      PHARMACIES_DATA.length = 0; // Vider le tableau
      
      data.forEach(pharm => {
        PHARMACIES_DATA.push({
          id: pharm.id,
          name: pharm.nom,
          address: pharm.adresse || 'Adresse non disponible',
          phone: pharm.telephone || 'Téléphone non disponible',
          hours: pharm.horaire_ouverture || '24h/24'
        });
      });
    }
  } catch (error) {
    console.error('Erreur chargement pharmacies:', error);
  }
}

/**
 * Charger les médicaments populaires
 */
function loadPopularMedicines() {
  const container = document.getElementById('popularMedicines');
  if (!container) return;

  if (MEDICINES_DATA.length === 0) {
    container.innerHTML = '<p class="text-muted">Aucun médicament disponible</p>';
    return;
  }

  const medicines = MEDICINES_DATA.slice(0, 6);
  container.innerHTML = medicines.map(medicine => createMedicineCard(medicine)).join('');
  
  // Ajouter les écouteurs d'événements après la création des cartes
  attachReservationListeners();
}

/**
 * Rechercher des médicaments
 */
function searchMedicines(query) {
  const resultsContainer = document.getElementById('medicamentos-results');
  if (!resultsContainer) return;

  if (!query || query.trim() === '') {
    resultsContainer.innerHTML = '';
    return;
  }

  const filtered = MEDICINES_DATA.filter(medicine =>
    medicine.name.toLowerCase().includes(query.toLowerCase()) ||
    (medicine.dosage && medicine.dosage.toLowerCase().includes(query.toLowerCase())) ||
    (medicine.category && medicine.category.toLowerCase().includes(query.toLowerCase()))
  );

  if (filtered.length === 0) {
    resultsContainer.innerHTML = `
      <div class="alert alert-warning">
        <i class="fas fa-info-circle me-2"></i>
        Aucun médicament trouvé pour "<strong>${query}</strong>"
      </div>
    `;
  } else {
    resultsContainer.innerHTML = `
      <h4 class="fw-bold mb-4">Résultats pour "${query}"</h4>
      <div class="row gy-4">
        ${filtered.map(medicine => createMedicineCard(medicine)).join('')}
      </div>
    `;
  }
  
  // Ajouter les écouteurs d'événements après la création des cartes
  attachReservationListeners();
}

/**
 * Créer une carte de médicament
 */
function createMedicineCard(medicine) {
  const stockStatus = medicine.inStock
    ? '<span class="badge bg-success">En stock</span>'
    : '<span class="badge bg-danger">Rupture</span>';

  const priceDisplay = typeof medicine.price === 'string' ? medicine.price : `${parseFloat(medicine.price).toFixed(2)} €`;

  return `
    <div class="col-md-6 col-lg-4">
      <div class="card h-100 shadow-sm">
        <div class="card-body">
          <h5 class="card-title">${medicine.name}</h5>
          <p class="card-text text-muted">${medicine.dosage || 'Dosage non spécifié'}</p>
          <p class="card-text small">${medicine.category || 'Catégorie non spécifiée'}</p>
          ${medicine.description ? `<p class="card-text small">${medicine.description}</p>` : ''}
          <div class="mb-2">
            ${stockStatus}
          </div>
          <p class="card-text fw-bold text-primary">${priceDisplay}</p>
        </div>
        <div class="card-footer bg-white">
          <button class="btn btn-sm btn-primary w-100 reserve-medicine-btn" data-medicine-id="${medicine.id}">
            <i class="fas fa-shopping-cart me-1"></i>Réserver
          </button>
        </div>
      </div>
    </div>
  `;
}

/**
 * Attacher les écouteurs d'événements aux boutons de réservation
 */
function attachReservationListeners() {
  const reserveButtons = document.querySelectorAll('.reserve-medicine-btn');
  reserveButtons.forEach(button => {
    // Supprimer les écouteurs existants pour éviter les doublons
    const newButton = button.cloneNode(true);
    button.parentNode.replaceChild(newButton, button);
    
    // Ajouter le nouvel écouteur
    newButton.addEventListener('click', function() {
      const medicineId = parseInt(this.dataset.medicineId);
      openReservationModal(medicineId);
    });
  });
}

/**
 * Ouvrir le modal de réservation
 */
function openReservationModal(medicineId) {
  const medicine = MEDICINES_DATA.find(m => m.id === medicineId);
  if (!medicine) {
    alert('Médicament non trouvé');
    return;
  }

  const pharmacyOptions = PHARMACIES_DATA.length > 0 
    ? PHARMACIES_DATA.map(p => `<option value="${p.id}">${p.name} — ${p.address.split(',')[0] || p.address}</option>`).join('')
    : '<option value="">Aucune pharmacie disponible</option>';

  const modalHTML = `
    <div class="modal fade" id="reservationModal" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered" style="max-width: 500px;">
        <div class="modal-content border-0 shadow-lg">
          <div class="modal-header border-0 pb-0 bg-light">
            <h5 class="modal-title fw-bold">Réserver un médicament</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <p class="text-muted small mb-4">Complétez votre réservation pour <strong>${medicine.name}</strong></p>
            
            <!-- Medicine Info -->
            <div class="bg-light p-3 rounded mb-4">
              <div class="mb-2">
                <h6 class="fw-bold mb-0">${medicine.name}</h6>
                <p class="text-muted small mb-0">Dosage: ${medicine.dosage || 'N/A'}</p>
              </div>
              <p class="text-muted small mb-0">
                <i class="fas fa-map-marker-alt me-1"></i>
                <span id="selected-pharmacy-display">Sélectionnez une pharmacie</span>
              </p>
            </div>

            <form id="reservationForm" class="needs-validation" novalidate>
              <!-- Pharmacy Selection -->
              <div class="mb-4">
                <label for="pharmacy-select" class="form-label fw-bold">Pharmacie</label>
                <select class="form-select" id="pharmacy-select" required>
                  <option value="">Sélectionnez une pharmacie</option>
                  ${pharmacyOptions}
                </select>
              </div>

              <!-- Quantity -->
              <div class="mb-4">
                <label for="qty" class="form-label fw-bold">Quantité</label>
                <div class="input-group">
                  <button type="button" class="btn btn-outline-secondary" id="qty-minus">−</button>
                  <input type="number" class="form-control text-center" id="qty" min="1" max="80" value="1" required />
                  <button type="button" class="btn btn-outline-secondary" id="qty-plus">+</button>
                </div>
                <small class="text-muted">max: 80</small>
              </div>

              <!-- Full Name -->
              <div class="mb-4">
                <label for="fullname" class="form-label fw-bold">Nom complet</label>
                <input type="text" class="form-control" id="fullname" placeholder="Votre nom" required />
              </div>

              <!-- Phone -->
              <div class="mb-4">
                <label for="phone" class="form-label fw-bold">Téléphone</label>
                <input type="tel" class="form-control" id="phone" placeholder="+229 XX XX XX XX" required />
              </div>

              <!-- Total Price -->
              <div class="bg-light p-3 rounded mb-4">
                <div class="d-flex justify-content-between align-items-center">
                  <span class="text-muted">Prix unitaire:</span>
                  <span class="fw-bold text-primary">${medicine.price}</span>
                </div>
                <hr class="my-2">
                <div class="d-flex justify-content-between align-items-center">
                  <span class="text-muted fw-bold">Total:</span>
                  <span class="fs-5 fw-bold text-success" id="total-price">${medicine.price}</span>
                </div>
              </div>

              <button type="submit" class="btn btn-success btn-lg w-100">
                <i class="fas fa-check me-2"></i>Confirmer la réservation
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  `;

  // Supprimer le modal existant s'il existe
  const existing = document.getElementById('reservationModal');
  if (existing) existing.remove();

  // Créer et afficher le nouveau modal
  document.body.insertAdjacentHTML('beforeend', modalHTML);
  const modal = new bootstrap.Modal(document.getElementById('reservationModal'));
  modal.show();

  // Configurer les contrôles du modal
  setupReservationModal(medicine, modal);
}

/**
 * Configurer le modal de réservation
 */
function setupReservationModal(medicine, modal) {
  const qtyInput = document.getElementById('qty');
  const qtyMinus = document.getElementById('qty-minus');
  const qtyPlus = document.getElementById('qty-plus');
  const totalPrice = document.getElementById('total-price');
  const pharmacySelect = document.getElementById('pharmacy-select');
  const selectedPharmacyDisplay = document.getElementById('selected-pharmacy-display');
  const reservationForm = document.getElementById('reservationForm');

  // Extraire la valeur du prix (support pour CFA et €)
  let priceValue = 0;
  const priceStr = String(medicine.price);
  const priceMatch = priceStr.match(/[\d.,]+/);
  if (priceMatch) {
    priceValue = parseFloat(priceMatch[0].replace(/\s/g, '').replace(',', '.'));
  }

  // Bouton moins
  qtyMinus.addEventListener('click', function (e) {
    e.preventDefault();
    const currentQty = parseInt(qtyInput.value);
    if (currentQty > 1) {
      qtyInput.value = currentQty - 1;
      updateTotal();
    }
  });

  // Bouton plus
  qtyPlus.addEventListener('click', function (e) {
    e.preventDefault();
    const currentQty = parseInt(qtyInput.value);
    if (currentQty < 80) {
      qtyInput.value = currentQty + 1;
      updateTotal();
    }
  });

  // Changer la quantité
  qtyInput.addEventListener('change', updateTotal);

  // Mettre à jour le prix total
  function updateTotal() {
    const qty = parseInt(qtyInput.value) || 1;
    const total = priceValue * qty;
    const priceStr = String(medicine.price);
    
    if (priceStr.includes('CFA')) {
      totalPrice.textContent = total.toLocaleString('fr-FR') + ' CFA';
    } else {
      totalPrice.textContent = total.toFixed(2) + ' €';
    }
  }

  // Changer la pharmacie
  pharmacySelect.addEventListener('change', function () {
    const selected = this.options[this.selectedIndex];
    if (selected.value) {
      const pharmacy = PHARMACIES_DATA.find(p => p.id == selected.value);
      if (pharmacy) {
        selectedPharmacyDisplay.textContent = pharmacy.name;
      }
    } else {
      selectedPharmacyDisplay.textContent = 'Sélectionnez une pharmacie';
    }
  });

  // Soumettre le formulaire
  reservationForm.addEventListener('submit', function (e) {
    e.preventDefault();
    
    const fullname = document.getElementById('fullname').value;
    const phone = document.getElementById('phone').value;
    const qty = document.getElementById('qty').value;
    const pharmacyId = document.getElementById('pharmacy-select').value;

    if (!fullname || !phone || !qty || !pharmacyId) {
      alert('Veuillez remplir tous les champs');
      return;
    }

    // Afficher un message de succès
    alert('Réservation effectuée avec succès !\n\nMédicament: ' + medicine.name + '\nQuantité: ' + qty + '\nNom: ' + fullname);
    modal.hide();
    
    // Réinitialiser le formulaire
    reservationForm.reset();
  });
}

/**
 * Initialiser au chargement de la page
 */
document.addEventListener('DOMContentLoaded', function () {
  // Charger les médicaments
  loadMedicinesFromAPI();

  // Gérer la recherche
  const searchBtn = document.getElementById('search-btn-med');
  const searchInput = document.getElementById('med-search');
  
  if (searchBtn && searchInput) {
    searchBtn.addEventListener('click', () => {
      const query = searchInput.value;
      searchMedicines(query);
    });
    
    searchInput.addEventListener('keypress', (e) => {
      if (e.key === 'Enter') {
        const query = searchInput.value;
        searchMedicines(query);
      }
    });
  }
});
