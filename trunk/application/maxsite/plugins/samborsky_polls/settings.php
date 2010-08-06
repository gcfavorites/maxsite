<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

	if( isset($_POST['sp_settings_submit']) ){

		// Это чтоб облегчить переход на новую идеологически более правильную форму — указание слагов без слешей.
		if (substr($_POST['sp_archive_url'], 0, 1) == '/') $_POST['sp_archive_url'] = substr($_POST['sp_archive_url'], 1);
		if (substr($_POST['sp_archive_url'], -1) == '/') $_POST['sp_archive_url'] = substr($_POST['sp_archive_url'], 0, strlen($_POST['sp_archive_url']) - 1);

		// Ссылка на архив голосований
		mso_add_option('sp_archive_url', $_POST['sp_archive_url'], 'plugins');

		// Показывать ли эту ссылку
		$show_archives_link = (isset($_POST['show_archives_link']) && $_POST['show_archives_link'] == 'on') ? TRUE : FALSE;
		mso_add_option('show_archives_link', $show_archives_link, 'plugins');

		// Показывать ссылку "Резульаты голосования"
		$show_results_link = (isset($_POST['show_results_link']) && $_POST['show_results_link'] == 'on') ? TRUE : FALSE;
		mso_add_option('show_results_link', $show_results_link, 'plugins');
	}

?>

<h1>Настройки</h1>

<form method="post">

	<fieldset>
		<legend>Архив</legend>

		<table cellspacing="10">
			<tr>
				<td>
					<a href="<?= getinfo('siteurl') . mso_get_option('sp_archive_url', 'plugins', 'polls-archive') ?>" target="_blank">Ссылка</a> на архив голосований (например «polls-archive»)
					<input type="text" name="sp_archive_url" value="<?= mso_get_option('sp_archive_url', 'plugins', 'polls-archive') ?>" size="100%">
				</td>
			</tr>
			<tr>
				<td>
					<input type="checkbox" name="show_archives_link" id="show_archives_link" <?= mso_get_option('show_archives_link', 'plugins', 1) ? ' checked="checked" ' : ''  ?>>
					<label for="show_archives_link">Показывать ссылку</label>
				</td>
			</tr>
		</table>

	</fieldset>

	<fieldset>
		<legend>Общие настройки</legend>

		<table cellspacing="10">
			<tr>
				<td>
					<input type="checkbox" name="show_results_link" id="show_results_link" <?= mso_get_option('show_results_link', 'plugins', 1) ? ' checked="checked" ' : ''  ?>>
					<label for="show_results_link">Показывать ссылку "Результаты голосования"</label>
				</td>
			</tr>
		</table>

	</fieldset>

	<br><br>
	<input type="submit" name="sp_settings_submit" value="Сохранить">

</form>
