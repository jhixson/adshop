<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * @package  Core
 *
 */
$config['_default'] = 'home';

$config['sold(/.*)?'] = 'home/sold/$1';
$config['page/(.*)'] = 'home/index/$1';
$config['item/(.*)'] = 'item/index/$1';
$config['view/saved'] = 'view/saved';
$config['view/saved(/.*)?'] = 'view/saved/$1';
$config['view/(.*)/(\d+)/(.*)'] = 'item/index/$2/$3';
$config['view/(\d+)/(.*)'] = 'item/index/$1/$2';
$config['view/(.*)'] = 'view/index/$1';


//$config['place/(.*)'] = 'place/index/$1';
//$config['place/edit/(.*)'] = 'place/edit/$1';
$config['renew/(.*)'] = 'renew/index/$1';
$config['edit/(.*)'] = 'edit/index/$1';
//$config['request/(.*)'] = 'request/index/$1';
