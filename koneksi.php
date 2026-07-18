<?php
$host = "localhost";
$user = "root";      // Sesuaikan dengan username database server Anda
$pass = "";          // Sesuaikan dengan password database server Anda
$db   = "lomba_hut";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi ke database gagal: " . mysqli_connect_error());
}
?>