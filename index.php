<?php
require_once __DIR__ . '/dbconnection.php';
requireRole('admin');

$stats = getInventoryStats($pdo);
$message = '';
if (isset($_GET['success'])) {
    $message = 'Medicine added successfully.';
} elseif (isset($_GET['updated'])) {
    $message = 'Medicine updated successfully.';
} elseif (isset($_GET['deleted'])) {
    $message = 'Medicine deleted successfully.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Pharmacy Inventory</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid px-4">
        <a class="navbar-brand fw-bold" href="#">Pharmacy Inventory</a>
        <div class="d-flex align-items-center gap-2">
            <span class="text-white small">Welcome, <?php echo htmlspecialchars($_SESSION['username'] ?? 'Admin'); ?></span>
            <a href="logout.php" class="btn btn-outline-light btn-sm">Logout</a>
        </div>
    </div>
</nav>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <h3 class="mb-0">Admin Dashboard</h3>
        <div class="d-flex gap-2">
            <a href="view_graph.php" class="btn btn-info">View Graph</a>
            <a href="add_medicine.php" class="btn btn-success">Add Medicine</a>
        </div>
    </div>

    <?php if ($message !== ''): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted">Total Medicines</div>
                    <div class="display-6 fw-bold text-primary"><?php echo $stats['total_medicines']; ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted">Total Stock</div>
                    <div class="display-6 fw-bold text-success"><?php echo $stats['total_stock']; ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted">Low Stock</div>
                    <div class="display-6 fw-bold text-warning"><?php echo $stats['low_stock']; ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted">Expired</div>
                    <div class="display-6 fw-bold text-danger"><?php echo $stats['expired']; ?></div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="card-title mb-3">Recent Medicines</h5>
            <div class="table-responsive">
                <table class="table table-striped align-middle text-center mb-0">
                    <thead class="table-primary">
                        <tr>
                            <th>ID</th>
                            <th>Medicine Name</th>
                            <th>Category</th>
                            <th>Stock</th>
                            <th>Price</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    try {
                        $stmt = $pdo->prepare('SELECT * FROM medicines ORDER BY id DESC LIMIT 20');
                        $stmt->execute();
                        $medicines = $stmt->fetchAll();

                        if ($medicines) {
                            foreach ($medicines as $row) {
                                $id = (int) $row['id'];
                                echo "
                                <tr>
                                    <td>{$id}</td>
                                    <td>" . htmlspecialchars($row['medicine_name']) . "</td>
                                    <td>" . htmlspecialchars($row['category']) . "</td>
                                    <td>" . (int) $row['stock'] . "</td>
                                    <td>" . formatCurrency((float) $row['price']) . "</td>
                                    <td>
                                        <a href='./api/edit_medicine_form.php?id={$id}' class='btn btn-warning btn-sm'>Edit</a>
                                        <a href='./api/delete_medicine.php?id={$id}' class='btn btn-danger btn-sm' onclick=\"return confirm('Are you sure you want to delete this medicine?')\">Delete</a>
                                    </td>
                                </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6' class='text-center text-muted'>No medicines found.</td></tr>";
                        }
                    } catch (PDOException $e) {
                        echo "<tr><td colspan='6' class='text-center text-danger'>Database error.</td></tr>";
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
