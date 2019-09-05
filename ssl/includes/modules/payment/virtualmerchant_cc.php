<?php
/*
  $Id: virtualmerchant.php,v 1.02 2007/12/07 roberto Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 Robertas Dereskevicius <roberto@mikrolineage.lt>
*/

  class virtualmerchant_cc {
    var $code, $title, $description, $enabled;

// class constructor
    function virtualmerchant_cc() {
      global $order;

      $this->code = 'virtualmerchant_cc';
      $this->title = MODULE_PAYMENT_VIRTUALMERCHANT_CC_TEXT_TITLE;
      $this->public_title = MODULE_PAYMENT_VIRTUALMERCHANT_CC_TEXT_PUBLIC_TITLE;
      $this->description = MODULE_PAYMENT_VIRTUALMERCHANT_CC_TEXT_DESCRIPTION;
      $this->sort_order = MODULE_PAYMENT_VIRTUALMERCHANT_CC_SORT_ORDER;
      $this->enabled = ((MODULE_PAYMENT_VIRTUALMERCHANT_CC_STATUS == 'True') ? true : false);

      if ((int)MODULE_PAYMENT_VIRTUALMERCHANT_CC_ORDER_STATUS_ID > 0) {
        $this->order_status = MODULE_PAYMENT_VIRTUALMERCHANT_CC_ORDER_STATUS_ID;
      }

      if (is_object($order)) $this->update_status();
    }

// class methods
    function update_status() {
      global $order;

      if ( ($this->enabled == true) && ((int)MODULE_PAYMENT_VIRTUALMERCHANT_CC_ZONE > 0) ) {
        $check_flag = false;
        $check_query = tep_db_query("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . MODULE_PAYMENT_VIRTUALMERCHANT_CC_ZONE . "' and zone_country_id = '" . $order->billing['country']['id'] . "' order by zone_id");
        while ($check = tep_db_fetch_array($check_query)) {
          if ($check['zone_id'] < 1) {
            $check_flag = true;
            break;
          } elseif ($check['zone_id'] == $order->billing['zone_id']) {
            $check_flag = true;
            break;
          }
        }

        if ($check_flag == false) {
          $this->enabled = false;
        }
      }
    }

    function javascript_validation() {
      return false;
    }

    function selection() {
    for ($i=1; $i<13; $i++) {
        $expires_month[] = array('id' => sprintf('%02d', $i), 'text' => strftime('%B',mktime(0,0,0,$i,1,2000)));
      }

      $today = getdate(); 
      for ($i=$today['year']; $i < $today['year']+10; $i++) {
        $expires_year[] = array('id' => strftime('%y',mktime(0,0,0,1,1,$i)), 'text' => strftime('%Y',mktime(0,0,0,1,1,$i)));
      }
	 $selection = array('id' => $this->code,
                   'module' => $this->public_title . " (We currently accept " . MODULE_PAYMENT_VIRTUALMERCHANT_CC_ACCEPTED_CARDS . ")",
                         'fields' => array(array('title' => MODULE_PAYMENT_VIRTUALMERCHANT_CC_CREDIT_CARD_OWNER,
                                                    'field' => tep_draw_input_field('virtualmerchant_cc_owner', $order->billing['firstname'] . ' ' . $order->billing['lastname'])),
                                              array('title' => MODULE_PAYMENT_VIRTUALMERCHANT_CC_CREDIT_CARD_NUMBER,
                                                    'field' => tep_draw_input_field('virtualmerchant_cc_number_nh-dns')),
                                              array('title' => MODULE_PAYMENT_VIRTUALMERCHANT_CC_CREDIT_CARD_EXPIRES,
                                                    'field' => tep_draw_pull_down_menu('virtualmerchant_cc_expires_month', $expires_month) . '&nbsp;' . tep_draw_pull_down_menu('virtualmerchant_cc_expires_year', $expires_year))));
    
	if (MODULE_PAYMENT_VIRTUALMERCHANT_CC_VERIFY_WITH_CVC == 'True') {
        $selection['fields'][] = array('title' => MODULE_PAYMENT_VIRTUALMERCHANT_CC_CREDIT_CARD_CVC,
                                          'field' => tep_draw_input_field('virtualmerchant_cc_cvc_nh-dns', '', 'size="5" maxlength="4"'));
      }
	
	return $selection;
	}

    function pre_confirmation_check() {
      global $_POST;
	  //$this->cc_card_type = $cc_validation->cc_type;
      $this->cc_card_owner = $_POST['virtualmerchant_cc_owner'];
	  $this->cc_card_number = $_POST['virtualmerchant_cc_number_nh-dns'];
      $this->cc_expiry_month = $_POST['virtualmerchant_cc_expires_month'];
      $this->cc_expiry_year = $_POST['virtualmerchant_cc_expires_year'];
	   if (MODULE_PAYMENT_VIRTUALMERCHANT_CC_VERIFY_WITH_CVC == 'True') {
        $this->cc_cvc = $_POST['virtualmerchant_cc_cvc_nh-dns'];
		
      }
    }

    function confirmation() {
      global $order;
	  global $HTTP_POST_VARS;
	  
      for ($i=1; $i<13; $i++) {
        $expires_month[] = array('id' => sprintf('%02d', $i), 'text' => strftime('%B',mktime(0,0,0,$i,1,2000)));
      }

      $today = getdate(); 
      for ($i=$today['year']; $i < $today['year']+10; $i++) {
        $expires_year[] = array('id' => strftime('%y',mktime(0,0,0,1,1,$i)), 'text' => strftime('%Y',mktime(0,0,0,1,1,$i)));
      }

      $confirmation = array('fields' => array(array('title' => MODULE_PAYMENT_VIRTUALMERCHANT_CC_CREDIT_CARD_OWNER,
                                                    'field' => $HTTP_POST_VARS['virtualmerchant_cc_owner']),
                                              array('title' => MODULE_PAYMENT_VIRTUALMERCHANT_CC_CREDIT_CARD_NUMBER,
                                                    'field' => substr($this->cc_card_number, 0, 4) . str_repeat('X', (strlen($this->cc_card_number) - 8)) . substr($this->cc_card_number, -4)),
                                              array('title' => MODULE_PAYMENT_VIRTUALMERCHANT_CC_CREDIT_CARD_EXPIRES,
                                                    'field' => strftime('%B, %Y', mktime(0,0,0,$HTTP_POST_VARS['virtualmerchant_cc_expires_month'], 1, '20' . $HTTP_POST_VARS['virtualmerchant_cc_expires_year'])))));

      if (MODULE_PAYMENT_VIRTUALMERCHANT_CC_VERIFY_WITH_CVC == 'True') {
        $confirmation['fields'][] = array('title' => MODULE_PAYMENT_VIRTUALMERCHANT_CC_CREDIT_CARD_CVC,
                                          'field' => $HTTP_POST_VARS['virtualmerchant_cc_cvc_nh-dns']);
      }

      return $confirmation;
    }

    function process_button() {
      global $HTTP_POST_VARS, $order, $customer_id;
	  $process_button_string = tep_draw_hidden_field('virtualmerchant_cc_number_nh-dns', $HTTP_POST_VARS['virtualmerchant_cc_number_nh-dns']) .
                               tep_draw_hidden_field('virtualmerchant_cc_expires_month', $HTTP_POST_VARS['virtualmerchant_cc_expires_month']) .
                               tep_draw_hidden_field('virtualmerchant_cc_expires_year', $HTTP_POST_VARS['virtualmerchant_cc_expires_year']);
							   
	if (MODULE_PAYMENT_VIRTUALMERCHANT_CC_VERIFY_WITH_CVC == 'True') {
        $process_button_string .= tep_draw_hidden_field('virtualmerchant_cc_cvc_nh-dns',$_POST['virtualmerchant_cc_cvc_nh-dns']);
      }
	  return $process_button_string;
    }

    function before_process() {
      global $customer_id, $order, $_POST;
$getmax = tep_db_query("select orders_id from " . TABLE_ORDERS . " order by orders_id desc limit 1 ");
$maxid = tep_db_fetch_array($getmax);
$this_order_id = $maxid['orders_id']+1;
      $this->pre_confirmation_check();
//      phpinfo();
//      print_r($HTTP_POST_VARS)."<br>";//exit;
      $params = array('ssl_transaction_type' => 'CCSALE',
                      'ssl_merchant_id' => MODULE_PAYMENT_VIRTUALMERCHANT_CC_ACCOUNT_ID,
                      'ssl_user_id' => MODULE_PAYMENT_VIRTUALMERCHANT_CC_USER_ID,
                      'ssl_amount' => round($order->info['total'],2),
                      'ssl_card_number' => str_replace("+","",str_replace("-","",str_replace(" " ,"",$_POST['virtualmerchant_cc_number_nh-dns']))),
                      'ssl_exp_date' => $_POST['virtualmerchant_cc_expires_month'].$_POST['virtualmerchant_cc_expires_year'],
                      'ssl_company' => $order->billing['company'],
                      'ssl_avs_address' => substr($order->billing['street_address'],0,20),
					  'ssl_address2' => substr($order->billing['street_address'],20,30),
                      'ssl_city' => $order->billing['city'],
                      'ssl_state' => $order->billing['state'],
                      'ssl_avs_zip' => str_replace("+","",str_replace("-","",str_replace(" " ,"",$order->billing['postcode']))),
                      'ssl_country' => $order->billing['country']['iso_code_3'],
                      'ssl_ship_to_address1' => substr($order->delivery['street_address'],0,30),
					  'ssl_ship_to_address2' => substr($order->delivery['street_address'],30,30),
                      'ssl_ship_to_city' => $order->delivery['city'],
                      'ssl_ship_to_state' => $order->delivery['state'],
                      'ssl_ship_to_zip' => str_replace("+","",str_replace("-","",str_replace(" " ,"",$order->delivery['postcode']))),
                      'ssl_ship_to_country' => $order->delivery['country']['iso_code_3'],
                      'ssl_phone' => $order->customer['telephone'],
                      'ssl_email' => $order->customer['email_address'],
		      		  'ssl_result_format' => "ASCII",
                      'ssl_show_form' => 'false',
                      'email_from' => STORE_OWNER_EMAIL_ADDRESS,
                      'ssl_description' => "from ip:".tep_get_ip_address().", cc owner:".$_POST['virtualmerchant_cc_owner'],
					  'ssl_customer_code' => $customer_id,
					  'ssl_salestax' => '0',
					  'ssl_first_name' => substr($order->billing['firstname'], 0, 20),
					  'ssl_last_name' => substr($order->billing['lastname'], 0, 30),
					  'ssl_salestax' => '0',
                      'ssl_invoice_number' => $this_order_id);

      if (MODULE_PAYMENT_VIRTUALMERCHANT_TESTMODE == 'Test') {
        $params['ssl_test_mode'] = "TRUE";
      }

      if (MODULE_PAYMENT_VIRTUALMERCHANT_CC_VERIFY_WITH_CVC == 'True') {
        $params['ssl_cvv2cvc2_indicator'] = 'present';
        //bug fixed caused by leading 0
		$params['ssl_cvv2cvc2'] = $_POST['virtualmerchant_cc_cvc_nh-dns'];
      }

      if (tep_not_null(MODULE_PAYMENT_VIRTUALMERCHANT_CC_MERCHANT_PIN)) {
        $params['ssl_pin'] = MODULE_PAYMENT_VIRTUALMERCHANT_CC_MERCHANT_PIN;
      }

      $post_string = '';
//print_r($params);exit();
      reset($params);
      while (list($key, $value) = each($params)) {
        $post_string .= $key . '=' . urlencode(trim($value)) . '&';
      }

    // print_r($params);exit;
      $post_string = substr($post_string, 0, -1);

//	print 123;exit;
      $transaction_response = $this->sendTransactionToGateway('https://www.myvirtualmerchant.com/VirtualMerchant/process.do', $post_string);

//      print $transaction_response;exit;
      $error = false;

//session var for checkout_success
$sessionccnum = substr($_POST['virtualmerchant_cc_number_nh-dns'],0,2) . "XXXXXXXXXX" .  substr($_POST['virtualmerchant_cc_number_nh-dns'],-4);
$_SESSION['ccnumchecksuccess']= $sessionccnum;
//session var for checkout_success ends

      if (!empty($transaction_response)) {
        $regs = explode("\n", trim($transaction_response));
        array_shift($regs);

        $result = array();

        reset($regs);
        while (list($key, $value) = each($regs)) {
          $res = explode('=', $value, 2);

          $result[strtolower(trim($res[0]))] = trim($res[1]);
        }

	//print_r($result);exit;
        if ($result['ssl_result'] != '0') {
          $error = explode(':', $result['reason'], 3);
          $error = $error[2];
		  
 		if (empty($error) && $result['ssl_result_message']!='APPROVED') {
            $error = $result['ssl_result_message'].' '.$transaction_response;
          }
		  if (empty($error) && $result['errorMessage']!='') {
            $error = $result['errorMessage'].'  '.$transaction_response;
          }
          if (empty($error)) {
            $error = MODULE_PAYMENT_VIRTUALMERCHANT_CC_ERROR_GENERAL.'   '.$transaction_response;
          }
        }
      } else {
        $error = MODULE_PAYMENT_VIRTUALMERCHANT_CC_ERROR_GENERAL.'    '.$transaction_response;
      }

      if ($error) {
        tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, 'payment_error=' . $this->code . '&error=' . urlencode($error), 'SSL'));
      }
    }

    function after_process() {
      return false;
    }

    function get_error() {
      global $HTTP_GET_VARS;

      $error = array('title' => MODULE_PAYMENT_VIRTUALMERCHANT_CC_TEXT_ERROR,
                     'error' => stripslashes(urldecode($HTTP_GET_VARS['error'])));

      return $error;
    }

    function check() {
      if (!isset($this->_check)) {
        $check_query = tep_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_PAYMENT_VIRTUALMERCHANT_CC_STATUS'");
        $this->_check = tep_db_num_rows($check_query);
      }
      return $this->_check;
    }

    function install() {
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable VirtualMerchant Credit Card Module', 'MODULE_PAYMENT_VIRTUALMERCHANT_CC_STATUS', 'False', 'Do you want to accept VirtualMerchant credit card payments?', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Account ID', 'MODULE_PAYMENT_VIRTUALMERCHANT_CC_ACCOUNT_ID', '', 'The account ID of the VirtualMerchant account to use.', '6', '0', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('User ID', 'MODULE_PAYMENT_VIRTUALMERCHANT_CC_USER_ID', '', 'The user ID of the VirtualMerchant account to use.', '6', '0', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Transaction Mode', 'MODULE_PAYMENT_VIRTUALMERCHANT_TESTMODE', 'Test', 'Transaction mode used for the VirtualMerchant service', '6', '0', 'tep_cfg_select_option(array(\'Test\', \'Production\'), ', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Merchant PIN', 'MODULE_PAYMENT_VIRTUALMERCHANT_CC_MERCHANT_PIN', '', 'Use this Merchant PIN if it is enabled on the VirtualMerchant Online Merchant Center.', '6', '0', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Credit Cards Accepted', 'MODULE_PAYMENT_VIRTUALMERCHANT_CC_ACCEPTED_CARDS', '', 'Enter all credit cards that you accept at the moment comma separated. for eg.(Mastercard, Visa, Discover and Amex.)', '6', '0', now())");
	  tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Verify With CVC', 'MODULE_PAYMENT_VIRTUALMERCHANT_CC_VERIFY_WITH_CVC', 'True', 'Verify the credit card with the billing address with the Credit Card Verification Checknumber (CVC)?', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Payment Zone', 'MODULE_PAYMENT_VIRTUALMERCHANT_CC_ZONE', '0', 'If a zone is selected, only enable this payment method for that zone.', '6', '2', 'tep_get_zone_class_title', 'tep_cfg_pull_down_zone_classes(', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added) values ('Set Order Status', 'MODULE_PAYMENT_VIRTUALMERCHANT_CC_ORDER_STATUS_ID', '0', 'Set the status of orders made with this payment module to this value', '6', '0', 'tep_cfg_pull_down_order_statuses(', 'tep_get_order_status_name', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort order of display.', 'MODULE_PAYMENT_VIRTUALMERCHANT_CC_SORT_ORDER', '0', 'Sort order of display. Lowest is displayed first.', '6', '0' , now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('cURL Program Location', 'MODULE_PAYMENT_VIRTUALMERCHANT_CC_CURL', '/usr/bin/curl', 'The location to the cURL program application.', '6', '0' , now())");
    }

    function remove() {
      tep_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      return array('MODULE_PAYMENT_VIRTUALMERCHANT_CC_STATUS', 'MODULE_PAYMENT_VIRTUALMERCHANT_CC_ACCOUNT_ID', 'MODULE_PAYMENT_VIRTUALMERCHANT_CC_USER_ID', 'MODULE_PAYMENT_VIRTUALMERCHANT_CC_MERCHANT_PIN', 'MODULE_PAYMENT_VIRTUALMERCHANT_CC_ACCEPTED_CARDS', 'MODULE_PAYMENT_VIRTUALMERCHANT_TESTMODE', 'MODULE_PAYMENT_VIRTUALMERCHANT_CC_VERIFY_WITH_CVC', 'MODULE_PAYMENT_VIRTUALMERCHANT_CC_ZONE', 'MODULE_PAYMENT_VIRTUALMERCHANT_CC_ORDER_STATUS_ID', 'MODULE_PAYMENT_VIRTUALMERCHANT_CC_SORT_ORDER', 'MODULE_PAYMENT_VIRTUALMERCHANT_CC_CURL');
    }

    function sendTransactionToGateway($url, $parameters) {
      $server = parse_url($url);

      if (isset($server['port']) === false) {
        $server['port'] = ($server['scheme'] == 'https') ? 443 : 80;
      }

      if (isset($server['path']) === false) {
        $server['path'] = '/';
      }

      if (isset($server['user']) && isset($server['pass'])) {
        $header[] = 'Authorization: Basic ' . base64_encode($server['user'] . ':' . $server['pass']);
      }

      $connection_method = 0;

      if (function_exists('curl_init')) {
        $curl = curl_init($server['scheme'] . '://' . $server['host'] . $server['path'] . (isset($server['query']) ? '?' . $server['query'] : ''));
        curl_setopt($curl, CURLOPT_PORT, $server['port']);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_FORBID_REUSE, 1);
        curl_setopt($curl, CURLOPT_FRESH_CONNECT, 1);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $parameters);

        $result = curl_exec($curl);
//echo $result;
//die();
        curl_close($curl);
      } else {
        exec(escapeshellarg(MODULE_PAYMENT_VIRTUALMERCHANT_CC_CURL) . ' -d ' . escapeshellarg($parameters) . ' "' . $server['scheme'] . '://' . $server['host'] . $server['path'] . (isset($server['query']) ? '?' . $server['query'] : '') . '" -P ' . $server['port'] . ' -k', $result);
        $result = implode("\n", $result);
      }

      return $result;
    }
  }
?>