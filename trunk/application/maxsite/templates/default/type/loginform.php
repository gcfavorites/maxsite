<?php 
	mso_remove_hook( 'body_start', 'demo_body_start');
	mso_remove_hook( 'body_end', 'demo_body_end');

	require(getinfo('template_dir') . 'main-start.php');
	echo '&nbsp';
//	require(getinfo('template_dir') . 'main-end.php');
	
	echo '
	<div class="loginform">
	<p><strong>Введите свой логин и пароль</strong></p><br />
	';
	
	if (!is_login())
	{
		$redirect_url = (isset($_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER'] : getinfo('siteurl');
		
		mso_login_form(array( 
			'login'=>'&nbsp;&nbsp;Логин: ', 
			'password'=>'<br /><br />Пароль: ', 
			'submit'=>'<br /><br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', 
			'submit_value'=>'&nbsp;&nbsp;&nbsp;Войти&nbsp;&nbsp;&nbsp;', 
			'form_end'=>'<br /><br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="' . getinfo('siteurl') . '">Вернуться к сайту</a>'
			), 
			$redirect_url);
	}
	else
	{
		echo '<p>Привет, ' . getinfo('users_nik') . '! [<a href="' . getinfo('siteurl') . 'logout'.'">выйти</a>]</p>';
	}

	echo '</div>';
	
	require(getinfo('template_dir') . 'main-end.php');

?>