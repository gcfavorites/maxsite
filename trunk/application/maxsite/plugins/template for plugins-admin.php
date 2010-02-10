<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (с) http://maxsite.org/
 */

%%% - замените на имя плагина


# функция автоподключения плагина
function %%%_autoload($args = array())
{
	mso_create_allow('%%%_edit', 'Админ-доступ к %%%');
	mso_hook_add( 'admin_init', '%%%_admin_init'); # хук на админку
}

# функция выполняется при активации (вкл) плагина
function %%%_activate($args = array())
{	
	return $args;
}

# функция выполняется при деактивации (выкл) плагина
function %%%_deactivate($args = array())
{	
	// mso_delete_option('plugin_%%%', 'plugins'); // удалим созданные опции
	return $args;
}

# функция выполняется при деинстяляции плагина
function %%%_uninstall($args = array())
{	
	// mso_delete_option('plugin_%%%', 'plugins'); // удалим созданные опции
	return $args;
}

# функция выполняется при указаном хуке admin_init
function %%%_admin_init($args = array()) 
{
	if ( !mso_check_allow('%%%_edit') ) 
	{
		return $args;
	}
	
	$this_plugin_url = 'plugin_%%%'; // url и hook
	
	# добавляем свой пункт в меню админки
	# первый параметр - группа в меню
	# второй - это действие/адрес в url - http://сайт/admin/demo
	#			можно использовать добавочный, например demo/edit = http://сайт/admin/demo/edit
	# Третий - название ссылки	
	
	mso_admin_menu_add('plugins', $this_plugin_url, '%%%');

	# прописываем для указаного admin_url_ + $this_plugin_url - (он будет в url) 
	# связанную функцию именно она будет вызываться, когда 
	# будет идти обращение по адресу http://сайт/admin/_null
	mso_admin_url_hook ($this_plugin_url, '%%%_admin_page');
	
	return $args;
}

# функция вызываемая при хуке, указанном в mso_admin_url_hook
function %%%_admin_page($args = array()) 
{
	# выносим админские функции отдельно в файл
	global $MSO;
	if ( !mso_check_allow('%%%_admin_page') ) 
	{
		echo 'Доступ запрещен';
		return $args;
	}
	
	mso_hook_add_dinamic( 'mso_admin_header', ' return $args . "Admin "; ' );
	mso_hook_add_dinamic( 'admin_title', ' return "Admin - " . $args; ' );

	require($MSO->config['plugins_dir'] . '%%%/admin.php');
}


# функции плагина
function %%%_custom($arg = array(), $num = 1)
{

	
}



?>