<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Основные функции MaxSite CMS
 * (c) http://max-3000.com/
 * Функции для рубрик
 */


# получить номера рубрик указанной страницы в виде массива

function mso_get_cat_page($id = 0)
{
	$id = (int) $id;
	
	if (!$id) return array();
	
	$CI = & get_instance();
	
	$CI->db->select('category_id');
	$CI->db->from('cat2obj');
	$CI->db->where('page_id', $id);
	$query = $CI->db->get();
		
	if ($query->num_rows() > 0)
	{
		$cat = array();
		foreach ($query->result_array() as $row)
			$cat[] = $row['category_id'];
	
		return $cat;
	}
	else return array();
}






# получение ul-списка всех рубрик путем sql-запроса
function mso_cat($li_format = '%NAME%', $checked_id = array(), $type = 'page', $parent_id = 0, $order = 'category_menu_order', $asc = 'asc', $child_order = 'category_menu_order', $child_asc = 'asc')
{

	// возможно, что этот список уже сформирован, поэтому посмотрим в кэше
	$cache_key = mso_md5('mso_cat'.$li_format.$type.$parent_id.$order.$asc.$child_order
						.$child_asc . implode(' ',$checked_id) );
	
	$k = mso_get_cache($cache_key);
	if ($k) return $k; // да есть в кэше

	// массив всех рубрик с учетом воложенности в виде массива
	$r = mso_cat_array($type, $parent_id, $order, $asc, $child_order, $child_asc);
	
	$list = "\n";
	foreach ($r as $key=>$row)
	{
		$add = $li_format;
		$add = str_replace('%NAME%', $row['category_name'], $add);
		$add = str_replace('%ID%', $row['category_id'], $add);
		$add = str_replace('%DESC%', $row['category_desc'], $add);
		
		// если нужно отмечать какие-то id 
		if ($checked_id)
		{
			// %CHECKED%
			if (in_array($row['category_id'], $checked_id )) 
				$add = str_replace('%CHECKED%', ' checked="checked" ', $add);
			else $add = str_replace('%CHECKED%', '', $add);
		
		}
		else $add = str_replace('%CHECKED%', '', $add);
		 
		$list .= '<ul class="category">' . "\n" . '<li>' . $add;
		if (isset($row['childs'])) // есть дети
		{
			$ch = _get_child2($row['childs'], $li_format, $checked_id); // возвращает индексы чилдевского массива
			$list .= "\n" .'<ul class="child">' . "\n" . '' . $ch . '' . "\n" . '</ul>';
		}
		$list .= "\n" . '</li>' . "\n" . '</ul>' . "\n";
	}
	
	mso_add_cache($cache_key, $list); // сразу в кэш добавим
			
	return $list;
}


# вспомогательная функция для создания списка mso_cat
function _get_child2($childs, $li_format = '', $checked_id = array(), $list = '')
{
	foreach ($childs as $key=>$row)
	{
		if (isset($row['childs'])) // есть дети
		{
			$ch = _get_child2($row['childs'], $li_format, $checked_id, $list); // возвращает индексы чилдевского массива
			$add = $li_format;
			
			$add = str_replace('%NAME%', $row['category_name'], $add);
			$add = str_replace('%ID%', $row['category_id'], $add);
			$add = str_replace('%DESC%', $row['category_desc'], $add);
			// если нужно отмечать какие-то id 
			if ($checked_id)
			{
				// %CHECKED%
				if (in_array($row['category_id'], $checked_id )) 
					$add = str_replace('%CHECKED%', ' checked="checked" ', $add);
				else $add = str_replace('%CHECKED%', '', $add);
			
			}
			else $add = str_replace('%CHECKED%', '', $add);
		
			$list .= '<li>'. $add . '</li>';
			if ($list != $ch) $list .= '<ul>' . $ch . '</ul>';
		}
		else 
		{
			$add = $li_format;
			$add = str_replace('%NAME%', $row['category_name'], $add);
			$add = str_replace('%ID%', $row['category_id'], $add);
			$add = str_replace('%DESC%', $row['category_desc'], $add);
			// если нужно отмечать какие-то id 
			if ($checked_id)
			{
				// %CHECKED%
				if (in_array($row['category_id'], $checked_id )) 
					$add = str_replace('%CHECKED%', ' checked="checked" ', $add);
				else $add = str_replace('%CHECKED%', '', $add);
			
			}
			else $add = str_replace('%CHECKED%', '', $add);
			
			$list .= '<li>'. $add . '</li>';
		}
	}
	return $list;
}






