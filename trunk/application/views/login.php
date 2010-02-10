<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MaxSite CMS
 * (c) http://maxsite.org/
 */

function _mso_login()
{
	global $MSO;
	
	if ($_POST 	and isset($_POST['flogin_submit']) 
				and isset($_POST['flogin_redirect'])
				and isset($_POST['flogin_user'])
				and isset($_POST['flogin_password'])
				and isset($_POST['flogin_session_id'])
		)
	{
		$flogin_session_id = $_POST['flogin_session_id'];
		
		# защита сесии
		if ($MSO->data['session']['session_id'] != $flogin_session_id) mso_redirect('loginform');
		
		
		$flogin_redirect = urldecode($_POST['flogin_redirect']);
		
		
		if ($flogin_redirect == 'home') $flogin_redirect = getinfo('siteurl');
		
		$flogin_user = $_POST['flogin_user'];
		$flogin_password = $_POST['flogin_password'];
		
		# проверяем на strip - запрещенные символы
		if ( ! mso_strip($flogin_user, true) or ! mso_strip($flogin_password, true) ) mso_redirect('loginform');
		
		$flogin_password = mso_md5($flogin_password);
		
		$CI = & get_instance();
		
		
		// если это комюзер, то логин = email 
		// проверяем валидность email и если он верный, то ставим куку на этого комюзера 
		// и редиректимся на главную (куку ставить только на главную!)
		// если же это обычный юзер-автор, то проверяем логин и пароль по базе
		
		if (mso_valid_email($flogin_user))
		{
			// это комюзер
			
			$CI->db->select('comusers_id, comusers_password, comusers_email, comusers_nik, comusers_url, comusers_avatar_url');
			$CI->db->where('comusers_email', $flogin_user);
			$CI->db->where('comusers_password', $flogin_password);
			$query = $CI->db->get('comusers');
			if ($query->num_rows()) // есть такой комюзер
			{
				$comuser_info = $query->row_array(1); // вся инфа о комюзере
				$name_cookies = 'maxsite_comuser';
				$expire  = time() + 60 * 60 * 24 * 30; // 30 дней = 2592000 секунд
				$value = serialize($comuser_info); 
				mso_add_to_cookie($name_cookies, $value, $expire, true); // в куку для всего сайта
				exit();
			}
			else // неверные данные
			{
				mso_redirect('loginform');
				exit;
			}
		}
		else 
		{
			// это обчный автор
			
			$CI->db->from('users'); # таблица users
			$CI->db->select('*'); # все поля
			$CI->db->limit(1); # одно значение
			
			$CI->db->where('users_login', $flogin_user); // where 'users_login' = $flogin_user
			$CI->db->where('users_password', $flogin_password);  // where 'users_password' = $flogin_password
			
			$query = $CI->db->get();
			
			if ($query->num_rows() > 0) # есть такой юзер
			{
				$userdata = $query->result_array();
				
				# добавляем юзера к сессии
				$CI->session->set_userdata('userlogged', '1');
				
				$data = array(
					'users_id' => $userdata[0]['users_id'],
					'users_nik' => $userdata[0]['users_nik'],
					'users_login' => $userdata[0]['users_login'],
					'users_password' => $userdata[0]['users_password'],
					'users_groups_id' => $userdata[0]['users_groups_id'],
					// 'users_levels_id' => $userdata[0]['users_levels_id'],
					// 'users_last_visit' => $userdata[0]['users_last_visit'],
					// 'users_avatar_url' => $userdata[0]['users_avatar_url'],
					'users_show_smiles' => $userdata[0]['users_show_smiles'],
					'users_time_zone' => $userdata[0]['users_time_zone'],
					'users_language' => $userdata[0]['users_language'],
					// 'users_skins' => $userdata[0]['users_skins']
				);
				
				$CI->session->set_userdata($data);
				mso_redirect($flogin_redirect, true);
			}
			else mso_redirect('loginform');
		} // автор
	}
	else 
	{
		
		$MSO->data['type'] = 'loginform';
		$template_file = $MSO->config['templates_dir'] . $MSO->config['template'] . '/index.php';
		
		if ( file_exists($template_file) ) require($template_file);
			else show_error('Ошибка - отсутствует файл шаблона index.php');
	};
}

_mso_login();

?>