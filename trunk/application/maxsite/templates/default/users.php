<?php 

// параметры для получения страниц
$par = array( 'limit' => mso_get_option('limit_post', 'templates', '15'), 
			'cut' => mso_get_option('more', 'templates', 'Читать полностью »'),
			'cat_order'=>'category_name', 'cat_order_asc'=>'asc' ); 

// $pages = mso_get_pages($par, $pagination); // получим все - второй параметр нужен для сформированной пагинации


// теперь сам вывод
# начальная часть шаблона
require('main-start.php');
	
echo 'Юзеры ';
// echo 'Выводим информацию о комюзере. внизу форму email+пароль, чтобы изменить инфорацию.';


# конечная часть шаблона
require('main-end.php');
	
?>