<?php
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
// ini_set('log_errors', 1);
// ini_set('error_log', __DIR__ . '/php_errors.log');

session_start(); // Sayfanın en başında olmalı, eğer zaten başka yerde başlatılmadıysa.

if(!isset($_SESSION['id'])){
    header("location:login.php");
}

// Örnek Session Yönetimi ve Kullanıcı Verileri (Projenize göre uyarlayın)
// if (!isset($_SESSION['login_user_id'])) {
//     header("location:login.php");
//     exit;
// }
// $kullanici_id = $_SESSION['login_user_id'];
require "assets/config/db.php";
require "includes/function.php";


function resmiKaydet2(string $fileInputName, string $uploadDirectory, array $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'], int $maxFileSize = 20971520): ?string
{
    // 1. Dosya yüklenmiş mi ve hata var mı kontrol et
    if (!isset($_FILES[$fileInputName])) {
        // echo "Hata: Formda '$fileInputName' isimli bir dosya alanı bulunamadı.";
        error_log("Formda '$fileInputName' isimli bir dosya alanı bulunamadı.");
        return null;
    }

    $file = $_FILES[$fileInputName];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        // Yükleme sırasında bir hata oluştu
        $phpFileUploadErrors = [
            UPLOAD_ERR_INI_SIZE   => 'Yüklenen dosya php.ini dosyasındaki upload_max_filesize direktifini aşıyor.',
            UPLOAD_ERR_FORM_SIZE  => 'Yüklenen dosya HTML formundaki MAX_FILE_SIZE direktifini aşıyor.',
            UPLOAD_ERR_PARTIAL    => 'Dosyanın sadece bir kısmı yüklendi.',
            UPLOAD_ERR_NO_FILE    => 'Hiç dosya yüklenmedi.', // Kullanıcı dosya seçmemişse bu hata alınır.
            UPLOAD_ERR_NO_TMP_DIR => 'Geçici klasör eksik.',
            UPLOAD_ERR_CANT_WRITE => 'Dosya diske yazılamadı.',
            UPLOAD_ERR_EXTENSION  => 'Bir PHP eklentisi dosya yüklemesini durdurdu.',
        ];
        $errorMessage = $phpFileUploadErrors[$file['error']] ?? 'Bilinmeyen bir yükleme hatası oluştu.';
        
        // UPLOAD_ERR_NO_FILE durumunda sessizce null dönebiliriz, çünkü bu kullanıcı hatası olabilir.
        if ($file['error'] !== UPLOAD_ERR_NO_FILE) {
            error_log("Dosya yükleme hatası ($fileInputName): " . $errorMessage);
        }
        return null; 
    }

    // 2. Dosya boyutunu kontrol et
    if ($file['size'] > $maxFileSize) {
        // echo "Hata: Dosya boyutu çok büyük. İzin verilen maksimum boyut: " . ($maxFileSize / 1024 / 1024) . " MB";
        error_log("Dosya boyutu çok büyük ($fileInputName): " . $file['size'] . " > " . $maxFileSize);
        return null;
    }

    // 3. Dosya türünü (uzantısını) kontrol et
    $fileName = basename($file['name']); // Güvenlik için basename kullanın
    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    if (!in_array($fileExtension, $allowedExtensions)) {
        // echo "Hata: İzin verilmeyen dosya türü. İzin verilenler: " . implode(', ', $allowedExtensions);
        error_log("İzin verilmeyen dosya türü ($fileInputName): " . $fileExtension);
        return null;
    }

    // 4. Hedef klasörün var olup olmadığını ve yazılabilir olup olmadığını kontrol et
    // $uploadDirectory göreli bir yolsa, scriptin çalıştığı dizine göre değerlendirilir.
    // Genellikle proje kök dizinine göre tam bir yol veya iyi tanımlanmış bir göreli yol kullanmak daha iyidir.
    // Örnek: __DIR__ . '/../uploads/avatars/' veya $_SERVER['DOCUMENT_ROOT'] . '/uploads/avatars/'
    if (!is_dir($uploadDirectory)) {
        // Klasör yoksa oluşturmayı deneyebiliriz (ve yazma izinlerini kontrol etmeliyiz)
        if (!mkdir($uploadDirectory, 0755, true)) { // 0755 izinleri, true ile iç içe klasörler de oluşturulur
            // echo "Hata: Yükleme klasörü oluşturulamadı veya mevcut değil.";
            error_log("Yükleme klasörü oluşturulamadı veya mevcut değil ($fileInputName): " . $uploadDirectory);
            return null;
        }
    }
    if (!is_writable($uploadDirectory)) {
        // echo "Hata: Yükleme klasörüne yazma izni yok.";
        error_log("Yükleme klasörüne yazma izni yok ($fileInputName): " . $uploadDirectory);
        return null;
    }
    
    // 5. Benzersiz bir dosya adı oluştur (aynı isimde dosyaların üzerine yazılmasını engellemek için)
    // Örneğin: timestamp_orijinalad.uzanti veya uniqid()_orijinalad.uzanti
    // Basit bir yaklaşım:
    $safeFileName = preg_replace("/[^a-zA-Z0-9-_\.]/", "", str_replace(" ", "_", $fileName)); // Özel karakterleri temizle
    $newFileName = time() . '_' . uniqid() . '_' . $safeFileName; // Daha benzersiz
    // Veya sadece: $newFileName = uniqid('', true) . '.' . $fileExtension;

    $targetFilePath = rtrim($uploadDirectory, '/') . '/' . $newFileName;

    // 6. Dosyayı geçici konumundan hedef konuma taşı
    if (move_uploaded_file($file['tmp_name'], $targetFilePath)) {
        // Başarılı! Kaydedilen dosyanın göreli yolunu döndür.
        // Eğer $uploadDirectory mutlak bir yolsa, web üzerinden erişilebilir
        // göreli bir yol döndürmeniz gerekebilir.
        // Örneğin, $uploadDirectory = '/var/www/html/projem/uploads/avatars/' ise
        // ve web kökü '/var/www/html/projem/' ise, 'uploads/avatars/' . $newFileName dönmelisiniz.
        // Bu örnekte, $uploadDirectory'nin zaten göreli olduğunu varsayıyoruz.
        return ltrim(str_replace($_SERVER['DOCUMENT_ROOT'], '', $targetFilePath), '/'); // DOCUMENT_ROOT'u kaldırıp göreli yapar
        // VEYA eğer $uploadDirectory zaten web'den erişilebilir göreli bir yolsa:
        // return rtrim($uploadDirectory, '/') . '/' . $newFileName; 
    } else {
        // echo "Hata: Dosya yüklenirken bir sorun oluştu.";
        error_log("move_uploaded_file başarısız oldu ($fileInputName): " . $targetFilePath);
        return null;
    }
}


