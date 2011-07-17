<?php if (!defined('BASEPATH')) exit('No direct script access allowed');?><!DOCTYPE HTML>
<html><head>
	<title><?= mso_head_meta('title') ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta name="generator" content="MaxSite CMS">
	<meta name="description" content="<?= mso_head_meta('description') ?>">
	<meta name="keywords" content="<?= mso_head_meta('keywords') ?>">
	<?= mso_rss() ?>
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
	
	<?= mso_load_jquery() ?>
	<?= mso_load_jquery('ui/effects.core.packed.js') ?>
	<?= mso_load_jquery('ui/effects.highlight.packed.js') ?>
	<script type="text/javascript" src="<?= getinfo('stylesheet_url') ?>js/my_ef.js"></script>
	
<?php mso_hook('head') ?>

<?php if ($my_style = mso_get_option('my_style', 'templates', '')) echo '<style type="text/css">' . NR . $my_style . NR . '</style>'; ?>

</head><?php if (!$_POST) flush(); ?>
<body>
<!-- end header -->
<?php mso_hook('body_start') ?>
