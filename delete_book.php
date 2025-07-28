<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user']) || !isset($_SESSION['role'])) {
  header("Location: login.php");
  exit;
}

if ($_SESSION['role'] === 'student') {
  echo "<script>alert('Access denied. Staff/Admin only.'); window.location.href='index.php';</script>";
  exit;
}

$id = $_GET['id'];
$conn->query("DELETE FROM books WHERE book_id = $id");

// ✅ Store success flag
$_SESSION['delete_success'] = true;

header("Location: index.php");
exit;
?>
