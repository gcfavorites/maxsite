<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');?>

	<script type="text/javascript" src="<?= $editor_config['url'] ?>jw/jquery-1.2.3.pack.js"></script>
	<script type="text/javascript" src="<?= $editor_config['url'] ?>jw/jquery.wysiwyg.js"></script>
	<script type="text/javascript">
		$(function()
		{
		  $('#wysiwyg').wysiwyg({
				css: '<?= $editor_config['url'] ?>jw/styles.css',
				controls : {
					//separator04 : { visible : true },
					//insertOrderedList : { visible : true },
					//insertUnorderedList : { visible : true },
					//html : { visible : true },
				}
				
			});
		});
	</script>

<form method="post" <?= $editor_config['action'] ?> >
<?= $editor_config['do'] ?>
<textarea id="wysiwyg" name="f_content" style="height: 400px; width: 99%;" ><?= $editor_config['content'] ?></textarea>
<?= $editor_config['posle'] ?>
</form>

