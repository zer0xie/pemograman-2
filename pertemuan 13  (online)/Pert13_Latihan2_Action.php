<?php
// Include koneksi database DULU sebelum digunakan
include "Pert13_Koneksi.php";

// Cek apakah tombol Submit ditekan
if (isset($_POST['Submit']) && $_POST['Submit'] == "Submit") {
    
    // Amankan input dari SQL Injection
    $id_mahasiswa = mysqli_real_escape_string($koneksi, $_POST['id_mahasiswa']);
    $nama         = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $jurusan      = mysqli_real_escape_string($koneksi, $_POST['jurusan']);
    $alamat       = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    $telepon      = mysqli_real_escape_string($koneksi, $_POST['telepon']);
    
    // Validasi data kosong
    if (empty($id_mahasiswa) || empty($nama) || empty($jurusan) || empty($alamat) || empty($telepon)) {
        echo "<script>
                alert('Data Harap Dilengkapi!');
                document.location='Pert13_Latihan1_Form.php';
              </script>";
        exit;
    }
    
    // Cek apakah NIM sudah ada di database
    $cek_query = mysqli_query($koneksi, "SELECT id_mahasiswa FROM mahasiswa WHERE id_mahasiswa='$id_mahasiswa'");
    $cek = mysqli_num_rows($cek_query);
    
    if ($cek > 0) {
        echo "<script>
                alert('NIM sudah dipakai! Silahkan ganti NIM yang lain.');
                document.location='Pert13_Latihan1_Form.php';
              </script>";
        exit;
    }
    
    // Masukkan data ke tabel mahasiswa
    $input = "INSERT INTO mahasiswa (id_mahasiswa, nama, jurusan, alamat, telepon) 
              VALUES ('$id_mahasiswa', '$nama', '$jurusan', '$alamat', '$telepon')";
    
    $query_input = mysqli_query($koneksi, $input);
    
    if ($query_input) {
        // Jika sukses
        echo "<script>
                alert('Input Data Mahasiswa Berhasil!');
                document.location='Pert13_Latihan1_Form.php';
              </script>";
    } else {
        // Jika gagal
        echo "Input Data Mahasiswa Gagal! Error: " . mysqli_error($koneksi);
    }
    
    // Tutup koneksi
    mysqli_close($koneksi);
    
} else {
    // Jika halaman diakses langsung tanpa submit form
    header("Location: Pert13_Latihan1_Form.php");
    exit;
}
?>