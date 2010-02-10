<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

require_once( getinfo('common_dir') . 'comments.php' );


$res_post = mso_comuser_edit(); // обработка отправленных данных - возвращает результат


// mso_get_comuser(0, array( 'limit'=> 20, 'tags'=>'<img><strong><em><i><b><u><s><font><pre><code><blockquote>' ) );
$comuser_info = mso_get_comuser(); // получим всю информацию о комюзере

mso_head_meta('title', getinfo('title') . ' - Форма редактирования комментатора' ); // meta title страницы


// теперь сам вывод
# начальная часть шаблона
require(getinfo('template_dir') . 'main-start.php');

echo $res_post;
	
if ($comuser_info)
{
	extract($comuser_info[0]);
	// pr($comuser_info[0]);
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
		
		echo '<h3>Для редактирования введите свой email и пароль:</h3>';
		echo '<p><strong>Ваш email:</strong> <input type="text" name="f_comusers_email">*</p>';
		echo '<p><strong>Ваш пароль:</strong> <input type="password" name="f_comusers_password">*</p>';
		echo '<p><a href="' . getinfo('siteurl') . 'users/' . $comusers_id . '/lost">Я забыл пароль</a></p>';
		
		echo '<br /><h2>Укажите свои данные</h2>';
		echo '<p class="info">Указывая свои данные вы соглашаетесь с тем, что они будут публичными и открытыми.</p>';
		
		
		
		echo '<p><strong>Отображаемый ник:</strong> <input type="text" name="f_comusers_nik" value="' . $comusers_nik . '"></p>';
		echo '<p><strong>Сайт (с http://):</strong> <input type="text" name="f_comusers_url" value="' . $comusers_url . '"></p>';
		echo '<p><strong>Аватарка (с http://, размер 80x80px):</strong> <input type="text" name="f_comusers_avatar_url" value="' . $comusers_avatar_url . '"></p><br />';
		
		echo '<p><strong>ICQ:</strong> <input type="text" name="f_comusers_icq" value="' . $comusers_icq . '"></p>';
		echo '<p><strong>MSN:</strong> <input type="text" name="f_comusers_msn" value="' . $comusers_msn . '"></p>';
		echo '<p><strong>Jaber:</strong> <input type="text" name="f_comusers_jaber" value="' . $comusers_jaber . '"></p>';
		echo '<p><strong>Дата рождения:</strong> <input type="text" name="f_comusers_date_birth" value="' . $comusers_date_birth . '"></p>';
		echo '<p><strong>О себе <br />(HTML удаляется):</strong> <textarea name="f_comusers_description">'. NR 
			. htmlspecialchars(strip_tags($comusers_description)) . '</textarea></p>';
		
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