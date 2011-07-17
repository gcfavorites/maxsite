<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

	# файл functions.php подключается при инициализации сайта
	# в этом файле нельзя выводить данные в браузер!
	
	# регистрируем сайдбары - имя, заголовок.
	# если имя совпадает, то берется последний заголовок
	mso_register_sidebar('1', t('Первый сайдбар', 'templates'));
	
	
	# меняем форат вывода заголовков Hx
	// заготовок виджета
	mso_set_val('widget_header_start', '<div class="widget_header"><span>');
	mso_set_val('widget_header_end', '</span></div><!-- class="widget_header" -->');
	
	// оставьте комментарий
	mso_set_val('leave_a_comment_start', '<div class="leave_a_comment">');
	mso_set_val('leave_a_comment_end', '</div>');
	
	//Комментариев
	mso_set_val('page_comments_count_start', '<div class="page_comments_count">');
	mso_set_val('page_comments_count_end', '</div>');
	
	//Подписаться на эту рубрику по RSS
	mso_set_val('show_rss_text_start', '<p class="show_rss_text">');
	mso_set_val('show_rss_text_end', '</p>');
	
	// Рубрика-заголовок в home-cat-block
	mso_set_val('home_full_text_cat_start', '<div class="header">');
	mso_set_val('home_full_text_cat_end', '</div>');
	
	// Еще записи по теме
	mso_set_val('page_other_pages_start', '<div class="page_other_pages_header">');
	mso_set_val('page_other_pages_end', '</div>');




# функция возвращает массив $path_url-файлов по указанному $path - каталог на сервере
# $full_path - нужно ли возвращать полный адрес (true) или только имя файла (false)
# $exts - массив требуемых расширений. По-умолчанию - картинки
if (!function_exists('get_path_files'))
{
	function get_path_files($path = '', $path_url = '', $full_path = true, $exts = array('jpg', 'jpeg', 'png', 'gif'))
	{
		// если не указаны пути, то отдаём пустой массив
		if (!$path or !$path_url) return array();
		if (!is_dir($path)) return array(); // это не каталог

		$CI = & get_instance(); // подключение CodeIgniter
		$CI->load->helper('directory'); // хелпер для работы с каталогами
		$files = directory_map($path, true); // получаем все файлы в каталоге
		if (!$files) return array();// если файлов нет, то выходим

		$all_files = array(); // результирующий массив с нашими файлами
		
		// функция directory_map возвращает не только файлы, но и подкаталоги
		// нам нужно оставить только файлы. Делаем это в цикле
		foreach ($files as $file)
		{
			if (@is_dir($path . $file)) continue; // это каталог
			
			$ext = substr(strrchr($file, '.'), 1);// расширение файла
			
			// расширение подходит?
			if (in_array($ext, $exts))
			{
				if (strpos($file, '_') === 0) continue; // исключаем файлы, начинающиеся с _
				
				// добавим файл в массив сразу с полным адресом
				if ($full_path)
					$all_files[] = $path_url . $file;
				else
					$all_files[] = $file;
			}
		}
		
		natsort($all_files); // отсортируем список для красоты
		
		return $all_files;
	}
}

# возвращает файлы для favicon
if (!function_exists('default_favicon'))
{
	function default_favicon()
	{
		$all = get_path_files(getinfo('template_dir') . 'images/favicons/', getinfo('template_url') . 'images/favicons/', false);
		return implode($all, '#');
	}
}

# возвращает файлы для компонент
if (!function_exists('default_components'))
{
	function default_components()
	{
		static $all = false; // запоминаем результат, чтобы несколько раз не вызывать функцию get_path_files
		
		if ($all === false)
			$all = get_path_files(getinfo('template_dir') . 'components/', getinfo('template_url') . 'components/', false, array('php'));
			
		return '0||' . t('Отсутствует', __FILE__) . '#' . implode($all, '#');
	}
}


# возвращает файлы для css-профиля
if (!function_exists('default_profiles'))
{
	function default_profiles()
	{
		$all = get_path_files(getinfo('template_dir') . 'css/profiles/', getinfo('template_url') . 'css/profiles/', false, array('css'));
		return implode($all, '#');
	}
}

