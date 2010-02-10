<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

	# файл functions.php подключается при инициализации сайта
	# в этом файле нельзя выводить данные в браузер!
	
	# регистрируем сайдбары - имя, заголовок.
	# если имя совпадает, то берется последний заголовок
	mso_register_sidebar('1', 'Первый сайдбар');
	mso_register_sidebar('2', 'Второй сайдбар');
	mso_register_sidebar('3', 'Верхний блок');

?>