<?php
// 1. Include our database connection
require_once 'db_connect.php';

// 2. Ensure page is accessed only via POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 3. Extract and sanitize basic inputs
    $name = trim($_POST['name'] ?? '');
    $brand = trim($_POST['brand'] ?? '');
    $category_id = trim($_POST['category_id'] ?? '');
    $price = trim($_POST['price'] ?? '');

    // 4. Basic Input Validation
    if (empty($name) || empty($category_id) || empty($price) || !is_numeric($price)) {
        header("Location: add_medication.php?status=error");
        exit();
    }

    try {
        // 5. Construct secure SQL using Named Placeholders
        $sql = "INSERT INTO medications (category_id, name, brand, price) 
                VALUES (:category_id, :name, :brand, :price)";

        // 6. Prepare the statement inside MySQL
        $stmt = $pdo->prepare($sql);

        // 7. Execute the statement with variables bound safely
        $stmt->execute([
            ':category_id' => $category_id,
            ':name'        => $name,
            ':brand'       => $brand,
            ':price'       => $price
        ]);

        // 8. Redirect back with positive success confirmation
        header("Location: add_medication.php?status=success");
        exit();

    } catch (PDOException $e) {
        // Log error messages safely instead of exposing raw database exceptions
        error_log("Database Error: " . $e->getMessage());
        header("Location: add_medication.php?status=error");
        exit();
    }
} else {
    // Block direct GET access to this processor file
    header("Location: add_medication.php");
    exit();
}

