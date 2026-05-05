// ========================================
// GESTION DES RÉSERVATIONS - PROTECTION AUTHENTIFICATION
// ========================================

/**
 * Afficher un modal de réservation
 * Protégé: l'utilisateur doit être connecté
 */
function showReservationModal(medicine, pharmacy) {
  // Vérifier que l'utilisateur est connecté
  if (!authManager.isLoggedIn()) {
    showLoginRequiredModal();
    return;
  }

  const currentUser = authManager.getCurrentUser();

  // Vérifier que le profil est complet
  if (!currentUser.profileComplete) {
    showProfileIncompleteAlert();
    return;
  }

  // Afficher le modal de réservation
  const modal = new bootstrap.Modal(document.getElementById('reservationModal') || createReservationModal());

  // Pré-remplir les données
  document.getElementById('resMedicineName').value = medicine.name;
  document.getElementById('resMedicineDosage').value = medicine.dosage;
  document.getElementById('resPharmacyName').value = pharmacy.name;
  document.getElementById('resPharmacyPhone').value = pharmacy.phone;

  modal.show();
}

/**
 * Modal d'authentification requise
 */
function showLoginRequiredModal() {
  const modalHTML = `
    <div class="modal fade" id="loginRequiredModal" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header border-0 bg-warning">
            <h5 class="modal-title text-dark">
              <i class="fas fa-sign-in-alt me-2"></i>Connexion requise
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body py-4">
            <p class="mb-0">
              <i class="fas fa-info-circle me-2 text-primary"></i>
              Vous devez être connecté pour effectuer une réservation.
            </p>
          </div>
          <div class="modal-footer border-0">
            <a href="connexion.html" class="btn btn-primary">
              <i class="fas fa-sign-in-alt me-2"></i>Se connecter
            </a>
            <a href="Inscription.html" class="btn btn-success">
              <i class="fas fa-user-plus me-2"></i>S'inscrire
            </a>
          </div>
        </div>
      </div>
    </div>
  `;

  let modal = document.getElementById('loginRequiredModal');
  if (!modal) {
    const tempDiv = document.createElement('div');
    tempDiv.innerHTML = modalHTML;
    modal = tempDiv.firstElementChild;
    document.body.appendChild(modal);
  }

  const bootstrapModal = new bootstrap.Modal(modal);
  bootstrapModal.show();
}

/**
 * Alerte : profil incomplet
 */
function showProfileIncompleteAlert() {
  showAlert('warning', `
    <strong>Profil incomplet!</strong> Veuillez compléter votre profil (téléphone et adresse) avant de réserver.
    <a href="profil.html" class="btn btn-sm btn-primary ms-2">Compléter le profil</a>
  `);
}

/**
 * Créer le modal de réservation
 */
function createReservationModal() {
  const modalHTML = `
    <div class="modal fade" id="reservationModal" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header bg-primary bg-opacity-10">
            <h5 class="modal-title">
              <i class="fas fa-prescription-bottle me-2"></i>Confirmer votre réservation
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <form id="quickReservationForm" class="needs-validation" novalidate>
            <div class="modal-body">
              <div class="form-group mb-3">
                <label class="form-label fw-bold">Médicament</label>
                <input type="text" class="form-control" id="resMedicineName" readonly />
              </div>

              <div class="form-group mb-3">
                <label class="form-label fw-bold">Dosage</label>
                <input type="text" class="form-control" id="resMedicineDosage" readonly />
              </div>

              <div class="form-group mb-3">
                <label class="form-label fw-bold">Pharmacie</label>
                <input type="text" class="form-control" id="resPharmacyName" readonly />
              </div>

              <div class="form-group mb-3">
                <label class="form-label fw-bold">Téléphone pharmacie</label>
                <div>
                  <a href="#" id="resPharmacyPhone" class="btn btn-sm btn-outline-primary" target="_blank">
                    <i class="fas fa-phone me-1"></i>Appeler
                  </a>
                </div>
              </div>

              <div class="form-group mb-3">
                <label for="resQuantity" class="form-label fw-bold">Quantité</label>
                <input type="number" class="form-control" id="resQuantity" min="1" value="1" required />
              </div>

              <div class="form-group mb-3">
                <label for="resPrice" class="form-label fw-bold">Prix estimé</label>
                <input type="text" class="form-control" id="resPrice" readonly />
              </div>

              <div class="alert alert-info">
                <i class="fas fa-clock me-2"></i>
                <small>Votre réservation sera valable 24 heures. Vous recevrez une confirmation par SMS.</small>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
              <button type="submit" class="btn btn-success">
                <i class="fas fa-check me-1"></i>Confirmer la réservation
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  `;

  const tempDiv = document.createElement('div');
  tempDiv.innerHTML = modalHTML;
  const modal = tempDiv.firstElementChild;
  document.body.appendChild(modal);

  // Ajouter l'événement de soumission
  document.getElementById('quickReservationForm').addEventListener('submit', submitReservation);

  return modal;
}

