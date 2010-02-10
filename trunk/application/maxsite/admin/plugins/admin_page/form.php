<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (с) http://maxsite.org/
 */

# Форма - работает совместно с edit и new

	# до 
	$do = <<<EOF
	<table style="border-col1lapse: collapse; width: 99%; border: none; line-height: 1.4em;">
	<tr>
		<td style="vertical-align: top; padding: 0 10px 0 0;">
		<input type="text" value="{$f_header}" name="f_header" {$input_style} />
		{$fses}
EOF;
	
	# после
	$posle = <<<EOF

			<div style="margin: 10px 0;">
				<input type="submit" name="{$name_submit}" value="           Готово           " class="wymupdate" />
			</div>
			
			<div style="margin: 20px 0;">
				<div class="block_page page_meta">
					<h3>Дополнительные поля meta</h3>
					{$all_meta}
				</div>
			</div>
		</td>
		
		<td style="vertical-align: top; width: 250px;">
			
			<div class="block_page">
				<h3>Рубрика</h3>
				<div class="cat_page">{$all_cat}</div>
			</div>
			
			<div class="block_page">
				<h3>Метки (через запятую)</h3>
				<p><input type="text" value="{$f_tags}" name="f_tags" style="width: 99%;" /></p>
			</div>
			
			<div class="block_page">
				<h3>Короткая ссылка</h3>
				<p><input type="text" value="{$f_slug}" name="f_slug" style="width: 99%;" /></p>
			</div>

			<div class="block_page">
				<h3>Обсуждение</h3>
				<p><input name="f_comment_allow" type="checkbox" {$f_comment_allow} /> Разрешить комментирование</p>
				<p><input name="f_ping_allow" type="checkbox" {$f_ping_allow} /> Разрешить пинг</p>
				<p><input name="f_feed_allow" type="checkbox" {$f_feed_allow} /> Публикация в RSS</p>
			</div>
			
			<div class="block_page">
				<h3>Тип страницы</h3>
				{$all_post_types}
			</div>			
			
			<div class="block_page">
				<h3>Статус записи</h3> 
				<p><input name="f_status[]" type="radio" {$f_status_publish} value="publish"> Опубликовать</p>
				<p><input name="f_status[]" type="radio" {$f_status_draft} value="draft"> Черновик</p>
				<p><input name="f_status[]" type="radio" {$f_status_private} value="private"> Личное (только для себя)</p>
			</div>
			
			<div class="block_page">
				<h3>Пароль для чтения</h3>
				<p><input type="text" value="{$f_password}" name="f_password" style="width: 99%;" /></p>
			</div>
			
			<div class="block_page">
				<h3>Родительская страница (id)</h3>
				<p><input type="text" value="{$f_page_parent}" name="f_page_parent" style="width: 99%;" /></p>
			</div>
			
			<div class="block_page">
				<h3>Автор</h3>
				<p>{$all_users}</p>
			</div>
		</td>
	</tr>
	</table>
EOF;

?>