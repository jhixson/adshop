<?php defined('SYSPATH') or die('No direct script access.');
 
class clean_obj_Core {
 
 	// given a database result object, clean up quotes and such on text values
	public static function clean(&$item, $key) {
		$item = htmlentities($item);
	}
}
 
?>