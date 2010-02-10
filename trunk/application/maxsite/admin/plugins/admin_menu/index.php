<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */


# функция автоподключения плагина
function admin_menu_autoload($args = array())
{	
	mso_hook_add( 'admin_menu', 'admin_menu_menu');
}


# выводит меню в админке
function admin_menu_menu($args = array()) 
{
	global $admin_menu, $MSO;
	
	$admin_url = getinfo('site_admin_url');
		
	$nr = "\n";
	$out = '';
	
	if ( count($MSO->data['uri_segment']) > 1 )
	{
		$cur_url = $MSO->data['uri_segment'][2];
		if (!$cur_url) $cur_url = 'home';
		if ( !mso_hook_present('admin_url_' . $cur_url)) $cur_url = 'home';
	}
	else  $cur_url = 'home';
	
	
	// если меню не содержит подменю, то не выводим его
	$admin_menu1 = $admin_menu; 
	
	foreach ($admin_menu1 as $key => $value)
		if (count($admin_menu1[$key])<2) unset($admin_menu1[$key]);
	
	foreach ($admin_menu1 as $key => $value)
	{
		$out .= $nr . '<ul class="admin-menu">';
		$out .= $nr . '<li class="admin-menu-top"><a href="#">' . _mso_del_menu_pod($value['']) . '</a>';
		//$out .= $nr . '  <li class="admin-menu-top">' . _mso_del_menu_pod($value['']);

		if (count($value)>1 )
		{
			$out .= $nr . '    <ul class="admin-submenu">';
			foreach ($value as $url => $name)
			{
				if ( $value[''] == $name ) continue;
				
				if ($url == $cur_url) $selected = ' class="admin-menu-selected"';
					else  $selected = '';
					
				$out .= $nr . '      <li' . $selected . '><a href="' . $admin_url . $url . '">' . _mso_del_menu_pod($name) . '</a></li>';
			}
			$out .= $nr . '    </ul>';
		}
		$out .= $nr . '  </li>' . $nr . '</ul>' . $nr;
	}

	return $out;
}



?>