<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

mso_cur_dir_lang('templates');

require_once( getinfo('common_dir') . 'comments.php' );


$res_post = mso_comuser_lost(); // обработка отправленных данных - возвращает результат


$comuser_info = mso_get_comuser(); // получим всю информацию о комюзере

mso_head_meta('title', getinfo('title') . ' - '. t('Восстановление пароля') ); // meta title страницы


// теперь сам вывод
# начальная часть шаблона
require(getinfo('template_dir') . 'main-start.php');

echo $res_post;
	
if ($comuser_info)
{
	extract($comuser_info[0]);
	
	if ($comusers_nik) echo '<h1>' . $comusers_nik . '</h1>';
		else echo '<h1>'. t('Комментатор'). ' ' . $comusers_id . '</h1>';
	
	echo '<p><a href="' . getinfo('siteurl') . 'users/' . $comusers_id . '">'. t('Персональная страница'). '</a></p>';
	
	// если актвация не завершена, то вначале требуем её завершить
	if ($comusers_activate_string != $comusers_activate_key) // нет активации
	{
		echo '<form action="" method="post">' . mso_form_session('f_session_id');
		echo '<p><span style="color: red; font-weight: bold;">'. t('Введите ключ активации'). ':</span> 
			 <input type="text" style="width: 200px;" name="f_comusers_activate_key"> ';
		echo '<input type="submit" name="f_submit[' . $comusers_id . ']" value="'. t('Готово'). '" /></p></form>';
		
		echo '<p>' . t('В случае проблем с активацией (не пришел ключ, указали ошибочный email), обращайтесь к администратору по email:') . ' <em>' . mso_get_option('admin_email', 'general', '-') . '</em></p>';
		
	}
	else // активация завершена - можно вывести поля для редактирования
	{
		echo '<form action="" method="post" class="comusers-form">' . mso_form_session('f_session_id');
		echo '<p>'. t('Если у вас сохранился код активации, то вы можете сразу заполнить все поля. Если код активации утерян, то вначале введите только email и нажмите кнопку «Готово». На указанный email вы получите код активации. После этого вы можете вернуться на эту страницу и заполнить все поля.'). '</p>';
		echo '<p><strong>'. t('Ваш email'). ':</strong> <input type="text" name="f_comusers_email" value="">*</p>';
		echo '<p><strong>'. t('Ваш код активации'). ':</strong> <input type="text" name="f_comusers_activate_key" value=""></p>';
		echo '<p><strong>'. t('Новый пароль'). ':</strong> <input type="text" name="f_comusers_password" value=""></p>';
		echo '<input type="submit" name="f_submit[' . $comusers_id . ']" value="'. t('Готово'). '" /></p></form>';
	}
	
	// pr($comuser_info[0]);
	
	
}
else
{
	echo '<h1>'. t('404. Ничего не найдено...'). '</h1>';
	echo '<p>'. t('Извините, пользователь с указанным номером не найден.'). '</p>';
}

# конечная часть шаблона
require(getinfo('template_dir') . 'main-end.php');
	
?>