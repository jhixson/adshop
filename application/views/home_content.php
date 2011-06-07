<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
	<div class="content">
		<h3>Recent Ads:</h3>
		<div id="recent_grid" class="clearfix">
			<?php foreach($recent_ads as $ad): 
				$media = json_decode($ad->media,true);
				$image = $media[0]['src'];
				$pathinfo = pathinfo($image);
				$image_t = $pathinfo['filename']."-t.".$pathinfo['extension'];
			?>
				<div><a href="<?php echo url::base().'item/'.$ad->item_id.'/'.url::title($ad->title) ?>" class="item" rel="<?php echo $ad->item_id ?>"><span class="inner_border"></span><img src="<?php echo url::base() ?>img/upload/<?php echo $image_t ?>" alt="<?php echo $ad->title ?>" /></a></div>
			<?php endforeach; ?>
		</div>
	</div>
	
	<a href="<?php echo url::base() ?>place/" class="small_black_button" id="place_ad_small"><span>10 Photos. 2 Months. &euro;2.</span></a>
	
	<div class="content">
		<h3>Recently Sold:</h3>
		<div id="sold_list" class="clearfix">
			<?php foreach($recently_sold as $ad):
				$media = json_decode($ad->media,true);
				$image = $media[0]['src'];
				$pathinfo = pathinfo($image);
				$image_t = $pathinfo['filename']."-t.".$pathinfo['extension'];
			?>
				<div><a href="<?php echo url::base().'item/'.$ad->item_id.'/'.url::title($ad->title) ?>"><span class="small_sold_stamp">SOLD</span><img src="<?php echo url::base() ?>img/upload/<?php echo $image_t ?>" alt="<?php echo $ad->title ?>" /></a></div>
			<?php endforeach; ?>
		</div>
	</div>