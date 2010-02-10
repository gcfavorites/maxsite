<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

# функция автоподключения плагина
function sitemap_autoload($args = array())
{
	mso_hook_add( 'content', 'sitemap_content'); # хук на обработку текста [sitemap]
	mso_hook_add( 'page_404', 'sitemap404'); # хук на 404-страницу
}


# оюработка текста на предмет в нем [sitemap]
function sitemap_content($text = '')
{
	if (strpos($text, '[sitemap]') === false) // нет в тексте
	{
		return $text;
	}
	else 
	{
		return str_replace('[sitemap]', sitemap(), $text);
	}
}


# оюработка текста на предмет в нем [sitemap]
function sitemap404($text = '')
{
	return  '<h2 class="sitemap">' . t('Воспользуйтесь картой сайта', 'plugins') . '</h2>' . sitemap();
}

# явный вызов функции - отдается карта сайта
function sitemap($arg = array())
{
	global $MSO;

	// кэш строим по url, потому что у он меняется от пагинации
	$cache_key = 'sitemap' . serialize($MSO->data['uri_segment']);
	$k = mso_get_cache($cache_key);
	if ($k) return $k; // да есть в кэше
	
	$out = '';
	// параметры для получения страниц
	$par = array( 
			//'no_limit' => true,
			 'limit'=>30,
			// 'type'=> false, 
			'custom_type'=> 'home', 
			'content'=> false,
			'cat_order'=>'category_id_parent', 
			'cat_order_asc'=>'asc',
			//'order_asc'=> 'desc',
			); 
	$pages = mso_get_pages($par, $pagination); // получим все
	
	if ($pages)
	{
		$out .= '<div class="sitemap">' . NR;
		$first = true;
		foreach ($pages as $page)
		{
			$date = mso_date_convert('m/Y', $page['page_date_publish']);
			
			if ($first) 
			{
				$out .= '<h3>' . $date . '</h3>' . NR . '<ul>' . NR;
				$first = false;
			}
			elseif ($date1 != $date)
			{
				$out .= '</ul>' . NR . '<h3>' . $date . '</h3>' . NR . '<ul>' . NR;
			}
			
			$slug = mso_slug($page['page_slug']);
			
			$out .= '<li>' . mso_date_convert('d', $page['page_date_publish']) . ': <a href="' . getinfo('siteurl') 
					. 'page/' . $slug . '" title="' . $page['page_title'] . '">' 
					. $page['page_title'] . '</a>';
			
			if ($page['page_categories'])
				$out .=  ' ('
						. mso_page_cat_link($page['page_categories'], ' &rarr; ', '', '', false)
						. ')';
					# синонимы ссылок
					/*
					. ' ('
					. '<a href="' . getinfo('siteurl') . $slug . '" title="slug: ' . $slug . '">slug</a>, '
					. '<a href="' . getinfo('siteurl') . 'page/' . $page['page_id'] . '" title="page: ' . $page['page_id'] . '">page: ' . $page['page_id'] . '</a>, '
					. '<a href="' . getinfo('siteurl') . $page['page_id'] . '" title="id: ' . $page['page_id'] . '">id: ' . $page['page_id'] . '</a>)'
					*/
					# /синонимы ссылок
					
			$out .=  '</li>' . NR;
					
			$date1 = $date;
		}

		$out .= '</ul>' . NR . '</div>' . NR;
	}
	
	
	$pagination['type'] = '';
	ob_start();
	mso_hook('pagination', $pagination);
	$out .=  ob_get_contents();
	ob_end_clean();
	

	mso_add_cache($cache_key, $out); // сразу в кэш добавим
	
	return $out;
}


?>