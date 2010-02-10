<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */


# функция автоподключения плагина
function admin_announce_autoload($args = array())
{
	mso_create_allow('admin_announce_edit', t('Админ-доступ к административному анонсу', __FILE__));
	mso_hook_add( 'admin_init', 'admin_announce_admin_init'); # хук на админку
	mso_hook_add( 'admin_home', 'admin_announce'); # хук на админ-анонс
}


# функция выполняется при деактивации (выкл) плагина
function admin_announce_uninstall($args = array())
{
	mso_delete_float_option('plugin_admin_announce', 'plugins'); // удалим созданные опции
	mso_remove_allow('admin_announce_edit'); // удалим созданные разрешения
	return $args;
}


# функция выполняется при указаном хуке admin_init
function admin_announce_admin_init($args = array()) 
{
	if ( !mso_check_allow('admin_announce_admin_page') ) 
	{
		return $args;
	}

	$this_plugin_url = 'plugin_admin_announce'; // url и hook
	mso_admin_menu_add('plugins', $this_plugin_url, t('Админ-анонс', __FILE__));
	mso_admin_url_hook ($this_plugin_url, 'admin_announce_admin_page');

	return $args;
}

# функция вызываемая при хуке, указанном в mso_admin_url_hook
function admin_announce_admin_page($args = array()) 
{
	# выносим админские функции отдельно в файл
	global $MSO;
	if ( !mso_check_allow('admin_announce_admin_page') ) 
	{
		echo t('Доступ запрещен', 'plugins');
		return $args;
	}

	mso_hook_add_dinamic( 'mso_admin_header', ' return $args . "' . t('Админ-анонс', __FILE__) . ' "; ' );
	mso_hook_add_dinamic( 'admin_title', ' return "' . t('Админ-анонс', __FILE__) . ' - " . $args; ' );

	require($MSO->config['plugins_dir'] . 'admin_announce/admin.php');
}



function admin_announce($arg = array())
{
	$options_key = 'plugin_admin_announce';
	$options = mso_get_float_option($options_key, 'plugins', array());
	if ( !isset($options['admin_announce']) )  $options['admin_announce']  = ''; // включен ли
	if ( !isset($options['admin_statistic']) ) $options['admin_statistic'] = true; // По умолчанию показываем статистику.
	if ( !isset($options['admin_showall']) )   $options['admin_showall']   = true; // По умолчанию показываем статистику всем.
	if ( !isset($options['delta']) or ($options['delta'] == 0) ) $options['delta'] = 10;


	if (trim($options['admin_announce']) <> '') echo NR. '<div class="info">'. $options['admin_announce']. '</div>'. NR;
	if ( !$options['admin_statistic'] ) return; //Если статистику не показываем, то выходим.
	if ( !$options['admin_showall'] ) if ( !mso_check_allow('admin_announce_admin_page') ) return;

	$CI = & get_instance();

	$cache_key = 'admin_announce_pages';
	if ( $k = mso_get_cache($cache_key) ) 
	{
		$all_title = $k;
	}
	else
	{
		$CI = & get_instance();
		$CI->db->select('page_id, page_title, page_slug, page_view_count');
		//$CI->db->where('page_date_publish <', date('Y-m-d H:i:s'));
		$CI->db->where('page_status', 'publish');
		$CI->db->from('page');
		$query = $CI->db->get();

		$all_title = $query->result_array();
		mso_add_cache($cache_key, $all_title);
	}

	$summ = $count = $avgcount = 0;
	$maxcount = $mincount = $all_title[0]['page_view_count'];
	foreach ( $all_title as $page ) :
		$count++;
		$summ += $page['page_view_count'];
		if ($maxcount < $page['page_view_count']) $maxcount = $page['page_view_count'];
		if ($mincount > $page['page_view_count']) $mincount = $page['page_view_count'];
	endforeach;
	$avgcount = $summ/$count;


	//echo $maxcount. ' '. $mincount. ' '. $avgcount;
	//pr($all_title);
	global $MSO;
	//pr($MSO);
	$maxout = NR. '<div class="info"><h3>'. t('Наиболее просматриваемые страницы', __FILE__). '</h3><ul>';
	$minout = NR. '<div class="info"><h3>'. t('Наименее просматриваемые страницы', __FILE__). '</h3><ul>';
	$avgout = NR. '<div class="info"><h3>'. t('Средне просматриваемые страницы', __FILE__). '</h3><ul>';
	foreach ( $all_title as $page ) :
		$out = NR. '<li><a href="'. $MSO->config['site_url']. 'page/'. $page['page_slug']. '" target="_blank">'. $page['page_title']. '</a> — '.
				t('просмотров: ', __FILE__). $page['page_view_count']. ' (<a href="'. $MSO->config['site_admin_url']. 'page_edit/'. $page['page_id']. '">'. t('редактировать', __FILE__). '</a>)</li>';
		if ( $page['page_view_count'] > ($maxcount - $options['delta']) ) $maxout .= $out;
		elseif ( $page['page_view_count'] < ($mincount + $options['delta']) ) $minout .= $out;
		elseif ( ($page['page_view_count'] < ($avgcount + $options['delta']/2)) and ($page['page_view_count'] > ($avgcount - $options['delta']/2)) )  $avgout .= $out;
	endforeach;
	$maxout .= '</ul></div>';
	$minout .= '</ul></div>';
	$avgout .= '</ul></div>';
	echo '<div class="info"><h3>'. t('Статистика', __FILE__). '</h3><ul><li>'. t('Всего страниц: ', __FILE__). $count. '</li><li>'. t('Всего просмотров: ', __FILE__). $summ.
		'</li><li>'. t('Максимум просмотров страницы: ', __FILE__). $maxcount. '</li><li>'. t('Минимум просмотров страницы: ', __FILE__). $mincount.
		'</li><li>'. t('В среднем: ', __FILE__). round($avgcount). '</li></ul></div>'. $maxout. $minout. $avgout;
}


?>