<?php
session_start();
require "function.php";
require "../assets/config/db.php";
$kendi_id = $_SESSION['id'];



if(($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['id'])) && isset($_GET['makale'])){

    $silinecek = $_GET['id'];
    $makale_id = $_GET['makale'];
    $query = "delete from makale_paylasim where id='$silinecek'";
    $result = mysqli_query($conn,$query);
    header("location:../makaleDetay.php?id=$makale_id");
}

?>