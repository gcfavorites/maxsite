<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

# функция автоподключения плагина
function tweetmeme_com_autoload()
{
	if (!is_feed()) mso_hook_add( 'content_content', 'tweetmeme_com_content'); # хук на вывод контента
}


# функция выполняется при деинсталяции плагина
function tweetmeme_com_uninstall($args = array())
{	
	mso_delete_option('plugin_tweetmeme_com', 'plugins'); // удалим созданные опции
	return $args;
}

# функция отрабатывающая миниопции плагина (function плагин_mso_options)
# если не нужна, удалите целиком
function tweetmeme_com_mso_options() 
{
	# ключ, тип, ключи массива
	mso_admin_plugin_options('plugin_tweetmeme_com', 'plugins', 
		array(
			'align' => array(
						'type' => 'select', 
						'name' => 'Выравнивание блока tweetmeme.com', 
						'description' => 'Укажите выравнивание блока. Он добавляется в начало каждой записи.',
						'values' => 'left||Влево # right||Вправо # none||Нет',
						'default' => 'right'
					),
			'style' => array(
						'type' => 'text', 
						'name' => 'Стиль блока', 
						'description' => 'Укажите свой css-стиль блока tweetmeme.com.', 
						'default' => ''
					),
			'tweetmeme_style' => array(
						'type' => 'select', 
						'name' => 'Вид блока', 
						'description' => 'Можно использовать обычный и компактный',
						'values' => 'none||Обычный # compact||Компактный',
						'default' => 'none'
					),		
			),
		'Настройки плагина tweetmeme.com', // титул
		'Укажите необходимые опции.'   // инфо
	);
}

# функции плагина
function tweetmeme_com_content($text = '')
{
	if (!is_type('page') and !is_type('home')) return $text;
	
	global $page;
	
	$options = mso_get_option('plugin_tweetmeme_com', 'plugins', array() ); // получаем опции
	
	if (!isset($options['style'])) $options['style'] = '';
	if (!isset($options['align'])) $options['align'] = 'right';
	if (!isset($options['tweetmeme_style'])) $options['tweetmeme_style'] = 'none';
	
	if ($options['style']) $style = ' style="' . $options['style'] . '"';
	else
	{
		if ($options['align'] == 'left') $style = ' style="float: left; margin-right: 10px;"';
		elseif ($options['align'] == 'right') $style = ' style="float: right; margin-left: 10px; width: "';
		else $style = '';
	}
	
	if (is_type('home')) 
		$js1 = 'tweetmeme_url = \'' . getinfo('site_url') . 'page/' . $page['page_slug'] . '\';';
	else 
		$js1 = '';
	
	if ($options['tweetmeme_style'] == 'compact') 
		$js2 = 'tweetmeme_style = \'compact\';';
	else 
		$js2 = '';
		
	if ($js1 or $js2)
		$js = '<script type="text/javascript">' . $js1 . $js2 . '</script>';
	else
		$js = '';
	
	$text = '<span style="display: none"><![CDATA[<noindex>]]></span><div class="tweetmeme_com"' . $style . '>' . $js . '<script type="text/javascript" src="' . getinfo('plugins_url'). 'tweetmeme_com/button.js"></script></div><span style="display: none"><![CDATA[</noindex>]]></span>' . $text;
	
	return $text;
}


# end file