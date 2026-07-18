<?php
header("Content-Type: application/json");
include 'koneksi.php';

// Membaca data JSON yang dikirim oleh JavaScript
$input = json_decode(file_get_contents('php://input'), true);

if ($input) {
    $nama = mysqli_real_escape_string($conn, $input['nama']);
    $umur = intval($input['umur']);
    $jk = mysqli_real_escape_string($conn, $input['jk']);
    $kategori = mysqli_real_escape_string($conn, $input['kategori']);
    $lomba = mysqli_real_escape_string($conn, $input['lomba']);
    $kelompok = mysqli_real_escape_string($conn, $input['kelompok']);

    $query = "INSERT INTO pendaftar (nama, umur, jk, kategori, lomba, kelompok) 
              VALUES ('$nama', $umur, '$jk', '$kategori', '$lomba', '$kelompok')";

    if (mysqli_query($conn, $query)) {
        echo json_encode(["status" => "success", "message" => "Pendaftaran berhasil disimpan secara online!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Gagal menyimpan ke database: " . mysqli_error($conn)]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Data tidak valid."]);
}
?>