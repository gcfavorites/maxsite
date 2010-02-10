<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
	
	require(getinfo('template_dir') . 'main-start.php');
	
	echo  '<h1>Карта сайта (архив)</h1>';
	
	if ( function_exists('sitemap') ) echo sitemap();

	require(getinfo('template_dir') . 'main-end.php'); 

?>