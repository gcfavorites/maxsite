<?php 

// нужно выводить рубрики блоками - сделаем отдельным файлом
if (mso_get_option('home_cat_block', 'templates', '0'))
{
	require('home-cat-block.php'); 
	return;
}

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
			'cat_order_asc' => 'asc',
			'pagination' => false,
			); 
	
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
			'cat_order' => 'category_id_parent', 
			
			// порядок сортировки
			'cat_order_asc' => 'asc',
	
			); 

// если только заголовки, то отключим пагинацию
// if ( !mso_get_option('home_full_text', 'templates', '1') ) $par['pagination'] = false;

$pages = mso_get_pages($par, $pagination); // получим все - второй параметр нужен для сформированной пагинации


// теперь сам вывод

# начальная часть шаблона
require(getinfo('template_dir') . 'main-start.php');

// если есть верхняя страница, то выводим
if ($page_top) // есть страницы
{ 	
	echo '<div class="home_top">';
	
	foreach ($page_top as $page)  // выводим в цикле
	{
		extract($page);
		mso_page_title($page_slug, $page_title, '<h1>', '</h1>', true);
	
		echo '<div class="page_content">';
			mso_page_content($page_content);
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
			
			echo NR . '<div class="page_only">' . NR;
		
			mso_page_title($page_slug, $page_title, '<h1>', '</h1>', true);

			echo '<div class="info">';
				mso_page_date($page_date_publish, 
							array(	'format' => 'D, j F Y г.', // 'd/m/Y H:i:s'
									'days' => 'Понедельник Вторник Среда Четверг Пятница Суббота Воскресенье',
									'month' => 'января февраля марта апреля мая июня июля августа сентября октября ноября декабря' ), 
							'<span>', '</span><br />');
				
				mso_page_cat_link($page_categories, ' -&gt; ', '<span>Рубрика:</span> ', '<br />');
				mso_page_tag_link($page_tags, ' | ', '<span>Метки:</span> ', '');
				mso_page_edit_link($page_id, 'Edit page', ' [', ']');
				# mso_page_feed($page_slug, 'комментарии по RSS', '<br /><span>Подписаться</span> на ', '', true);
			echo '</div>';
			
			echo '<div class="page_content type_home">';
			
				mso_page_content($page_content);
				echo '<div class="break"></div>';
				
				mso_page_comments_link( array( 
					'page_comment_allow' => $page_comment_allow,
					'page_slug' => $page_slug,
					'title' => 'Обсудить (' . $page_count_comments . ')',
					'title_no_link' => 'Читать комментарии (' . $page_count_comments . ')',
					'do' => '<div class="comments-link"><span>',
					'posle' => '</span></div>',
					'page_count_comments' => $page_count_comments
				 ));
				
				// mso_page_comments_link($page_comment_allow, $page_slug, 'Обсудить (' . $page_count_comments . ')', '<div class="comments-link">', '</div>');
				
			echo '</div>';
			echo NR . '</div><!--div class="page_only"-->' . NR;
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


# конечная часть шаблона
require(getinfo('template_dir') . 'main-end.php');
	
?>