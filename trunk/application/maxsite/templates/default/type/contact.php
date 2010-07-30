<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

	mso_cur_dir_lang('templates');

	require(getinfo('template_dir') . 'main-start.php');
	echo NR . '<div class="type type_contact">' . NR;
?>

<h1><?=t('Обратная связь')?></h1>

<?php

	echo mso_get_option('pre_contact', 'templates', '');

	$form_hide = false; //отобразить форму
	$ok = true;
	$err_name = false;
	$err_email = false;
	$err_text = false;
	$err_antispam = false;
	$err_subject = false;
	$errors_msg = '';

	if ($_POST and
		isset($_POST['submit'])
		and
		isset($_POST['contact_name']) // имя
		and
		isset($_POST['contact_mail']) // обратный адрес
		and
		isset($_POST['contact_subject']) // тема письма
		and
		isset($_POST['contact_antispam']) // антиспам
		and
		isset($_POST['antispam1']) // антиспам
		and
		isset($_POST['antispam2']) // антиспам
		)
	{
		// проверяем имя
		$_POST['contact_name'] = trim(mso_xss_clean($_POST['contact_name']));
		if (!$_POST['contact_name']) {
			$err_name = true;
			$ok = false;
			$errors_msg .= '<li>'.t('Не введено имя').'</li>';
		}

		// проверяем мыло
		$_POST['contact_mail'] = trim($_POST['contact_mail']);
		if (!mso_valid_email($_POST['contact_mail'])) {
			$err_email = true;
			$ok = false;
			$errors_msg .= '<li>'.t('Некорректный e-mail').'</li>';
		}

		$_POST['contact_subject'] = trim(mso_xss_clean($_POST['contact_subject']));
		if (!$_POST['contact_subject']) {
			$err_subject = true;
			$ok = false;
			$errors_msg .= '<li>'.t('Не введена тема письма').'</li>';
		}

		// проверяем текст
		$_POST['contact_message'] = trim(mso_xss_clean($_POST['contact_message']));
		if (!$_POST['contact_message']) {
			$err_text = true;
			$ok = false;
			$errors_msg .= '<li>'.t('Не введен текст письма').'</li>';
		}

		// антиспам
		$antispam1s = (int) $_POST['antispam1'];
		$antispam2s = (int) $_POST['antispam2'];
		$antispam3s = (int) $_POST['contact_antispam'];

		if ( ($antispam1s/711 + $antispam2s/931) != $antispam3s )
		{ // неверный код
			$err_antispam = true;
			$ok = false;
			$errors_msg .= '<li>'.t('Поле «Антиспам» заполнено ошибочно').'</li>';
		}

		if ($ok) // все ок, отправляем
		{
			$email = mso_get_option('admin_email', 'general', 'admin@site.com'); // куда приходят письма

			$subject = $_POST['contact_subject'];

			$text_email = t('Имя'). ': ' . $_POST['contact_name'] . "\n";
			$text_email .= t('E-mail'). ': ' . $_POST['contact_mail'] . "\n";
			if (isset($_POST['contact_phone']) and $_POST['contact_phone'])
				$text_email .= t('Телефон'). ': ' . trim(mso_xss_clean($_POST['contact_phone'])) . "\n";
			if (isset($_POST['contact_url']) and $_POST['contact_url'])
				$text_email .= t('Адрес сайта'). ': ' . trim(mso_xss_clean($_POST['contact_url'])) . "\n";
			$text_email .= "\n" . $_POST['contact_message'] . "\n\n";
			$text_email .= $_SERVER['REMOTE_ADDR'] . ', ' . $_SERVER['HTTP_REFERER'];

			$form_hide = mso_mail($email, $subject, $text_email, $_POST['contact_mail']);

			// отправим отправителю, если он хотел
			if (isset($_POST['subscribe']) and mso_get_option('ask_copy', 'templates', '1') ) {
				$subject = t('Копия Вашего письма с темой').' «'.$_POST['contact_subject'].'»';
				$text_email = t("Копия Вашего письма"). ": \n\n" . $text_email;
				$to_email = $_POST['contact_mail'];
				if ( mso_valid_email($to_email) ) mso_mail($to_email, $subject, $text_email);
			}

			if ($form_hide) echo '<p class="comment-ok"><h2>'. t('Ваше сообщение отправлено!'). '</h2></p><p>'
					. str_replace("\n", '<br>', htmlspecialchars($subject. "\n" . $message))
					. '</p>';
			//$form_hide = true;
		}
		// неверные данные
		else {
			echo '<p class="comment-error">'. t('Письмо не отправлено'). '</p>';
			echo '<p>'. t('Обнаружены следующие ошибки'). ':</p><ul>'.$errors_msg.'</ul>';
		}
	}

	if ( !$form_hide ) :

		srand((double) microtime() * 1000000);
		$antispam1 = rand(1, 10);
		$antispam2 = rand(1, 10);

?>

