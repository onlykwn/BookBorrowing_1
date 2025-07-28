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
$id = $_GET['id'];
$book = $conn->query("SELECT * FROM books WHERE book_id = $id")->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn->query("UPDATE books SET
        title = '{$_POST['title']}',
        author = '{$_POST['author']}',
        genre = '{$_POST['genre']}',
        status = '{$_POST['status']}'
        WHERE book_id = $id
    ");
    $success = true;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Book</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Winky+Rough:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="style.css">
<style>
body {
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 100vh;
  margin: 0;
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

.form-wrapper h2 {
  margin-top: -70px;
  text-align: center;
  color: #302014ff;
  font-size: 40px;
  font-family: "Winky Rough", serif;
  text-transform: uppercase;
  font-weight: 500;
  letter-spacing: 10px;
}

.form-wrapper {
  width: 100%;
  max-width: 500px;
  padding: 30px 20px;
  box-sizing: border-box;
  background: transparent;
}

.container {
  max-width: 600px;
  margin: 40px auto;
  padding: 20px;
}

.cancel-btn {
  width: 100%;
  padding: 10px;
  font-size: 16px;
  border: 1px solid #e0cbb5;
  border-radius: 4px;
  box-sizing: border-box;
  background-color: #f3e6c8;
  color: #4a3c2a;
  cursor: pointer;
}

.cancel-btn:hover {
  background-color: #e0d0b5;
}

h2 {
  text-align: center;
  margin-bottom: 20px;
  color: #4a3c2a;
}

form {
  display: flex;
  flex-direction: column;
  gap: 5px;
}

label {
  font-weight: bold;
  margin-bottom: 5px;
  color: #4a3c2a;
  text-align: left;
}

input[type="text"],
select,
button {
  width: 100%;
  padding: 10px;
  font-size: 16px;
  border: 1px solid #e0cbb5;
  border-radius: 4px;
  box-sizing: border-box;
  background-color: #f6eee5;
  color: #4a3c2a;
}

button {
  background-color: #3e2b1f;
  color: white;
  border: none;
  cursor: pointer;
}

button:hover {
  background-color: #8d5f3f;
}

.toast {
  visibility: hidden;
  min-width: 250px;
  background-color: #4caf50;
  color: white;
  text-align: center;
  border-radius: 6px;
  padding: 12px 20px;
  position: fixed;
  z-index: 1000;
  left: 50%;
  top: 20px;
  transform: translateX(-50%);
  font-size: 16px;
  box-shadow: 0 2px 6px rgba(0,0,0,0.3);
}

.toast.show {
  visibility: visible;
  animation: slideDown 0.4s ease, fadeout 0.5s 2.5s ease forwards;
}

@keyframes slideDown {
  from {
    opacity: 0;
    transform: translate(-50%, -20px);
  }
  to {
    opacity: 1;
    transform: translate(-50%, 0);
  }
}

@keyframes fadeout {
  from {
    opacity: 1;
    transform: translate(-50%, 0);
  }
  to {
    opacity: 0;
    transform: translate(-50%, -20px);
  }
}

</style>

</head>
<body>
<?php if (isset($success) && $success): ?>
  <div id="toast" class="toast">✅ Book successfully updated!</div>
<?php endif; ?>
  <div class="container">
    <div class="form-wrapper">
      <h2>Edit Book</h2>
<form method="POST">
  <label for="title">Title</label>
  <input type="text" name="title" id="title" value="<?= htmlspecialchars($book['title']) ?>" <?= $book['status'] == 'Borrowed' ? 'readonly' : 'required' ?>>

  <label for="author">Author</label>
  <input type="text" name="author" id="author" value="<?= htmlspecialchars($book['author']) ?>" <?= $book['status'] == 'Borrowed' ? 'readonly' : 'required' ?>>

  <label for="genre">Genre</label>
  <select name="genre" id="genre" <?= $book['status'] == 'Borrowed' ? 'disabled' : 'required' ?>>
    <option value="">-- Select Genre --</option>
    <?php
      $genres = ["Horror", "Romance", "Sci-Fi", "Fantasy", "Mystery", "Non-Fiction", "Biography", "Adventure"];
      foreach ($genres as $g) {
        $selected = ($g == $book['genre']) ? 'selected' : '';
        echo "<option value='$g' $selected>$g</option>";
      }
    ?>
  </select>

  <label for="status">Status</label>
  <input type="text" value="<?= htmlspecialchars($book['status']) ?>" disabled style="background-color: #f3e9db; color: #4a3c2a; border: 1px solid #d6c1aa;">
  <input type="hidden" name="status" value="Available">

  <?php if ($book['status'] === 'Borrowed'): ?>
    <small style="color: #e67e22;">
      ⚠ This book is currently borrowed. Editing is disabled until it is returned.
    </small>
  <?php endif; ?>

  <?php if ($book['status'] !== 'Borrowed'): ?>
    <button type="submit">Update</button>
  <?php endif; ?>

  <!-- Cancel button always visible -->
  <button type="button" class="cancel-btn" onclick="location.href='index.php'">Cancel</button>
</form>

    </div>
  </div>
<script>
  const toast = document.getElementById("toast");
  if (toast) {
    toast.classList.add("show");
    setTimeout(() => toast.classList.remove("show"), 3000);
  }
</script>
</body>
</html>
