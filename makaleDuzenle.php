<?php
 session_start();

//  echo 'Webserver user: ' . exec('whoami');
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
// ini_set('log_errors', 1);
// ini_set('error_log', __DIR__ . '/php_errors.log');

if(!isset($_SESSION['id'])){
    header("location:login.php");
}

// Veritabanı bağlantısı ve fonksiyonlar
require "assets/config/db.php";
require "includes/function.php";

$sayfa_basligi = "Yeni Makale Ekle | Scholar Mate";
$kullanici_adi_soyadi = $_SESSION['login_user_name'] ?? 'Değerli Kullanıcı';
$kullanici_avatar_url = $_SESSION['login_user_avatar'] ?? 'https://cdn-icons-png.flaticon.com/512/3135/3135715.png';

$duzenlenecek_id = intval($_GET['id']);
if(empty($_GET['id'])){
    header("location:index.php");
}
$id = $_SESSION['id'];
//Makale Var ve Kullanıcının mı ?
$query = "select * from makaleler where id='$duzenlenecek_id' AND kullanici_id='$id';";
$result = mysqli_query($conn,$query);
if(!mysqli_num_rows($result) == 1){
    header("location:index.php");
}
$makale = mysqli_fetch_assoc($result);



// Örnek veriler (normalde DB'den gelir veya boş olur)
$baslik = $makale['baslik'];
$yazarlar_str = $makale['yazarlar_raw'];
$dergi_konferans_adi = $makale['dergi_konferans'];
$yayin_yili = $makale['yayin_yili'];
$doi = $makale['doi'];
$ozet_manuel = $makale['ozet_manuel'];
$anahtar_kelimeler_str = $makale['anahtar_kelimeler_raw'];
$yuklenen_dosya_tipi = $makale['dosya_tipi'];



$kategoriler_secili_ids = []; // sadece ID'ler
$kquery = "SELECT kategori_id FROM makale_kategori WHERE makale_id = '$duzenlenecek_id';";
$kresult = mysqli_query($conn, $kquery);
while ($row = mysqli_fetch_assoc($kresult)) {
    $kategoriler_secili_ids[] = intval($row['kategori_id']);
}




$dosya_tipi_secim = $makale['dosya_tipi']; // Varsayılan
$makale_icerigi = $makale['makale_icerigi'];
$kullanici_id = $_SESSION['id'] ?? "0";
$otomatikOzet = $makale['otomatik_ozet'];
$orijinal_dosya_adi = $makale['orijinal_dosya_adi'];
$saklanan_dosya_yolu = $makale['saklanan_dosya_yolu'];

$error_message = "";
$success_message = "";

$mevcut_kategoriler = [];
$sql = "SELECT `id`, `kategori` FROM `kategoriler` ORDER BY `kategori` desc";
if ($res = mysqli_query($conn, $sql)) {
    while ($row = mysqli_fetch_assoc($res)) {
        // id anahtar, kategori değeri
        $mevcut_kategoriler[ intval($row['id']) ] = $row['kategori'];
    }

    mysqli_free_result($res);
} else {
    // Hata durumu
    error_log("Kategori sorgu hatası: " . mysqli_error($conn));
    // istersen burada bir uyarı dizisi atayabilirsin
    $mevcut_kategoriler = [
        0 => 'Kategori yüklenemedi'
    ];
}


