<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

	# опции редактора в виде массива
	$_options = array(
			
			# сортировка меток
			'tags_sort' => array(
							'type' => 'select', 
							'values' => t('0||По количеству записей (обратно) # 1||По количеству записей # 2||По алфавиту # 3||По алфавиту (обратно)'),
							'name' => t('Сортировка меток'), 
							'description' => t('Используется для отображения облака меток'),
							'default' => '0'
						),
			
			# количество меток
			'tags_count' => array(
							'type' => 'text', 
							'name' => t('Количество меток'), 
							'description' => t('Используется для отображения облака меток'),
							'default' => '20'
						),
						
			# разрешить комментарии
			'comment_allow_checked' => array(
							'type' => 'select', 
							'values' => '1||Отмечать # 0||Не отмечать',
							'name' => t('Разрешить комментирование'), 
							'description' => t('Отмечать опцию «Разрешить комментирование» по-умолчанию'),
							'default' => '1'
						),	
						
			# разрешить rss 
			'feed_allow_checked' => array(
							'type' => 'select', 
							'values' => '1||Отмечать # 0||Не отмечать',
							'name' => t('Разрешить публикацию RSS'), 
							'description' => t('Отмечать опцию «Публикация в RSS» по-умолчанию'),
							'default' => '1'
						),					
			
	);
	
	
	# если нужно подключить свои опции используйте хук editor_options
	$_options = mso_hook('editor_options', $_options);
	
	
	# отображение опций
	mso_admin_plugin_options('editor_options', 'admin', 
		$_options,
		t('Настройки редактора'), // титул
		t('Выберите нужные опции редактора'), // инфо
		false
	);

?>