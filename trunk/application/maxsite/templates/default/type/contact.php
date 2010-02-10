<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

	mso_cur_dir_lang('templates');

	require(getinfo('template_dir') . 'main-start.php');
	echo NR . '<div class="type type_contact">' . NR;
?>

<h1><?=t('Обратная связь')?></h1>

<?php

	$form_hide = false; //отобразить форму
	
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
		// проверяем мыло
		$ok = mso_valid_email($_POST['contact_mail']);
		
		if ($ok) 
		{	// антиспам
			$antispam1s = (int) $_POST['antispam1'];
			$antispam2s = (int) $_POST['antispam2'];
			$antispam3s = (int) $_POST['contact_antispam'];
			
			if ( ($antispam1s/711 + $antispam2s/931) != $antispam3s )
			{ // неверный код
				$ok = false;
				echo '<h2>'. t('Привет роботам!'). ' :-)</h2>';
			}
		}
		
		if ($ok) // все ок, отправляем
		{
			$email = mso_get_option('admin_email', 'general', 'admin@site.com'); // куда приходят письма
		
			$subject = $_POST['contact_subject'];
			
			$text_email = t('Ваше имя'). ': ' . $_POST['contact_name'] . "\n";
			$text_email .= t('Email'). ': ' . $_POST['contact_mail'] . "\n";
			$text_email .= t('Телефон'). ': ' . $_POST['contact_phone'] . "\n";
			$text_email .= t('Адрес сайта'). ': ' . $_POST['contact_url'] . "\n\n";
			$message = $text_email .= $_POST['contact_message'];
			
			$text_email = t("Вами отправлено сообщение"). ": \n" . $text_email;
			
			$form_hide = mso_mail($email, $subject, $text_email, $_POST['contact_mail']);

			if ( isset($_POST['subscribe']) ) 
			{
					$to_email = $_POST['contact_mail'];
					if ( mso_valid_email($to_email) ) mso_mail($to_email, $subject, $text_email);
			}
			
			echo '<h2>'. t('Ваше сообщение отправлено!'). '</h2><p>' 
					. str_replace("\n", '<br>', htmlspecialchars($subject. "\n" . $message)) 
					. '</p>';
			$form_hide = true;
		}
		else
		{
			// неверные данные 
			echo '<h2 style="color: red;">'. t('Нужно указать корректные данные'). '</h2>';
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
			<td align="right"><label for="contact_name"><?=t('Ваше имя')?>*</label></td>
			<td><input name="contact_name" type="text" value="" id="contact_name" style="width: 98%;"></td>
		</tr>
		<tr>
			<td align="right"><label for="contact_mail"><?=t('E-mail')?>*</label></td>
			<td><input name="contact_mail" type="text" value="" id="contact_mail" style="width: 98%;"></td>
		</tr>
		<tr>
			<td align="right"><label for="contact_phone"><?=t('Телефон (с кодом города)')?></label></td>
			<td><input name="contact_phone" type="text" value="" id="contact_phone" style="width: 98%;"></td>
		</tr>
		<tr>
			<td align="right"><label for="contact_url"><?=t('Адрес сайта')?></label></td>
			<td><input name="contact_url" type="text" value="" id="contact_url" style="width: 98%;"></td>
		</tr>
		<tr>
			<td align="right"><label for="contact_subject"><?=t('Выберите тему письма')?>*</label></td>
			<td><select id="contact_subject" name="contact_subject" style="width: 98%;">
					<option><?=t('Пожелания по сайту')?></option>
					<option><?=t('Нашел ошибку на сайте')?></option>
					<option><?=t('Подскажите, пожалуйста')?></option>
					<option><?=t('Я вас люблю!')?></option>
					<option><?=t('Я вас ненавижу...')?></option>
					<option><?=t('Я вам пишу, чего же боле....')?></option>
				</select></td>
		</tr>
		<tr>
			<td align="right" valign="top"><label for="contact_message"><?=t('Сообщение')?>:*</label></td>
			<td><textarea name="contact_message" style="width: 98%; height: 200px;"></textarea></td>
		</tr>
		<tr>
			<td align="right"><label for="contact_antispam"><?=t('Защита от спама')?>: <?= $antispam1; ?>+<?= $antispam2; ?>=</label></td>
			<td><input name="contact_antispam" type="text" value="" id="contact_antispam"><br><?=t('Укажите свой ответ')?></td>
		</tr>
		<tr>
			<td align="right"><?=t('Отправить копию письма на ваш e-mail?')?></td>
			<td><input name="subscribe" value="" type="checkbox"> <?=t('Да')?></td>
		</tr>
		<tr>
			<td align="right"><input name="submit" type="submit" value=" <?=t('Отправить')?> "></td>
			<td><input name="clear" type="reset" value=" <?=t('Очистить форму')?> "></td>
		</tr>
	</table>
</form>

<?php endif; //if ( !$form_hide )  

echo NR . '</div><!-- class="type type_contact" -->' . NR;

require(getinfo('template_dir') . 'main-end.php'); 

?>