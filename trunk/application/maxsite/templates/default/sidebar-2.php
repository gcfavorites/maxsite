<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

	$do = NR . '<div class="widget"><div class="w1">'; // оформление виджета - начало блока
	$posle = '</div><div class="w2"></div></div>' . NR; // оформление виджета - конец блока

	mso_show_sidebar('2', $do, $posle);
	
?>