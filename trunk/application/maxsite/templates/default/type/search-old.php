<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

	mso_cur_dir_lang('templates');
	
	# подготовка данных

	$search = mso_segment(2);

	$search = mso_strip(strip_tags($search));
	$searh_to_text = mb_strtolower($search, 'UTF8');

	if ($f = mso_page_foreach('search-head-meta')) require($f);
	else
	{ 
		mso_head_meta('title', $search); //  meta title страницы
	}
	
	// параметры для получения страниц
	if (!$search or (strlen(mso_slug($search)) <= 3) ) // нет запроса или он короткий
	{
		$search = t('Поиск');
		$pages = false; // нет страниц
	}
	else
	{
		$par = array( 'limit' => mso_get_option('limit_post', 'templates', '7'), 'cut'=>false, 'type'=>false ); 
		
		// подключаем кастомный вывод, где можно изменить массив параметров $par для своих задач
		if ($f = mso_page_foreach('search-mso-get-pages')) require($f); 

		$pages = mso_get_pages($par, $pagination); // получим все - второй параметр нужен для сформированной пагинации
	}
	
if (!$pages and mso_get_option('page_404_http_not_found', 'templates', 1) ) header('HTTP/1.0 404 Not Found'); 

# начальная часть шаблона
require(getinfo('template_dir') . 'main-start.php');

echo NR . '<div class="type type_search">' . NR;

if ($f = mso_page_foreach('search-do')) require($f); // подключаем кастомный вывод
	else echo '<h1 class="category">' . mb_strtoupper($search, 'UTF8') . '</h1>';

if ($pages) // есть страницы
{ 	
	
	$max_char_count = 150; // колво символов до и после
	
	echo '<ul class="category">';
	foreach ($pages as $page) : // выводим в цикле
		

		if ($f = mso_page_foreach('search')) 
		{
			require($f); // подключаем кастомный вывод
			continue; // следующая итерация
		}

		
		extract($page);
		
		mso_page_title($page_slug, $page_title, '<li>', '', true);
		mso_page_date($page_date_publish, 'd/m/Y', ' - ', '');
		
		// разобъем тексты так, чтобы в середине оказались поисковые слова
		$page_content = mb_strtolower(strip_tags($page_content), 'UTF8' );
		$page_content = str_replace($searh_to_text, '_mso_split_' . $searh_to_text, $page_content);
		
		$arr = explode('_mso_split_', $page_content);
		
		$flag = true;
		foreach($arr as $key=>$val)
		{
			if ( strpos( $val, $searh_to_text ) ) // есть сеарх
			{
				if ($flag) // текст перед сеарх
				{
					$arr[$key] = ' &lt;...&gt; ' . mb_substr($val, -100, 100, 'UTF8') . ' ';
					$flag = false;
				}
				else
				{
					$arr[$key] = ' ' . mb_substr($val, 0, $max_char_count, 'UTF8') . ' &lt;...&gt; <br> ';
					$flag = true;
				}
			}
			else 
			{
				if (!$flag) // текст перед сеарх
				{
					$arr[$key] = ' ' . mb_substr($val, -$max_char_count, $max_char_count, 'UTF8') . ' ';
					$flag = false;
				}
				else
				{
					$arr[$key] = ' ' . mb_substr($val, 0, $max_char_count, 'UTF8') . ' &lt;...&gt; ';
					$flag = true;
				}
			}
			// echo $arr[$key] . '<hr>';
		}
		
		$page_content = implode(' ', $arr); 
		
		// подсветим найденные
		$page_content = str_replace($searh_to_text, '<span style="color: red; background: yellow;" class="search">' . $searh_to_text . '</span>', $page_content);
		
		// кол-во совпадений
		$cou = substr_count($page_content, $searh_to_text) + substr_count(mb_strtolower($page_title, 'UTF8'), $searh_to_text);
		
		echo ' - '. t('Совпадений'). ': ' . $cou;
		echo '<p>' . $page_content . '</p>';

		echo '</li>';
	
	endforeach;
	
	echo '</ul>';
	
	mso_hook('pagination', $pagination);
}
else 
{
	if ($f = mso_page_foreach('pages-not-found')) 
	{
		require($f); // подключаем кастомный вывод
	}
	else // стандартный вывод
	{
		echo '<h2>'. t('404. Ничего не найдено...'). '</h2>';
		echo '<p>'. t('Извините, ничего не найдено, попробуйте повторить поиск.'). '</p>';

		echo '
		<p><br><form name="f_search" action="" method="get" onsubmit="location.href=\'' . getinfo('siteurl') . 'search/\' + encodeURIComponent(this.s.value).replace(/%20/g, \'+\'); return false;">	<input type="text" class="text" name="s" size="20" onfocus="if (this.value == \''. t('что искать?'). '\') {this.value = \'\';}" onblur="if (this.value == \'\') {this.value = \''. t('что искать?'). '\';}" value="'. t('что искать?'). '">&nbsp;<input type="submit" class="submit" name="Submit" value="  '. t('Поиск'). '  "></form></p>';
		
		echo mso_hook('page_404');
	}
	
} // endif $pages

echo NR . '</div><!-- class="type type_search" -->' . NR;

# конечная часть шаблона
require(getinfo('template_dir') . 'main-end.php');
	
?>