// Bu veriler normalde veritabanından $kullanici_id kullanılarak çekilir.
// Şimdilik örnek veriler kullanıyoruz:
// if ($_SERVER['REQUEST_METHOD']==='POST') {
//     echo '<pre>POST geldi: '.htmlspecialchars(print_r($_POST,1)).'</pre>';
// }



$query = "select * from uye where e_posta='".$_SESSION['eposta']."';";
$result = mysqli_query($conn,$query);
$result = mysqli_fetch_assoc($result);
$tema = $_SESSION['user_data']['tema_tercihi'] ?? (isset($_COOKIE['theme']) ? $_COOKIE['theme'] : 'light');






$kullanici_verisi = [
    "avatar_url" => $result['avatar'],
    "ad" => $result['ad'],
    "soyad" => $result['soyad'],
    "unvan" => $result['unvan'],
    "arastirma_alani" => $result['arastirma_alani'],
    "eposta" => $result['e_posta'],
    "kurum" => $result['kurum'],
    "tema_tercihi" => $tema ? $tema : "system"
];
$mevcutAvatar = $kullanici_verisi['avatar_url']; // Mevcut avatarı al
$yeniAvatarVeritabaniIcin = $mevcutAvatar; // Başlangıçta mevcut avatarı kullan

$sayfa_basligi = "Profilim | Scholar Mate";

$error_message = "";
$success_message = "";
$error_message_sifre = "";
$success_message_sifre = "";

if(isset($_COOKIE['sifreDegismedi'])){
    $error_message_sifre = $_COOKIE['sifreDegismedi'];
    setcookie("sifreDegismedi",$error_message_sifre,time()-10,'/');
}

