<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}
require_once 'includes/db.php';

$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['reg_username'] ?? '');
    $email    = trim($_POST['reg_email'] ?? '');
    $password = $_POST['reg_password'] ?? '';

    if (strlen($username) < 3) {
        $error = 'Username must be at least 3 characters.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters.';
    } else {
        $email_safe = mysqli_real_escape_string($conn, $email);
        $check = mysqli_query($conn, "SELECT id FROM users WHERE email = '$email_safe' LIMIT 1");
        if (mysqli_num_rows($check) > 0) {
            $error = 'That email address is already registered.';
        } else {
            $username_safe = mysqli_real_escape_string($conn, $username);
            $hashed        = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO users (username, email, password, role)
                    VALUES ('$username_safe', '$email_safe', '$hashed', 'customer')";
            if (mysqli_query($conn, $sql)) {
                $success = 'Account created successfully! You can now log in.';
            } else {
                $error = 'Registration failed. Please try again.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register &mdash; QuickCart</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<div class="container">
  <div class="auth-card">
    <div class="card shadow">
      <div class="card-header bg-primary text-white text-center py-3">
        <h4 class="mb-0">Create an Account</h4>
      </div>
      <div class="card-body p-4">
        <?php if ($error): ?>
          <div class="alert alert-danger py-2"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
          <div class="alert alert-success py-2"><?php echo htmlspecialchars($success); ?>
            <a href="login.php">Login now</a>
          </div>
        <?php endif; ?>

        <form id="registerForm" method="POST" action="register.php" novalidate>
          <div class="mb-3">
            <label for="reg_username" class="form-label">Username</label>
            <input type="text" class="form-control" id="reg_username" name="reg_username"
                   value="<?php echo htmlspecialchars($_POST['reg_username'] ?? ''); ?>"
                   placeholder="Choose a username">
            <div id="usernameErr" class="field-error"></div>
          </div>

          <div class="mb-3">
            <label for="reg_email" class="form-label">Email Address</label>
            <input type="email" class="form-control" id="reg_email" name="reg_email"
                   value="<?php echo htmlspecialchars($_POST['reg_email'] ?? ''); ?>"
                   placeholder="you@example.com">
            <div id="reg_emailErr" class="field-error"></div>
          </div>

          <div class="mb-3">
            <label for="reg_password" class="form-label">Password</label>
            <input type="password" class="form-control" id="reg_password" name="reg_password"
                   placeholder="Minimum 8 characters">
            <div class="mt-2">
              <div class="bg-light rounded" style="height:6px; overflow:hidden;">
                <div id="strength-bar" style="width:0%; height:100%;"></div>
              </div>
              <small id="strength-label" class="text-muted"></small>
            </div>
            <div id="reg_passErr" class="field-error"></div>
          </div>

          <div class="d-grid mt-4">
            <button type="submit" class="btn btn-primary">Create Account</button>
          </div>
        </form>
      </div>
      <div class="card-footer text-center text-muted">
        Already have an account? <a href="login.php">Login here</a>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/main.js"></script>
</body>
</html>
