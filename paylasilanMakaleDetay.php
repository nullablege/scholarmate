<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php_errors.log');



if(!isset($_SESSION['id'])){
    header("location:login.php");
}


require "assets/config/db.php";
require "includes/function.php";


$duzenlenecek_id = intval($_GET['id']);
if(empty($_GET['id'])){
    //header("location:index.php");
}
$id = $_SESSION['id'];

//Makale Var ve Kullanıcının mı ?
$query = "select * from makaleler where id='$duzenlenecek_id' AND kullanici_id='$id';";
$result = mysqli_query($conn,$query);
if(mysqli_num_rows($result) == 1){
    //makale var 
    
}
else{
    //Makale yok, paylaşılmışsa işlem yap
    $query = "select * from makale_paylasim where paylasilan_id='$id' AND makale_id='$duzenlenecek_id';";
    $result = mysqli_query($conn,$query);
    if(!mysqli_num_rows($result) == 1){
        header("location:index.php");
    }
    else{
        $query = "select * from makaleler where id='$duzenlenecek_id';";
        $result = mysqli_query($conn,$query);
    }
}
$makale_detay = mysqli_fetch_assoc($result);
$sayfa_basligi = $makale_detay['baslik']." | Scholar Mate ";

$kategoriler_str = "";
$kQuery = "select * from makale_kategori where makale_id='$duzenlenecek_id';";
$kResult = mysqli_query($conn,$kQuery);
while($row = mysqli_fetch_assoc($kResult)){
    $kategoriler_str = $kategoriler_str.", ".$row['kategori'];
}

$etiketler_str = "";
$eQuery = "select * from makale_etiket where makale_id='$duzenlenecek_id';";
$eResult = mysqli_query($conn,$eQuery);
while($row = mysqli_fetch_assoc($eResult)){
    $etiketler_str = $etiketler_str.", ".$etiketler_str;
}

$notlarQuery = "select * from notlar where makale_id='$duzenlenecek_id';";
$makale_notlari = mysqli_query($conn, $notlarQuery);

$notCount = "SELECT COUNT(*) AS toplam FROM notlar WHERE makale_id='$duzenlenecek_id'";
$notCountResult = mysqli_query($conn, $notCount);

