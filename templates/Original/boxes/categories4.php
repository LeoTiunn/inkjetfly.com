<?php
/*
  $Id: categories4.php,v 1.1 2004/04/05

  Copyright (c) 2005 Secret Pouch

  Released under the GNU General Public License
*/

  function tep_show_category($counter) {

// BoF - Contribution Category Box Enhancement 1.1
    global $tree, $categories_string, $cPath_array, $cat_name;

    for ($i=0; $i<$tree[$counter]['level']; $i++) {
      $categories_string .= "&nbsp;&nbsp;";
    }
    $cPath_new = 'cPath=' . $tree[$counter]['path'];
    if (isset($cPath_array) && in_array($counter, $cPath_array) && $cat_name == $tree[$counter]['name']) { //Link nicht anklickbar, wenn angewählt
             $categories_string .= '<a style="color:#874b5a;text-decoration:none" href="';
             $categories_string .= tep_href_link(FILENAME_DEFAULT, $cPath_new) . '">';																	 	 //Link nicht anklickbar, wenn angewählt
    } else {						 																					 //Link nicht anklickbar, wenn angewählt
    $categories_string .= '<a href="';
    $categories_string .= tep_href_link(FILENAME_DEFAULT, $cPath_new) . '">';
    }									 																						 //Link nicht anklickbar, wenn angewählt

    if (isset($cPath_array) && in_array($counter, $cPath_array)) {
      $categories_string .= '<b>';
    }

    if ($cat_name == $tree[$counter]['name']) {
      $categories_string .= '<span class="errorText">';
    }

// display category name
    $categories_string .= '<img src="'. DIR_WS_IMAGES  .'m25.gif" align="absmiddle" border=0 hspace="5"  vspace="0">' . $tree[$counter]['name'];

		if ($cat_name == $tree[$counter]['name']) {
			$categories_string .= '</span>';
    }

    if (isset($cPath_array) && in_array($counter, $cPath_array)) {
      $categories_string .= '</b>';
    }
// 	EoF Category Box Enhancement

    $categories_string .= '</a>';

    if (SHOW_COUNTS == 'true') {
      $products_in_category = tep_count_products_in_category($counter);
      if ($products_in_category > 0) {
        $categories_string .= '&nbsp;(' . $products_in_category . ')';
      }
    }

    $categories_string .= '<br>';

    if ($tree[$counter]['next_id'] != false) {
		$categories_string .= '<div style="height:5px; line-height:5px; background: url('.DIR_WS_IMAGES .'m26.gif) center repeat-x;"></div>';
      tep_show_category($tree[$counter]['next_id']);
    }
  }
?>
<!-- categories //-->
          <tr>
            <td>
<?php

// BoF - Contribution Category Box Enhancement 1.1
 if (isset($cPath_array)) {
		for ($i=0, $n=sizeof($cPath_array); $i<$n; $i++) {
				$categories_query = tep_db_query("select categories_name from " . TABLE_CATEGORIES_DESCRIPTION . " where categories_id = '" . (int)$cPath_array[$i] . "' and language_id = '" . (int)$languages_id . "'");
				if (tep_db_num_rows($categories_query) > 0)
				$categories = tep_db_fetch_array($categories_query);
		}
	$cat_name = $categories['categories_name'];
	}
// EoF Category Box Enhancement
// display category name

  $info_box_contents = array();
    $info_box_contents[] = array('align' => 'left',
                                 'text'  => '<font color="' . $font_color . '">Categories</font>');

  new infoBoxHeading($info_box_contents, true, true);

  $categories_string = '';
  $tree = array();

  $categories_query = tep_db_query("select c.categories_id, cd.categories_name, c.parent_id from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where c.parent_id = '0' and c.categories_id = cd.categories_id and cd.language_id='" . (int)$languages_id ."' order by sort_order, cd.categories_name");
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

  //------------------------
  if (tep_not_null($cPath)) {
    $new_path = '';
    reset($cPath_array);
    while (list($key, $value) = each($cPath_array)) {
      unset($parent_id);
      unset($first_id);
      $categories_query = tep_db_query("select c.categories_id, cd.categories_name, c.parent_id from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where c.parent_id = '" . (int)$value . "' and c.categories_id = cd.categories_id and cd.language_id='" . (int)$languages_id ."' order by sort_order, cd.categories_name");
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

  $info_box_contents = array();
  $info_box_contents[] = array('text' => $categories_string);

 //if ( (basename($PHP_SELF) != FILENAME_SPECIALS)) {
//$info_box_contents[] = array('align' => 'left',
                              // 'text'  => '<font size=-2><b><a href="' . tep_href_link(FILENAME_SPECIALS, '', 'NONSSL') . '">' . BOX_INFORMATION_SPECIALS . '</a></b></font>');
// }else{
// $info_box_contents[] = array('align' => 'left',
             //                  'text'  => '<font size=-2><b><a href="' . tep_href_link(FILENAME_SPECIALS, '', 'NONSSL') . '"><span class="errorText">' . BOX_INFORMATION_SPECIALS . '</a></b></font></span>');
 // }
// if ( (basename($PHP_SELF) != FILENAME_PRODUCTS_NEW)) {
//$info_box_contents[] = array('align' => 'left',
  //                             'text'  => '<font size=-2><b><a href="' . tep_href_link(FILENAME_PRODUCTS_NEW, '', 'NONSSL') . '">' . BOX_INFORMATION_PRODUCTS_NEW . '</a></b></font>');
// }else{
 //$info_box_contents[] = array('align' => 'left',
//                               'text'  => '<font size=-2><b><a href="' . tep_href_link(FILENAME_PRODUCTS_NEW, '', 'NONSSL') . '"><span class="errorText">' . BOX_INFORMATION_PRODUCTS_NEW . '</a></b></font></span>');
 // }
  function tep_get_paths($categories_array = '', $parent_id = '0', $indent = '', $path='') {
    global $languages_id;

    if (!is_array($categories_array)) $categories_array = array();

    $categories_query = tep_db_query("select c.categories_id, cd.categories_name from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where parent_id = '" . (int)$parent_id . "' and c.categories_id = cd.categories_id and cd.language_id = '" . (int)$languages_id . "' order by sort_order, cd.categories_name");
    while ($categories = tep_db_fetch_array($categories_query)) {
      if ($parent_id=='0'){
	$categories_array[] = array('id' => $categories['categories_id'],
                                      'text' => $indent . $categories['categories_name']);
      }
      else{
	$categories_array[] = array('id' => $path . $parent_id . '_' .$categories['categories_id'],
        	                          'text' => $indent . $categories['categories_name']);
      }

      if ($categories['categories_id'] != $parent_id) {
	$this_path=$path;
	if ($parent_id != '0')
	  $this_path = $path . $parent_id . '_';
        $categories_array = tep_get_paths($categories_array, $categories['categories_id'], $indent . '&nbsp;', $this_path);
      }
    }

    return $categories_array;
  }

  new infoBox($info_box_contents);

$info_box_contents = array();
  $info_box_contents[] = array('align' => 'left',
'text'  => tep_draw_separator('pixel_trans.gif', '100%', '1')
                              );
  new infoboxFooter($info_box_contents, true, true);

?>

            </td>
          </tr>
<!-- categories_eof //-->
