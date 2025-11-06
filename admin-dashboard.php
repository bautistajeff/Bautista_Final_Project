<?php
session_start();
require_once(__DIR__ . '/dbconnection.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

try {
    $stmt = $pdo->prepare("SELECT COUNT(*) AS total FROM medicines");
    $stmt->execute();
    $totalMedicines = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
} catch (PDOException $e) {
    $totalMedicines = 0;
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
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="#">Pharmacy Inventory</a>
        <div class="d-flex">
            <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
        </div>
    </div>
</nav>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>Admin Dashboard</h3>
        <div>
            <a href="view_graph.php" class="btn btn-info me-2">View Graph</a>
            <a href="add_medicine.php" class="btn btn-success">Add Medicine</a>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title">Total Medicines</h5>
            <p class="display-6 fw-bold text-primary"><?php echo $totalMedicines; ?></p>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="card-title mb-3">Recent Medicines</h5>
            <table class="table table-striped align-middle text-center">
                <thead class="table-primary">
                    <tr>
                        <th>ID</th>
                        <th>Medicine Name</th>
                        <th>Category</th>
                        <th>Stock</th>
                        <th>Price (₱)</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    try {
                        $stmt = $pdo->prepare("SELECT * FROM medicines ORDER BY id DESC LIMIT 10");
                        $stmt->execute();
                        $medicines = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        if ($medicines) {
                            foreach ($medicines as $row) {
                                $id = $row['id'];
                                echo "
                                <tr>
                                    <td>{$id}</td>
                                    <td>{$row['medicine_name']}</td>
                                    <td>{$row['category']}</td>
                                    <td>{$row['stock']}</td>
                                    <td>" . number_format($row['price'], 2) . "</td>
                                    <td>
                                        <a href='./api/edit_medicine_form.php?id={$id}' class='btn btn-warning btn-sm'>Edit</a>
                                        <a href='./api/delete_medicine.php?id={$id}' 
                                           class='btn btn-danger btn-sm'
                                           onclick=\"return confirm('Are you sure you want to delete this medicine?')\">Delete</a>
                                    </td>
                                </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6' class='text-center text-muted'>No medicines found.</td></tr>";
                        }
                    } catch (PDOException $e) {
                        echo "<tr><td colspan='6' class='text-danger text-center'>Database error.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
