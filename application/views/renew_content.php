<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
	
	<h2 class="heading">Renew: <?php echo $item->title ?> <a href="<?php echo url::base() ?>item/<?php echo $item->item_id.'/'.url::title($item->title) ?>">Return to Ad</a></h2>
	
	<?php
	/*
	<p><a href="#" class="green_button tick place" id="two_for_2" rel="2"><img src="<?php echo url::base() ?>img/tick.png" alt="tick" /><span>You Are Currently Selecting &euro;2 For 2 Months Option</span></a><span class="clear"></span></p>
	<p><a href="#" class="red_button place" id="three_for_4" rel="4"><span>Switch To &euro;3 For 4 Months ?</span></a><span class="clear"></span></p>
	*/
	?>
	
	<div id="place_form" class="clearfix">
		<div class="inner">
			<form id="step_5" class="form_section" action="<?php echo url::current() ?>">
				<div class="formitem clearfix">
					<label for="item_title">Pay By:</label>
					<p class="none"><a class="button" href="#" id="renew_by_phone"><span>Mobile</span></a></p> <p class="none" style="font-size: 14px; padding-top: 10px;">or...</p> <p class="none"><a class="button" href="#" id="renew_by_paypal"><span>PayPal</span></a></p>
				</div>
				
				<!--
				<div class="formitem clearfix" style="width: auto;">
					<label for="item_coupon">Coupon:</label>
					<input type="text" class="text small" name="item_coupon" id="item_coupon" value="Enter PAYCODE here." />
					<a href="#" class="button inline" id="renew_with_coupon"><span>Pay with Coupon</span></a>
				</div>
        -->

        <!--
				<div class="formitem clearfix">
					<label for="item_coupon" class="long">If you have a code you can:</label>
					<input type="text" class="text small hide" name="item_coupon" id="item_coupon" value="Enter PAYCODE here." />
					<a href="#" class="button inline start" id="renew_with_coupon"><span>Pay with Coupon</span></a>
        </div>
        -->
				
				<p class="important">IMPORTANT: ADSHOP.IE WILL NEVER ASK YOU TO SEND A TEXT</p>
				
				<input type="hidden" name="item_id" id="item_id" value="<?php echo $item->item_id ?>" />
			</form>
		</div>
	</div>
	
  <p class="tagline">&quot;&euro;2.50 for 3 months. Pay by Mobile or PayPal. Edit ad whenever you want, for free. Remove ad when sold&quot;.<small>You will be charged only once : )</small></p>

  <?php
    $paypal_url = "https://www.sandbox.paypal.com/cgi-bin/webscr";
    $return_url = "http://test.adshop.ie/";
    $business_email = "j_hixs_1307546967_biz@yahoo.com";
    if(IN_PRODUCTION) {
      $paypal_url = "https://www.paypal.com/cgi-bin/webscr";
      $return_url = "http://adshop.ie/";
      $business_email = "payments@adshop.ie";
    }
?>

  <form action="<?php echo $paypal_url ?>" method="post" id="paypal_form">
  <input type="hidden" name="business" value="<?php echo $business_email ?>" />
		<input type="hidden" name="cmd" value="_xclick" />
		<input type="hidden" name="item_name" value="AdShop Ad placement for 3 Months" />
		<input type="hidden" name="amount" value="2.50" />
		<input type="hidden" name="currency_code" value="EUR" />
		<input type="hidden" name="pp_timestamp" id="pp_timestamp" value="" />
    <input type="hidden" name="return" value="<?php echo $return_url ?>" />
		<input type="hidden" name="rm" value="2" />
		<input type="hidden" name="custom" id="pp_custom" value="" />
	</form>
