<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once( getinfo('common_dir') . 'comments.php' ); // функции комментариев

// получаем список комментариев текущей страницы
$comments = mso_get_comments(false, array('limit' => mso_get_option('comments_count', 'templates', '10'), 'order'=>'desc'));

mso_head_meta('title', 'Последние комментарии - ' . getinfo('title') ); //  meta title страницы


require('main-start.php');

echo '<h1 class="comments">Последние коммментарии</h1>';
echo '<p class="info"><a href="' . getinfo('siteurl') . 'comments/feed">Подписаться по RSS</a></p>';

echo '<div class="comments">';

// pr($comments);
if ($comments) // есть страницы
{ 	
	echo '<ul>';
	
	foreach ($comments as $comment)  // выводим в цикле
	{
		extract($comment);

		echo '<li><span><a href="' . getinfo('siteurl') . 'page/' . mso_slug($page_slug) . '#comment-' . $comments_id . '" name="comment-' . $comments_id . '">' . $page_title . '</a>';
		echo ' | ' . $comments_url;
		echo '</span><br />' . $comments_date;
		echo '</span><br />' . $comments_content;
		echo '</li>';
		
	//	pr($comment);
	}
	
	echo '</ul>';
}

echo '</div>';

require('main-end.php'); 
?>