<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
	
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
			echo '<div class="update">Обновлено!</div>';
		}
		else
			echo '<div class="error">Ошибка обновления</div>';
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
			echo '<div class="update">Добавлено!</div>';
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
			echo '<div class="update">Удалено! ' . $result['description'] . '</div>';
		}
		else
			echo '<div class="error">Ошибка удаления ' . $result['description'] . '</div>';
	}

	
?>
	<h1>Рубрики</h1>
	<p class="info">Настройка рубрик</p>

<?php
	
	$all = mso_cat_array('page', 0);
	
	$format = '
	<strong title="Номер рубрики. В этой рубрике [COUNT] страниц">[ID]</strong> - 
	<input title="Номер родителя" name="f_category_id_parent[[ID]]" value="[ID_PARENT]" maxlength="500" size="50" style="width: 40px;" type="text" />
	<input title="Название" name="f_category_name[[ID]]" value="[TITLE]" maxlength="500" size="50" style="width: 200px;" type="text" />
	<input title="Описание" name="f_category_desc[[ID]]" value="[DESCR]" maxlength="500" size="50" style="width: 250px;" type="text" />
	<input title="Короткая ссылка" name="f_category_slug[[ID]]" value="[SLUG]" maxlength="500" size="50" style="width: 100px;" type="text" />
	<input title="Порядок" name="f_category_menu_order[[ID]]" value="[MENU_ORDER]" maxlength="500" size="50" style="width: 40px;" type="text" />
	
	<input type="submit" name="f_edit_submit[[ID]]" value="&nbsp;Изменить&nbsp;">
	<input type="submit" name="f_delete_submit[[ID]]" value="&nbsp;Удалить&nbsp;" onClick="if(confirm(\'Удалить рубрику?\')) {return true;} else {return false;}" >
	';
	
	
	$out = mso_create_list($all, 
		array(
			'childs'=>'childs', 
			'format'=>$format, 
			'format_current'=>$format, 
			'class_ul'=>'', 
			
			'class_ul_style'=>'list-style-type: none; margin: 0;', 
			'class_child_style'=>'list-style-type: none;', 
			'class_li_style'=>'padding: 2px; margin: 2px;',
			
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
		 '<pre><b>ID</b> <b>Родитель</b> <b>Название</b>                     <b>Описание</b>                            <b>Ссылка</b>          <b>Порядок</b></pre>';
	
	echo $out;
	
	# строчка для добавления новой рубрики
	echo '
	<br />
	<br /><b>Название</b> <input style="width: 250px;" type="text" name="f_new_name" value="">
	<br /><b>Описание</b> <input style="width: 250px;" type="text" name="f_new_desc" value="">
	<br /><b>Ссылка</b> <input style="width: 250px;" type="text" name="f_new_slug" value="">
	<br /><b>Родитель</b> <input style="width: 250px;" type="text" name="f_new_parent" value="">
	<br /><b>Порядок</b> <input style="width: 250px;" type="text" name="f_new_order" value="">
	<br /><br /><input type="submit" name="f_new_submit" value="Добавить новую рубрику">
	</form>';
	
?>