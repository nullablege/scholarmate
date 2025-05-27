<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
session_start();

if (isset($_SESSION['login'])) {
    header("location:index.php");
    exit; 
}


require_once "../includes/function.php"; 
require_once "../assets/config/db.php";    


$ad = "";
$soyad = "";
$kurum = "";
$arastirmaAlani = "";
$eposta = "";
$unvan = "";
$error_message = ""; 

if (isset($_SESSION['register_form_data'])) {
    $form_data = $_SESSION['register_form_data'];
    $ad = htmlspecialchars($form_data['ad'] ?? $ad);
    $soyad = htmlspecialchars($form_data['soyad'] ?? $soyad);
    $kurum = htmlspecialchars($form_data['kurum'] ?? $kurum);
    $arastirmaAlani = htmlspecialchars($form_data['arastirmaAlani'] ?? $arastirmaAlani);
    $eposta = htmlspecialchars($form_data['eposta'] ?? $eposta);
    $unvan = htmlspecialchars($form_data['unvan'] ?? $unvan);
    // unset($_SESSION['register_form_data']); // Tek kullanımlık ise temizlenebilir
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ad = htmlspecialchars(trim($_POST['ad'] ?? ""));
    $soyad = htmlspecialchars(trim($_POST['soyad'] ?? ""));
    $unvan = htmlspecialchars(trim($_POST['unvan'] ?? ""));
    $kurum = htmlspecialchars(trim($_POST['kurum'] ?? ""));
    $arastirmaAlani = htmlspecialchars(trim($_POST['arastirmaAlani'] ?? ""));
    $eposta = htmlspecialchars(trim($_POST['eposta'] ?? ""));
    $sifre = $_POST['sifre'] ?? "";
    $sifreTekrar = $_POST['sifreTekrar'] ?? "";
    $uploadDirectory = "../assets/uploads/profilPhotos/";
    $avatar = resmiKaydet("avatar","$uploadDirectory");



    if (!filter_var($eposta, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Geçersiz e-posta formatı.";
    } else {
        $hashli = validateAndHashPassword($sifre, $sifreTekrar); 

        if (!$hashli) {
            if (empty($sifre) || strlen($sifre) < 8) {
                 $error_message = "Şifre en az 8 karakter olmalıdır.";
            } else if ($sifre !== $sifreTekrar) {
                $error_message = "Şifreler eşleşmiyor.";
            } else {
                $error_message = "Şifre yeterince güçlü değil. Büyük/küçük harf, rakam ve özel karakter içermelidir.";
            }
        } elseif (getEmailRecordCount($conn, $eposta) > 0) { 
            $error_message = "Bu e-posta adresi zaten kayıtlı.";
        } else {
            $_SESSION['registration_data'] = [
                'ad' => $ad,
                'soyad' => $soyad,
                'kurum' => $kurum,
                'arastirmaAlani' => $arastirmaAlani,
                'eposta' => $eposta,
                'hashli_sifre' => $hashli,
                'unvan' => $unvan,
                'avatar' => $avatar
            ];
            print_r($_SESSION['registration_data']);
            $dogrulamaKodu = rand(100000, 999999);
            $_SESSION['onay_kodu'] = $dogrulamaKodu;
            $_SESSION['onay_eposta'] = $eposta;

            require_once "../includes/mail.php"; 
            if(function_exists('gonderEposta')){

                gonderEposta($eposta, "$ad $soyad", $dogrulamaKodu);
                $_SESSION['registration_time'] = time();
                header("location: register.php");
                exit;
            } else {
                $error_message = "Kayıt işlemi başarısız.";
            }
            

        }
    }

}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kayıt Ol | Scholar Mate</title>
    <link rel="shortcut icon" href="../assets/images/logo.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <button id="themeToggleBtn" class="btn theme-toggle-button position-fixed top-0 end-0 m-3 shadow-sm" title="Temayı Değiştir">
        <i class="bi bi-moon-stars-fill"></i> 
    </button>

    <div class="container-fluid min-vh-100 d-flex align-items-center justify-content-center p-0 m-0" id="main-bg">
        <div class="registration-card">
            <div class="card-content">
                
                <div class="avatar-section text-center animate-fade-in-down" style="animation-delay: 0s;">
                    <div class="avatar-upload mx-auto" onclick="document.getElementById('avatarInput').click()">
                        <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" 
                             class="avatar-preview img-fluid" 
                             id="avatarPreview"
                             alt="Avatar Önizleme">
                        <div class="avatar-edit-icon">
                            <i class="bi bi-camera-fill"></i>
                        </div>
                    </div>
                    
                </div>

                <h2 class="text-center mb-1 mt-3 animate-fade-in-down" style="animation-delay: 0.1s;">Hesap Oluşturun</h2>
                <p class="text-center text-muted-color mb-4 animate-fade-in-down" style="animation-delay: 0.2s;">Akademik dünyaya katılın.</p>

                <?php if (!empty($error_message)): ?>
                    <div class="alert alert-danger text-center animate-fade-in-down" role="alert" style="animation-delay: 0.25s;">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo $error_message; ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="" id="registrationForm" enctype="multipart/form-data"  novalidate>
                <input type="file" id="avatarInput" name="avatar" hidden accept="image/*">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6 animate-fade-in-up" style="animation-delay: 0.3s;">
                            <div class="form-floating">
                                <input type="text" name="ad" value="<?php echo $ad; ?>" class="form-control" id="firstName" placeholder="Adınız" required>
                                <label for="firstName"><i class="bi bi-person me-2"></i>Adınız</label>
                                <div class="invalid-feedback">Lütfen adınızı girin.</div>
                            </div>
                        </div>
                        <div class="col-md-6 animate-fade-in-up" style="animation-delay: 0.35s;">
                            <div class="form-floating">
                                <input type="text" name="soyad" value="<?php echo $soyad; ?>" class="form-control" id="lastName" placeholder="Soyadınız" required>
                                <label for="lastName"><i class="bi bi-person-vcard me-2"></i>Soyadınız</label>
                                <div class="invalid-feedback">Lütfen soyadınızı girin.</div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6 animate-fade-in-up" style="animation-delay: 0.4s;">
                            <div class="form-floating">
                                <select name="unvan" class="form-select" id="title" required>
                                    <option value="" <?php if (empty($unvan)) echo "selected";?> disabled>Ünvan Seçiniz...</option>
                                    <option <?php if ($unvan == "Prof. Dr.") echo "selected";?> value="Prof. Dr.">Prof. Dr.</option>
                                    <option <?php if ($unvan == "Doç. Dr.") echo "selected";?>  value="Doç. Dr.">Doç. Dr.</option>
                                    <option <?php if ($unvan == "Dr.") echo "selected";?> value="Dr.">Dr.</option>
                                    <option <?php if ($unvan == "Öğretim Üyesi") echo "selected";?>  value="Öğretim Üyesi">Öğretim Üyesi</option>
                                    <option <?php if ($unvan == "Araştırmacı") echo "selected";?> value="Araştırmacı">Araştırmacı</option>
                                    <option <?php if ($unvan == "Doktora Öğrencisi") echo "selected";?> value="Doktora Öğrencisi">Doktora Öğrencisi</option>
                                    <option <?php if ($unvan == "Yüksek Lisans Öğrencisi") echo "selected";?>  value="Yüksek Lisans Öğrencisi">Yüksek Lisans Öğrencisi</option>
                                    <option <?php if ($unvan == "Lisans Öğrencisi") echo "selected";?> value="Lisans Öğrencisi">Lisans Öğrencisi</option>
                                </select>
                                <label for="title"><i class="bi bi-mortarboard me-2"></i>Ünvanınız</label>
                                <div class="invalid-feedback">Lütfen ünvanınızı seçin.</div>
                            </div>
                        </div>
                        <div class="col-md-6 animate-fade-in-up" style="animation-delay: 0.45s;">
                             <div class="form-floating">
                                <input type="text" value="<?php echo $kurum; ?>" name="kurum" class="form-control" id="institution" placeholder="Kurumunuz" required>
                                <label for="institution"><i class="bi bi-building me-2"></i>Kurumunuz</label>
                                <div class="invalid-feedback">Lütfen kurumunuzu girin.</div>
                            </div>
                        </div>
                    </div>

                    <div class="form-floating mb-3 animate-fade-in-up" style="animation-delay: 0.5s;">
                        <select class="form-select" name="arastirmaAlani" id="researchArea" required>
                            <option value="" <?php if (empty($arastirmaAlani)) echo "selected";?> disabled>Araştırma Alanı Seçiniz...</option>
                            <option <?php if ($arastirmaAlani == "Bilgisayar Bilimleri") echo "selected";?> value="Bilgisayar Bilimleri">Bilgisayar Bilimleri</option>
                            <option <?php if ($arastirmaAlani == "Mühendislik") echo "selected";?> value="Mühendislik">Mühendislik</option>
                            <option <?php if ($arastirmaAlani == "Tıp") echo "selected";?> value="Tıp">Tıp</option>
                            <option <?php if ($arastirmaAlani == "Sosyal Bilimler") echo "selected";?> value="Sosyal Bilimler">Sosyal Bilimler</option>
                            <option <?php if ($arastirmaAlani == "Fen Bilimleri") echo "selected";?> value="Fen Bilimleri">Fen Bilimleri</option>
                        </select>
                        <label for="researchArea"><i class="bi bi-lightbulb me-2"></i>Araştırma Alanınız</label>
                        <div class="invalid-feedback">Lütfen araştırma alanınızı seçin.</div>
                    </div>

                    <div class="form-floating mb-3 animate-fade-in-up" style="animation-delay: 0.55s;">
                        <input type="email" value="<?php echo $eposta; ?>" name="eposta" class="form-control" id="email" placeholder="E-posta Adresiniz" required>
                        <label for="email"><i class="bi bi-envelope-fill me-2"></i>E-posta Adresiniz</label>
                        <div class="invalid-feedback">Lütfen geçerli bir e-posta adresi girin.</div>
                    </div>

                    <div class="form-floating mb-1 animate-fade-in-up" style="animation-delay: 0.6s;">
                        <input type="password" name="sifre" class="form-control" id="password" placeholder="Şifreniz" required minlength="8">
                        <label for="password"><i class="bi bi-lock-fill me-2"></i>Şifreniz</label>
                        <button type="button" class="password-toggle-btn" id="togglePassword">
                            <i class="bi bi-eye-slash"></i>
                        </button>
                        <div class="invalid-feedback" id="passwordFeedback">Şifreniz en az 8 karakter olmalı, büyük/küçük harf, rakam ve özel karakter içermelidir.</div>
                    </div>
                    <div class="password-strength-indicator mb-3 animate-fade-in-up" style="animation-delay: 0.6s;">
                        <div class="progress" style="height: 6px; border-radius:3px;">
                            <div class="progress-bar" id="strengthBar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <small class="form-text text-muted-color mt-1">Şifre gücü: <span id="strengthText" class="fw-bold"></span></small>
                    </div>

                    <div class="form-floating mb-4 animate-fade-in-up" style="animation-delay: 0.65s;">
                        <input type="password" name="sifreTekrar" class="form-control" id="confirmPassword" placeholder="Şifrenizi Onaylayın" required>
                        <label for="confirmPassword"><i class="bi bi-shield-lock-fill me-2"></i>Şifrenizi Onaylayın</label>
                        <button type="button" class="password-toggle-btn" id="toggleConfirmPassword">
                            <i class="bi bi-eye-slash"></i>
                        </button>
                        <div class="invalid-feedback" id="confirmPasswordFeedback">Lütfen şifrenizi onaylayın. Şifreler eşleşmiyor.</div>
                    </div>
                    
                    <div class="d-grid mb-4 animate-fade-in-up" style="animation-delay: 0.7s;">
                        <button type="submit" class="btn btn-register btn-lg" name="registerBtn" id="submitBtn">
                            <i class="bi bi-person-plus-fill me-2"></i>Kayıt Ol
                        </button>
                    </div>

                    <p class="text-center mt-4 animate-fade-in-up" style="animation-delay: 0.75s;">
                        Zaten bir hesabınız var mı? <a href="login.php" class="login-link">Giriş Yapın</a>
                    </p>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/script.js"></script>
</body>
</html>