<?php
require_once __DIR__ . '/../dbconnection.php';

if (!isLoggedIn() || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

if (!isset($_GET['id'])) {
    header('Location: ../admin-dashboard.php?error=invalid_id');
    exit;
}

$id = (int) $_GET['id'];

try {
    $stmt = $pdo->prepare('DELETE FROM medicines WHERE id = :id');
    $stmt->execute([':id' => $id]);
    header('Location: ../admin-dashboard.php?deleted=1');
    exit;
} catch (PDOException $e) {
    header('Location: ../admin-dashboard.php?error=' . urlencode('Unable to delete medicine.'));
    exit;
}
