<?php if (!defined('BASEPATH')) exit('No direct script access allowed');?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html><head>
	<title><?= mso_head_meta('title') ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta name="generator" content="MaxSite CMS">
	<meta name="description" content="<?= mso_head_meta('description') ?>">
	<meta name="keywords" content="<?= mso_head_meta('keywords') ?>">
	<link rel="shortcut icon" href="<?= getinfo('stylesheet_url') ?>ico/favicon4.ico" type="image/x-icon">
	<link rel="stylesheet" href="<?= getinfo('stylesheet_url') ?>style.css" type="text/css" media="screen">
	<link rel="stylesheet" href="<?= getinfo('stylesheet_url') ?>print.css" type="text/css" media="print">
	<?php 
		if ( $menu = mso_get_option('menu_header', 'templates', 'custom_menu-034-1.css') ) 
		{
			echo '<link rel="stylesheet" href="'. getinfo('stylesheet_url') . 'menu/' . $menu
					.'" type="text/css" media="screen">';
		}
	?>

	<link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="<?= getinfo('rss_url') ?>">
	
	<?= mso_load_jquery() ?>
	<?= mso_load_jquery('ui/effects.core.packed.js') ?>
	<?= mso_load_jquery('ui/effects.highlight.packed.js') ?>
	<script type="text/javascript" src="<?= getinfo('stylesheet_url') ?>js/my_ef.js"></script>
	
<?php mso_hook('head') ?>

</head>
<body>
<!-- end header -->
<?php mso_hook('body_start') ?>
