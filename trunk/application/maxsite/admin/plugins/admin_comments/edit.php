<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<h1>Редактирование комментария</h1>
<p><a href="<?= $MSO->config['site_admin_url'] . 'comments' ?>">Вернуться к списку комментариев</a></p>	

<?php
	
	$CI = & get_instance();
	
	$id = mso_segment(4); // номер пользователя по сегменту url
	
	// проверим, чтобы это было число
	$id1 = (int) $id;
	if ( (string) $id != (string) $id1 ) $id = false; // ошибочный id
	
	if ($id) // есть корректный сегмент
	{
	
		# отредактировать комментарий
		if ( $post = mso_check_post(array('f_session_id', 'f_submit', 'f_comments_content', 'f_comments_date', 'f_comments_approved')) )
		{
			mso_checkreferer();
			// pr($post);
		
			$CI->db->where('comments_id', $id);
			
			$data = array(
				'comments_content' => $post['f_comments_content'],
				'comments_date' => $post['f_comments_date'],
				'comments_approved' => (int) $post['f_comments_approved']			
			);
			
			if ( isset($post['f_comments_author_name']) ) $data['comments_author_name'] = $post['f_comments_author_name'];
			
			
			if ($CI->db->update('comments', $data ) )
				echo '<div class="update">Обновлено!</div>';
			else 
				echo '<div class="error">Ошибка обновления</div>';
		}
		
		
		# вывод данных комментария
		$CI->db->select('comments.*, users.users_nik, comusers.comusers_nik, page.page_title, page.page_slug');
		$CI->db->from('comments');
		$CI->db->join('users', 'users.users_id = comments.comments_users_id', 'left');
		$CI->db->join('comusers', 'comusers.comusers_id = comments.comments_comusers_id', 'left');
		$CI->db->join('page', 'page.page_id = comments.comments_page_id', 'left');
		$CI->db->where('comments_id', $id);
		
		$query = $CI->db->get();
	
		// если есть данные, то выводим
		if ($query->num_rows() > 0)
		{
			$row = $query->row_array(); 
			echo '<form action="" method="post">' . mso_form_session('f_session_id');
			echo '<h3>Текст</h3>
				<p><textarea name="f_comments_content" cols="90" rows="10">' . htmlspecialchars($row['comments_content']) . '</textarea></p>';
			
			echo '<h3>Дата</h3>
				<p><input name="f_comments_date" type="text" value="' . htmlspecialchars($row['comments_date']) .'"></p>';
			
			if ( $row['comments_author_name'] ) 
			{
				echo '<h3>Автор</h3>
					<p><input name="f_comments_author_name" type="text" value="' . htmlspecialchars($row['comments_author_name']) .'"></p>';
			}
			
			$checked1 = $checked2 = '';
			 
			if ($row['comments_approved']) 
				$checked1 = 'checked="checked"'; 
			else
				$checked2 = 'checked="checked"'; 
			
			echo '<p>
				<input type="radio" name="f_comments_approved" value="1" ' . $checked1 . ' /> Одобрить
				<input type="radio" name="f_comments_approved" value="0" ' . $checked2 . ' /> Запретить
				</p>';
			
			echo '<br /><p><input type="submit" name="f_submit" value="   Готово   "></p>';
			echo '</form>';
			// pr($row);
		}
		else echo '<div class="error">Ошибочный комментарий</div>';
	}
	else
	{
		echo '<div class="error">Ошибочный запрос</div>'; // id - ошибочный
	}
?>