<?php if (!defined('BASEPATH')) exit('No direct script access allowed');?><!DOCTYPE HTML>
<html><head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<!--[if IE]><meta http-equiv="X-UA-Compatible" content="IE=8"><![endif]-->
	<title><?= mso_head_meta('title') ?></title>
	<meta name="generator" content="MaxSite CMS">
	<meta name="description" content="<?= mso_head_meta('description') ?>">
	<meta name="keywords" content="<?= mso_head_meta('keywords') ?>">
	<link rel="shortcut icon" href="<?= getinfo('template_url') ?>images/favicons/<?= mso_get_option('default_favicon', 'templates', 'favicon1.png') ?>" type="image/x-icon">
	<?php if (mso_get_option('default_canonical', 'templates', 0)) echo mso_link_rel('canonical'); ?>
	
	<!-- RSS -->
	<?= mso_rss() ?>

	<!-- css -->
	<link rel="stylesheet" href="<?= getinfo('template_url') ?>css/<?php 
		if (file_exists(getinfo('template_dir') . 'css/css.php')) echo 'css.php'; 
		else 
		{
			if (file_exists(getinfo('template_dir') . 'css/my_style.css')) echo 'my_style.css'; 
				else echo 'style-all-mini.css'; 
			
		}?>" type="text/css" media="screen">
		
	<link rel="stylesheet" href="<?= getinfo('template_url') ?>css/print.css" type="text/css" media="print">
	
	<?php out_component_css() ?>
		
	<!-- js -->
	<?= mso_load_jquery() ?>
	
	<?php 
	if (file_exists(getinfo('template_dir') . 'css/my.js')) 
		echo '	<script type="text/javascript" src="' . getinfo('template_url') . 'js/my.js"></script>';
	?>
	
	<!-- plugins -->
	<?php mso_hook('head') ?>
	<!-- /plugins -->
	
	<?php if (function_exists('default_out_profiles')) default_out_profiles(); ?>
	<?php if (file_exists(getinfo('template_dir') . 'css/add_style.css')) echo '<link rel="stylesheet" href="' . getinfo('template_url') .'css/add_style.css" type="text/css" media="screen">'; ?>
	<?php if (function_exists('ushka')) echo ushka('head'); ?>
	
	<?php if ($my_style = mso_get_option('my_style', 'templates', '')) echo NR . '<!-- custom css-my_style -->' . NR . '	<style type="text/css">' . NR . $my_style . '	</style>' . NR; ?>

</head><?php if (!$_POST) flush(); ?>
<body>
<!-- end header -->
<?php mso_hook('body_start') ?>
<?php if (function_exists('ushka')) echo ushka('body_start'); ?>
