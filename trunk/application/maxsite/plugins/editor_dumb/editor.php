<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');?>

<?php echo '<script type="text/javascript" src="'. getinfo('plugins_url') . 'editor_dumb/editor_zero.js"></script>'; ?>

<form method="post" <?= $editor_config['action'] ?> >
<?= $editor_config['do'] ?>
<p class="editor_button">
	<input type="button" value="B" title="Полужирный" onClick="addText('<b>', '</b>') " />
	<input type="button" value="I" title="Курсив" onClick="addText('<i>', '</i>') "/>
	<input type="button" value="U" title="Подчеркнутый" onClick="addText('<u>', '</u>') "/>
	<input type="button" value="S" title="Зачеркнутый" onClick="addText('<s>', '</s>') "/> &nbsp;
	<input type="button" value="A" title="Ссылка" onClick="addText('<a href=&quot;&quot;>', '</a>') "/>
	<input type="button" value="IMG" title="Картинка" onClick="addText('<img src=&quot;&quot; alt=&quot;&quot; />', '') "/>
	<input type="button" value="Цитата" title="Цитата" onClick="addText('<blockquote>', '</blockquote>') "/>
	<input type="button" value="Код" title="Код или преформатированный текст" onClick="addText('<code>', '</code>') "/>
	<input type="button" value="cut" title="Отрезать текст" onClick="addText('[cut]\n', '') "/>
</p>
<textarea id="f_content" name="f_content" rows="25" cols="80" style="height: <?= $editor_config['height'] ?>px; width: 100%;" ><?= $editor_config['content'] ?></textarea>
<?= $editor_config['posle'] ?>
</form>

