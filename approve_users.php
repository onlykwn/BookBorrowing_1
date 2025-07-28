<?php
session_start();
include 'config.php';

// ✅ Handle AJAX approve request
if (isset($_GET['approve']) && isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
    header('Content-Type: application/json');

    if (!isset($_SESSION['user']) || $_SESSION['user'] !== 'Admin') {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }

    $userId = intval($_GET['approve']);
    $update = $conn->query("UPDATE users SET status='approved' WHERE id = $userId");

    if ($update) {
        echo json_encode(['success' => true, 'message' => 'User approved']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to approve user']);
    }
    exit;
}

// ✅ Handle AJAX decline request
if (isset($_GET['decline']) && isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
    header('Content-Type: application/json');

    if (!isset($_SESSION['user']) || $_SESSION['user'] !== 'Admin') {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }

    $userId = intval($_GET['decline']);
    $deleted = $conn->query("DELETE FROM users WHERE id = $userId");

    if ($deleted) {
        echo json_encode(['success' => true, 'message' => 'User declined']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to decline user']);
    }
    exit;
}

// ✅ Fallback if not AJAX
if (!isset($_SESSION['user']) || $_SESSION['user'] !== 'Admin') {
    header("Location: login.php");
    exit;
}

// ✅ Decline user (normal redirect fallback)
if (isset($_GET['decline'])) {
    $userId = intval($_GET['decline']);
    $conn->query("DELETE FROM users WHERE id = $userId");
    header("Location: approve_users.php");
    exit;
}

// ✅ Fetch all pending users
$pendingUsers = $conn->query("SELECT * FROM users WHERE status='pending'");
?>

<!DOCTYPE html>
<html>
<head>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Winky+Rough:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">

  <title>Approve Accounts</title>
<style>
  body {
  margin: 0;
  padding: 20px;
  font-family: 'Space Grotesk', sans-serif;
  background: url('images/bb1.jpg') no-repeat center center fixed;
  background-size: cover;
  color: #4a3c2a;
  position: relative;
}

body::before {
  content: '';
  position: fixed;
  top: 0;
  left: 0;
  height: 100%;
  width: 100%;
  background: rgba(42, 34, 26, 0.3); /* dark brown overlay */
  z-index: -1;
}

h2 {
  text-align: center;
  font-size: 40px;
    text-transform: uppercase;
  font-family: "Winky Rough", serif;
  margin: 20px 0;
  color: #302014ff;
  font-weight: 500;
  letter-spacing: 10px; /* spacing between letters */
}

table {
  width: 70%;
  margin: 0 auto;
  border-collapse: collapse;
  background: #f6eee5; /* soft beige background */
  box-shadow: 0 2px 6px rgba(0,0,0,0.1);
}

th, td {
  border: 1px solid #e0cbb5; /* subtle brown border */
  padding: 12px;
  text-align: center;
  font-size: 16px;
  color: #4a3c2a;
  word-wrap: break-word;
}

th {
  background-color: #e8d5bc; /* light brown for headers */
  font-weight: bold;
  color: #3e2d1f;
}

a.button {
  padding: 6px 14px;
  background-color: #3e2b1f;
  color: white;
  border-radius: 6px;
  text-decoration: none;
  font-weight: bold;
  margin: 0 3px;
}

a.button:hover {
  background-color: #8d5f3f;
}

a.button.decline {
  background-color: #a54c4c;
}

a.button.decline:hover {
  background-color: #883737;
}

#notif {
  display: none;
  position: fixed;
  top: 20px;
  left: 50%;
  transform: translateX(-50%);
  background: #f3e6c8;
  color: #5a4529;
  padding: 10px 20px;
  border-radius: 6px;
  font-weight: bold;
  box-shadow: 0 2px 6px rgba(0,0,0,0.3);
  z-index: 9999;
  font-size: 15px;
}

.back-style {
  margin: 20px;
  padding: 10px 20px;
  background-color: #3e2b1f; /* dark pastel brown */
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-weight: bold;
}

.back-style:hover {
  background-color: #8d5f3f; /* darker on hover */
}

</style>

</head>
<body>
  <!-- Back Button -->
  <a href="index.php">
    <button class="back-style">Back</button>
  </a>
<h2>Pending User Accounts</h2>

<table>
  <tr>
    <th>Username</th>
    <th>Role</th>
    <th>Status</th>
    <th>Action</th>
  </tr>

  <?php while ($row = $pendingUsers->fetch_assoc()): ?>
    <tr id="row-<?= $row['id'] ?>">
      <td><?= htmlspecialchars($row['username']) ?></td>
      <td><?= htmlspecialchars($row['role']) ?></td>
      <td><?= htmlspecialchars($row['status']) ?></td>
      <td>
        <a href="#" class="button" onclick="approveUser(<?= $row['id'] ?>); return false;">Approve</a>
        <a class="button decline" href="approve_users.php?decline=<?= $row['id'] ?>" onclick="return confirm('Are you sure you want to decline this user?')">Decline</a>
      </td>
    </tr>
  <?php endwhile; ?>
</table>
<script>
function approveUser(userId) {
  const xhr = new XMLHttpRequest();
  xhr.open("GET", "approve_users.php?approve=" + userId, true);
  xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

  xhr.onload = function () {
    const res = JSON.parse(xhr.responseText);
    if (res.success) {
      // Remove the approved row
      const row = document.getElementById('row-' + userId);
      if (row) row.remove();

      // Show success notification
 Swal.fire({
  icon: 'success',
  title: 'Approved',
  text: res.message,
  timer: 1800,
  showConfirmButton: false
});

    } else {
      alert(res.message);
    }
  };

  xhr.send();
}
</script>

</body>
</html>