# получение всех рубрик в массиве - сразу всё с учетом вложенности
# используются рекурсивные функции с sql-запросами - РЕСУРСОЕМКАЯ!
function mso_cat_array($type = 'page', $parent_id = 0, $order = 'category_menu_order', $asc = 'asc', $child_order = 'category_menu_order', $child_asc = 'asc', $in = false, $ex = false, $in_child = false, $hide_empty = false, $only_page_publish = false)
{
	// если неверный тип, то возвратим пустой массив
	if ( ($type != 'page') and ($type != 'links') ) return array();
	
	$parent_id = (int)$parent_id;
	
	$CI = & get_instance();
	
	$CI->db->select('category.*, COUNT(cat2obj_id) AS pages_count');
	
	$CI->db->where('category.category_type', $type);
	$CI->db->where('category.category_id_parent', $parent_id);
	
	$CI->db->join('cat2obj', 'category.category_id = cat2obj.category_id', 'left');
	
	# только опубликованные делаются только при условии скрытия пустых рубрик
	if ($only_page_publish and $hide_empty)
	{
		$CI->db->join('page', 'page.page_id = cat2obj.page_id', 'left');
		$CI->db->where('page_status', 'publish');
	}
	
	if ($hide_empty) $CI->db->having('pages_count > ', 0);
	
	// включить только указанные
	if ($in) $CI->db->where_in('category.category_id', $in);
	
	// исключить указанные
	if ($ex) $CI->db->where_not_in('category.category_id', $ex);

	$CI->db->order_by('category.'.$order, $asc);
	$CI->db->group_by('category.category_id');
	
	$query = $CI->db->get('category');
	$result = $query->result_array(); // здесь все рубрики
	
	$r = array();
	foreach ($result as $key=>$row)
	{
		$k = $row['category_id'];
		$r[$k] = $row;
		$ch = _get_child($type, $row['category_id'], $child_order, $child_asc, $in, $ex, $in_child, $hide_empty, $only_page_publish);
	
		if ($ch) $r[$k]['childs'] = $ch;
	}
	
	return $r;
}

# вспомогательная рекурсивная рубрика для получения всех потомков рубрики mso_cat_array
function _get_child($type = 'page', $parent_id = 0, $order = 'category_menu_order', $asc = 'asc', $in = false, $ex = false, $in_child = false, $hide_empty = false, $only_page_publish = false)
{
	$CI = & get_instance();
	$CI->db->select('category.*, COUNT(cat2obj_id) AS pages_count');
	$CI->db->where(array('category.category_type'=>$type, 'category.category_id_parent'=>$parent_id));
	$CI->db->join('cat2obj', 'category.category_id = cat2obj.category_id', 'left');
	
	# только опубликованные делаются только при условии скрытия пустых рубрик
	if ($only_page_publish and $hide_empty)
	{
		$CI->db->join('page', 'page.page_id = cat2obj.page_id', 'left');
		$CI->db->where('page_status', 'publish');
	}
	
	// включить только указанные
	// если разрешено опцией для детей
	if ($in_child and $in) $CI->db->where_in('category.category_id', $in);
	
	if ($hide_empty) $CI->db->having('pages_count >', 0);
	
	// исключить указанные
	if ($ex) $CI->db->where_not_in('category.category_id', $ex);
	
	$CI->db->order_by('category.' . $order, $asc);
	$CI->db->group_by('category.category_id');
	
	$query = $CI->db->get('category');
	$result = $query->result_array(); // здесь все рубрики
	
	if ($result) 
	{
		$r0 = array();
		foreach ($result as $key=>$row)
		{
			$k = $row['category_id'];
			$r0[$k] = $row;
		}
		
		$result = $r0;
		foreach ($result as $key=>$row)
		{
			$r = _get_child($type, $row['category_id'], $order, $asc, $in, $ex, $in_child, $hide_empty, $only_page_publish);
			if ($r) $result[$key]['childs'] = $r;
		}
	}
	return $result;
}





