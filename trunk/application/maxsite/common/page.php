<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Основные функции MaxSite CMS
 * (с) http://maxsite.org/
 * Функции для страниц
 */

# данную переменную $page мы объявляем как глобальную - в ней содержится массив 
# текущей страницы 
global $page;

# главная страница - home
function _mso_sql_build_home($r, &$pag)
{
	$CI = & get_instance();

	$offset = 0;
	
	if ($r['cat_id']) $cat_id = mso_explode($r['cat_id']);
	else $cat_id = false;
	
	if ($r['pagination'])
	{
		# пагинация
		# для неё нужно при том же запросе указываем общее кол-во записей и кол-во на страницу
		# сама пагинация выводится отдельным плагином
		# запрос один в один, кроме limit и юзеров
		$CI->db->select('page.page_id');
		$CI->db->from('page');
		$CI->db->where('page.page_status', 'publish');
		if ($r['type']) $CI->db->where('page_type.page_type_name', $r['type']);
		
		if ($r['page_id']) $CI->db->where('page.page_id', $r['page_id']);
		
		$CI->db->join('page_type', 'page_type.page_type_id = page.page_type_id');
		
		if ($cat_id) // указаны рубрики
		{
			$CI->db->join('cat2obj', 'cat2obj.page_id = page.page_id', 'left');
			$CI->db->join('category', 'cat2obj.category_id = category.category_id');
			$CI->db->where_in('category.category_id', $cat_id);
		}
		
		$CI->db->order_by('page_date_publish', 'desc');
		$query = $CI->db->get();
		
		$pag_row = $query->num_rows();
		if ($pag_row > 0)
		{
			$pag['maxcount'] = ceil($pag_row / $r['limit']); // всего станиц пагинации
			$pag['limit'] = $r['limit']; // записей на страницу

			$current_paged = mso_current_paged();
			if ($current_paged > $pag['maxcount']) $current_paged = $pag['maxcount'];
			
			$offset = $current_paged * $pag['limit'] - $pag['limit']; 
		}
		else
		{
			$pag = false;
		}
	}
	else 
		$pag = false;
	
	// теперь сами страницы
	
	if ($r['content'])
	{
		$CI->db->select('page.page_id, page_type_name, page_slug, page_title, page_date_publish, page_status, users_nik, page_content, page_view_count, page_rating, page_password, page_comment_allow, users_avatar_url, COUNT(comments_id) AS page_count_comments');
	}
	else
	{
		$CI->db->select('page.page_id, page_type_name, page_slug, page_title, "" AS page_content, page_date_publish, page_status, users_nik,  page_view_count, page_rating, page_password, page_comment_allow, users_avatar_url, COUNT(comments_id) AS page_count_comments');
	}
		
	$CI->db->from('page');
	
	if ($r['page_id']) $CI->db->where('page_id', $r['page_id']);
	
	$CI->db->where('page_status', 'publish');
	if ($r['type']) $CI->db->where('page_type_name', $r['type']);
	
	$CI->db->join('users', 'users.users_id = page.page_id_autor', 'left');
	$CI->db->join('page_type', 'page_type.page_type_id = page.page_type_id', 'left');
	$CI->db->join('comments', 'comments.comments_page_id = page.page_id AND comments_approved = 1', 'left');
	
	if ($cat_id) // указаны рубрики
	{
		$CI->db->join('cat2obj', 'cat2obj.page_id = page.page_id', 'left');
		$CI->db->join('category', 'cat2obj.category_id = category.category_id');
		$CI->db->where_in('category.category_id', $cat_id);
	}
	
	//$CI->db->where('comments.comments_approved', 1);
	
	$CI->db->order_by('page_date_publish', 'desc');
	
	$CI->db->group_by('page.page_id');
	$CI->db->group_by('comments_page_id');
	
	// $CI->db->distinct('page.page_id');
	
	
	if ($pag and $offset) $CI->db->limit($r['limit'], $offset);
		else $CI->db->limit($r['limit']);

}

