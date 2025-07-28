<?php
include 'config.php';

$message = '';
$type = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $username = $_POST['username'];
  $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
  $role = $_POST['role'];

  if (!in_array($role, ['staff', 'student'])) {
    die("Invalid role selected.");
  }

  $status = ($role === 'student') ? 'approved' : 'pending';

  $stmt = $conn->prepare("INSERT INTO users (username, password, role, status) VALUES (?, ?, ?, ?)");
  $stmt->bind_param("ssss", $username, $password, $role, $status);

  if ($stmt->execute()) {
    $type = 'success';
    $message = ($role === 'staff') ? "Account created! Wait for admin approval." : "Account created! You can now log in.";
    $autoRedirect = ($role === 'student'); // redirect only if student
  } else {
    $type = 'error';
    $message = "Username already exists or an error occurred.";
    $autoRedirect = false;
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Sign Up</title>
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

    .circle1, .circle2 {
      height: 150px;
      width: 150px;
      border-radius: 50%;
      position: absolute;
      z-index: 0;
    }

    .circle1 {
      background-color: #d9c1a6;
      top: -90px;
      left: -110px;
    }

    .circle2 {
      background-color:rgb(172, 137, 110);
      bottom: -90px;
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
      font-size: 32px;
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
  </style>
</head>
<body>

<div class="card-container">
  <div class="circle1"></div>
  <div class="circle2"></div>
  <div class="container">
    <form method="post" class="log-card">
      <p class="heading">Create Account</p>
      <p class="para">Register to start managing your library.</p>

      <div class="input-group">
        <p class="text">Username</p>
        <input class="input" type="text" name="username" placeholder="Enter username" required>

        <p class="text">Password</p>
        <input class="input" type="password" name="password" placeholder="Enter password" required>
      </div>

      <p class="text">Role</p>
      <div class="input-group" style="display: flex; gap: 20px; align-items: center;">
        <label style="color: #4a3c2a;">
          <input type="radio" name="role" value="staff" required> Staff
        </label>
        <label style="color: #4a3c2a;">
          <input type="radio" name="role" value="student" required> Student
        </label>
      </div>

      <button type="submit" class="btn">Sign Up</button>

      <p class="no-account">Already have an account? <a href="login.php" class="link">Log In</a></p>
    </form>
  </div>
</div>

<?php if (!empty($message)): ?>
<script>
Swal.fire({
  toast: true,
  position: 'top',
  icon: '<?= $type ?>',
  title: <?= json_encode($message) ?>,
  showConfirmButton: false,
  background: '#fdf6f0',
  color: '#4a3c2a',
  timer: 3000,
  timerProgressBar: true,
  didClose: () => {
    <?php if (!empty($autoRedirect)) echo "window.location.href = 'login.php';"; ?>
  }
});
</script>
<?php endif; ?>

</body>
</html>
