<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

mso_cur_dir_lang('admin');

?>

<h1><?= t('Редактирование комментария') ?></h1>
<p><a href="<?= $MSO->config['site_admin_url'] . 'comments' ?>"><?= t('К списку комментариев') ?></a></p>

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
				echo '<div class="update">' . t('Обновлено!') . '</div>';
			else 
				echo '<div class="error">' . t('Ошибка обновления') . '</div>';
			
			$CI->db->cache_delete_all();
		}
		
		
		# вывод данных комментария
		$CI->db->select('comments.*, users.users_nik, users.users_id, comusers.comusers_nik, page.page_title, page.page_slug, page.page_id');
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
		
		//	pr($row);
			

			
			
			if ( $row['users_nik'] )
			{
				echo '<p><strong>' . t('Автор') . '</strong>: '
				. '<a href="' . getinfo('site_admin_url') . 'users/edit/' . $row['users_id'] . '">'
				. $row['users_nik'] 
				. '</a></p>';
			}
			
			if ( $row['comusers_nik'] )
			{
				echo '<p><strong>' . t('Автор') . '</strong>: '
				. '<a href="' . getinfo('site_admin_url') . 'comusers/edit/' . $row['comments_comusers_id'] . '">'
				. $row['comusers_nik'] 
				. '</a></p>';
			}			
			
			echo '<form action="" method="post">' . mso_form_session('f_session_id');
			//echo '<h3>' . t('Текст', 'admin') . '</h3>';
			
			// хуки для текстового поля комментирования
			mso_hook('admin_comment_edit');
			mso_hook('comments_content_start');
					
			echo '<p><textarea name="f_comments_content" id="comments_content" style="width: 100%; height: 150px;">' . htmlspecialchars($row['comments_content']) . '</textarea></p>';
			
			echo '<h3>' . t('Дата') . '</h3>
				<p><input name="f_comments_date" type="text" value="' . htmlspecialchars($row['comments_date']) .'"></p>';
			
			if ( $row['comments_author_name'] ) 
			{
				echo '<h3>' . t('Автор') . '</h3>
					<p><input name="f_comments_author_name" type="text" value="' . htmlspecialchars($row['comments_author_name']) .'"></p>';
			}

			
			$checked1 = $checked2 = '';
			 
			if ($row['comments_approved']) 
				$checked1 = 'checked="checked"'; 
			else
				$checked2 = 'checked="checked"'; 
			
			echo '<p><label><input type="radio" name="f_comments_approved" value="1" ' . $checked1 . ' /> ' . t('Одобрить')
				. '</label> <label><input type="radio" name="f_comments_approved" value="0" ' . $checked2 . ' /> ' . t('Запретить')
				. '</label></p>';
			
			echo '<br /><p><input type="submit" name="f_submit" value="' . t('Готово') . '"></p>';
			echo '</form>';
			
			echo '<p><a href="' . getinfo('siteurl') . 'page/' . $row['page_slug'] . '#comment-' . $id . '">' 
				. t('Вернуться к комментарию на сайте') . '</a>'
				
				. ' | <a href="' . getinfo('site_admin_url') . 'page_edit/' . $row['page_id'] . '">' 
				. t('Редактировать запись') . '</a>'
				
				
				. '</p>';
			
			// pr($row);
		}
		else echo '<div class="error">' . t('Ошибочный комментарий') . '</div>';
	}
	else
	{
		echo '<div class="error">' . t('Ошибочный запрос') . '</div>'; // id - ошибочный
	}
?>