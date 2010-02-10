<?php 

require_once( getinfo('common_dir') . 'comments.php' );


$res_post = mso_comuser_lost(); // обработка отправленных данных - возвращает результат


// mso_get_comuser(0, array( 'limit'=> 20, 'tags'=>'<img><strong><em><i><b><u><s><font><pre><code><blockquote>' ) );
$comuser_info = mso_get_comuser(); // получим всю информацию о комюзере

mso_head_meta('title', getinfo('title') . ' - Восстановление пароля' ); // meta title страницы


// теперь сам вывод
# начальная часть шаблона
require(getinfo('template_dir') . 'main-start.php');

echo $res_post;
	
if ($comuser_info)
{
	extract($comuser_info[0]);
	
	if ($comusers_nik) echo '<h1>' . $comusers_nik . '</h1>';
		else echo '<h1>Комментатор ' . $comusers_id . '</h1>';
	
	echo '<p><a href="' . getinfo('siteurl') . 'users/' . $comusers_id . '">Персональная страница</a></p>';
	
	// если актвация не завершена, то вначале требуем её завершить
	if ($comusers_activate_string != $comusers_activate_key) // нет активации
	{
		echo '<form action="" method="post">' . mso_form_session('f_session_id');
		echo '<p><span style="color: red; font-weight: bold;">Введите ключ активации:</span> 
			 <input type="text" style="width: 200px;" name="f_comusers_activate_key"> ';
		echo '<input type="submit" name="f_submit[' . $comusers_id . ']" value="Готово" sty1le="margin: 0 10px;" /></p></form>';
		
		
	}
	else // активация завершена - можно вывести поля для редактирования
	{
		echo '<form action="" method="post" class="comusers-form">' . mso_form_session('f_session_id');
		echo '<p>Если у вас сохранился код активации, то вы можете сразу заполнить все поля. Если код активации утерян, то вначале введите только email и нажмите кнопку «Готово». На указанный email вы получите код активации. После этого вы можете вернуться на эту страницу и заполнить все поля.</p>';
		echo '<p><strong>Ваш email:</strong> <input type="text" name="f_comusers_email" value="">*</p>';
		echo '<p><strong>Ваш код активации:</strong> <input type="text" name="f_comusers_activate_key" value=""></p>';
		echo '<p><strong>Новый пароль:</strong> <input type="text" name="f_comusers_password" value=""></p>';
		echo '<input type="submit" name="f_submit[' . $comusers_id . ']" value="Готово" sty1le="margin: 0 10px;" /></p></form>';
	}
	
	// pr($comuser_info[0]);
	
	
}
else
{
	echo '<h1>404. Ничего не найдено...</h1>';
	echo '<p>Извините, пользователь с указанным номером не найден.</p>';
}

# конечная часть шаблона
require(getinfo('template_dir') . 'main-end.php');
	
?>