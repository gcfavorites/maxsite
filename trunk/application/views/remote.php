<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MaxSite CMS
 * (c) http://maxsite.org/
 */

# начальная функция удаленного постинга

function mso_remote_post()
{
	# каждый запрос должен содержать логин, пароль, ключ безопасности, имя функции и её аргументы
	if ( $post = mso_check_post(array('remote_login', 'remote_password', 'remote_key', 'remote_function')) )
	{
		
		$remote_login = $post['remote_login'];
		$remote_password = $post['remote_password'];
		$remote_key = $post['remote_key']; # remote_key пока пустой
		
		$CI = & get_instance();
		
		# всегда проверяем логин и пароль
		
		$CI->db->from('users'); # таблица users
		$CI->db->select('users_id');
		$CI->db->limit(1); # одно значение
		$CI->db->where( array('users_login'=>$remote_login, 
							  'users_password'=>mso_md5($remote_password)) );
		
		$query = $CI->db->get();
		if ($query->num_rows() == 0) # нет такого - возможно взлом
		{
			echo 'ERROR: Login/password incorrect';
			return false;
		}
		
		# поучаем имя функции и её аргументы
		$remote_function = $post['remote_function'];
		$remote_function_args = $post['remote_function_args'];
		
		# контроль
		# echo 'remote_function : ' . $remote_function . "<br />";
		# echo 'remote_function_args : '; pr($remote_function_args);
		
		# выполняем запрашиваемую функцию
		if ($remote_function == 'addtwonumbers') echo mso_remote_f_addtwonumbers($remote_function_args);
		elseif ($remote_function == 'hello') echo mso_remote_f_hello($remote_function_args);
		
		/*
		планируемые функции

		+ Hello : возвращает Hello!
		+ addTwoNumbers : сложение двух чисел
		
		- getGeneralInfo : общая информация о сайте
		
		- getUsersBlogs : список всех авторов блога
		- getUserInfo : информация о авторе
		
		- newPost : новый пост
		- editPost : редактировать пост
		- getPost : получить пост
		- getRecentPostTitles : получить список всех постов (без текстов)
		
		- getCategoryList : рубрики
		- newCategory : новая рубрика
		
		??? getFileNameUploads : список уже загруженных файлов
		??? newMediaObject : загрузить файл
		*/
	}
	else
	{
		echo 'ERROR: No access';
	}
}

# функция addtwonumbers
function mso_remote_f_addtwonumbers($args = array())
{
	# в начале всегда проверяем корректность полученных аргументов данной функции
	# если неверные, то либо выводим ошибку, либо ставим дефолтные значения
	
	if (!isset($args[1])) $args[1] = 1;
	if (!isset($args[2])) $args[2] = 1;
	
	$out = $args[1] + $args[2];
	
	return $out;
}

# функция hello
function mso_remote_f_hello($args = array())
{
	return 'Hello!';
}

mso_remote_post();

?>