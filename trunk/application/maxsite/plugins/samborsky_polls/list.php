<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<h1>Список голосований</h1>
<?php

	$CI = &get_instance();
	
	$mode = mso_segment(4);
	$mode_id = mso_segment(5);
	
	// Удаляем
	if( 'delete' == $mode && is_numeric($mode_id) ){
		
		$CI = &get_instance();
		$CI->db->delete('sp_answers',array('a_qid' => $mode_id));
		$CI->db->delete('sp_questions',array('q_id' => $mode_id));
	}

	// Пагинация
	$CI->load->library('pagination');

	$config['base_url'] = 	$MSO->config['site_url'] . 'admin/samborsky_polls/list/';
	$config['total_rows'] = $CI->db->count_all('sp_questions');
	$config['per_page'] = '50';

	$CI->pagination->initialize($config);
	
	// Выборка
	$CI->db->select('*');
	
	$page = mso_segment(5);
	if( is_numeric($page) ){
		// TODO: Исправить пагинацию
		$CI->db->limit($config['per_page'],0);
	}
	else{
		$CI->db->limit($config['per_page'],0);
	}
	
	$query = $CI->db->order_by('q_id','desc')->get('sp_questions');
	
	if( $query->num_rows() ){
		
		$CI->load->library('table');
		$CI->table->set_template(array(
			'table_open'  => '<table border="0" cellpadding="0" cellspacing="0" class="samborsky_polls_table">',
			'heading_cell_start'  => '<th valign="top">',
		)); 
		$CI->table->set_heading('ID','Вопрос','<div align="right">Проголосовало<br>чел.</div>','<div align="right">Сумма<br>голосов</div>','Статус','Логи','Изменить','Удалить');
		
		foreach( $query->result() as $row ){
			
			$edit_url = $MSO->config['site_url'] . 'admin/samborsky_polls/manage/' . $row->q_id;
			$delete_url = $MSO->config['site_url'] . 'admin/samborsky_polls/list/delete/' . $row->q_id;
			$logs_url = $MSO->config['site_url'] . 'admin/samborsky_polls/logs/' . $row->q_id;
			
			$status = $row->q_active ? 'Активно' : 'Закрыто';
			
			$CI->table->add_row(
				$row->q_id,
				stripslashes($row->q_question),
				'<div align="right">' . number_format($row->q_totalvoters,0,' ',' ') . '</div>',
				'<div align="right">' . number_format($row->q_totalvotes,0,' ',' ') . '</div>',
				$status,
				"<a href='{$logs_url}'>Логи</a>",
				"<a href='{$edit_url}'>Изменить</a>",
				"<a href='".$delete_url."' onclick=\"return confirm('Удалить? Уверены?');\">Удалить</a>"
			);
		}
		
		echo $CI->table->generate(); 
	}
	
	echo $CI->pagination->create_links();	

?>
