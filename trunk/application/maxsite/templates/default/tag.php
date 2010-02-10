<?php 

// параметры для получения страниц
$par = array( 'limit' => mso_get_option('limit_post', 'templates', '7'), 
			'cut' => mso_get_option('more', 'templates', 'Читать полностью »'),
			'cat_order'=>'category_name', 'cat_order_asc'=>'asc' ); 

$pages = mso_get_pages($par, $pagination); // получим все - второй параметр нужен для сформированной пагинации

mso_head_meta('title', mso_segment(2) . ' - ' . getinfo('title')); //  meta title страницы


# начальная часть шаблона
require('main-start.php');

echo '<h1 class="category">' . mso_segment(2) . '</h1>';

if ($pages) // есть страницы
{ 	
	foreach ($pages as $page) : // выводим в цикле

		extract($page);
		
		// pr($page);
	
		mso_page_title($page_slug, $page_title, '<h1>', '</h1>', true);

		echo '<div class="info">';
			mso_page_cat_link($page_categories, ' | ', '<span>Рубрики:</span> ', '<br />');
			mso_page_tag_link($page_tags, ' | ', '<span>Метки:</span> ', '<br />');
			mso_page_date($page_date_publish, 'd/m/Y H:i:s', '<span>Дата:</span> ', '');
			mso_page_edit_link($page_id, 'Edit page', ' -', '-');
			mso_page_feed($page_slug, 'комментарии по RSS', '<br /><span>Подписаться</span> на ', '', true);
		echo '</div>';
		
		
		echo '<div class="page_content">';
			//mso_hook('content_start'); # хук на начало блока
			//echo $page_content;
			//mso_hook('content_end'); # хук на конец блока
			mso_page_content($page_content);
			mso_page_comments_link($page_comment_allow, $page_slug, 'Обсудить (' . $page_count_comments . ')', '<div class="comment">', '</div>');
			
		echo '</div>';
		
		
	endforeach;
	
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