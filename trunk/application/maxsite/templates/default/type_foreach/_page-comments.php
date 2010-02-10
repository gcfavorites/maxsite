<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

		extract($comment);
		
		// pr($comment);
		
		$comments_date = mso_date_convert('Y-m-d в H:i:s', $comments_date);
		
		echo '<li style="clear: both;"><span><a href="#comment-' . $comments_id . '" name="comment-' . $comments_id . '">' . $comments_date . '</a>';
		echo ' | ' . $comments_url;
		
		if ($edit_link) echo ' | <a href="' . $edit_link . $comments_id . '">edit</a>';
		
		if (!$comments_approved) echo ' | '. t('Ожидает модерации');

		
		$avatar_url = '';
		if ($comusers_avatar_url) $avatar_url = $comusers_avatar_url;
		elseif ($users_avatar_url) $avatar_url = $users_avatar_url;
		
		if (!$avatar_url) 
		{ // аватарки нет, попробуем получить из gravatara
			// pr($comment);
			
			if ($users_email) $grav_email = $users_email;
			elseif ($comusers_email) $grav_email = $comusers_email;
			else $grav_email = '';
			
			if ($grav_email)
			{
				$avatar_url = "http://www.gravatar.com/avatar.php?gravatar_id=" 
						. md5($grav_email)
						// . "&default=" . urlencode('')
						. "&size=80";
			}
		}
		
		if ($avatar_url) 
			$avatar_url = '<noindex><img src="' . $avatar_url . '" width="80" height="80" alt="" title="" style="float: left; margin: 5px 15px 10px 0;"/></noindex>';
		
		echo '</span><br>' . $avatar_url;
		echo mso_comments_content($comments_content);
		
		echo '</li>'; 
	
?>