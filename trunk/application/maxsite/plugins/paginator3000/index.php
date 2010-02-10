<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

function paginator3000_autoload($a = array()) {
	mso_hook_add('head', 'paginator3000_head');
	mso_hook_add('pagination', 'paginator3000_go', 10);
	return $a;
}

function paginator3000_head($args = array()) {
	global $MSO;
	$url = $MSO->config['plugins_url'] . 'paginator3000/';
	echo '<link rel="stylesheet" type="text/css" href="'.$url.'paginator3000.css" />
<script type="text/javascript" src="'.$url.'paginator3000.js"></script>
';
}

function paginator3000_mso_options() {
	mso_admin_plugin_options('paginator3000', 'plugins', 
		array(
				'page_count' => array(
					'type' => 'text', 
					'name' => 'Число страниц, видимых одновременно', 
					'description' => '', 
					'default' => t('10', 'plugins')
				),
				'width' => array(
					'type' => 'text', 
					'name' => 'Ширина пагинатора (в процентах)', 
					'description' => '', 
					'default' => t('100', 'plugins')
				)
			)
		);
}

function paginator3000_uninstall($args = array()) {	
	mso_delete_option('paginator3000', 'plugins');
	return $args;
}

function paginator3000_go($r = array()) {
	global $MSO;
	$r_orig = $r;
	if ( !isset($r['limit']) ) return $r;
	if ( !isset($r['type']) )  $r['type'] = false;
	if ( !isset($r['next_url']) ) $r['next_url'] = 'next';
	
	$current_page = mso_current_paged($r['next_url']);
	if ($current_page > $r['maxcount']) $current_page = $r['maxcount'];
	
	if ($r['type'] !== false) $type = $r['type'];
		else $type = $MSO->data['type'];

	$a_cur_url = $MSO->data['uri_segment'];
	$cur_url = $MSO->config['site_url'];
	
	foreach ($a_cur_url as $val)
	{
		if ($val == $r['next_url']) break;
		else
		{
			if ($val != $type) $cur_url .= '/@@' . $val;
		}
	}

	$cur_url = str_replace('//@@', '/', $cur_url);
	$cur_url = str_replace('@@', '', $cur_url);

	if ($type == 'home') $cur_url = $MSO->config['site_url'] . $type;

	$options = mso_get_option('paginator3000', 'plugins', array());
	if ( !isset($options['width']) ) $options['width'] = '100';
	if ( !isset($options['page_count']) ) $options['page_count'] = '10';

	echo '<div id="paginator3000" class="p3000" style="width:'.$options['width'].'%"></div>
<script type="text/javascript">
paginator = new Paginator("paginator3000",'.$r['maxcount'].','.$options['page_count'].','.$current_page.',"'.$cur_url.'/'.$r['next_url'].'/");
</script>
';
	
	return $r_orig;
}
?>