# 📖 Guide d'Utilisation - Espace Pharmacie

## 🚀 Démarrage Rapide

### Accès à l'Espace Pharmacie

1. **Via la page d'accueil PharmaGarde**
   - Allez sur la page d'accueil `/users/clients/index.html`
   - Cliquez sur le bouton "Connexion"

2. **Connexion (Page Unifiée)**
   - Email: Votre adresse email enregistrée
   - Mot de passe: Votre mot de passe sécurisé
   - Cliquez sur "Se connecter"
   - **Le système détecte automatiquement votre rôle**

3. **Redirection Automatique**
   - ✅ Si vous êtes pharmacien (rôle=pharmacie) → Tableau de bord pharmacie
   - ✅ Si vous êtes client (rôle=client) → Tableau de bord client
   - ✅ Si vous êtes administrateur (rôle=admin) → Tableau de bord administrateur
   - Vous arrivez sur le Tableau de Bord
   - Navigation via la barre latérale (sidebar)

---

## 📊 1. Tableau de Bord (`/dashboard.html`)

### Aperçu
La page principale affiche:
- **4 cartes statistiques** en haut (médicaments, réservations, alertes, revenu)
- **Réservations récentes** (tableaux avec actions)
- **Alertes de stock** (médicaments en faible stock)
- **Actions rapides** (accès rapide aux fonctions principales)

### Actions Disponibles
- **Voir détails**: Cliquez sur une réservation
- **Confirmer**: Valide une réservation en attente
- **Annuler**: Rejette une réservation
- **Réapprovisionner**: Accès direct à la gestion des stocks

---

## 💊 2. Gestion des Médicaments (`/medicaments.html`)

### Vue des Médicaments
Liste complète de tous les médicaments de votre pharmacie

### Recherche & Filtrage
- **Recherche par nom**: Tapez le nom du médicament
- **Filtrer par catégorie**: Analgésique, Antibiotique, Vitamine, etc.
- **Filtrer par statut**: 
  - ✅ Disponible (stock > 100)
  - ⚠️ Stock faible (50-100)
  - ❌ Expiré

### Actions par Médicament
- **✏️ Modifier**: Ouvre la modale d'édition
  - Changez: nom, dosage, prix, quantité
  - Validez vos modifications
- **🗑️ Supprimer**: Retire le médicament (avec confirmation)

### Colonnes du Tableau
| Colonne | Description |
|---------|-------------|
| # | Numéro séquentiel |
| Nom | Nom du médicament |
| Dosage | Dosage (ex: 500mg) |
| Quantité | Stock actuel |
| Prix | Prix unitaire en FCFA |
| Expiration | Date d'expiration |
| Statut | État du stock |

### Pagination
- Navigez entre les pages
- Affichage de 10 médicaments par page

---

## ➕ 3. Ajouter un Médicament (`/medicament-add.html`)

### Étapes d'Ajout

#### 1️⃣ Informations de Base
- **Nom**: Paracétamol, Amoxicilline, etc.
- **Dosage**: 500mg, 250mg, 1000mg
- **Catégorie**: Sélectionnez dans la liste
- **Description**: Courte description (optionnel)

#### 2️⃣ Informations de Stock
- **Prix**: Montant en FCFA
- **Quantité**: Nombre d'unités en stock

#### 3️⃣ Dates Importantes
- **Date d'expiration**: ⚠️ OBLIGATOIRE
- **Date de fabrication**: Date de production

#### 4️⃣ Informations Supplémentaires
- **N° de lot**: LOT123456 (traçabilité)
- **Fournisseur**: Nom du fournisseur
- **Notes**: Observations additionnelles

### Validation
- Tous les champs marqués d'un * sont obligatoires
- Erreurs surlignées en rouge = à corriger
- Bouton "Ajouter" active une fois tout validé

### Après Ajout
- Message de confirmation
- Redirection possible vers la liste
- Nouveau médicament apparaît dans la liste

---

