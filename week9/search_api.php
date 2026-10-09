<?php
require_once 'db_connect.php';

// Tell the browser to expect pure JSON data, not HTML
header('Content-Type: application/json; charset=utf-8');

// Get the search word from the URL, e.g., search_api.php?query=panadol
$query = trim($_GET['query'] ?? '');

// If the user typed nothing, return an empty list immediately
if ($query === '') {
    echo json_encode([]);
    exit();
}

try {
    // Look for drugs whose names contain the typed letters anywhere
    $sql = "SELECT id, name, brand, price FROM medications WHERE name LIKE :searchTerm LIMIT 10";
    $stmt = $pdo->prepare($sql);
    
    // The '%' wildcards mean "any letters before or after"
    $stmt->execute([':searchTerm' => "%" . $query . "%"]);
    $medications = $stmt->fetchAll();

    // Convert the PHP array into JSON text and send it out
    echo json_encode($medications);
} catch (PDOException $e) {
    // If the database fails, return an empty array safely
    echo json_encode([]);
}