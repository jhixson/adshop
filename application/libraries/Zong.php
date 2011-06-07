<?php defined('SYSPATH') OR die('No direct access allowed.');

include_once 'application/classes/PricePoint.php';
include_once 'application/classes/ZongLookUp.php';
include_once 'application/classes/CreditsManager.php';

class Zong_Core {	
	public function cacheZong() {
		$zongLookUp = new ZongLookUp();
		//$countries = $zongLookUp->getSupportedCountries();
		$countries = array('IE'); // we only need Ireland
		$zongLookUp->getAvailablePricePoints($countries);

		return "Done!";
	}
	
	public function verifySignature ($qs) {
	    $pem = file_get_contents('application/zong_cache/cert.pem');
	    //Grab Signature Value
	    preg_match("/signature=(.*?)&/", $qs, $matches);
	    $signature = urldecode($matches[1]);
	    $qs=preg_replace("/signature=([^&]*)/", "signature=", $qs);

	    //Grab Parameters, insert into array for sorting
	    $parameters = array();
	    $token = strtok($qs, "&");

	    while ($token != false) {
	        $parameters[] = $token;
	        $token = strtok("&");
	    }
	    //sort array
	    sort($parameters);

	    //Rebuild URL
	    $rebuiltURL = "?";
	    $firstElement = true;

	    foreach ($parameters as $p) {
	        if($firstElement) {
	            $rebuiltURL .= $p;
	            $firstElement = false;
	        } else {
	            $rebuiltURL .= "&" . $p;
	        }
	    }

	    $publickeyid = openssl_get_publickey($pem);
	    $verified = openssl_verify($rebuiltURL, base64_decode($signature), $publickeyid);
	    openssl_free_key($publickeyid);

	    if($verified == 1) {
	        return true;
	    } else {
	        return false;
	    }
	}
}

?>