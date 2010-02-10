<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Основные функции MaxSite CMS
 * (с) http://maxsite.org/
 */

define("NR", "\n");

#  функция для отладки
function pr($var, $html = false) 
{
	echo '<pre>';
	if (is_bool($var))
	{
		if ($var) echo 'TRUE';
			else echo 'FALSE';
	}
	else
	{
		if ( is_scalar($var) ) 
		{
			if (!$html) echo $var;
				else 
				{	
					$var = str_replace('<br />', "<br />\n", $var);
					$var = str_replace('</p>', "</p>\n", $var);
					$var = str_replace('<ul>', "\n<ul>", $var);
					$var = str_replace('<li>', "\n<li>", $var);
					$var = htmlspecialchars($var);
					$var = wordwrap($var, 300);
					echo $var;
				}
		}
			else print_r ($var);
	}
	echo '</pre>';
}


#  определяем наличие библиотеки mb_string 
function mso_mb_present() 
{
	if ( function_exists('mb_strpos') ) return true;
		else return false;
}


#  правильность email 
function mso_valid_email($em = '') 
{
	if ( eregi("^[a-z0-9\._+-]+@+[a-z0-9\._-]+\.+[a-z]{2,3}$", $em) )
		return true;
	else
		return false;
}


# проверем рефер на xss-атаку
function mso_checkreferer() 
{	
	$p = parse_url($_SERVER['HTTP_REFERER']);
	
	if (isset($p['host'])) $p = $p['host'];
		else $p = '';
	
	if ( $p != $_SERVER['HTTP_HOST'] )
	{
		if ($_POST) die('<b><font color="red">Achtung! XSS attack!</font></b>');
	}
}


# защита сессии 
# сравниваем переданную сессию с текущей
# и если указан редирект, то в случае несовпадения переходим по нему
# иначе возвращаем true - если все ок и false - ошибка сессии
function mso_checksession($session_id, $redirect = false) 
{
	global $MSO;
	
	$result = ($MSO->data['session']['session_id'] == $session_id );
	
	if ($redirect and !$result) 
	{
		mso_redirect($redirect);
		return;
	}

	return $result;
}


# удаляем все лишнее в формах
# если второй параметр = true то возвращает false, если данные после стрипа изменились и $s - теже
function mso_strip($s = '', $logical = false) 
{	
	$s1 = $s;
	$s1 = stripslashes($s1);
	$s1 = strip_tags($s1);
	$s1 = htmlspecialchars($s1, ENT_QUOTES);
	
	$arr_strip = array('\\', '|', '/', '?', '%', '*', '`');
	$s1 = str_replace($arr_strip, '', $s1);
	$s1 = trim($s1);
	
	if ($logical)
	{
		if ($s1 === $s) return $s;
			else return false;
	}
	else
		return $s1;
}


# функция инициализации
function mso_initalizing()
{
	global $MSO;
	
	# считываем файл конфигурации
	$fn = $MSO->config['config_file'];
	if ( file_exists($fn) ) require_once ($fn);
	
	# стоит ли флаг, что уже произведена инсталяция?
	if ($mso_install == false)
	{
		$CI = & get_instance();
		if ( !$CI->db->table_exists('options')) return false; # еще не установлен сайт
	}
	
	# подключаем опции - они могут быть в кэше
	global $cache_options;
	
	if ( $opt = mso_get_cache('options') ) # есть кэш опций
		$cache_options = $opt;
	else 
		mso_refresh_options(); # обновляем кэш опций
	
	
	# проверим текущий шаблон
	$template = mso_get_option('template', 'general'); // считали из опций
	$index = $MSO->config['templates_dir'] . $template . '/index.php'; // проверим в реале
	
	if (!file_exists($index)) // нет такого шаблона - меняем на дефолтный
	{ 
		mso_add_option('template', 'default', 'general');
		$MSO->config['template'] = 'default';
	}
	else // все ок
		$MSO->config['template'] = $template;
	
	# подключим файл functions.php в шаблоне - если есть
	$functions_file = $MSO->config['templates_dir'] . $MSO->config['template'] . '/functions.php';
	if (file_exists($functions_file)) 
	{ 
		require_once($functions_file);
	}
}

# загружаем включенные плагины
function mso_autoload_plugins()
{
	global $MSO;
	
	// функция mso_autoload_custom может быть в mso_config.php
	if ( function_exists('mso_autoload_custom') ) mso_autoload_custom();
	
	$d = mso_get_option('active_plugins', 'general');
	if (!$d) 
	{
		$d = $MSO->active_plugins;
		// mso_add_option ('active_plugins', $d, 'general');
	}

	foreach ($d as $load) mso_plugin_load($load);
}

# проверка типа страницы
function is_type($type)
{
	global $MSO;
	return ( $MSO->data['type'] == $type ) ? true : false;
}

# проверка если feed
function is_feed()
{
	global $MSO;
	return $MSO->data['is_feed'] ? true : false;
}


# получение нужного значения
function getinfo($info = '')
{
	global $MSO;
	
	$out = '';
	
	switch ($info) :
		case 'version' : 
				$out = $MSO->version;
				break;
			 
		case 'siteurl' :
				$out = $MSO->config['site_url'];
				break;
		
		case 'stylesheet_url' :
				$out = $MSO->config['templates_url'] 
						. $MSO->config['template']  # название из 
						. '/';
				break;
				
		case 'template' :
				$out = $MSO->config['template'];
				break;
				
		case 'template_dir' :
				$out = $MSO->config['templates_dir'] . $MSO->config['template'] . '/';
				break;				
				
		case 'url_new_comment' :
				$out = $MSO->config['site_url'] . 'newcomment';
				break;
				
		case 'pingback_url' :

				break;
		
		case 'rss_url' :
				$out = $MSO->config['site_url'] . 'feed';
				break;
				
		case 'feed' :
				$out = $MSO->config['site_url'] . 'feed';
				break;

		case 'atom_url' :
		
				break;
		
		case 'comments_rss2_url' :
		
				break;
		case 'admin_url' :
				$out = $MSO->config['admin_url']; // [admin_url] => http://localhost/codeigniter/application/maxsite/admin/
				break;
				
		case 'site_admin_url' :
				$out = $MSO->config['site_admin_url']; // [site_admin_url] => http://localhost/codeigniter/admin/
				break;
				
		case 'common_dir' :
				$out = $MSO->config['common_dir'];
				break;
				
		case 'uploads_url' :
				$out = $MSO->config['uploads_url'];
				break;
				
		case 'users_nik' :
				$out = $MSO->data['session']['users_nik'];
				break;
		
		case 'users_id' :
				$out = $MSO->data['session']['users_id'];
				break;
				
		case 'name_site' :
				$out = mso_get_option('name_site', 'general');
				break;
				
		case 'description_site' :
				$out = mso_get_option('description_site', 'general');
				break;
				
		case 'title' :
				$out = mso_get_option('title', 'general');
				break;
				
		case 'description' :
				$out = mso_get_option('description', 'general');
				break;
			
		case 'keywords' :
				$out = mso_get_option('keywords', 'general');
				break;
			
		case 'time_zone' :
				$out = (float) mso_get_option('time_zone', 'general');
				break;
		
	endswitch;
	
	return $out;
}


