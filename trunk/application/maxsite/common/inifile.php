<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 * Функции для ini-файлов
 */

# загружаем ini-файл
function mso_get_ini_file($file = '') 
{
	if ( !file_exists( $file ) ) return false;
	return parse_ini_file($file, true);
}


# проверяем есть ли POST
function mso_check_post_ini() 
{
	$CI = & get_instance();
	
	// проверяем входящие данные - поля всегда одни
	if ( $post = mso_check_post(array('f_session_id', 'f_options', 'f_submit', 'f_ini')) )
	{
		# защита рефера
		mso_checkreferer();
		
		$options = $post['f_options'];
		
		if ( isset($post['f_all_checkbox']) ) $all_checkbox = $post['f_all_checkbox'];
			else $all_checkbox = array();
		
		// добавим к $options $all_checkbox если их нет
		// и сразу заменим on на 1
		foreach ($all_checkbox as $key=>$val)
		{
			if (!isset($options[$key])) $options[$key] = '0';
				else $options[$key] = '1';
		}
		
		// pr($options);
		// pr($all_checkbox);
		
		foreach ($options as $key_type => $val)
		{
			// разделим имя опции на ключ и группу
			$key_type = explode('_m_s_o_', $key_type); 
			$key = $key_type[0];
			$type = $key_type[1];
			
			mso_add_option($key, $val, $type); // добавляем опцию
		}
		
		mso_flush_cache();
		// посколько у нас всегда true, то результат не анализируем
		return true;
	}
	
	return false;
}

# проверяем вхождение PHP_START функция PHP_END
# если есть, то вычлиняем имя функции и выполняем её - полученный разультат 
# вставляем вместо этой конструкции
# обрабатываются только values, description и default
function _mso_ini_check_php($value)
{
	$value = preg_replace_callback('!(PHP_START)(.*?)(PHP_END)!is', '_mso_ini_check_php_callback', $value);
	return $value;
}

function _mso_ini_check_php_callback($matches)
{
	$f = trim($matches[2]);
	if (function_exists($f)) $r = $f();
		else $r = '';	
	return $r;
}

# вывод ini-полей в виде таблицы
function mso_view_ini($all = false) 
{
	if (!$all) return '';
	
	$CI = & get_instance();
	
	$CI->load->library('table');

	$tmpl = array (
                    'table_open'          => '<table class="page" border="0" width="99%"><colgroup style="width: 25%;">',
                    'row_alt_start'		  => '<tr class="alt">',
					'cell_alt_start'	  => '<td class="alt">',
              );

	$CI->table->set_template($tmpl); // шаблон таблицы
	
	// заголовки
	$CI->table->set_heading(t('Настройка'), t('Значение'));
	
	$out = '';
	
	foreach ($all as $key=>$row)
	{
		if ( isset($row['options_key']) ) $options_key = stripslashes( trim( $row['options_key'] ) );
			else continue;
		
		if ( !isset($row['options_type']) ) $options_type = 'general';
			else $options_type = stripslashes(trim($row['options_type']));
			
		if ( !isset($row['type']) ) $type = 'textfield';
			else $type = stripslashes(trim($row['type']));
		
		if ( !isset($row['values']) ) $value = '';
			else $values = _mso_ini_check_php(stripslashes(htmlspecialchars(trim($row['values']))));
			
		if ( !isset($row['description']) ) $description = '';
			else $description = _mso_ini_check_php(stripslashes( trim( t($row['description']))));
		
		if ( !isset($row['delimer']) ) $delimer = '<br />';
			else $delimer = stripslashes($row['delimer']);
			
		if ( !isset($row['default']) ) $default = '';
			else $default = _mso_ini_check_php(stripslashes(htmlspecialchars(trim($row['default']))));
		
		$options_present = true; // признак, что опция есть в базе
		
		// получаем текущее значение опции
		$CI->db->select('options_value');
		$CI->db->where( array('options_type'=>$options_type, 'options_key'=>$options_key ) );
		$query = $CI->db->get('options');
		
		if ($query->num_rows() > 0 ) // есть запись
		{
			$val = $query->row_array();
			$value = htmlspecialchars($val['options_value']);
		}
		else 
		{
			$options_present = false;
			$value = $default; // нет значание, поэтому берем дефолт
		}
		
		$f = NR; 

		$name_f = 'f_options[' . $options_key . '_m_s_o_' . $options_type . ']'; // название поля 
		
		if ($type == 'textfield')
		{
			$value = str_replace('_QUOT_', '&quot;', $value);
			$f .= '<input style="width: 99%;" type="text" name="' . $name_f . '" value="' . $value . '" />' . NR;
		}
		elseif ($type == 'textarea')
		{
			$value = str_replace('_NR_', "\n", $value);
			$f .= '<textarea style="width: 99%;" rows="7" name="' . $name_f . '">'. $value . '</textarea>' . NR;
		}
		elseif ($type == 'checkbox')
		{
			if ($value) $checked = 'checked="checked"';
				else $checked = '';
				
			$f .= '<label><input type="checkbox" name="' . $name_f . '" ' . $checked . ' /> ' 
			. $key . '</label>' 
			. NR;
			
			$f .= '<input type="hidden" name="f_all_checkbox[' . $options_key . '_m_s_o_' . $options_type . ']" />' . NR;
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
					// $val может быть с || val - текст
					
					$val = trim($val);
					$val_t = $val;
					
					$ar = explode('||', $val);
					if (isset($ar[0])) $val = trim($ar[0]);
					if (isset($ar[1])) $val_t = trim($ar[1]);
					
					if ($value == $val) $checked = 'selected="selected"';
						else $checked = '';
					$f .= NR . '<option value="' . $val . '" ' . $checked . ' />' . $val_t . '</option>';
				}
				$f .= NR . '</select>' . NR;
			}
		}
		
		if ($description) $f .= '<p><em>' .  $description . '</em></p>';
		if (!$options_present) 
			$key = '<span title="' . $options_key . '" style="color: red;">* ' . t($key) . ' (нет в базе)</span>';
		else 
			$key = '<strong title="' . $options_key . '">' . t($key) . '</strong>';
			
		$CI->table->add_row($key, $f);
	}
	
	
	$out .= '<form action="" method="post">' . mso_form_session('f_session_id');
	$out .= '<input type="hidden" value="1" name="f_ini" />'; // доп. поле - индикатор, что это ini-форма
	$out .= $CI->table->generate(); // вывод подготовленной таблицы
	$out .= NR . '<br /><input type="submit" name="f_submit" value="' . t('Сохранить') . '">';
	$out .= '</form>';
	
	return $out;
}




?>