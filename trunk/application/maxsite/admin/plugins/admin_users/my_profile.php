<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

mso_cur_dir_lang('admin');
	
?>

<h1><?= t('Мой профиль') ?></h1>
<p class="info"></p>

<?php

	$CI = & get_instance();
	
	if ( $post = mso_check_post(array('f_session_id', 'f_submit')) )
	{
		mso_checkreferer();
		
		// получаем номер юзера id из fo_edit_submit[]
		$f_id = mso_array_get_key($post['f_submit']); 
		
		$tz = (float) $post['f_time_zone'];
		$tz = $tz * 3600;
		
		
		// подготавливаем данные для xmlrpc
		$data = array(
			
			'user_login' => $MSO->data['session']['users_login'],
			'password' => $MSO->data['session']['users_password'],
			
			'users_id' => $f_id,
			'users_nik' => $post['f_nik'],
			'users_first_name' => $post['f_first_name'],
			'users_last_name' => $post['f_last_name'],
			'users_email' => $post['f_email'],
			'users_icq' => $post['f_icq'],
			'users_url' => $post['f_url'],
			'users_msn' => $post['f_msn'],
			'users_jaber' => $post['f_jaber'],
			'users_skype' => $post['f_skype'],
			'users_avatar_url' => $post['f_avatar_url'],
			'users_description' => $post['f_description'],
			'users_date_birth_y' => $post['f_date_birth_y'],
			'users_date_birth_m' => $post['f_date_birth_m'],
			'users_date_birth_d' => $post['f_date_birth_d'],
			'users_show_smiles' => $post['f_show_smiles'],
			'users_time_zone' => $tz,
			'users_notify' => $post['f_notify'],
			'users_language' => $post['f_language']
			);
		
		if ( $post['f_new_password'] and ($post['f_new_password'] == $post['f_new_confirm_password']) )
		{
			$data['users_new_password'] = $post['f_new_password'];
			$change_pass = true;
		}
		else $change_pass = false; 
	
		if (isset($post['f_admin_note']) and $post['f_admin_note']) $data['users_admin_note'] = $post['f_admin_note'];
		
		if (isset($post['f_groups_id']) and $post['f_groups_id']) $data['users_groups_id'] = $post['f_groups_id'];
		
		
		// pr($data);
	
		require_once( getinfo('common_dir') . 'functions-edit.php' ); // функции редактирования
		$result = mso_edit_user($data);
		
		//pr($result);
		
		if (isset($result['result']) and $result['result']) 
		{
			echo '<div class="update">' . t('Обновлено!', 'admin') . '</div>'; // . $result['description'];
			mso_flush_cache(); // сбросим кэш, поскольку могла измениться группа юзера
			if ($change_pass ) 
			{
				// нужно ли после смены пароля перелогиниваться?
				// unset($MSO->data['session']); // обнуляем сессию
				// mso_redirect('login');
			}
		}
		else
			echo '<div class="error">' . t('Ошибка обновления', 'admin') . '</div>';
		
	}


	
	# вспомогательная функция
	# имя поле значение
	function _mso_add_row($title, $field, $val)
	{
		$CI = & get_instance();
		$CI->table->add_row($title, 
			form_input( array( 'name'=>$field, 'style'=>'width: 99%',
			'value'=>$val ) ) );
	}
	
	
	$id = (int) $MSO->data['session']['users_id']; // номер пользователя из сессии
	
	if ($id) // есть корректный сегмент
	{
		# подготавливаем выборку из базы
		$CI->db->select('*');
		$CI->db->from('users');
		$CI->db->where('users_id', $id);
		$CI->db->join('groups', 'users.users_groups_id = groups.groups_id');
		$CI->db->group_by('users_groups_id');
		$query = $CI->db->get();
		
		// если есть данные, то выводим
		if ($query->num_rows() > 0)
		{
			
			$CI->load->helper('form');
			$CI->load->library('table');
			
			$tmpl = array (
						'table_open'		  => '<table class="page" border="0" width="99%">
						<colgroup><colgroup>',
						'row_alt_start'		  => '<tr class="alt">',
						'cell_alt_start'	  => '<td class="alt">',
				  );

			$CI->table->set_template($tmpl); // шаблон таблицы
			
			// заголовки
			$CI->table->set_heading(t('Название', 'admin'), t('Значение', 'admin'));
			


			foreach ($query->result_array() as $row)
			{
				$id = $row['users_id'];
				//$login = $row['users_login'];
				//$nik = $row['users_nik'];
				//$email = $row['users_email'];
				//$url = $row['users_url'];
				//$groups_name = $row['groups_name'];
			
				$CI->table->add_row('ID', $id );
									
				$CI->table->add_row('Логин', $row['users_login'] );
										
				_mso_add_row('Ник', 'f_nik', $row['users_nik'] );
				_mso_add_row('E-mail', 'f_email', $row['users_email'] );
				_mso_add_row('Фамилия', 'f_first_name', $row['users_first_name'] );
				_mso_add_row('Имя', 'f_last_name', $row['users_last_name']);
				_mso_add_row('ICQ', 'f_icq', $row['users_icq']);
				_mso_add_row('Сайт', 'f_url', $row['users_url']);
				_mso_add_row('Twitter', 'f_msn', $row['users_msn']);
				_mso_add_row('Jabber', 'f_jaber', $row['users_jaber']);
				_mso_add_row('Skype', 'f_skype', $row['users_skype']);
				_mso_add_row('URL аватара', 'f_avatar_url', $row['users_avatar_url']);
				
				
				$CI->table->add_row(t('Описание', 'admin'), '<textarea name="f_description" cols="90" rows="3">' . htmlspecialchars($row['users_description']) . '</textarea>');
				
				if ( mso_check_allow('edit_users_admin_note') )
					$CI->table->add_row(t('Примечание админа', 'admin'), '<textarea name="f_admin_note" cols="90" rows="3">' . htmlspecialchars($row['users_admin_note']) . '</textarea>');
			
				// ДР это три поля
				$y = mso_date_convert('Y', $row['users_date_birth']);
				$m = mso_date_convert('n', $row['users_date_birth']);
				$d = mso_date_convert('j', $row['users_date_birth']);
				
				$y_r = array_flip(range(1960, 2008));
				foreach ($y_r as $key=>$val) $y_r[$key] = $key;

				$m_r = array_flip(range(1, 12));
				foreach ($m_r as $key=>$val) $m_r[$key] = $key;
				
				$d_r = array_flip(range(1, 31));
				foreach ($d_r as $key=>$val) $d_r[$key] = $key;			
				
				$CI->table->add_row('Дата рождения', 
				t('Год:', 'admin') . ' ' . form_dropdown('f_date_birth_y', $y_r, $y, ' style="width: 100px;" ') . 
				' ' . t('Месяц:', 'admin') . ' ' . form_dropdown('f_date_birth_m', $m_r, $m, ' style="width: 100px;" ' ) . 
				' ' . t('День:', 'admin') . ' ' . form_dropdown('f_date_birth_d', $d_r, $d, ' style="width: 100px;" ' ) 
				);


				
				###!!! найти заны и разницы с городами
				$tz = $row['users_time_zone'];
				// в базе смещение хранится в формате секунд
				$tz = sprintf('%.2f', $tz / 3600); // переводим в формат 7.00
				
				$CI->table->add_row(t('Временная зона', 'admin'), 
					form_dropdown('f_time_zone', array(
					'0.00'=>'0:00 Casablanca, Dublin, Edinburgh, London, Lisbon, Monrovia', 
					'1.00'=>'1:00 Berlin, Brussels, Copenhagen, Madrid, Paris, Rome', 
					'2.00'=>'2:00 Киев, Севастополь, Kaliningrad, South Africa, Warsaw', 
					'3.00'=>'3:00 Москва, Baghdad, Riyadh, Nairobi', 
					'4.00'=>'4:00 Adu Dhabi, Baku, Muscat, Tbilisi', 
					'5.00'=>'5:00 Islamabad, Karachi, Tashkent', 
					'6.00'=>'6:00 Almaty, Colomba, Dhakra', 
					'7.00'=>'7:00 Bangkok, Hanoi, Jakarta', 
					'8.00'=>'8:00 Beijing, Hong Kong, Perth, Singapore, Taipei', 
					'9.00'=>'9:00 Osaka, Sapporo, Seoul, Tokyo, Yakutsk', 
					'10.00'=>'10:00 Melbourne, Papua New Guinea, Sydney, Vladivostok', 
					'11.00'=>'11:00 Magadan, New Caledonia, Solomon Islands', 
					'12.00'=>'12:00 Auckland, Wellington, Fiji, Marshall Island', 
					'-1.00'=>'-1:00 Azores, Cape Verde Islands', 
					'-2.00'=>'-2:00 Mid-Atlantic, Ascention Is., St Helena', 
					'-3.00'=>'-3:00 Brazil, Buenos Aires, Georgetown, Falkland Is.', 
					'-4.00'=>'-4:00 Atlantic Time, Caracas, La Paz', 
					'-5.00'=>'-5:00 Eastern Time, Bogota, Lima, Quito', 
					'-6.00'=>'-6:00 Central Time, Mexico City',
					'-7.00'=>'-7:00 Mountain Time', 
					'-8.00'=>'-8:00 Pacific Time', 
					'-9.00'=>'-9:00 Alaska', 
					'-10.00'=>'-10:00 Hawaii', 
					'-11.00'=>'-11:00 Nome, Midway Island, Samoa',
					'-12.00'=>'-12:00 Enitwetok, Kwajalien'
					), $tz, ' style="width: 99%;" ' ) );
				

				$CI->table->add_row(t('Смайлики', 'admin'), 
					form_dropdown('f_show_smiles', array('0'=>t('Прятать', 'admin'), '1'=>t('Отображать', 'admin')), $row['users_show_smiles'], ' style="width: 300px;" '));
				
	
				###!!! что за уведомления? для чего???
				$CI->table->add_row(t('Уведомления', 'admin'), 
					form_dropdown('f_notify', array('0'=>t('Без уведомлений', 'admin'), '1'=>t('Подписаться', 'admin')), $row['users_notify'], ' style="width: 300px;" '));
				
				###!!! языки взять из CodeIgniter !!!
				$CI->table->add_row(t('Язык', 'admin'), 
					form_dropdown('f_language', array('ru'=>t('Русский', 'admin'), 'en'=>t('Английский', 'admin'), 'ua'=>t('Украинский', 'admin')), $row['users_language'], ' style="width: 300px;" '));	
				
				
				###!!! группу доделать !!!
				# если разрешено изменять группу
				# нельзя изменить юзера 1 - ибо это админ сайта
				if ( mso_check_allow('edit_users_group') and ($id > 1) )
				{
					// получить массив всех групп в $groups
					$CI->db->select('groups_id, groups_name');
					$q = $CI->db->get('groups');
					$groups = array();
					foreach ($q->result_array() as $rw)
						$groups[$rw['groups_id']] = $rw['groups_name'];
				
					$CI->table->add_row(t('Группа', 'admin'), 
					form_dropdown('f_groups_id', $groups, $row['groups_id'], ' style="width: 300px;" '));			
				}
				
				_mso_add_row(t('Новый пароль (только английские символы, длина > 6 символов)', 'admin'), 'f_new_password', '');
				_mso_add_row(t('Подтвердите пароль', 'admin'), 'f_new_confirm_password', '');


				###!!! здесь же по-идее нужно смотреть и мета для данного юзера
				###!!! и выводить её в виде - ключ-значение
				###!!! meta_table = 'users'  meta_id_obj = $id
				
			}
			
			echo '<form action="" method="post">' . mso_form_session('f_session_id');
			echo $CI->table->generate();
			echo '<p class="br"><input type="submit" name="f_submit[' . $id . ']" value="' . t('Изменить', 'admin') . '"></p>';
			echo '</form>';
		}
		else echo '<div class="error">' . t('Ошибочный запрос', 'admin') . '</div>';
	}
	else
	{
		echo '<div class="error">' . t('Ошибочный запрос', 'admin') . '</div>';
	}

# End of file