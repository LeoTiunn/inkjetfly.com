<?php
/*
  $Id: product_notifications.php,v 1.1.1.1 2004/03/04 23:42:16 ccwjr Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 osCommerce

  Released under the GNU General Public License
*/
?>
<!-- notifications //-->
<?php

  if (isset($HTTP_GET_VARS['products_id'])) {
    if (tep_session_is_registered('customer_id')) {
      $check_query = tep_db_query("select count(*) as count from " . TABLE_CUSTOMERS_INFO . " where customers_info_id = '" . $customer_id . "' and global_product_notifications = '1'");
      $check = tep_db_fetch_array($check_query);
      if ($check['count'] <= 0) {
?>
          <tr>
            <td>
<?php
  $info_box_contents = array();
    $info_box_contents[] = array('text'  => '<font color="' . $font_color . '">' . BOX_HEADING_NOTIFICATIONS . '</font>');
  new infoBoxHeading($info_box_contents, false, false );

  $check_query = tep_db_query("select count(*) as count from " . TABLE_PRODUCTS_NOTIFICATIONS . " where products_id = '" . $HTTP_GET_VARS['products_id'] . "' and customers_id = '" . $customer_id . "'");
  $check = tep_db_fetch_array($check_query);

  $info_box_contents = array();
  if ($check['count'] > 0) {
    $info_box_contents[] = array('align' => 'left',
                                 'text'  => '<table border="0" cellspacing="0" cellpadding="2"><tr><td class="infoBoxContents"><a href="' . tep_href_link(basename($PHP_SELF), tep_get_all_get_params(array('action')) . 'action=notify_remove', 'NONSSL') . '">' . tep_image(DIR_WS_IMAGES . 'box_products_notifications_remove.gif', IMAGE_BUTTON_REMOVE_NOTIFICATIONS) . '</a></td><td class="infoBoxContents"><a href="' . tep_href_link(basename($PHP_SELF), tep_get_all_get_params(array('action')) . 'action=notify_remove', 'NONSSL') . '">' . sprintf(BOX_NOTIFICATIONS_NOTIFY_REMOVE, tep_get_products_name($HTTP_GET_VARS['products_id'])) .'</a></td></tr></table>');
  } else {
    $info_box_contents[] = array('align' => 'left',
                                 'text'  => '<table border="0" cellspacing="0" cellpadding="2"><tr><td class="infoBoxContents"><a href="' . tep_href_link(basename($PHP_SELF), tep_get_all_get_params(array('action')) . 'action=notify', 'NONSSL') . '">' . tep_image(DIR_WS_IMAGES . 'box_products_notifications.gif', IMAGE_BUTTON_NOTIFICATIONS) . '</a></td><td class="infoBoxContents"><a href="' . tep_href_link(basename($PHP_SELF), tep_get_all_get_params(array('action')) . 'action=notify', 'NONSSL') . '">' . sprintf(BOX_NOTIFICATIONS_NOTIFY, tep_get_products_name($HTTP_GET_VARS['products_id'])) .'</a></td></tr></table>');
  }
   new infoBox($info_box_contents);


$info_box_contents = array();
  $info_box_contents[] = array('align' => 'left',
'text'  => tep_draw_separator('pixel_trans.gif', '100%', '1')
                              );
  new infoboxFooter($info_box_contents, true, true);
}}
?>
            </td>
          </tr>
<?php

}
?>
<!-- notifications_eof //-->