# возвращает файлы для логотипа
if (!function_exists('default_header_logo'))
{
	function default_header_logo()
	{
		$all = get_path_files(getinfo('template_dir') . 'images/logos/', getinfo('template_url') . 'images/logos/', false);
		return implode($all, '#');
	}
}


# возвращает каталоги в uploads, где могут храниться файлы для шапки 
if (!function_exists('default_header_image'))
{
	function default_header_image()
	{
		$CI = & get_instance(); // подключение CodeIgniter
		$CI->load->helper('directory'); // хелпер для работы с каталогами
		$all_dirs = directory_map(getinfo('uploads_dir'), true); // только в uploads
		
		$dirs = array();
		foreach ($all_dirs as $d)
		{
			// нас интересуют только каталоги
			if (is_dir( getinfo('uploads_dir') . $d) and $d != '_mso_float' and $d != 'mini' and $d != '_mso_i' and $d != 'smiles')
			{
				$dirs[] = $d;
			}
		}
		
		natsort($dirs);
		
		return '-template-||' . t('Каталог шаблона', __FILE__) . '#' . implode($dirs, '#');
	}
}


# вывод подключенных css-профилей
if (!function_exists('default_out_profiles'))
{
	function default_out_profiles()
	{
		if ($default_profiles = mso_get_option('default_profiles', 'templates', array())) // есть какие-то профили оформления
		{
			$css_out = '';
			foreach($default_profiles as $css_file)
			{
				$fn = getinfo('template_dir') . 'css/profiles/' . $css_file;
				
				if (file_exists($fn)) 
					$css_out .= file_get_contents($fn) . NR;
			}
			
			if ($css_out) echo NT . '<style type="text/css">' . NR . $css_out . NT . '</style>' . NR;
		}
	}
}

# функция возвращает полный путь к файлу компоненты для указанной опции
# $option - опция
# $def_file - файл по умолчанию
# пример использования
# if ($fn = get_component_fn('default_header_component2', 'menu.php')) require($fn);
if (!function_exists('get_component_fn'))
{
	function get_component_fn($option = '', $def_file = '')
	{
		if ($fn = mso_get_option($option, 'templates', $def_file)) // получение опции
		{
			if (file_exists(getinfo('template_dir') . 'components/' . $fn)) // проверяем если файл в наличии
				return (getinfo('template_dir') . 'components/' . $fn); // да
		}
		return false; // ничего нет
	}
}

# функция подключает файлы css-style установленных компонентов и выводит их содержимое в едином блоке <style>
# использовать в head 
# $component_options - названия опций, которыми определяются компоненты в шаблоне
# css-файл компонента находится в общем css-каталоге шаблона с именем помпонетна, наример menu.php и menu.css
if (!function_exists('out_component_css'))
{
	function out_component_css($component_options = array('default_header_component1', 'default_header_component2', 'default_header_component3', 'default_header_component4', 'default_header_component5', 'default_footer_component1', 'default_footer_component2', 'default_footer_component3', 'default_footer_component4', 'default_footer_component5'))
	{
		
		// $css_files = array(); // результирующий массив css-файлов
		$css_out = ''; // все стили из файлов
		
		// проходимся по всем заданным опциям
		foreach($component_options as $option)
		{
			// и если они определены
			if ($fn = mso_get_option($option, 'templates', false))
			{
				$fn = str_replace('.php', '.css', $fn); // в имени файла следует заменить расширение php на css
				
				if (file_exists(getinfo('template_dir') . 'components/css/' . $fn)) // проверяем если ли файл в наличии
				{
					// $css_files[] = $fn; // запомнили имя
					
					// получаем содержимое
					if ($r = @file_get_contents(getinfo('template_dir') . 'components/css/' . $fn))
						$css_out .= $r . NR;
				}
			}
		}
		
		if ($css_out) // если есть что выводить
		{
			echo NT . '<style type="text/css">' . NR . $css_out . TAB . '</style>' . NR;
		}
	}
}



# дополнительный файл my_functions.php
if (file_exists(getinfo('template_dir') . 'custom/my_functions.php')) require(getinfo('template_dir') . 'custom/my_functions.php');

