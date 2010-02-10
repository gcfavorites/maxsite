<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

	$CI = & get_instance();
	
	if ( $post = mso_check_post(array('f_session_id', 'f_submit', 'f_page_delete')) )
	{
		mso_checkreferer();
		
		// pr($post);
		
		$page_id = (int) $post['f_page_delete'];
		// проверим, чтобы это было число
		$page_id1 = (int) $page_id;
		if ( (string) $page_id != (string) $page_id1 ) $page_id = false; // ошибочный id
		
		if (!$page_id) // ошибка! 
		{
			echo '<div class="error">Ошибка обновления</div>';
		}
		else 
		{
			// проверим id, чтобы вообще такая страница была
			$CI->db->select('page_id');
			$CI->db->where(array('page_id'=>$page_id));
			$query = $CI->db->get('page');
			if ($query->num_rows() == 0) // нет такого
			{
				echo '<div class="error">Ошибочный номер страницы</div>';
			}
			else 
			{	// теперь можно удалять
				// при удалении страницы нужно сразу удалить её, рубрики и мета
				// потом будут еще и комментарии
				
				$CI->db->where( array('page_id'=>$page_id) );
				$CI->db->delete('cat2obj');
				
				$CI->db->where( array ('meta_id_obj' => $page_id, 'meta_table' => 'page') );
				$CI->db->delete('meta');
				
				$CI->db->where( array('page_id'=>$page_id) ); 
				$CI->db->delete('page');
				
				echo '<div class="update">Страница удалена</div>';
			}
		}
	}
	

?>
<h1>Страницы</h1>
<p class="info">Список всех страниц</p>

<?php

	$CI->load->library('table');
	$CI->load->helper('form');
	
	$tmpl = array (
				'table_open'		  => '<table class="page" border="0" width="99%">',
				'heading_row_start'	  => '<tr>',
				'heading_row_end'	  => '</tr>',
				'heading_cell_start'  => '<th style="background: #808080;">',
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

	$CI->table->set_heading('ID','Тип', 'Заголовок', 'Дата', 'Статус', 'Автор', 'Действие');
	
	# подготавливаем выборку из базы
	$CI->db->select('page_id, page_type_name, page_slug, page_title, page_date_publish, page_status, users_login, users_nik');
	$CI->db->from('page');
	$CI->db->join('users', 'users.users_id = page.page_id_autor');
	$CI->db->join('page_type', 'page_type.page_type_id = page.page_type_id');
	
	$CI->db->order_by('page_date_publish', 'desc');
	
	$query = $CI->db->get();
	
	$all_pages = array(); // сразу список всех страниц для формы удаления
	
	// если есть данные, то выводим
	if ($query->num_rows() > 0)
	{
		$this_url = $MSO->config['site_admin_url'] . 'page_edit/';
		$view_url = $MSO->config['site_url'] . 'page/';
		
		foreach ($query->result_array() as $row)
		{
			$id = $row['page_id'];
			$act = '<a href="' . $this_url . $id . '">Изменить</a>';
			
			$page_slug = $row['page_slug'];
			
			if (!$row['page_title']) $row['page_title'] = 'Нет заголовка';
			$page_title = '<a href="' . $view_url . $page_slug . '">' . htmlspecialchars( $row['page_title'] ) . '</a>';
			
			$page_type_name = $row['page_type_name'];
			$page_date_publish = $row['page_date_publish'];
			$page_status = $row['page_status'];
			$user = $row['users_login'] . ' (' . $row['users_nik'] . ')';
			
			$CI->table->add_row($id, $page_type_name, $page_title, $page_date_publish, $page_status, $user, $act);
			
			$all_pages[$id] = $id . ' - ' . $page_title . ' - ' . $page_date_publish . ' - ' . $page_status;
		}
	}
	

	echo $CI->table->generate(); // вывод подготовленной таблицы

	// добавляем форму для удаления записи
	$all_pages = form_dropdown('f_page_delete', $all_pages, -1, '');
	
	echo '<form action="" method="post">' . mso_form_session('f_session_id');
	echo '<br /><br /><h2>Удалить страницу</h2>';
	echo $all_pages;
	echo ' <input type="submit" name="f_submit" value="  Удалить  " onClick="if(confirm(\'Уверены?\')) {return true;} else {return false;}" >';
	echo '</form><br />';
	
	
?>