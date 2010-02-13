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
	mso_hook_add( 'admin_head', 'admin_announce_head');
}


# функция выполняется при деактивации (выкл) плагина
function admin_announce_uninstall($args = array())
{
	mso_delete_float_option('plugin_admin_announce', 'plugins'); // удалим созданные опции
	mso_remove_allow('admin_announce_edit'); // удалим созданные разрешения
	return $args;
}


function admin_announce_head($args = array()) 
{
	echo NR . '<link rel="stylesheet" href="' . getinfo('plugins_url') . 'tabs/to_template/tabs.css" type="text/css" media="screen">' . NR;
	echo mso_load_jquery();
	echo mso_load_jquery('ui/ui.core.packed.js');
	echo mso_load_jquery('ui/ui.tabs.packed.js');

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
	if ( !mso_check_allow('admin_announce_admin_page') ) 
	{
		echo t('Доступ запрещен', 'plugins');
		return $args;
	}

	mso_hook_add_dinamic( 'mso_admin_header', ' return $args . "' . t('Админ-анонс', __FILE__) . ' "; ' );
	mso_hook_add_dinamic( 'admin_title', ' return "' . t('Админ-анонс', __FILE__) . ' - " . $args; ' );

	require(getinfo('plugins_dir') . 'admin_announce/admin.php');
}



function admin_announce($arg = array())
{
	$options_key = 'plugin_admin_announce';
	$options = mso_get_float_option($options_key, 'plugins', array());
	if ( !isset($options['admin_announce']) )  $options['admin_announce']  = ''; // включен ли
	if ( !isset($options['admin_statistic']) ) $options['admin_statistic'] = true; // По умолчанию показываем статистику.
	if ( !isset($options['admin_showall']) )   $options['admin_showall']   = true; // По умолчанию показываем статистику всем.
	if ( !isset($options['delta']) or ($options['delta'] == 0) ) $options['delta'] = 10;
	if ( !isset($options['use_visual']) )      $options['use_visual']      = true;


	$tabs = array();
	$out = '';
	if (trim($options['admin_announce']) <> '')
	{
		if ($options['use_visual'] == 1) $tabs[] = array( t('Админ-анонс', __FILE__), NR. '<div class="info">'. mso_hook('content', $options['admin_announce']). '</div>'. NR );
			else $tabs[] = array( t('Админ-анонс', __FILE__),  NR. '<div class="info">'. $options['admin_announce']. '</div>'. NR );
	}
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
	//pr($MSO);

	$tabs[] = array(
					t('Статистика', __FILE__),
				    '<div class="info"><ul><li>' . t('Всего страниц: ', __FILE__) . $count . '</li><li>' . t('Всего просмотров: ', __FILE__) . $summ .
				    '</li><li>' . t('Дельта подсчёта: ', __FILE__) . $options['delta'] .
					'</li><li>' . t('Максимум просмотров страницы: ', __FILE__) . $maxcount. '</li><li>' . t('Минимум просмотров страницы: ', __FILE__). $mincount.
					'</li><li>' . t('В среднем: ', __FILE__). round($avgcount) . '</li></ul></div>'
					);

	$CI->load->library('table');
	$tmpl = array (
			'table_open'		  => '<table class="page tablesorter" border="0" id="pagetable">',
			'row_alt_start'		  => '<tr class="alt">',
			'cell_alt_start'	  => '<td class="alt">',
			'heading_row_start' 	=> NR . '<thead><tr>',
			'heading_row_end' 		=> '</tr></thead>' . NR,
			'heading_cell_start'	=> '<th style="cursor: pointer;">',
			'heading_cell_end'		=> '</th>',
				);
	$CI->table->set_template($tmpl);
	$CI->table->set_heading(t('Заголовок', __FILE__), t('Просмотров', __FILE__));

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


	foreach ( $all_title as $page ) :
		if ( $page['page_view_count'] > ($maxcount - $options['delta']) )
		$CI->table->add_row(
							'<a href="' . getinfo('site_url') . 'page/' . $page['page_slug'] . '" target="_blank">' . $page['page_title'] . '</a><br>' . '(<a href="' . getinfo('site_admin_url'). 'page_edit/' . $page['page_id']. '">' . t('редактировать', __FILE__). '</a>)',
							$page['page_view_count']
							);
	endforeach;

	$tabs[] = array(
					t('Наиболее просматриваемые страницы', __FILE__),
					'<div class="tabs-widget-fragment">' . $CI->table->generate() . '</div>'
					);

	$CI->table->clear();

	$CI->table->set_template($tmpl);
	$CI->table->set_heading(t('Заголовок', __FILE__), t('Просмотров', __FILE__));

	foreach ( $all_title as $page ) :
		if ( ($page['page_view_count'] < ($avgcount + $options['delta'])) and ($page['page_view_count'] > ($avgcount - $options['delta'])) )
		$CI->table->add_row(
							'<a href="' . getinfo('site_url') . 'page/' . $page['page_slug'] . '" target="_blank">' . $page['page_title'] . '</a><br>' . '(<a href="' . getinfo('site_admin_url'). 'page_edit/' . $page['page_id']. '">' . t('редактировать', __FILE__). '</a>)',
							$page['page_view_count']
							);
	endforeach;

	$tabs[] = array(
					t('Средне просматриваемые страницы', __FILE__),
					'<div class="tabs-widget-fragment">' . $CI->table->generate() . '</div>'
					);

	$CI->table->clear();

	$CI->table->set_template($tmpl);
	$CI->table->set_heading(t('Заголовок', __FILE__), t('Просмотров', __FILE__));

	foreach ( $all_title as $page ) :
		if ( $page['page_view_count'] < ($mincount + $options['delta']) )
		$CI->table->add_row(
							'<a href="' . getinfo('site_url') . 'page/' . $page['page_slug'] . '" target="_blank">' . $page['page_title'] . '</a><br>' . '(<a href="' . getinfo('site_admin_url'). 'page_edit/' . $page['page_id']. '">' . t('редактировать', __FILE__). '</a>)',
							$page['page_view_count']
							);
	endforeach;

	$tabs[] = array(
					t('Наименее просматриваемые страницы', __FILE__),
					'<div class="tabs-widget-fragment">' . $CI->table->generate() . '</div>'
					);

	if ($tabs) // есть закладки, можно выводить
	{
		$out .= NR . '<div id="tabs-widget" class="tabs-widget-all"><ul class="tabs-menu">';
		foreach($tabs as $key => $tab)
			$out .= '<li><a href="#tabs-widget-fragment-' . $key . '"><span>' . $tab[0] . '</span></a></li>' . NR;
		$out .= '</ul>';
		foreach($tabs as $key => $tab)
			$out .= '<div id="tabs-widget-fragment-' . $key . '" class="tabs-widget-fragment">' . $tab[1] . '</div>' . NR;
	}

	if ($out) $out .=  <<<EOF
	<script> 
		$("#tabs-widget > ul").tabs({ fx: { height: 'toggle', opacity: 'toggle', duration: 'fast' } });
	</script> 
EOF;
	echo $out;


}


?>