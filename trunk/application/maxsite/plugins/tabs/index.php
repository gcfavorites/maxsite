<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://maxsite.org/
 */

# функция автоподключения плагина
function tabs_autoload($args = array())
{
	mso_hook_add( 'head', 'tabs_head');
	mso_register_widget('tabs_widget', 'Табы (закладки)'); # регистрируем виджет
}

# функция выполняется при деинсталяции плагина
function tabs_uninstall($args = array())
{	
	mso_delete_option_mask('tabs_widget_', 'plugins'); // удалим созданные опции
	return $args;
}

# подключаем в заголовок стили и js
function tabs_head($args = array()) 
{
	// echo '	<link rel="stylesheet" href="' . getinfo('plugins_url') . 'tabs/flora.tabs.css" type="text/css" media="screen">' . NR;
	echo mso_load_jquery();
	echo mso_load_jquery('ui/ui.core.packed.js');
	echo mso_load_jquery('ui/ui.tabs.packed.js');

	return $args;
}


# функция, которая берет настройки из опций виджетов
function tabs_widget($num = 1) 
{
	$widget = 'tabs_widget_' . $num; // имя для опций = виджет + номер
	$options = mso_get_option($widget, 'plugins', array() ); // получаем опции
	
	// заменим заголовок, чтобы был в  h2 class="box"
	if ( isset($options['header']) and $options['header'] ) $options['header'] = '<h2 class="box">' . $options['header'] . '</h2>';
		else $options['header'] = '';
	
	return tabs_widget_custom($options, $num);
}


# форма настройки виджета 
# имя функции = виджет_form
function tabs_widget_form($num = 1) 
{
	$widget = 'tabs_widget_' . $num; // имя для формы и опций = виджет + номер
	
	// получаем опции 
	$options = mso_get_option($widget, 'plugins', array());
	
	if ( !isset($options['header']) ) $options['header'] = '';
	if ( !isset($options['tabs']) ) $options['tabs'] = '';
	if ( !isset($options['type_func']) ) $options['type_func'] = 'widget';
	
	// вывод самой формы
	$CI = & get_instance();
	$CI->load->helper('form');
	
	$form = '';
	
	if (!function_exists('ushka')) $form = '<p style="color: red; text-align: center;">Для работы этого виджета следует включить плагин «Ушки»!</p>'; 
	
	$form .= '<p><div class="t150">Заголовок:</div> '. form_input( array( 'name'=>$widget . 'header', 'value'=>$options['header'] ) ) ;
	$form .= '<p><div class="t150">Табы:</div> '. form_textarea( array( 'name'=>$widget . 'tabs', 'value'=>$options['tabs'] ) ) ;
	$form .= '<br /><div class="t150">&nbsp;</div>Указывайте по одному табу в каждом абзаце в формате: <strong>заголовок | виджет номер</strong>';
	$form .= '<br /><div class="t150">&nbsp;</div>Например: <strong>Цитаты | randomtext_widget 1</strong>';
	$form .= '<br /><div class="t150">&nbsp;</div>Для ушки: <strong>Цитаты | ушка_цитаты</strong>';
	
	
	$form .= '<p><div class="t150">Использовать:</div> '. form_dropdown( $widget . 'type_func', array( 'widget'=>'Виджет (функция и номер через пробел)', 'ushka'=>'Ушка (только название)'), $options['type_func']);

	return $form;
}


# сюда приходят POST из формы настройки виджета
# имя функции = виджет_update
function tabs_widget_update($num = 1) 
{
	$widget = 'tabs_widget_' . $num; // имя для опций = виджет + номер
	
	// получаем опции
	$options = $newoptions = mso_get_option($widget, 'plugins', array());
	
	# обрабатываем POST
	$newoptions['header'] = mso_widget_get_post($widget . 'header');
	$newoptions['tabs'] = mso_widget_get_post($widget . 'tabs');
	$newoptions['type_func'] = mso_widget_get_post($widget . 'type_func');
	
	if ( $options != $newoptions ) 
		mso_add_option($widget, $newoptions, 'plugins');
}

# функции плагина
function tabs_widget_custom($options = array(), $num = 1)
{
	if (!function_exists('ushka')) return ''; // не включены ушки - выходим
	
	$out = '';
	if ( !isset($options['header']) ) $options['header'] = '';
	if ( !isset($options['tabs']) ) $options['tabs'] = '';
	if ( !isset($options['type_func']) ) $options['type_func'] = 'widget';
	
	$ar = explode("\n", trim($options['tabs'])); // все табы в массив
	
	$tabs = array(); // наши закладки
	if ($ar)
	{
		foreach($ar as $key=>$val)
		{
			$t = explode('|', $val);
			if (isset($t[0]) and isset($t[1])) // есть и название и ушка
			{
				$tabs[$key]['title'] = trim($t[0]);
				$tabs[$key]['ushka'] = trim($t[1]);
			}
		}
	}
	
	if ($tabs) // есть закладки, можно выводить
	{
		$out .= NR . '<div id="tabs-widget-' . $num . '" class="flora"><ul>';
		foreach($tabs as $key => $tab)
			$out .= NR .  '<li><a href="#tabs-widget-fragment-' . $num . $key . '"><span>' . $tab['title'] . '</span></a></li>' . NR;
		$out .= '</ul>' . NR;
		
		foreach($tabs as $key => $tab)
		{
			if ($options['type_func'] == 'widget') // выводим с помощью функции виджета ($tab['ushka'])
			{
				$func = $tab['ushka']; // category_widget 20
				$nm = 0;
				
				// разделим и определим номер виджета
				$arr_w = explode(' ', $func); // в массив
				
				if ( sizeof($arr_w) > 1 ) // два или больше элементов
				{
					$func = trim( $arr_w[0] ); // первый - функция
					$nm = (int) trim( $arr_w[1] ); // второй - номер виджета
				}
				
				if ( function_exists($func) ) $func = $func($nm);
					else $func = 'no-func';
			
			}
			else $func = ushka($tab['ushka']);
			
			$out .= NR . '<div id="tabs-widget-fragment-' . $num . $key . '">' . $func . '</div>' . NR;
		}
			
		$out .= '</div>' . NR;
	}
	
	if ($out and $options['header']) $out = $options['header'] . $out;
		
	if ($out) $out .=  <<<EOF
	<script> 
		$(document).ready(function(){
				$("#tabs-widget-{$num} > ul").tabs({ fx: { height: 'toggle', opacity: 'toggle', duration: 'fast' } });
			});
	</script> 
EOF;
				// $("#tabs-widget-{$num} > ul").tabs();

	return $out;	
}

?>