<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://maxsite.org/
 */

// обновление с 0.18 до 0.19

	if (!$this->db->field_exists('page_rating_count', 'page'))
	{
		$this->load->dbforge();
		$fields = array(
						'page_rating_count' => array(
									 'type' => 'bigint',
									 'constraint' => 20,
									 'default' => 0,
									 'null' => FALSE,
							  )
				);
		$this->dbforge->add_column('page', $fields);
		
		die ('Обновление выполнено!');
	} 
	else die ('Обновление не требуется!');


?>