<?php
$servername = "localhost";
$username = "root"; // Sesuaikan dengan username MySQL kamu
$password = "";     // Sesuaikan dengan password MySQL kamu
$dbname = "tomatolite_db";

// Buat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}
?>