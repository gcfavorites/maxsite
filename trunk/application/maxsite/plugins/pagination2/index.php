<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */


function pagination2_autoload($a = array()) 
{
	mso_hook_add('pagination', 'pagination2_go', 5);
	return $a;
}


function pagination2_go($r = array()) 
{
	if ( !isset($r['maxcount']) ) return $r;

	$r_orig = $r; // сохраним исходный,	чтобы его же отдать дальше
	
	if ( !isset($r['old']) ) $r['old'] = t('Старее »»»', 'plugins');
	if ( !isset($r['new']) ) $r['new'] = t('««« Новее', 'plugins');
	if ( !isset($r['sep']) ) $r['sep'] = ' | '; // разделитель

	# раньше - позже
	if ($ran1 = mso_url_paged_inc($r['maxcount'], -1)) 
			$ran1 = '<span class="new"><a href="' . $ran1 . '">' . $r['new'] . '</a></span>';
	if ($ran2 = mso_url_paged_inc($r['maxcount'], 1))  
			$ran2 = '<span class="old"><a href="' . $ran2 . '">' . $r['old'] . '</a></span>';
	
	if (!$ran1 or !$ran2) $r['sep'] = '';

	echo NR . '<div class="pagination pagination2">' . $ran1 . $r['sep'] . $ran2 . '</div>' . NR;
	
	return $r_orig;
}

?>