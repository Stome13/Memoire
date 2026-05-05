
function loadFullPharmaciesList() {
  const container = document.getElementById('pharmaciesListFull');
  if (!container) return;

  let filteredPharmacies = PHARMACIES_DATA;

  // Apply filters
  const cityFilter = document.getElementById('city-filter');
  const distanceFilter = document.getElementById('distance-filter');
  const ratingFilter = document.getElementById('rating-filter');

  if (cityFilter && cityFilter.value) {
    const cityMap = {
      'Cotonou': ['Rue 123', 'Boulevard St Michel', 'Rue du Commerce', 'Centre Commercial'],
      'Porto-Novo': ['Avenue de France'],
      'Abomey-Calavi': ['Rue de l\'Industrie']
    };
    
    const city = cityFilter.value;
    filteredPharmacies = filteredPharmacies.filter(p => {
      if (cityMap[city]) {
        return cityMap[city].some(keyword => p.address.includes(keyword));
      }
      return true;
    });
  }

  if (distanceFilter && distanceFilter.value) {
    const maxDistance = parseFloat(distanceFilter.value);
    filteredPharmacies = filteredPharmacies.filter(p => p.distance <= maxDistance);
  }

  if (ratingFilter && ratingFilter.value) {
    const minRating = parseFloat(ratingFilter.value);
    filteredPharmacies = filteredPharmacies.filter(p => p.rating >= minRating);
  }

  if (filteredPharmacies.length === 0) {
    container.innerHTML = `
      <div class="col-12">
        <div class="alert alert-info text-center py-5">
          <i class="fas fa-search me-2 fs-4"></i>
          <p>Aucune pharmacie ne correspond à vos critères de recherche.</p>
        </div>
      </div>
    `;
  } else {
    container.innerHTML = filteredPharmacies.map(pharmacy => createPharmacyCard(pharmacy)).join('');
  }
}

function setupPharmacyDetailModal(pharmacyId) {
  const pharmacy = PHARMACIES_DATA.find(p => p.id === pharmacyId);
  if (!pharmacy) return;

  const modal = new bootstrap.Modal(document.getElementById('pharmacyModal'));
  
  const content = `
    <h5 class="fw-bold mb-3">${pharmacy.name}</h5>
    <div class="mb-3">
      <strong>Adresse:</strong> ${pharmacy.address}
    </div>
    <div class="mb-3">
      <strong>Téléphone:</strong> <a href="tel:${pharmacy.phone}">${pharmacy.phone}</a>
    </div>
    <div class="mb-3">
      <strong>Horaires:</strong> ${pharmacy.hours}
    </div>
    <div class="mb-3">
      <strong>Notations:</strong>
      <div class="stars">${generateStarRating(pharmacy.rating)}</div>
    </div>
    <div class="mb-3">
      <strong>Assurances acceptées:</strong>
      <div>${pharmacy.insurances.join(', ')}</div>
    </div>
    <div class="mb-3">
      <strong>Distance:</strong> ${pharmacy.distance} km
    </div>
    ${pharmacy.until ? `
      <div class="alert alert-danger mb-0">
        <i class="fas fa-hourglass-end me-2"></i>
        De garde jusqu'à ${pharmacy.until}
      </div>
    ` : ''}
  `;

  document.getElementById('pharmacyModalBody').innerHTML = content;
  modal.show();
}

// Map view (simulation)
function initializeMap() {
  const mapContainer = document.getElementById('pharmacy-map');
  if (!mapContainer) return;

  mapContainer.innerHTML = `
    <div style="background: linear-gradient(135deg, #e0e0e0, #f5f5f5); 
                padding: 100px 20px; 
                text-align: center; 
                border-radius: 12px;
                color: #666;">
      <i class="fas fa-map" style="font-size: 3rem; margin-bottom: 20px; opacity: 0.5;"></i>
      <p>Carte interactive des pharmacies</p>
      <small>Intégration Google Maps bientôt disponible</small>
    </div>
  `;
}

// Sort & pagination
function sortPharmacies(sortBy) {
  const container = document.getElementById('pharmaciesListFull');
  if (!container) return;

  let sorted = [...PHARMACIES_DATA];

  switch(sortBy) {
    case 'distance':
      sorted.sort((a, b) => a.distance - b.distance);
      break;
    case 'rating':
      sorted.sort((a, b) => b.rating - a.rating);
      break;
    case 'name':
      sorted.sort((a, b) => a.name.localeCompare(b.name));
      break;
    default:
      sorted.sort((a, b) => a.distance - b.distance);
  }

  container.innerHTML = sorted.map(pharmacy => createPharmacyCard(pharmacy)).join('');
}

// Search within page
function searchPharmacies(query) {
  const container = document.getElementById('pharmaciesListFull');
  if (!container) return;

  const filtered = PHARMACIES_DATA.filter(pharmacy => 
    pharmacy.name.toLowerCase().includes(query.toLowerCase()) ||
    pharmacy.address.toLowerCase().includes(query.toLowerCase())
  );

  if (filtered.length === 0) {
    container.innerHTML = `
      <div class="col-12">
        <div class="alert alert-warning text-center py-5">
          <i class="fas fa-search me-2 fs-4"></i>
          <p>Aucune pharmacie trouvée pour: "<strong>${query}</strong>"</p>
        </div>
      </div>
    `;
  } else {
    container.innerHTML = filtered.map(pharmacy => createPharmacyCard(pharmacy)).join('');
  }
}

// Call on page load
document.addEventListener('DOMContentLoaded', function () {
  if (document.getElementById('pharmaciesListFull')) {
    loadFullPharmaciesList();
    initializeMap();
  }
});
