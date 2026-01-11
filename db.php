<?php
$host = "sql100.infinityfree.com";
$user = "if0_40401498";  // MySQL kullanıcı adın
$pass = "s2XJc2KRuRxOW5";      // MySQL şifren
$db   = "if0_40401498_syline";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Veritabanı bağlantı hatası: " . $conn->connect_error);
    }
    ?>