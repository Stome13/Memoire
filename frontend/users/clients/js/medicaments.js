// ========================================
// GESTION DU PANIER DE RÉSERVATION
// ========================================

// Initialiser le panier depuis localStorage
let cartItems = JSON.parse(localStorage.getItem('medicineCart')) || [];

/**
 * Ajouter un article au panier
 */
function addToCart(medicineId, pharmacyId, medicineName, pharmacyName, quantity) {
  // Vérifier si l'article existe déjà
  const existingItem = cartItems.find(item => 
    item.medicineId === medicineId && item.pharmacyId === pharmacyId
  );

  if (existingItem) {
    existingItem.quantity += parseInt(quantity);
  } else {
    cartItems.push({
      medicineId: medicineId,
      pharmacyId: pharmacyId,
      medicineName: medicineName,
      pharmacyName: pharmacyName,
      quantity: parseInt(quantity)
    });
  }

  // Sauvegarder dans localStorage
  localStorage.setItem('medicineCart', JSON.stringify(cartItems));
  
  // Mettre à jour le badge du panier
  updateCartBadge();
  
  // Afficher une notification
  showCartNotification();
}

/**
 * Mettre à jour le badge du panier
 */
function updateCartBadge() {
  const badge = document.getElementById('cart-badge');
  const totalItems = cartItems.reduce((sum, item) => sum + item.quantity, 0);
  
  if (badge) {
    if (totalItems > 0) {
      badge.textContent = totalItems;
      badge.style.display = 'block';
    } else {
      badge.style.display = 'none';
    }
  }
}

/**
 * Afficher une notification de panier
 */
function showCartNotification() {
  const notification = document.createElement('div');
  notification.className = 'cart-notification';
  notification.innerHTML = `
    <i class="fas fa-check-circle me-2"></i>
    Médicament ajouté au panier!
  `;
  document.body.appendChild(notification);
  
  setTimeout(() => {
    notification.classList.add('show');
  }, 10);
  
  setTimeout(() => {
    notification.classList.remove('show');
    setTimeout(() => notification.remove(), 300);
  }, 3000);
}

/**
 * Afficher le modal pour ajouter au panier
 */
function openAddToCartModal(medicineId, pharmacyId, medicineName, pharmacyName) {
  const modalHTML = `
    <div class="modal fade" id="addToCartModal" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered" style="max-width: 450px;">
        <div class="modal-content border-0 shadow-lg">
          <div class="modal-header border-0 pb-0 bg-light">
            <h5 class="modal-title fw-bold">Ajouter au panier</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <p class="text-muted small mb-4">Sélectionnez la quantité pour <strong>${medicineName}</strong></p>
            
            <!-- Medicine & Pharmacy Info -->
            <div class="bg-light p-3 rounded mb-4">
              <div class="mb-2">
                <h6 class="fw-bold mb-0">${medicineName}</h6>
              </div>
              <p class="text-muted small mb-0">
                <i class="fas fa-map-marker-alt me-1"></i>
                ${pharmacyName}
              </p>
            </div>

            <form id="addToCartForm" class="needs-validation" novalidate>
              <!-- Quantity -->
              <div class="mb-4">
                <label for="qty-cart" class="form-label fw-bold">Quantité</label>
                <div class="input-group">
                  <button type="button" class="btn btn-outline-secondary" id="qty-minus-cart">−</button>
                  <input type="number" class="form-control text-center" id="qty-cart" min="1" max="80" value="1" required />
                  <button type="button" class="btn btn-outline-secondary" id="qty-plus-cart">+</button>
                </div>
                <small class="text-muted">max: 80</small>
              </div>

              <button type="submit" class="btn btn-success btn-lg w-100">
                <i class="fas fa-shopping-cart me-2"></i>Ajouter au panier
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  `;

  // Supprimer le modal existant s'il existe
  const existing = document.getElementById('addToCartModal');
  if (existing) existing.remove();

  // Créer et afficher le nouveau modal
  document.body.insertAdjacentHTML('beforeend', modalHTML);
  const modal = new bootstrap.Modal(document.getElementById('addToCartModal'));
  modal.show();

  // Configurer les contrôles du modal
  setupAddToCartModal(medicineId, pharmacyId, medicineName, pharmacyName, modal);
}

/**
 * Configurer le modal d'ajout au panier
 */
