<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'main_admin';
}

function isStudent() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'student';
}

function isStaffOrAdmin() {
    return isset($_SESSION['role']) && (
        $_SESSION['role'] === 'admin' ||
        $_SESSION['role'] === 'staff' ||
        $_SESSION['role'] === 'main_admin' // <-- add this line
    );
}
?>
