<?php
/*
  $Id: categories3.php,v 1.2 2008/06/23 00:18:17 datazen Exp $

  CRE Loaded, Open Source E-Commerce Solutions
  http://www.creloaded.com

  Copyright (c) 2008 CRE Loaded
  Copyright (c) 2008 AlogoZone, Inc.
  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/
function tep_show_category3($cid, $cpath, $COLLAPSABLE) {
  global $categories_string3, $languages_id, $level, $categories;
  $selectedPath = array();
  // Get all of the categories on this level
  $categories_query = tep_db_query("SELECT c.categories_id, cd.categories_name, c.parent_id 
                                      from " . TABLE_CATEGORIES . " c,
                                           " . TABLE_CATEGORIES_DESCRIPTION . " cd 
                                    WHERE c.parent_id = '$cid' 
                                      and c.categories_id = cd.categories_id 
                                      and cd.language_id='" . $languages_id ."' 
                                      order by sort_order, cd.categories_name");  
  while ($categories = tep_db_fetch_array($categories_query))  {
    if ($categories[$level]['parent_id'] == "") { $categories[$level]['parent_id'] = 0; }
    $categories[$level]['categories_id'] = $categories[$level]['parent_id'] + 1;
    // Add category link to $categories_string3
    for ($a=1; $a < $level[$categories]['categories_id']; $a++) {
      $categories_string3 .= "&nbsp;&nbsp;";
    }
    $categories_string3 .= '<a href="';
    $cPath_new = $cpath;
    if ($categories[$level]['parent_id'] > 0) {
      $cPath_new .= "_";
    }
    $cPath_new .= $categories['categories_id'];
    // added for CDS CDpath support
    $CDpath = (isset($_SESSION['CDpath'])) ? '&CDpath=' . $_SESSION['CDpath'] : '';     
    $cPath_new_text = "cPath=" . $cPath_new . $CDpath;
    $categories_string3 .= tep_href_link(FILENAME_DEFAULT, $cPath_new_text);
    $categories_string3 .= '">';
    if ($_GET['cPath']) {
      $selectedPath = split("_", $_GET['cPath']);
    }
    if (in_array($categories['categories_id'], $selectedPath)) { $categories_string3 .= '<b>'; }
    if ($categories[$level]['categories_id'] == 1) { $categories_string3 .= '<u>'; }
    $categories_string3 .= tep_db_output($categories['categories_name']);
    if ($COLLAPSABLE && tep_has_category_subcategories($categories['categories_id'])) { $categories_string3 .= ' ->'; }
    if ($categories[$level]['categories_id'] == 1) { $categories_string3 .= '</u>'; }
    if (in_array($categories['categories_id'], $selectedPath)) { $categories_string3 .= '</b>'; }
    $categories_string3 .= '</a>';
    if (SHOW_COUNTS) {
      $products_in_category = tep_count_products_in_category($categories['categories_id']);
      if ($products_in_category > 0) {
        $categories_string3 .= '&nbsp;(' . $products_in_category . ')';
      }
    }
    $categories_string3 .= '<br>';
    // If I have subcategories, get them and show them
    if (tep_has_category_subcategories($categories['categories_id'])) {
      if ($COLLAPSABLE) {
        if (in_array($categories['categories_id'], $selectedPath)) {
          tep_show_category3($categories['categories_id'], $cPath_new, $COLLAPSABLE);
        }
      } else { 
        tep_show_category3($categories['categories_id'], $cPath_new, $COLLAPSABLE); 
      }
    }
  }
}
if ((defined('USE_CACHE') && USE_CACHE == 'true') && !defined('SID')) {
  echo tep_cache_categories_box3();
} else {  
  ?>
  <!-- categories 3 //-->
  <tr>
    <td>
      <?php
      $info_box_contents = array();
      $info_box_contents[] = array('align' => 'left',
                                   'text'  => '<font color="' . $font_color . '">' . BOX_HEADING_CATEGORIES3 . '</font>'
                                  );
      new $infobox_template_heading($info_box_contents, '', $column_location); 
      $categories_string3 = '';
      tep_show_category3(0,'',0);
      $info_box_contents = array();
      $info_box_contents[] = array('align' => 'left',
                                   'text'  => $categories_string3
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
  <!-- categories3 eof//-->
  <?php
}
?>