# вывод html meta титла дескриптон или keywords страницы
function mso_head_meta($info = 'title', $args = '', $format = '%page_title%', $sep = '', $only_meta = false )
{
	// ошибочный info
	if ( $info!='title' and $info!='description' and $info!='keywords') return '';
	
	global $MSO;
	
	// измененный для вывода титле хранится в $MSO->title description или keywords
	
	if (!$args) // нет аргумента - выводим что есть
	{
		if ( !$MSO->$info )	$out = $MSO->$info = getinfo($info);
		else $out = $MSO->$info;
	}
	else // есть аргументы
	{
		if (is_scalar($args)) $out = $args; // какая-то явная строка - отдаем её как есть
		else // входной массив - скорее всего это страница
		{
			// %page_title% %title% %category_name%
			// | это разделитель, который = $sep
			
			$category_name = '';
			$page_title = '';
			$title = getinfo($info);
			
			// pr($args);
			if ( isset($args[0]['category_name']) ) $category_name = $args[0]['category_name'];
			
			if ( isset($args[0]['page_title']) ) $page_title = $args[0]['page_title'];
			
			// если есть мета, то берем её
			if ( isset($args[0]['page_meta'][$info][0]) and $args[0]['page_meta'][$info][0] )
			{
				if ( $only_meta ) $category_name = $title = $sep = '';
				$page_title = $args[0]['page_meta'][$info][0];
			}

			$arr_key = array( '%title%', '%page_title%',  '%category_name%', '|' );
			$arr_val = array( $title ,  $page_title, $category_name, $sep );
			
			$out = str_replace($arr_key, $arr_val, $format);
		}
	}
	
	// отдаем результат, сразу же указывая измененный $info в $MSO->
	$out = $MSO->$info = trim($out);
	
	return $out;
}


# подключение плагина
function mso_plugin_load($plugin = '')
{
	global $MSO;
	
	// $fn_plugin = $MSO->config['plugins_dir'] . $plugin . '/' . $plugin . '.php';
	$fn_plugin = $MSO->config['plugins_dir'] . $plugin . '/index.php';
	
	if ( !file_exists( $fn_plugin ) ) return false;
	else
	{
		require_once ($fn_plugin);
		
		$auto_load = $plugin . '_autoload';
		if ( function_exists($auto_load) ) $auto_load();
		
		# добавим плагин в список активных
		$MSO->active_plugins[] = $plugin;
		$MSO->active_plugins = array_unique($MSO->active_plugins);
		sort($MSO->active_plugins);
		
		return true;
	}
}


# подключение admin-плагина - выполняется только при входе в админку
function mso_admin_plugin_load($plugin = '')
{
	global $MSO;
	
	// $fn_plugin = $MSO->config['admin_plugins_dir'] . $plugin . '/' . $plugin . '.php';
	$fn_plugin = $MSO->config['admin_plugins_dir'] . $plugin . '/index.php';
	
	if ( !file_exists( $fn_plugin ) ) return false;
	else
	{
		require_once ($fn_plugin);
		
		$auto_load = $plugin . '_autoload';
		if ( function_exists($auto_load) ) $auto_load();
		
		return true;
	}
}


# подключение функции к хуку
function mso_hook_add($hook, $func, $priory = 0)
{
	global $MSO;
	
	$priory = (int) $priory;
	
	if ( $priory > 0 ) $MSO->hooks[$hook][$func] = $priory;
		else $MSO->hooks[$hook][$func] = 0;
	
	arsort($MSO->hooks[$hook]);	
}


# прописываем хук к admin_url_+hook 
function mso_admin_url_hook($hook, $func, $priory = 0)
{
	// нельзя указывать хуки на зарезервированные адреса: ???
	$hook = strtolower($hook);
	$no_hook = array('', 'pa1ge', 'c1at', 'pl1ugins', 'opti1ons', 'new1_page', 'ed1it_page', 'n1ew_cat', 'ed1it_cat');
	
	if ( !in_array($hook, $no_hook))
		mso_hook_add ('admin_url_' . $hook, $func, $priory = 0);
}


# выполнение хуков 
# название хука - переменная для результата
function mso_hook($hook = '', $result = '', $result_if_no_hook = '_mso_result_if_no_hook') 
{
	global $MSO;
	if ($hook == '') return $result;
	
	$arr = array_keys($MSO->hooks);
	if ( !in_array($hook, $arr) ) // если нука нет
	{	
		if ($result_if_no_hook != '_mso_result_if_no_hook') // если указана $result_if_no_hook
			return $result_if_no_hook;
		else return $result;
	}
	
	foreach ( $MSO->hooks[$hook] as $func => $val)
		if ( function_exists($func) ) $result = $func($result);
	
	return $result;
}


# проверяет существование хука
function mso_hook_present($hook = '') 
{
	global $MSO;
	if ($hook == '') return false;
	$arr = array_keys($MSO->hooks);
	if ( !in_array($hook, $arr) ) return false;
		else return true;
}


# удаляет из хука функцию
function mso_remove_hook($hook = '', $func = '')
{
	global $MSO;
	if ($hook == '') return false;
	if ($func == '') return false;
	
	$arr = array_keys($MSO->hooks);
	if ( !in_array($hook, $arr) ) return false;
	
	unset($MSO->hooks[$hook][$func]);
}

# динамическое создание функции на хук
# тело функции дожно работать как нормальный php
# функция принимает только один аргумент $args
function mso_hook_add_dinamic( $hook = '', $func = '' )
{
	if ($hook == '') return false;
	if ($func == '') return false;
	
	$func_name = @create_function('$args', $func);
	
	return mso_hook_add( $hook, $func_name);
}


# генератор md5 свой
function mso_md5($t = '')
{
	global $MSO;

	if ($MSO->config['secret_key'])
		return strrev( md5($t . $MSO->config['secret_key']) );
	else 
		return strrev( md5($t . $MSO->config['site_url']) );
}


# сброс кэша опций
function mso_refresh_options()
{
	global $cache_options;

	$CI = & get_instance();
	
	/* 
	$cache_options = 
		type = array (
				key  = value
				key1 = value2
				)
	*/ 
	
	$query = $CI->db->get('options');

	$cache_options = array();
	
	// $query = $ci->db->query('SELECT * FROM ci_options ORDER BY options_type');
	
	foreach ($query->result() as $row)
		$cache_options[$row->options_type][$row->options_key] = $row->options_value;

	mso_add_cache('options', $cache_options);
	
	return $cache_options;
}


