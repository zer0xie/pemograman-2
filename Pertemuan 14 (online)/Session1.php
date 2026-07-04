<?php

session_start(); 

if (isset($_POST['Login'])) { 
    $user = $_POST['user']; 
    $pass = $_POST['pass']; 

    // Periksa login
    if ($user == "ihwanto" && $pass == "123") { 
        // Menciptakan session
        $_SESSION['login'] = $user; 
        
        // Menuju ke halaman pemeriksaan session
        echo "<h1>Anda berhasil LOGIN</h1>"; 
        echo "<h2>Klik <a href='session2.php'>di sini (session2.php)</a> untuk menuju ke halaman pemeriksaan session</h2>"; 
    } else {
        echo "<h2>Username atau Password Salah!</h2>";
        echo "Silahkan <a href='session1.php'>Login Kembali</a>";
    }
} else { 
?>
<html>
<head>
    <title>Login here...</title> 
</head>
<body>
    <form action="" method="post"> 
        <h2>Login Here...</h2> 
        Username : <input type="text" name="user"><br> 
        Password : <input type="password" name="pass"><br> 
        <input type="submit" name="Login" value="Log In"> 
    </form>
</body>
</html>
<?php 
} 
?>