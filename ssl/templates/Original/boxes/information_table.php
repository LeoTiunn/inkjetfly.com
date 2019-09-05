<?php
/*
  $Id: information.php,v 1.1.1.1 2003/09/18 19:05:51 wilt Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2001 osCommerce

  Released under the GNU General Public License
*/
?>
<!-- information //-->
          <tr>
            <td>
<?php
require(DIR_WS_LANGUAGES . $language . '/informationbox.php');

  $info_box_contents = array();
    $info_box_contents[] = array('text'  => '<font color="' . $font_color . '">' . BOX_HEADING_INFORMATION_TABLE . '</font>');
  new infoBoxHeading($info_box_contents, true, true);

  // Retrieve information from Info table
   $informationString = "";

 // joles
   $sql_query = tep_db_query("SELECT information_id, languages_id, info_title FROM " . TABLE_INFORMATION . " WHERE visible= '1' and languages_id ='" . (int)$languages_id . "' ORDER BY v_order");
   while ($row = tep_db_fetch_array($sql_query)){
   $informationString .= '<a href="' . tep_href_link(FILENAME_INFORMATION,  'info_id=' . $row['information_id'] ) . '">' . $row['info_title'] . '</a><div style="height:5px; line-height:5px; background: url('.DIR_WS_IMAGES .'m26.gif) center repeat-x;"></div>';
   }

   $info_box_contents = array();

if (tep_session_is_registered('customer_id')) {
   $info_box_contents[] = array('text' =>  $informationString .
                         //                 '<a href="' . tep_href_link(FILENAME_GV_FAQ, '', 'NONSSL') . '">' . BOX_INFORMATION_GV . '</a><br>' .
                                          '<a href="' . tep_href_link(FILENAME_LINKS) . '"> ' . BOX_INFORMATION_LINKS . '</a><div style="height:5px; line-height:5px; background: url('.DIR_WS_IMAGES .'m26.gif) center repeat-x;"></div>' .
                                          '');

 } else if ((tep_session_is_registered('customer_id')) && (MODULE_ORDER_TOTAL_GV_STATUS == 'true')) {
   $info_box_contents[] = array('text' =>  $informationString .
                         //                 '<a href="' . tep_href_link(FILENAME_GV_FAQ, '', 'NONSSL') . '">' . BOX_INFORMATION_GV . '</a><br>' .
                                          '<a href="' . tep_href_link(FILENAME_LINKS) . '"> ' . BOX_INFORMATION_LINKS . '</a><div style="height:5px; line-height:5px; background: url('.DIR_WS_IMAGES .'m26.gif) center repeat-x;"></div>' .
                                          '');
} else {
   $info_box_contents[] = array('text' =>  $informationString .
                          //                '<a href="' . tep_href_link(FILENAME_GV_FAQ, '', 'NONSSL') . '">' . BOX_INFORMATION_GV . '</a><br>' .
                                          '<a href="' . tep_href_link(FILENAME_LINKS) . '"> ' . BOX_INFORMATION_LINKS . '</a><div style="height:5px; line-height:5px; background: url('.DIR_WS_IMAGES .'m26.gif) center repeat-x;"></div>' .
                                          '');
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
<!-- information_eof //-->
