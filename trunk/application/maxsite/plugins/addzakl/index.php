<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 *
 * Alexander Schilling
 * (c) http://maxsite.thedignity.biz
 *
 * Icons
 * (c) http://icondock.com
 */

# функция автоподключения плагина
function addzakl_autoload($args = array())
{
	if ( is_type('page') )
	{
		$options = mso_get_option('plugin_addzakl', 'plugins', array());
	
		if (!isset($options['priory'])) $options['priory'] = 10;
		mso_hook_add('content_end', 'addzakl_content_end', $options['priory']);
	}
}

# функция выполняется при деинсталяции плагина
function addzakl_uninstall($args = array())
{	
	mso_delete_option('plugin_addzakl', 'plugins'); // удалим созданные опции
	return $args;
}

function addzakl_mso_options() 
{
	# ключ, тип, ключи массива
	mso_admin_plugin_options('plugin_addzakl', 'plugins', 
		array(
			'size' => array(
							'type' => 'select', 
							'name' => 'Размеры иконок', 
							'description' => 'Выберите размеры иконок',
							'values' => '16 # 24',  // правила для select как в ini-файлах
							'default' => '16'
						),
			'text-do' => array(
							'type' => 'text', 
							'name' => 'Текст перед иконками', 
							'description' => 'Укажите произвольный текст перед иконками. Можно использовать HTML', 
							'default' => ''
						),
			'text-posle' => array(
							'type' => 'text', 
							'name' => 'Текст после иконками', 
							'description' => 'Укажите произвольный текст после иконок', 
							'default' => ''
						),	
								
			'priory' => array(
							'type' => 'text', 
							'name' => 'Приоритет блока', 
							'description' => 'Позволяет расположить блок до или после аналогичных. Используйте значения от 1 до 90. Чем больше значение, тем выше блок. По умолчанию значение равно 10.', 
							'default' => '10'
						),					
			),
		'Закдадки на соц.сервисы', // титул
		'Укажите необходимые опции.'   // инфо
	);
}

