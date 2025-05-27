// script.js

function isValidEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

document.addEventListener('DOMContentLoaded', function () {

    const kutuphanemLink = document.querySelector('body > nav > div > div > ul > li:nth-child(2) > a');

    if (kutuphanemLink) {
        kutuphanemLink.addEventListener('click', function (event) {
            // Opsiyonel: Eğer href="#" ise ve sayfanın başına gitmesini engellemek istiyorsanız


            // Yönlendirme işlemi
            window.location.href = 'kutuphanem.php';
        });
    }

    const cikisyap = document.querySelector('body > nav > div > div > div > ul > li:nth-child(3) > a');

    if (cikisyap) {
        cikisyap.addEventListener('click', function (event) {
            // Opsiyonel: Eğer href="#" ise ve sayfanın başına gitmesini engellemek istiyorsanız


            // Yönlendirme işlemi
            window.location.href = 'logout.php';
        });
    }

    // --- GLOBAL SETTINGS (PHP should populate these) ---
    // Example: In your PHP template, before including script.js:
    // <script>
    //   window.userSettings = {
    //     themePreference: "<?php echo $kullanici_verisi['tema_tercihi'] ?? 'system'; ?>", // 'light', 'dark', or 'system'
    //     // Add other user-specific settings here if needed
    //   };
    //   window.pageData = { // For page-specific data like countdowns
    //     registrationTime: <?php echo $registration_time ?? 0; ?>,
    //     codeValidityDuration: <?php echo $kod_gecerlilik_suresi ?? 0; ?>,
    //     serverTimeAtLoad: <?php echo time() ?? 0; ?>,
    //     // Flags for conditional logic based on PHP messages
    //     successMessageSifreUnuttum: <?php echo !empty($success_message_sifre_unuttum) ? 'true' : 'false'; ?>,
    //     successMessageReset: <?php echo !empty($success_message_reset) ? 'true' : 'false'; ?>,
    //     tokenErrorMessage: <?php echo !empty($token_error_message) ? 'true' : 'false'; ?>
    //   };
    // </script>
    const userSettings = window.userSettings || { themePreference: 'system' };
    const pageData = window.pageData || {};

    // --- THEME MANAGEMENT ---
    const themeToggleBtn = document.getElementById('themeToggleBtn') || document.getElementById('themeToggleBtnGlobal');
    const htmlElement = document.documentElement;
    const bodyElement = document.body;
    const profileThemeSelect = document.getElementById('temaTercihiSelect');
    const prefersDarkScheme = window.matchMedia('(prefers-color-scheme: dark)');

    function applyTheme(theme, storePreference = true, updateProfileSelect = true) {
        let actualTheme = theme;
        if (theme === 'system') {
            actualTheme = prefersDarkScheme.matches ? 'dark' : 'light';
        }

        htmlElement.setAttribute('data-theme', actualTheme);
        if (bodyElement) bodyElement.setAttribute('data-bs-theme', actualTheme); // For Bootstrap 5.3+

        if (themeToggleBtn) {
            const icon = themeToggleBtn.querySelector('i');
            if (icon) {
                if (actualTheme === 'dark') {
                    icon.classList.remove('bi-moon-stars-fill');
                    icon.classList.add('bi-sun-fill');
                } else {
                    icon.classList.remove('bi-sun-fill');
                    icon.classList.add('bi-moon-stars-fill');
                }
            }
        }

        if (profileThemeSelect && updateProfileSelect) {
            profileThemeSelect.value = theme; // Set to 'light', 'dark', or 'system'
        }

        if (storePreference) {
            if (theme === 'system') {
                localStorage.removeItem('theme');
            } else {
                localStorage.setItem('theme', theme);
            }
        }
        // Optional: Cookie for PHP to read the resolved theme (especially if 'system' is chosen)
        // document.cookie = `actual_theme=${actualTheme};path=/;max-age=2592000;SameSite=Lax`; // 30 days
    }

    function initializeTheme() {
        let currentTheme = localStorage.getItem('theme');

        if (!currentTheme) { // No localStorage preference
            currentTheme = userSettings.themePreference || 'system'; // Fallback to DB preference or system
            if (currentTheme === 'system') {
                // If DB also says 'system' or no DB preference, check time for non-OS preference systems
                // This hour-based logic is less common if OS preference is well-supported
                // const currentHour = new Date().getHours();
                // if (currentHour >= 19 || currentHour < 6) {
                //     currentTheme = 'dark'; // This would be a direct setting, not 'system'
                // } else {
                //     currentTheme = 'light'; // This would be a direct setting
                // }
                // For now, 'system' from DB directly applies OS preference
            }
            applyTheme(currentTheme, false); // Apply without storing in localStorage yet (unless it's 'light' or 'dark' from DB)
        } else {
            applyTheme(currentTheme, false); // Apply localStorage preference
        }
        // Ensure profile select matches on load
        if (profileThemeSelect) profileThemeSelect.value = currentTheme;
    }

    initializeTheme();

    if (themeToggleBtn) {
        themeToggleBtn.addEventListener('click', () => {
            const currentAppliedTheme = htmlElement.getAttribute('data-theme');
            const newTheme = currentAppliedTheme === 'dark' ? 'light' : 'dark';
            // When user clicks toggle, it's a direct choice, not 'system'
            applyTheme(newTheme, true, true);
        });
    }

    if (profileThemeSelect) {
        profileThemeSelect.addEventListener('change', function () {
            applyTheme(this.value, true, false); // Store this preference, don't re-update this select
            // This change will be saved to DB when the profile form is submitted
        });
    }

    prefersDarkScheme.addEventListener('change', () => {
        const storedTheme = localStorage.getItem('theme');
        const dbTheme = userSettings.themePreference;
        // Only update if user has chosen 'system' in localStorage or has no localStorage and DB preference is 'system' or not set
        if (storedTheme === 'system' || (!storedTheme && (dbTheme === 'system' || !dbTheme))) {
            applyTheme('system', false, true);
        }
    });

    // --- PASSWORD VISIBILITY TOGGLE ---
    function setupPasswordToggle(inputId, toggleButtonId) {
        const passwordInput = document.getElementById(inputId);
        const toggleButton = document.getElementById(toggleButtonId);
        if (!passwordInput || !toggleButton) return;

        const icon = toggleButton.querySelector('i');
        if (!icon) return;

        toggleButton.addEventListener('click', function () {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            icon.classList.toggle('bi-eye-slash');
            icon.classList.toggle('bi-eye');
        });
    }
    // Call for all known password fields
    setupPasswordToggle('password', 'togglePassword'); // Login, Register
    setupPasswordToggle('confirmPassword', 'toggleConfirmPassword'); // Register
    setupPasswordToggle('yeniSifreInput', 'toggleYeniSifre'); // Profile, New Password Reset
    setupPasswordToggle('yeniSifreTekrarInput', 'toggleYeniSifreTekrar'); // Profile, New Password Reset

    // --- PASSWORD STRENGTH CALCULATION ---
    function calculatePasswordStrength(password) {
        let strength = 0;
        const feedback = [];
        const criteria = {
            length: password.length >= 8,
            lowercase: /[a-z]/.test(password),
            uppercase: /[A-Z]/.test(password),
            digit: /\d/.test(password),
            special: /[^A-Za-z0-9]/.test(password)
        };

        if (criteria.length) strength++; else feedback.push("En az 8 karakter.");
        if (criteria.lowercase) strength++; else feedback.push("Küçük harf.");
        if (criteria.uppercase) strength++; else feedback.push("Büyük harf.");
        if (criteria.digit) strength++; else feedback.push("Rakam.");
        if (criteria.special) strength++; else feedback.push("Özel karakter.");

        const levels = [
            { text: 'Çok Zayıf', colorClass: 'bg-danger', minScore: 0 },
            { text: 'Zayıf', colorClass: 'bg-danger', minScore: 2 }, // Adjusted for typical expectations
            { text: 'Orta', colorClass: 'bg-warning', minScore: 3 },
            { text: 'Güçlü', colorClass: 'bg-success', minScore: 4 },
            { text: 'Çok Güçlü', colorClass: 'bg-success', minScore: 5 }
        ];
        const currentLevel = levels.slice().reverse().find(l => strength >= l.minScore) || levels[0];
        const percentage = Math.min((strength / 5) * 100, 100);
        const feedbackMessage = feedback.length > 0 && strength < 5 ? "Eksikler: " + feedback.join(' ') : (strength === 5 ? "Mükemmel!" : "");

        return {
            score: strength,
            text: currentLevel.text,
            colorClass: currentLevel.colorClass,
            percentage: percentage,
            feedback: feedbackMessage,
            isValid: strength >= 3 // Example: "Orta" is considered minimum valid
        };
    }

    // --- FORM VALIDATION & SUBMISSION ---
    function disableSubmitButton(button, loadingText = 'İşleniyor...') {
        if (button) {
            button.disabled = true;
            button.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>${loadingText}`;
        }
    }

    function enableSubmitButton(button, originalText) {
        if (button) {
            button.disabled = false;
            button.innerHTML = originalText;
        }
    }

    // Generic Form Handler (Bootstrap Validation)
    const formsToValidate = document.querySelectorAll('form.needs-validation, form#loginForm, form#registrationForm, form#makaleYukleForm, form#profilFormKapsayici, form#sifreDegistirForm, form#sifreSifirlamaIstekForm, form#yeniSifreForm, form#verificationForm');
    formsToValidate.forEach(form => {
        const submitButton = form.querySelector('button[type="submit"]');
        const originalButtonText = submitButton ? submitButton.innerHTML : '';

        form.addEventListener('submit', function (event) {
            // Run page-specific pre-submit validation if any
            if (form.id === 'registrationForm') {
                validateRegistrationPasswordsMatch();
            }
            if (form.id === 'sifreDegistirForm' || form.id === 'yeniSifreForm') {
                validateNewPasswordsMatch(
                    form.id === 'sifreDegistirForm' ? 'yeniSifreInput' : 'yeniSifreInput', // Could be different IDs per form
                    form.id === 'sifreDegistirForm' ? 'yeniSifreTekrarInput' : 'yeniSifreTekrarInput',
                    form.id === 'yeniSifreForm' ? 'yeniSifreTekrarFeedback' : null // Specific feedback for new password page
                );
            }
            if (form.id === 'sifreSifirlamaIstekForm') {
                validatePasswordResetEmail();
            }
            if (form.id === 'verificationForm') {
                if (pageData.codeValidityDuration && pageData.registrationTime && (Math.floor(Date.now() / 1000) - pageData.registrationTime) >= pageData.codeValidityDuration) {

                    // Show error message about expired code
                    const errorAlert = form.querySelector('.alert-custom-danger') || document.createElement('div');
                    if (!form.querySelector('.alert-custom-danger')) {
                        errorAlert.className = 'alert alert-custom-danger mt-3';
                        form.prepend(errorAlert);
                    }
                    errorAlert.innerHTML = `<i class="bi bi-exclamation-triangle-fill me-2"></i>Doğrulama kodunuzun süresi doldu. Lütfen <a href='yenikod.php' class='alert-link'>yeni bir kod isteyin</a>.`;
                    return;
                }
                let allFilled = true;
                form.querySelectorAll('.verification-code-inputs input').forEach(input => {
                    if (input.value === '') {
                        allFilled = false;
                        input.classList.add('is-invalid');
                    } else {
                        input.classList.remove('is-invalid');
                    }
                });
                if (!allFilled) {

                    event.stopPropagation();
                    // Optionally show a generic "fill all fields" message
                }
            }


            if (!form.checkValidity()) {

                event.stopPropagation();
                const firstInvalidField = form.querySelector(':invalid');
                if (firstInvalidField) {
                    firstInvalidField.focus();
                }
            } else {
                disableSubmitButton(submitButton, submitButton ? submitButton.dataset.loadingText || 'Kaydediliyor...' : 'Kaydediliyor...');
            }
            form.classList.add('was-validated');
        }, false);
    });

    // --- AVATAR PREVIEW ---
    const avatarInput = document.getElementById('avatarInput');
    const avatarPreview = document.getElementById('avatarPreview');

    function previewAvatar() {
        if (avatarInput && avatarInput.files && avatarInput.files[0]) {
            const reader = new FileReader();
            reader.onload = function (e) {
                if (avatarPreview) avatarPreview.src = e.target.result;
            }
            reader.readAsDataURL(avatarInput.files[0]);
        }
    }
    if (avatarInput) {
        avatarInput.addEventListener('change', previewAvatar);
    }

    // --- SUCCESS ALERT AUTO-HIDE ---
    const successAlert = document.querySelector('.alert-custom-success[data-auto-dismiss]');
    if (successAlert) {
        setTimeout(() => {
            successAlert.style.transition = 'opacity 0.5s ease';
            successAlert.style.opacity = '0';
            setTimeout(() => {
                successAlert.remove();
                // Optional: Clear specific cookies if needed
                if (successAlert.dataset.clearCookie) {
                    document.cookie = `${successAlert.dataset.clearCookie}=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;`;
                }
            }, 500);
        }, parseInt(successAlert.dataset.autoDismiss, 10) || 5000);
    }

    // --- FLOATING LABELS FOR SELECTS (Bootstrap 5 specific issue) ---
    const floatingSelects = document.querySelectorAll('.form-floating > .form-select');
    floatingSelects.forEach(select => {
        function checkSelectValue() {
            const firstOption = select.options[0];
            // Add .has-value if a non-empty, non-placeholder option is selected
            if (select.value && !(firstOption && firstOption.value === "" && select.selectedIndex === 0)) {
                select.classList.add('has-value');
            } else {
                select.classList.remove('has-value');
            }
        }
        checkSelectValue(); // Initial check
        select.addEventListener('change', checkSelectValue);
    });


    // --- PAGE-SPECIFIC INITIALIZATIONS ---

    // ** REGISTRATION PAGE SPECIFICS **
    const registrationForm = document.getElementById('registrationForm');
    if (registrationForm) {
        const regPasswordInput = document.getElementById('password'); // Assuming 'password' is the ID
        const regConfirmPasswordInput = document.getElementById('confirmPassword');
        const regStrengthBar = document.getElementById('strengthBar');
        const regStrengthText = document.getElementById('strengthText');
        const regPasswordFeedback = document.getElementById('passwordFeedback'); // General feedback for password
        const regConfirmPasswordFeedback = document.getElementById('confirmPasswordFeedback'); // Feedback for confirmation

        if (regPasswordInput && regStrengthBar && regStrengthText) {
            regPasswordInput.addEventListener('input', function () {
                const password = this.value;
                const strengthDetails = calculatePasswordStrength(password);

                regStrengthBar.style.width = strengthDetails.percentage + '%';
                regStrengthBar.className = 'progress-bar'; // Clear previous
                regStrengthBar.classList.add(strengthDetails.colorClass);
                regStrengthText.textContent = strengthDetails.text;
                regStrengthText.className = 'fw-bold'; // Base class
                if (strengthDetails.colorClass === 'bg-danger') regStrengthText.classList.add('text-danger');
                else if (strengthDetails.colorClass === 'bg-warning') regStrengthText.classList.add('text-warning');
                else if (strengthDetails.colorClass === 'bg-success') regStrengthText.classList.add('text-success');

                if (password.length > 0 && !strengthDetails.isValid) {
                    this.setCustomValidity(strengthDetails.feedback || "Şifre yeterince güçlü değil.");
                    if (regPasswordFeedback) regPasswordFeedback.textContent = strengthDetails.feedback || "Şifre yeterince güçlü değil. En az 8 karakter, büyük/küçük harf, rakam ve özel karakter içermelidir.";
                } else {
                    this.setCustomValidity('');
                    if (regPasswordFeedback) regPasswordFeedback.textContent = "Şifreniz en az 8 karakter olmalı, büyük/küçük harf, rakam ve özel karakter içermelidir.";
                }
                validateRegistrationPasswordsMatch(); // Re-validate confirm password
            });
        }

        window.validateRegistrationPasswordsMatch = function () { // Make it global if called from elsewhere or just keep local
            if (!regPasswordInput || !regConfirmPasswordInput) return;
            if (regConfirmPasswordInput.value === "" && regPasswordInput.value !== "") {
                regConfirmPasswordInput.setCustomValidity("Lütfen şifrenizi onaylayın.");
                if (regConfirmPasswordFeedback) regConfirmPasswordFeedback.textContent = "Lütfen şifrenizi onaylayın.";
            } else if (regPasswordInput.value !== regConfirmPasswordInput.value && regConfirmPasswordInput.value !== "") {
                regConfirmPasswordInput.setCustomValidity('Şifreler eşleşmiyor.');
                if (regConfirmPasswordFeedback) regConfirmPasswordFeedback.textContent = 'Şifreler eşleşmiyor.';
            } else {
                regConfirmPasswordInput.setCustomValidity('');
                if (regConfirmPasswordFeedback && regConfirmPasswordInput.value !== "") regConfirmPasswordFeedback.textContent = "Şifreler eşleşiyor.";
            }
        }

        if (regConfirmPasswordInput) regConfirmPasswordInput.addEventListener('input', validateRegistrationPasswordsMatch);
        if (regPasswordInput) regPasswordInput.addEventListener('input', validateRegistrationPasswordsMatch); //Also trigger on main pass change
    }


    // ** PROFILE PAGE / NEW PASSWORD (AFTER RESET) SPECIFICS **
    const sifreDegistirForm = document.getElementById('sifreDegistirForm');
    const yeniSifreFormReset = document.getElementById('yeniSifreForm'); // Form on sifre_belirle.php

    function handleNewPasswordStrength(passwordInputEl, strengthBarEl, strengthTextEl, feedbackEl) {
        if (!passwordInputEl || !strengthBarEl || !strengthTextEl) return;

        passwordInputEl.addEventListener('input', function () {
            const details = calculatePasswordStrength(this.value);
            strengthBarEl.style.width = details.percentage + '%';
            strengthBarEl.className = 'progress-bar ' + details.colorClass;
            strengthTextEl.textContent = details.text;

            if (this.value.length > 0 && !details.isValid) {
                this.setCustomValidity(details.feedback || "Zayıf şifre.");
                if (feedbackEl) {
                    feedbackEl.textContent = details.feedback || "Şifre zayıf. Büyük/küçük harf, rakam, özel karakter ve en az 8 karakter kullanın.";
                    feedbackEl.style.color = "var(--bs-danger-text-emphasis, red)";
                }
            } else {
                this.setCustomValidity("");
                if (feedbackEl) {
                    feedbackEl.textContent = "Şifreniz en az 8 karakter olmalı ve şifre gücü kriterlerini sağlamalıdır.";
                    feedbackEl.style.color = ""; // Reset color
                }
            }
            // Also re-validate confirmation if this is the main password input
            if (this.id === 'yeniSifreInput') { // Assuming this ID for new password
                validateNewPasswordsMatch('yeniSifreInput', 'yeniSifreTekrarInput', 'yeniSifreTekrarFeedback');
            }
        });
    }

    window.validateNewPasswordsMatch = function (newPassId, confirmPassId, feedbackId) {
        const newPasswordInput = document.getElementById(newPassId);
        const confirmPasswordInput = document.getElementById(confirmPassId);
        const feedbackElement = feedbackId ? document.getElementById(feedbackId) : null;

        if (!newPasswordInput || !confirmPasswordInput) return;

        if (confirmPasswordInput.value === "" && newPasswordInput.value !== "") {
            confirmPasswordInput.setCustomValidity("Onay şifresi boş olamaz.");
            if (feedbackElement) {
                feedbackElement.textContent = "Lütfen şifrenizi onaylayın.";
                feedbackElement.style.color = "var(--bs-danger-text-emphasis, red)";
            }
        } else if (newPasswordInput.value !== confirmPasswordInput.value && confirmPasswordInput.value !== "") {
            confirmPasswordInput.setCustomValidity('Yeni şifreler eşleşmiyor.');
            if (feedbackElement) {
                feedbackElement.textContent = 'Yeni şifreler eşleşmiyor.';
                feedbackElement.style.color = "var(--bs-danger-text-emphasis, red)";
            }
        } else {
            confirmPasswordInput.setCustomValidity('');
            if (feedbackElement) {
                if (confirmPasswordInput.value !== "") {
                    feedbackElement.textContent = "Şifreler eşleşiyor!";
                    feedbackElement.style.color = "var(--bs-success-text-emphasis, green)";
                } else { // Both empty or only confirm is empty
                    feedbackElement.textContent = "Lütfen yeni şifrenizi onaylayın.";
                    feedbackElement.style.color = ""; // Reset
                }
            }
        }
    }

    if (sifreDegistirForm) { // Profile page password change
        handleNewPasswordStrength(
            document.getElementById('yeniSifreInput'),
            document.getElementById('yeniSifreStrengthBar'),
            document.getElementById('yeniSifreStrengthText'),
            null // No specific single feedback div in provided snippets for profile's strength, uses native validation messages
        );
        const confirmInput = document.getElementById('yeniSifreTekrarInput');
        if (confirmInput) confirmInput.addEventListener('input', () => validateNewPasswordsMatch('yeniSifreInput', 'yeniSifreTekrarInput', null));
        const yeniSifreOnProfile = document.getElementById('yeniSifreInput');
        if (yeniSifreOnProfile) yeniSifreOnProfile.addEventListener('input', () => validateNewPasswordsMatch('yeniSifreInput', 'yeniSifreTekrarInput', null));

    }
    if (yeniSifreFormReset) { // Password reset new password form
        handleNewPasswordStrength(
            document.getElementById('yeniSifreInput'),
            document.getElementById('yeniSifreStrengthBar'),
            document.getElementById('yeniSifreStrengthText'),
            document.getElementById('yeniSifreFeedbackDefault')
        );
        const confirmInputReset = document.getElementById('yeniSifreTekrarInput');
        if (confirmInputReset) {
            confirmInputReset.addEventListener('input', () => validateNewPasswordsMatch('yeniSifreInput', 'yeniSifreTekrarInput', 'yeniSifreTekrarFeedback'));
        }
        const yeniSifreOnReset = document.getElementById('yeniSifreInput');
        if (yeniSifreOnReset) yeniSifreOnReset.addEventListener('input', () => validateNewPasswordsMatch('yeniSifreInput', 'yeniSifreTekrarInput', 'yeniSifreTekrarFeedback'));

        // Hide form if PHP indicates success/error
        if (pageData.successMessageReset || pageData.tokenErrorMessage) {
            yeniSifreFormReset.style.display = 'none';
            const securityBox = document.querySelector('.security-info-box');
            if (securityBox) securityBox.style.display = 'none';
        }
    }


    // ** MAKALE YUKLEME (FILE UPLOAD) PAGE SPECIFICS **
    const makaleYukleForm = document.getElementById('makaleYukleForm');
    if (makaleYukleForm) {
        // Select2
        if (typeof $ !== 'undefined' && $('#kategorilerSelect').length) {
            $('#kategorilerSelect').select2({
                theme: "bootstrap-5",
                tags: true,
                tokenSeparators: [',']
            });
        }

        // File Upload Type Toggle
        const pdfToggle = document.getElementById('inputTypePdf');
        const textToggle = document.getElementById('inputTypeText');
        const pdfSection = document.getElementById('pdfUploadSection');
        const textSection = document.getElementById('textInputSection');

        function toggleUploadType() {
            if (!pdfToggle || !textToggle || !pdfSection || !textSection) return;
            if (pdfToggle.checked) {
                pdfSection.classList.remove('d-none');
                textSection.classList.add('d-none');
            } else if (textToggle.checked) {
                pdfSection.classList.add('d-none');
                textSection.classList.remove('d-none');
            }
        }
        if (pdfToggle) pdfToggle.addEventListener('change', toggleUploadType);
        if (textToggle) textToggle.addEventListener('change', toggleUploadType);
        toggleUploadType(); // Initial state

        // Drag & Drop File Upload
        const dropArea = document.getElementById('dropArea');
        const fileInput = document.getElementById('makaleDosyasiInput');
        const filePreviewDiv = document.getElementById('filePreview');
        const fileNamePreview = document.getElementById('fileNamePreview');
        const fileSizePreview = document.getElementById('fileSizePreview');

        if (dropArea && fileInput && filePreviewDiv && fileNamePreview && fileSizePreview) {
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                dropArea.addEventListener(eventName, preventDefaults, false);
                document.body.addEventListener(eventName, preventDefaults, false);
            });
            function preventDefaults(e) { e.preventDefault(); e.stopPropagation(); }

            ['dragenter', 'dragover'].forEach(eventName => {
                dropArea.addEventListener(eventName, () => dropArea.classList.add('dragover'), false);
            });
            ['dragleave', 'drop'].forEach(eventName => {
                dropArea.addEventListener(eventName, () => dropArea.classList.remove('dragover'), false);
            });

            dropArea.addEventListener('drop', handleDrop, false);
            fileInput.addEventListener('change', function () {
                handleFiles(this.files);
            });

            function handleDrop(e) {
                const dt = e.dataTransfer;
                handleFiles(dt.files);
            }

            function handleFiles(files) {
                if (files.length > 0) {
                    const file = files[0];
                    if (file.type === "application/pdf") {
                        fileNamePreview.textContent = file.name;
                        fileSizePreview.textContent = `(${(file.size / 1024 / 1024).toFixed(2)} MB)`;
                        filePreviewDiv.classList.remove('d-none');
                        fileInput.files = files; // Assign to input for form submission
                    } else {
                        alert("Lütfen sadece PDF dosyası yükleyin.");
                        clearFilePreview();
                    }
                }
            }
            window.clearFilePreview = function () { // Make global if button calls it
                fileInput.value = "";
                fileNamePreview.textContent = "";
                fileSizePreview.textContent = "";
                filePreviewDiv.classList.add('d-none');
            }
        }
    }

    // ** VERIFICATION PAGE SPECIFICS **
    const verificationForm = document.getElementById('verificationForm');
    if (verificationForm) {
        const codeInputs = verificationForm.querySelectorAll('.verification-code-inputs input');
        const verifyButton = document.getElementById('verifyButton'); // Already handled by generic form handler
        const countdownContainer = document.getElementById('countdownSectionContainer');
        const resendLink = document.getElementById('resendLink');
        let countdownInterval;

        codeInputs.forEach((input, index) => {
            input.addEventListener('input', (e) => {
                const value = e.target.value;
                if (!/^[0-9]?$/.test(value)) { // Allow only single digit or empty
                    e.target.value = value.slice(0, -1);
                    return;
                }
                if (value.length === 1 && index < codeInputs.length - 1) {
                    codeInputs[index + 1].focus();
                }
            });
            input.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && e.target.value === '' && index > 0) {
                    codeInputs[index - 1].focus();
                } else if (e.key === 'ArrowLeft' && index > 0) {
                    codeInputs[index - 1].focus();
                } else if (e.key === 'ArrowRight' && index < codeInputs.length - 1) {
                    codeInputs[index + 1].focus();
                }
            });
            input.addEventListener('paste', (e) => {

                const pasteData = (e.clipboardData || window.clipboardData).getData('text').replace(/\s/g, '').slice(0, codeInputs.length - index);
                if (/^\d+$/.test(pasteData)) {
                    for (let i = 0; i < pasteData.length; i++) {
                        if (codeInputs[index + i]) {
                            codeInputs[index + i].value = pasteData[i];
                            if (i === pasteData.length - 1 && (index + i) < codeInputs.length - 1) {
                                codeInputs[index + i + 1].focus();
                            } else if (i === pasteData.length - 1) {
                                codeInputs[index + i].focus(); // Focus last pasted into
                            }
                        }
                    }
                }
            });
        });

        function startOrUpdateCountdown() {
            if (!countdownContainer || !pageData.registrationTime || !pageData.codeValidityDuration || !pageData.serverTimeAtLoad) return;
            if (countdownInterval) clearInterval(countdownInterval);

            // More accurate: calculate remaining time based on current browser time and initial server times
            const serverTimeNowApproximation = pageData.serverTimeAtLoad + (Math.floor(Date.now() / 1000) - Math.floor(pageData.serverTimeAtLoad)); // Rough client-side tick
            let timePassedSinceRegistration = serverTimeNowApproximation - pageData.registrationTime;
            let countdownValue = pageData.codeValidityDuration - timePassedSinceRegistration;

            if (countdownValue <= 0) {
                countdownContainer.innerHTML = `Kodunuzun süresi doldu.`;
                if (resendLink) resendLink.classList.remove('disabled');
                if (verifyButton) verifyButton.disabled = true;
                return;
            }

            if (resendLink) resendLink.classList.add('disabled');
            if (verifyButton) verifyButton.disabled = false;

            countdownContainer.innerHTML = `Kodu tekrar göndermek için <strong id="countdownTimer" class="fw-bold" style="color:var(--accent-color-1);">${countdownValue}</strong> saniye bekleyin.`;
            const countdownElement = document.getElementById('countdownTimer');

            countdownInterval = setInterval(() => {
                countdownValue--;
                if (countdownElement) countdownElement.textContent = countdownValue;
                if (countdownValue <= 0) {
                    clearInterval(countdownInterval);
                    countdownContainer.innerHTML = `Kodunuzun süresi doldu.`;
                    if (resendLink) resendLink.classList.remove('disabled');
                    if (verifyButton) verifyButton.disabled = true;
                }
            }, 1000);
        }
        startOrUpdateCountdown();
    }

    // ** PASSWORD RESET REQUEST PAGE (sifre_unuttum.php) **
    const sifreSifirlamaIstekForm = document.getElementById('sifreSifirlamaIstekForm');
    if (sifreSifirlamaIstekForm) {
        const emailInputSifreSifirlama = document.getElementById('epostaInputSifreSifirlama');
        const submitButtonSifreSifirlama = document.getElementById('sifreSifirlaIstekBtn'); // Already handled by generic

        window.validatePasswordResetEmail = function () { // Make global for form submit pre-check
            if (!emailInputSifreSifirlama) return;
            const emailValue = emailInputSifreSifirlama.value.trim();
            if (emailValue === '' || !isValidEmail(emailValue)) {
                emailInputSifreSifirlama.classList.add('is-invalid');
                emailInputSifreSifirlama.setCustomValidity("Lütfen geçerli bir e-posta girin.");
                if (submitButtonSifreSifirlama) submitButtonSifreSifirlama.disabled = true;
            } else {
                emailInputSifreSifirlama.classList.remove('is-invalid');
                emailInputSifreSifirlama.setCustomValidity("");
                if (submitButtonSifreSifirlama) submitButtonSifreSifirlama.disabled = false;
            }
        }

        if (emailInputSifreSifirlama) {
            emailInputSifreSifirlama.addEventListener('input', validatePasswordResetEmail);
            validatePasswordResetEmail(); // Initial check
        }

        // Hide form if PHP indicates success
        if (pageData.successMessageSifreUnuttum) {
            sifreSifirlamaIstekForm.style.display = 'none';
            const securityBox = document.querySelector('.security-info-box');
            if (securityBox) securityBox.style.display = 'none';
        }
    }


    //--- Placeholder for other JS functions if needed (Paylaşım Yönetimi vb.) ---
    // window.addUserToShare = function() { /*...*/ }
    // window.removeSharedUser = function() { /*...*/ }
    // window.copyCitation = function() { /*...*/ }
    // window.exportCitation = function() { /*...*/ }
    // window.saveNote = function() { /*...*/ }

}); // End DOMContentLoaded