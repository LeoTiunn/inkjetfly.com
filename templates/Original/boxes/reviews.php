<?php
/*
  $Id: reviews.php,v 1.1.1.1 2004/03/04 23:42:16 ccwjr Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 osCommerce

  Released under the GNU General Public License
*/
?>
<!-- reviews //-->
          <tr>
            <td>
<?php
  $info_box_contents = array();
    $info_box_contents[] = array('text'  => '<font color="' . $font_color . '">' . BOX_HEADING_REVIEWS . '</font>');
  new infoBoxHeading($info_box_contents, false, false, tep_href_link(FILENAME_REVIEWS, '', 'NONSSL'));

  $random_select = "select r.reviews_id, r.reviews_rating, p.products_id, p.products_image, pd.products_name from " . TABLE_REVIEWS . " r, " . TABLE_REVIEWS_DESCRIPTION . " rd, " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd where p.products_status = '1' and p.products_id = r.products_id and r.reviews_id = rd.reviews_id and rd.languages_id = '" . (int)$languages_id . "' and p.products_id = pd.products_id and pd.language_id = '" . (int)$languages_id . "'";
  if ($HTTP_GET_VARS['products_id']) {
    $random_select .= " and p.products_id = '" . $HTTP_GET_VARS['products_id'] . "'";
  }
  $random_select .= " order by r.reviews_id DESC limit " . MAX_RANDOM_SELECT_REVIEWS;
  $random_product = tep_random_select($random_select);

  if ($random_product) {
// display random review box
    $random_product['products_name'] = tep_get_products_name($random_product['products_id']);
    $review = htmlspecialchars($random_product['reviews_text']);
    $review = tep_break_string($review, 15, '-<br>');

    $info_box_contents = array();
    $info_box_contents[] = array('align' => 'left',
                                 'text'  => '<br><div align="center"><a href="' . tep_href_link(FILENAME_PRODUCT_REVIEWS_INFO, 'products_id=' . $random_product['products_id'] . '&reviews_id=' . $random_product['reviews_id'], 'NONSSL') . '">' . tep_image(DIR_WS_IMAGES . $random_product['products_image'], $random_product['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT) . '</a></div><div style="text-align: center;">
<br>Reviews: <span style="font-weight: bold;">23</span> (<a
 href="http://www.inkjetfly.com/reviews.php">view all</a>)
</div>
</div>

<div align="center">' . tep_image(DIR_WS_IMAGES . 'stars_' . $random_product['reviews_rating'] . '.gif' , sprintf(BOX_REVIEWS_TEXT_OF_5_STARS, $random_product['reviews_rating'])) . '</div>');
    new infoBox($info_box_contents);
  } elseif ($HTTP_GET_VARS['products_id']) {
// display 'write a review' box
    $info_box_contents = array();
    $info_box_contents[] = array('align' => 'left',
                                 'text'  => '<table border="0" cellspacing="0" cellpadding="2"><tr><td class="infoBoxContents"><a href="' . tep_href_link(FILENAME_PRODUCT_REVIEWS_WRITE, 'products_id=' . $HTTP_GET_VARS['products_id'], 'NONSSL') . '">' . tep_image(DIR_WS_IMAGES . 'box_write_review.gif', IMAGE_BUTTON_WRITE_REVIEW) . '</a></td><td class="infoBoxContents"><a href="' . tep_href_link(FILENAME_PRODUCT_REVIEWS_WRITE, 'products_id=' . $HTTP_GET_VARS['products_id'], 'NONSSL') . '">' . BOX_REVIEWS_WRITE_REVIEW .'</a></td></tr></table>');
    new infoBox($info_box_contents);
  } else {
// display 'no reviews' box
    $info_box_contents = array();
    $info_box_contents[] = array('align' => 'left',
                                 'text'  => BOX_REVIEWS_NO_REVIEWS);
    new infoBox($info_box_contents);
  }
$info_box_contents = array();
  $info_box_contents[] = array('align' => 'left',
'text'  => tep_draw_separator('pixel_trans.gif', '100%', '1')
                              );
  new infoboxFooter($info_box_contents, true, true);

?>
            </td>
          </tr>
<!-- reviews_eof //-->