# 
function _mso_sql_build_page($r, &$pag)
{
	$CI = & get_instance();
	
	$pag = false;
	
	$slug = mso_segment(2);
	
	// $page_status = 'publish'; // статус записи может быть сброшен, если это просматривает текущий автор и это не publish
	
	// если slug есть число, то выполняем поиск по id
	$id = (int) $slug;
	if ( (string) $slug != (string) $id ) $id = false; // slug не число
	
	$CI->db->select('page.page_id, page_type_name, page_slug, page_title, page_date_publish, page_status, users_nik, page_content, page_view_count, page_rating, page_password, page_comment_allow, users_avatar_url, page.page_id_autor');
	$CI->db->from('page');
	
	// if ($page_status) $CI->db->where('page_status', $page_status);
	
	// $CI->db->where('page_type_name', 'blog');
	if ($r['type']) $CI->db->where('page_type_name', $r['type']);
	
	if ($id)
		$CI->db->where('page_id', $id);
	else 
		$CI->db->where('page_slug', $slug);
		
	$CI->db->join('users', 'users.users_id = page.page_id_autor');
	$CI->db->join('page_type', 'page_type.page_type_id = page.page_type_id');
	$CI->db->limit(1);
}


# рубрики
function _mso_sql_build_category($r, &$pag)
{
	$CI = & get_instance();
	
	$slug = mso_segment(2);
	
	// если slug есть число, то выполняем поиск по id
	$id = (int) $slug;
	if ( (string) $slug != (string) $id ) $id = false; // slug не число
	
	$offset = 0;

	if ($r['pagination'])
	{
		# пагинация
		# для неё нужно при том же запросе указываем общее кол-во записей и кол-во на страницу
		# сама пагинация выводится отдельным плагином
		# запрос один в один, кроме limit и юзеров
		$CI->db->select('page.page_id');
		$CI->db->from('page');
		$CI->db->where('page_status', 'publish');
		//$CI->db->where('page_type_name', 'blog');
		if ($r['type']) $CI->db->where('page_type_name', $r['type']);
		$CI->db->join('page_type', 'page_type.page_type_id = page.page_type_id');
		$CI->db->join('cat2obj', 'cat2obj.page_id = page.page_id');
		$CI->db->join('category', 'cat2obj.category_id = category.category_id');
		
		if ($id) $CI->db->where('category.category_id', $id);
			else $CI->db->where('category.category_slug', $slug);

		$query = $CI->db->get();
		
		$pag_row = $query->num_rows();
		
		if ($pag_row > 0)
		{
			$pag['maxcount'] = ceil($pag_row / $r['limit']); // всего станиц пагинации
			$pag['limit'] = $r['limit']; // записей на страницу

			$current_paged = mso_current_paged();
			if ($current_paged > $pag['maxcount']) $current_paged = $pag['maxcount'];
			
			$offset = $current_paged * $pag['limit'] - $pag['limit']; 
		}
		else
		{
			$pag = false;
		}
	}
	else 
		$pag = false;
	
	// теперь сами страницы
	if ($r['content'])
		$CI->db->select('page.page_id, page_type_name, page_slug, page_title, page_date_publish, page_status, users_nik, page_content, page_view_count, page_rating, page_password, page_comment_allow, users_avatar_url, category.category_name, COUNT(comments_id) AS page_count_comments');
	else
		$CI->db->select('page.page_id, page_type_name, page_slug, page_title, "" AS page_content, page_date_publish, page_status, users_nik, page_view_count, page_rating, page_password, page_comment_allow, users_avatar_url, category.category_name, COUNT(comments_id) AS page_count_comments');
		
	$CI->db->from('page');
	$CI->db->where('page_status', 'publish');
	//$CI->db->where('page_type_name', 'blog');
	if ($r['type']) $CI->db->where('page_type_name', $r['type']);
	$CI->db->join('users', 'users.users_id = page.page_id_autor');
	$CI->db->join('page_type', 'page_type.page_type_id = page.page_type_id');
	
	$CI->db->join('cat2obj', 'cat2obj.page_id = page.page_id');
	$CI->db->join('category', 'cat2obj.category_id = category.category_id');
	$CI->db->join('comments', 'comments.comments_page_id = page.page_id AND comments_approved = 1', 'left');

	
	if ($id)
		$CI->db->where('category.category_id', $id);
	else 
		$CI->db->where('category.category_slug', $slug);

	$CI->db->order_by('page_date_publish', 'desc');
	
	$CI->db->group_by('page.page_id');
	$CI->db->group_by('comments_page_id');
		
	if ($pag and $offset) $CI->db->limit($r['limit'], $offset);
		else $CI->db->limit($r['limit']);
	
}


