<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://maxsite.org/
 */


# функция автоподключения плагина
function perelinks_autoload($args = array())
{
	mso_hook_add( 'content_content', 'perelinks_custom'); # хук на админку
}

# функции плагина
function perelinks_custom($content = '')
{
	
	// получаем список всех титлов - возможно из кэша
	// после этого выполняем замену всех этих вхождений в тексте на ссылки
	
	global $page; // текущая страница - это массив
	
	$cache_key = 'perelinks_custom';
	if ( $k = mso_get_cache($cache_key) ) 
	{
		$all_title = $k;
	}
	else
	{
		$CI = & get_instance();
		$CI->db->select('page_title, page_slug');
		$CI->db->from('page');
		$query = $CI->db->get();
		
		$all_title = array();
		if ($query->num_rows() > 0)
		{
			foreach ($query->result_array() as $row)
			{
				$title = mb_strtolower($row['page_title'], 'UTF-8');
				$title = str_replace(array('\\', '|', '/', '?', '%', '*', '`', ',', '.', '$', '!', '\'', '"') , '', $title);
				
				$a_words = explode(' ', $title);
				$a_words = array_unique($a_words);
				
				$title = array();
				foreach ($a_words as $word)
				{
					if (mb_strlen($word, 'UTF-8')>3) $title[] = $word;
				}
				
				// $all_title = explode(' ', $title);
				foreach ($title as $word)
				{
					$all_title[$word][] = $row['page_slug'];
				}
			}
		}
		mso_add_cache($cache_key, $all_title, 900);
	}
	
	$curr_page_slug = $page['page_slug']; // текущая страница - для ссылки
	$my_site = getinfo('siteurl') . 'page/';
	// pr($all_title);
	
	
	// из-за того, что preg_match не работает с юникодом, выполняем преобразование
	// потом обратно
	//$content = mb_convert_encoding($content, "Windows-1251", "UTF-8");
	
	// ищем вхождения
	foreach ($all_title as $key => $word)
	{
		
		//$key = mb_convert_encoding($key, "Windows-1251", "UTF-8");
		
		$r = '| (' . $key . ') |siu';
		
		if ( preg_match($r , $content) )
		{
			if (!in_array($curr_page_slug, $word))
			{
				$content = preg_replace($r, ' <a href="' . $my_site . $word[0] . '/" class="perelink">\1</a> ', $content);
			}
		}
		
		// pr($word);
		// if (mb_strlen($word, 'UTF-8')>3) $title[] = $word;
		
	}
	
	//$content = mb_convert_encoding($content, "UTF-8", "Windows-1251");
	
	return  $content;
}



?>