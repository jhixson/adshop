<?php
/*****************************************************************************
 * Class Name   : CreditsManager
 *
 * Date Created : April 2010
 *
 * Description  : - Singleton class
 *                - Parses XML stored in countries.txt and zong<countryCode>.txt
 *                  files. Stores parsed data from country files to PricePoint
 *                  objects.
 *                - Performs transaction management
 *                - Handles crediting
 * -----------
 * Properties:
 * -----------
 * $creditsPerUSD    : Number of credits users get per USD$1.00
 *
 * $countries        : File name of flat file with list of supported
 *                     countries extracted from ZongLookUp.getSupportedCountries()
 *
 * $transRefGenerated: File name of flat file with latest generated transRef
 *                     number. Starts from 0.
 *
 * $transactions     : File name of flat file storing transRef and numCredits
 *                     of transactions started
 * 
 * $app              : Application Name. See Zong Integration Guide.
 * $redirectUrl      : Users directed here after transaction completes.
 * $basketUrl        : Zong directs users here to restart purchase if needed
 * $lang             : Language for text in Zong's iframe UI
 *
 * ----------
 * Functions:
 * ----------
 * -----------------------------------------------------------------------------
 * Function Name: getCountries()
 * Description  : Extracts list of countries from "countries.txt"
 * Input        : -
 * Output       : Array of country codes
 * -----------------------------------------------------------------------------
 * Function Name: getPricePointsForCountry($country)
 * Description  : Opens file corressponding to $country, and parses the XML
 * Input        : $country - 2 letter country code
 * Output       : Array of PricePoint objects
 * -----------------------------------------------------------------------------
 * Function Name: calculateCredits($pricePoints)
 * Description  : Calculates and assigns number of credits to each price point
 *                by converting working price to USD and multiplying by number
 *                stored in $creditsPerUSD. Sets item description per price
 *                point.
 * Input        : Array of PricePoint objects
 * Output       : Array of PricePoint objects
 * -----------------------------------------------------------------------------
 * Function Name: initiateTransaction($pricePoint)
 * Description  : Generates new transaction reference, store it together with
 *                number of credits selected by User in "transactions.txt".
 *                Appends required parameters to entryPointUrl.
 * Input        : $pricePoint - User selected PricePoint object
 * Output       : $entryPointUrl - entryPointUrl with appended parameters
 * -----------------------------------------------------------------------------
 * Function Name: initiateBonusTransaction($completedTransRef, $bonusEntryPointUrl)
 * Description  : Generates new transaction reference for bonus transaction,
 *                retrieves number of credits purchased in previously completed
 *                transaction and store both in "transactions.txt". Appends
 *                new transaction ref to $bonusEntryPointUrl
 * Input        : $completedTransRef - transaction ref of previously completed
 *                                     purchase
 *                $bonusEntryPointUrl - for user to claim bonus on your site
 * Output       : $bonusEntryPointUrl with new transaction reference appended
 * -----------------------------------------------------------------------------
 * Function Name: generateTransactionRef()
 * Description  : Generates new transaction reference by opening
 *                "trans_ref_generated.txt" and appending the value extracted to
 *                "trans" and incrementing the value in the file by one.
 * Input        : -
 * Output       : Generated transaction ref. eg: "trans21" if value in file
 *                21.
 * -----------------------------------------------------------------------------
 * Function Name: storeTransRef($transRef, $numCredits)
 * Description  : Stores $transRef and $numCredits in "transactions.txt" for
 *                use in crediting user during callback or starting bonus
 *                transaction.
 * Input        : $transRef - transaction reference of transaction
 *                $numCredits - number of credits for transaction
 * Output       : -
 * -----------------------------------------------------------------------------
 * Function Name: getCreditsForTransaction($transRef)
 * Description  : Opens "transactions.txt" and returns number for credits
 *                associated with transaction ref passed in.
 * Input        : $transRef - transaction reference to grab credits for
 * Output       : Number of credits associated with transaction
 * -----------------------------------------------------------------------------
 * Function Name: creditUser($numCredits)
 * Description  : Credits user by reading the value stored in "credits.txt"
 *                and updating it after adding $numCredits to current credits.
 * Input        : $numCredits - number of credits to be added to user's account
 * Output       : Array of PricePoint objects
 * -----------------------------------------------------------------------------
 * Function Name: getUserCredits()
 * Description  : Returns current credit balance of User from "credits.txt"
 * Input        : -
 * Output       : $currentCredits - number of credits User currently has
 * -----------------------------------------------------------------------------
 *
 *  Referenced in: index,php, selectprice.php, iframe.php, redirect.php,
 *                callback.php
 *
 * @author Shaun Lim | shaun@zong.com | Zong, Inc.
 ****************************************************************************/

