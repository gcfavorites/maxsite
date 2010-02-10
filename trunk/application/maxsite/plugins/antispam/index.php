<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://maxsite.org/
 */


# функция автоподключения плагина
function antispam_autoload($args = array())
{
	mso_create_allow('antispam_edit', 'Админ-доступ к antispam');
	mso_hook_add( 'admin_init', 'antispam_admin_init'); # хук на админку
	mso_hook_add( 'new_comments_check_spam', 'antispam_check_spam'); # хук новый комментарий
	mso_hook_add( 'new_comments_check_spam_comusers', 'antispam_check_spam_comusers'); # хук новый комментарий для комюзера
}


# функция выполняется при деактивации (выкл) плагина
function antispam_deactivate($args = array())
{	
	mso_delete_option('plugin_antispam', 'plugins'); // удалим созданные опции
	return $args;
}


# функция выполняется при указаном хуке admin_init
function antispam_admin_init($args = array()) 
{
	if ( !mso_check_allow('antispam_admin_page') ) 
	{
		return $args;
	}
	
	$this_plugin_url = 'plugin_antispam'; // url и hook
	
	# добавляем свой пункт в меню админки
	# первый параметр - группа в меню
	# второй - это действие/адрес в url - http://сайт/admin/demo
	#			можно использовать добавочный, например demo/edit = http://сайт/admin/demo/edit
	# Третий - название ссылки	
	
	mso_admin_menu_add('plugins', $this_plugin_url, 'Антиспам');

	# прописываем для указаного admin_url_ + $this_plugin_url - (он будет в url) 
	# связанную функцию именно она будет вызываться, когда 
	# будет идти обращение по адресу http://сайт/admin/_null
	mso_admin_url_hook ($this_plugin_url, 'antispam_admin_page');
	
	return $args;
}

# функция вызываемая при хуке, указанном в mso_admin_url_hook
function antispam_admin_page($args = array()) 
{
	# выносим админские функции отдельно в файл
	global $MSO;
	if ( !mso_check_allow('antispam_admin_page') ) 
	{
		echo 'Доступ запрещен';
		return $args;
	}
	
	mso_hook_add_dinamic( 'mso_admin_header', ' return $args . "Admin "; ' );
	mso_hook_add_dinamic( 'admin_title', ' return "Admin - " . $args; ' );

	require($MSO->config['plugins_dir'] . 'antispam/admin.php');
}

# функция логгинга - сохраняем в файл все спамовские входы
function antispam_log($file = '', $msg = '')
{
	global $MSO;
	
	if ($file)
	{
		$fn = $MSO->config['uploads_dir'] . $file;
		$fp = fopen( $fn, "a+");
		fwrite($fp,  '====================' . "\n" . $msg . "\n\n");
		fclose($fp);
	}
}