# добавление в таблицу опций options
function mso_add_option($key, $value, $type)
{
	# если value массив или объект, то серилизуем его в строку
	if ( !is_scalar($value) ) $value = '_serialize_' . serialize($value);
	
	$data = array(
			'options_key'=>$key, 
			'options_type'=>$type );
	
	$CI = & get_instance();
	
	# проверим есть ли уже такой ключ		
	$CI->db->select('options_id');
	$CI->db->from('options');
	$CI->db->where($data);
	
	$query = $CI->db->get();
	
	if ($query->num_rows() > 0 ) # есть уже такой ключ, поэтому обновляем его значение
	{
		$CI->db->where($data);
		$data['options_value'] = $value;
		$CI->db->update('options', $data);
	}
	else # новый ключ
	{
		$data['options_value'] = $value;
		$CI->db->insert('options', $data); 
	}

	mso_refresh_options(); # обновляем опции из базы

	return true;
}

# удаление в таблице опций options ключа с типом
function mso_delete_option($key, $type)
{
	$CI = & get_instance();

	$CI->db->where('options_key', $key);
	$CI->db->where('options_type', $type);
	$CI->db->limit(1);
	$query = $CI->db->get('options');

	mso_refresh_options(); # обновляем опции из базы

	return true;
}

# получение опции из кэша опций
function mso_get_option($key, $type = 'general', $return_value = false)
{
	global $cache_options;
	
	if ( isset($cache_options[$type][$key]) )
		$result = $cache_options[$type][$key];
	else 
		$result = $return_value;
	
	// проверяем на сериализацию
	if (@preg_match( '|_serialize_|A', $result))
	{
		$result = preg_replace( '|_serialize_|A', '', $result, 1 );
		$result = unserialize($result);
	}

	return $result;
}


# добавить кеш
# ключ, значение, время
function mso_add_cache($key, $output, $time = false, $custom_fn = false)
{
	/* 
	Функция взята из _write_cache output.php - немного переделанная
	*/
	
	global $MSO;
	
	$CI = & get_instance();	
	$path = $CI->config->item('cache_path');
	$cache_path = ($path == '') ? BASEPATH.'cache/' : $path;
	
	if ( ! is_dir($cache_path) OR ! is_writable($cache_path))
		return;
	
	if (!$custom_fn)
		$cache_path .= mso_md5($key . $CI->config->item('base_url'));
	else 
		$cache_path .= $key;

	if ( ! $fp = @fopen($cache_path, 'wb'))
	{
		log_message('error', "Unable to write cache file: ".$cache_path);
		return;
	}
	
	if (!$time) $time = $MSO->config['cache_time'];
	
	$expire = time() + $time;
	$output = serialize($output);
	
	flock($fp, LOCK_EX);
	fwrite($fp, $expire.'TS--->' . $output);
	flock($fp, LOCK_UN);
	fclose($fp);
	@chmod($cache_path, 0777);

	log_message('debug', "Cache file written: ".$cache_path);
}


# сбросить весь кэш
function mso_flush_cache()
{
	$CI = & get_instance();	
	$path = $CI->config->item('cache_path');

	$cache_path = ($path == '') ? BASEPATH.'cache/' : $path;
		
	if ( ! is_dir($cache_path) OR ! is_writable($cache_path))
		return FALSE;
	
	// находим в каталоге все файлы и их удалаяем
	$CI->load->helper('file_helper');
	delete_files($cache_path);
}

# получить кеш по ключу
function mso_get_cache($key, $custom_fn = false)
{
	/* 
	Функция взята из _display_cache output.php - переделанная
	*/
	
	$CI = & get_instance();	
	$path = $CI->config->item('cache_path');

	$cache_path = ($path == '') ? BASEPATH.'cache/' : $path;
		
	if ( ! is_dir($cache_path) OR ! is_writable($cache_path))
		return FALSE;
		
	if (!$custom_fn)
		$filepath = $cache_path . mso_md5($key . $CI->config->item('base_url'));
	else
		$filepath = $cache_path . $key;

	if ( ! @file_exists($filepath))
		return FALSE;
	
	if ( ! $fp = @fopen($filepath, 'rb'))
		return FALSE;
		
	flock($fp, LOCK_SH);
	
	$cache = '';
	if (filesize($filepath) > 0)
		$cache = fread($fp, filesize($filepath));

	flock($fp, LOCK_UN);
	fclose($fp);
				
	if ( ! preg_match("/(\d+TS--->)/", $cache, $match))
		return FALSE;
	
	if (time() >= trim(str_replace('TS--->', '', $match['1'])))
	{ 		
		@unlink($filepath);
		log_message('debug', "Cache file has expired. File deleted");
		return FALSE;
	}

	$out = str_replace($match['0'], '', $cache);
	$out = @unserialize($out);
	
	log_message('debug', "Cache file is current. Sending it to browser.");		
	return $out;
}


# преобразование html-спецсимволов в тексте в обычный html
function mso_text_to_html($content) 
{
	//$content = str_replace(chr(10), "\n", $content);
	//$content = str_replace(chr(13), " ", $content);
	//$content = str_replace('&lt;', '<', $content);
	//$content = str_replace('&gt;', '>', $content);
	//$content = str_replace('\"', '&quot;', $content);
	//$content = str_replace('\\', '\\\\',$content);
	//$content = str_replace('\'', '&#039',$content);
	//$content = str_replace('&lt;','<', $content);
	//$content = str_replace('&gt;','>', $content);
	//$content = str_replace('&quot;','\"', $content);
	//$content = str_replace('&#039','\'', $content);
	// $content = str_replace('&amp;','&', $content);
		
	// $content = htmlspecialchars($content, ENT_QUOTES);
	
	return $content;
}


# преобразование html-спецсимволов 
function mso_html_to_text($content) 
{
	//$content = str_replace(chr(10), " ", $content);
	//$content = str_replace(chr(13), "", $content);
	//$content = str_replace('&lt;', '<', $content);
	//$content = str_replace('&gt;', '>', $content);
	//$content = str_replace('&amp;','&', $content);
	
	//$content = str_replace('\"', '&quot;', $content);
	//$content = str_replace('\\', '\\\\',$content);
	//$content = str_replace('\'', '&#039',$content);
	//$content = str_replace('&lt;','<', $content);
	//$content = str_replace('&gt;','>', $content);
	//$content = str_replace('&quot;','\"', $content);
	//$content = str_replace('&#039','\'', $content);
		
	// $content = htmlspecialchars($content, ENT_QUOTES);
	
	return $content;
}

# подчистка PRE + mso_auto_tag
function mso_clean_pre_special_chars($matches) 
{
	if ( is_array($matches) )
	{
		$m = $matches[2];

		$m = str_replace('<p>', '', $m);
		$m = str_replace('</p>', '', $m);
		$m = str_replace("<br />", "[mso_br_pre]", $m);
		
		$m = htmlspecialchars($m, ENT_QUOTES);
		
		// для смайлов избежать конфликта
		$arr1 = array(':', '\'', '(', ')', '|', '-');
		$arr2 = array('&#58;', '&#39;', '&#40;', '&#41;', '&#124;', '&#45;');
		$m = str_replace($arr1, $arr2, $m);
		
		$text = "\n\n" . $matches[1] . $m . "</pre>\n\n";
	}
	else
		$text = $matches;

	$text = str_replace('<p>', '', $text);
	$text = str_replace('</p>', '', $text);
	$text = str_replace("<br />", "[mso_br_pre]", $text);

	return $text;
}

