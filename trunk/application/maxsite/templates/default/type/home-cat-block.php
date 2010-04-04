<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

mso_cur_dir_lang('templates');

// нужно выводить рубрики блоками 

# начальная часть шаблона
require(getinfo('template_dir') . 'main-start.php');

echo NR . '<div class="type type_home_cat_block">' . NR;

// возможно указана страница для отображения вверху перед всеми страницами
if (mso_get_option('home_page_id_top', 'templates', '0'))
{
	$par = array( 
			'limit' => 1, 
			'page_id' => mso_get_option('home_page_id_top', 'templates', '0'), 
			'cut' => mso_get_option('more', 'templates', 'Читать полностью »'),
			'cat_order' => 'category_name', 
			'cat_order_asc' => 'asc',
			'pagination' => false,
			); 
	// подключаем кастомный вывод, где можно изменить массив параметров $par для своих задач
	if ($f = mso_page_foreach('home-top-mso-get-pages')) require($f); 
	
	$page_top = mso_get_pages($par, $pag);

	// если есть верхняя страница, то выводим
	if ($page_top) // есть страницы
	{ 	
		echo '<div class="home_top">';
		
		foreach ($page_top as $page)  // выводим в цикле
		{
			if ($f = mso_page_foreach('home-top')) 
			{
				require($f); // подключаем кастомный вывод
				continue; // следующая итерация
			}

			extract($page);
			mso_page_title($page_slug, $page_title, '<h1>', '</h1>', true);
		
			echo '<div class="page_content">';
				mso_page_content($page_content);
				mso_page_content_end();
				echo '<div class="break"></div>';
			echo '</div>';
		}
		echo '</div>';
	}	
}

// возможно указано выводить последнюю запись блога перед всеми
if (mso_get_option('home_last_page', 'templates', '0'))
{
	$par = array( 
			'limit' => 1, 
			'cut' => mso_get_option('more', 'templates', 'Читать полностью »'),
			'cat_order' => 'category_name', 
			'cat_order_asc' => 'asc',
			'pagination' => false,
			); 
	// подключаем кастомный вывод, где можно изменить массив параметров $par для своих задач
	if ($f = mso_page_foreach('home-cat-block-last-page-mso-get-pages')) require($f); 
	
	$page_last = mso_get_pages($par, $pag);
	
	if ($page_last)
	{
		echo '<div class="home_page_last">';
		
		foreach ($page_last as $page)  // выводим в цикле
		{
			if ($f = mso_page_foreach('home-cat-block-last-page')) 
			{
				require($f); // подключаем кастомный вывод
				continue; // следующая итерация
			}
		
			extract($page);

			mso_page_title($page_slug, $page_title, '<h1>', '</h1>', true);

			echo '<div class="page_content">';
				echo '<div class="info">';
					mso_page_date($page_date_publish, 
						array(	'format' => 'D, j F Y г.', // 'd/m/Y H:i:s'
								'days' => t('Понедельник Вторник Среда Четверг Пятница Суббота Воскресенье'),
								'month' => t('января февраля марта апреля мая июня июля августа сентября октября ноября декабря')), 
								'<span>', '</span><br>');
					mso_page_cat_link($page_categories, ' -&gt; ', '<span>'.t('Рубрика').':</span> ', '<br>');
					mso_page_tag_link($page_tags, ' | ', '<span>'.t('Метки').':</span> ', '');                  
					mso_page_edit_link($page_id, 'Edit page', ' [', ']');
				echo '</div>';
				mso_page_content($page_content);
				mso_page_content_end();
				echo '<div class="break"></div>';
			echo '</div>';
		}
		echo '</div>';
	}
}

// если указан текст перед всеми записями, то выводим и его
if ( $home_text_do = mso_get_option('home_text_do', 'templates', '') ) echo $home_text_do;

// Поскольку у нас вывод рубрик блоками, то нужно считать каждую указанную рубрику и в цикле
// получить для неё все данные и сразу вывести

// параметры для получения страниц - общие для всех
$par = array( 
			// колво записей на главной
			'limit' => mso_get_option('home_limit_post', 'templates', '7'), 
			// номер записи для главной
			'page_id' => mso_get_option('home_page_id', 'templates', '0'), 
			// рубрики для главной
			// 'cat_id' => mso_get_option('home_cat_id', 'templates', '0'), 
			// полные ли записи (1) или только заголовки (0)
			'content'=> mso_get_option('home_full_text', 'templates', '1'), 
			// текст для Далее
			'cut' => mso_get_option('more', 'templates', 'Читать полностью »'),
			// сортировка рубрик
			'cat_order' => 'category_id_parent', 
			// порядок сортировки
			'cat_order_asc' => 'asc',
			'pagination' => false,
			); 



