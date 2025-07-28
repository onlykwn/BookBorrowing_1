<?php
session_start();

if (!isset($_SESSION['user']) || !isset($_SESSION['role'])) {
header("Location: login.php");
exit;
}

include 'config.php';
// ✅ Approve user from index
if (isset($_GET['approve'])) {
$userId = intval($_GET['approve']);
$conn->query("UPDATE users SET status='approved' WHERE id = $userId");
header("Location: index.php");
exit;
}

$pendingCount = 0;

// ✅ Only show pending count if logged in as main Admin user
if ($_SESSION['user'] === 'Admin') {
$countQuery = $conn->query("SELECT COUNT(*) AS total FROM users WHERE status = 'pending'");
$pendingCount = $countQuery->fetch_assoc()['total'] ?? 0;
}
include 'functions.php';
// Search logic continues...
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

$sql = "SELECT * FROM books";
if (!empty($search)) {
$sql .= " WHERE title LIKE '%$search%'
OR author LIKE '%$search%'
OR genre LIKE '%$search%'";
$sql .= " ORDER BY title ASC";
} else {
$sql .= " ORDER BY title ASC";
}

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Book Borrowing and Inventory</title>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300..700&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Lora:wght@600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="style.css">
<style>
/* [Styles kept the same — unchanged from your version] */
body {
margin: 0;
padding: 0;
height: 100vh;
background: url('images/bb1.jpg') no-repeat center center fixed;
background-size: cover;
color: #2e1f14;
font-family: Arial, sans-serif;
position: relative;
}
body::before {
content: '';
position: fixed;
top: 0;
left: 0;
height: 100%;
width: 100%;
background: rgba(63, 57, 57, 0.51);
z-index: -1;
}

.main-title {
font-family: 'Cinzel', serif;
font-size: 50px;
color: #2e1f14;
text-transform: uppercase;
text-align: center;
margin-bottom: 20px;
letter-spacing: 1px;
}

.space-grotesk {
font-family: "Space Grotesk", sans-serif;
font-weight: 600;
font-size: 55px;
font-style: normal;
}

h2 {
font-family: 'Vast Shadow', serif;
color: #302014ff;
text-align: center;
letter-spacing: 5px; /* adjust this value to control spacing */
margin-bottom: 10px;
}

.top-links {
display: flex;
justify-content: center;
gap: 20px;
margin-bottom: 30px;
}

.top-links button {
background-color: #5a3e2b;
color: #fff;
border: none;
border-radius: 6px;
padding: 10px 20px;
font-weight: bold;
cursor: pointer;
}

.top-links button:hover {
background-color: #3d291b;
}

#bookSearchInput {
padding: 10px 14px;
border-radius: 8px;
border: 1px solid #a88b70;
background-color: #ede1d4;
color: #2e1f14;
font-size: 15px;
box-sizing: border-box;
width: 300px;
}

table {
width: 80%;
margin: 0 auto;
border-collapse: collapse;
margin-top: 20px;
background-color: #f7f1eac2;
}

th, td {
border: 1px solid #c4b09a;
padding: 10px;
text-align: center;
}

th {
background-color: #a88b70;
color: #fff;
}

td {
color: #2e1f14;
}

.status-available {
color: #4a8c69;
font-weight: bold;
}

.status-borrowed {
color: #8c3c2f;
font-weight: bold;
}

a {
color: #7e5b3c;
text-decoration: none;
margin: 0 4px;
}

a:hover {
text-decoration: underline;
color: #5a3e2b;
}

span.gray {
color: gray;
font-style: italic;
}

.sub-title {
font-family: 'Lora', sans-serif;
font-size: 28px;
color: #3d2a1a;
text-align: center;
margin-top: 10px;
text-transform: uppercase;
letter-spacing: 0.5px;
}

.rationale-regular {
font-family: "Rationale", sans-serif;
font-weight: 700;
font-style: normal;
font-size: 30px;
color: #e6ccb2;
letter-spacing: 1px;
}

.pending-box {
position: absolute;
top: 20px;
left: 20px;
background: #f1e3d4;
border: 1px solid #b29b85;
padding: 14px;
border-radius: 8px;
font-size: 14px;
max-width: 90%;
width: 260px;
box-sizing: border-box;
z-index: 999;
}

@media (max-width: 600px) {
.pending-box {
position: relative;
margin: 10px auto;
left: 0;
right: 0;
top: 10px;
width: 95%;
}
}

.pending-box h4 {
margin: 0 0 8px 0;
font-weight: bold;
font-size: 15px;
}

.pending-user {
margin-top: 6px;
}

.pending-user {
margin-top: 8px;
display: flex;
align-items: center;
justify-content: space-between;
background: #f6eee7;
padding: 8px 10px;
border-radius: 6px;
border: 1px solid #c7b4a1;
}

.approve-link, .decline-link {
margin-left: 8px;
font-weight: bold;
text-decoration: none;
padding: 4px 10px;
border-radius: 5px;
}

.approve-link {
background: #a3c4a0;
color: #234c1f;
}

.decline-link {
background: #d68a85;
color: #601a1a;
}

