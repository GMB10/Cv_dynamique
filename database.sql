-- =============================================
-- Base de données CVGenPro (mise à jour)
-- =============================================

-- Table utilisateurs
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    fullname VARCHAR(255) DEFAULT NULL,
    role ENUM('user','admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Exemples d'administrateurs
INSERT INTO users (email, password, fullname, role)
VALUES 
('lid@iam.sn', SHA2('passer123',256), 'Admin LID', 'admin'),
('ghislain.banabakobassa@gmail.com', SHA2('passer123',256), 'Κρύπτερος', 'admin');

-- Table CVs utilisateurs
CREATE TABLE IF NOT EXISTS cvs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    template VARCHAR(50) NOT NULL,
    content LONGTEXT NOT NULL,
    photo LONGTEXT DEFAULT NULL,           -- Photo encodée en base64
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Index pour optimiser la recherche par utilisateur
CREATE INDEX idx_user_id ON cvs(user_id);