-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Anamakine: localhost
-- Üretim Zamanı: 17 Haz 2025, 21:08:25
-- Sunucu sürümü: 10.4.28-MariaDB
-- PHP Sürümü: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Veritabanı: `makale`
--

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `etiketler`
--

CREATE TABLE `etiketler` (
  `id` int(11) NOT NULL,
  `etiket_adi` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `kategoriler`
--

CREATE TABLE `kategoriler` (
  `id` int(10) UNSIGNED NOT NULL,
  `kategori` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `kategoriler`
--

INSERT INTO `kategoriler` (`id`, `kategori`) VALUES
(1, 'Yapay Zeka'),
(2, 'Veri Bilimi'),
(3, 'Makine Öğrenmesi'),
(4, 'Doğal Dil İşleme'),
(5, 'Etik ve Toplum');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `kaynaklar`
--

CREATE TABLE `kaynaklar` (
  `id` int(11) NOT NULL,
  `kullanici_id` int(11) NOT NULL,
  `baslik` varchar(255) DEFAULT NULL,
  `yazarlar` text DEFAULT NULL,
  `yayin_yili` smallint(6) DEFAULT NULL,
  `yayin_yeri` varchar(255) DEFAULT NULL,
  `doi` varchar(100) DEFAULT NULL,
  `atif_format` text DEFAULT NULL,
  `makale_id` int(10) UNSIGNED DEFAULT NULL,
  `olusturulma_tarihi` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `makaleler`
--

CREATE TABLE `makaleler` (
  `id` int(10) UNSIGNED NOT NULL,
  `baslik` varchar(255) NOT NULL,
  `yayin_yili` smallint(6) NOT NULL,
  `dergi_konferans` varchar(255) DEFAULT NULL,
  `doi` varchar(100) DEFAULT NULL,
  `ozet_manuel` text DEFAULT NULL,
  `yazarlar_raw` text NOT NULL COMMENT 'Formdan ";" ile ayrılmış hali',
  `anahtar_kelimeler_raw` text DEFAULT NULL COMMENT 'Formdan "," ile ayrılmış hali',
  `makale_icerigi` longtext NOT NULL,
  `dosya_tipi` varchar(10) NOT NULL,
  `orijinal_dosya_adi` varchar(255) DEFAULT NULL,
  `saklanan_dosya_yolu` varchar(500) DEFAULT NULL COMMENT 'Webden erişilecek yol',
  `kullanici_id` int(10) UNSIGNED DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `otomatik_ozet` text DEFAULT NULL,
  `eski_ozet` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `makaleler`
--

INSERT INTO `makaleler` (`id`, `baslik`, `yayin_yili`, `dergi_konferans`, `doi`, `ozet_manuel`, `yazarlar_raw`, `anahtar_kelimeler_raw`, `makale_icerigi`, `dosya_tipi`, `orijinal_dosya_adi`, `saklanan_dosya_yolu`, `kullanici_id`, `created_at`, `updated_at`, `otomatik_ozet`, `eski_ozet`) VALUES
(13, 'Makale Basligi', 2023, 's', 'a', 'Özet Manuel', 'a', 'a,s,d', 'Bölüm 1-Giriş Bölüm1 Giriş07/03/2025 1 Konular Profesyonel yazılım geliştirme ▪Yazılım mühendisliği ne demek. Yazılım mühendisliği etiği ▪Yazılım mühendisliğini etkileyen etik ve mesleki konular neler. Durum çalışmaları ▪Bütün kitapta örnek olarak kullanılananfarklı türlerdeki sistemleri tanıyacaksınız. Bölüm1 Giriş07/03/2025 2 Yazılım Mühendisliği TÜM gelişmiş ulusların ekonomileri yazılıma bağımlıdır. Giderek daha fazla sistem yazılım kontrollüdür. Yazılım mühendisliği, profesyonel yazılım geliştirme için teoriler, yöntemler ve araçlarla ilgilenir. Yazılıma yapılan harcamalar, tüm gelişmiş ülkelerde GSMH\'nin önemli bir bölümünü temsil etmektedir. Bölüm1 Giriş07/03/2025 3 Yazılım maliyetleri Yazılım maliyetleri genellikle bilgisayar sistemi maliyetlerine hakimdir. Bir PC\'deki yazılımın maliyeti genellikle donanım maliyetinden daha fazladır. Yazılımın bakımı, geliştirilmesinden daha maliyetlidir. Uzun ömürlü sistemler için bakım maliyetleri geliştirme maliyetlerinin birkaç katı olabilir. Yazılım mühendisliği, uygun maliyetli yazılım geliştirme ile ilgilidir. Bölüm1 Giriş07/03/2025 4 Yazılım projesi hataları Artan sistem karmaşıklığı ▪Yeni yazılım mühendisliği teknikleri daha büyük, daha karmaşık sistemler oluşturmamıza yardım ederken, talepler değişmektedir. ▪Sistemler daha hızlı geliştirilmeli ve teslim edilmeli; daha büyük, ve hatta daha karmaşık sistemlere gereksinim var ve sistemler daha önceden imkansız olarak düşünülen yeni yeteneklere sahip olmalı. ▪Daha karmaşık yazılım sunabilmenin zorluklarını karşılayabilmek için yeni yazılım mühendisliği teknikleri geliştirilmelidir. Yazılım mühendisliği yöntemlerini kullanmadaki başarısızlık. ▪Bilgisayar programlarını yazılım mühendisliği, yöntemlerini ve tekniklerini kullanmadan yazmak oldukça kolaydır. ▪Birçok şirket ürünleri ve servisler geliştikçe yazılım geliştirmeye sürüklenmişlerdir. ▪Günlük işlerinde yazılım mühendisliği yöntemlerini kullanmazlar. ▪Bu nedenle genellikle yazılımları olması gerekenden daha pahalı ve daha az güvenilirdir. ▪Bu probleme yönelmek için daha iyi yazılım mühendisliği öğretimine ve eğitimine ihtiyacımız var. Bölüm1 Giriş07/03/2025 5 Profesyonel yazılım geliştirme Bölüm1 Giriş07/03/2025 6 Yazılım mühendisliği hakkında sık sorulan sorular Bölüm1 Giriş07/03/2025 7 Yazılım mühendisliği hakkında sık sorulan sorular Bölüm1 Giriş07/03/2025 8 Yazılım ürünleri Genel özellikli (jeneric) yazılımlar. ▪Serbest piyasada onları satın alabilen herhangi bir müşteriye pazarlanan ve satılan bağımsız sistemlerdir.. ▪Örnekler–Mobil uygulamalar, grafik programları, proje yönetim araçları vb., bu tür yazılımlar aynı zamanda ; muhasebe sistemleri veya diş tedavileri ile ilgili kayıtları tutan sistemler gibi belirli bir pazar için tasarlanmış uygulamaları içerir.. Ismarlama yazılımlar ▪Belirli bir müşteri tarafından kendi ihtiyaçları için ısmarlanmış, belirli bir müşteri için geliştirilen sistemlerdir. ▪Örnekler–gömülü kontrol sistemleri, hava trafik kontrol yazılımı, trafik izleme sistemleri. Bölüm1 Giriş07/03/2025 9 Ürün özellikleri Genel özellikli (jeneric) yazılımlar ▪Yazılımın ne yapması gerektiğine ilişkin spesifikasyon yazılım geliştiricisine aittir ve yazılım değişikliği ile ilgili kararlar geliştirici tarafından verilir. Ismarlama yazılımlar ▪Yazılımın ne yapması gerektiğine ilişkin spesifikasyon, yazılımı satın alan kuruluşa aittir ve gerekli yazılım değişikliklerine ilişkin kararlar müşteri tarafından verilir. Bölüm1 Giriş07/03/2025 10 İyi yazılımın gerekli özellikleri Bölüm1 Giriş07/03/2025 11 Yazılım mühendisliği Yazılım mühendisliği, sistem spesifikasyonunun ilk aşamalarından sistemin kullanıma verildikten sonraki bakımına kadar yazılım üretiminin tüm yönleriyle ilgilenen bir mühendislik disiplinidir. Mühendislik disiplini ▪Kurumsal ve finansal kısıtlamaları göz önünde bulundurarak sorunları çözmek için uygun teori ve yöntemleri kullanmak. Yazılım üretiminin tüm yönleri ▪Sadece teknik geliştirme süreci değil. Aynı zamanda yazılım proje yönetimi ve yazılım geliştirmeyi desteklemek için araçların, yöntemlerin ve teorilerin geliştirilmesi. Bölüm1 Giriş07/03/2025 12 Yazılım mühendisliğinin önemi Kişiler ve toplum, giderek daha fazla gelişmiş yazılım sistemlerine güvenmektedirler. Emniyetli ve güvenilir sistemleri ekonomik ve hızlı bir şekilde üretebilmemiz gerekiyor. Genellikle, uzun vadede, profesyonel yazılım sistemleri için yazılım mühendisliği yöntemlerini ve tekniklerini kullanmak, kişisel bir programlama projesiymiş gibi sadece programlar yazmaktan daha ucuzdur. Yazılım mühendisliği yöntemini kullanmamak, sınama, kalite güvence ve uzun dönemli bakım için daha yüksek maliyetlere neden olur. Bölüm1 Giriş07/03/2025 13 Yazılım süreci faaliyetleri Müşterilerin ve mühendislerin üretilecek yazılımı ve işleyişindeki kısıtları tanımladıkları yazılım spesifikasyonu. Yazılımın tasarlandığı ve programlandığı yazılım geliştirme. Yazılımın, müşterinin ihtiyaçlarına uygun olduğunun sağlanması amacıyla kontrol edildiği yazılım doğrulama. Yazılımın değişen müşteri ve pazar gereksinimlerini yansıtacak şekilde değiştirildiği yazılım evrimi. Bölüm1 Giriş07/03/2025 14 Yazılımı etkileyen genel sorunlar Heterojenlik ▪Sistemlerin, gittikçe artarak farklı tür bilgisayar ve taşınabilir cihazlar içeren ağlar arasında dağıtılmış sistemler olarak çalışması gerekmektedir. İş ve sosyal değişim ▪İş dünyası ve toplum, yeni ekonomiler geliştikçe ve yeni teknolojiler elde edildikçe inanılmaz derecede hızla değişir. Mevcut yazılımlarını değiştirebilmeye ve hızlıca yeni yazılım geliştirebilmeye gerek duyarlar. Güvenlik ve güven ▪Yazılım hayatımızın her alanına dahil oldukça, o yazılıma güvenebilmemiz şarttır. Ölçek ▪Yazılım, taşınabilir veya giyilebilir cihazlardaki çok küçük gömülü sistemlerden küresel bir topluluğa hizmet eden İnternet ölçeğinde, bulut tabanlı sistemlere kadar çok geniş bir ölçek yelpazesinde geliştirilmelidir. Bölüm1 Giriş07/03/2025 15 Yazılım mühendisliği çeşitliliği Pek çok farklı yazılım sistemi türü vardır ve bunların hepsine uygulanabilecek evrensel bir yazılım mühendisliği seti yoktur. Kullanılan yöntemler, araçlar ve teknikler yazılımı geliştiren kuruluşa, yazılımın türüne ve geliştirme sürecine katılan kişilere bağlıdır. Bölüm1 Giriş07/03/2025 16 Uygulama türleri Bağımsız uygulamalar ▪Bunlar, PC gibi kişisel bir bilgisayarda çalışan uygulama sistemleridir. Gerekli tüm fonksiyonelliği içerirler ve bir ağa bağlı olmaları gerekmez. Etkileşimli işlem tabanlı uygulamalar ▪Uzak bir bilgisayarda çalışan ve kullanıcılar tarafından kendi bilgisayarlarından veya telefon/tabletlerinden erişilen uygulamalardır. Bunlar, e-ticaret uygulamaları gibi web uygulamalarını içerir. Gömülü kontrol sistemleri ▪Bunlar, donanım cihazlarını kontrol eden ve yöneten yazılım kontrol sistemleridir. Sayısal olarak, belki de diğer bütün sistem türlerinden daha fazla gömülü sistem vardır. Bölüm1 Giriş07/03/2025 17 Uygulama türleri Deste işleme sistemleri ▪Bunlar, verileri büyük desteler halinde işlemek için tasarlanmış iş sistemleridir. Çok sayıda girdiye karşılık gelen sonuçları üretirler. Eğlence sistemleri ▪Bunlar, kişisel kullanım için kullanıcıyı eğlendirmeyi amaçlayan sistemlerdir. Modelleme ve simülasyon sistemleri ▪Bunlar, bilim adamları ve mühendisler tarafından fiziksel süreçleri ve durumları modellemek için geliştirilen, birçok ayrı, etkileşen nesneleri içeren sistemlerdir. Bölüm1 Giriş07/03/2025 18 Uygulama türleri Veri toplama ve analiz sistemleri ▪Bunlar, bir dizi sensör kullanarak çevrelerinden veri toplayan ve bu verileri işlenmek üzere diğer sistemlere gönderen sistemlerdir. Sistemlerin sistemleri ▪Bunlar, bir dizi başka yazılım sisteminden oluşan sistemlerdir. Bölüm1 Giriş07/03/2025 19 Yazılım mühendisliği temelleri Bazı temel ilkeler, kullanılan geliştirme tekniklerinden bağımsız olarak tüm yazılım sistemi türleri için geçerlidir: ▪Sistemler, yönetilebilen ve anlaşılan bir yazılım süreci kullanılarak geliştirilmelidirler. Tabii ki, farklı yazılım türleri için farklı süreçler kullanılır. ▪Güvenilebilirlik ve performans tüm sistem türleri için önemlidir. ▪Yazılım spesifikasyonunu ve gereksinimlerini (yazılımın ne yapması gerektiğini) anlamak ve yönetmek önemlidir. ▪Uygun olduğu durumda, yeni yazılım yazmak yerine daha önceden geliştirilmiş olan yazılımı yeniden kullanmalısınız. Bölüm1 Giriş07/03/2025 20 İnternet yazılım mühendisliği Web artık uygulama çalıştırmak için bir platformdur ve kuruluşlar yerel sistemlerden ziyade giderek daha fazla web tabanlı sistemler geliştirmektedir. Web hizmetleri (Bölüm 19\'da ele alınmıştır), uygulama işlevlerine web üzerinden erişilmesine izin verir. Bulut bilgi işlem, uygulamaların \"bulut\" üzerinde uzaktan çalıştığı bilgisayar hizmetlerinin sağlanmasına yönelik bir yaklaşımdır. ▪Kullanıcılar yazılımı satın almazlar, yazılımın kullanım miktarına göre ödeme yaparlar. Bölüm1 Giriş07/03/2025 21 Web tabanlı yazılım mühendisliği Web tabanlı sistemler karmaşık dağıtılmış sistemlerdir, ancak daha önce tartışılan yazılım mühendisliğinin temel ilkeleri, diğer herhangi bir sistem türü için olduğu kadar bunlar için de geçerlidir. Yazılım mühendisliğinin temel fikirleri, diğer tür yazılımlara olduğu gibi web tabanlı yazılımlara da uygulanır. Bölüm1 Giriş07/03/2025 22 Web yazılım mühendisliği Yazılımın yeniden kullanımı ▪Web-tabanlı sistemleri oluşturmak için yazılımın yeniden kullanımı baskın yaklaşım haline gelmiştir. Bu sistemleri inşa ederken, çoğunlukla bir çerçevede bir araya getirilmiş olan mevcut yazılım bileşenlerini ve sistemlerini nasıl birleştireceğinizi düşünürsünüz. Kademeli ve çevik geliştirme ▪Web tabanlı sistemler aşamalı olarak geliştirilmekte ve teslim edilmektedir. Bu tür sistemler için tüm gereksinimleri önceden belirlemenin pratik olmadığı artık genel olarak kabul edilmektedir. Bölüm1 Giriş07/03/2025 23 Web yazılım mühendisliği Servis yönelimli sistemler ▪Yazılım, yazılım bileşenlerinin tek web servisleri olduğu servis yönelimli yazılım mühendisliği kullanılarak gerçekleştirilebilir. Gelişmiş arayüzler ▪Bir web tarayıcısındaki gelişmiş arayüzlerin oluşturulmasını destekleyen AJAX ve HTML5 gibi arayüz geliştirme teknolojileri ortaya çıkmıştır. Bölüm1 Giriş07/03/2025 24 Yazılım mühendisliği etiği Bölüm1 Giriş07/03/2025 25 Yazılım mühendisliği etiği Yazılım mühendisliği, sadece teknik becerilerin uygulanmasından daha geniş sorumluluklar içerir. Profesyonel yazılım mühendisleri, saygı görmek için dürüst ve etik açıdan sorumlu bir şekilde davranmalıdır. Etik davranış,yalnızcayasalara bağlı kalmaktan çok daha fazlasıdır, ahlaki açıdan doğru olan bir dizi ilkeyi izlemeyi de gerektirir. Bölüm1 Giriş07/03/2025 26 Mesleki sorumluluk konuları Gizlilik ▪Mühendisler normal olarak, resmi bir gizlilik sözleşmesi imzalanmış olsun veya olmasın, normalde işverenlerinin veya müşterilerinin gizliliğine saygı göstermelidir. Yeterlilik ▪Mühendisler yeterlilik düzeylerini farklı göstermemelidirler. Kendi yeterlilikleri dışında bir işi bilerek kabul etmemelidirler. Bölüm1 Giriş07/03/2025 27 Mesleki sorumluluk konuları Fikri mülkiyet hakları ▪Mühendisler, patentler, telif hakları vb. gibi fikri mülkiyetin kullanımını yöneten yerel yasalardan haberdar olmalıdır. İşverenlerin ve müşterilerin fikri haklarının korunmasını sağlamak için dikkatli olmalıdırlar. Bilgisayarların kötü kullanımı ▪Yazılım mühendisleri, teknik yeteneklerini başkalarının bilgisayarlarını kötüye kullanmak için kullanmamalıdır. Bilgisayarın kötüye kullanımı, oldukça basitten (örneğin, bir işverenin makinesinde oyun oynamak) çok ciddiye kadar (virüslerin yayılması) değişebilir. Bölüm1 Giriş07/03/2025 28 ACM/IEEE Etik Kuralları ABD\'deki profesyonel topluluklar, bir etik uygulama kodu oluşturmak için işbirliği yaptı. Bu kuruluşların üyeleri ve yazılım mühendisleri bu etik kurallar ve mesleki uygulamalara bağlı kalmalıdırlar. Bu kurallar, uygulayıcılar, eğitimciler, yöneticiler, süpervizörler ve kural koyucular ile mesleğin öğrencileri dahil profesyonel yazılım mühendislerinin davranışlarıyla ve aldıkları kararlarla ilgili sekiz İlke içerir. Bölüm1 Giriş07/03/2025 29 Etik kurallarının gerekçesi ▪Bilgisayarların ticarette, endüstride, devlette, tıpta, eğitimde, eğlencede ve toplumun genelinde merkezi ve artan bir rolü vardır. Yazılım mühendisleri, yazılım sistemlerinin analiz, spesifikasyon, tasarım, geliştirilme, sertifikasyon, bakım ve testlerine doğrudan katılımla veya öğretimle katkıda bulunurlar. ▪Yazılım sistemlerinin geliştirilmesindeki rolleri nedeniyle yazılım mühendislerinin, iyi şeyler yapmak veya zarar vermek, başkalarının iyi şeyler yapmak veya zarar vermesini mümkün kılmak veya başkalarını iyi şeyler yapmak veya zarar vermeleri için etkileme fırsatları vardır. Yazılım mühendisleri, çabalarının mümkün olduğunca iyiye kullanılmasını sağlamak için, yazılım mühendisliğini yararlı ve saygı duyulan bir meslek yapmaya sorumlu kılmalıdırlar. Bölüm1 Giriş07/03/2025 30 ACM/IEEE Etik Kuralları Bölüm1 Giriş07/03/2025 31 Etik ilkeler Bölüm1 Giriş07/03/2025 32 Durum çalışmaları Bölüm1 Giriş07/03/2025 33 Etik ikilemler Üst yönetimin politikalarıyla prensipte uyuşmazlık. İşverenin etik olmayan bir şekilde hareket edip, emniyet-kritik tehlike arz eden bir sistemi testler bitmeden teslim etmesi (emniyet doğrulama kayıtlarında sahtecilik yapması). Askeri ve nükleer sistemlerin geliştirilmesinde yer almak. Bölüm1 Giriş07/03/2025 34 Durum çalışmaları İnsülin pompası kontrol sistemi ▪Şeker hastaları tarafından kullanılan, kullanıcıya kontrollü olaraninsülin dozu veren bir pompayı yöneten gömülü bir sistemdir. Ruh sağlığı vakası hasta bilgi sistemi (Mentcare) ▪(ZihinSaSsistemi) Ruh sağlığı problemleri olan hastalar ve aldıkları tedaviler hakkında bilgi tutan bir tıbbi bilgi sistemidir. . Kır hava istasyonu ▪Uzak bölgelerdeki hava koşulları hakkında bilgi toplayan bir veri toplama sistemi. Dijital bir öğrenme ortamı (iLearn) ▪Okullarda öğrenmeyi destekleyen bir sistem. Bölüm1 Giriş07/03/2025 35 İnsülin pompası kontrol sistemi Kan şekeri sensöründen veri toplar ve enjekte edilmesi gereken insülin miktarını hesaplar. Kan şekeri düzeylerinin değişim oranına dayalı hesaplama. Doğru insülin dozunu vermek için minyatürleştirilmiş bir pompaya sinyaller gönderir. Düşük kan şekeri kısa dönemde, beyin fonksiyonu bozukluklarıyla başlayarak en sonunda bilinç kaybı ve , ölümle sonuçlanabileceği için daha ciddi bir durumdur. Bununla birlikte uzun dönemde, sürekli yüksek kan şekeri, göz hasarına, böbrek hasarına ve kalp problemlerine neden olabilir. Bölüm1 Giriş07/03/2025 36 İnsülin pompası donanım mimarisi Bölüm1 Giriş07/03/2025 37 İnsülin pompasının aktivite modeli Bölüm1 Giriş07/03/2025 38 Üst düzey gereksinimler Sistem gerektiğinde insülin göndermek için hazır olmalıdır. Sistem güvenilir bir şekilde çalışmalı ve mevcut kan şekeri seviyesini dengelemek için doğru miktarda insülin göndermelidir. Bu nedenle sistem, her zaman gereksinimleri karşılayacak şekilde tasarlanmalı ve uygulanmalıdır. Bölüm1 Giriş07/03/2025 39 ZihinSas: Ruh sağlığını desteklemek için bir hasta bilgi sistemi Ruh sağlığını desteklemek için bir hasta bilgi sistemi, ruh sağlığı problemleri olan hastalar ve aldıkları tedaviler hakkında bilgi tutan bir tıbbi bilgi sistemidir. Çoğu ruh sağlığı hastası özel hastane tedavisine ihtiyaç duymaz, fakat problemleri hakkında ayrıntılı bilgi sahibi olan bir doktorla görüşebilecekleri uzman kliniklere düzenli olarak gitmeye ihtiyaç duyarlar. Hastaların gelmesini kolaylaştırmak için bu klinikler sadece hastanelerde yer almaz. Ayrıca yerel sağlık kuruluşlarında veya halk merkezlerinde bulunabilirler. Bölüm1 Giriş07/03/2025 40 ZihinSaS ZihinSaS, kliniklerde kullanılmak üzere tasarlanmış bir hasta bilgi sistemidir. Merkezi bir hasta bilgi veri tabanı kullanılır fakat aynı zamanda güvenli ağ bağlantısı olmayan sitelerden erişilip kullanılabilmesi için bir dizüstü bilgisayarda çalışabilecek şekilde tasarlanmıştır. Yerel sistemler güvenli ağ bağlantısı olduğu zaman, veri tabanındaki hasta bilgilerini kullanırlar fakat bağlantı olmadığı zaman hasta kayıtlarının yerel kopyalarını kullanabilirler. Bölüm1 Giriş07/03/2025 41 ZihinSaS’ınamaçları Sağlık servis yöneticilerinin yerel ve devlet hedeflerine göre performansının değerlendirebilmesine olanak tanıyan yönetim bilgilesioluşturmak. Hastaların tedavisini desteklemek için tıbbi personele zamanında veri sağlamak. Bölüm1 Giriş07/03/2025 42 ZihinSaSsisteminin organizasyonu Bölüm1 Giriş07/03/2025 43 ZihinSaSsisteminin temel özellikleri Bireysel bakım yönetimi ▪Klinik tedavi uzmanları hastalar için kayıtlar oluşturabilir, sistemdeki bilgileri düzenleyebilir, hasta geçmişini görüntüleyebilir, vb. Hasta izleme ▪Sistem düzenli olarak tedavi edilen hasta kayıtlarını izler ve olası problemler farkedildiğindeuyarılar verir. İdari raporlama ▪Sistem, her klinikte tedavi gören hasta sayısını, bakım sistemine giren ve çıkan hasta sayısını, ayrı bir bölümde tutulan hasta sayısını, reçete edilen ilaçları ve maliyetlerini vb. gösteren aylık yönetim raporları oluşturur. Bölüm1 Giriş07/03/2025 44 ZihinSaSsistem kaygıları Gizlilik ▪Hasta bilgilerinin gizli kalması ve yetkili tıbbi personel ve hastaların dışında hiç kimseye ifşa edilmemesi esastır. Emniyet ▪Bazı akıl hastalıkları, hastaların intihara meyilli olmasına veya diğer insanlar için tehlike oluşturmasına neden olur. Mümkün olduğu durumda sistem tıbbi personeli, potansiyel olarak intihara meyilli veya tehlikeli hastalar hakkında uyarmalıdır. ▪Sistem gerektiğinde kullanılabilir olmalıdır, aksi takdirde gizliliği ihlal edilebilir ve hastalara doğru ilaçları reçete etmek mümkün olmayabilir. Bölüm1 Giriş07/03/2025 45 Kır hava istasyonu İklim değişikliğini izlemeye yardımcı olmak ve uzak alanlardaki hava tahminlerinin doğruluğunu arttırmak için çok büyük kır alanları olan bir ülkenin hükümeti uzak alanlarda yüzlerce hava istasyonu kurmaya karar verir. Meteoroloji istasyonları sıcaklığı, basıncı, güneşin parlaklığını, yağış miktarını, rüzgarın hızını ve rüzgar yönünü ölçen bir dizi araçtan veri toplarlar. ▪Meteoroloji istasyonları, rüzgarın hızı ve yönü, yer ve hava sıcaklıkları, barometrik basınç ve 24 saatlik periyotta yağan yağmur miktarı gibi hava parametrelerini ölçen bir dizi araç içerir. Bu araçların her biri, parametreleri periyodik olarak okuyan ve araçlardan toplanan verileri yöneten bir yazılım sistemi tarafından kontrol edilir.  Bölüm1 Giriş07/03/2025 46 Meteoroloji istasyonunun çevresi Bölüm1 Giriş07/03/2025 47 Hava durumu bilgi sistemi Meteoroloji istasyonu sistemi ▪Bu sistem hava verisi toplamaktan, bazı başlangıç veri işlemeyi gerçekleştirmekten ve veri yönetimi sistemine aktarmaktan sorumludur. Veri yönetimi ve arşivleme sistemi ▪Bu sistem, tüm kır hava istasyonlarından veriler toplar, veri işleme ve analizi gerçekleştirir ve verileri arşivler. İstasyon bakım sistemi ▪Bu sistem uydu aracılığıyla, tüm kır hava istasyonları ile, bu sistemlerin sağlamlığını izlemek ve problemler hakkında raporlar sağlamak için haberleşirler. Bölüm1 Giriş07/03/2025 48 Ek yazılım işlevselliği Araçları, güç ve haberleşme donanımını izler ve kusurları yönetim sistemine raporlar. Sistemin gücünü yönetir, çevre koşulları izin verdiği zaman pillerin şarj edilmesini ama aynı zamanda şiddetli rüzgar gibi potansiyel olarak olumsuz hava durumlarında jeneratörlerin kapatılmasını sağlar. Yazılımın bölümlerinin yeni sürümleriyle değiştirilmesine ve sistem arızası durumunda yedek araçlarının sisteme alınmasına yani dinamik olarak yeniden yapılandırmayı destekler. Bölüm1 Giriş07/03/2025 49 Okullar için Dijital öğrenme ortamı Dijital öğrenme ortamı, öğrenme için bir dizi genel amaçlı ve özel olarak tasarlanmış araçların gömülü olabildiği ve sistemi kullanan öğrencilerin gereksinimlerine yönelik bir dizi uygulamanın bulunduğu bir çerçevedir. Ortamın her sürümünde yer alan araçlar, öğretmenler ve öğrenciler tarafından belirli gereksinimlere uygun olarak seçilir. ▪Bunlar hesap çizelgesi, ödev teslimi ve değerlendirmesi yönetmek için Sanal Öğrenme Ortamı (VLE) gibi öğrenme yönetimi uygulamaları, oyunlar ve simülasyonlar gibi genel uygulamalar olabilir. Bölüm1 Giriş07/03/2025 50 Servis tabanlı sistemler Sistem, tüm sistem bileşenlerinin değiştirilebilir servisler olarak düşünüldüğü servis tabanlı bir sistemdir. Ortam, yeni servisler mevcut olduğu zaman sistemin kademeli olarak güncellenmesini sağlar. Ayrıca, sistemin kullanıcılarının yaşlarına uygun olarak farklı sürümlerini sağlayabileyecekşekilde tasarlanmmıştır. Bölüm1 Giriş07/03/2025 51 Dijital öğrenme servisleri Yardımcı servisler, temel uygulamadan bağımsız fonksiyonellik sağlayan ve sistemdeki diğer servisler tarafından kullanılabilen servislerdir. Uygulama servisleri, e-posta, konferans, fotoğraf paylaşımı vb. gibi özel uygulamalar ve bilimsel filmler veya tarihi kaynaklar gibi özel eğitimsel içeriklerine erişim sağlayan servislerdir. Konfigürasyon servisleri, bir dizi uygulama servisiyle ortamı uyarlamak ve servislerin öğrenciler, öğretmenler ve ebeveynler arasında nasıl paylaşıldığını tanımlamak için kullanılan servislerdir. Bölüm1 Giriş07/03/2025 52 Dijital öğrenme ortamının mimarisi Bölüm1 Giriş07/03/2025 53 Dijital öğrenme servis entegrasyonu Entegre servisler, bir API (uygulama programlama arayüzü-UPA) sunan ve diğer servisler tarafından o API aracılığıyla erişilebilen servislerdir. Bu nedenle doğrudan servisten servise iletişim mümkündür. Bağımsız servisler, basitçe bir tarayıcı arayüzü aracılığıyla erişilebilen ve diğer servislerden bağımsız olarak çalışan servislerdir. Bilgi diğer servislerle kopyala ve yapıştır gibi sadece açık kullanıcı hareketleriyle paylaşılabilir; her bağımsız servis için kimlik doğrulaması gerekebilir. Bölüm1 Giriş07/03/2025 54 Anahtar noktalar Bölüm1 Giriş07/03/2025 55 Anahtar noktalar Bölüm1 Giriş07/03/2025 56', 'PDF', 'Ch1 Giris.pdf', 'assets/uploads/pdf/Ch1Giris_68363e2166996.pdf', 7, '2025-05-28 01:35:20', '2025-05-28 01:35:20', 'Bu bölüm, yazılım mühendisliğinin temel prensiplerini ve önemini vurgulamaktadır. Günümüz ekonomilerinin yazılıma olan bağımlılığına dikkat çekilerek, yazılım mühendisliğinin profesyonel yazılım geliştirme için gerekli teori, yöntem ve araçları sağladığı belirtilmektedir. Yazılım maliyetlerinin donanım maliyetlerini aştığı ve bakım maliyetlerinin geliştirme maliyetlerini katladığı vurgulanarak, yazılım mühendisliğinin maliyet etkin çözümler sunma gerekliliği üzerinde durulmaktadır. Artan sistem karmaşıklığı ve yazılım mühendisliği yöntemlerinin kullanımındaki eksiklikler, yazılım projelerindeki hataların temel nedenleri olarak gösterilmektedir.\n\nGenel ve ısmarlama yazılımlar arasındaki farklar açıklanırken, iyi bir yazılımın temel özellikleri tanımlanmaktadır. Yazılım mühendisliğinin, sistem spesifikasyonundan bakıma kadar yazılım üretiminin tüm aşamalarını kapsayan bir mühendislik disiplini olduğu belirtilmektedir. Yazılım süreci faaliyetleri; spesifikasyon, geliştirme, doğrulama ve evrim olarak sıralanmaktadır. Heterojenlik, iş ve sosyal değişim, güvenlik ve ölçek gibi yazılımı etkileyen genel sorunlara değinilmektedir.\n\nFarklı yazılım sistemi türleri ve bunlara uygulanabilecek evrensel bir yazılım mühendisliği yaklaşımının olmadığı vurgulanmaktadır. Bağımsız uygulamalar, etkileşimli işlem tabanlı uygulamalar, gömülü kontrol sistemleri, deste işleme sistemleri, eğlence sistemleri, modelleme ve simülasyon sistemleri, veri toplama ve analiz sistemleri ile sistemlerin sistemleri gibi çeşitli uygulama türleri örneklerle açıklanmaktadır. Yeniden kullanım, güvenilirlik, performans, gereksinimlerin yönetimi gibi yazılım mühendisliği temelleri üzerinde durulmaktadır.\n\nİnternet ve web tabanlı yazılım mühendisliğinin önemi vurgulanarak, servis yönelimli sistemler ve gelişmiş arayüzlerin rolü belirtilmektedir. Yazılım mühendisliği etiği ve mesleki sorumluluk konuları ele alınarak, gizlilik, yeterlilik, fikri mülkiyet hakları ve bilgisayarın kötüye kullanımı gibi etik ihlallerine dikkat çekilmektedir. ACM/IEEE Etik Kuralları\'na değinilerek, yazılım mühendislerinin etik davranış ilkelerine uymaları gerektiği vurgulanmaktadır.\n\nSon olarak, insülin pompası kontrol sistemi, ruh sağlığı hasta bilgi sistemi (Mentcare), kır hava istasyonu ve dijital öğrenme ortamı (iLearn) gibi çeşitli durum çalışmaları incelenerek, yazılım mühendisliğinin farklı alanlardaki uygulamaları ve karşılaşılan etik ikilemler örneklendirilmektedir.', NULL),
(14, 'Basol', 2023, '12314', '1234593214', 'Manuel Özet Burada', 'ege,ali,veli', 'Ege,1,2,3,4', 'Kullanıcı Gereksinimleri Raporu Proje Başlığı: Web Tabanlı Akademik Makale Yönetim Sistemi Hazırlayan: Daren K. Arga 1. Giriş Akademik ve bilimsel çalışmalarla ilgilenen bir araştırmacı olarak, araştırma sürecim boyunca bilimsel makaleleri verimli bir şekilde yönetebileceğim web tabanlı bir sisteme ihtiyacım var. Bu sistem, makaleleri yüklememe, saklamama, özetlememe, not almama ve kaynak göstermeme olanak tanımalı; bu sayede zaman kazanmamı ve manuel işleri azaltmamı sağlamalıdır. Sistem, kullanılabilirliğe, doğruluğa ve akademik iş akışlarına olan desteğe öncelik vermelidir. 2. Kullanıcı Hedefleri ve Beklentileri Bir araştırmacı olarak sistemden temel beklentilerim şunlardır: • Bilimsel makaleleri toplama ve düzenleme sürecini kolaylaştırmak. • Otomatik özetlemeler ile makaleleri hızlıca anlayabilmek. • Kişiselleştirilmiş notlar ve açıklamalar ekleyebilmek. • Yaygın kullanılan akademik formatlarda otomatik kaynak oluşturma desteği. • Kullandığım veya kullanmayı planladığım kaynaklardan oluşan aranabilir bir kütüphane. • Temiz, sezgisel ve farklı cihazlarda erişilebilir bir kullanıcı arayüzü. 3. Fonksiyonel Gereksinimler (Kullanıcı Odaklı) 3.1 Makale Yükleme ve Saklama • PDF veya metin formatında makale yüklemek istiyorum. • Sistem, birden fazla dosyanın toplu olarak yüklenmesine izin vermeli. • Yüklenen makaleleri saklayıp kolay erişim için kategorilere ayırmalı. 3.2 Metadata Girişi ve Düzenleme • Aşağıdaki alanlar için metadata girişi yapabilmeliyim: o Başlık o Yazarlar o Dergi/Konferans Adı o Yayın Yılı o DOI (varsa) o Özet o Anahtar Kelimeler • Yükleme sonrasında bu bilgileri düzenleyebilmeliyim. 3.3 Otomatik Makale Özeti Oluşturma • Sistem her yüklenen makale için otomatik bir özet oluşturmalı. • Özetler kısa ama makalenin amacı ve bulgularını anlamak için yeterli olmalı. • Gerekirse özetleri yeniden oluşturabilmeli veya manuel olarak düzenleyebilmeliyim. 3.4 Notlar ve Açıklamalar • Her makale için kişisel notlar ekleyebilmeliyim. • Bu notlar zaman damgalı olmalı ve isteğe bağlı olarak belirli bölümlere (örneğin sayfa veya paragraf) bağlanabilmeli. • Notları düzenleyip silebilmeliyim. 3.5 Kaynak ve Atıf Yönetimi • Her makale için APA, MLA, Chicago gibi farklı formatlarda kaynak ve atıf oluşturabilmeliyim. • Bu atıfları kopyalayabilmeli veya dışa aktarabilmeliyim. • Sistem, gerektiğinde metin içi atıfları da desteklemeli. 3.6 Kaynak Kütüphanesi • Kendi yazdığım makalelerde kullandığım kaynakları kaydedebilmeliyim. • Bu kaynaklar aranabilir olmalı ve projeye, konuya ya da makaleye göre gruplanabilmeli. • Kaynakları standart formatlarda (örneğin BibTeX, RIS) içe/dışa aktarabilmeliyim. 4. Kullanılabilirlik ve Arayüz Gereksinimleri • Arayüz sade, temiz ve masaüstü, tablet ve mobilde uyumlu olmalı. • Navigasyon sezgisel olmalı, öğrenme süreci minimumda tutulmalı. • Makaleleri ve kaynakları anahtar kelime, yazar veya yıl gibi kriterlerle arayabilmeli ve filtreleyebilmeliyim. • Sistem koyu tema veya özelleştirilebilir görünüm ayarlarını desteklemeli. 5. Fonksiyonel Olmayan Gereksinimler • Veri Güvenliği: Yüklenen dosyalar, notlar ve metadata güvenli bir şekilde saklanmalı ve yalnızca benim erişimime açık olmalı (paylaşmayı seçmedikçe). • Güvenilirlik: Yükleme veya düzenleme sırasında veri kaybı olmamalı. • Performans: Özet ve kaynak oluşturma işlemleri hızlı olmalı (ideal olarak birkaç saniye içinde tamamlanmalı). • Ölçeklenebilirlik: Sistem, yüzlerce hatta binlerce makaleden oluşan kişisel bir kütüphaneyi sorunsuz şekilde desteklemeli. 6. Geleceğe Yönelik Düşünceler (Opsiyonel fakat Tercih Edilenler) • Zotero, Mendeley veya EndNote gibi referans yöneticileriyle entegrasyon. • Makaleleri ve notları meslektaşlarla paylaşabilmek için iş birliği özellikleri. • Notlar ve özetler için sürüm kontrolü veya değişiklik geçmişi. • Daha iyi organizasyon için etiketleme sistemi. 7. Sonuç Bu sistem, araştırmalarımda kullandığım literatürü yönetmek için dijital bir çalışma alanı işlevi görmelidir. Tekrarlayan işleri azaltmalı, akademik yazım sürecini desteklemeli ve düzenli bir bilgi tabanı oluşturmama yardımcı olmalıdır. Gerçek araştırma iş akışlarına uygun, kullanıcı dostu ve sağlam bir araç görmeyi dört gözle bekliyorum.', 'PDF', 'btbs316.pdf', 'assets/uploads/pdf/btbs316_683790e4dd50d.pdf', 7, '2025-05-29 01:40:40', '2025-05-29 01:40:40', 'Bu rapor, araştırmacılar için web tabanlı bir akademik makale yönetim sistemine duyulan ihtiyacı belirtmektedir. Sistem, kullanıcıların makaleleri yüklemesine, saklamasına, özetlemesine, not almasına ve çeşitli formatlarda otomatik kaynakça oluşturmasına olanak tanıyarak akademik çalışma süreçlerini kolaylaştırmayı hedeflemektedir. Temel fonksiyonel gereksinimler arasında PDF/metin formatında makale yükleme, metadata girişi ve düzenleme, otomatik özet oluşturma, kişisel not ekleme, farklı formatlarda kaynak ve atıf yönetimi ile aranabilir bir kaynak kütüphanesi bulunmaktadır. Kullanılabilirlik açısından, sistemin arayüzünün sade, sezgisel ve farklı cihazlarla uyumlu olması beklenmektedir. Fonksiyonel olmayan gereksinimler arasında veri güvenliği, güvenilirlik, performans ve ölçeklenebilirlik vurgulanmaktadır. Geleceğe yönelik olarak, referans yöneticileriyle entegrasyon, iş birliği özellikleri ve sürüm kontrolü gibi ek özellikler de değerlendirilmektedir. Amaç, araştırmacılara literatür yönetimi için dijital bir çalışma alanı sağlayarak verimliliği artırmak ve düzenli bir bilgi tabanı oluşturmaktır.', NULL),
(16, 'Makale Başlık', 2023, 'Dergi Adı', 'DOİ no', 'Özet Manuel', 'Yazar1 , yazar2', '1,2,3,4,5', 'Kullanıcı Gereksinimleri Raporu Proje Başlığı: Web Tabanlı Akademik Makale Yönetim Sistemi Hazırlayan: Daren K. Arga 1. Giriş Akademik ve bilimsel çalışmalarla ilgilenen bir araştırmacı olarak, araştırma sürecim boyunca bilimsel makaleleri verimli bir şekilde yönetebileceğim web tabanlı bir sisteme ihtiyacım var. Bu sistem, makaleleri yüklememe, saklamama, özetlememe, not almama ve kaynak göstermeme olanak tanımalı; bu sayede zaman kazanmamı ve manuel işleri azaltmamı sağlamalıdır. Sistem, kullanılabilirliğe, doğruluğa ve akademik iş akışlarına olan desteğe öncelik vermelidir. 2. Kullanıcı Hedefleri ve Beklentileri Bir araştırmacı olarak sistemden temel beklentilerim şunlardır: • Bilimsel makaleleri toplama ve düzenleme sürecini kolaylaştırmak. • Otomatik özetlemeler ile makaleleri hızlıca anlayabilmek. • Kişiselleştirilmiş notlar ve açıklamalar ekleyebilmek. • Yaygın kullanılan akademik formatlarda otomatik kaynak oluşturma desteği. • Kullandığım veya kullanmayı planladığım kaynaklardan oluşan aranabilir bir kütüphane. • Temiz, sezgisel ve farklı cihazlarda erişilebilir bir kullanıcı arayüzü. 3. Fonksiyonel Gereksinimler (Kullanıcı Odaklı) 3.1 Makale Yükleme ve Saklama • PDF veya metin formatında makale yüklemek istiyorum. • Sistem, birden fazla dosyanın toplu olarak yüklenmesine izin vermeli. • Yüklenen makaleleri saklayıp kolay erişim için kategorilere ayırmalı. 3.2 Metadata Girişi ve Düzenleme • Aşağıdaki alanlar için metadata girişi yapabilmeliyim: o Başlık o Yazarlar o Dergi/Konferans Adı o Yayın Yılı o DOI (varsa) o Özet o Anahtar Kelimeler • Yükleme sonrasında bu bilgileri düzenleyebilmeliyim. 3.3 Otomatik Makale Özeti Oluşturma • Sistem her yüklenen makale için otomatik bir özet oluşturmalı. • Özetler kısa ama makalenin amacı ve bulgularını anlamak için yeterli olmalı. • Gerekirse özetleri yeniden oluşturabilmeli veya manuel olarak düzenleyebilmeliyim. 3.4 Notlar ve Açıklamalar • Her makale için kişisel notlar ekleyebilmeliyim. • Bu notlar zaman damgalı olmalı ve isteğe bağlı olarak belirli bölümlere (örneğin sayfa veya paragraf) bağlanabilmeli. • Notları düzenleyip silebilmeliyim. 3.5 Kaynak ve Atıf Yönetimi • Her makale için APA, MLA, Chicago gibi farklı formatlarda kaynak ve atıf oluşturabilmeliyim. • Bu atıfları kopyalayabilmeli veya dışa aktarabilmeliyim. • Sistem, gerektiğinde metin içi atıfları da desteklemeli. 3.6 Kaynak Kütüphanesi • Kendi yazdığım makalelerde kullandığım kaynakları kaydedebilmeliyim. • Bu kaynaklar aranabilir olmalı ve projeye, konuya ya da makaleye göre gruplanabilmeli. • Kaynakları standart formatlarda (örneğin BibTeX, RIS) içe/dışa aktarabilmeliyim. 4. Kullanılabilirlik ve Arayüz Gereksinimleri • Arayüz sade, temiz ve masaüstü, tablet ve mobilde uyumlu olmalı. • Navigasyon sezgisel olmalı, öğrenme süreci minimumda tutulmalı. • Makaleleri ve kaynakları anahtar kelime, yazar veya yıl gibi kriterlerle arayabilmeli ve filtreleyebilmeliyim. • Sistem koyu tema veya özelleştirilebilir görünüm ayarlarını desteklemeli. 5. Fonksiyonel Olmayan Gereksinimler • Veri Güvenliği: Yüklenen dosyalar, notlar ve metadata güvenli bir şekilde saklanmalı ve yalnızca benim erişimime açık olmalı (paylaşmayı seçmedikçe). • Güvenilirlik: Yükleme veya düzenleme sırasında veri kaybı olmamalı. • Performans: Özet ve kaynak oluşturma işlemleri hızlı olmalı (ideal olarak birkaç saniye içinde tamamlanmalı). • Ölçeklenebilirlik: Sistem, yüzlerce hatta binlerce makaleden oluşan kişisel bir kütüphaneyi sorunsuz şekilde desteklemeli. 6. Geleceğe Yönelik Düşünceler (Opsiyonel fakat Tercih Edilenler) • Zotero, Mendeley veya EndNote gibi referans yöneticileriyle entegrasyon. • Makaleleri ve notları meslektaşlarla paylaşabilmek için iş birliği özellikleri. • Notlar ve özetler için sürüm kontrolü veya değişiklik geçmişi. • Daha iyi organizasyon için etiketleme sistemi. 7. Sonuç Bu sistem, araştırmalarımda kullandığım literatürü yönetmek için dijital bir çalışma alanı işlevi görmelidir. Tekrarlayan işleri azaltmalı, akademik yazım sürecini desteklemeli ve düzenli bir bilgi tabanı oluşturmama yardımcı olmalıdır. Gerçek araştırma iş akışlarına uygun, kullanıcı dostu ve sağlam bir araç görmeyi dört gözle bekliyorum.', 'PDF', 'btbs316.pdf', 'assets/uploads/pdf/btbs316_683ebe0c1daf7.pdf', 11, '2025-06-03 12:19:12', '2025-06-03 12:25:01', 'Bu çalışma, araştırmacıların akademik makale yönetiminde yaşadığı zorluklara çözüm olarak web tabanlı bir sistem ihtiyacını ortaya koymaktadır. Sistem, makalelerin merkezi bir platformda depolanması, otomatik özetlenmesi, notlar eklenmesi ve çeşitli formatlarda kaynakça oluşturulması gibi temel işlevleri desteklemelidir. Kullanıcıların makale metadata\'larını kolayca girmesi ve düzenlemesi, kullanıcı dostu bir arayüz aracılığıyla sağlanmalıdır. Ayrıca, sistemin gelişmiş arama yetenekleri, veri güvenliği, performansı ve ölçeklenebilirliği de kritik öneme sahiptir. Gelecekte, referans yönetim araçlarıyla entegrasyon ve iş birliği özelliklerinin eklenmesi, sistemin değerini artıracaktır. Amaç, araştırmacıların literatür yönetimi süreçlerini optimize ederek, akademik yazım faaliyetlerini daha verimli hale getirmektir.', 'Bu rapor, bir araştırmacının akademik makaleleri etkin bir şekilde yönetmek amacıyla web tabanlı bir sistem için duyduğu ihtiyacı ve beklentileri tanımlamaktadır. Sistem, makalelerin yüklenmesi, saklanması, otomatik özetlenmesi, notlar eklenmesi ve çeşitli formatlarda kaynak gösteriminin yapılabilmesini sağlamalıdır. Kullanıcı, başlık, yazar, yayın tarihi gibi metadata girişlerini yapabilmeli ve düzenleyebilmelidir. Arayüzün kullanıcı dostu, farklı cihazlarda uyumlu ve sezgisel olması, ayrıca gelişmiş arama ve filtreleme özelliklerini desteklemesi beklenmektedir. Sistem veri güvenliğini, güvenilirliği, yüksek performansı ve ölçeklenebilirliği sağlamalıdır. Gelecekte Zotero gibi referans yöneticileriyle entegrasyon, iş birliği özellikleri ve sürüm kontrolü gibi ek özellikler de değerlendirilmektedir. Amaç, araştırmacının literatür yönetimi için dijital bir çalışma alanı oluşturarak, tekrarlayan işleri azaltmak ve akademik yazım sürecini desteklemektir.'),
(17, 'Deneme Makale', 2023, 'derhi', 'doi', 'özet', 'ege, eggeaa, asdsa, asd, asdad', '1,2,3,4,5', 'Kullanıcı Gereksinimleri Raporu Proje Başlığı: Web Tabanlı Akademik Makale Yönetim Sistemi Hazırlayan: Daren K. Arga 1. Giriş Akademik ve bilimsel çalışmalarla ilgilenen bir araştırmacı olarak, araştırma sürecim boyunca bilimsel makaleleri verimli bir şekilde yönetebileceğim web tabanlı bir sisteme ihtiyacım var. Bu sistem, makaleleri yüklememe, saklamama, özetlememe, not almama ve kaynak göstermeme olanak tanımalı; bu sayede zaman kazanmamı ve manuel işleri azaltmamı sağlamalıdır. Sistem, kullanılabilirliğe, doğruluğa ve akademik iş akışlarına olan desteğe öncelik vermelidir. 2. Kullanıcı Hedefleri ve Beklentileri Bir araştırmacı olarak sistemden temel beklentilerim şunlardır: • Bilimsel makaleleri toplama ve düzenleme sürecini kolaylaştırmak. • Otomatik özetlemeler ile makaleleri hızlıca anlayabilmek. • Kişiselleştirilmiş notlar ve açıklamalar ekleyebilmek. • Yaygın kullanılan akademik formatlarda otomatik kaynak oluşturma desteği. • Kullandığım veya kullanmayı planladığım kaynaklardan oluşan aranabilir bir kütüphane. • Temiz, sezgisel ve farklı cihazlarda erişilebilir bir kullanıcı arayüzü. 3. Fonksiyonel Gereksinimler (Kullanıcı Odaklı) 3.1 Makale Yükleme ve Saklama • PDF veya metin formatında makale yüklemek istiyorum. • Sistem, birden fazla dosyanın toplu olarak yüklenmesine izin vermeli. • Yüklenen makaleleri saklayıp kolay erişim için kategorilere ayırmalı. 3.2 Metadata Girişi ve Düzenleme • Aşağıdaki alanlar için metadata girişi yapabilmeliyim: o Başlık o Yazarlar o Dergi/Konferans Adı o Yayın Yılı o DOI (varsa) o Özet o Anahtar Kelimeler • Yükleme sonrasında bu bilgileri düzenleyebilmeliyim. 3.3 Otomatik Makale Özeti Oluşturma • Sistem her yüklenen makale için otomatik bir özet oluşturmalı. • Özetler kısa ama makalenin amacı ve bulgularını anlamak için yeterli olmalı. • Gerekirse özetleri yeniden oluşturabilmeli veya manuel olarak düzenleyebilmeliyim. 3.4 Notlar ve Açıklamalar • Her makale için kişisel notlar ekleyebilmeliyim. • Bu notlar zaman damgalı olmalı ve isteğe bağlı olarak belirli bölümlere (örneğin sayfa veya paragraf) bağlanabilmeli. • Notları düzenleyip silebilmeliyim. 3.5 Kaynak ve Atıf Yönetimi • Her makale için APA, MLA, Chicago gibi farklı formatlarda kaynak ve atıf oluşturabilmeliyim. • Bu atıfları kopyalayabilmeli veya dışa aktarabilmeliyim. • Sistem, gerektiğinde metin içi atıfları da desteklemeli. 3.6 Kaynak Kütüphanesi • Kendi yazdığım makalelerde kullandığım kaynakları kaydedebilmeliyim. • Bu kaynaklar aranabilir olmalı ve projeye, konuya ya da makaleye göre gruplanabilmeli. • Kaynakları standart formatlarda (örneğin BibTeX, RIS) içe/dışa aktarabilmeliyim. 4. Kullanılabilirlik ve Arayüz Gereksinimleri • Arayüz sade, temiz ve masaüstü, tablet ve mobilde uyumlu olmalı. • Navigasyon sezgisel olmalı, öğrenme süreci minimumda tutulmalı. • Makaleleri ve kaynakları anahtar kelime, yazar veya yıl gibi kriterlerle arayabilmeli ve filtreleyebilmeliyim. • Sistem koyu tema veya özelleştirilebilir görünüm ayarlarını desteklemeli. 5. Fonksiyonel Olmayan Gereksinimler • Veri Güvenliği: Yüklenen dosyalar, notlar ve metadata güvenli bir şekilde saklanmalı ve yalnızca benim erişimime açık olmalı (paylaşmayı seçmedikçe). • Güvenilirlik: Yükleme veya düzenleme sırasında veri kaybı olmamalı. • Performans: Özet ve kaynak oluşturma işlemleri hızlı olmalı (ideal olarak birkaç saniye içinde tamamlanmalı). • Ölçeklenebilirlik: Sistem, yüzlerce hatta binlerce makaleden oluşan kişisel bir kütüphaneyi sorunsuz şekilde desteklemeli. 6. Geleceğe Yönelik Düşünceler (Opsiyonel fakat Tercih Edilenler) • Zotero, Mendeley veya EndNote gibi referans yöneticileriyle entegrasyon. • Makaleleri ve notları meslektaşlarla paylaşabilmek için iş birliği özellikleri. • Notlar ve özetler için sürüm kontrolü veya değişiklik geçmişi. • Daha iyi organizasyon için etiketleme sistemi. 7. Sonuç Bu sistem, araştırmalarımda kullandığım literatürü yönetmek için dijital bir çalışma alanı işlevi görmelidir. Tekrarlayan işleri azaltmalı, akademik yazım sürecini desteklemeli ve düzenli bir bilgi tabanı oluşturmama yardımcı olmalıdır. Gerçek araştırma iş akışlarına uygun, kullanıcı dostu ve sağlam bir araç görmeyi dört gözle bekliyorum.', 'PDF', 'btbs316.pdf', 'assets/uploads/pdf/btbs316_683ff29f12465.pdf', 12, '2025-06-04 10:15:47', '2025-06-04 10:15:47', 'Bu rapor, bir araştırmacının akademik makaleleri etkin bir şekilde yönetebileceği web tabanlı bir sistem ihtiyacını ortaya koymaktadır. Sistem, makale yükleme, saklama, otomatik özetleme, kişiselleştirilmiş notlar ekleme ve çeşitli formatlarda otomatik kaynak oluşturma işlevlerini desteklemelidir. Kullanıcı, PDF veya metin formatındaki makaleleri toplu olarak yükleyebilmeli, metadata girişi yapabilmeli ve bu bilgileri düzenleyebilmelidir. Otomatik özetler, makalenin temel amacını ve bulgularını yansıtmalı, notlar zaman damgalı olmalı ve gerektiğinde belirli bölümlere bağlanabilmelidir. Sistem, APA, MLA, Chicago gibi yaygın akademik formatlarda kaynak ve atıf oluşturabilmeli, kullanıcıların kendi kaynak kütüphanelerini oluşturmalarına olanak tanımalıdır. Arayüzün sade, sezgisel ve farklı cihazlarda uyumlu olması, veri güvenliğinin sağlanması, veri kaybının önlenmesi, hızlı performans ve ölçeklenebilirlik gibi fonksiyonel olmayan gereksinimler de belirtilmiştir. Gelecekte Zotero, Mendeley gibi referans yöneticileriyle entegrasyon, iş birliği özellikleri, sürüm kontrolü ve etiketleme sistemi gibi ek özellikler de arzu edilmektedir. Amaç, araştırmacının literatür yönetimini kolaylaştıran, tekrarlayan işleri azaltan ve düzenli bir bilgi tabanı oluşturan kullanıcı dostu bir araç sağlamaktır.', NULL);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `makale_etiket`
--

CREATE TABLE `makale_etiket` (
  `id` int(11) NOT NULL,
  `makale_id` int(10) UNSIGNED NOT NULL,
  `etiket_id` int(11) NOT NULL,
  `etiket_adi` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `makale_kategori`
--

CREATE TABLE `makale_kategori` (
  `id` int(10) UNSIGNED NOT NULL,
  `kategori` varchar(255) NOT NULL,
  `makale_id` int(10) UNSIGNED NOT NULL,
  `kategori_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `makale_kategori`
--

INSERT INTO `makale_kategori` (`id`, `kategori`, `makale_id`, `kategori_id`) VALUES
(62, 'Etik ve Toplum', 13, 5),
(63, 'Makine Öğrenmesi', 14, 3),
(65, 'Veri Bilimi', 16, 2),
(66, 'Yapay Zeka', 17, 1);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `makale_paylasim`
--

CREATE TABLE `makale_paylasim` (
  `id` int(10) UNSIGNED NOT NULL,
  `makale_id` int(10) UNSIGNED NOT NULL COMMENT 'Hangi makale paylaşıldı',
  `paylasan_id` int(11) NOT NULL COMMENT 'Paylaşımı başlatan kullanıcı',
  `paylasilan_id` int(11) NOT NULL COMMENT 'Paylaşılan taraf',
  `paylasilan_eposta` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `makale_paylasim`
--

INSERT INTO `makale_paylasim` (`id`, `makale_id`, `paylasan_id`, `paylasilan_id`, `paylasilan_eposta`, `created_at`) VALUES
(13, 13, 7, 8, 'ege1@gmail.com', '2025-05-28 01:38:41'),
(15, 16, 11, 7, 'egenull0@gmail.com', '2025-06-03 12:20:36');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `notlar`
--

CREATE TABLE `notlar` (
  `id` int(11) NOT NULL,
  `makale_id` int(10) UNSIGNED NOT NULL,
  `kullanici_id` int(11) NOT NULL,
  `icerik` text NOT NULL,
  `olusturulma_tarihi` datetime DEFAULT current_timestamp(),
  `guncellenme_tarihi` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `referans` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `notlar`
--

INSERT INTO `notlar` (`id`, `makale_id`, `kullanici_id`, `icerik`, `olusturulma_tarihi`, `guncellenme_tarihi`, `referans`) VALUES
(6, 14, 7, 'Not 1', '2025-05-29 01:41:11', '2025-05-29 01:41:11', ''),
(7, 16, 11, 'Makale Not', '2025-06-03 12:19:35', '2025-06-03 12:19:35', '');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `uye`
--

CREATE TABLE `uye` (
  `id` int(11) NOT NULL,
  `ad` varchar(100) NOT NULL,
  `soyad` varchar(100) NOT NULL,
  `unvan` varchar(100) DEFAULT NULL,
  `kurum` varchar(255) DEFAULT NULL,
  `arastirma_alani` varchar(255) DEFAULT NULL,
  `e_posta` varchar(255) NOT NULL,
  `sifre` varchar(255) NOT NULL,
  `avatar` varchar(500) DEFAULT NULL,
  `olusturulma_tarihi` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `uye`
--

INSERT INTO `uye` (`id`, `ad`, `soyad`, `unvan`, `kurum`, `arastirma_alani`, `e_posta`, `sifre`, `avatar`, `olusturulma_tarihi`) VALUES
(7, 'ege', 'aytac', 'Prof. Dr.', 'DAU', 'Bilgisayar Bilimleri', 'egenull0@gmail.com', '$2a$12$sQAd0Z.LapR9m9MMqzxMWuUF/5DI.o78n7nI5oVTqUoF.OizxIii.', 'assets/uploads/profilPhotos/1748471971_683790a312ae8_Ekran_Resmi_2025-05-13_20.21.19.png', '2025-05-27 21:09:27'),
(8, 'ad,', 'soyad', NULL, NULL, NULL, 'ege1@gmail.com', 'sifre', NULL, '2025-05-27 22:36:38'),
(9, 'ege', 'ege', 'Yüksek Lisans Öğrencisi', 'Doğu Akdeniz Üniversitesi', 'Mühendislik', 'egeaytac7@gmail.com', '$2y$10$pxoMAk9EbJGyDjprHoQLcOxcMWeuLeWhMnT1ut4cftOD2C/xOsBX.', 'assets/uploads/profilPhotos/1748893270_683dfe5602d41_arrow_drop_down_24dp_E3E3E3_FILL0_wght400_GRAD0_opsz24.png', '2025-06-02 19:19:22'),
(10, 'ahmet', 'mehmet', 'Dr.', 'Doğu Akdeniz Üniversitesi', 'Mühendislik', 'v1avof74879@endelite.com', '$2y$10$DZuEYYafYOa3Wt1qENrKkeUrjGe6L20HSV0A/4V4MtQO03xP4tpN.', NULL, '2025-06-02 19:42:33'),
(11, 'Veli Bora', 'Sarıkaya', 'Dr.', 'Doğu Akdeniz Üniversitesi', 'Bilgisayar Bilimleri', 'vavof74879@endelite.com', '$2y$10$5hc5HIxMH9xjVDeasfJy4uBieDQwKAN/Q95lp1XBx6RrE0rclSOIm', 'assets/uploads/profilPhotos/1748942267_683ebdbba91a6_arrow_drop_down_24dp_E3E3E3_FILL0_wght400_GRAD0_opsz24.png', '2025-06-03 09:17:14'),
(12, 'ayse', 'alioğlu', 'Dr.', 'Doğu Akdeniz Üniversitesi', 'Bilgisayar Bilimleri', 'lemal21451@endelite.com', '$2y$10$oMlPD7BtEfbsG/31bnNP9.brVyavmWWLlwUVXeIZRj41eMzHHS8f2', 'assets/uploads/profilPhotos/1749027419_68400a5bee977_add_24dp_E3E3E3_FILL0_wght400_GRAD0_opsz24.png', '2025-06-04 07:00:23');

--
-- Dökümü yapılmış tablolar için indeksler
--

--
-- Tablo için indeksler `etiketler`
--
ALTER TABLE `etiketler`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `etiket_adi` (`etiket_adi`);

--
-- Tablo için indeksler `kategoriler`
--
ALTER TABLE `kategoriler`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `kaynaklar`
--
ALTER TABLE `kaynaklar`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kullanici_id` (`kullanici_id`),
  ADD KEY `makale_id` (`makale_id`);

--
-- Tablo için indeksler `makaleler`
--
ALTER TABLE `makaleler`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `makale_etiket`
--
ALTER TABLE `makale_etiket`
  ADD PRIMARY KEY (`id`),
  ADD KEY `makale_id` (`makale_id`),
  ADD KEY `etiket_id` (`etiket_id`);

--
-- Tablo için indeksler `makale_kategori`
--
ALTER TABLE `makale_kategori`
  ADD PRIMARY KEY (`id`),
  ADD KEY `makale_id` (`makale_id`);

--
-- Tablo için indeksler `makale_paylasim`
--
ALTER TABLE `makale_paylasim`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_makale` (`makale_id`),
  ADD KEY `idx_paylasan` (`paylasan_id`),
  ADD KEY `idx_paylasilan` (`paylasilan_id`);

--
-- Tablo için indeksler `notlar`
--
ALTER TABLE `notlar`
  ADD PRIMARY KEY (`id`),
  ADD KEY `makale_id` (`makale_id`),
  ADD KEY `kullanici_id` (`kullanici_id`);

--
-- Tablo için indeksler `uye`
--
ALTER TABLE `uye`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `e_posta` (`e_posta`);

--
-- Dökümü yapılmış tablolar için AUTO_INCREMENT değeri
--

--
-- Tablo için AUTO_INCREMENT değeri `etiketler`
--
ALTER TABLE `etiketler`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `kategoriler`
--
ALTER TABLE `kategoriler`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Tablo için AUTO_INCREMENT değeri `kaynaklar`
--
ALTER TABLE `kaynaklar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `makaleler`
--
ALTER TABLE `makaleler`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- Tablo için AUTO_INCREMENT değeri `makale_etiket`
--
ALTER TABLE `makale_etiket`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `makale_kategori`
--
ALTER TABLE `makale_kategori`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- Tablo için AUTO_INCREMENT değeri `makale_paylasim`
--
ALTER TABLE `makale_paylasim`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Tablo için AUTO_INCREMENT değeri `notlar`
--
ALTER TABLE `notlar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Tablo için AUTO_INCREMENT değeri `uye`
--
ALTER TABLE `uye`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Dökümü yapılmış tablolar için kısıtlamalar
--

--
-- Tablo kısıtlamaları `kaynaklar`
--
ALTER TABLE `kaynaklar`
  ADD CONSTRAINT `kaynaklar_ibfk_1` FOREIGN KEY (`kullanici_id`) REFERENCES `uye` (`id`),
  ADD CONSTRAINT `kaynaklar_ibfk_2` FOREIGN KEY (`makale_id`) REFERENCES `makaleler` (`id`);

--
-- Tablo kısıtlamaları `makale_etiket`
--
ALTER TABLE `makale_etiket`
  ADD CONSTRAINT `makale_etiket_ibfk_1` FOREIGN KEY (`makale_id`) REFERENCES `makaleler` (`id`),
  ADD CONSTRAINT `makale_etiket_ibfk_2` FOREIGN KEY (`etiket_id`) REFERENCES `etiketler` (`id`);

--
-- Tablo kısıtlamaları `makale_kategori`
--
ALTER TABLE `makale_kategori`
  ADD CONSTRAINT `fk_kategoriler_makale` FOREIGN KEY (`makale_id`) REFERENCES `makaleler` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Tablo kısıtlamaları `makale_paylasim`
--
ALTER TABLE `makale_paylasim`
  ADD CONSTRAINT `fk_paylasim_makale` FOREIGN KEY (`makale_id`) REFERENCES `makaleler` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_paylasim_paylasan` FOREIGN KEY (`paylasan_id`) REFERENCES `uye` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_paylasim_paylasilan` FOREIGN KEY (`paylasilan_id`) REFERENCES `uye` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `notlar`
--
ALTER TABLE `notlar`
  ADD CONSTRAINT `notlar_ibfk_1` FOREIGN KEY (`makale_id`) REFERENCES `makaleler` (`id`),
  ADD CONSTRAINT `notlar_ibfk_2` FOREIGN KEY (`kullanici_id`) REFERENCES `uye` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
