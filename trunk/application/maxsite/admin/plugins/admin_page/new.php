<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
	
	$CI = & get_instance();
	
	$id = 0;
	
	if ( $post = mso_check_post(array('f_session_id', 'f_submit', 'f_content')) )
	{
		mso_checkreferer();
		
		// $f_content = mso_text_to_html($post['f_content']);
		$f_content = trim($post['f_content']);
		
		$f_content = str_replace(chr(10), "<br />", $f_content);
		$f_content = str_replace(chr(13), "", $f_content);
			
		// глюк FireFox исправлем замену абсолютного пути src на абсолютный
		// $f_content = str_replace('src="../../application/', 'src="' . $MSO->config['application_url'], $f_content);
		// $f_content = str_replace('src="../application/', 'src="' . $MSO->config['application_url'], $f_content);

		$f_content = str_replace('src="../../', 'src="' . $MSO->config['site_url'], $f_content);
		$f_content = str_replace('src="../', 'src="' . $MSO->config['site_url'], $f_content);
			
		$f_header = mso_text_to_html($post['f_header']);
		
		if ( isset($post['f_tags']) and $post['f_tags'] ) $f_tags = $post['f_tags'] ;
				else $f_tags = '';
			
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
		// $f_id = mso_array_get_key($post['f_edit_submit']);
		
		// подготавливаем данные для xmlrpc
		$data = array(
			'user_login' => $MSO->data['session']['users_login'],
			'password' => $MSO->data['session']['users_password'],
			
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
			'page_meta_options' => $f_options
			);
			
		//pr($data);
		// pr($post);
		//pr($f_tags);
		//pr(mso_explode($f_tags, false, false));
		
		// выполняем запрос и получаем результат
		// $result = mso_xmlrpc_send('NewPage', mso_xmlrpc_this($data));
		
		require_once( getinfo('common_dir') . 'functions-edit.php' ); // функции редактирования
		$result = mso_new_page($data);
			
		// pr($result);
		
		if (isset($result['result']) and $result['result']) 
		{
			if (isset($result['result'][0])) 
			{
				$url = '<a href="' 
						. mso_get_permalink_page($result['result'][0])
						. '" target="_blank">Посмотреть запись</a> | '
						. '<a href="' . $MSO->config['site_admin_url'] . 'page_edit/' 
						. $result['result'][0] . '">Изменить</a>';
			}
			else $url = '';

			echo '<div class="update">Страница добавлена! ' . $url . '</div>'; // . $result['description'];
			
			mso_flush_cache(); // сбросим кэш
			
			# остальное на дефолт
			$f_content = '';
			$f_header = '';
			$f_tags = '';
			$f_slug = '';
			$f_status = '1';
			$f_page_type = '1';
			$f_cat = array();
			$f_password = '';
			$f_comment_allow = '1';
			$f_ping_allow = '1';
			$f_feed_allow = '1';
			$f_page_parent = '';
			$f_user_id = $MSO->data['session']['users_id'];
			
			// еще дата опубликования
			// и дата удаления
			
		}
		else
			echo '<div class="error">Ошибка обновления</div>';
		
	}
	else 
	{
		// дефолтные настройки новой записи
		$f_content = '';
		$f_header = '';
		$f_tags = '';
		$f_slug = '';
		$f_status = '1';
		$f_page_type = '1';
		$f_cat = array();
		$f_password = '';
		$f_comment_allow = '1';
		$f_ping_allow = '1';
		$f_feed_allow = '1';
		$f_page_parent = '';
		$f_user_id = $MSO->data['session']['users_id'];

	};
	
	
	echo '<h1 class="content">Создать страницу</h1>';
	
	
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
	require_once( $MSO->config['common_dir'] . 'category.php' );
	// $all_cat = mso_cat('<input name="f_cat[]" type="checkbox" %CHECKED% value="%ID%"> %NAME%', $f_cat);
	
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
	
	$name_submit = 'f_submit';
	
	

	# мета большие,вынесена в отдельный файл
	# из неё получается $all_meta = '<p>Нет</p>';
	require($MSO->config['admin_plugins_dir'] . 'admin_page/all_meta.php');
	
	
	$f_status_draft = $f_status_private = '';
	$f_status_publish = 'checked';
	
	# форма вынесена в отдельный файл, поскольку она одна и таже для new и edit
	# из неё получается $do и $posle
	require($MSO->config['admin_plugins_dir'] . 'admin_page/form.php');

	$ad_config = array(
				'action'=> '',
				'content' => $f_content,
				'do' 	=> $do,
				'posle' => $posle,
				);

	# отображаем редактор
	editor_jw($ad_config);
	
?>