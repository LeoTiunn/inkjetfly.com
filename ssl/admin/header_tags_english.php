<?php
/*
  $Id: header_tags_controller.php,v 1.0 2005/04/08 22:50:52 hpdl Exp $
  Originally Created by: Jack York - http://www.oscommerce-solution.com
  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');
  require('includes/functions/header_tags.php');
  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_HEADER_TAGS_CONTROLLER);

  if (!isset($_GET['lngdir'])) {
    $_GET['lngdir'] = $language;
  }

  if (isset($_GET['lngdir'])) {
    $language_1 = $_GET['lngdir'];
  } else {
    $language_1 = $language ;
  }

  $filename = DIR_FS_CATALOG . DIR_WS_LANGUAGES . $language_1 . '/header_tags.php';

  $formActive = false;

  /****************** READ IN FORM DATA ******************/
  $args_new = array();
  $main = array();
  $action = (isset($_POST['action']) ? $_POST['action'] : '');

  if (tep_not_null($action))
  {
      $main['title'] = htmlspecialchars($_POST['main_title']);  //read in the knowns
      $main['desc'] = htmlspecialchars($_POST['main_desc']);
      $main['keyword'] = htmlspecialchars($_POST['main_keyword']);

      $formActive = true;
      $pageCount = TotalPages($filename);
      for ($t = 0, $c = 0; $t < $pageCount; ++$t, $c += 3) //read in the unknowns
      {
         $args_new['title'][$t] = $_POST[$c];
         $args_new['desc'][$t] = $_POST[$c+1];
         $args_new['keyword'][$t] = $_POST[$c+2];

        $boxID = sprintf("HTTA_%d", $t);
         $args_new['HTTA'][$t] = (isset($_POST[$boxID]) ? $_POST[$boxID] : '');
         $boxID = sprintf("HTDA_%d", $t);
         $args_new['HTDA'][$t] = (isset($_POST[$boxID]) ? $_POST[$boxID] : '');
         $boxID = sprintf("HTKA_%d", $t);
         $args_new['HTKA'][$t] = (isset($_POST[$boxID]) ? $_POST[$boxID] : '');
         $boxID = sprintf("HTCA_%d", $t);
         $args_new['HTCA'][$t] = (isset($_POST[$boxID]) ? $_POST[$boxID] : '');
      }
  }

  /***************** READ IN DISK FILE ******************/
  $main_title = '';
  $main_desc = '';
  $main_key = '';
  $sections = array();      //used for unknown titles
  $args = array();          //used for unknown titles
  $ctr = 0;                 //used for unknown titles
  $findTitles = false;      //used for unknown titles
  $fp = file($filename);

  for ($idx = 0; $idx < count($fp); ++$idx)
  {
      if (strpos($fp[$idx], "define('HEAD_TITLE_TAG_ALL','") !== FALSE)
      {
          $main_title = GetMainArgument($fp[$idx], $main_title, $main['title'], $formActive);
      }
      else if (strpos($fp[$idx], "define('HEAD_DESC_TAG_ALL") !== FALSE)
      {
          $main_desc = GetMainArgument($fp[$idx], $main_desc, (isset($main['desc']) ? $main['desc'] : ''), $formActive);
      }
      else if (strpos($fp[$idx], "define('HEAD_KEY_TAG_ALL") !== FALSE)
      {
          if (!isset($main['keyword'])) $main['keyword'] = '';
          $main_key = GetMainArgument($fp[$idx], $main_key, $main['keyword'], $formActive);
          $findTitles = true;  //enable next section
      }
      else if ($findTitles)
      {
          if (($pos = strpos($fp[$idx], '.php')) !== FALSE) //get the section titles
          {
              $sections['titles'][$ctr] = GetSectionName($fp[$idx]);
              $ctr++;
          }
          else                                   //get the rest of the items in this section
          {
              if (! IsComment($fp[$idx])) // && tep_not_null($fp[$idx]))
              {
                  $c = $ctr - 1;
                  if (IsTitleSwitch($fp[$idx]))
                  {
                     $args['title_switch'][$c] = GetSwitchSetting($fp[$idx]);
                     $args['title_switch_name'][$c] = sprintf("HTTA_%d",$c);
                     if ($formActive)
                     {
                       $fp[$idx] = ChangeSwitch($fp[$idx], $args_new['HTTA'][$c]);
                       $args['title_switch'][$c] = GetSwitchSetting($fp[$idx]);
                       $args['title_switch_name'][$c] = sprintf("HTTA_%d",$c);
                     }
                  }
                  else if (IsDescriptionSwitch($fp[$idx]))
                  {
                     $args['desc_switch'][$c] = GetSwitchSetting($fp[$idx]);
                     $args['desc_switch_name'][$c] = sprintf("HTDA_%d",$c);
                     if ($formActive)
                     {
                       $fp[$idx] = ChangeSwitch($fp[$idx], $args_new['HTDA'][$c]);
                       $args['desc_switch'][$c] = GetSwitchSetting($fp[$idx]);
                       $args['desc_switch_name'][$c] = sprintf("HTDA_%d",$c);
                     }
                  }

                  if (IsKeywordSwitch($fp[$idx]))
                  {
                     $args['keyword_switch'][$c] = GetSwitchSetting($fp[$idx]);
                     $args['keyword_switch_name'][$c] = sprintf("HTKA_%d",$c);
                     if ($formActive)
                     {
                       $fp[$idx] = ChangeSwitch($fp[$idx], $args_new['HTKA'][$c]);
                       $args['keyword_switch'][$c] = GetSwitchSetting($fp[$idx]);
                       $args['keyword_switch_name'][$c] = sprintf("HTKA_%d",$c);
                     }
                  }
                  else if (IsCatSwitch($fp[$idx]))
                  {
                     $args['cat_switch'][$c] = GetSwitchSetting($fp[$idx]);
                     $args['cat_switch_name'][$c] = sprintf("HTCA_%d",$c);
                     if ($formActive)
                       $fp[$idx] = ChangeSwitch($fp[$idx], $args_new['HTCA'][$c]);
                  }
                  else if (IsTitleTag($fp[$idx]))
                  {
                    if (!isset($args_new['title'][$c])) $args_new['title'][$c] ='';
                    $args['title'][$c] = GetArgument($fp[$idx], $args_new['title'][$c], $formActive);
                  }
                  else if (IsDescriptionTag($fp[$idx]))
                  {
                    if (!isset($args_new['desc'][$c])) $args_new['desc'][$c] = '';
                    $args['desc'][$c] = GetArgument($fp[$idx], $args_new['desc'][$c], $formActive);
                  }
                  else if (IsKeywordTag($fp[$idx]))
                  {
                    if (!isset($args_new['keyword'][$c])) $args_new['keyword'][$c] = '';
                    $args['keyword'][$c] = GetArgument($fp[$idx], $args_new['keyword'][$c], $formActive);
                  }
              }
          }
      }
  }

  /***************** WRITE THE FILE ******************/
  if ($formActive)
  {
     WriteHeaderTagsFile($filename, $fp);
  //once file is updated refreash the page
  $formActive = false;
tep_redirect(tep_href_link(FILENAME_HEADER_TAGS_ENGLISH));
  }
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<script type="text/javascript" src="includes/prototype.js"></script>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<!--[if IE]>
<link rel="stylesheet" type="text/css" href="includes/stylesheet-ie.css">
<![endif]-->
<style type="text/css">
td.HTC_Head {color: sienna; font-size: 24px; font-weight: bold; }
td.HTC_subHead {color: sienna; font-size: 14px; }
</style>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<!-- body //-->
<div id="body">
<table border="0" width="100%" cellspacing="0" cellpadding="0" class="body-table">
  <tr>
     <!-- left_navigation //-->
     <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
     <!-- left_navigation_eof //-->
    <!-- body_text //-->
    <td valign="top" class="page-container"><table border="0" width="100%" cellspacing="0" cellpadding="0">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo HEADING_TITLE_ENGLISH; ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
      <tr>
        <td class="main"><?php echo TEXT_ENGLISH_TAGS; ?></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
      <!-- Begin of Header Tags -->
      <tr>
        <td align="right"><?php echo tep_draw_form('header_tags', FILENAME_HEADER_TAGS_ENGLISH, '', 'post') . tep_draw_hidden_field('action', 'process'); ?></td>
          <tr>
            <td width="100%" valign="top">
              <table border="0" cellspacing="0" cellpadding="2">
                <tr>
                  <td class="smallText" style="font-weight: bold;"><!-- Default Title --><?php echo HEADER_TAGS_ENGLISH_TXT_1;?></td>
                  <td class="smallText" ><?php echo tep_draw_input_field('main_title', tep_not_null($main_title) ? $main_title : '', 'maxlength="255", size="60"', false); ?> </td>
                </tr>
                <tr>
                  <td class="smallText" style="font-weight: bold;"><!-- Default Descriptions --><?php echo HEADER_TAGS_ENGLISH_TXT_2;?></td>
                  <td class="smallText" ><?php echo tep_draw_input_field('main_desc', tep_not_null($main_desc) ? $main_desc : '', 'maxlength="255", size="60"', false); ?> </td>
                </tr>
                <tr>
                  <td class="smallText" style="font-weight: bold;"><!-- Default Keyword(s) --><?php echo HEADER_TAGS_ENGLISH_TXT_3;?></td>
                  <td class="smallText" ><?php echo tep_draw_input_field('main_keyword', tep_not_null($main_key) ? $main_key : '', 'maxlength="255", size="60"', false); ?> </td>
                </tr>
              </table>
            </td>
          </tr>
          <tr>
            <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
          </tr>          
          <tr>
            <td><?php echo tep_black_line(); ?></td>
          </tr>          
          <tr>
            <td width="100%" valign="top">
              <table border="0" width="100%" cellspacing="0" cellpadding="2">          
                <?php 
                for ($i = 0, $id = 0; $i < count($sections['titles']); ++$i, $id += 3) { 
                  ?>
                  <tr>
                    <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
                  </tr>
                  <tr>
                    <td>
                      <table border="0" cellspacing="0" cellpadding="2">
                        <tr>
                          <td class="smallText"><b><?php echo $sections['titles'][$i]; ?></b> --> </td>
                          <td class="smallText"><?php echo HEADER_TAGS_ENGLISH_TXT_4;?></td>
                          <td align="left"><?php echo tep_draw_checkbox_field($args['title_switch_name'][$i], '', $args['title_switch'][$i], ''); ?> </td>
                          <td class="smallText"><?php echo HEADER_TAGS_ENGLISH_TXT_5;?> </td>
                          <td align="left"><?php echo tep_draw_checkbox_field($args['desc_switch_name'][$i], '', $args['desc_switch'][$i], ''); ?> </td>
                          <td class="smallText"><?php echo HEADER_TAGS_ENGLISH_TXT_6;?></td>
                          <td align="left"><?php echo tep_draw_checkbox_field($args['keyword_switch_name'][$i], '', $args['keyword_switch'][$i], ''); ?> </td>
                          <td class="smallText">
                            <?php
                            if (isset($args['cat_switch_name'][$i])){
                              echo HEADER_TAGS_ENGLISH_TXT_7;
                            }
                            ?>
                          </td>
                          <td align="left"><?php echo (( isset($args['cat_switch_name'][$i]) && isset($args['cat_switch'][$i])) ? tep_draw_checkbox_field($args['cat_switch_name'][$i], '', $args['cat_switch'][$i], '') : ''); ?> </td>
                        </tr>
                      </table>
                    </td>
                  </tr>
                  <tr>
                    <td colspan="3" ><table border="0" width="100%">
                      <tr>
                        <td width="2%">&nbsp;</td>
                        <td class="smallText" width="12%"><!-- Title: --><?php echo HEADER_TAGS_ENGLISH_TXT_9;?></td>
                        <td class="smallText" ><?php echo tep_draw_input_field($id, $args['title'][$i], 'maxlength="255", size="60"', false, 300); ?> </td>
                      </tr>
                      <tr>
                        <td width="2%">&nbsp;</td>
                        <td class="smallText" width="12%"><!-- Description: --><?php echo HEADER_TAGS_ENGLISH_TXT_10;?></td>
                        <td class="smallText" ><?php echo tep_draw_input_field($id+1, $args['desc'][$i], 'maxlength="255", size="60"', false); ?> </td>
                      </tr>
                      <tr>
                        <td width="2%">&nbsp;</td>
                        <td class="smallText" width="12%"><!-- Keyword(s): --><?php echo HEADER_TAGS_ENGLISH_TXT_11;?></td>
                        <td class="smallText" ><?php echo tep_draw_input_field($id+2, $args['keyword'][$i], 'maxlength="255", size="60"', false); ?> </td>
                      </tr>
                    </table></td>
                  </tr>
                  <?php 
                } 
                ?>
              </table>
            </td>
          </tr>
          <tr>
            <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
          </tr>
          <tr>
            <td align="center"><?php echo (tep_image_submit('button_update.gif', IMAGE_UPDATE) ) . ' <a href="' . tep_href_link(FILENAME_HEADER_TAGS_ENGLISH, tep_get_all_get_params(array('action'))) .'">' . '</a>'; ?></td>
          </tr>
          </form>
        </td>
      </tr>
      <!-- end of Header Tags -->
    </table></td>
    <!-- body_text_eof //-->
  </tr>
</table>
</div>
<!-- body_eof //-->
<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
<br>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>