<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MaxSite CMS
 * (c) http://maxsite.org/
 */
 
	$admin_css = getinfo('admin_url') . 'template/' . mso_get_option('admin_template', 'general', 'default') . '/style.css';
	$admin_css = mso_hook('admin_css', $admin_css);
	$admin_title = mso_hook('admin_title',mso_head_meta('title'));
	
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html><head>
<title><?=$admin_title?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link rel="shortcut icon" href="<?=getinfo('siteurl')?>favicon.ico" type="image/x-icon">
<link rel="stylesheet" href="<?=$admin_css?>" type="text/css" media="screen">
</head>
<body>
<h1 id="loginlogo"><a href="http://max-3000.com/" title="Работает на MaxSite CMS">MaxSite CMS</a></h1>
<div id="login_block">
	<p><strong>Введите свой логин и пароль</strong></p><br />

<?php 
	if (!is_login())
	{
		$redirect_url = (isset($_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER'] : getinfo('siteurl') . mso_current_url();
		
		mso_login_form(array( 
			'login'=>'&nbsp;&nbsp;Логин: ', 
			'password'=>'<br /><br />Пароль: ', 
			'submit'=>'<br /><br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', 'submit_value'=>'&nbsp;&nbsp;&nbsp;Войти&nbsp;&nbsp;&nbsp;',
			'form_end'=>'<br /><br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="' . getinfo('siteurl') . '">Вернуться к сайту</a>',
			
			), 
			$redirect_url);
	}
?>

</div>
</body>
</html>