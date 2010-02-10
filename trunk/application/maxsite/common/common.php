<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Основные функции MaxSite CMS
 * (c) http://maxsite.org/
 */

# подключаем библиотеку mbstring
# какие функции отсутствуют определяется в этом файле
require('mbstring.php');

define("NR", "\n");


#  функция для отладки
function pr($var, $html = false, $echo = true)
{
	if (!$echo) ob_start();
		else echo '<pre>';
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
	if (!$echo)
	{
		$out = ob_get_contents();
		ob_end_clean();
		return $out;
	}
	else echo '</pre>';
}


# функция, аналогичная pr, только завершающаяся die() 
# используется для отладки с помощью прерывания
function _pr($var, $html = false, $echo = true)
{
	pr($var, $html, $echo);
	die();
}


# функция, формирующая sql-запрос
# используется для отладки перед $CI->db->get()
function _sql()
{
	$CI = & get_instance();
	$sql = $CI->db->_compile_select();
	return $sql;
}


#  правильность email
function mso_valid_email($em = '')
{
	if ( eregi("^[a-z0-9\._+-]+@+[a-z0-9\._-]+\.+[a-z]{2,4}$", $em) )
		return true;
	else
		return false;
}


# проверем рефер на xss-атаку
# работает только если есть POST
function mso_checkreferer()
{
	if ($_POST)
	{
		if (!isset($_SERVER['HTTP_REFERER'])) die('<b><font color="red">Achtung! XSS attack! No REFERER!</font></b>');

		$p = parse_url($_SERVER['HTTP_REFERER']);

		if (isset($p['host'])) $p = $p['host'];
			else $p = '';

		if ( $p != $_SERVER['HTTP_HOST'] )
			die('<b><font color="red">Achtung! XSS attack!</font></b>');
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
function mso_strip($s = '', $logical = false, $arr_strip = array('\\', '|', '/', '?', '%', '*', '`'))
{
	$s1 = $s;
	$s1 = stripslashes($s1);
	$s1 = strip_tags($s1);
	$s1 = htmlspecialchars($s1, ENT_QUOTES);

	// $arr_strip = array('\\', '|', '/', '?', '%', '*', '`');
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
	global $MSO, $mso_install;
	$CI = & get_instance();

	# считываем файл конфигурации
	$fn = $MSO->config['config_file'];
	if ( file_exists($fn) ) require_once ($fn);

	// если кэш старый, то очищаем его
	$path = $CI->config->item('cache_path');
	$mso_cache_last = ($path == '') ? BASEPATH . 'cache/' . '_mso_cache_last.txt' : $path . '_mso_cache_last.txt';
	if (file_exists($mso_cache_last))
	{
		$time = (int) trim(implode('', file($mso_cache_last)));
		$time = $time + $MSO->config['cache_time'] + 60; // запас + 60 секунд
		if (time() > $time) mso_flush_cache(); // время истекло - сбрасываем кэш
	}
	else // файла нет > _mso_cache_last.txt < создадим - наверное совсем старый кэш
	{
		mso_flush_cache();
	}

	# стоит ли флаг, что уже произведена инсталяция?

	if (!isset($mso_install) or $mso_install == false)
	{
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

	# проверяем залогинненость юзера
	if (!isset($CI->session->userdata['userlogged']) or !$CI->session->userdata['userlogged'] )
	{
		// не залогинен
		$CI->session->userdata['userlogged'] = 0;
	}
	else
	{
		// отмечено, что залогинен
		// нужно проверить верность данных юзера
		$CI->db->from('users'); # таблица users
		$CI->db->select('users_id, users_groups_id');
		$CI->db->limit(1); # одно значение

		$CI->db->where( array('users_login'=>$CI->session->userdata['users_login'],
							  'users_password'=>$CI->session->userdata['users_password']) );

		$query = $CI->db->get();

		if ($query->num_rows() == 0) # нет такого - возможно взлом
		{
			$CI->session->sess_destroy(); // убиваем сессию
			$CI->session->userdata['userlogged'] = 0; // отмечаем, что не залогинен
		}
		else
		{
			// есть что-то
			$row = $query->row();
			// сразу выставим группу
			$MSO->data['session']['users_groups_id'] = $row->users_groups_id;

			# сразу обновляем время последней активности сессии
			$CI->session->set_userdata('last_activity', time());
		}
	}

	// аналогично проверяем и комюзера, только данные из куки
	// но при этом сразу сохраняем все данные комюзера, чтобы потом не обращаться к БД

	$comuser = mso_get_cookie('maxsite_comuser', false);
	if ($comuser)
	{
		$comuser = unserialize($comuser);
		/*
		[comusers_id] => 1
		[comusers_password] => 037035235237852
		[comusers_email] => max-3000@list.ru
		[comusers_nik] => Максим
		[comusers_url] => http://maxsite.org/
		[comusers_avatar_url] => http://maxsite.org/avatar.jpg
		*/
		// нужно сверить с тем, что есть

		$CI->db->select('comusers_id, comusers_password, comusers_email');
		$CI->db->where('comusers_id', $comuser['comusers_id']);
		$CI->db->where('comusers_password', $comuser['comusers_password']);
		$CI->db->where('comusers_email', $comuser['comusers_email']);
		$query = $CI->db->get('comusers');
		if ($query->num_rows()) // есть такой комюзер
		{
			$CI->session->userdata['comuser'] = $comuser;
		}
		else // неверные данные
		{
			$CI->session->userdata['comuser'] = 0;
		}
	}
	else $CI->session->userdata['comuser'] = 0;


	# дефолтные хуки
	mso_hook_add('content_auto_tag', 'mso_auto_tag'); // авторасстановка тэгов
	mso_hook_add('content_balance_tags', 'mso_balance_tags'); // автозакрытие тэгов - их баланс
}


# проверка залогиннености юзера
function is_login()
{
	global $MSO;
	return ($MSO->data['session']['userlogged'] == 1) ? true : false;
}


# проверка залогиннености комюзера
# если есть, то возвращает массив данных
function is_login_comuser()
{
	global $MSO;

	if (isset($MSO->data['session']['comuser']) and ($comuser = $MSO->data['session']['comuser']) ) return $comuser;
		else return false;
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
	}

	foreach ($d as $load) mso_plugin_load($load);
}


# проверка типа страницы, который определился в контролере
function is_type($type)
{
	global $MSO;
	return ( $MSO->data['type'] == $type ) ? true : false;
}


# возвращает true или false при проверке $MSO->data['uri_segment'], то есть по сегментам URL
# где например [1] => page  [2] => about
# что означает type = page  slug=about
# http://localhost/page/about
# можно указать только тип или только slug
# тогда неуказанный параметр не учитывается (всегда true)
function is_type_slug($type = '', $slug = '')
{
	global $MSO;

	$rt = $rs = '';

	// тип
	if ($type and isset($MSO->data['uri_segment'][1]) ) $rt = $MSO->data['uri_segment'][1];

	// slug
	if ( $slug and isset($MSO->data['uri_segment'][2]) ) $rs = $MSO->data['uri_segment'][2];

	return ($rt == $type and $rs == $slug);
}


# проверяем рубрику у страницы
# если это page и есть указанная рубрика, то возвращаем true
# если это не page или нет указанной рубрики, то возвращаем false
# если $and_id = true , то ищем и по id
# если $and_name = true , то ищем и по category_name
function is_page_cat($slug = '', $and_id = true, $and_name = true)
{
	global $MSO, $page;

	if (!$slug) return false; // slaug не указан
	if (!is_type('page')) return false; // тип не page
	if (!isset($page['page_categories_detail'])) return false; // нет информации о рубриках

	$result = false;

	// информация о slug, id и name в массиве $page['page_categories_detail']
	foreach($page['page_categories_detail'] as $id => $val)
	{
		if ( $val['category_slug'] == $slug ) $result = true; // slug совпал
		if ( !$result and $and_id and $id == $slug ) $result = true; // можно искать по $id
		if ( !$result and $val['category_name'] == $slug ) $result = true; // category_name совпал

		if ($result) break;
	}
	return $result;
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

		case 'templates_dir' :
				$out = $MSO->config['templates_dir'];
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

		case 'common_url' :
				$out = $MSO->config['common_url'];
				break;

		case 'uploads_url' :
				$out = $MSO->config['uploads_url'];
				break;

		case 'uploads_dir' :
				$out = $MSO->config['uploads_dir'];
				break;

		case 'users_nik' :
				if (isset($MSO->data['session']['users_nik']))
					$out = $MSO->data['session']['users_nik'];
				else $out = '';
				break;

		case 'users_id' :
				if (isset($MSO->data['session']['users_id']))
					$out = $MSO->data['session']['users_id'];
				else $out = '';
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
				$out = (string) mso_get_option('time_zone', 'general');
				break;

		case 'plugins_url' :
				$out = $MSO->config['plugins_url'];;
				break;

		case 'plugins_dir' :
				$out = $MSO->config['plugins_dir'];;
				break;

		case 'ajax' :
				$out = $MSO->config['site_url'] . 'ajax/';
				break;

		case 'admin_plugins_dir' :
				$out = $MSO->config['admin_plugins_dir'];
				break;

		case 'session' :
				$out = $MSO->data['session'];
				break;

		case 'remote_key' :
				$out = $MSO->config['remote_key'];
				break;

		case 'uri_get' :
				$out = $MSO->data['uri_get'];
				break;

	endswitch;

	return $out;
}


# вывод html meta титла дескриптон или keywords страницы
function mso_head_meta($info = 'title', $args = '', $format = '%page_title%', $sep = '', $only_meta = false )
{
	// ошибочный info
	if ( $info!='title' and $info!='description' and $info!='keywords') return '';


	if (mso_hook_present('head_meta')) // если есть хуки, то управление передаем им
	{
		return mso_hook('head_meta', array('info'=>$info, 'args'=>$args, 'format'=>$format, 'sep'=>$sep, 'only_meta'=>$only_meta));
	}

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
			// pr($args);

			$category_name = '';
			$page_title = '';
			$users_nik = '';
			$title = getinfo($info);

			if ( $info!='title') $format = '%title%';

			if ( isset($args[0]['category_name']) ) $category_name = $args[0]['category_name'];
			if ( isset($args[0]['page_title']) ) $page_title = $args[0]['page_title'];
			if ( isset($args[0]['users_nik']) ) $users_nik = $args[0]['users_nik'];

			// если есть мета, то берем её
			if ( isset($args[0]['page_meta'][$info][0]) and $args[0]['page_meta'][$info][0] )
			{
				if ( $only_meta ) $category_name = $title = $sep = '';
				$page_title = $args[0]['page_meta'][$info][0];

				if ( $info!='title') $title = $page_title;
			}
			else
			{
			//	$page_title = $title;
			//	if ($page_title == $title) $page_title = '';
			}

			// pr($page_title);

			$arr_key = array( '%title%', '%page_title%',  '%category_name%', '%users_nik%', '|' );
			$arr_val = array( $title ,  $page_title, $category_name, $users_nik, $sep );

			$out = str_replace($arr_key, $arr_val, $format);
			// pr($out);
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
	$no_hook = array('');

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

	if ( !in_array($hook, $arr) ) // если хука нет
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
# если функция не указана, то удаляются все функции из хука
function mso_remove_hook($hook = '', $func = '')
{
	global $MSO;

	if ($hook == '') return false;

	$arr = array_keys($MSO->hooks);
	if ( !in_array($hook, $arr) ) return false; // хука нет

	if ($func == '') // удалить весь хук
	{
		unset($MSO->hooks[$hook]);
	}
	else
	{
		if ( !in_array($hook, $arr) ) return false; // нет такой функции
		unset($MSO->hooks[$hook][$func]);
	}
	return true;
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
	$CI->db->cache_delete_all();

	$query = $CI->db->get('options');

	$cache_options = array();

	// $query = $ci->db->query('SELECT * FROM ci_options ORDER BY options_type');

	foreach ($query->result() as $row)
		$cache_options[$row->options_type][$row->options_key] = $row->options_value;

	mso_add_cache('options', $cache_options);

	return $cache_options;
}


# добавление в таблицу опций options
function mso_add_option($key, $value, $type = 'general')
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
function mso_delete_option($key, $type = 'general')
{
	$CI = & get_instance();

	$CI->db->limit(1);
	$CI->db->delete('options', array('options_key'=>$key, 'options_type'=>$type ));

	mso_refresh_options(); # обновляем опции из базы

	return true;
}


# удаление в таблице опций options ключа-маски с типом
# маска считается от начала, например mask*
function mso_delete_option_mask($mask, $type = 'general')
{
	$CI = & get_instance();

	$mask = str_replace('_', '/_', $mask);
	$mask = str_replace('%', '/%', $mask);

	$query = $CI->db->query('DELETE FROM ' . $CI->db->dbprefix('options') . ' WHERE options_type="' . $type . '" AND options_key LIKE "'. $mask . '%" ESCAPE "/"');

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
		$result = @unserialize($result);
	}

	return $result;
}


# добавление float-опции
# float-опция - это файл из серилизованного текста в каталоге uploads
# аналог опций, хранящейся в отдельном файле/каталоге _mso_float
function mso_add_float_option($key, $value, $type = 'general', $serialize = true, $ext = '', $md5_key = true, $dir = '')
{
	$CI = & get_instance();

	if ($dir) $dir .= '/';

	$path = getinfo('uploads_dir') . '_mso_float/' . $dir;

	if ( ! is_dir($path) ) @mkdir($path, 0777); // нет каталога, пробуем создать

	if ( ! is_dir($path) OR ! is_writable($path)) return false; // нет каталога или он не для записи

	if ($md5_key) $path .= mso_md5($key . $type) . $ext;
		else $path .= $key . $type . $ext;

	if ( ! $fp = @fopen($path, 'wb') ) return false; // нет возможности сохранить файл

	if ($serialize)	$output = serialize($value);
		else $output = $value;

	flock($fp, LOCK_EX);
	fwrite($fp, $output);
	flock($fp, LOCK_UN);
	fclose($fp);
	@chmod($path, 0777);

	// возвращаем имя файла
	if ($md5_key) $return = '_mso_float/' . $dir . mso_md5($key . $type) . $ext;
		else $return = '_mso_float/' . $dir . $key . $type . $ext;

	return $return;
}


# получение данных из flat-опций
function mso_get_float_option($key, $type = 'general', $return_value = false, $serialize = true, $ext = '', $md5_key = true, $dir = '')
{
	$CI = & get_instance();

	if (!$key or !$type) return $return_value;

	if ($dir) $dir .= '/';

	if ($md5_key) $path = getinfo('uploads_dir') . '_mso_float/' . $dir . mso_md5($key . $type) . $ext;
		else $path = getinfo('uploads_dir') . '_mso_float/' . $dir . $key . $type . $ext;

	if ( file_exists($path))
	{
		if ( ! $fp = @fopen($path, 'rb')) return $return_value;

		flock($fp, LOCK_SH);

		$out = $return_value;
		if (filesize($path) > 0)
		{
			if ($serialize) $out = @unserialize(fread($fp, filesize($path)));
				else $out = fread($fp, filesize($path));
		}

		flock($fp, LOCK_UN);
		fclose($fp);

		return $out;
	}
	else return $return_value;
}


# удаление flat-опции если есть
function mso_delete_float_option($key, $type = 'general', $dir = '')
{
	$CI = & get_instance();

	if (!$key or !$type) return false;

	if ($dir) $dir .= '/';

	$path = getinfo('uploads_dir') . '_mso_float/' . $dir . mso_md5($key . $type);

	if ( file_exists($path))
	{
		@unlink($path);
		return true;
	}
	else return false;
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

	if ( ! is_dir($cache_path) or ! is_writable($cache_path)) return;

	if (!$custom_fn)
		$cache_path .= mso_md5($key . $CI->config->item('base_url'));
	else
		$cache_path .= $key;

	if ( ! $fp = @fopen($cache_path, 'wb')) return;

	if (!$time) $time = $MSO->config['cache_time'];

	$expire = time() + $time;
	$output = serialize($output);

	flock($fp, LOCK_EX);
	fwrite($fp, $expire.'TS--->' . $output);
	flock($fp, LOCK_UN);
	fclose($fp);
	@chmod($cache_path, 0777);
}


# удаление файла в кэше файлов, начинающихся с указаной строки
function mso_flush_cache_mask($mask = '')
{
	if (!$mask) return;

	$CI = & get_instance();
	$path = $CI->config->item('cache_path');

	$cache_path = ($path == '') ? BASEPATH . 'cache/' : $path;

	if ( ! is_dir($cache_path) or ! is_writable($cache_path)) return;

	$CI->load->helper('directory');

	$files = directory_map($cache_path, true); // только в текущем каталоге

	if (!$files) return; // нет файлов вообще

	foreach ($files as $file)
	{
		if (@is_dir($cache_path . $file)) continue; // это каталог

		$pos = strpos($file, $mask);
		if ( $pos !== false and $pos === 0)
		{
			unlink($cache_path . $file);
		}
	}
}


# сбросить кэш - если указать true, то удалится кэш из вложенных каталогов
# если указан $dir, то удаляется только в этом каталоге
function mso_flush_cache($full = false, $dir = false)
{
	$CI = & get_instance();
	$path = $CI->config->item('cache_path');

	$cache_path = ($path == '') ? BASEPATH . 'cache/' : $path;

	if ( ! is_dir($cache_path) OR ! is_writable($cache_path))
		return FALSE;

	// находим в каталоге все файлы и их удалаяем
	if ($full)
	{
		$CI->load->helper('file_helper'); // этот хелпер удаляет все Файлы и во вложенных каталогах
		delete_files($cache_path);
	}
	else
	{
		// удаляем файлы только в текущем каталоге кэша
		// переделанная функция delete_files из file_helper
		$mso_cache_last = $cache_path . '_mso_cache_last.txt';

		if ($dir) $cache_path .= $dir . '/'; // если указан $dir, удаляем только в нем

		if (!$current_dir = @opendir($cache_path)) return false;
		while (FALSE !== ($filename = @readdir($current_dir)))
		{
			if ($filename != "." and $filename != "..")
			{
				if (!is_dir($cache_path . $filename)) unlink($cache_path . $filename);
			}
		}
		@closedir($current_dir);

		// создадим служебный файл _mso_cache_last.txt который используется для сброса кэша по дате создания
		// при инициализации смотрится дата этого файла и если он создан позже, чем время жизни кэша, то кэш сбрасывается mso_flush_cache
		if (!$dir)
		{
			$fp = @fopen($mso_cache_last, 'w');
			flock($fp, LOCK_EX);
			fwrite($fp, time());
			flock($fp, LOCK_UN);
			fclose($fp);
		}

		// если используется родное CodeIgniter sql-кэширование, то нужно очистить и его
		$CI->db->cache_delete_all();
	}
}


# получить кеш по ключу
function mso_get_cache($key, $custom_fn = false)
{
	/*
	Функция взята из _display_cache output.php - переделанная
	*/

	$CI = & get_instance();
	$path = $CI->config->item('cache_path');

	$cache_path = ($path == '') ? BASEPATH . 'cache/' : $path;

	if ( !is_dir($cache_path) OR ! is_writable($cache_path))
		return FALSE;

	if (!$custom_fn)
		$filepath = $cache_path . mso_md5($key . $CI->config->item('base_url'));
	else
		$filepath = $cache_path . $key;

	if ( !@file_exists($filepath))
		return FALSE;

	if ( !$fp = @fopen($filepath, 'rb'))
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
		return FALSE;
	}

	$out = str_replace($match['0'], '', $cache);
	$out = @unserialize($out);

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
		$m = str_replace("<br />", "[mso_br_n]", $m);

		$m = htmlspecialchars($m, ENT_QUOTES);

		// для смайлов избежать конфликта
		$arr1 = array(':', '\'', '(', ')', '|', '-', '[', ']');
		$arr2 = array('&#58;', '&#39;', '&#40;', '&#41;', '&#124;', '&#45;', '&#91;', '&#93;');
		$m = str_replace($arr1, $arr2, $m);

		$text = "\n\n" . $matches[1] . $m . "</pre>\n\n";
	}
	else
		$text = $matches;

	$text = str_replace('<p>', '', $text);
	$text = str_replace('</p>', '', $text);
	//$text = str_replace("<br />", "[mso_br_n]", $text);

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
	$text = str_replace('[', '&#91;', $text);
	$text = str_replace(']', '&#93;', $text);
	$text = str_replace("<br />", "[mso_br_n]", $text);

	return $text;
}


