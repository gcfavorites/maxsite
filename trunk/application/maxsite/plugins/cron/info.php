<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

$info = array(
	'name' => t('Cron', __FILE__),
	'description' => t('Выполнение периодических задач по крону. Для работы необходимо включить на сервере CRON: «GET http://сайт/cron»', __FILE__),
	'version' => '1.0',
	'author' => 'Максим',
	'plugin_url' => 'http://max-3000.com/',
	'author_url' => 'http://maxsite.org/',
	'group' => 'admin'
);

?>