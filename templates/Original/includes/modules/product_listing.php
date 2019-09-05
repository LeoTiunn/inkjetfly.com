<?php
/*
  $Id: product_listing.php,v 1.44 2003/06/09 22:49:59 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/
  $listing_split = new splitPageResults($listing_sql, MAX_DISPLAY_SEARCH_RESULTS, 'p.products_id');

  if ( ($listing_split->number_of_rows > 0) && ( (PREV_NEXT_BAR_LOCATION == '1') || (PREV_NEXT_BAR_LOCATION == '3') ) ) {
?>
<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
    <td class="smallText"><?php echo $listing_split->display_count(TEXT_DISPLAY_NUMBER_OF_PRODUCTS); ?></td>
    <td class="smallText" align="right"><?php echo TEXT_RESULT_PAGE . ' ' . $listing_split->display_links(MAX_DISPLAY_PAGE_LINKS, tep_get_all_get_params(array('page', 'info', 'x', 'y'))); ?></td>
  </tr>
          <tr>
            <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
          </tr>

</table>
<?php
  }

  $list_box_contents = array();

  for ($col=0, $n=sizeof($column_list); $col<$n; $col++) {
    switch ($column_list[$col]) {
      case 'PRODUCT_LIST_MODEL':
        $lc_text = TABLE_HEADING_MODEL;
        $lc_align = '';
        break;
      case 'PRODUCT_LIST_NAME':
        $lc_text = TABLE_HEADING_PRODUCTS;
        $lc_align = '';
        break;
      case 'PRODUCT_LIST_MANUFACTURER':
        $lc_text = TABLE_HEADING_MANUFACTURER;
        $lc_align = '';
        break;
      case 'PRODUCT_LIST_PRICE':
        $lc_text = TABLE_HEADING_PRICE;
        $lc_align = 'right';
        break;
      case 'PRODUCT_LIST_QUANTITY':
        $lc_text = TABLE_HEADING_QUANTITY;
        $lc_align = 'right';
        break;
      case 'PRODUCT_LIST_WEIGHT':
        $lc_text = TABLE_HEADING_WEIGHT;
        $lc_align = 'right';
        break;
      case 'PRODUCT_LIST_IMAGE':
        $lc_text = TABLE_HEADING_IMAGE;
        $lc_align = 'center';
        break;
      case 'PRODUCT_LIST_BUY_NOW':
        $lc_text = TABLE_HEADING_BUY_NOW;
        $lc_align = 'center';
        break;
    }

    if ( ($column_list[$col] != 'PRODUCT_LIST_BUY_NOW') && ($column_list[$col] != 'PRODUCT_LIST_IMAGE') ) {
      $lc_text = tep_create_sort_heading($HTTP_GET_VARS['sort'], $col+1, $lc_text);
    }

    $list_box_contents[0][] = array('align' => $lc_align,
                                    'params' => 'class="productListing-heading"',
                                    'text' => '&nbsp;' . $lc_text . '&nbsp;');
  }

  if ($listing_split->number_of_rows > 0) {
    $rows = 0;
    $column = 0;
    echo '     
              <table cellspacing=0 cellpadding=0>

               <tr><td colspan=10><img src=images/m35.gif width=749 height=4></td></tr>
               <tr><td valign=top class="bg3"><table cellspacing=0 cellpadding=0><tr>
    ';

    $listing_query = tep_db_query($listing_split->sql_query);
    while ($listing = tep_db_fetch_array($listing_query)) {
      $product_contents = array();
      $rows++;
      if (($rows/2) == floor($rows/2)) {
        $list_box_contents[] = array('params' => 'class="productListing-even"');
      } else {
        $list_box_contents[] = array('params' => 'class="productListing-odd"');
      }

      $cur_row = sizeof($list_box_contents) - 1;

      for ($col=0, $n=sizeof($column_list); $col<$n; $col++) {
        $lc_align = '';

        switch ($column_list[$col]) {
          case 'PRODUCT_LIST_MODEL':
            $lc_align = '';
            $lc_text = '&nbsp;' . $listing['products_model'] . '&nbsp;';
            break;
          case 'PRODUCT_LIST_NAME':
            $lc_align = '';
            if (isset($HTTP_GET_VARS['manufacturers_id'])) {
              $lc_text = '<a class=ml2 href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'manufacturers_id=' . $HTTP_GET_VARS['manufacturers_id'] . '&products_id=' . $listing['products_id']) . '">' . $listing['products_name'] . '</a>';
            } else {
              $lc_text = '<a class=ml2 href="' . tep_href_link(FILENAME_PRODUCT_INFO, ($cPath ? 'cPath=' . $cPath . '&' : '') . 'products_id=' . $listing['products_id']) . '">' . $listing['products_name'] . '</a>';
            }
            break;
          case 'PRODUCT_LIST_MANUFACTURER':
            $lc_align = '';
            $lc_text = '&nbsp;<a href="' . tep_href_link(FILENAME_DEFAULT, 'manufacturers_id=' . $listing['manufacturers_id']) . '">' . $listing['manufacturers_name'] . '</a>&nbsp;';
            break;
          case 'PRODUCT_LIST_PRICE':
            $lc_align = 'right';
            if (tep_not_null($listing['specials_new_products_price'])) {
              $lc_text = '<span style="color:#7F7F7F;font-size:12px"><s>' .  $currencies->display_price($listing['products_price'], tep_get_tax_rate($listing['products_tax_class_id'])) . '</s></span><br><span class=t15>' . $currencies->display_price($listing['specials_new_products_price'], tep_get_tax_rate($listing['products_tax_class_id'])) . '</span>';
            } else {
              $lc_text = '<span>&nbsp;' . $currencies->display_price($listing['products_price'], tep_get_tax_rate($listing['products_tax_class_id'])) . '&nbsp;</span>';
            }
            break;
          case 'PRODUCT_LIST_QUANTITY':
            $lc_align = 'right';
            $lc_text = '&nbsp;' . $listing['products_quantity'] . '&nbsp;';
            break;
          case 'PRODUCT_LIST_WEIGHT':
            $lc_align = 'right';
            $lc_text = '&nbsp;' . $listing['products_weight'] . '&nbsp;';
            break;
          case 'PRODUCT_LIST_IMAGE':
            $lc_align = 'center';
            if (isset($HTTP_GET_VARS['manufacturers_id'])) {
              $lc_text = '<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'manufacturers_id=' . $HTTP_GET_VARS['manufacturers_id'] . '&products_id=' . $listing['products_id']) . '">' . tep_image(DIR_WS_IMAGES . $listing['products_image'], $listing['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, ' class=br') . '</a>';
            } else {
              $lc_text = '&nbsp;<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, ($cPath ? 'cPath=' . $cPath . '&' : '') . 'products_id=' . $listing['products_id']) . '">' . tep_image(DIR_WS_IMAGES . $listing['products_image'], $listing['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, ' class=br') . '</a>&nbsp;';
            }
			if(!file_exists(DIR_WS_IMAGES . $listing['products_image']))
			$lc_text ='';
            break;
          case 'PRODUCT_LIST_BUY_NOW':
           $lc_align = 'center';
           $lc_text = '<a href="' . tep_href_link(basename($PHP_SELF), tep_get_all_get_params(array('action', 'pName')) . 'action=buy_now&products_id=' . $listing['products_id']) . '">' . tep_template_image_button('button_buy_now.gif', IMAGE_BUTTON_BUY_NOW) . '</a> ';
           break;	

        }

        $list_box_contents[$cur_row][] = array('align' => $lc_align,
                                               'params' => 'class="productListing-data"',
                                               'text'  => $lc_text);
        $product_contents[] = $lc_text;       
      }


      echo '<td  valign=top style="width:180px;text-align:center;'.($column>=3?'':'border-right:1px solid #dddddd;').'">
                          <table cellspacing=0 cellpadding=0 width=150 align=center border=0>
                           <tr><td colspan=2 height=5></td></tr>
                           <tr><td>'.$product_contents[0].'</td>
                               <td valign=middle><span class=cy1>'.$product_contents[2].'</span></td>
                           </tr>
                           <tr><td colspan=2 height=5></td></tr>
                           <tr><td colspan=2 align=center width=150 height=25>'.$product_contents[1].'</td></tr>
                           <tr><td colspan=2 height=10></td></tr>
                           <tr><td colspan=2 align=center><img src=images/m36.gif width=143 height=1></td></tr>
                           <tr><td colspan=2 height=10></td></tr>
                           <tr><td colspan=2 align=center><a href="'.tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $listing['products_id']) . '">' . tep_image_button('small_view.gif', IMAGE_BUTTON_BUY_NOW) . '</a>&nbsp;<a href="' . tep_href_link(basename($PHP_SELF), tep_get_all_get_params(array('action')) . 'action=buy_now&products_id=' . $listing['products_id']) . '">' . tep_image_button('button_in_cart.gif', IMAGE_BUTTON_BUY_NOW) . '</a></td></tr>
                           <tr><td colspan=2 height=12></td></tr>
                          </table>                        
                     </td>';
      $column ++;
      if ($column >= 4) {
        $rows ++;
        $column = 0;
        echo '            
                     </tr>
                    </table>
                    <table cellspacing=0 cellpadding=0>
                     <tr><td><img src=images/m37.gif width=729 height=1></td></tr>
                    </table>     
                    <table cellspacing=0 cellpadding=0 border=0>                     
                     <tr>
             ';
      } else echo '<td width=0></td>';
    
    
    
    }

    echo '
                </tr></table></td>
                </tr>
               <tr><td><img src=images/m43.gif width=749 height=7></td></tr>
              </table>
         ';

    //new productListingBox($list_box_contents);
  } else {
    $list_box_contents = array();

    $list_box_contents[0] = array('params' => 'class="productListing-odd"');
    $list_box_contents[0][] = array('params' => 'class="productListing-data"',
                                   'text' => TEXT_NO_PRODUCTS);

    echo '<div class=PageHeading>Categories</div>';
    new productListingBox($list_box_contents);
  }

  if ( ($listing_split->number_of_rows > 0) && ((PREV_NEXT_BAR_LOCATION == '2') || (PREV_NEXT_BAR_LOCATION == '3')) ) {
?>
<table border="0" width="100%" cellspacing="0" cellpadding="2">
          <tr>
            <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
          </tr>
  <tr>
    <td class="smallText"><?php echo $listing_split->display_count(TEXT_DISPLAY_NUMBER_OF_PRODUCTS); ?></td>
    <td class="smallText" align="right"><?php echo TEXT_RESULT_PAGE . ' ' . $listing_split->display_links(MAX_DISPLAY_PAGE_LINKS, tep_get_all_get_params(array('page', 'info', 'x', 'y'))); ?></td>
  </tr>
</table>

<?php
  }
?>
