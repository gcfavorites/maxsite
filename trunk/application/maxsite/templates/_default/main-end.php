<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

global $MAIN_OUT;

$MAIN_OUT = ob_get_contents();
ob_end_clean();

require('main.php');