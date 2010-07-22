<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

		extract($page);
		
		// pr($page);
		
		echo NR . '<div class="page_only">' . NR;
	
		mso_page_title($page_slug, $page_title, '<h1>', '</h1>', true);

		echo '<div class="info">';
			mso_page_cat_link($page_categories, ' | ', '<span>'. t('Рубрики'). ':</span> ', '<br>');
			mso_page_tag_link($page_tags, ' | ', '<span>'. t('Метки'). ':</span> ', '<br>');
			mso_page_date($page_date_publish, 'd/m/Y H:i:s', '<span>'. t('Дата'). ':</span> ', '');
			mso_page_edit_link($page_id, 'Edit page', ' -', '-');
			mso_page_feed($page_slug, t('комментарии по RSS'), '<br><span>'. t('Подписаться на'). '</span> ', '', true);
		echo '</div>';
		
		
		echo '<div class="page_content">';
			mso_page_content($page_content);
			mso_page_content_end();
			mso_page_comments_link(array( 
					'page_comment_allow' => $page_comment_allow,
					'page_slug' => $page_slug,
					'title' => t('Обсудить'). ' (' . $page_count_comments . ')',
					'title_no_link' => t('Читать комментарии').' (' . $page_count_comments . ')',
					'do' => '<div class="comments-link"><span>',
					'posle' => '</span></div>',
					'page_count_comments' => $page_count_comments
				 ));
			
		echo '</div>';
		
		echo NR . '</div><!--div class="page_only"-->' . NR;
	
?>