<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://maxsite.org/
 */
	
	mso_cur_dir_lang('admin');
	
	# Форма - работает совместно с edit и new
	
	
	# загрузки
	ob_start();	
	require($MSO->config['admin_plugins_dir'] . 'admin_page/files.php');
	$page_files = ob_get_contents();
	ob_end_clean();
	
	$page_admin_files = '<p>Скопируйте код в редактор. (<a href="'. $MSO->config['site_admin_url'] . 'files" target="_blank">Страница «Загрузки»</a>)</p>';
	
	# до 
	$do = <<<EOF
	<table style="width: 99%; border: none; line-height: 1.4em;">
	<tr>
		<td style="vertical-align: top; padding: 0 10px 0 0;">
		<input type="text" value="{$f_header}" name="f_header" class="f_header" />
		{$fses}
EOF;
	
	# после
	$posle = <<<EOF

			<div style="margin: 10px 0;">
				<p><input name="f_status[]" type="radio" {$f_status_publish} value="publish" id="f_status_publish">
						<label for="f_status_publish">Опубликовать</label> 
					<input name="f_status[]" type="radio" {$f_status_draft} value="draft" id="f_status_draft"> 
						<label for="f_status_draft">Черновик</label> 
					<input name="f_status[]" type="radio" {$f_status_private} value="private" id="f_status_private"> 
						<label for="f_status_private">Личное</label>
						
				</p>
									
				{$f_return}
				<input type="submit" name="{$name_submit}" value="Готово" class="wymupdate" /> <span class="autosave-editor"></span>
			</div>
			
			<div style="margin: 20px 0;">
				<div class="block_page page_meta">
					<h3>Дополнительные поля meta</h3>
					{$all_meta}
				</div>
				
				<div class="block_page page_files">
					<h3>Файлы</h3>
					{$page_admin_files}
					<div class="frame">
					{$page_files}
					</div>
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
				<p><input type="text" value="{$f_tags}" name="f_tags" id="f_tags" style="width: 99%;" /></p>
				{$f_all_tags}
			</div>
			
			<div class="block_page">
				<h3>Короткая ссылка</h3>
				<p><input type="text" value="{$f_slug}" name="f_slug" style="width: 99%;" /></p>
			</div>

			<div class="block_page">
				<h3>Обсуждение</h3>
				<p><input name="f_comment_allow" type="checkbox" {$f_comment_allow} /> Разрешить комментирование</p>
				<p><input name="f_feed_allow" type="checkbox" {$f_feed_allow} /> Публикация в RSS</p>
				<!--p><input name="f_ping_allow" type="checkbox" {$f_ping_allow} /> Разрешить пинг</p-->
			</div>

			<div class="block_page">
				<h3>Дата публикации</h3>
				<p><input name="f_date_change" type="checkbox" {$f_date_change} /> Изменить дату публикации</p>
				<p>{$date_y} - {$date_m} - {$date_d}</p>
				<p>{$time_h} : {$time_m} : {$time_s}</p>
				<p><em>{$date_time}</em></p>
			</div>
			
			<div class="block_page">
				<h3>Тип страницы</h3>
				{$all_post_types}
			</div>			
			
			<div class="block_page">
				<h3>Пароль для чтения</h3>
				<p><input type="text" value="{$f_password}" name="f_password" style="width: 99%;" /></p>
			</div>
			
			<div class="block_page">
				<h3>Порядок</h3>
				<p><input type="text" value="{$page_menu_order}" name="f_menu_order" style="width: 99%;" /></p>
			</div>
				
			<div class="block_page">
				<h3>Родительская страница</h3>
				<p>{$all_pages}</p>
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