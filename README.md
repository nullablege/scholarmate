# Scholar Mate - Akademik Makale Yönetim ve Analiz Platformu

![Scholar Mate Logo](assets/images/logo.png)

**Scholar Mate**, akademisyenlerin, araştırmacıların ve öğrencilerin makalelerini verimli bir şekilde yönetmelerini, analiz etmelerini, paylaşmalarını ve çalışmalarından maksimum fayda sağlamalarını amaçlayan **ileri düzey bir PHP tabanlı web uygulamasıdır.** Modern arayüzü, güçlü altyapısı ve kullanıcı odaklı özellikleriyle akademik süreçlerinizi kolaylaştırmak için tasarlandı.

Bu proje, **güvenlik, performans ve kullanıcı deneyimi** ön planda tutularak geliştirilmiş olup, her aşamasında titizlikle çalışılmıştır. Google Gemini API entegrasyonu ile makale özetleme gibi yenilikçi özellikler sunarken, PHPMailer, Mpdf gibi endüstri standardı kütüphanelerle de güvenilirliği pekiştirilmiştir.

---

## 🌟 Temel Özellikler

*   **Güvenli Kullanıcı Yönetimi:** Detaylı kayıt, e-posta doğrulama, güvenli şifreleme (BCRYPT) ve şifre sıfırlama mekanizmaları.
*   **Makale Yükleme ve Yönetimi:**
    *   PDF veya doğrudan metin girişi ile makale ekleme.
    *   **Smalot/PdfParser** ile PDF içeriklerinden otomatik metin çıkarma.
    *   Detaylı makale bilgileri (başlık, yazarlar, yayın yılı, DOI, dergi/konferans vb.).
    *   Kategorilendirme ve anahtar kelime etiketleme (Select2 ile gelişmiş arayüz).
*   **Akıllı Özetleme (Google Gemini API):**
    *   Yüklenen makaleler için **Google Gemini API** kullanılarak otomatik ve anlamlı özetler oluşturma.
    *   Oluşturulan özetleri manuel düzenleyebilme veya Gemini API ile yeniden farklı bir özet talep edebilme.
*   **Etkileşimli Makale Detay Sayfası:**
    *   PDF makaleler için gömülü görüntüleyici.
    *   Otomatik ve manuel özetleri ayrı tablarda görebilme.
    *   Makalelere özel notlar ekleyebilme ve yönetebilme.
    *   **Mpdf** kütüphanesi ile makale detaylarını şık bir PDF formatında dışa aktarabilme.
    *   Farklı formatlarda (APA, MLA, Chicago, BibTeX) atıf oluşturma.
*   **Güvenli Paylaşım Yönetimi:** Makaleleri platformdaki diğer kullanıcılarla "salt okunur" modda güvenli bir şekilde paylaşabilme.
*   **AJAX ile Dinamik İşlemler:** Makale silme gibi işlemler için sayfa yenilenmeden, akıcı AJAX tabanlı çözümler.
*   **Profil Yönetimi:** Kullanıcı bilgilerini, avatarını ve tema tercihini (açık/koyu/sistem) güncelleyebilme.
*   **E-posta Bildirimleri:** **PHPMailer** aracılığıyla SMTP üzerinden profesyonel HTML e-posta şablonları ile kayıt onayı ve şifre sıfırlama e-postaları.
*   **Gelişmiş Güvenlik Önlemleri:**
    *   **Prepared Statements (PDO ve MySQLi):** SQL Injection saldırılarına karşı tam koruma.
    *   **`htmlspecialchars()` ve `trim()`:** Cross-Site Scripting (XSS) ve gereksiz boşluklara karşı koruma.
    *   **`intval()` ve `filter_input()`:** Girdi verilerinin tip ve format doğrulaması.
    *   **Güvenli Dosya Yükleme:** Uzantı, MIME tipi (`finfo`), dosya boyutu kontrolleri; benzersiz ve güvenli dosya adlandırması.
    *   **CSRF Koruması Potansiyeli:** AJAX endpoint'lerinde oturum kontrolü (ileride token bazlı koruma eklenebilir).
    *   **Host Header Injection'a Karşı Potansiyel Dikkat:** (Örn: `mail.php` içinde `$_SERVER['HTTP_HOST']` kullanımı yerine sabit config önerilir).
    *   **Spam Önleme:** Yeni onay kodu isteme süresinde zaman kısıtlaması.
*   **Modern Arayüz ve Kullanıcı Deneyimi:**
    *   **Bootstrap 5** ile tamamen duyarlı (responsive) tasarım.
    *   Özel `style.css` ile şık ve modern görünüm.
    *   JavaScript (`script.js`) ile interaktif öğeler, form doğrulamaları, şifre gücü göstergesi, avatar önizlemesi.
    *   Açık/Koyu/Sistem tema desteği (`localStorage` ve OS tercihi ile senkronize).
*   **Bağımlılık Yönetimi:** **Composer** ile PHP kütüphanelerinin etkin yönetimi.

---

## 🛠️ Kullanılan Teknolojiler ve Kütüphaneler

*   **Backend:** PHP (Nesne Yönelimli ve Fonksiyonel Programlama paradigmaları bir arada)
*   **Veritabanı:** MySQL (MySQLi ve PDO ile erişim)
*   **Frontend:** HTML5, CSS3, JavaScript (ES6+)
*   **CSS Framework:** Bootstrap 5
*   **PHP Kütüphaneleri (Composer ile yönetilen):**
    *   `phpmailer/phpmailer`: Güvenilir e-posta gönderimi için.
    *   `mpdf/mpdf`: PHP ile dinamik PDF oluşturma için.
    *   `smalot/pdfparser`: PDF dosyalarından metin içeriği çıkarmak için.
    *   `symfony/dom-crawler`, `symfony/css-selector`: (PdfParser tarafından dolaylı kullanılabilir).
*   **Harici API'ler:**
    *   Google Generative Language API (Gemini 2.0 Flash): Akıllı metin özetleme için.
*   **JavaScript Kütüphaneleri:**
    *   Select2: Gelişmiş `select` kutuları (etiketleme, arama vb.) için.
    *   (jQuery Select2 tarafından gerektirildiği için dolaylı olarak kullanılır)
