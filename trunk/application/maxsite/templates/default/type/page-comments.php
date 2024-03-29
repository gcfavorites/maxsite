<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

mso_cur_dir_lang('templates');

# коммментарии
echo '<span><a id="comments"></a></span>';

// получаем список комментариев текущей страницы
require_once( getinfo('common_dir') . 'comments.php' ); // функции комментариев

// если был отправлен новый коммент, то обрабатываем его и выводим сообщение в случае ошибки
echo mso_get_new_comment( array('page_title'=>$page_title) ); 


// получаем все разрешенные комментарии
$comments = mso_get_comments($page_id);


// в сессии проверяем может быть только что отправленный комментарий
if (isset($MSO->data['session']['comments']) and $MSO->data['session']['comments'] )
{
	$anon_comm = $MSO->data['session']['comments']; // массив: id-коммент
	
	// получаем комментарии для этого юзера
	$an_comments = mso_get_comments($page_id, array('anonim_comments'=>$anon_comm));
	
	// добавляем в вывод
	if ($an_comments) $comments = array_merge($comments, $an_comments);
}

if (is_login()) $edit_link = getinfo('siteurl') . 'admin/comments/edit/';
	else $edit_link = '';

if ($comments or $page_comment_allow) echo NR . '<div class="type type_page_comments">' . NR;

if ($comments) // есть страницы
{ 	

	if ($f = mso_page_foreach('page-comments-do')) require($f); // подключаем кастомный вывод
	else 
	{
		echo '<div class="comments">';
		echo mso_get_val('page_comments_count_start', '<h3 class="comments">') . t('Комментариев') . ': ' . count($comments) . mso_get_val('page_comments_count_end', '</h3>');
	}
	
	echo '<ol>';
	
	foreach ($comments as $comment)  // выводим в цикле
	{
		if ($f = mso_page_foreach('page-comments')) 
		{
			require($f); // подключаем кастомный вывод
			continue; // следующая итерация
		}
		
		extract($comment);
		
		if ($users_id) $class = ' class="users"';
		elseif ($comusers_id) $class = ' class="comusers"';
		else $class = ' class="anonim"';
		
		$comments_date = mso_date_convert('Y-m-d в H:i:s', $comments_date);
		
		echo NR . '<li style="clear: both"' . $class . '><div class="comment-info"><span class="date"><a href="#comment-' . $comments_id . '" id="comment-' . $comments_id . '">' . $comments_date . '</a></span>';
		echo ' | <span class="url">' . $comments_url . '</span>';
		
		if ($edit_link) echo ' | <a href="' . $edit_link . $comments_id . '">edit</a>';
		
		if (!$comments_approved) echo ' | '. t('Ожидает модерации');
		
		echo '</div>';
		
		$avatar_url = '';
		if ($comusers_avatar_url) $avatar_url = $comusers_avatar_url;
		elseif ($users_avatar_url) $avatar_url = $users_avatar_url;
		
		$avatar_size = (int) mso_get_option('gravatar_size', 'templates', 80);
		if ($avatar_size < 1 or $avatar_size > 512) $avatar_size = 80;
		
		if (!$avatar_url) 
		{ // аватарки нет, попробуем получить из gravatara
			
			if ($users_email) $grav_email = $users_email;
			elseif ($comusers_email) $grav_email = $comusers_email;
			else $grav_email = '';
			
			if ($gravatar_type = mso_get_option('gravatar_type', 'templates', ''))
				$d = '&amp;d=' . urlencode($gravatar_type);
			else 
				$d = '';
			
			$avatar_url = "http://www.gravatar.com/avatar.php?gravatar_id=" 
					. md5($grav_email)
					. "&amp;size=" . $avatar_size
					. $d;
		}
		
		if ($avatar_url) 
			$avatar_url = '<img src="' . $avatar_url . '" width="' . $avatar_size . '" height="'. $avatar_size . '" alt="" title="" style="float: left; margin: 5px 10px 10px 0;" class="gravatar">';
		
		echo '<div class="comments_content">' . $avatar_url;
		echo mso_comments_content($comments_content);
		echo '</div>';
		
		echo '</li>'; 
		
	//	pr($comment);
	}
	
	echo '</ol>';
	echo '</div>' . NR;
}

if ($page_comment_allow)
{
	// если запрещены комментарии и от анонимов и от комюзеров, то выходим
	if ( mso_get_option('allow_comment_anonim', 'general', '1') 
		or mso_get_option('allow_comment_comusers', 'general', '1') )  
	{
		if ($f = mso_page_foreach('page-comment-form-do')) require($f); // подключаем кастомный вывод
		else echo '<div class="break"></div>' . mso_get_val('leave_a_comment_start', '<h3 class="comments">') . mso_get_option('leave_a_comment', 'templates', t('Оставьте комментарий!')). mso_get_val('leave_a_comment_end', '</h3>');
		
		if ($f = mso_page_foreach('page-comment-form')) 
		{
			require($f); // подключаем кастомный вывод
		}
		else 
		{
			// форма комментариев
			// page-comment-form.php может быть в type своего шаблона
			$fn1 = getinfo('template_dir') . 'type/page-comment-form.php'; 		 // путь в шаблоне
			$fn2 = getinfo('templates_dir') . 'default/type/page-comment-form.php'; // путь в default
			if ( file_exists($fn1) ) require($fn1); // если есть, подключаем шаблонный
			elseif (file_exists($fn2)) require($fn2); // нет, значит дефолтный
		}
	}
}

if ($comments or $page_comment_allow) echo NR . '</div><!-- class="type type_page_comments" -->' . NR;

?>