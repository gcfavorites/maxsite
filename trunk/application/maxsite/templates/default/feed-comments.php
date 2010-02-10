<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

$cache_key = mso_md5('feed_' . mso_current_url());
$k = mso_get_cache($cache_key);
if ($k) return print($k); // да есть в кэше
ob_start();


require_once( getinfo('common_dir') . 'comments.php' ); // функции комментариев

$this->load->helper('xml');

$encoding = 'utf-8';
$time_zone = '+0300';


$feed_name = mso_head_meta('title') . ' (Последние комментарии)';
$description = mso_head_meta('description');
$feed_url = getinfo('siteurl');
$language = 'en-ru';
$generator = 'MaxSite CMS (http://maxsite.org/)';

$comments = mso_get_comments(false, array('limit'=>'20', 'order'=>'desc'));

if ($comments) 
{
	$pubdate = date('D, d M Y H:i:s '. $time_zone, strtotime($comments[0]['comments_date']));
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
		
		<?php foreach ($comments as $comment) : extract($comment); ?>

		<item>
			<title><?= xml_convert(strip_tags($users_nik . $comments_author_name . $comusers_nik)) ?> к "<?= xml_convert(strip_tags($page_title)) ?>"</title>
			<link><?= getinfo('siteurl') . 'page/' . mso_slug($page_slug) ?>#comment-<?= $comments_id ?></link>
			<guid><?= getinfo('siteurl') . 'page/' . mso_slug($page_slug) ?>#comment-<?= $comments_id ?></guid>
			<pubdate><?= date('D, d M Y H:i:s '. $time_zone, strtotime($comments_date)) ?></pubdate>
			<author><?= xml_convert(strip_tags($users_nik . $comments_author_name . $comusers_nik)) ?></author>
			<creator><?= xml_convert(strip_tags($users_nik . $comments_author_name . $comusers_nik)) ?></creator>
			<description><![CDATA[<?= $comments_content  ?>]]></description>
		</item>
		
		<?php endforeach; ?>
		
	</channel>
</rss>
<?php 

} // if ($pages) 

mso_add_cache($cache_key, ob_get_flush(), 3600); // сразу и в кэш добавим - время 10 минут 60 сек * 10 минут *
?>