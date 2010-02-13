<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

# функция автоподключения плагина
function addzakl_autoload($args = array())
{
	if ( is_type('page') )
	{
		mso_hook_add( 'content_end', 'addzakl_content_end');
	}
}


# функции плагина
function addzakl_content_end($args = array())
{
	global $page;
	
	$sep = ' ';  # разделитель мужду кнопками - можно указать свой
	
	# ширина и высота картинок
	$width_height = ' width="16" height="16"';  

	$path = getinfo('plugins_url') . 'addzakl/images/'; # путь к картинкам
	
	$post_title = urlencode ( stripslashes($page['page_title'] . ' - ' . mso_get_option('name_site', 'general') ) );
	$post_link = getinfo('siteurl') . mso_current_url();
	$out = '';
	
	$img_src = 'twitter.png';
	$link = '<a rel="nofollow" href="http://twitter.com/home/?status=' . urlencode (stripslashes(mb_substr($page['page_title'], 0, 139 - mb_strlen($post_link, 'UTF8'), 'UTF8') . ' ' . $post_link)) . '">';
	$out .= $link . '<img border="0" title="Retweet twitter.com" alt="twitter.com" src="' . $path . $img_src  . '"' . $width_height . '></a>';	
	
	$img_src = 'buzz.png';
	$link = '<a rel="nofollow" href="http://www.google.com/reader/link?url=' . $post_link . '&title=' . $post_title . '&srcURL=' . getinfo('siteurl') . '">';
	$out .= $sep . $link . '<img border="0" title="Google Buzz" alt="Google Buzz" src="' . $path . $img_src  . '"' . $width_height . '></a>';		
	
	$img_src = 'google_bmarks.gif';
	$link = '<a rel="nofollow" href="http://www.google.com/bookmarks/mark?op=edit&amp;bkmk=' . $post_link . '&amp;title=' . $post_title .  '">';
	$out .= $sep . $link . '<img border="0" title="google.com" alt="google.com" src="' . $path . $img_src  . '"' . $width_height . '></a>';
	
	$img_src = 'bobrdobr.gif';
	$link = '<a rel="nofollow" href="http://bobrdobr.ru/addext.html?url=' . $post_link . '&amp;title=' . $post_title .  '">';
	$out .= $sep . $link . '<img border="0" title="bobrdobr.ru" alt="bobrdobr.ru" src="' . $path . $img_src  . '"' . $width_height . '></a>';
	
	$img_src = 'delicious.gif';
	$link = '<a rel="nofollow" href="http://del.icio.us/post?url=' . $post_link . '&amp;title=' . $post_title .  '">';
	$out .= $sep . $link . '<img border="0" title="del.icio.us" alt="del.icio.us" src="' . $path . $img_src  . '"' . $width_height . '></a>';

	$img_src = 'technorati.gif';
	$link = '<a rel="nofollow" href="http://www.technorati.com/faves?add=' . $post_link . '">';
	$out .= $sep . $link . '<img border="0" title="technorati.com" alt="technorati.com" src="' . $path . $img_src  . '"></a>';

	$img_src = 'linkstore.gif';
	$link = '<a rel="nofollow" href="http://www.linkstore.ru/servlet/LinkStore?a=add&amp;url=' . $post_link . '&amp;title=' . $post_title .  '">';
	$out .= $sep . $link . '<img border="0" title="linkstore.ru" alt="linkstore.ru" src="' . $path . $img_src  . '"' . $width_height . '></a>';
	
	$img_src = 'news2-ru.gif';
	$link = '<a rel="nofollow" href="http://news2.ru/add_story.php?url=' . $post_link . '">';
	$out .= $sep . $link . '<img border="0" title="news2.ru" alt="news2.ru" src="' . $path . $img_src  . '"></a>';

	$img_src = 'rumark.gif';
	$link = '<a rel="nofollow" href="http://rumarkz.ru/bookmarks/?action=add&amp;popup=1&amp;address=' . $post_link . '&amp;title=' . $post_title .  '">';
	$out .= $sep . $link . '<img border="0" title="rumarkz.ru" alt="rumarkz.ru" src="' . $path . $img_src  . '"' . $width_height . '></a>';
	
	$img_src = 'memori.gif';
	$link = '<a rel="nofollow" href="http://memori.ru/link/?sm=1&amp;u_data[url]=' . $post_link . '&amp;u_data[name]=' . $post_title .  '">';
	$out .= $sep . $link . '<img border="0" title="memori.ru" alt="memori.ru" src="' . $path . $img_src  . '"' . $width_height . '></a>';
	
	$img_src = 'moemesto.gif';
	$link = '<a rel="nofollow" href="http://moemesto.ru/post.php?url=' . $post_link . '&amp;title=' . $post_title .  '">';
	$out .= $sep . $link . '<img border="0" title="moemesto.ru" alt="moemesto.ru" src="' . $path . $img_src  . '"' . $width_height . '></a>';

	echo "\n<div class=\"addzakl\"><span style=\"display: none\"><![CDATA[<noindex>]]></span>" . $out . "<span style=\"display: none\"><![CDATA[</noindex>]]></span></div>\n";
	
	return $args;
}

?>