*   **Güvenlik Fonksiyonları:**
    *   `password_hash()` ve `password_verify()`: Güçlü BCRYPT şifreleme.
    *   `htmlspecialchars()`, `mysqli_real_escape_string()`, Prepared Statements.
    *   `filter_var()`, `filter_input()`, `intval()`.

---

## 📁 Dosya Yapısı (Özet)
```
scholar-mate/
├── assets/
│ ├── config/
│ │ └── db.php # Veritabanı bağlantı yapılandırması
│ ├── css/
│ │ └── style.css # Ana stil dosyası
│ ├── images/
│ │ └── logo.png # Proje logosu
│ ├── js/
│ │ └── script.js # Ana JavaScript dosyası
│ └── uploads/
│ ├── pdf/ # Yüklenen makale PDF'leri
│ └── profilPhotos/ # Kullanıcı profil fotoğrafları
├── includes/
│ ├── disaAktar.php # Makaleyi PDF olarak dışa aktarma
│ ├── function.php # Genel yardımcı fonksiyonlar
│ ├── mail.php # E-posta gönderme fonksiyonları (PHPMailer)
│ ├── makale_sil_ajax.php # AJAX ile makale silme
│ ├── notEkle.php # Makaleye not ekleme
│ ├── ozetDuzenle.php # Manuel özeti güncelleme
│ ├── paylasimEkle.php # Makale paylaşımı ekleme
│ ├── paylasimSil.php # Makale paylaşımı silme
│ ├── sifreDegistir.php # Kullanıcı şifresini değiştirme
│ ├── yeniKod.php # Yeni e-posta onay kodu gönderme
│ └── yeniOzet.php # Gemini API ile özeti yeniden oluşturma
├── register/
│ ├── index.php # Kayıt formu (adım 1)
│ └── register.php # E-posta onay kodu girme sayfası (adım 2)
├── sifremiUnuttum/
│ ├── index.php # Şifre sıfırlama isteği formu
│ ├── sifremiUnuttum.php # (Bu dosya akışta belirsiz, muhtemelen sifreYenile.php ile benzer)
│ └── sifreYenile.php # Yeni şifre belirleme formu (e-postadaki linkten gelinir)
├── templates/
│ ├── emails/
│ │ ├── email_template.html # Kayıt onayı e-posta şablonu
│ │ └── email_templateSifre.html # Şifre sıfırlama e-posta şablonu
│ └── partials/
│ └── navbar.php # Üst navigasyon menüsü
├── vendor/ # Composer bağımlılıkları
├── composer.json # Proje bağımlılıkları (PHP)
├── index.php # Ana sayfa (Makalelerim listesi)
├── login.php # Giriş sayfası
├── logout.php # Çıkış işlemi
├── makaleDetay.php # Makale detaylarını görüntüleme sayfası
├── makaleDuzenle.php # Makale düzenleme sayfası
├── makaleYukle.php # Yeni makale yükleme sayfası
├── paylasilanMakaleDetay.php # Paylaşılan makale detaylarını görüntüleme
└── profil.php # Kullanıcı profili ve ayarlar sayfası
```

## 📜 Modüller ve Sayfaların Detaylı Açıklaması

Projemizdeki her bir PHP dosyası, Scholar Mate'in zengin işlevselliğine katkıda bulunan özel bir amaca hizmet eder.

### `assets/config/db.php`
Bu kritik dosya, tüm veritabanı işlemlerinin temelini oluşturur. Güvenli bir şekilde MySQL veritabanına bağlantı kurar (`mysqli`). Bağlantı sırasında oluşabilecek hataları yakalar (`connect_error`) ve uygulamanın çökmesini engeller. Uluslararası karakter desteği için karakter setini **UTF-8** olarak ayarlar (`set_charset("utf8")`), bu da Türkçe karakterler ve çeşitli akademik sembollerin doğru bir şekilde saklanmasını ve görüntülenmesini garanti eder. Oturum yönetiminin (`session_start()`) güvenli bir şekilde başlatılmasını sağlar.

### `includes/disaAktar.php`
Bu dosya, kullanıcıların makalelerini profesyonel bir PDF formatında dışa aktarmalarını sağlayan **Mpdf** kütüphanesini ustalıkla kullanır.
*   URL'den alınan makale ID'si (`$_GET['id']`) `intval()` fonksiyonuyla güvenli bir şekilde tamsayıya dönüştürülür.
*   Sadece oturum açmış kullanıcının kendi makalesini (`kullanici_id` kontrolü) dışa aktarabilmesi sağlanarak yetkisiz erişim engellenir.
*   Makale başlığı, yazarları, yayın yılı, DOI, özetler (manuel ve otomatik) ve makale içeriği gibi tüm veriler dinamik olarak şık bir HTML şablonuna yerleştirilir.
*   **Güvenlik Vurgusu:** Makale içeriği `htmlspecialchars()` fonksiyonundan geçirilerek olası XSS (Cross-Site Scripting) zafiyetlerine karşı koruma sağlanır. `nl2br()` fonksiyonu ile metin içeriğindeki satır sonları PDF'te korunur.
*   Son olarak, oluşturulan PDF, makalenin orijinal dosya adıyla kullanıcıya sunulur (`$mpdf->Output(...)`).

### `includes/function.php`
Projenin beyni sayılabilecek bu dosya, uygulamanın birçok yerinde tekrar tekrar kullanılan temel fonksiyonları içerir. Bu modüler yaklaşım, kod tekrarını azaltır ve bakımı kolaylaştırır.
*   **`validateAndHashPassword()`:** Kullanıcı şifrelerinin güvenliğini en üst düzeye çıkarır. Şifrelerin belirli bir karmaşıklıkta (büyük harf, küçük harf, rakam, özel karakter, minimum uzunluk) olmasını regex (`preg_match`) ile kontrol eder ve ardından `password_hash()` fonksiyonu ile **BCRYPT** algoritmasını kullanarak güçlü bir şekilde şifreler.
*   **`getEmailRecordCount()`, `kullaniciEkle()`, `kullaniciDogruMu()`, `getUserByEmail()`, `kullaniciSifreGuncelle()`:** Bu fonksiyonlar, kullanıcı yönetimiyle ilgili veritabanı işlemlerini **MySQLi Prepared Statements** kullanarak gerçekleştirir (`$conn->prepare()`, `bind_param()`, `execute()`). Bu sayede **SQL Injection saldırılarına karşı tam koruma** sağlanır. Şifre doğrulama işlemleri `password_verify()` ile güvenli bir şekilde yapılır. Hata durumları için `error_log()` ile detaylı loglama yapılır.
*   **`resmiKaydet()` ve `pdfYukle()`:** Güvenli dosya yükleme mekanizmalarıdır.
    *   Yüklenen dosyanın tipi (`$_FILES[...]['type']` ve `finfo` ile MIME kontrolü), boyutu (`$maxFileSize`), uzantısı kontrol edilir.
    *   `basename()` ile path traversal saldırıları engellenir.
    *   Dosya adları `preg_replace` ile zararlı karakterlerden arındırılır ve `time() . '_' . uniqid()` ile benzersiz hale getirilerek çakışmaların önüne geçilir.
    *   Hedef klasörün varlığı ve yazılabilirliği kontrol edilir (`is_dir`, `mkdir`, `is_writable`).
