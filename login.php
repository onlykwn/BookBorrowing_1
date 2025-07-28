<?php
session_start();
include 'config.php';

$alert = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = $_POST['username'];
  $password = $_POST['password'];

  $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
  $stmt->bind_param("s", $username);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    if ($user['status'] === 'pending') {
      $alert = "Your account is pending approval by admin.";
    } elseif ($username === 'Admin' && $password === 'ADMIN12345') {
      $_SESSION['user'] = 'Admin';
      $_SESSION['role'] = 'main_admin';
      $_SESSION['user_id'] = $user['id'];
      header("Location: index.php");
      exit;
    } elseif (password_verify($password, $user['password'])) {
      $_SESSION['user'] = $user['username'];
      $_SESSION['role'] = $user['role'];
      $_SESSION['user_id'] = $user['id'];
      header("Location: index.php");
      exit;
    } else {
      $alert = "Invalid password.";
    }
  } else {
    $alert = "User not found.";
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
  <meta charset="UTF-8">
  <title>Login</title>
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
      top: -100px;
      left: -100px;
    }

    .circle2 {
      background-color:rgb(172, 137, 110);
      bottom: -100px;
      right: -100px;
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

    .password-group {
      display: flex;
      justify-content: space-between;
      margin-top: 5px;
    }

    .checkbox-group {
      color: #4a3c2a;
      font-size: 14px;
      font-weight: 500;
    }

    .forget-password {
      font-size: 14px;
      font-weight: 500;
      color: #c49c7e;
      text-decoration: none;
    }

    .forget-password:hover {
      text-decoration: underline;
      color: #a07758;
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
      font-size: 16px;
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

    .swal2-font {
      font-family: 'Winky Rough', serif;
      letter-spacing: 1px;
    }

    .swal2-popup {
      font-family: 'Cinzel', serif;
      letter-spacing: 0.5px;

    }
    html, body {
  overflow: hidden !important;
  padding-right: 0 !important;
  width: 100% !important;
}

  </style>
</head>
<body>
  <!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- SweetAlert2 Draggable Extension -->
<script src="https://cdn.jsdelivr.net/gh/KarimElghamry/Draggable-SweetAlert@main/SwalDraggable.min.js"></script>

<div class="card-container">
  <div class="circle1"></div>
  <div class="circle2"></div>
  <div class="container">
    <form action="login.php" method="POST" class="log-card">
      <p class="heading">Welcome to Book Borrowing / Inventory</p>
      <p class="para">We are happy to have you here!</p>

      <div class="input-group">
        <p class="text">Username</p>
        <input class="input" type="text" name="username" placeholder="Ex: yourqueen" required>

        <p class="text">Password</p>
        <input class="input" type="password" name="password" placeholder="Enter your password" required>
      </div>

      <div class="password-group">
        <div class="checkbox-group">
          <input type="checkbox" id="remember">
          <label for="remember">Remember Me</label>
        </div>
        <a href="forgot_password.php" class="forget-password">Forget Password</a>
      </div>

      <button class="btn" type="submit">Sign In</button>

      <p class="no-account">Don't have an account? <a href="signup.php" class="link">Sign Up</a></p>
    </form>
  </div>
</div>

<?php if (!empty($alert)): ?>
<script>
  document.addEventListener("DOMContentLoaded", function () {
    Swal.fire({
      toast: true,
      position: 'top',
      icon: 'error',
      title: <?= json_encode($alert) ?>,
      showConfirmButton: false,
      timer: 4000,
      timerProgressBar: true,
      background: '#f2e8dd',
      color: '#5b4433',
      customClass: {
        popup: 'swal2-font swal2-top-popup'
      },
      didOpen: () => {
        if (typeof SwalDraggable === 'function') {
          SwalDraggable();
        }
      }
    });
  });
</script>
<style>
  .swal2-top-popup {
    animation: popIn 0.3s ease-out;
  }

  @keyframes popIn {
    from {
      opacity: 0;
      transform: translateY(-20px) scale(0.95);
    }
    to {
      opacity: 1;
      transform: translateY(0) scale(1);
    }
  }
</style>
<?php endif; ?>




</body>
</html>


