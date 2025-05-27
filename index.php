<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php_errors.log');
require "assets/config/db.php";
require "includes/function.php";

$index = 0;
if(!isset($_SESSION['id'])){
    header("location:login.php");
}

if (isset($_SESSION['onay_kodu']) && isset($_SESSION['registration_time'])) {
    if ($_SESSION['registration_time'] + (5 * 60) < time()) {
        unset($_SESSION['onay_kodu']);
        unset($_SESSION['onay_eposta']);
        unset($_SESSION['registration_data']);
        unset($_SESSION['registration_time']);
        // Kullanıcıya bilgi verilebilir: $_SESSION['error_message'] = "Onay süresi doldu...";
    } else {
        // Henüz onaylamamışsa register2.php'ye yönlendir
        if (basename($_SERVER['PHP_SELF']) != 'register2.php') { // register2.php'de sonsuz döngüye girmesin
            // Yönlendirme öncesi cookie (gereksiz döngüyü önlemek için login.php'deki gibi)
            if (!isset($_COOKIE['uyari_register2_yonlendirme_index'])) {
                setcookie("uyari_register2_yonlendirme_index", "1", time() + 30, "/");
                header("location:register2.php");
                exit;
            }
        }
    }
}





$mevcut_kategoriler = [];
$sql = "SELECT `id`, `kategori` FROM `kategoriler` ORDER BY `kategori` ASC";
if ($res = mysqli_query($conn, $sql)) {
    while ($row = mysqli_fetch_assoc($res)) {
        $mevcut_kategoriler[ intval($row['id']) ] = $row['kategori'];
    }
    mysqli_free_result($res);
} else {
    error_log("Kategori sorgu hatası: " . mysqli_error($conn));
    $mevcut_kategoriler = [
        0 => 'Kategori yüklenemedi'
    ];
}

$id = $_SESSION['id'];// Kullanıcı ID
$kategori = isset($_GET['kategori']) ? intval($_GET['kategori']) : null;
$yil = isset($_GET['yil']) ? intval($_GET['yil']) : null;

$query = "SELECT * FROM makaleler WHERE kullanici_id = '$id'";

if ($yil) {
    $query .= " AND yayin_yili = '$yil'";
}

if ($kategori) {
    $altQuery = "SELECT makale_id FROM makale_kategori WHERE kategori_id = '$kategori'";
    $altResult = mysqli_query($conn, $altQuery);
    
    $idList = [];
    while ($row = mysqli_fetch_assoc($altResult)) {
        $idList[] = $row['makale_id'];
    }

    if (!empty($idList)) {
        $idString = implode(',', $idList);
        $query .= " AND id IN ($idString)";
    } else {
        $query .= " AND 0";
    }
}

$result = mysqli_query($conn, $query);


 

?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Makalelerim | Scholar Mate</title>
    <link rel="shortcut icon" href="assets/images/logo.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">

</head>
<body>
    <button id="themeToggleBtn" class="btn theme-toggle-button" title="Temayı Değiştir">
        <i class="bi bi-moon-stars-fill"></i> 
    </button>

