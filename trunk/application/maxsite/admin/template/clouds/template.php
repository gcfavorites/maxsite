<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */


	# поскольку в хуках могут быть простой вывод данных через echo, следует 
	# включить буферизацию вывода на каждый хук
	
	// в теле контента можно определить хуки на остальные части 
	ob_start();
		$admin_content_hook = mso_hook('mso_admin_content', mso_admin_content());
		$admin_content = ob_get_contents() . $admin_content_hook;
	ob_end_clean();
	
	ob_start();
		$admin_header_hook = mso_hook('mso_admin_header', mso_admin_header());
		$admin_header = ob_get_contents() . $admin_header_hook;
	ob_end_clean();
	
	ob_start();
		$admin_menu_hook = mso_hook('mso_admin_menu', mso_admin_menu());
		$admin_menu = ob_get_contents() . $admin_menu_hook;
	ob_end_clean();
	
	ob_start();
		$admin_footer_hook = mso_hook('mso_admin_footer', mso_admin_footer());
		$admin_footer = ob_get_contents() . $admin_footer_hook;
	ob_end_clean();
	
	if (!$admin_header) $admin_header = t('Админ-панель', 'admin');
	
	$admin_css = getinfo('admin_url') . 'template/' . mso_get_option('admin_template', 'general', 'default') . '/style.css';
	$admin_css = mso_hook('admin_css', $admin_css);
	$admin_title = t('Админ-панель', 'admin') . ' - ' . mso_hook('admin_title', mso_head_meta('title'));
		
	
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html><head>
<title><?= $admin_title ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<link rel="shortcut icon" href="<?= getinfo('siteurl') ?>favicon.ico" type="image/x-icon">
	<link rel="stylesheet" href="<?= $admin_css ?>" type="text/css" media="screen">
	<?= mso_load_jquery() ?>
	<?php mso_hook('admin_head') ?>
</head>
<body>
<div id="container">
	<div id="headers">
		<div id="headers-wrap">
			<div id="header1">
				<h1><a href="<?= getinfo('siteurl') ?>"><?= mso_get_option('name_site', 'general') ?></a></h1>
				<h2><?= $admin_header ?></h2>
			</div><!-- div id="header1" -->
		</div><!-- div id="headers-wrap" -->
	</div><!-- div id="headers" -->

	<div id="sub-container">
		<div id="wrapper">
			<div id="content">
				<div class="content-top"></div>
				<div class="content-all">
				<?= $admin_content ?>
				</div><!--div class="content-all"-->
				<div class="content-bottom"></div>
			</div><!-- div id="content" -->
		</div> <!-- div id="wrapper" -->
		
		<div id="sidebars"><div class="r1"></div>
		
			<div id="sidebar1">
				<div class="wrap">
				<?= $admin_menu ?>
				</div><!-- div class=wrap -->
			</div><!-- div id="sidebar-1" -->

		<div class="r2"></div></div><!-- div id="sidebars" -->
		
	</div><!-- div id="sub-container" -->
	
	
	<div id="footer"><?= $admin_footer ?></div>
	
	

</div><!-- div id="container" -->
</body>
</html>