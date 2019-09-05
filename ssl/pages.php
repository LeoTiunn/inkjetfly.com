<?php
/*
  $Id: pages.php,v 1.2 2004/03/12 19:28:57 ccwjr Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
  
  Chain Reaction Works, Inc
  
  Copyright &copy; 2006 Chain Reaction Works, Inc
  
  Last Modified By : $Author$
  Last Modified On : $Date$
  
  Latest Revision :  $Revision$
  
*/

require('includes/application_top.php');

$cID = 0;
$pID = 0;
$display_mode = '';

if (isset($_GET['cID']) && tep_not_null($_GET['cID'])) {
  $cID = (int)$_GET['cID'];
  $display_mode = 'categories';
}

if (isset($_GET['pID']) && tep_not_null($_GET['pID'])) {
  $pID = (int)$_GET['pID'];
  $display_mode = 'pages';
}

require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_PAGES);

// pages breadcrumb
$pages_categories_query = tep_db_query("select icd.categories_name from " . TABLE_PAGES_CATEGORIES_DESCRIPTION . " icd, " . TABLE_PAGES_CATEGORIES . " ic where ic.categories_id = icd.categories_id and icd.categories_id = '" . (int)$cID . "' and icd.language_id = '" . (int)$languages_id . "' and ic.categories_status = '1'");
$pages_categories_value = tep_db_fetch_array($pages_categories_query);

if (($display_mode == 'pages') || ($display_mode == 'categories')) {
  $breadcrumb->add(NAVBAR_TITLE, FILENAME_PAGES);

  if (tep_not_null($pages_categories_value['categories_name'])) {
    $breadcrumb->add($pages_categories_value['categories_name'], FILENAME_PAGES . '?cID=' . $cID);
  }
} else {
  $breadcrumb->add(NAVBAR_TITLE, FILENAME_PAGES);
}

$content = CONTENT_PAGES;

require(DIR_WS_TEMPLATES . TEMPLATE_NAME . '/' . TEMPLATENAME_MAIN_PAGE);

require(DIR_WS_INCLUDES . 'application_bottom.php');
?>