include_once 'application/classes/PricePoint.php';

class CreditsManager {
    //*******************Make CreditsManager Singleton********************//
    private static $instance;

    // A private constructor; prevents direct creation of object
    private function __construct() {
    }

    // The singleton method
    public static function singleton() {
        if (!isset(self::$instance)) {
            $c = __CLASS__;
            self::$instance = new $c;
        }

        return self::$instance;
    }
    //********************************************************************//
    
	public $transRef = 0;
  
    //Example conversion set as USD$1 = 100 credits.
    private $creditsPerUSD = 100;

    private $countries = "application/zong_cache/countries.txt";
    private $transRefGenerated = "application/zong_cache/trans_ref_generated.txt";
    private $transactions = "application/zong_cache/transactions.txt";
    private $credits = "application/zong_cache/credits.txt"; //Example assumes only single user

    //*******************Parameters to append to entryURLs**************//
    private $app = "AdShop.ie";
    private $redirectUrl = "http://adshop.ie/zong_redirect.php"; //TODO: Enter your redirectUrl here. Eg: http://yoursite.com/zongplusdemo/redirect.php
    private $basketUrl = "http://adshop.ie/place"; //TODO: Enter your basketUrl here. Eg: http://yoursite.com/zongplusdemo/lookup/
    private $lang = "en"; //language for UI
    //*******************************************************************//

    //Retrieves array of countryCodes from previously cached countrylist
    function getCountries() {
        $countries = array();
        $fh = fopen($this->countries,"r");
        while(!feof($fh)) {
            $country = fgets($fh);
            if($country != "")
                $countries[] = $country;
        }
        return $countries;
    }

    //Retrieves array of PricePoints from previously cached pricelist
    function getPricePointsForCountry($country) {
        //Opens flat file containing selected country's price points
        $filename = "application/zong_cache/zong" . "IE" . ".txt";
        $rawXML = file_get_contents($filename);
        $xml = simplexml_load_string($rawXML);

        $pricePoints = array();

        foreach ($xml->items->item as $item) {
            $pricePoint = new PricePoint();

            $pricePoint->localCurrency = strval($xml->localCurrency);
            $pricePoint->exchangeRate = strval($xml->exchangeRate);

            $pricePoint->itemRef = strval($item->attributes()->itemRef);
            $pricePoint->workingPrice = strval($item->attributes()->workingPrice);
            $pricePoint->outPayment = strval($item->attributes()->outPayment);
            $pricePoint->entryPointUrl = strval($item->entrypointUrl);

            if($item->attributes()->zongPlusOnly == 'true')
                $pricePoint->isZongPlus = true;
            else
                $pricePoint->isZongPlus = false;

            $pricePoints[] = $pricePoint;
        }
        return $pricePoints;
    }

	//Retrieves first elemet from array of PricePoints from previously cached pricelist
    function getLowestPricePointForCountry($country) {
        //Opens flat file containing selected country's price points
        $filename = "application/zong_cache/zong" . "IE" . ".txt";
        $rawXML = file_get_contents($filename);
        $xml = simplexml_load_string($rawXML);

        $pricePoints = array();

        //foreach ($xml->items->item as $item) {
		$item = $xml->items->item[0];
            $pricePoint = new PricePoint();

            $pricePoint->localCurrency = strval($xml->localCurrency);
            $pricePoint->exchangeRate = strval($xml->exchangeRate);

            $pricePoint->itemRef = strval($item->attributes()->itemRef);
            $pricePoint->workingPrice = strval($item->attributes()->workingPrice);
            $pricePoint->outPayment = strval($item->attributes()->outPayment);
            $pricePoint->entryPointUrl = strval($item->entrypointUrl);

            if($item->attributes()->zongPlusOnly == 'true')
                $pricePoint->isZongPlus = true;
            else
                $pricePoint->isZongPlus = false;

            $pricePoints[] = $pricePoint;
        //}
        return $pricePoint;
    }

