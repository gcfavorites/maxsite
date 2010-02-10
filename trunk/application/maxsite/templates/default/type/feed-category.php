<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

$cache_key = mso_md5('feed_' . mso_current_url());
$k = mso_get_cache($cache_key);
if ($k) return print($k); // да есть в кэше
ob_start();


require_once( getinfo('common_dir') . 'page.php' ); // основные функции страниц 
require_once( getinfo('common_dir') . 'category.php' ); 		// функции рубрик

$this->load->helper('xml');

$encoding = 'utf-8';
$time_zone = str_replace('.', '', getinfo('time_zone'));

$limit = mso_get_option('limit_post_rss', 'templates', 7); 
$cut = mso_get_option('full_rss', 'templates', 0) ? false : 'Читать полностью »'; 

$feed_name = mso_head_meta('title');
$description = mso_head_meta('description');
$feed_url = getinfo('siteurl');
$language = 'en-ru';
$generator = 'MaxSite CMS (http://maxsite.org/)';

$par = array( 'limit'=>$limit, 'cut'=>$cut, 'type'=>false, 'pagination'=>false, 'only_feed'=>true ); 

$pages = mso_get_pages($par, $pagination); 


if (!$pages) 
{	
	$pages = array();
	$pubdate = date('D, d M Y H:i:s ' . $time_zone);
}
else $pubdate = date('D, d M Y H:i:s ' . $time_zone, strtotime($pages[0]['page_date_publish']));

header("Content-Type: application/rss+xml");
echo '<' . '?xml version="1.0" encoding="utf-8"?' . '>';
?>

<rss version="2.0">
	<channel>
		<title><?= $feed_name ?></title>
		<link><?= $feed_url ?></link>
		<description><?= $description ?></description>
		<pubDate><?= $pubdate ?></pubDate>
		<language><?= $language ?></language>
		<generator><?= $generator ?></generator>
		<rights>Copyright <?= gmdate("Y", time()) ?></rights>
		
		<?php foreach($pages as $page) : extract($page); ?>

		<item>
			<title><?= xml_convert(strip_tags($page_title)) ?></title>
			<link><?= getinfo('siteurl') . 'page/' . mso_slug($page_slug) ?></link>
			<guid><?= getinfo('siteurl') . 'page/' . mso_slug($page_slug) ?></guid>
			<pubdate><?= date('D, d M Y H:i:s '. $time_zone, strtotime($page_date_publish)) ?></pubdate>
			<?= mso_page_cat_link($page_categories, "\n", '<category><![CDATA[', ']]></category>' . "\n", false) ?>
			<author><?= $users_nik ?></author>
			<creator><?= $users_nik ?></creator>
			<description><![CDATA[<?= mso_page_content($page_content) . mso_page_comments_link($page_comment_allow, $page_slug, ' Обсудить', '', '', false) ?>]]></description>
		</item>
		
		<?php endforeach; ?>
		
	</channel>
</rss>
<?php 

mso_add_cache($cache_key, ob_get_flush(), 3600); // сразу и в кэш добавим - время 10 минут 60 сек * 10 минут *
?>