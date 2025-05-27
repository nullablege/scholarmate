<?php
require 'vendor/autoload.php';


use Symfony\Component\DomCrawler\Crawler;
use Smalot\PdfParser\Parser;



function validateAndHashPassword(string $password, string $password2): string|false {
    if ($password !== $password2) {
        return false; 
    }

    $length = strlen($password) >= 8;
    $lowercase = preg_match('/[a-z]/', $password);
    $uppercase = preg_match('/[A-Z]/', $password);
    $number = preg_match('/\d/', $password);
    $special = preg_match('/[^A-Za-z0-9]/', $password); 

    if ($length && $lowercase && $uppercase && $number && $special) {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    return false; 
}

function getEmailRecordCount(mysqli $conn, string $email): int|false {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM uye WHERE e_posta = ?"); // Tablo adı 'users' olarak varsayıldı, kendi tablonuza göre değiştirin
    if (!$stmt) {
        // Hazırlama hatası
        error_log("getEmailRecordCount prepare failed: " . $conn->error); // Hata loglama
        return false;
    }

    $stmt->bind_param("s", $email);
    if (!$stmt->execute()) {
        error_log("getEmailRecordCount execute failed: " . $stmt->error); // Hata loglama
        $stmt->close();
        return false;
    }
    
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    return $count;
}

function kullaniciEkle(mysqli $conn, string $ad, string $soyad, string $unvan, string $kurum, string $arastirma_alani, string $email, string $hashli_sifre, ?string $avatar= null): bool {

    $stmt = $conn->prepare("INSERT INTO uye (ad, soyad, unvan, kurum, arastirma_alani, e_posta, sifre, avatar) VALUES (?, ?, ?, ?, ?, ?, ?, ?)"); // aktif=0 (onay bekliyor) veya aktif=1 (doğrudan aktif)
    if (!$stmt) {
        error_log("kullaniciEkle prepare failed: " . $conn->error); 
        return false;
    }

    $stmt->bind_param("ssssssss", $ad, $soyad, $unvan, $kurum, $arastirma_alani, $email, $hashli_sifre, $avatar);

    $sonuc = $stmt->execute();
    if (!$sonuc) {
        error_log("kullaniciEkle execute failed: " . $stmt->error); 
    }
    $stmt->close();

    return $sonuc;
}

function kullaniciDogruMu(mysqli $conn, string $email, string $sifre): bool 
{
    $sql = "SELECT ad, soyad, unvan, sifre, avatar,e_posta, id FROM uye WHERE e_posta = ?"; 

    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        error_log("MySQLi prepare failed for kullaniciDogruMu: (" . $conn->errno . ") " . $conn->error);
        return false;
    }

    $stmt->bind_param("s", $email);

    if (!$stmt->execute()) {
        error_log("MySQLi execute failed for kullaniciDogruMu: (" . $stmt->errno . ") " . $stmt->error);
        $stmt->close();
        return false;
    }

    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user_data = $result->fetch_assoc();
        $hashliSifre = $user_data['sifre'];
        
        if (password_verify($sifre, $hashliSifre)) {

            $_SESSION['unvan'] = $user_data['unvan'];
            $_SESSION['ad'] = $user_data['ad'] . " " . $user_data['soyad'];
            $_SESSION['login'] = 1;
            $_SESSION['avatar'] = $user_data['avatar'];
            $_SESSION['eposta'] = $user_data['e_posta'];
            $_SESSION['id'] = $user_data['id'];

            $stmt->close();
            return true; 
        }
    }
    
    $stmt->close();
    return false;
}


function getUserByEmail(mysqli $conn, string $eposta): ?array
{
    $sql = "SELECT id, ad, soyad, unvan, kurum, arastirma_alani, e_posta, sifre, olusturulma_tarihi, avatar FROM uye WHERE e_posta = ?";

    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        error_log("MySQLi prepare failed: (" . $conn->errno . ") " . $conn->error);
        return null;
    }

    $stmt->bind_param("s", $eposta);

    if (!$stmt->execute()) {
        error_log("MySQLi execute failed: (" . $stmt->errno . ") " . $stmt->error);
        $stmt->close();
        return null;
    }

    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        $stmt->close();
        return $user;
    } else {
        $stmt->close();
        return null;
    }
}

