<?php
session_start();
require_once(__DIR__ . '/dbconnection.php');

if (isset($_POST['btnSignup'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $_SESSION['msg'] = "⚠️ All fields are required!";
        header("Location: signup.php");
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['msg'] = "❌ Invalid email format!";
        header("Location: signup.php");
        exit();
    }

    if ($password !== $confirm_password) {
        $_SESSION['msg'] = "❌ Passwords do not match!";
        header("Location: signup.php");
        exit();
    }

    try {
        $check = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $check->execute([$email]);
        if ($check->rowCount() > 0) {
            $_SESSION['msg'] = "⚠️ Email already registered!";
            header("Location: signup.php");
            exit();
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'user')");
        $stmt->execute([$username, $email, $hashedPassword]);

        $_SESSION['msg_success'] = "✅ Account created successfully! Please login.";
        header("Location: login.php");
        exit();

    } catch (PDOException $e) {
        $_SESSION['msg'] = "⚠️ Database error: " . $e->getMessage();
        header("Location: signup.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <title>Sign Up | Inventory System</title>
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
   <div class="card mx-auto shadow" style="max-width: 450px; border-radius: 15px;">
      <div class="card-body">
         <h4 class="text-center mb-4">Create Account</h4>

         <?php
         if (!empty($_SESSION['msg'])) {
            echo '<div class="alert alert-warning">'.$_SESSION['msg'].'</div>';
            unset($_SESSION['msg']);
         }
         if (!empty($_SESSION['msg_success'])) {
            echo '<div class="alert alert-success">'.$_SESSION['msg_success'].'</div>';
            unset($_SESSION['msg_success']);
         }
         ?>

         <form action="signup.php" method="POST">
            <div class="mb-3">
               <label>Full Name</label>
               <input type="text" class="form-control" name="username" required>
            </div>
            <div class="mb-3">
               <label>Email</label>
               <input type="email" class="form-control" name="email" required>
            </div>
            <div class="mb-3">
               <label>Password</label>
               <input type="password" class="form-control" name="password" required>
            </div>
            <div class="mb-3">
               <label>Confirm Password</label>
               <input type="password" class="form-control" name="confirm_password" required>
            </div>
            <button type="submit" name="btnSignup" class="btn btn-success w-100">Sign Up</button>
         </form>

         <div class="text-center mt-3">
            <small>Already have an account? <a href="login.php">Login</a></small>
         </div>
      </div>
   </div>
</div>
</body>
</html>
