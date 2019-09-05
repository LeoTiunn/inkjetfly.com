<?php
/*
  $Id: categories.php,v 1.2 2008/06/23 00:18:17 datazen Exp $

  CRE Loaded, Open Source E-Commerce Solutions
  http://www.creloaded.com

  Copyright (c) 2008 CRE Loaded
  Copyright (c) 2008 AlogoZone, Inc.
  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/
$side = $column_location;
$nr = 1;
function tep_show_category_az($counter) {
  global $tree, $categories_string, $cPath_array, $id, $nr, $side;
    // end background variables
    $categories_string .= '<tr><td class="' . $side . 'category">';
    for ($i=0; $i<$tree[$counter]['level']; $i++) {
      $categories_string .= "&nbsp;-&nbsp;";
    }
    $categories_string .= '<a href="';
    if ($tree[$counter]['parent'] == 0) {
      $cPath_new = 'cPath=' . $counter;
    } else {
      $cPath_new = 'cPath=' . $tree[$counter]['path'];
    }
    $categories_string .= tep_href_link(FILENAME_DEFAULT, $cPath_new) . '">';
    if (isset($cPath_array) && in_array($counter, $cPath_array)) {
      $categories_string .= '<b><u>';
    }
    // display category name
    $categories_string .= $tree[$counter]['name'];
    if (isset($cPath_array) && in_array($counter, $cPath_array)) {
      $categories_string .= '</u></b>';
    }
    $categories_string .= '</a>';
    if (SHOW_COUNTS == 'true') {
      $products_in_category = tep_count_products_in_category($counter);
      if ($products_in_category > 0) {
        $categories_string .= '&nbsp;<span class="category_count">(' . $products_in_category . ')</span>';
      }
    }
    $categories_string .= '</td></tr>' . "\n";
    $nr ++;
    if ($tree[$counter]['next_id'] != false) {
      tep_show_category_az($tree[$counter]['next_id']);
    }
}
if ((defined('USE_CACHE') && USE_CACHE == 'true') && !defined('SID')) {
  echo tep_cache_categories_box();
} else {  
  ?>
  <!-- categories //-->
  <tr>
    <td valign="top">
      <?php
      $info_box_contents = array();
      $info_box_contents[] = array('text'  => '<font color="' . $font_color . '">' . BOX_HEADING_CATEGORIES . '</font>'); 
      new $infobox_template_heading($info_box_contents, '', $column_location); 
      $categories_string = '';
      $tree = array();
      $categories_query_raw = "SELECT c.categories_id, cd.categories_name, c.parent_id 
                                 from " . TABLE_CATEGORIES . " c,
                                      " . TABLE_CATEGORIES_DESCRIPTION . " cd 
                               WHERE c.parent_id = '0' 
                                 and c.categories_id = cd.categories_id 
                                 and cd.language_id='" . $languages_id ."'";
      $categories_query_raw .= " ORDER BY sort_order, cd.categories_name";
      $categories_query = tep_db_query($categories_query_raw);
      unset($parent_id, $first_element);  // make sure they did not care over foem somewhere else
      while ($categories = tep_db_fetch_array($categories_query))  {
        $tree[$categories['categories_id']] = array('name' => $categories['categories_name'],
                                                    'parent' => $categories['parent_id'],
                                                    'level' => 0,
                                                    'path' => $categories['categories_id'],
                                                    'next_id' => false);
        if (isset($parent_id)) {
          $tree[$parent_id]['next_id'] = $categories['categories_id'];
        }
        $parent_id = $categories['categories_id'];
        if (!isset($first_element)) {
          $first_element = $categories['categories_id'];
        }
      }
      if (tep_not_null($cPath)) {
        $new_path = '';
        reset($cPath_array);
        while (list($key, $value) = each($cPath_array)) {
          unset($parent_id);
          unset($first_id);
          $categories_query = tep_db_query("SELECT c.categories_id, cd.categories_name, c.parent_id 
                                              from " . TABLE_CATEGORIES . " c, 
                                                   " . TABLE_CATEGORIES_DESCRIPTION . " cd 
                                            WHERE c.parent_id = '" . (int)$value . "' 
                                              and c.categories_id = cd.categories_id 
                                              and cd.language_id='" . (int)$languages_id ."' 
                                            ORDER BY sort_order, cd.categories_name");
          if (tep_db_num_rows($categories_query)) {
            $new_path .= $value;
            while ($row = tep_db_fetch_array($categories_query)) {
              $tree[$row['categories_id']] = array('name' => $row['categories_name'],
                                                   'parent' => $row['parent_id'],
                                                   'level' => $key+1,
                                                   'path' => $new_path . '_' . $row['categories_id'],
                                                   'next_id' => false);
              if (isset($parent_id)) {
                $tree[$parent_id]['next_id'] = $row['categories_id'];
              }
              $parent_id = $row['categories_id'];
              if (!isset($first_id)) {
                $first_id = $row['categories_id'];
              }
              $last_id = $row['categories_id'];
            }
            $tree[$last_id]['next_id'] = $tree[$value]['next_id'];
            $tree[$value]['next_id'] = $first_id;
            $new_path .= '_';
          } else {
            break;
          }
        }
      }
      tep_show_category_az($first_element); 
      $info_box_contents = array();
      $info_box_contents[] = array('align' => 'left',
                                   'text'  => "\n" . '<table border="0" width="100%" cellspacing="0" cellpadding="0">'. "\n" . $categories_string . '</table>'
                                  );
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
  <!-- categories_eof //-->
  <?php
}
?>