<?php
// File: config.php
$host = "localhost";
$username = "root";
$password = "";
$database = "db_uaspemroweb";

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>