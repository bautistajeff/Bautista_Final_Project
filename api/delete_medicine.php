<?php
require_once __DIR__ . '/../dbconnection.php';

if (!isLoggedIn() || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../admin-dashboard.php');
    exit;
}

if (!isset($_POST['id'], $_POST['medicine_name'], $_POST['category'], $_POST['stock'], $_POST['price'], $_POST['expiry_date'])) {
    header('Location: ../admin-dashboard.php?error=missing_fields');
    exit;
}

try {
    $id = (int) $_POST['id'];
    $medicineName = trim($_POST['medicine_name']);
    $category = trim($_POST['category']);
    $stock = ensureValidStock($_POST['stock']);
    $price = ensureValidPositiveNumber($_POST['price']);
    $expiryDate = trim($_POST['expiry_date']);
    $description = trim($_POST['description'] ?? '');

    if ($medicineName === '' || $category === '' || $expiryDate === '') {
        throw new InvalidArgumentException('Medicine name, category, and expiry date are required.');
    }

    $stmt = $pdo->prepare('UPDATE medicines SET medicine_name = :medicine_name, category = :category, stock = :stock, price = :price, expiry_date = :expiry_date, description = :description WHERE id = :id');
    $stmt->execute([
        ':medicine_name' => $medicineName,
        ':category' => $category,
        ':stock' => $stock,
        ':price' => $price,
        ':expiry_date' => $expiryDate,
        ':description' => $description,
        ':id' => $id,
    ]);

    header('Location: ../admin-dashboard.php?updated=1');
    exit;
} catch (Throwable $e) {
    $_SESSION['edit_error'] = $e->getMessage();
    header('Location: ../admin-dashboard.php?error=' . urlencode($e->getMessage()));
    exit;
}
