<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Основные функции MaxSite CMS
 * (c) http://maxsite.org/
 * Функции для комментариев
 */



# функция получения комментариев
function mso_get_comments($page_id = 0, $r = array())
{
	global $MSO;
	
	if ( !isset($r['limit']) )	$r['limit'] = false;
	if ( !isset($r['order']) )	$r['order'] = 'asc';
	if ( !isset($r['tags']) )	$r['tags'] = '<p><img><strong><em><i><b><u><s><font><pre><code><blockquote>';
	if ( !isset($r['tags_users']) )	$r['tags_users'] = '<a><p><img><strong><em><i><b><u><s><font><pre><code><blockquote>';
	if ( !isset($r['tags_comusers']) )	$r['tags_comusers'] = '<a><p><img><strong><em><i><b><u><s><font><pre><code><blockquote>';
	if ( !isset($r['anonim_comments']) )	$r['anonim_comments'] = array();
	

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
			//pr($comment);
			
			$commentator = 3; // комментатор: 1-комюзер 2-автор 3-аноним
			
			if ($comment['comusers_id']) // это комюзер
			{
				if ($comment['comusers_nik']) $comment['comments_author_name'] = $comment['comusers_nik'];
				else $comment['comments_author_name'] = 'Комментатор ' . $comment['comusers_id'];
				$comment['comments_url'] = '<a href="' . getinfo('siteurl') . 'users/' . $comment['comusers_id'] . '">'
						. $comment['comments_author_name'] . '</a>';
				$commentator = 1;
			}
			elseif ($comment['users_id']) // это автор
			{
				if ($comment['users_url']) 
						$comment['comments_url'] = '<a href="' . $comment['users_url'] . '">' . $comment['users_nik'] . '</a>';
					else $comment['comments_url'] = $comment['users_nik'];
				$commentator = 2;
			}
			else $comment['comments_url'] = $comment['comments_author_name'] . ' (анонимно)'; // просто аноним
			
			
			$comments_content = $comment['comments_content'];
			$comments_content = mso_auto_tag($comments_content, true);
			
			if ($commentator==1) $comments_content = strip_tags($comments_content, $r['tags_comusers']);
			elseif ($commentator==2) $comments_content = strip_tags($comments_content, $r['tags_users']);
			else $comments_content = strip_tags($comments_content, $r['tags']);
			
			$comments_content = mso_balance_tags($comments_content);
			
			$comments_content = mso_hook('comments_content', $comments_content);
			
			$comments[$key]['comments_content'] = $comments_content;
			$comments[$key]['comments_url'] = $comment['comments_url'];
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


# функция отправляет новому комюзеру уведомление о новой регистрации
# первый парметр id, второй данные
function mso_email_message_new_comuser($comusers_id = 0, $ins_data = array() )
{
	$email = $ins_data['comusers_email']; // email куда приходят уведомления
	if (!$email) return false;
	
	// comusers_password
	// comusers_activate_key
							
	$subject = 'Регистрация на ' . getinfo('title');
	
	$text = 'Вы или кто-то еще зарегистрировал ваш адрес на сайте "' . getinfo('title') . '" - ' . getinfo('siteurl') . NR ;
	$text .= 'Если это действительно сделали вы, то вам нужно подтвердить эту регистрацию. Для этого следует пройти по ссылке: ' . NR;
	$text .= getinfo('siteurl') . 'users/' . $comusers_id . NR . NR;
	$text .= 'И ввести следующий код для активации: '. NR;
	$text .= $ins_data['comusers_activate_key'] . NR. NR;
	$text .= 'Если же эту регистрацию выполнили не вы, то просто удалите это письмо.' . NR;
	
	return mso_mail($email, $subject, $text, $email); // поскольку это регистрация, то отправитель - тот же email
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

		if (!trim($post['comments_content'])) return '<div class="' . $args['css_error']. '">Ошибка, нет текста!</div>';
		
		// возможно есть текст, но только из одних html - не пускаем
		if ( !trim(strip_tags(trim($post['comments_content']))) ) 
			return '<div class="' . $args['css_error']. '">Ошибка, нет полезного текста!</div>';
		
		
		$comments_author_ip = $_SERVER['REMOTE_ADDR'];
		$comments_date = date('Y-m-d H:i:s');
		
		$comments_content = mso_hook('new_comments_content', $post['comments_content']);
		
		// провека на спам - проверим через хук new_comments_check_spam
		$comments_check_spam = mso_hook('new_comments_check_spam', 
										array( 
											'comments_content' => $comments_content,
											'comments_date' => $comments_date,
											'comments_author_ip' => $comments_author_ip,
											'comments_page_id' => $comments_page_id,
											'comments_server' => $_SERVER,
										), false);
		
		// если есть спам, то возвращается что-то отличное от comments_content
		// если спама нет, то дожно вернуться false
		// если есть подозрения, то возвращается массив с moderation (comments_approved)
		// если есть параметр check_spam=true, значит определен спам и он вообще не пускается
		// сообщение для вывода в парметре 'message'
		
		// разрешение антиспама moderation
		// -1 - не определено, 0 - можно разрешить, 1 - отдать на модерацию
		$moderation = -1;
		
		if ($comments_check_spam) 
		{
			if (isset($comments_check_spam['check_spam']) and $comments_check_spam['check_spam']==true)
			{
				if ( isset($comments_check_spam['message']) and $comments_check_spam['message'] )
					return '<div class="' . $args['css_error']. '">' . $comments_check_spam['message'] . '</div>';
				else 
					return '<div class="' . $args['css_error']. '">Ваш комментарий определен как спам и удален.</div>';
			}
			else 
			{
				// спам не определен, но возможно стоит moderation - принудительная модерация
				if (isset($comments_check_spam['moderation'])) $moderation = $comments_check_spam['moderation'];
			}
		}
		
		$CI = & get_instance();
		
		// проверим есть ли уже такой комментарий
		// проверка по ip и тексту
		$CI->db->select('comments_id');
		$CI->db->where(array (
			'comments_page_id' => $comments_page_id,
			'comments_author_ip' => $comments_author_ip,
			'comments_content' => $comments_content,
			));
		
		$query = $CI->db->get('comments');
		if ($query->num_rows()) // есть такой коммент
		{
			return '<div class="' . $args['css_error']. '">Похоже, вы уже отправили этот комментарий...</div>';
		}
	
		
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
				mso_flush_cache();
				mso_redirect(mso_current_url() . '#comment-' . $CI->db->insert_id());
			}
			else
				return '<div class="' . $args['css_error']. '">Ошибка добавления комментария</div>';
		}
		else
		{
			if ( isset($post['comments_reg']) ) // комюзер или аноном
			{
				if ($post['comments_reg'] == 'reg') // нужно зарегистрировать или уже есть регистрация
				{
					
					if ( !isset($post['comments_email']) or !$post['comments_email'] )
						return '<div class="' . $args['css_error']. '">Нужно указать Email</div>';

					if ( !isset($post['comments_password']) or !$post['comments_password'] )
						return '<div class="' . $args['css_error']. '">Нужно указать пароль</div>';
					
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
						
						// генерируем случайный ключ активации
						$ins_data['comusers_activate_key'] = mso_md5(rand());
						$ins_data['comusers_date_registr'] = date('Y-m-d H:i:s');
						$ins_data['comusers_last_visit'] = date('Y-m-d H:i:s');
						$ins_data['comusers_ip_register'] = $_SERVER['REMOTE_ADDR'];
						
						$res = ($CI->db->insert('comusers', $ins_data)) ? '1' : '0';
						
						if ($res)
						{
							$comusers_id = $CI->db->insert_id(); // номер добавленной записи
							mso_email_message_new_comuser($comusers_id, $ins_data); // отправляем ему уведомление с кодом активации
						}
						else
							return '<div class="' . $args['css_error']. '">Ошибка регистрации</div>';
					}
					
					if ($comusers_id)
					{
						$comments_com_approved = mso_get_option('new_comment_comuser_moderate', 'general', 1);
						
						// но у нас в базе хранится значение наоборот - 1 разрешить 0 - запретить
						$comments_com_approved = !$comments_com_approved;
						
						if ($moderation == 1) $comments_com_approved = 0; // антиспам определил, что нужно премодерировать
						
						// комюзер добавлен или есть
						// теперь сам коммент
						$ins_data = array (
							'comments_page_id' => $comments_page_id,
							'comments_comusers_id' => $comusers_id,
							'comments_author_ip' => $comments_author_ip,
							'comments_date' => $comments_date,
							'comments_content' => $comments_content,
							'comments_approved' => $comments_com_approved 
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
							mso_flush_cache();
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
					
					if ($moderation==1) $comments_approved = 0; // антиспам определил, что нужно премодерировать
					
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
						mso_flush_cache();
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


# получаем данные комюзера.
# если id = 0, то номер получаем из текущего сегмента (2)
function mso_get_comuser($id = 0, $args = array())
{
	$id = (int) $id;
	if (!$id) $id = (int) mso_segment(2);
	if (!$id) return array(); // нет номера, выходим
	
	if ( !isset($args['limit']) )	$args['limit'] = 20;
	if ( !isset($args['tags']) )	$args['tags'] = '<p><img><strong><em><i><b><u><s><font><pre><code><blockquote>';
	
	$CI = & get_instance();
	
	$CI->db->select('*');
	$CI->db->from('comusers');
	$CI->db->where('comusers_id', $id);
	$CI->db->limit(1);
		
	$query = $CI->db->get();

	if ($query->num_rows() > 0)
	{
		$comuser = $query->result_array(); // данные комюзера
		
		// подсоединим к нему [comments] - все его комментарии
		$CI->db->select('comments.*, page.page_id, page.page_title, page.page_slug');
		$CI->db->from('comments');
		$CI->db->where('comments_comusers_id', $id);
		$CI->db->where('page.page_status', 'publish');
		$CI->db->where('comments.comments_approved', '1');
		$CI->db->join('page', 'page.page_id = comments.comments_page_id');
		if ($args['limit']) $CI->db->limit($args['limit']);
		
		$query = $CI->db->get();
		
		if ($query->num_rows() > 0)
		{
			// нужно обработать тексты комментариев на предмет всяких хуков и лишних тэгов
			$comments = $query->result_array();
			foreach ($comments as $key=>$comment)
			{
				$comments_content = $comment['comments_content'];
				$comments_content = mso_auto_tag($comments_content, true);
				$comments_content = strip_tags($comments_content, $args['tags']);
				$comments_content = mso_balance_tags($comments_content);
				$comments_content = mso_hook('comments_content', $comments_content);
				$comments[$key]['comments_content'] = $comments_content;
			}
			
			$comuser[0]['comments'] = $comments;
			// $comuser[0]['comments'] = $query->result_array();
			
			$comuser[0]['comusers_count_comments'] = count($comments);
		}
		else
			$comuser[0]['comments'] = array();
		
		// pr($comuser);
		
		return $comuser;
	}
	else return array();
}

# обработка POST из формы комюзера
function mso_comuser_edit($args = array())
{
	global $MSO;
	
	if ( !isset($args['css_ok']) )		$args['css_ok'] = 'comment-ok';
	if ( !isset($args['css_error']) )	$args['css_error'] = 'comment-error';
	
	if ( $post = mso_check_post(array('f_session_id', 'f_submit', 'f_comusers_activate_key')) ) // это активация
	{
		# защита рефера
		mso_checkreferer();
		
		# защита сессии - если не нужно закомментировать строчку!
		if ($MSO->data['session']['session_id'] != $post['f_session_id']) mso_redirect();
		
		// получаем номер юзера id из f_submit[]
		$id = (int) mso_array_get_key($post['f_submit']);
		if (!$id) return '<div class="' . $args['css_error']. '">Ошибочный номер пользователя</div>'; 
		
		$f_comusers_activate_key = trim($post['f_comusers_activate_key']);
		if (!$f_comusers_activate_key) return '<div class="' . $args['css_error']. '">Неверный (пустой) ключ</div>'; 
		
		// нужно проверить если у указанного комюзера не равные ключи
		// если они равны, то ничего не делаем
		$CI = & get_instance();
	
		$CI->db->select('comusers_activate_string, comusers_activate_key');
		$CI->db->from('comusers');
		$CI->db->where('comusers_id', $id);
		$CI->db->limit(1);
	
		$query = $CI->db->get();

		if ($query->num_rows() > 0)
		{
			$comuser = $query->result_array(); // данные комюзера
			
			if ($comuser[0]['comusers_activate_string'] == $comuser[0]['comusers_activate_key']) 
			{
				// уже равны, активация не требуется
				return '<div class="' . $args['css_ok']. '">Активация уже выполнена</div>'; 
			}	
			else
			{
				// ключи в базе не равны
				// сверяем с переданным ключом из формы
				if ($f_comusers_activate_key == $comuser[0]['comusers_activate_key']) 
				{
					// верный ключ - обновляем в базе
					
					$CI->db->where('comusers_id', $id);
					$res = ($CI->db->update('comusers', 
								array ('comusers_activate_string' => $f_comusers_activate_key  ) )) ? '1' : '0';
					
					if ($res)
						return '<div class="' . $args['css_ok']. '">Активация выполнена!</div>'; 
					else
						return '<div class="' . $args['css_error']. '">Ошибка БД при добавления ключа активации</div>';
				}
				else
				{
					return '<div class="' . $args['css_error']. '">Ошибочный ключ активации</div>'; 
				}
			}
		}
		else // вообще нет такого комюзера
			return '<div class="' . $args['css_error']. '">Ошибочный номер пользователя</div>'; 
	} // активация
	elseif ( $post = mso_check_post(array('f_session_id', 'f_submit', 'f_comusers_email', 'f_comusers_password', 
					'f_comusers_nik', 'f_comusers_url', 'f_comusers_icq', 'f_comusers_msn', 'f_comusers_jaber', 
					'f_comusers_date_birth',  'f_comusers_description')) ) // это обновление формы
	{
		# защита рефера
		mso_checkreferer();
		
		# защита сессии - если не нужно закомментировать строчку!
		if ($MSO->data['session']['session_id'] != $post['f_session_id']) mso_redirect();
		
		// получаем номер юзера id из f_submit[]
		$id = (int) mso_array_get_key($post['f_submit']);
		if (!$id) return '<div class="' . $args['css_error']. '">Ошибочный номер пользователя</div>'; 
		
		$f_comusers_email = trim($post['f_comusers_email']);
		$f_comusers_password = trim($post['f_comusers_password']);
		
		if (!$f_comusers_email or !$f_comusers_password)
			return '<div class="' . $args['css_error']. '">Необходимо указать emai и пароль</div>'; 
		
		// проверим есть ли такой комюзер
		$CI = & get_instance();
	
		$CI->db->select('comusers_id');
		$CI->db->from('comusers');
		$CI->db->where(array('comusers_id'=>$id, 
							'comusers_email'=>$f_comusers_email, 
							'comusers_password'=>mso_md5($f_comusers_password) ));
		$CI->db->where('comusers_activate_string=comusers_activate_key'); // активация должна уже быть
		$CI->db->limit(1);
	
		$query = $CI->db->get();

		if ($query->num_rows() > 0)
		{
			// все ок - логин пароль верные
			$comuser = $query->result_array(); // данные комюзера
			// pr($comuser);
			
			//$ = ;
			//$ = $post[''];
			//$ = $post[''];
			$upd_date = array (
				// 'comusers_id'=>$id,
				'comusers_nik' =>	strip_tags($post['f_comusers_nik']),
				'comusers_url' =>	strip_tags($post['f_comusers_url']),
				'comusers_icq' =>	strip_tags($post['f_comusers_icq']),
				'comusers_msn' =>	strip_tags($post['f_comusers_msn']),
				'comusers_jaber' =>	strip_tags($post['f_comusers_jaber']),
				'comusers_date_birth' =>	strip_tags($post['f_comusers_date_birth']),
				'comusers_description' =>	strip_tags($post['f_comusers_description']),
				);
			$CI->db->where('comusers_id', $id);
			$res = ($CI->db->update('comusers', $upd_date )) ? '1' : '0';
					
			if ($res)
				return '<div class="' . $args['css_ok']. '">Обновление выполнено!</div>'; 
			else
				return '<div class="' . $args['css_error']. '">Ошибка БД при обновлении</div>';
		}
		else return '<div class="' . $args['css_error']. '">Ошибочный email и пароль</div>'; 
	
	} // обновление формы
}



# список всех комюзеров
function mso_get_comusers_all($args = array())
{
	$CI = & get_instance();
	
	$CI->db->select('*');
	$CI->db->from('comusers');
	$query = $CI->db->get();

	if ($query->num_rows() > 0)
	{
		$comusers = $query->result_array(); 
		return $comusers;
	}
	else return array();
}



?>