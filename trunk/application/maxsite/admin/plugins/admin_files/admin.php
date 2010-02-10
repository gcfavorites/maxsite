<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<h1>Файлы</h1>
<p class="info">Здесь вы можете выполнить необходимые операции с файлами.</p>

<?php
	
	$CI = & get_instance();
	
	// разрешенные типы файлов
	$allowed_types = 'gif|jpg|jpeg|png|zip|txt|rar|doc|rtf|pdf|html|htm|css|xml|odt|flv|swf|mp3|wav';
	
	
	$CI->load->helper('file'); // хелпер для работы с файлами
	$CI->load->library('table');
	$CI->load->helper('directory');
	$CI->load->helper('form');
	
	// описания файлов хранятся в виде серилизованного массива в
	// uploads/_mso_i/_mso_descritions.dat
	$fn_mso_descritions = $MSO->config['uploads_dir'] . '_mso_i/_mso_descritions.dat';
	if (!file_exists( $fn_mso_descritions )) // файла нет, нужно его создать
		write_file($fn_mso_descritions, serialize(array())); // записываем в него пустой массив
	
	if (file_exists( $fn_mso_descritions )) // файла нет, нужно его создать
	{
		// массив данных: fn => описание )
		$mso_descritions = unserialize( read_file($fn_mso_descritions) ); // получим из файла все описания
	}
	else $mso_descritions = array();
	
	# удаление выделенных файлов
	if ( $post = mso_check_post(array('f_session_id', 'f_check_files', 'f_delete_submit')) )
	{
		mso_checkreferer();
		// pr($post);
		
		foreach ($post['f_check_files'] as $file)
		{
			@unlink($MSO->config['uploads_dir'] . $file);
			@unlink($MSO->config['uploads_dir'] . '_mso_i/' . $file);
			@unlink($MSO->config['uploads_dir'] . 'mini/' . $file);
			
			
		}
		echo '<div class="update">Выполнено</div>';
	}
	
	# загрузка нового файла
	if ( $post = mso_check_post(array('f_session2_id', 'f_upload_submit')) )
	{
		mso_checkreferer();
		
		$config['upload_path'] = $MSO->config['uploads_dir'];
		$config['allowed_types'] = $allowed_types;
		//$config['max_size'] = '2048';
		// $config['max_width'] = '1024';
		// $config['max_height'] = '768';
		
		$CI->load->library('upload', $config);
		
		$res = $CI->upload->do_upload('f_userfile');
		
		if ($res)
		{
			echo '<div class="update">Загрузка выполнена</div>';
			
			// если это файл картинки, то нужно сразу сделать скриншот маленький в _mso_i 100px, который будет выводиться в
			// списке файлов
			$up_data = $CI->upload->data();
			
			// файл нужно поменять к нижнему регистру
			if ( $up_data['file_name'] != strtolower($up_data['file_name']) )
			{
				// переименуем один раз
				if (rename($up_data['full_path'], $up_data['file_path'] . strtolower('__' . $up_data['file_name'])))
				{
					// потом второй в уже нужный - это из-за бага винды
					rename($up_data['file_path'] . strtolower('__' . $up_data['file_name']), 
								$up_data['file_path'] . strtolower($up_data['file_name']));
					
					$up_data['file_name'] = strtolower($up_data['file_name']);
					$up_data['full_path'] = $up_data['file_path'] . $up_data['file_name'];
					// echo '<div class="update">' . $up_data['full_path'] . $up_data['file_name'] . '</div>';
				}
				else echo '<div class="error">Не удалось перименовать файл в нижний регистр</div>';
			}
			
			$fn_descr = trim(strip_tags($post['f_userfile_title'])); // описание файла
			$fn_descr = str_replace('"', '', $fn_descr); // удалим лишнее
			$fn_descr = str_replace('\'', '', $fn_descr);
			
			$mso_descritions[$up_data['file_name']] = $fn_descr;
			write_file($fn_mso_descritions, serialize($mso_descritions) ); // сохраняем в файл
			
			
			//pr($up_data);
			/*
			    [file_name] => warfare7.jpg
				[file_type] => image/jpeg
				[file_path] => D:/xampplite/htdocs/codeigniter/uploads/
				[full_path] => D:/xampplite/htdocs/codeigniter/uploads/warfare7.jpg
				[raw_name] => warfare7
				[orig_name] => warfare.jpg
				[file_ext] => .jpg
				[file_size] => 52.09
				[is_image] => 1
				[image_width] => 450
				[image_height] => 300
				[image_type] => jpeg
				[image_size_str] => width="450" height="300"
			*/
			
			if ($up_data['is_image']) // это картинка
			{
				$r_conf = array(
						'image_library' => 'gd2',
						'source_image' => $up_data['full_path'],
						'new_image' => $up_data['file_path'] . '_mso_i/' . $up_data['file_name'],
						'maintain_ratio' => true,
				//		'width' => 100,
				//		'height' => 100,
					);
				
				if ( $up_data['image_width']>100 or $up_data['image_height']>100 ) // если сам файл большой, то размеры _mso_i-миниатюры меняем
				{
					$r_conf['width'] = 100;
					$r_conf['height'] = 100;
				}

				
				$CI->load->library('image_lib', $r_conf );
				if (!$CI->image_lib->resize())
					echo '<div class="error">' . $CI->image_lib->display_errors() . '</div>';
					
				
				if (isset($post['f_userfile_mini'])) // нужно создать миниатюру
				{
					$size = abs((int) $post['f_userfile_mini_size']);
					
					if ( $size > 1 and $size < $up_data['image_width'] and $size < $up_data['image_height'] ) // корректный размер
					{
						$r_conf = array(
							'image_library' => 'gd2',
							'source_image' => $up_data['full_path'],
							'new_image' => $up_data['file_path'] . 'mini/' . $up_data['file_name'],
							'maintain_ratio' => true,
							'width' => $size,
							'height' => $size,
						);
						
						$CI->image_lib->initialize($r_conf );
						
						if (!$CI->image_lib->resize())
							echo '<div class="error">Создание миниатюры: ' . $CI->image_lib->display_errors() . '</div>';
					}
				}
					
				if (isset($post['f_userfile_resize'])) // нужно изменить размер
				{
					$size = abs((int) $post['f_userfile_resize_size']);
					
					if ( $size > 1 and $size < $up_data['image_width'] and $size < $up_data['image_height'] ) // корректный размер
					{
						$r_conf = array(
							'image_library' => 'gd2',
							'source_image' => $up_data['full_path'],
							'new_image' => $up_data['full_path'],
							'maintain_ratio' => true,
							'width' => $size,
							'height' => $size,
						);
						
						$CI->image_lib->initialize($r_conf );
						
						if (!$CI->image_lib->resize())
							echo '<div class="error">Уменьшение изображения: ' . $CI->image_lib->display_errors() . '</div>';
					}
				}
			}
		}
		else
		{
			$er = $CI->upload->display_errors();
			echo '<div class="error">Ошибка загрузки файла.' . $er . '</div>';
		}
	}	
	
	echo '
		<div style="margin: 20px 0; padding: 5px 10px 15px 10px; border: 1px solid gray;">
		<h2>Загрузка файла</h2>
		<p>Для загрузки файла нажмите кнопку «Обзор», выберите файл на своем компьютере. После этого нажмите кнопку «Загрузить». Размер файла не должен превышать 2Мб.</p>
		<form action="" method="post" enctype="multipart/form-data">'
		. mso_form_session('f_session2_id') . 
		'<p><input type="file" name="f_userfile" size="80" />&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" name="f_upload_submit" value="Загрузить" /></p>
		<p>Описание файла: <input type="text" name="f_userfile_title" style="width: 380px" value="" /></p>
		
		<p><input type="checkbox" name="f_userfile_resize" checked="checked" value="" /> Для изображений изменить размер до 
			<input type="text" name="f_userfile_resize_size" style="width: 50px" maxlength="4" value="600" /> px (по максимальной стороне)</p>
		
		<p><input type="checkbox" name="f_userfile_mini" checked="checked" value="" /> Для изображений сделать миниатюру размером 
			<input type="text" name="f_userfile_mini_size" style="width: 50px" maxlength="4" value="150" /> px (по максимальной стороне). <br /><em>Примечание: миниатюра будет создана в каталоге <strong>uploads/mini/</strong></em></p>
		
		</form>
		</div>
		';
	
	
	
	
	$tmpl = array (
					'table_open'		  => '<table class="page" border="0" width="100%"><colgroup width="300"><colgroup width="100">',
					'row_alt_start'		  => '<tr class="alt">',
					'cell_alt_start'	  => '<td class="alt">',
			  );

	$CI->table->set_template($tmpl); // шаблон таблицы
	
	// заголовки
	$CI->table->set_heading('Файл', '', 'Коды для вставки');
	
	// проходимся по каталогу аплоада и выводим их списком
	
	$uploads_dir = $MSO->config['uploads_dir'];
	$uploads_url = $MSO->config['uploads_url'];
	
	// все файлы в массиве $dirs
	$dirs = directory_map($uploads_dir, true); // только в текущем каталоге
	sort($dirs);

	$allowed_ext = explode('|', $allowed_types);
	
	foreach ($dirs as $file)
	{
		if (@is_dir($uploads_dir . $file)) continue; // это каталог
		
		$ext = strtolower(str_replace('.', '', strrchr($file, '.'))); // расширение файла
		if ( !in_array($ext, $allowed_ext) ) continue; // запрещенный тип файла
		
		$cod = '';
		
		if (isset($mso_descritions[$file])) $title = $mso_descritions[$file];
			else $title = '';
		
		$sel =  form_checkbox('f_check_files[]', $file, false, 'title="' . $title . '"') . ' <b>' . $file . '</b>';
		
		$cod1 = stripslashes(htmlspecialchars( $uploads_url . $file ) );
		
		if ($title) $cod .= '<p><input type="text" style="width: 99%;" value="' . $title . '" />';
		
		$cod .= '<p><input type="text" style="width: 99%;" value="' . $cod1 . '" />';
		
		if ($title) $cod2 = stripslashes(htmlspecialchars( '<a href="' . $uploads_url . $file . '">' . $title . '</a>') );
			else $cod2 = stripslashes(htmlspecialchars( '<a href="' . $uploads_url . $file . '">' . $file . '</a>') );
		
		$cod .= '<p><input type="text" style="width: 99%;" value="' . $cod2 . '" />';
		
		if ( $ext == 'jpg' or $ext == 'jpeg' or $ext == 'gif' or $ext == 'png'  )
		{
			if (file_exists( $uploads_dir . '_mso_i/' . $file  )) $_f = '_mso_i/' . $file;
			else $_f = $file;
			
			if (file_exists( $uploads_dir . 'mini/' . $file  ))
				$file_mini = '=' . $uploads_url . 'mini/' . $file;
			else $file_mini = '=' . $uploads_url . $file;
			
			// $cod3 = stripslashes(htmlspecialchars( '<a href="' . $uploads_url . $file . '"><img src="' . $uploads_url . $file . '" /></a>') );
			//$cod .= '<p><input type="text" style="width: 99%;" value="' . $cod3 . '" />';
			
			if ($title)
				$cod3 = stripslashes(htmlspecialchars( '[image' . $file_mini . ' ' . $title . ']' . $uploads_url . $file . '[/image]') );
			else
				$cod3 = stripslashes(htmlspecialchars( '[image' . $file_mini . ']' . $uploads_url . $file . '[/image]') );
			
			$cod .= '<p><input type="text" style="width: 99%;" value="' . $cod3 . '" />';
			
			$predpr = '<a class="lightbox" href="' . $uploads_url . $file . '" target="_blank" title="' . $title . ' ('. $file . ')' . '"><img style="max-width: 100px;" src="' . $uploads_url . $_f . '" /></a>';
			
		}
		else $predpr = '';
		
		$CI->table->add_row($sel, $predpr, $cod);
	}
	
	// добавляем форму, а также текущую сессию
	echo '<form action="" method="post">' . mso_form_session('f_session_id');
	echo $CI->table->generate(); // вывод подготовленной таблицы
	echo '<br /><input type="submit" name="f_delete_submit" value="&nbsp;Удалить&nbsp;" onClick="if(confirm(\'Выделенные файы будут безвозвратно удалены! Удалять?\')) {return true;} else {return false;}" >';
	echo '</form>';
	
	$n = '\n';
	$up = $MSO->config['uploads_url'];
	
	echo <<<EOF
	<script type="text/javascript">
		$(function()
		{
			$('#gallerycodeclick').click(function()
			{ 
				$('#gallerycode').html('');
				
				codegal = '';
				$("input[@name='f_check_files[]']").each( function(i)
				{ 
					if (this.checked)
					{
						t = this.title;
						if (!t) { t = this.value; }
						codegal = codegal + '[gal={$up}mini/' + this.value + ' ' + t + ']{$up}'+ this.value +'[\/gal]{$n}';
					}
				});
				
				if ( codegal ) 
				{
					n = $('#gallerycodename').val();
					if (n) { n = '[galname]' + n + '[/galname]';}
					else { n = ''; }
					
					codegal = '[gallery]' + n + '{$n}'+ codegal + '[/gallery]';
					$('#gallerycode').html(codegal);
					$('#gallerycode').css({ background: '#F0F0F0', width: '100%', height: '150px',
											border: '1px solid gray', margin: '20px 0', 
											'font-family': 'Courier New',
											'font-size': '9pt'});
					$('#gallerycode').fadeIn('slow');
					$('#gallerycode').select();
				}
				else
				{
					$('#gallerycode').hide();
					alert('Предварительно нужно выделить файлы для галереи');
				}
			});
		});
	</script>
	<br /><hr />
	<p>Выделите нужные файлы. (У вас должен быть активирован плагин <strong>LightBox</strong>)</p>
	<p><input type="button" id="gallerycodeclick" value="   Генерировать код галереи   ">
	Название: <input type="text" id="gallerycodename" style="width: 200px" value="" /> (если нужно)</p>
	<p><textarea id="gallerycode" style="display: none"></textarea>
	
EOF;

	
?>