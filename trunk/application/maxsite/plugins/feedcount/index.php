<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

# функция автоподключения плагина
function feedcount_autoload($args = array())
{
	mso_register_widget('feedcount_widget', t('Виджет подсчета подписчиков RSS', 'plugins')); # регистрируем виджет
	mso_hook_add( 'init', 'feedcount_init');
}

# функция выполняется при деинсталяции плагина
function feedcount_uninstall($args = array())
{	
	mso_delete_option_mask('feedcount_widget_', 'plugins'); // удалим созданные опции
	return $args;
}

function feedcount_init($args = array())
{
	if (!is_feed()) return $args;
	
	$stat = mso_get_float_option('feedcount', 'feedcount', array());
	
	$date = date('Y-m-d');
	$url = mso_current_url();
	
	if (isset($stat[$date][$url]))
	{
		$stat[$date][$url]++;
	}
	else
	{
		$stat[$date][$url] = 1;
	}
	
	mso_add_float_option('feedcount', $stat, 'feedcount'); 
	
	return $args;
}

# функция, которая берет настройки из опций виджетов
function feedcount_widget($num = 1) 
{
	$widget = 'feedcount_widget_' . $num; // имя для опций = виджет + номер
	$options = mso_get_option($widget, 'plugins', array() ); // получаем опции
	
	// заменим заголовок, чтобы был в  h2 class="box"
	if ( isset($options['header']) and $options['header'] ) 
			$options['header'] = '<h2 class="box"><span>' . $options['header'] . '</span></h2>';
	else $options['header'] = '';
	
	return feedcount_widget_custom($options, $num);
}


# форма настройки виджета 
# имя функции = виджет_form
function feedcount_widget_form($num = 1) 
{
	$widget = 'feedcount_widget_' . $num; // имя для формы и опций = виджет + номер
	
	// получаем опции 
	$options = mso_get_option($widget, 'plugins', array());
	
	if ( !isset($options['header']) ) $options['header'] = '';
	if ( !isset($options['format']) ) 
		$options['format'] = '<strong>' . t('Сегодня:', 'plugins') . '</strong> [COUNT]<br/><strong>' . t('Вчера:', 'plugins') . '</strong> [COUNTOLD]';
	
	// вывод самой формы
	$CI = & get_instance();
	$CI->load->helper('form');
	
	$form = '<p><div class="t150">' . t('Заголовок:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'header', 'value'=>$options['header'] ) ) ;
	
	$form .= '<p><div class="t150">' . t('Формат:', 'plugins') . '</div> '. form_textarea( array( 'name'=>$widget . 'format', 'value'=>$options['format'] ) ) ;
	
	$form .= '<p><div class="t150">&nbsp</div>' . t('[COUNT] - подписчиков сегодня, [COUNTOLD] - подписчиков вчера', 'plugins') . '</p>';
	
	return $form;
}


# сюда приходят POST из формы настройки виджета
# имя функции = виджет_update
function feedcount_widget_update($num = 1) 
{
	$widget = 'feedcount_widget_' . $num; // имя для опций = виджет + номер
	
	// получаем опции
	$options = $newoptions = mso_get_option($widget, 'plugins', array());
	
	# обрабатываем POST
	$newoptions['header'] = mso_widget_get_post($widget . 'header');
	$newoptions['format'] = mso_widget_get_post($widget . 'format');
	
	if ( $options != $newoptions ) 
		mso_add_option($widget, $newoptions, 'plugins');
}

# функции плагина
function feedcount_widget_custom($options = array(), $num = 1)
{
	if ( !isset($options['header']) ) $options['header'] = '';
	if ( !isset($options['format']) ) 
		$options['format'] = '<strong>' . t('Сегодня:', 'plugins') . '</strong> [COUNT]<br/><strong>' . t('Вчера:', 'plugins') . '</strong> [COUNTOLD]';
	
	$out = $options['format'];
	
	$stat = mso_get_float_option('feedcount', 'feedcount', array());
	$date = date('Y-m-d');
	$date_old = date('Y-m-d', time() - 86400);
	
	if (isset($stat[$date]['feed'])) $count = $stat[$date]['feed'];
		else $count = 0;
		
	if (isset($stat[$date_old]['feed'])) $count_old = $stat[$date_old]['feed'];
		else $count_old = 0;
		
	$out = str_replace('[COUNT]', $count, $out);
	$out = str_replace('[COUNTOLD]', $count_old, $out);
	
	if ($out and $options['header']) $out = $options['header'] . $out;
	return $out;	
}

?>