<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Основные функции MaxSite CMS
 * (с) http://maxsite.org/
 * Функции для комментариев
 */



# функция получения комментариев
function mso_get_comments($page_id = 0, $r = array())
{
	global $MSO;
	
	if ( !isset($r['limit']) )	$r['limit'] = false;
	if ( !isset($r['order']) )	$r['order'] = 'asc';
	if ( !isset($r['tags']) )	$r['tags'] = '<img><strong><em><i><b><u><s><font><pre><code>';
	if ( !isset($r['anonim_comments']) )	$r['anonim_comments'] = array();
	
	$r['tags'] .= '<p>';
	
	$CI = & get_instance();
	
	$CI->db->select('page.page_id, page.page_slug, page.page_title, comments.*, users.*, comusers.*');
	$CI->db->from('comments');
	$CI->db->join('users', 'users.users_id = comments.comments_users_id', 'left');
	$CI->db->join('comusers', 'comusers.comusers_id = comments.comments_comusers_id', 'left');
	$CI->db->join('page', 'page.page_id = comments.comments_page_id');
	
	$CI->db->where('page_status', 'publish');
	
	if ($page_id) $CI->db->where('page.page_id', $page_id);
	
	// если нет анономого коммента, то вводим условие на comments_approved=1 - только разрешенные
	if (!$r['anonim_comments'])
	{
		$CI->db->where('comments_approved', '1');
	}
	else // есть массив с указанными комментариям - они выводятся отдельно 
	{
		$CI->db->where('comments_approved', '0');
		$CI->db->where_in('comments_id', $r['anonim_comments']);
	}
	
	if ($r['limit']) $CI->db->limit($r['limit']);
	
	$CI->db->order_by('comments_date', $r['order']);

		
	$query = $CI->db->get();
	
	if ($query->num_rows() > 0)	
	{	
		$comments = $query->result_array();
		
		foreach ($comments as $key=>$comment)
		{
			$comments_content = $comment['comments_content'];
			
			$comments_content = mso_auto_tag($comments_content, true);
			$comments_content = mso_balance_tags($comments_content);
			$comments_content = strip_tags($comments_content, $r['tags']);
			
			$comments_content = mso_hook('comments_content', $comments_content);
			
			$comments[$key]['comments_content'] = $comments_content;
		}
	}
	else 
		$comments = array();

	return $comments;
}


# функция отправляет админу уведомление о новом комментарии
# первый парметр id, второй данные текст и т.д.
function mso_email_message_new_comment($id = 0, $data = array(), $page_title = '')
{
	$email = mso_get_option('admin_email', 'general', false); // email куда приходят уведомления
	
	if (!$email) return false;
	
	$subject = '[' . getinfo('title') . '] ' . 'Новый комментарий (' . $id . ') "' . $page_title . '"';
	
	$text = 'Новый комментарий на "' . $page_title . '"'. NR ;
	$text .= mso_get_permalink_page($data['comments_page_id'])  . '#comment-' . $id . NR . NR;
	
	$text .= 'Автор IP: ' . $data['comments_author_ip'] . NR;
	$text .= 'Referer: ' . $_SERVER['HTTP_REFERER'] . NR;
	$text .= 'Дата: ' . $data['comments_date'] . NR;
	
	if (isset($data['comments_users_id'])) $text .= 'Пользователь: ' . $data['comments_users_id'] . NR;
	elseif (isset($data['comments_comusers_id'])) $text .= 'Комюзер: ' . $data['comments_comusers_id'] . NR;
	elseif (isset($data['comments_author_name'])) $text .= 'Аноним: ' . $data['comments_author_name'] . NR;
	
	$text .= NR . 'Текст: ' . NR . $data['comments_content'] . NR;
	
	$text .= NR . 'Администрировать комментарий вы можете по ссылке: ' . NR 
			. getinfo('site_admin_url') . 'comments' . NR;
	
	return mso_mail($email, $subject, $text);
}