*   **`pdfText()`:** Yüklenen PDF makalelerden metin içeriğini çıkarmak için **Smalot/PdfParser** kütüphanesini kullanır. Çıkarılan metin, gereksiz boşluklardan arındırılır.
*   **`Ozetle()` ve `TekrarOzetle()`:** Bu **heyecan verici fonksiyonlar**, projenin yapay zeka yeteneklerini sergiler! **Google Gemini API** (örneğin, `gemini-2.0-flash` modeli) ile entegrasyon sağlayarak, cURL aracılığıyla verilen makale metnini anlamlı ve akademik bir dille özetler. Prompt mühendisliği ile API'den istenen formatta ve kalitede özet alınması hedeflenmiştir. Hata yönetimi (`curl_error`, API hataları) titizlikle yapılmıştır. `TekrarOzetle()` fonksiyonu, kullanıcının ilk özeti beğenmemesi durumunda farklı bir yaklaşımla yeni bir özet talep etmesine olanak tanır. API anahtarının (`AIzaSy...`) doğrudan kodda yer alması yerine `.env` dosyası veya sunucu ortam değişkenlerinden okunması daha güvenli bir pratiktir.
*   `uyeBilgisiGetirMail()` ve `uyeBilgisiGetir()`: Bu fonksiyonlardan `uyeBilgisiGetirMail()` kullanıcının temel bilgilerini (e-posta dahil) getirirken, `uyeBilgisiGetir()` içindeki PDO yapısı ve global `$conn` MySQLi nesnesi ile olan çelişkisi nedeniyle muhtemelen aktif olarak kullanılmıyor veya bir geliştirme denemesi olabilir. Projenin genel tutarlılığı MySQLi üzerinedir.

### `includes/mail.php`
E-posta gönderim işlemlerini **PHPMailer** gibi endüstri standardı bir kütüphane ile yönetir. Bu, PHP'nin dahili `mail()` fonksiyonuna göre çok daha güvenilir ve esnek bir çözümdür.
*   SMTP ayarları (`define` ile sabitler) üzerinden (örneğin Gmail) e-posta gönderimi yapar. Bu, e-postaların spam klasörüne düşme olasılığını azaltır.
*   **`gonderEposta()`:** Yeni kullanıcı kayıtlarında doğrulama kodu göndermek için kullanılır. `templates/emails/email_template.html` dosyasını okuyarak (`file_get_contents()`) ve `str_replace()` ile kullanıcı adı, doğrulama kodu gibi dinamik verileri yerleştirerek profesyonel görünümlü HTML e-postalar oluşturur.
*   **`gonderEpostaSifreUnuttum()`:** Kullanıcılar şifrelerini unuttuğunda sıfırlama bağlantısı göndermek için kullanılır. Benzer şekilde `templates/emails/email_templateSifre.html` şablonunu kullanır. Şifre sıfırlama linki `$_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST']` kullanılarak oluşturulur; daha güvenli bir yaklaşım, domain adını sabit bir yapılandırma dosyasından almaktır.

### `includes/makale_sil_ajax.php`
Kullanıcının makalelerini sayfa yenilenmeden, akıcı bir şekilde silebilmesi için **AJAX** tabanlı bir endpoint sağlar.
*   `session_start()` ile oturum başlatılır ve kullanıcının giriş yapıp yapmadığı kontrol edilir (`$_SESSION['login']`). Yetkisiz isteklerde HTTP 401 (Unauthorized) yanıtı döner.
*   **PDO (PHP Data Objects)** kullanarak veritabanı bağlantısı kurar. Bu, projenin geri kalanındaki MySQLi kullanımından farklıdır ancak PDO, farklı veritabanı sistemleriyle çalışabilirlik ve daha modern bir arayüz sunar. `PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION` ile hatalar istisna olarak yakalanır ve `PDO::ATTR_EMULATE_PREPARES => false` ile gerçek prepared statement'lar kullanılır.
*   İstek başlığı `Content-Type: application/json` olarak ayarlanır.
*   Silinecek makale ID'si `filter_input(INPUT_POST, 'makale_id', FILTER_VALIDATE_INT)` ile güvenli bir şekilde alınır ve doğrulanır.
*   Sadece oturumdaki kullanıcının kendi makalesini silebilmesi için (`AND kullanici_id = :kullanici_id`) **Prepared Statements** ile sorgu yapılır (`bindParam`).
*   İşlem sonucuna göre (başarılı silme, makale bulunamadı/yetki yok, veritabanı hatası) uygun JSON formatında ve HTTP durum kodlarıyla (örn: 500, 400, 403) yanıt verilir. Hatalar `error_log` ile kaydedilir.

### `includes/notEkle.php`, `includes/ozetDuzenle.php`
Bu dosyalar, makalelere not ekleme ve özetleri düzenleme gibi işlemleri gerçekleştirir.
*   Girdiler `htmlspecialchars()`, `trim()` ve `intval()` ile temizlenir.
*   **Güvenlik Notu:** `mysqli_real_escape_string()` fonksiyonu SQL Injection saldırılarına karşı bir miktar koruma sağlasa da, `function.php` ve `makale_sil_ajax.php` gibi dosyalarda gördüğünüz **Prepared Statements** kullanımı çok daha güvenli ve tercih edilen bir yöntemdir. Tüm veritabanı sorgularında Prepared Statements kullanılması önerilir. `notEkle.php` dosyasında `makaleid` için `intval()` kullanılmalıydı.
*   `ozetDuzenle.php`'de, kullanıcı tarafından düzenlenen özet (manuel) `otomatik_ozet` alanına, Gemini'nin ürettiği önceki otomatik özet ise `eski_ozet` alanına kaydedilerek bir tür versiyonlama sağlanır.