# подчистка PRE + mso_auto_tag
function mso_clean_pre($matches) 
{
	if ( is_array($matches) )
		$text = "\n\n" . $matches[1] . $matches[2] . "</pre>\n\n";
	else
		$text = $matches;

	$text = str_replace('<p>', '', $text);
	$text = str_replace('</p>', '', $text);
	$text = str_replace("<br />", "[mso_br_pre]", $text);

	return $text;
}

# авторасстановка тэгов
function mso_auto_tag($pee, $pre_special_chars = false) 
{
	$pee = $pee . "\n";
	$pee = str_replace(array("\r\n", "\r"), "\n", $pee);
	$pee = str_replace("\n", "<br />", $pee);
	$pee = str_replace('<br>', '<br />', $pee);
	if ($pre_special_chars)
	{
		if (strpos($pee, '<pre') !== false) $pee = preg_replace_callback('!(<pre.*?>)(.*?)</pre>!is', 'mso_clean_pre_special_chars', $pee );
	}
	else
	{
		if (strpos($pee, '<pre') !== false) $pee = preg_replace_callback('!(<pre.*?>)(.*?)</pre>!is', 'mso_clean_pre', $pee );
	}
	
	
	$pee = preg_replace('|<br />\s*<br />|', "\n\n", $pee);
	// $pee = str_replace('<br />', '', $pee);
	$pee = str_replace('<br />', "\n\n", $pee);
	
	$allblocks = '(?:table|thead|tfoot|caption|colgroup|center|tbody|tr|td|th|div|dl|dd|dt|ul|ol|li|pre|select|form|map|area|blockquote|address|math|style|input|embed|p|h[1-6]|hr)';
	$pee = preg_replace('!(<' . $allblocks . '[^>]*>)!', "\n$1", $pee);
	$pee = preg_replace('!(</' . $allblocks . '>)!', "$1\n\n", $pee);

	$pee = str_replace("\n\n\n\n\n", "[mso_n]", $pee);
	$pee = str_replace("\n\n\n\n", "[mso_n]", $pee);
	$pee = str_replace("\n\n\n", "[mso_n]", $pee);
	$pee = str_replace("\n\n", "[mso_n]", $pee);
	$pee = str_replace('[mso_n]', "\n\n", $pee);
	
	$pee = preg_replace('/\n?(.+?)(?:\n\s*\n|\z)/s', "<p>$1</p>\n", $pee); 
	$pee = preg_replace('|<p>\s*?</p>|', '', $pee);

	$pee = preg_replace('!<p>([^<]+)\s*?(</(?:div|address|form)[^>]*>)!', "<p>$1</p>$2", $pee);
	$pee = preg_replace( '|<p>|', "$1<p>", $pee );
	$pee = preg_replace('!<p>\s*(</?' . $allblocks . '[^>]*>)\s*</p>!', "$1", $pee); 
	$pee = preg_replace("|<p>(<li.+?)</p>|", "$1", $pee);
	$pee = preg_replace('|<p><blockquote([^>]*)>|i', "<blockquote$1><p>", $pee);
	$pee = str_replace('</blockquote></p>', '</p></blockquote>', $pee);
	
	$pee = preg_replace('!<p>\s*(</?' . $allblocks . '[^>]*>)!', "$1", $pee);
	$pee = preg_replace('!(</?' . $allblocks . '[^>]*>)\s*</p>!', "$1", $pee);
	$pee = preg_replace( "|\n</p>$|", '</p>', $pee );
	
	$pee = str_replace('<p>[cut]</p>', '[cut]', $pee);
	$pee = str_replace('<p>[page]</p>', '[page]', $pee);
	
	$pee = str_replace('[mso_br_pre]', "\n", $pee);
	
	return $pee;
}

/*
# функция расстановки тэгов
# в наглую выдрана из WordPress wpautop()
function mso_auto_tag_wp($pee, $br = 1) 
{
	// return $pee;
	$pee = $pee . "\n"; // just to make things a little easier, pad the end
	
	$pee = str_replace('<br>', '<br />', $pee);
	$pee = str_replace('<p>', '<br />', $pee);
	
	$pee = preg_replace('|<br />\s*<br />|', "\n\n", $pee);
	// Space things out a little
	$allblocks = '(?:table|thead|tfoot|caption|colgroup|center|tbody|tr|td|th|div|dl|dd|dt|ul|ol|li|pre|select|form|map|area|blockquote|address|math|style|input|embed|p|h[1-6]|hr)';
	$pee = preg_replace('!(<' . $allblocks . '[^>]*>)!', "\n$1", $pee);
	$pee = preg_replace('!(</' . $allblocks . '>)!', "$1\n\n", $pee);
	$pee = str_replace(array("\r\n", "\r"), "\n", $pee); // cross-platform newlines
	$pee = preg_replace("/\n\n+/", "\n\n", $pee); // take care of duplicates
	// return $pee;
	
	$pee = preg_replace('/\n?(.+?)(?:\n\s*\n|\z)/s', "<p>$1</p>\n", $pee); // make paragraphs, including one at the end
	
	$pee = preg_replace('|<p>\s*?</p>|', '', $pee); // under certain strange conditions it could create a P of entirely whitespace
	$pee = preg_replace('!<p>([^<]+)\s*?(</(?:div|address|form)[^>]*>)!', "<p>$1</p>$2", $pee);
	$pee = preg_replace( '|<p>|', "$1<p>", $pee );
	$pee = preg_replace('!<p>\s*(</?' . $allblocks . '[^>]*>)\s*</p>!', "$1", $pee); // don't pee all over a tag
	$pee = preg_replace("|<p>(<li.+?)</p>|", "$1", $pee); // problem with nested lists
	$pee = preg_replace('|<p><blockquote([^>]*)>|i', "<blockquote$1><p>", $pee);
	$pee = str_replace('</blockquote></p>', '</p></blockquote>', $pee);
	
	
	$pee = str_replace('<p>[cut]</p>', '[cut]', $pee);
	$pee = str_replace('<p>[page]</p>', '[page]', $pee);
	
	$pee = preg_replace('!<p>\s*(</?' . $allblocks . '[^>]*>)!', "$1", $pee);
	$pee = preg_replace('!(</?' . $allblocks . '[^>]*>)\s*</p>!', "$1", $pee);
	
	if ($br) {
		$pee = preg_replace('/<(script|style).*?<\/\\1>/se', 'str_replace("\n", "<MSOPreserveNewline />", "\\0")', $pee);
		$pee = preg_replace('|(?<!<br />)\s*\n|', "<br />\n", $pee); // optionally make line breaks
		$pee = str_replace('<MSOPreserveNewline />', "\n", $pee);
	}
	$pee = preg_replace('!(</?' . $allblocks . '[^>]*>)\s*<br />!', "$1", $pee);
	$pee = preg_replace('!<br />(\s*</?(?:p|li|div|dl|dd|dt|th|pre|td|ul|ol)[^>]*>)!', '$1', $pee);
	
	if (strpos($pee, '<pre') !== false)
		$pee = preg_replace_callback('!(<pre.*?>)(.*?)</pre>!is', 'mso_clean_pre', $pee );
		
	$pee = preg_replace( "|\n</p>$|", '</p>', $pee );
	
	
	//$pee = str_replace( "<p><ul", "<ul", $pee );
	//$pee = str_replace( "<p>\n<ul", "<ul", $pee );
	//$pee = str_replace( "</li><li>", "</li>\n<li>", $pee );
	//$pee = str_replace( "</p><p>", "</p>\n<p>", $pee );
	// $pee = str_replace( "<br /><br />", "<p>", $pee );


	return $pee;
}


# служебная - из WordPress
function mso_clean_pre_wp($matches) 
{
	if ( is_array($matches) )
		$text = $matches[1] . $matches[2] . "</pre>";
	else
		$text = $matches;

	$text = str_replace('<p>', "<br />", $text);
	$text = str_replace('</p>', '', $text);
	$text = str_replace("\n", "<br />", $text);
	//$text = str_replace('<br />', "\n", $text);

	//$text = str_replace("\n\n", "\n", $text);

	//$text = str_replace('<br />', "\n", $text);
	//$text = str_replace('<p>', "\n", $text);
	//$text = str_replace('</p>', '', $text);

	return $text;
}

*/

