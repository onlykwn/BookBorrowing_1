<?php
session_start();
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

if (!isset($_SESSION['user'])) {
  header("Location: login.php");
  exit;
}

include 'config.php';

$result = $conn->query("
  SELECT br.record_id, br.book_id, br.borrower_id, br.borrow_date, br.due_date,
         b.title, bw.name AS borrower_name, bw.contact
  FROM borrow_records br
  JOIN books b ON br.book_id = b.book_id
  JOIN borrowers bw ON br.borrower_id = bw.borrower_id
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Borrowed Books</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Winky+Rough:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">
  <style>
body {
  background: url('images/bb1.jpg') no-repeat center center fixed;
  background-size: cover;
  font-family: 'Arial', sans-serif;
  margin: 0;
  padding: 0;
  color: #3e2b1f;
}

body::before {
  content: '';
  position: fixed;
  top: 0;
  left: 0;
  height: 100%;
  width: 100%;
  background: rgba(32, 24, 20, 0.6);
  z-index: -1;
}

h2 {
  text-align: center;
  font-size: 40px;
  font-family: 'Winky Rough', serif;
  margin: 20px 0;
  color: #2f1e13;
  font-weight: 500;
  letter-spacing: 10px;
}

.table-wrapper {
  max-width: 1100px;
  margin: 0 auto;
  padding: 0 20px;
  overflow-x: auto;
}

#borrowedTable {
  width: 100%;
  border-collapse: collapse;
  background-color: #e6d2c4;
}

#borrowedTable th,
#borrowedTable td {
  border: 1px solid #a58c76;
  padding: 12px;
  text-align: center;
  color: #3a2616;
}

#borrowedTable th {
  background-color: #c7a892;
  font-weight: bold;
}

.back-style {
  margin: 20px;
  padding: 10px 20px;
  background-color: #4a2f22;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-weight: bold;
}

.back-style:hover {
  background-color: #6e4b3a;
}

a {
  color: #8b5e3c;
  text-decoration: none;
}

a:hover {
  text-decoration: underline;
  color: #a8774f;
}

.reminder {
  font-size: 14px;
  font-weight: bold;
  color: #8b0000;
}
  </style>
</head>
<body>

<a href="index.php">
  <button class="back-style">Back</button>
</a>

<h2>Borrowed Books</h2>
<br>
<div class="table-wrapper">
  <table id="borrowedTable">
    <thead>
      <tr>
        <th>BOOK</th>
        <th>BORROWER</th>
        <th>CONTACT</th>
        <th>BORROW DATE</th>
        <th>DUE DATE</th>
        <th>ACTION</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
          <?php
            $role = isset($_SESSION['user']['role']) ? $_SESSION['user']['role'] : '';
            $dueDate = strtotime($row['due_date']);
            $today = strtotime(date('Y-m-d'));
            $daysLeft = floor(($dueDate - $today) / (60 * 60 * 24));
            $reminder = '';
            if ($daysLeft > 0) {
              $reminder = "<div class='reminder'>Reminder: $daysLeft day(s) left</div>";
            } elseif ($daysLeft == 0) {
              $reminder = "<div class='reminder'>Due today!</div>";
            } elseif ($daysLeft < 0) {
              $reminder = "<div class='reminder' style='color: darkred;'>Overdue by " . abs($daysLeft) . " day(s)</div>";
            }
          ?>
          <tr>
            <td><?= htmlspecialchars($row['title']) ?></td>
            <td><?= htmlspecialchars($row['borrower_name']) ?></td>
            <td><?= htmlspecialchars($row['contact']) ?></td>
            <td><?= !empty($row['borrow_date']) ? date('m/d/Y', strtotime($row['borrow_date'])) : 'N/A' ?></td>
            <td>
              <?= !empty($row['due_date']) ? date('m/d/Y', strtotime($row['due_date'])) : 'N/A' ?>
              <?= $reminder ?>
            </td>
            <td>
              <?php if ($role !== 'student'): ?>
                <a href="return_book.php?id=<?= $row['record_id'] ?>">Return</a>
              <?php else: ?>
                <span style="color: #999;">N/A</span>
              <?php endif; ?>
            </td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr>
          <td colspan="6">No borrowed books found.</td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?php if (isset($_SESSION['swal'])): ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
Swal.fire({
  icon: '<?= $_SESSION['swal']['type'] ?>',
  title: '<?= $_SESSION['swal']['title'] ?>',
  text: '<?= $_SESSION['swal']['text'] ?>',
  confirmButtonColor: '#a97b5d',
  background: '#f2e8dd',
  color: '#5b4433',
});
</script>
<?php unset($_SESSION['swal']); endif; ?>

</body>
</html>
