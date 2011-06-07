<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
	<div class="steps clearfix">
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
	
	<div id="place_form" class="clearfix">
		<div class="inner">
			<form id="place_ad_form" action="<?php echo url::current() ?>" method="post" enctype="multipart/form-data" >
				<div id="step_1" class="form_section">
					<div class="formitem clearfix">
						<label for="item_title">Ad Title:</label>
						<input type="text" class="text required edit" name="item_title" id="item_title" value="<?php echo $item->title ?>" />
						<label class="field_tip">60 characters max.</label>
					</div>
					
					<div class="formitem clearfix">
						<label for="item_description">Description:</label>
						<textarea class="text required edit" name="item_description" id="item_description" rows="6"><?php echo $item->description ?></textarea>
						<label class="field_tip">250 Words max.</label>
					</div>
					
					<div class="buttons">
						<a href="#step_4" class="blue_button right show"><span>Review Changes</span></a>
					</div>
				</div>
				
				<div id="step_2" class="form_section">
					<div class="formitem combo">
						<label>Category:</label>
						<div class="select_container">
							<div class="select clearfix"><span class="subname" id="item_category"><?php echo $item->cat ?></span></div>
							<div class="options_details" id="category_options">
								<?php foreach($categories as $k => $v): ?>
								<div class="item clearfix"><h5><?php echo $v['title'] ?></h5></div>
								<?php endforeach; ?>
							</div>
						</div>
					</div>
					
					<span class="clear"></span>
					
					<div class="formitem combo">
						<label>Subcategory:</label>
						<div class="select_container">
							<div class="select clearfix"><span class="subname" id="item_subcategory"><?php echo $item->subcat ?></span><br class="clear"/></div>
							<div class="options_details" id="subcategory_options"></div>
						</div>
					</div>
					
					<span class="clear"></span>
					
					<div class="formitem combo">
						<label>County:</label>
						<div class="select_container">
							<div class="select clearfix"><span class="subname" id="item_county"><?php echo $item->location ?></span></div>
							<div class="options_details" id="county_options">
								<?php foreach($counties as $c): ?>
								<div class="item clearfix"><h5><?php echo $c ?></h5></div>
								<?php endforeach; ?>
							</div>
						</div>
					</div>
					
					<span class="clear"><br /></span>
					
					<div class="formitem clearfix">
						<label>Photos:</label>
						<ul id="photo_grid" class="clearfix">
							<li class="photo_button_holder">
								<input id="fileInput1" name="fileInput1" class="swfupload" type="file" /> 
								<strong>Photo 1</strong>
								<div class="progress_bar"><div class="progress"></div></div>
							</li>
							<li class="photo_button_holder disabled">
								<input id="fileInput2" name="fileInput2" class="swfupload" type="file" /> 
								<strong>Photo 2</strong>
								<div class="progress_bar"><div class="progress"></div></div>
							</li>
							<li class="photo_button_holder disabled">
								<input id="fileInput3" name="fileInput3" class="swfupload" type="file" /> 
								<strong>Photo 3</strong>
								<div class="progress_bar"><div class="progress"></div></div>
							</li>
							<li class="photo_button_holder disabled">
								<input id="fileInput4" name="fileInput4" class="swfupload" type="file" /> 
								<strong>Photo 4</strong>
								<div class="progress_bar"><div class="progress"></div></div>
							</li>
							<li class="photo_button_holder disabled">
								<input id="fileInput5" name="fileInput5" class="swfupload" type="file" /> 
								<strong>Photo 5</strong>
								<div class="progress_bar"><div class="progress"></div></div>
							</li>
							<li class="photo_button_holder disabled">
								<input id="fileInput6" name="fileInput6" class="swfupload" type="file" /> 
								<strong>Photo 6</strong>
								<div class="progress_bar"><div class="progress"></div></div>
							</li>
							<li class="photo_button_holder disabled">
								<input id="fileInput7" name="fileInput7" class="swfupload" type="file" /> 
								<strong>Photo 7</strong>
								<div class="progress_bar"><div class="progress"></div></div>
							</li>
							<li class="photo_button_holder disabled">
								<input id="fileInput8" name="fileInput8" class="swfupload" type="file" /> 
								<strong>Photo 8</strong>
								<div class="progress_bar"><div class="progress"></div></div>
							</li>
							<li class="photo_button_holder disabled">
								<input id="fileInput9" name="fileInput9" class="swfupload" type="file" /> 
								<strong>Photo 9</strong>
								<div class="progress_bar"><div class="progress"></div></div>
							</li>
							<li class="photo_button_holder disabled">
								<input id="fileInput10" name="fileInput10" class="swfupload" type="file" /> 
								<strong>Photo 10</strong>
								<div class="progress_bar"><div class="progress"></div></div>
							</li>
						</ul>
					</div>
					
					<div class="formitem clearfix">
						<label for="item_price">Price: &euro</label>
						<input type="text" class="text  edit" name="item_price" id="item_price" value="<?php echo $item->price ?>" />
					</div>
					
					<div class="buttons">
						<a href="#step_4" class="blue_button right"><span>Review Changes</span></a>
					</div>
				</div>
				
				<div id="step_3" class="form_section">
					<div class="formitem clearfix slim">
						<label for="item_title">Your Name:</label>
						<input type="text" class="text required  edit" name="item_name" id="item_name" value="<?php echo isset($user) ? $user->name : "First name is fine." ?>" />
					</div>
					
					<div class="formitem clearfix slim">
						<label for="item_description">Your Phone:</label>
						<input type="text" class="text  edit" name="item_phone" id="item_phone" value="<?php echo isset($user) ? $user->phone : "Mobiles are preferred, for texting." ?>" />
					</div>
					
					<?php if(!Auth::instance()->logged_in()): ?>
					<div class="formitem clearfix slim">
						<label for="item_description">Your E-mail:</label>
						<input type="text" class="text email required  edit" name="item_email" id="item_email" value="<?php echo $item->username ?>" />
						<label class="field_options"><input type="checkbox" name="item_hide_email" id="item_hide_email" value="No e-mails from buyers" /></label>
					</div>
					
					<div class="formitem clearfix slim">
						<label for="item_description">Create Password:*</label>
						<input type="password" class="text required" name="item_password" id="item_password" />
					</div>
					<?php endif; ?>
					
					
					<div class="buttons">
						<p>*Password is used to edit your ad</p>
						<a href="#step_4" class="blue_button right"><span>Review Changes</span></a>
					</div>
					
					<!--
					<div class="buttons">
						<a href="#step_2" class="button arrow_left"><img src="<?php echo url::base() ?>img/arrow_left.png" alt="arrow" /><span>Back</span></a>
						<a href="#step_4" class="button right arrow_right"><img src="<?php echo url::base() ?>img/arrow_right.png" alt="arrow" /><span>Continue</span></a>
					</div>
					-->
				</div>
				
				<div id="step_4" class="form_section">
					<div class="content clearfix">
						<a class="button" href="#"><span>Cars</span></a>
						<a href="#step_2" id="edit_category" class="edit_button">edit category</a>
						<a href="#" class="button right disabled"><span>E-mail Button Goes Here</span></a>
						<span class="clear"></span>
						<div id="details">
							<div id="item_title_holder"><h2>Audi Q7</h2> <a href="#step_1" id="edit_title" class="edit_button">edit title</a></div>
							<div class="featured">
								<div class="img_holder">
									<img alt="Audi Q7" src="<?php echo url::base() ?>img/upload/audi-q71.jpg">
									<img alt="Audi Q7" src="<?php echo url::base() ?>img/upload/audi-q72.jpg">
									<img alt="Audi Q7" src="<?php echo url::base() ?>img/upload/audi-q73.jpg">
									<img alt="Audi Q7" src="<?php echo url::base() ?>img/upload/audi-q74.jpg">
								</div>
								<div id="scroller_track">
									<a id="scroller" href="#"></a>
								</div>
							</div>
							<p id="priceline">
								<strong>Price: <span id="price">&euro;37</span></strong>
								<strong>County: <span id="county">Waterford</span></strong>
								<strong>Call <span id="seller_name">Joe Fox</span>: <span id="phone">241 2141441</span></strong>
								<br />
								<a href="#step_2" id="edit_price" class="edit_button">edit price or county</a>
								<a href="#step_3" id="edit_details" class="edit_button">edit name or phone</a>
							</p>
							
							<p><strong>Description:</strong> <a href="#step_1" id="edit_description" class="edit_button">edit description</a></p>
							<div id="item_description_holder">
								<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent nec volutpat tortor. Donec bibendum semper nisl, adipiscing vestibulum leo hendrerit mattis.</p>
				
								<p>Cras sit amet dignissim nibh. Ut mi nisl, tristique nec tempus sed, facilisis vel felis. Quisque elit leo, faucibus non fringilla ut, fringilla et erat. Sed est dolor, hendrerit at tempus a, euismod sed diam.</p>			
							</div>
						</div>
						<div id="thumbstrip">
							<a href="#step_2" id="edit_photos" class="edit_button">edit photos</a>
							<ul>
								<li><a href="#" class="thumb active"><img alt="Audi Q7" src="<?php echo url::base() ?>img/upload/audi-q71.jpg" style="margin-top: 0px;"></a></li>
								<li><a href="#" class="thumb"><img alt="Audi Q7" src="<?php echo url::base() ?>img/upload/audi-q72.jpg" style="margin-top: 2px;"></a></li>
								<li><a href="#" class="thumb"><img alt="Audi Q7" src="<?php echo url::base() ?>img/upload/audi-q73.jpg" style="margin-top: 6px;"></a></li>
								<li><a href="#" class="thumb"><img alt="Audi Q7" src="<?php echo url::base() ?>img/upload/audi-q74.jpg" style="margin-top: 24px;"></a></li>
							</ul>
						</div>
						<a href="#" class="button disabled"><span>E-mail Button Goes Here</span></a>
					</div>
				</div>
			</form>
		</div>
	</div>
	
	<p class="tagline">&quot;&euro;<span id="deal">2 for 2</span> months. Pay by phone or PayPal. Edit ad whenever you want, for free. Remove ad when sold.&quot;<small>You will be charged only once : )</small></p>
