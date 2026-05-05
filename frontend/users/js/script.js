// ========================================
// DONNÉES MOCK
// ========================================

const PHARMACIES_DATA = [
  {
    id: 1,
    name: 'Pharmacie du Soleil',
    address: 'Rue 123, Quartier Ganhi, Cotonou',
    phone: '+229 21 31 45 67',
    hours: '24h/24 (Garde)',
    rating: 4.5,
    distance: 0.8,
    status: 'garde',
    insurances: ['Mutuelle de santé','Assurance entreprise','Assurance internationale','Assurance publique','Assurance privée'],
    until: 'Lundi 08h00'
  },
  {
    id: 2,
    name: 'Pharmacie Centrale',
    address: 'Boulevard St Michel, Cotonou',
    phone: '+229 21 32 56 78',
    hours: '24h/24 (Garde)',
    rating: 4.8,
    distance: 1.2,
    status: 'garde',
    insurances: ['Mutuelle de santé','Assurance entreprise','Assurance internationale','Assurance publique','Assurance privée'],
    until: 'Lundi 08h00'
  },
  {
    id: 3,
    name: 'Pharmacie de la Paix',
    address: 'Rue du Commerce, Cotonou',
    phone: '+229 21 33 44 55',
    hours: '24h/24 (Garde)',
    rating: 4.6,
    distance: 1.8,
    status: 'garde',
    insurances: ['Mutuelle de santé','Assurance entreprise','Assurance internationale','Assurance publique','Assurance privée'],
    until: 'Lundi 08h00'
  },
  {
    id: 4,
    name: 'Pharmacie San Cristobal',
    address: 'Avenue de France, Porto-Novo',
    phone: '+229 21 21 22 23',
    hours: '08h00 - 20h00',
    rating: 4.3,
    distance: 12.5,
    status: 'open',
    insurances: ['Mutuelle de santé','Assurance entreprise','Assurance internationale','Assurance publique','Assurance privée'],
    until: null
  },
  {
    id: 5,
    name: 'Pharmacie Nouvelle',
    address: 'Rue de l\'Industrie, Abomey-Calavi',
    phone: '+229 21 44 55 66',
    hours: '24h/24 (Garde)',
    rating: 4.4,
    distance: 8.3,
    status: 'garde',
    insurances: ['Mutuelle de santé','Assurance entreprise','Assurance internationale','Assurance publique','Assurance privée'],
    until: 'Lundi 08h00'
  },
  {
    id: 6,
    name: 'Pharmacie Plus',
    address: 'Centre Commercial, Cotonou',
    phone: '+229 21 55 66 77',
    hours: '08h00 - 22h00',
    rating: 4.7,
    distance: 2.1,
    status: 'open',
    insurances: ['Mutuelle de santé','Assurance entreprise','Assurance internationale','Assurance publique','Assurance privée'],
    until: null
  }
];

const MEDICINES_DATA = [
  {
    id: 1,
    name: 'Paracétamol',
    dosage: '500mg',
    price: '500 CFA',
    inStock: true,
    pharmacies: [1, 2, 3, 6]
  },
  {
    id: 2,
    name: 'Amoxicilline',
    dosage: '250mg',
    price: '1000 CFA',
    inStock: true,
    pharmacies: [1, 3, 5]
  },
  {
    id: 3,
    name: 'Ibuprofen',
    dosage: '200mg',
    price: '800 CFA',
    inStock: true,
    pharmacies: [2, 4, 6]
  },
  {
    id: 4,
    name: 'Aspirine',
    dosage: '500mg',
    price: '600 CFA',
    inStock: false,
    pharmacies: []
  },
  {
    id: 5,
    name: 'Vitamine C',
    dosage: '1000mg',
    price: '1500 CFA',
    inStock: true,
    pharmacies: [1, 2, 5, 6]
  },
  {
    id: 6,
    name: 'Doliprane',
    dosage: '1000mg',
    price: '1200 CFA',
    inStock: true,
    pharmacies: [1, 3, 4, 5]
  }
];



function generateStarRating(rating) {
  let stars = '';
  const fullStars = Math.floor(rating);
  const hasHalfStar = rating % 1 !== 0;

  for (let i = 0; i < fullStars; i++) {
    stars += '<i class="fas fa-star"></i>';
  }

  if (hasHalfStar) {
    stars += '<i class="fas fa-star-half-alt"></i>';
  }

  const emptyStars = 5 - Math.ceil(rating);
  for (let i = 0; i < emptyStars; i++) {
    stars += '<i class="far fa-star"></i>';
  }

  return stars;
}