# страница меток
function _mso_sql_build_tag($r, &$pag)
{
	$CI = & get_instance();
	
	$slug = mso_segment(2);

	$offset = 0;

	if ($r['pagination'])
	{
		# пагинация
		# для неё нужно при том же запросе указываем общее кол-во записей и кол-во на страницу
		# сама пагинация выводится отдельным плагином
		# запрос один в один, кроме limit и юзеров
		$CI->db->select('page.page_id');
		$CI->db->from('page');
		$CI->db->where('page_status', 'publish');
		// $CI->db->where('page_type_name', 'blog');
		if ($r['type']) $CI->db->where('page_type_name', $r['type']);
		$CI->db->join('page_type', 'page_type.page_type_id = page.page_type_id');
		$CI->db->join('meta', 'meta.meta_id_obj = page.page_id');
		$CI->db->where('meta_key', 'tags');
		$CI->db->where('meta_table', 'page');
		$CI->db->where('meta_value', $slug);

		$query = $CI->db->get();
		
		$pag_row = $query->num_rows();
		
		if ($pag_row > 0)
		{
			$pag['maxcount'] = ceil($pag_row / $r['limit']); // всего станиц пагинации
			$pag['limit'] = $r['limit']; // записей на страницу

			$current_paged = mso_current_paged();
			if ($current_paged > $pag['maxcount']) $current_paged = $pag['maxcount'];
			
			$offset = $current_paged * $pag['limit'] - $pag['limit']; 
		}
		else
		{
			$pag = false;
		}
	}
	else 
		$pag = false;
	
	// теперь сами страницы
	if ($r['content'])
		$CI->db->select('page.page_id, page_type_name, page_slug, page_title, page_date_publish, page_status, users_nik, page_content, page_view_count, page_rating, page_password, page_comment_allow, users_avatar_url, meta.meta_value AS tag_name, COUNT(comments_id) AS page_count_comments');
	else
		$CI->db->select('page.page_id, page_type_name, page_slug, page_title, "" AS page_content, page_date_publish, page_status, users_nik, page_content, page_view_count, page_rating, page_password, page_comment_allow, users_avatar_url, meta.meta_value AS tag_name, COUNT(comments_id) AS page_count_comments');
	
	
	$CI->db->from('page');
	$CI->db->where('page_status', 'publish');
	// $CI->db->where('page_type_name', 'blog');
	if ($r['type']) $CI->db->where('page_type_name', $r['type']);
	$CI->db->join('users', 'users.users_id = page.page_id_autor');
	$CI->db->join('page_type', 'page_type.page_type_id = page.page_type_id');
	$CI->db->join('meta', 'meta.meta_id_obj = page.page_id');
	$CI->db->join('comments', 'comments.comments_page_id = page.page_id AND comments_approved = 1', 'left');
	
	$CI->db->where('meta_key', 'tags');
	$CI->db->where('meta_table', 'page');
	$CI->db->where('meta_value', $slug);
	$CI->db->order_by('page_date_publish', 'desc');
	
	$CI->db->group_by('page.page_id');
	$CI->db->group_by('comments_page_id');
	
	if ($pag and $offset) $CI->db->limit($r['limit'], $offset);
		else $CI->db->limit($r['limit']);
}