# функция взятая из b2
function mso_balance_tags( $text, $force = true ) 
{
	if ( !$force ) return $text;
	
	//if (function_exists('iconv')) $text = iconv('UTF-8', 'WINDOWS-1251', $text ); // в WINDOWS-1251
	$text = balanceTags($text);
	//if (function_exists('iconv')) $text = iconv('WINDOWS-1251', 'UTF-8', $text ); // обратно у юникод	
	
	return $text;
}

/*
 force_balance_tags

 Balances Tags of string using a modified stack.

 @param text      Text to be balanced
 @param force     Forces balancing, ignoring the value of the option
 @return          Returns balanced text
 @author          Leonard Lin (leonard@acm.org)
 @version         v1.1
 @date            November 4, 2001
 @license         GPL v2.0
 @notes
 @changelog
 ---  Modified by Scott Reilly (coffee2code) 02 Aug 2004
	1.2  ***TODO*** Make better - change loop condition to $text
	1.1  Fixed handling of append/stack pop order of end text
	     Added Cleaning Hooks
	1.0  First Version
*/

function balanceTags($text)
{
	$tagstack = array(); $stacksize = 0; $tagqueue = ''; $newtext = '';
	$single_tags = array('br', 'hr', 'img', 'input'); //Known single-entity/self-closing tags
	$nestable_tags = array('blockquote', 'div', 'span'); //Tags that can be immediately nested within themselves

	# WP bug fix for comments - in case you REALLY meant to type '< !--'
	$text = str_replace('< !--', '<    !--', $text);
	# WP bug fix for LOVE <3 (and other situations with '<' before a number)
	$text = preg_replace('#<([0-9]{1})#', '&lt;$1', $text);

	while (preg_match("/<(\/?\w*)\s*([^>]*)>/",$text,$regex)) {
		$newtext .= $tagqueue;

		$i = strpos($text,$regex[0]);
		$l = strlen($regex[0]);

		// clear the shifter
		$tagqueue = '';
		// Pop or Push
		if ($regex[1][0] == "/") { // End Tag
			$tag = strtolower(substr($regex[1],1));
			// if too many closing tags
			if($stacksize <= 0) {
				$tag = '';
				//or close to be safe $tag = '/' . $tag;
			}
			// if stacktop value = tag close value then pop
			else if ($tagstack[$stacksize - 1] == $tag) { // found closing tag
				$tag = '</' . $tag . '>'; // Close Tag
				// Pop
				array_pop ($tagstack);
				$stacksize--;
			} else { // closing tag not at top, search for it
				for ($j=$stacksize-1;$j>=0;$j--) {
					if ($tagstack[$j] == $tag) {
					// add tag to tagqueue
						for ($k=$stacksize-1;$k>=$j;$k--){
							$tagqueue .= '</' . array_pop ($tagstack) . '>';
							$stacksize--;
						}
						break;
					}
				}
				$tag = '';
			}
		} else { // Begin Tag
			$tag = strtolower($regex[1]);

			// Tag Cleaning

			// If self-closing or '', don't do anything.
			if((substr($regex[2],-1) == '/') || ($tag == '')) {
			}
			// ElseIf it's a known single-entity tag but it doesn't close itself, do so
			elseif ( in_array($tag, $single_tags) ) {
				$regex[2] .= '/';
			} else {	// Push the tag onto the stack
				// If the top of the stack is the same as the tag we want to push, close previous tag
				if (($stacksize > 0) && !in_array($tag, $nestable_tags) && ($tagstack[$stacksize - 1] == $tag)) {
					$tagqueue = '</' . array_pop ($tagstack) . '>';
					$stacksize--;
				}
				$stacksize = array_push ($tagstack, $tag);
			}

			// Attributes
			$attributes = $regex[2];
			if($attributes) {
				$attributes = ' '.$attributes;
			}
			$tag = '<'.$tag.$attributes.'>';
			//If already queuing a close tag, then put this tag on, too
			if ($tagqueue) {
				$tagqueue .= $tag;
				$tag = '';
			}
		}
		$newtext .= substr($text,0,$i) . $tag;
		$text = substr($text,$i+$l);
	}

	// Clear Tag Queue
	$newtext .= $tagqueue;

	// Add Remaining text
	$newtext .= $text;

	// Empty Stack
	while($x = array_pop($tagstack)) {
		$newtext .= '</' . $x . '>'; // Add remaining tags to close
	}

	// WP fix for the bug with HTML comments
	$newtext = str_replace("< !--","<!--",$newtext);
	$newtext = str_replace("<    !--","< !--",$newtext);

	return $newtext;
}


# редирект на страницу сайта. путь указывать относительно сайта
# если $absolute = true - переход по указаному пути
function mso_redirect($url, $absolute = false)
{
	global $MSO;
	
	$url = strip_tags($url);

	$url = str_replace( array('%0d', '%0a'), '', $url );
	
	if ($absolute)
	{
		header("Refresh: 0; url={$url}");
		header("Location: {$url}");
	}
	else
	{
		$url = $MSO->config['site_url'] . $url;
		header("Refresh: 0; url={$url}");
		header("Location: {$url}");
	}
	exit();
}

# получение текущего url относительно сайта
function mso_current_url()
{
	global $MSO;

	$url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	$url = str_replace($MSO->config['site_url'], "", $url);
	
	return $url;
}

# проверка залогиннености юзера
function is_login()
{
	global $MSO;
	
	return ($MSO->data['session']['userlogged'] == 1) ? true : false;
}