    //Calculate number for credits per price point, populate numCredits and
    //itemDesc in each price point
    function calculateCredits($pricePoints) {
        foreach($pricePoints as $pricePoint) {
            //Convert to preferred currency. In this example, USD
            $workingPriceInUSD = (float) $pricePoint->workingPrice / (float) $pricePoint->exchangeRate;
            //Set number of credits per price point
            $pricePoint->numCredits = $workingPriceInUSD * $this->creditsPerUSD;
            settype($pricePoint->numCredits,"int"); //ensure round number credits
            //Set itemDesc per price point.
            $pricePoint->itemDesc = $pricePoint->numCredits . " credits";
        }

        return $pricePoints;
    }

    //Generates new transaction reference, store it together with
    //number of credits selected by User in "transactions.txt".
    //Appends required parameters to entryPointUrl.
    function initiateTransaction($pricePoint) {
        //Generate a transaction reference
        //$transRef = $this->generateTransactionRef();

		$transRef = $this->transRef;

        //Store transRef and numCredits
        //$this->storeTransRef($transRef, $pricePoint->numCredits);

        //Append parameters to entryPointUrl
        $entryPointUrl = $pricePoint->entryPointUrl . "&transactionRef=$transRef" .
            "&amp;itemDesc=" . urlencode($pricePoint->itemDesc) . "&amp;redirect=" . urlencode($this->redirectUrl) .
            "&amp;lang=" . $this->lang . "&amp;app=" . $this->app .
            "&amp;basketUrl=" . urlencode($this->basketUrl);

        return $entryPointUrl;
    }

    //Generates new transaction reference for bonus transaction,
    //retrieves number of credits purchased in previously completed
    //transaction and store both in "transactions.txt". Appends
    //new transaction ref to $bonusEntryPointUrl
    function initiateBonusTransaction($completedTransRef, $bonusEntryPointUrl) {
        //Generate new transaction ref for bonus
        $bonusTransRef = $this->generateTransactionRef();
        //Get number of credits for bonus
        $bonusCredits = $this->getCreditsForTransaction($completedTransRef);
        //Store transaction ref and number of credits
        $this->storeTransRef($bonusTransRef, $bonusCredits);

        //Append new transRef to bonusEntryPointUrl
        return $bonusEntryPointUrl . "&transactionRef=" . $bonusTransRef;
    }

    //Generates new transaction reference by opening
    //"trans_ref_generated.txt" and appending the value extracted to
    //"trans" and incrementing the value in the file by one.
    function generateTransactionRef() {
        $fh = fopen($this->transRefGenerated,'r+');
        $newCount = fgets($fh) + 1;
        rewind($fh); //prepare for overwrite
        fwrite ($fh, $newCount);
        fclose ($fh);

        return "trans" . $newCount; //returns generated transRef
    }

    //Stores $transRef and $numCredits in "transactions.txt" for
    //use in crediting user during callback or starting bonus
    //transaction.
    function storeTransRef($transRef, $numCredits) {
        $fh = fopen($this->transactions,'a'); //open to append
        $record = $transRef . "," . $numCredits;
        fwrite($fh,$record); //eg: trans1,100 -> transRef = trans1 numCredits = 100
        fwrite($fh,"\r\n");
        fclose($fh);
    }

    //Opens "transactions.txt" and returns number for credits
    // associated with transaction ref passed in.
    function getCreditsForTransaction($transRef) {
        $fh = fopen($this->transactions,'r');

        while(!feof($fh)) {
            $line = fgets($fh);
            $tokens = explode(",",$line);

            //match transaction references
            if($tokens[0] == $transRef)
                return $tokens[1]; //return number of credits for the transaction
        }
    }

    //Credits user by reading the value stored in "credits.txt"
    //and updating it after adding $numCredits to current credits.
    function creditUser($numCredits) {
        $creditsFile = fopen($this->credits,'r+');
        $currentCredits = fgets($creditsFile);
        $updatedCredits = $currentCredits + $numCredits;
        rewind($creditsFile); //prepare for overwrite
        fwrite ($creditsFile, $updatedCredits);
        fclose ($creditsFile);
    }

    //Returns current credit balance of User from "credits.txt"
    function getUserCredits() {
        $creditsFile = fopen($this->credits,'r');
        $currentCredits = fgets($creditsFile);
        fclose ($creditsFile);
        return $currentCredits;
    }
}
?>
