<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

		if ($comusers_nik) echo '<h1>' . $comusers_nik . '</h1>';
			else echo '<h1>'. t('Комментатор'). ' ' . $comusers_id . '</h1>';
		
		echo '<p><a href="' . getinfo('siteurl') . 'users/' . $comusers_id . '">' . t('Персональная страница') . '</a>';
		
		if (!$login_form)
			echo ' | <a href="' . getinfo('siteurl') . 'logout">' . t('Выход') . '</a>';
		
		echo '</p>';
		
		// если активация не завершена, то вначале требуем её завершить
		if ($comusers_activate_string != $comusers_activate_key) // нет активации
		{
			echo '<form action="" method="post">' . mso_form_session('f_session_id');
			echo '<p><span style="color: red; font-weight: bold;" class="users-form">'. t('Введите ключ активации'). ':</span> 
				 <input type="text" style="width: 200px;" class="users-form" name="f_comusers_activate_key"> ';
			echo '<input type="submit" name="f_submit[' . $comusers_id . ']" value="' . t('Готово') . '" /></p></form>';
			
			echo '<p>' . t('В случае проблем с активацией (не пришел ключ, указали ошибочный email), обращайтесь к администратору по email:') . ' <em>' . mso_get_option('admin_email', 'general', '-') . '</em></p>';
			
			
		}
		else // активация завершена - можно вывести поля для редактирования
		{
			echo '<form action="" method="post" class="comusers-form">' . mso_form_session('f_session_id');
			
			if ($login_form) // нужно отобразить форму
			{
				echo '<h3>'. t('Для редактирования введите свой email и пароль'). ':</h3>';
			
				echo '<input type="hidden" value="' . getinfo('siteurl') . 'users/' . $comusers_id . '/edit" name="flogin_redirect" />';
				echo '<p><strong>'. t('Ваш email'). ':</strong> <input type="text" name="flogin_user">*</p>';
				echo '<p><strong>'. t('Ваш пароль'). ':</strong> <input type="password" name="flogin_password">*</p>';
				echo mso_form_session('flogin_session_id');
		
				
				echo '<p><a href="' . getinfo('siteurl') . 'users/' . $comusers_id . '/lost">' . t('Я забыл пароль') . '</a></p>';
			}
			else
			{
				$CI = & get_instance();
				$CI->load->helper('form');
				
				echo '<input type="hidden" value="' . $comusers_email . '" name="f_comusers_email" />';
				echo '<input type="hidden" value="' . $comusers_password . '" name="f_comusers_password" />';
				
				echo '<h3>'. t('Укажите свои данные'). '</h3>';
				
				echo '<p><strong>'. t('Отображаемый ник'). ':</strong> <input type="text" name="f_comusers_nik" value="' . $comusers_nik . '"></p>';
				echo '<p><strong>'. t('Сайт (с http://)'). ':</strong> <input type="text" name="f_comusers_url" value="' . $comusers_url . '"></p>';
				echo '<p><strong>'. t('Аватарка (с http://, размер 80x80px)'). ':</strong> <input type="text" name="f_comusers_avatar_url" value="' . $comusers_avatar_url . '"></p><br />';
				
				echo '<p><strong>'. t('ICQ'). ':</strong> <input type="text" name="f_comusers_icq" value="' . $comusers_icq . '"></p>';
				echo '<p><strong>'. t('MSN'). ':</strong> <input type="text" name="f_comusers_msn" value="' . $comusers_msn . '"></p>';
				echo '<p><strong>'. t('Jaber'). ':</strong> <input type="text" name="f_comusers_jaber" value="' . $comusers_jaber . '"></p>';
				echo '<p><strong>'. t('Дата рождения'). ':</strong> <input type="text" name="f_comusers_date_birth" value="' . $comusers_date_birth . '"></p>';
				
				echo '<p><strong>'. t('Уведомления'). ':</strong>' . form_dropdown('f_comusers_notify', array('0'=>t('Без уведомлений'), '1'=>t('Подписаться')), $comusers_notify, '');
				
				echo '<p><strong>'. t('О себе'). ' <br />('. t('HTML удаляется'). '):</strong> <textarea name="f_comusers_description">'. NR 
					. htmlspecialchars(strip_tags($comusers_description)) . '</textarea></p>';
			
			}
			
			if ($login_form)
				echo '<p><input type="submit" name="flogin_submit" class="submit" value="' .  t('Отправить') . '" /></p></form>';
			else
				echo '<p><input type="submit" name="f_submit[' . $comusers_id . ']" class="submit" value="' .  t('Отправить') . '" /></p></form>';
			
		}
	
?>