<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

global $MSO;

if ( isset($MSO->data['uri_segment'][2]) )
{
	if (!isset($_SERVER['HTTP_X_REQUESTED_WITH'])) die();
	
	mso_checkreferer();
	
	$fn = $MSO->config['base_dir'] . base64_decode($MSO->data['uri_segment'][2]);
	
	// файл есть
	if (file_exists($fn)) 
	{
		// подключаются только файлы вида имя-ajax.php 
		if (strpos($fn, '-ajax.php') !== false ) // есть вхождение
		{
			$fn1 = preg_replace('!(.*)/(.*)-ajax.php(.*)!', "$1", $fn); // путь
			$fn2 = preg_replace('!(.*)/(.*)-ajax.php(.*)!', "$2", $fn); // имя
			$fn3 = preg_replace('!(.*)/(.*)-ajax.php(.*)!', "$3", $fn); // концовка
			
			if (strpos($fn1, $MSO->config['base_dir']) === false ) die(); // неверный путь
			if (!$fn2) die(); // неверное имя
			if ($fn3) die(); // в конеце какой-то мусор
			
			require($fn);
			
		}
		else die();
	}
}
else die();

?>