### `includes/paylasimEkle.php` ve `includes/paylasimSil.php`
Makale paylaşım özelliklerini yönetir.
*   `paylasimEkle.php`: Bir makaleyi başka bir kullanıcıyla paylaşmak için kullanılır. Paylaşılacak kullanıcının e-postası alınır, `htmlspecialchars()`, `trim()` ve `mysqli_real_escape_string()` ile temizlenir. `getUserByEmail()` (Prepared Statement kullanan güvenli fonksiyon) ile kullanıcı veritabanında aranır. Kullanıcının kendisiyle veya zaten paylaşılmış bir makaleyi tekrar paylaşması engellenir.
*   `paylasimSil.php`: Paylaşımı kaldırmak için kullanılır.
    *   **Kritik Güvenlik Notu:** `paylasimSil.php` dosyasında `$_GET` ile alınan `id` ve `makale` parametreleri için `intval()` veya `mysqli_real_escape_string()` gibi **hiçbir güvenlik önlemi alınmamış**. Bu durum **doğrudan SQL Injection zafiyetine yol açabilir.** Bu parametreler acilen `intval()` ile doğrulanmalı veya sorgu Prepared Statements ile yeniden yazılmalıdır.
*   **Genel Öneri:** Paylaşım ekleme ve silme işlemleri için de Prepared Statements kullanılmalıdır.

### `includes/sifreDegistir.php`
Kullanıcının mevcut şifresini doğrulayarak yeni bir şifre belirlemesini sağlar.
*   Girdiler (mevcut şifre, yeni şifre, yeni şifre tekrarı) alınır.
*   `validateAndHashPassword()` fonksiyonu ile yeni şifrenin karmaşıklığı kontrol edilir ve güvenli bir şekilde (BCRYPT) hash'lenir.
*   `kullaniciDogruMu()` fonksiyonu ile kullanıcının girdiği mevcut şifrenin doğruluğu kontrol edilir.
*   Doğrulama başarılıysa, yeni hash'lenmiş şifre veritabanına kaydedilir. Sorgu, `mysqli_query()` ile doğrudan çalıştırılıyor; gelen `$hashli` ve session'dan alınan `$eposta` güvenli olsa da, tutarlılık ve en iyi pratikler açısından Prepared Statements kullanılması daha iyi olurdu.
*   Başarı veya hata durumları `setcookie()` ile kullanıcıya bildirilir ve profil sayfasına yönlendirilir.

### `includes/yeniKod.php`
Kullanıcının e-posta onay kodunu yeniden talep etmesini sağlar.
*   Kullanıcının session'da kayıt verileri ve son kod gönderim zamanı olup olmadığı kontrol edilir.
*   Spam gönderimleri engellemek için, son kod isteği üzerinden en az 90 saniye geçmesi beklenir.
*   Süre dolmuşsa, yeni bir 6 haneli doğrulama kodu üretilir, session'a kaydedilir, gönderim zamanı güncellenir ve `gonderEposta()` fonksiyonu ile kullanıcıya e-posta iletilir.
*   Başarı/hata mesajları cookie ile `register/register.php` sayfasına iletilir.

### `includes/yeniOzet.php`
Bir makalenin mevcut otomatik özetini **Google Gemini API (`TekrarOzetle()` fonksiyonu)** kullanarak yeniden, farklı bir şekilde oluşturur.
*   URL'den alınan makale ID'si `intval()` ile güvenli hale getirilir.
*   Mevcut otomatik özet alınıp Gemini'ye gönderilir, dönen yeni özet `otomatik_ozet` alanına, önceki otomatik özet ise `eski_ozet` alanına kaydedilir.
*   Kullanılan `mysqli_real_escape_string()` yerine Prepared Statements daha güvenli olacaktır.

### `register/index.php` (Kayıt Formu - Adım 1)
Yeni kullanıcıların platforma kaydolduğu ilk adımdır.
*   Eğer kullanıcı zaten giriş yapmışsa ana sayfaya yönlendirilir.
*   Kullanıcı form verilerini (ad, soyad, unvan, kurum, araştırma alanı, e-posta, şifre) girer. Formda bir önceki denemeden kalan veriler varsa session'dan geri yüklenir (`htmlspecialchars` ile XSS korumalı).
*   Girdiler `htmlspecialchars(trim(...))` ile temizlenir.
*   **Avatar Yükleme:** `resmiKaydet()` fonksiyonu ile kullanıcının profil fotoğrafı güvenli bir şekilde sunucuya yüklenir.
*   E-posta formatı `filter_var(..., FILTER_VALIDATE_EMAIL)` ile kontrol edilir.
*   Şifre, `validateAndHashPassword()` ile karmaşıklık ve güçlü BCRYPT hash'leme kontrollerinden geçer.
*   E-postanın daha önce kaydedilip kaydedilmediği `getEmailRecordCount()` ile kontrol edilir.
*   Tüm kontroller başarılıysa, kullanıcının verileri, onay kodu ve kayıt zamanı session'a kaydedilir. Ardından `gonderEposta()` ile kullanıcıya onay e-postası gönderilir ve kullanıcı `register/register.php` (onay kodu girme) sayfasına yönlendirilir.
*   **Arayüz:** Modern Bootstrap 5 formu, floating labels, şifre görünürlük toggle, gerçek zamanlı şifre gücü göstergesi ve avatar yükleme önizlemesi gibi kullanıcı dostu özellikler içerir.

