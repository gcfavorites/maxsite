<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
	
	mso_cur_dir_lang('admin');
	
	$CI = & get_instance();
	
	require_once( getinfo('common_dir') . 'category.php' ); // функции рубрик 
	
	# редактирование существующей рубрики
	if ( $post = mso_check_post(array('f_session_id', 'f_edit_submit', 
									'f_category_id_parent', 'f_category_name', 
									'f_category_desc', 'f_category_slug', 
									'f_category_menu_order')) )
	{
		mso_checkreferer();
		
		// получаем номер опции id из fo_edit_submit[]
		$f_id = mso_array_get_key($post['f_edit_submit']); 
		
		// подготавливаем данные
		$data = array(
			'category_id' => $f_id,
			'category_id_parent' => (int) $post['f_category_id_parent'][$f_id],
			'category_name' => $post['f_category_name'][$f_id],
			'category_desc' => $post['f_category_desc'][$f_id],
			'category_slug' => $post['f_category_slug'][$f_id],
			'category_menu_order' => (int) $post['f_category_menu_order'][$f_id]
			);
		
		// выполняем запрос и получаем результат
		require_once( getinfo('common_dir') . 'functions-edit.php' ); // функции редактирования
		
		$result = mso_edit_category($data);
		
		if (isset($result['result']) and $result['result']) 
		{
			mso_flush_cache(); // сбросим кэш
			echo '<div class="update">' . t('Обновлено!', 'admin') . '</div>';
		}
		else
			echo '<div class="error">' . t('Ошибка обновления', 'admin') . '</div>';
	}
	
	# добавление новой рубрики
	if ( $post = mso_check_post(array('f_session_id', 'f_new_submit', 
									'f_new_parent', 'f_new_name', 
									'f_new_desc', 'f_new_slug', 
									'f_new_order')) )
	{
		mso_checkreferer();

		// подготавливаем данные для xmlrpc
		$data = array(
			'category_id_parent' => (int) $post['f_new_parent'],
			'category_name' => $post['f_new_name'],
			'category_desc' => $post['f_new_desc'],
			'category_slug' => $post['f_new_slug'],
			'category_menu_order' => (int) $post['f_new_order']
			);
		
		// выполняем запрос и получаем результат

		require_once( getinfo('common_dir') . 'functions-edit.php' ); // функции редактирования
		
		$result = mso_new_category($data);
		
		if (isset($result['result']) and $result['result']) 
		{
			mso_flush_cache(); // сбросим кэш
			echo '<div class="update">' . t('Добавлено!', 'admin') . '</div>';
		}
		else
			echo '<div class="error">Ошибка добавления! ' . $result['description'] . ' </div>';
	}
	
	# удаление существующей рубрики
	if ( $post = mso_check_post(array('f_session_id', 'f_delete_submit')) )
	{
		mso_checkreferer();
		
		// получаем номер опции id из fo_edit_submit[]
		$f_id = mso_array_get_key($post['f_delete_submit']); 
		
		// подготавливаем данные
		$data = array('category_id' => $f_id );
		
		require_once( getinfo('common_dir') . 'functions-edit.php' ); // функции редактирования
		
		$result = mso_delete_category($data);
		
		if (isset($result['result']) and $result['result']) 
		{	
			mso_flush_cache(); // сбросим кэш
			echo '<div class="update">' . t('Удалено!', 'admin') . ' ' . $result['description'] . '</div>';
		}
		else
			echo '<div class="error">' . t('Ошибка удаления', 'admin') . ' ' . $result['description'] . '</div>';
	}

	
?>
	<h1><?= t('Рубрики', 'admin') ?></h1>
	<p class="info"><?= t('Настройка рубрик', 'admin') ?></p>

