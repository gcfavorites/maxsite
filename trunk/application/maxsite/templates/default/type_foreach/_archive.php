<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

		extract($page);
		// pr($page);
		
		mso_page_title($page_slug, $page_title, '<h1>', '</h1>', true);

		echo '<div class="info">';
			mso_page_cat_link($page_categories, ' | ', '<span>' . t('Рубрика') . ':</span> ', '<br />');
			mso_page_tag_link($page_tags, ' | ', '<span>' . t('Метки') .':</span> ', '<br />');
			mso_page_date($page_date_publish, 'd/m/Y H:i:s', '<span>' . t('Дата') .':</span> ', '');
			mso_page_edit_link($page_id, 'Edit page', ' -', '-');
		echo '</div>';
		
		
		echo '<div class="page_content">';
			mso_page_content($page_content);
			mso_page_content_end();
			echo '<div class="break"></div>';
			mso_page_comments_link($page_comment_allow, $page_slug, t('Обсудить') . ' (' . $page_count_comments . ')', '<div class="comment">', '</div>');
			
		echo '</div>';

?>