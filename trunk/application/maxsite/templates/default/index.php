<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

// pr($MSO);

# глобальный кэш в каталоге html - должен быть создан и права на запись (777)!
if ( mso_get_option('global_cache', 'templates', false) ) // если разрешено в опциях шаблона
{
	//$cache_key = mso_md5($_SERVER['REQUEST_URI']);
	$cache_key = $_SERVER['REQUEST_URI'];
	$cache_key = str_replace('/', '-', $cache_key);
	$cache_key = mso_slug(' ' . $cache_key);
	$cache_key = 'html/' . $cache_key . '.html';

	if ( $k = mso_get_cache($cache_key, true) ) return print($k); // да есть в кэше
	ob_start();
}

if ( is_feed() )
{
	# для rss используются другие шаблоны
	if ( is_type('page') ) require('feed-page.php'); 					// только комментарии к странице
		elseif ( is_type('comments') ) require('feed-comments.php');	// все комментарии
	#	elseif ( is_type('archive') ) require('feed-archive.php'); 		// по датам - нафига???
		elseif ( is_type('category') ) require('feed-category.php'); 	// по рубрикам
	#	elseif ( is_type('tag') ) require('feed-tag.php'); 				// по меткам
		else require('feed-home.php'); // все записи					// все страницы
	
	exit; // выходим
}

# подключаем нужные библиотеки - они используются почти везде
require_once( getinfo('common_dir') . 'page.php' ); 			// функции страниц 
require_once( getinfo('common_dir') . 'category.php' ); 		// функции рубрик

# в зависимости от типа данных подключаем нужный файл
if ( is_type('archive') ) 			require('archive.php');		// архив по датам
	elseif ( is_type('home') ) 		require('home.php');		// главная
	elseif ( is_type('page') ) 		require('page.php');		// страницы 
	elseif ( is_type('comments') ) 	require('comments.php');	// все комментарии
	elseif ( is_type('loginform') )	require('loginform.php');	// форма логина
	elseif ( is_type('contact') ) 	require('contact.php');		// контактная форма
	elseif ( is_type('category') )	require('category.php');	// рубрики
	elseif ( is_type('search') )	require('search.php');		// поиск
	elseif ( is_type('tag') )		require('tag.php');			// метки
	# elseif ( is_type('author') ) 	require('author.php');
	# elseif ( is_type('link') )	require('link.php');
	elseif ( is_type('users') )	
	{
		if (mso_segment(3)=='edit')	require('users-form.php');
		else require('users.php');
	}
	else 							require('page_404.php');	// 404 - если не найдено

# глобальный кэш на 300 секунд = 5 минут
if ( mso_get_option('global_cache', 'templates', false) ) mso_add_cache($cache_key, ob_get_flush(), 300, true);

?>