/**
 * Soumettre la réservation
 */
function submitReservation(e) {
  e.preventDefault();

  const currentUser = authManager.getCurrentUser();
  if (!currentUser) {
    showLoginRequiredModal();
    return;
  }

  const reservation = {
    medicineName: document.getElementById('resMedicineName').value,
    dosage: document.getElementById('resMedicineDosage').value,
    pharmacyName: document.getElementById('resPharmacyName').value,
    quantity: parseInt(document.getElementById('resQuantity').value),
    price: document.getElementById('resPrice').value
  };

  const result = authManager.addReservation(currentUser.id, reservation);

  if (result.success) {
    showAlert('success', `
      <strong>Réservation confirmée!</strong> Votre réservation a été enregistrée.
      <a href="profil.html" class="btn btn-sm btn-primary ms-2">Voir mes réservations</a>
    `);

    // Fermer le modal
    const modal = bootstrap.Modal.getInstance(document.getElementById('reservationModal'));
    if (modal) modal.hide();

    // Réinitialiser le formulaire
    document.getElementById('quickReservationForm').reset();
  } else {
    showAlert('danger', result.message);
  }
}

/**
 * Fonction utilitaire pour afficher les alertes (version améliorée)
 */
function showAlert(type, message) {
  const alertDiv = document.createElement('div');
  alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
  alertDiv.role = 'alert';
  alertDiv.innerHTML = `
    ${message}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  `;

  const container = document.querySelector('main') || document.body;
  if (container) {
    container.insertBefore(alertDiv, container.firstChild);
  } else {
    document.body.insertBefore(alertDiv, document.body.firstChild);
  }

  setTimeout(() => {
    alertDiv.remove();
  }, 6000);
}

/**
 * Ajouter des boutons de réservation aux cartes de médicaments
 * Cette fonction doit être appelée après le chargement des médicaments
 */
function addReservationButtons() {
  // Trouver tous les boutons "Réserver" et ajouter l'événement
  document.querySelectorAll('[data-action="reserve"]').forEach(btn => {
    btn.addEventListener('click', function (e) {
      e.preventDefault();
      e.stopPropagation();

      // Récupérer les données du médicament et de la pharmacie
      const medicineCard = this.closest('[data-medicine-id]');
      const medicine = {
        id: medicineCard.dataset.medicineId,
        name: medicineCard.querySelector('[data-medicine-name]')?.textContent || 'Médicament',
        dosage: medicineCard.querySelector('[data-medicine-dosage]')?.textContent || '',
        price: medicineCard.querySelector('[data-medicine-price]')?.textContent || ''
      };

      const pharmacyCard = this.closest('[data-pharmacy-id]');
      const pharmacy = {
        id: pharmacyCard?.dataset.pharmacyId || '',
        name: pharmacyCard?.querySelector('[data-pharmacy-name]')?.textContent || 'Pharmacie',
        phone: pharmacyCard?.querySelector('[data-pharmacy-phone]')?.textContent || ''
      };

      showReservationModal(medicine, pharmacy);
    });
  });
}

// Initialiser au chargement de la page
document.addEventListener('DOMContentLoaded', function () {
  addReservationButtons();
});
