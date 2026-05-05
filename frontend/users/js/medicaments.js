

function loadPopularMedicines() {
  const container = document.getElementById('popularMedicines');
  if (!container) return;

  const medicines = MEDICINES_DATA.slice(0, 6);
  container.innerHTML = medicines.map(medicine => createMedicineCard(medicine)).join('');
}

function searchMedicines(query) {
  const resultsContainer = document.getElementById('medicamentos-results');
  if (!resultsContainer) return;

  if (!query || query.trim() === '') {
    resultsContainer.innerHTML = '';
    return;
  }

  const filtered = MEDICINES_DATA.filter(medicine =>
    medicine.name.toLowerCase().includes(query.toLowerCase()) ||
    medicine.dosage.toLowerCase().includes(query.toLowerCase())
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
}

function createMedicineDetailCard(medicine) {
  const stockStatus = medicine.inStock
    ? '<span class="badge badge-available">En stock</span>'
    : '<span class="badge bg-danger">Rupture</span>';

  const pharmacyNames = medicine.pharmacies.map(id => {
    const pharmacy = PHARMACIES_DATA.find(p => p.id === id);
    return pharmacy ? pharmacy.name : '';
  }).filter(n => n);

  return `
    <div class="card border-0 shadow-lg p-4">
      <div class="row">
        <div class="col-lg-8">
          <h3 class="fw-bold mb-2">${medicine.name}</h3>
          <p class="text-muted mb-4">${medicine.dosage}</p>

          <div class="mb-4">
            <h5 class="fw-bold mb-3">À propos du médicament</h5>
            <p class="text-muted">
              ${medicine.name} est un médicament couramment utilisé pour traiter diverses conditions.
              Consultez votre pharmacien ou médecin pour plus d'informations.
            </p>
          </div>

          <div class="mb-4">
            <h5 class="fw-bold mb-3">Disponibilité</h5>
            <p>Disponible dans ${medicine.pharmacies.length} pharmacie(s):</p>
            <ul class="list-unstyled">
              ${pharmacyNames.map(name => `<li class="mb-2"><i class="fas fa-check text-success me-2"></i>${name}</li>`).join('')}
            </ul>
          </div>
        </div>

        <div class="col-lg-4">
          <div class="card bg-light border-0">
            <div class="card-body">
              <h5 class="fw-bold mb-3">Détails</h5>
              
              <div class="mb-3">
                <strong>Prix:</strong> 
                <span class="fs-5 text-primary fw-bold">${medicine.price}</span>
              </div>

              <div class="mb-3">
                <strong>Dosage:</strong> ${medicine.dosage}
              </div>

              <div class="mb-4">
                <strong>Stock:</strong>
                ${stockStatus}
              </div>

              <button class="btn btn-primary btn-lg w-100 mb-2">
                <i class="fas fa-shopping-cart me-2"></i>Commander
              </button>

              <button class="btn btn-outline-primary btn-lg w-100">
                <i class="fas fa-bell me-2"></i>Notification de stock
              </button>

              <small class="d-block mt-3 text-muted">
                Consulter un pharmacien avant utilisation
              </small>
            </div>
          </div>
        </div>
      </div>
    </div>
  `;
}

function filterMedicinesByStock(inStockOnly) {
  const container = document.getElementById('medicamentos-results');
  if (!container) return;

  let filtered = MEDICINES_DATA;

  if (inStockOnly) {
    filtered = filtered.filter(m => m.inStock);
  }

  if (filtered.length === 0) {
    container.innerHTML = `
      <div class="alert alert-warning">
        <i class="fas fa-info-circle me-2"></i>
        Aucun médicament disponible
      </div>
    `;
  } else {
    const query = document.getElementById('med-search').value;
    const title = query ? `Résultats pour "${query}"` : 'Tous les médicaments';
    
    container.innerHTML = `
      <h4 class="fw-bold mb-4">${title}</h4>
      <div class="row gy-4">
        ${filtered.map(medicine => createMedicineCard(medicine)).join('')}
      </div>
    `;
  }
}

function setupMedicineSearchAutocomplete() {
  const searchInput = document.getElementById('med-search');
  if (!searchInput) return;

  const medicineNames = MEDICINES_DATA.map(m => m.name);

  searchInput.addEventListener('input', function (e) {
    const query = e.target.value.toLowerCase();
    
    if (query.length === 0) {
      return;
    }

    const suggestions = medicineNames.filter(name =>
      name.toLowerCase().includes(query)
    );

    // Simple implementation - could be enhanced with a dropdown
    console.log('Suggestions:', suggestions);
  });
}

// Reservation modal
function openReservationModal(medicineId) {
  const medicine = MEDICINES_DATA.find(m => m.id === medicineId);
  if (!medicine) return;

  const modalHTML = `
    <div class="modal fade" id="reservationModal" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered" style="max-width: 450px;">
        <div class="modal-content border-0 shadow-lg">
          <div class="modal-header border-0 pb-0">
            <h5 class="modal-title fw-bold">Réserver un produit</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <p class="text-muted small mb-4">Remplissez les informations pour réserver ce produit.</p>
            
            <!-- Medicine Info -->
            <div class="bg-light p-3 rounded mb-4">
              <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                  <h6 class="fw-bold mb-0">${medicine.name}</h6>
                  <p class="text-muted small mb-0">${medicine.dosage}</p>
                </div>
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
                  ${medicine.pharmacies.map(id => {
                    const pharmacy = PHARMACIES_DATA.find(p => p.id === id);
                    return `<option value="${id}" data-pharmacy-name="${pharmacy.name}" data-pharmacy-address="${pharmacy.address}">${pharmacy.name} — ${pharmacy.address.split(',')[0]}</option>`;
                  }).join('')}
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
                  <span class="text-muted">Total:</span>
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

  const existing = document.getElementById('reservationModal');
  if (existing) existing.remove();

  document.body.insertAdjacentHTML('beforeend', modalHTML);
  const modal = new bootstrap.Modal(document.getElementById('reservationModal'));
  modal.show();

  // Setup quantity controls
  const qtyInput = document.getElementById('qty');
  const qtyMinus = document.getElementById('qty-minus');
  const qtyPlus = document.getElementById('qty-plus');
  const totalPrice = document.getElementById('total-price');
  const pharmacySelect = document.getElementById('pharmacy-select');
  const selectedPharmacyDisplay = document.getElementById('selected-pharmacy-display');

  // Extract price value
  const priceValue = parseFloat(medicine.price.replace(' CFA', '').replace(/\s/g, ''));

  // Quantity minus button
  qtyMinus.addEventListener('click', function () {
    const currentQty = parseInt(qtyInput.value);
    if (currentQty > 1) {
      qtyInput.value = currentQty - 1;
      updateTotal();
    }
  });

  // Quantity plus button
  qtyPlus.addEventListener('click', function () {
    const currentQty = parseInt(qtyInput.value);
    if (currentQty < 80) {
      qtyInput.value = currentQty + 1;
      updateTotal();
    }
  });

  // Update total price
  function updateTotal() {
    const qty = parseInt(qtyInput.value);
    const total = priceValue * qty;
    totalPrice.textContent = total.toLocaleString('fr-FR') + ' CFA';
  }

  // Update pharmacy display
  pharmacySelect.addEventListener('change', function () {
    const selected = this.options[this.selectedIndex];
    if (selected.value) {
      const pharmacyName = selected.dataset.pharmacyName;
      const pharmacyAddress = selected.dataset.pharmacyAddress.split(',')[0]; // Get city
      selectedPharmacyDisplay.textContent = pharmacyName + ' — ' + pharmacyAddress;
    } else {
      selectedPharmacyDisplay.textContent = 'Sélectionnez une pharmacie';
    }
  });

  // Form submission
  document.getElementById('reservationForm').addEventListener('submit', function (e) {
    e.preventDefault();
    
    const fullname = document.getElementById('fullname').value;
    const phone = document.getElementById('phone').value;
    const qty = document.getElementById('qty').value;
    const pharmacy = document.getElementById('pharmacy-select').value;

    if (!fullname || !phone || !qty || !pharmacy) {
      alert('Veuillez remplir tous les champs');
      return;
    }

    // Show success message
    showSuccessAlert('Réservation effectuée avec succès !');
    modal.hide();
  });
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function () {
  if (document.getElementById('popularMedicines')) {
    loadPopularMedicines();
  }

  setupMedicineSearchAutocomplete();

  const searchBtn = document.getElementById('search-btn-med');
  if (searchBtn) {
    searchBtn.addEventListener('click', function () {
      const query = document.getElementById('med-search').value;
      searchMedicines(query);
    });

    document.getElementById('med-search').addEventListener('keypress', function (e) {
      if (e.key === 'Enter') {
        searchBtn.click();
      }
    });
  }

  // Delegate click for reserve buttons
  document.addEventListener('click', function (e) {
    if (e.target.closest('[data-action="reserve"]')) {
      const medicineId = parseInt(e.target.closest('[data-action="reserve"]').dataset.medicineId);
      openReservationModal(medicineId);
    }
  });
});
