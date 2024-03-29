<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

%%% - замените на имя плагина


# функция автоподключения плагина
function %%%_autoload()
{
	mso_hook_add( 'admin_init', '%%%_admin_init'); # хук на админку
	mso_register_widget('%%%_widget', t('%%%', __FILE__)); # регистрируем виджет
}

# функция выполняется при активации (вкл) плагина
function %%%_activate($args = array())
{	
	mso_create_allow('%%%_edit', t('Админ-доступ к настройкам', 'plugins') . ' ' . t('%%%', __FILE__));
	return $args;
}


# функция выполняется при деинсталяции плагина
function %%%_uninstall($args = array())
{	
	mso_delete_option_mask('%%%_widget_', 'plugins'); // удалим созданные опции
	mso_remove_allow('%%%_edit'); // удалим созданные разрешения
	return $args;
}

# функция выполняется при указаном хуке admin_init
function %%%_admin_init($args = array()) 
{
	if ( mso_check_allow('%%%_edit') ) 
	{
		$this_plugin_url = '%%%'; // url и hook
		
		# добавляем свой пункт в меню админки
		# первый параметр - группа в меню
		# второй - это действие/адрес в url - http://сайт/admin/demo
		#			можно использовать добавочный, например demo/edit = http://сайт/admin/demo/edit
		# Третий - название ссылки	
		
		mso_admin_menu_add('plugins', $this_plugin_url, t('Плагин %%%', __FILE__));

		# прописываем для указаного admin_url_ + $this_plugin_url - (он будет в url) 
		# связанную функцию именно она будет вызываться, когда 
		# будет идти обращение по адресу http://сайт/admin/%%%
		mso_admin_url_hook ($this_plugin_url, '%%%_admin_page');
	}
	
	return $args;
}

# функция вызываемая при хуке, указанном в mso_admin_url_hook
function %%%_admin_page($args = array()) 
{
	if ( !mso_check_allow('%%%_edit') ) 
	{
		echo t('Доступ запрещен', 'plugins');
		return $args;
	}

	# выносим админские функции отдельно в файл
	mso_hook_add_dinamic( 'mso_admin_header', ' return $args . "' . t('%%%', __FILE__) . '"; ' );
	mso_hook_add_dinamic( 'admin_title', ' return "' . t('%%%', __FILE__) . ' - " . $args; ' );
	require(getinfo('plugins_dir') . '%%%/admin.php');
}


# функция, которая берет настройки из опций виджетов
function %%%_widget($num = 1) 
{
	$widget = '%%%_widget_' . $num; // имя для опций = виджет + номер
	$options = mso_get_option($widget, 'plugins', array() ); // получаем опции
	
	// заменим заголовок, чтобы был в  h2 class="box"
	if ( isset($options['header']) and $options['header'] ) 
		$options['header'] = mso_get_val('widget_header_start', '<h2 class="box"><span>') . $options['header'] . mso_get_val('widget_header_end', '</span></h2>');
	else $options['header'] = '';
	
	return %%%_widget_custom($options, $num);
}


# форма настройки виджета 
# имя функции = виджет_form
function %%%_widget_form($num = 1) 
{
	$widget = '%%%_widget_' . $num; // имя для формы и опций = виджет + номер
	
	// получаем опции 
	$options = mso_get_option($widget, 'plugins', array());
	
	if ( !isset($options['header']) ) $options['header'] = '';
	
	// вывод самой формы
	$CI = & get_instance();
	$CI->load->helper('form');
		
	$form = '<p><div class="t150">' . t('Заголовок:', 'plugins') . '</div> '. 
			form_input( array( 'name'=>$widget . 'header', 'value'=>$options['header'] ) ) ;
	
	return $form;
}


# сюда приходят POST из формы настройки виджета
# имя функции = виджет_update
function %%%_widget_update($num = 1) 
{
	$widget = '%%%_widget_' . $num; // имя для опций = виджет + номер
	
	// получаем опции
	$options = $newoptions = mso_get_option($widget, 'plugins', array());
	
	# обрабатываем POST
	$newoptions['header'] = mso_widget_get_post($widget . 'header');
	
	if ( $options != $newoptions ) 
		mso_add_option($widget, $newoptions, 'plugins');
}

# функции плагина
function %%%_widget_custom($options = array(), $num = 1)
{
	// кэш 
	$cache_key = '%%%_widget_custom' . serialize($options) . $num;
	$k = mso_get_cache($cache_key);
	if ($k) return $k; // да есть в кэше
	
	$out = '';
	if ( !isset($options['header']) ) $options['header'] = '';
	


	
	mso_add_cache($cache_key, $out); // сразу в кэш добавим
	
	return $out;	
}


# end file