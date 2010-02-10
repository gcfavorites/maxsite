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
	if (!is_login())
				return mso_login_form(array( 'login'=>'Логин: ', 'password'=>'Пароль: ', 'submit'=>''), '', false);
			else
				return '<p><strong>Привет, ' . getinfo('users_nik') . '!</strong><br /> [<a href="' . getinfo('siteurl') 
						. 'logout'.'">выйти</a>] [<a href="' . getinfo('siteurl') . 'admin">управление</a>]</p>';
}


# форма настройки виджета 
# имя функции = виджет_form
function login_form_widget_form($num = 1) 
{
	return '';
}


?>