<?php
	
	$all = mso_cat_array('page', 0);
	//	<input title="Описание" name="f_category_desc[[ID]]" value="[DESCR]" maxlength="500" size="50" style="width: 250px;" type="text">

	$format = '
	
	<table class="page" style="width: 100%; border-collapse: collapse;">
	
	<colgroup style="width: 30px; padding: 0 4px;">
	<colgroup style="width: 50px; padding: 0 4px;">
	<colgroup style="width: 200px; padding: 0 4px;">
	<colgroup style="padding: 0 4px;">
	<colgroup style="width: 150px; padding: 0 4px;">
	<colgroup style="width: 50px; padding: 0 4px;">
	<colgroup style="width: 80px; padding: 0 4px;">
	<colgroup style="width: 80px; padding: 0 4px;">
	
	<tr style="text-align: center; vertical-align: top;">
	
	<td class="alt"><strong title="' 
	. t('Номер рубрики. В этой рубрике [COUNT] страниц', 'admin')
	. '">[ID]</strong><sub style="color: gray">[COUNT]</sub></td>
	
	<td><input title="' . t('Номер родителя', 'admin') 
	. '" name="f_category_id_parent[[ID]]" value="[ID_PARENT]" maxlength="50" style="width: 100%; margin: 0 -4px;" type="text"></td>
	
	<td><input title="' . t('Название', 'admin') . '" name="f_category_name[[ID]]" value="[TITLE]" maxlength="500" style="width: 100%; margin: 0 -4px;" type="text"></td>
	
	<td><textarea title="' . t('Описание', 'admin') . '" name="f_category_desc[[ID]]" style="width: 100%; margin: 0 -4px;">[DESCR]</textarea></td>
	
	<td><input title="' . t('Короткая ссылка', 'admin') . '" name="f_category_slug[[ID]]" value="[SLUG]" maxlength="500" style="width: 100%; margin: 0 -4px;" type="text"></td>
	
	<td><input title="' . t('Порядок', 'admin') . '" name="f_category_menu_order[[ID]]" value="[MENU_ORDER]" maxlength="500" style="width: 100%; margin: 0 -4px;" type="text"></td>
	
	<td><input type="submit" name="f_edit_submit[[ID]]" value="' . t('Изменить', 'admin') . '" style="width: 100%; margin: 0 -2px;"></td>
	
	<td><input type="submit" name="f_delete_submit[[ID]]" value="' . t('Удалить', 'admin') . '" style="width: 100%; margin: 0 -2px;" onClick="if(confirm(\'' . t('Удалить рубрику?', 'admin') . '\')) {return true;} else {return false;}" ></td>
	
	</tr></table>
	
	';
	
	// pr($all);
	
	$out = mso_create_list($all, 
		array(
			'childs'=>'childs', 
			'format'=>$format, 
			'format_current'=>$format, 
			'class_ul'=>'', 
			
			'class_ul_style'=>'list-style-type: none; margin: 0;', 
			'class_child_style'=>'list-style-type: none;', 
			'class_li_style'=>'margin: 5px 0;',
			
			'title'=>'category_name', 
			'link'=>'category_slug', 
			'current_id'=>false, 
			'prefix'=>'category/', 
			'count'=>'pages_count', 
			'id'=>'category_id', 
			'slug'=>'category_slug', 
			'menu_order'=>'category_menu_order', 
			'id_parent'=>'category_id_parent'
			) );
	
	// добавляем форму, а также текущую сессию
	echo '<form action="" method="post">' . mso_form_session('f_session_id') .
			'<table class="page" style="width: 100%; border-collapse: collapse;">
			<colgroup style="width: 30px; padding: 0 4px;">
			<colgroup style="width: 50px; padding: 0 4px;">
			<colgroup style="width: 200px; padding: 0 4px;">
			<colgroup style="padding: 0 4px;">
			<colgroup style="width: 150px; padding: 0 4px;">
			<colgroup style="width: 50px; padding: 0 4px;">
			<colgroup style="width: 80px; padding: 0 4px;">
			<colgroup style="width: 80px; padding: 0 4px;">
			<tr style="vertical-align: top; font-weight: bold;">
			<td>ID</td>
			<td>' . t('Род.', 'admin') . '</td>
			<td>' . t('Название', 'admin') . '</td>
			<td>' . t('Описание', 'admin') . '</td>
			<td>' . t('Ссылка', 'admin') . '</td>
			<td>' . t('Пор.', 'admin') . '</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			</tr></table>' ;
	
	echo $out;
	
	# строчка для добавления новой рубрики
	echo '
	<br>
	<br><b>' . t('Название', 'admin') . '</b> <input style="width: 250px;" type="text" name="f_new_name" value="">
	
	<br><b>' . t('Описание', 'admin') . '</b><br><textarea style="width: 350px;" name="f_new_desc"></textarea>
	
	<br><b>' . t('Ссылка', 'admin') . '</b> <input style="width: 250px;" type="text" name="f_new_slug" value="">
	<br><b>' . t('Родитель', 'admin') . '</b> <input style="width: 250px;" type="text" name="f_new_parent" value="">
	<br><b>' . t('Порядок', 'admin') . '</b> <input style="width: 250px;" type="text" name="f_new_order" value="">
	<br><br><input type="submit" name="f_new_submit" value="' . t('Добавить новую рубрику', 'admin') . '">
	</form>';
	
?>