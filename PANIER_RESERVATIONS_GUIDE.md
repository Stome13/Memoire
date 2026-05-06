# Guide du Système de Panier et Réservations

## Vue d'ensemble
Le système a été amélioré pour permettre aux utilisateurs connectés de:
1. Ajouter des médicaments au panier depuis la recherche
2. Gérer leur panier avec modification de quantités
3. Valider les réservations en masse
4. Voir, modifier et annuler leurs réservations dans leur profil

## Flux utilisateur

### 1. Recherche de médicaments
- L'utilisateur accède à la page "Médicaments" ou utilise la recherche depuis la page d'accueil
- Les résultats affichent les médicaments avec disponibilité par pharmacie
- Chaque pharmacie affiche:
  - Nom et adresse
  - Numéro de téléphone
  - Quantité en stock
  - Badge "Garde" si la pharmacie est de garde

### 2. Ajout au panier
- L'utilisateur clique sur "Réserver"
- S'il n'est pas connecté: redirection vers la connexion
- S'il est connecté: affichage d'un modal avec:
  - Sélecteur de quantité (1-80)
  - Bouton "Ajouter au panier"
- L'article est ajouté au localStorage avec les infos: medicineId, pharmacyId, medicineName, pharmacyName, quantity

### 3. Gestion du panier
- Le panier est accessible via l'icône panier en haut de la navigation (avec badge compteur)
- Le panier affiche:
  - Liste des articles
  - Possibilité de modifier la quantité de chaque article
  - Possibilité de supprimer un article
  - Bouton "Valider la réservation"
- Les données du panier sont persistantes (localStorage)

### 4. Validation des réservations
- Clic sur "Valider la réservation"
- Le système crée une réservation API pour chaque article du panier
- Le statut initial est "en attente"
- Une notification de succès s'affiche
- Le panier est vidé
- Le badge du panier se met à jour

### 5. Gestion des réservations (Profil)
L'utilisateur peut accéder à ses réservations via:
- Menu utilisateur → "Mes Réservations"
- Page profil → Onglet "Mes Réservations"

#### Informations affichées:
- Nom du médicament
- Pharmacie
- Quantité réservée
- Date de réservation
- Statut (avec code couleur)
- Dosage et catégorie

#### Actions possibles:
- **Modifier** (si statut = "en attente"): Change la quantité
- **Annuler** (si statut = "en attente"): Change le statut à "annulée"
- Les réservations confirmées/retirées ne peuvent pas être modifiées

## Modifications techniques

### Frontend

#### js/medicaments.js
- **Nouvelles fonctions**:
  - `addToCart()` - Ajoute un article au panier (localStorage)
  - `openAddToCartModal()` - Affiche le modal de sélection de quantité
  - `setupAddToCartModal()` - Configure les contrôles du modal
  - `openCartModal()` - Affiche le panier avec tous les articles
  - `updateCartQuantity()` - Met à jour la quantité d'un article
  - `removeFromCart()` - Supprime un article du panier
  - `validateCart()` - Crée les réservations API et vide le panier
  - `updateCartBadge()` - Met à jour l'affichage du compteur
  - `showCartNotification()` - Affiche une notification d'ajout au panier

- **Modifications**:
  - `checkLoginStatusAndReserve()` - Appelle maintenant `openAddToCartModal()` au lieu de `openReservationModalWithPharmacy()`

#### nav-client.php
- Ajout d'un bouton panier dans la navigation avec:
  - Icône panier FontAwesome
  - Badge compteur d'articles
  - Lien vers `openCartModal()`
- Chargement du script medicaments.js globally

#### profil.php
- **Nouvelles fonctions**:
  - `loadReservations()` - Récupère les réservations de l'utilisateur
  - `createReservationCard()` - Génère le HTML d'une carte de réservation
  - `openEditReservationModal()` - Affiche le modal de modification
  - `updateReservation()` - Met à jour la quantité via API
  - `deleteReservation()` - Annule une réservation via API

- **Modifications**:
  - Nouvel onglet "Mes Réservations" avec affichage dynamique
  - Compteur de réservations dans le menu

#### css/styles.css
- Nouvelles classes CSS:
  - `.cart-notification` - Notification d'ajout au panier
  - `.cart-badge` - Badge du panier
  - `#cart-badge` - Compteur du panier
  - `.cart-item` - Élément du panier
  - `.reservation-card` - Carte de réservation
  - `.reservation-status` - Statut avec codes couleur
  - `.status-pending`, `.status-confirmed`, `.status-cancelled`, `.status-picked`

### Backend

#### api/reservations.php
- **Nouvelles actions**:
  - `getByUser` - Récupère les réservations de l'utilisateur connecté
  - `update` - Met à jour la quantité d'une réservation (vérification du statut)
  - `delete` - Annule une réservation (statut → 'annulée')

- **Vérifications**:
  - Utilisateur appartient bien à la réservation
  - Réservation en attente seulement (pour modification/annulation)
  - Stock disponible pour modification

## Stockage des données

### localStorage
```javascript
medicineCart = [
  {
    medicineId: 1,
    pharmacyId: 2,
    medicineName: "Paracétamol",
    pharmacyName: "Pharmacie Centrale",
    quantity: 2
  }
]
```

### Database - Réservations
- `id` - ID unique
- `user_id` - Utilisateur
- `medicament_id` - Médicament réservé
- `pharmacie_id` - Pharmacie
- `quantite` - Quantité réservée
- `date_reservation` - Date de création
- `date_retrait` - Date du retrait
- `statut` - État de la réservation
- `notes` - Notes optionnelles

## Statuts de réservation
- **en attente** (jaune) - En cours de traitement par la pharmacie
- **confirmée** (vert) - Validée par la pharmacie
- **prête** (bleu) - Prête à être retirée
- **retirée** (gris) - Déjà retirée par le client
- **annulée** (rouge) - Annulée par le client ou la pharmacie

## Tests recommandés

1. **Recherche et ajout au panier**:
   - Rechercher un médicament
   - Vérifier l'affichage de la quantité disponible
   - Ajouter différentes quantités
   - Vérifier le badge du panier

2. **Gestion du panier**:
   - Modifier quantités
   - Supprimer articles
   - Vérifier la persistance (rechargement page)

3. **Validation des réservations**:
   - Valider le panier
   - Vérifier la création des réservations dans la DB
   - Vérifier le vidage du panier

4. **Gestion des réservations**:
   - Voir les réservations dans le profil
   - Modifier la quantité
   - Annuler une réservation
   - Vérifier l'impossibilité de modifier une réservation confirmée

5. **Connexion/Déconnexion**:
   - Essayer d'ajouter au panier sans être connecté
   - Vérifier la redirection
   - Vérifier que le panier persiste après reconnexion

## Notes de sécurité

- Vérification de l'authentification sur tous les endpoints API
- Vérification que l'utilisateur possède bien la réservation
- Vérification du stock lors de la modification
- Validation des statuts pour les modifications
- Hashage des mots de passe
- Protection CSRF via session

## Améliorations futures possibles

1. Ajouter un système de notifications
2. Permettre l'édition de l'adresse de livraison
3. Historique des réservations passées
4. Système de favoris de médicaments
5. Comparaison de prix entre pharmacies
6. Système de rappel de retrait
7. Paiement en ligne
8. Estimé de temps d'attente
