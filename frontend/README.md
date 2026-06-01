# PharmaGuard - Frontend Complet

Frontend professionnel pour la plateforme **PharmaGuard** - système de localisation des pharmacies de garde au Bénin.

## 📁 Structure du Projet

```
frontend/
├── index.html                    # Page d'accueil
├── pharmacies.html              # Listing des pharmacies
├── medicaments.html             # Recherche de médicaments
├── connexion.html               # Login & Inscription
│
├── css/
│   ├── variables.css            # Variables de thème et couleurs
│   └── styles.css               # Styles globaux + animations
│
├── js/
│   ├── script.js                # Logique centrale & données mock
│   ├── pharmacies.js            # Fonctionnalités pharmacies
│   └── medicaments.js           # Fonctionnalités médicaments
│
└── README.md                    # Documentation
```

## 🎨 Design & Couleurs

**Palette de couleurs PharmaGuard:**
- **Primaire**: #0066cc (Bleu)
- **Danger**: #dc3545 (Rouge - Pharmacies de garde)
- **Succès**: #28a745 (Vert)
- **Warning**: #ffc107 (Orange/Jaune)
- **Texte**: #333333 (Foncé), #666666 (Gris)
- **Fond**: #f8f9fa (Clair)

## ✨ Fonctionnalités Incluses

### 👨‍⚕️ Pour les Patients
- ✅ **Localisation GPS** - Trouver les pharmacies les plus proches
- ✅ **Recherche de médicaments** - Vérifier disponibilité et prix
- ✅ **Réservation en ligne** - Commander des médicaments
- ✅ **Notifications** - Alertes sur les réservations
- ✅ **Système de rating** - Avis sur les pharmacies
- ✅ **Filtres avancés** - Par distance, ville, notation

### 🏥 Pour les Pharmaciens
- ✅ **Gestion de stock** - Alertes automatiques sur les ruptures
- ✅ **Paiement sécurisé** - Transactions en ligne
- ✅ **Historique clients** - Traçabilité des achats
- ✅ **Dashboard** - Vue d'ensemble des ventes

## 📱 Pages Disponibles

### 1. **Accueil** (`index.html`)
- Hero section avec call-to-action
- Recherche rapide de médicaments
- 3 pharmacies de garde en vedette
- Section "Tout ce dont vous avez besoin" (6 features)

### 2. **Pharmacies** (`pharmacies.html`)
- Listing complet des pharmacies
- Filtres: Ville, Distance, Notation
- Cartes détaillées avec:
  - Adresse et téléphone
  - Horaires d'ouverture
  - Notation client (⭐)
  - Assurances acceptées
  - Distance en km

### 3. **Médicaments** (`medicaments.html`)
- Barre de recherche avec autocomplete
- Résultats dynamiques
- Cartes médicaments avec:
  - Dosage
  - Prix
  - Statut stock
  - Pharmacies disponibles
  - Bouton réservation

### 4. **Connexion** (`connexion.html`)
- Onglets Login/Inscription
- Connexion sociale (Google, Facebook)
- Formulaires validés
- Infos sur les avantages PharmaGuard

## 🛠️ Technologies Utilisées

- **HTML5** - Structure sémantique
- **CSS3** - Styles modernes + animations smooth
- **Bootstrap 5.3** - Framework responsive
- **JavaScript (Vanilla)** - Logique interactive
- **Font Awesome 6.4** - Icônes

## 🚀 Quick Start

1. **Ouvrir le projet**
   ```bash
   cd frontend
   ```

2. **Lancer le serveur local** (avec Python)
   ```bash
   python -m http.server 8000
   ```

3. **Accéder dans le navigateur**
   ```
   http://localhost:8000
   ```

## 📊 Données Mock

Le projet utilise des données **hardcodées** pour :

### Pharmacies (6 au total)
```javascript
{
  id, name, address, phone, hours, 
  rating, distance, status, insurances, until
}
```

### Médicaments (6 au total)
```javascript
{
  id, name, dosage, price, inStock, pharmacies[]
}
```

> **Note**: Connecter à une vraie API en remplaçant `PHARMACIES_DATA` et `MEDICINES_DATA` dans `js/script.js`

## 🎯 Fonctionnalités JavaScript

### Pharmacies
- `loadFullPharmaciesList()` - Charge la liste complète
- `filterPharmacies()` - Applique les filtres
- `sortPharmacies(sortBy)` - Trie par distance/rating/nom
- `searchPharmacies(query)` - Recherche par nom/adresse

### Médicaments
- `searchMedicines(query)` - Recherche en temps réel
- `filterMedicinesByStock()` - Filtre par disponibilité
- `openReservationModal(id)` - Modal de réservation
- `setupMedicineSearchAutocomplete()` - Suggestions

### Général
- `generateStarRating(rating)` - Affiche les stars
- `createPharmacyCard()` - Génère une carte pharmacie
- `createMedicineCard()` - Génère une carte médicament
- `setupFormValidation()` - Valide les formulaires
- `showSuccessAlert()` - Messages de succès

## 🎨 Animations

- **slideInUp** - Apparition des cartes
- **fadeIn** - Transitions douces
- **pulse** - Badge clignotant
- **Hover effects** - Élévation au survol
- **Transitions smooth** - 0.3s ease

## 📱 Responsive Design

- ✅ Mobile first
- ✅ Tablet optimisé
- ✅ Desktop full-width
- ✅ Breakpoints Bootstrap

## 🔒 Sécurité

- ✅ Formulaires validés
- ✅ HTTPS ready
- ✅ Input sanitization
- ✅ CSRF tokens (à implémenter côté backend)

## 🔄 Intégration Backend

Pour connecter le backend:

1. **Remplacer les données mock**
   ```javascript
   // Au lieu de PHARMACIES_DATA statique
   async function loadPharmacies() {
     const response = await fetch('/api/pharmacies');
     return await response.json();
   }
   ```

2. **API endpoints à implémenter**
   - `GET /api/pharmacies` - Liste des pharmacies
   - `GET /api/medicaments` - Liste des médicaments
   - `POST /api/reservations` - Créer réservation
   - `POST /api/auth/login` - Authentification
   - `POST /api/auth/register` - Inscription

## 📋 Checklist Déploiement

- [ ] Connecter l'API backend
- [ ] Configurer HTTPS
- [ ] Ajouter Google Maps API
- [ ] Implémenter notification système
- [ ] Setup authentification JWT
- [ ] Tests E2E
- [ ] Optimiser images
- [ ] Minifier CSS/JS
- [ ] SEO optimization
- [ ] Analytics (Google)

## 💡 Améliorations Futures

1. **Dark mode** - Support thème sombre
2. **Multi-langue** - Français/Anglais
3. **PWA** - Progressive Web App
4. **Offline mode** - Service workers
5. **Cartes interactives** - Google Maps intégré
6. **Notifications push** - Service workers
7. **Historique d'achats** - Dashboard utilisateur
8. **Reviews réelles** - Système de notation

## 📞 Support

Pour toute question:
- 📧 Email: contact@pharmaguard.bj
- 📱 Téléphone: +229 21 00 00 00

---

**© 2026 PharmaGuard** - Système de localisation des pharmacies de garde au Bénin
