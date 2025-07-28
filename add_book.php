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


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $genre = $_POST['genre'];

    $stmt = $conn->prepare("INSERT INTO books (title, author, genre) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $title, $author, $genre);
    $stmt->execute();
    $stmt->close();

    $success = true;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add New Book</title>
  <!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <!-- Winky Rough font -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Winky+Rough:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="style.css">
<style>
  .form-wrapper h2 {
  margin-top: -50px;
  font-family: "Winky Rough", sans-serif; /* 👈 change font here */
  font-size: 40px;
  text-align: center;
  text-transform: uppercase;
  color: #302014ff;
  font-weight: 500;
    letter-spacing: 8px; /* adjust this value to control spacing */

}

body {
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 100vh;
  margin: 0;
  background: url('images/bb1.jpg') no-repeat center center fixed;
  background-size: cover;
  color: #3e2b1f; /* unified darker brown text */
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

.form-wrapper {
  width: 100%;
  max-width: 500px;
  padding: 30px 20px;
  box-sizing: border-box;
  background: transparent;
  border-radius: 8px;
  text-align: left;
  position: relative;
}

form {
  display: flex;
  flex-direction: column;
  gap: 8px;
  margin-top: 20px;
}

label {
  font-weight: bold;
  margin-bottom: 5px;
  color: #3e2b1f; /* dark brown label */
  text-align: left;
}

input[type="text"],
select {
  width: 100%;
  padding: 10px;
  font-size: 16px;
  border: 1px solid #a68c73; /* muted beige border */
  border-radius: 4px;
  background-color: #b89e7fff; /* light coffee bg */
  color: #3e2b1f;
  box-sizing: border-box;
}

button[type="submit"] {
  background-color: #6f4f3a; /* dark brown button */
  color: white;
  border: none;
  border-radius: 4px;
  font-size: 16px;
  padding: 10px;
  cursor: pointer;
}

button[type="submit"]:hover {
  background-color: #5a3d2c;
}

.cancel-same {
  width: 100%;
  padding: 10px;
  font-size: 16px;
  border: 1px solid #a68c73;
  border-radius: 4px;
  box-sizing: border-box;
  background-color: #dbc8b8; /* light coffee tone */
  color: #3e2b1f;
  cursor: pointer;
}

.cancel-same:hover {
  background-color: #c9b8a6;
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
/* Apply custom font to SweetAlert modals */
.swal2-popup {
  font-family: 'Winky Rough', serif !important;
  letter-spacing: 2px;
}


</style>

</head>
<body>
  <div class="form-wrapper">
    <h2>Add New Book</h2>

    <form method="POST">
  <label for="title">Title</label>
  <input type="text" name="title" required>

  <label for="author">Author</label>
  <input type="text" name="author" required>

  <label for="genre">Genre</label>
  <select name="genre" required>
    <option value="">-- Select Genre --</option>
    <option value="Horror">Horror</option>
    <option value="Romance">Romance</option>
    <option value="Sci-Fi">Sci-Fi</option>
    <option value="Fantasy">Fantasy</option>
    <option value="Mystery">Mystery</option>
    <option value="Non-Fiction">Non-Fiction</option>
    <option value="Biography">Biography</option>
    <option value="Adventure">Adventure</option>
  </select>

  <button type="submit">Save</button>

  <!-- ✅ Cancel button inside the form -->
 <button type="button" class="cancel-same" onclick="location.href='index.php'">Back</button>

</form>

  </div>
<?php if (isset($success) && $success): ?>
<script>
  Swal.fire({
    title: 'Book Added!',
    text: 'The book was successfully saved.',
    icon: 'success',
    confirmButtonColor: '#6f4f3a',
    background: '#f2e8dd',
    color: '#3e2b1f',
    confirmButtonText: 'OK'
  });
</script>
<?php endif; ?>

</body>
</html>
