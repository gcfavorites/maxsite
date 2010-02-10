<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

# функция автоподключения плагина
function lightbox_autoload($args = array())
{
	if (!is_type('admin') ) mso_hook_add( 'head', 'lightbox_head');
	if (is_type('admin') ) mso_hook_add( 'admin_head', 'lightbox_head');
	
}

function lightbox_head($args = array()) 
{
	global $MSO;
	
	$url = $MSO->config['plugins_url'] . 'lightbox/';
	
	echo <<<EOF
	<link rel="stylesheet" href="{$url}css/lightbox.css" type="text/css" media="screen" />
	<script type="text/javascript" src="{$url}js/prototype.js"></script>
	<script type="text/javascript" src="{$url}js/scriptaculous.js?load=effects"></script>
	<script type="text/javascript">
		var fileLoadingImage = "{$url}images/loading.gif";		
		var fileBottomNavCloseImage = "{$url}images/closelabel.gif";
		var overlayOpacity = 0.8;
		var animate = true;
		var resizeSpeed = 7;
		var borderSize = 10;
	</script>
	<script type="text/javascript" src="{$url}js/lightbox.js"></script>
EOF;

}


?>