# преобразуем введенный html в тексте между [html] ... [/html] и для [volkman]
# к обычному html
function mso_clean_html($matches)
{
	$arr1 = array('<p>', '</p>', '<br />',      '&amp;', '&lt;', '&gt;', "\n");
	$arr2 = array('',    '',     '[mso_br_n]',   '&',     '<',    '>',    '[mso_br_n]');

	$matches[1] = trim( str_replace($arr1, $arr2, $matches[1]) );

	return $matches[1];
}


# предподготовка html в тексте между [html] ... [/html]
# конвертируем все символы в реальный html
# после этого кодируем его в одну строчку base64
# после всех операций в mso_balance_tags декодируем его в обычный текст mso_clean_html_posle
# кодирование нужно для того, чтобы корректно пропустить весь остальной текст
function mso_clean_html_do($matches)
{
	$arr1 = array('&amp;', '&lt;', '&gt;', '<br />', '&nbsp;');
	$arr2 = array('&',     '<',    '>',    "\n",     ' ');

	$m = trim( str_replace($arr1, $arr2, $matches[1]) );
	$m = '[html_base64]' . base64_encode($m) . '[/html_base64]';
	return $m;
}


# декодирование из mso_balance_tags см. mso_clean_html_do
function mso_clean_html_posle($matches)
{
	return base64_decode($matches[1]);
}


