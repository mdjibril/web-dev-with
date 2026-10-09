CREATE DATABASE IF NOT EXISTS pharmacy_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE pharmacy_db;

CREATE TABLE IF NOT EXISTS categories (
  category_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(100) NOT NULL,
  PRIMARY KEY (category_id)
);

CREATE TABLE IF NOT EXISTS medications (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  category_id INT UNSIGNED NOT NULL,
  name VARCHAR(150) NOT NULL,
  brand VARCHAR(100) DEFAULT NULL,
  price DECIMAL(10,2) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  CONSTRAINT fk_medications_category FOREIGN KEY (category_id)
    REFERENCES categories(category_id) ON DELETE RESTRICT ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS stocks (
  stock_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  medication_id INT UNSIGNED NOT NULL,
  batch_number VARCHAR(50) NOT NULL,
  expiry_date DATE NOT NULL,
  quantity INT UNSIGNED NOT NULL,
  PRIMARY KEY (stock_id),
  CONSTRAINT fk_stocks_medication FOREIGN KEY (medication_id)
    REFERENCES medications(id) ON DELETE CASCADE ON UPDATE CASCADE
);

INSERT INTO categories (name) VALUES ('Antibiotics');
INSERT INTO medications (category_id, name, brand, price) VALUES
  (1, 'Amoxicillin', 'Acme', 5.99),
  (1, 'Ciprofloxacin', 'Northstar', 8.50),
  (1, 'Paracetamol', 'Acme', 3.25);

INSERT INTO stocks (medication_id, batch_number, expiry_date, quantity) VALUES
  (1, 'AMX-EXPIRING', DATE_ADD(CURDATE(), INTERVAL 14 DAY), 5),
  (2, 'CIP-LOW', DATE_ADD(CURDATE(), INTERVAL 90 DAY), 4),
  (3, 'PCM-HEALTHY', DATE_ADD(CURDATE(), INTERVAL 180 DAY), 25);