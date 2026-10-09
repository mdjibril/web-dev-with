# Testing the pharmacy schema

This document shows how to test the SQL schema in `pharmacy_schema.md` locally (quick Docker method or local MySQL) and includes verification queries and test inserts.

## Prerequisites
- Docker (recommended) or a local MySQL / MariaDB server
- `mysql` CLI client (or a GUI like DBeaver / MySQL Workbench)
- (Optional) VS Code: install the SQLTools extension + MySQL/MariaDB driver

## 1. Extract the SQL to a file
Run from the `week5` folder:

```bash
sed -n '/^```sql/,/^```/p' pharmacy_schema.md | sed '1d;$d' > pharmacy_schema.sql
```

## 2A. Quick test with Docker
1. Start MySQL 8 in Docker:

```bash
docker run --name mysql-test -e MYSQL_ROOT_PASSWORD=root -e MYSQL_DATABASE=pharmacy -p 3306:3306 -d mysql:8.0 --default-authentication-plugin=mysql_native_password
```

2. Wait a few seconds for startup, then import the schema and verify:

```bash
sleep 5
docker exec -i mysql-test mysql -uroot -proot pharmacy < pharmacy_schema.sql
docker exec -it mysql-test mysql -uroot -proot -e "USE pharmacy; SHOW TABLES; SHOW CREATE TABLE medications\G"
```

3. (Optional) Connect with a GUI on `localhost:3306` using `root` / `root`.

To stop and remove the container when done:

```bash
docker stop mysql-test && docker rm mysql-test
```

## 2B. Test on a local MySQL install (Ubuntu example)

```bash
sudo apt update
sudo apt install mysql-server mysql-client
sudo systemctl start mysql
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS pharmacy;"
mysql -u root -p pharmacy < pharmacy_schema.sql
mysql -u root -p -e "USE pharmacy; SHOW TABLES; SHOW CREATE TABLE medications\G"
```

## 3. Verify with test inserts
Run these queries after importing the schema (replace `-p` prompts as needed):

```sql
INSERT INTO categories (name, description) VALUES ('Analgesics', 'Pain relief');
INSERT INTO medications (category_id, name, brand, description, price) VALUES (1, 'Paracetamol', 'Acme', '500mg tablets', 2.50);
INSERT INTO stocks (medication_id, batch_number, quantity, expiry_date) VALUES (1, 'BATCH001', 100, '2027-12-31');

SELECT * FROM categories;
SELECT * FROM medications;
SELECT * FROM stocks;
```

If a foreign key or unique constraint blocks an insert, check the referenced table and fix the referenced row values.

## 4. Quick verification queries

```sql
-- Show foreign keys
SELECT CONSTRAINT_NAME, TABLE_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME
FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
WHERE REFERENCED_TABLE_SCHEMA = 'pharmacy' AND REFERENCED_TABLE_NAME IS NOT NULL;

-- Count rows
SELECT 'categories', COUNT(*) FROM categories;
SELECT 'medications', COUNT(*) FROM medications;
SELECT 'stocks', COUNT(*) FROM stocks;
```

## 5. Optional: Docker Compose snippet
Create `docker-compose.yml` to spin up MySQL and persist data:

```yaml
version: '3.8'
services:
  db:
    image: mysql:8.0
    environment:
      MYSQL_ROOT_PASSWORD: root
      MYSQL_DATABASE: pharmacy
    ports:
      - 3306:3306
    volumes:
      - db_data:/var/lib/mysql

volumes:
  db_data:
```

After bringing `db` up (`docker compose up -d`), import the schema as in the Docker steps.

## 6. VS Code tips
- Install `SQLTools` and the `SQLTools MySQL/MariaDB` driver to run queries from the editor and browse tables.
- Use the Command Palette → `SQLTools: Add new connection` to connect to `localhost:3306`.

---
If you want, I can: add the `pharmacy_schema.sql` file, create the `docker-compose.yml` file, or run a quick test container for you now—tell me which.