# авторасстановка тэгов
# переделка из WordPress wpautop() + мои правки
function mso_auto_tag($pee, $pre_special_chars = false)
{
	$pee = $pee . "\n";
	$pee = str_replace(array("\r\n", "\r"), "\n", $pee);

	# если html в [html] код [/html]
	// $pee = preg_replace('!(\[html\])(.*?)(\[\/html\])!is', '$1', $pee);
	$pee = str_replace('<p>[html]</p>', '[html]', $pee);
	$pee = str_replace('<p>[/html]</p>', '[/html]', $pee);
	$pee = preg_replace_callback('!\[html\](.*?)\[\/html\]!is', 'mso_clean_html_do', $pee );

	$allblocks = '(?:table|thead|tfoot|caption|colgroup|center|tbody|tr|td|th|div|dl|dd|dt|ul|ol|li|pre|code|select|form|map|area|blockquote|address|math|style|input|hr|embed|h1|h2|h3|h4|h5|h6|br)';
	$pee = preg_replace('!(<' . $allblocks . '[^>]*>)!', "\n$1", $pee);
	$pee = preg_replace('!(</' . $allblocks . '>)!', "$1\n\n", $pee);

	$pee = str_replace("\n", "<br />", $pee);
	$pee = str_replace('<br>', '<br />', $pee);

	$pee = str_replace('<br />', "\n" . '<br />', $pee); // +

	$pee = str_replace('<hr style="width: 100%; height: 2px;">', "<hr>", $pee); // +

	if ( strpos($pee, '[volkman]') !== false and strpos(trim($pee), '[volkman]') == 0 ) // отдавать как есть
	{
		$pee = str_replace('[volkman]', '', $pee);
		$pee = mso_clean_html( array('1'=>$pee) );
		$pee = str_replace('[mso_br_n]', "\n", $pee);
		return $pee;
	}

	$pee = "\n<p>" . $pee;
	$pee = preg_replace('|<br />\s*<br />|', "<p>", $pee); // +
	$pee = str_replace('<p><p>', "<p>", $pee);
	$pee = str_replace("<p>\n<br />", "<p>", $pee);
	$pee = str_replace("<p><p ", "<p ", $pee);
	$pee = str_replace("<br />", "<p>", $pee);

	$pee = preg_replace('|<p>\s*?</p>|', '', $pee);

	$pee = preg_replace('!<p>([^<]+)\s*?(</(?:div|address|form)[^>]*>)!', "<p>$1</p>$2", $pee);
	$pee = preg_replace('!<p>\s*(</?' . $allblocks . '[^>]*>)\s*</p>!', "$1", $pee);

	$pee = preg_replace('!<p>\s*(</?' . $allblocks . '[^>]*>)\s*!', "\n$1", $pee);

	$pee = preg_replace('!(</' . $allblocks . '>)!', "$1\n", $pee);
	$pee = preg_replace('!\s*(</' . $allblocks . '>)!', "$1", $pee);


	$pee = preg_replace("|<p>(<li.+?)</p>|", "$1", $pee);
	$pee = preg_replace("|</li>\s*<li>|", "</li>\n<li>", $pee);
	$pee = preg_replace("|</li>\s*</ul>|", "</li>\n</ul>", $pee);

	$pee = preg_replace('|<p><blockquote([^>]*)></p>|i', "<blockquote$1>", $pee);
	$pee = str_replace('<p></blockquote></p>', '</blockquote>', $pee);

	$pee = preg_replace('|<p><blockquote([^>]*)>|i', "<blockquote$1>", $pee);
	$pee = str_replace('</blockquote></p>', '</blockquote>', $pee);

	$pee = preg_replace( "|\n</p>$|", '</p>', $pee );

	$pee = str_replace('<p>[cut]</p>', '[cut]', $pee);
	$pee = str_replace('<p>[page]</p>', '[page]', $pee);


	if ($pre_special_chars)
	{
		if (strpos($pee, '<pre') !== false) $pee = preg_replace_callback('!(<pre.*?>)(.*?)</pre>!is', 'mso_clean_pre_special_chars', $pee );
		else $pee = str_replace("\n\n", "\n", $pee);
	}
	else
	{
		if (strpos($pee, '<pre') !== false) $pee = preg_replace_callback('!(<pre.*?>)(.*?)</pre>!is', 'mso_clean_pre', $pee );
		else $pee = str_replace("\n\n", "\n", $pee);
	}

	$pee = str_replace('[mso_n]', "\n", $pee);
	$pee = str_replace('[mso_br_n]', "\n", $pee);

	return $pee;
}


