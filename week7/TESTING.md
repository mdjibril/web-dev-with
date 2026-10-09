# Testing the Week 7 PHP + MySQL App

This guide covers the pages in this folder: adding medications, searching medications, viewing inventory alerts, and the XSS demonstration. The app connects to a MySQL database named `pharmacy_db`.

## Prerequisites

- PHP 8+ with the `pdo_mysql` extension
- MySQL 5.7+ or MariaDB
- The MySQL command-line client, or a database GUI

## 1. Create and seed the database

Run this SQL in MySQL. It provides the columns used by all Week 7 pages and sample records for the alert dashboard.

Use a fresh test database or migrate existing tables first. In particular, `CREATE TABLE IF NOT EXISTS` does not change a Week 6 `medications` table that uses `medication_id` as its primary key, and Week 7 also requires the new `stocks` table.

```sql
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
```

The add-medication form uses category ID `1`, so keep the sample category or update the form submission to use an existing category ID. If you rerun the seed inserts, avoid duplicating the sample data or recreate the database first.

## 2. Start the app

The connection settings in `db_connect.php` use host `localhost`, database `pharmacy_db`, user `root`, and a blank password. Update those settings if your local MySQL credentials differ.

From the workspace root, import the SQL if you saved it as `week7/schema.sql`:

```bash
mysql -u root < week7/schema.sql
```

Then start PHP's built-in server:

```bash
cd week7
php -S localhost:8000
```

Open these pages in a browser:

- `http://localhost:8000/add_medication.php`
- `http://localhost:8000/search_medication.php`
- `http://localhost:8000/alerts_dashboard.php`
- `http://localhost:8000/add_comment.php`

## 3. Test each page

### Add medication

Submit a medication name, optional brand, category ID `1`, and a numeric price. A valid submission should return to the form with a success message and add a row to `medications`:

```sql
SELECT id, category_id, name, brand, price FROM medications ORDER BY id DESC;
```

Try omitting a required field or submitting a non-numeric price. The form should show its error status. A direct GET request to `insert_medication.php` should redirect to the add form.

### Search medications

Open `search_medication.php?search=Amoxicillin` and confirm the matching medication appears. Try a name that is not present to check the empty-results case. This page is explicitly labeled insecure: it interpolates search input into SQL and renders values without HTML escaping. Use only local test data and do not expose this page to the internet.

### Inventory alerts

Open `alerts_dashboard.php`. The seeded data should show `Amoxicillin` as expiring within 30 days and low in stock, and `Ciprofloxacin` as low in stock but not expiring soon. `Paracetamol` should not appear in either alert list. This page requires both the `medications` and `stocks` tables.

### XSS demonstration

Open `add_comment.php`. It displays a hard-coded script payload in an unsafe section and HTML-escaped text in a safe section. In a browser, the unsafe section triggers the demonstration alert; the safe section displays the markup as text. This is intentionally vulnerable demo code, so run it only on a local development server.

## 4. Verify database connectivity

Check that PHP has the MySQL PDO driver enabled:

```bash
php -m | grep -i pdo_mysql
```

Inspect the seeded records with:

```bash
mysql -u root -e "USE pharmacy_db; SELECT * FROM medications; SELECT * FROM stocks;"
```

## Notes

- `db_connect.php` currently uses a blank MySQL password; configure credentials for your environment.
- The search page intentionally demonstrates unsafe SQL construction and unescaped HTML output. Do not use it with untrusted users or production data.
- The comment page is a static XSS demonstration; it does not read or write comments in the database.
