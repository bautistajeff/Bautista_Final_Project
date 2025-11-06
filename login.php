<?php
session_start();
require_once(__DIR__ . '/dbconnection.php');

if (isset($_POST['btnSubmit'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        if ($user['role'] === 'admin') {
            header("Location: admin-dashboard.php");
        } else {
            header("Location: user-dashboard.php");
        }
        exit;
    } else {
        $_SESSION['msg'] = "Invalid email or password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <title>Login | Inventory System</title>
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container d-flex justify-content-center align-items-center" style="height:100vh;">
   <form class="p-4 border rounded bg-white shadow w-50" action="" method="POST">
      <h3 class="text-center mb-4">Login</h3>

      <?php
      if (!empty($_SESSION['msg'])) {
         echo '<div class="alert alert-danger text-center">'.$_SESSION['msg'].'</div>';
         unset($_SESSION['msg']);
      }
      ?>

      <div class="mb-3">
         <label>Email</label>
         <input type="email" class="form-control" name="email" required>
      </div>
      <div class="mb-3">
         <label>Password</label>
         <input type="password" class="form-control" name="password" required>
      </div>
      <button type="submit" name="btnSubmit" class="btn btn-primary w-100">Login</button>
      <p class="text-center mt-3">Don't have an account? <a href="signup.php">Sign up</a></p>
   </form>
</div>
</body>
</html>
