<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>	
	<?php if(count($items) > 0): ?>
		<ul id="item_list">
		<?php foreach($items as $i): ?>
			<?php
			$image = 'no_image.png';
			$media = json_decode($i->media,true); 
			if(count($media['images']) > 0)
				$image = $media['images'][0];
			?>
			<li class="content item">
				<a href="<?php echo url::base() ?>item/<?php echo $i->item_id ?>/<?php echo url::title($i->title) ?>"><img src="<?php echo url::base() ?>img/upload/<?php echo $image ?>" alt="<?php echo $i->title ?>" /></a>
				<span class="item_price">&euro;<?php echo round($i->price) ?>, <?php echo $i->location ?>.</span>
				<div class="item_brief">
					<h2><a href="<?php echo url::base() ?>item/<?php echo $i->item_id ?>/<?php echo url::title($i->title) ?>"><?php echo $i->title ?></a></h2>
					<p><?php echo text::limit_words($i->description,18) ?></p>
				</div>
			</li>
		<?php endforeach; ?>
		</ul>
	<?php else: ?>
		<div class="content">
			<p class="none">No items were found. Please try another search.</p>
		</div>
	<?php endif; ?>