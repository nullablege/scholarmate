<?php
session_start();
require "function.php";
require "../assets/config/db.php";



if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $icerik = $_POST['icerik'];
    $icerik = mysqli_real_escape_string($conn,$icerik);
    $makaleid = $_POST['makaleid'];
    $makaleid = mysqli_real_escape_string($conn,$makaleid);
    $referans = $_POST['referans'];
    $referans = mysqli_real_escape_string($conn,$referans);
    $kullanici_id = $_SESSION['id'];

    $query = "insert into notlar(makale_id, kullanici_id, icerik, referans) values('$makaleid','$kullanici_id', '$icerik', '$referans');";
    $result = mysqli_query($conn,$query);

    header("location:../makaleDetay.php?id=$makaleid");
    exit();
}


?>