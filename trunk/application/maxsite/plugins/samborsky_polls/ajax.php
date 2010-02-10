<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

	$return = array(
		'error_code' => 1,
		'error_description' => 'Не указан ID голосования',
		'resp' => ''
	);
	
	header('Content-Type: application/json; charset=utf-8');

	if( isset($_POST['q_id']) && is_numeric($_POST['q_id']) && isset($_POST['a_id']) ){
		
		$question = new sp_question($_POST['q_id']);
		
		// Получим данные о голосовании
		if( $question->get() ){
			
			// Проверим, можно ли голосовать
			if( $question->check_allow() ){
				
				// Ставим кукис
				if( 1 == $question->data->q_protection ){
					set_cookie(array(
						'name' => 'sp_' . $question->id,
						'value' => TRUE,
						// На месяц
						'expire' => 3600*24*30
					));
				}
				
				// Учитываем голоса
				foreach( $_POST['a_id'] as $a_id ){
					$answer = new sp_answer($a_id);
					$answer->inc();
				}
				
				// Запишем логи
				sp_write_logs();
				
				$question->update(array(
					// +1 проголосовавший 
					'q_totalvoters' => $question->data->q_totalvoters + 1,
					// + добавляем голоса 
					'q_totalvotes' => $question->data->q_totalvotes + count($_POST['a_id'])
				));
				
				if( $question->get() ){
					$return['error_code'] = 0;
					$return['error_description'] = '';
					$return['resp'] = $question->results();
				}
				else{
					$return['error_description'] = 'Проблема с загрузкой результатов голосования';
				}
			}
			else{
				$return['error_description'] = 'Вы уже голосовали';
			}
		}
		else{
			$return['error_description'] = 'Голосования не существует';
		}
	}
	
	echo json_encode($return);	
	
	function sp_write_logs(){
		global $MSO;
		
		$CI = &get_instance();
		
		$host = @gethostbyaddr($_SERVER['REMOTE_ADDR']);
		if( empty($host) ) $host = $_SERVER['REMOTE_ADDR'];
		
		$ip = ip2long($_SERVER['REMOTE_ADDR']);
		
		foreach( $_POST['a_id'] as $a_id ){
			
			$CI->db->insert('sp_logs',array(
				'l_qid' 		=> $_POST['q_id'],
				'l_aid' 		=> $a_id,
				'l_ip'			=> $ip,
				'l_host'		=> $host,
				'l_timestamp'	=> gmmktime(),
				'l_userid'		=> is_login() ? $MSO->data['session']['users_id'] : 0,
				'l_user'		=> is_login() ? $MSO->data['session']['users_login'] : '-'
			));
		}
	}	
	
?>