<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MaxSite CMS
 * (c) http://maxsite.org/
 */

function _mso_logout()
{
	$ci = & get_instance();
	$ci->session->sess_destroy();
	$url = (isset($_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER'] : '';
	mso_redirect($url, true);
}

_mso_logout();

?>