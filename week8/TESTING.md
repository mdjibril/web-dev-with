# Testing the Week 8 PHP + MySQL App

This guide covers the Week 8 login, session, role-based dashboard, and medication-entry pages. The app connects to a MySQL database named `pharmacy_db`.

## Prerequisites

- PHP 8+ with the `pdo_mysql` extension
- MySQL 5.7+ or MariaDB
- The MySQL command-line client, or a database GUI

## 1. Prepare the database

`migrate.php` creates and seeds the `users` table, but it does not create the database or medication tables. Create the database and the tables required by the medication form first. Run this SQL in MySQL:

```sql
CREATE DATABASE IF NOT EXISTS pharmacy_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE pharmacy_db;

CREATE TABLE IF NOT EXISTS categories (
  category_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(100) NOT NULL,
  PRIMARY KEY (category_id)
);

CREATE TABLE IF NOT EXISTS medications (
  medication_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  category_id INT UNSIGNED NOT NULL,
  name VARCHAR(150) NOT NULL,
  brand VARCHAR(100) DEFAULT NULL,
  price DECIMAL(10,2) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (medication_id),
  CONSTRAINT fk_medications_category FOREIGN KEY (category_id)
    REFERENCES categories(category_id) ON DELETE RESTRICT ON UPDATE CASCADE
);

INSERT INTO categories (name) VALUES ('Antibiotics');
```

If you already have a Week 6 or Week 7 database with these tables, use it instead of creating them again. Ensure category ID `1` exists because the form's example uses it.

The connection settings in `db_connect.php` are host `localhost`, database `pharmacy_db`, user `root`, and a blank password. Change them to match your local MySQL setup.

## 2. Run the migration and server

From the workspace root, start PHP's built-in server:

```bash
cd week8
php -S localhost:8000
```

In a browser, run the migration once:

```text
http://localhost:8000/migrate.php
```

It creates the `users` table and seeds these demo accounts:

| Role | Email | Password |
| --- | --- | --- |
| Admin | `admin@pharmacy.local` | `AdminPassword123!` |
| Staff | `staff@pharmacy.local` | `StaffPassword123!` |

The migration deletes and recreates these two seeded accounts each time it runs. Do not expose `migrate.php` on a public server; remove or restrict it after local setup.

Open `http://localhost:8000/login.php` to use the portal.

## 3. Test login and role access

1. Sign in with the admin credentials. Confirm the browser redirects to `admin_dashboard.php` and displays the admin name.
2. Log out, then sign in with the staff credentials. Confirm the browser redirects to `staff_dashboard.php` and displays the staff role.
3. While signed in as staff, visit `http://localhost:8000/admin_dashboard.php` directly. The page should return HTTP `403 Forbidden`.
4. While signed in as admin, visit `http://localhost:8000/staff_dashboard.php`. The page should load because both roles are allowed there.
5. In a private/incognito window with no active session, visit either dashboard URL. You should be redirected to `login.php?error=unauthorized`.
6. Test an incorrect password and an unknown email. Both should show the same invalid-credentials message. Submitting an empty email or password should show the missing-credentials message.
7. Log out and confirm the portal displays the signed-out message. Then revisit a dashboard URL and confirm that login is required again.

## 4. Test medication entry

Open `http://localhost:8000/add_medication.php`, enter a medication name, optional brand, category ID `1`, and a numeric price, then submit. The form should report success. Verify the new record in MySQL:

```sql
USE pharmacy_db;
SELECT medication_id, category_id, name, brand, price
FROM medications
ORDER BY medication_id DESC;
```

Also test a missing required field, a non-numeric price, and a direct GET request to `insert_medication.php`. Invalid input should return to the form with an error status; a GET request should redirect to the form.

**Access-control note:** `admin_dashboard.php` and `staff_dashboard.php` enforce roles, but `add_medication.php` and `insert_medication.php` currently do not require a login. The medication form and POST endpoint can therefore be accessed without authenticating. Treat this as a limitation when evaluating authorization; do not assume dashboard RBAC protects those files.

## 5. Current dashboard module links

The admin dashboard links to `../week-6-lab/add_medication.php` and `../week-7-lab/alerts_dashboard.php`. Those paths do not match this workspace's `week6/` and `week7/` directories, and the Week 8 folder itself does not include an alerts page. The dashboards and role checks can be tested independently; those module links may lead to a not-found page unless the paths are updated or the expected folders exist outside this workspace.

## 6. Troubleshooting

- Confirm MySQL is running and the `pharmacy_db` database exists before opening any PHP page.
- Check PHP has the MySQL PDO driver: `php -m | grep -i pdo_mysql`.
- If login fails after a migration, rerun `migrate.php` locally to recreate the seeded demo accounts.
- If medication insertion fails, confirm the `categories` and `medications` tables exist and category ID `1` is valid.
- Check the host, database name, username, and password in `db_connect.php` if the database connection fails.
