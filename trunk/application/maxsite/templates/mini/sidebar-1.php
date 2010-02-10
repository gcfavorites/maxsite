<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

	$do = NR . '<div class="widget widget_[SB]_[NUMW] [FN] [FN]_[NUMF]"><div class="w0"><div class="w1">'; // оформление виджета - начало блока
	$posle = '</div><div class="w2"></div></div></div>' . NR; // оформление виджета - конец блока
	
	# отображение сайдбара
	# сайдбар должен быть зарегистрирован в functions.php
	mso_show_sidebar('1', $do, $posle );
	
?>