if(isset($_COOKIE['sifreDegisti'])){
    $success_message_sifre = $_COOKIE['sifreDegisti'];
    setcookie("sifreDegisti",$error_message_sifre,time()-10,'/');
}

// Profil güncelleme formu
error_log("REQUEST_METHOD: " . $_SERVER['REQUEST_METHOD']);
error_log("profilGuncelleBtn isset?: " . (isset($_POST['profilGuncelleBtn']) ? 'YES' : 'NO'));
if ($_SERVER['REQUEST_METHOD'] == 'POST' ) {
    error_log("--- Profil Güncelleme İsteği Başladı ---");
    // echo '<pre>POST geldi: '.htmlspecialchars(print_r($_POST,1)).'</pre>';
    // Formdan gelen verileri al ve temizle
    $ad_form = htmlspecialchars(trim($_POST['ad'] ?? $kullanici_verisi['ad']));
    $soyad_form = htmlspecialchars(trim($_POST['soyad'] ?? $kullanici_verisi['soyad']));
    $unvan_form = htmlspecialchars(trim($_POST['unvan'] ?? $kullanici_verisi['unvan']));
    $kurum_form = htmlspecialchars(trim($_POST['kurum'] ?? $kullanici_verisi['kurum']));
    $arastirma_alani_form = htmlspecialchars(trim($_POST['arastirmaAlani'] ?? $kullanici_verisi['arastirma_alani']));
    $tema_tercihi_form = htmlspecialchars(trim($_POST['temaTercihi'] ?? $kullanici_verisi['tema_tercihi']));

    // Mevcut avatar yolunu al (yeni yükleme olmazsa bunu kullanacağız)
    $yeni_avatar_db_icin = $kullanici_verisi['avatar_url'];
    $avatar_guncellendi = false;

    // Temel validasyon
    if (empty($ad_form) || empty($soyad_form)) {
        $error_message = "Ad ve Soyad alanları boş bırakılamaz.";
        error_log("Profil Güncelleme Hatası: Ad veya Soyad boş.");
    } else {
        // Avatar yükleme işlemleri
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] == UPLOAD_ERR_OK) {
            error_log("Avatar dosyası bulundu, yükleme deneniyor...");
            // $uploadDirectory, HTDOCS kökünden itibaren WEB'DEN ERİŞİLEBİLİR göreli yol olmalı
            // resmiKaydet2 fonksiyonu da bu göreli yolun BAŞINA eklenerek tam sunucu yolunu oluşturmalı
            // ve yine WEB'DEN ERİŞİLEBİLİR göreli yolu (örn: assets/uploads/profilPhotos/dosya.jpg) döndürmeli.
            
            // Bu yolun, web sunucunuzun belge kökünden (DOCUMENT_ROOT) itibaren göreli yol olduğunu varsayıyoruz.
            // Örn: $_SERVER['DOCUMENT_ROOT'] -> /Applications/XAMPP/xamppfiles/htdocs
            //       $sunucuTarafiUploadKlasoru -> /Applications/XAMPP/xamppfiles/htdocs/assets/uploads/profilPhotos/
            //       resmiKaydet2 bu tam yolu kullanacak.
            //$sunucuTarafiUploadKlasoru = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'profilPhotos';
            $sunucuTarafiUploadKlasoru = "assets/uploads/profilPhotos/";
            // echo $sunucuTarafiUploadKlasoru;
             // Eğer projeniz 'ScholarMate' gibi bir alt klasördeyse bunu ekleyin, değilse kaldırın.
             // Eğer HTDOCS kökündeyse: $sunucuTarafiUploadKlasoru = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'profilPhotos';


            error_log("resmiKaydet2 için Upload Directory: " . $sunucuTarafiUploadKlasoru);

            $webdenErisilebilirAvatarYolu = resmiKaydet2(
                "avatar",                       // $_FILES içindeki input adı
                $sunucuTarafiUploadKlasoru      // Dosyanın kaydedileceği TAM SUNUCU YOLU
                                                // İzin verilen uzantılar ve max dosya boyutu fonksiyonun varsayılanlarını kullanacak
            );

            if ($webdenErisilebilirAvatarYolu !== null) {
                error_log("resmiKaydet2 başarılı, dönen yol: " . $webdenErisilebilirAvatarYolu);
                // Eski avatarı sil (eğer varsayılan değilse ve gerçekten değişmişse)
                $mevcutAvatarTamSunucuYolu = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . 'ScholarMate' .  DIRECTORY_SEPARATOR . ltrim($kullanici_verisi['avatar_url'], '/');
                // Eğer HTDOCS kökündeyse: $mevcutAvatarTamSunucuYolu = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . ltrim($kullanici_verisi['avatar_url'], '/');

                if ($kullanici_verisi['avatar_url'] &&
                    $kullanici_verisi['avatar_url'] !== 'https://cdn-icons-png.flaticon.com/512/3135/3135715.png' &&
                    $kullanici_verisi['avatar_url'] !== $webdenErisilebilirAvatarYolu && // Aynı dosyayı tekrar yüklemiş olabilir
                    file_exists($mevcutAvatarTamSunucuYolu)) {
                    
                    if (@unlink($mevcutAvatarTamSunucuYolu)) {
                        error_log("Eski avatar silindi: " . $mevcutAvatarTamSunucuYolu);
                    } else {
                        error_log("Eski avatar SİLİNEMEDİ: " . $mevcutAvatarTamSunucuYolu);
                    }
                }
                $yeni_avatar_db_icin = $webdenErisilebilirAvatarYolu; // Veritabanına yazılacak YENİ göreli yolu güncelle
                $avatar_guncellendi = true;
            } else {
                error_log("resmiKaydet2 başarısız oldu (null döndü). Avatar yüklenemedi.");
                $error_message .= (empty($error_message) ? "" : " ") . "Avatar yüklenirken bir sorun oluştu veya seçilen dosya geçerli değil.";
            }
        } elseif (isset($_FILES['avatar']) && $_FILES['avatar']['error'] != UPLOAD_ERR_NO_FILE) {
            // Dosya seçildi ama UPLOAD_ERR_OK dışında bir hata var (örn: boyut aşıldı php.ini'de)
            // resmiKaydet2 fonksiyonu bu hataları zaten logluyor olmalı.
             $error_message .= (empty($error_message) ? "" : " ") . "Avatar yüklenirken beklenmedik bir sorun oluştu (Hata kodu: ".$_FILES['avatar']['error'].").";
             error_log("Avatar yüklemede UPLOAD_ERR_OK dışında hata: " . $_FILES['avatar']['error']);
        }
        // Eğer $_FILES['avatar'] hiç set edilmemişse veya UPLOAD_ERR_NO_FILE ise, kullanıcı yeni avatar seçmemiştir,
        // $yeni_avatar_db_icin değişkeni mevcut avatar yolunu koruyacaktır.

        // Sadece $error_message BOŞ ise veritabanı güncellemesi yap
        if (empty($error_message)) {
            $update_query = "UPDATE uye SET ad = ?, soyad = ?, unvan = ?, kurum = ?, arastirma_alani = ?, avatar = ? WHERE e_posta = ?";
            $stmt = mysqli_prepare($conn, $update_query);

            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "sssssss", $ad_form, $soyad_form, $unvan_form, $kurum_form, $arastirma_alani_form, $yeni_avatar_db_icin, $kullanici_verisi['eposta']);
                
                if (mysqli_stmt_execute($stmt)) {
                    error_log("Profil DB'de başarıyla güncellendi. E-posta: " . $kullanici_verisi['eposta']);
                    // Başarılı güncelleme sonrası session ve $kullanici_verisi dizisini ANINDA güncelle
                    // Bu, sayfa yenilenmeden doğru bilgilerin gösterilmesini sağlar.
                    
                    // Session'daki genel kullanıcı bilgilerini güncelle
                    $_SESSION['login_user_name'] = $ad_form . " " . $soyad_form;
                    if ($avatar_guncellendi || $yeni_avatar_db_icin !== $kullanici_verisi['avatar_url']) { // Avatar gerçekten değiştiyse session'ı güncelle
                        $_SESSION['login_user_avatar'] = $yeni_avatar_db_icin;
                    }
                    
                    // Eğer 'user_data' gibi detaylı bir session tutuyorsanız onu da güncelleyin
                    if(isset($_SESSION['user_data'])){
                        $_SESSION['user_data']['ad'] = $ad_form;
                        $_SESSION['user_data']['soyad'] = $soyad_form;
                        $_SESSION['user_data']['unvan'] = $unvan_form;
                        $_SESSION['user_data']['kurum'] = $kurum_form;
                        $_SESSION['user_data']['arastirmaAlani'] = $arastirma_alani_form;
                        $_SESSION['user_data']['avatar_url'] = $yeni_avatar_db_icin; // Doğrudan 'avatar' yerine 'avatar_url' tutarlı olur
                        $_SESSION['user_data']['tema_tercihi'] = $tema_tercihi_form;
                    }
                    $_SESSION['avatar'] = $avatar_guncellendi;

                    // Sayfada o an gösterilen $kullanici_verisi dizisini de anında güncelle
                    $kullanici_verisi['ad'] = $ad_form;
                    $kullanici_verisi['soyad'] = $soyad_form;
                    $kullanici_verisi['unvan'] = $unvan_form;
                    $kullanici_verisi['kurum'] = $kurum_form;
                    $kullanici_verisi['arastirma_alani'] = $arastirma_alani_form;
                    $kullanici_verisi['avatar_url'] = $yeni_avatar_db_icin;
                    $kullanici_verisi['tema_tercihi'] = $tema_tercihi_form;
                    
                    // Navbar için de güncelle (eğer $kullanici_..._navbar kullanılıyorsa)
                    $kullanici_adi_soyadi_navbar = $kullanici_verisi['ad'] . ' ' . $kullanici_verisi['soyad'];
                    $kullanici_avatar_url_navbar = $kullanici_verisi['avatar_url'];


                    // Tema cookie'sini ayarla
                    if ($tema_tercihi_form === 'system') {
                        setcookie('theme', '', time() - 3600, "/"); // Sistem tercihinde cookie'yi sil
                    } else {
                        setcookie('theme', $tema_tercihi_form, time() + (86400 * 365), "/"); // 1 yıl geçerli
                    }
                    $success_message = "Profil bilgileriniz ve ayarlarınız başarıyla güncellendi.";
                } else {
                    $error_message = "Profil güncellenirken bir veritabanı hatası oluştu. Lütfen tekrar deneyin.";
                    error_log("Profil DB GÜNCELLEME HATASI (execute): " . mysqli_stmt_error($stmt));
                }
                mysqli_stmt_close($stmt);
            } else {
                $error_message = "Veritabanı sorgusu hazırlanırken bir hata oluştu.";
                error_log("Profil DB GÜNCELLEME HATASI (prepare): " . mysqli_error($conn));
            }
        }
    }
    error_log("--- Profil Güncelleme İsteği Bitti. Hata: " . $error_message . " Başarı: " . $success_message . " ---");
}



