<?php
require_once 'db_connect.php';

try {
    // 1. Fetch medications that are expiring in the next 30 days
    $expiry_sql = "SELECT m.name, s.batch_number, s.expiry_date, s.quantity 
                   FROM stocks s
                   JOIN medications m ON s.medication_id = m.id
                   WHERE s.expiry_date <= DATE_ADD(CURDATE(), INTERVAL 30 DAY) 
                     AND s.expiry_date >= CURDATE()";
    
    $expiry_stmt = $pdo->query($expiry_sql);
    $expiring_items = $expiry_stmt->fetchAll();

    // 2. Fetch low-stock alert logic items (Quantity less than 10)
    $stock_sql = "SELECT m.name, s.batch_number, s.quantity 
                  FROM stocks s
                  JOIN medications m ON s.medication_id = m.id
                  WHERE s.quantity < 10";
                  
    $stock_stmt = $pdo->query($stock_sql);
    $low_stock_items = $stock_stmt->fetchAll();

} catch (PDOException $e) {
    die("Query Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Pharmacy Control Alert Center</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
    <h2 class="mb-4">Inventory Warning Dashboard</h2>

    <div class="row">
        <!-- Expiring Soon Panel -->
        <div class="col-md-6">
            <div class="card border-warning mb-3">
                <div class="card-header bg-warning text-dark"><strong>⚠️ Expiring Within 30 Days</strong></div>
                <div class="card-body">
                    <?php if (empty($expiring_items)): ?>
                        <p class="text-muted">No items expiring soon.</p>
                    <?php else: ?>
                        <ul class="list-group">
                            <?php foreach ($expiring_items as $item): ?>
                                <li class="list-group-item">
                                    <strong><?php echo htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8'); ?></strong> 
                                    (Batch: <?php echo htmlspecialchars($item['batch_number'], ENT_QUOTES, 'UTF-8'); ?>) <br>
                                    <small class="text-danger">Expires: <?php echo $item['expiry_date']; ?> | Qty: <?php echo $item['quantity']; ?></small>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Low Stock Panel -->
        <div class="col-md-6">
            <div class="card border-danger mb-3">
                <div class="card-header bg-danger text-white"><strong>🚨 Low-Stock Critical Alert (&lt; 10 Units)</strong></div>
                <div class="card-body">
                    <?php if (empty($low_stock_items)): ?>
                        <p class="text-muted">All stocks are currently healthy.</p>
                    <?php else: ?>
                        <ul class="list-group">
                            <?php foreach ($low_stock_items as $item): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>
                                        <strong><?php echo htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8'); ?></strong> 
                                        <small class="text-muted">(Batch: <?php echo htmlspecialchars($item['batch_number'], ENT_QUOTES, 'UTF-8'); ?>)</small>
                                    </span>
                                    <span class="badge bg-danger rounded-pill"><?php echo $item['quantity']; ?> left</span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