function setupAddToCartModal(medicineId, pharmacyId, medicineName, pharmacyName, modal) {
  const qtyInput = document.getElementById('qty-cart');
  const qtyMinus = document.getElementById('qty-minus-cart');
  const qtyPlus = document.getElementById('qty-plus-cart');
  const form = document.getElementById('addToCartForm');

  // Bouton moins
  qtyMinus.addEventListener('click', function (e) {
    e.preventDefault();
    const currentQty = parseInt(qtyInput.value);
    if (currentQty > 1) {
      qtyInput.value = currentQty - 1;
    }
  });

  // Bouton plus
  qtyPlus.addEventListener('click', function (e) {
    e.preventDefault();
    const currentQty = parseInt(qtyInput.value);
    if (currentQty < 80) {
      qtyInput.value = currentQty + 1;
    }
  });

  // Soumettre le formulaire
  form.addEventListener('submit', function (e) {
    e.preventDefault();
    
    const quantity = document.getElementById('qty-cart').value;
    addToCart(medicineId, pharmacyId, medicineName, pharmacyName, quantity);
    modal.hide();
  });
}

/**
 * Afficher le panier
 */
function openCartModal() {
  if (cartItems.length === 0) {
    alert('Votre panier est vide');
    return;
  }

  let cartHTML = `<div class="cart-items mb-4">`;
  
  cartItems.forEach((item, index) => {
    cartHTML += `
      <div class="cart-item mb-3 p-3 border rounded">
        <div class="d-flex justify-content-between align-items-start mb-2">
          <div>
            <h6 class="fw-bold mb-1">${item.medicineName}</h6>
            <p class="text-muted small mb-0">
              <i class="fas fa-map-marker-alt me-1"></i>
              ${item.pharmacyName}
            </p>
          </div>
          <button class="btn btn-sm btn-danger" onclick="removeFromCart(${index})">
            <i class="fas fa-trash"></i>
          </button>
        </div>
        <div class="input-group input-group-sm">
          <button class="btn btn-outline-secondary" type="button" onclick="updateCartQuantity(${index}, -1)">−</button>
          <input type="number" class="form-control text-center" value="${item.quantity}" readonly />
          <button class="btn btn-outline-secondary" type="button" onclick="updateCartQuantity(${index}, 1)">+</button>
        </div>
      </div>
    `;
  });

  cartHTML += `</div>`;

  const modalHTML = `
    <div class="modal fade" id="cartModal" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered" style="max-width: 500px;">
        <div class="modal-content border-0 shadow-lg">
          <div class="modal-header border-0 pb-0 bg-light">
            <h5 class="modal-title fw-bold">Mon Panier</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            ${cartHTML}
            <hr>
            <div class="alert alert-info">
              <small><i class="fas fa-info-circle me-2"></i>Chaque médicament sera enregistré comme une réservation distincte</small>
            </div>
          </div>
          <div class="modal-footer border-top">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Continuer</button>
            <button type="button" class="btn btn-success btn-lg" id="validate-cart-btn">
              <i class="fas fa-check me-2"></i>Valider la réservation
            </button>
          </div>
        </div>
      </div>
    </div>
  `;

  // Supprimer le modal existant s'il existe
  const existing = document.getElementById('cartModal');
  if (existing) existing.remove();

  // Créer et afficher le nouveau modal
  document.body.insertAdjacentHTML('beforeend', modalHTML);
  const modal = new bootstrap.Modal(document.getElementById('cartModal'));
  modal.show();

  // Configurer le bouton de validation
  document.getElementById('validate-cart-btn').addEventListener('click', () => {
    validateCart(modal);
  });
}

/**
 * Modifier la quantité dans le panier
 */
function updateCartQuantity(index, change) {
  const newQty = cartItems[index].quantity + change;
  if (newQty > 0 && newQty <= 80) {
    cartItems[index].quantity = newQty;
    localStorage.setItem('medicineCart', JSON.stringify(cartItems));
    updateCartBadge();
    openCartModal(); // Rafraîchir l'affichage
  }
}

/**
 * Supprimer un article du panier
 */
function removeFromCart(index) {
  cartItems.splice(index, 1);
  localStorage.setItem('medicineCart', JSON.stringify(cartItems));
  updateCartBadge();
  
  if (cartItems.length === 0) {
    const modal = bootstrap.Modal.getInstance(document.getElementById('cartModal'));
    if (modal) modal.hide();
  } else {
    openCartModal(); // Rafraîchir l'affichage
  }
}

/**
 * Valider le panier et créer les réservations
 */
