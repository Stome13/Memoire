-- Suppression de la base de données existante
DROP DATABASE IF EXISTS pharmalocal;

-- Création de la base de données
CREATE DATABASE pharmalocal;
USE pharmalocal;

-- Table des utilisateurs
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    telephone VARCHAR(20),
    adresse TEXT,
    password VARCHAR(255) NOT NULL,
    role ENUM('client', 'admin', 'pharmacie') DEFAULT 'client',
    date_inscription DATETIME DEFAULT CURRENT_TIMESTAMP,
    date_modification DATETIME ON UPDATE CURRENT_TIMESTAMP,
    INDEX(email),
    INDEX(role)
);

-- Table des pharmacies
CREATE TABLE pharmacies (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(150) NOT NULL,
    adresse TEXT NOT NULL,
    telephone VARCHAR(20),
    email VARCHAR(150),
    ville VARCHAR(100),
    horaire_ouverture TIME,
    horaire_fermeture TIME,
    pharmacien_id INT,
    date_inscription DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY(pharmacien_id) REFERENCES users(id),
    INDEX(nom)
);

-- Table des médicaments
CREATE TABLE medicaments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(150) NOT NULL,
    dosage VARCHAR(50),
    description TEXT,
    prix DECIMAL(10, 2),
    categorie VARCHAR(100),
    date_ajout DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX(nom),
    INDEX(categorie)
);

-- Table des stocks
CREATE TABLE stocks (
    id INT PRIMARY KEY AUTO_INCREMENT,
    pharmacie_id INT NOT NULL,
    medicament_id INT NOT NULL,
    quantite INT DEFAULT 0,
    date_modification DATETIME ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY(pharmacie_id) REFERENCES pharmacies(id),
    FOREIGN KEY(medicament_id) REFERENCES medicaments(id),
    UNIQUE KEY(pharmacie_id, medicament_id),
    INDEX(pharmacie_id),
    INDEX(medicament_id)
);

-- Table des réservations
CREATE TABLE reservations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    medicament_id INT NOT NULL,
    pharmacie_id INT NOT NULL,
    quantite INT NOT NULL,
    date_reservation DATETIME DEFAULT CURRENT_TIMESTAMP,
    date_retrait DATETIME,
    statut ENUM('en attente', 'confirmée', 'prête', 'retirée', 'annulée') DEFAULT 'en attente',
    notes TEXT,
    FOREIGN KEY(user_id) REFERENCES users(id),
    FOREIGN KEY(medicament_id) REFERENCES medicaments(id),
    FOREIGN KEY(pharmacie_id) REFERENCES pharmacies(id),
    INDEX(user_id),
    INDEX(pharmacie_id),
    INDEX(statut),
    INDEX(date_reservation)
);

-- Table des gardes (pharmacies de garde)
CREATE TABLE gardes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    pharmacie_id INT NOT NULL,
    date_garde DATE NOT NULL,
    heure_debut TIME,
    heure_fin TIME,
    FOREIGN KEY(pharmacie_id) REFERENCES pharmacies(id),
    UNIQUE KEY(pharmacie_id, date_garde),
    INDEX(date_garde)
);

-- Table des logs
CREATE TABLE logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    action VARCHAR(100),
    details JSON,
    date_action DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY(user_id) REFERENCES users(id),
    INDEX(user_id),
    INDEX(date_action)
);

-- Insérer un utilisateur admin par défaut (password: Admin123!)
INSERT INTO users (nom, prenom, email, password, role) VALUES (
    'Admin',
    'Système',
    'admin@pharmalokal.com',
    '$2y$10$yacFOEtDrS0FpDg1xnlr8.VgUTmz5I2LUcInuFTcARUbZ7GDwQLWG',
    'admin'
);

-- Insérer un utilisateur pharmacien de test (password: Pharmacien123!)
INSERT INTO users (nom, prenom, email, password, role, telephone, adresse) VALUES (
    'Sani',
    'Jean',
    'pharmacien@pharmalokal.com',
    '$2y$10$7vCQxt5DNBM4XLedYy.sWOljx3gpYJj7ffv979GVkVE/1xFYyL0vy',
    'pharmacie',
    '0123456780',
    '123 Rue de la Paix'
);

-- Insérer des données de test
INSERT INTO medicaments (nom, dosage, categorie, prix) VALUES
('Paracétamol', '500mg', 'Analgésique', 1000),
('Ibuprofène', '200mg', 'Anti-inflammatoire', 500),
('Amoxicilline', '500mg', 'Antibiotique', 1500),
('Aspirin', '100mg', 'Anticoagulant', 800),
('Vitamin C', '1000mg', 'Vitamine', 600);

INSERT INTO pharmacies (nom, adresse, telephone, email, horaire_ouverture, horaire_fermeture, pharmacien_id) VALUES
('Pharmacie Centrale', '123 Rue de la Paix', '0123456789', 'central@pharmalokal.com', '08:00:00', '20:00:00', 2),
('Pharmacie du Plateau', '456 Avenue du Commerce', '0987654321', 'plateau@pharmalokal.com', '08:30:00', '19:30:00', NULL),
('Pharmacie du Port', '789 Boulevard Maritime', '0555555555', 'port@pharmalokal.com', '08:00:00', '21:00:00', NULL);

INSERT INTO stocks (pharmacie_id, medicament_id, quantite) VALUES
(1, 1, 20),
(1, 2, 15),
(2, 2, 10),
(2, 3, 5);
