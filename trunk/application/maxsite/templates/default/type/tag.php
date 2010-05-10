<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

mso_cur_dir_lang('templates');

// параметры для получения страниц
$par = array( 'limit' => mso_get_option('limit_post', 'templates', '7'), 
			'cut' => mso_get_option('more', 'templates', t('Читать полностью »')),
			'cat_order'=>'category_name', 'cat_order_asc'=>'asc', 'type'=>false ); 

// подключаем кастомный вывод, где можно изменить массив параметров $par для своих задач
if ($f = mso_page_foreach('tag-mso-get-pages')) require($f); 
		
$pages = mso_get_pages($par, $pagination); // получим все - второй параметр нужен для сформированной пагинации

if ($f = mso_page_foreach('tag-head-meta')) require($f);
else
{
	mso_head_meta('title', mso_segment(2) . ' » ' . getinfo('title')); //  meta title страницы
}

if (!$pages and mso_get_option('page_404_http_not_found', 'templates', 1) ) header('HTTP/1.0 404 Not Found'); 

# начальная часть шаблона
require(getinfo('template_dir') . 'main-start.php');

echo NR . '<div class="type type_tag">' . NR;

if ($f = mso_page_foreach('tag-do')) require($f); // подключаем кастомный вывод
	else echo '<h1 class="category">' . htmlspecialchars(mso_segment(2)) . '</h1>';

if ($pages) // есть страницы
{
	if ( mso_get_option('category_show_rss_text', 'templates', 1) )
	{
		if ($f = mso_page_foreach('tag-show-rss-text')) 
			require($f); // подключаем кастомный вывод
		else 
			echo '<h3 class="category"><a href="' . getinfo('siteurl') . mso_segment(1) . '/' . mso_segment(2) . '/feed">'. t('Подписаться на эту метку по RSS'). '</a></h3>';
	}
	
	$full_posts = mso_get_option('tag_full_text', 'templates', '1'); // полные или короткие записи
	
	if (!$full_posts) echo '<ul class="category">';
	
	foreach ($pages as $page) : // выводим в цикле

		if ($f = mso_page_foreach('tag')) 
		{
			require($f); // подключаем кастомный вывод
			continue; // следующая итерация
		}
		
		extract($page);
		
		if (!$full_posts)
		{
			mso_page_title($page_slug, $page_title, '<li>', '', true);
			mso_page_date($page_date_publish, 'd/m/Y', ' - ', '');
			echo '</li>';
		}
		else
		{
			echo NR . '<div class="page_only">' . NR;
			echo '<div class="info">';
				mso_page_title($page_slug, $page_title, '<h1>', '</h1>', true);
				mso_page_cat_link($page_categories, ' | ', '<span>'. t('Рубрика'). ':</span> ', '<br>');
				mso_page_tag_link($page_tags, ' | ', '<span>'. t('Метки'). ':</span> ', '<br>');
				mso_page_date($page_date_publish, 'd/m/Y H:i:s', '<span>'. t('Дата'). ':</span> ', '');
				mso_page_edit_link($page_id, 'Edit page', ' -', '-');
				// mso_page_feed($page_slug, t('комментарии по RSS'), '<br><span>'. t('Подписаться на'). '</span> ', '', true);
			echo '</div>';
			
			
			echo '<div class="page_content">';
				mso_page_content($page_content);
				mso_page_content_end();
				echo '<div class="break"></div>';
				mso_page_comments_link($page_comment_allow, $page_slug, t('Обсудить'). ' (' . $page_count_comments . ')', '<div class="comments-link"><span>', '</span></div>');
				
			echo '</div>';
			echo NR . '</div><!--div class="page_only"-->' . NR;
		}
		
	endforeach;
	
	if (!$full_posts) echo '</ul>';
	
	if ($f = mso_page_foreach('tag-posle-pages')) require($f); // подключаем кастомный вывод
	
	mso_hook('pagination', $pagination);
		
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

if ($f = mso_page_foreach('tag-posle')) require($f); // подключаем кастомный вывод


echo NR . '</div><!-- class="type type_tag" -->' . NR;

# конечная часть шаблона
require(getinfo('template_dir') . 'main-end.php');
	
?>