<?php
/*
  $Id: wishlist.php,v 1.1.1.1 2004/03/04 23:42:27 ccwjr Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com
*/
?>
<!-- wishlist //-->
<?php
if (basename($PHP_SELF) != FILENAME_WISHLIST_SEND){
if (basename($PHP_SELF) != FILENAME_WISHLIST)   {
?> 
          <tr>
            <td>
<?php
 // retreive the wishlist

  if (tep_session_is_registered('customer_id')) {
//wishlist query

$wishlist_query = tep_db_query("select * from " . TABLE_WISHLIST . " WHERE customers_id= '" . (int)$customer_id . "' order by products_name");

// if we have something in this clients list:

?>
<!-- customer_wishlist //-->
<script language="javascript"><!--
function popupWindowWishlist(url) {
  window.open(url,'popupWindow','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no,width=425,height=475,screenX=150,screenY=150,top=150,left=150')
}
//--></script>


<?php
    $info_box_contents = array();
    $info_box_contents[] = array('text'  => '<font color="' . $font_color . '">' . BOX_HEADING_WISHLIST . '</font>');
    new infoBoxHeading($info_box_contents, false, false, tep_href_link(FILENAME_WISHLIST, '','NONSSL'));

    $info_box_contents = array();

//set max listings for side box
    if (tep_db_num_rows($wishlist_query)) {
  	if (tep_db_num_rows($wishlist_query) < MAX_DISPLAY_WISHLIST_BOX) {
      $product_ids = '';
      while ($wishlist = tep_db_fetch_array($wishlist_query)) {
        $product_ids .= $wishlist['products_id'] . ',';
      }
      $product_ids = substr($product_ids, 0, -1);

      $customer_wishlist_string = '<table border="0" width="100%" cellspacing="0" cellpadding="0">' . "\n";
//get list of products
      $products_query = tep_db_query("select products_id, products_name from " . TABLE_PRODUCTS_DESCRIPTION . " where products_id in (" . $product_ids . ") and language_id = '" . $languages_id . "' order by products_name");
      while ($products = tep_db_fetch_array($products_query)) {
          $product_dis_id =$products['products_id'];
          $product_dis_name =$products['products_name'];
      $products1_query = tep_db_query("select products_id, products_image from " . TABLE_PRODUCTS . " where products_id = '" . $product_ids . "'");
      $products1 = tep_db_fetch_array($products1_query);
          $product_image = $products1['products_image'];

       $customer_wishlist_string .= '  <tr>' . "\n" .
                                    '    <td class="infoBoxContents" align="center">' . "\n" .
                                    '		<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'cPath=' . tep_get_product_path($products['products_id']) . '&products_id=' . $products['products_id'], 'NONSSL') . '">' . tep_image(DIR_WS_IMAGES . $product_image, $products['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT) . '</a><br>' . "\n" .
                                    '       <a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'cPath=' . tep_get_product_path($product_dis_id) . '&products_id=' . $product_dis_id, 'NONSSL') . '">' . $product_dis_name . '</a></td>' . "\n" .
  								    '	  </tr>' . "\n" .
  								    '	  <tr>' . "\n" .
                                    '    <td class="infoBoxContents" align="center" valign="bottom"><b><a href="' . tep_href_link(basename($PHP_SELF), tep_get_all_get_params(array('action')) . 'action=cust_order&pid=' . $product_dis_id . '&rfw=1', 'NONSSL') . '">Move to Cart</a>&nbsp;|' . "\n" .
                                    '    <a href="' . tep_href_link(basename($PHP_SELF), tep_get_all_get_params(array('action')) . 'action=remove_wishlist&pid=' . $product_dis_id, 'NONSSL') . '">Delete</a></b></td>' . "\n" .
                                    '  </tr><tr><td class="infoBoxContents" valign="top">' . tep_image(DIR_WS_IMAGES . 'pixel_trans.gif', '', '1', '5') . tep_draw_separator('pixel_black.gif', '100%', '1') . tep_image(DIR_WS_IMAGES . 'pixel_trans.gif', '', '1', '5') . '</td></tr>' . "\n";
      }
  	  } else {
      $customer_wishlist_string = '<table border="0" width="100%" cellspacing="0" cellpadding="0">' . "\n";
  	  $customer_wishlist_string .= '</td></tr>' . "\n";
  	  }
  	} else {
      $customer_wishlist_string = '<table border="0" width="100%" cellspacing="0" cellpadding="0">' . "\n";
  	$customer_wishlist_string .= '<tr><td class="infoBoxContents">' . BOX_WISHLIST_EMPTY . '</td></tr>' . "\n";
  	}
      $customer_wishlist_string .= '<tr><td colspan="3" align="right" class="smallText"><a href="' . tep_href_link(FILENAME_WISHLIST, '','NONSSL') . '"><u>View ' . BOX_HEADING_CUSTOMER_WISHLIST . '</u> [+]</a></td></tr>' . "\n";
      $customer_wishlist_string .= '<tr><td colspan="3" align="right" class="smallText"><a href="javascript:popupWindowWishlist(\'' . tep_href_link('popup_' . FILENAME_WISHLIST_HELP, '','NONSSL') . '\')"><u>'. BOX_HEADING_CUSTOMER_WISHLIST . ' Help</u> [?]</a></td></tr>' . "\n"; // Popup link
      $customer_wishlist_string .= '<tr><td colspan="3" align="right" class="smallText"><a href="' . tep_href_link(FILENAME_WISHLIST_HELP, '','NONSSL') . '"><u>'. BOX_HEADING_CUSTOMER_WISHLIST . ' Help</u> [?]</a></td></tr>' . "\n"; // Normal link
      $customer_wishlist_string .= '</table>';

      $info_box_contents[] = array('align' => 'left',
                                   'text'  => $customer_wishlist_string);

new infoBox($info_box_contents);
  $info_box_contents = array();
  $info_box_contents[] = array('align' => 'left',
                                          'text'  => tep_draw_separator('pixel_trans.gif', '100%', '1')
                                );
  new infoboxFooter($info_box_contents, true, true);

}
?>

            </td>
          </tr>
          <?php
	  }}
?> 
<!-- wishlist_eof //-->