if($row = mysqli_fetch_assoc($notCountResult)){
    $notSayac = $row['toplam'];
} else {
    $notSayac = 0;
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
    <link rel="stylesheet" href="assets/css/style.css">    
</head>
<body>
    <button id="themeToggleBtn" class="btn theme-toggle-button" title="Temayı Değiştir">
        <i class="bi bi-moon-stars-fill"></i>
    </button>

    <?php require "templates/partials/navbar.php"; ?>

    <div class="container-fluid" id="main-bg">
        <div class="container py-4">
            <div class="article-detail-card animate-fade-in-down">
                <!-- Sayfa Başı Aksiyonları -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                     <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 bg-transparent p-0" style="font-size: 0.9rem;">
                            <li class="breadcrumb-item"><a href="index.php" style="color:var(--link-color)">Makalelerim</a></li>
                            <li class="breadcrumb-item active" aria-current="page" style="color:var(--text-muted-color)"><?php echo htmlspecialchars(substr($makale_detay['baslik'], 0, 50)) . (strlen($makale_detay['baslik']) > 50 ? '...' : ''); ?></li>
                        </ol>
                    </nav>
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-outline-custom" title="PDF Olarak Dışa Aktar"><i class="bi bi-file-earmark-pdf-fill me-1"></i> Dışa Aktar</button>
                        <a href="" disabled class="btn btn-sm btn-action-filled" title="Makaleyi Düzenle"><i class="bi bi-pencil-square me-1"></i> Düzenle</a>
                        <button class="btn btn-sm btn-danger" disabled title="Makaleyi Sil" onclick="confirm('Bu makaleyi silmek istediğinizden emin misiniz?')"><i class="bi bi-trash3-fill"></i></button>
                    </div>
                </div>

                <h1 class="article-title"><?php echo htmlspecialchars($makale_detay['baslik']); ?></h1>
                
                <!-- Metadata Grid -->
                <div class="article-metadata-grid">
                    <div class="metadata-item">
                        <span class="label">Yazarlar</span>
                        <span class="value" id="yazarlar" ><?php echo htmlspecialchars($makale_detay['yazarlar_raw']); ?></span>
                    </div>
                    <div class="metadata-item">
                        <span class="label">Yayın Yılı</span>
                        <span class="value" id="yayinyili" ><?php echo htmlspecialchars($makale_detay['yayin_yili']); ?></span>
                    </div>
                    <?php if(!empty($makale_detay['dergi_konferans_adi'])): ?>
                    <div class="metadata-item">
                        <span class="label">Dergi / Konferans</span>
                        <span class="value" id="dergi" ><?php echo htmlspecialchars($makale_detay['dergi_konferans']); ?></span>
                    </div>
                    <?php endif; ?>
                     <?php if(!empty($makale_detay['doi'])): ?>
                    <div class="metadata-item">
                        <span class="label">DOI</span>
                        <span class="value" id="doi" ><a href="https://doi.org/<?php echo htmlspecialchars($makale_detay['doi']); ?>" target="_blank"><?php echo htmlspecialchars($makale_detay['doi']); ?> <i class="bi bi-box-arrow-up-right small"></i></a></span>
                    </div>
                    <?php endif; ?>
                     <div class="metadata-item">
                        <span class="label">Dosya Türü</span>
                        <span class="value"><i class="bi <?php echo $makale_detay['dosya_tipi'] == 'PDF' ? 'bi-file-earmark-pdf' : 'bi-file-earmark-text'; ?> me-1"></i> <?php echo htmlspecialchars($makale_detay['dosya_tipi']); ?> (<?php echo htmlspecialchars($makale_detay['orijinal_dosya_adi']); ?>)</span>
                    </div>
                     <div class="metadata-item">
                        <span class="label">Yüklenme Tarihi</span>
                        <span class="value"><?php echo date("d.m.Y H:i", strtotime($makale_detay['created_at'])); ?></span>
                    </div>
                     <?php if(!empty($makale_detay['guncellenme_tarihi'])): ?>
                    <div class="metadata-item">
                        <span class="label">Son Güncelleme</span>
                        <span class="value"><?php echo date("d.m.Y H:i", strtotime($makale_detay['updated_at'])); ?></span>
                    </div>
                    <?php endif; ?> 
                     <?php if(!empty($kategoriler_str)): ?>
                     <div class="metadata-item">
                        <span class="label">Kategoriler</span>
                        <span class="value"><?php echo htmlspecialchars($kategoriler_str); ?> <a href="#" class="ms-1 small" title="Kategorileri Düzenle"><i class="bi bi-pencil"></i></a></span>
                    </div>
                     <?php endif; ?>
                     <?php if(!empty($etiketler_str)): ?>
                     <div class="metadata-item">
                        <span class="label">Anahtar Kelimeler / Etiketler</span>
                        <span class="value">
                            <?php 
                            $etiketler = explode(',', $etiketler_str);
                            foreach($etiketler as $etiket){
                                echo '<span class="badge rounded-pill me-1 mb-1" style="background-color: var(--input-bg); color: var(--text-muted-color); border: 1px solid var(--input-border); font-weight:400;">'.htmlspecialchars(trim($etiket)).'</span>';
                            }
                            ?>
                            <a href="#" class="ms-1 small" title="Etiketleri Düzenle"><i class="bi bi-pencil"></i></a>
                        </span>
                    </div>
                    <?php endif; ?>
                </div>

                <hr style="border-color: var(--section-divider-color); margin: 2rem 0;">

                <!-- Tabs: Makale İçeriği, Notlar, Atıflar, Paylaşım -->
                <ul class="nav nav-tabs nav-tabs-custom" id="articleTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="content-tab" data-bs-toggle="tab" data-bs-target="#content-tab-pane" type="button" role="tab" aria-controls="content-tab-pane" aria-selected="true"><i class="bi bi-file-text-fill me-2"></i>Makale İçeriği</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="summary-tab" data-bs-toggle="tab" data-bs-target="#summary-tab-pane" type="button" role="tab" aria-controls="summary-tab-pane" aria-selected="false"><i class="bi bi-blockquote-left me-2"></i>Özet</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="notes-tab" data-bs-toggle="tab" data-bs-target="#notes-tab-pane" type="button" role="tab" aria-controls="notes-tab-pane" aria-selected="false"><i class="bi bi-journal-bookmark-fill me-2"></i>Notlarım (<?php echo $notSayac; ?>)</button>
                    </li>
                     <li class="nav-item" role="presentation">
                        <button class="nav-link" id="citation-tab" data-bs-toggle="tab" data-bs-target="#citation-tab-pane" type="button" role="tab" aria-controls="citation-tab-pane" aria-selected="false"><i class="bi bi-quote me-2"></i>Atıf Oluştur</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="share-tab" data-bs-toggle="tab" data-bs-target="#share-tab-pane" type="button" role="tab" aria-controls="share-tab-pane" aria-selected="false"><i class="bi bi-share-fill me-2"></i>Paylaşım Yönetimi</button>
                    </li>
                </ul>

                <div class="tab-content" id="articleTabContent">
                    <!-- Makale İçeriği Tab -->
                    <div class="tab-pane fade show active" id="content-tab-pane" role="tabpanel" aria-labelledby="content-tab" tabindex="0">
                        <div class="section-content">
                             <?php if($makale_detay['dosya_tipi'] == 'PDF'): ?>
                                <div class="pdf-viewer-placeholder" style="width: 100%; height: 100vh;">
                                    <iframe 
                                        src="<?php echo $makale_detay['saklanan_dosya_yolu'];?>"
                                        width="100%"
                                        height="100%"
                                        style="border:1px solid var(--input-border); border-radius:8px;"
                                    ></iframe>
                                </div>
                               
                            <?php else: // TXT vs. için basit gösterim ?>
                                <pre style="white-space: pre-wrap; word-wrap: break-word; background-color: var(--input-bg); padding:1rem; border-radius:8px; border:1px solid var(--input-border); max-height:600px; overflow-y:auto;">Makale metni burada gösterilecek. Dosya içeriği: <?php echo htmlspecialchars($makale_detay['saklanan_dosya_yolu']); ?></pre>
                                <a href="<?php echo htmlspecialchars($makale_detay['saklanan_dosya_yolu']); ?>" download="<?php echo htmlspecialchars($makale_detay['orijinal_dosya_adi']); ?>" class="btn btn-sm btn-outline-custom mt-2"><i class="bi bi-download me-1"></i> Dosyayı İndir</a>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Özet Tab -->

                    <div class="tab-pane fade" id="summary-tab-pane" role="tabpanel" aria-labelledby="summary-tab" tabindex="0">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="section-title mb-0">Özet</h5>
                            <div>
                                <button class="btn btn-sm btn-action-filled" disabled type="button" id="eskiOzetOto" title="Özeti Manuel Düzenle"><i class="bi bi-pencil-fill"></i> Eski Özete Dön</button>
                                <form style="display: inline;" action="includes/yeniOzet.php" method="POST">
                                <button class="btn btn-sm btn-outline-custom me-2" disabled type="submit" title="Otomatik Özeti Yeniden Oluştur"><i class="bi bi-arrow-repeat"></i> Yeniden Oluştur</button>
                                <input readonly type="hidden" name="makaleId2" value="<?php echo $duzenlenecek_id;?>">   
                                </form>
                                <form style="display: inline;" action="includes/ozetDuzenle.php" method="POST">
                                <button class="btn btn-sm btn-action-filled" disabled type="submit" title="Özeti Manuel Düzenle"><i class="bi bi-pencil-fill"></i> Kaydet</button>
                                
                            </div>
                        </div>
                        <div class="section-content p-3 rounded" style="background-color: var(--input-bg); border: 1px solid var(--input-border);">
                            <textarea readonly  name="manuelOzet" id="yeniOzet" rows="6" class="form-control" style="resize: vertical;"><?php echo htmlspecialchars($makale_detay['otomatik_ozet']); ?></textarea>
                            <input readonly type="hidden" name="makaleId" value="<?php echo $duzenlenecek_id;?>">
                            <input readonly type="hidden" name="eskiOzet" value="<?php echo $makale_detay['eski_ozet']; ?>">
                        </div>
                    </div>
                    </form>

                    <!-- Notlarım Tab -->
                    <div class="tab-pane fade" id="notes-tab-pane" role="tabpanel" aria-labelledby="notes-tab" tabindex="0">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="section-title mb-0">Kişisel Notlarım</h5>
                            <button class="btn btn-sm btn-action-filled" disabled data-bs-toggle="modal" data-bs-target="#addNoteModal"><i class="bi bi-plus-lg"></i> Yeni Not Ekle</button>
                        </div>
                        <div class="section-content">

                            <?php if (empty($makale_notlari)): ?>
                                <p class="text-muted-color">Bu makale için henüz not eklenmemiş.</p>
                            <?php else: ?>
                                <?php while($not = mysqli_fetch_assoc($makale_notlari) ):?>
                                <div class="note-card">
                                    <p class="note-content"><?php echo nl2br(htmlspecialchars($not['icerik'])); ?></p>
                                    <div class="note-meta d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="timestamp"><i class="bi bi-clock-fill me-1"></i><?php echo date("d.m.Y H:i", strtotime($not['guncellenme_tarihi'])); ?></span>
                                            <?php if (!empty($not['referans'])): ?>
                                            <span class="reference"><i class="bi bi-bookmark-fill me-1"></i>Referans: <?php echo htmlspecialchars($not['referans']); ?></span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="note-actions">
                                            <a href="#" class="text-primary me-2" title="Düzenle"><i class="bi bi-pencil-square"></i></a>
                                            <a href="#" class="text-danger" title="Sil"><i class="bi bi-trash3"></i></a>
                                        </div>
                                    </div>
                                </div>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Atıf Oluştur Tab -->
                    <div class="tab-pane fade" id="citation-tab-pane" role="tabpanel" aria-labelledby="citation-tab" tabindex="0">
                        <h5 class="section-title">Atıf Oluşturucu</h5>
                        <div class="section-content">
                            <div class="mb-3">
                                <label for="citationFormat" class="form-label">Atıf Formatı Seçin:</label>
                                <select class="form-select form-select-sm" id="citationFormat" style="background-color:var(--input-bg); color:var(--text-color); border-color:var(--input-border);">
                                    <option value="apa">APA 7th Edition</option>
                                    <option value="mla">MLA 9th Edition</option>
                                    <option value="chicago">Chicago Manual of Style 17th Edition</option>
                                    <option value="bibtex">BibTeX</option>
                                </select>
                            </div>
                            <div class="generated-citation p-3 rounded mb-3" style="background-color:var(--input-bg); border: 1px solid var(--input-border); font-family: 'Courier New', Courier, monospace; font-size:0.9rem; white-space:pre-wrap; word-break:break-all;">
                                Atıf burada görüntülenecek... (Örn: Arga, D. K., & Bilgin, E. (2023). Yapay Zeka Etik İlkeleri Üzerine Kapsamlı Bir İnceleme. Journal of Artificial Intelligence Research, X(Y), ss-ss. https://doi.org/10.1613/jair.1234)
                            </div>
                            <button class="btn btn-sm btn-outline-custom" onclick="copyCitation()"><i class="bi bi-clipboard-check-fill me-1"></i> Kopyala</button>
                            <button class="btn btn-sm btn-outline-custom ms-2" onclick="exportCitation()"><i class="bi bi-download me-1"></i> .txt Olarak Dışa Aktar</button>
                        </div>
                    </div>

                    <!-- Paylaşım Yönetimi Tab -->
                     <form action="includes/paylasimEkle.php" method="POST">
                        <div class="tab-pane fade" id="share-tab-pane" role="tabpanel" aria-labelledby="share-tab" tabindex="0">
                                <h5 class="section-title">Makaleyi Başkalarıyla Paylaş</h5>
                                <div class="section-content">
                                    <p class="text-muted-color small">Bu makaleyi platformdaki diğer kullanıcılarla farklı izin seviyelerinde paylaşabilirsiniz.</p>
                                    <div class="search-container mb-3">
                                        <div class="input-group">
                                            <input readonly type="text" name="eklenecekMail" class="form-control" id="userSearchInput" placeholder="E-posta veya kullanıcı adı ile ara..." autocomplete="off" style="background-color: var(--input-bg); color:var(--text-color); border-color:var(--input-border);">
                                            <input readonly type="hidden" name="makaleId" value="<?php echo $duzenlenecek_id; ?>">
                                            <select class="form-select flex-grow-0" id="permissionLevelSelect" style="width: auto; background-color: var(--input-bg); color:var(--text-color); border-color:var(--input-border);">
                                                <option value="read">Okuma İzni</option>
                                                <!-- <option value="comment">Yorum Yapma İzni</option>
                                                <option value="edit">Düzenleme İzni</option> -->
                                            </select>
                                            <button class="btn btn-action-filled" type="button"  disabled onclick="addUserToShare()"><i class="bi bi-person-plus-fill me-1"></i>Kullanıcı Ekle</button>
                                        </div>
                                        <div class="search-results" id="userSearchResults">
                                            <!-- JS ile doldurulacak örnek arama sonucu -->
                                            <!-- <div class="search-item" data-email="test@example.com">Test User (test@example.com)</div> -->
                                        </div>
                                    </div>
                                </form>  

                                <?php
                                $paylasilanlarQuery = "select * from makale_paylasim where makale_id='$duzenlenecek_id';";
                                $paylasilanlarResult = mysqli_query($conn,$paylasilanlarQuery);

                                ?>
                                <h6>Şu Anda Paylaşılanlar:</h6>
                                 
                                <div id="sharedUserList">
                                    <?php if(empty($paylasilanlarResult)): ?>
                                        <p class="text-muted-color">Bu makale henüz kimseyle paylaşılmamış.</p>
                                    <?php else: ?>
                                        <?php while($paylasilan = mysqli_fetch_assoc($paylasilanlarResult)):?>
                                        <div class="user-pill">
                                            <i class="bi bi-person-circle me-1"><?php echo " ".$paylasilan['paylasilan_eposta']?></i> 
                                            <span class="permission">(Okuma)</span>
                                            <?php
                                            $pid = $paylasilan['id'];
                                            ?>
                                            <a href="<?php  "includes/paylasimSil.php?makale=$duzenlenecek_id&id=$pid"?>"><button class="remove-user-btn" type="button" title="Paylaşımı Kaldır"><i class="bi bi-x-lg"></i></button></a>
                                            </div>
                                        <?php endwhile; ?>
                                    <?php endif; ?>
                                </div>
                               
                            </div>
                        </div>
                </div> <!-- Tab content sonu -->
            </div> <!-- Article detail card sonu -->
        </div> <!-- Container sonu (main-bg içinde) -->
    </div> <!-- Main-bg sonu -->

    <!-- Yeni Not Ekleme Modalı -->
    <div class="modal fade" id="addNoteModal" tabindex="-1" aria-labelledby="addNoteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="background-color:var(--content-card-bg); border-color:var(--input-border); color:var(--text-color);">
                <div class="modal-header" style="border-bottom-color:var(--input-border);">
                    <h5 class="modal-title" id="addNoteModalLabel"><i class="bi bi-journal-plus me-2"></i>Yeni Not Ekle</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="filter: var(--btn-close-filter);"></button>
                </div>
                <div class="modal-body">

                    <form action="includes/notEkle.php"id="addNoteForm" method="POST">
                        <div class="mb-3">
                            <label for="noteContent" class="form-label">Not İçeriği:</label>
                            <textarea readonly class="form-control" name="icerik" id="noteContent" rows="5" required style="background-color:var(--input-bg); color:var(--text-color); border-color:var(--input-border);"></textarea>
                                        <input readonly type="hidden" name="makaleid" value="<?php echo $duzenlenecek_id; ?>">
                        </div>
                        <div class="mb-3">
                            <label for="noteReference" class="form-label">Referans (Sayfa/Paragraf - Opsiyonel):</label>
                            <input readonly type="text"  name="referans "class="form-control" id="noteReference" placeholder="Örn: s.12, p.3" style="background-color:var(--input-bg); color:var(--text-color); border-color:var(--input-border);">
                        </div>
                    
                </div>
                <div class="modal-footer" style="border-top-color:var(--input-border);">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="border-color:var(--text-muted-color); color:var(--text-muted-color);">İptal</button>
                    <button type="submit" class="btn btn-action-filled" ><i class="bi bi-save-fill me-1"></i>Notu Kaydet</button>
                </div>
                </form>
            </div>
        </div>
    </div>

        <!-- ... (Sayfanızın önceki HTML içeriği, modal dahil) ... -->

    <!-- Bootstrap JS Bundle (Popper.js içerir) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="assets/js/script.js"></script>
    <script>
    function paylasimSil(makaleId, id){
        window.location = "";
    }
    var ozetSayac = 1;
    let yedekDeger = "";
    
    function ozetDegistir() {
        eski = document.querySelector('#eskiOzetOto');
        yeni = document.querySelector('#yeniOzet');
    if (ozetSayac % 2 == 1) {
        yedekDeger = yeni.value;
        yeni.value = eski.value;
        ozetSayac++;
    } else {
        yeni.value = yedekDeger;
        eski.value = yeni.value;
        ozetSayac++;
    }
    
    }

    document.querySelector('#eskiOzetOto').addEventListener('click',()=>{
        ozetDegistir()
        
    });

    function copyCitation() {
        const citationText = document.querySelector('.generated-citation').textContent;
        
        navigator.clipboard.writeText(citationText)
            .then(() => alert('Atıf panoya kopyalandı!'))
            .catch(err => alert('Kopyalama başarısız: ' + err));
    }

    function exportCitation() {
    const citationText = document.querySelector('.generated-citation').textContent;
    const blob = new Blob([citationText], { type: 'text/plain' });
    const url = URL.createObjectURL(blob);

    const link = document.createElement('a');
    link.href = url;
    const format = document.getElementById('citationFormat').value;
    link.download = format+"Formatı.txt";
    link.click();

    URL.revokeObjectURL(url);
}


    document.getElementById('citationFormat').addEventListener('change', generateCitation);

    function generateCitation() {
    const format = document.getElementById('citationFormat').value;

    // Yazarlar
    let authorsSpan = document.getElementById('yazarlar');
    let authors = authorsSpan ? authorsSpan.textContent.trim() : 'Yazar Bilgisi Yok';

    // Başlık
    let titleEl = document.querySelector('.article-title');
    let title = titleEl ? titleEl.textContent.trim() : 'Başlık Yok';

    // Yayın Yılı
    let yearEl = document.getElementById('yayinyili');
    let year = yearEl ? yearEl.textContent.trim() : 'Tarih Yok';

    // Dergi
    let journalEl = document.getElementById('dergi');
    let journal = journalEl ? journalEl.textContent.trim() : 'Kaynak Bilgisi Yok';

    // DOI
    let doiEl = document.getElementById('doi');
    let doi = doiEl ? doiEl.textContent.trim() : '';

    // Ortak sabitler
    const volume = "I";
    const issue = "I";

    // Atıf metni
    let citation = '';

    switch (format) {
        case 'apa':
            citation = `${authors} (${year}). ${title}. *${journal}*, ${volume}(${issue}). ${doi}`;
            break;

        case 'mla':
            citation = `${authors}. "${title}." *${journal}*, vol. ${volume}, no. ${issue}, ${year}. ${doi}`;
            break;

        case 'chicago':
            citation = `${authors}. "${title}." *${journal}* ${volume}, no. ${issue} (${year}). ${doi}`;
            break;

        case 'bibtex':
            citation = `@article{citation,
            author = {${authors}},
            title = {${title}},
            journal = {${journal}},
            year = {${year}},
            volume = {${volume}},
            number = {${issue}},
            doi = {${doi}}
            }`;
                        break;

        default:
            citation = 'Geçerli bir format seçilmedi.';
    }

    document.querySelector('.generated-citation').textContent = citation;
}
    </script>
</body>
</html>