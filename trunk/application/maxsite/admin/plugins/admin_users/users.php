<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

	$CI = & get_instance();
	if ( $post = mso_check_post(array('f_session_id', 'f_submit', 'f_user_login', 
			'f_user_email', 'f_user_password', 'f_user_group')) )
	{
		mso_checkreferer();
		
		
		// подготавливаем данные для xmlrpc
		$data = array(
			'user_login' => $MSO->data['session']['users_login'],
			'password' => $MSO->data['session']['users_password'],
			
			'users_login' => $post['f_user_login'],
			'users_email' => $post['f_user_email'],
			'users_password' => $post['f_user_password'],
			'users_groups_id' => $post['f_user_group']
			);
		
		// выполняем запрос и получаем результат
		// $result = mso_xmlrpc_send('NewUser', mso_xmlrpc_this($data));
		
		require_once( getinfo('common_dir') . 'functions-edit.php' ); // функции редактирования
		
		$result = mso_new_user($data);
		
		if (isset($result['result'])) 
		{
			if ($result['result'] == 1)
				echo '<div class="update">Пользователь создан!</div>'; // . $result['description'];
			else 
				echo '<div class="error">Произошла ошибка<p>' . $result['description'] . '</p></div>';
		}
		else
			echo '<div class="error">Ошибка обновления</div>';
	}

?>
<h1>Пользователи</h1>
<p class="info">Список пользователей сайта</p>

<?php
	$CI->load->library('table');
	
	$tmpl = array (
				'table_open'		  => '<table class="page" border="0" width="99%">',
				'row_alt_start'		  => '<tr class="alt">',
				'cell_alt_start'	  => '<td class="alt">',
		  );
		  
	$CI->table->set_template($tmpl); // шаблон таблицы

	$CI->table->set_heading('ID','Логин', 'Ник', 'E-mail', 'Сайт', 'Группа', 'Действие');
	
	
	$CI->db->select('*');
	$CI->db->from('users');
	$CI->db->join('groups', 'users.users_groups_id = groups.groups_id');
	$CI->db->order_by('users_groups_id');
	
	$query = $CI->db->get();
	
	$this_url = $MSO->config['site_admin_url'] . 'users';


	foreach ($query->result_array() as $row)
	{
		$id = $row['users_id'];
		$login = $row['users_login'];
		$nik = $row['users_nik'];
		$email = $row['users_email'];
		$url = $row['users_url'];
		
		$groups_name = $row['groups_name'];
		
		$act = '<a href="'.$this_url.'/edit/' . $id . '">Изменить</a>';
		
		$CI->table->add_row($id, $login, $nik, $email, $url, $groups_name, $act);
	}

	// добавляем форму, а также текущую сессию
	echo $CI->table->generate(); // вывод подготовленной таблицы
	
	
	if ( mso_check_allow('edit_add_new_users') ) // если разрешено создавать юзеров
	{
		// новый пользователь создается так:
		// указывается его логин, пароль, емайл, группа
		// создается
		// для того, чтобы отредактировать, нужно войти в его редактирование
		$new_user_login = '';
		$new_user_email = '';
		$new_user_password = '';
		$new_user_group = '';
		
		$form = '';
		$CI->load->helper('form');
		
		$form .= '<br />Логин: '. form_input( array( 'name'=>'f_user_login', 'style'=>'width: 200px' ) ) ;
		$form .= '<br />E-mail: '. form_input( array( 'name'=>'f_user_email', 'style'=>'width: 200px' ) );
		$form .= '<br />Пароль: '. form_input( array( 'name'=>'f_user_password', 'style'=>'width: 200px' ) );
		
		$CI->db->select('groups_id, groups_name');
		$q = $CI->db->get('groups');
		$groups = array();
		foreach ($q->result_array() as $rw)
			$groups[$rw['groups_id']] = $rw['groups_name'];

		$form .= '<br />Группа: '. form_dropdown('f_user_group', $groups, '', ' style="width: 200px;" ');	
		$form .=  '<br/ ><br /><input type="submit" name="f_submit" value="Создать пользователя" />';
		
		echo '<form action="" method="post">' . mso_form_session('f_session_id');
		echo '<br /><br /><h2>Создать нового пользователя</h2>';
		echo '<p>Если данные некорректны, то пользователь создан не будет. Для нового пользователя-админа нужно обновить разрешения.</p>';
		echo $form;
		echo '</form>';
	}

?>