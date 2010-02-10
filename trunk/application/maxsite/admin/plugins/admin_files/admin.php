<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<h1>Загрузки. Файлы. Галереи</h1>
<p class="info">Здесь вы можете выполнить необходимые операции с файлами.</p>

<?php
	
	$CI = & get_instance();
	$CI->load->helper('file'); // хелпер для работы с файлами
	$CI->load->library('table');
	$CI->load->helper('directory');
	$CI->load->helper('form');
		
	// разрешенные типы файлов
	$allowed_types = 'gif|jpg|jpeg|png|zip|txt|rar|doc|rtf|pdf|html|htm|css|xml|odt|flv|swf|mp3|wav|xls';
	
	
	// по сегменту определяем текущий каталог в uploads
	// если каталога нет, скидываем на дефолтный ''
	
	$current_dir = $current_dir_h2 = mso_segment(3);
	if ($current_dir) $current_dir .= '/';
	
	$path = $MSO->config['uploads_dir'] . $current_dir;
	if ( ! is_dir($path) ) // нет каталога
	{
		$path = $MSO->config['uploads_dir'];
		$current_dir = $current_dir_h2 = '';
	}
	else
	{
		if ($current_dir_h2) $current_dir_h2 = '/' . $current_dir_h2;
	}
	
	echo '<h2>Текущий каталог: uploads' . $current_dir_h2 . '</h2>';
	
	
	
	# новый каталог - создаем до того, как отобразить навигацию
	if ( $post = mso_check_post(array('f_session3_id', 'f_cat_name', 'f_newcat_submit')) )
	{
		mso_checkreferer();

		$f_cat_name = mso_slug($post['f_cat_name']);
		
		if (!$f_cat_name)
			echo '<div class="error">Нужно ввести имя каталога</div>';
		else 
		{
			$new_dir = getinfo('uploads_dir') . $f_cat_name;
			
			if ( is_dir($new_dir) ) // уже есть
			{
				echo '<div class="error">Такой каталог уже есть!</div>';
			}
			else
			{
				@mkdir($new_dir, 0777); // нет каталога, пробуем создать
				@mkdir($new_dir . '/_mso_i', 0777); // нет каталога, пробуем создать
				@mkdir($new_dir . '/mini', 0777); // нет каталога, пробуем создать	
				echo '<div class="update">Каталог <strong>' . $f_cat_name . '</strong> создан!</div>';
			}
		}
	}	
	
	
	
	
	
	
	// нужно вывести навигацию по каталогам в uploads
	$all_dirs = directory_map($MSO->config['uploads_dir'], true); // только в uploads
	$out = '';
	foreach ($all_dirs as $d)
	{
		// это каталог
		if (is_dir( getinfo('uploads_dir') . $d) and $d != '_mso_float' and $d != 'mini' and $d != '_mso_i' and $d != 'smiles') 
		{
			$out .= '<a href="'. $MSO->config['site_admin_url'] . 'files/' . $d . '">' . $d . '</a>   ';
		}
	}
	if ($out) 
	{
		$out = '<a href="'. $MSO->config['site_admin_url'] . 'files">uploads</a>   ' . $out;
		$out = str_replace('   ', ' | ', trim($out));
		$out = '<p>Навигация: ' . $out . '</p>';
		echo $out;
	}
	

	// нужно создать в этом каталоге _mso_i и mini если нет
	if ( ! is_dir($path . '_mso_i') ) @mkdir($path . '_mso_i', 0777); // нет каталога, пробуем создать
	if ( ! is_dir($path . 'mini') ) @mkdir($path . 'mini', 0777); // нет каталога, пробуем создать
	
		
	
	// описания файлов хранятся в виде серилизованного массива в
	// uploads/_mso_i/_mso_descritions.dat
	$fn_mso_descritions = $path . '_mso_i/_mso_descriptions.dat';
	
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
			@unlink($MSO->config['uploads_dir'] . $current_dir . $file);
			@unlink($MSO->config['uploads_dir'] . $current_dir . '_mso_i/' . $file);
			@unlink($MSO->config['uploads_dir'] . $current_dir . 'mini/' . $file);
		}
		echo '<div class="update">Выполнено</div>';
	}
	
	

	
	
	# загрузка нового файла
	if ( $post = mso_check_post(array('f_session2_id', 'f_upload_submit')) )
	{
		mso_checkreferer();
		
		$config['upload_path'] = $MSO->config['uploads_dir'] . $current_dir;
		$config['allowed_types'] = $allowed_types;
		//$config['max_size'] = '2048';
		// $config['max_width'] = '1024';
		// $config['max_height'] = '768';
		
		$CI->load->library('upload', $config);
		
		// если была отправка файла, то нужно заменить поле имени с русского на что-то другое
		// это ошибка при копировании на сервере - он не понимает русские буквы
		if (isset($_FILES['f_userfile']['name'])) 
		{
			$f_temp = $_FILES['f_userfile']['name'];
			
			// оставим только точку
			$f_temp = str_replace('.', '__mso_t__', $f_temp);
			$f_temp = mso_slug($f_temp); // остальное как обычно mso_slug
			$f_temp = str_replace('__mso_t__', '.', $f_temp);
			
			$_FILES['f_userfile']['name'] = $f_temp;
		}
		
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
						
						
						$mini_type = $post['f_mini_type']; // тип миниатюры
						/*
						1 Пропорционального уменьшения
						2 Обрезки (crop) по центру
						3 Обрезки (crop) с левого верхнего края
						4 Обрезки (crop) с левого нижнего края
						5 Обрезки (crop) с правого верхнего края
						6 Обрезки (crop) с правого нижнего края
						*/
						
						if ($mini_type == 2) // Обрезки (crop) по центру
						{
							$r_conf['x_axis'] = round($up_data['image_width'] / 2 - $size / 2);
							$r_conf['y_axis'] = round($up_data['image_height'] / 2 - $size / 2);
							
							$CI->image_lib->initialize($r_conf );
							if (!$CI->image_lib->crop())
								echo '<div class="error">Создание миниатюры: ' . $CI->image_lib->display_errors() . '</div>';
						}
						elseif ($mini_type == 3) // Обрезки (crop) с левого верхнего края
						{
							$r_conf['x_axis'] = 0;
							$r_conf['y_axis'] = 0;
							
							$CI->image_lib->initialize($r_conf );
							if (!$CI->image_lib->crop())
								echo '<div class="error">Создание миниатюры: ' . $CI->image_lib->display_errors() . '</div>';
						}
						elseif ($mini_type == 4) // Обрезки (crop) с левого нижнего края
						{
							$r_conf['x_axis'] = 0;
							$r_conf['y_axis'] = $up_data['image_height'] - $size;
							
							$CI->image_lib->initialize($r_conf );
							if (!$CI->image_lib->crop())
								echo '<div class="error">Создание миниатюры: ' . $CI->image_lib->display_errors() . '</div>';
						}						
						elseif ($mini_type == 5) // Обрезки (crop) с правого верхнего края
						{
							$r_conf['x_axis'] = $up_data['image_width'] - $size;
							$r_conf['y_axis'] = 0;
							
							$CI->image_lib->initialize($r_conf );
							if (!$CI->image_lib->crop())
								echo '<div class="error">Создание миниатюры: ' . $CI->image_lib->display_errors() . '</div>';
						}						
						elseif ($mini_type == 6) // Обрезки (crop) с правого нижнего края
						{
							$r_conf['x_axis'] = $up_data['image_width'] - $size;
							$r_conf['y_axis'] = $up_data['image_height'] - $size;
							
							$CI->image_lib->initialize($r_conf );
							if (!$CI->image_lib->crop())
								echo '<div class="error">Создание миниатюры: ' . $CI->image_lib->display_errors() . '</div>';
						}							
						else // ничего не указано - Пропорционального уменьшения
						{
							$CI->image_lib->initialize($r_conf );
							if (!$CI->image_lib->resize())
								echo '<div class="error">Создание миниатюры: ' . $CI->image_lib->display_errors() . '</div>';
						}
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
	
	// форма нового каталога
	echo '
		<div style="margin: 20px 0; padding: 5px 10px 5px 10px; border: 1px solid gray;">
		<form action="" method="post">' . mso_form_session('f_session3_id') . 
		'<p>Новый каталог: <input type="text" name="f_cat_name" style="width: 380px" value="" />
		<input type="submit" name="f_newcat_submit" value="&nbsp;Создать&nbsp;" onClick="if(confirm(\'Создать каталог в uploads?\')) {return true;} else {return false;}" ></p>
		</form></div>';
			
	
	$resize_images = mso_get_option('resize_images', 'general', 600);
	$size_image_mini = mso_get_option('size_image_mini', 'general', 150);
	
	// форма загрузки
	echo '
		<div style="margin: 20px 0; padding: 5px 10px 15px 10px; border: 1px solid gray;">
		<h2>Загрузка файла</h2>
		<p>Для загрузки файла нажмите кнопку «Обзор», выберите файл на своем компьютере. После этого нажмите кнопку «Загрузить». Размер файла не должен превышать 2Мб.</p>
		<form action="" method="post" enctype="multipart/form-data">' . mso_form_session('f_session2_id') . 
		'<p><input type="file" name="f_userfile" size="80" />&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" name="f_upload_submit" value="Загрузить" /></p>
		<p>Описание файла: <input type="text" name="f_userfile_title" style="width: 380px" value="" /></p>
		
		<p><input type="checkbox" name="f_userfile_resize" checked="checked" value="" /> Для изображений изменить размер до 
			<input type="text" name="f_userfile_resize_size" style="width: 50px" maxlength="4" value="' . $resize_images . '" /> px (по максимальной стороне)</p>
		
		<p><input type="checkbox" name="f_userfile_mini" checked="checked" value="" /> Для изображений сделать миниатюру размером 
			<input type="text" name="f_userfile_mini_size" style="width: 50px" maxlength="4" value="' . $size_image_mini . '" /> px (по максимальной стороне). <br /><em>Примечание: миниатюра будет создана в каталоге <strong>uploads/' . $current_dir . 'mini</strong></em></p>
		
		<p>Миниатюру делать путем: <select style="width: 350px" name="f_mini_type">
		<option value="1">Пропорционального уменьшения</option>
		<option value="2">Обрезки (crop) по центру</option>
		<option value="3">Обрезки (crop) с левого верхнего края</option>
		<option value="4">Обрезки (crop) с левого нижнего края</option>
		<option value="5">Обрезки (crop) с правого верхнего края</option>
		<option value="6">Обрезки (crop) с правого нижнего края</option>
		</select></p>
		
		</form>
		</div>
		';
	
	
	
	
	$tmpl = array (
					'table_open'		  => '<table class="page" border="0" width="100%"><colgroup width="100">',
					'row_alt_start'		  => '<tr class="alt">',
					'cell_alt_start'	  => '<td class="alt">',
			  );

	$CI->table->set_template($tmpl); // шаблон таблицы
	
	// заголовки
	$CI->table->set_heading('&bull;', 'Коды для вставки');
	
	// проходимся по каталогу аплоада и выводим их списком
	
	$uploads_dir = getinfo('uploads_dir') . $current_dir;
	$uploads_url = getinfo('uploads_url') . $current_dir;
	
	// все файлы в массиве $dirs
	$dirs = directory_map($uploads_dir, true); // только в текущем каталоге
	
	if (!$dirs) $dirs = array();
	
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
		
		$CI->table->add_row($predpr, $sel . $cod);
	}
	
	// добавляем форму, а также текущую сессию
	echo '<form action="" method="post">' . mso_form_session('f_session_id');
	echo $CI->table->generate(); // вывод подготовленной таблицы
	echo '<br /><input type="submit" name="f_delete_submit" value="&nbsp;Удалить&nbsp;" onClick="if(confirm(\'Выделенные файы будут безвозвратно удалены! Удалять?\')) {return true;} else {return false;}" >';
	echo '</form>';
	
	$n = '\n';
	$up = $uploads_url;
	
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