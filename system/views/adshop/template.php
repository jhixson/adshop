<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" 
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="description" content="Ireland's simplest ad website. Just &euro;3. Pay by Mobile or Paypal." />
    <meta name="keywords" content="adshop, ireland, classified, ads" />
		<title>AdShop.ie - <?php echo html::specialchars($title) ?></title>
		<link rel="stylesheet" href="<?php echo url::base() ?>css/style.css" type="text/css" />
		<link rel="stylesheet" href="<?php echo url::base() ?>css/menu.css" type="text/css" />
		<link rel="stylesheet" href="<?php echo url::base() ?>css/combo.css" type="text/css" />
		<link rel="stylesheet" href="<?php echo url::base() ?>css/tipTip.css" type="text/css" />
		<link rel="stylesheet" href="<?php echo url::base() ?>css/jqModal.css" type="text/css" />
		<!--[if lt IE 7]>
			<link rel="stylesheet" href="<?php echo url::base() ?>css/ie.css" type="text/css" />    
		<![endif]-->
		<link rel="icon" type="image/png" href="<?php echo url::base() ?>img/favicon.png" />
		<link rel="shortcut icon" href="<?php echo url::base() ?>img/favicon.ico" />
	</head>
	<body>
	<div id="header">		
		<div class="content">
		<h1><a<?php echo isset($no_rollover) || mobile::isMobile() ? ' class="no_rollover"' : '' ?> href="<?php echo url::base() ?>">AdShop.ie - Ireland's Classifieds<span>home</span></a></h1>
		
		<?php if($display_search): ?>
		<input type="text" name="q" id="q" class="<?php echo isset($search_term) ? 'active' : '' ?>" value="<?php echo isset($search_term) ? $search_term : 'Search' ?>" />
		<?php endif; ?>
		
		<div id="place_ad">
		<?php /*
			<p class="left">
				<?php if (Auth::instance()->logged_in()): ?><a href="<?php echo url::base() ?>user/logout" class="logout">logout</a><?php endif; ?>
			</p>
		*/ ?>
			<?php if(preg_match('/place$/',url::current())): ?>
			<a href="<?php echo url::base() ?>place" class="button_white_border" id="start_over_button"><span>Start Over</span></a>
			<?php elseif(!preg_match('/(place\/edit\/\d+|renew\/\d+|remove\/\d+)/',url::current())): ?>
			<a href="<?php echo url::base() ?>place" id="place_ad_button"></a>
			<?php endif; ?>
		</div>
		</div>
	</div>

	<div id="container">
		<?php if($display_favs): 
		echo "<!-- j/k -->\n";
		endif;
		?>
		
		<?php 
		/*
		<div id="favs">
			<?php if($favs): ?>
			<a href="#" id="clear_favs"></a>
			<ul>
				<?php foreach($favs as $f): ?>
				<li><a href="<?php echo url::base().'view/'.$f->cat.'/'.$f->subcat ?>" rel="<?php echo $f->subcat ?>"><img src="<?php echo url::base() ?>img/<?php echo $f->cat.'/'.$f->image ?>" /></a></li>
				<?php endforeach; ?>
			</ul>
			<?php else: ?>
			<h2>Your Choices Will Appear Here</h2>
			<?php endif; ?>
		</div>
		<?php endif; ?>
		*/
		?>
		
		<span class="clear"></span>
		
		<?php if($menu): ?>
		<div id="menu">
			<ul>
				<li class="caption"><a href="#">Menu</a></li>
				<?php foreach($menu as $k => $v): ?>
					<li><a href="<?php echo url::base() ?>view/<?php echo $k ?>" class="<?php echo $k.$v['active'] ?>"><span><?php echo $v['title'] ?></span></a></li>
				<?php endforeach; ?>
			</ul>
			<br />
			<?php if(!preg_match('/view\/liked/',url::current())): ?>
        <a href="<?php echo url::base() ?>view/liked" class="button" id="saved_ads"><span>Liked Ads</span></a>
      <?php endif; ?>
		</div>
		<?php endif; ?>
	
		<?php echo $content ?>
		
		<span class="clear"></span>
		<div id="footer"<?php echo ($counties) ? ' class="full"' : '' ?>>
      <p><span><?php echo Router::$controller == 'home' && $this->uri->segment('page',0) == 0 ? '&copy; AdShop.ie' : '' ?></span></p>
			<?php if (preg_match('/place|renew\/\d+|edit\/\d+/',url::current())): ?>
			<?php echo !preg_match('/remove/',url::current()) ? '<p><a href="#" id="tip2" class="tip">Terms and Conditions</a></p>' : '' ?>
			<?php else: ?>
			<?php 
			/*
			if($counties): ?>
			<div class="select_container">
				<div class="select" id="footer_county_options"><span class="subname">All Ireland</span><br class="clear"/></div>
				<div class="options_details">
					<div class="item"><h5>All Ireland</h5><br class="clear" /></div>
					<?php foreach($counties as $c): ?>
					<div class="item"><h5><?php echo $c ?></h5><br class="clear" /></div>
					<?php endforeach; ?>
				</div>
			</div>
			<?php endif; 
			*/
			?>
      <p>
        <?php /*
        <?php if(Router::$controller == 'home' && url::current() != 'sold') : ?>
        <a href="<?php echo url::base().'sold' ?>">Sold Ads</a>
				<?php if (!preg_match('/sold|place|renew\/\d+|edit\/\d+/',url::current())): ?>
				<a href="<?php echo url::base() ?>">Home</a>
				<?php elseif (url::current() == 'sold' || isset($item_sold)): ?>
				<a href="<?php echo url::base() ?>">Home</a>
        <?php endif; ?>
        */ ?>
        <a href="#" id="tip1" class="tip">Our Goal</a>
        <!--
        <a href="#" id="tip2" class="tip">Legal</a>
        -->
				<?php echo (Auth::instance()->logged_in()) ? '<a href="'.url::base().'user/myAds">View My Ads</a>' : '<a href="#" id="tip3" class="tip">View My Ads</a>' ?>
				<a href="#" id="tip4" class="tip">Contact Us</a>
        <?php echo (Router::$controller == 'home' && $this->uri->segment('page',0) == 0) || Router::$controller == 'search' ? '' : '<a href="'.url::base().'" class="home-icon"><img src="/img/home-icon.png" alt="Home" /></a>' ?>
        <?php echo Router::$controller == 'search' ? '<a href="'.url::base().'" class="home-icon"><img src="/img/home-icon.png" alt="Home" /></a>' : '' ?>
			</p>
			<?php endif; ?>
		</div>
		<div id="demotip"></div>
	</div>
	
	<div id="dialog" class="jqmWindow"></div>
	
	<?php 
  echo "\n<!--\n";
	print_r($_SESSION);
	print_r($_COOKIE);
	echo "-->\n";
	?>
	
	<script type="text/javascript" src="<?php echo url::base() ?>js/jquery-1.4.3.min.js"></script>
	<script type="text/javascript" src="<?php echo url::base() ?>js/jquery-ui-1.8.6.custom.min.js"></script>
	<script type="text/javascript" src="<?php echo url::base() ?>js/jquery.uploadify.v2.1.0.js"></script>
	<script type="text/javascript" src="<?php echo url::base() ?>js/swfobject.js"></script>
	<script type="text/javascript" src="<?php echo url::base() ?>js/jquery.tipTip.min.js"></script>
	<script type="text/javascript" src="<?php echo url::base() ?>js/jquery.validate.min.js"></script>
	<script type="text/javascript" src="<?php echo url::base() ?>js/jquery.cookie.js"></script>
	<script type="text/javascript" src="<?php echo url::base() ?>js/jquery.special.load.js"></script>
	<script type="text/javascript" src="<?php echo url::base() ?>js/jquery.hashchange.min.js"></script>
	<script type="text/javascript" src="<?php echo url::base() ?>js/jquery.jqmodal.min.js"></script>
	<script type="text/javascript" src="<?php echo url::base() ?>js/jquery.ajaxmanager.min.js"></script>
	<script type="text/javascript" src="<?php echo url::base() ?>js/jquery.elastic.min.js"></script>
	<script type="text/javascript" src="<?php echo url::base() ?>js/json2.min.js"></script>
	<script type="text/javascript" src="<?php echo url::base() ?>js/functions.js"></script>
	<script type="text/javascript" src="<?php echo url::base() ?>js/tips.js"></script>
	</body>
</html>
	
