<html>
<head><title>Contoh Penggunaan UDF</title></head>
<body>

<!-- Menentukan Form Input -->
<form method="POST">
Masukkan Bilangan Pertama : <br>
<input type="text" name="A" size="10"> <br>

Masukkan Bilangan Kedua : <br>
<input type="text" name="B" size="10"> <br><br>

<input type="submit" value="Hitung">
</form>

<?php
// Cek apakah form sudah dikirim
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Ambil input
    $A = $_POST["A"];
    $B = $_POST["B"];

    // Function
    function jumlah($A, $B) {
        return $A + $B;
    }

    function kurang($A, $B) {
        return $A - $B;
    }

    function kali($A, $B) {
        return $A * $B;
    }

    function bagi($A, $B) {
        if ($B == 0) {
            return "Tidak bisa dibagi 0";
        }
        return $A / $B;
    }

    echo "<br>";
    echo "Bilangan Pertama : $A <br>";
    echo "Bilangan Kedua : $B <br><br>";

    echo "Hasil Penjumlahan:<br>";
    printf("%d + %d = %d<br><br>", $A, $B, jumlah($A, $B));

    echo "Hasil Pengurangan:<br>";
    printf("%d - %d = %d<br><br>", $A, $B, kurang($A, $B));

    echo "Hasil Perkalian:<br>";
    printf("%d * %d = %d<br><br>", $A, $B, kali($A, $B));

    echo "Hasil Pembagian:<br>";
    $hasilBagi = bagi($A, $B);
    if (is_numeric($hasilBagi)) {
        printf("%d / %d = %0.2f<br><br>", $A, $B, $hasilBagi);
    } else {
        echo $hasilBagi . "<br><br>";
    }
}
?>

</body>
</html>