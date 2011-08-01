<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<style type="text/css">
* {
	margin: 0;
	padding: 0;
	line-height: normal;
}

body {
	background: #124d9b url('/img/header_bg_dark.png') top repeat-x;
	font-family: 'Lucida Grande','Lucida Sans Unicode','Lucida Sans',Arial,sans-serif;
	font-size: 12px;
	color: #000;
	text-align: center;
}

a { 
	color: #fff;
}

.oops {
	width: 412px;
	height: 157px;
	line-height: 100px;
	margin: 50px auto;
}
</style>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<title>404 - Page Not Found</title>
</head>
<body>
	<div class="oops">
		<p><img src="/img/404oops.png" alt="oops!" /></p>
		<a href="<?php echo url::base() ?>">Home</a>
	</div>
<!--
<?php echo $error ?>
-->
</body>
</html>
