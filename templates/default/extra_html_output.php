<?php
/*
  $Id: extra_html_output.php,v 1.0.0.0 2007/03/13 13:41:11 datazen Exp $

  CRE Loaded, Open Source E-Commerce Solutions
  http://www.creloaded.com

  Copyright (c) 2007 CRE Loaded
  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License

  Description:  Display Navigation from CDS globally on bottom of .tpl files.
*/

// The HTML form submit button wrapper function
// Outputs a button in the selected language
function tep_template_image_submit($image, $alt = '-AltValueError-', $parameters = '') {
  global $language;
  if(defined('TEMPLATE_BUTTONS_USE_CSS') && TEMPLATE_BUTTONS_USE_CSS == 'true'){
      //$image_submit = '<nobr><a href="javascript:void(0)" onclick="submitForm(this);  return false;" style="text-decoration: none;"><span class="template-button-left">&nbsp;</span><span class="template-button-middle">' . $alt . '</span><span class="template-button-right">&nbsp;</span></a></nobr>';//failed to work with FF3
      //$image_submit = '<nobr><a href="javascript:void(0)" onclick="form = this.childNodes[0].form; if (form.onsubmit && form.onsubmit()) { form.submit(); } else if (!form.onsubmit) { form.submit(); } return false;" style="text-decoration: none;"><input type="submit" style="display: none;" /><span class="template-button-left">&nbsp;</span><span class="template-button-middle">' . $alt . '</span><span class="template-button-right">&nbsp;</span></a></nobr>';
      $image_submit ='    <span class="nowrap"><span class="template-button-left">&nbsp;</span><span class="template-button-middle"><input class="submitButton" type="submit" value="'.$alt.'" ' . $parameters . '></span><span class="template-button-right">&nbsp;</span></span>';
//    $image_submit ='    <span class="template-button-left">&nbsp;</span><span class="template-button-middle"><input class="submitButton" type="submit" value="'.$alt.'" ' . $parameters . '></span><span class="template-button-right">&nbsp;</span>';
  } else {
      $image_submit = '<input type="image" src="' . tep_output_string(DIR_WS_TEMPLATES . TEMPLATE_NAME . '/images/buttons/' . $language . '/' .  $image) . '" border="0" alt="' . tep_output_string($alt) . '"';
      if (tep_not_null($alt)) $image_submit .= ' title=" ' . tep_output_string($alt) . ' "';
      if (tep_not_null($parameters)) $image_submit .= ' ' . $parameters;
      $image_submit .= '>';
  }
  return $image_submit;
}

// Output a function button in the selected language
function tep_template_image_button($image, $alt = '-AltValueError-', $parameters = '') {
  global $language;
  if(defined('TEMPLATE_BUTTONS_USE_CSS') && TEMPLATE_BUTTONS_USE_CSS == 'true'){
   $image_button = '<span class="nowrap"><span class="template-button-left">&nbsp;</span><span class="template-button-middle">' . $alt .'</span><span class="template-button-right">&nbsp;</span></span>';
//      $image_button = '<span class="template-button-left">&nbsp;</span><span class="template-button-middle">' . $alt .'</span><span class="template-button-right">&nbsp;</span>';
  } else {
      $image_button = tep_image(DIR_WS_TEMPLATES . TEMPLATE_NAME . '/images/buttons/' . $language . '/' .  $image, $alt, '', '', $parameters);
  }
  return $image_button;
}

