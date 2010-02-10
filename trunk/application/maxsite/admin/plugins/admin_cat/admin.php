<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
	
	$CI = & get_instance();
	
	# редактирование существующей рубрики
	if ( $post = mso_check_post(array('f_session_id', 'f_edit_submit', 
									'f_category_id_parent', 'f_category_name', 
									'f_category_desc', 'f_category_slug', 
									'f_category_menu_order')) )
	{
		mso_checkreferer();
		
		// получаем номер опции id из fo_edit_submit[]
		$f_id = mso_array_get_key($post['f_edit_submit']); 
		
		// подготавливаем данные для xmlrpc
		$data = array(
			'category_id' => $f_id,
			'category_id_parent' => (int) $post['f_category_id_parent'][$f_id],
			'category_name' => $post['f_category_name'][$f_id],
			'category_desc' => $post['f_category_desc'][$f_id],
			'category_slug' => $post['f_category_slug'][$f_id],
			'category_menu_order' => (int) $post['f_category_menu_order'][$f_id]
			);
		
		// выполняем запрос и получаем результат
		// $result = mso_xmlrpc_send('EditCategory', mso_xmlrpc_this($data));
		
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
		// $result = mso_xmlrpc_send('NewCategory', mso_xmlrpc_this($data));
		
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
		
		// подготавливаем данные для xmlrpc
		$data = array('category_id' => $f_id );
		
		// выполняем запрос и получаем результат
		// $result = mso_xmlrpc_send('DeleteCategory', mso_xmlrpc_this($data));
		
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
	// для вывода будем использовать html-таблицу
	$CI->load->library('table');
	
	# используем хелпер для формы
	$CI->load->helper('form');
	
	$tmpl = array (
					'table_open'		  => '<table class="page" border="0" width="99%"><colgroup width="30"><colgroup width="45"><colgroup><colgroup><colgroup width="50"><colgroup width="50"><colgroup width="180">',
					'heading_row_start'	  => '<tr>',
					'heading_row_end'	  => '</tr>',
					'heading_cell_start'  => '<th  style="background: #808080;">',
					'heading_cell_end'	  => '</th>',
					'row_start'			  => '<tr style="background: #E8E8E8;">',
					'row_end'			  => '</tr>',
					'cell_start'		  => '<td>',
					'cell_end'			  => '</td>',
					'row_alt_start'		  => '<tr style="background: #F2F2FF;">',
					'row_alt_end'		  => '</tr>',
					'cell_alt_start'	  => '<td>',
					'cell_alt_end'		  => '</td>',
					'table_close'		  => '</table>'
			  );

	$CI->table->set_template($tmpl); // шаблон таблицы
	
	// заголовки
	$CI->table->set_heading('ID','Родитель', 'Название', 'Описание', 'Ссылка', 'Порядок', 'Действие');

	// выполним sql-запрос на получение некоторых опций
	$CI->db->where(array('category_type'=>'page'));
	// $CI->db->order_by('category_id', 'asc');
	$CI->db->order_by('category_id_parent', 'asc');
	$CI->db->order_by('category_menu_order', 'asc');
	
	// получили
	$query = $CI->db->get('category');
	
	// обходим в цикле и выводим
	foreach ($query->result_array() as $row)
	{
		$id = $row['category_id'];
		
		$parent = form_input( array( 'name'=>'f_category_id_parent[' . $id . ']', 
								'value'=>$row['category_id_parent'],
								'style'=>'width:95%') );
		
		$name = form_input( array( 'name'=>'f_category_name[' . $id . ']', 
								'value'=>$row['category_name'],
								'style'=>'width:98%') );
									
		$desc = form_input( array( 'name'=>'f_category_desc[' . $id . ']', 
								'value'=>$row['category_desc'],
								'style'=>'width:98%') );
	
		$slug = form_input( array( 'name'=>'f_category_slug[' . $id . ']', 
								'value'=>$row['category_slug'],
								'style'=>'width:95%') );
									
		$order = form_input( array( 'name'=>'f_category_menu_order[' . $id . ']', 
								'value'=>$row['category_menu_order'],
								'style'=>'width:95%') );			
		
		$act = '<input type="submit" name="f_edit_submit[' . $id . ']" value="&nbsp;Изменить&nbsp;">';
		$act .= '<input type="submit" name="f_delete_submit[' . $id . ']" value="&nbsp;Удалить&nbsp;" onClick="if(confirm(\'Уверены?\')) {return true;} else {return false;}" >';

		$CI->table->add_row($id, $parent, $name, $desc, $slug, $order, $act);
	}
	
	# добавим строчку для добавления новой рубрики
	$parent = '<b>Родитель</b><br /><input style="width: 99%;" type="text" name="f_new_parent" value="">';
	$name = '<b>Название</b><br /><input style="width: 99%;" type="text" name="f_new_name" value="">';
	$desc = '<b>Описание</b><br /><input style="width: 99%;" type="text" name="f_new_desc" value="">';
	$slug = '<b>Ссылка</b><br /><input style="width: 99%;" type="text" name="f_new_slug" value="">';
	$order = '<b>Порядок</b><br /><input style="width: 99%;" type="text" name="f_new_order" value="">';
	$act = '<input type="submit" name="f_new_submit" value="&nbsp;Добавить новую рубрику&nbsp;">';
	
	$CI->table->add_row('', $parent, $name, $desc, $slug, $order, $act);

	// добавляем форму, а также текущую сессию
	echo '<form action="" method="post">' . mso_form_session('f_session_id');
	echo $CI->table->generate(); // вывод подготовленной таблицы
	echo '</form>';
	
?>