<?php
/**
 *  2Moons 
 *   by Jan-Otto Kröpke 2009-2016
 *
 * For the full copyright and license information, please view the LICENSE
 *
 * @package 2Moons
 * @author Jan-Otto Kröpke <slaver7@gmail.com>
 * @copyright 2009 Lucky
 * @copyright 2016 Jan-Otto Kröpke <slaver7@gmail.com>
 * @licence MIT
 * @version 1.8.0
 * @link https://github.com/jkroepke/2Moons
 */

// Turkce'ye Ibrahim Senyer tarafindan cevirilmistir. Butun Haklari saklidir (C) 2013
// 2Moons - Copyright (C) 2010-2012 Slaver
// Translated into Turkish by Ibraihm Senyer . All rights reversed (C) 2013
// 2Moons - Copyright (C) 2010-2012 Slaver


$LNG['back']					= 'Geri';
$LNG['continue']				= 'Devam Et';
$LNG['continueUpgrade']			= 'Guncellestir!';
$LNG['login']					= 'Giris';

$LNG['menu_intro']				= 'Tanitim';
$LNG['menu_install']			= 'Kurulum';
$LNG['menu_license']			= 'Lisans';
$LNG['menu_upgrade']			= 'Guncelle';

$LNG['title_install']			= 'Kurulum';

$LNG['intro_lang']				= 'Dil';
$LNG['intro_install']			= 'To installation';
$LNG['intro_welcome']			= 'Hosgeldin yeni pr0game Kullanicisi!';
$LNG['intro_text']				= 'pr0game en iyi ogame projelerinden birisidir.<br>pr0game mevcut gelistirilmis en yeni ve guvenilir Xnova versiyonudur. pr0game gerek kullanim kolayligi, gerek kod esnekligi, dinamizmi, kod kalitesi ve islevleri ile goz almaktadir. Her zaman sizin beklentilerinizden daha iyi olmaya calistik. <br><br> Kurulumdaki direktifler kurulum esnasinda size rehberlik edecektir. Her sorun ve problemde, bize danismak icin kesinlikle tereddut etmeyin!<br><br>The pr0game bir acik kod uygulamasidir ve GNU GPL v3 lisanslidir. Lisans hakkinda bilgi edinmek icin asagidaki "Lisans" linkine tiklayabilirsiniz.  <br><br> Kurulum islemine baslamadan once sisteminiz pr0game kurmak icin gerekli ozelliklere sahip olup olmadigi test edilecektir. ';
$LNG['intro_upgrade_head']		= 'Sisteminizde pr0game mevcut kurulu mu?';
$LNG['intro_upgrade_text']		= '<p>Sisteminizde pr0game kurulu, kolay bir guncelleme istermisiniz?</p><p>Buraya tiklayarak sisteminizin eski veritabanini bir kac tiklama ile guncelleyebilirsiniz.!</p>';


$LNG['upgrade_success']			= 'Guncelleme basari ile tamamlandi. Veritabaniniz su an surum  %s icin uygun.';
$LNG['upgrade_nothingtodo']		= 'Herhangi bir eylem gerekmiyor. Veritabani surum  %s icin uygun.';
$LNG['upgrade_back']			= 'Geri';
$LNG['upgrade_intro_welcome']	= 'Veritabani guncellemeye hosgeldiniz!';
$LNG['upgrade_available']		= 'Veritabaniniz icin gerekli guncellemeler! Veritabaninizin su anki surumu  %s ve surum %s guncellenebilir.<br><br Lutfen asagidaki menuden ilk SQL guncellemesini tiklayiniz:';
$LNG['upgrade_notavailable']	= 'Mevcut surum %s zaten son versiyon.';
$LNG['upgrade_required_rev']	= 'Guncelleme programi versiyon  r2579 (pr0game v1. 7) yada ust versiyonlari icin uygulanabilir.';


$LNG['licence_head']			= 'Lisans Kosullari';
$LNG['licence_desc']			= 'Lutfen asagidaki lisans kosullarini iyice okuyunuz.';
$LNG['licence_accept']			= 'pr0game kurulumuna devam etmek icin, lisans sartlarini kabul etmeniz gerekmektedir. ';
$LNG['licence_need_accept']		= 'Eger kuruluma devam etmek istiyorsaniz lisans kosullarini kabul etmelisiniz.';

$LNG['req_head']				= 'System Gereksinimleri';
$LNG['req_desc']				= 'Kuruluma baslamadan once pr0game sistemin kurulum icin uygun olup olmadigini test edecek. Sonuclari dikkatlice okumaniz tavsiye edilir ve butun testleri gecmeden bir sonraki adima gecmeyiniz';
$LNG['reg_yes']					= 'Evet';
$LNG['reg_no']					= 'Hayir';
$LNG['reg_found']				= 'Bulundu';
$LNG['reg_not_found']			= 'Bulunamadi';
$LNG['reg_writable']			= 'Kaydedilebilir';
$LNG['reg_not_writable']		= 'Kaydedilemez';
$LNG['reg_file']				= 'Dosya &raquo;%s&laquo; Kaydedilebilir mi?';
$LNG['reg_dir']					= 'Klasor &raquo;%s&laquo; Kaydedilebilir mi?';
$LNG['req_php_need']			= 'Mevcut script dili/versiyonu &raquo;PHP&laquo;';
$LNG['req_php_need_desc']		= '<strong>Gereken:</strong> — PHP pr0game yazilim dilidir. Bu sebeple, butun fonksiyonlarin tam calisabilmesi icin mevcut PHP versiyonu 5.2.5 yada ustu olmasi gereklidir. ';
$LNG['reg_gd_need']				= 'Yuklu GD PHP Script var mi? &raquo;gdlib&laquo;';
$LNG['reg_gd_desc']				= '<strong>Opsiyonel</strong> — Grapfik isleme kutuphanesi &raquo;gdlib&laquo; sistemdeki dinamik resimlerin olusturma isinden sorumludur. Bu olmadan sistemdeki bazi fonksiyonlar calismayabilir.';
$LNG['reg_mysqli_active']		= 'Extension support &raquo;MySQLi&laquo;';
$LNG['reg_mysqli_desc']			= '<strong>Gereken</strong> — PHP de MYSQL icin destek olmali. Eger girilen hic bir veritabani uygun degilse domain saglayiciniz ile gorusmeli yada PHP dosyalarini gozden gecirmelisiniz.';
$LNG['reg_json_need']			= ' &raquo;JSON&laquo; uzantisi uygun mu?';
$LNG['reg_iniset_need']			= 'PHP fonksiyonu &raquo;ini_set&laquo; uygun mu?';
$LNG['reg_global_need']			= 'Globallerin kaydi aktif mi?';
$LNG['reg_global_desc']			= 'pr0game globaller aktif olsun olsun olmasin calisacaktir. Ama, eger mumkunse guvenlik onlemleri bunu inaktif yapmaniz onerilir.';

