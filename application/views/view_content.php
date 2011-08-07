<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
	<?php if($subcategories): ?>
	<div class="content subcategories">
		<?php 
		$i = 0;
		foreach($subcategories as $k => $v):
			$i++; 
		?>
			<div class="subcategory<?php echo $v['class'] ?>">
				<a href="<?php echo url::base() ?>view/<?php echo $category ?>/<?php echo $k ?>">
					<img src="<?php echo url::base() ?>img/<?php echo $category.'/'.$v['image'] ?>" alt="<?php echo $v['title'] ?>" />
					<span><?php echo $v['title'] ?></span>
					<?php echo !empty($v['count']) ? '<span class="count">('.$v['count'].')</span>' : '' ?>
				</a>
			</div>
		<?php 
		if($i % 6 == 0)
			echo '<span class="clear"></span>';
		endforeach; ?>
	</div>
	<?php endif; ?>
	
	<?php
	if(isset($subsubcategories) && count($subsubcategories) > 0): ?>
	<div class="select_wrapper" id="subsubcategory_wrapper">
		<div class="select_container">
			<div class="select clearfix">
				<span class="subname" id="item_category">All <?php echo $subsubcategory_default."s (".$item_count.")" ?></span>
			</div>
		</div>
		<select id="view_subsubcategory_options">
			<option value="">All <?php echo $subsubcategory_default."s (".$item_count.")" ?></option>
			<?php foreach($subsubcategories as $k=>$v): ?>
			<option value="<?php echo $k ?>" <?php echo (!empty($v['class'])) ? ' selected="selected"' : '' ?>><?php echo ($v['count'] > 0) ? $v['title']." (".$v['count'].")" : $v['title'] ?></option>
			<?php endforeach; ?>
		</select>
	</div>
  <?php endif; ?>

	<?php if(isset($subcategory) && $subcategory == 'dogs' && !empty($subsubcategory)): ?>
	<label class="field_options">
		<input type="checkbox" name="show_crossbreeds" id="show_crossbreeds" value="Show crossbreeds" />
	</label>
	<?php endif; ?>
	
	<?php if(isset($sold_ads_count) && $sold_ads_count > 0): ?>
		<p class="context"><a href="<?php echo url::base() ?>user/mySoldAds">View My Sold Ads</a></p>
	<?php elseif(preg_match('/mySoldAds/',url::current())): ?>
		<p class="context"><a href="<?php echo url::base() ?>user/myAds">View My Ads</a></p>
	<?php elseif(preg_match('/sold/',url::current())): ?>
		<p class="context">Everyone's Sold Ads</p>
	<?php elseif(preg_match('/liked/',url::current())): ?>
		<p class="context">Your Liked Ads</p>
	<?php endif; ?>
	
	<?php if(isset($_GET['q'])):
		if(isset($_GET['desc'])): ?>
		<p class="context"><a href="<?php echo url::current() ?>?q=<?php echo $this->input->get('q') ?>">Ignore Descriptions</a></p>
		<?php else: ?>
		<p class="context"><a href="<?php echo url::current(TRUE) ?>&desc=true">Search Descriptions</a></p>
		<?php endif;?>
	<?php endif; ?>
	
	<?php if(count($items) > 0): ?>
		<div id="item_list">
		<?php 
			foreach($items as $i): 
			$image = 'no_photo_uploaded_thumb.gif';
			$media = json_decode($i->media,true);
			if(count($media) > 0 && isset($media[0]) && isset($media[0]['src'])) {
				//$image = $media[0]['src'];
				$angle = isset($media[0]['angle']) ? $media[0]['angle'] : 0;
				$pathinfo = pathinfo($media[0]['src']);
				$filename = $pathinfo['filename'];
				$ts = array();
				preg_match('/(\?&?.+=.+)/',$pathinfo['extension'],$ts);
				if(!isset($ts[0]))
					$ts[0] = '0';
				$ext = preg_replace('/\?&?.+=.+/','',$pathinfo['extension']);
				$image = $filename.'-t.'.$ext.'?ts='.$ts[0];
			}
    ?>
      <div class="content item" data-extra-attributes='<?php echo $i->extra_attributes ?>'>
        <?php echo ($save_list || userdata::is_saved($i->item_id)) ? '<a href="#" class="save_ad stars active" rel="'.$i->item_id.'"></a>' : '<a href="#" class="save_ad stars" rel="'.$i->item_id.'"></a>' ?>
				<?php
				$path = 'view/';
				if(!empty($category))
					$path .= $category.'/';
				if(!empty($subcategory))
					$path .= $subcategory.'/';
				$path .= $i->item_id.'/'.url::title($i->title);
				?>
				<?php if($i->sold): ?>
				<span class="small_sold_stamp">SOLD</span>
				<?php endif; ?>
				<?php 
				if(!$i->sold && ((time() - $i->publish_timestamp) / 60 < 25)): ?>
				<span class="small_new_stamp">NEW</span>
				<?php endif; ?>
				<?php 
				if(!$i->active && !$i->sold): ?>
				<span class="small_expired_stamp">EXPIRED</span>
				<?php endif; ?>
				<div class="img">
					<a href="<?php echo url::base() ?><?php echo $path ?>">
					<img src="<?php echo url::base() ?>img/upload/<?php echo $image ?>" alt="<?php echo $i->title ?>" /></a>
				</div>
				<span class="item_price">
				<?php 
					if(round($i->price) > 0)
            echo '&euro;'.number_format(round($i->price)).' ';
          elseif($i->cat_title == 'Services' || $i->subcat_title == 'Music Lessons' || $i->subcat_title == 'Sports Lessons')
            echo '<span class="disabled">call 4 quote</span> ';
          else
            echo '<span class="disabled">no price</span> ';
					echo $i->location;
				?>
				</span>
				<div class="item_brief">
					<h2><a href="<?php echo url::base() ?><?php echo $path ?>"><?php echo $i->title ?></a></h2>
					<p><?php echo text::widont(text::limit_chars($i->description,userdata::is_saved($i->item_id) ? 133 : 140)) ?></p>
          <?php if(!$i->sold): ?>
          <span class="date"><?php echo date("jS F Y", $i->publish_timestamp) ?></span>
          <?php endif; ?>
				</div>
        <?php /* echo ($save_list || userdata::is_saved($i->item_id)) ? '<div class="remove_ad_buttons"><a href="#" class="small_button remove_ad_button" rel="'.$i->item_id.'"><span>Remove from Saved</span></a></div>' : '' */ ?>
		<?php echo ($save_list || userdata::is_saved($i->item_id)) ? '<div class="remove_ad_buttons">Liked</div>' : '<div class="remove_ad_buttons"></div>' ?>
      </div>
		<?php endforeach; ?>
		</div>
		<?php echo isset($this->pagination) ? $this->pagination->render() : '' ?>
	<?php else: ?>
		<div class="content">
			<p class="none"><?php echo $no_items_message ?></p>
		</div>
	<?php endif; ?>
	
