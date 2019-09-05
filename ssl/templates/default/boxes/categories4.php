<?php
/*
  $Id: categories4.php,v 1.2 2008/06/23 00:18:17 datazen Exp $

  CRE Loaded, Open Source E-Commerce Solutions
  http://www.creloaded.com

  Copyright (c) 2008 CRE Loaded
  Copyright (c) 2008 AlogoZone, Inc.
  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/ 
function tep_show_category4($counter) {
  global $foo, $categories_string4, $id, $aa;
  for ($a=0; $a<$foo[$counter]['level']; $a++) {
    if ($a == $foo[$counter]['level']-1) {
      $categories_string4 .= "<font color='#ff0000'>|__</font>";
    } else {
      $categories_string4 .= "<b><font color='#ff0000'>&nbsp;&nbsp;&nbsp;&nbsp;</font></b>";
    }
  }
  if ($foo[$counter]['level'] == 0) {
    if ($aa == 1) {
      $categories_string4 .= "<hr>";
    } else {
      $aa = 1;
    }
  }
  $categories_string4 .= '<a href="';
  // added for CDS CDpath support
  $CDpath = (isset($_SESSION['CDpath'])) ? '&CDpath=' . $_SESSION['CDpath'] : ''; 
  if ($foo[$counter]['parent'] == 0) {
    $cPath_new = 'cPath=' . $counter . $CDpath;
  } else {
    $cPath_new = 'cPath=' . $foo[$counter]['path'] . $CDpath;
  }
  $categories_string4 .= tep_href_link(FILENAME_DEFAULT, $cPath_new);
  $categories_string4 .= '">';
  if ($foo[$counter]['parent'] == 0) {
    $categories_string4 .= '<b>';
  } else if ( ($id) && (in_array($counter, $id)) ) {
    $categories_string4 .= "<b><font color='#ff0000'>";
  }
  // display category name
  $categories_string4 .= tep_db_output($foo[$counter]['name']);
  if ($foo[$counter]['parent'] == 0) {
    $categories_string4 .= '</b>';
  } else if ( ($id) && (in_array($counter, $id)) ) {
    $categories_string4 .= '</font></b>';
  }
  if (tep_has_category_subcategories($counter)) {
    $categories_string4 .= '<b>-&gt; </b>';
  }
  $categories_string4 .= '</a>';
  if (SHOW_COUNTS == 'true') {
    $products_in_category = tep_count_products_in_category($counter);
    if ($products_in_category > 0) {
      $categories_string4 .= '&nbsp;(' . $products_in_category . ')';
    }
  }
  $categories_string4 .= '<br>';
  if ($foo[$counter]['next_id']) {
    tep_show_category4($foo[$counter]['next_id']);
  }
}
if ((defined('USE_CACHE') && USE_CACHE == 'true') && !defined('SID')) {
  echo tep_cache_categories_box4();
} else {  
  ?>
  <!-- categories4 //-->
  <tr>
    <td>
      <?php
      $aa = 0;
      $info_box_contents = array();
      $info_box_contents[] = array('text'  => '<font color="' . $font_color . '">' . BOX_HEADING_CATEGORIES4 . '</font>');
      new $infobox_template_heading($info_box_contents, '', $column_location);
      $categories_string4 = '';
      $categories_query = tep_db_query("SELECT c.categories_id, cd.categories_name, c.parent_id 
                                          from " . TABLE_CATEGORIES . " c,
                                               " . TABLE_CATEGORIES_DESCRIPTION . " cd 
                                        WHERE c.parent_id = '0' 
                                          and c.categories_id = cd.categories_id 
                                          and cd.language_id='" . $languages_id ."' 
                                        ORDER BY sort_order, cd.categories_name");
      while ($categories = tep_db_fetch_array($categories_query))  {
        $foo[$categories['categories_id']] = array('name' => $categories['categories_name'],
                                                   'parent' => $categories['parent_id'],
                                                   'level' => 0,
                                                   'path' => $categories['categories_id'],
                                                   'next_id' => false
                                                  );
        if (isset($prev_id)) {
          $foo[$prev_id]['next_id'] = $categories['categories_id'];
        }
        $prev_id = $categories['categories_id'];
        if (!isset($first_element)) {
          $first_element = $categories['categories_id'];
        }
      }
      if ($cPath) {
        $new_path = '';
        $id = split('_', $cPath);
        reset($id);
        while (list($key, $value) = each($id)) {
          unset($prev_id);
          unset($first_id);
          $categories_query = tep_db_query("select c.categories_id, cd.categories_name, c.parent_id from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where c.parent_id = '" . $value . "' and c.categories_id = cd.categories_id and cd.language_id='" . $languages_id ."' order by sort_order, cd.categories_name");
          $category_check = tep_db_num_rows($categories_query);
          if ($category_check > 0) {
            $new_path .= $value;
            while ($row = tep_db_fetch_array($categories_query)) {
              $foo[$row['categories_id']] = array('name' => $row['categories_name'],
                                                  'parent' => $row['parent_id'],
                                                  'level' => $key+1,
                                                  'path' => $new_path . '_' . $row['categories_id'],
                                                  'next_id' => false
                                                 );
              if (isset($prev_id)) {
                $foo[$prev_id]['next_id'] = $row['categories_id'];
              }
              $prev_id = $row['categories_id'];
              if (!isset($first_id)) {
                $first_id = $row['categories_id'];
              }
              $last_id = $row['categories_id'];
            }
            $foo[$last_id]['next_id'] = $foo[$value]['next_id'];
            $foo[$value]['next_id'] = $first_id;
            $new_path .= '_';
          } else {
            break;
          }
        }
      }
      tep_show_category4($first_element);
      // added for CDS CDpath support
      $params = (isset($_SESSION['CDpath'])) ? 'CDpath=' . $_SESSION['CDpath'] : ''; 
      //coment out the below lines if you do not want to have an all products list
      $categories_string4 .= "<hr>\n";
      $categories_string4 .= '<a href="' . tep_href_link(FILENAME_ALL_PRODS, $params) . '"><b>' . BOX_INFORMATION_ALLPRODS . "</b></a>\n";
      $categories_string4 .= "-&gt;<br><hr>\n";
      $categories_string4 .= '<a href="' . tep_href_link(FILENAME_ALL_PRODCATS, $params) . '"><b>' . ALL_PRODUCTS_LINK . "</b></a>\n";
      $categories_string4 .= "-&gt;<br><hr>\n";
      $categories_string4 .= '<a href="' . tep_href_link(FILENAME_ALL_PRODMANF, $params) . '"><b>' . ALL_PRODUCTS_MANF . "</b></a>\n";
      $categories_string4 .= "-&gt;<br>\n";
      $info_box_contents = array();
      $info_box_contents[] = array('align' => 'left',
                                   'text'  => $categories_string4
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
  <!-- categories4_eof //-->
  <?php
}
unset($prev_id);
unset($first_id);
unset($last_id);
unset($new_path);
unset($first_element);
unset($foo);
unset($categories_string4);
unset($id);
unset($aa);
?>