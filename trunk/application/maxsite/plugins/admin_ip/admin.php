<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://maxsite.org/
 */

	global $MSO;
	
	$CI = & get_instance();
	$options_key = 'plugin_admin_ip';
	
	if ( $post = mso_check_post(array('f_session_id', 'f_submit', 'f_secret_url', 'f_ip')) )
	{
		mso_checkreferer();
		
		$options = array();
		$options['ip'] = $post['f_ip'];
		$options['secret'] = mso_slug($post['f_secret_url']);
	
		mso_add_option($options_key, $options, 'plugins');
		
		echo '<div class="update">Обновлено! Обязательно сохраните секретный адрес сейчас!</div>';
	}
	
?>
<h1>Admin IP</h1>
<p class="info">Вы можете указать IP с которых разрешен доступ в админ-панель. Если пользователь попытается войти в панель управления с другого IP, то ему будет отказано в доступе. 
<br /><br />На тот случай, если у администратора сменится IP, следует указать секретный адрес (URL), по которому можно очистить список разрешенных IP. Сохраняйте этот секретный адрес в надежном месте. В случае, если вы его забудете у вас не будет другой возможности, кроме как отключить плагин (удалить его файлы) или вручную исправить базу данных. 
<br /><br />Если секретный адрес не указан, то сбросить список будет невозможно.
<br /><br />Если список IP пуст, то доступ в админ-панель разрешен с любого IP.</p>

<?php
		
		$options = mso_get_option($options_key, 'plugins', array());
		if ( !isset($options['secret']) ) $options['secret'] = '';
		if ( !isset($options['ip']) ) $options['ip'] = '';
		
		
		$form = '<br /><h2>Укажите секретный адрес для сброса списка IP</h2>';
		$form .= '<p>Следует указывать только цифры и английские буквы. Другие символы не допустимы!</p>';
		$form .= '<p>Текущий адрес: <strong>' . getinfo('site_admin_url') . 'plugin_admin_ip/' . $options['secret'] . '</strong></p>';
		$form .= '<br /><p>' . getinfo('site_admin_url') . 'plugin_admin_ip/<input name="f_secret_url" type="text" value="' . $options['secret'] . '"></p>';		
		
		
		$form .= '<br /><h2>Укажите разрешенные IP по одному в каждой строчке</h2>';
		$form .= '<p>Ваш текущий IP: <b>' . $MSO->data['session']['ip_address'] . '</b></p>';
		$form .= '<textarea name="f_ip" rows="7" style="width: 99%;">';
		$form .= htmlspecialchars($options['ip']);
		$form .= '</textarea>';
		$form .= '<p>Будьте внимательны! Обязательно указывайте свой текущий IP!</p>';
		

		
		
		echo '<form action="" method="post">' . mso_form_session('f_session_id');
		echo $form;
		echo '<br /><input type="submit" name="f_submit" value=" Сохранить изменения " style="margin: 25px 0 5px 0;" />';
		echo '</form>';

?>