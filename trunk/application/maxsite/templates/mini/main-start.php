<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

	require_once(getinfo('template_dir') . 'header.php'); 
	
?>

<div id="container">
	<div id="headers">
		<div id="headers-wrap">
			<div id="header1">
				<h1><a href="<?= getinfo('siteurl') ?>" style="<?= mso_get_option('h1_header', 'templates', '') ?>"><?= getinfo('name_site') ?></a></h1>
				<h2><span style="<?= mso_get_option('h2_header', 'templates', '') ?>"><?= getinfo('description_site') ?></span></h2>
				<div class="r1"></div>
				<div class="r2"></div>
			</div><!-- div id="header1" -->
			
			<div id="header2">
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
				<div class="r1"></div>
				<div class="r2"></div>
			</div><!-- div id="header2" -->

			<div id="header3">
				<div class="r1"></div>
				<div class="r2"></div>
			</div><!-- div id="header3" -->

		</div><!-- div id="headers-wrap" -->
	</div><!-- div id="headers" -->

	<div id="sub-container">
		<div id="wrapper">
			<div id="content">
				<div class="content-top"></div>
				<div class="content-all">
				