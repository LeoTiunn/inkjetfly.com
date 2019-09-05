<?php
/* This is the installer for Contribution Holiday settings.
Its purpose it to enable the user to ealisy and quickly enable the Holiday / Away Message.
 Written by Fimble (osc forums) http://www.linuxuk.co.uk
*/
?>
<?php
  require('includes/application_top.php');
  $confId_query = tep_db_query("select max(configuration_id) as count from " . TABLE_CONFIGURATION);
  $confId = tep_db_fetch_array($confId_query);
  $confId = $confId['count'];
  $sortOrder_query = tep_db_query("select max(sort_order) as count from " . TABLE_CONFIGURATION . " where configuration_group_id = '1'");
  $sortOrder = tep_db_fetch_array($sortOrder_query);
  $sortOrder = $sortOrder['count'];
 // $insertQuery = "INSERT INTO configuration (configuration_id, configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function) VALUES ('', 'Enable Holiday settings?', 'HOLIDAY_SETTINGS', 'true', 'Display Holiday settings?', 1, 900, now(), now(), NULL, 'tep_cfg_select_option(array(\'true\', \'false\'),')";
 $insertQuery = "delete from `configuration` where configuration_key='TABLE_MESSAGE' or configuration_key='TABLE_MESSAGE_CONTACT' ";  tep_db_query($insertQuery);
 $insertQuery = "Alter table `configuration` CHANGE  `configuration_value`  `configuration_value` TEXT";  tep_db_query($insertQuery);
 $insertQuery = "INSERT INTO `configuration`  VALUES (1009, 'Holiday Message', 'TABLE_MESSAGE', 'Our Inkjetfly Online Store and offices will close at 1:00PM EDT on Monday, Dec 20, to perform a yearend inventory count and will reopen on Tuesday, Jan 04 2011.
All orders placed before Sunday, Dec 19, at 9:00PM EDT will be processed normally. Orders placed after the above times will be processed on Wednesday, Jan 04 2011.
We apologize for the inconvenience and appreciate your patronage.', 'Place any message you want to here, you can use it for any type of notice not just holidays<br>', 1, 901, '2009-09-03 15:05:11', '2009-09-03 15:04:46', NULL, 'tep_cfg_textarea(');";
 tep_db_query($insertQuery);
 $insertQuery = "INSERT INTO `configuration`  VALUES (1010, 'Holiday Message(Contact us)', 'TABLE_MESSAGE_CONTACT', 'We are away at present from 12/19/2010 to 01/04/2011. We will reply to your enquiry as soon as possible.', 'Contact us page message<br>', 1, 902, '2009-09-03 15:05:11', '2009-09-03 15:04:46', NULL, 'tep_cfg_textarea(');";
  tep_db_query($insertQuery);
  echo "Holiday Message is installed in your database";  
  require('includes/application_bottom.php');
?>
<table>
<tr>
<td>Please remember to delete this file</td>
</tr>
</table>

