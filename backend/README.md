# Backend PharmaGarde - Documentation PHP

## Structure du Backend

```
backend/
├── api/
│   ├── auth.php          # Gestion login/inscription/logout
│   ├── users.php         # Gestion profil utilisateur
│   └── reservations.php  # Gestion réservations (authentification requise)
├── includes/
│   ├── config.php        # Configuration générale
│   ├── db.php            # Connexion à la base de données
│   └── functions.php     # Fonctions utilitaires
├── api-client.js         # Fonctions JavaScript pour appeler l'API
└── database.sql          # Schéma de la base de données
```

## Installation

### 1. Configuration MySQL

```bash
# Créer la base de données
mysql -u root -p < backend/database.sql
```

### 2. Configuration PHP

Modifiez les paramètres dans `backend/includes/config.php` :
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'pharmalokal');
define('DB_PORT', 3306);
```

### 3. Permissions des dossiers

Créez un dossier `logs` dans le backend avec les permissions d'écriture :
```bash
mkdir backend/logs
chmod 777 backend/logs
```

## API REST

### Auth Endpoints

#### 1. Inscription
- **URL**: `/backend/api/auth.php`
- **Méthode**: POST
- **Données**:
```json
{
    "action": "register",
    "nom": "Dupont",
    "prenom": "Jean",
    "email": "jean@example.com",
    "telephone": "0123456789",
    "password": "SecurePass123",
    "passwordConfirm": "SecurePass123"
}
```

#### 2. Connexion
- **URL**: `/backend/api/auth.php`
- **Méthode**: POST
- **Données**:
```json
{
    "action": "login",
    "email": "jean@example.com",
    "password": "SecurePass123"
}
```
- **Réponse réussie**:
```json
{
    "success": true,
    "user": {
        "id": 1,
        "email": "jean@example.com",
        "nom": "Dupont",
        "prenom": "Jean",
        "role": "client"
    }
}
```

#### 3. Déconnexion
- **URL**: `/backend/api/auth.php`
- **Méthode**: POST
- **Données**:
```json
{
    "action": "logout"
}
```

#### 4. Vérifier la session
- **URL**: `/backend/api/auth.php?action=check`
- **Méthode**: GET

### Users Endpoints (Authentification requise)

#### 1. Obtenir le profil
- **URL**: `/backend/api/users.php?action=getProfile`
- **Méthode**: GET
- **Résultat**:
```json
{
    "success": true,
    "user": {
        "id": 1,
        "nom": "Dupont",
        "prenom": "Jean",
        "email": "jean@example.com",
        "telephone": "0123456789",
        "role": "client"
    }
}
```

#### 2. Mettre à jour le profil
- **URL**: `/backend/api/users.php`
- **Méthode**: POST
- **Données**:
```json
{
    "action": "updateProfile",
    "nom": "Dupont",
    "prenom": "Jean",
    "telephone": "0987654321",
    "adresse": "123 Rue de la Paix"
}
```

#### 3. Changer le mot de passe
- **URL**: `/backend/api/users.php`
- **Méthode**: POST
- **Données**:
```json
{
    "action": "changePassword",
    "oldPassword": "AncienMDP123",
    "newPassword": "NouveauMDP456",
    "confirmPassword": "NouveauMDP456"
}
```

### Reservations Endpoints (Authentification obligatoire)

#### 1. Créer une réservation
- **URL**: `/backend/api/reservations.php`
- **Méthode**: POST
- **Données**:
```json
{
    "action": "create",
    "pharmacie_id": 1,
    "medicament_id": 2,
    "quantite": 2
}
```

#### 2. Obtenir mes réservations
- **URL**: `/backend/api/reservations.php?action=getMyReservations`
- **Méthode**: GET

#### 3. Annuler une réservation
- **URL**: `/backend/api/reservations.php`
- **Méthode**: POST
- **Données**:
```json
{
    "action": "cancel",
    "reservation_id": 1
}
```

## Utilisation depuis le Frontend

### 1. Inclure le fichier JavaScript
```html
<script src="/PharmaLocal/backend/api-client.js"></script>
```

### 2. Exemple d'utilisation - Inscription
```javascript
const result = await register({
    nom: 'Dupont',
    prenom: 'Jean',
    email: 'jean@example.com',
    telephone: '0123456789',
    password: 'SecurePass123',
    passwordConfirm: 'SecurePass123'
});

if (result.success) {
    console.log('Inscription réussie');
} else {
    console.error(result.error);
}
```

### 3. Exemple d'utilisation - Connexion
```javascript
const result = await login('jean@example.com', 'SecurePass123');

if (result.success) {
    localStorage.setItem('user_id', result.user.id);
    localStorage.setItem('user_email', result.user.email);
    // Rediriger vers profil
} else {
    console.error(result.error);
}
```

### 4. Exemple d'utilisation - Réservation
```javascript
// Vérifier l'authentification
await requireLogin(async (user) => {
    const result = await createReservation(
        pharmacieId = 1,
        medicamentId = 2,
        quantite = 2
    );
    
    if (result.success) {
        console.log('Réservation créée');
    }
});
```

## Points de sécurité importants

1. **Authentification obligatoire** pour les réservations
2. **Hash Bcrypt** pour les mots de passe
3. **Validation email** systématique
4. **Minimum 8 caractères** pour les mots de passe
5. **SQL injections** prévenues avec requêtes préparées
6. **Sessions** gérées côté serveur

## Fonctions utilitaires disponibles

### Dans functions.php

- `isLoggedIn()` - Vérifier si l'utilisateur est connecté
- `getCurrentUser()` - Obtenir l'utilisateur actuel
- `hashPassword($password)` - Hasher un mot de passe
- `verifyPassword($password, $hash)` - Vérifier un mot de passe
- `generateToken()` - Générer un token
- `isValidEmail($email)` - Valider un email
- `jsonResponse($data, $statusCode)` - Répondre en JSON
- `requireAuth()` - Vérifier l'authentification
- `logAction($userId, $action, $details)` - Loguer une action

## Erreurs courantes

### Erreur "Erreur de connexion à la base de données"
- Vérifier les identifiants MySQL dans `config.php`
- S'assurer que MySQL est en cours d'exécution
- Vérifier que la base de données a été créée

### Erreur "Authentification requise"
- Vérifier que l'utilisateur est connecté
- Vérifier que les cookies de session sont activés

### Erreur "Quantité insuffisante"
- Vérifier le stock dans la table `stocks`
- Vérifier que le medicament existe pour cette pharmacie

## Prochaines étapes

1. **Intégrer dans le frontend** :
   - Modifier `inscription.js` pour utiliser `/backend/api/auth.php`
   - Modifier `connexion.js` pour la connexion
   - Modifier `profil.js` pour afficher/modifier le profil

2. **Ajouter des fonctionnalités** :
   - Récupération des médicaments
   - Récupération des pharmacies
   - Système de recherche
   - Historique des réservations

3. **Sécurité supplémentaire** :
   - Implémenter HTTPS
   - Rate limiting
   - Two-factor authentication
   - Tokens JWT pour les sessions
