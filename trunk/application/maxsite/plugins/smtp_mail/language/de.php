<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MaxSite CMS
 * Language file
 * (c) http://max-3000.com/
 * Author: (c) Александр Шиллинг
 * Author URL: http://w25.biz
 * Update URL: http://w25.biz
 */
 
# smtp_mail
$lang['SMTP mail'] = 'SMTP mail';
$lang['Плагин позволяет отправлять почту через SMTP сервер.'] = 'Plugin erlaubt das Senden von E-mails per SMTP Server.';

$lang['E-mail, с которого отправляем почту'] = 'E-mail, von dem man ein Nachricht sendet';
$lang['Зачастую, со стороннего SMTP сервера можно отправить почту только если адрес принадлежит именно этому серверу.<br>Если пусто — используется тот, что указан в настройках сайта.'] = 'Oft, kann man von der seite des SMTP Server nur Nachrichten versenden, wenn die adresse die selbe ist wie bei dem Server.<br>Wenn leer — dann wird ein e-mail von Seiten Einstellungen genommen.';
$lang['Протокол отправки'] = 'Sende Protokol';
$lang['Для «smtp» укажите ниже SMTP хост, пользователя и пароль. Для «sendmail» укажите серверный путь к Sendmail.<br>Для «mail» планируются расширенные функции по сравнению со штатной возможностью системы.'] = 'Für «smtp» schreiben Sie unten SMTP host, user (benutzer) und Passwort. Für «sendmail» schreibe den Server pfad zu Sendmail..<br>Für «mail» werden die Funktionalität später noch entwickelt.';
$lang['Серверный путь к Sendmail.'] = 'Der Serverpfad zu Sendmail.';
$lang['Обычно это «/usr/sbin/sendmail»'] = 'Möglich «/usr/sbin/sendmail»';
$lang['SMTP host'] = 'SMTP host';
$lang['SMTP user'] = 'SMTP user (benutzername)';
$lang['SMTP pass'] = 'SMTP pass (passwort)';
$lang['<b style="color: red;">Примечание:</b> пароль в базе данных хранится в открытом виде.'] = '<b style="color: red;">Achtung:</b> der Passwort wird ohne Codierung in DB Gespeichet.';
$lang['Может быть, например, 25, 2525 или 587.'] = 'Möglich 25, 2525 oder 587.';
$lang['Настройки плагина «SMTP mail»'] = 'Option von «SMTP mail» plugin';
$lang['Укажите необходимые опции.'] = 'Wählen Sie die benötigte Optionen.';
$lang['Складывать ли письма в <b>uploads</b>'] = 'Sollen die Email in dem Ordner <b>uploads</b> Gespeicher werden?';
$lang['Письма можно не только отправлять на почту, но и сохранять в каталог <b>uploads</b>, где их можно посмотреть даже если они не дошли на e-mail.'] = 'Die Briefe kann man nicht nur an E-mail Senden, sondern auch in den Ordner <b>uploads</b>, wo man die Anzeigen lassen kann, wenn sie nicht an E-mail kommen.';
$lang['Каталог в <b>uploads</b>, куда складывать почту'] = 'Ordner in <b>uploads</b>, wo man das Post Speichern soll';
$lang['Каталог вы можете создать в разделе «Загрузки». Это может быть, например, <b>mail</b>.<br>Оставьте пустым, если хотите складывать письма в <b>uploads</b>.'] = 'Den Ordner können Sie in Bereich «Hochladen» erstellen. Es kann z.B., <b>mail</b>.<br>Lassen Sie frei wenn Sie alle Briefe in <b>uploads</b>. Speichern möchten';
$lang['Отправлять письма на e-mail'] = 'Sende Brief zu E-mail';
$lang['Если письма сохраняются в каталог <b>uploads</b> или просто нужно отключить отправку на e-mail, снимите галочку здесь.'] = 'Wenn ein Brief in Ordner <b>uploads</b> oder sie möchten keine Email mehr versenden, machen das häckchen raus.';

# end file
