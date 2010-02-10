<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

mso_cur_dir_lang('templates');

require_once( getinfo('common_dir') . 'comments.php' ); 

$comusers = mso_get_comusers_all(); // получим всех комюзеров

mso_head_meta('title', getinfo('title') . ' - ' . t('Комментаторы') . '' ); // meta title страницы

// теперь сам вывод
# начальная часть шаблона
require(getinfo('template_dir') . 'main-start.php');


if ($comusers)
{
	echo '<h1>'. t('Комментаторы'). '</h1><ul class="users-all">';

	// pr($comusers);
	foreach ($comusers as $comuser)
	{
		if (function_exists('mso_page_foreach'))
		{
			if ($f = mso_page_foreach('users-all')) 
			{
				require($f); // подключаем кастомный вывод
				continue; // следующая итерация
			}
		}
		
		if (!$comuser['comusers_nik']) $comuser['comusers_nik'] = t('Комментатор'). ' ' . $comuser['comusers_id'];
		echo '<li><a href="' . getinfo('siteurl') . 'users/' . $comuser['comusers_id'] . '">' . $comuser['comusers_nik'] . '</a></li>';
	}
	echo '</ul>';
}
else
{
	echo '<h1>'. t('404. Ничего не найдено...'). '</h1>';
	echo '<p>'. t('Извините, на сайте пока нет зарегистрированных комментаторов.'). '</p>';
}

# конечная часть шаблона
require(getinfo('template_dir') . 'main-end.php');
	
?>