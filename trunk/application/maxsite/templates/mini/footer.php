<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<div id="footer">
<?php
	$site_name = getinfo('name_site');
	$date = date('Y');
	$CI = & get_instance();	
	
	$mq = $CI->db->query_count;
	
	echo '
	&copy; ' . $site_name . ', ' . $date 
	. '<br>' 
	. sprintf( 
		t('Работает на <a href="http://max-3000.com/">MaxSite CMS</a> | Время: {elapsed_time} | SQL: %s | Память: {memory_usage}', 'templates')
		, $mq);

	if (is_login())
		echo ' | <a href="' . getinfo('siteurl') . 'admin">' . t('Управление', 'templates') 
				. '</a> | <a href="' . getinfo('siteurl') . 'logout'.'">' . t('Выйти', 'templates') . '</a>';
	else
		echo ' | <a href="' . getinfo('siteurl') . 'login">' . t('Вход', 'templates') . '</a>';

?>
</div>