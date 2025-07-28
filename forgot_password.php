<?php
include 'config.php';
$message = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = $_POST['username'];
  $newPassword = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

  $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
  $stmt->bind_param("s", $username);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows === 1) {
    $update = $conn->prepare("UPDATE users SET password = ? WHERE username = ?");
    $update->bind_param("ss", $newPassword, $username);
    if ($update->execute()) {
      $message = "Password updated successfully!";
      $success = true;
    } else {
      $message = "Failed to update password.";
    }
  } else {
    $message = "Username not found.";
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Forgot Password</title>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    body {
      margin: 0;
      padding: 0;
      height: 100vh;
      background-color: #f0e1d4ff;
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
    }

    .card-container {
      position: relative;
      width: 350px;
      height: 440px;
      margin: auto;
    }

    .circle1,
    .circle2 {
      height: 150px;
      width: 150px;
      border-radius: 50%;
      position: absolute;
      z-index: 0;
    }

    .circle1 {
      background-color: #d9c1a6;
      top: -65px;
      left: -110px;
    }

    .circle2 {
      background-color: rgb(172, 137, 110);
      bottom: -65px;
      right: -110px;
    }

    .container {
      height: 100%;
      width: 100%;
      position: relative;
      z-index: 2;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .log-card {
      position: absolute;
      width: 400px;
      border-radius: 8px;
      display: flex;
      flex-direction: column;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.4);
      backdrop-filter: blur(6px);
      padding: 20px;
      background-color: rgba(255, 255, 255, 0.4);
      z-index: 3;
    }

    .heading {
      font-size: 30px;
      font-weight: 700;
      color: #4a3c2a;
    }

    .para {
      font-size: 14px;
      font-weight: 500;
      color: #4a3c2a;
    }

    .text {
      margin-top: 5px;
      margin-bottom: 0;
      font-size: 14px;
      font-weight: 600;
      color: #7a6a55;
    }

    .input-group {
      margin-top: 10px;
      margin-bottom: 4px;
    }

    .input {
      box-sizing: border-box;
      margin-bottom: 5px;
      width: 100%;
      border: none;
      padding: 8px 16px;
      background-color: #f3e9db;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.4);
      border-radius: 8px;
      font-weight: 600;
      color: #4a3c2a;
    }

    .input:hover {
      color: #4a3c2a;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.4);
    }

    .btn {
      margin-top: 10px;
      margin-bottom: 10px;
      padding: 8px 16px;
      border: none;
      background-color: #c9a27e;
      color: white;
      font-size: 16px;
      font-weight: 700;
      border-radius: 8px;
      cursor: pointer;
    }

    .btn:hover {
      background-color: #b78c6c;
    }

    .no-account {
      font-size: 14px;
      font-weight: 400;
      color: #4a3c2a;
      text-align: center;
    }

    .link {
      font-weight: 800;
      color: #c49c7e;
      text-decoration: none;
    }

    .link:hover {
      color: #8a6a4c;
      text-decoration: underline;
    }
  </style>
</head>
<body>

<div class="card-container">
  <div class="circle1"></div>
  <div class="circle2"></div>
  <div class="container">
    <form method="POST" class="log-card">
      <p class="heading">Reset Password</p>
      <p class="para">Enter your username and new password below</p>

      <div class="input-group">
        <p class="text">Username</p>
        <input class="input" type="text" name="username" placeholder="Ex: yourqueen" required>

        <p class="text">New Password</p>
        <input class="input" type="password" name="new_password" placeholder="Enter new password" required>
      </div>

      <button type="submit" class="btn">Reset Password</button>

      <p class="no-account"><a href="login.php" class="link">Back to Login</a></p>
    </form>
  </div>
</div>

<?php if (!empty($message)): ?>
<script>
  Swal.fire({
    toast: true,
    position: 'top',
    showConfirmButton: false,
    timer: 2000,
    timerProgressBar: true,
    icon: <?= $success ? "'success'" : "'error'" ?>,
    title: <?= json_encode($message) ?>,
    didClose: () => {
      <?php if ($success): ?>
        window.location.href = 'login.php';
      <?php endif; ?>
    }
  });
</script>
<?php endif; ?>

</body>
</html>
