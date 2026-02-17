<?php
require_once "dbconnection.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $medicine_name = trim($_POST['medicine_name']);
    $category = trim($_POST['category']);
    $stock = trim($_POST['stock']);
    $price = trim($_POST['price']);
    $expiry_date = trim($_POST['expiry_date']);
    $description = trim($_POST['description']);

    if (!empty($medicine_name) && !empty($category) && !empty($stock) && !empty($price) && !empty($expiry_date)) {
        $stmt = $pdo->prepare("INSERT INTO medicines (medicine_name, category, stock, price, expiry_date, description, created_at) 
                               VALUES (?, ?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$medicine_name, $category, $stock, $price, $expiry_date, $description]);

        header("Location: admin-dashboard.php?success=1");
        exit();
    } else {
        $error = "Please fill in all required fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Medicine | Inventory</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="d-flex justify-content-center align-items-center vh-100">
    <div class="card shadow p-4" style="width: 400px;">
        <h4 class="text-center mb-3"><strong>➕ Add New Medicine</strong></h4>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Medicine Name</label>
                <input type="text" name="medicine_name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Category</label>
                <input type="text" name="category" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Stock Quantity</label>
                <input type="number" name="stock" class="form-control" min="1" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Price (₱)</label>
                <input type="number" name="price" step="0.01" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Expiry Date</label>
                <input type="date" name="expiry_date" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Description (optional)</label>
                <textarea name="description" class="form-control" rows="2" placeholder="e.g., for fever, headache, etc."></textarea>
            </div>
            <button type="submit" class="btn btn-success w-100 mb-2">Add Medicine</button>
            <a href="admin-dashboard.php" class="btn btn-secondary w-100">Back</a>
        </form>
    </div>
</div>

</body>
</html>
