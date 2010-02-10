<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

mso_cur_dir_lang('templates');

# подготовка данных

$full_posts = mso_get_option('category_full_text', 'templates', '1'); // полные или короткие записи
	
// параметры для получения страниц
$par = array( 'limit' => mso_get_option('limit_post', 'templates', '15'), 
			'cut' => mso_get_option('more', 'templates', 'Читать полностью »'),
			'cat_order'=>'category_id_parent', 'cat_order_asc'=>'asc', 'type'=> false, 'content'=> $full_posts ); 

$pages = mso_get_pages($par, $pagination); // получим все - второй параметр нужен для сформированной пагинации

$title_page = mso_head_meta('title', &$pages, '%category_name%'); // заголовок для записи на основе титла

mso_head_meta('title', &$pages, '%category_name%|%title%', ' » '); //  meta title страницы
mso_head_meta('description', &$pages, '%category_name%'); // meta description страницы
mso_head_meta('keywords', &$pages, '%category_name%'); // meta keywords страницы


# начальная часть шаблона
require(getinfo('template_dir') . 'main-start.php');

echo '<h1 class="category">' . $title_page . '</h1>';

if ($pages) // есть страницы
{ 	

	echo '<h3 class="category"><a href="' . getinfo('siteurl') . mso_current_url() . '/feed">'. t('Подписаться на эту рубрику по RSS'). '</a></h3>';

	if (!$full_posts) echo '<ul class="category">';
	
	foreach ($pages as $page) : // выводим в цикле

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
				mso_page_cat_link($page_categories, ' | ', '<span>'. t('Рубрика'). ':</span> ', '<br />');
				mso_page_tag_link($page_tags, ' | ', '<span>'. t('Метки'). ':</span> ', '<br />');
				mso_page_date($page_date_publish, 'd/m/Y H:i:s', '<span>'. t('Дата'). ':</span> ', '');
				mso_page_edit_link($page_id, 'Edit page', ' -', '-');
				// mso_page_feed($page_slug, t('комментарии по RSS'), '<br /><span>'. t('Подписаться на'). '</span> ', '', true);
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
	
	mso_hook('pagination', $pagination);

}
else 
{
 
	echo '<h1>'.t('404. Ничего не найдено...').'</h1>';
	echo '<p>'.t('Извините, ничего не найдено').'</p>';
	
} // endif $pages


# конечная часть шаблона
require(getinfo('template_dir') . 'main-end.php');
	
?>