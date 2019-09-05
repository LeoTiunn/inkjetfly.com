<?php
/*
  $Id: ups_print_worldship_xml.php,v 1.2 2008/02/12 21:17:13 lamastus Exp $
  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com
  Released under GPL and may be modified.
  written by: Thuan V. Nguyen, http://www.a8le.com
*/

   // editable variables
   $ups_account_num = "8W17V4";  // ups account number
   $country_origin = "US";  // country
   $description_goods = "Ink Bottles"; // description of goods
   $qvn_subject_line = "Inkjetfly Order";  // quantum view notification email subject line
   $qvn_memo = "Your Order Has Shipped"; // text note that will be sent to the users
   $qvn_refe2 = " "; //Reference that will show on the label
   
   // get order ID
   $oID = tep_db_prepare_input($HTTP_GET_VARS['oID']);
   
   // get service type from orders page
   $service_type = $HTTP_GET_VARS['ups_method'];

   // Change the reference1 to what you like, order's ID is a good fit.
   $qvn_refe1 = $oID; //Reference that will show on the label
   
   // query db for order info
   $ups_export_query = tep_db_query("select * from " . TABLE_ORDERS . " where orders_id = '" . (int)$oID . "'");
   $ups_export = tep_db_fetch_array($ups_export_query);
   
   $customers_id = $ups_export['customers_id'];	
   $country = $ups_export['delivery_country'];
   $customers_telephone = ereg_replace(" ", "", ereg_replace("\\.", "", ereg_replace("-", "", $ups_export['customers_telephone']))); // strip phone number of periods, hyphens, and spaces
   $customers_fax = ereg_replace(" ", "", ereg_replace("\\.", "", ereg_replace("-", "", $ups_export['customers_fax']))); // strip phone number of periods, hyphens, and spaces
   $customers_email_address = $ups_export['customers_email_address'];
   
   // if delivery_company is empty, use delivery_name
   if ($ups_export['delivery_company'] == "") {
     $delivery_company = $ups_export['delivery_name'];
     $delivery_name = "";
   } else {
     $delivery_company = $ups_export['delivery_company'];
     $delivery_name = $ups_export['delivery_name'];
   }
    
   $delivery_street_address = $ups_export['delivery_street_address'];
   $delivery_address_2 = $ups_export['delivery_suburb'];
   $delivery_city = $ups_export['delivery_city'];
   $delivery_postcode = $ups_export['delivery_postcode'];
   
   if (strlen($ups_export['delivery_state']) > 2) {
      // Get the 2 letter state
      $state_list = array(
                'AL'=>"Alabama",
                'AK'=>"Alaska", 
                'AZ'=>"Arizona", 
                'AR'=>"Arkansas", 
                'CA'=>"California", 
                'CO'=>"Colorado", 
                'CT'=>"Connecticut", 
                'DE'=>"Delaware", 
                'DC'=>"District Of Columbia", 
                'FL'=>"Florida", 
                'GA'=>"Georgia", 
                'HI'=>"Hawaii", 
                'ID'=>"Idaho", 
                'IL'=>"Illinois", 
                'IN'=>"Indiana", 
                'IA'=>"Iowa", 
                'KS'=>"Kansas", 
                'KY'=>"Kentucky", 
                'LA'=>"Louisiana", 
                'ME'=>"Maine", 
                'MD'=>"Maryland", 
                'MA'=>"Massachusetts", 
                'MI'=>"Michigan", 
                'MN'=>"Minnesota", 
                'MS'=>"Mississippi", 
                'MO'=>"Missouri", 
                'MT'=>"Montana",
                'NE'=>"Nebraska",
                'NV'=>"Nevada",
                'NH'=>"New Hampshire",
                'NJ'=>"New Jersey",
                'NM'=>"New Mexico",
                'NY'=>"New York",
                'NC'=>"North Carolina",
                'ND'=>"North Dakota",
                'OH'=>"Ohio", 
                'OK'=>"Oklahoma", 
                'OR'=>"Oregon", 
                'PA'=>"Pennsylvania", 
                'RI'=>"Rhode Island", 
                'SC'=>"South Carolina", 
                'SD'=>"South Dakota",
                'TN'=>"Tennessee", 
                'TX'=>"Texas", 
                'UT'=>"Utah", 
                'VT'=>"Vermont", 
                'VA'=>"Virginia", 
                'WA'=>"Washington", 
                'WV'=>"West Virginia", 
                'WI'=>"Wisconsin", 
                'WY'=>"Wyoming");                
     foreach ($state_list as $key => $value) {
       if ($ups_export['delivery_state'] == $value) 
         $delivery_state = $key;
     }
   } else {
     // Google Checkout/PayPal uses 2 letter state
     $delivery_state = $ups_export['delivery_state'];
   }

   // Weight Calculations
   $weight_query = tep_db_query("select sum(op.products_quantity * p.products_weight) as weight from " . TABLE_PRODUCTS . " p, " . TABLE_ORDERS_PRODUCTS . " op where op.products_id = p.products_id AND op.orders_id = '" . (int)$oID . "'");
   $total_weight = tep_db_fetch_array($weight_query);
   $shipping_weight =  $total_weight['weight'] + SHIPPING_BOX_WEIGHT;  // adds the "Package Tare weight" configuration value to the package value
   $shipping_weight = ($shipping_weight < 0.0625 ? 0.0625 : $shipping_weight); // if shipping weight is less than one ounce then make it one ounce
   $shipping_weight = ceil($shipping_weight*16)/16;  // rounds up to the next ounce, 4.6 oz becomes 5 oz, 15.7 oz becomes 1 lb
   $shipping_pounds = ceil ($shipping_weight);
   //$shipping_ounces = (16 * ($shipping_weight - floor($shipping_weight)));

   // create the customer delivery information
   $ups  ="<OpenShipments xmlns=\"x-schema: OpenShipments.xdr\">\r\n";
   $ups .="<OpenShipment ProcessStatus=\"\" ShipmentOption=\"SC\">\r\n";
	
   //shipto data
   $ups.=" <ShipTo>\r\n";
   $ups.="  <CustomerID>" . $customers_id . "</CustomerID>\r\n";
   $ups.="  <CompanyOrName>" . $delivery_company . "</CompanyOrName>\r\n";
   $ups.="  <Attention>" . $delivery_name . "</Attention>\r\n";
   $ups.="  <Address1>" . $delivery_street_address . "</Address1>\r\n";
   $ups.="  <Address2>" . $delivery_address_2 . "</Address2>\r\n";
   $ups.="  <Address3></Address3>\r\n";
   $ups.="  <CountryTerritory>" . $country . "</CountryTerritory>\r\n";
   $ups.="  <PostalCode>" . $delivery_postcode . "</PostalCode>\r\n";
   $ups.="  <CityOrTown>" . $delivery_city . "</CityOrTown>\r\n";
   $ups.="  <StateProvinceCounty>" . $delivery_state . "</StateProvinceCounty>\r\n";
   $ups.="  <Telephone>" . $customers_telephone . "</Telephone>\r\n";
   $ups.="  <FaxNumber>" . $customers_fax . "</FaxNumber>\r\n";
   $ups.="  <EmailAddress>" . $customers_email_address . "</EmailAddress>\r\n";
   $ups.="  <LocationID></LocationID>\r\n";
   $ups.="  <ResidentialIndicator>1</ResidentialIndicator>\r\n";
   $ups.=" </ShipTo>\r\n";
      
   //shipment info
   $ups.=" <ShipmentInformation>\r\n";
   $ups.="  <ServiceType>" . $service_type . "</ServiceType>\r\n";
   $ups.="  <NumberOfPackages>1</NumberOfPackages>\r\n";
   $ups.="  <DescriptionOfGoods>" . $description_goods . "</DescriptionOfGoods>\r\n";
   $ups.="  <ShipperNumber>" . $ups_account_num . "</ShipperNumber>\r\n";
   $ups.="  <BillingOption>PP</BillingOption>\r\n";
   $ups.="  <BillTransportationTo></BillTransportationTo>\r\n";
   $ups.="  <BillDutyTaxTo></BillDutyTaxTo>\r\n";
   $ups.="  <SplitDutyAndTax></SplitDutyAndTax>\r\n";
   $ups.="  <USI></USI>\r\n";
   $ups.=" </ShipmentInformation>\r\n";
      
   // package info
   $ups.=" <Package>\r\n";
   $ups.="  <PackageType>CP</PackageType>\r\n";
   $ups.="  <Weight>" . $shipping_pounds . "</Weight>\r\n";
   $ups.="  <TrackingNumber></TrackingNumber>\r\n";
   $ups.="  <LargePackageIndicator></LargePackageIndicator>\r\n";
   $ups.="  <Reference1>" . $qvn_refe1 . "</Reference1>\r\n";
   $ups.="  <Reference2>" . $qvn_refe2 . "</Reference2>\r\n";
   $ups.="  <QVNOrReturnNotificationOption>\r\n";
   $ups.="   <QVNOrReturnRecipientAndNotificationTypes>\r\n";
   $ups.="    <EMailAddress>" . $customers_email_address . "</EMailAddress>\r\n";
   $ups.="    <Ship>1</Ship>\r\n";
   $ups.="   </QVNOrReturnRecipientAndNotificationTypes>\r\n";
   $ups.="   <SubjectLine>" . $qvn_subject_line . "</SubjectLine>\r\n";
   $ups.="   <Memo>" . $qvn_memo . "</Memo>\r\n";
   $ups.="   </QVNOrReturnNotificationOption>\r\n";
   $ups.=" </Package>\r\n";
      
   // goods info
   $ups.=" <Goods>\r\n";
   $ups.="  <DescriptionOfGood>" . $description_goods . "</DescriptionOfGood>\r\n";
   $ups.="  <Inv-NAFTA-CO-CountryTerritoryOfOrigin>" . $country_origin . "</Inv-NAFTA-CO-CountryTerritoryOfOrigin>\r\n";
   $ups.=" </Goods>\r\n";
      
   // close   
   $ups.="</OpenShipment>";
   $ups.="</OpenShipments>";
   
   // output
   header("Content-Type: text/xml");
   header("Content-Disposition: attachment; filename=Order_$oID.xml"); 
   echo $ups;
   exit;

// end
?>