function resmiKaydet(string $fileInputName, string $uploadDirectory, array $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'], int $maxFileSize = 20971520): ?string
{
    if (!isset($_FILES[$fileInputName])) {
        error_log("Formda '$fileInputName' isimli bir dosya alanı bulunamadı.");
        return null;
    }

    $file = $_FILES[$fileInputName];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $phpFileUploadErrors = [
            UPLOAD_ERR_INI_SIZE   => 'Yüklenen dosya php.ini dosyasındaki upload_max_filesize direktifini aşıyor.',
            UPLOAD_ERR_FORM_SIZE  => 'Yüklenen dosya HTML formundaki MAX_FILE_SIZE direktifini aşıyor.',
            UPLOAD_ERR_PARTIAL    => 'Dosyanın sadece bir kısmı yüklendi.',
            UPLOAD_ERR_NO_FILE    => 'Hiç dosya yüklenmedi.',
            UPLOAD_ERR_NO_TMP_DIR => 'Geçici klasör eksik.',
            UPLOAD_ERR_CANT_WRITE => 'Dosya diske yazılamadı.',
            UPLOAD_ERR_EXTENSION  => 'Bir PHP eklentisi dosya yüklemesini durdurdu.',
        ];
        $errorMessage = $phpFileUploadErrors[$file['error']] ?? 'Bilinmeyen bir yükleme hatası oluştu.';
        
        if ($file['error'] !== UPLOAD_ERR_NO_FILE) {
            error_log("Dosya yükleme hatası ($fileInputName): " . $errorMessage);
        }
        return null; 
    }

    if ($file['size'] > $maxFileSize) {
        error_log("Dosya boyutu çok büyük ($fileInputName): " . $file['size'] . " > " . $maxFileSize);
        return null;
    }

    $fileName = basename($file['name']); 
    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    if (!in_array($fileExtension, $allowedExtensions)) {
        error_log("İzin verilmeyen dosya türü ($fileInputName): " . $fileExtension);
        return null;
    }

    if (!is_dir($uploadDirectory)) {
        if (!mkdir($uploadDirectory, 0755, true)) { 
            error_log("Yükleme klasörü oluşturulamadı veya mevcut değil ($fileInputName): " . $uploadDirectory);
            return null;
        }
    }
    if (!is_writable($uploadDirectory)) {
        error_log("Yükleme klasörüne yazma izni yok ($fileInputName): " . $uploadDirectory);
        return null;
    }
    
    $safeFileName = preg_replace("/[^a-zA-Z0-9-_\.]/", "", str_replace(" ", "_", $fileName)); 
    $newFileName = time() . '_' . uniqid() . '_' . $safeFileName; 

    $targetFilePath = rtrim($uploadDirectory, '/') . '/' . $newFileName;

    if (move_uploaded_file($file['tmp_name'], $targetFilePath)) {
      
        return ltrim(str_replace($_SERVER['DOCUMENT_ROOT'], '', $targetFilePath), '/'); // DOCUMENT_ROOT'u kaldırıp göreli yapar
        
    } else {
        error_log("move_uploaded_file başarısız oldu ($fileInputName): " . $targetFilePath);
        return null;
    }
}

function kullaniciSifreGuncelle(mysqli $conn, string $eposta, string $yeni_sifre_hash): bool {
    $sql = "UPDATE uye SET sifre = ? WHERE e_posta = ?";

    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        error_log("MySQLi prepare error for password update: " . $conn->error);
        return false;
    }

    $stmt->bind_param("ss", $yeni_sifre_hash, $eposta);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            $stmt->close();
            return true; 
        } else {
            $stmt->close();
            error_log("Password update for $eposta: No rows affected (user might not exist or password was the same).");
            return false; 
        }
    } else {
        error_log("MySQLi execute error for password update: " . $stmt->error);
        $stmt->close();
        return false;
    }
}

