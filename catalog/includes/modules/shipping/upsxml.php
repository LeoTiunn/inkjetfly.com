<?php
/*
  $Id: upsxml.php,v 1.00 2003/06/19 10:00:00 torinwalker Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

/*
  Written by Torin Walker.
  
  Please forgive the XML kludge. My host's php configuration doesn't include
  DOM XML, so I had to write my own. Feel free to replace it with something
  better, but leave me the option!
  
  Some code/style borrowed from both Fritz Clapp's UPS Choice 1.7 Module,
  and Kelvin, Kenneth, and Tom St.Croix's Canada Post 3.1 Module.
*/
require('includes/classes/xmldocument.php');
define('DIMENSIONS_SUPPORTED', 0);

class upsxml {
	var $code, $title, $description, $icon, $enabled, $types, $boxcount;
	
	// class constructor
	function upsxml() {
		global $order;
		
		$this->code = 'upsxml';
		$this->title = MODULE_SHIPPING_UPSXML_RATES_TEXT_TITLE;
		$this->description = MODULE_SHIPPING_UPSXML_RATES_TEXT_DESCRIPTION;
		$this->sort_order = MODULE_SHIPPING_UPSXML_RATES_SORT_ORDER;
		$this->icon = DIR_WS_ICONS . 'shipping_ups.gif';
		$this->tax_class = MODULE_SHIPPING_UPSXML_RATES_TAX_CLASS;
		$this->enabled = ((MODULE_SHIPPING_UPSXML_RATES_STATUS == 'True') ? true : false);
		$this->access_key = MODULE_SHIPPING_UPSXML_RATES_ACCESS_KEY;
		$this->access_username = MODULE_SHIPPING_UPSXML_RATES_USERNAME;
		$this->access_password = MODULE_SHIPPING_UPSXML_RATES_PASSWORD;
		$this->origin = MODULE_SHIPPING_UPSXML_RATES_ORIGIN;
		$this->origin_city = MODULE_SHIPPING_UPSXML_RATES_CITY;
		$this->origin_stateprov = MODULE_SHIPPING_UPSXML_RATES_STATEPROV;
		$this->origin_country = MODULE_SHIPPING_UPSXML_RATES_COUNTRY;
		$this->origin_postalcode = MODULE_SHIPPING_UPSXML_RATES_POSTALCODE;
		$this->pickup_method = MODULE_SHIPPING_UPSXML_RATES_PICKUP_METHOD;
		$this->package_type = MODULE_SHIPPING_UPSXML_RATES_PACKAGE_TYPE;
		$this->quote = MODULE_SHIPPING_UPSXML_RATES_QUOTE_TYPE;
		$this->customer_classification = MODULE_SHIPPING_UPSXML_RATES_CUSTOMER_CLASSIFICATION_CODE;
		$this->protocol = 'https';
		$this->host = ((MODULE_SHIPPING_UPSXML_RATES_TEST_MODE == 'Test') ? 'www3.ups.com' : 'wwwcie.ups.com');
		$this->port = '443';
		$this->path = '/ups.app/xml/Rate';
		$this->version = 'UPSXML Rate 1.0001';
		$this->timeout = '60';
		$this->xpci_version = '1.0001';
        $this->items_qty = 0;
        $this->items_price = 0;
		//$this->logfile = '/tmp/upsxml.log';

		if (($this->enabled == true) && ((int)MODULE_SHIPPING_UPSXML_RATES_ZONE > 0)) {
			$check_flag = false;
			$check_query = tep_db_query("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . MODULE_SHIPPING_UPSXML_RATES_ZONE . "' and zone_country_id = '" . $order->delivery['country']['id'] . "' order by zone_id");
			while ($check = tep_db_fetch_array($check_query)) {
				if ($check['zone_id'] < 1) {
					$check_flag = true;
					break;
				} elseif ($check['zone_id'] == $order->delivery['zone_id']) {
					$check_flag = true;
					break;
				}
			}
			
			if ($check_flag == false) {
				$this->enabled = false;
			}
		}

		// Available pickup types - set in admin
		$this->pickup_methods = array(
			'Daily Pickup' => '01',
			'Customer Counter' => '03',
			'One Time Pickup' => '06',
			'On Call Air Pickup' => '07',
			'Letter Center' => '09',
			'Air Service Center' => '10'
		);

		// Available package types	
		$this->package_types = array(
			'Unknown' => '00',
			'UPS Letter' => '01',
			'Customer Package' => '02',
			'UPS Tube' => '03',
			'UPS Pak' => '04',
			'UPS Express Box' => '21',
			'UPS 25kg Box' => '24',
			'UPS 10kg Box' => '25'
		);
		
		// Human-readable Service Code lookup table
		// The values returned by the Rates and Service "shop" method are numeric.
		// Using these codes, and the admininstratively defined Origin, the proper
		// human-readable service name is returned. Note: The origin specified in
		// the admin configuration affects only the product name as displayed to the user.
		$this->service_codes = array(
			// US Origin
			'US Origin' => array(
				'01' => MODULE_SHIPPING_UPSXML_SERVICE_CODE_US_ORIGIN_01,
				'02' => MODULE_SHIPPING_UPSXML_SERVICE_CODE_US_ORIGIN_02,
				'03' => MODULE_SHIPPING_UPSXML_SERVICE_CODE_US_ORIGIN_03,
				'07' => MODULE_SHIPPING_UPSXML_SERVICE_CODE_US_ORIGIN_07,
				'08' => MODULE_SHIPPING_UPSXML_SERVICE_CODE_US_ORIGIN_08,
				'11' => MODULE_SHIPPING_UPSXML_SERVICE_CODE_US_ORIGIN_11,
				'12' => MODULE_SHIPPING_UPSXML_SERVICE_CODE_US_ORIGIN_12,
				'13' => MODULE_SHIPPING_UPSXML_SERVICE_CODE_US_ORIGIN_13,
				'14' => MODULE_SHIPPING_UPSXML_SERVICE_CODE_US_ORIGIN_14,
				'54' => MODULE_SHIPPING_UPSXML_SERVICE_CODE_US_ORIGIN_54,
				'59' => MODULE_SHIPPING_UPSXML_SERVICE_CODE_US_ORIGIN_59,
				'65' => MODULE_SHIPPING_UPSXML_SERVICE_CODE_US_ORIGIN_65
			),
			// Canada Origin
			'Canada Origin' => array(
				'01' => MODULE_SHIPPING_UPSXML_SERVICE_CODE_CANADA_ORIGIN_01,
				'02' => MODULE_SHIPPING_UPSXML_SERVICE_CODE_CANADA_ORIGIN_02,
				'07' => MODULE_SHIPPING_UPSXML_SERVICE_CODE_CANADA_ORIGIN_07,
				'08' => MODULE_SHIPPING_UPSXML_SERVICE_CODE_CANADA_ORIGIN_08,
				'11' => MODULE_SHIPPING_UPSXML_SERVICE_CODE_CANADA_ORIGIN_11,
				'12' => MODULE_SHIPPING_UPSXML_SERVICE_CODE_CANADA_ORIGIN_12,
				'13' => MODULE_SHIPPING_UPSXML_SERVICE_CODE_CANADA_ORIGIN_13,
				'14' => MODULE_SHIPPING_UPSXML_SERVICE_CODE_CANADA_ORIGIN_14,
				'54' => MODULE_SHIPPING_UPSXML_SERVICE_CODE_CANADA_ORIGIN_54
			),
			// European Union Origin
			'European Union Origin' => array(
				'07' => MODULE_SHIPPING_UPSXML_SERVICE_CODE_EU_ORIGIN_07,
				'08' => MODULE_SHIPPING_UPSXML_SERVICE_CODE_EU_ORIGIN_08,
				'11' => MODULE_SHIPPING_UPSXML_SERVICE_CODE_EU_ORIGIN_11,
				'54' => MODULE_SHIPPING_UPSXML_SERVICE_CODE_EU_ORIGIN_54,
				'65' => MODULE_SHIPPING_UPSXML_SERVICE_CODE_EU_ORIGIN_65,
				'69' => MODULE_SHIPPING_UPSXML_SERVICE_CODE_EU_ORIGIN_69
			),
			// Puerto Rico Origin
			'Puerto Rico Origin' => array(
				'01' => MODULE_SHIPPING_UPSXML_SERVICE_CODE_PR_ORIGIN_01,
				'02' => MODULE_SHIPPING_UPSXML_SERVICE_CODE_PR_ORIGIN_02,
				'03' => MODULE_SHIPPING_UPSXML_SERVICE_CODE_PR_ORIGIN_03,
				'07' => MODULE_SHIPPING_UPSXML_SERVICE_CODE_PR_ORIGIN_07,
				'08' => MODULE_SHIPPING_UPSXML_SERVICE_CODE_PR_ORIGIN_08,
				'14' => MODULE_SHIPPING_UPSXML_SERVICE_CODE_PR_ORIGIN_14,
				'54' => MODULE_SHIPPING_UPSXML_SERVICE_CODE_PR_ORIGIN_54
			),
			// Mexico Origin
			'Mexico Origin' => array(
				'07' => MODULE_SHIPPING_UPSXML_SERVICE_CODE_MEXICO_ORIGIN_07,
				'08' => MODULE_SHIPPING_UPSXML_SERVICE_CODE_MEXICO_ORIGIN_08,
				'54' => MODULE_SHIPPING_UPSXML_SERVICE_CODE_MEXICO_ORIGIN_54
			),
			// All other origins
			'All other origins' => array(
				'07' => MODULE_SHIPPING_UPSXML_SERVICE_CODE_OTHER_ORIGIN_07,
				'08' => MODULE_SHIPPING_UPSXML_SERVICE_CODE_OTHER_ORIGIN_08,
				'54' => MODULE_SHIPPING_UPSXML_SERVICE_CODE_OTHER_ORIGIN_54
			)
		);
    }

	// class methods
    function quote($method = '') {
		global $HTTP_POST_VARS, $order, $shipping_weight, $shipping_num_boxes, $total_weight, $boxcount, $cart;
		//Round weights to 2 decimal places.
		$shipping_weight = round($shipping_weight, 2);
		
		// UPS purports that if the origin is left out, it defaults to the account's location. Yeah, right.
		$state = $order->delivery['state'];
	    $zone_query = tep_db_query("select zone_code from " . TABLE_ZONES . " where zone_name = '" .  $order->delivery['state'] . "'");
		if (tep_db_num_rows($zone_query)) {
		  $zone = tep_db_fetch_array($zone_query);
		  $state = $zone['zone_code'];
		}

		$this->_upsOrigin(MODULE_SHIPPING_UPSXML_RATES_CITY, MODULE_SHIPPING_UPSXML_RATES_STATEPROV, MODULE_SHIPPING_UPSXML_RATES_COUNTRY, MODULE_SHIPPING_UPSXML_RATES_POSTALCODE);
		$this->_upsDest($order->delivery['city'], $state, $order->delivery['country']['iso_code_2'], $order->delivery['postcode']);

		$products_array = $cart->get_products();
		for ($i = 0; $i < count($products_array); $i++) {
			if (DIMENSIONS_SUPPORTED) {
				$this->_addItem ($products_array[$i][quantity], $products_array[$i][final_price], $products_array[$i][weight], 
					$products_array[$i][length], $products_array[$i][width], $products_array[$i][height], $products_array[$i][name]);
			} else {
				$this->_addItem ($products_array[$i][quantity], $products_array[$i][final_price], $products_array[$i][weight], 0, 0, 0, $products_array[$i][name]);
			}
		}

		$upsQuote = $this->_upsGetQuote();
		
		if ((is_array($upsQuote)) && (sizeof($upsQuote) > 0)) {
			$this->quotes = array('id' => $this->code, 'module' => $this->title . ' (' . $this->boxCount . ' x ' . $shipping_weight . 'kgs)');
			
			$methods = array();
			for ($i=0; $i < sizeof($upsQuote); $i++) {
				list($type, $cost) = each($upsQuote[$i]);
				if ( $method == '' || $method == $type ) {
					$methods[] = array('id' => $type, 'title' => $type, 'cost' => (SHIPPING_HANDLING + $cost) * $shipping_num_boxes);
				}
			}

			if ($this->tax_class > 0) {
				$this->quotes['tax'] = tep_get_tax_rate($this->tax_class, $order->delivery['country']['id'], $order->delivery['zone_id']);
			}
			$this->quotes['methods'] = $methods;
		} else {
			if ( $upsQuote != false ) {
				$errmsg = $upsQuote;
			} else {
				$errmsg = 'An unknown error occured with the ups shipping calculations.';
			}
			$errmsg .= '<br>If you prefer to use ups as your shipping method, please contact '.STORE_NAME.' via <a href="mailto:'.STORE_OWNER_EMAIL_ADDRESS.'"><u>Email</U></a>.';
			$this->quotes = array('module' => $this->title, 'error' => $errmsg);
		}

		if (tep_not_null($this->icon)) {
			$this->quotes['icon'] = tep_image($this->icon, $this->title);
		}
		
		return $this->quotes;
    }
	
    function check() {
		if (!isset($this->_check)) {
			$check_query = tep_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_SHIPPING_UPSXML_RATES_STATUS'");
			$this->_check = tep_db_num_rows($check_query);
		}
		return $this->_check;
    }

    function install() {
		tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable UPS Shipping', 'MODULE_SHIPPING_UPSXML_RATES_STATUS', 'True', 'Do you want to offer UPS shipping?', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
		tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('UPS Rates Access Key', 'MODULE_SHIPPING_UPSXML_RATES_ACCESS_KEY', '', 'Enter the XML rates access key assigned to you by UPS.', '6', '1', now())");
		tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('UPS Rates Username', 'MODULE_SHIPPING_UPSXML_RATES_USERNAME', '', 'Enter your UPS Services account username.', '6', '2', now())");
		tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('UPS Rates Password', 'MODULE_SHIPPING_UPSXML_RATES_PASSWORD', '', 'Enter your UPS Services account password.', '6', '3', now())");
		tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Pickup Method', 'MODULE_SHIPPING_UPSXML_RATES_PICKUP_METHOD', 'Daily Pickup', 'How do you give packages to UPS?', '6', '4', 'tep_cfg_select_option(array(\'Daily Pickup\', \'Customer Counter\', \'One Time Pickup\', \'On Call Air Pickup\', \'Letter Center\', \'Air Service Center\'), ', now())");
		tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Packaging Type', 'MODULE_SHIPPING_UPSXML_RATES_PACKAGE_TYPE', 'Customer Packaging', 'What kind of packaging do you use?', '6', '5', 'tep_cfg_select_option(array(\'Unknown packaging\', \'UPS Letter\', \'Customer Package\', \'UPS Tube\', \'UPS Pak\', \'UPS Express Box\', \'UPS 25kg Box\', \'UPS 10kg box\'), ', now())");
		tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Customer Classification Code', 'MODULE_SHIPPING_UPSXML_RATES_CUSTOMER_CLASSIFICATION_CODE', '01', '01 - If you are billing to a UPS account and have a daily UPS pickup, 03 - If you do not have a UPS account or you are billing to a UPS account but do not have a daily pickup, 04 - If you are shipping from a retail outlet', '6', '6', now())");
		tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Shipping Origin', 'MODULE_SHIPPING_UPSXML_RATES_ORIGIN', 'US Origin', 'What origin point should be used (this setting affects only what UPS product names are shown.', '6', '7', 'tep_cfg_select_option(array(\'US Origin\', \'Canada Origin\', \'European Union Origin\', \'Puerto Rico Origin\', \'Mexico Origin\', \'All other origins\'), ', now())");
		tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Origin City', 'MODULE_SHIPPING_UPSXML_RATES_CITY', '', 'Enter the name of the origin city.', '6', '3', now())");
		tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Origin State/Province', 'MODULE_SHIPPING_UPSXML_RATES_STATEPROV', '', 'Enter the two-letter code for your origin state/province.', '6', '3', now())");
		tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Origin Country', 'MODULE_SHIPPING_UPSXML_RATES_COUNTRY', '', 'Enter the two-letter code for your origin country.', '6', '3', now())");
		tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Origin Zip/Postal Code', 'MODULE_SHIPPING_UPSXML_RATES_POSTALCODE', '', 'Enter your origin zip/postalcode.', '6', '3', now())");
		tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Test or Production Mode', 'MODULE_SHIPPING_UPSXML_RATES_MODE', 'Test', 'Use this module in Test or Production mode?', '6', '8', 'tep_cfg_select_option(array(\'Test\', \'Production\'), ', now())");
		tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Quote Type', 'MODULE_SHIPPING_UPSXML_RATES_QUOTE_TYPE', 'Commercial', 'Quote for Residential or Commercial Delivery', '6', '9', 'tep_cfg_select_option(array(\'Commercial\', \'Residential\'), ', now())");
		tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Handling Fee', 'MODULE_SHIPPING_UPSXML_RATES_HANDLING', '0', 'Handling fee for this shipping method.', '6', '10', now())");
		tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Tax Class', 'MODULE_SHIPPING_UPSXML_RATES_TAX_CLASS', '0', 'Use the following tax class on the shipping fee.', '6', '11', 'tep_get_tax_class_title', 'tep_cfg_pull_down_tax_classes(', now())");
		tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Shipping Zone', 'MODULE_SHIPPING_UPSXML_RATES_ZONE', '0', 'If a zone is selected, only enable this shipping method for that zone.', '6', '11', 'tep_get_zone_class_title', 'tep_cfg_pull_down_zone_classes(', now())");
		tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort order of display.', 'MODULE_SHIPPING_UPSXML_RATES_SORT_ORDER', '0', 'Sort order of display. Lowest is displayed first.', '6', '12', now())");
    }

    function remove() {
		tep_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
		return array('MODULE_SHIPPING_UPSXML_RATES_STATUS', 'MODULE_SHIPPING_UPSXML_RATES_ACCESS_KEY', 'MODULE_SHIPPING_UPSXML_RATES_USERNAME', 'MODULE_SHIPPING_UPSXML_RATES_PASSWORD', 'MODULE_SHIPPING_UPSXML_RATES_PICKUP_METHOD', 'MODULE_SHIPPING_UPSXML_RATES_PACKAGE_TYPE', 'MODULE_SHIPPING_UPSXML_RATES_CUSTOMER_CLASSIFICATION_CODE', 'MODULE_SHIPPING_UPSXML_RATES_ORIGIN', 'MODULE_SHIPPING_UPSXML_RATES_CITY', 'MODULE_SHIPPING_UPSXML_RATES_STATEPROV', 'MODULE_SHIPPING_UPSXML_RATES_COUNTRY', 'MODULE_SHIPPING_UPSXML_RATES_POSTALCODE', 'MODULE_SHIPPING_UPSXML_RATES_MODE', 'MODULE_SHIPPING_UPSXML_RATES_QUOTE_TYPE', 'MODULE_SHIPPING_UPSXML_RATES_HANDLING', 'MODULE_SHIPPING_UPSXML_RATES_TAX_CLASS', 'MODULE_SHIPPING_UPSXML_RATES_ZONE', 'MODULE_SHIPPING_UPSXML_RATES_SORT_ORDER');
    }

    function _upsProduct($prod){
		$this->_upsProductCode = $prod;
    }

	function _upsOrigin($city, $stateprov, $country, $postal){
		$this->_upsOriginCity = $city;
		$this->_upsOriginStateProv = $stateprov;
		$this->_upsOriginCountryCode = $country;

		$postal = str_replace(' ', '', $postal);
		if ($country == 'US') {
			$this->_upsOriginPostalCode = substr($postal, 0, 5);
		} else {
			$this->_upsOriginPostalCode = $postal;
		}
	}

    function _upsDest($city, $stateprov, $country, $postal) {
		$this->_upsDestCity = $city;
		$this->_upsDestStateProv = $stateprov;
		$this->_upsDestCountryCode = $country;

		$postal = str_replace(' ', '', $postal);
		if ($country == 'US') {
			$this->_upsDestPostalCode = substr($postal, 0, 5);
		} else {
			$this->_upsDestPostalCode = $postal;
		}
    }

    function _upsRescom($flag) {
		switch ($flag) {
			case 'RES': // Residential Address
				$this->_upsResComCode = '1';
				break;
			case 'COM': // Commercial Address
				$this->_upsResComCode = '2';
				break;
			}
    }

    function _upsAction($action) {
      // rate - Single Quote
      // shop - All Available Quotes

      $this->_upsActionCode = $action;
    }

    // Add items to parcel. If $readytoship=1, this item will be shipped in its oringinal box
    function _addItem($quantity, $rate, $weight, $length, $width, $height, $description, $readytoship=0) {
		$index = $this->items_qty;
		$this->item_quantity[$index] = (string)$quantity;
		$this->item_weight[$index] = ( $weight ? (string)$weight : '0' );
		$this->item_length[$index] = ( $length ? (string)$length : '0' );
		$this->item_width[$index] = ( $width ? (string)$width : '0' );
		$this->item_height[$index] = ( $height ? (string)$height : '0' );
		$this->item_description[$index] = $description;
		$this->item_readytoship[$index] = $readytoship;
		$this->items_qty ++;
		$this->items_price += $quantity * $rate;
    }
	
    function _upsGetQuote() {
	
		// Create the access request
		$accessRequestHeader = 
			"<?xml version=\"1.0\"?>\n".
			"<AccessRequest xml:lang=\"en-US\">\n".
			"   <AccessLicenseNumber>". $this->access_key ."</AccessLicenseNumber>\n".
			"   <UserId>". $this->access_username ."</UserId>\n".
			"   <Password>". $this->access_password ."</Password>\n".
			"</AccessRequest>\n";
		$ratingServiceSelectionRequestHeader = 
			"<?xml version=\"1.0\"?>\n".
			"<RatingServiceSelectionRequest xml:lang=\"en-US\">\n".
			"  <Request>\n".
			"    <TransactionReference>\n".
			"      <CustomerContext>Rating and Service</CustomerContext>\n".
			"      <XpciVersion>". $this->xpci_version ."</XpciVersion>\n".
			"    </TransactionReference>\n".
			"    <RequestAction>Rate</RequestAction>\n".
			"    <RequestOption>shop</RequestOption>\n".
			"  </Request>\n".
			"  <PickupType>\n".
			"    <Code>". $this->pickup_methods[$this->pickup_method] ."</Code>\n".
			"  </PickupType>\n".
			"  <Shipment>\n".
			"    <Shipper>\n".
			"      <Address>\n".
			"        <City>". $this->_upsOriginCity ."</City>\n".
			"        <StateProvinceCode>". $this->_upsOriginStateProv ."</StateProvinceCode>\n".
			"        <CountryCode>". $this->_upsOriginCountryCode ."</CountryCode>\n".
			"        <PostalCode>". $this->_upsOriginPostalCode ."</PostalCode>\n".
			"      </Address>\n".
			"    </Shipper>\n".
			"    <ShipTo>\n".
			"      <Address>\n".
			"        <City>". $this->_upsDestCity ."</City>\n".
			"        <StateProvinceCode>". $this->_upsDestStateProv ."</StateProvinceCode>\n".
			"        <CountryCode>". $this->_upsDestCountryCode ."</CountryCode>\n".
			"        <PostalCode>". $this->_upsDestPostalCode ."</PostalCode>\n".
			"      </Address>\n".
			"    </ShipTo>\n";
//			"    <Service>\n".
//			"      <Code>". $this->pickup_method ."</Code>\n".
//			"    </Service>\n";
			
		for ($i = 0; $i < $this->items_qty; $i++) {
			$ratingServiceSelectionRequestPackageContent .= 
				"    <Package>\n".
				"      <PackagingType>\n".
				"        <Code>". $this->package_types[$this->package_type] ."</Code>\n".
				"        <Description>Package</Description>\n".
				"      </PackagingType>\n";
			if (DIMENSIONS_SUPPORTED) {
				$ratingServiceSelectionRequestPackageContent .= 
				"      <Dimensions>\n".
				"        <UnitOfMeasurement>\n".
				"          <Code>IN</Code>\n".
				"        </UnitOfMeasurement>\n".
				"        <Length>". $this->item_length[$i] ."</Length>\n".
				"        <Width>". $this->item_width[$i] ."</Width>\n".
				"        <Height>". $this->item_height[$i] ."</Height>\n".
				"      </Dimensions>\n";
			}
			$ratingServiceSelectionRequestPackageContent .= 
				"      <Description>Rate Shopping</Description>\n".
				"      <PackageWeight>\n".
//				"        <UnitOfMeasurement>\n".
//				"          <Code>KG</Code>\n".
//				"        </UnitOfMeasurement>\n".
				"        <Weight>". $this->item_weight[$i] ."</Weight>\n".
				"      </PackageWeight>\n".
				"    </Package>\n";
		}
	
		$ratingServiceSelectionRequestFooter =
			"    <ShipmentServiceOptions/>\n".
			"  </Shipment>\n".
			"</RatingServiceSelectionRequest>\n";
			
		$xmlRequest = $accessRequestHeader .
					$ratingServiceSelectionRequestHeader .
					$ratingServiceSelectionRequestPackageContent .
					$ratingServiceSelectionRequestFooter;
		
		//	post request $strXML;
		$xmlResult = $this->_post($this->protocol, $this->host, $this->port, $this->path, $this->version, $this->timeout, $xmlRequest);
		return $this->_parseResult($xmlResult);
	}	

	function _sendToHost($host, $port, $method, $path, $data, $useragent=0) {
		// Supply a default method of GET if the one passed was empty
		if (empty($method)) {
			$method = 'GET';
		}
		$method = strtoupper($method);
		if ($method == 'GET') {
			$path .= '?' . $data;
		}
		$buf = "";

		// try to connect to UPS Post server, for 5 seconds
		$fp = @fsockopen($host, $port, &$errno, &$errstr, 5);
		if ($fp) {
			fputs($fp, "$method $path HTTP/1.1\n");
			fputs($fp, "Host: $host\n");
			fputs($fp, "Content-type: application/x-www-form-urlencoded\n");
			fputs($fp, "Content-length: " . strlen($data) . "\n");
			if ($useragent) {
				fputs($fp, "User-Agent: MSIE\n");
			}
			fputs($fp, "Connection: close\n\n");
			if ($method == 'POST') {
				fputs($fp, $data);
			}
			
			while (!feof($fp)) {
				$buf .= fgets($fp, 128);
			}
			fclose($fp);
		} else {
			$buf =  "<RatingServiceSelectionResponse>\n".
					"  <Response>\n".
					"    <TransactionReference>\n".
				    "      <CustomerContext>Rating and Service</CustomerContext>\n".
				    "      <XpciVersion>1.0001</XpciVersion>\n".
				    "    </TransactionReference>\n".
				    "    <ResponseStatusCode>0</ResponseStatusCode>\n".
				    "    <ResponseStatusDescription>A communication error occured while attempting to contact the UPS gateway</ResponseStatusDescription>\n".
			        "  </Response>\n".
			        "</RatingServiceSelectionResponse>\n";
		}
		return $buf;
    }

	function _post($protocol, $host, $port, $path, $version, $timeout, $xmlRequest) {
	  $url = $protocol."://".$host.":".$port.$path;
	  $ch = curl_init();
	  curl_setopt($ch, CURLOPT_URL, $url);
	  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	  curl_setopt($ch, CURLOPT_HEADER, 0);
	  curl_setopt($ch, CURLOPT_POST, 1);
	  curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlRequest);
	  curl_setopt($ch, CURLOPT_TIMEOUT, (int)$timeout);

	  if ($this->logfile) {
	  	error_log("------------------------------------------\n", 3, $this->logfile);
	  	error_log("UPS REQUEST: " . $xmlRequest . "\n", 3, $this->logfile);
	  }
	  $xmlResponse = curl_exec ($ch);
	  if ($this->logfile) {
	    error_log("UPS RESPONSE: " . $xmlResponse . "\n", 3, $this->logfile);
	  }
	  curl_close ($ch);
	
	  if(!$xmlResponse) {
		$xmlResponse = "<?xml version=\"1.0\"?>\n".
					"<RatingServiceSelectionResponse>\n".
					"  <Response>\n".
					"    <TransactionReference>\n".
				    "      <CustomerContext>Rating and Service</CustomerContext>\n".
				    "      <XpciVersion>1.0001</XpciVersion>\n".
				    "    </TransactionReference>\n".
				    "    <ResponseStatusCode>0</ResponseStatusCode>\n".
				    "    <ResponseStatusDescription>An unknown error occured while attempting to contact the UPS gateway</ResponseStatusDescription>\n".
			        "  </Response>\n".
			        "</RatingServiceSelectionResponse>\n";
	   }
	   return $xmlResponse;
	}

    // Parse XML message returned by the UPS post server.
    function _parseResult($xmlResult) {
		$doc = new XMLDocument();
		$xp = new XMLParser();
		$xp->setDocument($doc);
		$xp->parse($xmlResult);
		$doc = $xp->getDocument();

		// Get version. Must be xpci version 1.0001 or this might not work.
		$responseVersion = $doc->getValueByPath('RatingServiceSelectionResponse/Response/TransactionReference/XpciVersion');
		if ($this->xpci_version != $responseVersion) {
			$message = "This module supports only version ". $this->xpci_version ." of the UPS Rates Interface. The version ".
			           "received was ". $responseVersion .". Please contact the webmaster for additional assistance.";
			return $message;
		}
		
		// Get response code. 1 = SUCCESS, 0 = FAIL
		$responseStatusCode = $doc->getValueByPath('RatingServiceSelectionResponse/Response/ResponseStatusCode');

		if ($responseStatusCode != '1') {
			$errorMsg = $doc->getValueByPath('RatingServiceSelectionResponse/Response/Error/ErrorCode');
			$errorMsg .= ": ";
			$errorMsg .= $doc->getValueByPath('RatingServiceSelectionResponse/Response/Error/ErrorDescription');
			return $errorMsg;
		}
		
		$root = $doc->getRoot();
		$ratedShipments = $root->getElementsByName("RatedShipment");
		$aryProducts = false;
		
		for ($i = 0; $i < count($ratedShipments); $i++) { 
			$serviceCode = $ratedShipments[$i]->getValueByPath("/Service/Code");
			$totalCharge = $ratedShipments[$i]->getValueByPath("/TotalCharges/MonetaryValue");
			if (!($serviceCode && $totalCharge)) {
				continue;
			}
			$gdaysToDelivery = $ratedShipments[$i]->getValueByPath("/GuaranteedDaysToDelivery");
			$sheduledTime = $ratedShipments[$i]->getValueByPath("/ScheduledDeliveryTime");
			$title = '';
			$title = $this->service_codes[$this->origin][$serviceCode];
			if ($gdaysToDelivery) {
				$title .= ' (';
				$title .= $gdaysToDelivery . "Days";
				if ($scheduledTime) {
				  $title .= ' @ ' . $scheduledTime;
				}
				$title .= ')';
			}
			$aryProducts[$i] = array($title => $totalCharge);
		}

   		return $aryProducts;
	}
}
?>
