<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://maxsite.org/
 */


# функция автоподключения плагина
function admin_files_autoload($args = array())
{	
	mso_create_allow('admin_files', 'Админ-доступ к файлам (загрузка, просмотр)');
	mso_hook_add( 'admin_init', 'admin_files_admin_init');
}


# функция выполняется при указаном хуке admin_init
function admin_files_admin_init($args = array()) 
{
	if ( !mso_check_allow('admin_files') ) 
	{
		return $args;
	}

	$this_plugin_url = 'files'; // url и hook
	
	# добавляем свой пункт в меню админки
	# первый параметр - группа в меню
	# второй - это действие/адрес в url - http://сайт/admin/demo
	#			можно использовать добавочный, например demo/edit = http://сайт/admin/demo/edit
	# Третий - название ссылки	
	# Четвертый - номер в меню
	
	mso_admin_menu_add('options', $this_plugin_url, 'Загрузки', 10);

	# прописываем для указаного admin_url_ + $this_plugin_url - (он будет в url) 
	# связанную функцию именно она будет вызываться, когда 
	# будет идти обращение по адресу http://сайт/admin/_null
	mso_admin_url_hook ($this_plugin_url, 'admin_files_admin');
	
	return $args;
}

# функция вызываемая при хуке, указанном в mso_admin_url_hook
function admin_files_admin($args = array()) 
{
	if ( !mso_check_allow('admin_files') ) 
	{
		echo 'Доступ запрещен';
		return $args;
	}
	
	# выносим админские функции отдельно в файл
	global $MSO;
	
	mso_hook_add_dinamic( 'mso_admin_header', ' return $args . "Файлы"; ' );
	mso_hook_add_dinamic( 'admin_title', ' return "Файлы - " . $args; ' );
	
	require($MSO->config['admin_plugins_dir'] . 'admin_files/admin.php');
}
?>