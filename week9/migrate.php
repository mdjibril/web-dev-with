<?php
require_once 'db_connect.php';

try {
    // 1. Create Users Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        full_name VARCHAR(100) NOT NULL,
        email VARCHAR(150) NOT NULL UNIQUE,
        password_hash VARCHAR(255) NOT NULL,
        role ENUM('admin', 'staff') NOT NULL DEFAULT 'staff',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    // 2. Clear old test accounts if they exist
    $pdo->exec("DELETE FROM users WHERE email IN ('admin@pharmacy.local', 'staff@pharmacy.local')");

    // 3. Generate Secure Cryptographic Hashes
    $admin_hash = password_hash('AdminPassword123!', PASSWORD_BCRYPT);$staff_hash = password_hash('StaffPassword123!', PASSWORD_BCRYPT);

    // 4. Seed Admin and Staff accounts
    $stmt =$pdo->prepare("INSERT INTO users (full_name, email, password_hash, role) VALUES (:name, :email, :hash, :role)");

    $stmt->execute([
        ':name'  => 'Pharmacy Director',
        ':email' => 'admin@pharmacy.local',
        ':hash'  => $admin_hash,
        ':role'  => 'admin'
    ]);

    $stmt->execute([
        ':name'  => 'Dispensary Staff',
        ':email' => 'staff@pharmacy.local',
        ':hash'  => $staff_hash,
        ':role'  => 'staff'
    ]);

    echo "Migration Successful! Created `users` table and seeded accounts.<br>";
    echo "Admin Account: admin@pharmacy.local | Pass: AdminPassword123!<br>";
    echo "Staff Account: staff@pharmacy.local | Pass: StaffPassword123!<br>";
} catch (PDOException $e) {
    die("Migration Failed: " . $e->getMessage());
}