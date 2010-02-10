<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<div id="footer">
<?php
	$site_name = getinfo('name_site');
	$date = date('Y');
	$ver = getinfo('version');
	$CI = & get_instance();	
	$mq = $CI->db->query_count;
	
	echo <<<EOF
	&copy; {$site_name}, {$date} | Работает на <a href="http://maxsite.org/">MaxSite CMS</a> {$ver} | Время: {elapsed_time} секунд | MySQL: {$mq} | Память: {memory_usage}
EOF;

	if (is_login())
		echo ' | <a href="' . getinfo('siteurl') . 'admin">Управление</a> | <a href="' . getinfo('siteurl') . 'logout'.'">Выйти</a>';
	else
		echo ' | <a href="' . getinfo('siteurl') . 'login">Вход</a>';

?>
</div>