function pdfText($pdfYolu) {
    $parser = new Parser();

    $pdf = $parser->parseFile($pdfYolu);

    $metin = $pdf->getText();

    $metin = preg_replace('/\s+/', ' ', $metin); 
    $metin = trim($metin); 



    
    return $metin;
}
function txtText($txtYolu){
    $txtIcerik = file_get_contents($txtYolu);
    echo nl2br(htmlspecialchars($txtIcerik));
}

function pdfYukle($yuklenenDosya, $hedefKlasor = 'assets/uploads/pdf/') {
    if ($yuklenenDosya['error'] !== UPLOAD_ERR_OK) {
        error_log("Dosya yükleme hatası: " . $yuklenenDosya['error']);
        return false;
    }

    $maxBoyut = 25 * 1024 * 1024;
    if ($yuklenenDosya['size'] > $maxBoyut) {
        error_log("Dosya boyutu limiti aşıldı: " . $yuklenenDosya['size']);
        return false;
    }

    $izinliTipler = ['application/pdf' => 'pdf'];
    $dosyaInfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $dosyaInfo->file($yuklenenDosya['tmp_name']);

    if (!array_key_exists($mime, $izinliTipler)) {
        error_log("Geçersiz dosya tipi: $mime");
        return false;
    }

    $orijinalAd = pathinfo($yuklenenDosya['name'], PATHINFO_FILENAME);
    $guvenliAd = preg_replace("/[^a-zA-Z0-9_-]/", "", $orijinalAd); // Özel karakterleri temizle
    $dosyaUzantisi = $izinliTipler[$mime];
    $yeniDosyaAdi = $guvenliAd . '_' . uniqid() . '.' . $dosyaUzantisi;
    
    if (!file_exists($hedefKlasor)) {
        return false;
    }

    $hedefYol = $hedefKlasor . $yeniDosyaAdi;
    
    if (move_uploaded_file($yuklenenDosya['tmp_name'], $hedefYol)) {
        return $hedefYol;
    } else {
        error_log("Dosya taşıma hatası: " . $yuklenenDosya['tmp_name']);
        return false;
    }
}


/**
 * Google Generative Language API ile verilen metni özetler.
 *
 * @param string $text   Özetlenmek istenen metin.
 * @param string $apiKey Google API anahtarın (GEMINI_API_KEY).
 * @return string        API’den dönen özet metni.
 * @throws Exception     cURL hatası veya API’den hata mesajı gelmesi durumunda.
 */
function Ozetle(string $text, string $apiKey="AIzaSyA8Gu_94qwQEdvE64JiTnYNLJp-VLXPJH4"): string
{
    $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key={$apiKey}";
    $headers = [
        'Content-Type: application/json'
    ];

    $payload = [
        'contents' => [
            [
                'parts' => [
                    [
                        'text' => "Lütfen aşağıdaki metni anlamını kaybetmeyecek şekilde ve bunun bir akademik makale olduğunu bilerek özetle. Yanıt olarak sadece özeti yaz başka birşey yazma.:\n\n" . $text ." \n NOT : Benim doldurmam için bir yerleri boş bırakma. Mesela [Makale Konusu] gibi değişkenler kullanma. Direkt olarak makale sayfasında kullanacağım için buna müdahale edemem"
                    ]
                ]
            ]
        ]
    ];
    $jsonPayload = json_encode($payload, JSON_UNESCAPED_UNICODE);

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_HTTPHEADER     => $headers,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POSTFIELDS     => $jsonPayload,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_TIMEOUT        => 30,
    ]);

    $response = curl_exec($ch);
    if ($response === false) {
        $err = curl_error($ch);
        curl_close($ch);
        throw new Exception("cURL Error: {$err}");
    }
    curl_close($ch);

    $data = json_decode($response, true, 512, JSON_THROW_ON_ERROR);

    if (isset($data['error'])) {
        $msg = $data['error']['message'] ?? 'Bilinmeyen API hatası';
        throw new Exception("API Error: {$msg}");
    }

  
    if (
        isset($data['candidates'][0]['content']['parts'][0]['text'])
        && is_string($data['candidates'][0]['content']['parts'][0]['text'])
    ) {
        return trim($data['candidates'][0]['content']['parts'][0]['text']);
    }

    throw new Exception("API yanıtı beklenen formatta değil.");
}

