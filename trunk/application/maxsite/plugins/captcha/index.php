<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (с) http://maxsite.org/
 */


# функция автоподключения плагина
function captcha_autoload($args = array())
{	
	if (!is_login())
	{
		mso_hook_add( 'comments_content_end', 'captcha_go'); # хук на отображение картинки
		mso_hook_add( 'comments_new_captcha', 'captcha_new_comment'); # хук на обработку капчи
	}
}

function captcha_new_comment($args = array()) 
{
	global $MSO;
	
	if (isset($_POST['comments_captha']))
	{
		$captcha = $_POST['comments_captha']; // это введенное значение капчи
		// которое должно быть вычисляем как и в img.php
		$char = md5($MSO->data['session']['session_id'] . mso_slug(mso_current_url()));
		$char = str_replace(array('a', 'b', 'c', 'd', 'e', 'f'), array('0', '5', '8', '3', '4', '7'), $char);
		$char = substr( $char, 1, 4);
		return ($captcha == $char); // сравниваем
	}
	else
	{
		return false; // нет капчи, хотя должна быть!
	}
}

# выводим картинку капчи
function captcha_go($args = array()) 
{
	global $MSO;
	
	# сама картинка формируется в img.php
	# в ней мы передаем сессию, текущую страницу и время (против кэширования)
	echo '
			<label for="comments_captha">Введите нижние символы</label>
			<input type="text" name="comments_captha" value="" maxlength="4" class="comments_captha" /> <img src="' 
			. $MSO->config['plugins_url'] . 'captcha/img.php?image='
			. $MSO->data['session']['session_id']
			. '&page='
			. mso_slug(mso_current_url())
			. '&code='
			. time()
			. '" title="Защита от спама: введите только нижние символы" /> <span>(обязательно)</span><br /><br />
		';
}
?>