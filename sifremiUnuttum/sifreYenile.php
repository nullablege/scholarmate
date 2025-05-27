<?php
session_start();

require_once "../includes/function.php"; 
require_once "../assets/config/db.php";  

$sayfa_basligi = "Yeni Şifre Belirle | Scholar Mate";
$kullanici_eposta = ""; 
$is_request_valid = false; 
$sifre_sifirlama_suresi = 5 * 60; 

$error_message = "";
$success_message = "";

if (!isset($_SESSION['sifreYenile']) || empty($_SESSION['sifreYenile'])) {
    $error_message = "Geçersiz şifre sıfırlama isteği. Lütfen şifre sıfırlama işlemini <a href='SifremiUnuttum.php' class='alert-link'>baştan başlatın</a>.";
} elseif (!isset($_SESSION['yenileZaman'])) {
    $error_message = "Şifre sıfırlama isteğinin zaman bilgisi bulunamadı. Lütfen işlemi <a href='SifremiUnuttum.php' class='alert-link'>baştan başlatın</a>.";
} elseif (time() > ($_SESSION['yenileZaman'] + $sifre_sifirlama_suresi)) {
    $error_message = "Şifre sıfırlama bağlantınızın/kodunuzun süresi dolmuş. Lütfen <a href='SifremiUnuttum.php' class='alert-link'>yeni bir istekte</a> bulunun.";
    unset($_SESSION['sifreYenile']);
    unset($_SESSION['yenileZaman']);
} else {
    $is_request_valid = true;
    $kullanici_eposta = htmlspecialchars($_SESSION['sifreYenile']);
}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && $is_request_valid) {
    $yeni_sifre = $_POST['yeniSifre'] ?? "";
    $yeni_sifre_tekrar = $_POST['yeniSifreTekrar'] ?? "";

    if (function_exists('validateAndHashPassword')) {
        $hashlenmis_yeni_sifre = validateAndHashPassword($yeni_sifre, $yeni_sifre_tekrar);

        if ($hashlenmis_yeni_sifre === false) {
             if (empty($yeni_sifre) || empty($yeni_sifre_tekrar)){
                 $error_message = "Yeni şifre ve tekrarı alanları boş bırakılamaz.";
             } elseif ($yeni_sifre !== $yeni_sifre_tekrar){
                 $error_message = "Girdiğiniz yeni şifreler eşleşmiyor.";
             } elseif (strlen($yeni_sifre) < 8) {
                  $error_message = "Yeni şifre en az 8 karakter olmalıdır.";
             }
              else {
                  $error_message = "Girdiğiniz şifre güvenlik kriterlerini karşılamıyor. Lütfen ipuçlarına dikkat edin.";
              }
        } else {
            if (function_exists('kullaniciSifreGuncelle') && isset($conn)) {
                if (kullaniciSifreGuncelle($conn, $kullanici_eposta, $hashlenmis_yeni_sifre)) {
                    $success_message = "Şifreniz başarıyla güncellendi! Artık yeni şifrenizle <a href='../login.php' class='alert-link'>giriş yapabilirsiniz</a>.";
                    unset($_SESSION['sifreYenile']);
                    unset($_SESSION['yenileZaman']);
                    $is_request_valid = false; 
                } else {
                    $error_message = "Şifreniz güncellenirken bir veritabanı hatası oluştu. Lütfen tekrar deneyin.";
                }
            } else {
                $error_message = "Sistem hatası: Şifre güncelleme fonksiyonu yüklenemedi.";
                error_log("kullaniciSifreGuncelle fonksiyonu veya \$conn değişkeni bulunamadı.");
            }
        }
    } else {
        $error_message = "Sistem hatası: Şifre doğrulama fonksiyonu bulunamadı.";
        error_log("validateAndHashPassword fonksiyonu bulunamadı.");
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $sayfa_basligi; ?></title>
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
                <?php if ($is_request_valid && empty($success_message)): ?>
                    <p class="text-muted-color">
                        <strong><?php echo $kullanici_eposta; ?></strong> e-posta adresi için yeni şifrenizi oluşturun.
                    </p>
                <?php elseif(empty($error_message) && empty($success_message)): ?>
                     <p class="text-muted-color">Lütfen şifre sıfırlama adımlarını takip edin.</p>
                <?php endif; ?>
            </div>

            <?php if (!empty($error_message)): ?>
                <div class="alert alert-custom-danger text-center animate-fade-in-down" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo $error_message;  ?>
                </div>
            <?php endif; ?>
            <?php if (!empty($success_message)): ?>
                <div class="alert alert-custom-success text-center animate-fade-in-down" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i><?php echo $success_message;  ?>
                </div>
            <?php endif; ?>
            
            <?php if ($is_request_valid && empty($success_message)): ?>
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" id="yeniSifreForm" novalidate>
                
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
                    <button type="submit" class="btn btn-auth btn-lg" name="yeniSifreKaydetBtn" id="yeniSifreKaydetBtn">
                        <i class="bi bi-key-fill me-2"></i>Yeni Şifreyi Kaydet
                    </button>
                </div>
            </form>
            <?php endif; ?>

            <?php if (empty($success_message) && !$is_request_valid && !empty($error_message)): ?>
                <p class="text-center mt-4 mb-0 animate-fade-in-up" style="animation-delay: 0.5s;">
                    <a href="SifremiUnuttum.php" class="auth-link"><i class="bi bi-arrow-left-circle-fill me-1"></i>Şifre Sıfırlama Adımına Dön</a>
                </p>
            <?php elseif(!empty($success_message)): ?>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/script.js"></script> 
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            
            function setupPasswordToggle(inputId, toggleButtonId) {
                const passwordInput = document.getElementById(inputId);
                const toggleButton = document.getElementById(toggleButtonId);
                if (!passwordInput || !toggleButton) return;
                const icon = toggleButton.querySelector('i');
                toggleButton.addEventListener('click', function () {
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);
                    icon.classList.toggle('bi-eye-slash'); icon.classList.toggle('bi-eye');
                });
            }

            function calculatePasswordStrength(password) {
                let strength = 0;
                if (password.length >= 8) strength++;
                if (/[a-z]/.test(password)) strength++;
                if (/[A-Z]/.test(password)) strength++;
                if (/\d/.test(password)) strength++;
                if (/[^A-Za-z0-9]/.test(password)) strength++;
                const levels = [
                    { text: 'Çok Zayıf', colorClass: 'bg-danger', minScore: 0 },
                    { text: 'Zayıf', colorClass: 'bg-danger', minScore: 2 },
                    { text: 'Orta', colorClass: 'bg-warning', minScore: 3 },
                    { text: 'Güçlü', colorClass: 'bg-success', minScore: 4 },
                    { text: 'Çok Güçlü', colorClass: 'bg-success', minScore: 5 }
                ];
                const current = levels.slice().reverse().find(l => strength >= l.minScore) || levels[0];
                return { text: current.text, colorClass: current.colorClass, percentage: Math.min((strength / 5) * 100, 100), score: strength };
            }
            
            setupPasswordToggle('yeniSifreInput', 'toggleYeniSifre');
            setupPasswordToggle('yeniSifreTekrarInput', 'toggleYeniSifreTekrar');

            const yeniSifreInput = document.getElementById('yeniSifreInput');
            const yeniSifreStrengthBar = document.getElementById('yeniSifreStrengthBar');
            const yeniSifreStrengthText = document.getElementById('yeniSifreStrengthText');
            const yeniSifreFeedbackDefault = document.getElementById('yeniSifreFeedbackDefault');

            if (yeniSifreInput && yeniSifreStrengthBar && yeniSifreStrengthText) {
                yeniSifreInput.addEventListener('input', function() {
                    const details = calculatePasswordStrength(this.value);
                    yeniSifreStrengthBar.style.width = details.percentage + '%';
                    yeniSifreStrengthBar.className = 'progress-bar ' + details.colorClass;
                    yeniSifreStrengthText.textContent = details.text;
                    const feedbackEl = yeniSifreFeedbackDefault;
                    if (this.value.length > 0 && details.score < 3) { 
                        this.setCustomValidity("Zayıf şifre."); 
                        if(feedbackEl) {
                            feedbackEl.textContent="Şifre zayıf. Büyük/küçük harf, rakam, özel karakter ve en az 8 karakter kullanın.";
                            feedbackEl.style.color = "var(--danger-text)";
                        }
                    } else if (this.value.length > 0 && this.value.length < 8) { 
                         this.setCustomValidity("Kısa şifre."); 
                         if(feedbackEl) {
                            feedbackEl.textContent="Şifre en az 8 karakter olmalıdır.";
                            feedbackEl.style.color = "var(--danger-text)";
                         }
                    } else { 
                        this.setCustomValidity(""); 
                        if(feedbackEl) {
                            feedbackEl.textContent="Şifreniz en az 8 karakter olmalı ve şifre gücü kriterlerini sağlamalıdır.";
                             feedbackEl.style.color = ""; 
                        }
                    }
                    validatePasswordMatch();
                });
            }

            const form = document.getElementById('yeniSifreForm');
            const yeniSifreTekrarInput = document.getElementById('yeniSifreTekrarInput');
            const yeniSifreTekrarFeedback = document.getElementById('yeniSifreTekrarFeedback');
            const submitButton = document.getElementById('yeniSifreKaydetBtn');

            function validatePasswordMatch() {
                if (!yeniSifreInput || !yeniSifreTekrarInput) return;
                const feedbackEl = yeniSifreTekrarFeedback;
                if (yeniSifreTekrarInput.value === "" && yeniSifreInput.value !== "") { 
                    yeniSifreTekrarInput.setCustomValidity("Onay şifresi boş olamaz."); 
                    if(feedbackEl) {
                        feedbackEl.textContent="Lütfen şifrenizi onaylayın.";
                        feedbackEl.style.color = "var(--danger-text)";
                    }
                } else if (yeniSifreInput.value !== yeniSifreTekrarInput.value && yeniSifreTekrarInput.value !== "") { 
                    yeniSifreTekrarInput.setCustomValidity('Şifreler eşleşmiyor.'); 
                    if(feedbackEl) {
                        feedbackEl.textContent='Yeni şifreler eşleşmiyor.';
                        feedbackEl.style.color = "var(--danger-text)";
                    }
                } else { 
                    yeniSifreTekrarInput.setCustomValidity(''); 
                    if(feedbackEl && yeniSifreTekrarInput.value !== "") {
                        feedbackEl.textContent="Şifreler eşleşiyor!";
                        feedbackEl.style.color = "var(--success-text)";
                    } else if(feedbackEl) {
                        feedbackEl.textContent="Lütfen yeni şifrenizi onaylayın. Şifreler eşleşmiyor.";
                        feedbackEl.style.color = "";
                    }
                }
            }
            if (yeniSifreTekrarInput) yeniSifreTekrarInput.addEventListener('input', validatePasswordMatch);
            if (yeniSifreInput) yeniSifreInput.addEventListener('input', validatePasswordMatch);

            if (form) {
                form.addEventListener('submit', function (event) {
                    if (yeniSifreInput.value.length > 0 && calculatePasswordStrength(yeniSifreInput.value).score < 3) {
                       yeniSifreInput.setCustomValidity("Zayıf şifre");
                    } else if (yeniSifreInput.value.length > 0 && yeniSifreInput.value.length < 8) {
                        yeniSifreInput.setCustomValidity("Kısa şifre");
                    }
                     else {
                       yeniSifreInput.setCustomValidity("");
                    }
                    validatePasswordMatch();

                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    } else {
                        if(submitButton){
                            submitButton.disabled = true;
                            submitButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Kaydediliyor...';
                        }
                    }
                    form.classList.add('was-validated');
                }, false);
            }

            <?php if (!empty($success_message_reset) || (!$is_request_valid && !empty($error_message))): ?>
            if (form) {
                form.style.display = 'none';
                const securityBox = document.querySelector('.security-info-box');
                if(securityBox) securityBox.style.display = 'none';
            }
            <?php endif; ?>
        });
    </script>
</body>
</html>