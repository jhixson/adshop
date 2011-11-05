<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
	<div class="content">
		<a href="<?php echo url::base() ?>view/<?php echo $category ?>/<?php echo $subcategory ?>" class="button arrow_left"><img src="<?php echo url::base() ?>img/arrow_left.png" alt="arrow" /><span><?php echo $item->subcat_title ?></span></a>
		<?php if(!$item->sold): ?>
			<?php if($item->hide_email == 0): ?>
			<a href="#" class="button stamp right tip" id="email_seller_button"><img src="<?php echo url::base() ?>img/stamp--pencil.png" alt="stamp" /><span>E-mail Seller</span></a>
			<?php else: ?>
			<a href="#" class="button disabled right"><span>No E-mails Please</span></a>
      <?php endif; ?>
    <?php endif; ?>
		
		<span class="clear"></span>
		
		<div id="details">
			<div class="title_holder">
			<?php echo ($item->trade_ad == 1) ? '<h2 class="inline" data-item_id="'.$item->item_id.'">'.$item->title.'</h2><a href="#" id="trade_ad_link" class="tip" title="'.$item->trade_company.'. '.$item->trade_address.'">Trade Ad</a>' : '<h2 data-item_id="'.$item->item_id.'">'.$item->title.'</h2>' ?>
			</div>

			<div class="featured<?php echo ($item->trade_ad == 1) ? ' padded-top' : '' ?>">
				<?php if($item->sold): ?>
				<div class="sold_stamp">SOLD</div>
				<?php elseif(!$item->active): ?>
				<div class="expired_stamp">EXPIRED</div>
				<?php endif; ?>
					<?php if(count($media) > 0 && isset($media[0]) && isset($media[0]['src'])):
					$pathinfo = pathinfo($media[0]['src']);
					$ts = array();
					preg_match('/(\?&?.+=.+)/',$pathinfo['extension'],$ts);
					if(!isset($ts[0]))
						$ts[0] = '?ts=0';
					?>
					<div class="img_holder">
						<img src="<?php echo url::base() ?>img/upload/<?php echo $pathinfo['filename'].'.jpg'.$ts[0] ?>" alt="<?php echo $item->title ?>" />
					</div>
					<?php else: ?>
					<div class="img_holder">
						<img src="<?php echo url::base() ?>img/no_photo_uploaded.jpg" alt="" />
					</div>
					<?php endif; ?>
				<div id="scroller_track">
					<a href="#" id="scroller"></a>
				</div>
			</div>
			
			<p id="priceline">
      <?php
        if(round($item->price) > 0)
          echo '<strong>Price: <span id="price">&euro;'.number_format(round($item->price)).'</span></strong>';
        elseif($item->cat_title == 'Services' || $item->subcat_title == 'Music Lessons' || $item->subcat_title == 'Sports Lessons')
          echo '<span class="noprice disabled">call 4 quote</span> ';
        else
          echo '<span class="noprice disabled">no price</span> ';
        ?>
				<?php if(!$item->sold): ?>
				<strong>County: <span id="county"><?php echo $item->location; ?></span></strong>
				<strong>Call <?php echo $item->owner_name ?>: <span id="phone"><?php echo $item->owner_phone_prefix ?> <?php echo preg_replace('/^(\d{3})(\d+)/', '$1 $2', $item->owner_phone) ?></span></strong>
				<?php endif; ?>
			</p>
			
			<p><strong>Description:</strong></p>
			<?php echo text::auto_p($item->description) ?>
		</div>
		
		<div id="thumbstrip">
			<?php if(isset($media) && count($media) > 0): ?>
			<a href="#" class="next_image<?php echo count($media) <= 1 ? ' invisible' : '' ?>">next</a>
			<ul id="thumb_images" class="images-<?php echo min(10,count($media)) ?>">
				<?php 
        foreach($media as $m):
          if(isset($m['src'])) {
            $angle = isset($m['angle']) ? $m['angle'] : 0;
            $pathinfo = pathinfo($m['src']);
            $filename = $pathinfo['filename'];
            $ts = array();
            preg_match('/(\?&?.+=.+)/',$pathinfo['extension'],$ts);
            if(!isset($ts[0]))
              $ts[0] = '?ts=0';
            $ext = preg_replace('/\?&?.+=.+/','',$pathinfo['extension']);
            $image = $filename.'-t.'.$ext.$ts[0];
				?>
				<li class="thumb"><a href="#"><img src="<?php echo url::base() ?>img/upload/<?php echo $image ?>" alt="<?php echo $item->title ?>" /></a></li>
				<?php } endforeach; ?>
			</ul>
			<?php else: ?>
			<ul class="images-1">
				<a href="#" class="next_image invisible">next</a>
				<a href="#" class="no_photo"><li class="thumb noactive"><img src="<?php echo url::base() ?>img/upload/no_photo_uploaded_thumb.gif" alt="No photos were added" /></a></li>
			</ul>
			<?php endif; ?>
			
			<?php if(!$item->sold): ?>
			<ul class="action_buttons">
				<?php if($item->active): ?>
				<li><a href="#" class="button tip" id="safety_button"><span>Safety</span></a></li>
				<?php endif; ?>
				<li><a href="<?php echo (Auth::instance()->logged_in() && (Auth::instance()->get_user()->id == $item->user_id || Auth::instance()->get_user()->has($admin_role))) ? url::base()."renew/".$item->item_id : '#' ?>" class="dark_button tip" id="renew_button"><span>Renew</span></a></li>
				<?php if($item->active): ?>
				<li><a href="<?php echo (Auth::instance()->logged_in() && (Auth::instance()->get_user()->id == $item->user_id || Auth::instance()->get_user()->has($admin_role))) ? url::base()."place/edit/".$item->item_id."#step_4" : '#' ?>" class="dark_button tip" id="edit_button"><span>Edit</span></a></li>
				<?php endif; ?>
				<?php if(!$item->active): ?>
				<li><a href="#" class="button red_button tip" id="remove_button"><span>Remove</span></a></li>
				<?php endif; ?>
			</ul>
			<?php endif; ?>
    </div>
		<span class="clear"></span>
		<?php if(!$item->sold && $item->active): ?>
    <?php /* echo ($item->hide_email == 0) ? '<a href="#" class="button stamp tip" id="email_seller_bottom" rel="'.$item->item_id.'"><img src="'.url::base().'img/stamp--pencil.png" alt="stamp" /><span>E-mail Seller</span></a>' : '<a href="#" class="button disabled"><span>No E-mails Please</span></a>' */ ?>
		
    <?php /* echo ($is_saved) ? '<a href="#" class="button right" id="save_ad" rel="'.$item->item_id.'"><span>Remove from Saved</span></a>' : '<a href="#" class="button right" id="save_ad" rel="'.$item->item_id.'"><span>Save Ad</span></a>' */ ?>
    <?php if(!$item->sold && $item->active): ?>
      <!-- AddThis Button BEGIN -->
      <div class="addthis_toolbox addthis_default_style ">
      <a class="addthis_button_facebook"></a>
      <a class="addthis_button_twitter"></a>
      </div>
      <script type="text/javascript">
      var addthis_share = 
      {
          templates: {
                         twitter: '{{url}}'
                     }
      }
      </script>
      <script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#pubid=ra-4eadbf7f5914de64"></script>
      <!-- AddThis Button END -->
      <?php echo ($is_saved) ? '<a href="#" class="save_ad stars active" id="save_ad" rel="'.$item->item_id.'">Liked</a>' : '<a href="#" class="save_ad stars" id="save_ad" rel="'.$item->item_id.'"></a>' ?>
    <?php endif; ?>
		<!-- <a href="#" class="tip" id="report_ad">Report Ad</a> -->
		<span class="date"><?php echo date("jS F Y", $item->publish_timestamp) ?></span>
		<?php elseif ($item->sold): ?>
		<span class="date sold">Sold: <?php echo date("jS F Y", $item->sold_timestamp) ?></span>
		<?php elseif (!$item->sold && !$item->active): ?>
		<span class="date sold">Expired: <?php echo date("jS F Y", $item->expire_timestamp) ?></span>
		<?php endif; ?>
	</div>
