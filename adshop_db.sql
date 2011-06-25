-- phpMyAdmin SQL Dump
-- version 2.11.11.3
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 25, 2011 at 05:03 AM
-- Server version: 4.1.22
-- PHP Version: 5.2.9

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `adshop_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE IF NOT EXISTS `categories` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `slug` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=14 ;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `title`, `slug`) VALUES
(1, 'Motors', 'motors'),
(2, 'Pets', 'pets'),
(3, 'Farming', 'farming'),
(4, 'Household', 'household'),
(5, 'Electronics', 'electronics'),
(6, 'Music', 'music'),
(7, 'Equipment', 'equipment'),
(8, 'Gaming', 'gaming'),
(9, 'Sports', 'sports'),
(10, 'Tickets', 'tickets'),
(11, 'Services', 'services'),
(12, 'Personal', 'personal'),
(13, 'Rare', 'rare');

-- --------------------------------------------------------

--
-- Table structure for table `coupons`
--

CREATE TABLE IF NOT EXISTS `coupons` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `user_id` int(10) unsigned NOT NULL default '0',
  `item_id` int(10) unsigned NOT NULL default '0',
  `code` varchar(255) NOT NULL default '',
  `redeemed` tinyint(1) unsigned NOT NULL default '0',
  `timestamp` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=12 ;

--
-- Dumping data for table `coupons`
--


-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE IF NOT EXISTS `items` (
  `item_id` int(11) unsigned NOT NULL auto_increment,
  `user_id` int(11) unsigned NOT NULL default '0',
  `category_id` int(5) unsigned NOT NULL default '0',
  `subcategory_id` int(5) unsigned NOT NULL default '0',
  `subsubcategory_id` int(5) unsigned NOT NULL default '0',
  `active` tinyint(1) unsigned NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  `slug` varchar(255) NOT NULL default '',
  `description` text NOT NULL,
  `price` int(10) NOT NULL default '0',
  `owner_name` varchar(64) NOT NULL default '',
  `owner_phone_prefix` varchar(4) NOT NULL default '',
  `owner_phone` varchar(32) NOT NULL default '',
  `location` varchar(255) NOT NULL default '',
  `trade_ad` tinyint(1) unsigned NOT NULL default '0',
  `trade_company` varchar(255) NOT NULL default '',
  `trade_address` varchar(255) NOT NULL default '',
  `hide_email` tinyint(1) unsigned NOT NULL default '0',
  `sold` tinyint(1) unsigned NOT NULL default '0',
  `term` tinyint(1) unsigned NOT NULL default '0',
  `views` int(10) unsigned NOT NULL default '0',
  `publish_timestamp` int(10) unsigned NOT NULL default '0',
  `expire_timestamp` int(10) unsigned NOT NULL default '0',
  `sold_timestamp` int(10) unsigned NOT NULL default '0',
  `extra_attributes` text NOT NULL,
  PRIMARY KEY  (`item_id`),
  FULLTEXT KEY `title` (`title`,`description`),
  FULLTEXT KEY `title_2` (`title`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=165 ;

--
-- Dumping data for table `items`
--

-- --------------------------------------------------------

--
-- Table structure for table `media`
--

CREATE TABLE IF NOT EXISTS `media` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `item_id` int(11) unsigned NOT NULL default '0',
  `media` text NOT NULL,
  `timestamp` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=116 ;

--
-- Dumping data for table `media`
--

--
-- Table structure for table `media_temp`
--

CREATE TABLE IF NOT EXISTS `media_temp` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `user_id` varchar(255) NOT NULL default '',
  `filename` varchar(255) NOT NULL default '',
  `width` int(5) unsigned NOT NULL default '0',
  `height` int(10) unsigned NOT NULL default '0',
  `timestamp` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3435 ;

--
-- Dumping data for table `media_temp`
--

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE IF NOT EXISTS `roles` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `name` varchar(32) NOT NULL default '',
  `description` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `uniq_name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `description`) VALUES
