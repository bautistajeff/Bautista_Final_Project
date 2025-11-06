<?php
include('../dbconnection.php');

if (!isset($_GET['id'])) {
    header("Location: ../admin-dashboard.php");
    exit();
}

$id = (int)$_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM medicines WHERE id = ?");
$stmt->execute([$id]);
$medicine = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$medicine) {
    header("Location: ../admin-dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Medicine</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="card shadow p-4">
        <h3 class="mb-4 text-primary">✏️ Edit Medicine</h3>

        <form id="editMedicineForm" method="POST" action="../api/edit_medicine.php">

            <input type="hidden" name="id" value="<?= $medicine['id'] ?>">

            <div class="mb-3">
                <label class="form-label">Medicine Name</label>
                <input type="text" name="medicine_name" class="form-control" value="<?= htmlspecialchars($medicine['medicine_name']) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Category</label>
                <input type="text" name="category" class="form-control" value="<?= htmlspecialchars($medicine['category']) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Stock</label>
                <input type="number" name="stock" class="form-control" value="<?= $medicine['stock'] ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Price (₱)</label>
                <input type="text" name="price" class="form-control" value="<?= number_format($medicine['price'],2) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Expiry Date</label>
                <input type="date" name="expiry_date" class="form-control" value="<?= $medicine['expiry_date'] ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($medicine['description']) ?></textarea>
            </div>

            <button type="submit" class="btn btn-success px-4">Update</button>
            <a href="../admin-dashboard.php" class="btn btn-secondary px-4">Cancel</a>
        </form>
    </div>
</div>

<script src="../assets/js/edit_medicine.js"></script> 
</body>
</html>