if ($_SERVER['REQUEST_METHOD'] == 'POST' ) {
    // Form  al
    $baslik = htmlspecialchars(trim($_POST['baslik'] ?? ""));
    $yazarlar_str = htmlspecialchars(trim($_POST['yazarlar'] ?? ""));
    $dergi_konferans_adi = htmlspecialchars(trim($_POST['dergi_konferans'] ?? ""));
    $yayin_yili = filter_var(trim($_POST['yayin_yili'] ?? ""), FILTER_VALIDATE_INT);
    $doi = htmlspecialchars(trim($_POST['doi'] ?? ""));
    $ozet_manuel = htmlspecialchars(trim($_POST['ozet_manuel'] ?? ""));
    $anahtar_kelimeler_str = htmlspecialchars(trim($_POST['anahtar_kelimeler'] ?? ""));
    $kategoriler_secili_ids = $_POST['kategoriler'] ?? []; 
    $kontrol = $dosya_tipi_secim === 'pdf' && isset($_FILES['makaleDosyasi']) && $_FILES['makaleDosyasi']['error'] == UPLOAD_ERR_OK;

    $makale_dosyasi = null;
    $dosyaVarMi = (isset($_FILES['makaleDosyasi']) && $_FILES['makaleDosyasi']['error'] !== UPLOAD_ERR_NO_FILE);


    if ($dosyaVarMi || !empty($_POST['makaleMetni'])){
        $dosya_tipi_secim = $_POST['dosyaTipiSecim'] ?? 'pdf';
       
        if ($dosya_tipi_secim === 'PDF' && isset($_FILES['makaleDosyasi']) && $_FILES['makaleDosyasi']['error'] == UPLOAD_ERR_OK) {
            $dosya_tipi_secim = $_POST['dosyaTipiSecim'] ?? 'PDF';
            $makale_dosyasi = $_FILES['makaleDosyasi'];
            $saklanan_dosya_yolu = pdfYukle($makale_dosyasi);
            if(!$saklanan_dosya_yolu){
                $error_message = "PDF yüklenirken hata oluştu.";
                exit();
            }
            $orijinal_dosya_adi = basename($makale_dosyasi['name']);
            $yuklenen_dosya_tipi = "PDF";
            $makale_icerigi = pdfText($saklanan_dosya_yolu);
        } 
        elseif ($dosya_tipi_secim === 'text') {
            $metin_icerik = trim(htmlspecialchars($_POST['makaleMetni']) ?? "");
            if (!empty($metin_icerik)) {
                $makale_icerigi = $metin_icerik;
                $orijinal_dosya_adi = "text girildi";
                $saklanan_dosya_yolu = "text girildi";

                $yuklenen_dosya_tipi = "TXT";
               
            }
            else{
                $error_message = "Geçerli bir veri girişi yapmalısınız.";
                exit();
            }
        }
    }


    if (empty($baslik) || empty($yazarlar_str) || empty($yayin_yili)) {
        $error_message = "Başlık, Yazarlar ve Yayın Yılı alanları zorunludur.";
    } 
     else {
        if($dosyaVarMi || !empty($_POST['makaleMetni'])){
            $otomatikOzet = Ozetle($makale_icerigi);
            $query = "UPDATE makaleler SET
            baslik = '$baslik',
            yayin_yili = '$yayin_yili',
            dergi_konferans = '$dergi_konferans_adi',
            doi = '$doi',
            ozet_manuel = '$ozet_manuel',
            yazarlar_raw = '$yazarlar_str',
            anahtar_kelimeler_raw = '$anahtar_kelimeler_str',
            makale_icerigi = '$makale_icerigi',
            dosya_tipi = '$yuklenen_dosya_tipi',
            orijinal_dosya_adi = '$orijinal_dosya_adi',
            saklanan_dosya_yolu = '$saklanan_dosya_yolu',
            kullanici_id = '$kullanici_id',
            otomatik_ozet = '$otomatikOzet'
            WHERE id = '$duzenlenecek_id'";
            $result = mysqli_query($conn,$query);
        }
        else{
            $query = "UPDATE makaleler SET
            baslik = '$baslik',
            yayin_yili = '$yayin_yili',
            dergi_konferans = '$dergi_konferans_adi',
            doi = '$doi',
            ozet_manuel = '$ozet_manuel',
            yazarlar_raw = '$yazarlar_str',
            anahtar_kelimeler_raw = '$anahtar_kelimeler_str',
            makale_icerigi = '$makale_icerigi',
            dosya_tipi = '$yuklenen_dosya_tipi',
            orijinal_dosya_adi = '$orijinal_dosya_adi',
            saklanan_dosya_yolu = '$saklanan_dosya_yolu',
            kullanici_id = '$kullanici_id'
            WHERE id = '$duzenlenecek_id'";
            $result = mysqli_query($conn,$query);
        }
       

        $makale_yeni_id = $duzenlenecek_id;
        $query = "DELETE FROM makale_kategori WHERE makale_id = $makale_yeni_id;";
        $result = mysqli_query($conn,$query);


        $stmt = mysqli_prepare(
            $conn,
            "INSERT INTO makale_kategori (kategori, makale_id, kategori_id) VALUES (?, ?, ?)"
        );
        
        foreach ($kategoriler_secili_ids as $katId) {
            if (! isset($mevcut_kategoriler[$katId])) {
                continue;
            }
            $kategoriAdi = $mevcut_kategoriler[$katId];
        
            mysqli_stmt_bind_param($stmt, "sii", $kategoriAdi, $makale_yeni_id, $katId);
            mysqli_stmt_execute($stmt);
        }
        
        mysqli_stmt_close($stmt);
        
        if($result){
                header("location:makaleDetay.php?id=$makale_yeni_id");
        }
        else{
            $error_message = "Yüklenme sırasında problem.";
        }
        
    }
}





