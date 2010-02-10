<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
	require(getinfo('template_dir') . 'main-start.php');
?>

<h1>404 - несуществующая страница</h1>
<p>Извините по вашему запросу ничего не найдено!</p>

<?php echo mso_hook('page_404') ?>

<?php require(getinfo('template_dir') . 'main-end.php'); ?>