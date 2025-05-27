<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';
define('MAILHOST',"smtp.gmail.com");
define('USERNAME',"327hastanesibilgisistemi@gmail.com");
define('PASSWORD',"jvjk qkor afdo ptoi");
define('SEND_FROM',"327hastanesibilgisistemi@gmail.com");
define('SEND_FROM_NAME','327Hastanesi');
define('REPLY_TO',"info@327Hastanesi.com");
define('REPLY_TO_NAME','Ege');

function gonderEposta($email, $isim, $dogrulamaKodu) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = MAILHOST; 
        $mail->SMTPAuth = true;
        $mail->Username = USERNAME; 
        $mail->Password = PASSWORD; 
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->isHtml = true;
        $guncelYil = Date('Y');

        $mail->setFrom('info@ege.com', 'Scholar Mate');
        $mail->addAddress($email, $isim);

        $mail->isHTML(true);
        $mail->Subject = 'E-posta Doğrulama Kodu - Scholar Mate';

        $htmlContent = file_get_contents('../templates/emails/email_template.html');
        $htmlContent = str_replace(
            ['{{KULLANICI_ADI}}', '{{DOGRULAMA_KODU}}','{{GUNCEL_YIL}} '],
            [$isim, $dogrulamaKodu, $guncelYil],
            $htmlContent
        );

        $mail->Body = $htmlContent;

        $mail->send();
        echo 'E-posta başarıyla gönderildi.';
    } catch (Exception $e) {
        echo "E-posta gönderilemedi. Hata: {$mail->ErrorInfo}";
    }
}

function gonderEpostaSifreUnuttum($email, $isim ) {
    $mail = new PHPMailer(true);
    $baglanti =  $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/sifremiUnuttum/sifreYenile.php';
    try {
        $mail->isSMTP();
        $mail->Host = MAILHOST; 
        $mail->SMTPAuth = true;
        $mail->Username = USERNAME;
        $mail->Password = PASSWORD;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->isHtml = true;
        $guncelYil = Date('Y');

        $mail->setFrom('egenull@gmail.com', 'Scholar Mate');
        $mail->addAddress($email, $isim);

        $mail->isHTML(true);
        $mail->Subject = 'Sifremi Unuttum - Scholar Mate';

        $htmlContent = file_get_contents('../templates/emails/email_templateSifre.html');
        $htmlContent = str_replace(
            ['{{KULLANICI_ADI}}', '{{GUNCEL_YIL}}', '{{SIFIRLAMA_LINKI}}', '{{SIFIRLAMA_LINKI}}'],
            [$isim, $guncelYil, $baglanti, $baglanti],
            $htmlContent
        );

        $mail->Body = $htmlContent;

        $mail->send();
        echo 'E-posta başarıyla gönderildi.';
    } catch (Exception $e) {
        echo "E-posta gönderilemedi. Hata: {$mail->ErrorInfo}";
    }
}






?>