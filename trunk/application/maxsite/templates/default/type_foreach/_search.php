<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

		extract($page);
		
		mso_page_title($page_slug, $page_title, '<li>', '', true);
		mso_page_date($page_date_publish, 'd/m/Y', ' - ', '');
		
		// разобъем тексты так, чтобы в середине оказались поисковые слова
		$page_content = mb_strtolower(strip_tags($page_content), 'UTF8' );
		$page_content = str_replace($searh_to_text, '_mso_split_' . $searh_to_text, $page_content);
		
		$arr = explode('_mso_split_', $page_content);
		
		$flag = true;
		foreach($arr as $key=>$val)
		{
			if ( strpos( $val, $searh_to_text ) ) // есть сеарх
			{
				if ($flag) // текст перед сеарх
				{
					$arr[$key] = ' &lt;...&gt; ' . mb_substr($val, -100, 100, 'UTF8') . ' ';
					$flag = false;
				}
				else
				{
					$arr[$key] = ' ' . mb_substr($val, 0, $max_char_count, 'UTF8') . ' &lt;...&gt; <br /> ';
					$flag = true;
				}
			}
			else 
			{
				if (!$flag) // текст перед сеарх
				{
					$arr[$key] = ' ' . mb_substr($val, -$max_char_count, $max_char_count, 'UTF8') . ' ';
					$flag = false;
				}
				else
				{
					$arr[$key] = ' ' . mb_substr($val, 0, $max_char_count, 'UTF8') . ' &lt;...&gt; ';
					$flag = true;
				}
			}
			// echo $arr[$key] . '<hr>';
		}
		
		$page_content = implode(' ', $arr); 
		
		// подсветим найденные
		$page_content = str_replace($searh_to_text, '<span style="color: red; background: yellow;">' . $searh_to_text . '</span>', $page_content);
		
		// кол-во совпадений
		$cou = substr_count($page_content, $searh_to_text) + substr_count(mb_strtolower($page_title, 'UTF8'), $searh_to_text);
		
		echo ' - '. t('Совпадений'). ': ' . $cou;
		echo '<p>' . $page_content . '</p>';

		echo '</li>';
	
	
?>