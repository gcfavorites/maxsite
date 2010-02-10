<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://maxsite.org/
 */

# функция автоподключения плагина
function random_gal_autoload($args = array())
{
	mso_register_widget('random_gal_widget', t('Галерея', 'plugins')); # регистрируем виджет
}

# функция выполняется при деинсталяции плагина
function random_gal_uninstall($args = array())
{	
	mso_delete_option_mask('random_gal_widget_', 'plugins'); // удалим созданные опции
	return $args;
}

# функция, которая берет настройки из опций виджетов
function random_gal_widget($num = 1) 
{
	$widget = 'random_gal_widget_' . $num; // имя для опций = виджет + номер
	$options = mso_get_option($widget, 'plugins', array() ); // получаем опции
	
	// заменим заголовок, чтобы был в  h2 class="box"
	if ( isset($options['header']) and $options['header'] ) 
		$options['header'] = '<h2 class="box"><span>' . $options['header'] . '</span></h2>';
	else $options['header'] = '';
	
	return random_gal_widget_custom($options, $num);
}


# форма настройки виджета 
# имя функции = виджет_form
function random_gal_widget_form($num = 1) 
{
	$widget = 'random_gal_widget_' . $num; // имя для формы и опций = виджет + номер
	
	// получаем опции 
	$options = mso_get_option($widget, 'plugins', array());
	
	if ( !isset($options['header']) ) $options['header'] = '';
	if ( !isset($options['gal']) ) $options['gal'] = '';
	if ( !isset($options['count']) ) $options['count'] = 3;
	if ( !isset($options['style']) ) $options['style'] = '';
	if ( !isset($options['style_img']) ) $options['style_img'] = '';
	if ( !isset($options['html']) ) $options['html'] = '';
	
	// вывод самой формы
	$CI = & get_instance();
	$CI->load->helper('form');
	$CI->load->helper('directory');

	// получим все каталоги в uploads
	$all_dirs = directory_map(getinfo('uploads_dir'), true); // только в uploads
	$out = array('uploads/'=>'uploads/');
	foreach ($all_dirs as $d)
	{
		// это каталог
		if (is_dir( getinfo('uploads_dir') . $d) and $d != '_mso_float' and $d != 'mini' and $d != '_mso_i' and $d != 'smiles') 
			$out[$d] = $d;
	}
	
	
	$form = '<p><div class="t150">' . t('Заголовок:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'header', 'value'=>$options['header'] ) ) . '</p>';
	
	$form .= '<p><div class="t150">' . t('Галерея:', 'plugins') . '</div> '. form_dropdown( $widget . 'gal', $out, $options['gal']) . '</p>';
	
	$form .= '<p><div class="t150">' . t('Количество:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'count', 'value'=>$options['count'] ) ) . '</p>' ;
	
	$form .= '<p><div class="t150">' . t('CSS-cтиль блока:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'style', 'value'=>$options['style'] ) ) . '</p>' ;
	
	$form .= '<p><div class="t150">' . t('CSS-cтиль img:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'style_img', 'value'=>$options['style_img'] ) ) . '</p>';
	
	$form .= '<p><div class="t150">' . t('Свой HTML-блок:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'html', 'value'=>$options['html'] ) )  . '</p>';
	
	
	return $form;
}


# сюда приходят POST из формы настройки виджета
# имя функции = виджет_update
function random_gal_widget_update($num = 1) 
{
	$widget = 'random_gal_widget_' . $num; // имя для опций = виджет + номер
	
	// получаем опции
	$options = $newoptions = mso_get_option($widget, 'plugins', array());
	
	# обрабатываем POST
	$newoptions['header'] = mso_widget_get_post($widget . 'header');
	$newoptions['gal'] = mso_widget_get_post($widget . 'gal');
	$newoptions['count'] = mso_widget_get_post($widget . 'count');
	$newoptions['style'] = mso_widget_get_post($widget . 'style');
	$newoptions['style_img'] = mso_widget_get_post($widget . 'style_img');
	$newoptions['html'] = mso_widget_get_post($widget . 'html');
	
	if ( $options != $newoptions ) 
		mso_add_option($widget, $newoptions, 'plugins');
}

# функции плагина
function random_gal_widget_custom($options = array(), $num = 1)
{
	$out = '';
	
	if ( !isset($options['header']) ) $options['header'] = '';
	if ( !isset($options['gal']) ) $options['gal'] = 'uploads/';
	if ( !isset($options['count']) ) $options['count'] = 3;
	if ( !isset($options['style']) ) $options['style'] = ''; // стиль div блока
	if ( !isset($options['style_img']) ) $options['style_img'] = ''; // стиль каждой картинки
	if ( !isset($options['html']) ) $options['html'] = ''; // дополнительный html в конце вывода
	
	if ($options['gal'] == 'uploads/') $options['gal'] = '';
	
	// получим список всех файлов в указаном каталоге
	if ($options['gal']) $options['gal'] .= '/';
	
	$dir0 = getinfo('uploads_dir') . $options['gal'] . '/';
	$dir = getinfo('uploads_dir') . $options['gal'] . 'mini/';
	$dir_url = getinfo('uploads_url') . $options['gal'];
	$dir_url_mini = getinfo('uploads_url') . $options['gal'] . 'mini/';
	
	
	if ( ! is_dir($dir) ) return ''; // нет каталога
	
	$CI = & get_instance();
	$CI->load->helper('file');
	$CI->load->helper('directory');
	
	$fn_mso_descritions = $dir0 . '_mso_i/_mso_descriptions.dat';
	if (file_exists( $fn_mso_descritions )) 
	{
		// массив данных: fn => описание 
		$descritions = unserialize( read_file($fn_mso_descritions) ); // получим из файла все описания
	}
	else $descritions = array();
	
	
	$files = directory_map($dir, true); // все файлы в каталоге
	if (!$files) $files = array();
	
	$all_files = array(); // массив для всех нужных файлов
	$allowed_ext = array('gif', 'jpg', 'jpeg', 'png');
	foreach ($files as $file)
	{
		if (@is_dir($dir . $file)) continue; // это каталог
		else
		{
			$ext = strtolower(str_replace('.', '', strrchr($file, '.'))); // расширение файла
			if ( !in_array($ext, $allowed_ext) ) continue; // запрещенный тип файла
			$all_files[] = $file;
		}
	}
	
	shuffle($all_files); // перемешиваем массив
	
	$all_files = array_slice($all_files, 0, (int) $options['count']); // только нужное нам количество
	
	foreach ($all_files as $file)
	{
		if (isset($descritions[$file])) $title = ' title="' . $descritions[$file] . '" ';
			else $title = '';
		
		$out .= '<a href="' . $dir_url . $file . '" class="lightbox"' . $title . '><img src="' 
				. $dir_url_mini . $file . '" alt="" style="' . $options['style_img'] . '" /></a>' . NR;
	}	
	
	if ($out) $out = '<div class="random-gal-widget" style="' . $options['style'] . '">' . $out . '</div>' . $options['html'];
	
	if ($out and $options['header']) $out = $options['header'] . $out;

	return $out;	
}

?>