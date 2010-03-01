<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

	mso_cur_dir_lang('admin');

?>

<h1><?= t('Загрузки. Файлы. Галереи') ?></h1>
<p class="info"><?= t('Здесь вы можете выполнить необходимые операции с файлами.') ?></p>

<?php

	$CI = & get_instance();
	$CI->load->helper('file'); // хелпер для работы с файлами
	# $CI->load->library('table');
	$CI->load->helper('directory');
	$CI->load->helper('form');

	// разрешенные типы файлов
	$allowed_types = 'mp3|gif|jpg|jpeg|png|zip|txt|rar|doc|rtf|pdf|html|htm|css|xml|odt|avi|wmv|flv|swf|wav|xls|7z|gz';


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

	echo '<h2>' . t('Текущий каталог:', 'admin') . ' uploads' . $current_dir_h2 . '</h2>';


	# новый каталог - создаем до того, как отобразить навигацию
	if ( $post = mso_check_post(array('f_session3_id', 'f_cat_name', 'f_newcat_submit')) )
	{
		mso_checkreferer();

		$f_cat_name = mso_slug($post['f_cat_name']);

		if (!$f_cat_name)
			echo '<div class="error">' . t('Нужно ввести имя каталога', 'admin') . '</div>';
		else
		{
			$new_dir = getinfo('uploads_dir') . $f_cat_name;

			if ( is_dir($new_dir) ) // уже есть
			{
				echo '<div class="error">' . t('Такой каталог уже есть!', 'admin') . '</div>';
			}
			else
			{
				@mkdir($new_dir, 0777); // нет каталога, пробуем создать
				@mkdir($new_dir . '/_mso_i', 0777); // нет каталога, пробуем создать
				@mkdir($new_dir . '/mini', 0777); // нет каталога, пробуем создать
				echo '<div class="update">' . sprintf(t('Каталог <strong>%s</strong> создан!'), $f_cat_name)
					. '</div>';
			}
		}
	}


	// нужно вывести навигацию по каталогам в uploads
	$all_dirs = directory_map($MSO->config['uploads_dir'], true); // только в uploads
	asort($all_dirs);
	$out = '';
	foreach ($all_dirs as $d)
	{
		// это каталог
		if (is_dir( getinfo('uploads_dir') . $d) and $d != '_mso_float' and $d != 'mini' and $d != '_mso_i' and $d != 'smiles')
		{
			if (mso_segment(3) == $d)
				$out .= '<a href="'. $MSO->config['site_admin_url'] . 'files/' . $d . '"><strong>' . $d . '</strong></a>   ';
			else
				$out .= '<a href="'. $MSO->config['site_admin_url'] . 'files/' . $d . '">' . $d . '</a>   ';
		}
	}
	if ($out)
	{
		if (!mso_segment(3))
			$out = '<a href="' . $MSO->config['site_admin_url'] . 'files"><strong>uploads</strong></a>   ' . $out;
		else
			$out = '<a href="' . $MSO->config['site_admin_url'] . 'files">uploads</a>   ' . $out;

		$out = str_replace('   ', ' | ', trim($out));
		$out = '<p>' . t('Навигация:', 'admin') . ' ' . $out . '</p>';
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

	# Добавление Рамира -  редактирование описания
	if ( $post = mso_check_post(array('f_session_id', 'f_file_name', 'f_file_description', 'f_edit_submit')) )
	{
		mso_checkreferer();

			// удалим описание из _mso_i/_mso_descriptions.dat
			unset($mso_descritions[$post['f_file_name']]);
			$mso_descritions[$post['f_file_name']]=$post['f_file_description'];
			write_file($fn_mso_descritions, serialize($mso_descritions) ); // сохраняем файл

		echo '<div class="update">' . t('Описание обновлено!', 'admin') . '</div>';
	}
	# Конец Добавление Рамира

	# удаление выделенных файлов
	if ( $post = mso_check_post(array('f_session_id', 'f_check_files', 'f_delete_submit')) )
	{
		mso_checkreferer();

		foreach ($post['f_check_files'] as $file)
		{
			@unlink($MSO->config['uploads_dir'] . $current_dir . $file);
			@unlink($MSO->config['uploads_dir'] . $current_dir . '_mso_i/' . $file);
			@unlink($MSO->config['uploads_dir'] . $current_dir . 'mini/' . $file);

			// удалим описание из _mso_i/_mso_descriptions.dat
			unset($mso_descritions[$file]);
			write_file($fn_mso_descritions, serialize($mso_descritions) ); // сохраняем файл
		}
		echo '<div class="update">' . t('Выполнено', 'admin') . '</div>';
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
			echo '<div class="update">' . t('Загрузка выполнена', 'admin') . '</div>';

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
				else echo '<div class="error">' . t('Не удалось перименовать файл в нижний регистр', 'admin') . '</div>';
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
				$CI->load->library('image_lib');


				# вначале нужно изменить размер
				# потом делаем миниатюру с указанными размерами
				# потом делаем такую же миниатюру для  _mso_i с размером 100x100

				# меняем размер
				if (isset($post['f_userfile_resize'])) // нужно изменить размер
				{
					$size = abs((int) $post['f_userfile_resize_size']);

					($up_data['image_width'] >= $up_data['image_height']) ? ($max = $up_data['image_width']) : ($max = $up_data['image_height']);
					if ( $size > 1 and $size < $max ) // корректный размер
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
							echo '<div class="error">' . t('Уменьшение изображения:', 'admin') . ' ' . $CI->image_lib->display_errors() . '</div>';
					}

				}

				//Меняли или не меняли размер, но теперь проверяем, нужна ли нам ватермарка.
				if (isset($post['f_userfile_water']))
				{ //todo — проверка, всё ли нам прислали, всё ли на месте. В идеале бы проверить размеры картинки по отношению к ватермарке.
					if (!file_exists($MSO->config['uploads_dir']. 'watermark.png'))
					{
						echo '<div class="error">' . t('Водяной знак:', 'admin') . ' ' . t('файл водяного знака не найден! Загрузите его в каталог uploads/', 'admin') . '</div>';
					}
					else
					{
						$water_type = $post['f_water_type']; // Расположение ватермарка
						$hor = 'right'; //Инитим дефолтом.
						$vrt = 'bottom'; //Инитим дефолтом.
						if (($water_type == 2) or ($water_type == 4)) $hor = 'left';
						if (($water_type == 2) or ($water_type == 3)) $vrt = 'top';
						if ($water_type == 1) {$hor = 'center'; $vrt = 'middle';}

						$r_conf = array(
							'image_library' => 'gd2',
							'source_image' => $up_data['full_path'],
							'new_image' => $up_data['full_path'],
							'wm_type' => 'overlay',
							'wm_vrt_alignment' => $vrt,
							'wm_hor_alignment' => $hor,
							'wm_overlay_path' => $MSO->config['uploads_dir']. 'watermark.png' //Жёстко, а что делать?
						);

						$CI->image_lib->initialize($r_conf );
						if (!$CI->image_lib->watermark())
							echo '<div class="error">' . t('Водяной знак:', 'admin') . ' ' . $CI->image_lib->display_errors() . '</div>';
					}
				}

				# получим новые размеры файла
				$image_info = GetImageSize($up_data['full_path']);
				$image_width = $image_info[0];
				$image_height = $image_info[1];


				# теперь нужно сделать миниатюру указанного размера в mini
				if (isset($post['f_userfile_mini']))
				{
					$size = abs((int) $post['f_userfile_mini_size']);

					($image_width >= $image_height) ? ($max = $image_width) : ($max = $image_height);
					if ( $size > 1 and $size < $max ) // корректный размер
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
							$r_conf['x_axis'] = round($image_width / 2 - $size / 2);
							$r_conf['y_axis'] = round($image_height / 2 - $size / 2);
							$CI->image_lib->initialize($r_conf );
							if (!$CI->image_lib->crop())
								echo '<div class="error">' . t('Создание миниатюры:', 'admin') . ' ' . $CI->image_lib->display_errors() . '</div>';
						}
						elseif ($mini_type == 3) // Обрезки (crop) с левого верхнего края
						{
							$r_conf['x_axis'] = 0;
							$r_conf['y_axis'] = 0;

							$CI->image_lib->initialize($r_conf );
							if (!$CI->image_lib->crop())
								echo '<div class="error">' . t('Создание миниатюры:', 'admin') . ' ' . $CI->image_lib->display_errors() . '</div>';
						}
						elseif ($mini_type == 4) // Обрезки (crop) с левого нижнего края
						{
							$r_conf['x_axis'] = 0;
							$r_conf['y_axis'] = round($image_height - $size * $image_height/$image_width);

							$CI->image_lib->initialize($r_conf );
							if (!$CI->image_lib->crop())
								echo '<div class="error">' . t('Создание миниатюры:', 'admin') . ' ' . $CI->image_lib->display_errors() . '</div>';
						}
						elseif ($mini_type == 5) // Обрезки (crop) с правого верхнего края
						{
							$r_conf['x_axis'] = $image_width - $size;
							$r_conf['y_axis'] = 0;

							$CI->image_lib->initialize($r_conf );
							if (!$CI->image_lib->crop())
								echo '<div class="error">' . t('Создание миниатюры:', 'admin') . ' ' . $CI->image_lib->display_errors() . '</div>';
						}
						elseif ($mini_type == 6) // Обрезки (crop) с правого нижнего края
						{
							$r_conf['x_axis'] = $image_width - $size;
							$r_conf['y_axis'] = $image_height - $size;

							$CI->image_lib->initialize($r_conf );
							if (!$CI->image_lib->crop())
								echo '<div class="error">' . t('Создание миниатюры:', 'admin') . ' ' . $CI->image_lib->display_errors() . '</div>';
						}
						else // ничего не указано - Пропорционального уменьшения
						{
							$CI->image_lib->initialize($r_conf );
							if (!$CI->image_lib->resize())
								echo '<div class="error">' . t('Создание миниатюры:', 'admin') . ' ' . $CI->image_lib->display_errors() . '</div>';
						}
					}
					else
					{
						//Размер некорректный и миниатюру просто копируем из большого изображения.
						copy($up_data['full_path'], $up_data['file_path']. 'mini/'. $up_data['file_name']);
					}
				}


				# всегда делаем 100 на 100
				# алгоритм тот же, что и у миниатюры
				$size = 100;

				$r_conf = array(
					'image_library' => 'gd2',
					'source_image' => $up_data['full_path'],
					'new_image' => $up_data['file_path'] . '_mso_i/' . $up_data['file_name'],
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
					$r_conf['x_axis'] = round($image_width / 2 - $size / 2);
					$r_conf['y_axis'] = round($image_height / 2 - $size / 2);

					$CI->image_lib->initialize($r_conf );
					if (!$CI->image_lib->crop())
						echo '<div class="error">' . t('Создание миниатюры:', 'admin') . ' ' . $CI->image_lib->display_errors() . '</div>';
				}
				elseif ($mini_type == 3) // Обрезки (crop) с левого верхнего края
				{
					$r_conf['x_axis'] = 0;
					$r_conf['y_axis'] = 0;

					$CI->image_lib->initialize($r_conf );
					if (!$CI->image_lib->crop())
						echo '<div class="error">' . t('Создание миниатюры:', 'admin') . ' ' . $CI->image_lib->display_errors() . '</div>';
				}
				elseif ($mini_type == 4) // Обрезки (crop) с левого нижнего края
				{
					$r_conf['x_axis'] = 0;
					$r_conf['y_axis'] = round($image_height - $size * $image_height/$image_width);

					$CI->image_lib->initialize($r_conf );
					if (!$CI->image_lib->crop())
						echo '<div class="error">' . t('Создание миниатюры:', 'admin') . ' ' . $CI->image_lib->display_errors() . '</div>';
				}
				elseif ($mini_type == 5) // Обрезки (crop) с правого верхнего края
				{
					$r_conf['x_axis'] = $image_width - $size;
					$r_conf['y_axis'] = 0;

					$CI->image_lib->initialize($r_conf );
					if (!$CI->image_lib->crop())
						echo '<div class="error">' . t('Создание миниатюры:', 'admin') . ' ' . $CI->image_lib->display_errors() . '</div>';
				}
				elseif ($mini_type == 6) // Обрезки (crop) с правого нижнего края
				{
					$r_conf['x_axis'] = $image_width - $size;
					$r_conf['y_axis'] = $image_height - $size;

					$CI->image_lib->initialize($r_conf );
					if (!$CI->image_lib->crop())
						echo '<div class="error">' . t('Создание миниатюры:', 'admin') . ' ' . $CI->image_lib->display_errors() . '</div>';
				}
				else // ничего не указано - Пропорционального уменьшения
				{
					$CI->image_lib->initialize($r_conf );
					if (!$CI->image_lib->resize())
						echo '<div class="error">' . t('Создание миниатюры:', 'admin') . ' ' . $CI->image_lib->display_errors() . '</div>';
				}


			}
		}
		else
		{
			$er = $CI->upload->display_errors();
			echo '<div class="error">' . t('Ошибка загрузки файла.', 'admin') . $er . '</div>';
		}
	}

	// форма нового каталога
	echo '
		<div class="new_cat_upload"><h2>'. t('Новый каталог', 'admin'). '</h2>
		<form action="" method="post">' . mso_form_session('f_session3_id') .
		'<p>'. t('Имя каталога', 'admin'). ': <input type="text" name="f_cat_name" value="">
		<input type="submit" name="f_newcat_submit" value="'. t('Создать', 'admin'). '" onClick="if(confirm(\'' . t('Создать каталог в uploads?', 'admin') . '\')) {return true;} else {return false;}" ></p>
		</form></div>';


	$resize_images = (int) mso_get_option('resize_images', 'general', 600);
	if ($resize_images < 1) $resize_images = 600;
	
	$size_image_mini = (int) mso_get_option('size_image_mini', 'general', 150);
	if ($size_image_mini < 1) $size_image_mini = 150;

	$watermark_type = mso_get_option('watermark_type', 'general', 1);
	
	// форма загрузки
	echo '
		<div class="upload_file">
		<h2>' . t('Загрузка файла', 'admin') . '</h2>
		<p>' . t('Для загрузки файла нажмите кнопку «Обзор», выберите файл на своем компьютере. После этого нажмите кнопку «Загрузить». Размер файла не должен превышать', 'admin') . ' ' . ini_get ('post_max_size') . '.</p>
		<form action="" method="post" enctype="multipart/form-data">' . mso_form_session('f_session2_id') .
		'<p><input type="file" name="f_userfile" size="80"> &nbsp; <input type="submit" name="f_upload_submit" value="' . t('Загрузить', 'admin') . '"></p>
		<p>' . t('Описание файла:', 'admin') . ' <input type="text" name="f_userfile_title" class="description_file" value=""></p>

		<p><label><input type="checkbox" name="f_userfile_resize" checked="checked" value=""> ' . t('Для изображений изменить размер до', 'admin') . '</label>
			<input type="text" name="f_userfile_resize_size" style="width: 50px" maxlength="4" value="' . $resize_images . '"> ' . t('px (по максимальной стороне).', 'admin') . '</p>

		<p><label><input type="checkbox" name="f_userfile_mini" checked="checked" value=""> ' . t('Для изображений сделать миниатюру размером', 'admin') . '</label>
			<input type="text" name="f_userfile_mini_size" style="width: 50px" maxlength="4" value="' . $size_image_mini . '"> ' . t('px (по максимальной стороне).', 'admin') . ' <br><em>' . t('Примечание: миниатюра будет создана в каталоге', 'admin') . ' <strong>uploads/' . $current_dir . 'mini</strong></em></p>

		<p>' . t('Миниатюру делать путем:', 'admin') . ' <select name="f_mini_type">
		<option value="1">' . t('Пропорционального уменьшения', 'admin') . '</option>
		<option value="2">' . t('Обрезки (crop) по центру', 'admin') . '</option>
		<option value="3">' . t('Обрезки (crop) с левого верхнего края', 'admin') . '</option>
		<option value="4">' . t('Обрезки (crop) с левого нижнего края', 'admin') . '</option>
		<option value="5">' . t('Обрезки (crop) с правого верхнего края', 'admin') . '</option>
		<option value="6">' . t('Обрезки (crop) с правого нижнего края', 'admin') . '</option>
		</select></p>

		<p><label><input type="checkbox" name="f_userfile_water" value="" '.
			((file_exists($MSO->config['uploads_dir']. 'watermark.png'))?'':' disabled="disabled"') . 
			((mso_get_option('use_watermark', 'general', 0))?(' checked="checked"'):('')) .
			'> ' . t('Для изображений установить водяной знак', 'admin') . '</label>
			<br><em>' . t('Примечание: водяной знак должен быть файлом <strong>watermark.png</strong> и находиться в каталоге', 'admin') . ' <strong>uploads</strong></em></p>

		<p>' . t('Водяной знак устанавливается:', 'admin') . ' <select name="f_water_type">
		<option value="1"'.(($watermark_type == 1)?(' selected="selected"'):('')).'>' . t('По центру', 'admin') . '</option>
		<option value="2"'.(($watermark_type == 2)?(' selected="selected"'):('')).'>' . t('В левом верхнем углу', 'admin') . '</option>
		<option value="3"'.(($watermark_type == 3)?(' selected="selected"'):('')).'>' . t('В правом верхнем углу', 'admin') . '</option>
		<option value="4"'.(($watermark_type == 4)?(' selected="selected"'):('')).'>' . t('В левом нижнем углу', 'admin') . '</option>
		<option value="5"'.(($watermark_type == 5)?(' selected="selected"'):('')).'>' . t('В правом нижнем углу', 'admin') . '</option>
		</select></p>

		</form>
		</div>
		';



	/*
	$tmpl = array (
					'table_open'		  => '<table class="page" border="0" width="100%"><colgroup width="110">',
					'row_alt_start'		  => '<tr class="alt">',
					'cell_alt_start'	  => '<td class="alt">',
			  );

	$CI->table->set_template($tmpl); // шаблон таблицы

	// заголовки
	$CI->table->set_heading('&bull;', t('Коды для вставки', 'admin'));
	*/

	// проходимся по каталогу аплоада и выводим их списком

	$uploads_dir = getinfo('uploads_dir') . $current_dir;
	$uploads_url = getinfo('uploads_url') . $current_dir;

	// все файлы в массиве $dirs
	$dirs = directory_map($uploads_dir, true); // только в текущем каталоге

	if (!$dirs) $dirs = array();

	sort($dirs);

	$allowed_ext = explode('|', $allowed_types);

	$out_all = ''; // весь вывод

	foreach ($dirs as $file)
	{
		if (@is_dir($uploads_dir . $file)) continue; // это каталог

		$ext = strtolower(str_replace('.', '', strrchr($file, '.'))); // расширение файла
		if ( !in_array($ext, $allowed_ext) ) continue; // запрещенный тип файла

		$cod = '<p>';
		$title = '';
		$title_f = '';

		if (isset($mso_descritions[$file]))
		{
			$title = $mso_descritions[$file];
			if ($title) $title_f = '<br><em>' . $title . '</em>';
		}

		$sel = form_checkbox('f_check_files[]', $file, false,
			'title="' . $title . '" id="' . mso_strip($file) . '"')
			. '<label for="' . mso_strip($file)
			. '"> '
			. $file . $title_f . '</label>';

		$cod1 = stripslashes(htmlspecialchars( $uploads_url . $file ) );

		# if ($title) $cod .= '<input type="text" style="width: 300px;" value="' . $title . '">';


		# $cod .= '<p><input type="text" style="width: 99%;" value="' . $cod1 . '">';

		$cod .= '<a href="#"
			onClick = "jAlert(\'<textarea cols=60 rows=4>' . $cod1 . '</textarea>\', \'Адрес файла\'); return false;">Адрес</a>';

		# $cod .= '<p><textarea style="width: 99%;">' . $cod1 . '</textarea>';



		//Если картинка - делаем ссылку превьюшкой, иначе титлом или именем файла.
		if ( $ext == 'jpg' or $ext == 'jpeg' or $ext == 'gif' or $ext == 'png'  ) {
			$cod2 = stripslashes(htmlspecialchars( '<a href="' . $uploads_url . $file . '"><img src="' . $uploads_url . 'mini/' . $file . '"></a>') );
		} else {
			if ($title) $cod2 = stripslashes(htmlspecialchars( '<a href="' . $uploads_url . $file . '">' . $title . '</a>') );
				else $cod2 = stripslashes(htmlspecialchars( '<a href="' . $uploads_url . $file . '">' . $file . '</a>') );
		}

		# $cod .= '<p><input type="text" style="width: 99%;" value="' . $cod2 . '">';
		$cod .= ' | <a href="#"
			onClick = "jAlert(\'<textarea cols=60 rows=5>' . $cod2 . '</textarea>\', \'HTML-ссылка файла\'); return false;">HTML-ссылка</a>';


		if ( $ext == 'jpg' or $ext == 'jpeg' or $ext == 'gif' or $ext == 'png'  )
		{
			if (file_exists( $uploads_dir . '_mso_i/' . $file  )) $_f = '_mso_i/' . $file;
			else $_f = $file;

			if (file_exists( $uploads_dir . 'mini/' . $file  ))
				$file_mini = '=' . $uploads_url . 'mini/' . $file;
			else $file_mini = '=' . $uploads_url . $file;


			// $cod3 = stripslashes(htmlspecialchars( '<a href="' . $uploads_url . $file . '"><img src="' . $uploads_url . $file . '"></a>') );
			//$cod .= '<p><input type="text" style="width: 99%;" value="' . $cod3 . '">';

			if ($title)
				$cod3 = stripslashes(htmlspecialchars( '[image' . $file_mini . ' ' . $title . ']' . $uploads_url . $file . '[/image]') );
			else
				$cod3 = stripslashes(htmlspecialchars( '[image' . $file_mini . ']' . $uploads_url . $file . '[/image]') );

			# $cod .= '<p><input type="text" style="width: 99%;" value="' . $cod3 . '">';
			$cod .= ' | <a href="#"
			onClick = "jAlert(\'<textarea cols=60 rows=6>' . $cod3 . '</textarea>\', \'Код [image] файла\'); return false;">Код [image]</a>';

			$predpr = '<a class="lightbox" href="' . $uploads_url . $file . '" target="_blank" title="' . $title . ' ('. $file . ')' . '"><img class="file_img" alt="" src="' . $uploads_url . $_f . '"></a>';

		}
		else
		{
			if ( $ext == 'mp3')
			{
				$predpr = '<a href="' . $uploads_url . $file . '" target="_blank" title="' . $title . ' ('. $file . ')' . '"><img class="file_img" alt="" src="' . getinfo('admin_url') . 'plugins/admin_files/mp3.png"></a>';

				$cod .= ' | <a href="#"
			onClick = "jAlert(\'<textarea cols=60 rows=6>' . stripslashes(htmlspecialchars( '[audio=' . $uploads_url . $file . ']') ) . '</textarea>\', \'Код [audio] файла\'); return false;">Код [audio]</a>';

			}
			else
			{
				$predpr = '<a href="' . $uploads_url . $file . '" target="_blank" title="' . $title . ' ('. $file . ')' . '"><img class="file_img" alt="" src="' . getinfo('admin_url') . 'plugins/admin_files/document_plain.png"></a>';
			}


		}

		// nicothin добавил:
		$cod .= '<br><a href="#" class="edit_descr_link" onClick="return false;">' . t('Изменить описание', 'admin') . '</a>';
		// конец добавления

		$out_all .= '<div class="cornerz">' . $sel . $predpr . $cod . '</div>';

		# $CI->table->add_row($predpr, $sel . $cod);
	}

	// добавляем форму, а также текущую сессию
	if ($out_all != '') 
	{
		echo '<form action="" method="post">' . mso_form_session('f_session_id');
		# echo $CI->table->generate(); // вывод подготовленной таблицы
		echo '<div class="float-parent" style="width:100%">';
		echo $out_all;
		echo '<div style="clear:both"></div></div>';

		echo '<p class="br"><input type="submit" name="f_delete_submit" value="' . t('Удалить', 'admin') . '" onClick="if(confirm(\'' . t('Выделенные файы будут безвозвратно удалены! Удалять?', 'admin') . '\')) {return true;} else {return false;}" ></p>
			<p class="br"><input type="button" id="check-all" value="' . t('Инвертировать выделение', 'admin') . '"></p>
			</form>';

		$n = '\n';
		$up = $uploads_url;

		$mess = t('Предварительно нужно выделить файлы для галереи', 'admin');
		$session = mso_form_session('f_session_id');
		$save_button = t('Сохранить', 'admin');

		echo <<<EOF
<script type="text/javascript">
function toggleAll() {
	var allCheckboxes = $(".cornerz input:checkbox:enabled");
	var notChecked = allCheckboxes.not(':checked');
	allCheckboxes.removeAttr('checked');
	notChecked.attr('checked', 'checked');
}

$(function()
{
	$("#check-all").click(function(){
		toggleAll()
	});

	//nicothin добавления
	if ($('script[src$="jquery/cornerz.js"]').size()) 
	{ 
		$('div.cornerz').cornerz({radius:10, background: "#FFFFFF"}); 
	}
	$('.edit_descr_link').toggle(function () 
	{
		if (!$(this).parent().parent().children('.edit_descr').size())
		{
			var file_name = $(this).parent().parent().children(':checkbox').attr('id');
			var old_descr = $(this).parent().parent().children('label').children('em').text();
			var form_code = '<div class="edit_descr" style="display:none"><form action="" method="post">{$session}<input type="hidden" name="f_file_name" value="' + file_name + '"><textarea name="f_file_description" >' + old_descr + '</textarea><br><input type="submit" name="f_edit_submit" value="{$save_button}"></form></div>';
			$(this).parent().parent().append(form_code);
		}
		$(this).parent().parent().find('.edit_descr').slideDown('fast');
	},
	function () {
		$(this).parent().parent().find('.edit_descr').slideUp('fast');
	});
	// nicothin конец добавления
	
	$('#gallerycodeclick').click(function()
	{
		$('#gallerycode').html('');

		codegal = '';
		$("input[name='f_check_files[]']").each( function(i)
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
			alert('{$mess}');
		}
	});
});
</script>
<hr class="br">
EOF;
		echo '<h2 class="br">' . t('Создание галереи', 'admin') . '</h2>
		<p>' . t('Выделите нужные файлы. (У вас должен быть активирован плагин <strong>LightBox</strong>)', 'admin') . '</p>
		<p>' . t('Название:', 'admin') . ' <input type="text" id="gallerycodename" value=""> ' . t('(если нужно)', 'admin') . '<br><input class="br" type="button" id="gallerycodeclick" value="' . t('Генерировать код галереи', 'admin') . '">
		</p>
		<p><textarea id="gallerycode" style="display: none"></textarea>
		';
	}
	else
	{
		echo '<p>' . t('Нет загруженных файлов', 'admin') . '</p>';
	}

?>