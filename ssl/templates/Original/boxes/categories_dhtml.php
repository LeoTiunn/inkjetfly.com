<?php
/*
  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com
  Released under the GNU General Public License
  
  Dynamic HTML menu categories with nonjavascript compatibility
  Version 1.1, 02/23/2005 Jaroslav Jedlinsky, xjedlins@centrum.cz
*/
?>

<?php

// module configration:

// compatibility mode for browsers with no Javascript support or with
// disabled Javascript
// the module generates code between <noscript></noscript> tags with
// old style items menu when set to 'True'
define('NOJAVASCRIPT_COMPATIBILITY', 'False');

// sorting mode of items in the list
// set Original for sorting by 'sort_order' and 'categories_name'
// set NameOnly for sorting only by 'categories_name'
define('SORTING_MODE', 'Original' /* 'NameOnly' */);

?>

<?php
  switch (SORTING_MODE) {
    case 'NameOnly': $sort_by = 'cd.categories_name'; break;
	default        : $sort_by = 'sort_order, cd.categories_name'; break;
  } // switch
?>

<!-- categories //-->
          <tr>
            <td>
<?php
  function tep_show_category($counter) {
    global $tree, $njs_categories_string, $cPath_array;

    for ($i=0; $i<$tree[$counter]['level']; $i++) {
      $njs_categories_string .= "&nbsp;&nbsp;";
    }

    $njs_categories_string .= '<a href="';

    if ($tree[$counter]['parent'] == 0) {
      $cPath_new = 'cPath=' . $counter;
    } else {
      $cPath_new = 'cPath=' . $tree[$counter]['path'];
    }

    $njs_categories_string .= str_replace('&amp;','&',tep_href_link(FILENAME_DEFAULT, $cPath_new)) . '">';

    if (isset($cPath_array) && in_array($counter, $cPath_array)) {
      $njs_categories_string .= '<b>';
    }

// display category name
    $njs_categories_string .= $tree[$counter]['name'];

    if (isset($cPath_array) && in_array($counter, $cPath_array)) {
      $njs_categories_string .= '</b>';
    }

    if (tep_has_category_subcategories($counter)) {
      $njs_categories_string .= '-&gt;';
    }

    $njs_categories_string .= '</a>';

    if (SHOW_COUNTS == 'true') {
      $products_in_category = tep_count_products_in_category($counter);
      if ($products_in_category > 0) {
        $njs_categories_string .= '&nbsp;(' . $products_in_category . ')';
      }
    }

    $njs_categories_string .= '<br>';

    if ($tree[$counter]['next_id'] != false) {
      tep_show_category($tree[$counter]['next_id']);
    }
  }

