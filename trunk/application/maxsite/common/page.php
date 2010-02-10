<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Основные функции MaxSite CMS
 * (c) http://maxsite.org/
 * Функции для страниц
 */

# переменную $page мы объявляем как глобальную - в ней содержится массив 
# текущей страницы 
global $page;




# функция получения выборки страниц
function mso_get_pages($r = array(), &$pag)
{
	global $MSO;
	
	if ( !isset($r['limit']) )			$r['limit'] = 7; // сколько отдавать страниц 
	else
	{
		// проверим входящий лимит - он должен быть числом
		$r['limit'] = (int) $r['limit'];
		$r['limit'] = abs( $r['limit'] );
		if (!$r['limit']) $r['limit'] = 7; // что-то не то, заменяем на дефолт=7
	}

	if ( !isset($r['cut']) )			$r['cut'] = 'Далее'; // ссылка на [cut]
	if ( !isset($r['xcut']) )			$r['xcut'] = true; // для тех у кого нет cut, но есть xcut выводить после xcut
	
	if ( !isset($r['cat_order']) )		$r['cat_order'] = 'category_name'; // сортировка рубрик
	if ( !isset($r['cat_order_asc']) )	$r['cat_order_asc'] = 'asc'; // порядок рубрик
	if ( !isset($r['pagination']) )		$r['pagination'] = true; // использовать пагинацию
	if ( !isset($r['content']) )		$r['content'] = true; // получать весь текст
	if ( !isset($r['page_id']) )		$r['page_id'] = 0; // если 0, значит все страницы - только для главной
	if ( !isset($r['cat_id']) )			$r['cat_id'] = 0; // если 0, значит все рубрики - только для главной
	
	if ( !isset($r['type']) )			$r['type'] = 'blog'; // если false - то все, иначе blog или static
	if ($r['page_id']) $r['type'] = false; // если указан номер, то тип страницы сбрасываем
	
	if ( !isset($r['order']) )			$r['order'] = 'page_date_publish'; // поле сортировки страниц
	if ( !isset($r['order_asc']) )		$r['order_asc'] = 'desc'; // поле сортировки страниц
	
	// если нужно вывести все данные, невзирая на limit, то no_limit=true - пагинация при этом отключается
	if ( !isset($r['no_limit']) )		$r['no_limit'] = false;	
	if ($r['no_limit']) $r['pagination'] = false;
	
	// custom_type - аналог is_type - анализ явного указания типа данных
	if ( !isset($r['custom_type']) )	$r['custom_type'] = false;
	
	// кастомная функция - вызывается вместо автоанализа по is_type
	// эта функция обязательно должна быть подобна _mso_sql_build_home($r, &$pag) и т.п.
	if ( !isset($r['custom_func']) )	$r['custom_func'] = false;
	
	// для функции mso_page_title - передаем тип ссылки для страниц
	if ( !isset($r['link_page_type']) )	$r['link_page_type'] = 'page';
	
	// для _mso_sql_build_category можно указать массив номеров рубрик
	// и получить все записи указанных рубрик
	if ( !isset($r['categories']) )		$r['categories'] = array();
	
	// исключить указанные в массиве записи
	if ( !isset($r['exclude_page_id']) )$r['exclude_page_id'] = array();
	
	// произвольный slug - используется там, где вычисляется mso_segment(2)
	// страница, рубрика, метка, поиск
	if ( !isset($r['slug']) )			$r['slug'] = false;
	
	
	// если true, то публикуется только те, которые старше текущей даты
	// если false - то публикуются все
	if ( !isset($r['date_now']) )		$r['date_now'] = true;
	
	
	// учитывать ли опцию публикация RSS в странице - 
	// если true, то отдаются только те, которые отмечены с этой опцией, false - все
	if ( !isset($r['only_feed']) )			$r['only_feed'] = false;
	
	// стутус страниц - если false, то не учитывается
	if ( !isset($r['page_status']) )		$r['page_status'] = 'publish';
	
	// можно указать номер автора - получим только его записи
	if ( !isset($r['page_id_autor']) )		$r['page_id_autor'] = false;
	
	
	$CI = & get_instance();
	
	# для каждого типа страниц строится свой sql-запрос
	# мы оформляем его в $CI, а здесь только выполняем $CI->db->get();
	
	// если указана кастомная функция, то выполняем r1
	if ( $r['custom_func'] and function_exists($r['custom_func']) ) $r['custom_func']();
	elseif ($r['custom_type']) // указан какой-то свой тип данных - аналог is_type
	{
		$custom_type = $r['custom_type'];
		if ( $custom_type == 'home' ) _mso_sql_build_home($r, &$pag);
		elseif ( $custom_type == 'page' ) _mso_sql_build_page($r, &$pag);
		elseif ( $custom_type == 'category' ) _mso_sql_build_category($r, &$pag);
		elseif ( $custom_type == 'tag' ) _mso_sql_build_tag($r, &$pag);
		elseif ( $custom_type == 'archive' ) _mso_sql_build_archive($r, &$pag);
		elseif ( $custom_type == 'search' ) _mso_sql_build_search($r, &$pag);
		elseif ( $custom_type == 'author' ) _mso_sql_build_author($r, &$pag);
		else return array();
	}
	elseif ( is_type('home') ) _mso_sql_build_home($r, &$pag);
	elseif ( is_type('page') ) _mso_sql_build_page($r, &$pag);
	elseif ( is_type('category') ) _mso_sql_build_category($r, &$pag);
	elseif ( is_type('tag') ) _mso_sql_build_tag($r, &$pag);
	elseif ( is_type('archive') ) _mso_sql_build_archive($r, &$pag);
	elseif ( is_type('search') ) _mso_sql_build_search($r, &$pag);
	elseif ( is_type('author') ) _mso_sql_build_author($r, &$pag);
	else return array();
	
	
	$sql = $CI->db->_compile_select();
	
	$sql = str_replace('ORDER BY `` RAND()', 'ORDER BY RAND()', $sql); # fix CodeIgniter ORDER BY `` RAND() 
	

	// сам запрос и его обработка
	//$query = $CI->db->get();
	
	// сам запрос теперь сделаем вручную
	$query = $CI->db->query($sql);
	$CI->db->_reset_select();
		
	if ($query->num_rows() > 0)	
	{	
		$pages = $query->result_array();
		
		
		if (is_type('page'))
		{
			// проверяем статус публикации - если page_status <> publish то смотрим автора и сравниваем с текущим юзером
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
			
			$content = $page['page_content'];
			
			$content = str_replace('<!-- pagebreak -->', '[cut]', $content); // совместимость с TinyMCE
			
			$content = mso_hook('content', $content);
			$content = mso_hook('content_auto_tag', $content);
			$content = mso_hook('content_balance_tags', $content);
			$content = mso_hook('content_out', $content);
			
			$pages[$key]['page_slug'] = $page['page_slug'] = mso_slug($page['page_slug']);
			
			if ($r['xcut']) // можно использовать [xcut] 
				$content = str_replace('[xcut', '[mso_xcut][cut', $content);
			else
				$content = str_replace('[xcut', '[cut', $content);
				
			if ( preg_match('/\[cut(.*?)?\]/', $content, $matches) ) 
			{
				$content = explode($matches[0], $content, 2);
				$cut = $matches[1];
			}
			else 
			{
				$content = array($content);
				$cut = '';
			}
		
			$output = $content[0]; 
			if ( count($content) > 1 ) 
			{
				// ссылка на «далее...»
				if ($r['cut'])
				{
					if ($cut) 
					{
						if (isset($content[1]))
						{
							if (strpos($cut, '%wordcount%')!==false)
								$cut = str_replace('%wordcount%', mso_wordcount($content[1]), $cut);
						}
					} 
					else $cut = $r['cut'];
					
					$output .= mso_page_title( $page['page_slug'], $cut, 
								$do = '<span class="cut">', $posle = '</span>', true, false, $r['link_page_type'] );
				}
				else
				{
					$output .= mso_balance_tags($content[1]);
				}
				
				$output = mso_balance_tags($output);
			}
			
			if ($r['xcut'])
			{
				if ($r['cut'])
					$output = preg_replace('~(.*?)\[mso_xcut\](.*?)~s', "$1", $output);
				else 
					$output = preg_replace('~(.*?)\[mso_xcut\](.*?)~s', "$2", $output);
			}
			
			$output = mso_hook('content_complete', $output);
			
			$pages[$key]['page_content'] = $output;
			
			$pages[$key]['page_categories'] = array();
			$pages[$key]['page_categories_detail'] = array();
			$pages[$key]['page_tags'] = array();
			$pages[$key]['page_meta'] = array();
		}
		
		// теперь одним запросом получим все рубрики каждой записи
		
		$CI->db->select('page_id, category.category_id, category.category_name, category.category_slug');
		$CI->db->where_in('page_id', $all_page_id);
		$CI->db->order_by('category.' . $r['cat_order'], $r['cat_order_asc']); // сортировка рубрик
		$CI->db->from('cat2obj');
		$CI->db->join('category', 'cat2obj.category_id = category.category_id');
		
		$query = $CI->db->get();
		$cat = $query->result_array();
		
		// переместим все в массив page_id[] = category_id
		$page_cat = array();
		$page_cat_detail = array();
		foreach ($cat as $key=>$val)
		{
			$page_cat[$val['page_id']][] = $val['category_id'];
			$page_cat_detail[$val['page_id']][$val['category_id']] = array('category_name'=>$val['category_name'], 'category_slug' => $val['category_slug']);
		}
		
		// по этому же принципу получаем все метки
		$CI->db->select('meta_id_obj, meta_key, meta_value');
		$CI->db->where( array (	'meta_table' => 'page' ) );
		$CI->db->where_in('meta_id_obj', $all_page_id);
		$CI->db->order_by('meta_value');
		$query = $CI->db->get('meta');
		$meta = $query->result_array();
		
		// переместим все в массив page_id[] = category_id
		$page_meta = array();
		foreach ($meta as $key=>$val)
		{
			$page_meta[$val['meta_id_obj']][$val['meta_key']][] = $val['meta_value'];
		}
		
		// добавим в массив pages полученную информацию по меткам и рубрикам
		foreach ($pages as $key=>$val)
		{
			// рубрики 
			if ( isset($page_cat[$val['page_id']]) and $page_cat[$val['page_id']] )
			{
				$pages[$key]['page_categories'] = $page_cat[$val['page_id']];
				$pages[$key]['page_categories_detail'] = $page_cat_detail[$val['page_id']];
			}
			
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








# главная страница - home
function _mso_sql_build_home($r, &$pag)
{
	$CI = & get_instance();

	$offset = 0;
	
	if ($r['cat_id']) $cat_id = mso_explode($r['cat_id']);
	else $cat_id = false;
	
	// еслу указан массив номеров рубрик, значит выводим только его
	if ($r['categories']) $categories = true;
	else $categories = false;
	
	// если указаны номера записей, котоыре следует исключить
	if ($r['exclude_page_id']) $exclude_page_id = true;
	else $exclude_page_id = false;
	
	// при получении учитываем часовой пояс
	$date_now = mso_date_convert('Y-m-d H:i:s', date('Y-m-d H:i:s'));
	
	if ($r['pagination'])
	{
		# пагинация
		# для неё нужно при том же запросе указываем общее кол-во записей и кол-во на страницу
		# сама пагинация выводится отдельным плагином
		# запрос один в один, кроме limit и юзеров
		$CI->db->select('page.page_id');
		$CI->db->from('page');
		
		if ($r['page_status']) $CI->db->where('page.page_status', $r['page_status']);
		
		if ($r['date_now']) $CI->db->where('page_date_publish<', $date_now);
		
		if ($r['type']) $CI->db->where('page_type.page_type_name', $r['type']);
		
		if ($r['page_id']) $CI->db->where('page.page_id', $r['page_id']);
		
		if ($r['page_id_autor']) $CI->db->where('page.page_id_autor', $r['page_id_autor']);
		
		$CI->db->join('page_type', 'page_type.page_type_id = page.page_type_id');
		
		if ($cat_id) // указаны рубрики
		{
			$CI->db->join('cat2obj', 'cat2obj.page_id = page.page_id', 'left');
			$CI->db->join('category', 'cat2obj.category_id = category.category_id');
			$CI->db->where_in('category.category_id', $cat_id);
		}
		
		if ($categories)
			$CI->db->where_in('category.category_id', $r['categories']);
		
		if ($exclude_page_id)
			$CI->db->where_not_in('page.page_id', $r['exclude_page_id']);
		
		
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
		$CI->db->select('page.page_id, page_type_name, page_slug, page_title, page_date_publish, page_status, users_nik, page_content, page_view_count, page_rating, page_rating_count, page_password, page_comment_allow, page_id_parent, users_avatar_url, COUNT(comments_id) AS page_count_comments, page.page_id_autor, users_description');
	}
	else
	{
		$CI->db->select('page.page_id, page_type_name, page_slug, page_title, "" AS page_content, page_date_publish, page_status, users_nik, page_view_count, page_rating, page_rating_count, page_password, page_comment_allow, page_id_parent, users_avatar_url, COUNT(comments_id) AS page_count_comments, page.page_id_autor, users_description');
	}
		
	$CI->db->from('page');
	
	if ($r['page_id']) $CI->db->where('page.page_id', $r['page_id']);
	
	if ($r['page_status']) $CI->db->where('page_status', $r['page_status']);
	
	if ($r['type']) $CI->db->where('page_type_name', $r['type']);
	
	if ($r['date_now']) $CI->db->where('page_date_publish<', $date_now );
	
	if ($r['only_feed']) $CI->db->where('page_feed_allow', '1');
	
	if ($r['page_id_autor']) $CI->db->where('page.page_id_autor', $r['page_id_autor']);
	
	$CI->db->join('users', 'users.users_id = page.page_id_autor', 'left');
	$CI->db->join('page_type', 'page_type.page_type_id = page.page_type_id', 'left');
	$CI->db->join('comments', 'comments.comments_page_id = page.page_id AND comments_approved = 1', 'left');
	
	if ($cat_id) // указаны рубрики
	{
		$CI->db->join('cat2obj', 'cat2obj.page_id = page.page_id', 'left');
		$CI->db->join('category', 'cat2obj.category_id = category.category_id');
		$CI->db->where_in('category.category_id', $cat_id);
	}
	
	if ($categories)
		$CI->db->where_in('category.category_id', $r['categories']);
	
	if ($exclude_page_id)
			$CI->db->where_not_in('page.page_id', $r['exclude_page_id']);

	
	$CI->db->order_by($r['order'], $r['order_asc']);
	
	$CI->db->group_by('page.page_id');
	$CI->db->group_by('comments_page_id');
	
	if (!$r['no_limit'])
	{
		if ($pag and $offset) $CI->db->limit($r['limit'], $offset);
			else $CI->db->limit($r['limit']);
	}

}

# одиночная страница по id или slug
function _mso_sql_build_page($r, &$pag)
{
	$CI = & get_instance();
	
	// $pag = false;
	
	if ($r['slug']) 
		$slug = $r['slug'];
	else
		$slug = mso_segment(2);

	// если slug есть число, то выполняем поиск по id
	if (!is_numeric($slug)) $id = false; // slug не число
		else $id = (int) $slug;
		
	// $id = (int) $slug;
	// if ( (string) $slug != (string) $id ) $id = false; // slug не число
	
	
	$CI->db->select('page.page_id, page_type_name, page_slug, page_title, page_date_publish, page_status, users_nik, page_content, page_view_count, page_rating, page_rating_count, page_password, page_comment_allow, page_id_parent, users_avatar_url, page.page_id_autor, users_description');
	$CI->db->from('page');
	
	// if ($page_status) $CI->db->where('page_status', $page_status);
	
	if ($r['type']) $CI->db->where('page_type_name', $r['type']);
	
		// при получении учитываем часовой пояс
	$date_now = mso_date_convert('Y-m-d H:i:s', date('Y-m-d H:i:s'));
	
	if ($r['date_now']) $CI->db->where('page_date_publish<', $date_now);
	
	if ($id) // если slug число, то это может быть и номер и сам slug - неопределенность!
	{
		$CI->db->where(array('page_slug'=>$slug));
		$CI->db->or_where(array('page_id'=>$slug));
	}
	else
	{
		$CI->db->where(array('page_slug'=>$slug));
	}

	/*
	#if ($id)
	#	$CI->db->where('page_id', $id);
	#else 
	#	$CI->db->where('page_slug', $slug);
	*/
	
	$CI->db->join('users', 'users.users_id = page.page_id_autor');
	$CI->db->join('page_type', 'page_type.page_type_id = page.page_type_id');
	$CI->db->limit(1);
}


# рубрики
function _mso_sql_build_category($r, &$pag)
{
	$CI = & get_instance();
	
	if ($r['slug']) 
		$slug = $r['slug'];
	else
		$slug = mso_segment(2);
	
	// $slug = mso_segment(2);
	
	// если slug есть число, то выполняем поиск по id
	if (!is_numeric($slug)) $id = false; // slug не число
		else $id = (int) $slug;
	
	// еслу указан массив номеров рубрик, значит выводим только его
	if ($r['categories']) $categories = true;
	else $categories = false;
	
	// если указаны номера записей, котоыре следует исключить
	if ($r['exclude_page_id']) $exclude_page_id = true;
	else $exclude_page_id = false;
	
	// при получении учитываем часовой пояс
	$date_now = mso_date_convert('Y-m-d H:i:s', date('Y-m-d H:i:s'));
	
	$offset = 0;

	if ($r['pagination'])
	{
		# пагинация
		# для неё нужно при том же запросе указываем общее кол-во записей и кол-во на страницу
		# сама пагинация выводится отдельным плагином
		# запрос один в один, кроме limit и юзеров
		$CI->db->select('page.page_id');
		$CI->db->from('page');

		if ($r['page_status']) $CI->db->where('page_status', $r['page_status']);
		
		//$CI->db->where('page_type_name', 'blog');
		if ($r['type']) $CI->db->where('page_type_name', $r['type']);
		
		if ($r['date_now']) $CI->db->where('page_date_publish<', $date_now);
		
		if ($r['page_id_autor']) $CI->db->where('page.page_id_autor', $r['page_id_autor']);
		
		$CI->db->join('page_type', 'page_type.page_type_id = page.page_type_id');
		$CI->db->join('cat2obj', 'cat2obj.page_id = page.page_id');
		$CI->db->join('category', 'cat2obj.category_id = category.category_id');
		
		if ($categories)
		{
			$CI->db->where_in('category.category_id', $r['categories']);
		}
		else
		{
			if ($id) $CI->db->where('category.category_id', $id);
				else $CI->db->where('category.category_slug', $slug);
		}
		
		if ($exclude_page_id)
			$CI->db->where_not_in('page.page_id', $r['exclude_page_id']);
		
		
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
		$CI->db->select('page.page_id, page_type_name, page_slug, page_title, page_date_publish, page_status, users_nik, page_content, page_view_count, page_rating, page_rating_count, page_password, page_comment_allow, page_id_parent, users_avatar_url, category.category_name, COUNT(comments_id) AS page_count_comments, page.page_id_autor, users_description');
	else
		$CI->db->select('page.page_id, page_type_name, page_slug, page_title, "" AS page_content, page_date_publish, page_status, users_nik, page_view_count, page_rating, page_rating_count, page_password, page_comment_allow, users_avatar_url, category.category_name, COUNT(comments_id) AS page_count_comments, page.page_id_autor, users_description');
		
	$CI->db->from('page');
	if ($r['page_status']) $CI->db->where('page_status', $r['page_status']);
	
	if ($r['date_now']) $CI->db->where('page_date_publish<', $date_now);
	
	if ($r['only_feed']) $CI->db->where('page.page_feed_allow', '1');
	
	if ($r['page_id_autor']) $CI->db->where('page.page_id_autor', $r['page_id_autor']);
	
	//$CI->db->where('page_type_name', 'blog');
	if ($r['type']) $CI->db->where('page_type_name', $r['type']);
	$CI->db->join('users', 'users.users_id = page.page_id_autor');
	$CI->db->join('page_type', 'page_type.page_type_id = page.page_type_id');
	
	$CI->db->join('cat2obj', 'cat2obj.page_id = page.page_id');
	$CI->db->join('category', 'cat2obj.category_id = category.category_id');
	$CI->db->join('comments', 'comments.comments_page_id = page.page_id AND comments_approved = 1', 'left');


	if ($categories)
	{
		$CI->db->where_in('category.category_id', $r['categories']);
	}
	else
	{
		if ($id)
		{
			$CI->db->where('category.category_id', $id);
			$CI->db->or_where('category.category_slug', $slug);
		}
		else 
			$CI->db->where('category.category_slug', $slug);
	}	
	
	if ($exclude_page_id)
			$CI->db->where_not_in('page.page_id', $r['exclude_page_id']);

	$CI->db->order_by($r['order'], $r['order_asc']);
	
	$CI->db->group_by('page.page_id');
	$CI->db->group_by('comments_page_id');
	
	if (!$r['no_limit'])
	{
		if ($pag and $offset) $CI->db->limit($r['limit'], $offset);
			else $CI->db->limit($r['limit']);
	}
}


# страница меток
function _mso_sql_build_tag($r, &$pag)
{
	$CI = & get_instance();
	
	if ($r['slug']) 
		$slug = $r['slug'];
	else
		$slug = mso_segment(2);
	
	// $slug = mso_segment(2);

	// при получении учитываем часовой пояс
	$date_now = mso_date_convert('Y-m-d H:i:s', date('Y-m-d H:i:s'));
	
	$offset = 0;

	if ($r['pagination'])
	{
		# пагинация
		# для неё нужно при том же запросе указываем общее кол-во записей и кол-во на страницу
		# сама пагинация выводится отдельным плагином
		# запрос один в один, кроме limit и юзеров
		$CI->db->select('page.page_id');
		$CI->db->from('page');
		if ($r['page_status']) $CI->db->where('page_status', $r['page_status']);
		// $CI->db->where('page_type_name', 'blog');
		
		if ($r['date_now']) $CI->db->where('page_date_publish<', $date_now);
		
		if ($r['type']) $CI->db->where('page_type_name', $r['type']);
		
		if ($r['page_id_autor']) $CI->db->where('page.page_id_autor', $r['page_id_autor']);
		
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
		$CI->db->select('page.page_id, page_type_name, page_slug, page_title, page_date_publish, page_status, users_nik, page_content, page_view_count, page_rating, page_rating_count, page_password, page_comment_allow, page_id_parent, users_avatar_url, meta.meta_value AS tag_name, COUNT(comments_id) AS page_count_comments, page.page_id_autor, users_description');
	else
		$CI->db->select('page.page_id, page_type_name, page_slug, page_title, "" AS page_content, page_date_publish, page_status, users_nik, page_content, page_view_count, page_rating, page_rating_count, page_password, page_comment_allow, page_id_parent, users_avatar_url, meta.meta_value AS tag_name, COUNT(comments_id) AS page_count_comments, page.page_id_autor, users_description');
	
	
	$CI->db->from('page');
	if ($r['page_status']) $CI->db->where('page_status', $r['page_status']);
	// $CI->db->where('page_type_name', 'blog');
	
	if ($r['date_now']) $CI->db->where('page_date_publish<', $date_now);
	
	if ($r['type']) $CI->db->where('page_type_name', $r['type']);
	
	if ($r['page_id_autor']) $CI->db->where('page.page_id_autor', $r['page_id_autor']);
	
	$CI->db->join('users', 'users.users_id = page.page_id_autor');
	$CI->db->join('page_type', 'page_type.page_type_id = page.page_type_id');
	$CI->db->join('meta', 'meta.meta_id_obj = page.page_id');
	$CI->db->join('comments', 'comments.comments_page_id = page.page_id AND comments_approved = 1', 'left');
	
	$CI->db->where('meta_key', 'tags');
	$CI->db->where('meta_table', 'page');
	$CI->db->where('meta_value', $slug);
	
	$CI->db->order_by($r['order'], $r['order_asc']);
	
	$CI->db->group_by('page.page_id');
	$CI->db->group_by('comments_page_id');
	
	if (!$r['no_limit'])
	{
		if ($pag and $offset) $CI->db->limit($r['limit'], $offset);
			else $CI->db->limit($r['limit']);
	}
}



# архивы по датам
function _mso_sql_build_archive($r, &$pag)
{
	$CI = & get_instance();
	
	$offset = 0;
	
	$year = (int) mso_segment(2);
	if ($year>date('Y', mktime()) or $year<2006) $year = date('Y', mktime());

	$month = (int) mso_segment(3);
	if ($month>12 or $month<1) $month = date('m', mktime());
	
	$day = (int) mso_segment(4);
	
	if ($day)
	{
		if ($day>31 or $day<1) $day = 1;
		
		$dmax = get_total_days($month, $year);
		if ( $day>$dmax ) $day = $dmax;
	}
	//else $day = 1;
	
	if ($day) 
	{
		$date_in = mso_date_convert('Y-m-d H:i:s', $year . '-' . $month. '-' . $day . ' 00:00:00', -1);
		$date_in_59 = mso_date_convert('Y-m-d H:i:s', $year . '-' . $month. '-' . $day . ' 23:59:59', -1);
	}
	else 
	{
		$date_in = mso_date_convert('Y-m-d H:i:s', $year . '-' . $month. '-1 00:00:00', -1);
		$date_in_59 = mso_date_convert('Y-m-d H:i:s', $year . '-' . $month. '-31 23:59:59', -1);	
	}
	
	// pr($date_in);
	// pr($date_in_59);
	
	// при получении учитываем часовой пояс
	$date_now = mso_date_convert('Y-m-d H:i:s', date('Y-m-d H:i:s'));
	
	// echo $year . $month . $day;

	if ($r['pagination'])
	{
		# пагинация
		# для неё нужно при том же запросе указываем общее кол-во записей и кол-во на страницу
		# сама пагинация выводится отдельным плагином
		# запрос один в один, кроме limit и юзеров
		$CI->db->select('page_id');
		$CI->db->from('page');
		if ($r['page_status']) $CI->db->where('page_status', $r['page_status']);
		
		if ($r['date_now']) $CI->db->where('page_date_publish<', $date_now);
		
		if ($r['type'])
		{
			$CI->db->where('page_type_name', $r['type']);
		}
		
		$CI->db->where('page_date_publish >= ', $date_in);
		$CI->db->where('page_date_publish <= ', $date_in_59);
		
		if ($r['page_id_autor']) $CI->db->where('page.page_id_autor', $r['page_id_autor']);
		
		$CI->db->join('page_type', 'page_type.page_type_id = page.page_type_id');
		
		// $CI->db->order_by('page_date_publish', 'desc');
		
		$CI->db->order_by($r['order'], $r['order_asc']);
		
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
	$CI->db->select('page.page_id, page_type_name, page_slug, page_title, page_date_publish, page_status, users_nik, page_content, page_view_count, page_rating, page_rating_count, page_password, page_comment_allow, page_id_parent, users_avatar_url, COUNT(comments_id) AS page_count_comments, page.page_id_autor, users_description');
	$CI->db->from('page');
	if ($r['page_status']) $CI->db->where('page_status', $r['page_status']);
	
	
	$CI->db->where('page_date_publish >= ', $date_in);
	$CI->db->where('page_date_publish <= ', $date_in_59);

	if ($r['page_id_autor']) $CI->db->where('page.page_id_autor', $r['page_id_autor']);
	
	if ($r['date_now']) $CI->db->where('page_date_publish<', $date_now);
	
	if ($r['type']) $CI->db->where('page_type_name', $r['type']);
	$CI->db->join('users', 'users.users_id = page.page_id_autor');
	$CI->db->join('page_type', 'page_type.page_type_id = page.page_type_id');
	$CI->db->join('comments', 'comments.comments_page_id = page.page_id AND comments_approved = 1', 'left');
	
	$CI->db->order_by($r['order'], $r['order_asc']);
	
	$CI->db->group_by('page.page_id');
	$CI->db->group_by('comments_page_id');
	
	if (!$r['no_limit'])
	{
		if ($pag and $offset) $CI->db->limit($r['limit'], $offset);
			else $CI->db->limit($r['limit']);
	}
}


# страница поиска
function _mso_sql_build_search($r, &$pag)
{
	$CI = & get_instance();
	
	if ($r['slug']) 
		$search = $r['slug'];
	else
		$search = mso_segment(2);
		
	// $search = mso_segment(2);
	$search = mso_strip(strip_tags($search));
	
	// при получении учитываем часовой пояс
	$date_now = mso_date_convert('Y-m-d H:i:s', date('Y-m-d H:i:s'));
		
	$offset = 0;

	if ($r['pagination'])
	{
		# пагинация
		# для неё нужно при том же запросе указываем общее кол-во записей и кол-во на страницу
		# сама пагинация выводится отдельным плагином
		# запрос один в один, кроме limit и юзеров
		$CI->db->select('page_id');
		$CI->db->from('page');
		if ($r['page_status']) $CI->db->where('page_status', $r['page_status']);
		if ($r['type'])
		{
			$CI->db->where('page_type_name', $r['type']);
		}
		
		if ($r['date_now']) $CI->db->where('page_date_publish<', $date_now);
		
		if ($r['page_id_autor']) $CI->db->where('page.page_id_autor', $r['page_id_autor']);
		
		$CI->db->like('page_content', $search); 
		$CI->db->or_like('page_title', $search); 
		
		$CI->db->join('page_type', 'page_type.page_type_id = page.page_type_id');
		
		// $CI->db->order_by('page_date_publish', 'desc');
		$CI->db->order_by($r['order'], $r['order_asc']);
		
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
	
	$CI->db->select('page.page_id, page_type_name, page_slug, page_title, page_date_publish, page_status, users_nik, page_content, page_view_count, page_rating, page_rating_count, page_password, page_comment_allow, page_id_parent, users_avatar_url, COUNT(comments_id) AS page_count_comments, page.page_id_autor, users_description');
	
		
	$CI->db->from('page');
	
	if ($r['page_status']) $CI->db->where('page_status', $r['page_status']);
	
	if ($r['date_now']) $CI->db->where('page_date_publish<', $date_now);
	
	if ($r['page_id_autor']) $CI->db->where('page.page_id_autor', $r['page_id_autor']);
	
	$CI->db->like('page_content', $search); 
	$CI->db->or_like('page_title', $search);
	
	if ($r['type']) $CI->db->where('page_type_name', $r['type']);
	
	$CI->db->join('users', 'users.users_id = page.page_id_autor', 'left');
	$CI->db->join('page_type', 'page_type.page_type_id = page.page_type_id', 'left');
	$CI->db->join('comments', 'comments.comments_page_id = page.page_id AND comments_approved = 1', 'left');
	
	// $CI->db->order_by('page_date_publish', 'desc');
	$CI->db->order_by($r['order'], $r['order_asc']);
	
	$CI->db->group_by('page.page_id');
	$CI->db->group_by('comments_page_id');
	
	if (!$r['no_limit'])
	{
		if ($pag and $offset) $CI->db->limit($r['limit'], $offset);
			else $CI->db->limit($r['limit']);
	}
}


# страницы автора
function _mso_sql_build_author($r, &$pag)
{
	// $CI = & get_instance();
	// _mso_sql_build_home($r, &$pag);
	$CI = & get_instance();
	
	if ($r['slug']) 
		$slug = $r['slug'];
	else
		$slug = mso_segment(2);
	
	// $slug = mso_segment(2);
	
	// если slug есть число, то выполняем поиск по id
	if (!is_numeric($slug)) $id = 0; // slug не число
		else $id = (int) $slug;
	
	// при получении учитываем часовой пояс
	$date_now = mso_date_convert('Y-m-d H:i:s', date('Y-m-d H:i:s'));
	
	$offset = 0;

	if ($r['pagination'])
	{
		# пагинация
		# для неё нужно при том же запросе указываем общее кол-во записей и кол-во на страницу
		# сама пагинация выводится отдельным плагином
		# запрос один в один, кроме limit и юзеров
		$CI->db->select('page.page_id');
		$CI->db->from('page');

		if ($r['page_status']) $CI->db->where('page_status', $r['page_status']);
		
		//$CI->db->where('page_type_name', 'blog');
		if ($r['type']) $CI->db->where('page_type_name', $r['type']);
		
		if ($r['date_now']) $CI->db->where('page_date_publish<', $date_now);
		
		$CI->db->join('page_type', 'page_type.page_type_id = page.page_type_id');
		//$CI->db->join('cat2obj', 'cat2obj.page_id = page.page_id');
		//$CI->db->join('category', 'cat2obj.category_id = category.category_id');
		
		$CI->db->where('page.page_id_autor', $id);
		
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
		$CI->db->select('page.page_id, page_type_name, page_slug, page_title, page_date_publish, page_status, users_nik, page_content, page_view_count, page_rating, page_rating_count, page_password, page_comment_allow, page_id_parent, users_avatar_url, category.category_name, COUNT(comments_id) AS page_count_comments, page.page_id_autor, users_description');
	else
		$CI->db->select('page.page_id, page_type_name, page_slug, page_title, "" AS page_content, page_date_publish, page_status, users_nik, page_view_count, page_rating, page_rating_count, page_password, page_comment_allow, users_avatar_url, category.category_name, COUNT(comments_id) AS page_count_comments, page.page_id_autor, users_description');
		
	$CI->db->from('page');
	if ($r['page_status']) $CI->db->where('page_status', $r['page_status']);
	
	if ($r['date_now']) $CI->db->where('page_date_publish<', $date_now);
	
	if ($r['only_feed']) $CI->db->where('page.page_feed_allow', '1');
	
	
	//$CI->db->where('page_type_name', 'blog');
	if ($r['type']) $CI->db->where('page_type_name', $r['type']);
	$CI->db->join('users', 'users.users_id = page.page_id_autor');
	$CI->db->join('page_type', 'page_type.page_type_id = page.page_type_id');
	
	$CI->db->join('cat2obj', 'cat2obj.page_id = page.page_id');
	$CI->db->join('category', 'cat2obj.category_id = category.category_id');
	$CI->db->join('comments', 'comments.comments_page_id = page.page_id AND comments_approved = 1', 'left');


	$CI->db->where('page.page_id_autor', $id);
			

	$CI->db->order_by($r['order'], $r['order_asc']);
	
	$CI->db->group_by('page.page_id');
	//$CI->db->group_by('comments_page_id');
	
	if (!$r['no_limit'])
	{
		if ($pag and $offset) $CI->db->limit($r['limit'], $offset);
			else $CI->db->limit($r['limit']);
	}	
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
function  mso_page_cat_link($cat = array(), $sep = ', ', $do = '', $posle = '', $echo = true, $type = 'category', $link = true)
{
	global $MSO;
	
	if (!$cat) return '';
	
	// получим массив рубрик из mso_cat_array_single
	$all_cat = mso_cat_array_single();
	
	$out = '';
	foreach ($cat as $id)
	{
		if ($link)
			$out .=  '<a href="' 
					. $MSO->config['site_url'] 
					. $type . '/' 
					. $all_cat[$id]['category_slug'] 
					. '">' 
					. $all_cat[$id]['category_name'] 
					. '</a>   ';
		else
			$out .= $all_cat[$id]['category_name'] . '   ';			
	}
	
	$out = trim($out);
	$out = str_replace('   ', $sep, $out);
	
	if ($echo) echo $do . $out . $posle;
		else return $do . $out . $posle;
}

# получить ссылки на метки указанной страницы
function mso_page_tag_link($tags = array(), $sep = ', ', $do = '', $posle = '', $echo = true, $type = 'tag', $link = true)
{
	global $MSO;
	
	if (!$tags) return '';
	
	$out = '';
	foreach ($tags as $tag)
	{
		if ($link)
			$out .=  '<a href="' 
					. $MSO->config['site_url'] 
					. $type . '/' 
					. $tag 
					. '" rel="tag">' 
					. $tag 
					. '</a>   ';
		else
			$out .=  $tag . '   ';		
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
	
	if (is_array($format)) // формат в массиве, значит там и замены
	{
		if (isset($format['format'])) $df = $format['format'];
			else $df = 'Y-m-d H:i:s';
			
		if (isset($format['days'])) $dd = $format['days'];
			else $dd = false;
			
		if (isset($format['month'])) $dm = $format['month'];
			else $dm = false;
	}
	else
	{
		$df = $format;
		$dd = false;
		$dm = false;
	}
	
	// учитываем смещение времени time_zone
	$out = mso_date_convert($df, $date, true, $dd, $dm);

	if ($echo) echo $do . $out . $posle;
		else return $do . $out . $posle;
}


# формирование титла или ссылки на страницу
function mso_page_title($page_slug = '', $page_title = 'no title', $do = '<h1>', $posle = '</h1>', $link = true, $echo = true, $type = 'page')
{
	global $MSO;
	
	if (!$page_slug) return '';
	
	if ($link)
		$out = '<a href="' . $MSO->config['site_url'] . $type . '/' . $page_slug . '" title="' . mso_strip($page_title) . '">' . $page_title . '</a>';
	else
		$out = $page_title;
	
	if ($echo) echo $do . $out . $posle;
		else return $do . $out . $posle;
}


# формирование ссылки для rss страницы
function mso_page_feed($page_slug = '', $page_title = 'Подписаться', $do = '<p>', $posle = '</p>', $link = true, $echo = true, $type = 'page')
{
	global $MSO;
	
	if (!$page_slug) return '';
	
	if ($link)
		$out = '<a href="' . $MSO->config['site_url'] . $type . '/' . $page_slug . '/feed">' . $page_title . '</a>';
	else
		$out = $page_title;
	
	if ($echo) echo $do . $out . $posle;
		else return $do . $out . $posle;
}

# вывод текста
function mso_page_content($page_content = '', $use_password = true, $message = 'Данная запись защищена паролем.')
{
	global $page;
	
	mso_hook('content_start'); # хук на начало блока
	
	if ($use_password and $page['page_password']) // есть пароль
	{
		
		$form ='<p><strong>' . $message . '</strong></p>';
		$form .= '<form action="' . getinfo('siteurl') . 'page/' . $page['page_slug'] . '" method="post">' . mso_form_session('f_session_id');
		$form .= '<input type="hidden" name="f_page_id" value="' . $page['page_id'] . '" />';
		$form .= '<p>Пароль: <input type="text" name="f_password" value="" /> ';
		$form .= '<input type="submit" name="f_submit" value="ОК" /></p>';
		$form .= '</form>';
		
		// возможно пароль уже был отправлен
		if ( $post = mso_check_post(array('f_session_id', 'f_submit', 'f_page_id', 'f_password')) ) 
		{
			mso_checkreferer();
			
			$f_page_id = (int) $post['f_page_id']; // номер записи
			$f_password = $post['f_password']; // пароль
			
			if ($f_page_id == $page['page_id'] and $f_password == $page['page_password'])
			{ 	// верный пароль
				echo mso_hook('content_content', $page_content);
			}
			else // ошибка в пароле
			{
				echo '<p style="color: red;"><strong>Ошибочный пароль!</strong> Повторите ввод.</p>'. $form;
			}
		}
		else // нет post, выводим форму 
		{
			echo $form;
		}
	}
	else // нет пароля 
	{
		echo mso_hook('content_content', $page_content);
	}

	mso_hook('content_end'); # хук на конец блока
	
}

# получение meta
function mso_page_meta($meta = '', $page_meta = array(), $do = '', $posle = '', $razd = ', ', $echo = true)
{
	if (!$meta or !$page_meta) return '';
	
	if (isset($page_meta[$meta]) and $page_meta[$meta]) 
	{
		$out = '';
		foreach ( $page_meta[$meta] as $val ) 
			$out .= $val . '     ';
		
		$out = trim($out);
		if (!$out) return '';
		
		$out = str_replace('     ', $razd, trim($out) );
	}
	else return '';
	
	if ($echo) echo $do . $out . $posle;
		else return $do . $out . $posle;
}

# формирование ссылки «обсудить» если разрешен комментарий
function mso_page_comments_link($page_comment_allow = true, $page_slug = '', $title = 'Обсудить', $do = '', $posle = '', $echo = true, $type = 'page')
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
				$out = $r['do'] . '<a href="' . $MSO->config['site_url'] . $type . '/' 
						. $r['page_slug'] . '#comments">' . $r['title_no_link'] . '</a>' . $r['posle'];
			}			
		}
		else 
			$out = $r['do'] . '<a href="' . $MSO->config['site_url'] . $type . '/' 
						. $r['page_slug'] . '#comments">' . $r['title'] . '</a>' . $r['posle'];
		
		
		if ($r['echo']) echo $out;
			else return $out;	
	}
	else // обычные параметры
	{
		if (!$page_slug) return '';
		if (!$page_comment_allow) return '';
		
		$out = $do . '<a href="' . $MSO->config['site_url'] . $type . '/' . $page_slug . '#comments">' . $title . '</a>' . $posle;
		if ($echo) echo $out;
			else return $out;		
	}
}

# получить ссылкe на автора страницы
function  mso_page_author_link($users_nik = '', $page_id_autor = '', $do = '', $posle = '', $echo = true, $type = 'author', $link = true)
{
	global $MSO;
	
	if (!$users_nik or !$page_id_autor) return '';
	
	$out = '';

	if ($link)
		$out .=  '<a href="' 
				. $MSO->config['site_url'] 
				. $type . '/' 
				. $page_id_autor 
				. '">' 
				. $users_nik
				. '</a>   ';
	else
		$out .= $users_nik;

	
	if ($echo) echo $do . $out . $posle;
		else return $do . $out . $posle;
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


# Функции которые выполняют роль подсчета количества прочтения записи
# первая функция, проверяет из кука значение массива с текущим url
# если номера не совпадают, то функция устанавливает значение прочтений больше на 1
# если совпадают, значит запись уже была прочитана с этого компа
# если нужно убрать уникальность и учитывать все хиты, то $unique = false
# начения хранятся в виде url1|url2|url2|url3 
# url - второй сегмент
# время жизни 30 дней: 60 секунд * 60 минут * 24 часа * 30 дней = 2592000

function mso_page_view_count_first($unique = true, $name_cookies = 'maxsite-cms', $expire = 2592000) 
{
	global $_COOKIE;
	
	if (isset($_COOKIE[$name_cookies]))	$all_slug = $_COOKIE[$name_cookies]; // значения текущего кука
		else $all_slug = ''; // нет такой куки вообще
	
	$slug = mso_segment(2);
	
	$all_slug = explode('|', $all_slug); // разделим в массив
	
	if ( $unique ) 
		if ( in_array($slug, $all_slug) ) return false; // уже есть текущий урл - не увеличиваем счетчик
	
	// нужно увеличить счетчик
	$all_slug[] = $slug; // добавляем текущий id
	$all_slug = array_unique($all_slug); // удалим дубли на всякий пожарный
	$all_slug = implode('|', $all_slug); // соединяем обратно в строку
	$expire = time() + $expire; 
	@setcookie($name_cookies, $all_slug, $expire); // записали в кук
	
	// получим текущее значение page_view_count
	// и увеличиваем значение на 1
	$CI = & get_instance();
	$CI->db->select('page_view_count');
	$CI->db->where('page_slug', $slug);
	$CI->db->limit(1);
	$query = $CI->db->get('page');
	
	if ($query->num_rows() > 0)	
	{	
		$pages = $query->row_array();
		$page_view_count = $pages['page_view_count'] + 1;
		
		$CI->db->where('page_slug', $slug);
		$CI->db->update('page', array('page_view_count'=>$page_view_count));
		
		return true;
	}
}

# вывод количества просмотров текущей записи
function mso_page_view_count($page_view_count = 0, $do = '<span>Прочтений:</span> ', $posle = '', $echo = true)
{
	if (!$page_view_count) return '';
	
	if ($echo) echo $do . $page_view_count . $posle;
		else return $do . $page_view_count . $posle;
}


# вывод списка страниц по паренту - навигация под страницами - все связанные
function mso_page_nav($page_id = 0, $page_id_parent = 0, $echo = false)
{
	$r = mso_page_map($page_id, $page_id_parent); // построение карты страниц
	$r = mso_create_list($r); // создание ul-списка
	
	if ($echo) echo $r;
		else return $r;
}


# вывод карты страниц по паренту - готовый массив с вложениями с childs=>...
# функция ресурсоемкая!
function mso_page_map($page_id = 0, $page_id_parent = 0)
{
	$cache_key = 'mso_page_map' . $page_id . '-' . $page_id_parent;
	$k = mso_get_cache($cache_key);
	if ($k) return $k; // да есть в кэше
	
	$CI = & get_instance();
	$CI->db->select('page_id, page_id_parent, page_title, page_slug');
	
	if ($page_id) 
	{
		$CI->db->where('page_id', $page_id);
		$CI->db->where('page_id_parent', '0');
		$CI->db->where('page_status', 'publish');
		$CI->db->where('page_date_publish<', date('Y-m-d H:i:s'));
		
		$CI->db->or_where('page_id', $page_id_parent);
	}
	
	$query = $CI->db->get('page');
	$result = $query->result_array(); // здесь все страницы
	
	foreach ($result as $key=>$row)
	{
		$k = $row['page_id'];
		$r[$k] = $row;
		if ($k == $page_id) $r[$k]['current'] = 1;
		
		$ch = _mso_page_map_get_child($row['page_id'], $page_id);
		if ($ch) $r[$k]['childs'] = $ch;
	}
	
	// pr($k);
	// pr($r);
	
	if (!isset($r[$k]['childs'])) $r = array(); // в итоге нет детей у первого элемента, все обнуляем
	
	mso_add_cache($cache_key, $r); // в кэш

	return $r;
}


# вспомогательная рекурсивная рубрика для получения всех потомков страницы
function _mso_page_map_get_child($page_id = 0, $cur_id = 0)
{
	$CI = & get_instance();
	$CI->db->select('page_id, page_id_parent, page_title, page_slug');
	$CI->db->where('page_id_parent', $page_id);
	$CI->db->where('page_status', 'publish');
	$CI->db->where('page_date_publish<', date('Y-m-d H:i:s'));	
	$query = $CI->db->get('page');
	
	$result = $query->result_array(); // здесь все рубрики
	
	if ($result) 
	{
		$r0 = array();
		foreach ($result as $key=>$row)
		{
			$k = $row['page_id'];
			$r0[$k] = $row;
			
			if ($k == $cur_id) $r0[$k]['current'] = 1;
		}
		
		
		$result = $r0;
		foreach ($result as $key=>$row)
		{
			$r = _mso_page_map_get_child($row['page_id'], $cur_id);
			if ($r) $result[$key]['childs'] = $r;
		}
	}
	
	return $result;
}


?>