# архивы по датам
function _mso_sql_build_archive($r, &$pag)
{
	$CI = & get_instance();
	
	$offset = 0;
	
	$year = (int) mso_segment(2);
	if ($year>date('Y', mktime()) or $year<2008) $year = date('Y', mktime());

	$month = (int) mso_segment(3);
	if ($month>12 or $month<1) $month = date('m', mktime());
	
	$day = (int) mso_segment(4);
	
	if ($day)
	{
		if ($day>31 or $day<1) $day = 1;
		
		$dmax = get_total_days($month, $year);
		if ( $day>$dmax ) $day = $dmax;
	}
	
	// echo $year . $month . $day;

	if ($r['pagination'])
	{
		# пагинация
		# для неё нужно при том же запросе указываем общее кол-во записей и кол-во на страницу
		# сама пагинация выводится отдельным плагином
		# запрос один в один, кроме limit и юзеров
		$CI->db->select('page_id');
		$CI->db->from('page');
		$CI->db->where('page_status', 'publish');
		if ($r['type'])
		{
			$CI->db->where('page_type_name', $r['type']);
		}
		if ($day)
		{
			$CI->db->where('page_date_publish >= ', mso_date_convert_to_mysql($year, $month, $day));
			$CI->db->where('page_date_publish <= ', mso_date_convert_to_mysql($year, $month, $day, 23, 59, 59));
		}
		else
		{
			$CI->db->where('page_date_publish >= ', mso_date_convert_to_mysql($year, $month));
			$CI->db->where('page_date_publish <= ', mso_date_convert_to_mysql($year, $month+1));	
		}
		
		$CI->db->join('page_type', 'page_type.page_type_id = page.page_type_id');
		$CI->db->order_by('page_date_publish', 'desc');
		$query = $CI->db->get();
		
		$pag_row = $query->num_rows();
		if ($pag_row > 0)
		{
			$pag['maxcount'] = ceil($pag_row / $r['limit']); // всего станиц пагинации
			$pag['limit'] = $r['limit']; // записей на страницу

			$current_paged = mso_current_paged();
			if ($current_paged > $pag['maxcount']) $current_paged = $pag['maxcount'];
			
			$offset = $current_paged * $pag['limit'] - $pag['limit']; 
		}
		else
		{
			$pag = false;
		}
	}
	else 
		$pag = false;
	
	// теперь сами страницы
	$CI->db->select('page.page_id, page_type_name, page_slug, page_title, page_date_publish, page_status, users_nik, page_content, page_view_count, page_rating, page_password, page_comment_allow, users_avatar_url, COUNT(comments_id) AS page_count_comments');
	$CI->db->from('page');
	$CI->db->where('page_status', 'publish');
	
	if ($day)
	{
		$CI->db->where('page_date_publish >= ', mso_date_convert_to_mysql($year, $month, $day));
		$CI->db->where('page_date_publish <= ', mso_date_convert_to_mysql($year, $month, $day, 23, 59, 59));
	}
	else
	{
		$CI->db->where('page_date_publish >= ', mso_date_convert_to_mysql($year, $month));
		$CI->db->where('page_date_publish <= ', mso_date_convert_to_mysql($year, $month+1));	
	}
	
	if ($r['type']) $CI->db->where('page_type_name', $r['type']);
	$CI->db->join('users', 'users.users_id = page.page_id_autor');
	$CI->db->join('page_type', 'page_type.page_type_id = page.page_type_id');
	$CI->db->join('comments', 'comments.comments_page_id = page.page_id AND comments_approved = 1', 'left');
	
	$CI->db->order_by('page_date_publish', 'desc');
	
	$CI->db->group_by('page.page_id');
	$CI->db->group_by('comments_page_id');
	
	if ($pag and $offset) $CI->db->limit($r['limit'], $offset);
		else $CI->db->limit($r['limit']);

}


