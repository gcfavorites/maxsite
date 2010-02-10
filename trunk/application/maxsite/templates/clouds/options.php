<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<h1><?= t('Настройка шаблона', 'templates') ?></h1>
<p class="info"><?= t('Выберите необходимые опции', 'templates') ?></p>

<?php

	// функции для работы с ini-файлом
	require_once( getinfo('common_dir') . 'inifile.php' );
	
	// проверка на обновление POST
	if (mso_check_post_ini()) echo '<div class="update">' . t('Обновлено!', 'templates') . '</div>';
	
	// получим ini-файл
	$all = mso_get_ini_file( getinfo('templates_dir') . 'default/options.ini'); // можно использовать дефолтный
	
	if (file_exists(getinfo('template_dir') . 'options.ini'))
	{
		$all_add = mso_get_ini_file( getinfo('template_dir') . 'options.ini'); // и свой
		$all = array_merge($all, $all_add);
	}
	
	// вывод всех ini-опций
	echo mso_view_ini($all);

?>