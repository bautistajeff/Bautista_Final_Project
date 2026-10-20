<?php
require_once __DIR__ . '/dbconnection.php';
requireRole('admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $medicineName = trim($_POST['medicine_name'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $stock = trim($_POST['stock'] ?? '');
    $price = trim($_POST['price'] ?? '');
    $expiryDate = trim($_POST['expiry_date'] ?? '');
    $description = trim($_POST['description'] ?? '');

    try {
        if ($medicineName === '' || $category === '' || $stock === '' || $price === '' || $expiryDate === '') {
            throw new InvalidArgumentException('Please fill in all required fields.');
        }

        $stockValue = ensureValidStock($stock);
        $priceValue = ensureValidPositiveNumber($price);

        $stmt = $pdo->prepare('INSERT INTO medicines (medicine_name, category, stock, price, expiry_date, description, created_at) VALUES (:medicine_name, :category, :stock, :price, :expiry_date, :description, :created_at)');
        $stmt->execute([
            ':medicine_name' => $medicineName,
            ':category' => $category,
            ':stock' => $stockValue,
            ':price' => $priceValue,
            ':expiry_date' => $expiryDate,
            ':description' => $description,
            ':created_at' => date('Y-m-d H:i:s'),
        ]);

        header('Location: admin-dashboard.php?success=1');
        exit;
    } catch (Throwable $e) {
        $errorMessage = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Medicine</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="card shadow-sm border-0" style="width: 100%; max-width: 520px;">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="mb-0">Add New Medicine</h3>
                <a href="admin-dashboard.php" class="btn btn-secondary btn-sm">Back</a>
            </div>

            <?php if (!empty($errorMessage)): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($errorMessage); ?></div>
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
                    <input type="number" name="stock" class="form-control" min="0" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Price (₱)</label>
                    <input type="number" name="price" step="0.01" min="0" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Expiry Date</label>
                    <input type="date" name="expiry_date" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Description (optional)</label>
                    <textarea name="description" class="form-control" rows="3" placeholder="Example: For fever, headache, and mild pain"></textarea>
                </div>
                <button type="submit" class="btn btn-success w-100">Add Medicine</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>