# страница поиска
function _mso_sql_build_search($r, &$pag)
{
	$CI = & get_instance();
	
	$search = mso_segment(2);
	$search = mso_strip(strip_tags($search));
	
	$offset = 0;

	if ($r['pagination'])
	{
		# пагинация
		# для неё нужно при том же запросе указываем общее кол-во записей и кол-во на страницу
		# сама пагинация выводится отдельным плагином
		# запрос один в один, кроме limit и юзеров
		$CI->db->select('page_id');
		$CI->db->from('page');
		$CI->db->where('page_status', 'publish');
		if ($r['type'])
		{
			$CI->db->where('page_type_name', $r['type']);
		}
		
		$CI->db->like('page_content', $search); 
		$CI->db->or_like('page_title', $search); 
		
		$CI->db->join('page_type', 'page_type.page_type_id = page.page_type_id');
		$CI->db->order_by('page_date_publish', 'desc');
		$query = $CI->db->get();
		
		$pag_row = $query->num_rows();
		if ($pag_row > 0)
		{
			$pag['maxcount'] = ceil($pag_row / $r['limit']); // всего станиц пагинации
			$pag['limit'] = $r['limit']; // записей на страницу

			$current_paged = mso_current_paged();
			if ($current_paged > $pag['maxcount']) $current_paged = $pag['maxcount'];
			
			$offset = $current_paged * $pag['limit'] - $pag['limit']; 
		}
		else
		{
			$pag = false;
		}
	}
	else 
		$pag = false;
	
	// теперь сами страницы
	
	$CI->db->select('page.page_id, page_type_name, page_slug, page_title, page_date_publish, page_status, users_nik, page_content, page_view_count, page_rating, page_password, page_comment_allow, users_avatar_url, COUNT(comments_id) AS page_count_comments');
	
		
	$CI->db->from('page');
	
	$CI->db->where('page_status', 'publish');
	
	$CI->db->like('page_content', $search); 
	$CI->db->or_like('page_title', $search);
	
	if ($r['type']) $CI->db->where('page_type_name', $r['type']);
	
	$CI->db->join('users', 'users.users_id = page.page_id_autor', 'left');
	$CI->db->join('page_type', 'page_type.page_type_id = page.page_type_id', 'left');
	$CI->db->join('comments', 'comments.comments_page_id = page.page_id AND comments_approved = 1', 'left');
	
	$CI->db->order_by('page_date_publish', 'desc');
	
	$CI->db->group_by('page.page_id');
	$CI->db->group_by('comments_page_id');

	if ($pag and $offset) $CI->db->limit($r['limit'], $offset);
		else $CI->db->limit($r['limit']);
}


# страницы автора
function _mso_sql_build_author($r, &$pag)
{
	$CI = & get_instance();
	_mso_sql_build_home($r, &$pag);
}


# страницы ссылок
function _mso_sql_build_link($r, &$pag)
{
	$CI = & get_instance();
	_mso_sql_build_home($r, &$pag);
}