$home_cat_block = mso_get_option('home_cat_id', 'templates', '0');
$home_cat_block = mso_explode($home_cat_block); // в массив

# все блоки можно закэшировать
$key_home_cache = serialize($home_cat_block);

if ( $k = mso_get_cache($key_home_cache) ) 
	print($k); // да есть в кэше
else
{
	ob_start();

	if ($home_cat_block) // есть рубрики
	{
		$all_cats = mso_cat_array_single(); // список всех рубрик
		
		foreach($home_cat_block as $cat_id)
		{
			// выводим кастомный вывод на этот цикл, чтобы иметь возможность менять его целиком

			if ($f = mso_page_foreach('home-cat-block')) 
			{
				require($f); // подключаем кастомный вывод
				continue; // следующая итерация
			}
		
			$par['cat_id'] = $cat_id;
			
			// подключаем кастомный вывод, где можно изменить массив параметров $par для своих задач
			if ($f = mso_page_foreach('home-cat-block-mso-get-pages')) require($f); 
			
			$pages = mso_get_pages($par, $pagination); // получим все - второй параметр нужен для сформированной пагинации
			
			// и выводим как обычно на главной, только добавляем в начало блока заголовок из рубрик
			if ($pages) // есть страницы
			{ 	
				
				echo '<h1 class="home-cat-block">' . $all_cats[$cat_id]['category_name'] . '</h1>';
				
				
				// выводим полнные тексты или списком
				if ( !mso_get_option('home_full_text', 'templates', '1') ) echo '<ul class="home-cat-block">';
					
				foreach ($pages as $page) : // выводим в цикле

					extract($page);
					// pr($page);
					
					// выводим полные тексты или списком
					if ( mso_get_option('home_full_text', 'templates', '1') )
					{ 
						mso_page_title($page_slug, $page_title, '<h1>', '</h1>', true);

						echo '<div class="info">';
							mso_page_date($page_date_publish, 
										array(	'format' => 'D, j F Y г.', // 'd/m/Y H:i:s'
												'days' => t('Понедельник Вторник Среда Четверг Пятница Суббота Воскресенье'),
												'month' => t('января февраля марта апреля мая июня июля августа сентября октября ноября декабря')), 
										'<span>', '</span><br>');
							
							mso_page_cat_link($page_categories, ' -&gt; ', '<span>'.t('Рубрика').':</span> ', '<br>');
							mso_page_tag_link($page_tags, ' | ', '<span>'.t('Метки').':</span> ', '');                  
							mso_page_edit_link($page_id, 'Edit page', ' [', ']');
							# mso_page_feed($page_slug, 'комментарии по RSS', '<br><span>Подписаться</span> на ', '', true);
						echo '</div>';
						
						echo '<div class="page_content type_home">';
						
							mso_page_content($page_content);
							mso_page_content_end();
							echo '<div class="break"></div>';
							
							mso_page_comments_link( array( 
								'page_comment_allow' => $page_comment_allow,
								'page_slug' => $page_slug,
								'title' => 'Обсудить (' . $page_count_comments . ')',
								'title_no_link' => t('Читать комментарии').' (' . $page_count_comments . ')',
								'do' => '<div class="comments-link"><span>',
								'posle' => '</span></div>',
								'page_count_comments' => $page_count_comments
							 ));
							
							// mso_page_comments_link($page_comment_allow, $page_slug, 'Обсудить (' . $page_count_comments . ')', '<div class="comments-link">', '</div>');
							
						echo '</div>';
					}
					else // списком
					{
						mso_page_title($page_slug, $page_title, '<li>', '', true);
						mso_page_date($page_date_publish, 'd/m/Y', ' - ', '');
						echo '</li>';
					}
					
				endforeach;
				
				if ( !mso_get_option('home_full_text', 'templates', '1') ) echo '</ul><!--ul class="home-cat-block"-->';
				
				// if (function_exists('pagination_go')) echo pagination_go($pagination); // вывод навигации
					
			}// endif $pages
			
		} # end foreach $home_cat_block
	}
	
	mso_add_cache($key_home_cache, ob_get_flush());
	
} // if $k

echo NR . '</div><!-- class="type type_home_cat_block" -->' . NR;

# конечная часть шаблона
require(getinfo('template_dir') . 'main-end.php');
	
?>