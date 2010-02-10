<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

	mso_cur_dir_lang('templates');
	
	require(getinfo('template_dir') . 'main-start.php');
	
	echo  '<h1>'. t('Карта сайта (архив)').'</h1>';
	
	if ( function_exists('sitemap') ) echo sitemap();

	require(getinfo('template_dir') . 'main-end.php'); 

?>