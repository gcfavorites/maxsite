<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */
	
	global $MSO;
	
	$CI = & get_instance();	
	
	$step = $MSO->data['step'];
	
	$username = '';
	$userpassword = '';
	$useremail = '';
	$namesite = '';
	$demoposts = 0;
	$error = false;
	
	if ( ($step == 2) and $_POST ) 
	{
		mso_checkreferer(); // проверка на чужой реферер
		
		if ( $_POST['mysubmit'] ) 
		{
			$username = isset ($_POST['username']) ? mso_strip($_POST['username'], true) : false;
			$userpassword = isset ($_POST['userpassword']) ? mso_strip($_POST['userpassword'], true) : false;
			$useremail = $_POST['useremail'] ? mso_strip($_POST['useremail'], true) : false;
			$namesite = isset ($_POST['namesite']) ? mso_strip($_POST['namesite'], true) : false;
			$demoposts = isset ($_POST['demoposts']) ? (int) mso_strip($_POST['demoposts'], true) : 0;
			
			if ( !mso_valid_email($useremail) ) $useremail = false; 
			if ( strlen($userpassword) < 6) $userpassword = false; 
			
			if ( !$useremail or !$username or !$userpassword or !$namesite ) 
			{
				$step = 1;
				$error = '<h2 class="error">Ошибочные или неполные данные!<br />Попробуйте заново</h2>';
			}
			
			if ( $step === 2 ) 
			{
				require_once ('install-common.php');
				$res = mso_install_newsite( array('username'=>$username, 
										   'userpassword'=>mso_md5($userpassword), 
										   'userpassword_orig'=>$userpassword, 
										   'useremail'=>$useremail,
										   'namesite'=>$namesite,
										   'demoposts'=>$demoposts,
										   'ip'=>$_SERVER['REMOTE_ADDR']
										  ) );
				
			}
		}
		else
			$step == 1;
	}
	
	mso_nocache_headers();
	
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<title>Install MaxSite CMS</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta name="generator" content="MaxSite CMS">
	<link rel="shortcut icon" href="<?=getinfo('siteurl')?>favicon.ico" type="image/x-icon">
	<link rel="stylesheet" href="<?=$MSO->data['url_css']?>" type="text/css" media="screen">