# формируем скрытый input для формы с текущей сессией
function mso_form_session($name_form = 'flogin_session_id')
{
	global $MSO;

	return '<input type="hidden" value="' . $MSO->data['session']['session_id'] . '" name="' . $name_form . '" />';
}


# вывод логин-форма
function mso_login_form($conf = array(), $redirect = '', $echo = true)
{
	global $MSO;
	
	if ($redirect == '') $redirect = urlencode(mso_current_url());
	
	$login = (isset($conf['login'])) ? $conf['login'] : '';
	$password = (isset($conf['password'])) ? $conf['password'] : '';
	$submit = (isset($conf['submit'])) ? $conf['submit'] : '';
	$submit_value = (isset($conf['submit_value'])) ? $conf['submit_value'] : 'Войти';
	$form_end = (isset($conf['form_end'])) ? $conf['form_end'] : '';

	$action = $MSO->config['site_url'] . 'login';
	$session_id = $MSO->data['session']['session_id'];

	$out = <<<EOF
	<form method="post" action="{$action}" name="flogin" id="flogin">
		<input type="hidden" value="{$redirect}" name="flogin_redirect" id="flogin_redirect" />
		<input type="hidden" value="{$session_id}" name="flogin_session_id" id="flogin_session_id" />
		{$login}<input type="text" value="" name="flogin_user" id="flogin_user" />
		{$password}<input type="password" value="" name="flogin_password" id="flogin_password" />
		{$submit}<input type="submit" name="flogin_submit" id="flogin_submit" value="{$submit_value}">
		{$form_end}
	</form>
EOF;
	if ($echo) echo $out;
		else return $out;
}


# посыл в хидере no-кэш
# кажется не работает - как проверить хз...
function mso_nocache_headers() {
	@header('Expires: Wed, 11 Jan 1984 05:00:00 GMT');
	@header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
	@header('Cache-Control: no-cache, must-revalidate, max-age=0');
	@header('Pragma: no-cache');
}


# функция проверяет существование POST, а также обязательных полей
# которые передаются в массиве ( array('f_session_id','f_desc','f_edit_submit') )
# если полей нет, то возвращается false
# если поля есть, то возвращается $_POST
function mso_check_post($args = array())
{
	if ($_POST)
	{
		$check = true;
		foreach ($args as $key=>$field)
		{
			if (!isset($_POST[$field])) 
			{  // нет значения - выходим
				$check = false;
				break;
			}
		}
		if (!$check) return false;
			else return $_POST;
	}
	else return false;
}


# получение из массива номера $num_key ключа
# array('2'=>'Изменить');
# возвратит 2
function mso_array_get_key($ar, $num_key = 0, $no = false)
{
	$ar = array_keys($ar);
	
	if (isset($ar[$num_key])) return $ar[$num_key];
		else return $no;
}

# получение из массива ключ значения
# array('2'=>'Изменить');
# mso_array_get_key_value($ar, 'Изменить' ) возвратит 2
function mso_array_get_key_value($ar, $value = false, $no = false)
{
	if (!$value) return $no;
	if (!in_array($value, $ar)) return $no;
	
	foreach ($ar as $key=>$val)
	{
		if ($val == $value)	return $key;
	}
}

/*
# вспомогательная функция для xmlrpc
# возвращает массив парметров, где первые три 
# $blog_id = (int) $parameters[0];
# $user_login = $parameters[1];
# $password = $parameters[2];
function mso_xmlrpc_this($data = array())
{
	global $MSO;
	
	$blog_id = 1;
	
	if (is_login())
	{
		$user_login = $MSO->data['session']['users_login'];
		$password = $MSO->data['session']['users_password'];
	}
	else
	{
		$user_login = '';
		$password = '';
	}
	
	return array($blog_id, $user_login, $password, array($data, 'struct') );
}
*/

# проверка комбинации логина-пароля
# если указан act - то стразу смотрим разрешение на действие - пока act не работает!
function mso_check_user_password($login = false, $password = false, $act = false)
{
	if (!$login or !$password) return false;
	
	$CI = & get_instance();
	
	//_log($CI, false);
	// $CI->load->library('database');
	// _log('123');
	
	$CI->db->select('users_id, users_groups_id');
	$CI->db->where(array('users_login'=>$login, 'users_password'=>$password) );  // where 'users_password' = $password
	$CI->db->limit(1); # одно значение
	
	
	$query = $CI->db->get('users');
	if ($query->num_rows() > 0) # есть такой юзер
	{
		if ($act) 
		{
			// $act = mso_slug($act); // защищаем имя действия
		
			// !!!!!!!!!!!!!!!!!!
			// пока заглушка
			// нужно проверить по users_groups_id разрешение для этого юзера для этого действия
			
			return true;
		}
		else return true;
	}
	else return false;
}

# получаем данные юзера по его логину/паролю
function mso_get_user_data($login = false, $password = false)
{
	if (!$login or !$password) return false;
	
	$CI = & get_instance();
	$CI->db->select('*');
	$CI->db->limit(1); # одно значение
	$CI->db->where('users_login', $login); // where 'users_login' = $login
	$CI->db->where('users_password', $password);  // where 'users_password' = $password
	
	$query = $CI->db->get('users');
	
	if ($query->num_rows() > 0) # есть такой юзер
	{
		$r = $query->result_array();
		return $r[0];
		 
	}
	else return false;
}

/*
# функция отправки xmlrpc к себе же
# при debug выводятся сообщения об ошибке
function mso_xmlrpc_send99($method = 'Hello', $request = array('Test'), $debug = false)
{	
	$CI = & get_instance();
	$CI->load->helper('url');
	$server_url = site_url('xmlrpc_server');
	$CI->load->library('xmlrpc');
	$CI->xmlrpc->server($server_url, 80);
	$CI->xmlrpc->timeout(30);
	$CI->xmlrpc->method($method);
	
	//$CI->xmlrpc->set_debug(TRUE);
	
	$CI->xmlrpc->request($request);
		
	if ( ! $CI->xmlrpc->send_request())
	{
		if ($debug) return $CI->xmlrpc->display_error();
			else return false; //$CI->xmlrpc->result;
	}
	else
	{
		//if ($debug) 
		return $CI->xmlrpc->display_response();
	}
}
*/

