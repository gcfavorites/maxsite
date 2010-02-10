<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://maxsite.org/
 */

# функция автоподключения плагина
function ushki_autoload($args = array())
{
	mso_create_allow('ushki_edit', 'Админ-доступ к Ушкам');
	mso_hook_add( 'admin_init', 'ushki_admin_init'); # хук на админку
	mso_register_widget('ushki_widget', 'Ушки'); # регистрируем виджет
	mso_hook_add( 'content', 'ushki_content'); # хук на вывод контента
}

# функция выполняется при активации (вкл) плагина
function ushki_activate($args = array())
{	
	return $args;
}


# функция выполняется при деинсталяции плагина
function ushki_uninstall($args = array())
{	
	mso_delete_option_mask('ushki_widget_', 'plugins'); // удалим созданные опции
	return $args;
}

# функция выполняется при указаном хуке admin_init
function ushki_admin_init($args = array()) 
{
	if ( mso_check_allow('plugin_ushki') ) 
	{
		$this_plugin_url = 'plugin_ushki'; // url и hook
		
		# добавляем свой пункт в меню админки
		# первый параметр - группа в меню
		# второй - это действие/адрес в url - http://сайт/admin/demo
		#			можно использовать добавочный, например demo/edit = http://сайт/admin/demo/edit
		# Третий - название ссылки	
		
		mso_admin_menu_add('plugins', $this_plugin_url, 'Ушки');

		# прописываем для указаного admin_url_ + $this_plugin_url - (он будет в url) 
		# связанную функцию именно она будет вызываться, когда 
		# будет идти обращение по адресу http://сайт/admin/_null
		mso_admin_url_hook ($this_plugin_url, 'ushki_admin_page');
		
	}
	
	return $args;
}

# функция вызываемая при хуке, указанном в mso_admin_url_hook
function ushki_admin_page($args = array()) 
{
	# выносим админские функции отдельно в файл
	global $MSO;
	if ( !mso_check_allow('plugin_ushki') ) 
	{
		echo 'Доступ запрещен';
		return $args;
	}
	
	mso_hook_add_dinamic( 'mso_admin_header', ' return $args . "Настройки ушек"; ' );
	mso_hook_add_dinamic( 'admin_title', ' return "Настройки ушек - " . $args; ' );
	
	require($MSO->config['plugins_dir'] . 'ushki/admin.php');
}


# функция, которая берет настройки из опций виджетов
function ushki_widget($num = 1) 
{
	$widget = 'ushki_widget_' . $num; // имя для опций = виджет + номер
	$options = mso_get_option($widget, 'plugins', array() ); // получаем опции
	
	// заменим заголовок, чтобы был в  h2 class="box"
	if ( isset($options['header']) and $options['header'] ) $options['header'] = '<h2 class="box">' . $options['header'] . '</h2>';
		else $options['header'] = '';

	if ( !isset($options['ushka']) ) $options['ushka'] = '';

	return ushki_widget_custom($options, $num);
}


# форма настройки виджета 
# имя функции = виджет_form
function ushki_widget_form($num = 1) 
{
	$widget = 'ushki_widget_' . $num; // имя для формы и опций = виджет + номер
	
	// получаем опции 
	$options = mso_get_option($widget, 'plugins', array());
	
	if ( !isset($options['header']) ) $options['header'] = '';
	if ( !isset($options['ushka']) ) $options['ushka'] = '';
	
	// вывод самой формы
	$CI = & get_instance();
	$CI->load->helper('form');
		
	$form = '<p><div class="t150">Заголовок (блока):</div> '. form_input( array( 'name'=>$widget . 'header', 'value'=>$options['header'] ) ) ;
	$form .= '<p><div class="t150">Ушка (название):</div> '. form_input( array( 'name'=>$widget . 'ushka', 'value'=>$options['ushka'] ) ) ;
	
	return $form;
}


# сюда приходят POST из формы настройки виджета
# имя функции = виджет_update
function ushki_widget_update($num = 1) 
{
	$widget = 'ushki_widget_' . $num; // имя для опций = виджет + номер
	
	// получаем опции
	$options = $newoptions = mso_get_option($widget, 'plugins', array());
	
	# обрабатываем POST
	$newoptions['header'] = mso_widget_get_post($widget . 'header');
	$newoptions['ushka'] = mso_widget_get_post($widget . 'ushka');
	
	if ( $options != $newoptions ) 
		mso_add_option($widget, $newoptions, 'plugins');
}

# вывод ушки в виджете
function ushki_widget_custom($options = array(), $num = 1)
{
	if ( !isset($options['header']) ) $options['header'] = '';
	if ( !isset($options['ushka']) ) $options['ushka'] = '';
	
	$out = ushka($options['ushka']);
	
	if ($out) $out = $options['header'] . $out;
	
	return $out;
}

# получение массива всех ушек
# если указать true, то произойдет считывание с диска, иначе попытка взять уже готовый static
function ushki_get_all($no_cashe = false)
{
	static $all = false;
	
	if ($no_cashe or !$all) 
		return $all = mso_get_float_option('ushki', 'ushki', array());
	else
		return $all;
}

function ushki_content_callback($matches)
{
	if (isset($matches[1])) return ushka($matches[1]);
	else return '';
}

# [ushka=ads] - выведет ушку ads
function ushki_content($content = '')
{
	if (!is_feed()) $content = preg_replace_callback('!\[ushka=(.*?)\]!is', 'ushki_content_callback', $content);
	
	return $content;
}

# получение ушки
function ushka($name = '', $delim = '<br />', $not_exists = '')
{
	
	if (! trim($name) ) return '';
	
	$all = ushki_get_all(); // получили все ушки
	
	$out = '';
	
	// найдем нужные нам. Их может быть несколько
	foreach($all as $us)
	{
		if ($us['name'] == $name) // наша
		{
			$text = $us['text'];
			
			if  ($us['type'] == 'php') // нужно текст выполнить как php
			{
				ob_start();
				eval( '?>' . stripslashes( $text ) . '<?php ');
				$text = ob_get_contents();
				ob_end_clean();
			}
			
			if ($out != '') $out = $out . $delim . $text;
				else $out .= $text;
		}
	}
	
	if ($out == '') $out = $not_exists; // если такой ушки не обнаружилось
	
	return $out;
}


?>