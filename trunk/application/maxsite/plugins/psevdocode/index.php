<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://maxsite.org/
 */


# функция автоподключения плагина
function psevdocode_autoload($args = array())
{
	mso_hook_add( 'content_out', 'psevdocode_go'); # хук на вывод контента
}



# функция вызываемая при хуке, указанном в mso_admin_url_hook
function psevdocode_go($text) 
{

	#  задайте список замен в массиве
	#  строки задаются по правилам PHP!
	#  элемены массива разделяются запятыми
	#  после последнего элемента запятая не нужна

	$psevdocodes = array (
		'[список]' => '<ul class="text">', 
		'[/список]' => '</ul>', 
		'_:список:' => '<ul class="text">', 
		':список:' => '<ul class="text">', 
		':/список:' => '</ul>', 
		'<br />[*]' => '<li>',
		'[*]' => '<li>',
		'[номера]' => '<ol class="text">',
		'[/номера]' => '</ol>',
		':номера:' => '<ol class="text">',
		':/номера:' => '</ol>',
		'[отступ]' => '<blockquote class="otstup">', 
		'[/отступ]' => '</blockquote>', 
		':отступ:' => '<blockquote class="otstup">', 
		':/отступ:' => '</blockquote>', 
		'[комментарий]' => '<blockquote>', 
		'[/комментарий]' => '</blockquote>',
		':комментарий:' => '<blockquote>', 
		':/комментарий:' => '</blockquote>',
		'[цитата]' => '<blockquote>', 
		'[/цитата]' => '</blockquote>',
		'[врезка вправо]' => '<div class="vrezka-right">',
		'[врезка]' => '<div class="vrezka">',
		'[/врезка]' => '</div>',
		'[текст]' => '<pre>', 
		'[/текст]' => '</pre>', 
		'[подзаголовок]' => '<h2>',
		'[/подзаголовок]' => '</h2>',
		'[подзаголовок1]' => '<h3>',
		'[/подзаголовок1]' => '</h3>',
		':врез:' => '<p class="vrez">',
		':/врез:' => '</p>',
		'[врез]' => '<p class="vrez">',
		'[/врез]' => '</p>',
		'[подпись]' => '<p class="podpis">',
		'[/подпись]' => '</p>',
		//'/-' => '<strong>',
		//'-/' => '</strong>',
		'[---]' => '<hr>',
		);
	
	$text = strtr($text, $psevdocodes);
	return $text;
}


?>