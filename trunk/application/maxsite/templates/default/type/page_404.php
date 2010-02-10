<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
	
	header('HTTP/1.0 404 Not Found');
	
	mso_cur_dir_lang('templates');
	
	require(getinfo('template_dir') . 'main-start.php');
?>

<h1><?=t('404 - несуществующая страница')?></h1>
<p><?=t('Извините по вашему запросу ничего не найдено!')?></p>

<?php echo mso_hook('page_404') ?>

<?php require(getinfo('template_dir') . 'main-end.php'); ?>