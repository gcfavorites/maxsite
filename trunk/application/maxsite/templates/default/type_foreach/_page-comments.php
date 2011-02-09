<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

		
		extract($comment);
		
		if ($users_id) $class = ' class="users"';
		elseif ($comusers_id) $class = ' class="comusers"';
		else $class = ' class="anonim"';
		
		$comments_date = mso_date_convert('Y-m-d в H:i:s', $comments_date);
		
		echo NR . '<li style="clear: both"' . $class . '><span class="date"><a href="#comment-' . $comments_id . '" name="comment-' . $comments_id . '">' . $comments_date . '</a></span>';
		echo ' | <span class="url">' . $comments_url . '</span>';
		
		if ($edit_link) echo ' | <a href="' . $edit_link . $comments_id . '">edit</a>';
		
		if (!$comments_approved) echo ' | '. t('Ожидает модерации');

		
		$avatar_url = '';
		if ($comusers_avatar_url) $avatar_url = $comusers_avatar_url;
		elseif ($users_avatar_url) $avatar_url = $users_avatar_url;
		
		if (!$avatar_url) 
		{ // аватарки нет, попробуем получить из gravatara
			
			if ($users_email) $grav_email = $users_email;
			elseif ($comusers_email) $grav_email = $comusers_email;
			else $grav_email = '';
			
			if ($grav_email)
			{
				if ($gravatar_type = mso_get_option('gravatar_type', 'templates', ''))
					$d = '&amp;d=' . urlencode($gravatar_type);
				else 
					$d = '';
				
				$avatar_url = "http://www.gravatar.com/avatar.php?gravatar_id=" 
						. md5($grav_email)
						. "&amp;size=80"
						. $d;
			}
		}
		
		if ($avatar_url) 
			$avatar_url = '<span style="display: none"><![CDATA[<noindex>]]></span><img src="' . $avatar_url . '" width="80" height="80" alt="" title="" style="float: left; margin: 5px 15px 10px 0;" class="gravatar"><span style="display: none"><![CDATA[</noindex>]]></span>';
		
		echo '<div class="comments_content">' . $avatar_url;
		echo mso_comments_content($comments_content);
		echo '</div>';
		
		echo '</li>'; 
	
?>