function table_image_border_top($left, $right, $header) {
  if (defined('MAIN_TABLE_BORDER') && (MAIN_TABLE_BORDER == 'yes')) {
    echo '<!--table_image_border_top: BOF-->' . "\n";
    echo '<tr>' . "\n";
    echo '  <td valign="top" width="100%"><table width="100%" border="0" cellspacing="0" cellpadding="0">' . "\n";

if (SHOW_HEADING_TITLE_ORIGINAL!='yes' && $header != '') {
    echo '      <tr>' . "\n";
    echo '        <td><table width="100%" border="0" cellspacing="0" cellpadding="1">' . "\n";
    echo '            <tr>' . "\n";
    echo '              <td class="main_table_heading"><table width="100%" border="0" cellspacing="0" cellpadding="1">' . "\n";
    echo '                  <tr>' . "\n";
    echo '                    <td class="main_table_heading_inner"><table width="100%" border="0" cellspacing="0" cellpadding="4">' . "\n";
    echo '                        <tr>' . "\n";
    echo '                          <td class="pageHeading">' . $header . '</td>' . "\n";
    echo '                        </tr>' . "\n";
    echo '                      </table></td>' . "\n";
    echo '                  </tr>' . "\n";
    echo '                </table></td>' . "\n";
    echo '            </tr>' . "\n";
    echo '          </table></td>' . "\n";
    echo '      </tr>' . "\n";
    echo '      <tr>' . "\n";
    echo '        <td>' . tep_draw_separator('pixel_trans.gif', '100%', '10') . '</td>' . "\n";
    echo '      </tr>' . "\n";
}    
    echo '      <tr>' . "\n";
    echo '        <td valign="top" width="100%"><table width="100%" border="0" cellspacing="0" cellpadding="0">' . "\n";
    echo '            <tr>' . "\n";
    echo '              <td class="main_table_heading"><table width="100%" border="0" cellspacing="0" cellpadding="1">' . "\n";
    echo '                  <tr>' . "\n";
    echo '                    <td><table width="100%" border="0" cellspacing="0" cellpadding="1">' . "\n";
    echo '                        <tr>' . "\n";
    echo '                          <td class="main_table_heading_inner"><table width="100%" border="0" cellspacing="0" cellpadding="4">' . "\n";
    echo '<!--table_image_border_top: BOF-->' . "\n";
  }
}

function table_image_border_bottom() {
  if (defined('MAIN_TABLE_BORDER') && (MAIN_TABLE_BORDER == 'yes')) {
    echo '<!-- table_image_border_bottom -->' . "\n";
    echo '                  </table></td>' . "\n";
    echo '                </tr>' . "\n";
    echo '              </table></td>' . "\n";
    echo '            </tr>' . "\n";
    echo '          </table></td>' . "\n";
    echo '        </tr>' . "\n";
    echo '      </table></td>' . "\n";
    echo '    </tr>' . "\n";
    echo '  </table></td>' . "\n";
    echo '</tr>' . "\n";
    echo '<!-- table_image_border_bottom //eof -->' . "\n";
  }
}

function table_image_main_border_top($left, $right, $header){
  if (defined('MAIN_TABLE_BORDER') && (MAIN_TABLE_BORDER == 'yes')) {
    echo '<!-- table_image_main_border_top -->' . "\n";
    echo '  <tr>' . "\n";
    echo '    <td valign="top" width="100%"><table width="100%" border="0" cellspacing="0" cellpadding="0">' . "\n";
    echo '      <tr>' . "\n";
    echo '        <td><table width="100%"  border="0" cellspacing="0" cellpadding="0">' . "\n";
    echo '          <tr>' . "\n";
    echo '            <td><table width="100%" border="0" cellspacing="0" cellpadding="0">' . "\n";
    echo '              <tr>' . "\n";
    echo '                <td><table width="100%" border="0" cellspacing="0" cellpadding="4">' . "\n";
    echo '                  <tr>' . "\n";
    echo '                    <td class="infoBoxHeading">' . $header . '</td>' . "\n";
    echo '                  </tr>' . "\n";
    echo '                </table></td>' . "\n";
    echo '              </tr>' . "\n";
    echo '            </table></td>' . "\n";
    echo '          </tr>' . "\n";
    echo '        </table></td>' . "\n";
    echo '      </tr>' . "\n";
    echo '      <tr>' . "\n";
    echo '        <td valign="top" width="100%"><table width="100%" border="0" cellspacing="0" cellpadding="0">' . "\n";
    echo '          <tr>' . "\n";
    echo '            <td class="main_table_heading"><table width="100%" border="0" cellspacing="0" cellpadding="1">' . "\n";
    echo '              <tr>' . "\n";
    echo '                <td><table width="100%" border="0" cellspacing="0" cellpadding="1">' . "\n";
    echo '                  <tr>' . "\n";
    echo '                    <td class="main_table_heading_inner"><table width="100%" border="0" cellspacing="0" cellpadding="4">' . "\n";
    echo '                      <tr>' . "\n";
    echo '                         <td>' . "\n";
    echo '<!-- table_image_main_border_bottom -->' . "\n";
  }
}

