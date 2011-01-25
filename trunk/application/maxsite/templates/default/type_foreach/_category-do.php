<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

		# выводим только если есть найденные страницы
		if ($pages) 
		{
			echo '<h1 class="category">' . $title_page . '</h1>';
			
			// выведем описание рубрики
			if (isset($pages[0]['page_categories'][0]) 
				and isset($pages[0]['page_categories_detail'][$pages[0]['page_categories'][0]]['category_desc'])
				and $pages[0]['page_categories_detail'][$pages[0]['page_categories'][0]]['category_desc'])
			{
				echo '<div class="category_desc">' 
					. $pages[0]['page_categories_detail'][$pages[0]['page_categories'][0]]['category_desc']
					. '</div>';
			}
		}