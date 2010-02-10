<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

	$CI = & get_instance();
	
	$options_key = 'sape';
	
	if ( $post = mso_check_post(array('f_session_id', 'f_submit', 'f_kod')) )
	{
		mso_checkreferer();
		
		$options = array();
		$options['kod'] = $post['f_kod'];
		
		$options['go'] = 0; // признак, что код установлен верно - каталог есть и доступен для записи
		
		// проверим введенный код
		$fn = $_SERVER['DOCUMENT_ROOT'] . '/' . $options['kod'] . '/sape.php';
		
		if (!file_exists($fn)) // нет файла, просто выведем предупреждение
		{
			echo '<div class="error">Введенный вам код, возможно неправильный, или вы не распаковали архив на сервере!</div>';
		}
		else // есть файл, проверим что каталог доступен на запись
		{
			if (!is_writable($_SERVER['DOCUMENT_ROOT'] . '/' . $options['kod'])) 
				echo '<div class="error">Указанный вами каталог недоступен для записи. Установите для него права 777 (разрешающие запись).</div>';
			else
				$options['go'] = 1; // нет ошибок
		}
		
		$options['start'] = isset($post['f_start']) ? 1 : 0;
		$options['context'] = isset($post['f_context']) ? 1 : 0;
		$options['context_comment'] = isset($post['f_context_comment']) ? 1 : 0;
		$options['test'] = isset($post['f_test']) ? 1 : 0;
		$options['anticheck'] = isset($post['f_anticheck']) ? 1 : 0;

		mso_add_option($options_key, $options, 'plugins');
		echo '<div class="update">Настройки обновлены!</div>';
	}
	
?>
<h1>Настройка Sape.ru</h1>
<p>С помощью этой страницы вы можете настроить свою работу с <a href="http://www.sape.ru/r.aa92aef9c6.php" target="_blank">sape.ru</a>. Перед началом работы вам следует выполнить следующие действия:</p>
<ol>
<li>Скачать с <a href="http://www.sape.ru/r.aa92aef9c6.php" target="_blank">sape.ru</a> архив с вашим кодом для загрузки на сервер.
<li>Распаковать архив. Внутри него будет лежать папка с именем вроде такого: «8df7s4sd2if89as5v34vbez3e2».
<li>Загрузите эту папку на ваш сервер в корень(!!!) вашего сайта.
<li>Установите права на эту папку «777» (разрешающие запись).
</ol>
<br/>
<p><strong>Только после этого вы можете выполнить настройки на этой странице!</strong></p>
<ol>
<li>Укажите свой код (он совпадает с именем папки).
<li>Отметьте будете ли вы использовать контекстные ссылки. (Контекстные ссылки могут быть только при использовании обычных.)
</ol>
<br/>
<p><strong>Для размещения блоков вывода ссылок вы можете воспользоваться виджетами или вручную прописать вызов функций в шаблоне.</strong></p>
<p><strong>При использовании виджетов</strong> у вас будет возможность указать количество ссылок для данного виджета (виджетов может быть несколько). Обратите внимание, что в последнем виджете вам следует оставить это поле пустым, чтобы вывести оставшиеся ссылки.</p>
<p><strong>При ручном размещении</strong> вам следует в шаблоне прописать вызов функции <strong>sape_out()</strong></p>
<pre>
	
	if (function_exists('sape_out')) sape_out();
	
</pre>

<p>Для того, чтобы разбить вывод ссылок на несколько частей, следует указать в <strong>sape_out()</strong> количество ссылок для вывода. Обратите внимание, что последний вызов <strong>sape_out()</strong> должен быть без параметров - это выведет все оставшиеся ссылки.</p>
<pre>
	
	if (function_exists('sape_out')) sape_out(3); // первый блок из 3-х ссылок
	
	if (function_exists('sape_out')) sape_out(4); // второй блок из 4-х ссылок
	
	if (function_exists('sape_out')) sape_out(); // последний блок - оставшиеся ссылки
	
</pre>

<p><strong>После размещения всех блоков вы можете проверить верность размещения.</strong> Для этого отметьте опцию «Режим проверки установленного кода» и обновите страницу сайта. С помощью браузера (FireFox) просмотрите исходный код страницы. В каждом установленном блоке вы увидите закомментированное число или строку <strong>&lt;!--check code--&gt;</strong>. Если данной строки нет, значит код установлен неверно. Если строчка есть, то код установлен верно и опцию нужно отключить.</p>

<p><strong>Примечание</strong>. Если вы размещаете код через виджеты, то при включенной проверке в виджете появится текст «Код sape.ru установлен верно!»</p>

<p>После проверки кода, вы можете войти в свой аккаунт на sape.ru и добавить свой сайт. В течение некоторого времени, робот <a href="http://www.sape.ru/r.aa92aef9c6.php" target="_blank">sape.ru</a> его проиндексирует.</p>

<p><strong>Обратите внимание! Помощь по установке кода <a href="http://www.sape.ru/r.aa92aef9c6.php" target="_blank">sape.ru</a>, любые подсказки и разъяснения пр этому поводу я оказываю только в двух случаях: 1) Вы зарегистрировались по моей ссылке и являетесь моим рефералом; 2) На платной основе - 30WMZ.</strong></p>
<br>

<?php
		$options = mso_get_option($options_key, 'plugins', array());
		if ( !isset($options['kod']) ) $options['kod'] = ''; 
		if ( !isset($options['context']) ) $options['context'] = true; 
		if ( !isset($options['context_comment']) ) $options['context_comment'] = true; 
		if ( !isset($options['test']) ) $options['test'] = false; 
		if ( !isset($options['start']) ) $options['start'] = true; 
		if ( !isset($options['anticheck']) ) $options['anticheck'] = false; 
		
		$checked_context = $options['context'] ? ' checked="checked" ' : '';
		$checked_context_comment = $options['context_comment'] ? ' checked="checked" ' : '';
		$checked_test = $options['test'] ? ' checked="checked" ' : '';
		$checked_start = $options['start'] ? ' checked="checked" ' : '';
		$checked_anticheck = $options['anticheck'] ? ' checked="checked" ' : '';
		
		$form = '';
		$form .= '<p><strong>Ваш номер/код в <a href="http://www.sape.ru/r.aa92aef9c6.php" target="_blank">sape.ru</a>:</strong> ' . ' <input name="f_kod" type="text" style="width: 300px;" value="' . $options['kod'] . '"></p>';
		
		$form .= '<p><label><input name="f_start" type="checkbox"' . $checked_start . '> Включить плагин</label></p>';
		$form .= '<p><label><input name="f_context" type="checkbox"' . $checked_context . '> Использовать контекстные ссылки</label></p>';
		$form .= '<p><label><input name="f_context_comment" type="checkbox"' . $checked_context_comment . '> Использовать контекстные ссылки в комментариях</label></p>';
		$form .= '<p><label><input name="f_test" type="checkbox"' . $checked_test . '> Режим проверки установленного кода</label></p>';
		$form .= '<p><label><input name="f_anticheck" type="checkbox"' . $checked_anticheck . '> Включить антиобнаружитель продажных ссылок</label></p>';
		
		echo '<form action="" method="post">' . mso_form_session('f_session_id');
		echo $form;
		echo '<input type="submit" name="f_submit" value=" Сохранить изменения " style="margin: 25px 0 5px 0;">';
		echo '</form>';

?>