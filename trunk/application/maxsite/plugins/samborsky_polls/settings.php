<h1>Настройки</h1>

<?php

	if( isset($_POST['sp_settings_submit']) ){
		
		// Ссылка на архив голосований
		mso_add_option('sp_archive_url',$_POST['sp_archive_url']);
	}

?>

<form method="post">
		
	<fieldset>
		<legend>Архив</legend>
		
		<table>
				<td><input type="text" name="sp_archive_url" value="<?= mso_get_option('sp_archive_url') ?>" size="100%" /></td>
		
	</fieldset>
	
	<br /><br />
	<input type="submit" name="sp_settings_submit" value="Сохранить" />	
	
</form>	
	