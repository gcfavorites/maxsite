<?php 
	require('main-start.php');
?>

<h1>Обратная связь</h1>

<?php

	function valid_email($em) {
		if ( eregi("^[a-z0-9\._-]+@+[a-z0-9\._-]+\.+[a-z]{2,3}$", $em) )
			return true;
		else
			return false;
	}
	
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
		$ok = valid_email($_POST['contact_mail']);
		
		if ($ok) 
		{	// антиспам
			$antispam1s = (int) $_POST['antispam1'];
			$antispam2s = (int) $_POST['antispam2'];
			$antispam3s = (int) $_POST['contact_antispam'];
			
			if ( ($antispam1s/711 + $antispam2s/931) != $antispam3s )
			{ // неверный код
				$ok = false;
				echo '<h2>Привет роботам! :-)</h2>';
			}
		}
		
		if ($ok) // все ок, отправляем
		{
			$email = mso_get_option('admin_email', 'general', 'admin@site.com'); // куда приходят письма
		
			$subject = $_POST['contact_subject'];
			
			$text_email = 'Ваше имя: ' . $_POST['contact_name'] . "\n";
			$text_email .= 'Email: ' . $_POST['contact_mail'] . "\n";
			$text_email .= 'Телефон: ' . $_POST['contact_phone'] . "\n";
			$text_email .= 'Адрес сайта: ' . $_POST['contact_url'] . "\n\n";
			$message = $text_email .= $_POST['contact_message'];
			
			$text_email = "Вами отправлено сообщение: \n" . $text_email;
			
			$form_hide = mso_mail($email, $subject, $text_email);

			if ( isset($_POST['subscribe']) ) 
			{
					$to_email = $_POST['contact_mail'];
					if ( valid_email($to_email) ) mso_mail($to_email, $subject, $text_email);
			}
			
			echo '<h2>Ваше сообщение отправлено!</h2><p>' 
					. str_replace("\n", '<br />', htmlspecialchars($subject. "\n" . $message)) 
					. '</p>';
			$form_hide = true;
		}
		else
		{
			// неверные данные 
			echo '<h2 style="color: red;">Нужно указать корректные данные</h2>';
		}
	}
	
	if ( !$form_hide ) : 
	
		srand((double) microtime() * 1000000);
		$antispam1 = rand(1, 10);
		$antispam2 = rand(1, 10);

?>

<form name="contact-form" class="contact-form" action="" method="post">
	<input type="hidden" name="antispam1" value="<?= $antispam1 * 711; ?>" id="antispam1" />
	<input type="hidden" name="antispam2" value="<?= $antispam2 * 931; ?>" id="antispam2" />
	
	<table border="0" width="99%" cellspacing="10">
		<tr>
			<td align="right"><label for="contact_name">Ваше имя*</label></td>
			<td><input name="contact_name" type="text" value="" id="contact_name" style="width: 98%;" /></td>
		</tr>
		<tr>
			<td align="right"><label for="contact_mail">E-mail*</label></td>
			<td><input name="contact_mail" type="text" value="" id="contact_mail" style="width: 98%;" /></td>
		</tr>
		<tr>
			<td align="right"><label for="contact_phone">Телефон (с кодом города)</label></td>
			<td><input name="contact_phone" type="text" value="" id="contact_phone" style="width: 98%;" /></td>
		</tr>
		<tr>
			<td align="right"><label for="contact_url">Адрес сайта</label></td>
			<td><input name="contact_url" type="text" value="" id="contact_url" style="width: 98%;" /></td>
		</tr>
		<tr>
			<td align="right"><label for="contact_subject">Выберите тему письма*</label></td>
			<td><select id="contact_subject" name="contact_subject" style="width: 98%;">
					<option>Тема письма1</option>
					<option>Тема письма2</option>
					<option>Тема письма3</option>
					<option>Тема письма4</option>
					<option>Тема письма5</option>
					<option>Тема письма6</option>
					<option>Тема письма7</option>
					<option>Тема письма8</option>
					<option>Тема письма9</option>
				</select></td>
		</tr>
		<tr>
			<td align="right" valign="top"><label for="contact_message">Сообщение:*</label></td>
			<td><textarea name="contact_message" style="width: 98%; height: 200px;"></textarea></td>
		</tr>
		<tr>
			<td align="right"><label for="contact_antispam">Защита от спама: <?= $antispam1; ?>+<?= $antispam2; ?>=</label></td>
			<td><input name="contact_antispam" type="text" value="" id="contact_antispam" /><br />Укажите свой ответ</td>
		</tr>
		<tr>
			<td align="right">Отправить копию письма на ваш e-mail?</td>
			<td><input name="subscribe" value="" type="checkbox" /> Да</td>
		</tr>
		<tr>
			<td align="right"><input name="submit" type="submit" value=" Отправить " /></td>
			<td><input name="clear" type="reset" value=" Очистить форму " /></td>
		</tr>
	</table>
</form>

<?php endif; //if ( !$form_hide )  ?>

<?php require('main-end.php'); ?>