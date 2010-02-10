<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MaxSite CMS
 * (с) http://maxsite.org/
 */

# здесь подключаются дефолтные хуки админки


function mso_admin_menu_default($args = array())
{
	# группа - адрес - название ссылка - порядок в своей группе
	# вначале нужно добавить все главные меню в той последовательности, которая нужна

	mso_admin_menu_add('', '', 'Начало');

	mso_admin_menu_add('page', '', 'Страницы');
	mso_admin_menu_add('options', '', 'Настройки');
	mso_admin_menu_add('users', '', 'Пользователи');
	mso_admin_menu_add('plugins', '', 'Плагины');
	
	$out = 'Меню не определено';
	
	return $out;
}


function mso_admin_header_default($args = array())
{
	//$out = '<h1>' . mso_get_option('name_site', 'general') . '</h1>';
	//return $out;
}

function mso_admin_content_default($args = '')
{
	# связываем дефолтное содержимое с плагином admin_home
	if (function_exists('admin_home_admin')) admin_home_admin();
		else return 'Добро пожаловать в MaxSite CMS!';
}

function mso_admin_footer_default($args = '')
{
	global $MSO;
	
	$CI = & get_instance(); 
	$query_count = $CI->db->query_count;
	$ver = $MSO->version;
	$out ='
	<p>Страница создавалась {elapsed_time} секунд. Потребление памяти: {memory_usage}. Запросов MySQL: '
	. $query_count
	. '. Работает на <a href="http://max-3000.com/" style="color: white;">MaxSite CMS</a>. Версия '
	. $ver . ' [<a href="' . $MSO->config['site_url'] . 'logout'.'">выйти</a>]</p>';
	
	return $out;
}

function mso_admin_plugins_default($args = array())
{
	mso_admin_plugin_load('admin_page');
	mso_admin_plugin_load('admin_home');
	mso_admin_plugin_load('admin_cat');
	// mso_admin_plugin_load('admin_link');
	mso_admin_plugin_load('admin_options');
	mso_admin_plugin_load('admin_plugins');
	mso_admin_plugin_load('admin_users');
	mso_admin_plugin_load('admin_menu');
	mso_admin_plugin_load('admin_sidebars');
	
	mso_admin_plugin_load('admin_files');
	
	mso_admin_plugin_load('admin_comments');
	
	// mso_admin_plugin_load('editor_freert');
	// mso_admin_plugin_load('editor_wymeditor');
	
	mso_admin_plugin_load('editor_jw');
	
	mso_admin_plugin_load('template_options');
	
	
	# кустомная функция, если есть
	if (function_exists('mso_autoload_admin_custom')) mso_autoload_admin_custom();
	
	return $args;
}

# дефолтные хуки
mso_hook_add('admin_header_default', 'mso_admin_header_default');
mso_hook_add('admin_menu_default', 'mso_admin_menu_default');
mso_hook_add('admin_content_default', 'mso_admin_content_default');
mso_hook_add('admin_footer_default', 'mso_admin_footer_default');


# дефолтные разрешения
# их можно указать в самих плагинах, но это дефолтные, поэтому указываем здесь
# для удобства

mso_create_allow('edit_users_group', 'Разрешить изменять группу другим участникам');
mso_create_allow('edit_users_admin_note', 'Разрешить изменять примечание админа');
mso_create_allow('edit_other_users', 'Разрешить изменять анкетные данные других участников');
mso_create_allow('edit_self_users', 'Разрешить изменять свои анкетные данные');
mso_create_allow('edit_users_password', 'Разрешить изменять пароль других участников');
mso_create_allow('edit_add_new_users', 'Разрешить добавлять новых пользователей');
mso_create_allow('edit_page_author', 'Разрешить менять автора');

mso_create_allow('admin_users_group', 'Админ-доступ к «Группам и разрешениям»');
mso_create_allow('admin_users_users', 'Админ-доступ к «Список пользователей»');
mso_create_allow('admin_plugins', 'Админ-доступ к «Плагинам»');
mso_create_allow('admin_cat', 'Админ-доступ к «Рубрики»');
mso_create_allow('admin_options', 'Админ-доступ к «Настройки»');

mso_create_allow('admin_page', 'Админ-доступ к «Страницы-список»');
mso_create_allow('admin_page_new', 'Админ-доступ к «Создание страниц»');
mso_create_allow('admin_page_edit', 'Админ-доступ к «Редактирование страниц»');

mso_create_allow('admin_sidebars', 'Админ-доступ к настройкам сайдбаров');

mso_create_allow('admin_comments', 'Админ-доступ к просмотру комментариев');
mso_create_allow('admin_comments_edit', 'Админ-доступ к редактированию комментариев');



?>