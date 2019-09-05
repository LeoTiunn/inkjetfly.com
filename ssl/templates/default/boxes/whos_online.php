<?php
/*
  $Id: whos_online.php,v 1.2 2008/06/23 00:18:17 datazen Exp $

  CRE Loaded, Open Source E-Commerce Solutions
  http://www.creloaded.com

  Copyright (c) 2008 CRE Loaded
  Copyright (c) 2008 AlogoZone, Inc.
  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/
require(DIR_WS_LANGUAGES . $language . '/'.FILENAME_WHOS_ONLINEBOX);
?>
<!-- whos_online //-->
<tr>
  <td>
    <?php
    // Set expiration time, default is 900 secs (15 mins)
    $xx_mins_ago = (time() - 900);
    if (!isset($n_members)) {
      $n_members = 0;
    }
    if (!isset($n_guests)) {
      $n_guests = 0;
    }
    tep_db_query("delete from " . TABLE_WHOS_ONLINE . " where time_last_click < '" . $xx_mins_ago . "'");
    $whos_online_query = tep_db_query("select customer_id from " . TABLE_WHOS_ONLINE);
    while ($whos_online = tep_db_fetch_array($whos_online_query)) {
      if (!$whos_online['customer_id'] == 0) $n_members++;
      if ($whos_online['customer_id'] == 0) $n_guests++;
      $user_total = sprintf(tep_db_num_rows($whos_online_query));                                                                
    }
    if ($user_total == 1) {
      $there_is_are = BOX_WHOS_ONLINE_THEREIS . '&nbsp;';
    } else {
      $there_is_are = BOX_WHOS_ONLINE_THEREARE . '&nbsp;';
    }
    if ($n_guests == 1) {
      $word_guest = '&nbsp;' . BOX_WHOS_ONLINE_GUEST;
    } else {
      $word_guest = '&nbsp;' . BOX_WHOS_ONLINE_GUESTS;
    }
    if ($n_members == 1) {
      $word_member = '&nbsp;' . BOX_WHOS_ONLINE_MEMBER;
    } else {
      $word_member = '&nbsp;' . BOX_WHOS_ONLINE_MEMBERS;
    }
    if (($n_guests >= 1) && ($n_members >= 1)) $word_and = '&nbsp;' . BOX_WHOS_ONLINE_AND . '&nbsp;<br>';
    $textstring = $there_is_are;
    if ($n_guests >= 1) $textstring .= $n_guests . $word_guest;
    if (!isset($word_and)) {
      $word_and = '';
    }
    $textstring .= $word_and;
    if ($n_members >= 1) $textstring .= $n_members . $word_member;
    $textstring .= '&nbsp;online.';
    $info_box_contents = array();
    $info_box_contents[] = array('text'  => '<font color="' . $font_color . '">' . BOX_HEADING_WHOS_ONLINE . '</font>');
    new $infobox_template_heading($info_box_contents, '', $column_location); 
    $info_box_contents = array();
    $info_box_contents[] = array('align' => 'left',
                                 'text'  =>  $textstring
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
<!-- whos_online eof//-->