<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
	
	mso_cur_dir_lang('admin');
	
	$CI = & get_instance();
	
	if ( $post = mso_check_post(array('f_session_id')) )
	{
		mso_checkreferer();
		
		// есть ли выбранные пункты?
		if (isset($post['f_check_submit']))
		{
			// определяем действие
			if (isset($post['f_activate_submit'])) $act = 'activate';
				elseif (isset($post['f_deactivate_submit'])) $act = 'deactivate';
				elseif (isset($post['f_uninstall_submit'])) $act = 'uninstall';
				else $act = false;
			
			if ($act)
			{
				$out = '' . t('Выполнено:', 'admin') . ' ';
				foreach ($post['f_check_submit'] as $f_name=>$val)
				{
					if ($act == 'activate') mso_plugin_activate($f_name); # активация плагина
					elseif ($act == 'deactivate') mso_plugin_deactivate($f_name); # деактивация плагина
					elseif ($act == 'uninstall') mso_plugin_uninstall($f_name); # унинстал 
					$out .= ' &#149; ' . $f_name;
				}
				mso_redirect('admin/plugins');
				// mso_admin_menu();
				// echo '<div class="update">' . $out . ' &#149;</div>';
			}
			else
				echo '<div class="error">' . t('Ошибка обновления', 'admin') . '</div>';
		}
		else
			echo '<div class="error">' . t('Отметьте необходимые плагины', 'admin') . '</div>';
	}

?>
	<h1><?= t('Плагины') ?></h1>
	<p class="info"><?= t('Плагины расширяют стандартные возможности сайта. Здесь вы можете включить или отключить плагины. Если вы деинсталируете плагин, то это удаляет его настройки, что позволяет избежать «замусоривания» базы данных.') ?></p>
	<p class="info"><?= t('Отметьте необходимые плагины и выберите нужное действие.') ?></p>

<?php
	// для вывода будем использовать html-таблицу
	$CI->load->library('table');
	
	$tmpl = array (
					'table_open'		  => '<table class="page" border="0" width="99%">',
					'row_alt_start'		  => '<tr class="alt">',
					'cell_alt_start'	  => '<td class="alt">',
			  );

	$CI->table->set_template($tmpl); // шаблон таблицы
	
	// заголовки
	$CI->table->set_heading(t('Статус', 'admin'), ' ', t('Каталог', 'admin'), t('Название', 'admin'), t('Версия', 'admin'), t('Автор', 'admin'), t('Описание', 'admin'));
	
	// проходимся по каталогу плагинов и выводим информацию о них
	$CI->load->helper('directory');
	
	$plugins_dir = $MSO->config['plugins_dir'];
	
	// все каталоги в массиве $dirs
	$dirs = directory_map($plugins_dir, true);
	
	# пересортируем элементы масива так чтобы активные плагины из 
	# $MSO->active_plugins оказались вверху
	$dirs = array_unique(array_merge($MSO->active_plugins, $dirs));
	
	foreach ($dirs as $dir)
	{
		$info_f = $plugins_dir . $dir . '/info.php';
		
		if (file_exists($info_f))
		{
			require($info_f);
			
			if (isset( $info )) 
			{
				/* 
				    [name] => Demo
					[description] => Демонстрационный плагин
					[version] => 1.0
					[author] => Максим
					[plugin_url] => http://maxsite.org/
					[author_url] => http://maxsite.org/
					[group] => template
				*/
				
				$name = isset($info['name']) ? mso_strip($info['name']) : '';
				$version = isset($info['version']) ? $info['version'] : '';
				$description = isset($info['description']) ? $info['description'] : '';
				$author = isset($info['author']) ? mso_strip($info['author']) : '';
				$author_url = isset($info['author_url']) ? $info['author_url'] : false;
				$plugin_url = isset($info['plugin_url']) ? $info['plugin_url'] : false;
				
				if ($author_url) $author = '<a href="' . $author_url . '">' . $author . '</a>';
				if ($plugin_url) $name = '<a href="' . $plugin_url . '">' . $name . '</a>';
				
				$act = '<input type="checkbox" name="f_check_submit[' . $dir . ']">';
				
				if ( in_array($dir, $MSO->active_plugins)) 
				{
					$status = '<span style="color: green;"><strong>' . t('вкл', 'admin') . '</strong></span>';
				}
				else 
				{
					$status = '<span class="gray">' . t('откл', 'admin') . '</span>';
					$description = '<span class="gray">' . $description . '</span>';
					$dir = '<span class="gray">' . $dir . '</span>';
					$version = '<span class="gray">' . $version . '</span>';
					$name = '<span class="gray">' . $name . '</span>';
					$author = '<span class="gray">' . $author . '</span>';
					
				}
				
				
				
				$CI->table->add_row($status, $act, $dir, $name, $version, $author, $description);
			}
		}
	}
	
	# добавим строчку для дополнительного действия
	$dop = '<div style="margin: 10px 0;">
				<input type="submit" name="f_activate_submit" value="&nbsp;+ &nbsp;&nbsp;' . t('Включить', 'admin') . '&nbsp;">
				<input type="submit" name="f_deactivate_submit" value="&nbsp;- &nbsp;&nbsp;' . t('Выключить', 'admin') . '&nbsp;">
				<input type="submit" name="f_uninstall_submit" value="&nbsp;x&nbsp;&nbsp;' . t('Деинсталировать', 'admin') . '&nbsp;">
	</div>';
	
	// добавляем форму, а также текущую сессию
	echo '<form action="" method="post">' . mso_form_session('f_session_id');
	echo $CI->table->generate(); // вывод подготовленной таблицы
	echo $dop;
	echo '</form>';
	
?>