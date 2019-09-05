<?php
/*
  $Id: cresecure_orders_sidebarbuttons.php,v 1.0 2009/04/09 13:41:11 datazen Exp $

  CRE Loaded, Open Source E-Commerce Solutions
  http://www.creloaded.com

  Copyright (c) 2009 CRE Loaded
  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/
global $oInfo, $language;

if (isset($oInfo->payment_method) && $oInfo->orders_status_name == 'Preparing [CRE Secure]') {
  $rci = '';
  // re-create order object
  require_once(DIR_WS_CLASSES . 'order.php');  
  if ($oInfo->orders_id == '') return;
  $order_id = $oInfo->orders_id;
  $order = new order($order_id);  
  $username = (defined('MODULE_PAYMENT_CRESECURE_LOGIN')) ? MODULE_PAYMENT_CRESECURE_LOGIN : '';
  $password = (defined('MODULE_PAYMENT_CRESECURE_PASS')) ? MODULE_PAYMENT_CRESECURE_PASS : '';                       
  $test = (defined('MODULE_PAYMENT_CRESECURE_TEST_MODE') && MODULE_PAYMENT_CRESECURE_TEST_MODE == 'True') ? 1 : 0;                             
  $branded_url = (file_exists('checkout_payment_template.php')) ? 'checkout_payment_template.php' : 'default';   
  $content_template_url = '';
  $allowed_types = (defined('MODULE_PAYMENT_CRESECURE_ACCEPTED_CC')) ? str_replace(', ', '|', MODULE_PAYMENT_CRESECURE_ACCEPTED_CC) : '';
  if (defined('MODULE_PAYMENT_CRESECURE_TEST_MODE') && MODULE_PAYMENT_CRESECURE_TEST_MODE == 'True') {
    $form_action_url = 'https://cregateway.net/securepayments/a1/cc_collection.php';          
  } else {
    $form_action_url = 'https://cresecure.net/securepayments/a1/cc_collection.php';          
  }   
  // calculate total weight
  $total_weight = 0;
  for ($i=0; $i<sizeof($order->products); $i++) {
    if (isset($order->products[$i]['weight'])) {
      $total_weight = $total_weight + ((int)$order->products[$i]['qty'] * (float)$order->products[$i]['weight']);
    }
  }
  $rci .= '<form action="' . $form_action_url . '" method="post" target="Details" onSubmit="return popWindow(this.target)">' .  
          tep_draw_hidden_field('CREMerchID', $username) . 
          tep_draw_hidden_field('CREMerchPass', $password) .
          tep_draw_hidden_field('postal_code', $order->billing['postcode']) .
          tep_draw_hidden_field('street', $order->billing['street_address']) .
          tep_draw_hidden_field('total_amt', number_format($order->info['total_value'], 2)) .
          tep_draw_hidden_field('customer_email', $order->customer['email_address']) .
          tep_draw_hidden_field('customer_phone', $order->customer['telephone']) .
          tep_draw_hidden_field('total_weight', $total_weight) .
          tep_draw_hidden_field('order_num', $order_id) .
          tep_draw_hidden_field('order_id', $order_id) .
          tep_draw_hidden_field('currency_code', $order->info['currency']) .
          tep_draw_hidden_field('lang', $language) .
          tep_draw_hidden_field('allowed_types', $allowed_types) .
          tep_draw_hidden_field('branded_template', $branded_url) .
          tep_draw_hidden_field('test', $test) .
          tep_draw_hidden_field('oscsid', tep_session_id()) .
          tep_draw_hidden_field('return_url', tep_href_link('cresecure_payment.php', '', 'SSL', false, false)) .
          tep_draw_hidden_field('content_template_url', $content_template_url);
  $rci .= tep_image_submit('button_complete_payment.gif', IMAGE_COMPLETE_PAYMENT);
  $rci .= '</form>' . "\n";  
}
?>
<script language="javascript">
function popWindow(wName){
features = 'width=550,height=470,toolbar=no,location=no,directories=no,menubar=no,scrollbars=no,copyhistory=no,resizable=no';
pop = window.open('',wName,features);
if(pop.focus){ pop.focus(); }
return true;
}
</script>