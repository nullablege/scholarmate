<?php
session_start();
require_once "../includes/function.php"; 
require_once "../assets/config/db.php";  
require_once "../includes/mail.php";

if (isset($_SESSION['id'])) {
    header("location: index.php");
    exit;
}

$error_message_sifre_unuttum = "";
$success_message_sifre_unuttum = "";
$isim = $_SESSION['ad'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['sifreSifirlaIstekBtn'])) {
    $eposta_sifre_sifirlama = trim($_POST['eposta_sifre_sifirlama'] ?? "");

    if (empty($eposta_sifre_sifirlama)) {
        $error_message_sifre_unuttum = "E-posta adresi boş bırakılamaz.";
    } elseif (!filter_var($eposta_sifre_sifirlama, FILTER_VALIDATE_EMAIL)) {
        $error_message_sifre_unuttum = "Lütfen geçerli bir e-posta adresi girin.";
    } else {
        $eposta = htmlspecialchars(trim($_POST['eposta_sifre_sifirlama']));
        if(getEmailRecordCount($conn,$eposta) == 1){
            $_SESSION['sifreYenile'] = $eposta;
            $_SESSION['yenileZaman'] = time();
            gonderEpostaSifreUnuttum($eposta,$isim);
            header("location:sifremiUnuttum.php");
        }


        
      
        

    }
}

?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Şifremi Unuttum | Scholar Mate</title>
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
                 <i class="bi bi-patch-question-fill auth-card-icon"></i>
                <h2>Şifrenizi mi Unuttunuz?</h2>
                <p class="text-muted-color">Endişelenmeyin! Şifre sıfırlama bağlantısı almak için kayıtlı e-posta adresinizi girin.</p>
            </div>

            <?php if (!empty($error_message_sifre_unuttum)): ?>
                <div class="alert alert-custom-danger text-center animate-fade-in-down" role="alert" style="animation-delay: 0.1s;">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo $error_message_sifre_unuttum; ?>
                </div>
            <?php endif; ?>
            <?php if (!empty($success_message_sifre_unuttum)): ?>
                <div class="alert alert-custom-success text-center animate-fade-in-down" role="alert" style="animation-delay: 0.1s;">
                    <i class="bi bi-check-circle-fill me-2"></i><?php echo $success_message_sifre_unuttum; ?>
                </div>
            <?php endif; ?>
            
            <?php if (empty($success_message_sifre_unuttum)):?>
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" id="sifreSifirlamaIstekForm" novalidate>
                <div class="security-info-box animate-fade-in-up" style="animation-delay: 0.2s;">
                    <p class="mb-0 small text-muted-color">
                        <i class="bi bi-info-circle-fill me-1 text-primary"></i>
                        Sisteme kayıtlı e-posta adresinizi girdiğinizden emin olun. Şifre sıfırlama talimatları e-posta adresinize gönderilecektir.
                    </p>
                </div>

                <div class="form-floating mb-4 animate-fade-in-up" style="animation-delay: 0.3s;">
                    <input type="email" name="eposta_sifre_sifirlama" class="form-control" id="epostaInputSifreSifirlama" placeholder="E-posta Adresiniz" required value="<?php echo htmlspecialchars($_POST['eposta_sifre_sifirlama'] ?? ''); ?>">
                    <label for="epostaInputSifreSifirlama"><i class="bi bi-envelope-fill me-2"></i>Kayıtlı E-posta Adresiniz</label>
                    <div class="invalid-feedback">Lütfen geçerli bir e-posta adresi girin.</div>
                </div>

                <div class="d-grid mb-4 animate-fade-in-up" style="animation-delay: 0.4s;">
                    <button type="submit" class="btn btn-auth btn-lg" name="sifreSifirlaIstekBtn" id="sifreSifirlaIstekBtn">
                         <i class="bi bi-send-fill me-2"></i>Şifre Sıfırlama Bağlantısı Gönder
                    </button>
                </div>
            </form>
            <?php endif; ?>

            <p class="text-center mt-4 mb-0 animate-fade-in-up" style="animation-delay: 0.5s;">
                <a href="login.php" class="auth-link"><i class="bi bi-arrow-left-circle-fill me-1"></i>Giriş Sayfasına Dön</a>
            </p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="script.js"></script>
</body>
</html>