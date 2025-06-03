<?php

function check_login() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }
}

function check_admin() {
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
        header("Location: index.php"); // Redirect ke homepage jika bukan admin
        exit();
    }
}

function check_user() {
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
        header("Location: index.php");
        exit();
    }
}

// REMOVED: The unconditional check_user(); call from here.
// This function should only be called manually where needed.

?>