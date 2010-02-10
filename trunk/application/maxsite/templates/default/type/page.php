<?php 

mso_page_view_count_first(); // для подсчета количества прочтений страницы

// параметры для получения страниц
$par = array( 'cut'=>false, 'cat_order'=>'category_id_parent', 'cat_order_asc'=>'asc', 'type'=>false ); 

$pages = mso_get_pages($par, $pagination); // получим все

// в титле следует указать формат вывода | заменяется на  » true - использовать только page_title
mso_head_meta('title', &$pages, '%page_title%|%title%', ' » ', true ); // meta title страницы
mso_head_meta('description', &$pages); // meta description страницы
mso_head_meta('keywords', &$pages); // meta keywords страницы


// теперь сам вывод

# начальная часть шаблона
require(getinfo('template_dir') . 'main-start.php');

if ($pages) // есть страницы
{ 	
	foreach ($pages as $page) : // выводим в цикле

		extract($page);
		// pr($page);
		
		mso_page_title($page_slug, $page_title, '<h1>', '</h1>', false);

		echo '<div class="info">';
			mso_page_cat_link($page_categories, ' -&gt; ', '<span>Рубрика:</span> ', '<br />');
			mso_page_tag_link($page_tags, ' | ', '<span>Метки:</span> ', '<br />');
			mso_page_date($page_date_publish, 'd/m/Y H:i:s', '<span>Дата:</span> ', '');
			mso_page_view_count($page_view_count, '<br /><span>Просмотров:</span> ', '');
			mso_page_meta('nastr', $page_meta, '<br /><span>Настроение:</span> ', '');
			mso_page_meta('music', $page_meta, '<br /><span>В колонках звучит:</span> ', '');
			if ($page_comment_allow) mso_page_feed($page_slug, 'комментарии по RSS', '<br /><span>Подписаться</span> на ', '', true);
			mso_page_edit_link($page_id, 'Edit page', '<br />[', ']');
		echo '</div>';
		
		echo '<div class="page_content">';
			mso_page_content($page_content);
			echo '<div class="break"></div>';
			
			// выводить ли блок "Еще записи этой рубрики"
			if ($bl_title = mso_get_option('page_other_pages', 'templates', 'Еще записи по теме'))
			{
				$bl_pages = mso_get_pages(
									array( 'limit'=> 7, 'type'=> false, 'content'=> false, 'pagination'=>false, 
											'custom_type'=> 'category', 'categories'=>$page_categories, 
											'exclude_page_id'=>array($page_id), 'order_asc'=>'random'),
											$_temp);
				if ($bl_pages)
				{
					echo '<div class="page_other_pages"><h3>' . $bl_title . '</h3><ul>';
					foreach ($bl_pages as $bl_page)
						mso_page_title($bl_page['page_slug'], $bl_page['page_title'], '<li>', '</li>', true);
					echo '</ul></div>';
				}
			}
			
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
require(getinfo('template_dir') . 'main-end.php');
	
?>