<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

# каталог type - можно использовать дефолтный
$type_dir = getinfo('templates_dir') . 'default/type/';
// $type_dir = 'type/'; // или свой


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
	if ( is_type('page') ) require($type_dir . 'feed-page.php'); 					// только комментарии к странице
		elseif ( is_type('comments') ) require($type_dir . 'feed-comments.php');	// все комментарии
		elseif ( is_type('category') ) require($type_dir . 'feed-category.php'); 	// по рубрикам
		else require($type_dir . 'feed-home.php'); // все записи					// все страницы
	
	exit; // выходим
}

# подключаем нужные библиотеки - они используются почти везде
require_once( getinfo('common_dir') . 'page.php' ); 			// функции страниц 
require_once( getinfo('common_dir') . 'category.php' ); 		// функции рубрик

# в зависимости от типа данных подключаем нужный файл
if ( is_type('archive') ) 			require($type_dir . 'archive.php');	// архив по датам
	elseif ( is_type('home') ) 		require($type_dir . 'home.php');		// главная
	elseif ( is_type('page') ) 		require($type_dir . 'page.php');		// страницы 
	elseif ( is_type('comments') ) 	require($type_dir . 'comments.php');	// все комментарии
	elseif ( is_type('loginform') )	require($type_dir . 'loginform.php');	// форма логина
	elseif ( is_type('contact') ) 	require($type_dir . 'contact.php');	// контактная форма
	elseif ( is_type('category') )	require($type_dir . 'category.php');	// рубрики
	elseif ( is_type('search') )	require($type_dir . 'search.php');		// поиск
	elseif ( is_type('tag') )		require($type_dir . 'tag.php');		// метки
	# elseif ( is_type('author') ) 	require($type_dir . 'type/author.php');
	elseif ( is_type('users') )	
	{
		if (mso_segment(3)=='edit')	require($type_dir . 'users-form.php'); // редактирование комюзера
		elseif (mso_segment(2)=='') require($type_dir . 'users-all.php');	// список всех комюзеров
		else require($type_dir . 'users.php');								// комюзер
	}
	elseif ( mso_segment(1)=='sitemap' ) require($type_dir . 'sitemap.php'); // карта сайта
	else 							require($type_dir . 'page_404.php');	// 404 - если ничего так и не найдено


# глобальный кэш на 300 секунд = 5 минут
if ( mso_get_option('global_cache', 'templates', false) ) mso_add_cache($cache_key, ob_get_flush(), 300, true);

?>