<?php
require "../assets/config/db.php";
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $yeniOzet = htmlspecialchars(trim($_POST['manuelOzet']));
    $id = intval($_POST['makaleId']);
    $query = "select * from makaleler where id='$id';";
    $result = mysqli_query($conn,$query);
    $result = mysqli_fetch_assoc($result);
    $eskiOzet = $result['otomatik_ozet'];
    $eskiOzet = mysqli_real_escape_string($conn, $eskiOzet);
    $yeniOzet = mysqli_real_escape_string($conn, $yeniOzet);
    $query = "update makaleler set eski_ozet='$eskiOzet', otomatik_ozet='$yeniOzet' where id='$id';";
    $result = mysqli_query($conn,$query);
    header("location:../makaleDetay.php?id=$id");
}

?>