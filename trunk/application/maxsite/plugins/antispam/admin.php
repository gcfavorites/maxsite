<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://maxsite.org/
 */

	global $MSO;
	
	$CI = & get_instance();
	
	$options_key = 'plugin_antispam';
	
	if ( $post = mso_check_post(array('f_session_id', 'f_submit')) )
	{
		mso_checkreferer();
		
		$options = array();
		$options['antispam_on'] = isset( $post['f_antispam_on']) ? 1 : 0;
		$options['logging'] = isset( $post['f_logging']) ? 1 : 0;
		$options['moderation_links'] = isset( $post['f_moderation_links']) ? 1 : 0;
		$options['logging_file'] = $post['f_logging_file'];
		$options['black_ip'] = $post['f_black_ip'];
		$options['black_words'] = $post['f_black_words'];
		$options['moderation_words'] = $post['f_moderation_words'];
		$options['moderation_comusers'] = $post['f_moderation_comusers'];
	
	
		mso_add_option($options_key, $options, 'plugins');
		
		echo '<div class="update">Обновлено!</div>';
	}
	
?>
<h1>Антиспам</h1>
<p class="info">С помощью этого плагина вы можете активно бороться со спамерами. Обратите внимание, что комментарии авторов публикуются без модерации.

<?php
		
		$options = mso_get_option($options_key, 'plugins', array());
		if ( !isset($options['antispam_on']) ) $options['antispam_on'] = false; // включен ли антиспам
		if ( !isset($options['logging']) ) $options['logging'] = false; // разрешен ли логинг в файл?
		if ( !isset($options['logging_file']) ) $options['logging_file'] = 'antispam.log'; // путь к файлу логинга
		if ( !isset($options['black_ip']) ) $options['black_ip'] = ''; // черный список IP
		if ( !isset($options['black_words']) ) $options['black_words'] = ''; // черный список слов
		if ( !isset($options['moderation_words']) ) $options['moderation_words'] = ''; // список слов для модерации
		if ( !isset($options['moderation_comusers']) ) $options['moderation_comusers'] = ''; // список слов для модерации комюзеров
		if ( !isset($options['moderation_links']) ) $options['moderation_links'] = true; // модерация всех ссылок

		
		$form = '';

		$form .= '<h2>Настройки</h2>';
		
		$chk = $options['antispam_on'] ? ' checked="checked"  ' : '';
		$form .= '<p><input name="f_antispam_on" type="checkbox" ' . $chk . '> <strong>Включить антиспам</strong>';
		
		$chk = $options['logging'] ? ' checked="checked"  ' : '';
		$form .= '<p><input name="f_logging" type="checkbox" ' . $chk . '> <strong>Вести лог отловленных спамов</strong>';
		
		$chk = $options['moderation_links'] ? ' checked="checked"  ' : '';
		$form .= '<p><input name="f_moderation_links" type="checkbox" ' . $chk . '> <strong>Отправлять комментарий на модерацию, если в нем встречается, хоть одна ссылка.</strong>';
		
		$form .= '<p><strong>Файл для логов:</strong> ' . $MSO->config['uploads_dir'] . ' <input name="f_logging_file" type="text" value="' . $options['logging_file'] . '">';
		if (file_exists( $MSO->config['uploads_dir'] . $options['logging_file'] ))
			$form .= ' <a href="' . $MSO->config['uploads_url'] . $options['logging_file'] . '" target="_blank">Посмотреть</a>';
		
		
		$form .= '<br /><br /><h2>Черный список IP</h2>';
		$form .= '<p>Укажите IP, с которых недопустимы комментарии. Один IP в одной строчке.</p>';
		$form .= '<textarea name="f_black_ip" rows="7" style="width: 99%;">';
		$form .= htmlspecialchars($options['black_ip']);
		$form .= '</textarea>';
		
		$form .= '<br /><br /><h2>Черный список слов</h2>';
		$form .= '<p>Укажите слова, которые нельзя использовать в комментариях. Одно слово в одной строчке.</p>';
		$form .= '<textarea name="f_black_words" rows="7" style="width: 99%;">';
		$form .= htmlspecialchars($options['black_words']);
		$form .= '</textarea>';		
		
		$form .= '<br /><br /><h2>Слова для модерации</h2>';
		$form .= '<p>Укажите слова, которые принудительно отравляют комментарий на премодерацию. Одно слово в одной строчке. Обратите внимание, что этот список проверяется только если пройдена проверка на Черные списки.</p>';
		$form .= '<textarea name="f_moderation_words" rows="7" style="width: 99%;">';
		$form .= htmlspecialchars($options['moderation_words']);
		$form .= '</textarea>';		
		
		$form .= '<br /><br /><h2>Номера комюзеров, которые всегда попадают в модерацию</h2>';
		$form .= '<p>Укажите номера комюзеров, которые принудительно отравляют комментарий на премодерацию. Один номер в одной строчке. Обратите внимание, что этот список проверяется только если пройдена проверка на Черные списки.</p>';
		$form .= '<textarea name="f_moderation_comusers" rows="7" style="width: 99%;">';
		$form .= htmlspecialchars($options['moderation_comusers']);
		$form .= '</textarea>';	
		
		echo '<form action="" method="post">' . mso_form_session('f_session_id');
		echo $form;
		echo '<br /><input type="submit" name="f_submit" value=" Сохранить изменения " style="margin: 25px 0 5px 0;" />';
		echo '</form>';

?>