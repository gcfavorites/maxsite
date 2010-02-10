<?php 

# подготовка данных

// параметры для получения страниц
$par = array( 'limit' => mso_get_option('limit_post', 'templates', '15'), 
			'cut' => mso_get_option('more', 'templates', 'Читать полностью »'),
			'cat_order'=>'category_name', 'cat_order_asc'=>'asc', 'content'=> false ); 

$pages = mso_get_pages($par, $pagination); // получим все - второй параметр нужен для сформированной пагинации

$title_page = mso_head_meta('title', &$pages, '%category_name%'); // заголовок для записи на основе титла

mso_head_meta('title', &$pages, '%category_name%|%title%', ' » '); //  meta title страницы
mso_head_meta('description', &$pages, '%category_name%'); // meta description страницы
mso_head_meta('keywords', &$pages, '%category_name%'); // meta keywords страницы


# начальная часть шаблона
require('main-start.php');

echo '<h1 class="category">' . $title_page . '</h1>';

if ($pages) // есть страницы
{ 	
	echo '<ul class="category">';
	foreach ($pages as $page) : // выводим в цикле

		extract($page);
		//pr($page);
		
		mso_page_title($page_slug, $page_title, '<li>', '', true);
		mso_page_date($page_date_publish, 'd/m/Y', ' - ', '');
		echo '</li>';
		/*
		echo '<div class="info">';
			mso_page_cat_link($page_categories, ' | ', '<span>Рубрика:</span> ', '<br />');
			mso_page_tag_link($page_tags, ' | ', '<span>Метки:</span> ', '<br />');
			mso_page_date($page_date_publish, 'd/m/Y H:i:s', '<span>Дата:</span> ', '');
			mso_page_edit_link($page_id, 'Edit page', ' -', '-');
			mso_page_feed($page_slug, 'комментарии по RSS', '<br /><span>Подписаться</span> на ', '', true);
		echo '</div>';
		
		
		echo '<div class="page_content">';
			mso_hook('content_start'); # хук на начало блока
			echo $page_content;
			mso_hook('content_end'); # хук на конец блока
			echo '<div class="break"></div>';
			mso_page_comments_link($page_comment_allow, $page_slug, 'Обсудить (' . $page_count_comments . ')', '<div class="comments-link">', '</div>');
			
		echo '</div>';
		*/
		
	endforeach;
	echo '</ul>';
	
	if (function_exists('pagination_go')) 
		echo pagination_go($pagination); // вывод навигации

}
else 
{
 
	echo '<h1>404. Ничего не найдено...</h1>';
	echo '<p>Извините, ничего не найдено</p>';
	
} // endif $pages


# конечная часть шаблона
require('main-end.php');
	
?>