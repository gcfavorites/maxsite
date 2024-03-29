<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

mso_cur_dir_lang('templates');

mso_page_view_count_first(); // для подсчета количества прочтений страницы

// параметры для получения страниц
$par = array( 'cut'=>false, 'cat_order'=>'category_id_parent', 'cat_order_asc'=>'asc', 'type'=>false ); 

// подключаем кастомный вывод, где можно изменить массив параметров $par для своих задач
if ($f = mso_page_foreach('page-mso-get-pages')) require($f); 

$pages = mso_get_pages($par, $pagination); // получим все

if ($f = mso_page_foreach('page-head-meta')) require($f);
else
{ 
	// в титле следует указать формат вывода | заменяется на  » true - использовать только page_title
	mso_head_meta('title', $pages, '%page_title%'); // meta title страницы
	mso_head_meta('description', $pages); // meta description страницы
	mso_head_meta('keywords', $pages); // meta keywords страницы
}
	
	
// теперь сам вывод

if (!$pages and mso_get_option('page_404_http_not_found', 'templates', 1) ) header('HTTP/1.0 404 Not Found'); 

# начальная часть шаблона
require(getinfo('template_dir') . 'main-start.php');

echo NR . '<div class="type type_page">' . NR;

if ($f = mso_page_foreach('page-do')) require($f);

if ($pages) // есть страницы
{ 	
	foreach ($pages as $page) : // выводим в цикле

		if ($f = mso_page_foreach('page')) 
		{
			require($f); // подключаем кастомный вывод
			
			// здесь комментарии
			// page-comments.php может быть в type своего шаблона
			$fn1 = getinfo('template_dir') . 'type/page-comments.php'; 		 // путь в шаблоне
			$fn2 = getinfo('templates_dir') . 'default/type/page-comments.php'; // путь в default
			if ( file_exists($fn1) ) require($fn1); // если есть, подключаем шаблонный
			elseif (file_exists($fn2)) require($fn2); // нет, значит дефолтный
			
			continue; // следующая итерация
		}

		
		extract($page);
		# pr($page);
		echo NR . '<div class="page_only">' . NR;
			
			if ($f = mso_page_foreach('info-top')) 
			{
				require($f); // подключаем кастомный вывод
			}
			else
			{
				echo '<div class="info info-top">';
					mso_page_title($page_slug, $page_title, '<h1>', '</h1>', false);
					
					mso_page_date($page_date_publish, 
									array(	'format' => 'D, j F Y г.', // 'd/m/Y H:i:s'
											'days' => t('Понедельник Вторник Среда Четверг Пятница Суббота Воскресенье'),
											'month' => t('января февраля марта апреля мая июня июля августа сентября октября ноября декабря')), 
									'<span>', '</span>');
					mso_page_cat_link($page_categories, ' -&gt; ', '<br><span>' . t('Рубрика') . ':</span> ', '');
					mso_page_tag_link($page_tags, ' | ', '<br><span>' . t('Метки') . ':</span> ', '');
					mso_page_view_count($page_view_count, '<br><span>' . t('Просмотров') . ':</span> ', '');
					mso_page_meta('nastr', $page_meta, '<br><span>' . t('Настроение') . ':</span> ', '');
					mso_page_meta('music', $page_meta, '<br><span>' . t('В колонках звучит') . ':</span> ', '');
					if ($page_comment_allow) mso_page_feed($page_slug, t('комментарии по RSS'), '<br><span>' . t('Подписаться на').'</span> ', '', true);
					mso_page_edit_link($page_id, 'Edit page', '<br>[', ']');
				echo '</div>';
			}
			
			echo '<div class="page_content type_page">';
				mso_page_content($page_content);
				if ($f = mso_page_foreach('info-bottom')) require($f); // подключаем кастомный вывод
				mso_page_content_end();
				echo '<div class="break"></div>';
				
				// связанные страницы по родителям
				if ($page_nav = mso_page_nav($page_id, $page_id_parent))
					echo '<div class="page_nav">' . $page_nav . '</div>';
				
				// блок "Еще записи этой рубрики"
				mso_page_other_pages($page_id, $page_categories);
				
			echo '</div>';
		
		echo NR . '</div><!--div class="page_only"-->' . NR;
		
		if ($f = mso_page_foreach('page-only-end')) require($f);
		
		// здесь комментарии
		// page-comments.php может быть в type своего шаблона
		$fn1 = getinfo('template_dir') . 'type/page-comments.php'; 		 // путь в шаблоне
		$fn2 = getinfo('templates_dir') . 'default/type/page-comments.php'; // путь в default
		if ( file_exists($fn1) ) require($fn1); // если есть, подключаем шаблонный
		elseif (file_exists($fn2)) require($fn2); // нет, значит дефолтный
			
	endforeach;

}
else 
{
	if ($f = mso_page_foreach('pages-not-found')) 
	{
		require($f); // подключаем кастомный вывод
	}
	else // стандартный вывод
	{
		echo '<h1>' . t('404. Ничего не найдено...') . '</h1>';
		echo '<p>' . t('Извините, ничего не найдено') . '</p>';
		echo mso_hook('page_404');
	}
} // endif $pages

if ($f = mso_page_foreach('page-posle')) require($f);

echo NR . '</div><!-- class="type type_page" -->' . NR;

# конечная часть шаблона
require(getinfo('template_dir') . 'main-end.php');
	
?>