<?php
session_start();
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");


if (!isset($_SESSION['user']) || !isset($_SESSION['role'])) {
  header("Location: login.php");
  exit;
}

// ❌ Deny access for students only
if ($_SESSION['role'] === 'student') {
  echo "<script>alert('Access denied. Staff/Admin only.'); window.location.href='index.php';</script>";
  exit;
}

include 'config.php';

$book_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$book = null;
if ($book_id > 0) {
  $book = $conn->query("SELECT * FROM books WHERE book_id = $book_id")->fetch_assoc();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $book_id = $_POST['book_id'];
  $name = $_POST['name'];
  $contact = $_POST['contact'];
  $borrow_date = $_POST['borrow_date'];
  $due_date = $_POST['due_date'];

  $stmt = $conn->prepare("INSERT INTO borrowers (name, contact) VALUES (?, ?)");
  $stmt->bind_param("ss", $name, $contact);
  $stmt->execute();
  $borrower_id = $stmt->insert_id;

  $stmt2 = $conn->prepare("INSERT INTO borrow_records (book_id, borrower_id, borrow_date, due_date) VALUES (?, ?, ?, ?)");
  $stmt2->bind_param("iiss", $book_id, $borrower_id, $borrow_date, $due_date);
  $stmt2->execute();

  $conn->query("UPDATE books SET status = 'Borrowed' WHERE book_id = $book_id");

  // ✅ Set success session
  $_SESSION['borrow_success'] = true;

  header("Location: index.php");
  exit;
}

?>

<!DOCTYPE html>
<html>
<head>
  <title>Borrow Book</title>
 <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Winky+Rough:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="style.css">
<style>
  body {
background: url('images/bb1.jpg') no-repeat center center fixed;
  background-size: cover;
      color: #4a3c2a;
    font-family: Arial, sans-serif;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    margin: 0;
  }
body::before {
  content: '';
  position: fixed;
  top: 0;
  left: 0;
  height: 100%;
  width: 100%;
  background: rgba(44, 34, 27, 0.4); /* darker brown overlay */
  z-index: -1;
}

  ..borrow-form-wrapper {
  background-color: #fffdf9;
  padding: 30px 50px;
  border-radius: 10px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  width: 500%;
  max-width: 600px;
  margin: 50px auto; /* ✅ Center the box */
  text-align: left;  /* ✅ Align text inside nicely */
}

  .borrow-form-wrapper h2 {
     margin-top: -50px;
  font-family: "Winky Rough", sans-serif; /* 👈 change font here */
  font-size: 40px;
  text-align: center;
  text-transform: uppercase;
  color: #302014ff;
  font-weight: 500;
    letter-spacing: 8px; /* adjust this value to control spacing */
    }

  form {
    display: flex;
    flex-direction: column;
    gap: 5px;
  }

  label {
    font-weight: bold;
    color: #4a3c2a;
    text-align: left;
  }

  input[type="text"],
  input[type="date"] {
    padding: 10px;
    border-radius: 6px;
    border: 1px solid #d6c1aa;
    background-color: #f3e9db;
    color: #4a3c2a;
    font-size: 14px;
    width: 100%;
    box-sizing: border-box;
  }

  .button-group {
    display: flex;
    justify-content: center;
    gap: 15px;
    margin-top: 10px;
  }

  button {
    padding: 10px 18px;
    border: none;
    border-radius: 6px;
    background-color: #b58150ff;
    color: white;
    font-weight: bold;
    cursor: pointer;
    font-size: 14px;
  }

  button:hover {
    background-color: #b48c6e;
  }

  .cancel-btn {
    background-color: #e0d6c3;
    color: #4a3c2a;
  }

  .cancel-btn:hover {
  background-color: #c9b8a6;
  }
</style>

</head>
<body>

  <div class="borrow-form-wrapper">
    <h2>Borrow a Book</h2>
    <form method="post">
      <input type="hidden" name="book_id" value="<?= $book['book_id'] ?>">

      <label>Book Title:</label>
      <input type="text" value="<?= htmlspecialchars($book['title']) ?>" readonly>

      <label>Borrower's Name:</label>
      <input type="text" name="name" required>

      <label>Borrower's Contact:</label>
      <input type="text" name="contact" required>

      <label>Borrow Date:</label>
      <input type="date" name="borrow_date" required min="2000-01-01" max="2100-12-31">
      
      <label>Due Date:</label>
      <input type="date" name="due_date" required min="2000-01-01" max="2100-12-31">

      <button type="submit">Confirm Borrow</button>
      <button type="button" class="cancel-btn" onclick="location.href='index.php'">Cancel</button>
    </form>
  </div>

</body>
</html>
