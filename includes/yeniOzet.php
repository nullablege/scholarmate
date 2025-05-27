<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php_errors.log');
    require "function.php";
    require "../assets/config/db.php";
    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        $id = intval($_POST['makaleId2']);
        $query = "select * from makaleler where id='$id';";
        $result = mysqli_query($conn,$query);
        $result = mysqli_fetch_assoc($result);
        $eskiOzet = $result['otomatik_ozet'];
        $yeniOzet = TekrarOzetle($eskiOzet);
        $eskiOzet = mysqli_real_escape_string($conn, $eskiOzet);
        $yeniOzet = mysqli_real_escape_string($conn, $yeniOzet);
        $query = "update makaleler set eski_ozet='$eskiOzet', otomatik_ozet='$yeniOzet' where id='$id';";
        $result = mysqli_query($conn,$query);
        header("location:../makaleDetay.php?id=$id");
    }
?>