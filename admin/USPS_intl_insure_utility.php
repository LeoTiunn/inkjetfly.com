<?php
/*
  $Id: USPS_intl_insure_utility.php ver 1.0 by Kevin L. Shelton 2011-08-20
  
  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2009 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');
if (PHP_VERSION >= '5.0.0') { // PHP 5 does not need to use call-time pass by reference
  require_once ('includes/classes/xml_5.php');
} else {
  require_once ('includes/classes/xml.php');
}
if ( !function_exists('htmlspecialchars_decode') ) {
  function htmlspecialchars_decode($text) {
    return strtr($text, array_flip(get_html_translation_table(HTML_SPECIALCHARS)));
  }
}
  $action = (isset($HTTP_GET_VARS['action']) ? $HTTP_GET_VARS['action'] : '');
  $id = (isset($HTTP_POST_VARS['id']) ? tep_db_prepare_input($HTTP_POST_VARS['id']) : '');
  if (defined('MODULE_SHIPPING_USPS_USERID') && (MODULE_SHIPPING_USPS_USERID != 'NONE')) {
    $action = 'process';
    $id = MODULE_SHIPPING_USPS_USERID;
  }

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta name="robots" content="noindex, nofollow">
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title>USPS International Insurance Utility</title>
<link rel="stylesheet" type="text/css" href=includes/stylesheet.css>
<script type="text/javascript" src=includes/general.js></script>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<!-- body //-->
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <td width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
<!-- left_navigation //-->
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- left_navigation_eof //-->
    </table></td>
<!-- body_text //-->
    <td width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td width="100%"><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading">Utility to get USPS maximum insurance for all countries and all international shipping methods</td>
            <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td>
      <?php if ($action != 'process') {
      echo tep_draw_form('id_entry', 'USPS_methods_utility', 'action=process', 'post');
      echo '<p>Enter the USPS USERID assigned to you. You must contact USPS to have them switch you to the Production server. Otherwise this module will not work! ' . tep_draw_input_field('id') . "</p>\n";
      echo tep_image_submit('button_save.gif', IMAGE_SAVE) . '&nbsp;&nbsp;<a href="' . tep_href_link(FILENAME_DEFAULT, 'eid=' . $eid) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . "</a>\n";
      ?>
      </form></td></tr>
<?php } else { // get USPS method values
?>
      <tr>
        <td>Preparing to recreate USPS international maximum international insurance database file.</td>
      </tr>
      <tr>
        <td class="main"><p></p><p></p>
<?php

if (!file_exists(DIR_FS_CATALOG_MODULES . 'shipping/usps.php')) {
  echo '<h1>ERROR! USPS shipping module must be installed before this utility can be used!</h1>';
  exit;
} else {
 include(DIR_FS_CATALOG_MODULES . 'shipping/usps.php');
}

$usps = new usps();

function decode_response($svc) {
  $id = str_replace(array('<sup>' , '&reg;', '</sup>', '&trade;'), '', htmlspecialchars_decode($svc['SvcDescription']));
  if (isset($svc['InsComment'])) {
    if ($svc['InsComment'] == 'INSURED VALUE') {
      $insurable = true;
      $valid = false;
    } else {
      $insurable = false;
      $valid = true;
    }
  } else {
    $insurance_found = false;
    $valid = true;
    if (isset($svc['ExtraServices']['ExtraService'])) {
      if (isset($svc['ExtraServices']['ExtraService'][0])) { // multiple extras returned
        foreach ($svc['ExtraServices']['ExtraService'] as $extra) {
          if (($extra['ServiceName'] == 'Insurance') && ($extra['Available'] == 'True')) $insurance_found = true;
        }
      } elseif (isset($svc['ExtraServices']['ExtraService']['ServiceName'])) { //single extra returned
        $insurance_found = (($svc['ExtraServices']['ExtraService']['ServiceName'] == 'Insurance') && ($svc['ExtraServices']['ExtraService']['Available'] == 'True'));
      }
    }
    $insurable = $insurance_found;
  }
  return array('id' => $id, 'insurable' => $insurable, 'valid' => $valid);
}

tep_db_query("create table if not exists USPS_intl_maxins (country_code char(2) not null, method varchar(128) not null, insurable tinyint(1) not null default 0, max_insurance smallint unsigned not null default 0, last_modified datetime not null, primary key (country_code, method))");

$tocheck = array();
// if you only need to redo some countries uncomment the line below and enter the needed ISO codes
//$tocheck = array('LY', 'KN', 'SO', 'SS', 'TK');

set_time_limit(300);
foreach ($usps->countries as $code => $cname) {
  if (!empty($tocheck) && !in_array($code, $tocheck)) continue; // skip country if check specific countries and this one isn't in list
  $pkgvalue = 5000; //start with the maximum insurance USPS will do for any country
  echo '<p>Checking maximum insurance values for ' . $cname . ".<br>\n";
  $methods = array();
  do {
    // international requires two packages to be sure of getting all shipping methods
    $request  = '<IntlRateV2Request USERID="' . $id . '"><Revision>2</Revision><Package ID="1st"><Pounds>10</Pounds><Ounces>10</Ounces><Machinable>true</Machinable><MailType>All</MailType><GXG><POBoxFlag>N</POBoxFlag><GiftFlag>N</GiftFlag></GXG><ValueOfContents>' . $pkgvalue . '</ValueOfContents><Country>' . $cname . '</Country><Container>NONRECTANGULAR</Container><Size>LARGE</Size><Width>12</Width><Length>16</Length><Height>3</Height><Girth>20</Girth><ExtraServices><ExtraService>1</ExtraService></ExtraServices></Package>';
    $request  .= '<Package ID="2nd"><Pounds>0</Pounds><Ounces>1</Ounces><Machinable>true</Machinable><MailType>All</MailType><GXG><POBoxFlag>N</POBoxFlag><GiftFlag>N</GiftFlag></GXG><ValueOfContents>' . $pkgvalue . '</ValueOfContents><Country>' . $cname . '</Country><Container>RECTANGULAR</Container><Size>REGULAR</Size><Width>3</Width><Length>5</Length><Height>0.1</Height><Girth>6.2</Girth><ExtraServices><ExtraService>1</ExtraService></ExtraServices></Package></IntlRateV2Request>';
    $request = 	'API=IntlRateV2&XML=' . urlencode($request);
    $usps_server = 'production.shippingapis.com';
    $api_dll = 'shippingAPI.dll';
    $body = '';
    if (!class_exists('httpClient')) {
      include(DIR_FS_CATALOG . 'includes/classes/http_client.php');
    }
    $http = new httpClient();
    if ($http->Connect($usps_server, 80)) {
      $http->addHeader('Host', $usps_server);
    	$http->addHeader('User-Agent', 'osCommerce');
    	$http->addHeader('Connection', 'Close');
  	  if ($http->Get('/' . $api_dll . '?' . $request)) $body = $http->getBody();
      $http->Disconnect();
    } else {
      $body = '<error>Connection Failed</error>';
    }
    $doc = XML_unserialize ($body);
    if (isset($doc['IntlRateV2Response']['Package']['Service']['SvcDescription'])) { // single mail service response
      $tmp = decode_response($doc['IntlRateV2Response']['Package']['Service']);
      if (isset($methods[$tmp['id']])) {
        if ($tmp['insurable']) {
          if ($tmp['valid']) {
            if ($methods[$tmp['id']]['minvalid'] < $pkgvalue) $methods[$tmp['id']]['minvalid'] = $pkgvalue;
          } else {
            if ($methods[$tmp['id']]['maxinvalid'] > $pkgvalue) $methods[$tmp['id']]['maxinvalid'] = $pkgvalue;
          }
        }
      } else {
        $methods[$tmp['id']] = $tmp;
        $methods[$tmp['id']]['maxinvalid'] = $pkgvalue;
        $methods[$tmp['id']]['minvalid'] = 0;
        if ($tmp['insurable'] && $tmp['valid']) $methods[$tmp['id']]['minvalid'] = $pkgvalue;
      }
    } elseif (isset($doc['IntlRateV2Response']['Package']['Service'][0])) { // multiple mail service response single package
      foreach($doc['IntlRateV2Response']['Package']['Service'] as $key => $value) {
        if (isset($value['SvcDescription'])) { // not class id attribute for postage
          $tmp = decode_response($value);
          if (isset($methods[$tmp['id']])) {
            if ($tmp['insurable']) {
              if ($tmp['valid']) {
                if ($methods[$tmp['id']]['minvalid'] < $pkgvalue) $methods[$tmp['id']]['minvalid'] = $pkgvalue;
              } else {
                if ($methods[$tmp['id']]['maxinvalid'] > $pkgvalue) $methods[$tmp['id']]['maxinvalid'] = $pkgvalue;
              }
            }
          } else {
            $methods[$tmp['id']] = $tmp;
            $methods[$tmp['id']]['maxinvalid'] = $pkgvalue;
            $methods[$tmp['id']]['minvalid'] = 0;
            if ($tmp['insurable'] && $tmp['valid']) $methods[$tmp['id']]['minvalid'] = $pkgvalue;
          }
        }
      }
    } elseif (isset($doc['IntlRateV2Response']['Package'][0])) { // multiple package service response
      foreach ($doc['IntlRateV2Response']['Package'] as $pkg) {
        if (isset($pkg['Service']['SvcDescription'])) { // single mail service response for package
          $tmp = decode_response($pkg['Service']);
          if (isset($methods[$tmp['id']])) {
            if ($tmp['insurable']) {
              if ($tmp['valid']) {
                if ($methods[$tmp['id']]['minvalid'] < $pkgvalue) $methods[$tmp['id']]['minvalid'] = $pkgvalue;
              } else {
                if ($methods[$tmp['id']]['maxinvalid'] > $pkgvalue) $methods[$tmp['id']]['maxinvalid'] = $pkgvalue;
              }
            }
          } else {
            $methods[$tmp['id']] = $tmp;
            $methods[$tmp['id']]['maxinvalid'] = $pkgvalue;
            $methods[$tmp['id']]['minvalid'] = 0;
            if ($tmp['insurable'] && $tmp['valid']) $methods[$tmp['id']]['minvalid'] = $pkgvalue;
          }
        } elseif (isset($pkg['Service'][0])) { // multiple mail service response
          foreach($pkg['Service'] as $key => $value) {
            if (isset($value['SvcDescription'])) { // not class id attribute for postage
              $tmp = decode_response($value);
              if (isset($methods[$tmp['id']])) {
                if ($tmp['insurable']) {
                  if ($tmp['valid']) {
                    if ($methods[$tmp['id']]['minvalid'] < $pkgvalue) $methods[$tmp['id']]['minvalid'] = $pkgvalue;
                  } else {
                    if ($methods[$tmp['id']]['maxinvalid'] > $pkgvalue) $methods[$tmp['id']]['maxinvalid'] = $pkgvalue;
                  }
                }
              } else {
                $methods[$tmp['id']] = $tmp;
                $methods[$tmp['id']]['maxinvalid'] = $pkgvalue;
                $methods[$tmp['id']]['minvalid'] = 0;
                if ($tmp['insurable'] && $tmp['valid']) $methods[$tmp['id']]['minvalid'] = $pkgvalue;
              }
            }
          }
        }
      }
    }
    $invalid_methods = array();
    foreach ($methods as $method) {
      if ($method['insurable'] && (($method['maxinvalid'] - $method['minvalid']) > 1)) $invalid_methods[] = $method;
    }
    if (!empty($invalid_methods)) $pkgvalue = $invalid_methods[0]['minvalid'] + ceil(($invalid_methods[0]['maxinvalid'] - $invalid_methods[0]['minvalid']) / 2);
  } while (!empty($invalid_methods));
  tep_db_query("delete from USPS_intl_maxins where country_code = '" . tep_db_input($code) . "'");
  foreach ($methods as $service) {
    $data_array = array('country_code' => $code,
      'method' => $service['id'],
      'insurable' => ($service['insurable'] ? 1 : 0),
      'max_insurance' => $service['minvalid'],
      'last_modified' => 'now()');
    tep_db_perform('USPS_intl_maxins', $data_array);
    echo $service['id'];
    if ($service['insurable']) {
      echo ' insurable for up to $' . $service['minvalid'];
    } else {
      echo ' not insurable';
    }
    echo '<br>';
  }
} // end //foreach $usps->countries
?>
            </td>
          </tr>
        </table></td>
      </tr>
      <?php } ?>
    </table></td>
<!-- body_text_eof //-->
  </tr>
</table>
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
<br>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>