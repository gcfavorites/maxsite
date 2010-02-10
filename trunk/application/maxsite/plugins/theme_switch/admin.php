<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

	global $MSO;
	$CI = & get_instance();
	
	$CI->load->helper('directory');
	
	$options_key = 'theme_switch';
	
	$templates_dir = $MSO->config['templates_dir'];
	
	if ( $post = mso_check_post(array('f_session_id', 'f_submit', 'f_templates')) )
	{
		mso_checkreferer();
		
		$options = array();
		$options['templates'] = $post['f_templates'];
		
		// переделаем массив в [default] => Default из info.php [name]
		foreach ($options['templates'] as $dir=>$val)
		{
			if (file_exists( $templates_dir . $dir . '/info.php' ))
			{
				require($templates_dir . $dir . '/info.php');
				$options['templates'][$dir] = $info['name'];
			}
		}
		// pr($options['templates']);
		mso_add_option($options_key, $options, 'plugins');
		echo '<div class="update">' . t('Обновлено!', 'plugins') . '</div>';
	}
	
?>
<h1><?= t('Theme switch', 'plugins') ?></h1>
<p class="info"><?= t('Плагин позволяет переключать шаблоны сайта вашим посетителям. Отметьте те шаблоны, которые могут переключаться. Форма переключения настраивается в виджетах.', 'plugins') ?></p>

<?php
		$options = mso_get_option($options_key, 'plugins', array());
		if ( !isset($options['templates']) ) $options['templates'] = array(); 

		// получаем все шаблоны на диске
		// выводим их списком с чекбоксами
		// в опциях сохраняем только те, которые отмечены
		
		$dirs = directory_map($templates_dir, true);
		
		$form = '';

		foreach ($dirs as $dir)
		{
			// обязательный файл index.php
			if (file_exists( $templates_dir . $dir . '/index.php' ))
			{
				if (isset($options['templates'][$dir])) $checked = 'checked="checked"';
					else $checked = '';
				
				if (file_exists( $templates_dir . $dir . '/info.php' ))
				{
					require($templates_dir . $dir . '/info.php');
					$iname = $info['name'];
				}
				else $iname = 'not info.php!';
				
				$form .= '<input type="checkbox" name="f_templates[' . $dir . ']" id="f_templates_' . $dir . '" ' . $checked . '> <label for="f_templates_' . $dir . '">' . $iname . ' (' . $dir . ')</label><br>';
			}
		}

		echo '<form action="" method="post">' . mso_form_session('f_session_id') . '<p>';
		echo $form;
		echo '</p><input type="submit" name="f_submit" value="' . t('Сохранить изменения', 'plugins') . '" style="margin: 25px 0 5px 0;">';
		echo '</form>';

?>