?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $sayfa_basligi; ?></title>
    <link rel="shortcut icon" href="assets/images/logo.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        
    </style>
</head>
<body>
    <button id="themeToggleBtn" class="btn theme-toggle-button" title="Temayı Değiştir">
        <i class="bi bi-moon-stars-fill"></i>
    </button>
    <?php require "templates/partials/navbar.php"; ?>

    <div class="container-fluid" id="main-bg">
        <div class="container py-4">
            <div class="upload-form-card animate-fade-in-down">
                <div class="text-center mb-4">
                    <i class="bi bi-cloud-arrow-up-fill display-3" style="color:var(--accent-color-1);"></i>
                    <h2 class="mt-2">Yeni Makale Yükle</h2>
                    <p class="text-muted-color">Akademik çalışmalarınızı platforma ekleyin ve yönetin.</p>
                </div>

                <?php if (!empty($error_message)): ?>
                    <div class="alert alert-custom-danger text-center animate-fade-in-down" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo $error_message; ?>
                    </div>
                <?php endif; ?>
                <?php if (!empty($success_message)): ?>
                    <div class="alert alert-custom-success text-center animate-fade-in-down" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i><?php echo $success_message; ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="" id="makaleYukleForm" enctype="multipart/form-data" novalidate>
                    
                    <div class="toggle-input-type mb-4">
                        <input type="radio" class="btn-check" name="dosyaTipiSecim" id="inputTypePdf" value="PDF" autocomplete="off" <?php echo ($dosya_tipi_secim === 'pdf' ? 'checked' : ''); ?>>
                        <label class="btn" for="inputTypePdf"><i class="bi bi-file-earmark-pdf-fill me-2"></i>PDF Yükle</label>

                        <input type="radio" class="btn-check" name="dosyaTipiSecim" id="inputTypeText" value="text" autocomplete="off" <?php echo ($dosya_tipi_secim === 'text' ? 'checked' : ''); ?>>
                        <label class="btn" for="inputTypeText"><i class="bi bi-file-text-fill me-2"></i>Metin Olarak Gir</label>
                    </div>
                    <center>
                    <div>
                        Mevcut Pdf : <a href="<?php echo $saklanan_dosya_yolu; ?>" target="_blank"><?php echo $orijinal_dosya_adi?></a>
                    </div>
                    </center>

                    <div id="pdfUploadSection" class="<?php echo ($dosya_tipi_secim !== 'pdf' ? 'd-none' : ''); ?>">
                        <div class="file-upload-area mb-3" id="dropArea">
                            <i class="bi bi-cloud-upload-fill"></i>
                            <h5>PDF Dosyasını Buraya Sürükleyin veya Seçin</h5>
                            <p class="small">Maksimum dosya boyutu: 25MB. Sadece .pdf uzantılı dosyalar.</p>
                            <input type="file" id="makaleDosyasiInput" name="makaleDosyasi" hidden accept=".pdf">
                            <button type="button" class="btn btn-sm btn-outline-custom mt-2" onclick="document.getElementById('makaleDosyasiInput').click();">Dosya Seç</button>
                        </div>
                        <div id="filePreview" class="file-preview-item d-none mb-3">
                             <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <span class="badge file-preview-badge me-2">PDF</span>
                                    <span id="fileNamePreview">dosya_adi.pdf</span>
                                    <span id="fileSizePreview" class="small text-muted-color ms-2"></span>
                                </div>
                                <button type="button" class="btn-close btn-sm" aria-label="Kaldır" onclick="clearFilePreview()" style="filter:var(--btn-close-filter);"></button>
                            </div>
                        </div>
                    </div>
                    
                    <div id="textInputSection" class="mb-3 <?php echo ($dosya_tipi_secim !== 'text' ? 'd-none' : ''); ?>">
                        <div class="form-floating">
                            <textarea class="form-control" name="makaleMetni" id="makaleMetniInput" placeholder="Makale içeriğini buraya yapıştırın veya yazın..." style="min-height: 200px;"><?php /* metin girildiyse buraya PHP ile yazdırılabilir */ ?></textarea>
                            <label for="makaleMetniInput"><i class="bi bi-textarea-t me-2"></i>Makale Metni</label>
                        </div>
                        <div class="form-text text-muted-color small mt-1">Metin içeriği otomatik olarak .txt formatında kaydedilecektir.</div>
                    </div>

                    <hr style="border-color:var(--section-divider-color); margin: 2rem 0;">
                    <h4 class="form-section-title">Makale Bilgileri</h4>

                    <div class="row g-3">
                        <div class="col-12">
                            <div class="form-floating">
                                <input type="text" name="baslik" class="form-control" id="baslikInput" placeholder="Makale Başlığı" value="<?php echo $baslik; ?>" required>
                                <label for="baslikInput"><i class="bi bi-type me-2"></i>Makale Başlığı</label>
                                <div class="invalid-feedback">Lütfen makale başlığını girin.</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" name="yazarlar" class="form-control" id="yazarlarInput" placeholder="Yazarlar (Noktalı virgül ; ile ayırın)" value="<?php echo $yazarlar_str; ?>" required>
                                <label for="yazarlarInput"><i class="bi bi-people-fill me-2"></i>Yazarlar</label>
                                <div class="form-text text-muted-color small ms-1">Yazarları noktalı virgül (;) ile ayırarak girin. Örn: Daren Arga; Elif Bilgin</div>
                                <div class="invalid-feedback">Lütfen yazar(lar)ı girin.</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                             <div class="form-floating">
                                <input type="number" name="yayin_yili" class="form-control" id="yayinYiliInput" placeholder="Yayın Yılı" value="<?php echo $yayin_yili; ?>" min="1800" max="<?php echo date('Y') + 5; ?>" required>
                                <label for="yayinYiliInput"><i class="bi bi-calendar3 me-2"></i>Yayın Yılı</label>
                                <div class="invalid-feedback">Lütfen geçerli bir yayın yılı girin.</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                             <div class="form-floating">
                                <input type="text" name="dergi_konferans" class="form-control" id="dergiKonferansInput" placeholder="Dergi veya Konferans Adı (Opsiyonel)" value="<?php echo $dergi_konferans_adi; ?>">
                                <label for="dergiKonferansInput"><i class="bi bi-journal-text me-2"></i>Dergi / Konferans Adı</label>
                            </div>
                        </div>
                         <div class="col-md-6">
                             <div class="form-floating">
                                <input type="text" name="doi" class="form-control" id="doiInput" placeholder="DOI Numarası (Opsiyonel)" value="<?php echo $doi; ?>">
                                <label for="doiInput"><i class="bi bi-link-45deg me-2"></i>DOI Numarası</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-floating">
                                <textarea class="form-control" name="ozet_manuel" id="ozetManuelInput" placeholder="Makale Özeti (Manuel - Opsiyonel)" style="min-height: 120px;"><?php echo $ozet_manuel; ?></textarea>
                                <label for="ozetManuelInput"><i class="bi bi-blockquote-left me-2"></i>Özet (Manuel Giriş)</label>
                                <div class="form-text text-muted-color small ms-1">PDF yüklerseniz özet otomatik olarak çıkarılmaya çalışılacaktır. Buraya manuel olarak da girebilirsiniz.</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" name="anahtar_kelimeler" class="form-control" id="anahtarKelimelerInput" placeholder="Anahtar Kelimeler (Virgül , ile ayırın)" value="<?php echo $anahtar_kelimeler_str; ?>">
                                <label for="anahtarKelimelerInput"><i class="bi bi-tags-fill me-2"></i>Anahtar Kelimeler</label>
                                <div class="form-text text-muted-color small ms-1">Kelimeleri virgül (,) ile ayırın.</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label"><i class="bi bi-folder2-open me-2"></i>Kategoriler</label>
                                <select class="form-select select2-multiple" name="kategoriler[]" id="kategorilerSelect" multiple="multiple" data-placeholder="Kategori Seçin veya ekleyin...">
                                    <?php foreach ($mevcut_kategoriler as $id => $ad): ?>
                                        <option value="<?php echo $id; ?>" <?php  if(in_array($id, $kategoriler_secili_ids,true)) echo 'selected' ?>>
                                            <?php echo htmlspecialchars($ad); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="form-text text-muted-color small mt-1">Mevcut kategorileri seçin veya yeni ekleyin.</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="index.php" class="btn btn-outline-custom"><i class="bi bi-x-lg me-1"></i>İptal</a>
                        <button type="submit" class="btn btn-action-filled btn-lg" name="makaleKaydetBtn" id="makaleKaydetBtn">
                            <i class="bi bi-check-circle-fill me-2"></i>Makaleyi Kaydet
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- Select2 için jQuery gerekli -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="assets/js/script.js"></script>
    <script>
        $(document).ready(function() {
            $('.select2-multiple').select2({
                theme: 'bootstrap-5',
                placeholder: $(this).data('placeholder'),
                allowClear: true,
                tags: false 
            });
        });
        $('.select2-multiple').select2({
            width: '100%', 
            dropdownAutoWidth: true,
            tags: true 
        });

    </script>
</body>
</html>