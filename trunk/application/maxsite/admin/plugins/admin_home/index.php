<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (с) http://maxsite.org/
 */


# функция автоподключения плагина
function admin_home_autoload($args = array())
{	
	mso_hook_add( 'admin_init', 'admin_home_admin_init');
}


# функция выполняется при указаном хуке admin_init
function admin_home_admin_init($args = array()) 
{


	$this_plugin_url = 'home'; // url и hook
	
	# добавляем свой пункт в меню админки
	# первый параметр - группа в меню
	# второй - это действие/адрес в url - http://сайт/admin/demo
	#			можно использовать добавочный, например demo/edit = http://сайт/admin/demo/edit
	# Третий - название ссылки	
	# Четвертый - номер в меню
	
	mso_admin_menu_add('', $this_plugin_url, 'Информация');

	# прописываем для указаного admin_url_ + $this_plugin_url - (он будет в url) 
	# связанную функцию именно она будет вызываться, когда 
	# будет идти обращение по адресу http://сайт/admin/admin_home
	mso_admin_url_hook ($this_plugin_url, 'admin_home_admin');
	
	
	mso_admin_menu_add('', 'go_site', 'Переход к сайту');
	mso_admin_url_hook ('go_site', 'admin_home_go_site');
	
	return $args;
}

# функция вызываемая при хуке, указанном в mso_admin_url_hook
function admin_home_admin($args = array()) 
{
	# выносим админские функции отдельно в файл
	global $MSO;
	
	mso_hook_add_dinamic( 'mso_admin_header', ' return $args . "Админ-панель"; ' );
	mso_hook_add_dinamic( 'admin_title', ' return "Админ-панель - " . $args; ' );
	
	require($MSO->config['admin_plugins_dir'] . 'admin_home/admin.php');
}

# редирект к сайту
function admin_home_go_site($args = array()) 
{
	mso_redirect('');
}

?>