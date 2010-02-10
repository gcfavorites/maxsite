<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

	require_once('header.php'); 

?>
<div id="container">
	<div id="header1">
		
		<div class="header-searh">
			<form name="f_search" action="" method="get" onsubmit="location.href='<?= getinfo('siteurl') ?>search/' + encodeURIComponent(this.s.value).replace(/%20/g, '+'); return false;">	<input type="text" name="s" id="s" size="20" onfocus="if (this.value == '<?= t('что искать?', 'templates') ?>') {this.value = '';}" onblur="if (this.value == '') {this.value = '<?= t('что искать?', 'templates') ?>';}" value="<?= t('что искать?', 'templates') ?>">&nbsp;<input type="submit" id="searchsubmit" name="Submit" value="  <?= t('Поиск', 'templates') ?>  "></form>
		</div><!-- div class= -->
		
		<?php if (!mso_hook_present('main_menu')) { ?>
		<div id="MainMenu">
			<div id="tab">
				<ul>
					<?php
						$def_menu = t('/ | Главная_NR_about | О сайте_NR_comments | Комментарии_NR_contact | Контакты_NR_sitemap | Архив_NR_feed | RSS', 'templates');
						if ( $menu = mso_get_option('top_menu', 'templates', $def_menu) ) 
							echo mso_menu_build($menu, 'selected', true);
					?>
				</ul>
			</div><!-- div id="tab" -->
		</div><!-- div id="MainMenu" -->
		<?php } else mso_hook('main_menu'); ?>
	</div><!-- div id="header1" -->
	
	<?php 
		if ( $h2 = mso_get_option('image_header', 'templates', '') )
				$h2 = 'style="background: url(' . getinfo('stylesheet_url') . 'images/' . $h2 . ') no-repeat;"';
	?>
	<div id="header2" <?= $h2 ?> >
		<div>
			<h1><a href="<?= getinfo('siteurl') ?>" style="<?= mso_get_option('h1_header', 'templates', '') ?>"><?= getinfo('name_site') ?></a></h1>
			<h2><span style="<?= mso_get_option('h2_header', 'templates', '') ?>"><?= getinfo('description_site') ?></span></h2>
		</div>
	</div><!-- div id="header2" -->
	
	
	<div id="sub-container">
		<div id="wrapper">
			<div id="content">
				<div class="content-top"></div>
				<div class="content-all">