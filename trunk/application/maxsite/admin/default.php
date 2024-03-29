<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

# здесь подключаются дефолтные хуки админки


function mso_admin_menu_default($args = array())
{
	# группа - адрес - название ссылка - порядок в своей группе
	# вначале нужно добавить все главные меню в той последовательности, которая нужна

	mso_admin_menu_add('', '', t('Начало', 'admin') );
	mso_admin_menu_add('page', '', t('Страницы', 'admin'));
	mso_admin_menu_add('options', '', t('Настройки', 'admin'));
	mso_admin_menu_add('users', '', t('Пользователи', 'admin'));
	mso_admin_menu_add('plugins', '', t('Плагины', 'admin'));
	
	$out = t('Меню не определено', 'admin');
	
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
		else return t('Добро пожаловать в MaxSite CMS!', 'admin');
}

function mso_admin_footer_default($args = '')
{
	global $MSO;
	
	$CI = & get_instance(); 
	$query_count = $CI->db->query_count;
	$ver = $MSO->version;
	$out = '<p>' . t('Страница создавалась {elapsed_time} секунд. Потребление памяти: {memory_usage}. Запросов MySQL:', 'admin') . ' '
	. $query_count . '. '
	. t('Работает на <a href="http://max-3000.com/" style="color: white;">MaxSite CMS</a>.', 'admin'). ' ' 
	. t('Версия') . ' '
	. $ver . ' [<a href="' . $MSO->config['site_url'] . 'logout'.'">' . t('выйти', 'admin') . '</a>]</p>';
	
	return $out;
}

function mso_admin_plugins_default($args = array())
{
	// все плагины в admin подключаются автоматом
	
	$CI = & get_instance();
	$CI->load->helper('directory'); 
	$plugins_dir = getinfo('admin_plugins_dir'); // получаем список каталогов в admin/plugins
	$dirs = directory_map($plugins_dir, true);	 // все каталоги в массиве $dirs
	
	foreach ($dirs as $dir)
	{
		if (is_dir($plugins_dir . $dir)) mso_admin_plugin_load($dir); // если это каталог
	}
	
	# кастомная функция, если есть
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

mso_create_allow('edit_users_group', t('Разрешить изменять группу другим участникам', 'admin'));
mso_create_allow('edit_users_admin_note', t('Разрешить изменять примечание админа', 'admin'));
mso_create_allow('edit_other_users', t('Разрешить изменять анкетные данные других участников', 'admin'));
mso_create_allow('edit_self_users', t('Разрешить изменять свои анкетные данные', 'admin'));
mso_create_allow('edit_users_password', t('Разрешить изменять пароль других участников', 'admin'));
mso_create_allow('edit_add_new_users', t('Разрешить добавлять новых пользователей', 'admin'));
mso_create_allow('edit_delete_users', t('Разрешить удалять пользователей', 'admin'));
mso_create_allow('edit_page_author', t('Разрешить менять автора', 'admin'));

mso_create_allow('admin_users_group', t('Админ-доступ к «Группам и разрешениям»', 'admin'));
mso_create_allow('admin_users_users', t('Админ-доступ к «Список пользователей»', 'admin'));
mso_create_allow('admin_plugins', t('Админ-доступ к «Плагинам»', 'admin'));
mso_create_allow('admin_cat', t('Админ-доступ к «Рубрики»', 'admin'));
mso_create_allow('admin_options', t('Админ-доступ к «Настройки»', 'admin'));

mso_create_allow('admin_page', t('Админ-доступ к «Страницы-список»', 'admin'));
mso_create_allow('admin_page_new', t('Админ-доступ к «Создание страниц»', 'admin'));
mso_create_allow('admin_page_publish', t('Разрешить сразу публиковать записи. Иначе только как черновик', 'admin'));
mso_create_allow('admin_page_delete', t('Разрешить удалять страницы', 'admin'));
mso_create_allow('admin_page_edit', t('Админ-доступ к «Редактирование страниц»', 'admin'));
mso_create_allow('admin_page_edit_other', t('Админ-доступ к редактированию чужих страниц', 'admin'));

mso_create_allow('admin_sidebars', t('Админ-доступ к настройкам сайдбаров', 'admin'));

mso_create_allow('admin_comments', t('Админ-доступ к просмотру комментариев', 'admin'));
mso_create_allow('admin_comments_edit', t('Админ-доступ к редактированию комментариев', 'admin'));


mso_create_allow('admin_comusers', t('Админ-доступ к «Комментаторам»', 'admin'));

mso_create_allow('admin_home', t('Разрешить доступ (выборочно) к «Информация»', 'admin'));

?>