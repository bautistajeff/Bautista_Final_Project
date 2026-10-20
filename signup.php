<?php
require_once __DIR__ . '/dbconnection.php';

if (isLoggedIn()) {
    redirectToDashboard();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pharmacy Inventory</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="text-center mb-5">
            <h1 class="display-5 fw-bold">Pharmacy Inventory System</h1>
            <p class="lead text-muted">Manage stock, pricing, user access, and medicine requests in one place.</p>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm border-0">
                    <div class="card-body text-center p-4">
                        <h4 class="mb-3">Welcome</h4>
                        <p class="text-muted">Please login to access the inventory dashboard or create a new account to continue.</p>
                        <div class="d-grid gap-2">
                            <a href="login.php" class="btn btn-primary btn-lg">Login</a>
                            <a href="signup.php" class="btn btn-outline-primary btn-lg">Sign Up</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
