<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

	$CI = & get_instance();
	
	$f_all_comments = false; // только неразрешенные комментарии
	
	# показать все комментарии
	if ( $post = mso_check_post(array('f_session_id', 'f_all_comments')) )
	{
		mso_checkreferer();
		$f_all_comments = true; 
	}
	
	
	# показать только требующие модерации
	if ( $post = mso_check_post(array('f_session_id', 'f_moderation_comments')) )
	{
		mso_checkreferer();
		$f_all_comments = false; 
	}
	
	
	# разрешить или запретить
	if ( ( $post = mso_check_post(array('f_session_id', 'f_check_comments')) ) and 
		( isset($_POST['f_aproved_submit']) or isset($_POST['f_unaproved_submit']) ) )
		
	{
		mso_checkreferer();

		$action = '0'; // запретить по-умолчанию
		if (isset($post['f_aproved_submit'])) $action = '1'; // разрешить
		
		$f_check_comments = $post['f_check_comments']; // номера отмеченных
		
		// на всякий случай пройдемся по массиву и составим массив из ID
		$arr_ids = array(); // список всех где ON
		foreach ($f_check_comments as $id_com=>$val)
			if ($val) $arr_ids[] = $id_com;
		
		$CI->db->where_in('comments_id', $arr_ids);
		if ($CI->db->update('comments', array('comments_approved'=>$action) ) )
		{
			mso_flush_cache();
			echo '<div class="update">Обновлено!</div>';
		}
		else 
			echo '<div class="error">Ошибка обновления</div>';
	}
	
	
	# удалить комментарий
	if ( $post = mso_check_post(array('f_session_id', 'f_delete_submit', 'f_check_comments')) )
	{
		mso_checkreferer();
		// pr($post);
		
		$f_check_comments = $post['f_check_comments']; // номера отмеченных
		
		// на всякий случай пройдемся по массиву и составим массив из ID
		$arr_ids = array(); // список всех где ON
		foreach ($f_check_comments as $id_com=>$val)
			if ($val) $arr_ids[] = $id_com;
		
		$CI->db->where_in('comments_id', $arr_ids);
		
		if ( $CI->db->delete('comments') )
		{
			mso_flush_cache();
			echo '<div class="update">Удалено!</div>';
		}
		else 
			echo '<div class="error">Ошибка удаления</div>';
	}
	

?>
<h1>Комментарии</h1>
<p class="info">Список последних 20 комментариев.</p>

<?php
	//pr($MSO);
	$CI->load->library('table');
	
	$tmpl = array (
				'table_open'		  => '<table class="page" border="0" width="99%">',
				'row_alt_start'		  => '<tr class="alt">',
				'cell_alt_start'	  => '<td class="alt">',
		  );
		  
	$CI->table->set_template($tmpl); // шаблон таблицы

	$CI->table->set_heading('ID', '', '+', 'Текст',  'Действие');
	
	# подготавливаем выборку из базы
	$CI->db->select('comments_id, comments_users_id, comments_comusers_id, comments_author_name, comments_date, comments_content, comments_approved, comments_author_ip, users.users_nik, comusers.comusers_nik, page.page_title, page.page_slug');
	$CI->db->from('comments');
	$CI->db->join('users', 'users.users_id = comments.comments_users_id', 'left');
	$CI->db->join('comusers', 'comusers.comusers_id = comments.comments_comusers_id', 'left');
	$CI->db->join('page', 'page.page_id = comments.comments_page_id', 'left');
	
	if (!$f_all_comments) $CI->db->where('comments_approved', 0);

	$CI->db->order_by('comments_date', 'desc');
	
	$CI->db->limit(20); // не более 20
	
	$query = $CI->db->get();
	
	// если есть данные, то выводим
	if ($query->num_rows() > 0)
	{
		$this_url = $MSO->config['site_admin_url'] . 'comments/';
		$view_url = $MSO->config['site_url'] . 'page/';
		
		foreach ($query->result_array() as $row)
		{
			$id = $row['comments_id'];
			
			// для вывода делаем чекбокс + hidden всех комментов для того, чтобы проверить тех,
			// которые окажутся не отмечены - их POST не передает
			$id_out = '<input type="checkbox" name="f_check_comments[' . $id . ']">' . NR;
			
			$act = '<a href="' . $this_url . 'edit/'. $id . '">Изменить</a>';
			
			$comments_date = $row['comments_date'];
			
			$author = '';
			if ( $row['comments_users_id'] ) $author = '<span style="color: grey">' . $row['users_nik'] . '</span>';
			elseif ($row['comments_comusers_id']) $author = $row['comusers_nik'] . ' (комюзер)';
			else $author = $row['comments_author_name'] . ' (анонимно)';
			
			$page_slug = $row['page_slug'];
			$page_title = '<a target="_blank" href="' . $view_url . $page_slug . '#comment-' . $id . '">' . htmlspecialchars( $row['page_title'] ) . '</a>';
			
			$comments_content = htmlspecialchars($row['comments_content']);
			$comments_content = str_replace('&lt;p&gt;', '<br />', $comments_content);
			$comments_content = str_replace('&lt;/p&gt;', '', $comments_content);
			$comments_content = str_replace('&lt;br /&gt;', '<br />', $comments_content);
			
			
			if ( $row['comments_approved'] > 0 ) $comments_approved = '+';
				else $comments_approved = '-';
				
			$out = '<strong><i>' . $author . '</i> написал в «' . $page_title . '»</strong> (' . $comments_date. ') ip: ' . $row['comments_author_ip'] . '<p>' . $comments_content . '</p>' . NR;
						
			
			$CI->table->add_row($id, $id_out, $comments_approved, $out, $act);
		}
	}
	

	echo '<form action="" method="post">' . mso_form_session('f_session_id');
	echo '
		<p>Показать <input type="submit" name="f_all_comments" value="   Все   ">
		<input type="submit" name="f_moderation_comments" value="   Только требующие модерации   ">
		';
	echo $CI->table->generate();
	echo '
		<br /><br />C отмеченными:
		<input type="submit" name="f_aproved_submit" value="   Разрешить   ">
		<input type="submit" name="f_unaproved_submit" value="   Запретить   ">
		<input type="submit" name="f_delete_submit" onClick="if(confirm(\'Уверены?\')) {return true;} else {return false;}" value="   Удалить   ">
		';
	echo '</form>';
	
?>