<?php
 session_start(); 

// // Eğer kullanıcı zaten giriş yapmışsa ana sayfaya yönlendir
// if (isset($_SESSION['login_user_id'])) {
//     header("location: index.php");
//     exit;
// }
require_once "../includes/function.php"; 
require_once "../assets/config/db.php";  

if (isset($_SESSION['id'])) {
    header("location: index.php");
    exit;
}


?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yeni Şifre Belirle | Scholar Mate</title>
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
                 <i class="bi bi-key-fill auth-card-icon"></i>
                <h2>Yeni Şifrenizi Belirleyin</h2>
                <h6>Güvenliğiniz için bu bağlantıya giriş yaparken şifremi unuttum isteği yaptığınız cihaz ve tarayıcıdan
                girdiğinize emin
                olun.</h6>
                <?php if ($is_token_valid && empty($success_message_reset)): ?>
                    <p class="text-muted-color"><strong><?php echo htmlspecialchars($eposta_from_token_lookup); ?></strong> e-posta adresi için yeni şifrenizi oluşturun.</p>
                <?php elseif(empty($token_error_message) && empty($success_message_reset)): ?>
                    <p class="text-muted-color">Lütfen şifre sıfırlama bağlantısındaki talimatları izleyin.</p>
                <?php endif; ?>
            </div>

            <?php if (!empty($token_error_message)): ?>
                <div class="alert alert-custom-danger text-center animate-fade-in-down" role="alert">
                    <i class="bi bi-exclamation-octagon-fill me-2"></i><?php echo $token_error_message; /* HTML içerebilir */ ?>
                </div>
            <?php endif; ?>
            <?php if (!empty($error_message_reset)): ?>
                <div class="alert alert-custom-danger text-center animate-fade-in-down" role="alert" style="animation-delay: 0.1s;">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo $error_message_reset; ?>
                </div>
            <?php endif; ?>
            <?php if (!empty($success_message_reset)): ?>
                <div class="alert alert-custom-success text-center animate-fade-in-down" role="alert" style="animation-delay: 0.1s;">
                    <i class="bi bi-check-circle-fill me-2"></i><?php echo $success_message_reset; /* HTML içerebilir */ ?>
                </div>
            <?php endif; ?>
            
            <?php if ($is_token_valid && empty($success_message_reset)): ?>
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'] . '?token=' . urlencode($token_from_url)); ?>" id="yeniSifreForm" novalidate>
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token_from_url); ?>">
                
                <div class="security-info-box animate-fade-in-up" style="animation-delay: 0.15s;">
                    <p class="mb-1 small text-muted-color">
                        <i class="bi bi-shield-check-fill me-1" style="color:var(--accent-color-1)"></i>
                        <strong>Güvenli şifre oluşturma ipuçları:</strong>
                    </p>
                    <ul class="list-unstyled mb-0 small text-muted-color ps-3">
                        <li><i class="bi bi-dot"></i> En az 8 karakter olmalıdır.</li>
                        <li><i class="bi bi-dot"></i> Büyük ve küçük harf içermelidir.</li>
                        <li><i class="bi bi-dot"></i> Rakam ve özel karakter (!@#$%^&*) kullanın.</li>
                    </ul>
                </div>

                <div class="form-floating mb-1 animate-fade-in-up" style="animation-delay: 0.2s;">
                    <input type="password" name="yeniSifre" class="form-control" id="yeniSifreInput" placeholder="Yeni Şifreniz" required minlength="8">
                    <label for="yeniSifreInput"><i class="bi bi-lock-fill me-2"></i>Yeni Şifreniz</label>
                    <button type="button" class="password-toggle-btn" id="toggleYeniSifre">
                        <i class="bi bi-eye-slash"></i>
                    </button>
                    <div class="invalid-feedback" id="yeniSifreFeedbackDefault">Şifreniz en az 8 karakter olmalı ve şifre gücü kriterlerini sağlamalıdır.</div>
                </div>
                <div class="password-strength-indicator mb-3 animate-fade-in-up" style="animation-delay: 0.2s;">
                    <div class="progress">
                        <div class="progress-bar" id="yeniSifreStrengthBar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <small class="form-text text-muted-color mt-1">Şifre gücü: <span id="yeniSifreStrengthText" class="fw-bold"></span></small>
                </div>

                <div class="form-floating mb-4 animate-fade-in-up" style="animation-delay: 0.3s;">
                    <input type="password" name="yeniSifreTekrar" class="form-control" id="yeniSifreTekrarInput" placeholder="Yeni Şifrenizi Onaylayın" required>
                    <label for="yeniSifreTekrarInput"><i class="bi bi-shield-lock-fill me-2"></i>Yeni Şifrenizi Onaylayın</label>
                    <button type="button" class="password-toggle-btn" id="toggleYeniSifreTekrar">
                        <i class="bi bi-eye-slash"></i>
                    </button>
                    <div class="invalid-feedback" id="yeniSifreTekrarFeedback">Lütfen yeni şifrenizi onaylayın. Şifreler eşleşmiyor.</div>
                </div>
                
                <div class="d-grid mb-4 animate-fade-in-up" style="animation-delay: 0.4s;">
                    <button type="submit" class="btn btn-auth btn-lg" name="yeniSifreBelirleBtn" id="yeniSifreBelirleBtn">
                        <i class="bi bi-key-fill me-2"></i>Yeni Şifreyi Kaydet
                    </button>
                </div>
            </form>
            <?php endif; ?>

            <?php if (!empty($success_message_reset) || !empty($token_error_message)): ?>
                 <p class="text-center mt-4 mb-0 animate-fade-in-up" style="animation-delay: 0.5s;">
                    <a href="login.php" class="auth-link"><i class="bi bi-arrow-left-circle-fill me-1"></i>Giriş Sayfasına Dön</a>
                </p>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/script.js"></script>
</body>
</html>