function createPharmacyCard(pharmacy) {
  const statusBadge = pharmacy.status === 'garde' 
    ? '<span class="pharmacy-badge"><i class="fas fa-circle-dot me-1"></i>De Garde</span>'
    : '<span class="pharmacy-badge" style="background: linear-gradient(135deg, #22a356, #1a7d43); box-shadow: 0 2px 8px rgba(34, 163, 86, 0.3);"><i class="fas fa-check me-1"></i>Ouvert</span>';

  const insurances = pharmacy.insurances.join(', ');

  return `
    <div class="col-md-6 col-lg-4">
      <article class="pharmacy-card animate-in" role="article" aria-label="Fiche pharmacie ${pharmacy.name}">
        ${statusBadge}
        <div class="pharmacy-info">
          <h3 class="pharmacy-name">${pharmacy.name}</h3>
          
          <div class="pharmacy-detail">
            <i class="fas fa-map-marker-alt" aria-hidden="true"></i>
            <address style="all: unset; display: inline;">${pharmacy.address}</address>
          </div>
          
          <div class="pharmacy-detail">
            <i class="fas fa-phone" aria-hidden="true"></i>
            <a href="tel:${pharmacy.phone}" title="Appeler ${pharmacy.name}">${pharmacy.phone}</a>
          </div>
          
          <div class="pharmacy-detail">
            <i class="fas fa-clock" aria-hidden="true"></i>
            <time>${pharmacy.hours}</time>
          </div>

          <div class="pharmacy-rating">
            <span class="stars" aria-label="Note: ${pharmacy.rating} sur 5 étoiles">${generateStarRating(pharmacy.rating)}</span>
            <span class="score" aria-hidden="true">${pharmacy.rating}</span>
          </div>

          <div class="pharmacy-detail">
            <i class="fas fa-location" aria-hidden="true"></i>
            <span>${pharmacy.distance} km</span>
          </div>

          <div class="pharmacy-insurance">
            <i class="fas fa-shield-halved" aria-hidden="true"></i> ${insurances}
          </div>

          ${pharmacy.until ? `<div class="pharmacy-detail" style="color: var(--danger);">
            <i class="fas fa-hourglass-end" aria-hidden="true"></i>
            <span>Jusqu'à ${pharmacy.until}</span>
          </div>` : ''}

          <div class="pharmacy-actions">
            <a href="#pharmacie-${pharmacy.id}" class="btn-primary" tabindex="0">Voir détails</a>
            <a href="#produits-${pharmacy.id}" class="btn-outline" tabindex="0">Produits</a>
          </div>
        </div>
      </article>
    </div>
  `;
}

function createMedicineCard(medicine) {
  const stockStatus = medicine.inStock
    ? '<span class="badge badge-available"><i class="fas fa-check me-1"></i>En stock</span>'
    : '<span class="badge bg-danger text-white"><i class="fas fa-times me-1"></i>Rupture</span>';

  return `
    <div class="col-md-6 col-lg-4">
      <article class="feature-card animate-in" role="article" aria-label="Fiche médicament ${medicine.name}">
        <div class="d-flex justify-content-between align-items-start mb-3">
          <div>
            <h4 class="fw-bold mb-1">${medicine.name}</h4>
            <p class="text-muted mb-0 small">${medicine.dosage}</p>
          </div>
          ${stockStatus}
        </div>
        <div class="mb-3">
          <span class="fs-5 fw-bold text-primary">${medicine.price}</span>
        </div>
        <p class="text-muted small mb-3">
          <i class="fas fa-map-marker-alt me-1" aria-hidden="true"></i>
          Disponible dans ${medicine.pharmacies.length} pharmacie(s)
        </p>
        <button class="btn btn-primary w-100" data-action="reserve" data-medicine-id="${medicine.id}" aria-label="Réserver ${medicine.name}">
          <i class="fas fa-shopping-cart me-2"></i>Réserver
        </button>
      </article>
    </div>
  `;
}



function loadFullPharmaciesList() {
  const container = document.getElementById('pharmaciesListFull');
  if (!container) return;

  let filteredPharmacies = PHARMACIES_DATA;

  // Apply filters
  const cityFilter = document.getElementById('city-filter');
  const distanceFilter = document.getElementById('distance-filter');
  const ratingFilter = document.getElementById('rating-filter');

  if (cityFilter?.value) {
   
  }

  if (distanceFilter?.value) {
    const maxDistance = parseFloat(distanceFilter.value);
    filteredPharmacies = filteredPharmacies.filter(p => p.distance <= maxDistance);
  }

  if (ratingFilter?.value) {
    const minRating = parseFloat(ratingFilter.value);
    filteredPharmacies = filteredPharmacies.filter(p => p.rating >= minRating);
  }

  container.innerHTML = filteredPharmacies.map(pharmacy => createPharmacyCard(pharmacy)).join('');
}


function setupFormValidation() {
  const forms = document.querySelectorAll('form.needs-validation');

  forms.forEach(form => {
    form.addEventListener('submit', function (e) {
      if (!form.checkValidity()) {
        e.preventDefault();
        e.stopPropagation();
      } else {
        e.preventDefault();
        showSuccessAlert('Votre demande a bien été traitée !');
        form.reset();
      }
      form.classList.add('was-validated');
    });
  });
}

function showSuccessAlert(message) {
  const alertDiv = document.createElement('div');
  alertDiv.className = 'alert alert-success position-fixed bottom-0 end-0 m-3';
  alertDiv.style.zIndex = '9999';
  alertDiv.innerHTML = `<i class="fas fa-check-circle me-2"></i>${message}`;
  
  document.body.appendChild(alertDiv);

  setTimeout(() => {
    alertDiv.remove();
  }, 3000);
}



document.addEventListener('DOMContentLoaded', function () {
  // Load pharmacies on home page
  if (document.getElementById('pharmaciesList')) {
    const limited = PHARMACIES_DATA.slice(0, 3);
    document.getElementById('pharmaciesList').innerHTML = limited.map(pharmacy => createPharmacyCard(pharmacy)).join('');
  }

  // Setup form validation
  setupFormValidation();

  // Search functionality
  const searchBtn = document.getElementById('search-btn');
  if (searchBtn) {
    searchBtn.addEventListener('click', function () {
      const query = document.getElementById('search-med').value;
      if (query) {
        window.location.href = `medicaments.html?q=${encodeURIComponent(query)}`;
      }
    });

    document.getElementById('search-med').addEventListener('keypress', function (e) {
      if (e.key === 'Enter') {
        searchBtn.click();
      }
    });
  }
});