# функция добавляет новый коммент и выводит сообщение о результате
function mso_get_new_comment($args = array())
{
	global $MSO;
	
	if ( $post = mso_check_post(array('comments_session', 'comments_submit', 'comments_page_id', 'comments_content')) )
	{
		// mso_checkreferer(); // если нужно проверять на реферер
		
		if ( !isset($args['page_title']) )		$args['page_title'] = '';
		if ( !isset($args['css_ok']) )		$args['css_ok'] = 'comment-ok';
		if ( !isset($args['css_error']) )	$args['css_error'] = 'comment-error';
		if ( !isset($args['tags']) )		$args['tags'] = '<p><a><br><span><strong><em><i><b><u><s><font><pre><code>';
		if ( !isset($args['noword']) )		$args['noword'] = array('.com', '.ru', '.net', '.org', '.info', '.ua', '.com.ua',
																	'.com.ru', '.su', '/', 'www.', 'http', ':', '-', '"', 
																	'«', '»', '%', '<', '>', '&', '*', '+', '\'' );
		
		if (!mso_checksession($post['comments_session']) )
			return '<div class="' . $args['css_error']. '">Ошибка сессии! Обновите страницу</div>';
		
		if (!$post['comments_page_id']) return '<div class="' . $args['css_error']. '">Ошибка!</div>';
		
		
		$comments_page_id = $post['comments_page_id'];
		$id = (int) $comments_page_id;
		if ( (string) $comments_page_id != (string) $id ) $id = false; // $comments_page_id не число
		if (!$id) return '<div class="' . $args['css_error']. '">Ошибка!</div>';
		
		
		// капчу проверим
		if (!mso_hook('comments_new_captcha', true))
		{	// если этот хук возвращает false, значит капча неверная
			return '<div class="' . $args['css_error']. '">Ошибка! Неверно введены нижние символы!</div>';
		}

		if (!$post['comments_content']) return '<div class="' . $args['css_error']. '">Ошибка, нет текста!</div>';
		
		$comments_author_ip = $_SERVER['REMOTE_ADDR'];
		$comments_date = date('Y-m-d H:i:s');
		
		$comments_content = mso_hook('new_comments_content', $post['comments_content']);
		
		// провека на спам - проверем через хук new_comments_check_spam
		$comments_check_spam = mso_hook('new_comments_check_spam', $comments_content, false);
		
		// если есть спам, то возвращается что-то отличное от comments_content
		// если спама нет, то дожно вернуться false
		if ($comments_check_spam) 
			return '<div class="' . $args['css_error']. '">' . $comments_check_spam . '</div>';
			
			
		// $comments_content = strip_tags($comments_content, $args['tags']);
		// $comments_content = mso_auto_tag($comments_content);
		// $comments_content = mso_balance_tags($comments_content);
		
		$CI = & get_instance();
		
		if (is_login()) // коммент от автора
		{
			$comments_users_id = $MSO->data['session']['users_id'];
			
			$ins_data = array (
				'comments_users_id' => $comments_users_id,
				'comments_page_id' => $comments_page_id,
				'comments_author_ip' => $comments_author_ip,
				'comments_date' => $comments_date,
				'comments_content' => $comments_content,
				'comments_approved' => 1 // авторы могут сразу публиковать комменты без модерации
				);

			$res = ($CI->db->insert('comments', $ins_data)) ? '1' : '0';
			
			if ($res)
			{
				mso_email_message_new_comment($CI->db->insert_id(), $ins_data, $args['page_title']);
				mso_redirect(mso_current_url() . '#comment-' . $CI->db->insert_id());
			}
			else
				return '<div class="' . $args['css_error']. '">Ошибка добавления комментария</div>';
		}
		else
		{
			if ( isset($post['comments_reg']) ) // комюзер
			{
				if ($post['comments_reg'] == 'reg') // нужно зарегистрировать 
				{
					
					if ( !isset($post['comments_email']) or !$post['comments_email'] )
						return '<div class="' . $args['css_error']. '">Для регистрации нужно указать Email</div>';

					if ( !isset($post['comments_password']) or !$post['comments_password'] )
						return '<div class="' . $args['css_error']. '">Для регистрации нужно указать пароль</div>';
					
					$comments_email = mso_strip($post['comments_email']);
					$comments_password = mso_strip($post['comments_password']);
					
					if ( !mso_valid_email($comments_email) ) 
						return '<div class="' . $args['css_error']. '">Ошибочный Email</div>';
					
					// вначале нужно зарегистрировать comюзера - получить его id и только после этого доабвить сам коммент
					// но вначале есть смысл проверить есть ли такой ком-пользователь	
					
					$comusers_id = false;
					
					$CI->db->select('comusers_id, comusers_password');
					$CI->db->where('comusers_email', $comments_email);
					$query = $CI->db->get('comusers');
					if ($query->num_rows()) // есть такой комюзер
					{
						$row = $query->row_array(1);

						if ($row['comusers_password'] != mso_md5($comments_password)) // пароль неверный
							return '<div class="' . $args['css_error']. '">Неверный пароль</div>';
						
						$comusers_id = $row['comusers_id']; // получаем номер комюзера
					}
					else
					{
						// такого комюзера нет
						$ins_data = array (
							'comusers_email' => $comments_email,
							'comusers_password' => mso_md5($comments_password)
							);
						
						$res = ($CI->db->insert('comusers', $ins_data)) ? '1' : '0';
						
						if ($res)
						{
							$comusers_id = $CI->db->insert_id(); // номер добавленной записи
						}
						else
							return '<div class="' . $args['css_error']. '">Ошибка регистрации</div>';
					}
					
					if ($comusers_id)
					{
						// комюзер добавлен или есть
						// теперь сам коммент
						$ins_data = array (
							'comments_page_id' => $comments_page_id,
							'comments_comusers_id' => $comusers_id,
							'comments_author_ip' => $comments_author_ip,
							'comments_date' => $comments_date,
							'comments_content' => $comments_content,
							'comments_approved' => 0 // не разрешаем комюзерам сразу публиковаться
							);
						
						$res = ($CI->db->insert('comments', $ins_data)) ? '1' : '0';
						if ($res)
						{
							// посколько у нас идет редирект, то данные об отправленном комменте
							// сохраняем в сессии номер комментария
							if ( isset($MSO->data['session']) )
							{
								$CI->session->set_userdata(array( 'comments' => 
													array(
													// $CI->db->insert_id()=>$comments_page_id
													$CI->db->insert_id()
													)));
							}
							mso_email_message_new_comment($CI->db->insert_id(), $ins_data, $args['page_title']);
							mso_redirect(mso_current_url() . '#comment-' . $CI->db->insert_id());
						}
						else
							return '<div class="' . $args['css_error']. '">Ошибка добавления комментария</div>';
					}
				}
				elseif  ($post['comments_reg'] == 'noreg')
				{
					// комментарий от анонима
					
					if ( isset($post['comments_author']) ) 
					{
						$comments_author_name = mso_strip($post['comments_author']);
						$comments_author_name = str_replace($args['noword'], '', $comments_author_name);
						$comments_author_name = trim($comments_author_name);
						if (!$comments_author_name) $comments_author_name = 'Аноним';
					}
					else $comments_author_name = 'Аноним';
					
					// можно ли публиковать без модерации?
					$comments_approved = mso_get_option('new_comment_anonim_moderate', 'general', 1);
					// но у нас в базе хранится значение наоборот - 1 разрешить 0 - запретить
					$comments_approved = !$comments_approved;					
					
					$ins_data = array (
						'comments_page_id' => $comments_page_id,
						'comments_author_name' => $comments_author_name,
						'comments_author_ip' => $comments_author_ip,
						'comments_date' => $comments_date,
						'comments_content' => $comments_content,
						'comments_approved' => $comments_approved
						);

					$res = ($CI->db->insert('comments', $ins_data)) ? '1' : '0';
					
					if ($res)
					{
						// посколько у нас идет редирект, то данные об отправленном комменте
						// сохраняем в сессии номер комментария
						if ( isset($MSO->data['session']) )
						{
							$CI->session->set_userdata(array( 'comments' => 
												array(
							 					// $CI->db->insert_id()=>$comments_page_id
							 					$CI->db->insert_id()
							 					)));
						}
						mso_email_message_new_comment($CI->db->insert_id(), $ins_data, $args['page_title']);
						mso_redirect(mso_current_url() . '#comment-' . $CI->db->insert_id());
					}
					else
						return '<div class="' . $args['css_error']. '">Ошибка добавления комментария</div>';
				}
			}
		}
	}
	// else return '<div class="comment-new">Комментарий добавлен и возможно ожидает модерации.</div>';
}



?>