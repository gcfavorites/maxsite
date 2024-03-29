<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */


# функция автоподключения плагина
function psevdocode_autoload($args = array())
{
	mso_hook_add( 'content', 'psevdocode_go'); # хук на вывод контента
	mso_create_allow('psevdocode_edit', t('Админ-доступ к настройкам', 'plugins') . ' ' . t('псевдокода', 'plugins'));
}

# функция выполняется при деинсталяции плагина
function psevdocode_uninstall($args = array())
{
	mso_delete_option('plugin_psevdocode', 'plugins'); // удалим созданные опции
	mso_remove_allow('psevdocode_edit'); // удалим созданные разрешения 
	return $args;
}

# опции
function psevdocode_mso_options()
{
	# ключ, тип, ключи массива
	mso_admin_plugin_options('plugin_psevdocode', 'plugins',
		array(
			'replace' => array(
							'type' => 'textarea',
							'name' => t('Укажите замены через || ', 'plugins'),
							'description' => t('Замены следует указывать через || по одной в строчке, например<br>[подзаголовок] || &lt;h2&gt;<br>[/подзаголовок] || &lt;/h2&gt;', 'plugins'),
							'default' => '[список] || <ul class="text"> 
[/список] || </ul> 
[номера] || <ol class="text">
[/номера] || </ol>
[отступ] || <blockquote class="otstup"> 
[/отступ] || </blockquote> 
[комментарий] || <blockquote> 
[/комментарий] || </blockquote>
[цитата] || <blockquote> 
[/цитата] || </blockquote>
[врезка вправо] || <div class="vrezka-right">
[врезка] || <div class="vrezka">
[/врезка] || </div>
[текст] || <pre> 
[/текст] || </pre> 
[подзаголовок] || <h2>
[/подзаголовок] || </h2>
[подзаголовок1] || <h3>
[/подзаголовок1] || </h3>
[врез] || <p class="vrez">
[/врез] || </p>
[подпись] || <p class="podpis">
[/подпись] || </p>
[---] || <hr>'
						),
			),
		t('Настройки псевдокода', 'plugins'),
		t('Плагин позволяет создавать псевдокод, который будет автоматически заменяться при отображении страниц. Например можно вместо сложного и часто встречающегося HTML-кода, задать короткий псевдокод, которым будет проще и удобней пользоваться.', 'plugins')
	);
}

# функция вызываемая при хуке, указанном в mso_admin_url_hook
function psevdocode_go($text) 
{
	
	$options = mso_get_option('plugin_psevdocode', 'plugins', array());
	if (!isset($options['replace']))
	{		
		$psevdocodes = array(
			'[список]' => '<ul class="text">', 
			'[/список]' => '</ul>', 
			// '_:список:' => '<ul class="text">', 
			// ':список:' => '<ul class="text">', 
			// ':/список:' => '</ul>', 
			//'<br />[*]' => '<li>',
			//'<br>[*]' => '<li>',
			'[номера]' => '<ol class="text">',
			'[/номера]' => '</ol>',
			//':номера:' => '<ol class="text">',
			//':/номера:' => '</ol>',
			'[отступ]' => '<blockquote class="otstup">', 
			'[/отступ]' => '</blockquote>', 
			//':отступ:' => '<blockquote class="otstup">', 
			//':/отступ:' => '</blockquote>', 
			'[комментарий]' => '<blockquote>', 
			'[/комментарий]' => '</blockquote>',
			//':комментарий:' => '<blockquote>', 
			//':/комментарий:' => '</blockquote>',
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
			//':врез:' => '<p class="vrez">',
			//':/врез:' => '</p>',
			'[врез]' => '<p class="vrez">',
			'[/врез]' => '</p>',
			'[подпись]' => '<p class="podpis">',
			'[/подпись]' => '</p>',
			'[---]' => '<hr>');
	}
	else
	{
		$psevdocodes_all = explode("\n", $options['replace']); // строки в массив
		
		$psevdocodes = array();
		
		foreach($psevdocodes_all as $line) // проходим каждую строчку
		{
			if (trim($line))
			{
				$kv = explode('||', $line); // строку, разделенную || в массив
				
				if (count($kv) > 1) // должно быть два элемента
				{
					$psevdocodes[trim($kv[0])] = trim($kv[1]);
				}
			}
		}
	}
	
	/*
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
		'<br>[*]' => '<li>',
		//'[*]' => '<li>',
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
	*/
	
	
	$text = strtr($text, $psevdocodes);
	return $text;
}


# end file