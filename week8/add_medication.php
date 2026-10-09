<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pharmacy System - Add Medication</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5" style="max-width: 600px;">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h3 class="mb-0">Add New Medication</h3>
            </div>
            <div class="card-body">
                
                <!-- Display status messages from URL -->
                <?php if (isset($_GET['status']) && $_GET['status'] == 'success'): ?>
                    <div class="alert alert-success">Medication successfully added!</div>
                <?php elseif (isset($_GET['status']) && $_GET['status'] == 'error'): ?>
                    <div class="alert alert-danger">Error: Missing or invalid inputs.</div>
                <?php endif; ?>

                <form action="insert_medication.php" method="POST">
                    <div class="mb-3">
                        <label class="form-label">Medication Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Brand</label>
                        <input type="text" name="brand" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Category ID (Run `1` for Antibiotics)</label>
                        <input type="number" name="category_id" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Price ($)</label>
                        <input type="number" step="0.01" name="price" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Save Medication</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
