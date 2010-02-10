<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://maxsite.org/
 */

// 

function editor_jw_admin_header($args = '') 
{
	global $MSO;
	
	echo NR . '<link rel="stylesheet" href="' 
			. $MSO->config['admin_plugins_url'] 
			. 'editor_jw/jw/jquery.wysiwyg.css" type="text/css" media="screen" />' . NR;
}

function editor_jw($args = array()) 
{
	global $MSO;
	
	$editor_config['url'] = $MSO->config['admin_plugins_url'] . 'editor_jw/';
	$editor_config['dir'] = $MSO->config['admin_plugins_dir'] . 'editor_jw/';
	
	// if (isset($args['content'])) $editor_config['content'] = mso_text_to_html($args['content']);
	if (isset($args['content'])) $editor_config['content'] = $args['content'];
		else $editor_config['content'] = '';
		
	if (!$editor_config['content']) $editor_config['content'] = '<br>';
		
	if (isset($args['do'])) $editor_config['do'] = $args['do'];
		else $editor_config['do'] = '';
		
	if (isset($args['posle'])) $editor_config['posle'] = $args['posle'];
		else $editor_config['posle'] = '';	
		
	if (isset($args['action'])) $editor_config['action'] = ' action="' . $args['action'] . '"';
		else $editor_config['action'] = '';		
	
	mso_hook_add( 'admin_head', 'editor_jw_admin_header');
	
	/*
	
	if (isset($args['height'])) $editor_config['height'] = $args['height'];
		else $editor_config['height'] = '200px';	
	
	if (isset($args['width'])) $editor_config['width'] = $args['width'];
		else $editor_config['width'] = '100%';
		
	if (isset($args['css_style'])) $editor_config['css_style'] = $args['css_style'];
		else $editor_config['css_style'] = '';
		
	if (isset($args['panel1'])) $editor_config['panel1'] = (bool) $args['panel1'];
		else $editor_config['panel1'] = true;

	if (isset($args['panel2'])) $editor_config['panel2'] = (bool) $args['panel2'];
		else $editor_config['panel2'] = true;

	if (isset($args['panel3'])) $editor_config['panel3'] = (bool) $args['panel3'];
		else $editor_config['panel3'] = true;
		
	if (isset($args['PreviewMode'])) $editor_config['PreviewMode'] = (bool) $args['PreviewMode'];
		else $editor_config['PreviewMode'] = true;
		
	if (isset($args['CodeMode'])) $editor_config['CodeMode'] = (bool) $args['CodeMode'];
		else $editor_config['CodeMode'] = true;
	*/
	
	require($editor_config['dir'] . 'editor.php');

}

?>