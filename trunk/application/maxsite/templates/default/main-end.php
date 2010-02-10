<?php if (!defined('BASEPATH')) exit('No direct script access allowed');?>
				
			</div><!-- div id="content" -->
		</div><!-- div id="wrapper" -->
		
		<div id="sidebars">
			
			<div id="sidebar-top"><div class="wrap">
				<?php require('sidebar-top.php'); ?>
			</div><!-- div class=wrap --></div><!-- div id="sidebar-top" -->
			
			<div id="sidebar1"><div class="wrap">
				<?php require('sidebar-1.php'); ?>
			</div><!-- div class=wrap --></div><!-- div id="sidebar-1" -->
			
			<div id="sidebar2"><div class="wrap">
				<?php require('sidebar-2.php') ?>
			</div><!-- div class=wrap --></div><!-- div id="sidebar-2" -->
			
		</div><!-- div id="sidebars" -->

	</div><!-- div id="sub-container" -->
	
	<?php require('footer.php')	?>

</div><!-- div id="container" -->

<?php mso_hook('body_end') ?>

</body>
</html>