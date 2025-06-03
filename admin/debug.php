<?php
session_start();
echo "Session data: ";
print_r($_SESSION);

if (isset($_SESSION['role'])) {
    echo "<br>Role: " . $_SESSION['role'];
} else {
    echo "<br>Tidak ada role di session";
}

if (file_exists('../includes/db.php')) {
    echo "<br>File db.php ada";
} else {
    echo "<br>File db.php TIDAK ada";
}

if (file_exists('../includes/auth.php')) {
    echo "<br>File auth.php ada";
} else {
    echo "<br>File auth.php TIDAK ada";
}
?>