# функции плагина
function addzakl_content_end($args = array())
{
	global $page;
	
	$options = mso_get_option('plugin_addzakl', 'plugins', array());
	
	if (!isset($options['size'])) $options['size'] = 16;
	if (!isset($options['text-do'])) $options['text-do'] = '';
	if (!isset($options['text-posle'])) $options['text-posle'] = '';
	
	$size = (int) $options['size']; // размер икнонок
	
	$sep = ' ';  # разделитель мужду кнопками - можно указать свой
	
	# ширина и высота картинок
	$width_height = ' width="' . $size . '" height="' . $size . '"';  
	
	if ($size == 16) // если размер 16, то каталог /images/
		$path = getinfo('plugins_url') . 'addzakl/images/'; # путь к картинкам
	else // каталог /imagesXX/
		$path = getinfo('plugins_url') . 'addzakl/images' . $size . '/'; # путь к картинкам
		
	$post_title = urlencode ( stripslashes($page['page_title'] . ' - ' . mso_get_option('name_site', 'general') ) );
	$post_link = getinfo('siteurl') . mso_current_url();
	$out = '';
	
	$img_src = 'twitter.png';
	$link = '<a rel="nofollow" href="http://twitter.com/home/?status=' . urlencode (stripslashes(mb_substr($page['page_title'], 0, 139 - mb_strlen($post_link, 'UTF8'), 'UTF8') . ' ' . $post_link)) . '">';
	$out .= $link . '<img border="0" title="Добавить в Twitter" alt="twitter.com" src="' . $path . $img_src  . '"' . $width_height . '></a>';	
	
	$img_src = 'facebook.png';
	$link = '<a rel="nofollow" href="http://www.facebook.com/sharer.php?u=' . $post_link . '">';
	$out .= $sep . $link . '<img border="0" title="Поделиться в Facebook" alt="facebook.com" src="' . $path . $img_src  . '"' . $width_height . '></a>';		
	
	$img_src = 'vkontakte.png';
	$link = '<a rel="nofollow" href="http://vkontakte.ru/share.php?url=' . $post_link . '&title=' . $post_title  . '">';
	$out .= $sep . $link . '<img border="0" title="Поделиться В Контакте" alt="vkontakte.ru" src="' . $path . $img_src  . '"' . $width_height . '></a>';

	$img_src = 'odnoklassniki.png';
	$link = '<a rel="nofollow" href="http://www.odnoklassniki.ru/dk?st.cmd=addShare&st._surl=' . $post_link . '&title=' . $post_title .  '">';
	$out .= $sep . $link . '<img border="0" title="Добавить в Одноклассники" alt="odnoklassniki.ru" src="' . $path . $img_src  . '"' . $width_height . '></a>';

	$img_src = 'mail-ru.png';
	$link = '<a rel="nofollow" href="http://connect.mail.ru/share?url=' . $post_link . '&title=' . $post_title .  '">';
	$out .= $sep . $link . '<img border="0" title="Поделиться в Моем Мире@Mail.Ru" alt="mail.ru" src="' . $path . $img_src  . '"' . $width_height . '></a>';

	$img_src = 'yaru.png';
	$link = '<a rel="nofollow" href="http://my.ya.ru/posts_add_link.xml?URL=' . $post_link . '&title=' . $post_title .  '">';
	$out .= $sep . $link . '<img border="0" title="Поделиться в Я.ру" alt="ya.ru" src="' . $path . $img_src  . '"' . $width_height . '></a>';

	$img_src = 'rutvit.png';
	$link = '<a rel="nofollow" href="http://rutvit.ru/tools/widgets/share/popup?url=' . $post_link . '&title=' . $post_title .  '">';
	$out .= $sep . $link . '<img border="0" title="Добавить в РуТвит" alt="rutvit.ru" src="' . $path . $img_src  . '"' . $width_height . '></a>';

	$img_src = 'myspace.png';
	$link = '<a rel="nofollow" href="http://www.myspace.com/Modules/PostTo/Pages/?u=' . $post_link . '&t=' . $post_title .  '">';
	$out .= $sep . $link . '<img border="0" title="Добавить в MySpace" alt="myspace.com" src="' . $path . $img_src  . '"' . $width_height . '></a>';
	
	$img_src = 'buzz.png';
	$link = '<a rel="nofollow" href="http://www.google.com/buzz/post?message=' . $post_link . '&url=' . $post_title . '&srcURL=' . getinfo('siteurl') . '">';
	$out .= $sep . $link . '<img border="0" title="Добавить в Google Buzz" alt="Google Buzz" src="' . $path . $img_src  . '"' . $width_height . '></a>';		

	$img_src = 'technorati.png';
	$link = '<a rel="nofollow" href="http://www.technorati.com/faves?add=' . $post_link . '">';
	$out .= $sep . $link . '<img border="0" title="Добавить в Technorati" alt="technorati.com" src="' . $path . $img_src  . '"' . $width_height . '></a>';

	$img_src = 'digg.png';
	$link = '<a rel="nofollow" href="http://digg.com/submit?url=' . $post_link .  '">';
	$out .= $sep . $link . '<img border="0" title="Добавить в Digg" alt="digg.com" src="' . $path . $img_src  . '"' . $width_height . '></a>';

	$img_src = 'friendfeed.png';
	$link = '<a rel="nofollow" href="http://www.friendfeed.com/share?title=' . $post_link .  '">';
	$out .= $sep . $link . '<img border="0" title="Добавить в FriendFeed" alt="friendfeed.com" src="' . $path . $img_src  . '"' . $width_height . '></a>';

	$img_src = 'pikabu.png';
	$link = '<a rel="nofollow" href="http://pikabu.ru/add_story.php?story_url=' . $post_link . '&title=' . $post_title .  '">';
	$out .= $sep . $link . '<img border="0" title="Добавить в Pikabu" alt="pikabu.ru" src="' . $path . $img_src  . '"' . $width_height . '></a>';

	$img_src = 'blogger.png';
	$link = '<a rel="nofollow" href="http://www.blogger.com/blog_this.pyra?t&u=' . $post_link . '&n=' . $post_title .  '">';
	$out .= $sep . $link . '<img border="0" title="Опубликовать в Blogger.com" alt="blogger.com" src="' . $path . $img_src  . '"' . $width_height . '></a>';

	$img_src = 'liveinternet.png';
	$link = '<a rel="nofollow" href="http://www.liveinternet.ru/journal_post.php?action=n_add&cnurl=' . $post_link . '&cntitle=' . $post_title .  '">';
	$out .= $sep . $link . '<img border="0" title="Опубликовать в LiveInternet" alt="liveinternet.ru" src="' . $path . $img_src  . '"' . $width_height . '></a>';

	$img_src = 'livejournal.png';
	$link = '<a rel="nofollow" href="http://www.livejournal.com/update.bml?event=' . $post_link . '&subject=' . $post_title .  '">';
	$out .= $sep . $link . '<img border="0" title="Опубликовать в LiveJournal" alt="livejournal.ru" src="' . $path . $img_src  . '"' . $width_height . '></a>';

	$img_src = 'memori.png';
	$link = '<a rel="nofollow" href="http://memori.ru/link/?sm=1&amp;u_data[url]=' . $post_link . '&amp;u_data[name]=' . $post_title .  '">';
	$out .= $sep . $link . '<img border="0" title="Сохранить закладку в Memori.ru" alt="memori.ru" src="' . $path . $img_src  . '"' . $width_height . '></a>';

	$img_src = 'google-bookmarks.png';
	$link = '<a rel="nofollow" href="http://www.google.com/bookmarks/mark?op=edit&amp;bkmk=' . $post_link . '&amp;title=' . $post_title .  '">';
	$out .= $sep . $link . '<img border="0" title="Сохранить закладку в Google" alt="google.com" src="' . $path . $img_src  . '"' . $width_height . '></a>';
	
	$img_src = 'bobrdobr.png';
	$link = '<a rel="nofollow" href="http://bobrdobr.ru/addext.html?url=' . $post_link . '&amp;title=' . $post_title .  '">';
	$out .= $sep . $link . '<img border="0" title="Забобрить" alt="bobrdobr.ru" src="' . $path . $img_src  . '"' . $width_height . '></a>';

	$img_src = 'mister-wong.png';
	$link = '<a rel="nofollow" href="http://www.mister-wong.ru/index.php?action=addurl&bm_url=' . $post_link . '&bm_description=' . $post_title .  '">';
	$out .= $sep . $link . '<img border="0" title="Сохранить закладку в Мистер Вонг" alt="mister-wong.ru" src="' . $path . $img_src  . '"' . $width_height . '></a>';

	$img_src = 'yahoo-bookmarks.png';
	$link = '<a rel="nofollow" href="http://bookmarks.yahoo.com/toolbar/savebm?u=' . $post_link . '&t=' . $post_title .  '">';
	$out .= $sep . $link . '<img border="0" title="Добавить в Yahoo! Закладки" alt="yahoo.com" src="' . $path . $img_src  . '"' . $width_height . '></a>';

	$img_src = 'yandex.png';
	$link = '<a rel="nofollow" href="http://zakladki.yandex.ru/newlink.xml?url=' . $post_link . '&name=' . $post_title .  '">';
	$out .= $sep . $link . '<img border="0" title="Добавить в Яндекс.Закладки" alt="yandex.ru" src="' . $path . $img_src  . '"' . $width_height . '></a>';

	$img_src = 'delicious.png';
	$link = '<a rel="nofollow" href="http://del.icio.us/post?url=' . $post_link . '&amp;title=' . $post_title .  '">';
	$out .= $sep . $link . '<img border="0" title="Сохранить закладку в Delicious" alt="del.icio.us" src="' . $path . $img_src  . '"' . $width_height . '></a>';

	/*$img_src = 'linkstore.gif';
	$link = '<a rel="nofollow" href="http://www.linkstore.ru/servlet/LinkStore?a=add&amp;url=' . $post_link . '&amp;title=' . $post_title .  '">';
	$out .= $sep . $link . '<img border="0" title="linkstore.ru" alt="linkstore.ru" src="' . $path . $img_src  . '"' . $width_height . '></a>';
	
	$img_src = 'news2-ru.gif';
	$link = '<a rel="nofollow" href="http://news2.ru/add_story.php?url=' . $post_link . '">';
	$out .= $sep . $link . '<img border="0" title="news2.ru" alt="news2.ru" src="' . $path . $img_src  . '"' . $width_height . '></a>';

	$img_src = 'rumark.gif';
	$link = '<a rel="nofollow" href="http://rumarkz.ru/bookmarks/?action=add&amp;popup=1&amp;address=' . $post_link . '&amp;title=' . $post_title .  '">';
	$out .= $sep . $link . '<img border="0" title="rumarkz.ru" alt="rumarkz.ru" src="' . $path . $img_src  . '"' . $width_height . '></a>';
	
	$img_src = 'moemesto.gif';
	$link = '<a rel="nofollow" href="http://moemesto.ru/post.php?url=' . $post_link . '&amp;title=' . $post_title .  '">';
	$out .= $sep . $link . '<img border="0" title="moemesto.ru" alt="moemesto.ru" src="' . $path . $img_src  . '"' . $width_height . '></a>';*/


	echo NR . '<div class="addzakl">' . $options['text-do'] . $out . $options['text-posle'] . '</div>' . NR;
	
	return $args;
}

# end file
