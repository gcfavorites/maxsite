<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

	global $MSO;
	mso_cur_dir_lang(__FILE__);

	$CI = & get_instance();

	$options_key = 'plugin_admin_announce';

	if ( $post = mso_check_post(array('f_session_id', 'f_submit')) )
	{
		mso_checkreferer();

		$options = array();
		$options['admin_announce']  = isset( $post['f_admin_announce'])  ? $post['f_admin_announce']      : '';
		$options['delta']           = isset( $post['f_delta'])           ? (int)$post['f_admin_announce'] : 10;
		$options['admin_statistic'] = isset( $post['f_admin_statistic']) ? 1 : 0;
		$options['admin_showall']   = isset( $post['f_admin_showall'])   ? 1 : 0;

		mso_add_float_option($options_key, $options, 'plugins');

		echo '<div class="update">' . t('Обновлено!', 'plugins') . '</div>';
	}

?>
<h1><?= t('Админ-анонс') ?></h1>
<p class="info"><?= t('Позволяет на стартовой странице админки размещать… что-то.') ?></p>

<?php

		$options = mso_get_float_option($options_key, 'plugins', array());
		if ( !isset($options['admin_announce']) )  $options['admin_announce']  = '';
		if ( !isset($options['admin_statistic']) ) $options['admin_statistic'] = true;
		if ( !isset($options['admin_showall']) )   $options['admin_showall']   = true;
		if ( !isset($options['delta']) or ($options['delta'] == 0) ) $options['delta'] = 10;

		$form = '';

		$form .= '<h2>' . t('Настройки', 'plugins') . '</h2>';

		$chk = $options['admin_statistic'] ? ' checked="checked"  ' : '';
		$form .= '<p><label><input name="f_admin_statistic" type="checkbox" ' . $chk . '> <strong>' . t('Показывать на стартовой странице админки статистику') . '</strong></label></p>';

		$chk = $options['admin_showall'] ? ' checked="checked"  ' : '';
		$form .= '<p><label><input name="f_admin_showall" type="checkbox" ' . $chk . '> <strong>' . t('Показывать статистику всем') . '</strong></label><br />';
		$form .= t('Если не отмечено, то показывается только для тех, кому разрешено редактировать «Админ-анонс»'). '</p>';

		$form .= '<br /><br /><p><input name="f_delta" type="text" value="' . $options['delta'] . '" /> <strong>' . t('Приблизительность максимальных и минимальных страниц.') . '</strong><br>';
		$form .= t('Насколько близко по количеству просмотров к минимуму и максимуму должны быть страницы в отчёте.');

		$form .= '<br /><br /><h2>' . t('Текст на стартовой странице') . '</h2>';
		$form .= '<p>' . t('Введите текст (с html-оформлением), который должен быть на стартовой странице админки.') . '</p>';
		$form .= '<textarea name="f_admin_announce" rows="7" style="width: 99%;">';
		$form .= htmlspecialchars($options['admin_announce']);
		$form .= '</textarea>';

		echo '<form action="" method="post">' . mso_form_session('f_session_id');
		echo $form;
		echo '<br /><input type="submit" name="f_submit" value="' . t('Сохранить изменения', 'plugins') . '" style="margin: 25px 0 5px 0;" />';
		echo '</form>';

?>