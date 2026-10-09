<?php
require_once 'db_connect.php';

$search = $_GET['search'] ?? '';

// INSECURE: Directly concatenating user input into the SQL string
$sql = "SELECT * FROM medications WHERE name = '$search'";

try {
    // We execute the query directly without preparing it!
    $query = $pdo->query($sql);
    $results = $query->fetchAll();
} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Insecure Search Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
    <h3>Search Medications (Insecure)</h3>
    <form method="GET" class="d-flex gap-2 mb-4">
        <input type="text" name="search" class="form-control" value="<?php echo $search; ?>">
        <button type="submit" class="btn btn-danger">Search</button>
    </form>

    <p><strong>Raw SQL Executed:</strong> <code class="text-danger"><?php echo $sql; ?></code></p>

    <table class="table table-bordered">
        <thead>
            <tr><th>ID</th><th>Name</th><th>Price</th></tr>
        </thead>
        <tbody>
            <?php foreach ($results as $row): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['name']; ?></td>
                    <td>$<?php echo $row['price']; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
