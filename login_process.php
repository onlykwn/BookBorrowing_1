<?php
session_start();
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $username = $_POST['username'];
  $password = $_POST['password'];

  $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
  $stmt->bind_param("s", $username);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    // ✅ First check if the account is pending
    if ($user['status'] === 'pending') {
      echo "<script>alert('Your account is pending approval by admin.'); window.location.href = 'login.php';</script>";
      exit;
    }

    // ✅ Then check password
    if (password_verify($password, $user['password'])) {
      $_SESSION['user'] = $user['username'];
      $_SESSION['role'] = $user['role']; // Optional if needed
      header("Location: index.php");
      exit;
    }
  }

  // ❌ Login failed
  echo "<script>alert('Invalid username or password'); window.location.href = 'login.php';</script>";
}
?>
