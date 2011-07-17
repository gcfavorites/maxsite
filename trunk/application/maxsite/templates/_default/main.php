<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
	require(getinfo('template_dir') . 'header.php'); 
?>

<div class="all"><div class="all-wrap">
<div class="section header-main">

<div class="header"><div class="header-wrap">
	<?php if (file_exists(getinfo('template_dir') . 'components/header-start.php')) require(getinfo('template_dir') . 'components/header-start.php'); ?>

	<div class="header-component1"><?php if ($fn = get_component_fn('default_header_component1', 'logo-links.php')) require($fn); ?></div><!-- div class="header-component1" -->

	<div class="header-component2"><?php if ($fn = get_component_fn('default_header_component2', 'menu.php')) require($fn); ?></div><!-- div class="header-component2" -->

	<div class="header-component3"><?php if ($fn = get_component_fn('default_header_component3', 'image-slider.php')) require($fn); ?></div><!-- div class="header-component3" -->

	<div class="header-component4"><?php if ($fn = get_component_fn('default_header_component4')) require($fn); ?></div><!-- div class="header-component4" -->

	<div class="header-component5"><?php if ($fn = get_component_fn('default_header_component5')) require($fn); ?></div><!-- div class="header-component5" -->

	<?php if (file_exists(getinfo('template_dir') . 'components/header-end.php')) require(getinfo('template_dir') . 'components/header-end.php'); ?>
</div><!-- div class="header-wrap" -->
</div><!-- div class="header" -->


<div class="section article main"><div class="main-wrap">
	<?php if (file_exists(getinfo('template_dir') . 'components/content-start.php')) require(getinfo('template_dir') . 'components/content-start.php'); ?>

	<div class="content"><div class="content-wrap"><?php if (file_exists(getinfo('template_dir') . 'custom/main_out_start.php')) require(getinfo('template_dir') . 'custom/main_out_start.php'); global $MAIN_OUT; echo $MAIN_OUT; if (file_exists(getinfo('template_dir') . 'custom/main_out_end.php')) require(getinfo('template_dir') . 'custom/main_out_end.php'); ?></div><!-- div class="content-wrap" --></div><!-- div class="content" -->

	<div class="aside sidebar sidebar1"><div class="sidebar1-wrap"><?php mso_show_sidebar('1', NR . '<div class="widget widget_[NUMW] widget_[SB]_[NUMW] [FN] [FN]_[NUMF]"><div class="w0"><div class="w1">', '</div><div class="w2"></div></div></div>' . NR); ?></div><!-- div class="sidebar1-wrap" --></div><!-- div class="aside sidebar sidebar1" -->

	<div class="clearfix"></div><!-- div class="clearfix" -->
</div><!-- div class="main-wrap" -->
</div><!-- div class="section article main" -->
</div><!-- div class="section header-main" -->


<div class="footer-do-separation"></div><!-- div class="footer-do-separation" -->



<div class="footer"><div class="footer-wrap">
	<?php if (file_exists(getinfo('template_dir') . 'components/footer-start.php')) require(getinfo('template_dir') . 'components/footer-start.php'); ?>

	<div class="footer-component1"><?php if ($fn = get_component_fn('default_footer_component1', 'footer-copyright.php')) require($fn); ?></div><!-- div class="footer-component1" -->

	<div class="footer-component2"><?php if ($fn = get_component_fn('default_footer_component2', 'footer-statistic.php')) require($fn); ?></div><!-- div class="footer-component2" -->

	<div class="footer-component3"><?php if ($fn = get_component_fn('default_footer_component3')) require($fn); ?></div><!-- div class="footer-component3" -->

	<div class="footer-component4"><?php if ($fn = get_component_fn('default_footer_component4')) require($fn); ?></div><!-- div class="footer-component4" -->

	<div class="footer-component5"><?php if ($fn = get_component_fn('default_footer_component5')) require($fn); ?></div><!-- div class="footer-component5" -->

	<?php if (file_exists(getinfo('template_dir') . 'components/footer-end.php')) require(getinfo('template_dir') . 'components/footer-end.php'); ?>
</div><!-- div class="footer-wrap" -->
</div><!-- div class="footer" -->
</div><!-- div class="all-wrap" --></div><!-- div class="all" -->

<?php if (function_exists('ushka')) {echo ushka('google_analytics'); echo ushka('body_end');} mso_hook('body_end'); ?>
</body></html>