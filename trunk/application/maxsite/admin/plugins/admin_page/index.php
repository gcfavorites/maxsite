<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */


# функция автоподключения плагина
function admin_page_autoload($args = array())
{	
	mso_hook_add( 'admin_init', 'admin_page_admin_init');
}

# функция выполняется при указаном хуке admin_init
function admin_page_admin_init($args = array()) 
{

	if ( mso_check_allow('admin_page') ) 
	{
		$this_plugin_url = 'page'; // url и hook
		
		# добавляем свой пункт в меню админки
		# первый параметр - группа в меню
		# второй - это действие/адрес в url - http://сайт/admin/demo
		#			можно использовать добавочный, например demo/edit = http://сайт/admin/demo/edit
		# Третий - название ссылки	
		# четвертый номер по порядку
		
		mso_admin_menu_add('page', $this_plugin_url, t('Список', __FILE__), 2);

		# прописываем для указаного admin_url_ + $this_plugin_url - (он будет в url) 
		# связанную функцию именно она будет вызываться, когда 
		# будет идти обращение по адресу http://сайт/admin/_null
		mso_admin_url_hook ($this_plugin_url, 'admin_page_admin');
	}
	
	if ( mso_check_allow('admin_page_new') ) 
	{
		$this_plugin_url = 'page_edit'; // url и hook
		//mso_admin_menu_add('page', $this_plugin_url, 'Редактировать запись', 2);
		mso_admin_url_hook ($this_plugin_url, 'admin_page_edit');
		
		$this_plugin_url = 'page_new'; // url и hook
		mso_admin_menu_add('page', $this_plugin_url, t('Создать', __FILE__), 1);
		mso_admin_url_hook ($this_plugin_url, 'admin_page_new');	
	}
	
	return $args;
}

# функция вызываемая при хуке, указанном в mso_admin_url_hook
function admin_page_admin($args = array()) 
{
	# выносим админские функции отдельно в файл
	global $MSO;
	if ( !mso_check_allow('admin_page') ) 
	{
		echo t('Доступ запрещен');
		return $args;
	}
	
	mso_hook_add_dinamic( 'mso_admin_header', ' return $args . "' . t('Список страниц', 'admin') . '"; ' );
	mso_hook_add_dinamic( 'admin_title', ' return "' . t('Список страниц', 'admin') . ' - " . $args; ' );
	
	require($MSO->config['admin_plugins_dir'] . 'admin_page/admin.php');
}


# функция вызываемая при хуке, указанном в mso_admin_url_hook
function admin_page_edit($args = array()) 
{
	# выносим админские функции отдельно в файл
	global $MSO;
	
	if ( !mso_check_allow('admin_page_edit') ) 
	{
		echo t('Доступ запрещен');
		return $args;
	}
	
	mso_hook_add_dinamic( 'mso_admin_header', ' return $args . "' . t('Редактирование страницы', 'admin') . '"; ' );
	mso_hook_add_dinamic( 'admin_title', ' return "' . t('Редактирование страницы', 'admin') . ' - " . $args; ' );
	
	require($MSO->config['admin_plugins_dir'] . 'admin_page/edit.php');
}


# функция вызываемая при хуке, указанном в mso_admin_url_hook
function admin_page_new($args = array()) 
{
	# выносим админские функции отдельно в файл
	global $MSO;
	
	if ( !mso_check_allow('admin_page_new') ) 
	{
		echo t('Доступ запрещен');
		return $args;
	}
	
	mso_hook_add_dinamic( 'mso_admin_header', ' return $args . "' . t('Создать страницу', 'admin') . '"; ' );
	mso_hook_add_dinamic( 'admin_title', ' return "' . t('Создать страницу', 'admin') . ' - " . $args; ' );
	
	require($MSO->config['admin_plugins_dir'] . 'admin_page/new.php');
}



?>