# получение всех рубрик в одномерной структуре
# функция возвращает массив, 
#[10] => Array
#        (
#            [category_id] => 10
#            [category_id_parent] => 1
#            [category_menu_order] => 4
#            [category_name] => Новости
#            [parents] => 3 1
#            [childs] => 6 5
#            [level] => 2
#        )
# где ключ - номер рубрики
# массив можно использовать для быстрого доступа к параметрам рубрик
# автоматом вычисляются родители (parents) и дочерние элементы (childs)
# дополнительный параметр level указывает на левый отступ от края списка
function mso_cat_array_single($type = 'page', $order = 'category_name', $asc = 'ASC', $type_page = 'blog', $cache = true)
{
	if ($cache) // можно кэшировать
	{
		// возможно, что этот список уже сформирован, поэтому посмотрим в кэше
		$cache_key = mso_md5( __FUNCTION__ . $type . $order . $asc . $type_page );
		$k = mso_get_cache($cache_key);
		if ($k) return $k; // да есть в кэше
	}
	
	// если неверный тип, то возвратим пустой массив
	if ( ($type != 'page') and ($type != 'links') ) return array();

	$CI = & get_instance();
	
	/*
	$p = $CI->db->dbprefix;

	$query = $CI->db->query("
	
	SELECT {$p}category.*
	FROM {$p}category
	WHERE {$p}category.category_type = '{$type}'
	GROUP BY {$p}category.category_id
	ORDER BY {$p}category.{$order} {$asc}
	");
	*/
	/*
	JOIN {$p}cat2obj ON {$p}cat2obj.category_id = {$p}category.category_id	
	, COUNT({$p}cat2obj.page_id) AS count_pages
		JOIN {$p}page ON {$p}page.page_id = {$p}cat2obj.page_id
	JOIN {$p}page_type ON {$p}page.page_type_id = {$p}page_type.page_type_id
	
		AND {$p}page.page_status = 'publish'
		AND {$p}page_type.page_type_name = 'blog'
	*/
	
	

	//$CI->db->select();
	//$CI->db->select('COUNT(page_id) AS count_pages', false);
	
	$CI->db->from('category');
	$CI->db->where('category_type', $type);
	$CI->db->order_by($order, $asc);
	
	
	// $CI->db->from('page');
	
	// $CI->db->where('page_status', 'publish');
	// $CI->db->where('page_type_name', 'blog');
	
	// $CI->db->join('page_type', 'page_type.page_type_id = page.page_type_id');
	// $CI->db->join('cat2obj', 'cat2obj.page_id = page.page_id');
	
	//$CI->db->join('cat2obj', 'cat2obj.category_id = category.category_id');
	//$CI->db->group_by('cat2obj.page_id');
	
	$query = $CI->db->get();
	$cats = $query->result_array(); // здесь все рубрики
	
	$r = array();
	foreach ($cats as $row)
	{
		$r[$row['category_id']] = $row;
		$r[$row['category_id']]['level'] = 0;
		$r[$row['category_id']]['parents'] = '';
		$r[$row['category_id']]['childs'] = '';
		$r[$row['category_id']]['pages'] = array();
		$r[$row['category_id']]['links'] = array();
		
		// ошибочный парент!
		if ($r[$row['category_id']]['category_id_parent'] == $r[$row['category_id']]['category_id'])
			$r[$row['category_id']]['category_id_parent'] = 0;
		
	}
	
	// pr($r);

	# вычисляем уровень вложенности
	foreach ($r as $row)
	{
		$id = $row['category_id'];
		$parent = $row['category_id_parent'];
		
		if (!isset($r[$parent])) // а нету такого id!
		{
			$r[$id]['category_id_parent'] = 0;
			$parent = 0;
		}
		
		$max = 3;
		$level = 0;
		if ($parent>0)
		{
			$level++;
			$r[$id]['parents'] = $parent;
			$r[$parent]['childs'] = $id . ' ' . $r[$parent]['childs'];
			while ($parent>0)
			{
				// родитель родителя
				if (isset($r[$parent]['category_id_parent'])) $parent = $r[$parent]['category_id_parent'];
					else $parent = 0;

				if ($parent>0)
				{
					$level++;
					$r[$id]['parents'] = $r[$id]['parents'] . ' ' . $parent;
					$r[$parent]['childs'] = $id . ' ' . $r[$parent]['childs'];
				}
				else break(1);
			}
		}
		else 
		{
			$r[$id]['parents'] = '0';
		}
		$r[$id]['level'] = $level;
	}
	
	// сделаем ключом массива номер рубрики
	$cat = array();
	foreach ($r as $row) 
	{
		$cat[$row['category_id']] = $row;
		$cat[$row['category_id']]['childs'] = trim($cat[$row['category_id']]['childs']);
	}	
		
	// нам нужно получить количество записей по каждой рубрике
	$CI->db->select('cat2obj.*');
	$CI->db->from('category');
	$CI->db->join('cat2obj', 'cat2obj.category_id = category.category_id');
	$CI->db->join('page', 'cat2obj.page_id = page.page_id', 'left');
	$CI->db->join('page_type', 'page_type.page_type_id = page.page_type_id');
	$CI->db->where('page_status', 'publish');
	$CI->db->where('page_date_publish <', date('Y-m-d H:i:s'));
	// $CI->db->where('page_type_name', 'blog');
	if ($type_page) $CI->db->where('page_type_name', $type_page);
	$CI->db->where('category_type', $type);
	$CI->db->order_by('category.category_id');

	$query = $CI->db->get();
	$cats_post = $query->result_array(); // здесь все рубрики
	
	//pr($cats_post);
	
	
	foreach ($cats_post as $key=>$val) 
	{
		if ($type == 'page') 
			$cat[$val['category_id']]['pages'][] = $val['page_id'];
		else
			$cat[$val['category_id']]['links'][] = $val['links_id'];
		
		// $cat[$val['category_id']]['count_pages'] = count($cat[$val['category_id']]['pages']);
	}

	//pr($cat);

	if ($cache) mso_add_cache($cache_key, $cat); // сразу в кэш добавим
	
	return $cat;
}




