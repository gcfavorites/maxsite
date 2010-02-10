<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
?>

<h1><?= t('Добро пожаловать в MaxSite CMS!', 'admin') ?></h1>
<br />
<ul>
	<li><a href="http://max-3000.com/"><?= t('Официальный сайт', 'admin') ?></a></li>
	<li><a href="http://max-3000.com/help"><?= t('Центр помощи', 'admin') ?></a></li>
	<li><a href="http://forum.maxsite.org/viewforum.php?id=13"><?= t('Форум поддержки', 'admin') ?></a></li>
	<li><a href="http://code.google.com/p/maxsite/issues/list"><?= t('Google Code (для тестеров)', 'admin') ?></a></li>
</ul>
<br />
<p>Ваша версия <strong>MaxSite CMS</strong>: <?= getinfo('version') ?></p>
<?php
	if ( $post = mso_check_post(array('f_session_id', 'f_submit')) )
	{
		mso_checkreferer();
		
		$url = 'http://max-3000.com/uploads/latest.txt';
		$latest = @file($url); // массив
		if (!$latest) echo '<div class="error">Ошибка соединения с max-3000.com</div>';
		else
		{
			if ( !isset($latest[0]) or !isset($latest[1]) ) echo '<div class="error">Полученная информация является ошибочной</div>';
			else
			{
				$info1 = explode('|', $latest[0]);
				echo '<p>Текущая официальня версия: <a href="' . $info1[2] . '">' . $info1[0] . '</a> (' . $info1[1] . ')</p>';
				
				$info2 = explode('|', $latest[1]);
				
				$build = ($info2[0] * 100 - floor($info2[0] * 100)) * 10;
				$vers = floor($info2[0] * 100) / 100;
				echo '<p>Latest-версия: <a href="' . $info2[2] . '">' . $vers . ' build ' . $build . '</a> (' . $info2[1] . ')</p>';
				
				if ( $info1[0] > getinfo('version') )
					echo '<p><strong>Вы можете <a href="' . $info1[2] . '">выполнить обновление</a>.</strong></p>';
				else
					echo '<p>Обновление не требуется.</p>';
			}
		}
	}
	else
	{
		echo '<form action="" method="post">' . mso_form_session('f_session_id');
		echo '<input type="submit" name="f_submit" value="Проверить последнюю версию MaxSite CMS">';
		echo '</form>';
	}
?>
