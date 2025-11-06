<?php
include('../dbconnection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!isset($_POST['id'], $_POST['medicine_name'], $_POST['category'], $_POST['stock'], $_POST['price'], $_POST['expiry_date'], $_POST['description'])) {
        header("Location: ../admin-dashboard.php?error=missing_fields");
        exit();
    }

    $id = (int)$_POST['id'];
    $medicine_name = trim($_POST['medicine_name']);
    $category = trim($_POST['category']);
    $stock = (int)$_POST['stock'];
    $price = (float)$_POST['price'];
    $expiry_date = $_POST['expiry_date'];
    $description = trim($_POST['description']);

    try {
       
        $stmt = $pdo->prepare("
            UPDATE medicines SET 
                medicine_name = :medicine_name,
                category = :category,
                stock = :stock,
                price = :price,
                expiry_date = :expiry_date,
                description = :description
            WHERE id = :id
        ");

        $stmt->execute([
            ':medicine_name' => $medicine_name,
            ':category' => $category,
            ':stock' => $stock,
            ':price' => $price,
            ':expiry_date' => $expiry_date,
            ':description' => $description,
            ':id' => $id
        ]);

        header("Location: ../admin-dashboard.php?updated=1");
        exit();

    } catch (PDOException $e) {
        die("Error updating medicine: " . htmlspecialchars($e->getMessage()));
    }
} else {
    
    header("Location: ../admin-dashboard.php");
    exit();
}
