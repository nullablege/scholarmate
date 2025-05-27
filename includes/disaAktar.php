
<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
session_start();
// Composer autoload dosyasını dahil et
require_once '../vendor/autoload.php';
require_once "../assets/config/db.php";

// Örnek Makale Verisi (Normalde bu veriyi veritabanından veya bir POST/GET isteği ile alırsınız)
// $_GET['makale_id'] gibi bir parametre ile ID'yi alıp veritabanından ilgili makaleyi çekebilirsiniz.
// Bu örnekte, sizin verdiğiniz array yapısını doğrudan kullanıyorum.

if(!isset($_GET['id'])){
    header("location:../index.php");
}
else{
    $Mid = intval($_GET['id']);
}
$kullanici = $_SESSION['id'];

$query = "select * from makaleler where id=$Mid and kullanici_id=$kullanici;";
$result = mysqli_query($conn,$query);

if(mysqli_num_rows($result)==1){
    $result = mysqli_fetch_assoc($result);
}
else{
    header("location:../index.php");
}

$makale_verisi = [ 
    'id' => $result['id'],
    'baslik' => $result['baslik'],
    'yayin_yili' => $result['yayin_yili'],
    'dergi_konferans' => $result['dergi_konferans'],
    'doi' => $result['doi'],
    'ozet_manuel' => $result['ozet_manuel'],
    'yazarlar_raw' => $result['yazarlar_raw'],
    'anahtar_kelimeler_raw' => $result['anahtar_kelimeler_raw'],
    'makale_icerigi' => $result['makale_icerigi'],
    'orijinal_dosya_adi' => $result['orijinal_dosya_adi'],
    'saklanan_dosya_yolu' => $result['saklanan_dosya_yolu'],
    'kullanici_id' => $result['kullanici_id'],
    'created_at' => $result['created_at'],
    'updated_at' => $result['updated_at'],
    'otomatik_ozet' => $result['otomatik_ozet'],
    'eski_ozet' => $result['eski_ozet']
];
$sayfa_basligi = $result['baslik']." | Scholar Mate ";
$mpdf = new \Mpdf\Mpdf([
    'default_font' => 'dejavusans',
    'format' => 'A4'
]);

$icerik = nl2br(htmlspecialchars($makale_verisi['makale_icerigi'])); // satır sonlarını koru

// PDF içeriği
$html = <<<HTML
<title>$sayfa_basligi</title>
<style>
    body { font-family: dejavusans; font-size: 12pt; }
    h1 { color: #2c3e50; font-size: 18pt; border-bottom: 1px solid #ccc; padding-bottom: 5px; }
    h2 { color: #34495e; font-size: 14pt; margin-top: 20px; }
    p { margin: 5px 0; }
    .meta { background: #f5f5f5; padding: 10px; border-radius: 6px; margin-bottom: 20px; }
    .label { font-weight: bold; color: #555; width: 180px; display: inline-block; }
</style>

<h1>{$makale_verisi['baslik']}</h1>

<div class="meta">
    <p><span class="label">Yayın Yılı:</span> {$makale_verisi['yayin_yili']}</p>
    <p><span class="label">Dergi / Konferans:</span> {$makale_verisi['dergi_konferans']}</p>
    <p><span class="label">DOI:</span> {$makale_verisi['doi']}</p>
    <p><span class="label">Yazarlar:</span> {$makale_verisi['yazarlar_raw']}</p>
    <p><span class="label">Anahtar Kelimeler:</span> {$makale_verisi['anahtar_kelimeler_raw']}</p>
    <p><span class="label">Kullanıcı ID:</span> {$makale_verisi['kullanici_id']}</p>
    <p><span class="label">Oluşturulma:</span> {$makale_verisi['created_at']}</p>
    <p><span class="label">Güncellenme:</span> {$makale_verisi['updated_at']}</p>
</div>

<h2>Manuel Özet</h2>
<p>{$makale_verisi['ozet_manuel']}</p>

<h2>Otomatik Özet</h2>
<p>{$makale_verisi['otomatik_ozet']}</p>

<h2>Makale İçeriği</h2>
<p>{$icerik}</p>
HTML;

// PDF'e yaz
$mpdf->WriteHTML($html);

// Tarayıcıya gönder (veya dosya olarak kaydet)
$mpdf->Output($makale_verisi['orijinal_dosya_adi'], \Mpdf\Output\Destination::INLINE); // veya FILE

header("location:ege.php");
?>

<head><title><?php echo $sayfa_basligi?></title></head>