if (NOJAVASCRIPT_COMPATIBILITY == 'True') {

  $njs_categories_string = '';
  $tree = array();

  $categories_query = tep_db_query("select c.categories_id, cd.categories_name, c.parent_id from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where c.parent_id = '0' and c.categories_id = cd.categories_id and cd.language_id='" . (int)(int)$languages_id ."' order by " . $sort_by);
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

// begin of NOJAVASCRIPT compatibility code

  if (tep_not_null($cPath)) {
    $new_path = '';
    reset($cPath_array);
    while (list($key, $value) = each($cPath_array)) {
      unset($parent_id);
      unset($first_id);
      $categories_query = tep_db_query("select c.categories_id, cd.categories_name, c.parent_id from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where c.parent_id = '" . (int)$value . "' and c.categories_id = cd.categories_id and cd.language_id='" . (int)$languages_id ."' order by " . $sort_by);
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
  tep_show_category($first_element);

}
// end of NOJAVASCRIPT compatibility code
?>

<!-- javascript enabled DTHML menu //-->
<script language="JavaScript" src="external/jscookmenu/jscookmenu.js"></script>
<link rel="stylesheet" href="external/jscookmenu/themeie/theme.css" type="text/css">
<script language="javascript" src="external/jscookmenu/themeie/theme.js"></script> 

<?php
  function tep_build_tree($entry, $last_member = false, $level = 0, $path = '') {
    global $tree, $categories_string;

	$prefix = '';
    for ($i = 0; $i <= $level; $i++) {
      $prefix .= ' ';
    }

    if (SHOW_COUNTS == 'true') {
      $products_in_category = tep_count_products_in_category($entry);
      if ($products_in_category > 0) {
        $pinc = '&nbsp;(' . $products_in_category . ')';
      } else {
	    $pinc = '';
	  }
    }

    if ($path == '') {
      $new_path = $entry;
    } else {
	  $new_path = $path . '_' . $entry;
    }

    $categories_string .= $prefix . '[\'\', \'' . addslashes($tree[$entry]['name']) . $pinc . '\', \'' . str_replace('&amp;','&',tep_href_link(FILENAME_DEFAULT, 'cPath=' . $new_path)) . '\', null, null';

	$array_size = sizeof($tree[$entry]['children']);
    if ($array_size > 0) {
	  $categories_string .= ',' . "\n";
	  $children = $tree[$entry]['children'];
	  reset($children);
	  end($children);
	  $end_key = key($children);
	  reset($children);
	  while (list($key, $new_entry) = each($children)) {
	    $new_last_member = ($key == $end_key) ? true : false;
        tep_build_tree($new_entry, $new_last_member, $level + 1, $new_path);
	  }
      $categories_string .= $prefix;
	  $categories_string .= ($last_member == true) ? ']' : '],';
	  $categories_string .= "\n";
    } else {
	  $categories_string .= ($last_member == true) ? ']' : '],';
	  $categories_string .= "\n";
    }
  }

  $info_box_contents = array();
  $info_box_contents[] = array('align' => 'left',
                               'text'  => BOX_HEADING_CATEGORIES
                              );
  new infoBoxHeading($info_box_contents, true, false);

  $tree = array();
  $categories_string = '';
  $categories_query = tep_db_query("select c.categories_id, cd.categories_name, c.parent_id, c.sort_order from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where c.categories_id = cd.categories_id and cd.language_id='" . $languages_id . "' order by " . $sort_by);
  $last_root = 0;
  $first_root = 0;
  while ($categories = tep_db_fetch_array($categories_query)) {
    $current_id = $categories['categories_id'];
	$parent_id = $categories['parent_id'];

	if (!isset($tree[$current_id])) {
	  $tree[$current_id] = array();
	  $tree[$current_id]['children'] = array();
	  $tree[$current_id]['next'] = 0;
	}

	$tree[$current_id]['name'] = $categories['categories_name'];
    $tree[$current_id]['parent'] = $parent_id;
	$tree[$current_id]['order'] = $categories['sort_order'];

	if ($parent_id != 0) {
      if (!isset($tree[$parent_id])) {
	    $tree[$parent_id] = array();
	    $tree[$parent_id]['children'] = array();
	  }

	  $tree[$parent_id]['children'][] = $current_id;
	} else {
	  if ($last_root == 0) {
	    $last_root = $current_id;
		$first_root = $current_id;
	  } else {
	    $tree[$last_root]['next'] = $current_id;
		$last_root = $current_id;
	  }
	}
  }

  if ($first_root != 0) {
    $categories_string = '[' . "\n";
    $root = $first_root;

    do {
	  $last_member = ($tree[$root]['next'] == 0) ? true : false;
      tep_build_tree($root, $last_member);
	  $root = $tree[$root]['next'];
	} while ($root != 0);
	
	$categories_string .= ']';
  }
?>

<script language="JavaScript" type="text/javascript">
<!--
var menuID = 
<?php echo $categories_string; ?>;
//-->
</script>

<?php
$info_box_contents = array();

if (NOJAVASCRIPT_COMPATIBILITY == 'True') {
  $info_box_text = '<div id="mainmenu"><noscript>' . $njs_categories_string . '</noscript></div>';
} else {
  $info_box_text = '<div id="mainmenu"></div>';
}

$info_box_contents[] = array('text' => $info_box_text);
new infoBox($info_box_contents);
?>

<script language="JavaScript" type="text/javascript">
<!--
  cmDraw('mainmenu', menuID, 'vbr', cmThemeIE, 'themeie');
//-->
</script>
<!-- javascript enabled DTHML menu eof //-->

            </td>
          </tr>
<!-- categories_eof //-->