# получение ul-списка всех рубрик
# рекомендуется для использования
function mso_cat_ul(
	$li_format = '%NAME%', // формат вывода: %NAME% %ID% %DESC% %LEVEL% %LINK_START% %LINK_END% %CHECKED% %COUNT_PAGES%
	$show_empty = true, // показывать рубрики без записей
	$checked_id = array(), // номера, где меняет %CHECKED% на checked="checked" - для чекбоксов
	$selected_id = array(), // номера, где отмечать <li class="selected">
	$ul_class = 'category', // класс главного списка ul
	$type_page = 'blog',
	$type = 'page', // тип - для выборки данных
	$order = 'category_menu_order', // сортировка по указанному полю
	$asc = 'asc', // порядок сортировки ASC или DESC
	$custom_array = false, // какой-то другой массив
	$include = array(), // только указанные рубрики и их дети
	$exclude = array() // исключить указанные рубрики
	
	) 
{
	// спасибо за помощь http://tedbeer.net/wp/
	
	// $include = array('3');
	// $exclude = array('3');
	
	
	// возможно, что этот список уже сформирован, поэтому посмотрим в кэше
	$cache_key = mso_md5('mso_cat_ul' . $li_format . $show_empty . implode(' ',$checked_id) . implode(' ',$selected_id)
						. $ul_class . $type_page . $type . $order . $asc . implode(' ', $include) . implode(' ', $exclude) );
	$k = mso_get_cache($cache_key);
	
	if ($k) // да есть в кэше
	{
		// находим текущий url (код повтояет внизу)
		$current_url = getinfo('siteurl') . mso_current_url(); // текущий урл
		$out = str_replace( '<a href="' . $current_url . '">', '<a href="' . $current_url . '" class="current_url">', $k);
		$pattern = '|<li class="(.*?)">(.*?)(<a href="' . $current_url . '")|ui';
		$out = preg_replace($pattern, '<li class="$1 current_url">$2$3', $out);
		return $out; 
	}
	
	# получим все рубрики в виде одномерного массива
	if ($custom_array) $all = $custom_array; // какой-то свой массив
		else $all = mso_cat_array_single('page', $order, $asc, $type_page);
	
	$top = array();

	foreach($all as $item)
	{
		$parentId = $item['category_id_parent'];
		$id = $item['category_id'];

		if( $parentId && isset($all[ $parentId]))
		{
			if( !isset($all[ $parentId]['children'])) 
				$all[ $parentId]['children'] = array($id);
			else $all[ $parentId]['children'][] = $id;
		} 
		else 
		{
			$top[] = $id;
		}
	}

	// непосредственно формирование списка
	$out = _mso_cat_ul_glue($top, $all, $li_format, $checked_id, $selected_id, $show_empty, $include, $exclude);
	# $out = str_replace("\n{*}</li>", "</li>", $out);
	
	if ($ul_class) $out = '<ul class="' . $ul_class . '">' . "\n" . $out. "\n" . '</ul>';
		else $out = '<ul>' . "\n" . $out. "\n" . '</ul>';

	mso_add_cache($cache_key, $out); // сразу в кэш добавим
	
	// отметим текущую рубрику. Поскольку у нас к кэше должен быть весь список и не делать кэш для каждого url
	// то мы просто перед отдачей заменяем текущий url на url с li.current_url 
	$current_url = getinfo('siteurl') . mso_current_url(); // текущий урл
	$out = str_replace( '<a href="' . $current_url . '">', '<a href="' . $current_url . '" class="current_url">', $out);
	$pattern = '|<li class="(.*?)">(.*?)(<a href="' . $current_url . '")|ui';
	$out = preg_replace($pattern, '<li class="$1 current_url">$2$3', $out);
	
	return $out;
}

