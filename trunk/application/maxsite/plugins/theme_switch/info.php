<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

$info = array(
	'name' => t('Theme switch', 'plugins'),
	'description' => t('Переключение тем оформления сайта посетителями', 'plugins'),
	'version' => '1.1',
	'author' => 'Максим',
	'plugin_url' => 'http://max-3000.com/',
	'author_url' => 'http://maxsite.org/',
	'group' => 'template',
	'options_url' => getinfo('site_admin_url') . 'theme_switch',
);

# end file