<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */


# функция автоподключения плагина
function redirect_autoload($args = array())
{
	mso_create_allow('redirect_edit', t('Админ-доступ к плагину редиректов', 'plugins'));
	mso_hook_add( 'admin_init', 'redirect_admin_init'); # хук на админку
	mso_hook_add( 'init', 'redirect_init'); # хук на init
}


# функция выполняется при деинстяляции плагина
function redirect_uninstall($args = array())
{	
	mso_delete_option('redirect', 'plugins'); // удалим созданные опции
	return $args;
}

# цепляемся к хуку init
function redirect_init($args = array()) 
{
	// получаем опции
	// в опциях all - строки с редиректами
	// загоняем их в массив
	// смотрим текущий url 
	// если он есть в редиректах, то редиректимся
	
	$options = mso_get_option('redirect', 'plugins', array());
	if ( !isset($options['all']) ) return $args; // нет опций
	
	$all = explode("\n", $options['all']); // разобъем по строкам
	
	if (!$all) return $args; // пустой массив
	
	// текущий адрес
	$current_url = mso_current_url(true);

	foreach ($all as $row) // перебираем каждую строчку
	{
		$urls = explode('|', $row); //  адрес | редирект
		if ( isset($urls[0]) and isset($urls[1]) and trim($urls[0]) and trim($urls[1]) ) // если есть урлы
		{
			if ( $current_url != trim(trim($urls[0])) ) 
				continue; // адреса разные
			else // совпали, делаем редирект
				mso_redirect(trim($urls[1]), true);
		}
	}

	return $args;
}

# функция выполняется при хуке admin_init
function redirect_admin_init($args = array()) 
{
	if ( !mso_check_allow('redirect_edit') ) 
	{
		return $args;
	}
	
	$this_plugin_url = 'redirect'; // url и hook
	
	# добавляем свой пункт в меню админки
	# первый параметр - группа в меню
	# второй - это действие/адрес в url - http://сайт/admin/demo
	#			можно использовать добавочный, например demo/edit = http://сайт/admin/demo/edit
	# Третий - название ссылки	
	
	mso_admin_menu_add('plugins', $this_plugin_url, t('Редиректы', 'plugins'));

	# прописываем для указаного admin_url_ + $this_plugin_url - (он будет в url) 
	# связанную функцию именно она будет вызываться, когда 
	# будет идти обращение по адресу http://сайт/admin/redirect
	mso_admin_url_hook ($this_plugin_url, 'redirect_admin_page');
	
	return $args;
}

# функция вызываемая при хуке, указанном в mso_admin_url_hook
function redirect_admin_page($args = array()) 
{
	# выносим админские функции отдельно в файл
	if ( !mso_check_allow('redirect_edit') ) 
	{
		echo t('Доступ запрещен', 'plugins');
		return $args;
	}
	
	mso_hook_add_dinamic( 'mso_admin_header', ' return $args . "' . t('Редиректы', __FILE__) . '"; ' );
	mso_hook_add_dinamic( 'admin_title', ' return "' . t('Редиректы', __FILE__) . ' - " . $args; ' );
	
	require(getinfo('plugins_dir') . 'redirect/admin.php');
}

?>