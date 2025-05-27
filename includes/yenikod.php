<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php_errors.log');

require_once "../includes/function.php"; 
require_once "../assets/config/db.php"; 


$yonlendirme_sayfasi = "../register/register.php"; 
$session_eposta_anahtari = 'onay_eposta'; 
$session_kod_anahtari = 'onay_kodu';     
$session_zaman_anahtari = 'registration_time';
$cookie_basari_adi = "yenikod_success";
$cookie_hata_adi = "uyari_register2"; 

if (!isset($_SESSION[$session_eposta_anahtari]) || !isset($_SESSION[$session_zaman_anahtari]) || !isset($_SESSION['registration_data'])) {
    header("location: register.php"); 
    exit;
}

$eposta = $_SESSION[$session_eposta_anahtari];
$kullanici_ad_soyad = $_SESSION['ad'];
$son_kod_gonderim_zamani = $_SESSION[$session_zaman_anahtari];
$bekleme_suresi = 90; 

if (time() < ($son_kod_gonderim_zamani + $bekleme_suresi)) {
    $kalan_sure = ($son_kod_gonderim_zamani + $bekleme_suresi) - time();
    setcookie($cookie_hata_adi, "Yeni kod istemek için lütfen {$kalan_sure} saniye daha bekleyin.", time() + 30, "/");
    header("location: " . $yonlendirme_sayfasi);
    exit;
} else {
    $yeni_dogrulama_kodu = rand(100000, 999999);
    $_SESSION[$session_kod_anahtari] = $yeni_dogrulama_kodu;
    $_SESSION[$session_zaman_anahtari] = time();

   
    gonderEposta($eposta, $kullanici_ad_soyad, $yeni_dogrulama_kodu);
        
    setcookie($cookie_basari_adi, "Yeni doğrulama kodunuz e-posta adresinize başarıyla gönderildi.", time() + 30, "/");

    header("location: " . $yonlendirme_sayfasi);
    exit;
}

?>