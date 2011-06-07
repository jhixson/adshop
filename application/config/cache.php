<?php defined('SYSPATH') OR die('No direct access allowed.');
$config['default'] = array
(
	'driver'   => 'file',
	'params'   => APPPATH.'cache',
	'lifetime' => 3600,
	'requests' => 1000
);
?>