<?php

$koneksi = mysqli_connect("localhost","root","","bukutamu");

if(isset($_POST['simpan'])){

    $nama   = $_POST['nama'];
    $email  = $_POST['email'];
    $pesan  = $_POST['pesan'];

    mysqli_query($koneksi,
    "INSERT INTO tamu(nama,email,pesan)
    VALUES('$nama','$email','$pesan')");

    echo "Data berhasil disimpan!";
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Buku Tamu</title>
</head>
<body>

<h2>Form Buku Tamu</h2>

<form method="POST">

    Nama : <br>
    <input type="text" name="nama" required>
    <br><br>

    Email : <br>
    <input type="email" name="email" required>
    <br><br>

    Pesan : <br>
    <textarea name="pesan" required></textarea>
    <br><br>

    <input type="submit" name="simpan" value="Simpan">

</form>

</body>
</html>