# функция преобразует неанглийские буквы в англйские
# также удаляются все служебные символы
function mso_slug($slug)
{
	// таблица замены
	$repl = array(
	"А"=>"a", "Б"=>"b",  "В"=>"v",  "Г"=>"g",   "Д"=>"d",
	"Е"=>"e", "Ё"=>"yo", "Ж"=>"zh",
	"З"=>"z", "И"=>"i",  "Й"=>"j",  "К"=>"k",   "Л"=>"l",
	"М"=>"m", "Н"=>"n",  "О"=>"o",  "П"=>"p",   "Р"=>"r",
	"С"=>"s", "Т"=>"t",  "У"=>"u",  "Ф"=>"f",   "Х"=>"x",
	"Ц"=>"c", "Ч"=>"ch", "Ш"=>"sh", "Щ"=>"shh", "Ъ"=>"",
	"Ы"=>"C", "Ь"=>"",   "Э"=>"e",  "Ю"=>"yu", "Я"=>"ya",
	
	"а"=>"a", "б"=>"b",  "в"=>"v",  "г"=>"g",   "д"=>"d",
	"е"=>"e", "ё"=>"yo", "ж"=>"zh",
	"з"=>"z", "и"=>"i",  "й"=>"j",  "к"=>"k",   "л"=>"l",
	"м"=>"m", "н"=>"n",  "о"=>"o",  "п"=>"p",   "р"=>"r",
	"с"=>"s", "т"=>"t",  "у"=>"u",  "ф"=>"f",   "х"=>"x",
	"ц"=>"c", "ч"=>"ch", "ш"=>"sh", "щ"=>"shh", "ъ"=>"",
	"ы"=>"y", "ь"=>"",   "э"=>"e",  "ю"=>"yu",  "я"=>"ya",
	
	"Є"=>"ye", "І"=>"i", "Ѓ"=>"g", "і"=>"i", "є"=>"ye", "ѓ"=>"g",
	
	"«"=>"", "»"=>"", "—"=>"-", "`"=>"", " "=>"-",
	"["=>"", "]"=>"", "{"=>"", "}"=>"", "<"=>"", ">"=>"",
	"?"=>"", "."=>"", ","=>"", "*"=>"", "%"=>"", "$"=>"",
	"@"=>"", "!"=>"", ";"=>"", ":"=>"", "^"=>"", "\""=>"",
	"&"=>"", "="=>"", "№"=>"", "\\"=>"", "/"=>"", "#"=>"",
	"("=>"", ")"=>"", "~"=>"", "|"=>"", "+"=>""
	);

	return strtolower(strtr(trim($slug), $repl));
}

# содание разрешения для действия
function mso_create_allow($act = '', $desc = '')
{
	global $MSO;
	
	$act = mso_slug($act); // защищаем имя действия
	
	// считываем опцию 
	$d = mso_get_option('groups_allow', 'general');
	
	if (!$d) // нет таких опций вообще
	{
		$d = array($act => $desc); // создаем массив
		mso_add_option ('groups_allow', $d, 'general'); // добавляем опции
		return;
	}
	else // есть опции 
	{
		if ( isset($d[$act]) and ($d[$act] == $desc)) return; // ничего не изменилось
		else
		{	// что-то новенькое
			$d[$act] = $desc; // добавляем
			mso_add_option ('groups_allow', $d, 'general');
			return;
		}
	}
}

# удалить действие/функцию
function mso_remove_allow($act = '')
{
	global $MSO;
	
	$act = mso_slug($act); // защищаем имя действия

	$d = mso_get_option('groups_allow', 'general');

	if ( isset($d[$act]) )
	{
		unset($d[$act]);
		mso_delete_option('groups_allow', 'general');
		mso_add_option ('groups_allow', $d, 'general');
	}
}

# проверка доступа для юзера для указанного действия/функции
# если $cache = true то данные можно брать из кэша, иначе всегда из SQL
function mso_check_allow($act = '', $user_id = false, $cache = true)
{
	global $MSO;
	
	$act = mso_slug($act); // защищаем имя действия
	
	if (!$act) return false;
	
	// незалогиненным сразу выход
	// if (! $MSO->data['session']['userlogged']) return false;
	
	if ( $user_id == false ) // если юзер не указан
	{
		if (! $MSO->data['session']['users_id']) // и в сесии
			return false; 
		else 
			$user_id = $MSO->data['session']['users_id']; // берем его номер из сессии
		
		if ( $MSO->data['session']['users_groups_id'] == '1' ) return true; // админам всё можно
	}
	else 
		$user_id = (int) $user_id; // юзер указан явно - нужно проверять
	// если есть кэш этого участника, где уже хранятся его разрешения
	// то берем кэш, если нет, то выполняем запрос полностью
	
	if ($cache)	$k = mso_get_cache('user_rules_' . $user_id );
		else $k = false;
	
	if (!$k) // нет кэша
	{
		// по номеру участника получаем номер группы
		// по номеру группы получаем все разрешения этой группы
		
		$CI = & get_instance();
		$CI->db->select('users_groups_id, groups_rules, groups_id');// groups_name
		$CI->db->limit(1);
		$CI->db->where('users_id', $user_id);
		$CI->db->from('users');
		$CI->db->join('groups', 'groups.groups_id = users.users_groups_id');
		
		$query = $CI->db->get();

		foreach ($query->result_array() as $rw)	
		{
			$rules = $rw['groups_rules'];
			$groups_id = $rw['groups_id'];
		}
		
		if ($groups_id == 1) return true; // админам можно всё
		
		$rules = unserialize($rules); // востанавливаем массив с разрешениями этой группы
		mso_add_cache('user_rules_' . $user_id, $rules); // сразу в кэш добавим
	}
	else // есть кэш
	{
		$rules = $k;
	}
	
	
	/* 
	$rules = Array ( 
		[edit_users_group] => 1 
		[edit_users_admin_note] => 1 
		[edit_other_users] => 1 
		[edit_self_users] => 1 )
	*/
	
	if (isset( $rules[$act] )) // если действие есть в массиве
	{
		if ($rules[$act] == 1) return true; // и разрешено
			else return false; // запрещено
	}
	else return false; // действия вообще нет в разрешениях
}



# получаем название указанного сегменту текущей страницы 
# http://localhost/codeigniter/admin/users/edit/1
# mso_segment(3) -> edit
# номер считается от home-сайта
function mso_segment($segment = 2)
{
	global $MSO;
	if ( count($MSO->data['uri_segment']) > ($segment - 1) )
		$seg = $MSO->data['uri_segment'][$segment];
	else $seg = '';
	
	return urldecode($seg);
}

# функция преобразования MySql-даты (ГГГГ-ММ-ДД ЧЧ:ММ:СС) в указанный формат date
# идея - http://dimoning.ru/archives/31
function mso_date_convert($format = 'Y-m-d H:i:s', $data, $timezone = true)
{
	$res = '';
	$part = explode(' ' , $data);
	$ymd = explode ('-', $part[0]);
	$hms = explode (':', $part[1]);
	
	$y = $ymd[0];
	$m = $ymd[1];
	$d = $ymd[2];
	$h = $hms[0]; 
	$n = $hms[1]; 
	$s = $hms[2];
	
	$time = mktime($h, $n, $s, $m, $d, $y);
	
	if ($timezone) $time = $time + getinfo('time_zone') * 60 * 60;
	
	return date($format, $time);
}

