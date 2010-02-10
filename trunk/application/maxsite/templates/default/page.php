<?php 

// параметры для получения страниц
$par = array( 'cut'=>false, 'cat_order'=>'category_name', 'cat_order_asc'=>'asc', 'type'=>false ); 

$pages = mso_get_pages($par, $pagination); // получим все

// в титле следует указать формат вывода | заменяется на  » true - использовать только page_title
mso_head_meta('title', &$pages, '%page_title%|%title%', ' » ', true ); // meta title страницы
mso_head_meta('description', &$pages); // meta description страницы
mso_head_meta('keywords', &$pages); // meta keywords страницы


// теперь сам вывод

# начальная часть шаблона
require('main-start.php');

if ($pages) // есть страницы
{ 	
	foreach ($pages as $page) : // выводим в цикле

		extract($page);
		// pr($page);
		
		mso_page_title($page_slug, $page_title, '<h1>', '</h1>', false);

		echo '<div class="info">';
			mso_page_cat_link($page_categories, ' | ', '<span>Рубрика:</span> ', '<br />');
			mso_page_tag_link($page_tags, ' | ', '<span>Метки:</span> ', '<br />');
			mso_page_date($page_date_publish, 'd/m/Y H:i:s', '<span>Дата:</span> ', '');
			mso_page_edit_link($page_id, 'Edit page', ' -', '-');
			mso_page_feed($page_slug, 'комментарии по RSS', '<br /><span>Подписаться</span> на ', '', true);
		echo '</div>';
		
		echo '<div class="page_content">';
			mso_hook('content_start'); # хук на начало блока
			echo mso_hook('content_content', $page_content);
			mso_hook('content_end'); # хук на конец блока
			echo '<div class="break"></div>';
			require('page-comments.php'); // здесь форма комментариев
		echo '</div>';
		
		
	endforeach;
}
else 
{
 
	echo '<h1>404. Ничего не найдено...</h1>';
	echo '<p>Извините, ничего не найдено</p>';
	
} // endif $pages


# конечная часть шаблона
require('main-end.php');
	
?>