<?php if (!defined('BASEPATH')) exit('No direct script access allowed');?><!DOCTYPE HTML>
<html><head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title><?= mso_head_meta('title') ?></title>
	<meta name="generator" content="MaxSite CMS">
	<meta name="description" content="<?= mso_head_meta('description') ?>">
	<meta name="keywords" content="<?= mso_head_meta('keywords') ?>">
	<link rel="shortcut icon" href="<?= getinfo('stylesheet_url') ?>favicon.ico" type="image/x-icon">
	<link rel="stylesheet" href="<?= getinfo('stylesheet_url') ?>struct.css" type="text/css" media="screen">
	<link rel="stylesheet" href="<?= getinfo('stylesheet_url') ?>style.css" type="text/css" media="screen">
	<?= mso_rss() ?>
	<?= mso_load_jquery() ?>
	
<?php mso_hook('head') ?>

<?php if ($my_style = mso_get_option('my_style', 'templates', '')) echo '<style type="text/css">' . NR . $my_style . NR . '</style>'; ?>

</head><?php if (!$_POST) flush(); ?>
<body>
<!-- end header -->
<?php mso_hook('body_start') ?>
