<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

	$CI = & get_instance();
	
	require_once( getinfo('common_dir') . 'page.php' ); 			// функции страниц 
	// require_once( getinfo('common_dir') . 'category.php' ); 		// функции рубрик
	
	if ( $post = mso_check_post(array('f_session_id', 'f_submit', 'f_page_delete')) )
	{
		mso_checkreferer();
		
		// pr($post);
		
		$page_id = (int) $post['f_page_delete'];
		// проверим, чтобы это было число
		$page_id1 = (int) $page_id;
		if ( (string) $page_id != (string) $page_id1 ) $page_id = false; // ошибочный id
		
		if (!$page_id) // ошибка! 
		{
			echo '<div class="error">Ошибка обновления</div>';
		}
		else 
		{
			// проверим id, чтобы вообще такая страница была
			$CI->db->select('page_id');
			$CI->db->where(array('page_id'=>$page_id));
			$query = $CI->db->get('page');
			if ($query->num_rows() == 0) // нет такого
			{
				echo '<div class="error">Ошибочный номер страницы</div>';
			}
			else 
			{	// теперь можно удалять
				// при удалении страницы нужно сразу удалить её, рубрики и мета
				// потом будут еще и комментарии
				
				$CI->db->where( array('page_id'=>$page_id) );
				$CI->db->delete('cat2obj');
				
				$CI->db->where( array ('meta_id_obj' => $page_id, 'meta_table' => 'page') );
				$CI->db->delete('meta');
				
				$CI->db->where( array('page_id'=>$page_id) ); 
				$CI->db->delete('page');
				
				echo '<div class="update">Страница удалена</div>';
			}
		}
	}
	

?>
<h1>Страницы</h1>
<p class="info">Список всех страниц</p>

<?php

	$CI->load->library('table');
	$CI->load->helper('form');
	
	$tmpl = array (
				'table_open'		  => '<table class="page" border="0" width="99%">',
				'row_alt_start'		  => '<tr class="alt">',
				'cell_alt_start'	  => '<td class="alt">',
		  );
		  
	$CI->table->set_template($tmpl); // шаблон таблицы

	$CI->table->set_heading('ID','Тип', 'Заголовок', 'Дата', 'Статус', 'Автор', 'Действие');
	
	$par = array( 
			'limit' => 50, // колво записей на страницу
			'type' => false, // любой тип страниц
			'custom_type' => 'home', // запрос как в home
			'order_asc' => 'desc', // в обратном порядке
			'page_status' => false, // статус любой
			'date_now' => false, // любая дата
			'content'=> false, // без содержания
			);
	
	$pages = mso_get_pages($par, $pagination); // получим все - второй параметр нужен для сформированной пагинации

	$all_pages = array(); // сразу список всех страниц для формы удаления
	
	$this_url = getinfo('site_admin_url') . 'page_edit/';
	$view_url = getinfo('siteurl') . 'page/';
	$view_url_cat = getinfo('siteurl') . 'category/';
	$view_url_tag = getinfo('siteurl') . 'tag/';
		
	if ($pages) // есть страницы
	{ 	
		foreach ($pages as $page) // выводим в цикле
		{
			// pr($page);
			$act = '<a href="' . $this_url . $page['page_id'] . '">Изменить</a>';
			
			$all_pages[$page['page_id']] = $page['page_id'] . ' - ' . $page['page_title'] 
				. ' - ' . $page['page_date_publish'] . ' - ' . $page['page_status'];
			
			$cats = '';
			$tags = '';
			
			foreach ($page['page_categories_detail'] as $key => $val)
			{
				$cats .= '<a href="' . $view_url_cat . $page['page_categories_detail'][$key]['category_slug'] . '">'
					. $page['page_categories_detail'][$key]['category_name'] . '</a>  ';
			}
			
			$cats = str_replace('  ', ', ', trim($cats));
			
			foreach ($page['page_tags'] as $val)
			{
				$tags .= '<a href="' . $view_url_tag . $val . '">' . $val . '</a>  ';
			}			
			$tags = str_replace('  ', ', ', trim($tags));
			
			
			$title = '<a href="' . $view_url . $page['page_slug'] . '">' . $page['page_title'] . '</a>';
			
			if ($cats) $title .= '<br />Рубрика: ' . $cats;
			if ($tags) $title .= '<br />Метки: ' . $tags;
			
			$CI->table->add_row($page['page_id'], $page['page_type_name'], $title, 
					$page['page_date_publish'], $page['page_status'], $page['users_nik'], $act);
		}
	}
	
	echo $CI->table->generate(); // вывод подготовленной таблицы

	// добавляем форму для удаления записи
	$all_pages = form_dropdown('f_page_delete', $all_pages, -1, '');
	
	if (function_exists('pagination_go')) 
	{
		$pagination['type'] = '';
		$pagination['range'] = 10;
		echo '<br />' . pagination_go($pagination); // вывод навигации
	}
	
	echo '<form action="" method="post">' . mso_form_session('f_session_id');
	echo '<br /><br /><h2>Удалить страницу</h2>';
	echo $all_pages;
	echo ' <input type="submit" name="f_submit" value="  Удалить  " onClick="if(confirm(\'Удалить страницу?\')) {return true;} else {return false;}" >';
	echo '</form><br />';
	
	
?>