<?php require "templates/partials/navbar.php"; ?>

    <div class="container-fluid" id="main-bg">
        <div class="container py-4">
            <div class="content-card">
                <div class="content-card-header d-flex justify-content-between align-items-center mb-4">
                    <h3 class="animate-fade-in-down">Makalelerim</h3>
                    <a href="MakaleYukle.php" class="btn btn-action animate-fade-in-down" style="animation-delay: 0.1s;">
                        <i class="bi bi-plus-circle-fill"></i> Yeni Makale Ekle
                    </a>
                </div>

                <div class="row">
                    <div class="col-md-3 mb-3 mb-md-0">
                        <form action="">
                        <div class="p-3 rounded" style="background-color: var(--input-bg); border: 1px solid var(--input-border);">
                            <h6 class="mb-3 fw-bold">Filtreler</h6>
                            <div class="mb-2">
                                <label for="kategoriFilter" class="form-label small">Kategori</label>
                                <select id="kategoriFilter" name="kategori" class="form-select form-select-sm" style="background-color: var(--article-card-bg); color: var(--text-color); border-color: var(--input-border);">
                                <option value=0 >Tüm Kategoriler</option>
                                <?php foreach($mevcut_kategoriler as $id => $kategori):?>   
                                    <?php
                                        $get = $_GET['kategori'];
                                        ?>
                                    <option <?php if($id == $get) echo "selected"; ?> value="<?php  echo $id; ?>"><?php echo $kategori?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                             <div class="mb-2">
                                <label for="yilFilter" class="form-label small">Yayın Yılı</label>
                                <input type="number" name="yil" id="yilFilter" class="form-control form-control-sm" placeholder="Örn: 2023" style="background-color: var(--article-card-bg); color: var(--text-color); border-color: var(--input-border);">
                            </div>
                            <button class="btn btn-sm btn-outline-primary w-100 mt-2" style="border-color: var(--link-color); color:var(--link-color);"><i class="bi bi-funnel"></i> Filtrele</button>
                        </div>
                        </form>
                    </div>

                    
                    <?php
                    $kategori = isset($_GET['kategori']) ? intval($_GET['kategori']) : null;
                    $yil = isset($_GET['yil']) ? intval($_GET['yil']) : null;
                            $id= $_SESSION['id'];
                    $queryA = "SELECT makale_id FROM makale_paylasim WHERE paylasilan_id='$id'";
                    $resultA = mysqli_query($conn, $queryA);

                    $paylasilanMakaleIdler = [];
                    while ($row = mysqli_fetch_assoc($resultA)) {
                        $paylasilanMakaleIdler[] = $row['makale_id'];
                    }

                    if (empty($paylasilanMakaleIdler)) {
                        $queryA = "SELECT * FROM makaleler WHERE 0"; 
                    } else {
                        $idString = implode(',', $paylasilanMakaleIdler);
                        $queryA = "SELECT * FROM makaleler WHERE id IN ($idString)";

                        if ($yil) {
                            $queryA .= " AND yayin_yili = '$yil'";
                        }

                        if ($kategori) {
                            $altQuery = "SELECT makale_id FROM makale_kategori WHERE kategori_id = '$kategori'";
                            $altResult = mysqli_query($conn, $altQuery);
                            $kategoriIdler = [];

                            while ($row = mysqli_fetch_assoc($altResult)) {
                                $kategoriIdler[] = $row['makale_id'];
                            }

                            if (!empty($kategoriIdler)) {
                                $kategoriString = implode(',', $kategoriIdler);
                                $queryA .= " AND id IN ($kategoriString)";
                            } else {
                                $queryA .= " AND 0";
                            }
                        }
                    }

                    $resultA = mysqli_query($conn, $queryA);

                    ?>
                    <div class="col-md-9">
                        <h3>Sizinle Paylaşılan Makaleler : </h3>
                        <?php if (empty($resultA)): ?>
                            <div class="alert alert-info text-center" style="background-color: var(--input-bg); border-color: var(--input-border); color: var(--text-color);">
                                <i class="bi bi-info-circle-fill fs-3 d-block mb-2"></i>
                                Henüz hiç sizinle paylaşılan makale bulunmuyor. <a href="" class="fw-bold" style="color:var(--link-color);">Hemen bir tane isteyin!</a>
                            </div>
                        <?php else: ?>
                            <?php while($makale = mysqli_fetch_assoc($resultA)): ?>
                            <div class="article-card <?php echo !$makale['kullanici_id'] ? 'shared' : ''; ?> animate-fade-in-down" 
                                 style="animation-delay: <?php echo $index * 0.1 + 0.2; ?>s;" 
                                 onclick="window.location.href='paylasilanMakaleDetay.php?id=<?php echo $makale['id']; ?>'">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h5><?php echo htmlspecialchars($makale['baslik']); ?></h5>
                                        <div class="mb-2 text-muted-color">
                                            <i class="bi bi-person-fill me-1"></i><?php echo htmlspecialchars($makale['yazarlar_raw']); ?>
                                            <span class="mx-2">•</span>
                                            <i class="bi bi-calendar-event me-1"></i><?php echo htmlspecialchars($makale['yayin_yili']); ?>
                                        </div>
                                         <p class="mb-2 text-muted-color small" style="max-height: 3.2em; overflow:hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
                                            <?php echo htmlspecialchars($makale['otomatik_ozet']); ?>
                                        </p>
                                        <div class="d-flex flex-wrap gap-2 align-items-center">

                                        <span class="badge rounded-pill bg-secondary-soft text-secondary-shared">
                                                Sizinle Paylaşılan
                                            </span>
                                            <span class="badge rounded-pill bg-success-soft text-success">
                                                Yayınlandı
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ms-3 text-center">
                                        <i class="bi bi-chevron-right fs-4 text-muted-color"></i>
                                        <!-- <button class="btn btn-sm btn-light border-0 mt-1" style="font-size:0.8rem;"><i class="bi bi-three-dots"></i></button> -->
                                    </div>
                                </div>
                                <div class="article-card-footer d-flex justify-content-between align-items-center">
                                    <div class="article-actions">
                                        <a href="makaleDetay.php?id=<?php echo $makale['id']; ?>&action=edit" onclick="event.stopPropagation();"><i class="bi bi-pencil-square"></i> Düzenle</a>
                                        <a href="#" onclick="event.stopPropagation(); alert('Silme işlemi eklenecek');"><i class="bi bi-trash3"></i> Sil</a>
                                         <a href="#" onclick="event.stopPropagation(); alert('Paylaşım ayarları eklenecek');"><i class="bi bi-share"></i> Paylaş</a>
                                    </div>
                                    <small class="text-muted-color">Son Güncelleme: <?php echo date("d.m.Y H:i", strtotime($makale['updated_at'])); ?></small> <!-- Bu bilgi de DB'den gelmeli -->
                                </div>
                            </div>
                            <?php endwhile; ?>
                        <?php endif; ?>

                        <h3>Kendi Makaleleriniz : </h3>
                         <?php if (empty($result)): ?>
                            <div class="alert alert-info text-center" style="background-color: var(--input-bg); border-color: var(--input-border); color: var(--text-color);">
                                <i class="bi bi-info-circle-fill fs-3 d-block mb-2"></i>
                                Henüz hiç makaleniz bulunmuyor. <a href="MakaleYukle.php" class="fw-bold" style="color:var(--link-color);">Hemen bir tane ekleyin!</a>
                            </div>
                        <?php else: ?>
                            <?php while($makale = mysqli_fetch_assoc($result)): ?>
                            <div class="article-card <?php echo !$makale['kullanici_id'] ? 'shared' : ''; ?> animate-fade-in-down" 
                                 style="animation-delay: <?php echo $index * 0.1 + 0.2; ?>s;" 
                                 onclick="window.location.href='makaleDetay.php?id=<?php echo $makale['id']; ?>'">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h5><?php echo htmlspecialchars($makale['baslik']); ?></h5>
                                        <div class="mb-2 text-muted-color">
                                            <i class="bi bi-person-fill me-1"></i><?php echo htmlspecialchars($makale['yazarlar_raw']); ?>
                                            <span class="mx-2">•</span>
                                            <i class="bi bi-calendar-event me-1"></i><?php echo htmlspecialchars($makale['yayin_yili']); ?>
                                        </div>
                                         <p class="mb-2 text-muted-color small" style="max-height: 3.2em; overflow:hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
                                            <?php echo htmlspecialchars($makale['otomatik_ozet']); ?>
                                        </p>
                                        <div class="d-flex flex-wrap gap-2 align-items-center">

                                            <span class="badge rounded-pill bg-success-soft text-success">
                                                Yayınlandı
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ms-3 text-center">
                                        <i class="bi bi-chevron-right fs-4 text-muted-color"></i>
                                        <!-- <button class="btn btn-sm btn-light border-0 mt-1" style="font-size:0.8rem;"><i class="bi bi-three-dots"></i></button> -->
                                    </div>
                                </div>
                                <div class="article-card-footer d-flex justify-content-between align-items-center">
                                    <div class="article-actions">
                                        <a href="makaleDetay.php?id=<?php echo $makale['id']; ?>&action=edit" onclick="event.stopPropagation();"><i class="bi bi-pencil-square"></i> Düzenle</a>
                                        <a href="#" onclick="event.stopPropagation(); alert('Silme işlemi eklenecek');"><i class="bi bi-trash3"></i> Sil</a>
                                         <a href="#" onclick="event.stopPropagation(); alert('Paylaşım ayarları eklenecek');"><i class="bi bi-share"></i> Paylaş</a>
                                    </div>
                                    <small class="text-muted-color">Son Güncelleme: <?php echo date("d.m.Y H:i", strtotime($makale['updated_at'])); ?></small> <!-- Bu bilgi de DB'den gelmeli -->
                                </div>
                            </div>
                            <?php endwhile; ?>
                        <?php endif; ?>
                        
                        <!-- Sayfalama (Eğer çok fazla makale varsa) -->
                        <?php //if(count($makaleler) > 0): // Örnek, gerçekte sayfalama mantığına göre ?>
                        <!-- <nav aria-label="Page navigation" class="mt-4 d-flex justify-content-center">
                            <ul class="pagination shadow-sm">
                                <li class="page-item disabled"><a class="page-link" href="#" style="background-color: var(--input-bg); color:var(--text-muted-color); border-color:var(--input-border);">Önceki</a></li>
                                <li class="page-item active"><a class="page-link" href="#" style="background-color: var(--accent-color-1); border-color:var(--accent-color-1); color:white;">1</a></li>
                                <li class="page-item"><a class="page-link" href="#" style="background-color: var(--input-bg); color:var(--link-color); border-color:var(--input-border);">2</a></li>
                                <li class="page-item"><a class="page-link" href="#" style="background-color: var(--input-bg); color:var(--link-color); border-color:var(--input-border);">3</a></li>
                                <li class="page-item"><a class="page-link" href="#" style="background-color: var(--input-bg); color:var(--link-color); border-color:var(--input-border);">Sonraki</a></li>
                            </ul>
                        </nav> -->
                        <?php// endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

       <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
       <script src="assets/js/script.js"></script>
</body>
</html>