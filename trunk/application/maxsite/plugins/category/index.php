<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://maxsite.org/
 */
 

# функция автоподключения плагина
function category_autoload($args = array())
{
	# регистрируем виджет
	mso_register_widget('category_widget', 'Рубрики'); 
}


# функция, которая берет настройки из опций виджетов
function category_widget($num = 1) 
{
	$widget = 'category_widget_' . $num; // имя для опций = виджет + номер
	$options = mso_get_option($widget, 'plugins', array() ); // получаем опции
	
	// заменим заголовок, чтобы был в  h2 class="box"
	if ( isset($options['header']) and $options['header'] ) $options['header'] = '<h2 class="box">' . $options['header'] . '</h2>';
		else $options['header'] = '';
	
	if ( isset($options['include']) ) $options['include'] = mso_explode($options['include']);
		else $options['include'] = array();
		
	if ( isset($options['exclude']) ) $options['exclude'] = mso_explode($options['exclude']);
		else $options['exclude'] = array();
	
	if ( !isset($options['format']) ) $options['format'] = '%LINK_START%%NAME% (%COUNT_PAGES%)<br/><i>%DESC%</i>%LINK_END%';
	
	return category_widget_custom($options, $num);
}


# форма настройки виджета 
# имя функции = виджет_form
function category_widget_form($num = 1) 
{

	$widget = 'category_widget_' . $num; // имя для формы и опций = виджет + номер
	
	// получаем опции 
	$options = mso_get_option($widget, 'plugins', array());
	
	if ( !isset($options['header']) ) $options['header'] = '';
	if ( !isset($options['format']) ) $options['format'] = '%LINK_START%%NAME% (%COUNT_PAGES%)<br/><i>%DESC%</i>%LINK_END%';
	if ( !isset($options['include']) ) $options['include'] = '';
	if ( !isset($options['exclude']) ) $options['exclude'] = '';
	
	// вывод самой формы
	$CI = & get_instance();
	$CI->load->helper('form');
	
	$form = '<p><div class="t150">Заголовок:</div> '. form_input( array( 'name'=>$widget . 'header', 'value'=>$options['header'] ) ) ;
	
	$form .= '<p><div class="t150">Формат:</div> '. form_input( array( 'name'=>$widget . 'format', 'value'=>$options['format'] ) ) 
			. '<br /><div class="t150">пример</div> %LINK_START%%NAME% (%COUNT_PAGES%)&lt;br/&gt;&lt;i&gt;%DESC%&lt;/i&gt;%LINK_END%'
			. '<br /><div class="t150">все</div> %NAME% %ID% %DESC% %LEVEL% %LINK_START% %LINK_END% %CHECKED% %COUNT_PAGES%' ;
	
	$form .= '<p><div class="t150">Включить только:</div> '. form_input( array( 'name'=>$widget . 'include', 'value'=>$options['include'] ) ) 
			. '<br /><div class="t150">&nbsp;</div> Укажите номера рубрик через запятую или пробел';
	
	$form .= '<p><div class="t150">Исключить:</div> '. form_input( array( 'name'=>$widget . 'exclude', 'value'=>$options['exclude'] ) )
			. '<br /><div class="t150">&nbsp;</div> Укажите номера рубрик через запятую или пробел';
	
	return $form;
}


# сюда приходят POST из формы настройки виджета
# имя функции = виджет_update
function category_widget_update($num = 1) 
{

	$widget = 'category_widget_' . $num; // имя для опций = виджет + номер
	
	// получаем опции
	$options = $newoptions = mso_get_option($widget, 'plugins', array());
	
	# обрабатываем POST
	$newoptions['header'] = mso_widget_get_post($widget . 'header');
	$newoptions['format'] = mso_widget_get_post($widget . 'format');
	$newoptions['include'] = mso_widget_get_post($widget . 'include');
	$newoptions['exclude'] = mso_widget_get_post($widget . 'exclude');
	
	if ( $options != $newoptions ) 
		mso_add_option($widget, $newoptions, 'plugins');
}


function category_widget_custom($arg = array(), $num = 1)
{

	/*
	$li_format = '%NAME%', // формат вывода: %NAME% %ID% %DESC% %LEVEL% %LINK_START% %LINK_END% %CHECKED% %COUNT_PAGES%
	$show_empty = true, // показывать рубрики без записей
	$checked_id = array(), // номера, где меняет %CHECKED% на checked="checked" - для чекбоксов
	$selected_id = array(), // номера, где отмечать <li class="selected">
	$ul_class = 'category', // класс главного списка ul
	$type_page = 'blog',
	$type = 'page', // тип - для выборки данных
	$order = 'category_menu_order', // сортировка по указанному полю
	$asc = 'asc', // порядок сортировки ASC или DESC
	$custom_array = false, // какой-то другой массив
	$include = array(), // только указанные рубрики и их дети
	$exclude = array() // исключить указанные рубрики
	
	*/
	// <h2 class="box">Рубрики</h2>

	
	return $arg['header'] . mso_cat_ul($arg['format'], true, array(), array(), 'is_link category', 'blog', 'page', 'category_menu_order', 'asc', false, $arg['include'], $arg['exclude'] );
}


?>