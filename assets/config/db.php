<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$host = "localhost";
$username = "root";
$password = "";
$db = "makale";

$conn = new mysqli($host,$username,$password,$db);

if ($conn->connect_error) {
    die("Bağlantı hatası: " . $mysqli->connect_error);
}

if (!$conn->set_charset("utf8")) {
    die("Karakter seti ayarlanamadı: " . $mysqli->error);
}


?>