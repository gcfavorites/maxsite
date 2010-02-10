<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

	$options_key = 'redirect';
	
	if ( $post = mso_check_post(array('f_session_id', 'f_submit', 'f_all')) )
	{
		mso_checkreferer();
		
		$options = array();
		$options['all'] = $post['f_all'];
		
		mso_add_option($options_key, $options, 'plugins');
		echo '<div class="update">' . t('Обновлено!', 'plugins') . '</div>';
	}
	
?>
<h1><?= t('Редиректы', 'plugins') ?></h1>
<p class="info"><?= t('С помощью этого плагина вы можете организовать редиректы со своего сайта. Укажите исходный и конечный адрес через «|», например:', 'plugins') ?></p>
<pre>http://mysite.com/about | http://newsite.com/hello</pre>
<p class="info"><?= t('При переходе к странице вашего сайта «http://mysite.com/about» будет осуществлен автоматический редирект на указанный «http://newsite.com/hello».', 'plugins') ?></p>

<?php

		$options = mso_get_option($options_key, 'plugins', array());
		if ( !isset($options['all']) ) $options['all'] = '';

		echo '<form action="" method="post">' . mso_form_session('f_session_id');
		echo '<textarea name="f_all" style="width: 650px; height: 300px;">' .  $options['all'] . '</textarea>';
		echo '<br><input type="submit" name="f_submit" value="' . t('Сохранить изменения', 'plugins') . '" style="margin: 25px 0 5px 0;">';
		echo '</form>';

?>