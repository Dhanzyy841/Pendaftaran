<?php
header("Content-Type: application/json");
include 'koneksi.php';

$result = mysqli_query($conn, "SELECT * FROM pendaftar ORDER BY id DESC");
$data = [];

while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}

echo json_encode($data);
?>