<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<h1>Шаблоны для сайта</h1>
<p class=""info>Выберите нужный шаблон. Все шаблоны хранятся в каталоге <strong>«maxsite/templates»</strong>. Название шаблона совпадает с названием его каталога.</p>

<?php 
	$CI = & get_instance();
	
	
	if ( $post = mso_check_post(array('f_session_id', 'f_submit')) )
	{
		mso_checkreferer();
		$f_template = mso_array_get_key($post['f_submit']); 
		
		# еще раз проверим есть ли шаблон
		$index = $MSO->config['templates_dir'] . $f_template . '/index.php';
		
		if (file_exists($index))
		{
			mso_add_option('template', $f_template, 'general');
			$MSO->config['template'] = $f_template;
			echo '<div class="update">Обновлено!</div>';
		}
		else
		{
			echo '<div class="error">Ошибка обновления</div>';
		}
	}
	
	
	// получаем список каталогов 
	$CI->load->helper('directory');
	
	$current_template = $MSO->config['template'];
	
	echo '<p>Текущий шаблон: <strong>' . $current_template . '</strong></p>';
	
	
	$templates_dir = $MSO->config['templates_dir'];
	
	// все каталоги в массиве $dirs
	$dirs = directory_map($templates_dir, true);
	
	echo '<form action="" method="post">' . mso_form_session('f_session_id');
	echo '<div style="width: 99%;">';
	foreach ($dirs as $dir)
	{
		
		if ($dir == $current_template) continue;
		
		// обязательный файл index.php
		$index = $templates_dir . $dir . '/index.php';
		
		
		if (file_exists($index))
		{
			$out = '<div style="float: left; margin: 10px; border: 1px silver solid; border-right: 3px gray solid; border-bottom: 3px #676767 solid; padding: 0px 10px; width: 280px; text-align: center; ">';
			$out .= '<h2>' . $dir . '</h2>';
			
			$screenshot = $templates_dir . $dir . '/screenshot.jpg';
			
			if (file_exists($screenshot))
			{
				$screenshot = $MSO->config['templates_url'] . $dir . '/screenshot.jpg';
				$out .= '<img src="' . $screenshot . '" width="250" height="200" alt="" title="" />';
			}
			else
			{
				$out .= '<div style="margin: 0 auto; width: 250px; height: 200px; background: #f0f0f0; border: 1px solid silver;">Нет изображения</div>';
			}
			
						$info_f = $templates_dir . $dir . '/info.php';
			if (file_exists($info_f))
			{
				require($info_f);
				$out .= '<p><a href="' . $info['template_url'] . '">' . $info['name'] . ' ' . $info['version'] . '</a>';
				$out .= '<br />' . $info['description'];
				$out .= '<br />Автор: <a href="' . $info['author_url'] . '">' . $info['author'] . '</a>';
				$out .= '</p>';
			}
			
			$out .= '<input type="submit" name="f_submit[' . $dir . ']" value="Выбрать этот шаблон" style="margin: 10px;" />';
			$out .= '</div>';
			echo $out;
		}
	}

	echo '</div>';
	echo '</form>';
?>