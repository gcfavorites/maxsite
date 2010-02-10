<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MaxSite CMS
 * (c) http://maxsite.org/
 */
 
global $MSO;

if ( isset($MSO->data['uri_segment'][2]) )
{
	if (!isset($_SERVER['HTTP_X_REQUESTED_WITH'])) die('AJAX Error');
	$fn = $MSO->config['base_dir'] . base64_decode($MSO->data['uri_segment'][2]);
	
	if (file_exists($fn)) require($fn);
	else die('Error AJAX (no file)');
}
else die('Error AJAX');

?>