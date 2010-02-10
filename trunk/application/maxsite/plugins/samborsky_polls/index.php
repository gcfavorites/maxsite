<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * Плагин голосования от Евгения Самборского
 * Работы по разработке начаты 4 апреля 2009 года
 * sp = {Samborsky Polls}
 */

# функция автоподключения плагина
function samborsky_polls_autoload($args = array()){
	global $MSO;
	
	if( is_type('admin') ){
		// хук на админку
		mso_hook_add('admin_init','samborsky_polls_init');
	}
	
	// Ядро
	require($MSO->config['plugins_dir'] . 'samborsky_polls/sp_kernel.php');
	
	// Хук в <head></head>
	mso_hook_add('head', 'samborsky_polls_head');
}

function samborsky_polls_head($args = array()){
	
	mso_load_jquery();
	
	$path = getinfo('plugins_url') . 'samborsky_polls/';
	echo '<script type="text/javascript" src="',$path,'js/kernel.js"></script>',NR;
	echo '<link rel="stylesheet" href="',$path,'css/style.css" type="text/css" media="screen">',NR;
}

# функция выполняется при активации (вкл) плагина
function samborsky_polls_activate($args = array()){
	global $MSO;
	
	mso_create_allow('samborsky_polls_edit','Админ-доступ к samborsky_polls',__FILE__);
	
	require($MSO->config['plugins_dir'] . 'samborsky_polls/install.php');
	sp_install();
	sp_add_options();
	
	return $args;
}

# функция выполняется при деактивации (выкл) плагина
function samborsky_polls_deactivate($args = array()){
	// ничего не трогаем при деактивации
	return $args;
}

# функция выполняется при деинстяляции плагина
function samborsky_polls_uninstall($args = array()){
	
	$CI = &get_instance();
	$CI->load->dbforge();
	
	// Удаляем таблицы
	$CI->dbforge->drop_table('sp_questions');	
	$CI->dbforge->drop_table('sp_answers');
	$CI->dbforge->drop_table('sp_logs');
			
	return $args;
}

# функция выполняется при указаном хуке admin_init
function samborsky_polls_init($args = array()){
	
	if( !mso_check_allow('samborsky_polls_edit') ){
		return $args;
	}
	
	$this_plugin_url = 'samborsky_polls';

	mso_admin_menu_add('plugins',$this_plugin_url,'Голосования');
	mso_admin_url_hook($this_plugin_url, 'samborsky_polls_admin_page');
	
	return $args;
}

# функция вызываемая при хуке, указанном в mso_admin_url_hook
function samborsky_polls_admin_page($args = array()){
	global $MSO;
	
	# выносим админские функции отдельно в файл	
	if( !mso_check_allow('samborsky_polls_edit') ){
		echo t('Доступ запрещен', 'plugins');
		return $args;
	}
	
	mso_hook_add_dinamic('mso_admin_header',' return $args . "' . t('samborsky_polls', __FILE__) . '"; ' );
	mso_hook_add_dinamic('admin_title',' return "' . t('samborsky_polls', __FILE__) . ' - " . $args; ' );
	
	require($MSO->config['plugins_dir'] . 'samborsky_polls/admin.php');
}

/***
 * Выводит голосование либо результаты голосования (если определено что юзер голосовал)
 * @return 
 * @param object $id[optional] - необязательный параметр, который выводит голосование с нужным ID
 */
function samborsky_polls($id = 0){
	global $MSO;
	
	$question = new sp_question();
	return $question->get_active_code();
}

/***
 * Выводит архив голосований
 * @return 
 */
function samborspy_polls_archive(){
	global $MSO;
	
	$archive = new sp_archive;
	return $archive->get();
}


?>