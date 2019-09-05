<?php
/*
  $Id: paypalwpp_checkoutshipping_top.php,v 1.0.0.0 2007/11/13 13:41:11 datazen Exp $

  CRE Loaded, Open Source E-Commerce Solutions
  http://www.creloaded.com

  Copyright (c) 2007 CRE Loaded
  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License

*/

global $_GET, $_POST, $comments, $_SESSION;

if ( defined('MODULE_PAYMENT_PAYPAL_WPP_STATUS') && MODULE_PAYMENT_PAYPAL_WPP_STATUS == 'True' && isset($_GET['error']) && tep_not_null($_GET['error']) ) {
  echo '<div class="messageStackError">' . urldecode($_GET['error']) . '</div>';
}

if (defined('MODULE_PAYMENT_PAYPAL_WPP_STATUS') && MODULE_PAYMENT_PAYPAL_WPP_STATUS == 'True' && isset($_POST['error']) && tep_not_null($_POST['error'])) {
  if (!isset($_SESSION['comments']))
  {
    $_SESSION['comments'] = tep_db_prepare_input($_POST['comments']);
  }
}

?>
