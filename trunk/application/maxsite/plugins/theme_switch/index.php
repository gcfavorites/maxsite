<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */


# функция автоподключения плагина
function theme_switch_autoload($args = array())
{
	mso_create_allow('theme_switch_edit', t('Админ-доступ к редактированию Theme switch', __FILE__));
	mso_hook_add( 'admin_init', 'theme_switch_admin_init'); # хук на админку
	mso_register_widget('theme_switch_widget', t('Theme switch', __FILE__)); # регистрируем виджет
	mso_hook_add( 'init', 'theme_switch_init'); # хук на init
}


# функция выполняется при init
function theme_switch_init($args = array())
{	
	global $MSO;

	// проверяем есть ли post
	if ( $post = mso_check_post(array('f_session_id', 'f_theme_switch_submit', 'theme_switch_radio')) )
	{
		mso_checkreferer();
		
		$dir = $post['theme_switch_radio'][0]; // каталог шаблона
		
		// если он есть - проверяем, то пишем куку и редиректимся
		if (file_exists( $MSO->config['templates_dir'] . $dir . '/index.php' )) // есть
		{	
			$opt = mso_get_option('theme_switch', 'plugins', array());
			if ( isset($opt['templates'][$dir]) ) 
			{ 
				// 30 дней = 2592000 секунд
				mso_add_to_cookie('theme_switch', $dir, time() + 60 * 60 * 24 * 30, true);
			}
		}
	}
	
	// проверяем существование куки theme_switch
	if (isset($_COOKIE['theme_switch'])) 
	{
		$dir = $_COOKIE['theme_switch']; // значения текущего кука
		if (file_exists( $MSO->config['templates_dir'] . $dir . '/index.php' )) 
		{
			$opt = mso_get_option('theme_switch', 'plugins', array());
			if ( isset($opt['templates'][$dir]) ) 
			{
				$MSO->config['template'] = $dir;
			}
			else @setcookie('theme_switch', '', time()); // сбросили куку
		}
		else @setcookie('theme_switch', '', time()); // сбросили куку
	}

	return $args;
}

# функция выполняется при деинсталяции плагина
function theme_switch_uninstall($args = array())
{	
	mso_delete_option_mask('theme_switch_widget_', 'plugins'); // удалим созданные опции
	mso_delete_option('theme_switch', 'plugins'); // удалим созданные опции
	mso_remove_allow('theme_switch_edit'); // удалим созданные разрешения
	
	return $args;
}

# функция выполняется при указаном хуке admin_init
function theme_switch_admin_init($args = array()) 
{
	if ( mso_check_allow('theme_switch_edit') ) 
	{
		$this_plugin_url = 'theme_switch'; // url и hook
		
		# добавляем свой пункт в меню админки
		# первый параметр - группа в меню
		# второй - это действие/адрес в url - http://сайт/admin/demo
		#			можно использовать добавочный, например demo/edit = http://сайт/admin/demo/edit
		# Третий - название ссылки	
		
		mso_admin_menu_add('plugins', $this_plugin_url, 'Theme switch');

		# прописываем для указаного admin_url_ + $this_plugin_url - (он будет в url) 
		# связанную функцию именно она будет вызываться, когда 
		# будет идти обращение по адресу http://сайт/admin/theme_switch
		mso_admin_url_hook ($this_plugin_url, 'theme_switch_admin_page');
	}
	
	return $args;
}

# функция вызываемая при хуке, указанном в mso_admin_url_hook
function theme_switch_admin_page($args = array()) 
{
	global $MSO;
	
	# выносим админские функции отдельно в файл
	if ( !mso_check_allow('theme_switch_edit') ) 
	{
		echo t('Доступ запрещен', 'plugins');
		return $args;
	}
	# выносим админские функции отдельно в файл
	mso_hook_add_dinamic( 'mso_admin_header', ' return $args . "' . t('Theme switch', __FILE__) . '"; ' );
	mso_hook_add_dinamic( 'admin_title', ' return "' . t('Theme switch', __FILE__) . ' - " . $args; ' );
	require($MSO->config['plugins_dir'] . 'theme_switch/admin.php');
}


# функция, которая берет настройки из опций виджетов
function theme_switch_widget($num = 1) 
{
	$widget = 'theme_switch_widget_' . $num; // имя для опций = виджет + номер
	$options = mso_get_option($widget, 'plugins', array() ); // получаем опции
	
	// заменим заголовок, чтобы был в  h2 class="box"
	if ( isset($options['header']) and $options['header'] ) 
		$options['header'] = '<h2 class="box"><span>' . $options['header'] . '</span></h2>';
	else $options['header'] = '';
	
	return theme_switch_widget_custom($options, $num);
}


# форма настройки виджета 
# имя функции = виджет_form
function theme_switch_widget_form($num = 1) 
{
	$widget = 'theme_switch_widget_' . $num; // имя для формы и опций = виджет + номер
	
	// получаем опции 
	$options = mso_get_option($widget, 'plugins', array());
	
	if ( !isset($options['header']) ) $options['header'] = '';
	if ( !isset($options['submit']) ) $options['submit'] = t('Переключить', 'plugins');;
	
	// вывод самой формы
	$CI = & get_instance();
	$CI->load->helper('form');
		
	$form = '<p><div class="t150">' . t('Заголовок:', 'plugins') . '</div> '. 
			form_input( array( 'name'=>$widget . 'header', 'value'=>$options['header'] ) ) ;
	$form .= '<p><div class="t150">' . t('Надпись на кнопке:', 'plugins') . '</div> '. 
			form_input( array( 'name'=>$widget . 'submit', 'value'=>$options['submit'] ) ) ;			
	
	return $form;
}


# сюда приходят POST из формы настройки виджета
# имя функции = виджет_update
function theme_switch_widget_update($num = 1) 
{
	$widget = 'theme_switch_widget_' . $num; // имя для опций = виджет + номер
	
	// получаем опции
	$options = $newoptions = mso_get_option($widget, 'plugins', array());
	
	# обрабатываем POST
	$newoptions['header'] = mso_widget_get_post($widget . 'header');
	$newoptions['submit'] = mso_widget_get_post($widget . 'submit');
	
	if ( $options != $newoptions ) 
		mso_add_option($widget, $newoptions, 'plugins');
}

# функция виджета
function theme_switch_widget_custom($options = array(), $num = 1)
{
	if ( !isset($options['header']) ) $options['header'] = '';
	if ( !isset($options['submit']) ) $options['submit'] = t('Переключить', 'plugins');
	
	// выводим списком шаблоны, которые отмечены и сохранены в опции theme_switch (через admin.php)
	$opt = mso_get_option('theme_switch', 'plugins', array());
	if ( !isset($opt['templates']) ) $opt['templates'] = array(); 
	
	$current_template = getinfo('template');
	
	$out = '';
	foreach($opt['templates'] as $key=>$val)
	{
		if ( $key == $current_template ) $checked = 'checked="checked"';
			else $checked = '';
					
		$out .= '<input type="radio" name="theme_switch_radio[]" value="' . $key . '" id="theme_switch_radio_' . $key . '" ' 
				. $checked . '/> <label for="theme_switch_radio_' . $key . '" title="' . $key . '">' . $val . '</label><br />';
	}
	
	if ($out) 
		$out = '<div class="theme_switch">' 
			. $options['header'] 
			. '<form action="" method="post">' 
			. mso_form_session('f_session_id') . $out 
			. '<input type="submit" name="f_theme_switch_submit" class="submit" value="' . $options['submit'] . '" /></form></div>';
	
	return $out;	
}


?>