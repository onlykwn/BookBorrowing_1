<?php
session_start();
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

// ✅ Check role correctly
if ($_SESSION['role'] === 'student') {
    $_SESSION['swal'] = [
        'type' => 'error',
        'title' => 'Access Denied',
        'text' => 'Students are not allowed to return books.'
    ];
    header("Location: view_borrowed.php");
    exit;
}

include 'config.php';

if (isset($_GET['id'])) {
    $record_id = intval($_GET['id']);

    $result = $conn->query("SELECT book_id FROM borrow_records WHERE record_id = $record_id");

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $book_id = $row['book_id'];

        $conn->query("UPDATE books SET status = 'Available' WHERE book_id = $book_id");
        $conn->query("DELETE FROM borrow_records WHERE record_id = $record_id");

        $_SESSION['swal'] = [
            'type' => 'success',
            'title' => 'Success',
            'text' => 'Book returned successfully.'
        ];
    }
}

header("Location: view_borrowed.php");
exit;
?>
