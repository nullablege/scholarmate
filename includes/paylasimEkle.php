<?php
session_start();
require "function.php";
require "../assets/config/db.php";
$kendi_id = $_SESSION['id'];

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $mail = trim(htmlspecialchars($_POST['eklenecekMail']));
    $mail = mysqli_real_escape_string($conn, $mail);
    $kullanici = getUserByEmail($conn, $mail);
    $makale_id = intval($_POST['makaleId']);
    $id = $kullanici['id'];
    $eposta = $kullanici['e_posta'];
    $kontrolQ = "select * from makale_paylasim where makale_id=$makale_id and paylasilan_id=$id";
    $kontrolR = mysqli_query($conn,$kontrolQ);
    if(!mysqli_num_rows($kontrolR) > 0){
        if($mail != $_SESSION['eposta']){
            if($kullanici != null){
                $query = "insert into makale_paylasim(makale_id, paylasan_id, paylasilan_id, paylasilan_eposta) values('$makale_id','$kendi_id' ,'$id', '$eposta');";
                $result = mysqli_query($conn,$query);
                
            }
        }
    }


    header("location:../makaleDetay.php?id=$makale_id");
}

?>