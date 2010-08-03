<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

mso_cur_dir_lang('admin');

?>

<h1><?= t('Основные настройки') ?></h1>
<p class="info"><?= t('Здесь вы можете указать основные настройки. Если указанная настройка отмечена «нет в базе», значит нужно ввести её значение и нажать кнопку «Сохранить».') ?></p>

<?php

	function _time_zone_current_time()
	{
		return '<br>' . t('Сейчас на сервере:') . date(' Y-m-d H:i:s');
	}



	$CI = & get_instance();
	require_once( getinfo('common_dir') . 'inifile.php' ); // функции для работы с ini-файлом
	
	// проверяем входящие данные
	if (mso_check_post_ini()) 
	{
		mso_redirect('admin/options');
	}
	
	$all = mso_get_ini_file( $MSO->config['admin_plugins_dir'] . 'admin_options/general.ini');
	echo mso_view_ini($all); // вывод таблицы ini 

?>