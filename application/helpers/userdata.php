<?php defined('SYSPATH') or die('No direct script access.');
 
class userdata_Core {
 
	public static function is_saved($item_id) {
		$saved_json = json_decode(cookie::get('saved'),true);
		return $saved_json && in_array($item_id,$saved_json);
	}
}
 
?>