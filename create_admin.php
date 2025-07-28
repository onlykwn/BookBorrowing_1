<?php
include 'config.php';

$username = 'Admin';
$password = password_hash('ADMIN12345', PASSWORD_DEFAULT);
$role = '   Admin';
$status = 'approved';

// Check if admin already exists
$check = $conn->prepare("SELECT * FROM users WHERE username = ?");
$check->bind_param("s", $username);
$check->execute();
$result = $check->get_result();

if ($result->num_rows > 0) {
    echo "⚠️ Admin account already exists!";
} else {
    $stmt = $conn->prepare("INSERT INTO users (username, password, role, status) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, $password, $role, $status);

    if ($stmt->execute()) {
        echo "✅ Admin account created successfully!";
    } else {
        echo "❌ Error: " . $stmt->error;
    }
}
?>
