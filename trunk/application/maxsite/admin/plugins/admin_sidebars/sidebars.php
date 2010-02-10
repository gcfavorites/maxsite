<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (с) http://maxsite.org/
 */

	$CI = & get_instance();
	
	// проверяем входящие данные
	if ( $post = mso_check_post(array('f_session_id', 'f_submit', 'f_sidebars')) )
	{
		# защита рефера
		mso_checkreferer();
		
		# защита сессии - если нужно убрать коммент
		// if ($MSO->data['session']['session_id'] != $fo_session_id) mso_redirect('loginform');
		
		$sidebars = $post['f_sidebars'];
		
		# перебираем поулченные сайдбары
		foreach ($sidebars as $sidebar => $widgets)
		{
			# готовим опцию для каждого
			$option = array();
			
			$widgets = explode("\n", $widgets); // в массив, потому что указано через Enter
			// проверяем виджеты
			foreach ($widgets as $widget)
			{
				$widget = trim($widget); // удлаим лишнее
				if ($widget) $option[] = $widget; // добавим в опцию
			}
			
			// pr($option);
			mso_add_option('sidebars-' . mso_slug($sidebar), $option, 'sidebars'); // добавили
		}
		
		echo '<div class="update">Обновлено!</div>';
		
		// pr($sidebars);

		// поскольку мы обновили опции, то обновляем и их кэш
		mso_refresh_options();
	}
?>

<h1>Настройки сайдбаров</h1>
<p class="info">Добавьте в сайдбары необходимые виджеты. Каждый виджет в одной строчке. Виджеты будут отображаться в указанном вами порядке. Если указанные виджеты не существуют, то они будут проигнорированы при выводе в сайдбаре.</p>
<p class="info">Если вы указываете несколько виджетов, то через пробел указывайте его номер.</p>

<?php

	// mso_hook('widgets_show_form');
	// pr($MSO->sidebars);
	
	$error = '';
	
	if ($MSO->sidebars)
	{ // есть сайдбары
		$form = '';
		foreach ($MSO->sidebars as $name => $sidebar)
		{
			// у сайддара уже может быть определены виджеты - считываем их из опций
			// потому что мы их будем там хранить
			// это простой массив с именами виджетов
			$options = mso_get_option('sidebars-' . mso_slug($name), 'sidebars', array());
			$options = implode("\n", $options); // разделим по строкам 
			
			// $form .= '<h2>' . $sidebar['title'] . ' (' . $name . '):</h2>';
			$form .= '<h2>' . $sidebar['title'] . ':</h2>';
			$form .= '<textarea name="f_sidebars[' . $name . ']" rows="7" style="width: 99%;">';
			$form .= htmlspecialchars($options);
			$form .= '</textarea>';
		}
	}
	else 
	{
		$error .= '<div class="error">Сайдбары не определены. Обычно они регистрируются в файле <b>functions.php</b> вашего шаблона. Например: <br /><b>mso_register_sidebar(\'1\', \'Первый сайдбар\');</b></div>';
	}
	
	if ($MSO->widgets)
	{ // есть виджеты
		$form .= '<br /><br /><h2>Доступные виджеты (добавляйте только функцию/выделено полужирным)</h2><ul class="widgets-allow">';
		foreach ($MSO->widgets as $function => $title)
		{
			$form .= '<li><b>' . $function . '</b> (' . $title . ')</li>';
		}
		$form .= '</ul>';
	}
	else 
	{
		$error .= '<div class="error">К сожалению у вас нет доступных виджетов. Обычно они определяются в плагинах.</div>';
	}
	
	if (!$error)
	{
		// добавляем форму, а также текущую сессию
		echo '<form action="" method="post">' . mso_form_session('f_session_id');
		echo $form;
		echo '<input type="submit" name="f_submit" value="Сохранить изменения" style="margin: 25px 0 5px 0;" />';
		echo '</form>';
	}
	else
	{
		echo $error;
	}

?>