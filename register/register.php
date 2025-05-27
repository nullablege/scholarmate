<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

require_once "../includes/function.php"; 
require_once "../assets/config/db.php";  





// Gerekli session verileri yoksa veya kullanıcı zaten giriş yapmışsa yönlendirme
// if (!isset($_SESSION['onay_kodu']) || !isset($_SESSION['registration_data']) || !isset($_SESSION['registration_time'])) {
//     header("location: register.php");
//     exit;
// }
// if (isset($_SESSION['login_user_id'])) { // login_user_id gibi spesifik bir session anahtarı kullanın
//     header("location: index.php");
//     exit;
// }


$registration_data = $_SESSION['registration_data'];
$ad_soyadi = htmlspecialchars($registration_data['ad'] . ' ' . $registration_data['soyad']); 
$eposta = htmlspecialchars($registration_data['eposta']);
$hashli_sifre = $registration_data['hashli_sifre'];
$unvan = htmlspecialchars($registration_data['unvan']);
$kurum = htmlspecialchars($registration_data['kurum']);
$arastirmaAlani = htmlspecialchars($registration_data['arastirmaAlani']);
$avatar = $registration_data['avatar'] ?? null;



$onay_kodu_session = $_SESSION['onay_kodu'];
//echo $onay_kodu_session;
$registration_time = $_SESSION['registration_time'];
$error_message = "";
$success_message = "";

if (isset($_COOKIE['uyari_register2'])) {
    $error_message = htmlspecialchars($_COOKIE['uyari_register2']);
    setcookie("uyari_register2", "", time() - 3600, "/");
}
if (isset($_COOKIE['yenikod_success'])) {
    $success_message = "Yeni doğrulama kodunuz e-posta adresinize gönderildi.";
    setcookie("yenikod_success", "", time() - 3600, "/");
}

$kod_gecerlilik_suresi = 5 * 60; 
$kod_suresi_doldu = false;

if (time() >= ($registration_time + $kod_gecerlilik_suresi)) {
    $kod_suresi_doldu = true;
    $error_message = $error_message ?: "Doğrulama kodunuzun süresi doldu. Lütfen <a href='../includes/yenikod.php' class='alert-link'>yeni bir kod isteyin</a>.";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($kod_suresi_doldu) {
        $error_message = "Doğrulama kodunuzun süresi doldu. İşlem yapılamadı. Lütfen <a href='../includes/yenikod.php' class='alert-link'>yeni bir kod isteyin</a>.";
    } else {
        $p1 = trim($_POST['p1'] ?? ""); $p2 = trim($_POST['p2'] ?? ""); $p3 = trim($_POST['p3'] ?? "");
        $p4 = trim($_POST['p4'] ?? ""); $p5 = trim($_POST['p5'] ?? ""); $p6 = trim($_POST['p6'] ?? "");
        $girilen_kod = $p1 . $p2 . $p3 . $p4 . $p5 . $p6;

        if (ctype_digit($girilen_kod) && strlen($girilen_kod) === 6) {
            if ((string)$onay_kodu_session === $girilen_kod) {
                if (function_exists('kullaniciEkle') && isset($conn)) {
                    if (kullaniciEkle($conn, $registration_data['ad'], $registration_data['soyad'], $unvan, $kurum, $arastirmaAlani, $eposta, $hashli_sifre, $avatar)) {
                        setcookie("kaydoldu_basarili", "1", time() + 60, "/");
                        
                        unset($_SESSION['onay_kodu']);
                        unset($_SESSION['onay_eposta']); 
                        unset($_SESSION['registration_data']);
                        unset($_SESSION['registration_time']);
                        
                        header("location: ../login.php");
                        exit;
                    } else {
                        $error_message = "Kullanıcı kaydı sırasında bir veritabanı hatası oluştu. Lütfen daha sonra tekrar deneyin.";
                    }
                } else {
                     $error_message = "Sistem hatası: Kayıt fonksiyonları doğru yüklenemedi.";
                     error_log("kullaniciEkle fonksiyonu veya \$conn değişkeni bulunamadı.");
                }
            } else {
                $error_message = "Girdiğiniz doğrulama kodu yanlış.";
            }
        } else {
            $error_message = "Lütfen 6 haneli sayısal bir doğrulama kodu girin.";
        }
    }
}

