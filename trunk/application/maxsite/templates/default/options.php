<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<h1>Настройка шаблона</h1>
<p class="info">Выберите необходимые опции</p>

<?php

	// функции для работы с ini-файлом
	require_once( getinfo('common_dir') . 'inifile.php' );
	
	// проверка на обновление POST
	if (mso_check_post_ini()) echo '<div class="update">Обновлено!</div>';
	
	// получим ini-файл
	$all = mso_get_ini_file( getinfo('template_dir') . 'options.ini');
	
	// вывод всех ini-опций
	echo mso_view_ini($all);

?>