# рекурсивная - вспомогательная к mso_cat_ul
function _mso_cat_ul_glue($in, &$all, $li_format, $checked_id, $selected_id, $show_empty, $include, $exclude)
{
	// Спасибо http://tedbeer.net/wp/
	
	$out = array();
	foreach( $in as $id)
	{
		
		if ($include)
		{
			// если указано включать только избранные рубрики
			if ( !in_array($all[$id]['category_id'], $include ) // указанная рубрика 
				 and
				 !in_array($all[$id]['category_id_parent'], $include ) ) // её дети
				 continue;
		}
		
		if ($exclude)
		{
			// исключить указанные рубрики
			if ( in_array($all[$id]['category_id'], $exclude ) // указанная рубрика 
				 or
				 in_array($all[$id]['category_id_parent'], $exclude ) ) // её дети
				 continue;
		}		
		
		$level = $all[$id]['level'];
		$count_pages = count($all[$id]['pages']);
		
		$css_level = 'level' . $level;
		$css_count = 'count' . $count_pages;
		
		$add = $li_format;
		$add = str_replace('%NAME%', $all[$id]['category_name'], $add);
		$add = str_replace('%ID%', $all[$id]['category_id'], $add);
		if ($all[$id]['category_desc'])
			$add = str_replace('%DESC%', '<div class="category_desc">' . $all[$id]['category_desc'] . '</div>', $add);
		else 
			$add = str_replace('%DESC%', '', $add);
		$add = str_replace('%LEVEL%', $level, $add);
		$add = str_replace('%COUNT_PAGES%', $count_pages, $add);
		
		if ($count_pages)
		{
			$add = str_replace('%LINK_START%', '<a href="' . mso_get_permalink_cat_slug($all[$id]['category_slug']) . '">', $add);
			$add = str_replace('%LINK_END%', '</a>', $add);
		}
		else
		{
			$add = str_replace('%LINK_START%', '', $add);
			$add = str_replace('%LINK_END%', '', $add);
		}
		
		if ($checked_id) // заменяем %CHECKED%
		{
			if (in_array($all[$id]['category_id'], $checked_id )) 
				$add = str_replace('%CHECKED%', ' checked="checked" ', $add);
			else $add = str_replace('%CHECKED%', '', $add);
		
		}
		else $add = str_replace('%CHECKED%', '', $add);
		
		if ($selected_id) 
		{
			if (in_array($all[$id]['category_id'], $selected_id )) 
				$li = str_repeat("\t", $level+1) . '<li class="selected ' . $css_count . ' ' . $css_level . '">';
			else $li = str_repeat("\t", $level+1) . '<li class="' . $css_count . ' ' . $css_level . '">';
		}
		else $li = '<li class="' . $css_count . ' ' . $css_level . '">';
		
		if ($show_empty) $out[] = $li . $add; // если показывать все, то в любом случае выводим
		else 
		{ // иначе проверяем кол-во
			if ($count_pages) $out[] = $li . $add;
		}
		
		
		if( isset( $all[$id]['children']))
		{
			$out[] = str_repeat("\t", $level+2) . "<ul class=\"child\">";
			$out[] = _mso_cat_ul_glue($all[$id]['children'], $all, $li_format, $checked_id, $selected_id, $show_empty, $include, $exclude);
			$out[] = str_repeat("\t", $level+2) . '</ul>';
		}
			$out[] = str_repeat("\t", $level+1) . '</li>';
			# $out[] = '{*}</li>';
		}
	return implode("\n", $out);
}

# Получает ID категории по slug
# false - не найдено, иначе вернет ID категории
# если slug не указан, то берется mso_segment(2)
# если $full = false, то возвращаем только category_id
# если $full = true, то возвращаем массив всех данных рубрики (mso_cat_array_single)
# идея Евгений Самборский (http://www.samborsky.com/)
# http://forum.maxsite.org/viewtopic.php?pid=38939
function mso_get_cat_from_slug($slug = '', $full = false)
{
	if (!$slug) $slug = mso_segment(2);
	$all_cats = mso_cat_array_single();
	
	foreach ($all_cats as $val)
	{
		if ($val['category_slug'] == $slug)
		{
			if ($full) return $val;
			else return $val['category_id'];
		}
	}
	
	return false;
}

# Получаем url рубрики по ID
# если id массив, то берется только первый элемент
function mso_get_cat_url_from_id($id = 0)
{
	$all_cats = mso_cat_array_single();
	
	if (is_array($id) and count($id)>0) $id = $id[0];
	
	foreach ($all_cats as $val)
	{
		if ($val['category_id'] == $id)
		{
			return getinfo('siteurl') . 'category/' . $val['category_slug'];
		}
	}
	
	return '';
}

?>