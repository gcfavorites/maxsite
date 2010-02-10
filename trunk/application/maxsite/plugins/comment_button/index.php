<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://maxsite.org/
 */

# функция автоподключения плагина
function comment_button_autoload($args = array())
{
	mso_hook_add( 'head', 'comment_button_head'); # хук на head шаблона - для JS
	mso_hook_add( 'comments_content_start', 'comment_button_custom'); # хук на форму
}

# подключаем JS в head
function comment_button_head($arg = array())
{
	echo '	<script type="text/javascript" src="'. getinfo('plugins_url') . 'comment_button/comment_button.js"></script>' . NR;
}


# функции плагина
function comment_button_custom($arg = array())
{
	echo '<p class="comment_button">
	<input type="button" value="B" title="Полужирный" onClick="addText(\'<b>\', \'</b>\') " />
	<input type="button" value="I" title="Курсив" onClick="addText(\'<i>\', \'</i>\') "/>
	<input type="button" value="U" title="Подчеркнутый" onClick="addText(\'<u>\', \'</u>\') "/>
	<input type="button" value="S" title="Зачеркнутый" onClick="addText(\'<s>\', \'</s>\') "/>
	<input type="button" value="CITE" title="Цитата" onClick="addText(\'<blockquote>\', \'</blockquote>\') "/>
	<input type="button" value="PRE" title="Код или преформатированный текст" onClick="addText(\'<pre>\', \'</pre>\') "/>
	</p>';
}

?>