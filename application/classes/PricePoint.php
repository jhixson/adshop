<?php
/*****************************************************************************
 * Class Name   : PricePoint
 *
 * Date Created : April 2010
 *
 * Description  : - Entity class to store data related to each Price Point.
 *                - Used to build radio buttons in selectprice.php
 *                - Serialized in selectprice.php and passed to iframe.php
 *
 * Referenced in: CreditsManager, ZongLookUp, iframe.php, selectprice.php
 *
 * @author Shaun Lim | shaun@zong.com | Zong, Inc.
 ****************************************************************************/
class PricePoint {
    //Stores data extracted from Zong's lookup response
    public $itemRef;
    public $entryPointUrl;
    public $isZongPlus;
    public $localCurrency;
    public $exchangeRate;
    public $workingPrice;
    public $outPayment;
    //End

    //Populated in CreditsManager.calculateCredits()
    public $numCredits; 
    public $itemDesc;
    //End
}
?>
