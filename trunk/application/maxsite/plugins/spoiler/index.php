<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * spoiler plugin
 * Author: (c) Sam
 * Plugin URL: http://6log.ru/spoiler 
 */

# функция автоподключения плагина
function spoiler_autoload($args = array())
{
	mso_hook_add( 'head', 'spoiler_head');
	mso_hook_add( 'content', 'spoiler_custom'); # хук на вывод контента
	mso_hook_add( 'admin_init', 'spoiler_admin_init'); # хук на админку
	mso_create_allow( 'spoiler', t('Админ-доступ к редактированию spoiler', __FILE__) );
}


# функция выполняется при указаном хуке admin_init
function spoiler_admin_init($args = array())
{
	if( mso_check_allow('spoiler') )
	{
		$this_plugin_url = 'plugin_spoiler'; // url и hook
		mso_admin_menu_add('plugins', $this_plugin_url, 'Spoiler');
		mso_admin_url_hook ($this_plugin_url, 'spoiler_admin_page');
	}
	return $args;
}


# функция вызываемая при хуке, указанном в mso_admin_url_hook
function spoiler_admin_page($args = array())
{
	global $MSO;
	if ( !mso_check_allow('spoiler') ) 
	{
		echo t('Доступ запрещен', __FILE__);
		return $args;
	}
	
	# выносим админские функции отдельно в файл
	mso_hook_add_dinamic( 'mso_admin_header', ' return $args . "' . t('Настройка spoiler', __FILE__) . ' "; ' );
	mso_hook_add_dinamic( 'admin_title', ' return "' . t('Настройка spoiler', __FILE__) . ' - " . $args; ' );

	require($MSO->config['plugins_dir'] . 'spoiler/admin.php');
}


# функция выполняется при деинсталяции плагина
function spoiler_uninstall($args = array())
{
	// константа
	$options_key = 'plugin_spoiler';

	mso_delete_option($options_key,'plugins');
	return $args;
}


# функции плагина
function spoiler_custom($text)
{
	// константа
	$options_key = 'plugin_spoiler';

	/* Настройки*/
	$options = mso_get_option($options_key, 'plugins', array());
	if ( !isset($options['hide']) ) $options['hide'] = t('Скрыть', __FILE__);
	if ( !isset($options['show']) ) $options['show'] = t('Показать...', __FILE__);

	$showtext = $options['show'];
    $hidetext = $options['hide'];
   
	// dont edit!
	//$pattern = '@(\[spoiler\](.*?)\[/spoiler\])@is';
	$pattern = "@\[spoiler(=)?(.*?)\](.*?)\[\/spoiler\]@is";

	// замена  [spoiler]...[/spoiler] тегов

	if (preg_match_all($pattern, $text, $matches))
	{
		for ($i = 0; $i < count($matches[0]); $i++)
		{
			//$id   = 'id'.rand();
			$id = 'id'.rand(100,999);
			$html = '';
			
			if ($matches[1][$i] == '=')
			{
				if ( strpos($matches[2][$i], "/") !== false )
				{
					$tm = explode("/", $matches[2][$i]);
					if ( strpos($matches[2][$i], "/") === 0 )
					{
						$hidetext = $tm[1];
						$showtext = $options['show'];
					}
					else
					{
						$hidetext = $tm[1];
						$showtext = $tm[0];
					}
				} 
				else
				{
					$showtext = $matches[2][$i];
					$hidetext = $options['hide'];
				}
				
			}
			else
			{
				$showtext = $options['show'];
				$hidetext = $options['hide'];			
			}
			  
			$html .= '<a class="spoiler_link_show" href="javascript:void(0)" onclick="SpoilerToggle(document.getElementById(\''.
			$id.'\'), this, \''.$showtext.'\', \''.$hidetext.'\')">'.$showtext.'</a>' . PHP_EOL;
			$html .= '<div class="spoiler_div" id="'.$id.'" style="display:none">' . $matches[3][$i].'</div>'.PHP_EOL;

			//$text = str_replace($matches[0][$i], $html, $text);
			$text = preg_replace($pattern, $html, $text,1);
		}

    }

    return $text;
}


# JavaScript & css text добавляем в head
function spoiler_head($args = array())
{
	$options_key = 'plugin_spoiler';
	$options = mso_get_option($options_key, 'plugins', array());
	
	if ( !isset($options['style']) ) $options['style'] = ''; 
	if ($options['style'] != '')
	{
		echo '<link rel="stylesheet" href="' . getinfo('plugins_url') . 'spoiler/'.$options['style'].
		'.css" type="text/css" media="screen">';
	}	
	//echo <<<EOF
	echo '	
	<script type="text/javascript">
		function SpoilerToggle(spoiler, link, showtext, hidetext)
		{
        	if (spoiler.style.display != "none")
			{
            	spoiler.style.display = "none";
                link.innerHTML = showtext;
                link.className = "spoiler_link_show";
             }
			 else
			 {
             	spoiler.style.display = "block";
                link.innerHTML = hidetext;
                link.className = "spoiler_link_hide";
             }
        }
	</script>';
	//EOF;
}
?>