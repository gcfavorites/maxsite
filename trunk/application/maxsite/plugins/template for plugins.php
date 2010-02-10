<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

%%% - замените на имя плагина


# функция автоподключения плагина
function %%%_autoload()
{

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
	// mso_remove_allow('%%%_edit'); // удалим созданные разрешения
	return $args;
}

# функции плагина
function %%%_custom($arg = array())
{

	
}

?>