# функция получения выборки страниц
function mso_get_pages($r = array(), &$pag)
{
	global $MSO;
	
	if ( !isset($r['limit']) )			$r['limit'] = 7;
	else
	{
		// проверим входящий лимит
		$r['limit'] = (int) $r['limit'];
		$r['limit'] = abs( $r['limit'] );
		if (!$r['limit']) $r['limit'] = 7;
	}
	
	if ( !isset($r['cut']) )			$r['cut'] = 'Далее';
	if ( !isset($r['cat_order']) )		$r['cat_order'] = 'category_name';
	if ( !isset($r['cat_order_asc']) )	$r['cat_order_asc'] = 'asc';
	if ( !isset($r['pagination']) )		$r['pagination'] = true;
	if ( !isset($r['content']) )		$r['content'] = true;
	if ( !isset($r['type']) )			$r['type'] = 'blog'; // если false - то все, иначе blog или static
	if ( !isset($r['page_id']) )		$r['page_id'] = 0; // если 0, значить все страницы - только для главной
	if ( !isset($r['cat_id']) )			$r['cat_id'] = 0; // если 0, значить все рубрики - только для главной
	
	if ($r['page_id']) $r['type'] = false; // если указан номер, то тип страницы сбрасываем
	
	$CI = & get_instance();
	
	# для каждого типа страниц строится свой sql-запрос
	# мы оформляем его в $CI, а здесь только выполняем $CI->db->get();
	if ( is_type('home') ) _mso_sql_build_home($r, &$pag);
	elseif ( is_type('page') ) _mso_sql_build_page($r, &$pag);
	elseif ( is_type('category') ) _mso_sql_build_category($r, &$pag);
	elseif ( is_type('tag') ) _mso_sql_build_tag($r, &$pag);
	elseif ( is_type('archive') ) _mso_sql_build_archive($r, &$pag);
	elseif ( is_type('search') ) _mso_sql_build_search($r, &$pag);
	elseif ( is_type('author') ) _mso_sql_build_author($r, &$pag);
	elseif ( is_type('link') ) _mso_sql_build_link($r, &$pag);
	// elseif ($MSO->data['is_feed']) _mso_sql_build_home($r, &$pag);
	else return array();
	
	
	// сам запрос и его обработка
	$query = $CI->db->get();
	
	if ($query->num_rows() > 0)	
	{	
		$pages = $query->result_array();
		
		
		if (is_type('page'))
		{
			// проверяем статус публикации - если page_status <> publish то смотрим автора и саравниваем с текеущим юзером
			$page_status = $pages[0]['page_status']; // в page - всегда одна запись
			
			if ($page_status<>'publish') // не опубликовано
			{
				if ( isset($MSO->data['session']['users_id']) ) // залогинен
				{
					if ( $pages[0]['page_id_autor'] <> $MSO->data['session']['users_id'] ) return array();
					else $pages[0]['page_title'] .= ' (черновик)';
				}
				else return array(); // не залогинен
			}
		}

		// массив всех page_id
		$all_page_id = array();

		foreach ($pages as $key=>$page)
		{
			$all_page_id[] = $page['page_id'];
			
			// $content = mso_hook('content', $page['page_content']);
			$content = $page['page_content'];
			$content = mso_hook('do_content', $content);
			$content = mso_auto_tag($content);
			$content = mso_balance_tags($content);
			$content = mso_hook('content', $content);
			$content = mso_auto_tag($content);
			$content = mso_balance_tags($content);
			
			
			// pr($content, 1);
			// pr($page['page_content']);
			// echo mso_text_to_html($page['page_content']);
			
			if ( preg_match('/\[cut(.*?)?\]/', $content, $matches) ) 
					$content = explode($matches[0], $content, 2);
			else 
					$content = array($content);
		
			$output = $content[0]; 
			if ( count($content) > 1 ) 
			{
				// ссылка на «далее...»
				if ($r['cut'])
				{
					$output .= mso_page_title( $page['page_slug'], $r['cut'], 
								$do = '<span class="cut">', $posle = '</span>', true, false );
				}
				else
				{
					$output .= mso_balance_tags($content[1]);
				}
				
				$output = mso_balance_tags($output);
				
			}

			$pages[$key]['page_content'] = $output;
			
			$pages[$key]['page_categories'] = array();
			$pages[$key]['page_tags'] = array();
		}
		
		// теперь одним запросом получим все рубрики каждой записи
		
		$CI->db->select('page_id, category.category_id, category.category_name');
		$CI->db->where_in('page_id', $all_page_id);
		$CI->db->order_by('category.' . $r['cat_order'], $r['cat_order_asc']); // сортировка рубрик
		$CI->db->from('cat2obj');
		$CI->db->join('category', 'cat2obj.category_id = category.category_id');
		
		$query = $CI->db->get();
		$cat = $query->result_array();
		
		//pr($cat);
		
		// переместим все в массив page_id[] = category_id
		$page_cat = array();
		foreach ($cat as $key=>$val)
		{
			$page_cat[$val['page_id']][] = $val['category_id'];
		}
		
		// pr($page_cat);
		
		// по этому же принципу получаем все метки
		$CI->db->select('meta_id_obj, meta_key, meta_value');
		// $CI->db->where( array (	'meta_key' => 'tags', 'meta_table' => 'page' ) );
		$CI->db->where( array (	'meta_table' => 'page' ) );
		$CI->db->where_in('meta_id_obj', $all_page_id);
		$CI->db->order_by('meta_value');
		$query = $CI->db->get('meta');
		$meta = $query->result_array();
		
		// pr($meta);
		
		// переместим все в массив page_id[] = category_id
		$page_meta = array();
		foreach ($meta as $key=>$val)
		{
			$page_meta[$val['meta_id_obj']][$val['meta_key']][] = $val['meta_value'];
			
			
			// $page_meta[$val['meta_id_obj']][] = $val['meta_value'];
			// $page_meta[$val['meta_id_obj']] = array_unique($page_meta[$val['meta_id_obj']]);
		}
		
		// pr($page_meta);
		
		// добавим в массив pages полученную информацию по меткам и рубрикам
		foreach ($pages as $key=>$val)
		{
			// рубрики 
			if ( isset($page_cat[$val['page_id']]) and $page_cat[$val['page_id']] )
				$pages[$key]['page_categories'] = $page_cat[$val['page_id']];
			
			// метки отдельно как page_tags
			if ( isset($page_meta[ $val['page_id'] ]['tags'] ) and $page_meta[$val['page_id']]['tags'] )
				$pages[$key]['page_tags'] = $page_meta[$val['page_id']]['tags'];
			
			// остальные мета отдельно в page_meta
			if ( isset($page_meta[$val['page_id']]) and $page_meta[$val['page_id']] )
				$pages[$key]['page_meta'] = $page_meta[$val['page_id']];	
		}
	}
	else 
		$pages = array();

	return $pages;
}