# переобразование даты в формат MySql
function mso_date_convert_to_mysql($year = 1970, $mon = 1, $day = 1, $hour = 0, $min = 0, $sec = 0)
{

	if ($day>31) 
	{
		$day = 1;
		$mon ++;
		$year ++;
	}

	if ($mon>12) 
	{
		$mon = 1;
		$year ++;
	}

	if ( $mon < 10 ) $mon = '0' . $mon; 
	if ( $day < 10 ) $day = '0' . $day; 
	if ( $hour < 10 ) $hour = '0' . $hour; 
	if ( $min < 10 ) $min = '0' . $min; 
	if ( $sec < 10 ) $sec = '0' . $sec; 
	
	$res = $year . '-' . $mon . '-' . $day . ' ' . $hour . ':' . $min . ':' . $sec;
	return $res;
}

# получить пермалинк страницы по её id
# через запрос БД
function mso_get_permalink_page($id = 0)
{
	global $MSO;
	$id = (int) $id;
	if (!$id) return '';
	
	$CI = & get_instance();
	$CI->db->select('page_slug, page_id');
	$CI->db->where(array('page_id'=>$id));

	$query = $CI->db->get('page'); 
		
	if ($query->num_rows()>0) 
	{
		foreach ($query->result_array() as $row)
			$slug = $row['page_slug'];

		return  $MSO->config['site_url'] . 'page/' . mso_slug($slug);
	}
	else return '';
}

# получить пермалинк рубрики по указанному слагу
function mso_get_permalink_cat_slug($slug = '')
{
	global $MSO;
	if (!$slug) return '';
	return  $MSO->config['site_url'] . 'category/' . mso_slug($slug);
}


#  разделить строку из чисел, разделенных запятыми в массив
# если $integer = true, то дополнительно преобразуется в числа 
# если $probel = true, то разделителем может быть пробелы
function mso_explode($s = '', $int = true, $probel = true ) 
{
	//$s = trim( str_replace(',', ',', $s) );
	$s = trim( str_replace(';', ',', $s) );
	if ($probel)
	{
		$s = trim( str_replace('     ', ',', $s) );
		$s = trim( str_replace('    ', ',', $s) );
		$s = trim( str_replace('   ', ',', $s) );
		$s = trim( str_replace('  ', ',', $s) );
		$s = trim( str_replace(' ', ',', $s) );
	}
	
	$s = trim( str_replace(',,', ',', $s) );
	$s = array_unique( explode(',', trim($s) ) );
	
	$out = array();
	foreach ( $s as $key => $val ) 
	{
		if ($int) 
		{
			if (  (int) $val > 0 ) $out[] = $val; // id в массив
		}
		else 
		{
			if (trim($val)) $out[] = trim($val);
		}
	}
	
	$out = array_unique($out);
		
	return $out;
}


#  обрезаем строку на кол-во слов
function mso_str_word($text, $counttext = 10, $sep = ' ') 
{
	$words = split($sep, $text);
	if ( count($words) > $counttext ) 
		$text = join($sep, array_slice($words, 0, $counttext));
	return $text;
}

# получить текущую страницу пагинации
function mso_current_paged()
{
	global $MSO;
	
	$uri = $MSO->data['uri_segment'];
	
	if ($n = mso_array_get_key_value($uri, 'next')) 
	{
		if (isset($uri[$n+1])) $n = (int) $uri[$n+1];
			else $n = 1;
		if ($n > 0) $current_paged = $n;
		else $current_paged = 1;
	}
	else $current_paged = 1;
	
	return $current_paged;
}


# регистрируем сайдбар
function mso_register_sidebar($sidebar = '1', $title = 'Cайдбар', $options = array() )
{
	global $MSO;
	
	$sidebar = mso_slug($sidebar);
	
	$MSO->sidebars[$sidebar] = array('title' => $title, 'options' => $options);
}

# регистрируем виджет
function mso_register_widget($widget = false, $title = 'Виджет')
{
	global $MSO;
	if ($widget) $MSO->widgets[$widget] = $title;
}


# вывод сайбрара
function mso_show_sidebar($sidebar = '1', $block_start = '', $block_end = '')
{
	global $MSO;
	
	$sidebar = mso_slug($sidebar);
	
	$widgets = mso_get_option('sidebars-' . mso_slug($sidebar), 'sidebars', array());
	
	$out = '';
	
	if ($widgets) // есть виджеты
	{
		foreach ($widgets as $widget)
		{
			// имя виджета может содержать номер через пробел
			$arr_w = explode(' ', $widget); // в массив
			if ( sizeof($arr_w) > 1 ) // два или больше элементов
			{
				$widget = trim( $arr_w[0] ); // первый - функция
				$num = (int) trim( $arr_w[1] ); // второй - номер виджета
			}
			else 
			{
				$num = 0; // номер виджета не указан, значит 0
			}
			
			if ( function_exists($widget) ) 
			{
				$out .= $block_start;
				$out .= $widget($num);
				$out .= $block_end;
			}
		}
		
		echo $out;
	}
}

# вспомогательная функция, которая принимает глобальный _POST
# и поле $option. Использовать в _update виджетов
function mso_widget_get_post($option = '')
{
	if ( isset($_POST[$option]) ) 
	    return stripslashes($_POST[$option]);
	else return '';
}


# функция отправки письма по email
function mso_mail($email = '', $subject = '', $message = '')
{
	$CI = & get_instance();
	$CI->load->library('email');
	
	$admin_email = mso_get_option('admin_email_server', 'general', 'admin@site.com');

	$config['wordwrap'] = TRUE;
	$config['wrapchars'] = 90;
	$CI->email->initialize($config);

	$CI->email->to($email);
	$CI->email->from($admin_email);
	$CI->email->subject($subject);
	$CI->email->message($message);
	
	// pr($CI->email);

	return $CI->email->send();
}

# для юникода отдельный wordwrap
# часть кода отсюда: http://us2.php.net/manual/ru/function.wordwrap.php#78846
# переделал и исправил ошибки я
function mso_wordwrap($str, $wid, $tag)
{
		$pos = 0;
		$tok = array();
		$l = mb_strlen($str, 'UTF8');
		
		if($l == 0) return '';
		
		$flag = false;
		
		$tok[0] = mb_substr($str, 0, 1, 'UTF8');
		
		for($i = 1 ; $i < $l ; ++$i)
		{
				$c = mb_substr($str, $i, 1, 'UTF8');
				
				if(!preg_match('/[a-z\'\"]/i',$c))
				{
						++$pos;
						$flag = true;
				}
				elseif($flag)
				{
						++$pos;
						$flag = false;
				}
				
				if (isset($tok[$pos])) $tok[$pos] .= $c;
					else $tok[$pos] = $c;
		}		

		$linewidth = 0;
		$pos = 0;
		$ret = array();
		$l = count($tok);
		for($i = 0 ; $i < $l ; ++$i)
		{
				if($linewidth + ($w = mb_strwidth($tok[$i], 'UTF8') ) > $wid)
				{
						++$pos;
						$linewidth = 0;
				}
				if (isset($ret[$pos])) $ret[$pos] .= $tok[$i];
					else $ret[$pos] = $tok[$i];
				
				$linewidth += $w;
		}
		return implode($tag, $ret);
}



?>