## 📦 4. Gestion des Stocks (`/stocks.html`)

### Aperçu des Stocks
Quatre cartes principales:
- 📊 **Total en stock**: Nombre total d'unités
- ✅ **Références**: Nombre de médicaments différents
- ⚠️ **Stock faible**: Médicaments < 50 unités
- ❌ **Expirés**: Médicaments hors date

### Inventaire Détaillé

**Chaque ligne affiche:**
- Nom et lot du médicament
- Quantité actuelle (badge coloré)
- Seuil minimum requis
- Statut (OK / Faible / Critique)
- Valeur stock (quantité × prix)

### Mettre à Jour le Stock

1. Cliquez sur le bouton ✏️ d'une ligne
2. La modale "Mettre à jour le stock" s'ouvre
3. Remplissez:
   - **Nouvelle quantité**: À ajouter ou retirer
   - **Raison**: Approvisionnement / Retrait / Correction / Expiration
   - **Notes**: Détails additionnels
4. Cliquez "Enregistrer"

### Importer en Masse
- Cliquez sur "Importer"
- Téléchargez un fichier CSV
- Format: Nom | Dosage | Quantité | Prix | Expiration
- Vérifiez avant d'importer

### Seuil d'Alerte
Les médicaments en dessous du seuil affichent une alerte automatique

---

## 🤝 5. Gestion des Réservations (`/reservations.html`)

### Navigation par Onglet

#### 🕐 En Attente (Orange)
Réservations needing action:
- Client: Nom et téléphone du client
- Médicament: Médicament réservé
- Actions disponibles:
  - ✅ **Confirmer**: Valide la réservation
  - ❌ **Annuler**: Rejette la demande

#### ✅ Confirmée (Bleu)
Réservations approuvées:
- Affiche les détails
- Status: En attente de retrait ou Retiré
- Cliquez "Marquer retiré" quand client prend le médicament

#### ❌ Annulée (Rouge)
Réservations rejetées:
- Raison de l'annulation
- Affichage informatif (non-éditable)

### Fonctionnalités Utiles

**Recherche:**
- Tapez nom client ou nom du médicament
- Filtre en temps réel

**Confirmation rapide:**
- Cliquez ✅ Confirmer
- Message de succès immédiat
- Modale se ferme

**Marquage retrait:**
- Cliquez "Marquer retiré"
- Statut change à "Retiré"
- Date de retrait enregistrée

---

## 🛡️ 6. Pharmacies de Garde (`/garde.html`)

### Statut Actuel
Affiche:
- 🟢 Status: ACTIVE (si vous êtes en garde)
- ⏰ Jusqu'au: Date et heure de fin
- 📍 Votre pharmacie: Affichée avec adresse

### Calendrier des Gardes
Liste de tous vos créneaux de garde planifiés

**Pour chaque garde:**
| Info | Détail |
|------|--------|
| Date début | Quand ça commence |
| Date fin | Quand ça se termine |
| Horaires | 20h00 - 08h00 (exemple) |
| Statut | Active / Planifiée |

### Déclarer une Garde

1. Cliquez "Déclarer garde"
2. Remplissez:
   - Date début et fin
   - Horaire ouverture (20h00)
   - Horaire fermeture (08h00)
   - Notes (optionnel)
3. Enregistrez

### Modifier une Garde
1. Trouvez la garde dans le calendrier
2. Cliquez le bouton ✏️
3. Modifiez les champs
4. Enregistrez

### Supprimer une Garde
1. Cliquez le bouton 🗑️
2. Confirmez la suppression

### Pharmacies de Garde à Proximité
Affiche les autres pharmacies en garde:
- Nom et adresse
- Statut de garde
- Bouton d'appel téléphonique direct

---

## 👤 7. Profil & Paramètres (`/profil.html`)

### 5 Onglets Disponibles

#### 👤 PROFIL
**Votre photo et informations personnelles**

Affichage:
- Photo profil (avatar)
- Nom complet
- Titre professionnel