# получить ссылку на редактирование страницы
function mso_page_edit_link($id = 0, $title = 'Редактировать', $do = '', $posle = '', $echo = true)
{
	global $MSO;
	
	$id = (int) $id;
	if (!$id) return '';
	
	if (is_login())	
	{
		if ($echo)
			echo $do . '<a href="' . $MSO->config['site_admin_url'] . 'page_edit/' . $id . '">' . $title . '</a>' . $posle;
		else
			return $do . '<a href="' . $MSO->config['site_admin_url'] . 'page_edit/' . $id . '">' . $title . '</a>' . $posle;
	}
}


# получить ссылки на рубрики указанной страницы
function  mso_page_cat_link($cat = array(), $sep = ', ', $do = '', $posle = '', $echo = true)
{
	global $MSO;
	
	if (!$cat) return '';
	
	// получим массив рубрик из mso_cat_array_single
	$all_cat = mso_cat_array_single();
	
	$out = '';
	foreach ($cat as $id)
		$out .=  '<a href="' 
					. $MSO->config['site_url'] 
					. 'category/' 
					. $all_cat[$id]['category_slug'] 
					. '">' 
					. $all_cat[$id]['category_name'] 
					. '</a>   ';
	
	$out = trim($out);
	$out = str_replace('   ', $sep, $out);
	
	if ($echo) echo $do . $out . $posle;
		else return $do . $out . $posle;
}

# получить ссылки на метки указанной страницы
function mso_page_tag_link($tags = array(), $sep = ', ', $do = '', $posle = '', $echo = true)
{
	global $MSO;
	
	if (!$tags) return '';
	
	// получим массив рубрик из mso_cat_array_single
	$out = '';
	
	foreach ($tags as $tag)
	{
		$out .=  '<a href="' 
					. $MSO->config['site_url'] 
					. 'tag/' 
					. $tag 
					. '">' 
					. $tag 
					. '</a>   ';
	}
	
	$out = trim($out);
	$out = str_replace('   ', $sep, $out);
	
	if ($echo) echo $do . $out . $posle;
		else return $do . $out . $posle;
}

# получение даты
function mso_page_date($date = 0, $format = 'Y-m-d H:i:s', $do = '', $posle = '', $echo = true)
{
	if (!$date) return '';
	
	// учитываем смещение времени time_zone
	$out = mso_date_convert($format, $date, true);

	if ($echo) echo $do . $out . $posle;
		else return $do . $out . $posle;
}


