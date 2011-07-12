<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
	<div class="steps clearfix<?php echo ($editmode) ? ' hide' : '' ?>">
		<?php 
		$i = 1;
		foreach($steps as $k => $v): 
		?>		
		<div class="circle<?php echo $v['active'] ?>"><span class="num"><?php echo $i ?></span><span class="text"><?php echo $v['title'] ?></span></div>
		<?php 
		$i++;
		endforeach;
		?>
	</div>

	<?php if ($editmode): ?>
	<div class="edit_mode_buttons">
		<a href="<?php echo url::base().'place/remove/'.$item->item_id ?>" class="red_button"><span>Remove Ad</span></a>
		<a href="#" class="blue_button" id="finished_editing"><span>I'm Finished Editing</span></a>
	</div>
	<?php
	/*
	<p><a href="#" class="green_button tick place" id="two_for_2" rel="2"><img src="<?php echo url::base() ?>img/tick.png" alt="tick" /><span>You Are Currently Selecting &euro;2 For 2 Months Option</span></a><span class="clear"></span></p>
	<p><a href="#" class="red_button place" id="three_for_4" rel="4"><span>Switch To &euro;3 For 4 Months ?</span></a><span class="clear"></span></p>
	*/
	?>
	<?php endif; ?>
	
	<div id="place_form" class="clearfix<?php echo ($editmode) ? ' edit' : '' ?><?php echo (mobile::isMobile()) ? ' mobile' : '' ?>">
		<div id="place_ad_form">
			<form id="step_1" class="form_section hide" action="<?php echo url::current() ?>">
				<div class="formitem clearfix">
					<label for="item_title">Ad Title:</label>
					<?php /* if (!$editmode): ?><label class="inset">e.g. 2 Year Old Trek Bike, Fully Serviced.</label><?php endif; */ ?>
					<input type="text" class="text required<?php echo ($editmode) ? ' edit' : '' ?>" name="item_title" id="item_title" value="<?php echo ($editmode) ? htmlspecialchars($item->title) : 'e.g. 2 Year Old Trek Bike, Fully Serviced.' ?>" />
					<label class="field_tip">45 characters max.</label>
				</div>
				
				<div class="formitem clearfix">
					<label for="item_description">Description:</label>
					<?php /* if (!$editmode): ?><label class="inset">Be honest when describing your ad. There is no need to add your phone number, contact details or price in the description box.</label><?php endif; */ ?>
					<textarea class="text required<?php echo ($editmode) ? ' edit' : '' ?>" name="item_desc" id="item_desc" rows="8"><?php echo ($editmode) ? htmlspecialchars($item->description) : 'Be honest when describing your ad. There is no need to add your phone number, contact details or price in this description box.' ?></textarea>
					<label class="field_tip">250 Words max.</label>
				</div>
				
				<div class="buttons">
					<?php if (!$editmode): ?>
					<a href="#step_2" class="button right arrow_right"><img src="<?php echo url::base() ?>img/arrow_right.png" alt="arrow" /><span>Continue</span></a>
					<?php endif; ?>
					<a href="#step_4" class="blue_button right<?php echo ($editmode) ? ' show' : '' ?>"><span>Review Changes</span></a>
				</div>
			</form>
			
			<form id="step_2" class="form_section hide" action="<?php echo url::current() ?>">
				<div class="formitem combo">
					<label>Category:</label>
					<div class="select_wrapper">
						<div class="select_container">
							<div class="select clearfix">
								<span class="subname" id="item_category"><?php echo ($editmode) ? $item->cat_title : 'Choose' ?></span>
							</div>
						</div>
						<select id="category_options">
							<option value="">Choose</option>
							<?php foreach($categories as $k => $v): ?>
							<option value="<?php echo $v['id'] ?>"<?php echo ($editmode && $item->cat_title == $v['title']) ? ' selected="selected"' : '' ?>><?php echo $v['title'] ?></option>
							<?php endforeach; ?>
						</select>
					</div>
					<label class="err">This field is required.</label>
				</div>
				
				<span class="clear"></span>
				
				<div class="formitem combo">
					<label>Subcategory:</label>
					<div class="select_wrapper">
						<div class="select_container">
							<div class="select clearfix">
								<span class="subname<?php echo ($editmode) ? '' : ' disabled' ?>" id="item_subcategory"><?php echo ($editmode) ? '' : 'Choose' ?></span>
							</div>
						</div>
						<select id="subcategory_options"<?php echo ($editmode) ? '' : ' disabled="disabled"' ?>>
							<?php if(isset($subcategories)): ?>
							<option value="">Choose</option>
							<?php foreach($subcategories as $k => $v): ?>
							<option value="<?php echo $v['id'] ?>"<?php echo ($editmode && $item->subcat_title == $v['title']) ? ' selected="selected"' : '' ?>><?php echo $v['title'] ?></option>
							<?php 
							endforeach; 
							endif;
							?>
						</select>
					</div>
					<label class="err">This field is required.</label>
				</div>
				
				<span class="clear"></span>
				
				<div class="formitem combo<?php echo !empty($item->subsubcategory_id) ? '' : ' hide' ?>">
					<label id="subsubcategory_label"><?php echo ($editmode && isset($subsubcategories)) ? $subsubcategory_label : 'SubSubcategory' ?>:</label>
					<div class="select_wrapper">
						<div class="select_container">
							<div class="select clearfix">
								<span class="subname<?php echo ($editmode) ? '' : ' disabled' ?>" id="item_subsubcategory"><?php echo ($editmode) ? '' : 'Choose' ?></span>
							</div>
						</div>
						<select id="subsubcategory_options"<?php echo ($editmode) ? '' : ' disabled="disabled"' ?>>
							<?php if(isset($subsubcategories)): ?>
							<option value="">Choose</option>
							<?php foreach($subsubcategories as $k => $v): ?>
							<option value="<?php echo $v['id'] ?>"<?php echo ($editmode && $item->subsubcat_title == $v['title']) ? ' selected="selected"' : '' ?>><?php echo $v['title'] ?></option>
							<?php 
							endforeach; 
							endif;
							?>
						</select>
					</div>
					<label class="err">This field is required.</label>
					<label class="field_options<?php echo ($editmode && $item->subcat_title == 'Dogs') ? '' : ' hide' ?>"><input type="checkbox" name="extra_attributes[]" id="item_crossbreed" value="Crossbreed" <?php echo ($editmode && (array_key_exists('Crossbreed',$extra_attributes) && $extra_attributes['Crossbreed'] == "true")) ? ' checked="checked"' : '' ?>/></label>
				</div>
				
				<span class="clear"></span>
				
				<div class="formitem combo">
					<label>County:</label>
					<div class="select_wrapper">
						<div class="select_container">
							<div class="select clearfix">
								<span class="subname" id="item_county"><?php echo ($editmode) ? $item->location : 'Choose' ?></span>
							</div>
						</div>
						<select id="county_options">
							<option value="">Choose</option>
							<?php foreach($counties as $c): ?>
							<option value="<?php echo $c ?>"<?php echo ($editmode && $item->location == $c) ? ' selected="selected"' : '' ?>><?php echo $c ?></option>
							<?php endforeach; ?>
						</select>
					</div>
					<label class="err">This field is required.</label>
				</div>
				
				<span class="clear"><br /><br /></span>
				
				<div class="formitem clearfix" id="photogrid_holder">
					<?php if(!mobile::isMobile()): ?>
						<span class="phototip">You can drag photos to rearrange them.</span>
					<?php endif ?>
					<label>Photos:</label>
					<?php if(mobile::isMobile()): ?>
					<img src="<?php echo url::base().'img/mobile_no_upload.jpg' ?>" alt="Mobile upload disabled." />
					<?php endif ?>
          <ul id="photo_grid" class="clearfix"<?php echo mobile::isMobile() ? ' style="display: none;"' : ''?>>
						<?php 
						if(isset($media))
							$media_json = json_decode($media,true);
						for($i = 0; $i < 10; $i++) {
							$disabled = ($i < 1) ? '' : ' disabled';
							$strong = 'Photo '.($i+1);
							echo '<li class="photo_button_holder'.$disabled.'">';
							if($editmode && isset($media_json[$i]) && isset($media_json[$i]['src'])) {
								//$width = isset($media_json[$i]['width']) ? $media_json[$i]['width'] : 0;
								//$height = isset($media_json[$i]['height']) ? $media_json[$i]['height'] : 0;
								$angle = isset($media_json[$i]['angle']) ? $media_json[$i]['angle'] : 0;
								$pathinfo = pathinfo($media_json[$i]['src']);
								$image = $pathinfo['filename'];
								$ext = preg_replace('/\?&?.+=.+/','',$pathinfo['extension']);
								echo '<div class="image_holder"><img src="'.url::base().'img/upload/'.$image.'-'.$angle.'.'.$ext.'" data-angle="'.$angle.'" /></div>';
								$strong = '';
							}
							
							echo '<input id="fileInput'.($i+1).'" name="fileInput'.($i+1).'" class="swfupload" type="file" style="display: none;" />';
							echo '<strong>'.$strong.'</strong>';
							echo '<div class="progress_bar"><div class="progress"></div></div>';
							echo "</li>\n";
						}
						?>							
					</ul>
				</div>
				
				<div class="formitem clearfix" id="item_price_field">
					<label for="item_price">Price: &euro;</label>
					<?php /* if (!$editmode): ?><label class="inset">Optional, but recommended.</label><?php endif; */ ?>
					<input type="text" class="text<?php echo ($editmode) ? ' edit' : '' ?>" name="item_price" id="item_price" value="<?php echo ($editmode) ? htmlspecialchars($item->price) : 'A price is recommended.' ?>" />
				</div>
				
				<div class="buttons">
					<?php if (!$editmode): ?>
					<a href="#step_1" class="button arrow_left"><img src="<?php echo url::base() ?>img/arrow_left.png" alt="arrow" /><span>Back</span></a>
					<a href="#step_3" class="button right arrow_right"><img src="<?php echo url::base() ?>img/arrow_right.png" alt="arrow" /><span>Continue</span></a>
					<?php endif; ?>
					<a href="#step_4" class="blue_button right<?php echo ($editmode) ? ' show' : '' ?>"><span>Review Changes</span></a>
				</div>
			</form>
			
			<form id="step_3" class="form_section hide" action="<?php echo url::current() ?>">
				<div class="formitem clearfix">
					<label for="item_name">Your Name:</label>
					<?php /* if (!$editmode): ?><label class="inset"><?php echo isset($user) ? $user->name : "Your first name is fine." ?></label><?php endif; */ ?>
					<input type="text" class="text required<?php echo ($editmode) ? ' edit' : '' ?>" name="item_name" id="item_name" value="<?php echo ($editmode) ? htmlspecialchars($item->owner_name) : 'Your first name is fine.' ?>" tabindex="1" <?php echo ($editmode && !$isAdmin) ? ' readonly="readonly"' : '' ?>/>
				</div>
				
				<div class="formitem clearfix">
					<label for="item_phone">Your Phone:</label>
					<?php /* if (!$editmode): ?><label class="inset"><?php echo isset($user) ? $user->phone : "Mobile numbers are preferred." ?></label><?php endif; */ ?>
					<input type="text" class="text smaller required<?php echo ($editmode) ? ' edit' : '' ?>" name="item_phone_prefix" id="item_phone_prefix" value="<?php echo ($editmode) ? htmlspecialchars($item->owner_phone_prefix) : '087' ?>" tabindex="2" <?php echo ($editmode && !$isAdmin) ? ' readonly="readonly"' : '' ?>/>
					<input type="text" class="text smallish required<?php echo ($editmode) ? ' edit' : '' ?>" name="item_phone" id="item_phone" value="<?php echo ($editmode) ? htmlspecialchars($item->owner_phone) : '7777777' ?>" tabindex="3" <?php echo ($editmode && !$isAdmin) ? ' readonly="readonly"' : '' ?>/>
				</div>
				
				<div class="formitem clearfix">
					<label for="item_email">Your E-mail:</label>
					<?php /* if (!$editmode): ?>
						<?php if(Auth::instance()->logged_in()): ?>
							<label class="inset"><?php echo $user->username ?></label>
						<?php else: ?>
							<label class="inset">Your address will be hidden.</label>
						<?php endif; ?>
					<?php endif; */ ?>
					<input type="email" class="text email required<?php echo ($editmode) ? ' edit' : '' ?>" name="item_email" id="item_email" value="<?php echo ($editmode || Auth::instance()->logged_in()) ? htmlspecialchars($ad_owner) : 'Your address will be hidden.' ?>" tabindex="4" <?php echo ($editmode && !$isAdmin) ? ' readonly="readonly"' : '' ?>/>
					<label class="field_options<?php echo ($editmode && !$isAdmin) ? ' hide' : '' ?>"><input type="checkbox" name="item_hide_email" id="item_hide_email" value="No e-mails from buyers" <?php echo ($editmode && $item->hide_email) ? ' checked="checked"' : '' ?>/></label>
				</div>
				
				<?php if(!Auth::instance()->logged_in()): ?>
				<div class="formitem clearfix">
					<?php if (!$editmode): ?>
					<label for="item_password">Create Password:</label>
					<?php else: ?>
					<label for="item_password">Edit Password:</label>
					<?php endif; ?>
					<?php /*<label class="inset">Your password is used to edit your ad.</label>*/ ?>
					<input type="text" class="text required" name="item_password" id="item_password" value="Your password is used to edit your ad." tabindex="5" />
					<label class="field_options"><small class="hide"><a href="#" id="password_reminder" class="tip">I forgot my password</a></small></label>
				</div>
				<?php endif; ?>
				
				
				<div class="buttons">
					<?php if (!$editmode): ?>
					<a href="#step_2" class="button arrow_left"><img src="<?php echo url::base() ?>img/arrow_left.png" alt="arrow" /><span>Back</span></a>
					<a href="#step_4" class="button right arrow_right"><img src="<?php echo url::base() ?>img/arrow_right.png" alt="arrow" /><span>Continue</span></a>
					<?php endif; ?>
					<a href="#step_4" class="blue_button right<?php echo ($editmode) ? ' show' : '' ?>"><span>Review Changes</span></a>
					<p class="trade_ad right<?php echo ($editmode && !$isAdmin) ? ' hide' : '' ?>"><input type="checkbox"<?php echo ($editmode && $item->trade_ad) ? ' checked="checked"' : '' ?> name="item_trade_ad" id="item_trade_ad" value="Trade Ad?" /></p>
				</div>
				
				<div id="trade_details" class="clear <?php echo ($editmode && $item->trade_ad) ? '' : 'hide' ?>">
					<div class="formitem clearfix">
						<label for="item_business_name">Business Name:</label>
						<input type="text" class="text<?php echo ($editmode) ? ' edit' : '' ?>" name="item_business_name" id="item_business_name" value="<?php echo ($editmode) ? htmlspecialchars($item->trade_company) : '' ?>" />
					</div>
					
					<div class="formitem clearfix">
						<label for="item_business_address">Business Address:</label>
						<input type="text" class="text<?php echo ($editmode) ? ' edit' : '' ?>" name="item_business_address" id="item_business_address" value="<?php echo ($editmode) ? htmlspecialchars($item->trade_address) : '' ?>" />
					</div>
				</div>
			</form>
			
			<form id="step_4" class="form_section hide" action="<?php echo url::current() ?>">
				<div class="content clearfix">
					<a class="button edit_button" href="#step_2"><span></span></a>
          <a href="#step_2" id="edit_category" class="edit_button">edit category</a>
          <a href="#" class="button right disabled"><span>E-mail Button Goes Here</span></a>
					<span class="clear"></span>
					
					<div id="details">
						<div id="item_title_holder"><h2></h2> <a href="#step_1" id="edit_title" class="edit_button">edit title</a></div>
						<div class="featured">
              <div class="img_holder"></div>
							<div id="scroller_track">
								<a id="scroller" href="#"></a>
							</div>
						</div>
						
						<p id="priceline" class="clearfix">
							<strong>Price: <span id="price"></span></strong>
							<strong>County: <span id="county"></span></strong>
							<strong>Call <span id="seller_name"></span>: <span id="phone"></span></strong>
							<br />
							<a href="#step_2" id="edit_price" class="edit_button">edit price or county</a>
							<?php if (!$editmode || $isAdmin): ?>
							<a href="#step_3" id="edit_details" class="edit_button">edit name, phone, e-mail or password</a>
							<?php else: ?>
							<a href="#" id="edit_details" class="edit_button disabled"><b>For security:</b> after publishing, you cannot change your name, phone or e-mail address.</a>
							<span class="mistakes">For mistakes <a href="#" id="mistake" class="tip">Contact Us</a></span>
							<?php endif; ?>
						</p>
						
						<p><strong>Description:</strong> <a href="#step_1" id="edit_description" class="edit_button">edit description</a></p>
						<div id="item_description_holder"></div>
					</div>
		
					<div id="thumbstrip">
						<a href="#step_2" id="edit_photos" class="edit_button">edit photos</a>
						<ul id="thumb_images"></ul>
					</div>
					<!--
          <a href="#" class="button disabled"><span>E-mail Button Goes Here</span></a>
          -->
					<?php if (!$editmode): ?>
					<a href="#step_5" id="finish_ad_button" class="blue_button right"><span>Finish and Pay</span></a>
					<?php endif; ?>
				</div>
			</form>
			
			<form id="step_5" class="form_section hide" action="<?php echo url::current() ?>">
        <div class="formitem clearfix"<?php echo mobile::isMobile() ? ' style="width: 400px;"' : '' ?>>
					<label for="item_title">Pay By:</label>
					<?php if(!mobile::isMobile()): ?>
            <p class="none"><a class="button" href="#" id="pay_by_phone"><span>Mobile</span></a></p> <p class="none" style="font-size: 14px; padding-top: 10px;">or...</p>
          <?php endif; ?>
					 <p class="none"><a class="button" href="#" id="pay_by_paypal"><span>PayPal</span></a></p>
				</div>
				
				<!--
				<div class="formitem clearfix" style="width: auto;">
					<label for="item_coupon">Coupon:</label>
					<input type="text" class="text small" name="item_coupon" id="item_coupon" value="Enter PAYCODE here." />
					<a href="#" class="button inline" id="pay_with_coupon"><span>Pay with Coupon</span></a>
				</div>
				-->
				<div class="formitem clearfix">
					<label for="item_coupon" class="long">If you have a code you can:</label>
					<input type="text" class="text small hide" name="item_coupon" id="item_coupon" value="Enter PAYCODE here." />
					<a href="#" class="button inline start" id="pay_with_coupon"><span>Pay with Coupon</span></a>
				</div>
				
				<p class="important">IMPORTANT: ADSHOP.IE WILL NEVER ASK YOU TO SEND A TEXT</p>
				
				<input type="hidden" name="item_id" id="item_id" value="<?php echo ($editmode) ? $item->item_id : '' ?>" />
			</form>
		</div>
	</div>
	
	<?php if (!$editmode): ?>
  <p class="tagline">&quot;&euro;2.50 for 3 months. Pay by <?php echo !mobile::isMobile() ? 'Mobile or ' : '' ?>PayPal. Edit ad whenever you want, for free. Remove ad when sold&quot;.<small>You will be charged only once : )</small></p>
	<?php endif; ?>
	
	<form action="https://www.sandbox.paypal.com/cgi-bin/webscr" method="post" id="paypal_form">
		<input type="hidden" name="business" value="j_hixs_1307546967_biz@yahoo.com" />
		<input type="hidden" name="cmd" value="_xclick" />
		<input type="hidden" name="item_name" value="AdShop Ad placement for 3 Months" />
		<input type="hidden" name="amount" value="2.50" />
		<input type="hidden" name="currency_code" value="EUR" />
		<input type="hidden" name="pp_timestamp" id="pp_timestamp" value="" />
		<input type="hidden" name="return" value="http://adshop.ie/" />
		<input type="hidden" name="rm" value="2" />
		<input type="hidden" name="custom" id="pp_custom" value="" />
	</form> 
