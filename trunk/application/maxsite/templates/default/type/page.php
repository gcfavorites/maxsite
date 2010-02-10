<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

mso_cur_dir_lang('templates');

mso_page_view_count_first(); // для подсчета количества прочтений страницы

// параметры для получения страниц
$par = array( 'cut'=>false, 'cat_order'=>'category_id_parent', 'cat_order_asc'=>'asc', 'type'=>false ); 

$pages = mso_get_pages($par, $pagination); // получим все

// в титле следует указать формат вывода | заменяется на  » true - использовать только page_title
mso_head_meta('title', &$pages, '%page_title%|%title%', ' » ', true ); // meta title страницы
mso_head_meta('description', &$pages); // meta description страницы
mso_head_meta('keywords', &$pages); // meta keywords страницы

// теперь сам вывод

if (!$pages and mso_get_option('page_404_http_not_found', 'templates', 1) ) header('HTTP/1.0 404 Not Found'); 

# начальная часть шаблона
require(getinfo('template_dir') . 'main-start.php');

echo NR . '<div class="type type_page">' . NR;

if ($pages) // есть страницы
{ 	
	
	foreach ($pages as $page) : // выводим в цикле
		

		if ($f = mso_page_foreach('page')) 
		{
			require($f); // подключаем кастомный вывод
			require('page-comments.php'); // здесь форма комментариев
			continue; // следующая итерация
		}

		
		extract($page);
		# pr($page);
		echo NR . '<div class="page_only">' . NR;
		
		mso_page_title($page_slug, $page_title, '<h1>', '</h1>', false);

		echo '<div class="info">';
			mso_page_cat_link($page_categories, ' -&gt; ', '<span>' . t('Рубрика') . ':</span> ', '<br>');
			mso_page_tag_link($page_tags, ' | ', '<span>' . t('Метки') . ':</span> ', '<br>');
			mso_page_date($page_date_publish, 
							array(	'format' => 'D, j F Y г.', // 'd/m/Y H:i:s'
									'days' => t('Понедельник Вторник Среда Четверг Пятница Суббота Воскресенье'),
									'month' => t('января февраля марта апреля мая июня июля августа сентября октября ноября декабря')), 
							'<span>', '</span>');
			mso_page_view_count($page_view_count, '<br><span>' . t('Просмотров') . ':</span> ', '');
			mso_page_meta('nastr', $page_meta, '<br><span>' . t('Настроение') . ':</span> ', '');
			mso_page_meta('music', $page_meta, '<br><span>' . t('В колонках звучит') . ':</span> ', '');
			if ($page_comment_allow) mso_page_feed($page_slug, t('комментарии по RSS'), '<br><span>' . t('Подписаться на').'</span> ', '', true);
			mso_page_edit_link($page_id, 'Edit page', '<br>[', ']');
		echo '</div>';
		
		echo '<div class="page_content type_page">';
			mso_page_content($page_content);
			mso_page_content_end();
			echo '<div class="break"></div>';
			
			// связанные страницы по родителям
			if ($page_nav = mso_page_nav($page_id, $page_id_parent))
			{
				echo '<div class="page_nav">' . $page_nav . '</div>';
			}
			
			// выводить ли блок "Еще записи этой рубрики"
			if ($bl_title = mso_get_option('page_other_pages', 'templates', t('Еще записи по теме', '')))
			{
				$bl_pages = mso_get_pages(
									array(  'type'=> false, 'content'=> false, 'pagination'=>false, 
											'custom_type'=> 'category', 'categories'=>$page_categories, 
											'exclude_page_id'=>array($page_id), 
											'content'=>false,
											'limit'=> mso_get_option('page_other_pages_limit', 'templates', 7), 
											'order'=>mso_get_option('page_other_pages_order', 'templates', 'page_date_publish'),
											'order_asc'=>mso_get_option('page_other_pages_order_asc', 'templates', 'random')
											),
											$_temp);
				if ($bl_pages)
				{
					echo '<div class="page_other_pages"><h3>' . $bl_title . '</h3><ul>';
					foreach ($bl_pages as $bl_page)
						mso_page_title($bl_page['page_slug'], $bl_page['page_title'], '<li>', '</li>', true);
					echo '</ul></div>';
				}
			}
			
		echo '</div>';
		
		echo NR . '</div><!--div class="page_only"-->' . NR;
		
		require('page-comments.php'); // здесь форма комментариев
		
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

echo NR . '</div><!-- class="type type_page" -->' . NR;

# конечная часть шаблона
require(getinfo('template_dir') . 'main-end.php');
	
?>