# вычищаем списки UL
function mso_balance_tags_ul_callback($matches)
{
	$text = str_replace('<p> </p>', '', $matches[2]);
	$text = str_replace("\n\n", "\n", $text);

	return $matches[1] . $text . $matches[3];
}


# моя функция авторасстановки тэгов
function mso_balance_tags($text)
{
	//return $text;
	// те тэги, которые нужно закрывать автоматом до конца строки
	$blocks_for_close = 'p|li';

	$text = preg_replace("!<(" . $blocks_for_close . ")(.*)>(.*)([\n]*)!", "<$1$2>$3</$1>\n", $text);

	// удалим двойные закрывающие и открывающие и пустые
	$ar = explode('|', $blocks_for_close);
	foreach ($ar as $t)
	{
		$text = str_replace("<" . $t ."></" . $t .">", "", $text); //  <p></p> <li></li>
		$text = str_replace("<" . $t ."><" . $t .">", "<" . $t . ">", $text);
		$text = str_replace("</" . $t . "></" . $t . ">", "</" . $t . ">", $text);
	}

	$text = str_replace('</div></p>', '</p></div>', $text);
	$text = str_replace('<p></p></div>', '</div>', $text);
	$text = str_replace('<p></ul></div></p>', '</ul></div>', $text);
	$text = str_replace('<p></div></p>', '</div>', $text);

	$text = str_replace('<p></ul></p>', '</ul>', $text);
	$text = str_replace('<p></table></p>', '</table>', $text);
	$text = str_replace('<p></tr></p>', '</tr>', $text);
	$text = str_replace('<p></td></p>', '</td>', $text);
	$text = str_replace('</td></p>', '</p></td>', $text);
	$text = str_replace('<p></div></tr></p>', '</tr></div>', $text);

	$text = str_replace('<p></tr></tbody></table></p>', '</tr></tbody></table>', $text);

	$text = preg_replace('!<pre(.*?)</p>!si', '<pre$1', $text);
	$text = preg_replace('~<p><!--(.*?)--></p>~si', '<!--$1-->', $text);
	$text = preg_replace('~<p><a name=\"(.*?)\"></a></p>~si', '<a name="$1"></a>', $text);

	$text = preg_replace_callback('!(<ul>)(.*?)(</ul>)!si', 'mso_balance_tags_ul_callback', $text);

	$text = str_replace('<p></li></p>', '</li>', $text);

	$text = str_replace('<p> </p>', '<p>&nbsp;</p>', $text);

	$text = str_replace("\n\n\n\n", "\n", $text);
	$text = str_replace("\n\n\n", "\n", $text);
	$text = str_replace("\n\n", "\n", $text);

	$text = str_replace('<p>[html_base64]', '[html_base64]', $text);
	$text = str_replace('[/html_base64]</p>', '[/html_base64]', $text);
	$text = str_replace('[/html_base64] </p>', '[/html_base64]', $text);

	$text = preg_replace_callback('!\[html_base64\](.*?)\[\/html_base64\]!is', 'mso_clean_html_posle', $text );

	return $text;
}


