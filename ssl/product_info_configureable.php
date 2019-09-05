<?php
/*
  $Id: product_info.php,v 1.1.1.1 2004/03/04 23:38:02 ccwjr Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_PRODUCT_INFO);

  $product_check_query = tep_db_query("select count(*) as total from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd where p.products_status = '1' and p.products_id = '" . (int)$_GET['products_id'] . "' and pd.products_id = p.products_id and pd.language_id = '" . (int)$languages_id . "'");
  $product_check = tep_db_fetch_array($product_check_query);

//check if product is really a subproduct
  $product_sub_product_query = tep_db_query("select products_parent_id from " . TABLE_PRODUCTS . " p where p.products_id = '" . (int)$_GET['products_id'] . "'");
  while ($product_sub_product = tep_db_fetch_array($product_sub_product_query)){
  $product_sub_check = $product_sub_product['products_parent_id'];
  }

  if ($product_sub_check > 0){
   tep_redirect(tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $product_sub_check));
  }
//check to see if products have sub_products
$product_has_sub = '0';
$sub_products_sql1 = tep_db_query("select p.products_id, p.products_price, p.products_image, pd.products_name from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd where p.products_parent_id = " . (int)$_GET['products_id'] . " and p.products_quantity > '0' and p.products_id = pd.products_id and pd.language_id = " . (int)$languages_id);
if (tep_db_num_rows($sub_products_sql1) > 0) {
$product_has_sub = '1';
}else{
$product_has_sub = '0';
}
// BOF MaxiDVD: Modified For Ultimate Images Pack!
    $product_info_query = tep_db_query("select p.products_id, pd.products_name, pd.products_description, p.products_model, p.products_quantity, p.products_image, p.products_image_med, p.products_image_lrg, p.products_image_sm_1, p.products_image_xl_1, p.products_image_sm_2, p.products_image_xl_2, p.products_image_sm_3, p.products_image_xl_3, p.products_image_sm_4, p.products_image_xl_4, p.products_image_sm_5, p.products_image_xl_5, p.products_image_sm_6, p.products_image_xl_6, pd.products_url, p.products_price, p.products_tax_class_id, p.products_date_added, p.products_date_available, p.manufacturers_id from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd where p.products_status = '1' and p.products_id = '" . (int)$_GET['products_id'] . "' and pd.products_id = p.products_id and pd.language_id = '" . (int)$languages_id . "'");
// EOF MaxiDVD: Modified For Ultimate Images Pack!
    $product_info = tep_db_fetch_array($product_info_query);

    tep_db_query("update " . TABLE_PRODUCTS_DESCRIPTION . " set products_viewed = products_viewed+1 where products_id = '" . (int)$_GET['products_id'] . "' and language_id = '" . (int)$languages_id . "'");

    if (tep_not_null($product_info['products_model'])) {
      $products_name = $product_info['products_name'] . '&nbsp;<span class="smallText">[' . $product_info['products_model'] . ']</span>';
    } else {
      $products_name = $product_info['products_name'];
    }
  
   // Extra Products Fields are checked and presented
    // START: Extra Fields Contribution  DMG
    /*
    $extra_fields_query = tep_db_query("SELECT pef.products_extra_fields_status as status, pef.products_extra_fields_name as name, ptf.products_extra_fields_value as value
                                        FROM ". TABLE_PRODUCTS_TO_PRODUCTS_EXTRA_FIELDS ." ptf,
                                             ". TABLE_PRODUCTS_EXTRA_FIELDS ." pef
                                        WHERE ptf.products_id='".(int)$_GET['products_id']."' 
                                          and ptf.products_extra_fields_value <> ''
                                          and ptf.products_extra_fields_id = pef.products_extra_fields_id
                                          and (pef.languages_id='0' or pef.languages_id='".$languages_id."')
                                        ORDER BY products_extra_fields_order");
*/

  $content = CONTENT_PRODUCT_INFO;
  $javascript = 'popup_window.js';

  require(DIR_WS_TEMPLATES . TEMPLATE_NAME . '/' . TEMPLATENAME_MAIN_PAGE);

  require(DIR_WS_INCLUDES . 'application_bottom.php');
?>
