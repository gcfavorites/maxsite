<span><a name="comments"></a></span>
<?php
# коммментарии

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

if ($comments) // есть страницы
{ 	
	echo '<div class="comments">';
	echo '<h3 class="comments">Комментариев: ' . count($comments) . '</h3>';
	
	echo '<ol>';
	
	foreach ($comments as $comment)  // выводим в цикле
	{
		extract($comment);
		
		$comments_date = mso_date_convert('Y-m-d в H:i:s', $comments_date);
		
		echo '<li><span><a href="#comment-' . $comments_id . '" name="comment-' . $comments_id . '">' . $comments_date . '</a>';
		echo ' | ' . $comments_url;
		
		if ($edit_link) echo ' | <a href="' . $edit_link . $comments_id . '">edit</a>';
		
		if (!$comments_approved) echo ' | Ожидает модерации';

		echo '</span><br />' . mso_comments_content($comments_content);
		echo '</li>'; 
		
	//	pr($comment);
	}
	
	echo '</ol>';
	echo '</div>';
}

if ($page_comment_allow)
{
	echo '<h3 class="comments">Оставьте комментарий!</h3>';
	require( 'page-comment-form.php' ); // форма комментариев
}
else
{
	// echo '<div class="no-comments"><h3 class="comments">Комментарии запрещены</h3></div>';
}


?>