<form name="contact-form" class="contact-form" action="" method="post">
	<input type="hidden" name="antispam1" value="<?= $antispam1 * 711; ?>" id="antispam1">
	<input type="hidden" name="antispam2" value="<?= $antispam2 * 931; ?>" id="antispam2">

	<table border="0" width="99%" cellspacing="10">
		<tr>
			<td align="right"><label for="contact_name"><?=t('Ваше имя')?><span class="reqtxt">*</span></label></label></td>
			<td><input name="contact_name" type="text" value="<?=( (isset($_POST['contact_name'])?($_POST['contact_name']):('')) );?>" id="contact_name" style="width: 98%;<?=( ($err_name)?(' border-color: red;'):('') );?>"></td>
		</tr>
		<tr>
			<td align="right"><label for="contact_mail"><?=t('E-mail')?><span class="reqtxt">*</span></label></td>
			<td><input name="contact_mail" type="text" value="<?=( (isset($_POST['contact_mail'])?($_POST['contact_mail']):('')) );?>" id="contact_mail" style="width: 98%;<?=( ($err_email)?(' border-color: red;'):('') );?>"></td>
		</tr>
<?php if (mso_get_option('ask_phone', 'templates', '1')) { ?>
		<tr>
			<td align="right"><label for="contact_phone"><?=t('Телефон (с кодом города)')?></label></td>
			<td><input name="contact_phone" type="text" value="<?=( (isset($_POST['contact_phone'])?($_POST['contact_phone']):('')) );?>" id="contact_phone" style="width: 98%;"></td>
		</tr>
<?php } ?>
<?php if (mso_get_option('ask_site', 'templates', '1')) { ?>
		<tr>
			<td align="right"><label for="contact_url"><?=t('Адрес сайта')?></label></td>
			<td><input name="contact_url" type="text" value="<?=( (isset($_POST['contact_url'])?($_POST['contact_url']):('')) );?>" id="contact_url" style="width: 98%;"></td>
		</tr>
<?php } ?>
		<tr>
			<td align="right"><label for="contact_subject"><?=t('Тема')?><span class="reqtxt">*</span></label></td>
			<td><?php
$subj = trim(mso_get_option('subjects', 'templates', 'Пожелания по сайту_NR_Нашел ошибку на сайте_NR_Подскажите, пожалуйста_NR_Я вас люблю!_NR_Я вас ненавижу..._NR_Я вам пишу, чего же боле...'));
if (!$subj) echo '<input name="contact_subject" type="text" value="'.( (isset($_POST['contact_subject'])?($_POST['contact_subject']):('')) ).'" id="contact_url" style="width: 98%;'.( ($err_subject)?(' border-color: red;'):('') ).'">';
else
{
	$subj = str_replace("_NR_", "\n", $subj);
	$subj = str_replace("\n\n\n", "\n", $subj);
	$subj = str_replace("\n\n", "\n", $subj);
	$subj = explode("\n", trim($subj));

	echo '<select id="contact_subject" name="contact_subject" style="width: 98%;">';
	foreach ($subj as $elem)
	{
		echo '<option';
		if (isset($_POST['contact_subject']) and $_POST['contact_subject'] == $elem)
			echo ' selected="selected">' . t($elem);
		else
			echo '>'.t($elem);
		echo '</option>' . NR;
	}
	echo '</select>';
}
?></td>
		</tr>
		<tr>
			<td align="right" valign="top"><label for="contact_message"><?=t('Сообщение')?><span class="reqtxt">*</span></label></td>
			<td><textarea name="contact_message" style="width: 98%; height: 200px;<?=( ($err_text)?(' border-color: red;'):('') );?>"><?=( (isset($_POST['contact_message'])?($_POST['contact_message']):('')) );?></textarea></td>
		</tr>
		<tr>
			<td align="right" valign="top"><label for="contact_antispam"><?=t('Антиспам')?>: <?= $antispam1; ?>+<?= $antispam2; ?>=</label></td>
			<td><input name="contact_antispam" type="text" value="" id="contact_antispam" style="<?=( ($err_antispam)?('border-color: red;'):('') );?>"><br><?=t('Укажите свой ответ')?><span class="reqtxt">*</span></td>
		</tr>
<?php if (mso_get_option('ask_copy', 'templates', '1')) { ?>
		<tr>
			<td align="right"><?=t('Отправить копию письма на ваш e-mail?')?></td>
			<td valign="top"><input name="subscribe" value="" <?=( (isset($_POST['subscribe'])?('checked="checked"'):('')) );?> type="checkbox">&nbsp;<?=t('Да')?></td>
		</tr>
<?php } ?>
		<tr>
			<td align="right" colspan="2"><?=t('Поля, помеченные символом <span class="reqtxt">*</span> обязательны для заполнения.')?></td>
		</tr>
		<tr>
			<td align="right"><input name="submit" type="submit" value=" <?=t('Отправить')?> "></td>
			<td><input name="clear" type="reset" value=" <?=t('Очистить форму')?> "></td>
		</tr>
	</table>
</form>

<?php endif; //if ( !$form_hide )

	echo mso_get_option('post_contact', 'templates', '');

echo NR . '</div><!-- class="type type_contact" -->' . NR;

require(getinfo('template_dir') . 'main-end.php');

?>