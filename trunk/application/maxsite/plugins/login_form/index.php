<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://maxsite.org/
 */
 

# функция автоподключения плагина
function login_form_autoload($args = array())
{
	# регистрируем виджет
	mso_register_widget('login_form_widget', 'Форма логина'); 
}


# функция, которая берет настройки из опций виджетов
function login_form_widget($num = 1) 
{
	$out = '';
	if (is_login())
	{
		$out = '<p><strong>Привет, ' . getinfo('users_nik') . '!</strong><br /> [<a href="' . getinfo('siteurl') 
						. 'logout'.'">выйти</a>] [<a href="' . getinfo('siteurl') . 'admin">управление</a>]</p>';	
	}
	elseif ($comuser = is_login_comuser())
	{
		$out = '<p><strong>Привет, ' . $comuser['comusers_nik'] . '!</strong><br /> [<a href="' . getinfo('siteurl')
		. 'logout'.'">выйти</a>] [<a href="' . getinfo('siteurl') . 'users/' . $comuser['comusers_id'] . '">своя страница</a>]</p>';

	}
	else
	{
		$out = mso_login_form(array( 'login'=>'Логин (email): ', 'password'=>'Пароль: ', 'submit'=>''), getinfo('siteurl'), false);
	}
	
	if ($out)
	{
		$widget = 'login_form_widget_' . $num; // имя для опций = виджет + номер
		$options = mso_get_option($widget, 'plugins', array() ); // получаем опции
		// заменим заголовок, чтобы был в  h2 class="box"
		if ( isset($options['header']) and $options['header'] ) $out = '<h2 class="box">' . $options['header'] . '</h2>' . $out;
	}
	
	return $out;
}


# форма настройки виджета 
# имя функции = виджет_form
function login_form_widget_form($num = 1) 
{
	$widget = 'login_form_widget_' . $num; // имя для формы и опций = виджет + номер
	
	// получаем опции 
	$options = mso_get_option($widget, 'plugins', array());
	
	if ( !isset($options['header']) ) $options['header'] = '';
	
	// вывод самой формы
	$CI = & get_instance();
	$CI->load->helper('form');
	
	$form = '<p><div class="t150">Заголовок:</div> '. form_input( array( 'name'=>$widget . 'header', 'value'=>$options['header'] ) ) ;
	
	return $form;
}


# сюда приходят POST из формы настройки виджета
# имя функции = виджет_update
function login_form_widget_update($num = 1) 
{
	$widget = 'login_form_widget_' . $num; // имя для опций = виджет + номер
	
	// получаем опции
	$options = $newoptions = mso_get_option($widget, 'plugins', array());
	
	# обрабатываем POST
	$newoptions['header'] = mso_widget_get_post($widget . 'header');
	
	if ( $options != $newoptions ) 
		mso_add_option($widget, $newoptions, 'plugins');
}

?>