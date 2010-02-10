<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>				
				</div><!--div class="content-all"-->
				<div class="content-bottom"></div>
			</div><!-- div id="content" -->
		</div> <!-- div id="wrapper" -->
		
		<div id="sidebars"><div class="r1"></div>
		
			<div id="sidebar1">
				<div class="wrap">
				<?php require(getinfo('template_dir') . 'sidebar-1.php'); ?>
				</div><!-- div class=wrap -->
			</div><!-- div id="sidebar-1" -->

		<div class="r2"></div></div><!-- div id="sidebars" -->
		
	</div><!-- div id="sub-container" -->
	
	<?php require(getinfo('template_dir') . 'footer.php'); ?>

</div><!-- div id="container" -->
<?php mso_hook('body_end') ?>

<?php
	if (function_exists('ushka')) echo ushka('google_analytics');
?>
</body>
</html>