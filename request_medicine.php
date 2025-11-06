<?php
session_start();
require_once "dbconnection.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === "POST") {

    if (!isset($_POST['medicine_id'], $_POST['quantity'])) {
        $_SESSION['msg'] = "Invalid form data.";
        header("Location: user-dashboard.php");
        exit();
    }

    $user_id = $_SESSION['user_id'];
    $medicine_id = intval($_POST['medicine_id']);
    $quantity = intval($_POST['quantity']);

    if ($quantity <= 0) {
        $_SESSION['msg'] = "Invalid quantity.";
        header("Location: user-dashboard.php");
        exit();
    }

    try {
        $stmt = $pdo->prepare("SELECT stock FROM medicines WHERE id = ?");
        $stmt->execute([$medicine_id]);
        $medicine = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$medicine) {
            $_SESSION['msg'] = "Medicine not found.";
            header("Location: user-dashboard.php");
            exit();
        }

        if ($medicine['stock'] < $quantity) {
            $_SESSION['msg'] = "Not enough stock.";
            header("Location: user-dashboard.php");
            exit();
        }

        $stmt = $pdo->prepare("
            INSERT INTO requests (user_id, medicine_id, quantity, status, created_at) 
            VALUES (?, ?, ?, 'Pending', NOW())
        ");
        $stmt->execute([$user_id, $medicine_id, $quantity]);

        $update = $pdo->prepare("UPDATE medicines SET stock = stock - ? WHERE id = ?");
        $update->execute([$quantity, $medicine_id]);

        $_SESSION['msg_success'] = "✅ Request sent successfully!";
        header("Location: user-dashboard.php");
        exit();

    } catch (PDOException $e) {
        $_SESSION['msg'] = "Database error: " . $e->getMessage();
        header("Location: user-dashboard.php");
        exit();
    }
}

header("Location: user-dashboard.php");
exit();
?>
