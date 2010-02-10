<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * For MaxSite CMS
 * Spoiler Plugin
 * Author: (c) Sam
 * Plugin URL: http://6log.ru/spoiler 
 */

# функция автоподключения плагина
function spoiler_autoload($args = array())
{
	mso_hook_add( 'head', 'spoiler_head');
	mso_hook_add( 'content', 'spoiler_custom'); # хук на вывод контента
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
	//mso_cur_dir_lang(__FILE__);
	
	// константа
	$options_key = 'plugin_spoiler';

	/* Настройки*/
	$options = mso_get_option($options_key, 'plugins', array());
	if ( !isset($options['hide']) ) $options['hide'] = t('Скрыть',__FILE__);
	if ( !isset($options['show']) ) $options['show'] = t('Показать...',__FILE__);

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
			$id.'\'), this, \''.$showtext.'\', \''.$hidetext.'\')">'.$showtext.'</a>'.PHP_EOL;
			$html .= '<div class="spoiler_div" id="'.$id.'" style="display:none">'.$matches[3][$i].'</div>'.PHP_EOL;

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
	
	if ( !isset($options['style'])  ) {$options['style'] = ''; }
	if ($options['style'] != '')
	{
		echo '<link rel="stylesheet" href="' . getinfo('plugins_url') . 'spoiler/style/'.$options['style'].
		'" type="text/css" media="screen">';
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

# функция отрабатывающая миниопции плагина (function плагин_mso_options)
function spoiler_mso_options() 
{
	mso_cur_dir_lang(__FILE__);
	
    # ключ, тип, ключи массива
    mso_admin_plugin_options('plugin_spoiler', 'plugins', 
        array(
            'hide' => array(
                            'type' => 'text', 
                            'name' => t('Спрятать:'), 
                            'description' => t('Можно настроить какой текст появится в раскрытом виде'), 
                            'default' => t('Скрыть')
                        ),
            'show' => array(
                            'type' => 'text', 
                            'name' => t('Показать:'), 
                            'description' => t('Можно настроить какой текст появится в скрытом виде'), 
                            'default' => t('Показать...')
                        ), 
            'style' => array(
                            'type' => 'text', 
                            'name' => t('Выберите файл стилей:'), 
                            'description' => t('Указывать только имя файла с расширением(Например: spoiler.css). Оставьте поле пустым, если не хотите использовать стили.<br />Стили лежат в следеющей папке: (.../plugins/spoiler/style/...)'),
                            'default' => ' '
                        ),
            ),
		t('Настройки плагина Spoiler'), // титул
		t('<p>С помощью этого плагина вы можете скрывать текст под спойлер.<br />Для использования плагина обрамите нужный текст в код [spoiler]ваш текст[/spoiler]</p><p class="info">Также возможны такие варианты: <br />[spoiler=показать]ваш текст[/spoiler], [spoiler=показать/спрятать]ваш текст[/spoiler], [spoiler=/спрятать]ваш текст[/spoiler]</p>')  // инфа
    );
}

?>