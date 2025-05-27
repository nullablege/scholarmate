<?php
 session_start(); // session_start() her zaman en üste taşınmalı

// // Eğer kullanıcı zaten giriş yapmışsa ana sayfaya yönlendir
// if (isset($_SESSION['login_user_id'])) { // Daha spesifik bir session anahtarı kullanmak iyi bir pratik
//     header("location:index.php"); // Veya dashboard.php gibi bir ana panel sayfası
//     exit;
// }

if(isset($_SESSION['id'])){
    header("location:index.php");
}

require "assets/config/db.php";
require "includes/function.php";

$login_error_message = ""; 

if (isset($_COOKIE['kaydoldu_basarili'])) {
    $login_success_message = "Kayıt işleminiz başarılı, lütfen giriş yapınız.";
}

if (isset($_SESSION['onay_kodu']) && isset($_SESSION['registration_time'])) { 
    if ($_SESSION['registration_time'] + (5 * 60) < time()) {
        unset($_SESSION['onay_kodu']);
        unset($_SESSION['onay_eposta']);
        unset($_SESSION['registration_data']);
        unset($_SESSION['registration_time']);
        $login_error_message = "E-posta onay süreniz dolmuştur. Lütfen tekrar kayıt olmayı deneyin.";
    } else {
        if (!isset($_COOKIE['uyari_register2_yonlendirme'])) { 
            setcookie("uyari_register2_yonlendirme", "1", time() + 30, "/"); 
            header("location:register2.php");
            exit;
        }
    }
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $eposta = htmlspecialchars(trim($_POST['eposta'])) ?? "" ;
    $sifre = htmlspecialchars(trim($_POST['sifre'])) ?? "" ;
    if(kullaniciDogruMu($conn,$eposta,$sifre)){
        header("location:index.php");
    }
    else{
        $login_error_message = "Giriş bilgileri hatalı.";
    }
}

?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giriş Yap | Scholar Mate</title>
    <link rel="shortcut icon" href="assets/images/logo.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <button id="themeToggleBtn" class="btn theme-toggle-button position-fixed top-0 end-0 m-3 shadow-sm" title="Temayı Değiştir">
        <i class="bi bi-moon-stars-fill"></i> 
    </button>

    <div class="container-fluid min-vh-100 d-flex align-items-center justify-content-center p-0 m-0" id="main-bg">
        <div class="auth-card">
            <div class="text-center mb-4 animate-fade-in-down">
                <img src="assets/images/logo.png" alt="Logo" class="auth-card-logo">
                <h2>Scholar Mate Giriş</h2>
                <p class="text-muted-color">Hesabınıza erişin ve çalışmalarınıza devam edin.</p>
            </div>

            <?php if (!empty($login_error_message)): ?>
                <div class="alert alert-custom-danger text-center animate-fade-in-down" role="alert" style="animation-delay: 0.1s;">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo $login_error_message; ?>
                </div>
            <?php endif; ?>
            <?php if (!empty($login_success_message)): ?>
                <div class="alert alert-custom-success text-center animate-fade-in-down" role="alert" style="animation-delay: 0.1s;">
                    <i class="bi bi-check-circle-fill me-2"></i><?php echo $login_success_message; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="" id="loginForm" novalidate>
                <div class="form-floating mb-3 animate-fade-in-up" style="animation-delay: 0.2s;">
                    <input type="email" name="eposta" class="form-control" id="email" placeholder="E-posta Adresiniz" required value="<?php echo htmlspecialchars($_POST['eposta'] ?? ''); ?>">
                    <label for="email"><i class="bi bi-envelope-fill me-2"></i>E-posta Adresiniz</label>
                    <div class="invalid-feedback">Lütfen geçerli bir e-posta adresi girin.</div>
                </div>

                <div class="form-floating mb-3 animate-fade-in-up" style="animation-delay: 0.3s;">
                    <input type="password" name="sifre" class="form-control" id="password" placeholder="Şifreniz" required>
                    <label for="password"><i class="bi bi-lock-fill me-2"></i>Şifreniz</label>
                    <button type="button" class="password-toggle-btn" id="togglePassword">
                        <i class="bi bi-eye-slash"></i>
                    </button>
                    <div class="invalid-feedback">Lütfen şifrenizi girin.</div>
                </div>
                
                <div class="d-flex justify-content-between align-items-center mb-4 animate-fade-in-up" style="animation-delay: 0.4s;">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="" id="rememberMe" name="rememberMe">
                        <label class="form-check-label text-muted-color small" for="rememberMe">
                            Beni Hatırla
                        </label>
                    </div>
                    <a href="sifremiUnuttum.php" class="auth-link small">Şifremi Unuttum?</a>
                </div>

                <div class="d-grid mb-3 animate-fade-in-up" style="animation-delay: 0.5s;">
                    <button type="submit" class="btn btn-auth btn-lg" name="loginBtn" id="loginBtn">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Giriş Yap
                    </button>
                </div>
                
                <div class="divider animate-fade-in-up" style="animation-delay: 0.6s;">VEYA</div>

                <div class="text-center mb-3 animate-fade-in-up" style="animation-delay: 0.65s;">
                    <p class="text-muted-color small mb-2">Sosyal medya ile giriş yapın</p>
                    <button type="button" class="btn social-login-btn" title="Google ile Giriş Yap">
                        <img src="https://cdn-icons-png.flaticon.com/512/281/281764.png" alt="Google">
                    </button>
                    <button type="button" class="btn social-login-btn" title="GitHub ile Giriş Yap">
                        <img src="https://cdn-icons-png.flaticon.com/512/25/25231.png" alt="GitHub"> 
                    </button>
                     <button type="button" class="btn social-login-btn" title="ORCID ile Giriş Yap">
                        <img src="https://orcid.org/sites/default/files/images/orcid_16x16.png" alt="ORCID">
                    </button>
                </div>

                <p class="text-center mt-4 mb-0 animate-fade-in-up" style="animation-delay: 0.7s;">
                    Hesabınız yok mu? <a href="/register" class="auth-link">Hemen Kayıt Olun</a>
                </p>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/script.js"></script>
</body>
</html>