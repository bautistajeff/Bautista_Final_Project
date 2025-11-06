<?php
include('../dbconnection.php');

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    try {
        $stmt = $pdo->prepare("DELETE FROM medicines WHERE id = ?");
        $stmt->execute([$id]);
        header("Location: ../admin-dashboard.php?deleted=1");
        exit();
    } catch (PDOException $e) {
        die("Error deleting medicine: " . $e->getMessage());
    }
} else {
    header("Location: ../admin-dashboard.php?error=invalid_id");
    exit();
}
?>
