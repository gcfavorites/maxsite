<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

mso_cur_dir_lang('admin');

?>
<h1><?= t('Добро пожаловать в MaxSite CMS!') ?></h1>
<br />
<ul>
	<li><a href="http://max-3000.com/"><?= t('Официальный сайт') ?></a></li>
	<li><a href="http://max-3000.com/help"><?= t('Центр помощи') ?></a></li>
	<li><a href="http://forum.maxsite.org/viewforum.php?id=13"><?= t('Форум поддержки') ?></a></li>
	<li><a href="http://code.google.com/p/maxsite/issues/list"><?= t('Google Code (для тестеров)') ?></a></li>
</ul>
<br />
<p><?= t('Ваша версия <strong>MaxSite CMS</strong>') ?>: <?= getinfo('version') ?></p>
<?php
	
	$show_check_version = true;
	$show_clear_cache = true;
	
	if ( $post = mso_check_post(array('f_session_id', 'f_submit_check_version')) )
	{
		mso_checkreferer();
		$show_check_version = false;
		$url = 'http://max-3000.com/uploads/latest.txt';
		$latest = @file($url); // массив
		if (!$latest) 
		{
			echo '<div class="error">'. t('Ошибка соединения с max-3000.com!') . '</div>';
		}
		else
		{
			if ( !isset($latest[0]) or !isset($latest[1]) ) 
			{
				echo '<div class="error">' . t('Полученная информация является ошибочной') . '</div>';
			}
			else
			{
				$info1 = explode('|', $latest[0]);
				echo '<p>' . t('Текущая официальная версия') . ': <a href="' . $info1[2] . '">' . $info1[0] . '</a> (' . $info1[1] . ')</p>';
				
				$info2 = explode('|', $latest[1]);
				
				$build = ($info2[0] * 100 - floor($info2[0] * 100)) * 10;
				$vers = floor($info2[0] * 100) / 100;
				echo '<p>' . t('Latest-версия') . ': <a href="' . $info2[2] . '">' . $vers . ' build ' . $build . '</a> (' . $info2[1] . ')</p>';
				
				if ( $info1[0] > getinfo('version') )
					echo '<p style="margin: 10px 0; font-weight: bold;">' . 
							sprintf( t('Вы можете %sвыполнить обновление'), '<a href="' . $info1[2] . '">' ) . '</a>.</p>';
				else
					echo '<p style="margin: 10px 0; font-weight: bold;">' . t('Обновление не требуется.') . '</p>';
			}
		}
	}
	
	if ( $post = mso_check_post(array('f_session_id', 'f_submit_clear_cache')) )
	{
		mso_checkreferer();
		$show_clear_cache = false;
		mso_flush_cache(); // сбросим кэш
		echo '<p style="margin: 10px 0; font-weight: bold;">' . t('Кэш удален') . '</p><br />';
	}
	
	
	if ($show_check_version or $show_clear_cache)
	{
		echo '<form action="" method="post">' . mso_form_session('f_session_id');
		
		if ($show_check_version)
			echo '<p><input type="submit" name="f_submit_check_version" value="' . t('Проверить последнюю версию MaxSite CMS') . '"></p>';
		
		if ($show_clear_cache)
			echo '<p><input type="submit" name="f_submit_clear_cache" value="' . t('Сбросить кэш системы') . '"></p>';
		
		echo '</form>';
	}
?>
