<?php
require_once __DIR__ . '/dbconnection.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

if ($_SESSION['role'] === 'admin') {
    header('Location: admin-dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $medicineName = trim($_POST['medicine_name'] ?? '');
    $quantity = (int) ($_POST['quantity'] ?? 1);
    $notes = trim($_POST['notes'] ?? '');

    if ($medicineName === '') {
        $_SESSION['request_error'] = 'Medicine name is required.';
    } else {
        $stmt = $pdo->prepare('INSERT INTO medicine_requests (user_id, medicine_name, quantity, notes, status, created_at) VALUES (:user_id, :medicine_name, :quantity, :notes, :status, :created_at)');
        $stmt->execute([
            ':user_id' => (int) $_SESSION['user_id'],
            ':medicine_name' => $medicineName,
            ':quantity' => max(1, $quantity),
            ':notes' => $notes,
            ':status' => 'pending',
            ':created_at' => date('Y-m-d H:i:s'),
        ]);

        $_SESSION['request_success'] = 'Your medicine request has been submitted.';
        header('Location: user-dashboard.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Request Medicine</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h3 class="mb-0">Request Medicine</h3>
                        <a href="user-dashboard.php" class="btn btn-secondary btn-sm">Back</a>
                    </div>

                    <?php if (!empty($_SESSION['request_error'])): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($_SESSION['request_error']); unset($_SESSION['request_error']); ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Medicine Name</label>
                            <input type="text" class="form-control" name="medicine_name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Quantity</label>
                            <input type="number" class="form-control" name="quantity" min="1" value="1" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Notes</label>
                            <textarea class="form-control" name="notes" rows="3" placeholder="Optional details"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Submit Request</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