$LNG['step1_head']				= 'Kurulum veritabanini konfigure et';
$LNG['step1_desc']				= 'pr0game sisteminize kurululabilir. Simdi, veritabanina baglanmak icin asagidaki bilgileri girmelisiniz. Eger gerekli bilgileri bilmiyorsaniz, domain (alan) saglayiciniz ile irtibata geciniz yada pr0game forumlarindaki direktifleri okuyunuz.';
$LNG['step1_mysql_server']		= 'Veritabani sinicisi <br /> SQL Server';
$LNG['step1_mysql_port']		= 'Veritabani-Port';
$LNG['step1_mysql_dbuser']		= 'Veritabani-Kullanici adi';
$LNG['step1_mysql_dbpass']		= 'Veritabani-Sifre';
$LNG['step1_mysql_dbname']		= 'Veritabani';
$LNG['step1_mysql_prefix']		= 'Veritabani prefix eki: <br /> Degistirmeniz gerekmez';

$LNG['step2_prefix_invalid']	= 'Prefix eki sadece alfanumerik karakterlerden olusur ve son karakter altcizgi ( _ ) olmalidir. ';
$LNG['step2_db_no_dbname']		= 'Veritabani adi girmediniz.';
$LNG['step2_db_too_long']		= 'Prefiks eki cok uzun. En fazla 36 hane olabilir.';
$LNG['step2_db_con_fail']		= 'Veritabanina baglanirken hata olustu. Detaylar : ';
$LNG['step2_conf_op_fail']		= "config.php yazilamadi!";
$LNG['step2_conf_create']		= 'config.php basariyla olustu!';
$LNG['step2_config_exists']		= 'config.php zaten mevcut!';
$LNG['step2_db_done']			= 'Basari ile veritabananina baglandi!';

$LNG['step3_head']				= 'Veritabani dosyalari olusturuluyor';
$LNG['step3_desc']				= 'pr0game veritabani icin mevcut tablolar yaratildi ve varsayilan degerler yuklendi. Bir sonraki isleme gecip kurulumu tamamlamak icin;';
$LNG['step3_db_error']			= 'Veritabani tablolari olusturulamadi:';

$LNG['step4_head']				= 'Admin Hesabi';
$LNG['step4_desc']				= 'Kurulum sihirbazi simdi sizin icin admin hesabi yaratacak. Asagiya kullanici adi, sifre ve mail adresinizi yaziniz.';
$LNG['step4_admin_name']		= 'Admin-kullanici adi:';
$LNG['step4_admin_name_desc']	= '3-20 karakter arasi kullanici adi giriniz.';
$LNG['step4_admin_pass']		= 'Admin sifresi:';
$LNG['step4_admin_pass_desc']	= '6-30 karakter arasi sifre giriniz';
$LNG['step4_admin_mail']		= 'E-mail adresi:';

$LNG['step6_head']				= 'Kurulum basari ile tamamlandi!';
$LNG['step6_desc']				= 'pr0game sisteminize basari ile kuruldu';
$LNG['step6_info_head']			= 'pr0game kullanilmaya hazir!';
$LNG['step6_info_additional']	= 'Asagidaki butona tiklayarak pr0game admin sayfasina yonlendirileceksiniz. pr0game admin araclarini buradan inceleyebilirsiniz.<br/><br/><strong> &raquo;includes/ENABLE_INSTALL_TOOL&laquo; dosyasini silmeyi unutmayiniz. Eger bu dosya sisteminizde kalirsa baska birisinin oyunu tekrar kurmasina izin vererek kurulumunuzu buyuk riske atarsiniz!</strong>';

$LNG['sql_close_reason']		= 'Oyun su an kapali';
$LNG['sql_welcome']				= 'pr0game\'a hosgeldiniz';
$LNG['reg_pdo_active']			= 'PDO Aktif';
$LNG['reg_pdo_desc']			= 'PHP Veri Nesneleri (PDO) eklentisi, PHP\'deki veritabanlarina erismek için hafif ve tutarli bir arayuz tanimlar. PDO arayuzu tanimi bulunan her veritabani surucuusu, veritabanina ozgu ozellikleri siradan eklenti islevleri olarak ifade edebilir.';
$LNG['step8_need_fields']		= 'Zorunlu alanlari doldurmadiniz. Lutfen geri giderek butnu alanlari doldurunuz!';