// this function is used to produce a header for modules used as center content.
//it is used with the function table_center_module_footer
function table_center_module_header($header1) {
  echo '<!-- table_center_module_header -->' . "\n";
  echo '<tr>' . "\n";
  echo '  <td>' . "\n";
  echo '    <table border="' . tep_output_string(TEMPLATE_TABLE_BORDER) . '" width="' . tep_output_string(TEMPLATE_TABLE_WIDTH) . '" cellspacing="' . tep_output_string(TEMPLATE_TABLE_CELLSPACING) . '" cellpadding="' . tep_output_string(TEMPLATE_TABLE_CELLPADDIING) . '">' . "\n";
  echo '      <tr>' . "\n";
  echo '        <td height="14" class="infoBoxHeading">' . tep_image(TEMPLATE_BOX_IMAGE_TOP_LEFT,  'image', '', '', 'border=\"0\"') . '</td>' . "\n";
  echo '        <td width="100%" align ="center" height="14" class="infoBoxHeadingImage">' . $header1 . '</td>' . "\n";
  echo '        <td height="14" class="infoBoxHeading" nowrap="nowrap">' . tep_image(TEMPLATE_BOX_IMAGE_TOP_RIGHT,  'image', '', '', 'border=\"0\"') . '</td>' . "\n";
  echo '      </tr>' . "\n";
  echo '    </table>' . "\n";
  echo '    <table border="0" width="100%" cellspacing="0" cellpadding="' . TEMPLATE_TABLE_CENTER_CONTENT_CELLPADING . '" class="templateinfoBox">' . "\n";
  echo '      <tr>' . "\n";
  echo '        <td align="left"  width="'. CELLPADDING_SUB .'" style="background-image: url(\'' . DIR_WS_TEMPLATES . TEMPLATE_NAME . '/images/infobox/box_bg_l.gif\');background-repeat: repeat-y; background-width: ' . CELLPADDING_SUB . ';"></td>' . "\n";
  echo '          <table border="0" width="100%" cellspacing="0" cellpadding="' . TEMPLATE_TABLE_CENTER_CONTENT_CELLPADING . '" >' . "\n";
  echo '            <tr>' . "\n";
  echo '              <td><img src="images/pixel_trans.gif" border="0" alt="" width="100%" height="1"></td>' . "\n";
  echo '            </tr>' . "\n";
  echo '            <!-- table_center_module_header //eof-->' . "\n";
}

function table_center_module_footer() {
  echo '            <!-- table_center_module_footer -->' . "\n";
  echo '          </table>' . "\n";
  echo '        </td>' . "\n";
  echo '        <td width="'.SIDE_BOX_RIGHT_WIDTH.'" style="background-image: url(\'' . DIR_WS_TEMPLATES . TEMPLATE_NAME . '/images/infobox/box_bg_r.gif\');background-repeat: repeat-y;"></td>' . "\n"; 
  echo '      </tr>' . "\n";
  echo '    </table>' . "\n";
  if (defined('TEMPLATE_INCLUDE_FOOTER') && (TEMPLATE_INCLUDE_FOOTER == 'true')) {
    echo '    <table border="0" width="100%" cellspacing="0" cellpadding="0">' . "\n";
    echo '      <tr>' . "\n";
    echo '        <td class="infoBoxFooter"><img src="<?php echo TEMPLATE_BOX_IMAGE_FOOT_LEFT ;?>" border="0" alt=""></td>' . "\n";
    echo '        <td  align="center" style="background-image: url(' . TEMPLATE_BOX_IMAGE_FOOT_BACKGROUND .'); background-repeat: repeat-x; background-position: right;" width="100%"  class="infoBoxFooterImage"><img src="images/pixel_trans.gif" border="0" alt="image" width="100%" height="1"></td>' . "\n";
    echo '        <td class="infoBoxFooter"><img src="<?php echo TEMPLATE_BOX_IMAGE_FOOT_RIGHT ;?>" border="0" alt="image" ></td>' . "\n";
    echo '      </tr>' . "\n";
    echo '    </table>' . "\n";
  }
  echo '  </td>' . "\n";
  echo '</tr>' . "\n";
  echo '<!-- table_center_module_footer -->' . "\n";
}

?>