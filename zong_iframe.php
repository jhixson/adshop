<?php
include_once 'application/classes/CreditsManager.php';
include_once 'application/classes/PricePoint.php';

$item_id = $_GET['item_id'];
if(!is_numeric($item_id))
	die('invalid item id');

$creditsManager = CreditsManager::singleton();

$creditsManager->transRef = $item_id;

$pricePoint = $creditsManager->getLowestPricePointForCountry('IE');
$pricePoint->itemDesc = 'Ad Placement for 3 months';
//$pricePoint = unserialize(base64_decode($pricePoint));

$creditsManager = CreditsManager::singleton();

$entryPointUrl = $creditsManager->initiateTransaction($pricePoint);
?>

<iframe src="<?php echo $entryPointUrl; ?>" width="490" height="350" frameborder="0" scrolling="no" name="zong_iframe" id="zong_iframe"></iframe>
