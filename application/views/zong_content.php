<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>

<?php if(isset($credits) && isset($countries)): ?>
<p>You currently have <?php echo $credits; ?> credits.</p>
<form name="selectcountry" action="/zong/selectPrice" method="POST">
    <label for="country">Select Country:</label>
    <select name="country">
        <?php foreach ($countries as $country) { ?>
        <option value="<?php echo $country; ?>"><?php echo $country; ?></option>
        <?php } ?>
    </select>
    <input type="submit" value="Submit" name="Submit" />
</form>
<?php endif ?>

<?php if(isset($pricePoints)): ?>
	<form name="selectprice" action="/zong/iFrame" method="POST">
        <?php foreach($pricePoints as $pricePoint) { ?>
        <input type="radio" name="pricePoint" value="<?php echo base64_encode(serialize($pricePoint)); ?>"/>
            <?php
            echo $pricePoint->itemDesc . " for " . $pricePoint->workingPrice . " " . $pricePoint->localCurrency;
            if($pricePoint->isZongPlus) {
                echo " (Z+ only)";
            }
            ?>
        <br />
        <?php } ?>
        <br />
        <input type="submit" value="Buy" name="Submit" />
    </form>
<?php endif ?>

<?php if(isset($entryPointUrl)): ?>
	<iframe src="<?php echo $entryPointUrl; ?>" width="490" height="350" frameborder="0" scrolling="no"/>
<?php endif ?>
