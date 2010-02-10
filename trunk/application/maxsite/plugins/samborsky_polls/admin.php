<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

	$plugin_url = $MSO->config['site_url'] . 'admin/samborsky_polls/';

?>

<!-- jQuery UI (DatePicker) -->
<script type="text/javascript" src="<?= getinfo('plugins_url') ?>samborsky_polls/js/jquery-ui.min.js"></script>
<link href="<?= getinfo('plugins_url') ?>samborsky_polls/css/jquery-ui.css" rel="stylesheet" type="text/css" media="screen" />	

<!-- Стили админки -->
<link rel="stylesheet" href="<?= getinfo('plugins_url') ?>samborsky_polls/css/style_admin.css" type="text/css" media="screen" charset="utf-8" />

<!-- jQuery плагин для перемещения строк в таблице -->
<script src="<?= getinfo('plugins_url') ?>samborsky_polls/js/jquery.tablednd.js"></script>

<!-- JS скрипт админки -->
<script src="<?= getinfo('plugins_url') ?>samborsky_polls/js/admin.js"></script>

<div class="admin-h-menu">
	<a href="<?= $plugin_url ?>" class="select">Управление голосованиями</a>&nbsp;|&nbsp;
	<a href="<?= $plugin_url ?>manage" class="select">Добавить новое</a>&nbsp;|&nbsp;
	<a href="<?= $plugin_url ?>settings" class="select">Настройки</a>
</div>

<?php

	$seg = mso_segment(3);
	
	if( empty($seg) ){
		require($MSO->config['plugins_dir'] . 'samborsky_polls/list.php');
	}
	else if( $seg == 'manage' ){
		require($MSO->config['plugins_dir'] . 'samborsky_polls/manage.php');
	}
	else if( $seg == 'list' ){
		require($MSO->config['plugins_dir'] . 'samborsky_polls/list.php');
	}
	else if( $seg == 'logs' ){
		require($MSO->config['plugins_dir'] . 'samborsky_polls/logs.php');
	}
	else if( $seg == 'settings' ){
		require($MSO->config['plugins_dir'] . 'samborsky_polls/settings.php');
	}

	
?>