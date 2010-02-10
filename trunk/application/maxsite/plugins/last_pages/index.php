<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */


# функция автоподключения плагина
function last_pages_autoload($args = array())
{
	# регистрируем виджет
	mso_register_widget('last_pages_widget', t('Последние записи', 'plugins')); 
}

# функция выполняется при деинсталяции плагина
function last_pages_uninstall($args = array())
{	
	mso_delete_option_mask('last_pages_widget_', 'plugins'); // удалим созданные опции
	return $args;
}

# функция, которая берет настройки из опций виджетов
function last_pages_widget($num = 1) 
{
	$widget = 'last_pages_widget_' . $num; // имя для опций = виджет + номер
	$options = mso_get_option($widget, 'plugins', array() ); // получаем опции
	
	// заменим заголовок, чтобы был в  h2 class="box"
	if ( isset($options['header']) and $options['header'] ) $options['header'] = '<h2 class="box"><span>' . $options['header'] . '</span></h2>';
		else $options['header'] = '';
	
	if ( isset($options['format']) ) $options['format'] = '<li>' . $options['format'] . '</li>';
		else $options['format'] = '<li>%TITLE%</li>';
	
	return last_pages_widget_custom($options, $num);
}


# форма настройки виджета 
# имя функции = виджет_form
function last_pages_widget_form($num = 1) 
{

	$widget = 'last_pages_widget_' . $num; // имя для формы и опций = виджет + номер
	
	// получаем опции 
	$options = mso_get_option($widget, 'plugins', array());
	
	if ( !isset($options['header']) ) $options['header'] = '';
	if ( !isset($options['count']) ) 	$options['count'] = 7;
	if ( !isset($options['type']) )  	$options['type'] = 'blog';
	if ( !isset($options['exclude_cat']) )  	$options['exclude_cat'] = '';
	if ( !isset($options['include_cat']) )  	$options['include_cat'] = '';
	if ( !isset($options['sort']) ) 	$options['sort'] = 'page_date_publish';
	if ( !isset($options['sort_order']) ) 	$options['sort_order'] = 'desc';
	if ( !isset($options['order']) ) 	$options['order'] = 'desc';
	if ( !isset($options['date_format']) ) 	$options['date_format'] = 'd/m/Y';
	if ( !isset($options['format']) ) 	$options['format'] = '%TITLE%';	
	if ( !isset($options['page_type']) ) 	$options['page_type'] = 'blog';
	
	// вывод самой формы
	$CI = & get_instance();
	$CI->load->helper('form');
		
	$form = '<p><div class="t150">' . t('Заголовок:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'header', 'value'=>$options['header'] ) ) ;
	$form .= '<p><div class="t150">' . t('Формат:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'format', 'value'=>$options['format'] ) ) ;
	
	$form .= '<br /><div class="t150">&nbsp</div> %TITLE% %DATE% %TEXT% %TEXT_CUT%';
	
	
	
	$form .= '<p><div class="t150">' . t('Формат даты:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'date_format', 'value'=>$options['date_format'] ) ) ;
	
	$form .= '<p><div class="t150">' . t('Количество:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'count', 'value'=>$options['count'] ) ) ;
	
	$form .= '<p><div class="t150">' . t('Тип страниц:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'page_type', 'value'=>$options['page_type'] ) ) ;
	
	$form .= '<p><div class="t150">' . t('Исключить рубрики:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'exclude_cat', 'value'=>$options['exclude_cat'] ) ) ;
	
	$form .= '<p><div class="t150">' . t('Включить рубрики:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'include_cat', 'value'=>$options['include_cat'] ) ) ;
	
	$form .= '<p><div class="t150">' . t('Сортировка:', 'plugins') . '</div> '. form_dropdown( $widget . 'sort', array( 'page_date_publish'=>t('По дате', 'plugins'), 'page_title'=>t('По алфавиту', 'plugins')), $options['sort']);
	
	$form .= '<p><div class="t150">' . t('Порядок сортировки:', 'plugins') . '</div> '. form_dropdown( $widget . 'sort_order', array( 'asc'=>t('Прямой', 'plugins'), 'desc'=>t('Обратный', 'plugins')), $options['sort_order']);
	
	return $form;
}


# сюда приходят POST из формы настройки виджета
# имя функции = виджет_update
function last_pages_widget_update($num = 1) 
{

	$widget = 'last_pages_widget_' . $num; // имя для опций = виджет + номер
	
	// получаем опции
	$options = $newoptions = mso_get_option($widget, 'plugins', array());
	
	# обрабатываем POST
	$newoptions['header'] = mso_widget_get_post($widget . 'header');
	$newoptions['format'] = mso_widget_get_post($widget . 'format');
	$newoptions['date_format'] = mso_widget_get_post($widget . 'date_format');
	$newoptions['count'] = (int) mso_widget_get_post($widget . 'count');
	$newoptions['page_type'] = mso_widget_get_post($widget . 'page_type');
	$newoptions['exclude_cat'] = mso_widget_get_post($widget . 'exclude_cat');
	$newoptions['include_cat'] = mso_widget_get_post($widget . 'include_cat');
	$newoptions['sort'] = mso_widget_get_post($widget . 'sort');
	$newoptions['sort_order'] = mso_widget_get_post($widget . 'sort_order');
	
	if ( $options != $newoptions ) 
		mso_add_option($widget, $newoptions, 'plugins');
}


function last_pages_widget_custom($arg = array(), $num = 1)
{
	global $MSO;
	
	if ( !isset($arg['count']) ) 	$arg['count'] = 7;
	if ( !isset($arg['page_type']) )  	$arg['page_type'] = 'blog';
	if ( !isset($arg['sort']) ) 	$arg['sort'] = 'page_date_publish';
	if ( !isset($arg['sort_order']) ) 	$arg['sort_order'] = 'desc';
	if ( !isset($arg['date_format']) ) 	$arg['date_format'] = 'd/m/Y';
	if ( !isset($arg['format']) ) 	$arg['format'] = '%TITLE%';	
	if ( !isset($arg['exclude_cat']) ) 	$arg['exclude_cat'] = '';	
	if ( !isset($arg['include_cat']) ) 	$arg['include_cat'] = '';	
	
	if ( !isset($arg['header']) ) $arg['header'] = '<h2 class="box"><span>' . t('Последние записи', 'plugins') . '</span></h2>';
	if ( !isset($arg['block_start']) ) $arg['block_start'] = '<div class="last-pages"><ul class="is_link">';
	if ( !isset($arg['block_end']) ) $arg['block_end'] = '</ul></div>';
	
	
	$cache_key = 'last_pages_widget'. serialize($arg) . $num;
	$k = mso_get_cache($cache_key);
	if ($k) // да есть в кэше
	{
		$current_url = getinfo('siteurl') . mso_current_url(); // текущий урл
		$k = str_replace( '<a href="' . $current_url . '">', '<a href="' . $current_url . '" class="current_url">', $k);
		return $k; 
	}
	
	$arg['exclude_cat'] = mso_explode($arg['exclude_cat']); // рубрики из строки в массив
	$arg['include_cat'] = mso_explode($arg['include_cat']); // рубрики из строки в массив
	
	$CI = & get_instance();
	
	if (strpos($arg['format'], '%TEXT%') === false and strpos($arg['format'], '%TEXT_CUT%') === false)
		$CI->db->select('page.page_id, page_type_name, page_type_name AS page_content, page_slug, page_title, page_date_publish, page_status', false);
	else	
		$CI->db->select('page.page_id, page.page_content, page_type_name, page_slug, page_title, page_date_publish, page_status');
		
	$CI->db->from('page');
	$CI->db->where('page_status', 'publish');
	$CI->db->where('page_date_publish <', date('Y-m-d H:i:s'));
	
	if ($arg['page_type']) $CI->db->where('page_type_name', $arg['page_type']);
	
	$CI->db->join('page_type', 'page_type.page_type_id = page.page_type_id');
	
	if ($arg['exclude_cat']) // указаны исключающие рубрики
	{
		$CI->db->join('cat2obj', 'cat2obj.page_id = page.page_id', 'left');
		$CI->db->where_not_in('cat2obj.category_id', $arg['exclude_cat']);
	}
	
	if ($arg['include_cat']) // указаны включающие рубрики
	{
		$CI->db->join('cat2obj', 'cat2obj.page_id = page.page_id', 'left');
		$CI->db->where_in('cat2obj.category_id', $arg['include_cat']);
	}	
	
	$CI->db->order_by($arg['sort'], $arg['sort_order']);
	$CI->db->limit($arg['count']);
	
	$query = $CI->db->get();
	
	if ($query->num_rows() > 0)	
	{	
		$pages = $query->result_array();
		
		$out = '';
		foreach ($pages as $key=>$page)
		{
			$out .= $arg['format'];
			
			$out = str_replace('%TITLE%', 
							mso_page_title(mso_slug($page['page_slug']), $page['page_title'], '', '', true, false), $out);
							
			$out = str_replace('%DATE%', 
							mso_page_date($page['page_date_publish'], $arg['date_format'], '', '', false), $out);
			
			$page_content = $page['page_content'];
			$page_content = mso_hook('content', $page_content);
			$page_content = mso_hook('content_auto_tag', $page_content);
			$page_content = mso_hook('content_balance_tags', $page_content);
			$page_content = mso_hook('content_out', $page_content);
			
			$out = str_replace('%TEXT%', mso_balance_tags( mso_auto_tag( mso_hook('content_complete', $page['page_content']) ) ), $out);
			
			# если есть cut, то обрабатываем и его
			if ( preg_match('/\[cut(.*?)?\]/', $page_content, $matches) )
			{
				$page_content = explode($matches[0], $page_content, 2);
				$page_content = $page_content[0];
				$page_content = mso_hook('content_complete', $page_content);
			}
			
			$out = str_replace('%TEXT_CUT%', mso_balance_tags( mso_auto_tag( $page_content ) ), $out);
		}
		
		$out = $arg['header'] . $arg['block_start'] . $out . $arg['block_end'];
		
		mso_add_cache($cache_key, $out); // сразу в кэш добавим
		
		// отметим текущую рубрику. Поскольку у нас к кэше должен быть весь список и не делать кэш для каждого url
		// то мы просто перед отдачей заменяем текущий url на url с li.current_url 
		$current_url = getinfo('siteurl') . mso_current_url(); // текущий урл
		$out = str_replace( '<a href="' . $current_url . '">', '<a href="' . $current_url . '" class="current_url">', $out);
		
		return $out;
	}
}


?>