.approve-link:hover {
background: #89b489;
}

.decline-link:hover {
background: #c2736e;
}
/* From Uiverse.io by JaydipPrajapati1910 */
.Btn {
display: flex;
align-items: center;
justify-content: flex-start;
width: 45px;
height: 45px;
border: none;
border-radius: 50%;
cursor: pointer;
position: relative;
overflow: hidden;
transition-duration: .3s;
box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.199);
background-color: color: #3e2b1f; /* unified darker brown text */

}

/* icon (arrow) */
.sign {
width: 100%;
transition-duration: .3s;
display: flex;
align-items: center;
justify-content: center;
padding-left: 0; /* reset default padding */
}

.sign svg {
width: 17px;
}

.sign svg path {
fill: white;
}

/* logout text */
.text {
position: absolute;
right: 0%;
width: 0%;
opacity: 0;
color: white;
font-size: 1.2em;
font-weight: 600;
transition-duration: .3s;
white-space: nowrap;
}

/* hover effects */
.Btn:hover {
width: 130px;
border-radius: 40px;
transition-duration: .3s;
}

.Btn:hover .sign {
width: 20%; /* make icon narrower */
transition-duration: .3s;
padding-left: 12px; /* space between left edge and icon */
}

.Btn:hover .text {
opacity: 1;
width: auto;
transition-duration: .3s;
padding-left: 10px; /* space between icon and text */
padding-right: 20px;
white-space: nowrap;
}

/* button click effect */
.Btn:active {
transform: translate(2px ,2px);
}

.logout-container {
position: absolute;
top: 20px;
right: 20px;
z-index: 999;
}

</style>

</head>
<body>

<!-- ✅ Only main Admin sees pending users -->

<?php if (isset($_SESSION['user'])): ?>
<div style="position: absolute; top: 15px; right: 20px; display: flex; align-items: center;">
<span style="margin-right: 15px; color: #ffe7c8ff; font-weight: bold;">
👋 Welcome, <?= htmlspecialchars($_SESSION['user']) ?>
</span>
<a href="logout.php" style="text-decoration: none;">
<button class="Btn">
<div class="sign">
<svg viewBox="0 0 512 512"><path d="M377.9 105.9L500.7 228.7c7.2 7.2 11.3 17.1 11.3 27.3s-4.1 20.1-11.3 27.3L377.9 406.1c-6.4 6.4-15 9.9-24 9.9c-18.7 0-33.9-15.2-33.9-33.9l0-62.1-128 0c-17.7 0-32-14.3-32-32l0-64c0-17.7 14.3-32 32-32l128 0 0-62.1c0-18.7 15.2-33.9 33.9-33.9c9 0 17.6 3.6 24 9.9zM160 96L96 96c-17.7 0-32 14.3-32 32l0 256c0 17.7 14.3 32 32 32l64 0c17.7 0 32 14.3 32 32s-14.3 32-32 32l-64 0c-53 0-96-43-96-96L0 128C0 75 43 32 96 32l64 0c17.7 0 32 14.3 32 32s-14.3 32-32 32z"></path></svg>
</div>
<div class="text">Logout</div>
</button>
</a>
</div>

</div>
<?php endif; ?>
<?php if ($_SESSION['user'] === 'Admin'): ?>
<div id="pendingBox" style="display: none; margin: 20px auto; max-width: 400px; padding: 15px; background: #f9f9f9; border: 1px solid #ccc; border-radius: 8px;">
<h4>Pending Accounts</h4>
<?php
$pending = $conn->query("SELECT * FROM users WHERE status = 'pending'");
if ($pending->num_rows > 0):
while ($row = $pending->fetch_assoc()):
?>
<div style="margin-bottom: 10px;">
👤 <strong><?= htmlspecialchars($row['username']) ?></strong> (<?= $row['role'] ?>)
<a href="#" onclick="approveUser(<?= $row['id'] ?>); return false;" style="color: green;">[Approve]</a>
<a href="#" onclick="declineUser(<?= $row['id'] ?>); return false;" style="color: red;">[Decline]</a>
</div>
<?php endwhile; else: ?>
<div>No pending accounts</div>
<?php endif; ?>
</div>
<?php endif; ?>

<div class="top-wrapper"><br>
<h2 class="space-grotesk">BOOK BORROWING / INVENTORY</h2>

<div class="top-links">
<?php if ($_SESSION['role'] !== 'student'): ?>
<a href="add_book.php"><button class="ui-button">Add Book</button></a>
<?php endif; ?>

<a href="view_borrowed.php"><button class="ui-button">View Borrowed</button></a>

<?php if ($_SESSION['user'] === 'Admin'): ?>
<a href="approve_users.php">
<button class="ui-button" style="background-color: #a07b58;">
Approve Accounts<?= $pendingCount > 0 ? " ({$pendingCount})" : " (0)" ?>
</button>
</a>
<?php endif; ?>


</div>
</div>

<br>
<center><h2 class="sub-title rationale-regular">All Books</h2></center>
<div style="display:flex; justify-content:center; align-items:center; gap: 20px; margin-bottom: 15px;">
<input type="text" id="bookSearchInput" placeholder="Search books...">
</div>