function TekrarOzetle(string $text, string $apiKey="AIzaSyA8Gu_94qwQEdvE64JiTnYNLJp-VLXPJH4"): string
{
    $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key={$apiKey}";
    $headers = [
        'Content-Type: application/json'
    ];

    $payload = [
        'contents' => [
            [
                'parts' => [
                    [
                        'text' =>"Benim için akademik bir makale içeriği olan aşağıya bıraktığım metni özetlemiştin. (Özetini bırakacağım.) Fakat bu özeti pek beğenmedim. \n $text \n Benim için akademik bir makale olduğunu göz önünde bulundurarak tekrardan daha farklı şekilde özetler misin ? Sadece özeti yazmanı istiyorum başka bir cevap verme. Cevabında sadece özet olsun. NOT : Benim doldurmam için bir yerleri boş bırakma. Mesela [Makale Konusu] gibi değişkenler kullanma. Direkt olarak makale sayfasında kullanacağım için buna müdahale edemem."
                    ]
                ]
            ]
        ]
    ];
    $jsonPayload = json_encode($payload, JSON_UNESCAPED_UNICODE);

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_HTTPHEADER     => $headers,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POSTFIELDS     => $jsonPayload,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_TIMEOUT        => 30,
    ]);

    $response = curl_exec($ch);
    if ($response === false) {
        $err = curl_error($ch);
        curl_close($ch);
        throw new Exception("cURL Error: {$err}");
    }
    curl_close($ch);

    $data = json_decode($response, true, 512, JSON_THROW_ON_ERROR);

    if (isset($data['error'])) {
        $msg = $data['error']['message'] ?? 'Bilinmeyen API hatası';
        throw new Exception("API Error: {$msg}");
    }

   
    if (
        isset($data['candidates'][0]['content']['parts'][0]['text'])
        && is_string($data['candidates'][0]['content']['parts'][0]['text'])
    ) {
        return trim($data['candidates'][0]['content']['parts'][0]['text']);
    }

    throw new Exception("API yanıtı beklenen formatta değil.");
}



function uyeBilgisiGetir($id) {
    try {
        $pdo = $conn;
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $pdo->prepare("SELECT unvan, ad, soyad FROM uyeler WHERE id = ?");
        $stmt->execute([$id]);

        $uye = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($uye) {
            return $uye['unvan'] . ' ' . $uye['ad'] . ' ' . $uye['soyad'];
        } else {
            return "Üye bulunamadı.";
        }
    } catch (PDOException $e) {
        return "Hata: " . $e->getMessage();
    }
}

function uyeBilgisiGetirMail($id) {
    $host = "localhost";
    $username = "root";
    $password = "";
    $db = "makale";

    $conn = new mysqli($host, $username, $password, $db);

    if ($conn->connect_error) {
        return "Veritabanı bağlantı hatası: " . $conn->connect_error;
    }

    $stmt = $conn->prepare("SELECT unvan, ad, soyad, e_posta FROM uyeler WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    $result = $stmt->get_result();
    $uye = $result->fetch_assoc();

    $stmt->close();
    $conn->close();

    if ($uye) {
        return $uye['unvan'] . ' ' . $uye['ad'] . ' ' . $uye['soyad'] . ' - ' . $uye['e_posta'];
    } else {
        return "Üye bulunamadı.";
    }
}





?>



