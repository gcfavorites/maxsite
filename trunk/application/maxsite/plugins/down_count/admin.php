<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://maxsite.org/
 */

	global $MSO;
	
	$CI = & get_instance();
	
	$options_key = 'plugin_down_count';
	
	if ( $post = mso_check_post(array('f_session_id', 'f_submit', 'f_file', 'f_prefix', 'f_format')) )
	{
		mso_checkreferer();
		
		$options = array();
		$options['file'] = $post['f_file'];
		$options['prefix'] = $post['f_prefix'];
		$options['format'] = $post['f_format'];
		$options['referer'] = isset( $post['f_referer']) ? 1 : 0;
	
		mso_add_option($options_key, $options, 'plugins');
		
		echo '<div class="update">Обновлено!</div>';
	}
	
?>
<h1>Счетчик переходов</h1>
<p class="info">С помощью этого плагина вы можете подсчитывать количество скачиваний или переходв по ссылке. Для использования плагина обрамите нужную ссылку в код [dc]ваша ссылка[/dc]</p>

<?php
		
		$options = mso_get_option($options_key, 'plugins', array());
		if ( !isset($options['file']) ) $options['file'] = 'dc.dat'; // путь к файлу данных
		if ( !isset($options['prefix']) ) $options['prefix'] = 'dc'; // префикса
		if ( !isset($options['format']) ) $options['format'] = ' <sup title="Количество переходов">%COUNT%</sup>'; // формат количества
		if ( !isset($options['referer']) ) $options['referer'] = 1; // запретить скачку с чужого сайта

		$form = '';

		$form .= '<h2>Настройки</h2>';
		
		$form .= '<p><strong>Файл для хранения количества скачиваний:</strong><br />' . $MSO->config['uploads_dir'] . ' <input name="f_file" type="text" value="' . $options['file'] . '"></p>';
			
		$form .= '<p><strong>Префикс URL:</strong> ' . getinfo('siteurl') . ' <input name="f_prefix" type="text" value="' . $options['prefix'] . '">/ссылка</p>';
		
		$form .= '<p><strong>Формат количества переходов:</strong> <input name="f_format" style="width: 400px;" type="text" value="' . htmlspecialchars($options['format']) . '"></p>';
		
		
		$chk = $options['referer'] ? ' checked="checked"  ' : '';
		$form .= '<p><input name="f_referer" type="checkbox" ' . $chk . '> <strong>Запретить переходы с чужих сайтов</strong></p>';
		
		echo '<form action="" method="post">' . mso_form_session('f_session_id');
		echo $form;
		echo '<input type="submit" name="f_submit" value=" Сохранить изменения " style="margin: 25px 0 5px 0;" />';
		echo '</form>';
		
		// выведем ниже формы всю статистику
		$fn = $MSO->config['uploads_dir'] . $options['file'];
		
		$CI = & get_instance();
		$CI->load->helper('file'); // хелпер для работы с файлами
		
		if (file_exists( $fn )) // файла нет, нужно его создать
		{
			// массив данных: url => array ( count=>77 )
			$data = unserialize( read_file($fn) ); // поулчим из файла
			
			if ($data)
			{
				echo '<br /><h2>Статистика переходов</h2>';
				echo '<ul>';
				foreach($data as $url => $aaa)
				{
					echo '<li><strong>' . $url . '</strong> - переходов: ' . $data[$url]['count'] . '</li>' . NR;
				}
				echo '</ul>';
			}
			// pr($data);
		}

?>