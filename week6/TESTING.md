# Testing the PHP + MySQL app (week6)

This document explains how to run and test the PHP files in this folder (`add_medication.php`, `insert_medication.php`, `db_connect.php`) using either a local PHP/MySQL install or Docker.

## Prerequisites
- PHP 8+ with `pdo_mysql` extension
- MySQL 5.7+ or MySQL 8 / MariaDB
- `mysql` CLI client (or a GUI like DBeaver)
- (Optional) Docker and Docker Compose
- VS Code extensions: **PHP Intelephense**, **SQLTools** (+ MySQL driver)

## 1 — Create the database and tables
Create a minimal schema that matches the PHP code (the code expects a database named `pharmacy_db`):

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
  CONSTRAINT fk_medications_category FOREIGN KEY (category_id) REFERENCES categories(category_id) ON DELETE RESTRICT ON UPDATE CASCADE
);

-- Insert a sample category (the form mentions "Run `1` for Antibiotics")
INSERT INTO categories (name) VALUES ('Antibiotics');
```

Save that SQL as `week6/schema.sql` (optional) and import it, or run the commands directly in your MySQL client.

## 2 — Option A: Use local MySQL + PHP built-in server

1. Import the schema (from the workspace root):

```bash
mysql -u root -p < week6/schema.sql
```

2. Serve the PHP files from the `week6` folder:

```bash
cd week6
php -S localhost:8000
```

3. Open a browser to `http://localhost:8000/add_medication.php` and submit the form. The form posts to `insert_medication.php` and will redirect back with `?status=success` or `?status=error`.

4. To inspect the database, use `mysql` or SQLTools in VS Code:

```bash
mysql -u root -p -e "USE pharmacy_db; SELECT * FROM medications;"
```

Or use a curl POST to simulate the form:

```bash
curl -X POST -d "name=TestMed&brand=Acme&category_id=1&price=5.99" http://localhost:8000/insert_medication.php -v
```

## 3 — Option B: Quick Docker (recommended, isolated)

Start a MySQL container and map port 3306:

```bash
docker run --name mysql-test -e MYSQL_ROOT_PASSWORD=root -e MYSQL_DATABASE=pharmacy_db -d mysql:8.0 --default-authentication-plugin=mysql_native_password
```

Import the schema:

```bash
sleep 5
docker exec -i mysql-test mysql -uroot -proot pharmacy_db < week6/schema.sql
```

Serve PHP locally (the PHP built-in server connects to that MySQL instance on `localhost:3306`):

```bash
cd week6
php -S localhost:8000
```

Or run a full stack with `php:8-apache` + `mysql` in Docker Compose (see next section).

## 4 — Optional: Docker Compose snippet
Create a `docker-compose.yml` at the workspace root to run Apache+PHP and MySQL together:

```yaml
version: '3.8'
services:
  db:
    image: mysql:8.0
    environment:
      MYSQL_ROOT_PASSWORD: root
      MYSQL_DATABASE: pharmacy_db
    ports:
      - 3306:3306
    volumes:
      - db_data:/var/lib/mysql

  web:
    image: php:8.2-apache
    ports:
      - 8080:80
    volumes:
      - ./week6:/var/www/html:cached
    depends_on:
      - db

volumes:
  db_data:
```

After creating it, run:

```bash
docker compose up -d
# then import schema into db container
docker exec -i $(docker-compose ps -q db) mysql -uroot -proot pharmacy_db < week6/schema.sql
```

Open `http://localhost:8080/add_medication.php` to use the form.

## 5 — VS Code tips
- Install **PHP Intelephense** for code intelligence.
- Install **SQLTools** + MySQL driver to run queries and browse tables without leaving VS Code.
- Use the built-in terminal to run `php -S` and `mysql` commands.

## 6 — Troubleshooting
- If `db_connect.php` fails, check credentials: host (`localhost`), `dbname` (`pharmacy_db`), username/password in `db_connect.php`.
- Ensure the `pdo_mysql` extension is enabled: `php -m | grep pdo_mysql`.
- Check PHP error log or enable temporary debugging by uncommenting `echo 'Connected successfully';` in `db_connect.php` (not recommended in production).

---
If you want, I can also:
- create `week6/schema.sql` with the SQL above, or
- add `docker-compose.yml` to the repo and bring up the stack for a live test.
