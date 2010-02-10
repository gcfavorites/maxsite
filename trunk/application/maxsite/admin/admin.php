<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MaxSite CMS
 * (с) http://maxsite.org/
 */
 
global $MSO;

$dir_admin = $MSO->config['admin_dir'];


if (!is_login())
{
	require($dir_admin . 'template/loginform.php');
}
else
{
	require($dir_admin . 'common.php'); # админские функции
	require($dir_admin . 'default.php'); # дефолтные хуки и значения
	
	mso_admin_init(); # инициализация
	
	# подключаем шаблон админки
	require($dir_admin . 'template/template.php');
}

?>