(1, 'login', 'Login privileges, granted after account confirmation'),
(2, 'admin', 'Administrative user, has access to everything.');

-- --------------------------------------------------------

--
-- Table structure for table `roles_users`
--

CREATE TABLE IF NOT EXISTS `roles_users` (
  `user_id` int(10) unsigned NOT NULL default '0',
  `role_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`user_id`,`role_id`),
  KEY `fk_role_id` (`role_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `roles_users`
--

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE IF NOT EXISTS `sessions` (
  `session_id` varchar(127) NOT NULL default '',
  `last_activity` int(10) unsigned NOT NULL default '0',
  `data` text NOT NULL,
  PRIMARY KEY  (`session_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `sessions`
--
-- --------------------------------------------------------

--
-- Table structure for table `subcategories`
--

CREATE TABLE IF NOT EXISTS `subcategories` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `category_id` int(5) unsigned NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  `slug` varchar(255) NOT NULL default '',
  `image` varchar(255) NOT NULL default '',
  `order_Id` tinyint(2) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=112 ;

--
-- Dumping data for table `subcategories`
--

INSERT INTO `subcategories` (`id`, `category_id`, `title`, `slug`, `image`, `order_Id`) VALUES
(1, 1, 'Cars', 'cars', 'car.png', 0),
(2, 1, 'Motorbikes', 'motorbikes', 'motorbike.png', 0),
(3, 1, 'Jeeps & Carriers', 'jeeps-carriers', 'jeep.png', 0),
(4, 1, 'Vans', 'vans', 'van.png', 0),
(5, 1, 'Lorries', 'lorries', 'truck.png', 0),
(7, 1, 'Caravans', 'caravans', 'caravan.png', 0),
(6, 1, 'Boats', 'boats', 'boat.png', 0),
(8, 1, 'Quads', 'quads', 'quad.png', 0),
(9, 1, 'Radio Control', 'radio-control', 'rc_car2.jpg', 0),
(10, 1, 'Car Accessories', 'car-accessories', 'tire.png', 0),
(11, 1, 'Motorbike Accessories', 'motorbike-accessories', 'helmet.png', 0),
(12, 2, 'Dogs', 'dogs', 'dogs.png', 0),
(13, 2, 'Cats', 'cats', 'cats.png', 0),
(14, 2, 'Horses', 'horses', 'horses.png', 0),
(15, 2, 'Small Animals', 'small-animals', 'small-animals.png', 0),
(16, 2, 'Birds', 'birds', 'birds.png', 0),
(17, 2, 'Exotic Animals', 'exotic-animals', 'exotic-animals.png', 0),
(18, 2, 'Fish Tanks', 'fish-tanks', 'fish-tanks.png', 0),
(19, 3, 'Tractors', 'tractors', 'tractor.png', 0),
(20, 3, 'Trailers', 'trailers', 'trailer.png', 0),
(21, 3, 'Farm Machinery', 'farm-machinery', 'machinery.png', 0),
(22, 3, 'Livestock', 'livestock', 'livestock.png', 0),
(23, 3, 'Poultry', 'poultry', 'poultry.png', 0),
(24, 4, 'Sofas', 'sofas', 'sofa.png', 0),
(25, 4, 'Tables', 'tables', 'table.png', 0),
(26, 4, 'Chairs', 'chairs', 'chair.png', 0),
(27, 4, 'Bedroom Furnishings', 'bedroom-furnishings', 'bed.png', 0),
(28, 4, 'All Appliances', 'all-appliances', 'range.png', 0),
(29, 4, 'Garden Furniture', 'garden-furniture', 'bench.png', 0),
(30, 4, 'Occasional', 'occasional', 'occasional.jpg', 0),
(31, 4, 'Ornamental', 'ornamental', 'curtains.png', 0),
(32, 5, 'Phones', 'phones', 'iphone.png', 1),
(33, 5, 'Computers', 'computers', 'macbook.png', 2),
(35, 5, 'TVs', 'tvs', 'tv.png', 5),
(36, 5, 'Sound', 'sound', 'stereo.png', 6),
(37, 5, 'Cameras', 'cameras', 'camera.png', 7),
(38, 5, 'Car Audio', 'car-audio', 'speakers.png', 8),
(39, 5, 'Peripherals', 'peripherals', 'printer.png', 9),
(40, 5, 'CDs & DVDs', 'cds-dvds', 'dvd.png', 10),
(41, 6, 'Stringed Instruments', 'stringed-instruments', 'guitar.png', 0),
(42, 6, 'Pianos', 'pianos', 'piano.png', 0),
(43, 6, 'Keyboards', 'keyboards', 'keyboard.png', 0),
(44, 6, 'Drums', 'drums', 'cymbal.png', 0),
(45, 6, 'Speakers', 'speakers', 'speaker.png', 0),
(46, 6, 'Brass &amp; Woodwind', 'brass-woodwind-instruments', 'sax.png', 0),
(47, 6, 'Amps', 'amps', 'amp.png', 0),
(48, 6, 'Microphones &amp; Leads', 'microphones-leads', 'mic.png', 0),
(49, 6, 'Music Lessons', 'music-lessons', 'trebleclef.png', 0),
(50, 7, 'All Lawnmowers', 'all-lawnmowers', 'tractor.png', 0),
(51, 7, 'Garden Tools', 'garden-tools', 'trimmer.jpg', 0),
(52, 7, 'Power Tools', 'power-tools', 'drill.png', 0),
(53, 7, 'Hand Tools', 'hand-tools', 'hammer.png', 0),
(54, 7, 'Construction', 'construction', 'bulldozer.png', 0),
(55, 7, 'Fabrication', 'fabrication', 'generator.png', 0),
(56, 8, 'Newer Consoles', 'newer-consoles', 'ps3.png', 0),
(57, 8, 'PS3 Games', 'ps3-games', 'fifa-ps3.png', 0),
(58, 8, 'XBox Games', 'xbox-games', 'fifa-xbox360.png', 0),
(59, 8, 'Wii Games', 'wii-games', 'fifa-wii.png', 0),
(60, 8, 'Newer Peripherals', 'newer-peripherals', 'controller-ps3.png', 0),
(61, 8, 'Retro Consoles', 'retro-consoles', 'n64.png', 0),
(62, 8, 'Retro Games', 'retro-games', 'cartridge.png', 0),
(63, 8, 'Retro Peripherals', 'Retro-peripherals', 'controller-n64.png', 0),
(64, 8, 'PC Rigs & Extras', 'pc-rigs-extras', 'pc.png', 0),
(65, 8, 'PC Games', 'pc-games', 'sims-pc.png', 0),
(66, 9, 'Bicycles', 'bicycles', 'bike.png', 1),
(67, 9, 'Golf', 'golf-equipment', 'golfball.png', 3),
(68, 9, 'Gym', 'gym-equipment', 'barbell.jpg', 4),
(69, 9, 'Extreme Sports', 'extreme-sports', 'wheel.jpg', 5),
(70, 9, 'Sports Lessons', 'sports-lessons', 'golf.jpg', 6),
(71, 10, 'Concert Tickets', 'concert-tickets', 'concert.png', 0),
(72, 10, 'Sports Tickets', 'sports-tickets', 'sport.png', 0),
(73, 11, 'Plumber', 'plumber', 'faucet.png', 0),
(74, 11, 'Electrician', 'electrician', 'bulb.png', 0),
(75, 11, 'Builder', 'builder', 'brick.png', 0),
(76, 11, 'Plasterer', 'plasterer', 'trowel.png', 0),
(87, 11, 'Painter', 'painter', 'brush.png', 0),
(89, 11, 'Photographer', 'photographer', 'camera.png', 0),
(85, 11, 'Carpenter', 'carpenter', 'sawblade.jpg', 0),
(88, 11, 'Florist', 'florist', 'flowers.png', 0),
(90, 11, 'Landscaper', 'landscaper', 'landscape.png', 0),
(83, 12, 'Everything for Babies', 'everything-for-babies', 'stroller.png', 0),
(84, 12, 'Women''s Dress', 'womens-dress', 'dress.png', 0),
(77, 12, 'Gents & Ladies Bling', 'gents-ladies-bling', 'rings.png', 0),
(79, 13, 'Antiques', 'antiques', 'monalisa.png', 0),
(78, 13, 'Collectibles', 'collectibles', 'stamp.png', 0),
(80, 13, 'Spares & Extras', 'spares-extras', 'boot.png', 0),
(86, 11, 'Tiler', 'tiler', 'tile.jpg', 0),
(91, 3, 'Horses', 'horses', 'horses.png', 0),
(92, 6, 'CDs & DVDs', 'cds-dvds', 'dvd.png', 0),
(93, 6, 'Unusual', 'unusual', 'xylophone.jpg', 0),
(94, 9, 'Pool &amp; Snooker', 'pool-snooker', 'pool_table.png', 7),
(95, 9, 'Water Sports', 'water-sports', 'canoe.jpg', 8),
(97, 9, 'Sports Tickets', 'sports-tickets', 'sport.png', 10),
(98, 8, 'Portables', 'portables', 'ds_portable.jpg', 0),
(99, 8, 'Portable Games', 'portable-games', 'ds_game.jpg', 0),
(100, 4, 'Cookware', 'cookware', 'pot.jpg', 0),
(96, 9, 'Sports Gear', 'sports-gear', 'jersey.jpg', 9),
(102, 1, 'All Lawnmowers', 'all-lawnmowers', 'tractor.png', 0),
(103, 9, 'Rackets &amp; Extras', 'rackets-extras', 'racket.jpg', 11),
(104, 11, 'Sports Lessons', 'sports-lessons', 'golf.jpg', 0),
(105, 11, 'Music Lessons', 'music-lessons', 'trebleclef.png', 0),
(108, 5, 'Tablets', 'tablets', 'ipad.png', 3),
(34, 5, 'iPods', 'ipods', 'ipod.png', 4),
(109, 7, 'Catering', 'catering', 'chef.png', 0),
(110, 7, 'Shop Fittings', 'shop-fittings', 'shelves.jpg', 0),
(111, 9, 'Bicycle Accessories', 'bicycle-accessories', 'pedal.jpg', 2);

-- --------------------------------------------------------

--
-- Table structure for table `subsubcategories`
--

CREATE TABLE IF NOT EXISTS `subsubcategories` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `subcategory_id` int(5) unsigned NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  `slug` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=252 ;

--
-- Dumping data for table `subsubcategories`
--

INSERT INTO `subsubcategories` (`id`, `subcategory_id`, `title`, `slug`) VALUES
(1, 1, 'Alfa Romeo', 'alfa-romeo'),
(2, 1, 'Audi', 'audi'),
(3, 1, 'BMW', 'bmw'),
(4, 1, 'Chevrolet', 'chevrolet'),
(5, 1, 'Chrysler', 'chrysler'),
(6, 1, 'Citroen', 'citroen'),
(7, 1, 'Daewoo', 'daewoo'),
(8, 1, 'Daihatsu', 'daihatsu'),
(9, 1, 'Fiat', 'fiat'),
(10, 1, 'Ford', 'ford'),
(11, 1, 'Honda', 'honda'),
(12, 1, 'Hyundai', 'hyundai'),
(13, 1, 'Isuzu', 'isuzu'),
(14, 1, 'Jaguar', 'jaguar'),
(15, 1, 'Jeep', 'jeep'),
(16, 1, 'KIA', 'kia'),
(17, 1, 'Lancia', 'lancia'),
(18, 1, 'Land Rover', 'land-rover'),
(19, 1, 'Lexus', 'lexus'),
(20, 1, 'Mazda', 'mazda'),
(21, 1, 'Mercedes', 'mercedes'),
(22, 1, 'MG', 'mg'),
(23, 1, 'Mini', 'mini'),
(24, 1, 'Mitsubishi', 'mitsubishi'),
(25, 1, 'Morris', 'morris'),
(26, 1, 'Nissan', 'nissan'),
(27, 1, 'Opel', 'opel'),
(28, 1, 'Peugeot', 'peugeot'),
(29, 1, 'Porsche', 'porsche'),
(30, 1, 'Rally', 'rally'),
(31, 1, 'Renault', 'renault'),
(32, 1, 'Rover', 'rover'),
(33, 1, 'Saab', 'saab'),
(34, 1, 'Seat', 'seat'),
(35, 1, 'Skoda', 'skoda'),
(36, 1, 'Smart', 'smart'),
(37, 1, 'SsangYong', 'ssangyong'),
(38, 1, 'Subaru', 'subaru'),
(39, 1, 'Suzuki', 'suzuki'),
(40, 1, 'Toyota', 'toyota'),
(41, 1, 'Vauxhall', 'vauxhall'),
(42, 1, 'VW', 'vw'),
(43, 1, 'Volvo', 'volvo'),
(44, 2, 'Aprilla', 'aprilla'),
(45, 2, 'BMW', 'bmw'),
(46, 2, 'Ducati', 'ducati'),
(47, 2, 'Gilera', 'gilera'),
(48, 2, 'Harley-Davidson', 'harley-davidson'),
(49, 2, 'Honda', 'honda'),
(50, 2, 'Kawasaki', 'kawasaki'),
(51, 2, 'Keeway', 'keeway'),
(52, 2, 'KTM', 'ktm'),
(53, 2, 'Lambretta', 'lambretta'),
(54, 2, 'Rexton', 'rexton'),
(55, 2, 'Suzuki', 'suzuki'),
(56, 2, 'Triumph', 'triumph'),
(57, 2, 'Yamaha', 'yamaha'),
(229, 12, 'Pointer', 'pointer'),
(228, 12, 'Pekingese', 'pekingese'),
(227, 12, 'Papillon', 'papillon'),
(226, 12, 'Old English Sheepdog', 'old-english-sheepdog'),
(225, 12, 'Norwegian Elkhound', 'norwegian-elkhound'),
(223, 12, 'Newfoundland', 'newfoundland'),
(224, 12, 'Norfolk Terrier', 'norfolk-terrier'),
(222, 12, 'Maltese', 'maltese'),
(221, 12, 'Lhasa Apso', 'lhasa-apso'),
(220, 12, 'Labrador Retriever', 'labrador-retriever'),
(219, 12, 'Kerry Blue Terrier', 'kerry-blue-terrier'),
(218, 12, 'Japanese Spitz', 'japanese-spitz'),
(217, 12, 'Jack Russell Terrier', 'jack-russell-terrier'),
(216, 12, 'Irish Wolfhound', 'irish-wolfhound'),
(214, 12, 'Irish Terrier', 'irish-terrier'),
(215, 12, 'Irish Water Spaniel', 'irish-water-spaniel'),
(213, 12, 'Greyhound', 'greyhound'),
(212, 12, 'Great Dane', 'great-dane'),
(211, 12, 'Golden Retriever', 'golden-retriever'),
(210, 12, 'German Shepherd Dog', 'german-shepherd-dog'),
(209, 12, 'German Pointer', 'german-pointer'),
(208, 12, 'Dogue de Bordeaux', 'dogue-de-bordeaux'),
(207, 12, 'Doberman Pinscher', 'doberman-pinscher'),
(206, 12, 'Dalmatian', 'dalmatian'),
(205, 12, 'Dachshund', 'dachshund'),
(204, 12, 'Collie', 'collie'),
(203, 12, 'Cocker Spaniel', 'cocker-spaniel'),
(202, 12, 'Chow Chow', 'chow-chow'),
(111, 2, 'Vespa', 'vespa'),
(201, 12, 'Chinese Shar-Pei', 'chinese-shar-pei'),
(200, 12, 'Chihuahua', 'chihuahua'),
(199, 12, 'Cavalier King Charles Spaniel', 'cavalier-king-charles-spaniel'),
(198, 12, 'Cairn Terrier', 'cairn-terrier'),
(197, 12, 'Bullmastiff', 'bullmastiff'),
(196, 12, 'Bulldog', 'bulldog'),
(195, 12, 'Bull Terrier', 'bull-terrier'),
(194, 12, 'Boxer', 'boxer'),
(193, 12, 'Boston Terrier', 'boston-terrier'),
(192, 12, 'Border Terrier', 'border-terrier'),
(191, 12, 'Bloodhound', 'bloodhound'),
(190, 12, 'Bichon Frise', 'bichon-frise'),
(189, 12, 'Bernese Mountain Dog', 'bernese-mountain-dog'),
(188, 12, 'Beagle', 'beagle'),
(187, 12, 'Basset Hound', 'basset-hound'),
(186, 12, 'Basenji', 'basenji'),
(185, 12, 'Alaskan Malamute', 'alaskan-malamute'),
(184, 12, 'Akita', 'akita'),
(183, 12, 'Airedale Terrier', 'airedale-terrier'),
(182, 12, 'Afghan Hound', 'afghan-hound'),
(230, 12, 'Pomeranian', 'pomeranian'),
(231, 12, 'Poodle', 'poodle'),
(232, 12, 'Pug', 'pug'),
(233, 12, 'Puli', 'puli'),
(234, 12, 'Rhodesian Ridgeback', 'rhodesian-ridgeback'),
(235, 12, 'Rottweiler', 'rottweiler'),
(236, 12, 'Samoyed', 'samoyed'),
(237, 12, 'Schnauzer', 'schnauzer'),
(238, 12, 'Scottish Terrier', 'scottish-terrier'),
(239, 12, 'Setter', 'setter'),
(240, 12, 'Sheepdog', 'sheepdog'),
(241, 12, 'Shih Tzu', 'shih-tzu'),
(242, 12, 'Siberian Husky', 'siberian-husky'),
(243, 12, 'Springer Spaniel', 'springer-spaniel'),
(244, 12, 'St. Bernard', 'st-bernard'),
(245, 12, 'Staffordshire Terrier', 'staffordshire-terrier'),
(246, 12, 'Tibetan Spaniel', 'tibetan-spaniel'),
(247, 12, 'Weimaraner', 'weimaraner'),
(248, 12, 'Welsh Corgi', 'welsh-corgi'),
(249, 12, 'West Highland White Terrier', 'west-highland-white-terrier'),
(250, 12, 'Whippet', 'whippet'),
(251, 12, 'Yorkshire Terrier', 'yorkshire-terrier');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE IF NOT EXISTS `transactions` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `item` varchar(255) NOT NULL default '',
  `amount` decimal(5,2) NOT NULL default '0.00',
  `name` varchar(255) NOT NULL default '',
  `email` varchar(255) NOT NULL default '',
  `status` varchar(32) NOT NULL default '',
  `signature` varchar(255) NOT NULL default '',
  `timestamp` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=14 ;

--
-- Dumping data for table `transactions`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `name` varchar(64) NOT NULL default '',
  `location` varchar(32) NOT NULL default '',
  `phone` varchar(32) NOT NULL default '',
  `username` varchar(127) NOT NULL default '',
  `password` varchar(50) NOT NULL default '',
  `logins` int(10) unsigned NOT NULL default '0',
  `last_login` int(10) unsigned default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `uniq_email` (`username`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=32 ;

--
-- Dumping data for table `users`
--

-- --------------------------------------------------------

--
-- Table structure for table `user_tokens`
--

CREATE TABLE IF NOT EXISTS `user_tokens` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `user_id` int(11) unsigned NOT NULL default '0',
  `user_agent` varchar(40) NOT NULL default '',
  `token` varchar(32) NOT NULL default '',
  `created` int(10) unsigned NOT NULL default '0',
  `expires` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `uniq_token` (`token`),
  KEY `fk_user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `user_tokens`
--

