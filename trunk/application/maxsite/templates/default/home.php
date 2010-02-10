<?php 

// возможно указана страница для отображения вверху перед всеми страницами
if (mso_get_option('home_page_id_top', 'templates', '0'))
{
	$par = array( 
			// колво записей на главной
			'limit' => 1, 
			// номер записи для главной
			'page_id' => mso_get_option('home_page_id_top', 'templates', '0'), 
			// текст для Далее
			'cut' => mso_get_option('more', 'templates', 'Читать полностью »'),
			// сортировка рубрик
			'cat_order' => 'category_name', 
			// порядок сортировки
			'cat_order_asc' => 'asc' ); 
	
	$page_top = mso_get_pages($par, $pag);
}
else $page_top = false;


// параметры для получения страниц
$par = array( 
			// колво записей на главной
			'limit' => mso_get_option('home_limit_post', 'templates', '7'), 
			
			// номер записи для главной
			'page_id' => mso_get_option('home_page_id', 'templates', '0'), 
			
			// рубрики для главной
			'cat_id' => mso_get_option('home_cat_id', 'templates', '0'), 
			
			// полные ли записи (1) или только заголовки (0)
			'content'=> mso_get_option('home_full_text', 'templates', '1'), 
			
			// текст для Далее
			'cut' => mso_get_option('more', 'templates', 'Читать полностью »'),
			
			// сортировка рубрик
			'cat_order' => 'category_name', 
			
			// порядок сортировки
			'cat_order_asc' => 'asc' ); 

$pages = mso_get_pages($par, $pagination); // получим все - второй параметр нужен для сформированной пагинации


// теперь сам вывод

# начальная часть шаблона
require('main-start.php');


// если есть верхняя страница, то выводим
if ($page_top) // есть страницы
{ 	
	echo '<div class="home_top">';
	
	foreach ($page_top as $page)  // выводим в цикле
	{
		extract($page);
		mso_page_title($page_slug, $page_title, '<h1>', '</h1>', true);
	
		echo '<div class="page_content">';
			mso_hook('content_start'); # хук на начало блока
			echo mso_hook('content_content', $page_content);
			mso_hook('content_end'); # хук на конец блока
			echo '<div class="break"></div>';
		echo '</div>';
	}
	echo '</div>';
}

// если указан текст перед всеми записями, то выводим и его
if ( $home_text_do = mso_get_option('home_text_do', 'templates', '') ) echo $home_text_do;


if ($pages) // есть страницы
{ 	
	// выводим полнные тексты или списком
	if ( !mso_get_option('home_full_text', 'templates', '1') ) echo '<ul class="category">';
		
	foreach ($pages as $page) : // выводим в цикле

		extract($page);
		// pr($page);
		
		// выводим полные тексты или списком
		if ( mso_get_option('home_full_text', 'templates', '1') )
		{ 
			mso_page_title($page_slug, $page_title, '<h1>', '</h1>', true);

			echo '<div class="info">';
				mso_page_date($page_date_publish, 'd/m/Y H:i:s', '<span>Дата:</span> ', '<br />');
				mso_page_cat_link($page_categories, ' | ', '<span>Рубрика:</span> ', '<br />');
				mso_page_tag_link($page_tags, ' | ', '<span>Метки:</span> ', '');
				mso_page_edit_link($page_id, 'Edit page', ' -', '-');
				# mso_page_feed($page_slug, 'комментарии по RSS', '<br /><span>Подписаться</span> на ', '', true);
			echo '</div>';
			
			echo '<div class="page_content">';
				mso_hook('content_start'); # хук на начало блока
				echo mso_hook('content_content', $page_content);
				mso_hook('content_end'); # хук на конец блока
				echo '<div class="break"></div>';
				mso_page_comments_link($page_comment_allow, $page_slug, 'Обсудить (' . $page_count_comments . ')', '<div class="comments-link">', '</div>');
			echo '</div>';
		}
		else // списком
		{
			mso_page_title($page_slug, $page_title, '<li>', '', true);
			mso_page_date($page_date_publish, 'd/m/Y', ' - ', '');
			echo '</li>';
		}
		
	endforeach;
	
	if ( !mso_get_option('home_full_text', 'templates', '1') ) echo '</ul><!--ul class="category"-->';
	
	if (function_exists('pagination_go')) 
		echo pagination_go($pagination); // вывод навигации
		
}
else 
{
 
	echo '<h1>404. Ничего не найдено...</h1>';
	echo '<p>Извините, ничего не найдено</p>';
	
} // endif $pages

// pr($MSO);

# конечная часть шаблона
require('main-end.php');
	
?>