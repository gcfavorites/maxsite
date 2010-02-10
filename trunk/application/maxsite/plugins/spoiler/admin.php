<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * spoiler plugin
 * Author: (c) Sam
 * Plugin URL: http://6log.ru/spoiler
 */

	global $MSO;
	
	$CI = & get_instance();
	
	$options_key = 'plugin_spoiler';
	
	# Обновление значений
	if ( $post = mso_check_post(array('f_session_id', 'f_submit', 'f_show', 'f_hide','f_style')) )
	{
		mso_checkreferer();
		
		$options = array();
		$options['show'] = $post['f_show'];
		$options['hide'] = $post['f_hide'];
		$options['style'] = $post['f_style'];
	
		mso_add_option($options_key, $options, 'plugins');
		
		echo '<div class="update">' . t('Обновлено!', __FILE__) . '</div>';
	}
?>
<h1><?= t('Настройки плагина Spoiler', __FILE__) ?></h1>
<p class="info"><?= t('С помощью этого плагина вы можете скрывать текст под спойлер.<br />
Для использования плагина обрамите нужную ссылку в код <b>[spoiler]</b>ваш текст<b>[/spoiler]</b><br />
Также возможны такие варианты: [spoiler=показать]ваш текст[/spoiler], [spoiler=показать/спрятать]ваш текст[/spoiler],
[spoiler=/спрятать]ваш текст[/spoiler]',__FILE__) ?></p><br />
<p><?= t('Можно настроить какой текст появится при замене:',__FILE__) ?></p>
<?php
	#Вывод настроек
	$options = mso_get_option($options_key, 'plugins', array());

	if ( !isset($options['hide']) ) $options['hide'] = t('Скрыть',__FILE__);
	if ( !isset($options['show']) ) $options['show'] = t('Показать...',__FILE__);
	if ( !isset($options['style']) ) $options['style'] = '';

	$form = '';
	$form .= '<p><strong>'.t('Показать ',__FILE__).'</strong><input name="f_show" type="text" value="' . $options['show'] . '"></p>';
	$form .= '<p><strong>'.t('Спрятать ',__FILE__).'</strong><input name="f_hide" type="text" value="' . $options['hide'] . '"></p>';
	$form .= '<br /><br /><p><strong>' . t('Выбор файла стиля', __FILE__) . '</strong></p>
			<p>'.getinfo('plugins_url').'spoiler/<input name="f_style" type="text" value="' . $options['style'] . '">.css</p>';
	$form .= '<p class="info">' . t('Оставьте поле пустым, если не хотите использовать стили.
			(стандартный файл: <b>spoiler</b>)',__FILE__) . '</p>';			
		
	echo '<form action="" method="post">' . mso_form_session('f_session_id');
	echo $form;
	echo '<input type="submit" name="f_submit" value="'.t('Сохранить изменения',__FILE__).'" style="margin: 25px 0 5px 0;" />';
	echo '</form>';
?>