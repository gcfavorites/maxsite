<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */


# функция автоподключения плагина
function internal_links_autoload()
{
	mso_hook_add('content_content', 'internal_links_custom');
}

# функция выполняется при активации (вкл) плагина
function internal_links_activate($args = array())
{	
	mso_create_allow('internal_links_edit', t('Админ-доступ к настройкам', 'plugins') . ' ' . t('Internal links', 'plugins'));
	return $args;
}

# функция выполняется при деинсталяции плагина
function internal_links_uninstall($args = array())
{	
	mso_delete_option('plugin_internal_links', 'plugins'); // удалим созданные опции
	mso_remove_allow('internal_links_edit'); // удалим созданные разрешения
	return $args;
}

# функция отрабатывающая миниопции плагина (function плагин_mso_options)
# если не нужна, удалите целиком
function internal_links_mso_options() 
{
	if ( !mso_check_allow('internal_links_edit') ) 
	{
		echo t('Доступ запрещен', 'plugins');
		return;
	}
	
	# ключ, тип, ключи массива
	mso_admin_plugin_options('plugin_internal_links', 'plugins', 
		array(
			'links' => array(
							'type' => 'textarea', 
							'rows' => 20,
							'name' => t('Ключевые фразы и их ссылки', 'plugins'), 
							'description' => t('Укажите в формате: <strong>фраза | ссылка | css-класс ссылки</strong><br>Располагайте большие фразы выше мелких, чтобы не было пересечений.', 'plugins'), 
							'default' => ''
						),
			'default_class' => array(
							'type' => 'text', 
							'name' => t('CSS-класс по-умолчанию', 'plugins'), 
							'description' => t('Этот класс будет подставляться для всех ссылок по-умолчанию.', 'plugins'), 
							'default' => ''
						),
			'only_page_type' => array(
							'type' => 'checkbox', 
							'name' => t('Выполнять замены только на одиночных страницах', 'plugins'), 
							'description' => t('На всех остальных страницах сайта замены выполняться не будут', 'plugins'), 
							'default' => '1'
						),
			'max_count' => array(
							'type' => 'text', 
							'name' => t('Максимальное количество ссылок одной фразы в тексте', 'plugins'), 
							'description' => t('Если указать «0», то выделены будут все вхождения.', 'plugins'), 
							'default' => '1'
						),			
			),
		t('Настройки плагина «Внутренние ссылки»', 'plugins'), 
		t('Плагин позволяет выполнить автоматическую замену указанных слов на ссылки. Если фраза сопровождается в тексте символами «&gt;» или «"»,- то она не будет оформлена как ссылка.', 'plugins')
	);
	
}



# функции плагина
function internal_links_custom($text = '')
{
	static $a_link; // здесь хранится обработанный массив ссылок - чтобы не обрабатывать несколько раз
	
	global $_internal_links;
	
	$options = mso_get_option('plugin_internal_links', 'plugins', array());
	
	// только на page
	if (!isset($options['only_page_type'])) $options['only_page_type'] = true;
	if ($options['only_page_type'] and !is_type('page')) return $text;
	
	// не указаны ссылки
	if (!isset($options['links'])) return $text; 
	if (!trim($options['links'])) return $text;
	

	if (!isset($options['default_class'])) $options['default_class'] = '';
	if (!isset($options['max_count'])) $options['max_count'] = 1;
		else $options['max_count'] = (int) $options['max_count'];
	if ($options['max_count'] === 0) $options['max_count'] = -1; // замена для preg_replace_callback
	
	$links = explode("\n", str_replace("\r", '', trim($options['links']))); // все ссылки в массив
	
	if (!isset($a_link) or !$a_link)
	{
		$a_link = array();
		foreach ($links as $link)
		{
			$l1 = explode('|', $link);
			
			if ( isset($l1[0]) and isset($l1[1]) ) // фраза | ссылка
			{
				$key = trim($l1[0]);
				$a_link[$key]['word'] = trim($l1[0]);
				$a_link[$key]['link'] = trim($l1[1]);
				
				if (strpos($a_link[$key]['link'], 'http://') === false)
					$a_link[$key]['link'] = getinfo('siteurl') . $a_link[$key]['link'];
				
				if ( isset($l1[2]) and trim($l1[2]) ) // class
				{
					$a_link[$key]['class'] = trim($l1[2]);
				}
				else
				{
					$a_link[$key]['class'] = trim($options['default_class']);
				}
			}
		}
	}
	
	$current_url = getinfo('siteurl') . mso_current_url(false);
	
	foreach ($a_link as $key)
	{
		if (strpos($text, $key['word']) === false) continue; // нет вхождения
		
		if ($key['link'] == $current_url) continue; // ссылка на себя 
		
		if ($key['class']) $class = ' class="' . $key['class']. '"';
			else $class = '';
		
		/*
		// параметры для lambda функции
		$fs = '$matches, $ar=array(\'' . $key['link'] . '\', \'' . $class . '\')';
		$fr = 'return $matches[1] . \'<a href="\' . $ar[0] . \'"\' . $ar[1] . \'>\' . $matches[2] . \'</a>\';';
		
		$text = preg_replace_callback(
			//'~([^">])(' . preg_quote($key['word']) . ')([^"<])~siu', 
			'~([^">])(' . preg_quote($key['word']) . ')~siu', 
			create_function($fs, $fr),
			$text, 
			$options['max_count']);
		*/	
		
		$_internal_links['class'] = $class;
		$_internal_links['link'] = $key['link'];
		
		$text = preg_replace_callback(
			'~([^">])(' . preg_quote($key['word']) . ')~siu', 
			//'~([^">])(' . preg_quote($key['word']) . ')([^"<])~siu', 
			
			// '~(?<!title=")(' . preg_quote($key['word']) . ')~siu',
			
			'internal_links_custom_callback',
			$text, 
			$options['max_count']);	


	}
	
	
	//pr($text,1);
	return $text;
}


function internal_links_custom_callback($matches)
{
	global $_internal_links;
	
	// pr($matches, 1);
	// pr($matches[2],1);
	
	$out = 
		  $matches[1] 
		. '<a href="' . $_internal_links['link'] . '"' . $_internal_links['class'] . '>' 
		. $matches[2] 
		. '</a>';
		//. $matches[3]; 
	
	return $out;
}


# end file