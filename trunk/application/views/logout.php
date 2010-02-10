<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

function _mso_logout()
{
	$ci = & get_instance();
	$ci->session->sess_destroy();
	$url = (isset($_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER'] : '';
	
	// сразу же удаляем куку комюзера
	$comuser = mso_get_cookie('maxsite_comuser', false);
	
	if ($comuser) 
	{
		$name_cookies = 'maxsite_comuser';
		$expire  = time() - 2592100; // 30 дней = 2592000 секунд
		$value = ''; 
		mso_add_to_cookie($name_cookies, $value, $expire, true); // в куку для всего сайта
	}
	else mso_redirect($url, true);
}

_mso_logout();

?>