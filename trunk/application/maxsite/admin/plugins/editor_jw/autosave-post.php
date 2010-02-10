<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

	mso_checkreferer(); // защищаем реферер
	
	if ( $post = mso_check_post(array('text')) )
	{
		$text = $post['text'];
		$fn = mso_add_float_option('autosave', $text, '', false, '.txt', false); // и в float-опции без серилизации
		
		echo getinfo('uploads_url') . $fn;
	}
	
?>