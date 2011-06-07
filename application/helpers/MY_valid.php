<?php defined('SYSPATH') or die('No direct script access.');
 
class valid extends valid_Core {
 
	/**
	 * Checks whether a string is a valid currency amount (positive decimal numbers with precision:2).
	 *
	 * @see Uses locale conversion to allow decimal point to be locale specific.
	 * @see http://www.php.net/manual/en/function.localeconv.php
	 * 
	 * @param   string   input string
	 * @return  boolean
	 */
	public static function currency($str) {
		$locale = localeconv();
		echo $locale['decimal_point'];
		return (bool) preg_match('/\d+['.$locale['decimal_point'].']\d{2}$/D', (string) $str);
	}
 
}
?>