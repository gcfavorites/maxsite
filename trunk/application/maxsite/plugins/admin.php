<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://maxsite.org/
 */

	global $MSO;
	$CI = & get_instance();
	
	$options_key = '';
	
	if ( $post = mso_check_post(array('f_session_id', 'f_submit')) )
	{
		mso_checkreferer();
		
		$options = array();
		$options['header'] = $post['f_header'];
	
		mso_add_option($options_key, $options, 'plugins');
		echo '<div class="update">Обновлено!</div>';
	}
	
?>
<h1>Плагин </h1>
<p class="info"></p>

<?php
		$options = mso_get_option($options_key, 'plugins', array());
		if ( !isset($options['header']) ) $options['header'] = ''; 

		$form = '';
		$form .= '<h2>Настройки</h2>';
		$form .= '<p><strong>Заголовок:</strong> ' . ' <input name="f_header" type="text" value="' . $options['header'] . '"></p>';
		
		echo '<form action="" method="post">' . mso_form_session('f_session_id');
		echo $form;
		echo '<input type="submit" name="f_submit" value=" Сохранить изменения " style="margin: 25px 0 5px 0;" />';
		echo '</form>';

?>