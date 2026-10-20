<?php
require_once __DIR__ . '/dbconnection.php';

if (isLoggedIn()) {
    redirectToDashboard();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($email !== '' && $password !== '') {
        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = (int) $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            redirectToDashboard();
        }

        $_SESSION['msg'] = 'Invalid email or password.';
    } else {
        $_SESSION['msg'] = 'Please complete both fields.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Pharmacy Inventory</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="card shadow-sm border-0" style="width: 100%; max-width: 420px;">
        <div class="card-body p-4">
            <h3 class="text-center mb-4">Login</h3>

            <?php if (!empty($_SESSION['msg'])): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($_SESSION['msg']); unset($_SESSION['msg']); ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" name="email" value="admin@pharmacy.local" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" class="form-control" name="password" value="admin123" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Login</button>
            </form>

            <div class="text-center mt-3">
                <span>Need an account?</span>
                <a href="signup.php">Create one</a>
            </div>

            <div class="alert alert-info mt-3 mb-0 small">
                Demo admin account: admin@pharmacy.local / admin123
            </div>
        </div>
    </div>
</div>
</body>
</html>
