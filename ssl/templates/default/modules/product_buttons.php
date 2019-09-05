<?php
//
?>
  <?php
    $reviews_query = tep_db_query("select count(*) as count from " . TABLE_REVIEWS . " where products_id = '" . (int)$_GET['products_id'] . "'");
    $reviews = tep_db_fetch_array($reviews_query);
 if ($reviews['count'] > 0) {
?>
  <tr>
    <td class="main"><?php echo TEXT_CURRENT_REVIEWS . ' ' . $reviews['count']; ?></td>
  </tr>
  <tr>
    <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
  </tr>
  <?php
    }
    ?>
 <tr>
    <td><table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox">
        <tr class="infoBoxContents">
          <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr>
                <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
                <td class="main" valign="middle"><?php echo '<a href="' . tep_href_link(FILENAME_PRODUCT_REVIEWS, tep_get_all_get_params()) . '">' . tep_template_image_button('button_reviews.gif', IMAGE_BUTTON_REVIEWS,'align="middle"') . '</a>'; ?></td>
                <!-- Begin Wishlist Code -->
<?php 
                  if (DESIGN_BUTTON_WISHLIST == 'true') {
                    echo '<td align="right" class="main" valign="middle">';
                    if( ( ! isset($_SESSION['customer_id']) ) || ( ! isset($_SESSION['SESSION_WISHLIST']) ) ){
                      $_SESSION['SESSION_WISHLIST'] = $product_info['products_id'];
                      }
                    echo '<a href="javascript:document.cart_quantity.action=\'' . str_replace('&amp;', '&', tep_href_link(FILENAME_PRODUCT_INFO, 'action=add_wishlist')) . '\'; document.cart_quantity.submit();">' . tep_template_image_button('button_add_wishlist.gif', IMAGE_BUTTON_ADD_WISHLIST,'align="middle"') . '</a>' ;
                    echo '</td>';
                  } 
?>
   <!-- End Wishlist Code -->
<?php 
    // } 
?>
                <td class="main" align="right" valign="middle"><table border="0" cellspacing="0" cellpadding="0" align="right">
  <tr>
  <?php if (tep_db_num_rows($sub_products_sql) ==0) {?>
    <td class="main"><?php echo TEXT_ENTER_QUANTITY . ':&nbsp;&nbsp;';?></td>
    <td class="main"><?php echo tep_draw_input_field('cart_quantity', '1', 'size="4"');?>&nbsp;&nbsp;</td>
  <?php }?>
    <td class="main"><?php echo tep_draw_hidden_field('products_id', $product_info['products_id']) . tep_template_image_submit('button_in_cart.gif', IMAGE_BUTTON_IN_CART,'align="middle"'); ?></td>
  </tr>
</table></form></td>
                <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
              </tr>
            </table></td>
        </tr>
      </table></td>