<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */


# функция автоподключения плагина
function main_menu_autoload()
{
	mso_create_allow('main_menu_edit', t('Админ-доступ к редактированию MainMenu', __FILE__));
	mso_hook_add( 'main_menu', 'main_menu_custom');
	mso_hook_add( 'head', 'main_menu_head');
}


# функция выполняется при деактивации (выкл) плагина
function main_menu_deactivate($args = array())
{	
	mso_delete_option('plugin_main_menu', 'plugins'); // удалим созданные опции
	return $args;
}

# функция выполняется при деинсталяции плагина
function main_menu_uninstall($args = array())
{	
	mso_delete_option('plugin_main_menu', 'plugins'); // удалим созданные опции
	mso_remove_allow('main_menu_edit'); // удалим созданные разрешения
	return $args;
}

# функция отрабатывающая миниопции плагина (function плагин_mso_options)
# если не нужна, удалите целиком
function main_menu_mso_options() 
{
	if ( !mso_check_allow('main_menu_edit') ) 
	{
		echo t('Доступ запрещен', 'plugins');
		return $args;
	}
	
	# ключ, тип, ключи массива
	mso_admin_plugin_options('plugin_main_menu', 'plugins', 
		array(
			'menu' => array(
							'type' => 'textarea', 
							'name' => 'Пункты меню', 
							'description' => 'Укажите полные адреса в меню и через | название ссылки. Каждый пункт в одной строчке.<br>Пример: http://maxsite.org/ | Блог Макса<br> Для группы меню используйте [ для открытия и ] для закрытия группы выпадающих пунктов. Например:<pre>[<br># | Медиа<br>audio | Аудио<br>video | Видео<br>photo | Фото<br>]</pre>', 
							'default' => ''
						),
			),
		'Настройки плагина Main menu', // титул
		'Укажите необходимые опции.'   // инфо
	);
}

# функции плагина
function main_menu_head($arg = array())
{
	echo mso_load_jquery();
	
	echo mso_load_jquery('ddsmoothmenu.js');
	
	if (file_exists(getinfo('template_dir') . 'main-menu.css'))
		echo NR . '		<link rel="stylesheet" href="' . getinfo('template_url') . 'main-menu.css' . '" type="text/css" media="screen">
	';
	else
		echo NR . '		<link rel="stylesheet" href="' . getinfo('plugins_url') . 'main_menu/main-menu.css' . '" type="text/css" media="screen">
	';		
}

# функции плагина
function main_menu_custom($arg = array())
{

	$options = mso_get_option('plugin_main_menu', 'plugins', array());
	
	if (!isset($options['menu'])) $options['menu'] = '';
	
	if (!$options['menu']) return $arg;
	
	$menu = mso_menu_build($options['menu'], 'selected', true);
	
	if ($menu)
		echo '
		<div id="MainMenu">
			<div id="smoothmenu1" class="ddsmoothmenu">
				<ul>
				' . $menu . '
				</ul>
			</div>
		</div>
	';
	
	return $arg;
}

?>