<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */


# функция автоподключения плагина
function down_count_autoload($args = array())
{
	mso_create_allow('down_count_edit', t('Админ-доступ к настройкам счетчика переходов (Download count)', 'plugins')); 
	mso_hook_add( 'admin_init', 'down_count_admin_init'); # хук на админку
	mso_hook_add( 'content', 'down_count_content'); # хук на обработку текста
	mso_hook_add( 'init', 'down_count_init'); # хук на обработку входящего url
}

# функция выполняется при деинстяляции плагина
function down_count_uninstall($args = array())
{	
	mso_delete_option('plugin_down_count', 'plugins'); // удалим созданные опции
	return $args;
}

# функция выполняется при указаном хуке admin_init
function down_count_admin_init($args = array()) 
{
	if ( !mso_check_allow('down_count_edit') ) return $args;
			
	$this_plugin_url = 'plugin_down_count'; // url и hook
	mso_admin_menu_add('plugins', $this_plugin_url, t('Счетчик переходов', 'plugins'));
	mso_admin_url_hook ($this_plugin_url, 'down_count_admin_page');
	
	return $args;
}

# функция вызываемая при хуке, указанном в mso_admin_url_hook
function down_count_admin_page($args = array()) 
{
	# выносим админские функции отдельно в файл
	global $MSO;
	if ( !mso_check_allow('down_count_edit') ) 
	{
		echo t('Доступ запрещен', 'plugins');
		return $args;
	}
	
	mso_hook_add_dinamic( 'mso_admin_header', ' return $args . "' . t('Счетчик переходов', 'plugins') . ' "; ' );
	mso_hook_add_dinamic( 'admin_title', ' return "' . t('Счетчик переходов', 'plugins') . ' - " . $args; ' );

	require($MSO->config['plugins_dir'] . 'down_count/admin.php');
}

function down_count_get_data()
{
	// вспомогательная опция, которая получает массив из файла
	// делаем её статик, чтобы не было многократного ображения к файлу
	// когда нужно получить данные из хука на контент
	
	global $MSO;
	static $data;
	
	if (!isset($data))
	{
		$options = mso_get_option('plugin_down_count', 'plugins', array());
		if ( !isset($options['file']) ) $options['file'] = 'dc.dat';

		$fn = $MSO->config['uploads_dir'] . $options['file'];
		
		$CI = & get_instance();
		$CI->load->helper('file'); // хелпер для работы с файлами
		
		if (!file_exists( $fn )) // файла нет, нужно его создать
			write_file($fn, serialize(array())); // записываем в него пустой массив
		
		// массив данных
		// url => array ( count=>77 )
		$data = unserialize( read_file($fn) ); // поулчим из файла
	}

	return $data;
}


# функции плагина
function down_count_init($args = array())
{
	global $MSO;
	
	# опции плагина
	$options = mso_get_option('plugin_down_count', 'plugins', array());
	if ( !isset($options['prefix']) ) $options['prefix'] = 'dc';
	
	if (mso_segment(1) == $options['prefix'] and mso_segment(2)) 
	{
		if ( !isset($options['referer']) ) $options['referer'] = 1; // запретить скачку с чужих сайтов
		
		if ($options['referer'])
		{
			// если нет реферера, то рубим
			if (!isset($_SERVER['HTTP_REFERER'])) //
				die( sprintf('<b><font color="red">' . t('Данная ссылка доступна только со <a href="%s">страниц сайта</a>', 'plugins') . '</font></b>', getinfo('siteurl')) );
			
			// проверяем реферер - откуда пришел
			$p = parse_url($_SERVER['HTTP_REFERER']);
			if (isset($p['host'])) $p = $p['host'];
				else $p = '';
			if ( $p != $_SERVER['HTTP_HOST'] ) // чужой сайт
				die('<b><font color="red">' . t('Запрещен переход по этой ссылке с чужого сайта', 'plugins') . '</font></b>');
		}
		
		// это редирект на указанный в сегментах url
		
		$url = base64_decode(mso_segment(2)); // декодируем
		
		// все урлы хранятся в файле 
		// в виде серилизованного массива
		if ( !isset($options['file']) ) $options['file'] = 'dc.dat';
		
		$fn = $MSO->config['uploads_dir'] . $options['file']; // имя файла
		
		$CI = & get_instance();
		$CI->load->helper('file'); // хелпер для работы с файлами
		
		if (!file_exists( $fn )) // файла нет, нужно его создать
			write_file($fn, serialize(array())); // записываем в него пустой массив
		
		// массив данных: url => array ( count=>77 )
		$data = unserialize( read_file($fn) ); // получим из файла
		
		if (isset($data[$url])) // такой url уже есть
			$data[$url]['count'] = $data[$url]['count'] + 1;
		else // нет еще
			$data[$url]['count'] = 1; // записываем один переход
		
		write_file($fn, serialize($data) ); // созраняем в файл

		header("Location: $url"); // переход на файл
		exit;
	}
	return $args;
}


function down_count_content_callback($matches)
{
	//'|\[dc\]<a(.*?)href="(.*?)"(.*?)>(.*?)</a>\[/dc\]|ui';
	
	// ститик, чтобы не получать каждый раз опции
	static $prefix, $format;
	
	if (!isset($prefix) or !isset($format)) 
	{
		$options = mso_get_option('plugin_down_count', 'plugins', array());
		
		if ( !isset($options['prefix']) ) $options['prefix'] = 'dc';
		$prefix = $options['prefix'];
		
		if ( !isset($options['format']) ) $options['format'] = ' <sup title="' . t('Количество переходов', 'plugins') . '">%COUNT%</sup>';
		$format = $options['format'];
	}
	
	$data = down_count_get_data(); // получаем массив из файла, в котором ведется подсчет колва переходов

	if (isset( $data[$matches[2]]['count'] )) $count = $data[$matches[2]]['count'];
		else $count = 0;
	
	$url  = base64_encode($matches[2]); // кодируем урл в одну строку
	$url  = getinfo('siteurl') . $prefix . '/' . $url;
	
	$format_out = str_replace('%COUNT%', $count, $format);
	$matches[1] = str_replace('%COUNT%', $count, $matches[1]);
	$matches[3] = str_replace('%COUNT%', $count, $matches[3]);
	
	$out = '<a' . $matches[1] . 'href="' . $url . '"' . ' title="' . $matches[2] . '" '. $matches[3] . '>' . $matches[4] . '</a>' . $format_out;
	
	return $out;
}


# замена ссылок в тексте
function down_count_content($text = '')
{
	// [dc]<a href="http://localhost/codeigniter/">ссылка</a>[/dc]
	$pattern = '|\[dc\]<a(.*?)href="(.*?)"(.*?)>(.*?)</a>\[/dc\]|ui';
	$text = preg_replace_callback($pattern, 'down_count_content_callback', $text);

	return $text;
}



?>