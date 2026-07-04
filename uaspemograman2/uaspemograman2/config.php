<?php
// ==========================================================
// Konfigurasi koneksi database
// Sesuaikan host, user, password sesuai environment (XAMPP/Laragon/dll)
// ==========================================================
$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_paspor";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error() .
        "\nPastikan database 'db_paspor' sudah dibuat dengan mengimpor schema.sql");
}

mysqli_set_charset($conn, "utf8mb4");
?>
