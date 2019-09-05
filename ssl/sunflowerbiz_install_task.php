<?php
/*
  $Id: checkout_success.php,v 1.1 2003/05/16 00:17:34 ft01189 Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');
  



/*

$test2 = tep_db_query('Describe orders  delivery_telephone');
$test = tep_db_fetch_array($test2);
if($test['Field']==''){
tep_db_query("ALTER TABLE  `orders` ADD  `delivery_telephone` varchar(32)  NULL DEFAULT '' ;");
tep_db_query("ALTER TABLE  `orders` ADD  `delivery_fax` varchar(32)  NULL DEFAULT '' ;");
tep_db_query("ALTER TABLE  `orders` ADD  `delivery_email_address` varchar(96)  NULL DEFAULT '' ;");
}


$test2 = tep_db_query('Describe orders  billing_telephone');
$test = tep_db_fetch_array($test2);
if($test['Field']==''){
tep_db_query("ALTER TABLE  `orders` ADD  `billing_telephone` varchar(32)  NULL DEFAULT '' ;");
tep_db_query("ALTER TABLE  `orders` ADD  `billing_email_address` varchar(96)  NULL DEFAULT '' ;");
tep_db_query("ALTER TABLE  `orders` ADD  `billing_fax` varchar(32)  NULL DEFAULT '' ;");
}

$test2 = tep_db_query('Describe orders  ipaddy');
$test = tep_db_fetch_array($test2);
if($test['Field']==''){
tep_db_query("ALTER TABLE  `orders` ADD  `ipaddy` varchar(15)  NULL DEFAULT '' ;");
tep_db_query("ALTER TABLE  `orders` ADD  `ipisp` varchar(15)  NULL DEFAULT '' ;");
}

$test2 = tep_db_query('Describe orders  export');
$test = tep_db_fetch_array($test2);
if($test['Field']==''){
//tep_db_query("ALTER TABLE  `orders` ADD  `export` INT(1)   ;");
//tep_db_query("ALTER TABLE  `orders` ADD  `emailed` INT(1)   ;");
}


tep_db_query("delete FROM `infobox_configuration` WHERE `template_id`=1 ");//and `infobox_file_name` not in ('categories_dhtml.php','shopping_cart.php','boxad.php','search1.php','information_table.php')
tep_db_query("INSERT INTO `infobox_configuration` (`template_id`, `infobox_id`, `infobox_file_name`, `infobox_define`, `infobox_display`, `display_in_column`, `location`, `last_modified`, `date_added`, `box_heading`, `box_template`, `box_heading_font_color`) VALUES 
(1, 153, 'categories_dhtml.php', 'BOX_HEADING_CATEGORIES', 'yes', 'left', 2, '2007-10-17 21:44:30', '0000-00-00 00:00:00', 'Products', 'infobox', '#989899')
,(1, 38, 'boxad.php', 'BOX_AD_BANNER_HEADING', 'yes', 'left', 20, '2010-04-29 15:35:02', '2006-05-25 17:53:20', 'InkjetFly CIS Forums', 'infobox', '#FF9900'),(1, 25, 'shopping_cart.php', 'BOX_HEADING_SHOPPING_CART', 'yes', 'left', 10, '2008-08-19 13:38:25', '2006-05-25 17:53:20', 'Shopping Cart', 'infobox', '#989899'),
(1, 32, 'information_table.php', 'BOX_HEADING_INFORMATION_TABLE', 'yes', 'left', 50, '2008-08-19 13:39:21', '2006-05-25 17:53:20', 'Information', 'infobox', '#989899'),
(1, 36, 'search1.php', 'BOX_HEADING_SEARCH1', 'yes', 'left', 30, '2008-08-19 13:38:45', '2006-05-25 17:53:20', 'Search', 'infobox', '#989899');

");

*/


 ?>
