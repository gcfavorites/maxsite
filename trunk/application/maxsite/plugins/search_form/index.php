<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */


# функция автоподключения плагина
function search_form_autoload($args = array())
{
	mso_register_widget('search_form_widget', t('Форма поиска', 'plugins')); # регистрируем виджет
}

# функция выполняется при деинсталяции плагина
function search_form_uninstall($args = array())
{	
	mso_delete_option_mask('search_form_widget_', 'plugins'); // удалим созданные опции
	return $args;
}

# функция, которая берет настройки из опций виджетов
function search_form_widget($num = 1) 
{
	$widget = 'search_form_widget_' . $num; // имя для опций = виджет + номер
	$options = mso_get_option($widget, 'plugins', array() ); // получаем опции
	
	// заменим заголовок, чтобы был в  h2 class="box"
	if ( isset($options['header']) and $options['header'] ) 
		$options['header'] = '<h2 class="box"><span>' . $options['header'] . '</span></h2>';
	else $options['header'] = '';
	
	return search_form_widget_custom($options, $num);
}


# форма настройки виджета 
# имя функции = виджет_form
function search_form_widget_form($num = 1) 
{
	$widget = 'search_form_widget_' . $num; // имя для формы и опций = виджет + номер
	
	// получаем опции 
	$options = mso_get_option($widget, 'plugins', array());
	
	if ( !isset($options['header']) ) $options['header'] = '';
	if ( !isset($options['text']) ) $options['text'] = t('Что искать?', 'plugins');
	if ( !isset($options['submit']) ) $options['submit'] = t('Поиск', 'plugins');
	if ( !isset($options['style_text']) ) $options['style_text'] = 'width: 120px;';
	if ( !isset($options['style_submit']) ) $options['style_submit'] = 'margin-left: 5px; font-size: 8pt;';
	
	// вывод самой формы
	$CI = & get_instance();
	$CI->load->helper('form');
	
	$form = '<p><div class="t150">' . t('Заголовок:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'header', 'value'=>$options['header'] ) ) ;
	
	$form .= '<p><div class="t150">' . t('Текст подсказки:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'text', 'value'=>$options['text'] ) ) ;
	
	$form .= '<p><div class="t150">' . t('Текст на кнопке:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'submit', 'value'=>$options['submit'] ) ) ;
	
	$form .= '<p><div class="t150">' . t('CSS-стиль текста:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'style_text', 'value'=>$options['style_text'] ) ) ;
	
	$form .= '<p><div class="t150">' . t('CSS-стиль кнопки:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'style_submit', 'value'=>$options['style_submit'] ) ) ;
	
	return $form;
}


# сюда приходят POST из формы настройки виджета
# имя функции = виджет_update
function search_form_widget_update($num = 1) 
{
	$widget = 'search_form_widget_' . $num; // имя для опций = виджет + номер
	
	// получаем опции
	$options = $newoptions = mso_get_option($widget, 'plugins', array());
	
	# обрабатываем POST
	$newoptions['header'] = mso_widget_get_post($widget . 'header');
	$newoptions['text'] = mso_widget_get_post($widget . 'text');
	$newoptions['submit'] = mso_widget_get_post($widget . 'submit');
	$newoptions['style_text'] = mso_widget_get_post($widget . 'style_text');
	$newoptions['style_submit'] = mso_widget_get_post($widget . 'style_submit');

	
	if ( $options != $newoptions ) 
		mso_add_option($widget, $newoptions, 'plugins');
}

# функции плагина
function search_form_widget_custom($options = array(), $num = 1)
{
	$out = '';
	if ( !isset($options['header']) ) $options['header'] = '';
	if ( !isset($options['text']) ) $options['text'] = t('Что искать?', 'plugins');
	if ( !isset($options['submit']) ) $options['submit'] = t('Поиск', 'plugins');
	if ( !isset($options['style_text']) ) $options['style_text'] = 'width: 120px;';
	if ( !isset($options['style_submit']) ) $options['style_submit'] = 'margin-left: 5px; font-size: 8pt;';
	
	$out .= '
	<form class="search_form_widget" name="f_search" action="" method="get" onsubmit="location.href=\'' . getinfo('siteurl') . 'search/\' + encodeURIComponent(this.s.value).replace(/%20/g, \'+\'); return false;">
	<input type="text" name="s" style="' . $options['style_text'] . '" onfocus="if (this.value == \'' . $options['text'] . '\') {this.value = \'\';}" onblur="if (this.value == \'\') {this.value = \'' . $options['text'] . '\';}" value="' . $options['text'] . '" /><input type="submit" name="Submit" value="' . $options['submit'] . '" style="' . $options['style_submit'] . '" /></form>
	';
	
	if ($options['header']) $out = $options['header'] . $out;
	
	return $out;	
}

?>