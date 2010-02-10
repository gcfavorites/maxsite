<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://maxsite.org/
 */


# функция автоподключения плагина
function links_autoload($args = array())
{
	mso_register_widget('links_widget', 'Ссылки'); # регистрируем виджет
}

# функция выполняется при деинсталяции плагина
function links_uninstall($args = array())
{	
	mso_delete_option_mask('links_widget_', 'plugins'); // удалим созданные опции
	return $args;
}

# функция, которая берет настройки из опций виджетов
function links_widget($num = 1) 
{
	$widget = 'links_widget_' . $num; // имя для опций = виджет + номер
	$options = mso_get_option($widget, 'plugins', array() ); // получаем опции
	
	// заменим заголовок, чтобы был в  h2 class="box"
	if ( isset($options['header']) and $options['header'] ) $options['header'] = '<h2 class="box">' . $options['header'] . '</h2>';
		else $options['header'] = '';
	
	return links_widget_custom($options, $num);
}


# форма настройки виджета 
# имя функции = виджет_form
function links_widget_form($num = 1) 
{
	$widget = 'links_widget_' . $num; // имя для формы и опций = виджет + номер
	
	// получаем опции 
	$options = mso_get_option($widget, 'plugins', array());
	
	if ( !isset($options['header']) ) $options['header'] = '';
	if ( !isset($options['links']) ) $options['links'] = '';
	
	// вывод самой формы
	$CI = & get_instance();
	$CI->load->helper('form');
	
	$form = '<p><div class="t150">Заголовок:</div> '. form_input( array( 'name'=>$widget . 'header', 'value'=>$options['header'] ) ) ;
	$form .= '<p><div class="t150">Ссылки:</div> '. form_textarea( array( 'name'=>$widget . 'links', 'value'=>$options['links'] ) ) ;
	$form .= '<br /><div class="t150">&nbsp;</div>Указывайте по одной ссылке в каждом абзаце в формате:
			  <br /><div class="t150">&nbsp;</div><strong>http://ссылка/ | название | описание | noindex | _blank</strong>
			  <br /><div class="t150">&nbsp;</div><strong>noindex</strong> - обрамить ссылку в noindex, если не нужно - указать пробел
			  <br /><div class="t150">&nbsp;</div><strong>_blank</strong> - открыть ссылку в новом окне, если не нужно - указать пробел
			  ';

	return $form;
}


# сюда приходят POST из формы настройки виджета
# имя функции = виджет_update
function links_widget_update($num = 1) 
{
	$widget = 'links_widget_' . $num; // имя для опций = виджет + номер
	
	// получаем опции
	$options = $newoptions = mso_get_option($widget, 'plugins', array());
	
	# обрабатываем POST
	$newoptions['header'] = mso_widget_get_post($widget . 'header');
	$newoptions['links'] = mso_widget_get_post($widget . 'links');
	
	if ( $options != $newoptions ) 
		mso_add_option($widget, $newoptions, 'plugins');
}

# функции плагина
function links_widget_custom($options = array(), $num = 1)
{
	// кэш 
	$cache_key = 'links_widget_custom' . serialize($options) . $num;
	$k = mso_get_cache($cache_key);
	if ($k) return $k; // да есть в кэше
	
	$out = '';
	
	if ( !isset($options['header']) ) $options['header'] = '';
	
	if ( isset($options['links']) ) 
	{
		$links = explode("\n", $options['links']); // разбиваем по строкам
		
		foreach ($links as $row)
		{
			$ar_link = explode('|', $row); // разбиваем по |
			
			// всего должно быть 5 элементов
			if ( isset($ar_link[0]) and trim($ar_link[0]) ) // если есть первый элемент
			{
				$href = trim($ar_link[0]); // адрес
				
				if ( $href and isset($ar_link[1]) and trim($ar_link[1]) ) // если есть второй элемент - название
				{
					$title = trim($ar_link[1]); // название
					
					if ( isset($ar_link[2]) and trim($ar_link[2]) )// если есть описание 
					{	
						$descr = '<div>' . trim($ar_link[2]) . '</div>'; 
					}	
					else 
					{
						$descr = '';
					}
					
					if ( isset($ar_link[3]) and trim($ar_link[3]) )// если есть noindex 
					{	
						$noindex1 = '<noindex>'; 
						$noindex2 = '</noindex>';
						$nofollow = 'rel="nofollow"';
					}	
					else 
					{
						$noindex1 = $noindex2 = $nofollow = '';
					}
					
					if ( isset($ar_link[4]) and trim($ar_link[4]) ) $blank = 'target="_blank"'; // если есть _blank
						else $blank = '';
					
					$out .= NR . '<li>' . $noindex1 . '<a href="' . $href . '" title="' . $title . '"' . $nofollow . $blank . '>' 
							. $title . '</a>' . $descr . $noindex2 . '</li>';
					
				}
			}
		}
	}
	
	if ($out) $out = $options['header'] . NR . '<ul class="is_link links">' . $out . NR . '</ul>' .NR ;
	
	mso_add_cache($cache_key, $out); // сразу в кэш добавим
	
	return $out;
}

?>