# формирование титла или ссылки на страницу
function mso_page_title($page_slug = '', $page_title = 'no title', $do = '<h1>', $posle = '</h1>', $link = true, $echo = true)
{
	global $MSO;
	
	if (!$page_slug) return '';
	
	if ($link)
		$out = '<a href="' . $MSO->config['site_url'] . 'page/' . mso_slug($page_slug) . '">' . $page_title . '</a>';
	else
		$out = $page_title;
	
	if ($echo) echo $do . $out . $posle;
		else return $do . $out . $posle;
}


# формирование ссылки для rss страницы
function mso_page_feed($page_slug = '', $page_title = 'Подписаться', $do = '<p>', $posle = '</p>', $link = true, $echo = true)
{
	global $MSO;
	
	if (!$page_slug) return '';
	
	if ($link)
		$out = '<a href="' . $MSO->config['site_url'] . 'page/' . mso_slug($page_slug) . '/feed">' . $page_title . '</a>';
	else
		$out = $page_title;
	
	if ($echo) echo $do . $out . $posle;
		else return $do . $out . $posle;
}

# формирование ссылки для rss страницы
function mso_page_content($page_content = '')
{
	mso_hook('content_start'); # хук на начало блока
	echo mso_hook('content_content', $page_content);
	mso_hook('content_end'); # хук на конец блока
}



# формирование ссылки «обсудить» если разрешен комментарий
function mso_page_comments_link($page_comment_allow = true, $page_slug = '', $title = 'Обсудить', $do = '', $posle = '', $echo = true)
{
	global $MSO;
	
	if (is_array($page_comment_allow)) // первый элемент - массив, значит принимаем его значения - остальное игнорируем
	{
		$def = array(
			'page_comment_allow' => true, // разрешены комментарии?
			'page_slug' => '', // короткая ссылка страницы
			'title' => 'Обсудить', // титул, если есть ссылка
			'title_no_link' => 'Посмотреть комментарии', // титул если ссылки нет
			'do' => '', // текст ДО
			'posle' => '', // текст ПОСЛЕ
			'echo' => true, // выводить?
			'page_count_comments' => 0 // колво комментов
		);
		$r = array_merge($def, $page_comment_allow); // объединяем дефолт с входящим
		
		if (!$r['page_slug']) return ''; // не указан slug - выходим
		
		// pr($r);
		
		$out = '';
		
		if (!$r['page_comment_allow']) // коментирование запрещено
		{
			if ( $r['page_count_comments'] ) // но если уже есть комментарии, то выводи строчку title_no_link
			{
				$out = $r['do'] . '<a href="' . $MSO->config['site_url'] . 'page/' 
						. mso_slug($r['page_slug']) . '#comments">' . $r['title_no_link'] . '</a>' . $r['posle'];
			}			
		}
		else 
			$out = $r['do'] . '<a href="' . $MSO->config['site_url'] . 'page/' 
						. mso_slug($r['page_slug']) . '#comments">' . $r['title'] . '</a>' . $r['posle'];
		
		
		if ($r['echo']) echo $out;
			else return $out;	
	}
	else // обычные параметры
	{
		if (!$page_slug) return '';
		if (!$page_comment_allow) return '';
		
		$out = $do . '<a href="' . $MSO->config['site_url'] . 'page/' . mso_slug($page_slug) . '#comments">' . $title . '</a>' . $posle;
		if ($echo) echo $out;
			else return $out;		
	}
}




# функция из Calendar.php
if ( !function_exists('get_total_days') ) 
{
	function get_total_days($month, $year)
	{
		$days_in_month	= array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
		if ($month < 1 OR $month > 12) return 0;
		if ($month == 2)
		{
			if ($year % 400 == 0 OR ($year % 4 == 0 AND $year % 100 != 0))	return 29;
		}
		return $days_in_month[$month - 1];
	}
}


?>