?>
<!DOCTYPE html>
<html lang="tr" data-theme="<?php echo htmlspecialchars($kullanici_verisi['tema_tercihi'] == 'system' ? (isset($_COOKIE['actual_theme']) ? $_COOKIE['actual_theme'] : 'light') : $kullanici_verisi['tema_tercihi']); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $sayfa_basligi; ?></title>
    <link rel="shortcut icon" href="assets/images/logo.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <button id="themeToggleBtnGlobal" class="btn theme-toggle-button" title="Temayı Değiştir">
        <i class="bi bi-moon-stars-fill"></i> <!-- JS ile değişecek -->
    </button>
    <?php require "templates/partials/navbar.php"; ?>

    <div class="container-fluid" id="main-bg">
        <div class="container py-4">
            <div class="row justify-content-center">
                <div class="col-12 col-lg-10 col-xl-8">
                    <div class="profile-page-card animate-fade-in-down">
                        <div class="avatar-section text-center">
                            <div class="avatar-upload" onclick="document.getElementById('avatarInput').click()">
                                <img <?php echo "src='".$kullanici_verisi['avatar_url']."'"; ?> 
                                     class="avatar-preview img-fluid"
                                     id="avatarPreview"
                                     alt="Avatar Önizleme">
                                <div class="avatar-edit-icon">
                                    <i class="bi bi-camera-fill"></i>
                                </div>
                            </div>
                        </div>
                        <div class="profile-info text-center mb-4">
                            <h3><?php echo htmlspecialchars($kullanici_verisi['ad'] . ' ' . $kullanici_verisi['soyad']); ?></h3>
                            <p class="lead text-muted-color"><?php echo htmlspecialchars($kullanici_verisi['unvan']); ?> | <?php echo htmlspecialchars($kullanici_verisi['arastirma_alani']); ?></p>
                            <p class="small text-muted-color"><i class="bi bi-building me-1"></i> <?php echo htmlspecialchars($kullanici_verisi['kurum']); ?> <span class="mx-2">•</span> <i class="bi bi-envelope-fill me-1"></i> <?php echo htmlspecialchars($kullanici_verisi['eposta']); ?></p>
                        </div>

                        <?php if (!empty($error_message)): ?>
                            <div class="alert alert-custom-danger text-center" role="alert">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo $error_message; ?>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($success_message)): ?>
                            <div class="alert alert-custom-success text-center" role="alert">
                                <i class="bi bi-check-circle-fill me-2"></i><?php echo $success_message; ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="" id="profilFormKapsayici" enctype="multipart/form-data">
                            <!-- Kişisel Bilgiler Formu -->
                            <div class="form-section">
                                <h5 class="form-section-title">Kişisel Bilgiler</h5>
                                <input type="file" id="avatarInput"  name="avatar" hidden accept="image/*" onchange="previewAvatar()">
                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" name="ad" class="form-control" id="adInput" placeholder="Adınız" value="<?php echo htmlspecialchars($kullanici_verisi['ad']); ?>" required>
                                            <label for="adInput"><i class="bi bi-person me-2"></i>Adınız</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" name="soyad" class="form-control" id="soyadInput" placeholder="Soyadınız" value="<?php echo htmlspecialchars($kullanici_verisi['soyad']); ?>" required>
                                            <label for="soyadInput"><i class="bi bi-person-vcard me-2"></i>Soyadınız</label>
                                        </div>
                                    </div>
                                </div>
                                 <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <select name="unvan" class="form-select" id="unvanSelect" required>
                                                <option value=""></option> <!-- Boş placeholder için -->
                                                <?php $unvanlar = ["Prof. Dr.", "Doç. Dr.", "Dr.", "Öğretim Üyesi", "Araştırmacı", "Doktora Öğrencisi", "Yüksek Lisans Öğrencisi", "Lisans Öğrencisi"]; ?>
                                                <?php foreach ($unvanlar as $u): ?>
                                                <option value="<?php echo $u; ?>" <?php echo ($kullanici_verisi['unvan'] == $u) ? 'selected' : ''; ?>><?php echo $u; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            <label for="unvanSelect"><i class="bi bi-mortarboard me-2"></i>Ünvanınız</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" name="kurum" class="form-control" id="kurumInput" placeholder="Kurumunuz" value="<?php echo htmlspecialchars($kullanici_verisi['kurum']); ?>" required>
                                            <label for="kurumInput"><i class="bi bi-building me-2"></i>Kurumunuz</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-floating mb-3">
                                    <select class="form-select" name="arastirmaAlani" id="arastirmaAlaniSelect" required>
                                        <option value=""></option> <!-- Boş placeholder için -->
                                         <?php $alanlar = ["Bilgisayar Bilimleri", "Mühendislik", "Tıp", "Sosyal Bilimler", "Fen Bilimleri"]; ?>
                                        <?php foreach ($alanlar as $a): ?>
                                        <option value="<?php echo $a; ?>" <?php echo ($kullanici_verisi['arastirma_alani'] == $a) ? 'selected' : ''; ?>><?php echo $a; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <label for="arastirmaAlaniSelect"><i class="bi bi-lightbulb me-2"></i>Araştırma Alanınız</label>
                                </div>
                                <div class="form-floating mb-3">
                                    <input type="email" name="eposta_readonly" class="form-control" id="epostaInput" placeholder="E-posta Adresiniz" value="<?php echo htmlspecialchars($kullanici_verisi['eposta']); ?>" readonly>
                                    <label for="epostaInput"><i class="bi bi-envelope-fill me-2"></i>E-posta Adresiniz (Değiştirilemez)</label>
                                </div>
                                <div class="form-section mt-0"> <!-- Tema tercihini bu formla birleştir -->
                                    <h5 class="form-section-title">Görünüm Ayarları</h5>
                                    <div class="form-floating">
                                        <select class="form-select" id="temaTercihiSelect" name="temaTercihi">
                                            <option value="light" <?php echo ($kullanici_verisi['tema_tercihi'] == 'light') ? 'selected' : ''; ?>>Açık Mod</option>
                                            <option value="dark" <?php echo ($kullanici_verisi['tema_tercihi'] == 'dark') ? 'selected' : ''; ?>>Koyu Mod</option>
                                            <option value="system" <?php echo ($kullanici_verisi['tema_tercihi'] == 'system' || empty($kullanici_verisi['tema_tercihi'])) ? 'selected' : ''; ?>>Sistem Tercihi / Otomatik</option>
                                        </select>
                                        <label for="temaTercihiSelect"><i class="bi bi-palette-fill me-2"></i>Tema Seçimi</label>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end mt-3">
                                    <button type="submit" class="btn btn-action-filled" name="profilGuncelleBtn" id="profilGuncelleBtn">
                                        <i class="bi bi-save-fill me-2"></i>Bilgileri ve Ayarları Kaydet
                                    </button>
                                </div>
                            </div> <!-- Kişisel bilgiler form section sonu -->
                        </form> <!-- Kapsayıcı form sonu -->


                        <!-- Şifre Değiştirme Formu -->
                        <form method="POST" action="includes/sifreDegistir.php" id="sifreDegistirForm" novalidate>
                            <div class="form-section">
                                <h5 class="form-section-title">Şifre Değiştir</h5>
                                 <?php if (!empty($error_message_sifre)): ?>
                                    <div class="alert alert-custom-danger text-center" role="alert">
                                        <i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo $error_message_sifre; ?>
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($success_message_sifre)): ?>
                                    <div class="alert alert-custom-success text-center" role="alert">
                                        <i class="bi bi-check-circle-fill me-2"></i><?php echo $success_message_sifre; ?>
                                    </div>
                                <?php endif; ?>
                                <div class="form-floating mb-3">
                                    <input type="password" name="mevcutSifre" class="form-control" id="mevcutSifreInput" placeholder="Mevcut Şifreniz" required>
                                    <label for="mevcutSifreInput"><i class="bi bi-shield-lock me-2"></i>Mevcut Şifreniz</label>
                                </div>
                                 <div class="form-floating mb-1">
                                    <input type="password" name="yeniSifre" class="form-control" id="yeniSifreInput" placeholder="Yeni Şifreniz" required minlength="8">
                                    <label for="yeniSifreInput"><i class="bi bi-lock-fill me-2"></i>Yeni Şifreniz</label>
                                    <button type="button" class="password-toggle-btn" id="toggleYeniSifre">
                                        <i class="bi bi-eye-slash"></i>
                                    </button>
                                </div>
                                <div class="password-strength-indicator mb-3">
                                    <div class="progress">
                                        <div class="progress-bar" id="yeniSifreStrengthBar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <small class="form-text text-muted-color mt-1">Şifre gücü: <span id="yeniSifreStrengthText" class="fw-bold"></span></small>
                                </div>
                                <div class="form-floating mb-3">
                                    <input type="password" name="yeniSifreTekrar" class="form-control" id="yeniSifreTekrarInput" placeholder="Yeni Şifrenizi Onaylayın" required>
                                    <label for="yeniSifreTekrarInput"><i class="bi bi-shield-check-fill me-2"></i>Yeni Şifrenizi Onaylayın</label>
                                    <button type="button" class="password-toggle-btn" id="toggleYeniSifreTekrar">
                                        <i class="bi bi-eye-slash"></i>
                                    </button>
                                </div>
                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-action-filled" name="sifreDegistirBtn" id="sifreDegistirBtn">
                                        <i class="bi bi-key-fill me-2"></i>Şifreyi Değiştir
                                    </button>
                                </div>
                            </div> <!-- Şifre değiştirme form section sonu -->
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/script.js"></script>
</body>
</html>