### `register/register.php` (E-posta Onay Kodu Girişi - Adım 2)
Kullanıcının, e-postasına gönderilen 6 haneli onay kodunu girdiği sayfadır. (Dosya adlandırması `register_confirm.php` veya `verify_email.php` gibi daha açıklayıcı olabilirdi).
*   Gerekli session verileri (onay kodu, kayıt verileri, kayıt zamanı) yoksa veya kullanıcı zaten giriş yapmışsa uygun sayfalara yönlendirilir.
*   Cookie'lerden hata/başarı mesajları (`uyari_register2`, `yenikod_success`) okunur.
*   **Kod Geçerlilik Süresi:** Onay kodunun geçerliliği 5 dakika ile sınırlıdır. Süre dolmuşsa kullanıcıya uyarı verilir ve yeni kod istemesi için link gösterilir.
*   Kullanıcı kodu girdiğinde, session'daki kodla karşılaştırılır.
*   Kod doğruysa ve süresi dolmamışsa, `kullaniciEkle()` fonksiyonu (Prepared Statement kullanan) çağrılarak kullanıcı bilgileri veritabanına kaydedilir.
*   Başarılı kayıt sonrası, ilgili session verileri temizlenir, başarı cookie'si ayarlanır ve kullanıcı `login.php` sayfasına yönlendirilir.
*   **Arayüz:** 6 haneli kodun kolayca girilebilmesi için ayrı input alanları sunar. Kalan süre ve yeni kod isteme linki `script.js` ile dinamik olarak yönetilir.

### `sifremiUnuttum/index.php` (Şifre Sıfırlama İsteği)
Kullanıcıların şifrelerini unuttuklarında sıfırlama işlemi başlatmalarını sağlar.
*   Kullanıcı, kayıtlı e-posta adresini girer. Girdi `trim()`, `htmlspecialchars()` ve `filter_var()` ile temizlenir ve doğrulanır.
*   `getEmailRecordCount()` ile e-postanın sistemde kayıtlı olup olmadığı kontrol edilir.
*   E-posta kayıtlıysa, şifre sıfırlama için e-posta adresi (`sifreYenile`) ve istek zamanı (`yenileZaman`) session'a kaydedilir. Bu, sıfırlama linkinin zaman aşımına uğramasını sağlar.
*   `gonderEpostaSifreUnuttum()` ile kullanıcıya şifre sıfırlama linki içeren bir e-posta gönderilir.
*   Kullanıcı aynı sayfada bilgilendirme mesajı görür.

### `sifremiUnuttum/sifreYenile.php` (Yeni Şifre Belirleme)
E-postadaki şifre sıfırlama linkine tıklandığında gelinen sayfadır.
*   Session'daki `sifreYenile` (e-posta) ve `yenileZaman` bilgileri kontrol edilerek isteğin geçerliliği ve zaman aşımı (5 dakika) doğrulanır.
*   Geçerli bir istekse, kullanıcıdan yeni şifresini ve tekrarını girmesi istenir.
*   Yeni şifre, `validateAndHashPassword()` ile kontrol edilir ve BCRYPT ile hash'lenir.
*   `kullaniciSifreGuncelle()` (Prepared Statement kullanan) fonksiyonu ile kullanıcının şifresi veritabanında güncellenir.
*   İşlem başarılıysa, ilgili session verileri temizlenir ve kullanıcıya başarı mesajı gösterilerek `login.php` sayfasına yönlendirilir.
*   **Arayüz:** Şifre gücü göstergesi ve şifre görünürlük toggle gibi özelliklerle kullanıcı dostu bir şifre giriş formu sunar. `script.js` içindeki JS kodları ile şifre kriterleri ve eşleşme anlık olarak kontrol edilir.

### `templates/emails/email_template.html` ve `templates/emails/email_templateSifre.html`
Kullanıcılara gönderilen e-postalar için şık ve profesyonel HTML şablonlarıdır.
*   Scholar Mate logosu ve markasına uygun tasarım.
*   `{{KULLANICI_ADI}}`, `{{DOGRULAMA_KODU}}`, `{{SIFIRLAMA_LINKI}}` gibi yer tutucular kullanılarak dinamik içerik oluşturulur.
*   Responsive tasarımları sayesinde farklı e-posta istemcilerinde ve cihazlarda iyi görünürler.

### `templates/partials/navbar.php`
Tüm sayfalarda ortak olarak kullanılan üst navigasyon menüsünü içerir.
*   Session'dan alınan kullanıcı adı, soyadı, ünvan ve avatar bilgileri ile kişiselleştirilmiş bir menü sunar.
*   Kullanıcı avatarı ve adı/ünvanı gösterilir, tıklandığında profil sayfasına ve çıkış işlemine yönlendiren bir dropdown menü açılır.
*   Makalelerim (ana sayfa) gibi temel navigasyon linklerini içerir.
*   `htmlspecialchars()` kullanılarak XSS saldırılarına karşı güvenlik sağlanmıştır.

### `composer.json`
PHP projesinin bağımlılıklarını tanımlayan ve **Composer** tarafından kullanılan standart bir dosyadır.
*   Projenin kullandığı kütüphaneler (örn: `smalot/pdfparser`, `phpmailer/phpmailer`, `mpdf/mpdf`, `symfony/*`) burada listelenir. Bu, projenin kurulumunu ve bağımlılık yönetimini son derece kolaylaştırır.

### `index.php` (Ana Sayfa - Makalelerim)
Kullanıcının platforma giriş yaptıktan sonra karşılaştığı ana sayfadır. Kendi makalelerini ve kendisiyle paylaşılan makaleleri listeler.
*   Kullanıcı giriş yapmamışsa `login.php`'ye yönlendirilir. Eğer kayıt işlemi yarım kalmışsa (e-posta onayı bekleniyorsa) ve onay süresi dolmamışsa `register/register.php` sayfasına yönlendirilir (zaman aşımı kontrolüyle).
*   Veritabanından mevcut kategoriler çekilerek filtreleme için kullanılır.
*   Kullanıcılar, makalelerini **kategoriye** ve **yayın yılına** göre filtreleyebilirler. Filtre değerleri `$_GET` ile alınır ve `intval()` ile güvenli hale getirilir.
*   Hem kullanıcının kendi makaleleri hem de kendisiyle paylaşılan makaleler ayrı bölümlerde listelenir. Sorgular, string birleştirme ile oluşturulsa da, filtre parametreleri `intval` ile güvenli hale getirildiği ve kullanıcı ID'si session'dan alındığı için mevcut haliyle doğrudan SQL Injection riski düşüktür. Ancak en iyi pratik olarak Prepared Statements kullanımı önerilir.
*   Her makale, başlığı, yazarları, yayın yılı ve kısa bir özeti içeren bir "kart" üzerinde gösterilir. Bu kartlara tıklandığında ilgili makalenin detay sayfasına gidilir.
*   **Arayüz:** Modern Bootstrap 5 tabanlı tasarımı, kullanıcı dostu filtreleme seçenekleri ve bilgilendirici makale kartları sunar.

