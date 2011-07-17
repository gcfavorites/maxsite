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
	
	$all = mso_cat_array('page', 0, 'category_menu_order', 'asc', 'category_menu_order', 'asc', false, false, false, false, false, false);
	//	<input title="Описание" name="f_category_desc[[ID]]" value="[DESCR]" maxlength="500" size="50" style="width: 250px;" type="text">

	$format = '
	
	<table class="page cats">
	
	<colgroup style="width: 30px">
	<colgroup style="width: 50px">
	<colgroup style="width: 200px">
	<colgroup>
	<colgroup style="width: 150px">
	<colgroup style="width: 50px">
	<colgroup style="width: 80px">
	<colgroup style="width: 80px">
	
	<tr>
	
	<td class="alt"><strong title="' 
	. t('Номер рубрики. Записей в этой рубрике: [COUNT]', 'admin')
	. '">[ID]</strong><sub>[COUNT]</sub></td>
	
	<td><input title="' . t('Номер родителя', 'admin') 
	. '" name="f_category_id_parent[[ID]]" value="[ID_PARENT]" maxlength="50" type="text"></td>
	
	<td><input title="' . t('Название', 'admin') . '" name="f_category_name[[ID]]" value="[TITLE_HTML]" maxlength="500" type="text"></td>
	
	<td><textarea title="' . t('Описание', 'admin') . '" name="f_category_desc[[ID]]">[DESCR_HTML]</textarea></td>
	
	<td><input title="' . t('Короткая ссылка', 'admin') . '" name="f_category_slug[[ID]]" value="[SLUG]" maxlength="500" type="text"></td>
	
	<td><input title="' . t('Порядок', 'admin') . '" name="f_category_menu_order[[ID]]" value="[MENU_ORDER]" maxlength="500" type="text"></td>
	
	<td><input type="submit" name="f_edit_submit[[ID]]" value="' . t('Изменить', 'admin') . '"></td>
	
	<td><input type="submit" name="f_delete_submit[[ID]]" value="' . t('Удалить', 'admin') . '" onClick="if(confirm(\'' . t('Удалить рубрику?', 'admin') . '\')) {return true;} else {return false;}" ></td>
	
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
			'<table class="page cats">
			<colgroup style="width: 30px">
			<colgroup style="width: 50px">
			<colgroup style="width: 200px">
			<colgroup>
			<colgroup style="width: 150px">
			<colgroup style="width: 50px">
			<colgroup style="width: 80px">
			<colgroup style="width: 80px">
			<tr>
			<th>ID</th>
			<th>' . t('Род.', 'admin') . '</th>
			<th>' . t('Название', 'admin') . '</th>
			<th>' . t('Описание', 'admin') . '</th>
			<th>' . t('Ссылка', 'admin') . '</th>
			<th>' . t('Пор.', 'admin') . '</th>
			<th>&nbsp;</th>
			<th>&nbsp;</th>
			</tr></table>' ;
	
	echo $out;
	
	# строчка для добавления новой рубрики
	echo '
	<div class="item new_cat">
		<h2>' . t('Новая рубрика') . '</h2>
		<p class="input"><strong>' . t('Название', 'admin') . ' </strong><input type="text" name="f_new_name" value=""></p>
		<p class="textarea"><strong>' . t('Описание', 'admin') . ' </strong><textarea name="f_new_desc"></textarea></p>
		<p class="input"><strong>' . t('Ссылка', 'admin') . ' </strong><input type="text" name="f_new_slug" value=""></p>
		<p class="input short"><strong>' . t('Родитель', 'admin') . ' </strong><input type="text" name="f_new_parent" value=""></p>
		<p class="input short"><strong>' . t('Порядок', 'admin') . ' </strong><input type="text" name="f_new_order" value=""></p>
		<p class="input_submit"> <input type="submit" name="f_new_submit" value="' . t('Добавить новую рубрику', 'admin') . '"></p>
	</div>
	</form>';
	
# end file