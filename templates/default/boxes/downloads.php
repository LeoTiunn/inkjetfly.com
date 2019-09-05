<?php
/*
  $Id: downloads.php,v 1.2 2008/06/23 00:18:17 datazen Exp $

  CRE Loaded, Open Source E-Commerce Solutions
  http://www.creloaded.com

  Copyright (c) 2008 CRE Loaded
  Copyright (c) 2008 AlogoZone, Inc.
  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/
?>
<!-- downloads //-->
<?php
// modification of the logic to prevent the queries if there is no customer id
if ( isset($_SESSION['customer_id']) && $_SESSION['customer_id'] != '' ) {
  if (!strstr($PHP_SELF, FILENAME_ACCOUNT_HISTORY_INFO)) {
    // Get last order id for checkout_success
    $orders_query_raw = "SELECT orders_id FROM " . TABLE_ORDERS . " WHERE customers_id = '" . $_SESSION['customer_id'] . "' ORDER BY orders_id DESC LIMIT 1";
    $orders_query = tep_db_query($orders_query_raw);
    $orders_values = tep_db_fetch_array($orders_query);
    $last_order = $orders_values['orders_id'];
  } else {
    $last_order = (int)$_GET['order_id'];
  }
  // Now get all downloadable products in that order
  $downloads_query_raw = "SELECT DATE_FORMAT(date_purchased, '%Y-%m-%d') as date_purchased_day, opd.download_maxdays, op.products_name, opd.orders_products_download_id, opd.orders_products_filename, opd.download_count, opd.download_maxdays
                            from " . TABLE_ORDERS . " o, " . TABLE_ORDERS_PRODUCTS . " op, " . TABLE_ORDERS_PRODUCTS_DOWNLOAD . " opd
                          WHERE customers_id = '" . $_SESSION['customer_id'] . "'
                            and o.orders_id = '" . $last_order . "'
                            and op.orders_id = '" . $last_order . "'
                            and opd.orders_products_id=op.orders_products_id
                            and o.orders_status >= '" . DOWNLOADS_CONTROLLER_ORDERS_STATUS . "'
                            and opd.orders_products_filename<>''";
  $downloads_query = tep_db_query($downloads_query_raw);
  // Don't display if there is no downloadable product
  if (tep_db_num_rows($downloads_query) > 0) {
     require(DIR_WS_LANGUAGES . $language . '/'.FILENAME_DOWNLOADBOX);
     echo '<tr><td>' ;
     $info_box_contents = array();
     $info_box_contents[] = array('text'  => '<font color="' . $font_color . '">' . BOX_HEADING_DOWNLOADS . '</font>');
     new $infobox_template_heading($info_box_contents, '', $column_location); 
     $info_box_contents = array();
     ?>
     <!-- list of products -->
     <?php
     while ($downloads_values = tep_db_fetch_array($downloads_query)) {
       list($dt_year, $dt_month, $dt_day) = explode('-', $downloads_values['date_purchased_day']);
       $download_timestamp = mktime(23, 59, 59, $dt_month, $dt_day + $downloads_values['download_maxdays'], $dt_year);
       $download_expiry = date('Y-m-d H:i:s', $download_timestamp);
       // The link will appear only if:
       // - Download remaining count is > 0, AND
       // - The file is present in the DOWNLOAD directory, AND EITHER
       // - No expiry date is enforced (maxdays == 0), OR
       // - The expiry date is not reached
       if (($downloads_values['download_count'] > 0) &&
          (file_exists(DIR_FS_DOWNLOAD . $downloads_values['orders_products_filename'])) &&
          (($downloads_values['download_maxdays'] == 0) ||
          ($download_timestamp > time()))) {
         $info_box_contents = array();
         $info_box_contents[] = array('align' => 'left',
                                      'text'  => TEXT_HEADING_DOWNLOAD_FILE . '<br><br><a href="' . tep_href_link(FILENAME_DOWNLOAD, 'order=' . $last_order . '&id=' . $downloads_values['orders_products_download_id']) . '">' . $downloads_values['products_name'] . '</a>'
                                     );
         new $infobox_template($info_box_contents, true, true, $column_location);
       } else {
         $info_box_contents = array();
         $info_box_contents[] = array('align' => 'left',
                                      'text'  => $downloads_values['products_name']
                                     );
         new $infobox_template($info_box_contents, true, true, $column_location);
       }
       $info_box_contents = array();
       $info_box_contents[] = array('align' => 'left',
                                    'text'  => TEXT_HEADING_DOWNLOAD_DATE . '<br>' .  tep_date_long($download_expiry)
                                   );
 
       new $infobox_template($info_box_contents, true, true, $column_location);
       $info_box_contents = array();
       $info_box_contents[] = array('align' => 'left',
                                    'text'  => $downloads_values['download_count'] . '  ' .  TEXT_HEADING_DOWNLOAD_COUNT
                                   );
       new $infobox_template($info_box_contents, true, true, $column_location);
     }
     if (!strstr($PHP_SELF, FILENAME_ACCOUNT_HISTORY_INFO)) {
       $info_box_contents = array();
       $info_box_contents[] = array('align' => 'left',
                                    'text'  => TEXT_FOOTER_DOWNLOAD . '<br><a href="' . tep_href_link(FILENAME_ACCOUNT, '', 'SSL') . '">' . TEXT_DOWNLOAD_MY_ACCOUNT . '</a>'
                                   );
       new $infobox_template($info_box_contents, true, true, $column_location);
       if (TEMPLATE_INCLUDE_FOOTER =='true'){
         $info_box_contents = array();
         $info_box_contents[] = array('align' => 'left',
                                      'text'  => tep_draw_separator('pixel_trans.gif', '100%', '1')
                                     );
         new $infobox_template_footer($info_box_contents, $column_location);
       } 
     }
     echo '</td></tr>';
  }
}
?>
<!-- downloads eof//-->