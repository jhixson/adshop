<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
	
	<h2 class="heading">Remove: <?php echo $item->title ?></h2>
	
	<p class="center">Are you sure you want to remove this ad and mark it as sold?</p>
	
	<div class="remove_buttons">
		<a href="#" class="red_button" id="remove_ad"><span>Remove Ad</span></a>
		<a href="<?php echo url::base().'place/edit/'.$item->item_id.'#step_4' ?>" class="blue_button"><span>Later</span></a>
	</div>
	
	<input type="hidden" name="item_id" id="item_id" value="<?php echo $item->item_id ?>" />