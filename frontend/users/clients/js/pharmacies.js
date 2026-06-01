
function filterPharmacies() {
  const cityFilter = document.getElementById('city-filter');
  if (!cityFilter) return;

  const selectedCity = cityFilter.value;
  const pharmacyCards = document.querySelectorAll('.col-md-6.col-lg-4');

  pharmacyCards.forEach(card => {
    const pharmacyElement = card.querySelector('.pharmacy-info');
    if (!pharmacyElement) return;

    const cityElement = pharmacyElement.querySelector('.pharmacy-detail i.fa-map-marker-alt')?.parentElement;
    if (!cityElement) return;

    const addressText = cityElement.textContent.trim();
    
    // Extract city from address (city is after the last comma)
    const parts = addressText.split(', ');
    const cityInCard = parts.length > 1 ? parts[parts.length - 1].trim() : '';
    
    const matches = selectedCity === '' || cityInCard === selectedCity;
    card.style.display = matches ? 'block' : 'none';
  });

  const visibleCards = Array.from(pharmacyCards).filter(card => card.style.display !== 'none');
  const container = document.querySelector('.py-5 .container-lg .row');
  
  if (visibleCards.length === 0 && container) {
    if (!document.getElementById('no-results-message')) {
      const noResults = document.createElement('div');
      noResults.id = 'no-results-message';
      noResults.innerHTML = `
        <div class="col-12">
          <div class="alert alert-info text-center py-5">
            <i class="fas fa-search me-2 fs-4"></i>
            <p>Aucune pharmacie ne correspond à vos critères de recherche.</p>
          </div>
        </div>
      `;
      container.appendChild(noResults);
    }
  } else {
    const noResults = document.getElementById('no-results-message');
    if (noResults) noResults.remove();
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

// Initialize filter on page load
document.addEventListener('DOMContentLoaded', function () {
  const cityFilter = document.getElementById('city-filter');
  if (cityFilter) {
    cityFilter.addEventListener('change', filterPharmacies);
  }
  if (document.getElementById('pharmaciesListFull')) {
    initializeMap();
  }
});
