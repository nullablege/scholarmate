<?php



session_start(); 


if (!isset($_SESSION['login'])) {
    // Kullanıcı giriş yapmamışsa, AJAX isteği için yetkisiz yanıtı döndür
    http_response_code(401); // Unauthorized
    echo json_encode(['success' => false, 'message' => 'Bu işlemi yapmak için giriş yapmalısınız.']);
    exit;
}



$host = "localhost";
$username = "root";
$password = "";
$db_name = "makale"; // Veritabanı adı için farklı bir değişken kullanmak daha iyi
$charset = "utf8mb4"; // utf8mb4, emojiler gibi daha geniş karakter setlerini destekler

// Veritabanı bağlantısı için DSN (Data Source Name)
$dsn = "mysql:host=$host;dbname=$db_name;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Hataları exception olarak fırlatır
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Varsayılan fetch modunu assoc array yap
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Gerçek prepared statement'ları kullan
];

try {
    $pdo = new PDO($dsn, $username, $password, $options); // PDO bağlantı nesnesini $pdo olarak adlandıralım
} catch (\PDOException $e) {
    // Geliştirme aşamasında hatayı loglayın veya gösterin
    // error_log("Veritabanı bağlantı hatası: " . $e->getMessage());
    // die("Veritabanına bağlanılamadı. Lütfen daha sonra tekrar deneyin."); // Kullanıcıya genel bir mesaj
    throw new \PDOException($e->getMessage(), (int)$e->getCode()); // Veya hatayı yeniden fırlat
}

header('Content-Type: application/json');

// Güncellenmiş db.php dosyasını dahil et (artık $pdo değişkenini sağlayacak)

$response = ['success' => false, 'message' => 'Bilinmeyen bir hata oluştu.'];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['makale_id'])) {
    $makale_id = filter_input(INPUT_POST, 'makale_id', FILTER_VALIDATE_INT);

    if ($makale_id === false || $makale_id <= 0) {
        $response['message'] = 'Geçersiz makale ID\'si.';
        echo json_encode($response);
        exit;
    }

    if (!isset($_SESSION['id'])) { // Giriş yapmış kullanıcının ID'si olmalı
        $response['message'] = 'Oturum bilgileri eksik. Lütfen tekrar giriş yapın.';
        http_response_code(403); // Forbidden
        echo json_encode($response);
        exit;
    }
    $kullanici_id_session = $_SESSION['id']; // Kullanıcının kendi makalesini sildiğini kontrol etmek için

    try {
        // $pdo bağlantı nesnesini kullanıyoruz
        // Yalnızca kullanıcıya ait makaleleri silmeye izin ver
        $stmt = $pdo->prepare("DELETE FROM makaleler WHERE id = :id AND kullanici_id = :kullanici_id");
        $stmt->bindParam(':id', $makale_id, PDO::PARAM_INT);
        $stmt->bindParam(':kullanici_id', $kullanici_id_session, PDO::PARAM_INT); // Session'daki ID ile eşleştir

        if ($stmt->execute()) {
            if ($stmt->rowCount() > 0) {
                $response['success'] = true;
                $response['message'] = 'Makale başarıyla silindi.';
            } else {
                // Bu durum, makale bulunamadığında VEYA makale başkasına ait olduğunda oluşur.
                $response['message'] = 'Makale bulunamadı veya bu makaleyi silme yetkiniz yok.';
            }
        } else {
            // execute() false döndürdüyse (genellikle olmaz, çünkü ERRMODE_EXCEPTION ayarlı)
            $response['message'] = 'Makale silinirken bir veritabanı hatası oluştu.';
        }
    } catch (PDOException $e) {
        // Hata mesajını loglayın, kullanıcıya genel bir mesaj gösterin
        error_log("Makale AJAX silme hatası (Kullanıcı ID: {$kullanici_id_session}, Makale ID: {$makale_id}): " . $e->getMessage());
        $response['message'] = 'Veritabanı hatası oluştu. Lütfen sistem yöneticisine başvurun.';
        // Geliştirme aşamasında: $response['message'] = 'Veritabanı hatası: ' . $e->getMessage();
        http_response_code(500); // Internal Server Error
    }
} else {
    $response['message'] = 'Geçersiz istek türü veya eksik parametre.';
    http_response_code(400); // Bad Request
}

echo json_encode($response);
exit;


?>