<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

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
	if ( is_type('page') ) require('type/feed-page.php'); 					// только комментарии к странице
		elseif ( is_type('comments') ) require('type/feed-comments.php');	// все комментарии
		elseif ( is_type('category') ) require('type/feed-category.php'); 	// по рубрикам
		else require('type/feed-home.php'); // все записи					// все страницы
	
	exit; // выходим
}

# подключаем нужные библиотеки - они используются почти везде
require_once( getinfo('common_dir') . 'page.php' ); 			// функции страниц 
require_once( getinfo('common_dir') . 'category.php' ); 		// функции рубрик

# в зависимости от типа данных подключаем нужный файл
if ( is_type('archive') ) 			require('type/archive.php');	// архив по датам
	elseif ( is_type('home') ) 		require('type/home.php');		// главная
	elseif ( is_type('page') ) 		require('type/page.php');		// страницы 
	elseif ( is_type('comments') ) 	require('type/comments.php');	// все комментарии
	elseif ( is_type('loginform') )	require('type/loginform.php');	// форма логина
	elseif ( is_type('contact') ) 	require('type/contact.php');	// контактная форма
	elseif ( is_type('category') )	require('type/category.php');	// рубрики
	elseif ( is_type('search') )	require('type/search.php');		// поиск
	elseif ( is_type('tag') )		require('type/tag.php');		// метки
	# elseif ( is_type('author') ) 	require('type/author.php');
	elseif ( is_type('users') )	
	{
		if (mso_segment(3)=='edit')	require('type/users-form.php'); // редактирование комюзера
		elseif (mso_segment(2)=='') require('type/users-all.php');	// список всех комюзеров
		else require('type/users.php');								// комюзер
	}
	elseif ( mso_segment(1)=='sitemap' ) require('type/sitemap.php'); // карта сайта
	else 							require('type/page_404.php');	// 404 - если ничего так и не найдено


# глобальный кэш на 300 секунд = 5 минут
if ( mso_get_option('global_cache', 'templates', false) ) mso_add_cache($cache_key, ob_get_flush(), 300, true);

?>