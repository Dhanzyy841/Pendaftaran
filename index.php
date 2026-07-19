<?php
// 1. Pengaturan Header agar HP Lain Tidak Terblokir (CORS)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");

// 2. Koneksi ke phpMyAdmin XAMPP
$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_lomba";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die(json_encode(["error" => "Koneksi database gagal: " . $conn->connect_error]));
}

// 3. Membaca Data JSON yang Dikirim oleh JavaScript Handphone
$inputData = file_get_contents("php://input");
$data = json_decode($inputData, true);

// 4. Jika Ada Data Masuk, Simpan ke MySQL
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($data)) {
    // Sesuaikan nama kolom di dalam $data[...] dengan properti object di script.js Anda
    $nama  = $data['nama'] ?? '';
    $lomba = $data['lomba'] ?? '';
    $hp    = $data['hp'] ?? '';

    if (!empty($nama) && !empty($lomba)) {
        $stmt = $conn->prepare("INSERT INTO pendaftar (nama, lomba, hp) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $nama, $lomba, $hp);
        
        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Data berhasil masuk phpMyAdmin!"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Gagal menyimpan ke database."]);
        }
        $stmt->close();
    } else {
        echo json_encode(["status" => "error", "message" => "Data tidak boleh kosong."]);
    }
    $conn->close();
    exit;
}

// 5. Jika Diakses Biasa Lewat Browser HP, Tampilkan Frontend Asli
// Menampilkan file HTML utama secara utuh tanpa merubah kodenya sedikitpun
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    header("Content-Type: text/html; charset=UTF-8");
    if (file_exists("index.html")) {
        echo file_get_contents("index.html");
    } else {
        echo "File index.html tidak ditemukan di folder XAMPP.";
    }
    exit;
}
?>