?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-posta Doğrulama | Scholar Mate</title>
    <link rel="shortcut icon" href="../assets/images/logo.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body data-bs-theme="light">
    <button id="themeToggleBtn" class="btn theme-toggle-button position-fixed top-0 end-0 m-3 shadow-sm" title="Temayı Değiştir">
        <i class="bi bi-moon-stars-fill"></i> 
    </button>

    <div class="container-fluid min-vh-100 d-flex align-items-center justify-content-center p-0 m-0" id="main-bg">
        <div class="auth-card">
            <div class="text-center mb-4 animate-fade-in-down">
                <i class="bi bi-shield-lock-fill auth-card-icon"></i> 
                <h2>E-posta Doğrulama</h2>
                <p class="text-muted-color">
                    Merhaba <strong><?php echo $ad_soyadi; ?></strong>, hesabınızı etkinleştirmek için <br><strong><?php echo $eposta; ?></strong> adresine gönderdiğimiz 6 haneli kodu girin.
                </p>
            </div>

            <?php if (!empty($error_message)): ?>
                <div class="alert alert-custom-danger text-center animate-fade-in-down" role="alert" style="animation-delay: 0.1s;">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo $error_message; /* HTML link için echo güvenli olmalı veya linki PHP içinde oluşturun */ ?>
                </div>
            <?php endif; ?>
            <?php if (!empty($success_message)): ?>
                <div class="alert alert-custom-success text-center animate-fade-in-down" role="alert" style="animation-delay: 0.1s;">
                    <i class="bi bi-patch-check-fill me-2"></i><?php echo $success_message; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="" id="verificationForm" novalidate>
                <div class="verification-code-inputs animate-fade-in-up" style="animation-delay: 0.2s;">
                    <input type="text" name="p1" class="form-control" maxlength="1" data-index="0" pattern="[0-9]" inputmode="numeric" required autofocus autocomplete="one-time-code" title="İlk hane">
                    <input type="text" name="p2" class="form-control" maxlength="1" data-index="1" pattern="[0-9]" inputmode="numeric" required title="İkinci hane">
                    <input type="text" name="p3" class="form-control" maxlength="1" data-index="2" pattern="[0-9]" inputmode="numeric" required title="Üçüncü hane">
                    <input type="text" name="p4" class="form-control" maxlength="1" data-index="3" pattern="[0-9]" inputmode="numeric" required title="Dördüncü hane">
                    <input type="text" name="p5" class="form-control" maxlength="1" data-index="4" pattern="[0-9]" inputmode="numeric" required title="Beşinci hane">
                    <input type="text" name="p6" class="form-control" maxlength="1" data-index="5" pattern="[0-9]" inputmode="numeric" required title="Altıncı hane">
                </div>
                
                <div class="d-grid mb-3 animate-fade-in-up" style="animation-delay: 0.3s;">
                    <button type="submit" name="onaylaBtn" class="btn btn-auth btn-lg" id="verifyButton" <?php if ($kod_suresi_doldu) echo 'disabled'; ?>>
                        <i class="bi bi-check2-circle me-2"></i>Doğrula ve Kaydı Tamamla
                    </button>
                </div>
            </form>

            <div class="text-center mb-3 animate-fade-in-up countdown-timer" style="animation-delay: 0.4s;" id="countdownSectionContainer">
            </div>

            <div class="text-center animate-fade-in-up" style="animation-delay: 0.5s;">
                <a href="../includes/yenikod.php" class="resend-code-link <?php if (!$kod_suresi_doldu) echo 'disabled'; ?>" id="resendLink">
                    <i class="bi bi-send-check-fill me-1"></i>Kodu Yeniden Gönder
                </a>
            </div>
             <hr class="my-4" style="border-color: var(--input-border);">
            <p class="text-center text-muted-color small animate-fade-in-up" style="animation-delay: 0.6s;">
                Yanlış e-posta mı girdiniz? <a href="register.php" class="auth-link">Geri dön ve düzelt</a>.
            </p>
        </div>
    </div>

    <script src="../assets/js/script.js"></script>
</body>
</html>