async function validateCart(modal) {
  if (cartItems.length === 0) {
    alert('Votre panier est vide');
    return;
  }

  const btn = document.getElementById('validate-cart-btn');
  const originalText = btn.innerHTML;
  btn.disabled = true;
  btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>En cours...';

  try {
    // Créer une réservation pour chaque article du panier
    const promises = cartItems.map(item => {
      const formData = new FormData();
      formData.append('action', 'create');
      formData.append('medicament_id', item.medicineId);
      formData.append('pharmacie_id', item.pharmacyId);
      formData.append('quantite', item.quantity);

      return fetch('/PharmaLocal/backend/api/reservations.php', {
        method: 'POST',
        body: formData
      }).then(response => response.json());
    });

    const results = await Promise.all(promises);
    const allSuccess = results.every(result => result.success);

    if (allSuccess) {
      alert('Réservation(s) effectuée(s) avec succès !');
      
      // Vider le panier
      cartItems = [];
      localStorage.removeItem('medicineCart');
      updateCartBadge();
      
      modal.hide();
    } else {
      alert('Erreur lors de la création de certaines réservations');
    }
  } catch (error) {
    console.error('Erreur lors de la validation du panier:', error);
    alert('Erreur lors de la validation du panier. Veuillez réessayer.');
  } finally {
    btn.disabled = false;
    btn.innerHTML = originalText;
  }
}

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
 * Rechercher des médicaments avec pharmacies et gardes
 */
async function searchMedicinesWithPharmacies(query) {
  const resultsContainer = document.getElementById('medicamentos-results');
  if (!resultsContainer) return;

  if (!query || query.trim() === '') {
    resultsContainer.innerHTML = '';
    return;
  }

  // Afficher un loader
  resultsContainer.innerHTML = `
    <div class="text-center">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Chargement...</span>
      </div>
    </div>
  `;

  try {
    const response = await fetch(`/PharmaLocal/backend/api/medicaments.php?action=search_with_stocks&q=${encodeURIComponent(query)}`);
    const result = await response.json();

    if (result.success && result.data && Array.isArray(result.data)) {
      const medicaments = result.data;

      if (medicaments.length === 0) {
        resultsContainer.innerHTML = `
          <div class="alert alert-warning">
            <i class="fas fa-info-circle me-2"></i>
            Aucun médicament trouvé pour "<strong>${query}</strong>"
          </div>
        `;
      } else {
        let html = `<h4 class="fw-bold mb-4">Résultats pour "<strong>${query}</strong>" (${medicaments.length})</h4>`;
        html += '<div class="row gy-4">';
        
        medicaments.forEach(medicine => {
          html += createMedicineSearchResultCard(medicine);
        });
        
        html += '</div>';
        resultsContainer.innerHTML = html;
        
        // Ajouter les écouteurs d'événements
        attachReservationWithPharmacyListeners();
      }
    } else {
      resultsContainer.innerHTML = `
        <div class="alert alert-danger">
          <i class="fas fa-exclamation-triangle me-2"></i>
          Erreur lors de la recherche. Veuillez réessayer.
        </div>
      `;
    }
  } catch (error) {
    console.error('Erreur lors de la recherche:', error);
    resultsContainer.innerHTML = `
      <div class="alert alert-danger">
        <i class="fas fa-exclamation-triangle me-2"></i>
        Erreur lors de la recherche. Veuillez réessayer.
      </div>
    `;
  }
}

/**
 * Créer une carte de résultat de recherche de médicament avec pharmacies
 */
