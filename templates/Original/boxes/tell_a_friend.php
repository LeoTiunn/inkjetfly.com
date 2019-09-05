<?php
/*
  $Id: tell_a_friend.php,v 1.1.1.1 2004/03/04 23:42:16 ccwjr Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 osCommerce

  Released under the GNU General Public License
*/

  if (isset($HTTP_GET_VARS['products_id'])) {
    if (basename($PHP_SELF) != FILENAME_TELL_A_FRIEND)
   {
?>

<!-- tell_a_friend //-->
          <tr>
            <td>
<?php
  if (isset($HTTP_GET_VARS['products_id'])) {
    if (basename($PHP_SELF) != FILENAME_TELL_A_FRIEND)
   {
    
  $info_box_contents = array();
    $info_box_contents[] = array('text'  => '<font color="' . $font_color . '">' . BOX_HEADING_TELL_A_FRIEND . '</font>');
  new infoBoxHeading($info_box_contents, false, false);

  $hide = tep_draw_hidden_field('products_id', $HTTP_GET_VARS['products_id']);
  $hide .= tep_hide_session_id();

  $info_box_contents = array();
  $info_box_contents[] = array('form'  => '<form name="tell_a_friend" method="get" action="' . tep_href_link(FILENAME_TELL_A_FRIEND, '', 'NONSSL', false) . '">',
                               'align' => 'center',
                               'text'  => '<div align="center">' . tep_draw_input_field('send_to', '', 'size="10"') . '&nbsp;' . tep_image_submit('button_tell_a_friend.gif', BOX_HEADING_TELL_A_FRIEND) . $hide . '</div><br>' . BOX_TELL_A_FRIEND_TEXT
                              );
   new infoBox($info_box_contents);


$info_box_contents = array();
  $info_box_contents[] = array('align' => 'left',
'text'  => tep_draw_separator('pixel_trans.gif', '100%', '1')
                              );
  new infoboxFooter($info_box_contents, true, true);
}}
?>
            </td>
          </tr>
<?php
}}
?>
<!-- tell_a_friend_eof //-->