</head>
<body>
<div id="container">
<?php if ( $step == 1 ) : // первый шаг ?>
	
	<h1>Добро пожаловать в программу установки <span>MaxSite CMS</span></h1>
	<?=$error?>
	<?php 
		
		$this->load->helper('form');

		echo form_open('install/2', array('class' => 'myform', 'id' => 'myform'));
		
		echo '<p>Ник админа (по английски, без пробелов)<br />' 
			. form_input( array( 'name'=>'username', 
								'id'=>'username', 
								'value'=>$username,
								'maxlength'=>'100',
								'size'=>'50',
								'style'=>'width:80%') );
	
		echo '<p>Пароль (по английски, без пробелов, минимум 6 символов)<br />' 
			. form_input( array( 'name'=>'userpassword', 
								'id'=>'userpassword', 
								'value'=>$userpassword,
								'maxlength'=>'100',
								'size'=>'50',
								'style'=>'width:80%') );	
	
		echo '<p>E-mail (рабочий!)<br />' 
			. form_input( array( 'name'=>'useremail', 
								'id'=>'useremail', 
								'value'=>$useremail,
								'maxlength'=>'100',
								'size'=>'50',
								'style'=>'width:80%') );
						
		echo '<p>Название сайта<br />' 
			. form_input(array( 'name'=>'namesite', 
								'id'=>'namesite', 
								'value'=>$namesite,
								'maxlength'=>'100',
								'size'=>'50',
								'style'=>'width:80%') );						
							
		echo '<p><label>' . form_checkbox('demoposts', '1', $demoposts) . ' Установить демонстрационные данные</label></p>';
		
		// echo '<br />';
		
		// сразу выполним проверку на все права файла 
		// 
		$show_button = true;
		echo '<div class="proverka">';
			
			if (file_exists( $MSO->config['base_dir'] . 'mso_config.php' )) 
			{
				# echo '<p class="ok">Файл mso_config.php - OK!</p>';
			}
			else
			{
				echo '<p class="error">Файл <em>' . $MSO->config['base_dir'] . 'mso_config.php' . '</em> - не найден!</p>';
				$show_button = false;
			}	
			
			
			$cache_path = getinfo('cache_dir');
			if ( !is_dir($cache_path) or !is_writable($cache_path))
			{
				echo '<p class="error">Каталог <em>' . $cache_path . '</em> - не найден или нет разрешения на запись (777)!</p>';
				$show_button = false;
			}
			else
			{
				# echo '<p class="ok">Каталог кэша - OK!</p>';
			}
			
			$path = getinfo('uploads_dir');
			if ( !is_dir($path) or !is_writable($path))
			{
				echo '<p class="error">Каталог <em>' . $path . '</em> - не найден или нет разрешения на запись (777)!</p>';
				$show_button = false;
			}
			else
			{
				# echo '<p class="ok">Каталог «uploads» - OK!</p>';
			}
			// в uploads _mso_float
			if ( !is_dir($path . '_mso_float') or !is_writable($path . '_mso_float'))
			{
				echo '<p class="error">Каталог <em>' . $path . '_mso_float' . '</em> - не найден или нет разрешения на запись (777)!</p>';
				$show_button = false;
			}
			else
			{
				# echo '<p class="ok">Каталог «uploads/_mso_float» - OK!</p>';
			}
			// в uploads _mso_i
			if ( !is_dir($path . '_mso_i') or !is_writable($path . '_mso_i'))
			{
				echo '<p class="error">Каталог <em>' . $path . '_mso_i' . '</em> - не найден или нет разрешения на запись (777)!</p>';
				$show_button = false;
			}
			else
			{
				# echo '<p class="ok">Каталог «uploads/_mso_i» - OK!</p>';
			}			
			
			# CodeIgniter 1.7.1
			# $path = realpath(dirname(FCPATH)) . '/.htaccess';
			
			$path = FCPATH . '/.htaccess';
			if ( !file_exists($path))
			{
				echo '<p class="error">Файл <em>' . $path . '</em> - не найден!</p>';
				$show_button = false;
			}
			else
			{
				# echo '<p class="ok">Файл «.htaccess» - OK!</p>';
			}
			
			
			$path = FCPATH . '/sitemap.xml';
			if ( !file_exists($path) or !is_writable($path))
			{
				echo '<p class="error">Файл <em>' . $path . '</em> - не найден или нет разрешения на запись!</p>';
				$show_button = false;
			}
			else
			{
				# echo '<p class="ok">Файл «sitemap.xml» - OK!</p>';
			}			
						
		
			if ($show_button) echo '<p class="ok">Проверка выполнена</p>';
			
		echo '</div>';
		
		if ($show_button) echo form_submit('mysubmit', 'Установить!', 'id="mysubmit"');
			else echo '<p style="color: red;"><strong>Исправьте замечания и обновите эту страницу (F5)</strong></p>';
		
		echo form_close();

	?>
	
<?php 
	endif; // конец первого шага
	if ($step == 2 ) : // второй шаг
	
	$text = 'Ваш новый сайт создан: ' . getinfo('siteurl') . NR;
	$text .= 'Для входа воспользуйтесь данными:' . NR;
	$text .= 'Логин: ' . $username . NR;
	$text .= 'Пароль: ' . $userpassword . NR . NR . NR;
	$text .= 'Сайт поддержки: http://max-3000.com/';
	
	mso_flush_cache(); // сбросим кэш
?>

	<h1>Поздравляем! Всё готово!</h1>
	<h2>Ваша информация:</h2>
	<?= $res ?>
	<p><a href="<?= getinfo('siteurl') ?>">Переход к сайту</a></p>
	<p>Не забудьте открыть файл «application/maxsite/mso_config.php» и измените на <em>$mso_install = true;</em></p>
	<?php 
		// поскольку это инсталяция, то отправитель - тот же email
		@mso_mail($useremail, 'Новый сайт на MaxSite CMS', $text, $useremail); 
	?>
<?php endif; // конец второго шага ?>
</div><!-- div id="container" -->
</body>
</html>