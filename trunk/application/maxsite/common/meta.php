<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Основные функции MaxSite CMS
 * (c) http://maxsite.org/
 * Функции для meta, включая метки
 */


# получаем все метки указанной страницы
function mso_get_tags_page($id = 0)
{
	$id = (int) $id;
	if (!$id) return array();
	
	$CI = & get_instance();
	
	$CI->db->select('meta_value');
	$CI->db->where( array (	'meta_key' => 'tags', 'meta_id_obj' => $id, 'meta_table' => 'page' ) );
	$CI->db->group_by('meta_value');
	$query = $CI->db->get('meta');
	
	if ($query->num_rows() > 0)
	{
		$tags = array();
		foreach ($query->result_array() as $row)
			$tags[] = $row['meta_value'];
	
		return $tags;
	}
	else return array();
}

# получаем все метки в массиве
function mso_get_all_tags_page()
{
	$CI = & get_instance();
	
	$CI->db->select('meta_value, COUNT(meta_value) AS meta_count');
	$CI->db->where( array (	'meta_key' => 'tags', 'meta_table' => 'page' ) );
	$CI->db->join('page', 'page.page_id = meta.meta_id_obj' );
	$CI->db->group_by('meta_value');
	$query = $CI->db->get('meta');
	
	// переделаем к виду [метка] = кол-во 
	if ($query->num_rows() > 0)
	{
		$tags = array();
		foreach ($query->result_array() as $row)
			$tags[$row['meta_value']] = $row['meta_count'];
	
		return $tags;
	}
	else return array();
}

?>