<div class="table-wrapper">
<table id="bookTable">
<thead>
<tr>
<th>TITLE</th>
<th>AUTHOR</th>
<th>GENRE</th>
<th>STATUS</th>
<th>ACTIONS</th>
</tr>
</thead>
<tbody>
<?php if ($result->num_rows > 0): ?>
<?php while ($row = $result->fetch_assoc()): ?>
<tr>
<td><?= htmlspecialchars($row['title']) ?></td>
<td><?= htmlspecialchars($row['author']) ?></td>
<td><?= htmlspecialchars($row['genre']) ?></td>
<td>
<span class="<?= $row['status'] == 'Available' ? 'status-available' : 'status-borrowed' ?>">
<?= $row['status'] ?>
</span>
</td>
<td>
<?php if ($_SESSION['role'] !== 'student'): ?>
<a href="edit_book.php?id=<?= $row['book_id'] ?>">Edit</a> |
<a href="#" onclick="confirmDelete(<?= $row['book_id'] ?>, '<?= $row['status'] ?>')">Delete</a>


<?php if ($row['status'] == 'Available'): ?>
<a href="borrow_book.php?id=<?= $row['book_id'] ?>">Borrow</a>
<?php else: ?>
<span class="gray">Not Available</span>
<?php endif; ?>
<?php else: ?>
<span class="gray">View Only</span>
<?php endif; ?>
</td>

</tr>
<?php endwhile; ?>
<?php else: ?>
<tr><td colspan="5">No books found.</td></tr>
<?php endif; ?>
</tbody>
</table>
</div>

<script>
document.getElementById("bookSearchInput").addEventListener("keyup", function() {
let filter = this.value.toLowerCase();
let rows = document.querySelectorAll("#bookTable tbody tr");

rows.forEach(row => {
let text = row.textContent.toLowerCase();
row.style.display = text.includes(filter) ? "" : "none";
});
});
</script>
<script>
function declineUser(userId) {
if (!confirm("Are you sure you want to decline this user?")) return;

const xhr = new XMLHttpRequest();
xhr.open('GET', 'approve_users.php?decline=' + userId, true);
xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

xhr.onload = function () {
if (xhr.status === 200) {
const result = JSON.parse(xhr.responseText);
if (result.success) {
document.getElementById('user-' + userId).remove();
const notif = document.getElementById('notif');
notif.textContent = "❌ " + result.message;
notif.style.display = 'block';
setTimeout(() => notif.style.display = 'none', 2000);
} else {
alert(result.message);
}
} else {
alert("Something went wrong.");
}
};

xhr.send();
}
</script>
<script>
function togglePending() {
const box = document.getElementById("pendingBox");
box.style.display = box.style.display === "none" ? "block" : "none";
}
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function confirmDelete(id, status) {
if (status === 'Borrowed') {
Swal.fire({
title: 'Cannot Delete',
text: 'This book is currently borrowed and cannot be deleted.',
icon: 'error',
confirmButtonColor: '#6f4f3a',
background: '#f2e8dd',
color: '#3e2b1f',
confirmButtonText: 'OK',
customClass: {
popup: 'swal2-font'
}
});
return;
}

// If book is available, allow delete confirmation
Swal.fire({
title: 'Are you sure?',
text: "This book will be permanently deleted.",
icon: 'warning',
showCancelButton: true,
confirmButtonColor: '#b31919ff',
cancelButtonColor: '#aaa',
background: '#f2e8dd',
color: '#3e2b1f',
confirmButtonText: 'Yes, delete it!',
cancelButtonText: 'Cancel',
customClass: {
popup: 'swal2-font'
}
}).then((result) => {
if (result.isConfirmed) {
window.location.href = 'delete_book.php?id=' + id;
}
});
}
</script>

<style>
.swal2-font {
font-family: 'Winky Rough', serif !important;
letter-spacing: 2px;
}
</style>
<?php if (isset($_SESSION['delete_success'])): ?>
<script>
Swal.fire({
title: 'Deleted!',
text: 'The book has been deleted.',
icon: 'success',
confirmButtonColor: '#6f4f3a',
background: '#f2e8dd',
color: '#3e2b1f',
confirmButtonText: 'OK',
customClass: {
popup: 'swal2-font'
}
});
</script>
<?php unset($_SESSION['delete_success']); ?>
<?php endif; ?>
<?php if (isset($_SESSION['borrow_success'])): ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
Swal.fire({
title: 'Book Borrowed!',
text: 'The book has been successfully borrowed.',
icon: 'success',
confirmButtonColor: '#6f4f3a',
background: '#f2e8dd',
color: '#3e2b1f',
confirmButtonText: 'OK',
customClass: {
popup: 'swal2-font'
}
});
</script>
<style>
.swal2-font {
font-family: 'Winky Rough', serif !important;
letter-spacing: 2px;
}
</style>
<?php unset($_SESSION['borrow_success']); ?>
<?php endif; ?>

</body>
</html>
