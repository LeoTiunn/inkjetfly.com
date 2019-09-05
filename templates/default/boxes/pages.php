<?php
/*
  $Id: pages.php,v 1.2 2008/06/23 00:18:17 datazen Exp $

  CRE Loaded, Open Source E-Commerce Solutions
  http://www.creloaded.com

  Copyright (c) 2008 CRE Loaded
  Copyright (c) 2008 AlogoZone, Inc.
  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/
function tep_show_pages($pages_counter) {
  global $foo, $page_categories_string, $id;
  for ($a=0; $a<$foo[$pages_counter]['level']; $a++) {
    $page_categories_string .= "&nbsp;&nbsp;";
  }
  $page_categories_string .= '<a href="';
  if ($foo[$pages_counter]['parent'] == 0) {
    $pPath_new = 'cID=' . $pages_counter;
  } else {
    $pPath_new = 'cID=' . $foo[$pages_counter]['path'];
  }
  $page_categories_string .= tep_href_link(FILENAME_PAGES, $pPath_new);
  $page_categories_string .= '">';
  if ( ($id) && (in_array($pages_counter, $id)) ) {
    $page_categories_string .= '<b>';
  }
  // display category name
  $page_categories_string .= $foo[$pages_counter]['name'];
  if ( ($id) && (in_array($pages_counter, $id)) ) {
    $page_categories_string .= '</b>';
  }
  if (tep_has_category_subcategories($pages_counter)) {
    $page_categories_string .= '';
  }
  $page_categories_string .= '</a>';
  $page_categories_string .= '<br>';
  if ($foo[$pages_counter]['next_id']) {
    tep_show_pages($foo[$pages_counter]['next_id']);
  }
}
?>
<!-- pages //-->
<tr>
  <td>
    <?php
    $info_box_contents = array();
    $info_box_contents[] = array('align' => 'left',
                                 'text'  => '<font color="' . $font_color . '">' . BOX_HEADING_PAGES . '</font>');
    new $infobox_template_heading($info_box_contents, '', $column_location); 
    $page_categories_string = '';
    $categories_query = tep_db_query("select c.categories_id, cd.categories_name from " . TABLE_PAGES_CATEGORIES . " c, " . TABLE_PAGES_CATEGORIES_DESCRIPTION . " cd where c.categories_status = '1' and c.categories_id = cd.categories_id and cd.language_id='" . $languages_id ."' order by cd.categories_name");
    while ($categories = tep_db_fetch_array($categories_query))  {
      $foo[$categories['categories_id']] = array('name' => $categories['categories_name'],
                                                 'parent' => 0,
                                                 'level' => 0,
                                                 'path' => $categories['categories_id'],
                                                 'next_id' => false
                                                );
      if (isset($prev_id)) {
        $foo[$prev_id]['next_id'] = $categories['categories_id'];
      }
      $prev_id = $categories['categories_id'];
      if (!isset($first_pages_element)) {
        $first_pages_element = $categories['categories_id'];
      }  
    }
    
/*
    if ($_GET['cID']) {
      $new_path = '';
      $id = split('_', $_GET['cID']);
      reset($id);
      while (list($key, $value) = each($id)) {
        unset($prev_id);
        unset($first_id);
        $categories_query = tep_db_query("select c.categories_id, cd.categories_name, c.categories_parent_id from " . TABLE_PAGES_CATEGORIES . " c, " . TABLE_PAGES_CATEGORIES_DESCRIPTION . " cd where c.categories_parent_id = '" . $value . "' and c.categories_status = '1' and c.categories_id = cd.categories_id and cd.language_id='" . $languages_id ."' order by cd.categories_name");
        $category_check = tep_db_num_rows($categories_query);
        if ($category_check > 0) {
          $new_path .= $value;
          while ($row = tep_db_fetch_array($categories_query)) {
            $foo[$row['categories_id']] = array('name' => $row['categories_name'],
                                                'parent' => $row['categories_parent_id'],
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
*/

    tep_show_pages($first_pages_element);
    $contact_us_link = '<a href="' . tep_href_link(FILENAME_CONTACT_US, '', 'NONSSL') . '">' . CONTACT_US_ALT . '</a><br>';
    $info_box_contents = array();
    $info_box_contents[] = array('align' => 'left',
                                 'text'  => $page_categories_string . $contact_us_link
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
<!-- pages eof//-->