# функция проверки 
function antispam_check_spam($arg = array())
{
	// в аргумента куча информации
	/*
Array
(
    [comments_content] => текст
    [comments_date] => 2008-07-10 13:39:30
    [comments_author_ip] => 127.0.0.1
    [comments_page_id] => 13
    [comments_server] => Array
        (
            [REDIRECT_STATUS] => 200
            [HTTP_HOST] => localhost
            [HTTP_USER_AGENT] => Mozilla/5.0 (Windows; U; Windows NT 5.1; ru; rv:1.9) Gecko/2008052906 Firefox/3.0
            [HTTP_ACCEPT] => text/html,application/xhtml+xml,application/xml;q=0.9, ;q=0.8
            [HTTP_ACCEPT_LANGUAGE] => ru
            [HTTP_ACCEPT_ENCODING] => gzip,deflate
            [HTTP_ACCEPT_CHARSET] => windows-1251,utf-8;q=0.7,*;q=0.7
            [HTTP_KEEP_ALIVE] => 300
            [HTTP_CONNECTION] => keep-alive
            [HTTP_REFERER] => http://localhost/codeigniter/page/souvlaki-ignitus-carborundum
            [HTTP_COOKIE] => PHPSESSID=
            [CONTENT_TYPE] => application/x-www-form-urlencoded
            [CONTENT_LENGTH] => 187
            [WINDIR] => C:\WINDOWS
            [SERVER_SIGNATURE] => Apache/2.2.4 (Win32) DAV/2 mod_ssl/2.2.4 Open
            [SERVER_SOFTWARE] => Apache/2.2.4 (Win32) DAV/2 mod_ssl/2.2.4 OpenSSL/0.9.8e mod_autoindex_color PHP/5.2.3
            [SERVER_NAME] => localhost
            [SERVER_ADDR] => 127.0.0.1
            [SERVER_PORT] => 80
            [REMOTE_ADDR] => 127.0.0.1
            [DOCUMENT_ROOT] => D:/xampplite/htdocs
            [SERVER_ADMIN] => admin@localhost
            [SCRIPT_FILENAME] => D:/xampplite/htdocs/codeigniter/index.php
            [REMOTE_PORT] => 3339
            [REDIRECT_URL] => /codeigniter/page/souvlaki-ignitus-carborundum
            [GATEWAY_INTERFACE] => CGI/1.1
            [SERVER_PROTOCOL] => HTTP/1.1
            [REQUEST_METHOD] => POST
            [QUERY_STRING] => 
            [REQUEST_URI] => /codeigniter/page/souvlaki-ignitus-carborundum
            [SCRIPT_NAME] => /codeigniter/index.php
            [PATH_INFO] => /page/souvlaki-ignitus-carborundum
            [PATH_TRANSLATED] => D:\xampplite\htdocs\page\souvlaki-ignitus-carborundum
            [PHP_SELF] => /codeigniter/index.php/page/souvlaki-ignitus-carborundum
            [REQUEST_TIME] => 1215686369
            [argv] => Array
                (
                )
            [argc] => 0
        )
)
	*/
	
	$options_key = 'plugin_antispam';
	
	$options = mso_get_option($options_key, 'plugins', array()); // все опции
	
	if ( !isset($options['antispam_on']) ) $options['antispam_on'] = false; // включен ли антиспам
	if 	(!$options['antispam_on']) return;
	
	if ( !isset($options['logging']) ) $options['logging'] = false; // разрешено ли логирование?
	if ( !isset($options['moderation_links']) ) $options['moderation_links'] = true; // модерировать все ссылки
	if ( !isset($options['logging_file']) ) $options['logging_file'] = ''; // разрешено ли логирование?
	if ( !isset($options['black_ip']) ) $options['black_ip'] = ''; // черный список IP
	if ( !isset($options['black_words']) ) $options['black_words'] = ''; // черный список слов
	if ( !isset($options['moderation_words']) ) $options['moderation_words'] = ''; // список слов модерации
	
	
	$black_ip = split("\n", trim($options['black_ip']));
	
	if (in_array($arg['comments_author_ip'], $black_ip)) 
	{
		if ($options['logging']) antispam_log($options['logging_file'], 
												  'BLACK_IP: ' . $arg['comments_author_ip'] . NR 
												. 'PAGE_ID: ' . $arg['comments_page_id'] . NR
												. 'DATE: ' . $arg['comments_date'] . NR
												. 'CONTENT: ' . NR . $arg['comments_content']
												);
		return array('check_spam'=>true, 'message'=>'Для вашего IP комментирование запрещено!');
	}
	
	$black_words = split("\n", trim($options['black_words']));
	
	foreach ($black_words as $word)
	{
		if (mb_stristr($arg['comments_content'], $word, false, 'UTF-8')) // есть какое-то вхождение
		{
			if ($options['logging']) antispam_log($options['logging_file'], 
												  'BLACK WORD: ' . $word . NR 
												. 'IP: ' . $arg['comments_author_ip'] . NR 
												. 'PAGE_ID: ' . $arg['comments_page_id'] . NR
												. 'DATE: ' . $arg['comments_date'] . NR
												. 'CONTENT: ' . NR . $arg['comments_content']
												);
			return array('check_spam'=>true, 'message'=>'Вы используете запрещенные слова!');
		} 
	}
	
	if ($options['moderation_links'])
	{
		// Если в комментарии хоть одна ссылка - сразу на модерацию
		$check_a = (strpos( $arg['comments_content'], '<a') === false ) ? false : true;
		if ($check_a) return array('moderation'=>1); // отправим на модерацию
	}


	$moderation_words = split("\n", trim($options['moderation_words']));
	
	foreach ($moderation_words as $word)
	{
		if (mb_stristr($arg['comments_content'], $word, false, 'UTF-8')) // есть какое-то вхождение
		{
			return array('moderation'=>1);
		} 
	}

}

# модерирование комюзеров
function antispam_check_spam_comusers($arg = array())
{
	# входящий параметр 
	# array( 'comments_page_id' => $comments_page_id, 'comments_comusers_id' => $comusers_id, 
	# 'comments_com_approved' => $comments_com_approved
	
	# выход: 1 разрешить 0 - модерация
	
	// смотрим есть ли id в списке модерируемом. если есть, то возвращаем на модерацию = 0
	$options_key = 'plugin_antispam';
	
	$options = mso_get_option($options_key, 'plugins', array()); // все опции
	
	if ( !isset($options['antispam_on']) ) return $arg['comments_com_approved']; // включен ли антиспам
	if ( !isset($options['moderation_comusers']) ) return $arg['comments_com_approved']; // нет списка

	$nums = split("\n", trim($options['moderation_comusers'])); // список комюзеров
	
	foreach ($nums as $num)
	{
		if ( ( (int) trim($num)) == $arg['comments_comusers_id']) return 0;
	}
	
	return 1;
}

?>