<?php 

require_once( getinfo('common_dir') . 'comments.php' ); 

// mso_get_comuser(0, array( 'limit'=> 20, 'tags'=>'<img><strong><em><i><b><u><s><font><pre><code><blockquote>' ) );

$comuser_info = mso_get_comuser(); // получим всю информацию о комюзере

mso_head_meta('title', getinfo('title') . ' - Комментаторы' ); // meta title страницы

// теперь сам вывод
# начальная часть шаблона
require(getinfo('template_dir') . 'main-start.php');

if ($comuser_info)
{
	extract($comuser_info[0]);

	if ($comusers_nik) echo '<h1>' . $comusers_nik . '</h1>';
		else echo '<h1>Комментатор ' . $comusers_id . '</h1>';
	
	if ($comusers_activate_string != $comusers_activate_key) // нет активации
		echo '<p><span style="color: red;">Активация не завершена.</span> <a href="' . getinfo('siteurl') . 'users/' . $comusers_id . '/edit">Завершить</a></p>';
	
	// выводим все данные
	if ($comusers_date_registr) echo '<p><strong>Дата регистрации:</strong> ' . $comusers_date_registr . '</p>';
	if ($comusers_nik) echo '<p><strong>Ник:</strong> ' . $comusers_nik . '</p>';
	if ($comusers_count_comments) echo '<p><strong>Комментариев:</strong> ' . $comusers_count_comments . '</p>';
	if ($comusers_url) echo '<p><strong>Сайт:</strong> <noindex><a rel="nofollow" href="' . $comusers_url . '">' . $comusers_url . '</a></noindex></p>';
	if ($comusers_icq) echo '<p><strong>ICQ:</strong> ' . $comusers_icq . '</p>';
	if ($comusers_msn) echo '<p><strong>MSN:</strong> ' . $comusers_msn . '</p>';
	if ($comusers_jaber) echo '<p><strong>Jaber:</strong> ' . $comusers_jaber . '</p>';
	if ($comusers_date_birth and $comusers_date_birth!='1970-01-01 00:00:00' and $comusers_date_birth!='0000-00-00 00:00:00'   ) 
			echo '<p><strong>Дата рождения:</strong> ' . $comusers_date_birth . '</p>';
	
	if ($comusers_description) 
	{
		$comusers_description = strip_tags($comusers_description);
		$comusers_description = str_replace("\n", '<br />', $comusers_description);
		$comusers_description = str_replace('<br /><br />', '<br />', $comusers_description);
		
		echo '<p><strong>О себе:</strong> ' . $comusers_description . '</p>';
	}
	
	if ($comusers_admin_note) echo '<p><strong>Примечание админа:</strong> ' . $comusers_admin_note . '</p>';
	
	echo '<p><a href="' . getinfo('siteurl') . 'users/' . $comusers_id . '/edit">Редактировать персональные данные</a></p>';
	
	if ($comments) // есть комментарии
	{
		echo '<br /><h2>Его последние комментарии:</h2><ul>';
		
		foreach ($comments as $comment)
		{
			echo '<li><span><a href="' . getinfo('siteurl') . 'page/' . mso_slug($comment['page_slug']) . '#comment-' . $comment['comments_id'] . '" name="comment-' . $comment['comments_id'] . '">' . $comment['page_title'] . '</a>';
			// echo ' | ' . $comments_url;
			echo '</span><br />' . $comment['comments_date'];
			echo '</span><br />' . $comment['comments_content'];
			echo '</li>';
		}
		
		echo '</ul>';
	}


}
else
{
	echo '<h1>404. Ничего не найдено...</h1>';
	echo '<p>Извините, пользователь с указанным номером не найден.</p>';
}

# конечная часть шаблона
require(getinfo('template_dir') . 'main-end.php');
	
?>