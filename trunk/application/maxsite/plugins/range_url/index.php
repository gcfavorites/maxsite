<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

# функция автоподключения плагина
function range_url_autoload()
{
	// в админке и rss не используем
	if (mso_segment(1) != 'admin' and !is_feed()) mso_hook_add('init', 'range_url_init'); # хук на init
}

# функция выполняется при активации (вкл) плагина
function range_url_activate($args = array())
{	
	mso_create_allow('range_url_edit', t('Админ-доступ к настройкам Range URL', 'plugins') . ' ' . t('range_url', 'plugins'));
	return $args;
}

# функция выполняется при деактивации (выкл) плагина
function range_url_deactivate($args = array())
{	
	mso_delete_option('plugin_range_url', 'plugins'); // удалим созданные опции
	return $args;
}

# функция выполняется при деинсталяции плагина
function range_url_uninstall($args = array())
{	
	mso_delete_option('plugin_range_url', 'plugins'); // удалим созданные опции
	mso_remove_allow('range_url_edit'); // удалим созданные разрешения
	return $args;
}

# функция отрабатывающая миниопции плагина (function плагин_mso_options)
# если не нужна, удалите целиком
function range_url_mso_options() 
{
	if ( !mso_check_allow('range_url_edit') ) 
	{
		echo t('Доступ запрещен', 'plugins');
		return;
	}
	
	
	# ключ, тип, ключи массива
	mso_admin_plugin_options('plugin_range_url', 'plugins', 
		array(
		
			'default-templates' => array(
							'type' => 'checkbox', 
							'name' => t('Использовать стандартные шаблоны URL.', 'plugins'), 
							'description' => t('В этом случае они будут использоваться автоматически. Если опция не активна, то будут использоваться только указанные вами шаблоны URL.', 'plugins'), 
							'default' => '1'
						),
						
			'templates' => array(
							'type' => 'textarea', 
							'rows' => 10,
							'name' => t('Шаблоны URL', 'plugins'), 
							'description' => t('Каждый сегмент URL представляет собой часть адреса (исключая адрес сайта) ограниченную символами «/». Сегменты в шаблоне следует указывать в скобках. Если какой-то сегмент может быть произвольным, то он указывается как «(*)». В сегментах шаблона можно использовать регулярные выражения.<br>Например: <br>(page)(*)<br>
(page)(*)(next)(*)', 'plugins'), 
							'default' => ''
						),
			'min-count-segment' => array(
							'type' => 'text', 
							'name' => t('Минимальное количество сегментов URL которые будут разрешены автоматически', 'plugins'), 
							'description' => t('Например, если нужно разрешить все адреса, состоящие из одного сегмента, то укажите «1»: адреса вида «http://сайт/about» будут одобряться автоматически, но «http://сайт/about/slug» будут уже проверяться по указанным шаблонам. Если указать «2», то автоматически будут одобрены и «http://сайт/about», и «http://сайт/about/slug», но не «http://сайт/about/slug/slug2».', 'plugins'), 
							'default' => '1'
						),
						
						
						
			),
		t('Настройки плагина Range URL', 'plugins'), // титул
		t('Плагин позволяет задавать шаблоны URL, которые будут считаться правильными для сайта. Все остальные адреса будут отдаваться как 404-страница. Если вы используете какие-то свои типы данных, то укажите соответствующий шаблон.', 'plugins')   // инфо
	);
}

# основная функция
function range_url_init($arg = array())
{
	global $MSO;
	
	$current_url = mso_current_url(); // текущий адрес
	if ($current_url === '') return $arg; // главная
	
	$options = mso_get_option('plugin_range_url', 'plugins', array());
	if (!isset($options['templates']) ) $options['templates'] = '';
	$templates = explode("\n", trim($options['templates'])); // разобъем по строкам
	
	
	if (!isset($options['default-templates']) ) $options['default-templates'] = true;
	
	if ($options['default-templates'])
	{
		// в шаблоны добавим дефолтные адреса
		array_push($templates, 
				'(loginform)', 
				'(loginform)(*)', 
				'(contact)', 
				'(logout)', 
				'(login)', 
				'(password-recovery)',
				'(require-maxsite)', 
				'(ajax)', 
				'(remote)',
				'(sitemap)', 
				
				'(home)(next)(*)',
				
				'(category)(*)',
				'(category)(*)(next)(*)',
				
				'(page)(*)',
				'(page)(*)(next)(*)',
				
				'(tag)(*)',
				'(tag)(*)(next)(*)',
				
				'(archive)',
				'(archive)(*)',
				'(archive)(*)(next)(*)',
				'(archive)(*)(*)',
				'(archive)(*)(*)(next)(*)',
				'(archive)(*)(*)(*)',
				'(archive)(*)(*)(*)(next)(*)',
				
				'(author)(*)',
				'(author)(*)(next)(*)',
				
				'(users)',
				'(users)(*)',
				'(users)(*)(edit)',
				'(users)(*)(lost)',
				
				'(search)(*)',
				'(search)(*)(next)(*)',
				
				'(comments)', // адреса, совпадающие с type-файлом, можно в принципе не добавлять
				'(comments)(*)',
				'(comments)(*)(next)(*)'
		);
	}

	if (!isset($options['min-count-segment']) ) $options['min-count-segment'] = 1; // минимальное количество сегментов
	$options['min-count-segment'] = (int) $options['min-count-segment'];
	
	if (count(explode('/', $current_url)) <= $options['min-count-segment']) return $arg; // адрес имеет менее N сегментов

	$allow = false; // результат 
	
	foreach($templates as $template)
	{
		$template = trim($template);
		if (!$template ) continue;
		
		$reg = str_replace('(*)', '(.[^/]*)', $template);
		$reg = '~' . str_replace(')(', '){1}/(', $reg) . '\z~siu';

		if (preg_match($reg, $current_url)) 
		{
			$allow = true;
			break;
		}
	}
	
	# pr($allow);
	
	if (!$allow)
	{
		$MSO->data['type'] = 'page_404';
	}
	
	
	return $arg;
}

# end file