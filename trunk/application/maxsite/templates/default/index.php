<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

# каталог type - можно использовать дефолтный
$type_dir = getinfo('templates_dir') . 'default/type/';
//$type_dir = 'type/';

# глобальное кэширование выполняется на уровне хука при наличии соответствующего плагина
# если хук вернул true, значит данные выведены из кэша, то есть выходим
if (mso_hook('global_cache_start', false)) return;

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
	elseif ( is_type('author') ) 	require($type_dir . 'author.php');
	elseif ( is_type('users') )	
	{
		if (mso_segment(3)=='edit')	require($type_dir . 'users-form.php'); // редактирование комюзера
		elseif (mso_segment(3)=='lost') require($type_dir . 'users-form-lost.php');	// список всех комюзеров
		elseif (mso_segment(2)=='') require($type_dir . 'users-all.php');	// список всех комюзеров
		else require($type_dir . 'users.php');								// комюзер
	}
	elseif ( mso_segment(1)=='sitemap' ) require($type_dir . 'sitemap.php'); // карта сайта
	else
	{
		// ничего не найдено, пробуем проверить хук «custom_page_404»
		if ( !mso_hook_present('custom_page_404') or !mso_hook('custom_page_404')) 
			require($type_dir . 'page_404.php');	// 404 - если ничего так и не найдено
	}

mso_hook('global_cache_end');

?>