### `login.php` (Giriş Sayfası)
Kullanıcıların Scholar Mate platformuna giriş yapmalarını sağlar.
*   Eğer kullanıcı zaten giriş yapmışsa ana sayfaya yönlendirilir.
*   Başarılı kayıt sonrası yönlendirme (`kaydoldu_basarili` cookie) veya e-posta onay süresinin dolması gibi durumlar için kullanıcıya bilgilendirme mesajları gösterilir.
*   Kullanıcı e-posta ve şifresini girer. Girdiler `htmlspecialchars(trim(...))` ile temizlenir.
*   `kullaniciDogruMu()` fonksiyonu (güvenli şifre doğrulama ve Prepared Statement kullanan) ile giriş bilgileri kontrol edilir. Başarılı girişte ana sayfaya yönlendirilir, aksi halde hata mesajı gösterilir.
*   **Arayüz:** Şık bir logo, "Beni Hatırla" seçeneği, "Şifremi Unuttum?" linki, (işlevsiz olsa da) sosyal medya ile giriş butonları ve yeni kayıt için link içeren kullanıcı dostu bir form sunar. Şifre görünürlük toggle mevcuttur.

### `logout.php` (Çıkış İşlemi)
Kullanıcının mevcut oturumunu güvenli bir şekilde sonlandırır.
*   `session_start()` ile oturum başlatılır.
*   `session_unset()` ile tüm session değişkenleri silinir.
*   `session_destroy()` ile session tamamen sonlandırılır.
*   Kullanıcı `login.php` sayfasına yönlendirilir.