function createMedicineSearchResultCard(medicine) {
  const medicineId = medicine.id;
  const medicineName = medicine.nom;
  const medicineDosage = medicine.dosage || 'Dosage non spécifié';
  const medicineCategory = medicine.categorie || 'Catégorie non spécifiée';
  const medicineDescription = medicine.description || '';

  let pharmaciesHTML = '';
  
  if (medicine.pharmacies && medicine.pharmacies.length > 0) {
    pharmaciesHTML = medicine.pharmacies.map(pharmacy => {
      const gardeHTML = pharmacy.is_garde 
        ? '<span class="badge bg-danger rounded-pill ms-2"><i class="fas fa-moon"></i> Garde</span>'
        : '';
      
      return `
        <div class="pharmacy-result-item">
          <div class="d-flex justify-content-between align-items-start mb-2">
            <div>
              <h6 class="fw-bold mb-1">${pharmacy.pharmacie_nom} ${gardeHTML}</h6>
              <p class="text-muted small mb-1">
                <i class="fas fa-map-marker-alt"></i> ${pharmacy.pharmacie_adresse}
              </p>
              <p class="text-muted small mb-0">
                <i class="fas fa-phone"></i> ${pharmacy.pharmacie_telephone}
              </p>
            </div>
            <div class="text-end">
              <div class="quantity-badge">
                ${pharmacy.quantite} en stock
              </div>
            </div>
          </div>
          <button class="btn btn-sm btn-primary w-100 reserve-with-pharmacy-btn" 
                  data-medicine-id="${medicineId}" 
                  data-pharmacy-id="${pharmacy.pharmacie_id}"
                  data-medicine-name="${medicineName}"
                  data-pharmacy-name="${pharmacy.pharmacie_nom}">
            <i class="fas fa-calendar-check me-1"></i> Réserver
          </button>
        </div>
      `;
    }).join('');
  } else {
    pharmaciesHTML = `
      <div class="alert alert-warning small mb-0">
        <i class="fas fa-info-circle me-1"></i>
        Aucune pharmacie n'a ce médicament en stock actuellement
      </div>
    `;
  }

  return `
    <div class="col-12 col-lg-6">
      <div class="medicine-search-card">
        <div class="medicine-header">
          <h5 class="medicine-name">${medicineName}</h5>
        </div>
        
        <div class="medicine-details">
          <p class="dosage"><strong>Dosage:</strong> ${medicineDosage}</p>
          <p class="category"><strong>Catégorie:</strong> ${medicineCategory}</p>
          ${medicineDescription ? `<p class="description"><strong>Description:</strong> ${medicineDescription}</p>` : ''}
        </div>

        <div class="pharmacies-list">
          <h6 class="fw-bold mb-3">Disponibilité en pharmacies:</h6>
          ${pharmaciesHTML}
        </div>
      </div>
    </div>
  `;
}

/**
 * Attacher les écouteurs aux boutons de réservation avec pharmacie
 */
function attachReservationWithPharmacyListeners() {
  const reserveButtons = document.querySelectorAll('.reserve-with-pharmacy-btn');
  
  reserveButtons.forEach(button => {
    button.addEventListener('click', function(e) {
      e.preventDefault();
      
      const medicineId = this.dataset.medicineId;
      const pharmacyId = this.dataset.pharmacyId;
      const medicineName = this.dataset.medicineName;
      const pharmacyName = this.dataset.pharmacyName;
      
      // Vérifier si l'utilisateur est connecté
      checkLoginStatusAndReserve(medicineId, pharmacyId, medicineName, pharmacyName);
    });
  });
}

/**
 * Vérifier si l'utilisateur est connecté et afficher le modal d'ajout au panier
 */
function checkLoginStatusAndReserve(medicineId, pharmacyId, medicineName, pharmacyName) {
  // Vérifier le statut de connexion
  fetch('/PharmaLocal/backend/api/auth.php?action=check')
    .then(response => response.json())
    .then(data => {
      if (data.loggedIn) {
        // L'utilisateur est connecté, afficher le modal d'ajout au panier
        openAddToCartModal(medicineId, pharmacyId, medicineName, pharmacyName);
      } else {
        // L'utilisateur n'est pas connecté, rediriger vers la connexion
        const currentUrl = encodeURIComponent(window.location.href);
        window.location.href = `connexion.php?redirect=${currentUrl}`;
      }
    })
    .catch(error => {
      console.error('Erreur lors de la vérification de connexion:', error);
      // En cas d'erreur, rediriger vers la connexion pour être sûr
      const currentUrl = encodeURIComponent(window.location.href);
      window.location.href = `connexion.php?redirect=${currentUrl}`;
    });
}

/**
 * Ouvrir le modal de réservation avec pharmacie pré-sélectionnée
 */
