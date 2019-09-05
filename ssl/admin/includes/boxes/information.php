<?php
/*
  $Id: information.php,v 1.1 2008/06/11 00:18:17 datazen Exp $

  CRE Loaded, Open Source E-Commerce Solutions
  http://www.creloaded.com

  Copyright (c) 2008 CRE Loaded
  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/
?>
<!-- information //-->
          <tr>
            <td>
<?php
$heading = array();
$contents = array();
$heading[] = array('text'  => BOX_HEADING_INFORMATION,
                   'link'  => tep_href_link(FILENAME_PAGES_CATEGORIES, 'selected_box=information'));
if ($_SESSION['selected_box'] == 'information' || MENU_DHTML == 'True') {
    //RCI to include links  
    $returned_rci_top = $cre_RCI->get('information', 'boxestop');
    $returned_rci_bottom = $cre_RCI->get('information', 'boxesbottom');
    $contents[] = array('text'  => $returned_rci_top .
                                   tep_admin_files_boxes('', BOX_HEADING_PAGE_MANAGER, '') .
                                   tep_admin_files_boxes(FILENAME_PAGES_CATEGORIES, BOX_PAGES_CATEGORIES, 'NONSSL', '', '2') .
                                   tep_admin_files_boxes(FILENAME_PAGES, BOX_PAGES, 'NONSSL' , '', '2') .
                                   tep_admin_files_boxes('','FAQ System') .
                                   tep_admin_files_boxes(FILENAME_FAQ_MANAGER, BOX_FAQ_MANAGER, 'NONSSL' , '', '2') .
                                   tep_admin_files_boxes(FILENAME_FAQ_CATEGORIES, BOX_FAQ_CATEGORIES, 'NONSSL' , '', '2') .
                                   tep_admin_files_boxes(FILENAME_DEFINE_MAINPAGE, BOX_CATALOG_DEFINE_MAINPAGE, 'SSL','','2') .
                                   tep_admin_files_boxes('','Information Manage') .
                                   tep_admin_files_boxes(FILENAME_INFORMATION_MANAGER, 'Information Manage', 'NONSSL' , '', '2') .
                                   $returned_rci_bottom);
}
$box = new box;
echo $box->menuBox($heading, $contents);
?>
            </td>
          </tr>
<!-- information_eof //-->