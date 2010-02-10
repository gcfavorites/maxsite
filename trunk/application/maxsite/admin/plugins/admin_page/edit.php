<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<h1>Редактирование страницы</h1>
<p><a href="<?= $MSO->config['site_admin_url'] . 'page' ?>">Вернуться к списку страниц</a>	

<?php
	
	$id = mso_segment(3); // номер страницы по сегменту url
	
	// проверим, чтобы это было число
	$id1 = (int) $id;
	if ( (string) $id != (string) $id1 ) $id = false; // ошибочный id
	
	echo ' | <a href="' . mso_get_permalink_page($id) . '">Посмотреть запись</a> (<a target="_blank" href="' . mso_get_permalink_page($id) . '">в новом окне</a>)</p>';
							
	if ($id) // есть корректный сегмент
	{
	
	
		require_once( getinfo('common_dir') . 'category.php' ); // функции рубрик
		require_once( getinfo('common_dir') . 'meta.php' ); // функции meta - для меток
	
////////////////////////////////////////////////////////////////////////////////
		// этот код почти полностью повторяет код из new.php
		// разница только в том, что указан id
		
		$CI = & get_instance();
		
		if ( $post = mso_check_post(array('f_session_id', 'f_submit', 'f_content')) )
		{
			mso_checkreferer();
			
			// pr($_POST);
			
			// pr($post['f_content'], true);
			// $f_content = mso_text_to_html($post['f_content']);
			
			$f_content = trim($post['f_content']);
			
			$f_content = str_replace(chr(10), "<br />", $f_content);
			$f_content = str_replace(chr(13), "", $f_content);
			
			// pr($f_content, true);
			
			// глюк FireFox исправлем замену абсолютного пути src на абсолютный
			$f_content = str_replace('src="../../', 'src="' . $MSO->config['site_url'], $f_content);
			$f_content = str_replace('src="../', 'src="' . $MSO->config['site_url'], $f_content);
			
			// $f_content = str_replace('src="../../application/', 'src="' . $MSO->config['application_url'], $f_content);
			// $f_content = str_replace('src="../application/', 'src="' . $MSO->config['application_url'], $f_content);
			
			$f_header = mso_text_to_html($post['f_header']);
			// $f_tags = $post['f_tags'];
			
			if ( isset($post['f_tags']) and $post['f_tags'] ) $f_tags = $post['f_tags'] ;
				else $f_tags = '';
			
			// pr(mso_explode($f_tags, false, false));	
			
			if ( isset($post['f_slug']) and $post['f_slug'] ) $f_slug = $post['f_slug'] ;
				else $f_slug = mso_slug($f_header);
				
			if ( isset($post['f_password']) and $post['f_password']) $f_password = $post['f_password'] ;
				else $f_password = '';			
				
				
			if ( isset($post['f_cat']) ) $f_cat = $post['f_cat'] ;
				else $f_cat = array();
			
			// все мета
			$f_options = '';
			if ( isset($post['f_options']) )
			{
				foreach ($post['f_options'] as $key=>$val)
				{
					$f_options .= $key . '##VALUE##' . trim($val) . '##METAFIELD##';
				}
			}
			
			
			if ( isset($post['f_status']) ) $f_status = $post['f_status'][0];
				else $f_status = 'publish';	
				
			if ( isset($post['f_page_type']) ) $f_page_type = $post['f_page_type'][0];
				else $f_page_type = '1';
				
			if ( isset($post['f_page_parent']) and $post['f_page_parent'] ) $f_page_parent = (int) $post['f_page_parent'];
				else $f_page_parent = '';
			
					$f_date_change = isset($post['f_date_change']) ? '1' : '0'; // сменить дату?
		
			if ( // проверяем есть ли дата
				$f_date_change and
				isset($post['f_date_y']) and 
				isset($post['f_date_m']) and
				isset($post['f_date_d']) and 
				isset($post['f_time_h']) and
				isset($post['f_time_m']) and
				isset($post['f_time_s']) and
				$post['f_date_y'] and
				$post['f_date_m'] and
				$post['f_date_d'] and
				$post['f_time_h'] and
				$post['f_time_m'] and
				$post['f_time_s'] )
			{
				$page_date_publish_y = (int) $post['f_date_y'];
				$page_date_publish_m = (int) $post['f_date_m'];
				$page_date_publish_d = (int) $post['f_date_d'];
				$page_date_publish_h = (int) $post['f_time_h'];
				$page_date_publish_n = (int) $post['f_time_m'];
				$page_date_publish_s = (int) $post['f_time_s'];
				
				$page_date_publish = date('Y-m-d H:i:s', mktime($page_date_publish_h, $page_date_publish_n, $page_date_publish_s,
										$page_date_publish_m, $page_date_publish_d, $page_date_publish_y) );
				
			}
			else
				$page_date_publish = false;
					

			// тут нужно будет изменить логику
			// если автор указан, то нужно проверять есть разрешение на указание другого
			// если есть разрешение, то все нормуль
			// если нет, то автор остается текущим
			if (isset($post['f_user_id'])) $f_user_id = (int) $post['f_user_id'];
				else $f_user_id = $MSO->data['session']['users_id'];
			
			$f_comment_allow = isset($post['f_comment_allow']) ? '1' : '0';
			$f_ping_allow = isset($post['f_ping_allow']) ? '1' : '0';
			$f_feed_allow = isset($post['f_feed_allow']) ? '1' : '0';
			
		
			// получаем номер опции id из fo_edit_submit[]
			$f_id = mso_array_get_key($post['f_submit']);
			
			// подготавливаем данные для xmlrpc
			$data = array(
				'user_login' => $MSO->data['session']['users_login'],
				'password' => $MSO->data['session']['users_password'],
				
				'page_id' => $f_id,
				'page_title' => $f_header,
				'page_content' => $f_content,
				'page_type_id' => $f_page_type,
				'page_id_cat' => implode(',', $f_cat),
				'page_id_parent' => $f_page_parent,
				'page_id_autor' => $f_user_id,
				'page_status' => $f_status,
				'page_slug' => $f_slug,
				'page_password' => $f_password,
				'page_comment_allow' => $f_comment_allow,
				'page_ping_allow' => $f_ping_allow,
				'page_feed_allow' => $f_feed_allow,
				'page_tags' => $f_tags,
				'page_meta_options' => $f_options,
				'page_date_publish' => $page_date_publish,

				);
				
			// pr($data);
			// pr($post);
			//pr($f_tags);
			//pr(mso_explode($f_tags, false, false));
			// pr(mso_xmlrpc_this($data));
			
			// выполняем запрос и получаем результат
			// $result = mso_xmlrpc_send('EditPage', mso_xmlrpc_this($data));
			
			require_once( getinfo('common_dir') . 'functions-edit.php' ); // функции редактирования
			$result = mso_edit_page($data);
			
			// pr($result);
			
			if (isset($result['result']) and $result['result']) 
			{
				if (isset($result['result'][0])) 
				{
					$url = '<a href="' 
							. mso_get_permalink_page($result['result'][0])
							. '">Посмотреть запись</a>';
						//	. ' | <a href="' . $MSO->config['site_admin_url'] . 'page_edit/' . $result['result'][0] . '">Изменить</a>';

				}
				else $url = '';

				echo '<div class="update">Страница обновлена! ' . $url . '</div>'; // . $result['description'];
				
				mso_flush_cache(); // сбросим кэш
				
				# пулучаем данные страниц
				$CI->db->select('*');
				$CI->db->from('page');
				$CI->db->where(array('page_id'=>$id));
				$query = $CI->db->get();
				if ($query->num_rows() > 0)
				{
					foreach ($query->result_array() as $row)
					{
						// pr($row);
						$f_content = $row['page_content'];
						$f_header = $row['page_title'];
						$f_slug = $row['page_slug'];
						$f_status = $row['page_status'];
						$f_page_type = $row['page_type_id'];
						$f_password = $row['page_password'];
						$f_comment_allow = $row['page_comment_allow'];
						$f_ping_allow = $row['page_ping_allow'];
						$f_feed_allow = $row['page_feed_allow'];
						$f_page_parent = $row['page_id_parent'];
						$f_user_id = $row['page_id_autor'];
						$page_date_publish = $row['page_date_publish'];
					}
					$f_cat = mso_get_cat_page($id); // рубрики в виде массива
					$f_tags = implode(', ', mso_get_tags_page($id)); // метки страницы в виде массива			
				}
				
				// еще дата опубликования
				// и дата удаления
				
			}
			else
				echo '<div class="error">Ошибка обновления</div>';
			
		}
		else 
		{
			// получаем данные записи
			$CI->db->select('*');
			$CI->db->from('page');
			$CI->db->where(array('page_id'=>$id));
			$query = $CI->db->get();
			if ($query->num_rows() > 0)
			{
				foreach ($query->result_array() as $row)
				{
					// pr($row);
					$f_content = $row['page_content'];
					$f_header = $row['page_title'];
					$f_slug = $row['page_slug'];
					$f_status = $row['page_status'];
					$f_page_type = $row['page_type_id'];
					$f_password = $row['page_password'];
					$f_comment_allow = $row['page_comment_allow'];
					$f_ping_allow = $row['page_ping_allow'];
					$f_feed_allow = $row['page_feed_allow'];
					$f_page_parent = $row['page_id_parent'];
					$f_user_id = $row['page_id_autor'];
					$page_date_publish = $row['page_date_publish'];
				}
				
				$f_cat = mso_get_cat_page($id); // рубрики в виде массива
				$f_tags = implode(', ', mso_get_tags_page($id)); // метки страницы в виде массива			
			}
			else
			{
				echo '<div class="error">Ошибочная страница (нет такой страницы)</div>';
				return;
			}
		
		}
		
		$input_style = 'style="width: 99%; border: 1px solid #3B619C; margin: 5px auto 5px auto; background: #E3FAFF; color: #333399; padding: 2px; font-size: 18pt;"';
		
		$f_header = mso_text_to_html($f_header);
		$f_tags = mso_text_to_html($f_tags);
		
		$fses = mso_form_session('f_session_id'); // сессия

		// получаем все типы страниц
		$all_post_types = '';
		$query = $CI->db->get('page_type');
		foreach ($query->result_array() as $row)
		{
			if ($f_page_type == $row['page_type_id']) $che = 'checked="checked"';
				else $che = '';
				
			$all_post_types .= '<p><input name="f_page_type[]" type="radio" ' . $che 
									. ' value="' . $row['page_type_id'] . '"> ' 
									. $row['page_type_name'] . '</p>';
		}
		
		
		// получаем все рубрики чекбоксы
		$all_cat = mso_cat_ul('<input name="f_cat[]" type="checkbox" %CHECKED% value="%ID%"> %NAME%', true, $f_cat, array());

		
		if ($f_comment_allow) $f_comment_allow = 'checked="checked"';
			else $f_comment_allow = '';
			
		if ($f_ping_allow) $f_ping_allow = 'checked="checked"';
			else $f_ping_allow = '';
			
		if ($f_feed_allow) $f_feed_allow = 'checked="checked"';
			else $f_feed_allow = '';
		
		# получаем список юзеров
		$CI->db->select('users_id, users_login, users_nik');
		$CI->db->from('users');
		$query = $CI->db->get();
		
		$all_users = array();
		
		// если есть данные, то выводим
		if ($query->num_rows() > 0)
		{
			foreach ($query->result_array() as $row)
				$all_users[$row['users_id']] = $row['users_login'] . ' (' . $row['users_nik'] . ')';
		}
		
		$CI->load->helper('form');
		$all_users = form_dropdown('f_user_id', $all_users, $f_user_id, ' style="width: 99%;" ');
		
		
		$f_status_draft = $f_status_private = $f_status_publish = '';
		if ($f_status == 'draft') $f_status_draft = 'checked';
		elseif ($f_status == 'private') $f_status_private = 'checked';
		else $f_status_publish = 'checked'; // ($f_status == 'publish') 
		
		$name_submit = 'f_submit[' . $id . ']';
		
		
		// дата публикации
		// $f_date_change = 'checked="checked"';
		$f_date_change = ''; // сменить дату не нужно - будет время автоматом поставлено текущее
		
		// $date_time = date('Y-m-d H:i:s');
		
		$date_cur = strtotime($page_date_publish);
		$date_time = 'Сейчас: ' . $page_date_publish;
		
		// $page_date_publish;
		
		$date_cur_y = date('Y', $date_cur);
		$date_cur_m = date('m', $date_cur);
		$date_cur_d = date('d', $date_cur);	
		$tyme_cur_h = date('H', $date_cur);
		$tyme_cur_m = date('i', $date_cur);
		$tyme_cur_s = date('s', $date_cur);
		
		
		$date_all_y = array();
		for ($i=2005; $i<2021; $i++) $date_all_y[$i] = $i;
		
		$date_all_m = array();
		for ($i=1; $i<13; $i++) $date_all_m[$i] = $i;
		
		$date_all_d = array();
		for ($i=1; $i<32; $i++) $date_all_d[$i] = $i;
		
		$date_y = form_dropdown('f_date_y', $date_all_y, $date_cur_y, ' style="margin-top: 5px; width: 60px;" ');
		$date_m = form_dropdown('f_date_m', $date_all_m, $date_cur_m, ' style="margin-top: 5px; width: 60px;" ');
		$date_d = form_dropdown('f_date_d', $date_all_d, $date_cur_d, ' style="margin-top: 5px; width: 60px;" ');
		
		$time_all_h = array();
		for ($i=0; $i<24; $i++) $time_all_h[$i] = $i;
		
		$time_all_m = array();
		for ($i=0; $i<60; $i++) $time_all_m[$i] = $i;

		$time_all_s = $time_all_m;
		
		$time_h = form_dropdown('f_time_h', $time_all_h, $tyme_cur_h, ' style="margin-top: 5px; width: 60px;" ');
		$time_m = form_dropdown('f_time_m', $time_all_m, $tyme_cur_m, ' style="margin-top: 5px; width: 60px;" ');
		$time_s = form_dropdown('f_time_s', $time_all_s, $tyme_cur_s, ' style="margin-top: 5px; width: 60px;" ');
		
		
		# мета большие,вынесена в отдельный файл
		# из неё получается $all_meta = '<p>Нет</p>';
		require($MSO->config['admin_plugins_dir'] . 'admin_page/all_meta.php');
		
	
		# форма вынесена в отдельный файл, поскольку она одна и таже для new и edit
		# из неё получается $do и $posle
		require($MSO->config['admin_plugins_dir'] . 'admin_page/form.php');
	
		$f_content = htmlspecialchars($f_content);
		
		$ad_config = array(
					'action'=> '',
					'content' => $f_content,
					'do' 	=> $do,
					'posle' => $posle,
				
					);

		# отображаем редактор
		editor_jw($ad_config);


	////////////////////////////////////////////////////////////////////////////////

	
	}
	else
	{
		echo '<div class="error">Ошибочный запрос</div>'; // id - ошибочный
	}
?>