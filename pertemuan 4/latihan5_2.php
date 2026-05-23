<html>
<head>
    <title>Tabel Perkalian</title>
</head>
<body>
<h2>Tabel Perkalian</h2>
<?php
for ($i = 15; $i <= 45; $i++) {
    if ($i % 2 == 0) continue; 
    echo "12 * $i = " . (12 * $i) . "<br>";
}
?> 
</body>
</html>