### `makaleDetay.php` (Makale Detay Sayfası)
Bir makalenin tüm detaylarının görüntülendiği ve yönetildiği kapsamlı bir sayfadır.
*   URL'den alınan makale ID'si `intval()` ile güvenli hale getirilir.
*   Makalenin varlığı ve görüntüleme yetkisi (kullanıcıya ait mi veya kendisiyle paylaşılmış mı) kontrol edilir. Yetkisiz erişim engellenir.
*   Makalenin tüm bibliyografik bilgileri, kategorileri, etiketleri ve notları veritabanından çekilir.
*   **Özellikler:**
    *   Sayfanın üst kısmında breadcrumb navigasyon ve hızlı aksiyon butonları bulunur: "PDF Olarak Dışa Aktar" (`includes/disaAktar.php`), "Düzenle" (`makaleDuzenle.php`'ye link) ve **AJAX ile çalışan** "Sil" (`includes/makale_sil_ajax.php`).
    *   **Tablı Arayüz:**
        *   **Makale İçeriği:** PDF dosyaları için gömülü `<iframe>` görüntüleyici, TXT dosyaları için metin gösterimi ve indirme linki.
        *   **Özet:** Hem Google Gemini tarafından otomatik oluşturulan özeti hem de kullanıcının manuel olarak girdiği özeti gösterir. Kullanıcı, otomatik özeti manuel olarak **düzenleyebilir ve kaydedebilir** (`includes/ozetDuzenle.php`). Ayrıca, Gemini API ile özeti **tekrar, farklı bir şekilde oluşturma** (`includes/yeniOzet.php`) seçeneği sunar. JavaScript ile eski ve yeni (düzenlenen) özet arasında geçiş yapılabilir.
        *   **Notlarım:** Kullanıcının makaleyle ilgili kişisel notlarını listeler ve modal pencere üzerinden yeni not eklemesine (`includes/notEkle.php`) olanak tanır.
        *   **Atıf Oluştur:** Seçilen formata (APA, MLA, Chicago, BibTeX) göre makalenin atıfını JavaScript ile dinamik olarak oluşturur. Oluşturulan atıf panoya kopyalanabilir veya `.txt` dosyası olarak indirilebilir.
        *   **Paylaşım Yönetimi:** Kullanıcının makalesini platformdaki diğer kullanıcılarla paylaşmasını (`includes/paylasimEkle.php`) ve mevcut paylaşımları yönetmesini/kaldırmasını (`includes/paylasimSil.php`) sağlar.
*   Girdiler ve çıktılar `htmlspecialchars()` ile XSS'e karşı korunur.

### `makaleDuzenle.php` (Makale Düzenleme Sayfası)
Kullanıcının mevcut bir makalesinin bilgilerini ve dosyasını güncellemesini sağlar.
*   Düzenlenecek makalenin ID'si URL'den `intval()` ile güvenli bir şekilde alınır ve sadece makalenin sahibi tarafından düzenlenebilmesi sağlanır.
*   Mevcut makale bilgileri (başlık, yazarlar, dosya tipi vb.) form alanlarına otomatik olarak doldurulur.
*   Kullanıcı, makalenin PDF dosyasını veya metin içeriğini değiştirebilir. Yeni bir PDF yüklenirse, içeriği `pdfText()` ile okunur ve **Google Gemini API** (`Ozetle()`) ile yeni bir otomatik özet oluşturulur. Metin içeriği güncellenirse de yeni özet oluşturulur.
*   Kategoriler, **Select2** kütüphanesi kullanılarak çoklu seçim ve yeni kategori ekleme özelliğiyle yönetilir.
*   Form verileri `htmlspecialchars(trim(...))`, `filter_var()` ile temizlenir ve doğrulanır.
*   Veritabanı güncelleme işlemi sırasında, `makaleYukle.php`'de olduğu gibi, tüm string veriler `mysqli_real_escape_string()` ile SQL Injection'a karşı güvenli hale getirilmelidir (bu dosyadaki POST işleme bloğunda eksik olabilir, kontrol edilmeli). İdeal olarak **Prepared Statements** kullanılmalıdır. Makale-kategori ilişkileri de güncellenir (eskiler silinip yenileri eklenir).
*   Başarılı güncelleme sonrası kullanıcı, makalenin detay sayfasına (`makaleDetay.php`) yönlendirilir.

### `makaleYukle.php` (Yeni Makale Yükleme Sayfası)
Kullanıcıların platforma yeni makaleler eklemesini sağlar.
*   **Giriş Seçenekleri:** Kullanıcılar makalelerini ya **PDF dosyası olarak yükleyebilir** ya da **doğrudan metin olarak girebilirler**. Arayüzdeki toggle butonları ile bu iki mod arasında geçiş yapılabilir.
    *   **PDF Yükleme:** `script.js` içerisinde tanımlanan sürükle-bırak (drag-and-drop) alanı veya dosya seçme butonu ile PDF dosyası seçilir. Yüklenen PDF'in adı ve boyutu önizlenir. Backend'de `pdfYukle()` ile dosya güvenli bir şekilde sunucuya kaydedilir ve `pdfText()` ile içeriği okunur.
    *   **Metin Girişi:** Kullanıcılar makale metnini doğrudan bir `<textarea>` alanına yapıştırabilir veya yazabilirler.
*   Yüklenen/girilen makale içeriği, **Google Gemini API** (`Ozetle()`) aracılığıyla otomatik olarak özetlenir.
*   Kullanıcıdan makalenin bibliyografik bilgileri (başlık, yazarlar, yayın yılı, dergi/konferans, DOI), manuel bir özet (isteğe bağlı), anahtar kelimeler ve kategoriler istenir.
*   **Kategoriler:** **Select2** kütüphanesi sayesinde kullanıcılar mevcut kategorilerden seçim yapabilir veya anında yeni kategoriler oluşturabilirler (tags: true özelliği).
*   Formdan alınan tüm veriler `htmlspecialchars(trim(...))`, `filter_var()`, `intval()` ve **çok önemlisi `mysqli_real_escape_string()`** ile SQL Injection ve XSS saldırılarına karşı güvenli hale getirilir.
*   Hazırlanan verilerle birlikte `INSERT` sorgusu çalıştırılarak makale ve ilgili kategorileri veritabanına kaydedilir. (Bu `INSERT` sorgusu `mysqli_real_escape_string` kullanıldığı için mevcut haliyle güvenlidir, ancak **Prepared Statements** kullanımı daha modern ve genel kabul gören en iyi pratiktir).
*   Başarılı yükleme sonrası kullanıcı ana sayfaya (`index.php`) yönlendirilir.

### `paylasilanMakaleDetay.php` (Paylaşılan Makale Detay Sayfası)
Bu sayfa, `makaleDetay.php`'nin bir varyasyonudur ve bir kullanıcının kendisiyle paylaşılmış bir makaleyi görüntülemesi için tasarlanmıştır.
*   Temel farkı yetkilendirme mantığındadır: Makalenin oturumdaki kullanıcıya ait olup olmadığını değil, `makale_paylasim` tablosu üzerinden oturumdaki kullanıcıyla **paylaşılıp paylaşılmadığını** kontrol eder.
*   Kullanıcı, paylaşılan makalenin içeriğini, özetlerini ve diğer bibliyografik bilgilerini görebilir.
*   Ancak, makale üzerinde herhangi bir **değişiklik yapma (Düzenle, Sil, Özet Düzenle/Yenile, Not Ekle, Paylaşım Ekle/Kaldır) yetkisi yoktur.** Bu nedenle ilgili butonlar ya gizlenir ya da `disabled` (devre dışı) olarak gösterilir. Bu, makale sahibinin verilerinin bütünlüğünü korur.
*   Kullanıcı yine de makaleyi PDF olarak dışa aktarabilir ve atıf oluşturabilir.

### `profil.php` (Kullanıcı Profili Sayfası)
Kullanıcıların kendi profil bilgilerini görüntüleyip güncellemelerine ve bazı site ayarlarını kişiselleştirmelerine olanak tanır.
*   Kullanıcının mevcut bilgileri (avatar, ad, soyad, unvan, kurum, araştırma alanı, e-posta) veritabanından çekilerek form alanlarında gösterilir.
*   **Avatar Güncelleme:** Kullanıcılar, `resmiKaydet2()` fonksiyonu aracılığıyla profil fotoğraflarını değiştirebilirler. Yeni bir avatar yüklendiğinde, eğer eski avatar varsayılan bir resim değilse sunucudan silinerek gereksiz dosya birikiminin önüne geçilir.
*   **Kişisel Bilgi Güncelleme:** Ad, soyad, unvan, kurum ve araştırma alanı gibi bilgiler güncellenebilir. E-posta adresi güvenlik nedeniyle değiştirilemez olarak ayarlanmıştır.
*   **Tema Tercihi:** Kullanıcılar, sitenin görünüm temasını "Açık Mod", "Koyu Mod" veya "Sistem Tercihi/Otomatik" olarak seçebilirler. Bu tercih hem `localStorage`'a hem de (form gönderildiğinde) veritabanına kaydedilir.
*   **Güvenlik Vurgusu:** Profil bilgileri güncellenirken **MySQLi Prepared Statements** (`mysqli_prepare`, `mysqli_stmt_bind_param`, `mysqli_stmt_execute`) kullanılır. Bu, SQL Injection saldırılarına karşı en üst düzeyde koruma sağlar ve projedeki **en iyi güvenlik pratiğini** temsil eder!
*   **Şifre Değiştirme:** Ayrı bir form bölümü aracılığıyla kullanıcılar şifrelerini değiştirebilirler. Bu formun işlemleri `includes/sifreDegistir.php` dosyasına yönlendirilir.
*   Başarı ve hata mesajları kullanıcıya net bir şekilde gösterilir.

### `style.css` (Ana Stil Dosyası)
Scholar Mate'in görsel kimliğini ve kullanıcı arayüzünün estetiğini belirleyen kapsamlı bir CSS dosyasıdır.
*   **CSS Değişkenleri (`:root` ve `[data-theme="dark"]`):** Açık ve koyu temalar için renk paletleri, yazı tipleri, arkaplanlar gibi temel stil özelliklerini tanımlar. Bu, tema geçişlerini kolaylaştırır ve kodun bakımını basitleştirir.
*   **Genel Sayfa Stilleri:** `body`, `#main-bg` (animasyonlu gradient arkaplan) gibi temel sayfa elemanları için global stiller.
*   **Animasyonlar:** `fadeInScaleUp`, `fadeInDown`, `fadeInUp` gibi CSS animasyonları ile sayfa öğelerine yumuşak geçiş efektleri kazandırır.
*   **Navbar Stilleri:** Hem açık hem koyu tema için özel olarak tasarlanmış, yarı saydam (backdrop-filter) ve gölgeli modern bir navigasyon çubuğu.
*   **Form Elemanları:** `input`, `select`, `textarea` gibi form elemanları için Bootstrap'in üzerine yazılan, floating label'lar ve temaya uygun stil tanımları. **Select2** kütüphanesi için de tema uyumlu stiller içerir.
*   **Buton Stilleri:** Çeşitli amaçlar için (ana aksiyon, form gönderme, outline vb.) farklı ve çekici buton tasarımları. Gradient arkaplanlar ve hover efektleri ile zenginleştirilmiştir.
*   **Kart Stilleri:** Giriş/kayıt kartları, içerik kartları, makale kartları gibi farklı türdeki bilgi blokları için ayırt edici ve modern tasarımlar.
*   **Spesifik Bileşenler:** Avatar yükleme alanı, şifre gücü göstergesi, e-posta doğrulama kod giriş alanları, makale detay sayfasındaki metadata gridi, tablar, PDF görüntüleyici, not kartları gibi birçok özel bileşen için detaylı stil tanımları.
*   **Responsive Tasarım:** `@media` sorguları kullanılarak farklı ekran boyutlarında (masaüstü, tablet, mobil) optimum kullanıcı deneyimi sağlanır.

### `script.js` (Ana JavaScript Dosyası)
Scholar Mate'in dinamik ve interaktif özelliklerini hayata geçiren kapsamlı bir JavaScript dosyasıdır.
*   **Tema Yönetimi:** Kullanıcının `localStorage`'daki tercihine veya işletim sisteminin renk şemasına (`prefers-color-scheme`) göre site temasını (açık/koyu) dinamik olarak ayarlar. Tema değiştirme butonu ve profil sayfasındaki tema seçimi ile senkronizedir.
*   **Şifre Görünürlük Toggle:** Tüm şifre alanlarına "göster/gizle" işlevselliği ekler.
*   **Şifre Gücü Hesaplama ve Göstergesi:** Kullanıcı şifre girerken anlık olarak şifrenin gücünü hesaplar (uzunluk, harf/rakam/özel karakter kombinasyonu) ve görsel bir progress bar ve metin ile geri bildirim sağlar.
*   **Gelişmiş Form Doğrulama ve Yönetimi:**
    *   Bootstrap'in client-side doğrulama mekanizmalarını destekler ve geliştirir.
    *   Form gönderimi sırasında submit butonlarını devre dışı bırakarak ve "spinner" ikonu göstererek çift tıklama ve bekleme sürecini yönetir.
    *   Sayfaya özel ek doğrulamalar (örn: kayıt formunda şifrelerin eşleşip eşleşmediği) yapar.
*   **Avatar Önizleme:** Kullanıcı profil fotoğrafı seçtiğinde, form gönderilmeden önce anlık olarak önizlemesini gösterir.
*   **Otomatik Kapanan Alertler:** Başarı mesajlarının belirli bir süre sonra otomatik olarak kaybolmasını sağlar.
*   **Select2 için Floating Label Desteği:** Bootstrap 5 floating label'ları ile Select2 entegrasyonunu iyileştirir.
*   **Sayfaya Özgü İşlevler:**
    *   **Makale Yükleme:** Dosya tipi (PDF/Metin) toggle butonlarının çalışması, sürükle-bırak dosya yükleme alanı ve dosya önizlemesi.
    *   **E-posta Doğrulama Sayfası:** 6 haneli kod giriş inputları arasında otomatik geçiş, yapıştırma (paste) desteği, silme (backspace) ile önceki input'a odaklanma ve kodun geçerlilik süresi için **geri sayım sayacı.**
    *   **Şifre Sıfırlama Sayfaları:** E-posta formatının client-side doğrulaması, yeni şifre için güç ve eşleşme kontrolleri.
    *   **Makale Detay Sayfası:** Atıf kopyalama/indirme, özet değiştirme gibi interaktif tab içerikleri için temel fonksiyon çağrıları (fonksiyonların tam implementasyonu bu dosyada olmayabilir ama çağrı noktaları burada).
*   Kod, `DOMContentLoaded` olayıyla tetiklenerek sayfanın tüm elemanları yüklendikten sonra çalışır. Fonksiyonlar modüler bir şekilde organize edilmiştir.

---

## 🚀 Gelecekteki Geliştirmeler

Scholar Mate, halihazırda sunduğu zengin özellik setiyle bile güçlü bir platform olmasına rağmen, potansiyel olarak şu alanlarda daha da geliştirilebilir:

*   **Gelişmiş Arama ve Filtreleme:** Makaleler arasında tam metin arama, daha detaylı filtre seçenekleri (yazar, dergi vb. için autocomplete).
*   **Referans Yönetimi Entegrasyonu:** Zotero, Mendeley gibi popüler referans yönetim araçlarıyla entegrasyon veya içe/dışa aktarım (.bib, .ris formatları).
*   **İşbirlikçi Araçlar:** Aynı makale üzerinde birden fazla kullanıcının not alması, yorum yapması veya ortak düzenleme yapması.
*   **Gelişmiş Analitikler:** Kullanıcının okuma alışkanlıkları, en çok atıf yapılan makaleleri, araştırma alanlarındaki trendler gibi istatistiksel bilgiler.
*   **API Erişimi:** Diğer uygulamaların Scholar Mate verilerine (izinler dahilinde) erişebilmesi için bir RESTful API.
*   **Mobil Uygulama veya PWA Desteği:** Platforma mobil cihazlardan daha kolay erişim.
*   **Çoklu Dil Desteği.**

---

Scholar Mate, akademik araştırmaların yönetimi ve analizi için **kapsamlı, güvenli ve modern bir çözüm** sunma hedefiyle yola çıkmış etkileyici bir projedir. Geliştirme sürecindeki titizlik, kullanılan teknolojiler ve kullanıcı odaklı yaklaşım, projenin başarısını garantilemektedir.
