<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (с) http://maxsite.org/
 */

### каркас для плагина
### вместо _null укажите свой плагин


# функция автоподключения плагина
function _null_autoload($args = array())
{	
	mso_hook_add( 'admin_init', '_null_admin_init');
}

# функция выполняется при активации (вкл) плагина
function _null_activate($args = array())
{	
	return $args;
}

# функция выполняется при деактивации (выкл) плагина
function _null_deactivate($args = array())
{	
	return $args;
}

# функция выполняется при удалении плагина
function _null_uninstall($args = array())
{	
	return $args;
}

# функция выполняется при указаном хуке admin_init
function _null_admin_init($args = array()) 
{


	$this_plugin_url = '_null'; // url и hook
	
	# добавляем свой пункт в меню админки
	# первый параметр - группа в меню
	# второй - это действие/адрес в url - http://сайт/admin/demo
	#			можно использовать добавочный, например demo/edit = http://сайт/admin/demo/edit
	# Третий - название ссылки	
	# Четвертый - номер в меню
	
	mso_admin_menu_add('plugins', $this_plugin_url, '_null');

	# прописываем для указаного admin_url_ + $this_plugin_url - (он будет в url) 
	# связанную функцию именно она будет вызываться, когда 
	# будет идти обращение по адресу http://сайт/admin/_null
	mso_admin_url_hook ($this_plugin_url, '_null_admin');
	
	return $args;
}

# функция вызываемая при хуке, указанном в mso_admin_url_hook
function _null_admin($args = array()) 
{
	# выносим админские функции отдельно в файл
	global $MSO;
	require($MSO->config['admin_plugins_dir'] . '_null/admin.php');
}
?>