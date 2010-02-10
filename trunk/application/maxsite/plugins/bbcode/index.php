<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://maxsite.org/
 */


# функция автоподключения плагина
function bbcode_autoload($args = array())
{
	mso_hook_add( 'content', 'bbcode_custom'); # хук на вывод контента
}

# функции плагина
function bbcode_custom($markup = '')
{

    $preg = array(    
		// Text arrtibutes
		'~\[s\](.*?)\[\/s\]~si'        => '<del>$1</del>',
		'~\[b\](.*?)\[\/b\]~si'                 => '<strong>$1</strong>',
		'~\[i\](.*?)\[\/i\]~si'                 => '<em>$1</em>',
		'~\[u\](.*?)\[\/u\]~si'                 => '<u>$1</u>',
		'~\[color=(.*?)\](.*?)\[\/color\]~si'   => '<span style="color:$1">$2</span>',
		
		'~\[size=(.*?)\](.*?)\[\/size\]~si'   => '<span style="font-size:$1">$2</span>',

		// стиль для блока
		'~\[div=(.*?)\](.*?)\[\/div\]~si' => '<div style="$1">$2</div>',
		'~\[p=(.*?)\](.*?)\[\/p\]~si'   => '<p style="$1">$2</p>',
		'~\[span=(.*?)\](.*?)\[\/span\]~si'   => '<span style="$1">$2</span>',
		
		'~\[left (.*?)\](.*?)\[\/left\]~si'       => '<div style="text-align: left; $1">$2</div>',
		'~\[left\](.*?)\[\/left\]~si'       => '<div style="text-align: left;">$1</div>',
		
		'~\[right (.*?)\](.*?)\[\/right\]~si'     => '<div style="text-align: right; $1">$2</div>',
		'~\[right\](.*?)\[\/right\]~si'     => '<div style="text-align: right;">$1</div>',
		
		'~\[center (.*?)\](.*?)\[\/center\]~si'   => '<div style="text-align: center; $1">$2</div>',
		'~\[center\](.*?)\[\/center\]~si'   => '<div style="text-align: center;">$1</div>',
		
		'~\[pleft\](.*?)\[\/pleft\]~si'       => '<p style="text-align: left;">$1</p>',
		'~\[pright\](.*?)\[\/pright\]~si'     => '<p style="text-align: right;">$1</p>',
		'~\[pcenter\](.*?)\[\/pcenter\]~si'   => '<p style="text-align: center;">$1</p>',		
		
		'~\[br\]~si'   => '<br clear="all" />',
		'~\[hr\]~si'   => '<hr />',
		'~\[line\]~si'   => '<hr />',
		
		'~\[\*\](.*?)\[\/\*\]~si'   => '<li>$1</li>',
		'~\[\*\]~si'   => '<li>',
		'~\[ul\](.*?)\[\/ul\]~si'   => "<ul>$1</li></ul>",
		'~\[list\](.*?)\[\/list\]~si'   => "<ul>$1</li></ul>",
		'~\[ol\](.*?)\[\/ol\]~si'   => '<ol>$1</li></ol>',

		
		//headers
		'~\[h1\](.*?)\[\/h1\]~si'           => '<h1>$1</h1>',
		'~\[h2\](.*?)\[\/h2\]~si'           => '<h2>$1</h2>',
		'~\[h3\](.*?)\[\/h3\]~si'           => '<h3>$1</h3>',
		'~\[h4\](.*?)\[\/h4\]~si'           => '<h4>$1</h4>',
		'~\[h5\](.*?)\[\/h5\]~si'           => '<h5>$1</h5>',
		'~\[h6\](.*?)\[\/h6\]~si'           => '<h6>$1</h6>',

		// [code=language][/code]
		'~\[code\](.*?)\[\/code\]~si'       => '<code>$1</code>',
		'~\[pre\](.*?)\[\/pre\]~si'         => '<pre>$1</pre>',
		// '~\[code=(.*?)\](.*?)\[\/code\]~si'     => '<pre><code class="$1">$2</code></pre>',               

		// email with indexing prevention & @ replacement
		// '~\[email\](.*?)\[\/email\]~sei'         => "'<a rel=\"noindex\" href=\"mailto:'.str_replace('@', '.at.','$1').'\">'.str_replace('@', '.at.','$1').'</a>'",
		//'~\[email=(.*?)\](.*?)\[\/email\]~sei'   => "'<a rel=\"noindex\" href=\"mailto:'.str_replace('@', '.at.','$1').'\">$2</a>'",

		// links
		//'~\[url\]www\.(.*?)\[\/url\]~si'        => '<a href="http://www.$1">$1</a>',
		'~\[url\](.*?)\[\/url\]~si'             => '<a href="$1">$1</a>',
		'~\[url=(.*?)?\](.*?)\[\/url\]~si'      => '<a href="$1">$2</a>',


		// images

		'~\[imgleft\](.*?)\[\/imgleft\]~si'      => '<img src="$1" style="float: left; margin: 0 10px 0 0;" />',
		'~\[imgleft (.*?)\](.*?)\[\/imgleft\]~si'      => '<img src="$2" title="$1" alt="$1" style="float: left; margin: 0 10px 0 0;" />',
		
		'~\[imgright\](.*?)\[\/imgright\]~si'    => '<img src="$1" style="float: right; margin: 0 0 0 10px;" />',
		'~\[imgright (.*?)\](.*?)\[\/imgright\]~si'    => '<img src="$2" title="$1" alt="$1" style="float: right; margin: 0 0 0 10px;" />',
		
		'~\[imgcenter\](.*?)\[\/imgcenter\]~si'  => '<div style="text-align: center"><img src="$1" /></div>',
		'~\[imgcenter (.*?)\](.*?)\[\/imgcenter\]~si'  => '<div style="text-align: center"><img src="$2" title="$1" alt="$1" /></div>',
		
		// [imgmini=http://site/uploads/sborka-mini.jpg]http://site/uploads/sborka.jpg[/imgmini]
		'~\[imgmini=_(.*?)\](.*?)\[\/imgmini\]~si' => '<a href="$2" target="_blank" class="lightbox"><img src="$1" /></a>',
		'~\[imgmini=(.*?)\](.*?)\[\/imgmini\]~si'  => '<a href="$2"><img src="$1" class="lightbox" /></a>',
		
		'~\[img=(.*?)x(.*?)\](.*?)\[\/img\]~si'  => '<img src="$3" style="width: $1px; height: $2px" />',
		
		'~\[img (.*?)\](.*?)\[\/img\]~si'              => '<img src="$2" title="$1" alt="$1" />',
		'~\[img\](.*?)\[\/img\]~si'              => '<img src="$1" title="" alt="" />',
		
		// quoting
		'~\[quote\](.*?)\[\/quote\]~si'         => '<blockquote>$1</blockquote>',
		'~\[quote=(?:&quot;|"|\')?(.*?)["\']?(?:&quot;|"|\')?\](.*?)\[\/quote\]~si'   => '<blockquote><strong class="src">$1:</strong>$2</blockquote>',

  );
  
  return preg_replace(array_keys($preg), array_values($preg), $markup);

}

?>