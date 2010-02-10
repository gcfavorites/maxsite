<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (с) http://maxsite.org/
 */

	# получение всех мета из ini-файла 
	# результат в  $all_meta
	# мета-поля, которые следует здесь отобразить описываются в ini-файле.

	$all_meta = '';
	
	
	// получим одним запросом все мета поля 
	if ($id)
	{
		$CI->db->select('meta_value, meta_key');
		$CI->db->where( array ('meta_id_obj' => $id, 'meta_table' => 'page' ) );
		$query = $CI->db->get('meta');
		
		$page_all_meta = array();
		foreach ($query->result_array() as $row)
		{
			$page_all_meta[$row['meta_key']][] = $row['meta_value'];
		}
	}
	else
	{
		$page_all_meta = array();
	}
	
	// pr($page_all_meta);
	
	require_once( getinfo('common_dir') . 'inifile.php' ); // функции для работы с ini-файлом
	
	// получим все данные из ini-файла
	$all = mso_get_ini_file( $MSO->config['admin_plugins_dir'] . 'admin_page/meta.ini');
	
	//  pr($all);
	
	// подключаем meta.ini из текущего шаблона
	if (file_exists(getinfo('template_dir') . 'meta.ini')) 
	{
		$meta_templ = mso_get_ini_file( getinfo('template_dir') . 'meta.ini' );
		//pr($meta_templ);
		if ($meta_templ) $all = array_merge($all, $meta_templ);
		
	}

	// pr($all);

	// проходимся по всем ini-опциям
	// для совместимости используем вместо meta_  options_
	foreach ($all as $key=>$row)
	{
		if ( isset($row['options_key']) ) $options_key = stripslashes(trim($row['options_key']));
			else continue;
		
		if ($options_key == 'tags') continue; // метки отдельно идут
		
		if ( !isset($row['type']) ) $type = 'textfield';
			else $type = stripslashes(trim($row['type']));
		
		if ( !isset($row['values']) ) $value = '';
			else $values = stripslashes(htmlspecialchars(trim($row['values'])));
			
		if ( !isset($row['description']) ) $description = '';
			else $description = stripslashes(trim($row['description']));
			
		if ( !isset($row['delimer']) ) $delimer = '<br />';
			else $delimer = stripslashes($row['delimer']);	
			
		if ( !isset($row['default']) ) $default = '';
			else $default = stripslashes(htmlspecialchars(trim($row['default'])));
		
		$options_present = true; // признак, что опция есть в базе
		
		// получаем текущее значение 
		
		if (isset($page_all_meta[$options_key])) // есть в мета
		{
			foreach ($page_all_meta[$options_key] as $val)
			{
				$value = htmlspecialchars($val);
			}
		}
		else 
		{
			$options_present = false;
			$value = $default; // нет значание, поэтому берем дефолт
		}
		
		$f = NR; 

		$name_f = 'f_options[' . $options_key . ']'; // название поля 
		
		if ($type == 'textfield')
		{
			$f .= '<input style="width: 99%;" type="text" name="' . $name_f . '" value="' . $value . '" />' . NR;
		}
		elseif ($type == 'textarea')
		{
			$f .= '<textarea style="width: 99%;" name="' . $name_f . '">'. $value . '</textarea>' . NR;
		}
		elseif ($type == 'radio')
		{
			$values = explode('#', $values); // все значения разделены #
			
			if ($values) // есть что-то
			{
				foreach( $values as $val ) 
				{
					if ($value == trim($val)) $checked = 'checked="checked"';
						else $checked = '';
						
					$f .= '<input type="radio" name="' . $name_f . '" value="' . trim($val) . '" ' 
							. $checked . ' /> ' . trim($val) . $delimer . NR;
				}
			}
		}
		elseif ($type == 'select')
		{
			$values = explode('#', $values); // все значения разделены #
			
			if ($values) // есть что-то
			{
				$f .= '<select style="width: 99%;" name="' . $name_f . '">';
				
				foreach( $values as $val ) 
				{
					if ($value == trim($val)) $checked = 'selected="selected"';
						else $checked = '';
					$f .= NR . '<option value="' . trim($val) . '" ' . $checked . ' />' . trim($val) . '</option>';
				}
				$f .= NR . '</select>' . NR;
			}
		}
		
		if ($description) $f .= '<p>' .  $description . '</p>';
		$key = '<h3>' . $key . '</h3>';
		
		$all_meta .= '<div>' . $key . NR . $f . '</div>';
	}


?>