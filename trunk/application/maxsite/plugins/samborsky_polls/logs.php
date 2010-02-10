<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<h1>Логи</h1>

<?php

	$q_id = mso_segment(4);
	if( is_numeric($q_id) ){
		
		$CI = &get_instance();
		
		// Получаем название голосования
		$query = $CI->db->select('q_question')->where('q_id',$q_id)->limit(1)->get('sp_questions');
		if( $query->num_rows() ){
		
			$row = $query->row();
			$edit_url = $MSO->config['site_url'] . 'admin/samborsky_polls/manage/' . $q_id;
						
			echo '<h2>Голосование: <a href="',$edit_url,'">',$row->q_question,'</a></h2>';
			
			$CI->db->select('sp_logs.*,sp_answers.a_answer');
			$CI->db->join('sp_answers','sp_answers.a_id = sp_logs.l_aid','left');
			$CI->db->where('l_qid',$q_id);
			$query = $CI->db->order_by('l_id','desc')->get('sp_logs');
			
			if( $query->num_rows() ){
				
				$CI->load->library('table');
				$CI->table->clear();
				
				$tmpl = array ( 'table_open'  => '<table border="0" cellpadding="0" cellspacing="0" class="samborsky_polls_table">' );
				$CI->table->set_template($tmpl); 
				$CI->table->set_heading('ID','IP','Хост','Дата','Логин','Ответ');
				
				foreach( $query->result() as $row ){
					
					$CI->table->add_row(
						$row->l_id,
						long2ip($row->l_ip),
						$row->l_host,
						date('m/d/Y H:m:s',$row->l_timestamp),
						$row->l_user,
						$row->a_answer
					);
				}
				
				echo $CI->table->generate(); 
				
			}
		}
	}

?>