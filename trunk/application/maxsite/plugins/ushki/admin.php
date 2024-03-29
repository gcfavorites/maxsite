<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

?>
<h1><?= t('Ушки', 'plugins')?></h1>

<p class="info"><?= t('С помощью ушек вы можете размещать произвольный html/php код в шаблоне, виджете или прочих плагинах. Ушки удобно использовать для вывода счетчика, рекламы и т.п. Просто создайте ушку, а потом укажите её имя в виджете или с помощью кода:', 'plugins') ?></p>
<pre>
&lt;?php
	if (function_exists('ushka')) echo ushka('имя ушки');
?&gt;
</pre>
<br>
<p class="info"><?= t('Вы можете вывести произвольную ушку прямо в тексте. Данный код выведет ушку «reklama»:', 'plugins') ?></p>

<pre>
[ushka=reklama]
</pre>
<br>

<?php

	$CI = & get_instance();
	
	// ушки хранят свои данные во flat-опциях - файлы в кэше, только отдельном каталоге flat и не имеют времени
	
	$key = 'ushki';
	$type = 'ushki';
	
	// новая ушка
	if ( $post = mso_check_post(array('f_session_id', 'f_submit_new', 'f_ushka_new')) )
	{
		mso_checkreferer();
		
		if ($ushka_new = trim($post['f_ushka_new'])) 
		{
			// текущие ушки
			$ushki = mso_get_float_option($key, $type, array());
			$ushki[] = array('name' => $ushka_new, 'type' => 'html', 'text' => '' ); // добавили новую
			mso_add_float_option($key, $ushki, $type); // и в опции
			echo '<div class="update">' . t('Ушка добавлена!', 'plugins') . '</div>';
		}
		else 
			echo '<div class="error">' . t('Необходимо указать название ушки!', 'plugins') . '</div>';
	}
	
	if ( $post = mso_check_post(array('f_session_id', 'f_submit', 'f_ushka')) )
	{
		mso_checkreferer();
		
		// pr($post);
		
		$ushki = $post['f_ushka'];
		$ushki_new = array();
		
		foreach ($ushki as $us)
		{
			if (!isset($us['delete'])) $ushki_new[] = $us;
		}
		
		// pr($ushki_new);
		mso_add_float_option($key, $ushki_new, $type);

		echo '<div class="update">' . t('Обновлено!', 'plugins') . '</div>';
	}
	

		
		$ushki = mso_get_float_option($key, $type, array());
		
		/*
		[0] => 
				[name] => 'ушка 1'
				[type] => 'html'
				[text] => ''
		[1] => 
				[name] => 'ушка 2'
				[type] => 'php'
				[text] => ''				
		...
		*/
		// pr($ushki);
		
		echo '<form action="" method="post">' . mso_form_session('f_session_id') . '
		<p><strong>' . t('Новая ушка:', 'plugins') . '</strong> ' . ' <input name="f_ushka_new" type="text" value="">
		<input type="submit" name="f_submit_new" value="' . t('Добавить новую ушку', 'plugins') . '" style="margin: 25px 0 5px 0;"></p>
		</form>';
		
		$form = '';
		
		foreach ($ushki as $id => $us)
		{
			$form .= '<div class="ushki">';
			
			$sel_html = $sel_php = '';
			
			if ($us['type'] == 'php') $sel_php = ' selected="selected" ';
				else $sel_html = ' selected="selected" ';
			
			
			$form .= '<p><input name="f_ushka['.$id.'][name]" type="text" value="'. $us['name'] . '"  style="width: 400px;">
				<select style="width: 150px;" name="f_ushka[' . $id . '][type]"><option value="html"' . $sel_html . '/>TEXT/HTML</option><option value="php"' . $sel_php . '>PHP</option></select>
				<label><input name="f_ushka[' . $id . '][delete]" type="checkbox"> ' . t('Удалить', 'plugins') . '</label>
			</p>';
			
			$form .= '<p><textarea name="f_ushka[' . $id . '][text]" style="width: 99%; height: 220px;">' . htmlspecialchars($us['text']) . '</textarea>';
			
			$form .= '</div>';
		}
		
		if ($form)
		{
			echo '<h2>' . t('Ушки', 'plugins') . '</h2><form action="" method="post">' . mso_form_session('f_session_id');
			echo $form;
			echo '<input type="submit" name="f_submit" value="' . t('Сохранить изменения', 'plugins') . '" style="margin: 25px 0 5px 0;">';
			echo '</form>';
		}

?>