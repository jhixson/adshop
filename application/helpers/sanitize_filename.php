<?php defined('SYSPATH') or die('No direct script access.');
 
class sanitize_filename_Core {
 
	public static function clean($str) {
		$str = preg_replace('/\s/','_',$str);
		return stripslashes(preg_replace('/[\?\[\]\/=\+<>\:;"\',\*\|#]/','',$str));
	}
}
 
?>