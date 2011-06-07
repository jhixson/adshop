<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
	<div class="inner item">
		<h2><?php echo $item->title ?></h2>
		<p><?php echo text::limit_words($item->description,18) ?></p>
		<p id="priceline">
			<strong>Price: <span id="price">&euro;<?php echo $item->price; ?></span></strong>
			<strong>County: <span id="county"><?php echo $item->location; ?></span></strong>
		</p>
		<p class="readmore"><a href="<?php echo url::base() ?>item/<?php echo $item->item_id ?>/<?php echo url::title($item->title) ?>">Read More...</a></p>
	</div>