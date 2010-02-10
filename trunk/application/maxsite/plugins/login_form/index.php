<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */
 

# функция автоподключения плагина
function login_form_autoload($args = array())
{
	# регистрируем виджет
	mso_register_widget('login_form_widget', t('Форма логина', 'plugins')); 
}


# функция, которая берет настройки из опций виджетов
function login_form_widget($num = 1) 
{
	$out = '';
	if (is_login())
	{
		$out = '<p><strong>' . t('Привет,', 'plugins') . ' ' . getinfo('users_nik') 
				. '!</strong><br /> [<a href="' . getinfo('siteurl') 
				. 'logout'.'">' . t('выйти', 'plugins') . '</a>] [<a href="' 
				. getinfo('siteurl') . 'admin">' . t('управление', 'plugins') . '</a>]</p>';	
	}
	elseif ($comuser = is_login_comuser())
	{
		$out = '<p><strong>' . t('Привет,', 'plugins') . ' ' 
				. $comuser['comusers_nik'] . '!</strong><br /> [<a href="' . getinfo('siteurl')
				. 'logout'.'">' . t('выйти', 'plugins') . '</a>] [<a href="' 
				. getinfo('siteurl') . 'users/' . $comuser['comusers_id'] . '">' . t('своя страница', 'plugins') . '</a>]</p>';

	}
	else
	{
		$out = mso_login_form(array( 'login'=>t('Логин (email):', 'plugins') . ' ', 'password'=> t('Пароль:', 'plugins') . ' ', 'submit'=>''), getinfo('siteurl') . mso_current_url(), false);
	}
	
	if ($out)
	{
		$widget = 'login_form_widget_' . $num; // имя для опций = виджет + номер
		$options = mso_get_option($widget, 'plugins', array() ); // получаем опции
		// заменим заголовок, чтобы был в  h2 class="box"
		if ( isset($options['header']) and $options['header'] ) $out = '<h2 class="box"><span>' . $options['header'] . '</span></h2>' . $out;
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
	
	$form = '<p><div class="t150">' . t('Заголовок:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'header', 'value'=>$options['header'] ) ) ;
	
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