Actions:
- Cliquez "Changer photo" pour mettre à jour
- Modifiez: Prénom, Nom, Email, Téléphone
- Mettez à jour: Numéro de licence, Région/Spécialité
- Ajoutez une biographie

#### 🏥 PHARMACIE
**Informations de votre pharmacie**

Champs éditables:
- Nom de la pharmacie
- Sigle (ex: PDS)
- Adresse complète
- Téléphone
- Email
- Horaires d'ouverture/fermeture
- Numéro de licence

#### 🔒 SÉCURITÉ
**Gestion du mot de passe et des sessions**

Sections:
1. **Changer mot de passe**
   - Mot de passe actuel
   - Nouveau mot de passe
   - Confirmer le nouveau
   - Cliquez "Changer"

2. **Sessions actives**
   - Liste des appareils connectés
   - IP address
   - Dernière activité
   - Statut (Actif)

#### 🔔 NOTIFICATIONS
**Préférences de notifications**

Options disponibles:
- ✅ Nouvelles réservations
- ✅ Alertes stock
- ☐ Mises à jour système (optionnel)
- ✅ Rappels de garde

Cochez/décochez selon vos préférences

#### ⚙️ PARAMÈTRES
**Réglages généraux**

Options:
- **Langue**: Français, English, etc.
- **Thème**: Clair, Sombre, Automatique
- **Seuil stock**: Set minimum quantity alert (ex: 50)

**Zone Danger:**
- Suppression du compte (Bouton Rouge)
- ⚠️ Action irréversible

---

## ⌨️ Raccourcis Clavier & Tips

### Navigation
- **Sidebar**: Toujours visible sur desktop
- **Mobile**: Menu burger en haut à gauche
- **Retour**: Bouton flèche gauche en haut

### Formulaires
- **Tab**: Passer au champ suivant
- **Enter**: Soumettre dans certains cas
- **Escape**: Fermer une modale

### Recherche
- **Ctrl+F**: Recherche dans la page
- **Ctrl+A après recherche**: Select all results

---

## 🎯 Cas d'Usage Courants

### 📥 J'ai reçu des nouveaux médicaments
1. Aller à "Ajouter médicament"
2. Remplir tous les détails
3. Cliquer "Ajouter"
4. Vérifier dans "Gestion des médicaments"

### 💬 Un client réserve un médicament
1. Allez à "Réservations"
2. Onglet "En attente"
3. Vérifiez le stock disponible
4. Cliquez "Confirmer"
5. Client retire le médicament
6. Cliquez "Marquer retiré"

### ⚠️ Stock d'un médicament baisse
1. Allez à "Gestion des stocks"
2. Trouvez le médicament
3. Cliquez le bouton ✏️
4. Ajoutez la nouvelle quantité
5. Sélectionnez "Approvisionnement"
6. Enregistrez

### 🛡️ Je suis de garde cette nuit
1. Allez à "Pharmacies de garde"
2. Cliquez "Déclarer garde"
3. Entrez aujourd'hui jusqu'à demain 08h00
4. Horaires: 20h00 à 08h00
5. Enregistrez
6. ✅ Vous apparaissez maintenant en ligne

---

## 🆘 Problèmes Courants

### Mon mot de passe ne fonctionne pas
→ Cliquez "Mot de passe oublié?" sur la page de connexion

### Je ne vois pas ma réservation
→ Vérifiez l'onglet (peut-être "Confirmée" au lieu de "En attente")

### Mon stock n'est pas à jour
→ Aller à "Gestion des stocks" et mettre à jour manuellement

### Un médicament n'apparaît pas
→ Utilisez la recherche ou filtres pour vérifier

---

## 📞 Support

Pour toute question:
- **Email**: support@pharmaguard.bj
- **Téléphone**: +229 21 XX XX XX
- **Chat**: Disponible en haut à droite (9h-17h)

---

**Dernière mise à jour**: 30 Mars 2026  
**Version**: 1.0
