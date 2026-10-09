<?php

// Database connection details for the local MySQL server.
$host = 'localhost';
$dbname = 'pharmacy_db';
$username = 'root';
$password = '1069';

try {
    // Create a PDO instance to connect to the MySQL database.
    $pdo = new PDO(
        "mysql:host={$host};dbname={$dbname};charset=utf8mb4",
        $username,
        $password,
        [
            // Throw exceptions on database errors so they can be caught and handled properly.
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,

            // Return query results as associative arrays by default (e.g. ['id' => 1]).
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,

            // Disable emulated prepared statements to use native prepared statements for better security.
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );

    // Optional: uncomment this line if you want to confirm the connection is successful.
    // echo 'Connected successfully';
} catch (PDOException $e) {
    // Handle any connection or query errors securely.
    die('Database connection failed: ' . $e->getMessage());
}
