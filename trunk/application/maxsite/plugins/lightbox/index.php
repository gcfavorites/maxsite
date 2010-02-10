<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (с) http://maxsite.org/
 */


# функция автоподключения плагина
function lightbox_autoload($args = array())
{
	if (!is_type('admin') ) mso_hook_add( 'head', 'lightbox_head');
	if (is_type('admin') ) mso_hook_add( 'admin_head', 'lightbox_head');
	mso_hook_add( 'content_out', 'lightbox_content'); # хук на вывод контента после обработки всех тэгов
}

function lightbox_head($args = array()) 
{
	global $MSO;
	
	echo mso_load_jquery();
	
	$url = $MSO->config['plugins_url'] . 'lightbox/';
	
	echo <<<EOF
	
	<script type="text/javascript" src="{$url}js/jquery.lightbox-0.5.pack.js"></script>
	<script type="text/javascript">
		$(function(){
			lburl = '{$url}images/';
			$('div.gallery a').lightBox({
				imageLoading: lburl+'lightbox-ico-loading.gif',
				imageBtnClose: lburl+'lightbox-btn-close.gif',
				imageBtnPrev: lburl+'lightbox-btn-prev.gif',
				imageBtnNext: lburl+'lightbox-btn-next.gif'
			});
			
			$('a.lightbox').lightBox({
				imageLoading: lburl+'lightbox-ico-loading.gif',
				imageBtnClose: lburl+'lightbox-btn-close.gif',
				imageBtnPrev: lburl+'lightbox-btn-prev.gif',
				imageBtnNext: lburl+'lightbox-btn-next.gif'
			});
		});
	</script>
	<link rel="stylesheet" href="{$url}css/jquery.lightbox-0.5.css" type="text/css" media="screen" />
	
EOF;

}

function lightbox_content($text = '')
{
	global $MSO;
	
	$url = $MSO->config['plugins_url'] . 'lightbox/images/';
	
	$preg = array(
	
		// удалим раставленные абзацы
		'~<p>\[gal=(.*?)\[\/gal\]</p>~si' => '[gal=$1[/gal]',
		'~<p>\[gallery(.*?)\](\s)*</p>~si' => '[gallery$1]',
		'~<p>\[\/gallery\](\s)*</p>~si' => '[/gallery]',
		
		'~<p>\[gallery(.*?)\](\s)*~si' => '[gallery$1]',
		'~\[\/gallery\](\s)*</p>~si' => '[/gallery]',
		
		'~\[gallery=(.*?)\](.*?)\[\/gallery\]~si' => '<div class="gallery$1">$2</div><script type="text/javascript">\$(function() { lburl = \'' . $url . '\'; \$(\'div.gallery$1 a\').lightBox({imageLoading: lburl+\'lightbox-ico-loading.gif\', imageBtnClose: lburl+\'lightbox-btn-close.gif\', imageBtnPrev: lburl+\'lightbox-btn-prev.gif\', imageBtnNext: lburl+\'lightbox-btn-next.gif\'});});</script>
		',
		
		'~\[gallery\](.*?)\[\/gallery\]~si' => '<div class="gallery">$1</div>',
		
		'~\[gal=(.[^\s]*?) (.*?)\](.*?)\[\/gal\]~si' => '<a href="$3" title="$2"><img src="$1" alt="$2" /></a>',
		
		'~\[gal=(.*?)\](.*?)\[\/gal\]~si' => '<a href="$2"><img src="$1" alt="" /></a>',
		
		'~\[image=(.[^\s]*?)[\s]+(.*?)\](.*?)\[\/image\]~si' => '<a href="$3" class="lightbox" title="$2"><img src="$1" alt="$2" /></a>',
		'~\[image=(.*?)\](.*?)\[\/image\]~si' => '<a href="$2" class="lightbox"><img src="$1" alt="" /></a>',
		'~\[image\](.*?)\[\/image\]~si' => '<a href="$1" class="lightbox"><img src="$1" alt="" /></a>',
	
		'~\[galname\](.*?)\[\/galname\]~si' => '<div>$1</div>',
	);

	return preg_replace(array_keys($preg), array_values($preg), $text);
}

?>