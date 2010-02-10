<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Language for MaxSite CMS (c) http://max-3000.com/
 * SMTP mail plugin
 * Author: (c) Wave
 * Author URL: http://wave.fantregata.com/
 * Update URL: http://wave.fantregata.com/page/work-for-maxsite
 */
 
# smtp_mail
$lang['SMTP mail'] = 'SMTP mail';
$lang['Плагин позволяет отправлять почту через SMTP сервер.'] = 'Plagin allows to send the mail via SMTP server.';

$lang['E-mail, с которого отправляем почту'] = 'E-mail, from which we send mail';
$lang['Зачастую, со стороннего SMTP сервера можно отправить почту только если адрес принадлежит именно этому серверу.<br>Если пусто — используется тот, что указан в настройках сайта.'] = 'Often possible to send the mail only if the address belong that server.<br>If emptily — is used that that is specified in options of the site.';
$lang['Протокол отправки'] = 'The mail sending protocol';
$lang['Для «smtp» укажите ниже SMTP хост, пользователя и пароль. Для «sendmail» укажите серверный путь к Sendmail.<br>Для «mail» планируются расширенные функции по сравнению со штатной возможностью системы.'] = 'For «smtp» write below SMTP host, user and password. For «sendmail» write the server path to Sendmail..<br>For «mail» extended functions are planned.';
$lang['Серверный путь к Sendmail.'] = 'The server path to Sendmail.';
$lang['Обычно это «/usr/sbin/sendmail»'] = 'May be «/usr/sbin/sendmail»';
$lang['SMTP host'] = 'SMTP host';
$lang['SMTP user'] = 'SMTP user';
$lang['SMTP pass'] = 'SMTP pass';
$lang['<b style="color: red;">Примечание:</b> пароль в базе данных хранится в открытом виде.'] = '<b style="color: red;">Attention:</b> password in database is stored not crypted.';
$lang['Может быть, например, 25, 2525 или 587.'] = 'May be 25, 2525 или 587.';
$lang['Настройки плагина «SMTP mail»'] = 'Option of «SMTP mail» plugin';
$lang['Укажите необходимые опции.'] = 'Indicate the required options.';
$lang['Складывать ли письма в <b>uploads</b>'] = 'Store mails in <b>uploads</b>';
$lang['Письма можно не только отправлять на почту, но и сохранять в каталог <b>uploads</b>, где их можно посмотреть даже если они не дошли на e-mail.'] = 'Letters possible not only to send to mail, but also save in catalogue(directory) <b>uploads</b>, where their possible read even though they not sent per e-mail.';
$lang['Каталог в <b>uploads</b>, куда складывать почту'] = 'Directory in <b>uploads</b>, where mails must be stored';
$lang['Каталог вы можете создать в разделе «Загрузки». Это может быть, например, <b>mail</b>.<br>Оставьте пустым, если хотите складывать письма в <b>uploads</b>.'] = 'The Catalogue(Directory) you may create in «Upoading». It may be, for instance, <b>mail</b>.<br>Leave empty if you want to store letters in <b>uploads</b>.';
$lang['Отправлять письма на e-mail'] = 'Send mails to e-mail';
$lang['Если письма сохраняются в каталог <b>uploads</b> или просто нужно отключить отправку на e-mail, снимите галочку здесь.'] = 'If mails is saved to <b>uploads</b> or you need to disable sending mails to e-mail, uncheck this option.';

# end file