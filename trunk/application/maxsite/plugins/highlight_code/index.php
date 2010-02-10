<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (с) http://maxsite.org/
 */

# функция автоподключения плагина
function highlight_code_autoload($args = array())
{	
	mso_hook_add( 'content', 'highlight_code_content'); # хук 
}


# функция выполняется при удалении плагина
function highlight_code_content($content = '')
{	
	$CI = & get_instance();	
	$CI->load->helper('text');
	
	$content = highlight_code($content);

	return $content;
}

?>