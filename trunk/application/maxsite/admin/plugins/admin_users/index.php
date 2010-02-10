<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (с) http://maxsite.org/
 */


# функция автоподключения плагина
function admin_users_autoload($args = array())
{	
	mso_hook_add( 'admin_init', 'admin_users_admin_init');
}


# функция выполняется при указаном хуке admin_init
function admin_users_admin_init($args = array()) 
{


	$this_plugin_url = 'users'; // url и hook
	
	# добавляем свой пункт в меню админки
	# первый параметр - группа в меню
	# второй - это действие/адрес в url - http://сайт/admin/demo
	#			можно использовать добавочный, например demo/edit = http://сайт/admin/demo/edit
	# Третий - название ссылки	
	# Четвертый - номер в меню
	
	if ( mso_check_allow('admin_users_users') ) 
		mso_admin_menu_add('users', $this_plugin_url, 'Список пользователей', 1);

	# прописываем для указаного admin_url_ + $this_plugin_url - (он будет в url) 
	# связанную функцию именно она будет вызываться, когда 
	# будет идти обращение по адресу http://сайт/admin/admin_users
	mso_admin_url_hook ($this_plugin_url, 'admin_users_admin');
	
	if ( mso_check_allow('admin_users_group') ) 
	{
		$this_plugin_url = 'users_group'; // url и hook
		mso_admin_menu_add('users', $this_plugin_url, 'Группы и разрешения', 2);
		mso_admin_url_hook ($this_plugin_url, 'admin_users_group');	
	}

	$this_plugin_url = 'users_my_profile'; // url и hook
	mso_admin_menu_add('users', $this_plugin_url, 'Мой профиль', 3);
	mso_admin_url_hook ($this_plugin_url, 'admin_users_my_profile');	
	
	
//	$this_plugin_url = 'users_edit'; // url и hook
//	mso_admin_menu_add('users', $this_plugin_url, 'Редактировать пользователя', 3);
//	mso_admin_url_hook ($this_plugin_url, 'admin_users_edit');	
	
	
	return $args;
}

# функция вызываемая при хуке, указанном в mso_admin_url_hook
function admin_users_admin($args = array()) 
{
	# выносим админские функции отдельно в файл
	global $MSO;
	
	# если идет вызов с номером юзера, то подключаем страницу для редактирования
	
	
	// Определим текущую страницу (на основе сегмента url)
	// http://localhost/codeigniter/admin/users/edit/1
	$seg = mso_segment(3); // третий - edit

	mso_hook_add_dinamic( 'mso_admin_header', ' return $args . "Пользователи"; ' );
	mso_hook_add_dinamic( 'admin_title', ' return "Пользователи - " . $args; ' );

	// подключаем соответственно нужный файл
	if ($seg == '') require($MSO->config['admin_plugins_dir'] . 'admin_users/users.php');
		elseif ($seg == 'edit') require($MSO->config['admin_plugins_dir'] . 'admin_users/edit.php');
	
}


function admin_users_group($args = array()) 
{
	# выносим админские функции отдельно в файл
	global $MSO;
	
	if ( !mso_check_allow('admin_users_group') ) 
	{
		echo 'Доступ запрещен';
		return $args;
	}
	
	mso_hook_add_dinamic( 'mso_admin_header', ' return $args . "Настройка групп пользователей"; ' );
	mso_hook_add_dinamic( 'admin_title', ' return "Настройка групп пользователей - " . $args; ' );
	
	require($MSO->config['admin_plugins_dir'] . 'admin_users/group.php');
}



function admin_users_my_profile($args = array()) 
{
	# выносим админские функции отдельно в файл
	global $MSO;
	
	mso_hook_add_dinamic( 'mso_admin_header', ' return $args . "Настройка своего профиля"; ' );
	mso_hook_add_dinamic( 'admin_title', ' return "Настройка своего профиля - " . $args; ' );
	
	require($MSO->config['admin_plugins_dir'] . 'admin_users/my_profile.php');
}


?>