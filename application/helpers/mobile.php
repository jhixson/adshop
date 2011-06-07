<?php defined('SYSPATH') or die('No direct script access.');
 
class mobile_Core {

    public function isMobile() {
    	$devices = array(
	        "android"       => "android",
	        "blackberry"    => "blackberry",
	        "iphone"        => "(iphone|ipod)",
	        "opera"         => "opera mini",
	        "palm"          => "(avantgo|blazer|elaine|hiptop|palm|plucker|xiino)",
	        "windows"       => "windows ce; (iemobile|ppc|smartphone)",
	        "generic"       => "(kindle|mobile|mmp|midp|o2|pda|pocket|psp|symbian|smartphone|treo|up.browser|up.link|vodafone|wap)"
	    );
    	
        $userAgent = $_SERVER['HTTP_USER_AGENT'];
        $accept    = $_SERVER['HTTP_ACCEPT'];

        if (isset($_SERVER['HTTP_X_WAP_PROFILE']) || isset($_SERVER['HTTP_PROFILE'])) {
            return true;
        } elseif (strpos($accept,'text/vnd.wap.wml') > 0 || strpos($accept,'application/vnd.wap.xhtml+xml') > 0) {
            return true;
        } else {
            foreach ($devices as $device => $regexp) {
                if (mobile::isDevice($devices,$device)) {
                    return true;
                }
            }
        }
        return false;
    }

    protected function isDevice($devices,$device) {
        if(preg_match("/" . $devices[$device] . "/i", $_SERVER['HTTP_USER_AGENT']))
        	return true;
		else
        	return false;
    }
}
 
?>