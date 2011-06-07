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
				<div class="formitem clearfix">
					<label for="item_coupon" class="long">If you have a code you can:</label>
					<input type="text" class="text small hide" name="item_coupon" id="item_coupon" value="Enter PAYCODE here." />
					<a href="#" class="button inline start" id="renew_with_coupon"><span>Pay with Coupon</span></a>
				</div>
				
				<p class="important">IMPORTANT: ADSHOP.IE WILL NEVER ASK YOU TO SEND A TEXT</p>
				
				<input type="hidden" name="item_id" id="item_id" value="<?php echo $item->item_id ?>" />
			</form>
		</div>
	</div>
	
	<p class="tagline">&quot;&euro;<span id="deal">2.50 for 3</span> months. Pay by Mobile or PayPal. Edit ad whenever you want, for free. Remove ad when sold&quot;.<small>You will be charged only once : )</small></p>
