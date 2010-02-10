<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');?>

	<script type="text/javascript" src="<?= $editor_config['url'] ?>jw/jquery.wysiwyg.js"></script>
	<script type="text/javascript" src="<?= $editor_config['url'] ?>jw/jquery.timers.js"></script>
	<script type="text/javascript">
		$(function()
		{
		  autosavetime = 60000; // 60 sec
		  autosaveurl = '<?= getinfo('ajax') . base64_encode('admin/plugins/editor_jw/autosave-post.php') ?>';
		  autosaveold = '<?= getinfo('siteurl') . 'uploads/_mso_float/autosave.txt' ?>';
		  
		  $('#wysiwyg').wysiwyg({
				css: '<?= $editor_config['url'] ?>jw/styles.css',
				controls : {}
			});
		});
	</script>

<form method="post" <?= $editor_config['action'] ?> >
<?= $editor_config['do'] ?>
<textarea id="wysiwyg" name="f_content" style="height: 400px; width: 100%;" ><?= $editor_config['content'] ?></textarea>
<?= $editor_config['posle'] ?>
</form>

