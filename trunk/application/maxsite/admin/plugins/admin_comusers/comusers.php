<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
	
	mso_cur_dir_lang('admin');
	
	$CI = & get_instance();
?>

<h1><?= t('Комментаторы') ?></h1>
<p class="info"><?= t('Список комментаторов сайта') ?></p>

<?php
	
	$CI->load->library('table');
	$tmpl = array (
				'table_open'		  => '<table class="page tablesorter" border="0" width="99%" id="pagetable">',
				'row_alt_start'		  => '<tr class="alt">',
				'cell_alt_start'	  => '<td class="alt">',
				'heading_row_start' 	=> NR . '<thead><tr>',
				'heading_row_end' 		=> '</tr></thead>' . NR,
				'heading_cell_start'	=> '<th style="cursor: pointer;">',
				'heading_cell_end'		=> '</th>',
		  );
		  
	$CI->table->set_template($tmpl); // шаблон таблицы
	$CI->table->set_heading('ID', t('Ник', 'admin'), t('Актив.', 'admin'), t('Кол.', 'admin'), t('Последний вход', 'admin'),  t('E-mail', 'admin'), t('Сайт', 'admin'));
	
	
	// для пагинации нам нужно знать общее количество записей
	// только после этого выполняем запрос 
	
	$pag = array(); // для пагинации
	$pag['limit'] = 30; // записей на страницу
	$offset = 0;
	
	$CI->db->select('comusers_id');
	$CI->db->from('comusers');
	$query = $CI->db->get();
	$pag_row = $query->num_rows();
	
	if ($pag_row > 0)
	{
		$pag['maxcount'] = ceil($pag_row / $pag['limit']); // всего станиц пагинации
		$current_paged = mso_current_paged();
		if ($current_paged > $pag['maxcount']) $current_paged = $pag['maxcount'];
		$offset = $current_paged * $pag['limit'] - $pag['limit'];
	}
	else $pag = false;
	
	
	$CI->db->select('comusers_id, comusers_nik, comusers_email, comusers_url, comusers_activate_key, comusers_activate_string, comusers_date_registr, comusers_last_visit, comusers_count_comments');
	$CI->db->from('comusers');
	$CI->db->order_by('comusers_id');
	
	if ($pag) 
	{
		if ($pag and $offset) $CI->db->limit($pag['limit'], $offset);
			else $CI->db->limit($pag['limit']);
	}
	
	$query = $CI->db->get();
	
	$this_url = $MSO->config['site_admin_url'] . 'comusers';

	foreach ($query->result_array() as $row)
	{
		$id = $row['comusers_id'];
		$nik = $row['comusers_nik'];
		$email = $row['comusers_email'];
		$url = $row['comusers_url'];
		
		# не указан ник
		if (!$nik) $nik = '! Комментатор ' . $id;
		
		# отмечаем невыполненную активацию
		if ($row['comusers_activate_string'] != $row['comusers_activate_key'])
		{
			$activat = 'нет';
			$nik = '<span style="color: red" title="Активация не выполнена!">' . $nik . '</span>';
		}
		else $activat = '';
		
		$nik = '<a href="' . $this_url . '/edit/' . $id . '">' 
				. $nik . '</a> [<a href="' . getinfo('siteurl') . 'users/' . $id . '" target="_blank">Просмотр</a>]';
		
		if ($row['comusers_date_registr'] != $row['comusers_last_visit'])
			$date = '<span style="color: gray" title="Дата регистрации">' . $row['comusers_date_registr'] 
					. '</span><br />' . $row['comusers_last_visit'];
		else
			$date = $row['comusers_date_registr'];
		
		$CI->table->add_row($id, $nik, $activat, $row['comusers_count_comments'], $date, $email, $url);
	}

	mso_hook('pagination', $pag);
	echo '<br />'; // вывод навигации
	
	echo mso_load_jquery('jquery.tablesorter.js');
	
	echo '
	<script type="text/javascript">
	$(function() {
		$("table.tablesorter th").animate({opacity: 0.7});
		$("table.tablesorter th").hover(function(){ $(this).animate({opacity: 1}); }, function(){ $(this).animate({opacity: 0.7}); });
		$("#pagetable").tablesorter();
	});	
	</script>
	';
	
	echo $CI->table->generate(); // вывод подготовленной таблицы
	
	echo '<br />';
	mso_hook('pagination', $pag);

	
?>