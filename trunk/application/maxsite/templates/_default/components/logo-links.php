<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

	
echo '<div class="logo-links">';

	echo '<div class="left w75">';
	
		$logo = getinfo('stylesheet_url') . 'images/logos/' . mso_get_option('default_header_logo', 'templates', 'logo01.png');
		
		if (!is_type('home')) echo '<a href="' . getinfo('siteurl') . '">';
			
		echo '<img class="left" src="' . $logo . '" alt="' . getinfo('name_site') . '" title="' . getinfo('name_site') . '" height="64" width="64">';
		
		if (!is_type('home')) echo '</a>';


		echo '
			<div class="name_site">' . getinfo('name_site') . '</div>
			<div class="description_site">' . getinfo('description_site') . '</div>';

	echo '</div>';

	echo '<div class="right text-right w25 social">';
	
		echo '<a class="header-social rss" href="' . getinfo('rss_url') . '"><img src="' . getinfo('stylesheet_url') . 'images/social/rss.png" width="16" height="16" alt="RSS" title="RSS"></a>';
		
		if ($u = mso_get_option('default_twitter_url', 'templates', ''))
			echo '<a class="header-social twitter" href="' . $u .'"><img src="' . getinfo('stylesheet_url') . 'images/social/twitter.png" width="16" height="16" alt="Twitter" title="Twitter"></a>';
		
		if ($u = mso_get_option('default_facebook_url', 'templates', ''))
			echo '<a class="header-social facebook" href="' . $u .'"><img src="' . getinfo('stylesheet_url') . 'images/social/facebook.png" width="16" height="16" alt="Facebook" title="Facebook"></a>';
			
		if ($u = mso_get_option('default_skype_url', 'templates', ''))
			echo '<a class="header-social skype" href="' . $u .'"><img src="' . getinfo('stylesheet_url') . 'images/social/skype.png" width="16" height="16" alt="Skype" title="Skype"></a>';	
	
	echo '</div>';
	
	echo '<div class="clearfix"></div>';
	
echo '</div><!-- class="logo-links" -->';