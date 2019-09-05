<?php
/*
  $Id: order_history.php,v 1.2 2008/06/23 00:18:17 datazen Exp $

  CRE Loaded, Open Source E-Commerce Solutions
  http://www.creloaded.com

  Copyright (c) 2008 CRE Loaded
  Copyright (c) 2008 AlogoZone, Inc.
  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/
if ( isset($_SESSION['customer_id']) ) {
  // retreive the last x products purchased
  $orders_query = tep_db_query("select distinct op.products_id from " . TABLE_ORDERS . " o, " . TABLE_ORDERS_PRODUCTS . " op, " . TABLE_PRODUCTS . " p where o.customers_id = '" . (int)$_SESSION['customer_id'] . "' and o.orders_id = op.orders_id and op.products_id = p.products_id and p.products_status = '1' group by products_id order by o.date_purchased desc limit " . MAX_DISPLAY_PRODUCTS_IN_ORDER_HISTORY_BOX);
  if (tep_db_num_rows($orders_query)) {
    ?>
    <!-- order_history //-->
    <tr>
      <td>
        <?php
        $info_box_contents = array();
        $info_box_contents[] = array('text'  => '<font color="' . $font_color . '">' . BOX_HEADING_CUSTOMER_ORDERS . '</font>');
        new $infobox_template_heading($info_box_contents, '', $column_location);
        $product_ids = '';
        while ($orders = tep_db_fetch_array($orders_query)) {
          $product_ids .= (int)$orders['products_id'] . ',';
        }
        $product_ids = substr($product_ids, 0, -1);
        $customer_orders_string = '<table border="0" width="100%" cellspacing="0" cellpadding="1">';
        $products_query = tep_db_query("select products_id, products_name from " . TABLE_PRODUCTS_DESCRIPTION . " where products_id in (" . $product_ids . ") and language_id = '" . (int)$languages_id . "' order by products_name");
        while ($products = tep_db_fetch_array($products_query)) {
          // changes the cust_order into a buy_now action
          $customer_orders_string .= '  <tr>' .
                                     '    <td class="infoBoxContents"><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $products['products_id']) . '">' . $products['products_name'] . '</a></td>' .
                                     '    <td class="infoBoxContents" align="right" valign="top"><a href="' . tep_href_link(basename($PHP_SELF), tep_get_all_get_params(array('action','cPath','products_id')) . 'action=buy_now&products_id=' . $products['products_id'] . '&cPath=' . tep_get_product_path($products['products_id'])) . '">' . tep_image(DIR_WS_ICONS . 'cart.gif', ICON_CART) . '</a></td>' .
                                     '  </tr>';
        }
        $customer_orders_string .= '</table>';
        $info_box_contents = array();
        $info_box_contents[] = array('text' => $customer_orders_string);
        new $infobox_template($info_box_contents, true, true, $column_location);
        if (TEMPLATE_INCLUDE_FOOTER =='true'){
          $info_box_contents = array();
          $info_box_contents[] = array('align' => 'left',
                                       'text'  => tep_draw_separator('pixel_trans.gif', '100%', '1')
                                      );
          new $infobox_template_footer($info_box_contents, $column_location);
        }
        ?>
      </td>
    </tr>
    <!-- order_history_eof //-->
    <?php
  }
}
?>