function openReservationModalWithPharmacy(medicineId, pharmacyId, medicineName, pharmacyName) {
  const modalHTML = `
    <div class="modal fade" id="reservationWithPharmacyModal" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered" style="max-width: 500px;">
        <div class="modal-content border-0 shadow-lg">
          <div class="modal-header border-0 pb-0 bg-light">
            <h5 class="modal-title fw-bold">Réserver un médicament</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <p class="text-muted small mb-4">Complétez votre réservation pour <strong>${medicineName}</strong></p>
            
            <!-- Medicine & Pharmacy Info -->
            <div class="bg-light p-3 rounded mb-4">
              <div class="mb-2">
                <h6 class="fw-bold mb-0">${medicineName}</h6>
              </div>
              <p class="text-muted small mb-0">
                <i class="fas fa-map-marker-alt me-1"></i>
                ${pharmacyName}
              </p>
            </div>

            <form id="reservationFormPharmacy" class="needs-validation" novalidate>
              <!-- Quantity -->
              <div class="mb-4">
                <label for="qty-pharm" class="form-label fw-bold">Quantité</label>
                <div class="input-group">
                  <button type="button" class="btn btn-outline-secondary" id="qty-minus-pharm">−</button>
                  <input type="number" class="form-control text-center" id="qty-pharm" min="1" max="80" value="1" required />
                  <button type="button" class="btn btn-outline-secondary" id="qty-plus-pharm">+</button>
                </div>
                <small class="text-muted">max: 80</small>
              </div>

              <!-- Full Name -->
              <div class="mb-4">
                <label for="fullname-pharm" class="form-label fw-bold">Nom complet</label>
                <input type="text" class="form-control" id="fullname-pharm" placeholder="Votre nom" required />
              </div>

              <!-- Phone -->
              <div class="mb-4">
                <label for="phone-pharm" class="form-label fw-bold">Téléphone</label>
                <input type="tel" class="form-control" id="phone-pharm" placeholder="+229 XX XX XX XX" required />
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
  const existing = document.getElementById('reservationWithPharmacyModal');
  if (existing) existing.remove();

  // Créer et afficher le nouveau modal
  document.body.insertAdjacentHTML('beforeend', modalHTML);
  const modal = new bootstrap.Modal(document.getElementById('reservationWithPharmacyModal'));
  modal.show();

  // Configurer les contrôles du modal
  setupReservationWithPharmacyModal(medicineId, pharmacyId, medicineName, pharmacyName, modal);
}

/**
 * Configurer le modal de réservation avec pharmacie
 */
function setupReservationWithPharmacyModal(medicineId, pharmacyId, medicineName, pharmacyName, modal) {
  const qtyInput = document.getElementById('qty-pharm');
  const qtyMinus = document.getElementById('qty-minus-pharm');
  const qtyPlus = document.getElementById('qty-plus-pharm');
  const reservationForm = document.getElementById('reservationFormPharmacy');

  // Bouton moins
  qtyMinus.addEventListener('click', function (e) {
    e.preventDefault();
    const currentQty = parseInt(qtyInput.value);
    if (currentQty > 1) {
      qtyInput.value = currentQty - 1;
    }
  });

  // Bouton plus
  qtyPlus.addEventListener('click', function (e) {
    e.preventDefault();
    const currentQty = parseInt(qtyInput.value);
    if (currentQty < 80) {
      qtyInput.value = currentQty + 1;
    }
  });

  // Soumettre le formulaire
  reservationForm.addEventListener('submit', function (e) {
    e.preventDefault();
    
    const fullname = document.getElementById('fullname-pharm').value.trim();
    const phone = document.getElementById('phone-pharm').value.trim();
    const qty = document.getElementById('qty-pharm').value;

    if (!fullname || !phone || !qty) {
      alert('Veuillez remplir tous les champs');
      return;
    }

    // Afficher un loader
    const submitBtn = reservationForm.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>En cours...';

    // Faire un appel API pour créer la réservation
    const formData = new FormData();
    formData.append('action', 'create');
    formData.append('medicament_id', medicineId);
    formData.append('pharmacie_id', pharmacyId);
    formData.append('quantite', qty);

    fetch('/PharmaLocal/backend/api/reservations.php', {
      method: 'POST',
      body: formData
    })
    .then(response => response.json())
    .then(data => {
      submitBtn.disabled = false;
      submitBtn.innerHTML = originalText;
      
      if (data.success) {
        alert('Réservation effectuée avec succès !\n\nMédicament: ' + medicineName + '\nPharmacies: ' + pharmacyName + '\nQuantité: ' + qty);
        modal.hide();
        reservationForm.reset();
      } else {
        alert('Erreur: ' + (data.error || 'Impossible de créer la réservation'));
      }
    })
    .catch(error => {
      console.error('Erreur lors de la réservation:', error);
      submitBtn.disabled = false;
      submitBtn.innerHTML = originalText;
      alert('Erreur lors de la réservation. Veuillez réessayer.');
    });
  });
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
  // Gérer la recherche
  const searchBtn = document.getElementById('search-btn-med');
  const searchInput = document.getElementById('med-search');
  
  if (searchBtn && searchInput) {
    searchBtn.addEventListener('click', () => {
      const query = searchInput.value.trim();
      if (query) {
        searchMedicinesWithPharmacies(query);
      }
    });
    
    searchInput.addEventListener('keypress', (e) => {
      if (e.key === 'Enter') {
        const query = searchInput.value.trim();
        if (query) {
          searchMedicinesWithPharmacies(query);
        }
      }
    });
  }
});
