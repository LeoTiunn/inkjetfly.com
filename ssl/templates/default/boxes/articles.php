<?php
/*
  $Id: articles.php,v 1.2 2008/06/23 00:18:17 datazen Exp $

  CRE Loaded, Open Source E-Commerce Solutions
  http://www.creloaded.com

  Copyright (c) 2008 CRE Loaded
  Copyright (c) 2008 AlogoZone, Inc.
  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/
function tep_show_topic_1($counter) {
  global $tree, $topics_string, $tPath_array;
  // added for CDS CDpath support
  $CDpath = (isset($_SESSION['CDpath'])) ? $_SESSION['CDpath'] : ''; 
  for ($i=0; $i<$tree[$counter]['level']; $i++) {
    $topics_string .= "&nbsp;&nbsp;";
  }
  $topics_string .= '<a href="';
  if ($tree[$counter]['parent'] == 0) {
    if ($CDpath != '') {
      $tPath_new = 'tPath=' . $counter . '&CDpath=' . $CDpath; 
    } else {
      $tPath_new = 'tPath=' . $counter;
    }
  } else {
    if ($CDpath != '') {
      $tPath_new = 'tPath=' . $tree[$counter]['path'] . $CDpath;
    } else {
      $tPath_new = 'tPath=' . $tree[$counter]['path'];
    }
  }
  $topics_string .= tep_href_link(FILENAME_ARTICLES, $tPath_new) . '">';         
  if (isset($tPath_array) && in_array($counter, $tPath_array)) {
    $topics_string .= '<b>';
  }
  // display topic name
  $topics_string .= $tree[$counter]['name'];
  if (isset($tPath_array) && in_array($counter, $tPath_array)) {
    $topics_string .= '</b>';
  }

    if (tep_has_topic_subtopics($counter)) {
      $topics_string .= ' -&gt;';
    }
    $topics_string .= '</a>';
    if (SHOW_ARTICLE_COUNTS == 'true') {
      $articles_in_topic = tep_count_articles_in_topic($counter);
      if ($articles_in_topic > 0) {
        $topics_string .= '&nbsp;(' . $articles_in_topic . ')';
      }
    }
    $topics_string .= '<br>';
    if ($tree[$counter]['next_id'] != false) {
      tep_show_topic_1($tree[$counter]['next_id']);
    }
  }
?>
<!-- articles //-->
<tr>
  <td>
    <?php
    $info_box_contents = array();
    $info_box_contents[] = array('text' =>'<font color="' . $font_color . '">' . BOX_HEADING_ARTICLES . '</font>');
    new $infobox_template_heading($info_box_contents, '', $column_location);
    $topics_string = '';
    $tree = array();
    $topics_query = tep_db_query("select t.topics_id, td.topics_name, t.parent_id from " . TABLE_TOPICS . " t, " . TABLE_TOPICS_DESCRIPTION . " td where t.parent_id = '0' and t.topics_id = td.topics_id and td.language_id = '" . (int)$languages_id . "' order by sort_order, td.topics_name");
    unset($parent_id, $first_topic_element);  // make sure they did not care over foem somewhere else
    while ($topics = tep_db_fetch_array($topics_query))  {
      $tree[$topics['topics_id']] = array('name' => $topics['topics_name'],
                                          'parent' => $topics['parent_id'],
                                          'level' => 0,
                                          'path' => $topics['topics_id'],
                                          'next_id' => false);
      if (isset($parent_id)) {
        $tree[$parent_id]['next_id'] = $topics['topics_id'];
      }
      $parent_id = $topics['topics_id'];
      if (!isset($first_topic_element)) {
        $first_topic_element = $topics['topics_id'];
      }
    }
    
    if (tep_not_null($tPath)) {
      $new_path = '';
      if(is_array($tPath_array)) {
      reset($tPath_array);
      
      while (list($key, $value) = each($tPath_array)) {
        unset($parent_id);
        unset($first_id);
        $topics_query = tep_db_query("select t.topics_id, td.topics_name, t.parent_id from " . TABLE_TOPICS . " t, " . TABLE_TOPICS_DESCRIPTION . " td where t.parent_id = '" . (int)$value . "' and t.topics_id = td.topics_id and td.language_id = '" . (int)$languages_id . "' order by sort_order, td.topics_name");
        if (tep_db_num_rows($topics_query)) {
          $new_path .= $value;
          while ($row = tep_db_fetch_array($topics_query)) {
            $tree[$row['topics_id']] = array('name' => $row['topics_name'],
                                             'parent' => $row['parent_id'],
                                             'level' => $key+1,
                                             'path' => $new_path . '_' . $row['topics_id'],
                                             'next_id' => false);
            if (isset($parent_id)) {
              $tree[$parent_id]['next_id'] = $row['topics_id'];
            }
            $parent_id = $row['topics_id'];
            if (!isset($first_id)) {
              $first_id = $row['topics_id'];
            }
            $last_id = $row['topics_id'];
          }
          $tree[$last_id]['next_id'] = $tree[$value]['next_id'];
          $tree[$value]['next_id'] = $first_id;
          $new_path .= '_';
        } else {
          break;
        }
      }
      }
    }
    if (isset($first_topic_element)) {
      tep_show_topic_1($first_topic_element);
    }

    $info_box_contents = array();
    $new_articles_string = '';
    $all_articles_string = '';

    if (DISPLAY_NEW_ARTICLES == 'true') {
      if (SHOW_ARTICLE_COUNTS == 'true') {
        $articles_new_query = tep_db_query("select a.articles_id
                                            from " . TABLE_ARTICLES . " a,
                                                 " . TABLE_AUTHORS . " au,
                                                 " . TABLE_ARTICLES_DESCRIPTION . " ad,
                                                 " . TABLE_ARTICLES_TO_TOPICS . " a2t,
                                                 " . TABLE_TOPICS_DESCRIPTION . " td
                                            where a.articles_status = '1'
                                              and(a.articles_date_available IS NULL or to_days(a.articles_date_available) <= to_days(now()))
                                              and a.authors_id = au.authors_id
                                              and a.articles_id = ad.articles_id
                                              and a.articles_id = a2t.articles_id
                                              and a2t.topics_id = td.topics_id
                                              and ad.language_id = '" . (int)$languages_id . "'
                                              and td.language_id = '" . (int)$languages_id . "'
                                              and a.articles_date_added > SUBDATE(now( ), INTERVAL '" . NEW_ARTICLES_DAYS_DISPLAY . "' DAY)");
        $articles_new_count = ' (' . tep_db_num_rows($articles_new_query) . ')';
      } else {
        $articles_new_count = '';
      }
      if (strstr($_SERVER['PHP_SELF'], CONTENT_ARTICLES_NEW . '.php') or strstr($PHP_SELF,FILENAME_ARTICLES_NEW)) {
        $new_articles_string = '<b>';
      }
      //  added logic for CDS support
      if (isset($CDpath) && $CDpath != '') {
        $new_articles_string .= '<a href="' . tep_href_link(FILENAME_ARTICLES_NEW, 'CDpath=' . $CDpath, 'NONSSL') . '">' . BOX_NEW_ARTICLES . '</a>';   
      } else {
        $new_articles_string .= '<a href="' . tep_href_link(FILENAME_ARTICLES_NEW, '', 'NONSSL') . '">' . BOX_NEW_ARTICLES . '</a>';
      }
      if (strstr($_SERVER['PHP_SELF'], CONTENT_ARTICLES_NEW . '.php') or strstr($PHP_SELF,FILENAME_ARTICLES_NEW)) {
        $new_articles_string .= '</b>';
      }
      $new_articles_string .= $articles_new_count . '<br>';
    }
    if (DISPLAY_ALL_ARTICLES == 'true') {
      if (SHOW_ARTICLE_COUNTS == 'true') {
        $articles_all_query = tep_db_query("select a.articles_id
                                            from " . TABLE_ARTICLES . " a,
                                                 " . TABLE_AUTHORS . " au,
                                                 " . TABLE_ARTICLES_DESCRIPTION . " ad,
                                                 " . TABLE_ARTICLES_TO_TOPICS . " a2t,
                                                 " . TABLE_TOPICS_DESCRIPTION . " td
                                            where a.articles_status = '1'
                                              and (a.articles_date_available IS NULL or to_days(a.articles_date_available) <= to_days(now()))
                                              and a.authors_id = au.authors_id
                                              and a.articles_id = a2t.articles_id
                                              and a.articles_id = ad.articles_id
                                              and a2t.topics_id = td.topics_id
                                              and ad.language_id = '" . (int)$languages_id . "'
                                              and td.language_id = '" . (int)$languages_id . "'");
        $articles_all_count = ' (' . tep_db_num_rows($articles_all_query) . ')';
      } else {
        $articles_all_count = '';
      }
      if ($topic_depth == 'top') {
        $all_articles_string = '<b>';
      }
      //  added logic for CDS support
      if (isset($CDpath) && $CDpath != '') {
        $all_articles_string .= '<a href="' . tep_href_link(FILENAME_ARTICLES, 'CDpath=' . $CDpath, 'NONSSL') . '">' . BOX_ALL_ARTICLES . '</a>';
      } else {
        $all_articles_string .= '<a href="' . tep_href_link(FILENAME_ARTICLES, '', 'NONSSL') . '">' . BOX_ALL_ARTICLES . '</a>';
      }
      if ($topic_depth == 'top') {
        $all_articles_string .= '</b>';
      }
      $all_articles_string .= $articles_all_count . '<br>';
    }
    $info_box_contents[] = array('text' => $new_articles_string . $all_articles_string . $topics_string);
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
<!-- articles_eof //-->