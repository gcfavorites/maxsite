<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<h1>Файлы</h1>
<p class="info">Здесь вы можете выполнить необходимые операции с файлами.</p>

<?php
	
	$CI = & get_instance();
	
	# удаление выделенных файлов
	if ( $post = mso_check_post(array('f_session_id', 'f_check_files', 'f_delete_submit')) )
	{
		mso_checkreferer();
		// pr($post);
		
		foreach ($post['f_check_files'] as $file)
		{
			@unlink($MSO->config['uploads_dir'] . $file);
		}
		echo '<div class="update">Выполнено</div>';
	}
	
	# загрузка нового файла
	if ( $post = mso_check_post(array('f_session2_id', 'f_upload_submit')) )
	{
		mso_checkreferer();
		
		$config['upload_path'] = $MSO->config['uploads_dir'];
		$config['allowed_types'] = 'gif|jpg|png|zip|txt|rar|html|htm|css';
		$config['max_size'] = '100';
		$config['max_width'] = '1024';
		$config['max_height'] = '768';
		
		$CI->load->library('upload', $config);
		
		$res = $CI->upload->do_upload('f_userfile');
		
		if ($res)
		{
			# pr($CI->upload->data()); // данные загруженого файла
			echo '<div class="update">Выполнено</div>';
		}
		else
		{
			$er = $CI->upload->display_errors();
			echo '<div class="error">Ошибка загрузки файла.' . $er . '</div>';
		}
	}	
	

	$CI->load->library('table');
	$CI->load->helper('directory');
	$CI->load->helper('form');
	

	echo '
		<div style="margin: 20px 0; background: #E0E0FF; padding: 5px 10px 15px 10px; border: 1px solid gray;">
		<h2>Загрузка файла</h2>
		<p>Для загрузки файла нажмите кнопку «Обзор», выберите файл на своем компьютере. После этого нажмите кнопку «Загрузить». Размер файла не должен превышать 100Кб. Изображения не должны быть больше 1024x768px.</p>
		<form action="" method="post" enctype="multipart/form-data">'
		. mso_form_session('f_session2_id') . 
		'<input type="file" name="f_userfile" size="60" />
		&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" name="f_upload_submit" value="Загрузить" />
		</form>
		</div>
		';
	
	
	
	
	$tmpl = array (
					'table_open'		  => '<table class="page" border="0" width="100%"><colgroup width="300"><colgroup width="100">',
					'heading_row_start'	  => '<tr>',
					'heading_row_end'	  => '</tr>',
					'heading_cell_start'  => '<th  style="background: #808080;">',
					'heading_cell_end'	  => '</th>',
					'row_start'			  => '<tr style="background: #E8E8E8;">',
					'row_end'			  => '</tr>',
					'cell_start'		  => '<td>',
					'cell_end'			  => '</td>',
					'row_alt_start'		  => '<tr style="background: #F2F2FF;">',
					'row_alt_end'		  => '</tr>',
					'cell_alt_start'	  => '<td>',
					'cell_alt_end'		  => '</td>',
					'table_close'		  => '</table>'
			  );

	$CI->table->set_template($tmpl); // шаблон таблицы
	
	// заголовки
	$CI->table->set_heading('Файл', '', 'Коды для вставки');
	
	// проходимся по каталогу аплоада и выводим их списком
	
	$uploads_dir = $MSO->config['uploads_dir'];
	
	// все файлы в массиве $dirs
	$dirs = directory_map($uploads_dir, false);
	sort($dirs);
	foreach ($dirs as $file)
	{
		
		if (!is_scalar($file)) continue; // это каталог
		
		$sel =  form_checkbox('f_check_files[]', $file, false) . ' <b>' . $file . '</b>';
		
		$cod1 = stripslashes(htmlspecialchars( $MSO->config['uploads_url'] . $file ) );
		$cod = '<p><input type="text" style="width: 99%;" value="' . $cod1 . '" />';
		
		$cod2 = stripslashes(htmlspecialchars( '<a href="' . $MSO->config['uploads_url'] . $file . '">' . $file . '</a>') );
		$cod .= '<p><input type="text" style="width: 99%;" value="' . $cod2 . '" />';
		
		if ( strpos($file, '.jpg') or strpos($file, '.gif') or strpos($file, '.png') )
		{
			$cod3 = stripslashes(htmlspecialchars( '<a href="' . $MSO->config['uploads_url'] . $file . '"><img src="' . $MSO->config['uploads_url'] . $file . '" /></a>') );
			$cod .= '<p><input type="text" style="width: 99%;" value="' . $cod3 . '" />';
			
			$predpr = '<a rel="lightbox" href="' . $MSO->config['uploads_url'] . $file . '" target="_blank"><img style="width: 98px;" src="' . $MSO->config['uploads_url'] . $file . '" /></a>';
			
		}
		else $predpr = '';
		
		$CI->table->add_row($sel, $predpr, $cod);
	}
	
	// добавляем форму, а также текущую сессию
	echo '<form action="" method="post">' . mso_form_session('f_session_id');
	echo $CI->table->generate(); // вывод подготовленной таблицы
	echo '<br /><input type="submit" name="f_delete_submit" value="&nbsp;Удалить&nbsp;" onClick="if(confirm(\'Выделенные файы будут безвозвратно удалены! Удалять?\')) {return true;} else {return false;}" >';
	echo '</form>';
	
?>