<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php_errors.log');
require "../assets/config/db.php";
require "function.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $mevcut_sifre = $_POST['mevcutSifre'] ?? "";
    $yeni_sifre = $_POST['yeniSifre'] ?? "";
    $yeni_sifre_tekrar = $_POST['yeniSifreTekrar'] ?? "";
    $eposta = $_SESSION['eposta'];

    if (empty($mevcut_sifre) || empty($yeni_sifre) || empty($yeni_sifre_tekrar)) {
        $error_message_sifre = "Tüm şifre alanları doldurulmalıdır.";
    } elseif ($yeni_sifre !== $yeni_sifre_tekrar) {
        $error_message_sifre = "Yeni şifreler eşleşmiyor.";
    } elseif (strlen($yeni_sifre) < 8) {
        $error_message_sifre = "Yeni şifre en az 8 karakter olmalıdır.";
    }
    else {
        if(validateAndHashPassword($yeni_sifre,$yeni_sifre_tekrar)){
            if(kullaniciDogruMu($conn, $eposta, $mevcut_sifre)){
                $hashli = validateAndHashPassword($yeni_sifre,$yeni_sifre_tekrar);
                $query = "update uye set sifre='$hashli' where e_posta='$eposta'";
                $result = mysqli_query($conn,$query);
                if($result){
                    $success_message_sifre = "Şifreniz başarıyla değiştirildi.";
                    setcookie("sifreDegisti",$success_message_sifre,time()+10,'/');
                    header("location:../profil.php");
                }
                
            }
            else{
                $error_message_sifre = "Mevcut şifre hatalı.";
                setcookie("sifreDegismedi",$error_message_sifre,time()+10,'/');
                header("location:../profil.php");
            }

        }
        else{
            $error_message_sifre = "Şifreniz değiştirilemedi.";
            setcookie("sifreDegismedi",$error_message_sifre,time()+10,'/');
            header("location:../profil.php");
        }

    }
}

?>