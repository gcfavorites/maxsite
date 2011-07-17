<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

# функция автоподключения плагина
function ushki_to_hook_autoload()
{
	// для админки плагин не работает
	if (mso_segment(1)!= 'admin') mso_hook_add('init', 'ushki_to_hook_custom');
}

# функция выполняется при активации (вкл) плагина
function ushki_to_hook_activate($args = array())
{	
	mso_create_allow('ushki_to_hook_edit', t('Админ-доступ к настройкам', 'plugins') . ' ' . t('ushki to hook', __FILE__));
	return $args;
}

# функция выполняется при деактивации (выкл) плагина
function ushki_to_hook_deactivate($args = array())
{	
	mso_delete_option('plugin_ushki_to_hook', 'plugins'); // удалим созданные опции
	return $args;
}

# функция выполняется при деинсталяции плагина
function ushki_to_hook_uninstall($args = array())
{	
	// mso_delete_option('plugin_to_hook', 'plugins'); // удалим созданные опции
	mso_remove_allow('ushki_to_hook_edit'); // удалим созданные разрешения
	return $args;
}

# функция отрабатывающая миниопции плагина (function плагин_mso_options)
# если не нужна, удалите целиком
function ushki_to_hook_mso_options() 
{
	if ( !mso_check_allow('ushki_to_hook_edit') ) 
	{
		echo t('Доступ запрещен', 'plugins');
		return;
	}
	
	if (!function_exists('ushka')) $info = ' <span style="color: red">Включите плагин «Ушки»!</span>';
		else $info = 'Укажите необходимые опции плагина.';
	
	# ключ, тип, ключи массива
	mso_admin_plugin_options('plugin_ushki_to_hook', 'plugins', 
		array(
			'option1' => array(
							'type' => 'textarea', 
							'name' => t('Задайте хуки и ушки', __FILE__), 
							'description' => t('
							Например: 
							<pre>    content_end | page_bottom</pre>
							- где <strong>«content_end»</strong> - хук по которому сработает ушка <strong>«page_bottom»</strong>.
							
							<br><br>Если необходимо задать приоритет хука, то он указывается третьим параметром (стандарт: 10, чем больше, тем раньше сработает хук), например: 
							<pre>    content_end | page_bottom | 20</pre>
							
							<br>Стандартно этот плагин отдаёт ушку через <strong>echo</strong>. Если же необходимо отдавать функцию через <strong>return</strong>, то четвертым параметром указывается один из вариантов: <strong>echo</strong> или <strong>return</strong>:
							 <pre>    content_contetn | my_page | 20 | return</pre>
							<br>
							', __FILE__), 
							'default' => ''
						),
			),
		'Настройки плагина «Ушки к хукам»', // титул
		$info  // инфо
	);
}

# функции плагина
function ushki_to_hook_custom($args = array())
{
	// если плагин ушек не включен, то выходим
	if (!function_exists('ushka')) return $args;
	
	$options = mso_get_option('plugin_ushki_to_hook', 'plugins', array());
	if (isset($options['option1']) and $options['option1']) 
	{
		// mso_hook_add('хук', 'функция', приоритет);
		// content_end | page_bottom | 20
		// ushka($name_ushka = '', $delim_ushka = '<br>', $not_exists_ushka = '')
		
		
		// разобъем построчкно в массив
		$lines = explode("\n", $options['option1']);
		
		// рассматриваем каждую строчку
		foreach ($lines as $line)
		{
			// если линия пустая, то пропускаем
			if (!trim($line)) continue;
			
			// разобъем строчку на части
			$ar = explode("|", $line);
			
			// раскидываем значения
			$hook = (isset($ar[0]) and trim($ar[0])) ? trim($ar[0]) : false; // хук 
			$ushka = (isset($ar[1]) and trim($ar[1])) ? trim($ar[1]) : false; // ушка
			$prior = (isset($ar[2]) and trim($ar[2])) ? (int) trim($ar[2]) : false; // приоритет хука
			$echo_return = (isset($ar[3]) and trim($ar[3])) ? trim($ar[3]) : ''; // как выводить по return или echo
			
			
			if ($echo_return != 'return' and $echo_return != 'echo' ) $echo_return = 'echo';
			
			if ($hook and $ushka) // указаны хук и ушка
			{	
				// создадим функцию для хука
				$fn = '_ushki_to_hook_' . $hook . '_' . $ushka;
				
				// если её еще не создали
				if (!function_exists($fn)) 
				{
					eval(
					' function '.$fn.'($args = "")
					{
						' . $echo_return . ' ushka("'.$ushka.'");
						return $args;
					}
					'
					);
				}
				
				// и регистрируем в хуке
				if ($prior) 
					mso_hook_add($hook, $fn, $prior); // указан приоритет
				else
					mso_hook_add($hook, $fn); // приоритет не указан
				
			}
		}
	}
}

# end file