<?php
/*
  $Id: checkout_success.php,v 1.1 2003/05/16 00:17:34 ft01189 Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');
  
    $listing_sql = tep_db_query("Select * from ".TABLE_PRODUCTS_DESCRIPTION." where products_description like '%p7.secure.hostingprod.com/@www.youtube.com%'");
	while($product_check = tep_db_fetch_array($listing_sql)){
	echo $product_check['products_id'].'<br />';
	if($_GET['update']==1)
	tep_db_query("update ".TABLE_PRODUCTS_DESCRIPTION." set products_description =REPLACE(products_description,'p7.secure.hostingprod.com/@www.youtube.com','www.youtube.com') where products_id=".$product_check['products_id']."");
	}
  
/*  echo nl2br(tep_address_label(1, 0, 0, '', "\n") ). "\n";

   if(!defined('USPS_RETURN_NAME')){
   tep_db_query("INSERT INTO `configuration` (`configuration_id`, `configuration_title`, `configuration_key`, `configuration_value`, `configuration_description`, `configuration_group_id`, `sort_order`, `last_modified`, `date_added`, `use_function`, `set_function`) VALUES 
('', 'USPS Return Name', 'USPS_RETURN_NAME', 'Leo Chang', 'Enter the name you want on shipping labels etc.', 7, 10, '2009-02-26 14:17:50', '2007-06-30 08:15:58', NULL, NULL),
('', 'USPS Return Company', 'USPS_RETURN_COMPANY', 'inkjetfly.com', 'Enter the Company you want on shipping labels etc.', 7, 11, '2007-07-02 21:47:35', '2007-06-30 08:15:58', NULL, NULL),
('', 'USPS Return Street', 'USPS_RETURN_STREET', '467 Saratoga Ave. #117', 'Enter the street address you want on shipping labels.', 7, 12, '2007-06-30 08:19:09', '2007-06-30 08:15:58', NULL, NULL),
('', 'USPS Return Street 2', 'USPS_RETURN_STREET2', '', 'Enter the 2nd line of the street address you want on shipping labels.', 7, 13, NULL, '2007-06-30 08:15:58', NULL, NULL),
('', 'USPS Return City', 'USPS_RETURN_CITY', 'San Jose', 'Enter the city you want on shipping labels etc.', 7, 14, '2007-06-30 08:19:19', '2007-06-30 08:15:58', NULL, NULL),
('', 'USPS Return State', 'USPS_RETURN_STATE', 'CA', 'Enter the two letter state code you want on shipping labels.', 7, 15, '2007-06-30 08:19:28', '2007-06-30 08:15:58', NULL, NULL),
('', 'USPS Return Zip', 'USPS_RETURN_ZIP', '95129', 'Enter the zip code you want on shipping labels.', 7, 16, '2007-06-30 08:19:40', '2007-06-30 08:15:58', NULL, NULL),
('', 'USPS Ship To Address', 'USPS_SHIP_ADDRESS', 'Shipping', 'Use the shipping or billing address to ship to.', 7, 17, '2007-06-30 08:19:49', '2007-06-30 08:15:58', NULL, 'tep_cfg_select_option(array(''Shipping'', ''Billing''),'),
('', 'USPS Weight Override', 'USPS_WEIGHT_OVERRIDE', '', 'To override automatic weights, enter the weight in pounds that you want all packages to be. Set to blank to calculate weight. Set to 0 and all weights will have to be entered.', 7, 18, NULL, '2007-06-30 08:15:58', NULL, NULL),
('', 'USPS Send Value Over', 'USPS_SEND_VALUE_OVER', '99999999999999', 'When to send contents value so that insurance can be purchased - Set to 0 to send values for all, or a very large number for never.', 7, 19, '2008-11-24 13:52:31', '2007-06-30 08:15:58', NULL, NULL),
('', 'USPS Shipping Cutoff Hour', 'USPS_CUTOFF_HOUR', '12', 'Cutoff time for shipments in 24 hour format. Shipments done after this time will be defaulted to the next date. Note: Uses your server time.', 7, 20, '2008-11-20 19:16:03', '2007-06-30 08:15:58', NULL, NULL),
('', 'USPS Ship From Zip', 'USPS_SHIP_FROM_ZIP', '95129', 'Enter the zip code you will be shipping from. Leave blank if it is the same as the return zip code.', 7, 21, '2007-06-30 08:20:24', '2007-06-30 08:15:58', NULL, NULL),
('', 'USPS Phone Number', 'USPS_PHONE', '4085124088', 'Enter your phone number that is required for international shipments, no dashes (XXXXXXXXXX).', 7, 22, '2007-06-30 08:20:43', '2007-06-30 08:15:58', NULL, NULL),
('', 'USPS Email', 'USPS_EMAIL', 'NO', 'Send customers email address?', 7, 23, '2009-11-03 12:24:19', '2007-06-30 08:15:58', NULL, 'tep_cfg_select_option(array(''YES'', ''NO''),');");

}

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

$test2 = tep_db_query('Describe orders  emailed');
$test = tep_db_fetch_array($test2);
if($test['Field']==''){
tep_db_query("ALTER TABLE  `orders` ADD  `export` INT(1)  NULL DEFAULT '0' ;");
tep_db_query("ALTER TABLE  `orders` ADD  `emailed` INT(1)  NULL DEFAULT '0' ;");
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