# функция преобразует русские и украинские буквы в английские
# также удаляются все служебные символы
function mso_slug($slug)
{
	$slug = mso_hook('slug_do', $slug);

	if (!mso_hook_present('slug'))
	{
		// таблица замены
		$repl = array(
		"А"=>"a", "Б"=>"b",  "В"=>"v",  "Г"=>"g",   "Д"=>"d",
		"Е"=>"e", "Ё"=>"jo", "Ж"=>"zh",
		"З"=>"z", "И"=>"i",  "Й"=>"j",  "К"=>"k",   "Л"=>"l",
		"М"=>"m", "Н"=>"n",  "О"=>"o",  "П"=>"p",   "Р"=>"r",
		"С"=>"s", "Т"=>"t",  "У"=>"u",  "Ф"=>"f",   "Х"=>"h",
		"Ц"=>"c", "Ч"=>"ch", "Ш"=>"sh", "Щ"=>"shh", "Ъ"=>"",
		"Ы"=>"y", "Ь"=>"",   "Э"=>"e",  "Ю"=>"ju", "Я"=>"ja",

		"а"=>"a", "б"=>"b",  "в"=>"v",  "г"=>"g",   "д"=>"d",
		"е"=>"e", "ё"=>"jo", "ж"=>"zh",
		"з"=>"z", "и"=>"i",  "й"=>"j",  "к"=>"k",   "л"=>"l",
		"м"=>"m", "н"=>"n",  "о"=>"o",  "п"=>"p",   "р"=>"r",
		"с"=>"s", "т"=>"t",  "у"=>"u",  "ф"=>"f",   "х"=>"h",
		"ц"=>"c", "ч"=>"ch", "ш"=>"sh", "щ"=>"shh", "ъ"=>"",
		"ы"=>"y", "ь"=>"",   "э"=>"e",  "ю"=>"ju",  "я"=>"ja",

		# украина
		"Є" => "ye", "є" => "ye", "І" => "i", "і" => "i",
		"Ї" => "yi", "ї" => "yi", "Ґ" => "g", "ґ" => "g",

		"«"=>"", "»"=>"", "—"=>"-", "`"=>"", " "=>"-",
		"["=>"", "]"=>"", "{"=>"", "}"=>"", "<"=>"", ">"=>"",

		"?"=>"", ","=>"", "*"=>"", "%"=>"", "$"=>"",

		"@"=>"", "!"=>"", ";"=>"", ":"=>"", "^"=>"", "\""=>"",
		"&"=>"", "="=>"", "№"=>"", "\\"=>"", "/"=>"", "#"=>"",
		"("=>"", ")"=>"", "~"=>"", "|"=>"", "+"=>"", "”"=>"", "“"=>""
		);

		$slug = strtolower(strtr(trim($slug), $repl));

		# разрешим расширение .html
		$slug = str_replace('.htm', '@HTM@', $slug);
		$slug = str_replace('.', '', $slug);
		$slug = str_replace('@HTM@', '.htm', $slug);

		$slug = str_replace('---', '-', $slug);
		$slug = str_replace('--', '-', $slug);

		$slug = str_replace('-', ' ', $slug);
		$slug = str_replace(' ', '-', trim($slug));
	}
	else $slug = mso_hook('slug', $slug);

	return $slug;
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
# ведущий и конечные слэши удаляем
function mso_current_url()
{
	global $MSO;

	$url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ? "https://" : "http://";
	$url .= $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	$url = str_replace($MSO->config['site_url'], "", $url);

	$url = trim( str_replace('/', ' ', $url) );
	$url = str_replace(' ', '/', $url);

	return $url;
}


# формируем скрытый input для формы с текущей сессией
function mso_form_session($name_form = 'flogin_session_id')
{
	global $MSO;

	return '<input type="hidden" value="' . $MSO->data['session']['session_id'] . '" name="' . $name_form . '" />';
}


# вывод логин-формы
function mso_login_form($conf = array(), $redirect = '', $echo = true)
{
	global $MSO;

	if ($redirect == '') $redirect = urlencode(mso_current_url());

	$login = (isset($conf['login'])) ? $conf['login'] : '';
	$password = (isset($conf['password'])) ? $conf['password'] : '';
	$submit = (isset($conf['submit'])) ? $conf['submit'] : '';
	$submit_value = (isset($conf['submit_value'])) ? $conf['submit_value'] : t('Войти');
	$form_end = (isset($conf['form_end'])) ? $conf['form_end'] : '';

	$action = $MSO->config['site_url'] . 'login';
	$session_id = $MSO->data['session']['session_id'];

	$out = <<<EOF
	<form method="post" action="{$action}" name="flogin" id="flogin">
		<input type="hidden" value="{$redirect}" name="flogin_redirect" />
		<input type="hidden" value="{$session_id}" name="flogin_session_id" />
		<span>{$login}</span><input type="text" value="" name="flogin_user" id="flogin_user" />
		<span>{$password}</span><input type="password" value="" name="flogin_password" id="flogin_password" />
		{$submit}<input type="submit" name="flogin_submit" id="flogin_submit" value="{$submit_value}">
		{$form_end}
	</form>
EOF;
	if ($echo) echo $out;
		else return $out;
}


# посыл в хидере no-кэш
# кажется не работает - как проверить хз...
function mso_nocache_headers()
{
	# см. http://www.nomagic.ru/all.php?aid=58
	@header("Cache-Control: no-store, no-cache, must-revalidate"); 
	@header("Expires: " . date("r")); 
	
	// @header('Expires: Wed, 11 Jan 1984 05:00:00 GMT');
	// @header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
	// @header('Cache-Control: no-cache, must-revalidate, max-age=0');
	// @header('Pragma: no-cache');
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


# проверка комбинации логина-пароля
# если указан act - то сразу смотрим разрешение на действие
function mso_check_user_password($login = false, $password = false, $act = false)
{
	if (!$login or !$password) return false;

	$CI = & get_instance();

	$CI->db->select('users_id, users_groups_id');
	$CI->db->where(array('users_login'=>$login, 'users_password'=>$password) );  // where 'users_password' = $password
	$CI->db->limit(1); # одно значение

	$query = $CI->db->get('users');
	if ($query->num_rows() > 0) # есть такой юзер
	{
		if ($act)
		{
			// нужно проверить по users_groups_id разрешение для этого юзера для этого действия
			$r = $query->result_array();
			return mso_check_allow($act, $r[0]['users_id']);
		}
		else return true; // если act не указан, значит можно
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


# содание разрешения для действия
function mso_create_allow($act = '', $desc = '')
{
	global $MSO;

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

	if (!$act) return false;

	if ( $user_id == false ) // если юзер не указан
	{
		if (! $MSO->data['session']['users_id']) // и в сесии
			return false;
		else
			$user_id = $MSO->data['session']['users_id']; // берем его номер из сессии

		if ( $MSO->data['session']['users_groups_id'] == '1' ) // отмечена первая группа - это админы
		{
			return true; // админам всё можно
		}
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
# http://localhost/admin/users/edit/1
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
# $days и $month - массивы или строка (через пробел) названия дней недели и месяцев
function mso_date_convert($format = 'Y-m-d H:i:s', $data, $timezone = true, $days = false, $month = false)
{
	$res = '';
	$part = explode(' ' , $data);

	if (isset($part[0])) $ymd = explode ('-', $part[0]);
		else $ymd = array (0,0,0);

	if (isset($part[1])) $hms = explode (':', $part[1]);
		else $hms = array (0,0,0);

	$y = $ymd[0];
	$m = $ymd[1];
	$d = $ymd[2];
	$h = $hms[0];
	$n = $hms[1];
	$s = $hms[2];

	$time = mktime($h, $n, $s, $m, $d, $y);

	if ($timezone)
	{
		if ($timezone === -1) // в случаях, если нужно убрать таймзону
			$time = $time - getinfo('time_zone') * 60 * 60;
		else
			$time = $time + getinfo('time_zone') * 60 * 60;
	}

	$out = date($format, $time);

	if ($days)
	{
		if (!is_array($days)) $days = explode(' ', trim($days));

		$day_en = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
		$out = str_replace($day_en, $days, $out);

		$day_en = array('Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun');
		$out = str_replace($day_en, $days, $out);
	}

	if ($month)
	{
		if (!is_array($month)) $month = explode(' ', trim($month));

		$month_en = array('January', 'February', 'March', 'April', 'May', 'June', 'July',
							'August', 'September', 'October', 'November', 'December');
		$out = str_replace($month_en, $month, $out);

		$month_en = array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
		$out = str_replace($month_en, $month, $out);
	}

	return $out;
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
function mso_get_permalink_page($id = 0, $prefix = 'page/')
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

		return  $MSO->config['site_url'] . $prefix . $slug;
	}
	else return '';
}


# получить пермалинк рубрики по указанному слагу
function mso_get_permalink_cat_slug($slug = '', $prefix = 'category/')
{
	if (!$slug) return '';
	return  getinfo('siteurl') . $prefix . $slug;
}


#  разделить строку из чисел, разделенных запятыми в массив
# если $integer = true, то дополнительно преобразуется в числа
# если $probel = true, то разделителем может быть пробел
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


# подсчет кол-ва слов в тексте
# можно предварительно удалить все тэги и преобразовать CR в $delim
function mso_wordcount($str = '', $delim = ' ', $strip_tags = true, $cr_to_delim = true)
{
	if ($strip_tags) $str = strip_tags($str);
	if ($cr_to_delim) $str = str_replace("\n", $delim, $str);

	$out = str_replace($delim . $delim, $delim, $str);

	return count( explode($delim, $str) );
}


# получить текущую страницу пагинации
# next - признак сегмент после которого указывается номер страницы
function mso_current_paged($next = 'next')
{
	global $MSO;

	$uri = $MSO->data['uri_segment'];

	if ($n = mso_array_get_key_value($uri, $next))
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

	$MSO->sidebars[$sidebar] = array('title' => t($title), 'options' => $options);
}


# регистрируем виджет
function mso_register_widget($widget = false, $title = 'Виджет')
{
	global $MSO;

	if ($widget) $MSO->widgets[$widget] = t($title);
}


# вывод сайбрара
function mso_show_sidebar($sidebar = '1', $block_start = '', $block_end = '')
{
	global $MSO;

	static $num_widget = array(); // номер виджета по порядку в одном сайдбаре

	$widgets = mso_get_option('sidebars-' . $sidebar, 'sidebars', array());

	$out = '';

	if ($widgets) // есть виджеты
	{
		foreach ($widgets as $widget)
		{
			$usl_res = 1; // предполагаем, что нет условий, то есть всегда true

			// имя виджета может содержать номер через пробел
			$arr_w = explode(' ', $widget); // в массив
			if ( sizeof($arr_w) > 1 ) // два или больше элементов
			{
				$widget = trim( $arr_w[0] ); // первый - функция
				$num = (int) trim( $arr_w[1] ); // второй - номер виджета

				if (isset($arr_w[2])) // есть какое-то php-условие
				{
					$u = $arr_w; // поскольку у нас разделитель пробел, то нужно до конца все подбить в одну строчку
					$u[0] = $u[1] = '';
					$usl = trim(implode(' ', $u));

					// текст условия, is_type('home') or is_type('category')
					$usl = 'return ( ' . $usl . ' ) ? 1 : 0;';
					$usl_res = eval($usl); // выполяем
					if ($usl_res === false) $usl_res = 1; // возможно произошла ошибка
				}
			}
			else
			{
				$num = 0; // номер виджета не указан, значит 0
			}

			if ( function_exists($widget) and $usl_res === 1)
			{
				if ($temp = $widget($num)) // выполняем виджет если он пустой, то пропускаем вывод
				{
					if (isset($num_widget[$sidebar]['numw'])) //уже есть номер виджета
					{
						$numw = ++$num_widget[$sidebar]['numw'];
						$num_widget[$sidebar]['numw'] = $numw;
					}
					else // нет такого = пишем 1
					{
						$numw = $num_widget[$sidebar]['numw'] = 1;
					}

					$st = str_replace('[FN]', $widget, $block_start); // название функции виджета
					$st = str_replace('[NUMF]', $num, $st); // номер функции
					$st = str_replace('[NUMW]', $numw, $st);	//
					$st = str_replace('[SB]', $sidebar, $st); // номер сайдбара

					$en = str_replace('[FN]', $widget, $block_end);
					$en = str_replace('[NUMF]', $num, $en);
					$en = str_replace('[NUMW]', $numw, $en);
					$en = str_replace('[SB]', $sidebar, $en);

					$out .= $st . $temp . $en;
				}
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
function mso_mail($email = '', $subject = '', $message = '', $from = false)
{
	$CI = & get_instance();
	$CI->load->library('email');

	if ($from) $admin_email = $from;
		else $admin_email = mso_get_option('admin_email_server', 'general', 'admin@site.com');

	$config['wordwrap'] = TRUE;
	$config['wrapchars'] = 90;
	$CI->email->initialize($config);

	$CI->email->to($email);
	$CI->email->from($admin_email, getinfo('name_site'));
	$CI->email->subject($subject);
	$CI->email->message($message);

	// pr($admin_email);
	// pr($CI->email);

	$res = @$CI->email->send();

	if (!$res) echo $CI->email->print_debugger();

	return $res;
}


# для юникода отдельный wordwrap
# часть кода отсюда: http://us2.php.net/manual/ru/function.wordwrap.php#78846
# переделал и исправил ошибки я
# ширина, разделитель
function mso_wordwrap($str, $wid = 80, $tag = ' ')
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


# возвращает script с jquery или +url
function mso_load_jquery($plugin = '')
{
	global $MSO;

	if ( !isset($MSO->js['jquery'][$plugin]) ) // еще нет включения этого плагина
	{
		$MSO->js['jquery'][$plugin] = '1';
		if ($plugin)
			return '<script type="text/javascript" src="'. getinfo('common_url') . 'jquery/' . $plugin . '"></script>' . NR;
		else
			return '<script type="text/javascript" src="'. getinfo('common_url') . 'jquery/jquery-1.3.2.min.js"></script>' . NR;
	}
}


# формируем li-элементы для меню
# элементы представляют собой текст, где каждая строчка один пункт
# каждый пункт делается так:  http://ссылка|название
# на выходе так:
# <li class="selected"><a href="url"><span>ссылка</span></a></li>
function mso_menu_build($menu = '', $select_css = 'selected', $add_link_admin = false)
{
	global $MSO;

	# добавить ссылку на admin
	if ($add_link_admin and is_login()) $menu .= NR . 'admin|Admin';

	$menu = str_replace("_NR_", "\n", $menu);
	$menu = str_replace("\n\n\n", "\n", $menu);
	$menu = str_replace("\n\n", "\n", $menu);

	# в массив
	$menu = explode("\n", trim($menu));

	# определим текущий url
	$http = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ? "https://" : "http://";
	$current_url = $http . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

	$out = '';
	# обходим в цикле
	$i = 1;
	foreach ($menu as $elem)
	{
		# разобъем строчку по адрес | название
		$elem = explode('|', $elem);

		# должно быть два элемента
		if (count($elem) > 1 )
		{
			$url = trim($elem[0]);  // адрес
			$name = trim($elem[1]); // название

			if (strpos($url, $http) === false) // нет в адресе http:// - значит это текущий сайт
			{
				if ($url == '/') $url = getinfo('siteurl'); // это главная
					else $url = getinfo('siteurl') . $url;
			}

			# если текущий адрес совпал, значит мы на этой странице
			if ($url == $current_url) $class = $select_css;
				else $class = '';

			# для первого элемента добавляем класс first
			if ($i == 1) $class .= ' first';

			# для последнего элемента добавляем класс last
			if ($i == count($menu)) $class .= ' last';

			$class = trim($class);

			if ($class)
				$out .= '<li class="' . $class . '"><a href="' . $url . '"><span>' . $name . '</span></a></li>' . NR;
			else
				$out .= '<li><a href="' . $url . '"><span>' . $name . '</span></a></li>' . NR;

			$i++;
		}
	}
	return $out;
}


# добавляем куку ко всему сайту с помощью сессии и редиректа на главную или другую указанную страницу (после главной)
function mso_add_to_cookie($name_cookies, $value, $expire, $redirect = false)
{
	$CI = & get_instance();

	if (isset($CI->session->userdata['_add_to_cookie'])) $add_to_cookie = $CI->session->userdata['_add_to_cookie'];
		else $add_to_cookie = array();

	$add_to_cookie[$name_cookies] = array('value'=>$value, 'expire'=> $expire );

	$CI->session->set_userdata(	array(	'_add_to_cookie' => $add_to_cookie ) );
	$CI->session->set_userdata(	array(	'_add_to_cookie_redirect' => $redirect ) ); // куда редиректимся

	if ($redirect)
	{
		mso_redirect(getinfo('siteurl'), true);
		exit;
	}
}


# получаем куку. Если нет вообще или нет в $allow_vals, то возвращает $def_value
function mso_get_cookie($name_cookies, $def_value = '', $allow_vals = false)
{

	if (!isset($_COOKIE[$name_cookies])) return $def_value; // нет вообще

	$value = $_COOKIE[$name_cookies]; // значение куки

	if ($allow_vals)
	{
		if (in_array($value, $allow_vals)) return $value; // нет в разрешенных
		else return $def_value;
	}
	else return $value;
}


# функция построения из массивов списка UL
# вход - массив из с [childs]=>array(...)
function mso_create_list($a = array(), $options = array(), $child = false)
{
	if (!$a) return '';

	if (!isset($options['class_ul'])) $options['class_ul'] = ''; // класс UL
	if (!isset($options['class_ul_style'])) $options['class_ul_style'] = ''; // свой стиль для UL
	if (!isset($options['class_child'])) $options['class_child'] = 'child'; // класс для ребенка
	if (!isset($options['class_child_style'])) $options['class_child_style'] = ''; // свой стиль для ребенка

	if (!isset($options['class_current'])) $options['class_current'] = 'current-page'; // класс li текущей страницы
	if (!isset($options['class_current_style'])) $options['class_current_style'] = ''; // стиль li текущей страницы

	if (!isset($options['class_li'])) $options['class_li'] = ''; // класс LI
	if (!isset($options['class_li_style'])) $options['class_li_style'] = ''; // стиль LI

	if (!isset($options['format'])) $options['format'] = '[LINK][TITLE][/LINK]'; // формат ссылки
	if (!isset($options['format_current'])) $options['format_current'] = '<span>[TITLE]</span>'; // формат для текущей

	if (!isset($options['title'])) $options['title'] = 'page_title'; // имя ключа для титула
	if (!isset($options['link'])) $options['link'] = 'page_slug'; // имя ключа для слага
	if (!isset($options['descr'])) $options['descr'] = 'category_desc'; // имя ключа для описания
	if (!isset($options['id'])) $options['id'] = 'page_id'; // имя ключа для id
	if (!isset($options['slug'])) $options['slug'] = 'page_slug'; // имя ключа для slug
	if (!isset($options['menu_order'])) $options['menu_order'] = 'page_menu_order'; // имя ключа для menu_order
	if (!isset($options['id_parent'])) $options['id_parent'] = 'page_id_parent'; // имя ключа для id_parent

	if (!isset($options['count'])) $options['count'] = 'count'; // имя ключа для количества элементов

	if (!isset($options['prefix'])) $options['prefix'] = 'page/'; // префикс для ссылки
	if (!isset($options['current_id'])) $options['current_id'] = true; // текущая страница отмечается по page_id - иначе по текущему url
	if (!isset($options['childs'])) $options['childs'] = 'childs'; // поле для массива детей

	# функция, которая сработает на [FUNCTION]
	# эта функция получает в качестве параметра текущий массив $elem
	if (!isset($options['function'])) $options['function'] = false;

	$class_child = $class_child_style = $class_ul = $class_ul_style = '';
	$class_current = $class_current_style = $class_li = $class_li_style = '';

	if ($options['class_child']) $class_child = ' class="' . $options['class_child'] . '"';
	if ($options['class_child_style']) $class_child_style = ' style="' . $options['class_child_style'] . '"';
	if ($options['class_ul']) $class_ul = ' class="' . $options['class_ul'] . '"';
	if ($options['class_ul_style']) $class_ul_style = ' style="' . $options['class_ul_style'] . '"';

	if ($options['class_current']) $class_current = ' class="' . $options['class_current'] . '"';
	if ($options['class_current_style']) $class_current_style = ' style="' . $options['class_current_style'] . '"';
	if ($options['class_li']) $class_li = ' class="' . $options['class_li'] . '"';
	if ($options['class_li_style']) $class_li_style = ' style="' . $options['class_li_style'] . '"';


	if ($child) $out = NR . '	<ul' . $class_child . $class_child_style . '>';
		else $out = NR . '<ul' . $class_ul . $class_ul_style . '>';

	$current_url = getinfo('siteurl') . mso_current_url(); // текущий урл

	foreach ($a as $elem)
	{
		$title = $elem[$options['title']];
		$url = getinfo('siteurl') . $options['prefix'] . $elem[$options['link']];

		$link = '<a href="' . $url . '" title="' . mso_strip($title) . '">';

		if (isset($elem[$options['descr']])) $descr = $elem[$options['descr']];
		else $descr = '';

		if (isset($elem[$options['count']])) $count = $elem[$options['count']];
		else $count = '';

		if (isset($elem[$options['id']])) $id = $elem[$options['id']];
		else $id = '';

		if (isset($elem[$options['slug']])) $slug = $elem[$options['slug']];
		else $slug = '';

		if (isset($elem[$options['menu_order']])) $menu_order = $elem[$options['menu_order']];
		else $menu_order = '';

		if (isset($elem[$options['id_parent']])) $id_parent = $elem[$options['id_parent']];
		else $id_parent = '';

		$cur = false;

		if ($options['current_id']) // текущий определяем по id страницы
		{
			if (isset($elem['current']))
			{
				$e = $options['format_current'];
				$cur = true;
			}
			else
				$e = $options['format'];
		}
		else // определяем по урлу
		{
			if ($url == $current_url)
			{
				$e = $options['format_current'];
				$cur = true;
			}
			else $e = $options['format'];

		}

		$e = str_replace('[LINK]', $link, $e);
		$e = str_replace('[/LINK]', '</a>', $e);
		$e = str_replace('[TITLE]', $title, $e);
		$e = str_replace('[DESCR]', $descr, $e);
		$e = str_replace('[ID]', $id, $e);
		$e = str_replace('[SLUG]', $slug, $e);
		$e = str_replace('[MENU_ORDER]', $menu_order, $e);
		$e = str_replace('[ID_PARENT]', $id_parent, $e);
		$e = str_replace('[COUNT]', $count, $e);

		if ($options['function'] and function_exists($options['function']))
		{
			$function = $options['function']($elem);
			$e = str_replace('[FUNCTION]', $function, $e);
		}
		else $e = str_replace('[FUNCTION]', '', $e);

		if (isset($elem[$options['childs']]))
		{
			if ($cur) $out .= NR . '<li' . $class_current . $class_current_style . '>' . $e;
				else $out .= NR . '<li' . $class_li . $class_li_style . '>' . $e;
			$out .= mso_create_list($elem[$options['childs']], $options, true);
			$out .= NR . '</li>';
		}
		else
		{
			if ($child) $out .= NR . '	';
				else $out .= NR;

			if ($cur) $out .= '<li' . $class_current . $class_current_style . '>' . $e . '</li>';
				else $out .= '<li' . $class_li . $class_li_style . '>' . $e . '</li>';
		}
	}

	if ($child) $out .= NR . '	</ul>' . NR;
		else $out .= NR . '</ul>' . NR;

	return $out;
}


# устанавливаем $MSO->current_lang_dir в которой хранится
# текущий каталог языка. Это второй параметр функции t()
function mso_cur_dir_lang($dir = false)
{
	global $MSO;
	return $MSO->current_lang_dir = $dir;
}


# функция трансляции (языковой перевод)
# первый параметр - переводимое слово - учитывается регистр полностью
# второй параметр - переводимый файл либо __FILE__
# либо путь к каталогу относительно application/maxsite/
# например:
#	для плагина ушки это plugins/ushki
# 	для админ - admin
#	для общего - common (используется по-умолчанию)
# файл перевода должен находится в каталоге $file/language/язык.php
# если второй параметр равен plugins, то язык берется из application/maxsite/common/language/plugins/
# если второй параметр равен templates, то язык берется из application/maxsite/common/language/templates/
function t($w = '', $file = false)
{
	global $MSO;

	if (!isset($MSO->language)) return $w;

	$current_language = $MSO->language; // тест на английский

	if (!$current_language) return $w; // не указан язык, выходим

	static $langs = array(); // общий массив перевода

	if (!$file) // не указан каталог
	{
		if ($MSO->current_lang_dir) // есть в $MSO_CURRENT_LANG_DIR
			$file = $MSO->current_lang_dir;
		else
			$file = 'common'; // берем дефолтный - common
	}

	// путь относительно application/maxsite/
	if ($file != 'common' and $file != 'plugins' and $file != 'templates' )
	{
		// заменим windows \ на /
		$file = str_replace('\\', '/', $file);
		$bd = str_replace('\\', '/', $MSO->config['base_dir']);

		// если в $file входит base_dir, значит это использован __FILE__
		// нужно вычленить base_dir
		$pos = strpos($file, $bd);
		if ($pos !== false) // есть вхождение
		{
			$file = str_replace($bd, '', $file);
			$file = dirname($file);
		}
	}

	// спецкаталог плагины и шаблоны, физически в common/language/plugins
	$spec_dir = '';
	if ($file == 'plugins')
	{
		$file = 'common';
		$spec_dir = 'plugins';
	}
	elseif ($file == 'templates')
	{
		$file = 'common';
		$spec_dir = 'templates';
	}

	// pr($langs);
	// $file у нас каталог plugins/ushki
	// если в $langs нет такого ключа, значит нужно проверить есть ли файл
	// plugins/ushki/language/en.php

	// если это спецкаталог, то ключ по нему - иначе по файлу
	if ($spec_dir) $key = $spec_dir;
		else $key = $file;

	if (!isset($langs[$key])) // нет такого ключа
	{
		$langs[$key] = array();

		// слэш в конец добавим
		if ($spec_dir) $current_language = '/' . $current_language;

		// грузим файл, если есть
		$fn = $MSO->config['base_dir'] . $file . '/language/' . $spec_dir . $current_language . '.php';

		if (file_exists($fn))
		{
			// есть такой файл
			require_once ($fn);

			if (isset($lang)) // есть ли в нем $lang ?
			{
				$langs[$key] = $lang;
			}
		}
	}

	// pr($langs);

	// если есть такой перевод, заменяем его
	// если перевод пустое слово, то не меняем
	if (isset($langs[$key][$w]) and $langs[$key][$w]) $w = $langs[$key][$w];

	return $w;
}


# получение информации об авторе по его номеру из url http://localhost/author/1
# или явно указанному номеру
function mso_get_author_info($id = 0)
{
	if (!$id) $id = mso_segment(2);
	if (!$id or !is_numeric($id)) return array(); // неверный id

	$key_cache = 'mso_get_author_info_' . $id;
	if ( $k = mso_get_cache($key_cache) ) return $k; // да есть в кэше

	$out = array();

	$CI = & get_instance();
	$CI->db->select('*');
	$CI->db->where('users_id', $id);
	$query = $CI->db->get('users');

	if ($query->num_rows() > 0) # есть такой юзер
	{
		$out = $query->result_array();
		$out = $out[0];
	}

	mso_add_cache($key_cache, $out);

	return $out;
}


# получение текущих сегментов url в массив
# в отличие от CodeIgniter - происходит анализ get и отсекание до «?»
# если «?» нет, то возвращает стандартное $this->uri->segment_array();
function mso_segment_array()
{
	$CI = & get_instance();

	if ( isset($_SERVER['REQUEST_URI']) and $_SERVER['REQUEST_URI'] )
	{
		// http://localhost/page/privet?get=dsfsdklfjkldsjflsdf
		$url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ? "https://" : "http://";
		$url .= $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		$url = str_replace($CI->config->config['base_url'], '', $url); // page/privet?get=dsfsdklfjkldsjflsdf

		if ( strpos($url, '?') !== FALSE ) // есть «?»
		{
			$url = explode('?', $url); // разделим в массив
			$url = $url[0]; // сегменты - это только первая часть
			$url = explode('/', $url); // разделим в массив по /

			// нужно изменить нумерацию - начало с 1
			$out = array();
			$i = 1;
			foreach($url as $val)
			{
				if ($val)
				{
					$out[$i] = $val;
					$i++;
				}
			}

			return $out;
		}
		else return $CI->uri->segment_array();
	}
	else return $CI->uri->segment_array();
}


# получение get-строки из текущего адреса
function mso_url_get()
{
	$CI = & get_instance();
	if ( isset($_SERVER['REQUEST_URI']) and $_SERVER['REQUEST_URI'] and (strpos($_SERVER['REQUEST_URI'], '?') !== FALSE) )
	{
		$url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ? "https://" : "http://";
		$url .= $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		$url = str_replace($CI->config->config['base_url'], "", $url);
		$url = explode('?', $url);
		return $url[1];
	}
	else return '';
}


# функция преобразования get-строки в массив
# разделитель элементов массива & или &amp;
# значение через стандартную parse_str
function mso_parse_url_get($s = '')
{
	if ($s)
	{
		$s = str_replace('&amp;', '&', $s);
		$s = explode('&', $s);
		$uri_get_array = array();
		foreach ($s as $val)
		{
			parse_str($val, $arr);
			foreach ($arr as $key1 => $val1)
			{
				$uri_get_array[$key1] = $val1;
			}
		}
		return $uri_get_array;
	}
	else return array();
}

?>