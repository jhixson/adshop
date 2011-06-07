<?php
/*****************************************************************************
 * Class Name   : ZongLookUp
 *
 * Date Created : April 2010
 *
 * Description  : Contains two functions that connect to Zong's API to get
 *                data needed to integrate Zong.
 * -----------
 * Properties:
 * -----------
 * $apikey           : Your Zong Developers Account API Key
 * $custKey          : Your customer key
 * $countryLookUpUrl : URL of Zong's listConnectedCountries method
 * $priceLookUpUrl   : URL of Zong's priceLookUp method
 *
 * ----------
 * Functions:
 * ----------
 * Function Name:   getSupportedCountries
 *
 * Description  :   Calls Zong's listConnectedCountries API method to get list
 *                  of countries connected and available. Caches results in a
 *                  flat file - "countries.txt".
 * Input        :    -
 * Output       :   "countries.txt" flat file that contains extracted list
 *                  of countries
 *
 * Function Name:   getAvailablePricePoints
 *
 * Description  :   Calls Zong's priceLookUp method to get price points data
 *                  for each country. Caches results in txt files.
 * Input        :   $countries - Array of country codes
 * Output       :   One flat file for each country, name "zong<countryName>.txt"
 *                  eg: For US, "zongUS.txt"              
 *
 * Referenced in: cachezong.php
 *
 * @author Shaun Lim | shaun@zong.com | Zong, Inc.
 ****************************************************************************/

class ZongLookUp {
    //Customer-specific information
    private $apikey = "c7610ab7-e77c-4cae-b864-58cce3e5ec5f"; //TODO: Enter your Zong Developer Account API key
    private $custKey = "adshpprd"; //TODO: Enter your customer key

    //Zong lookup URLs
    private $countryLookUpUrl = "https://api.zong.com/zapi/v1/lookup/?method=listConnectedCountries";
    private $priceLookUpUrl = "http://pay01.zong.com/zongpay/actions/default?method=lookup";

    function getSupportedCountries() {
        $postVars = "api_key=$this->apikey";

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL,$this->countryLookUpUrl);

        //Dirty fix to ignore SSL verification. Fix for production.
        //http://unitstep.net/blog/2009/05/05/using-curl-in-php-to-access-https-ssltls-protected-sites/
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postVars);
        curl_setopt($ch, CURLOPT_FAILONERROR,1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);

        $rawXml = curl_exec($ch); //TODO: check if response is correct
        curl_close($ch);

        $xml = simplexml_load_string($rawXml);

        $countries = array();

        //Cache list of countries in flat file
        $fh = fopen("application/zong_cache/countries.txt",'a');

        foreach ($xml->countries->country as $country) {
            $country = $country->attributes()->code;
            $countries[] = $country;
            fwrite($fh,$country);
            fwrite ($fh, "\r\n");
        }
        fclose($fh);
        
        return $countries; //return array of country codes
    }

    function getAvailablePricePoints($countries) {
        //Get Price Look Up table per country in $countries
        foreach ($countries as $countryCode) {
            $requestXML = "<?xml version='1.0' encoding='UTF-8'?>
                <requestMobilePaymentProcessEntrypoints
                    xmlns='http://pay01.zong.com/zongpay'
                    xmlns:xsi='http://www.w3.org/2001/XMLSchema-instance'
                    xsi:schemaLocation='http://pay01.zong.com/zongpay/zongpay.xsd'>
                     <customerKey>$this->custKey</customerKey>
                     <countryCode>$countryCode</countryCode>
                     <items currency='USD' />
                </requestMobilePaymentProcessEntrypoints>
                ";

            //Prepare XML string to be sent as "request" POST parameter
            $postVars = "request=$requestXML";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL,$this->priceLookUpUrl);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postVars);
            curl_setopt($ch, CURLOPT_FAILONERROR,1);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 15);

            $rawXml = curl_exec($ch); //TODO: check if response is correct
            curl_close($ch);

            //Cache Response. In this example, flat files are used.
            $filename = "application/zong_cache/zong" . $countryCode . ".txt";
            $file = fopen($filename,'w+') OR die ("Can't open file\n");

            fwrite ($file, $rawXml);
            fclose ($file);
        }
    }
    
}
?>
