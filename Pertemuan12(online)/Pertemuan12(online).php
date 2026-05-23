<?php

// koneksi database
$koneksi = mysqli_connect("localhost","root","","latihan12");

if(!$koneksi){
    die("Koneksi gagal");
}

echo "<h2>MANIPULASI DATABASE</h2>";


// UPDATE DATA
mysqli_query($koneksi,
"UPDATE mahasiswa
SET umur='36'
WHERE nama='Karina'");

echo "Update data berhasil <br><br>";


// DELETE DATA
mysqli_query($koneksi,
"DELETE FROM mahasiswa
WHERE nama='Prabowo'");

echo "Delete data berhasil <br><br>";


// TAMPILKAN DATA
echo "<h3>Data Mahasiswa</h3>";

$query = mysqli_query($koneksi,
"SELECT * FROM mahasiswa");

while($data = mysqli_fetch_array($query)){

    echo "Nama : ".$data['nama']."<br>";
    echo "Umur : ".$data['umur']."<br><br>";
}

?>