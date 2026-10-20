<?php
require_once __DIR__ . '/dbconnection.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

if ($_SESSION['role'] === 'admin') {
    header('Location: admin-dashboard.php');
    exit;
}

if (!empty($_SESSION['request_success'])) {
    $requestMessage = $_SESSION['request_success'];
    unset($_SESSION['request_success']);
} else {
    $requestMessage = '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard | Pharmacy Inventory</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-success">
    <div class="container-fluid px-4">
        <a class="navbar-brand fw-bold" href="#">Pharmacy Inventory</a>
        <div class="d-flex align-items-center gap-2">
            <span class="text-white small">User: <?php echo htmlspecialchars($_SESSION['username'] ?? 'Customer'); ?></span>
            <a href="logout.php" class="btn btn-outline-light btn-sm">Logout</a>
        </div>
    </div>
</nav>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <h3 class="mb-0">User Dashboard</h3>
        <a href="request_medicine.php" class="btn btn-primary">Request Medicine</a>
    </div>

    <?php if ($requestMessage !== ''): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($requestMessage); ?></div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="card-title mb-3">Available Medicines</h5>
            <div class="table-responsive">
                <table class="table table-striped align-middle text-center mb-0">
                    <thead class="table-success">
                        <tr>
                            <th>Medicine</th>
                            <th>Category</th>
                            <th>Stock</th>
                            <th>Price</th>
                            <th>Expiry Date</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $stmt = $pdo->query('SELECT * FROM medicines ORDER BY medicine_name ASC');
                    $medicines = $stmt->fetchAll();

                    if ($medicines) {
                        foreach ($medicines as $row) {
                            echo "
                            <tr>
                                <td>" . htmlspecialchars($row['medicine_name']) . "</td>
                                <td>" . htmlspecialchars($row['category']) . "</td>
                                <td>" . (int) $row['stock'] . "</td>
                                <td>" . formatCurrency((float) $row['price']) . "</td>
                                <td>" . htmlspecialchars($row['expiry_date']) . "</td>
                            </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5' class='text-center text-muted'>No medicines available right now.</td></tr>";
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</body>
</html>
