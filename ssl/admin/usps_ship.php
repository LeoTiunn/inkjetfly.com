<?php
/**
 * USPS.com Click-n-Ship Auto-Fill Contrib
 * Qhome (qhomezone@gmail.com)
 *
 * @package
 * @copyright Copyright 2003-2006 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: usps_autofill_button.php 3182 2007-04-23 15:40:58Z qhome $
 */

	global $db;
	require('includes/application_top.php');
	$oID = (int)tep_db_prepare_input($HTTP_GET_VARS['oID']);
	include(DIR_WS_CLASSES . 'order.php');


	$email_notification = "false";  // notify reciepient of shipping --true or false


	$order = new order($oID);
	$USPS_file_dir = DIR_WS_INCLUDES . 'usps_ship_files';    //directory where all support files are
	//Gets the return & billing address two digit state code
	$shipping_zone_query = tep_db_query("select z.zone_code from " . TABLE_ZONES . " z, " . TABLE_COUNTRIES . " c where zone_name = '" . $order->delivery['state'] . "' AND c.countries_name = '" . $order->delivery['country'] . "' AND c.countries_id = z.zone_country_id");
	$shipping_zone = tep_db_fetch_array($shipping_zone_query);
	$shipping_zone_code = ($shipping_zone['zone_code'] == '' ? $order->delivery['state'] : $shipping_zone['zone_code']);  // if the query result was empty, then use the state name
	if ($order->billing['state'] == $order->delivery['state']) {  // if billing and shipping states are the same, then we can save a query
	  $billing_zone_code = $shipping_zone_code;
	  } else {
	  $billing_zone_query = tep_db_query("select z.zone_code from " . TABLE_ZONES . " z, " . TABLE_COUNTRIES . " c where z.zone_name = '" . $order->billing['state'] . "' AND c.countries_name = '" . $order->billing['country'] . "' AND c.countries_id = z.zone_country_id");
	  $billing_zone = tep_db_fetch_array($billing_zone_query);
	  $billing_zone_code = ($billing_zone['zone_code'] == '' ? $order->billing['state'] : $billing_zone['zone_code']); // if the query result was empty, then use the state name
	  }

	// This checks the country to see if it should be using state check for USA or International
	//$order_check_country = $db->Execute("select delivery_country from " . TABLE_ORDERS . " where orders_id = '" . (int)$oID . "'");
	$UpperPHPCountry = strtoupper($order->delivery['country']);

	//$order_check = $db->Execute("select customers_telephone, customers_email_address,
	//                                    TRIM(TRAILING substring_index(delivery_name,' ', -1) from delivery_name) as delivery_firstname,
	 ///                                   substring_index(delivery_name,' ', -1) as delivery_lastname,
	   //                                 delivery_company, delivery_street_address, delivery_suburb,
	    ///                                delivery_city, delivery_postcode, delivery_state, delivery_country, zone_code,
	      //                              last_modified from " . TABLE_ORDERS . ', ' . TABLE_ZONES ."
	       //                             where orders_id = '" . (int)$oID . "' and zone_name = delivery_state");

	// Get order subtotal for use as insurance or declaration value (if those options are enabled)
	If (USPS_DELIVERY_DEFAULT_CONTENTS_VALUE == "subtotal" || USPS_DELIVERY_INSURANCE_VALUE == "subtotal") {
	  //$order_total_check = $db->Execute("select value from " . TABLE_ORDERS_TOTAL . " where orders_id = '" . (int)$oID . "' and class = 'ot_subtotal'");
	}

	If (USPS_DELIVERY_DEFAULT_CONTENTS_VALUE == "total" || USPS_DELIVERY_INSURANCE_VALUE == "total") {
	 // $order_total_check2 = $db->Execute("select value from " . TABLE_ORDERS_TOTAL . " where orders_id = '" . (int)$oID . "' and class = 'ot_total'");
	}

	// String of all the countries copied from the usps form. Followed by explode functions to save me the work of having to list them manually.
	$AllCountries=('<option value="840">UNITED STATES</option><option value="4">AFGHANISTAN</option><option value="248">ALAND ISLAND</option><option value="8">ALBANIA</option><option value="12">ALGERIA</option><option value="20">ANDORRA</option><option value="24">ANGOLA</option><option value="660">ANGUILLA</option><option value="28">ANTIGUA AND BARBUDA</option><option value="32">ARGENTINA</option><option value="51">ARMENIA</option><option value="533">ARUBA</option><option value="36">AUSTRALIA</option><option value="40">AUSTRIA</option><option value="31">AZERBAIJAN</option><option value="44">BAHAMAS</option><option value="48">BAHRAIN</option><option value="50">BANGLADESH</option><option value="52">BARBADOS</option><option value="112">BELARUS</option><option value="56">BELGIUM</option><option value="84">BELIZE</option><option value="204">BENIN</option><option value="60">BERMUDA</option><option value="64">BHUTAN</option><option value="68">BOLIVIA</option><option value="70">BOSNIA-HERZEGOVINA</option><option value="72">BOTSWANA</option><option value="76">BRAZIL</option><option value="92">BRITISH VIRGIN ISLANDS</option><option value="96">BRUNEI DARUSSALAM</option><option value="100">BULGARIA</option><option value="854">BURKINA FASO</option><option value="104">BURMA</option><option value="108">BURUNDI</option><option value="116">CAMBODIA</option><option value="120">CAMEROON</option><option value="124">CANADA</option><option value="132">CAPE VERDE</option><option value="136">CAYMAN ISLANDS</option><option value="140">CENTRAL AFRICAN REPUBLIC</option><option value="148">CHAD</option><option value="152">CHILE</option><option value="156">CHINA</option><option value="162">CHRISTMAS ISLANDS</option><option value="166">COCOS ISLAND</option><option value="170">COLOMBIA</option><option value="174">COMOROS</option><option value="178">CONGO (BRAZZAVILLE), REPUBLIC OF THE</option><option value="180">CONGO, DEMOCRATIC REPUBLIC OF THE</option><option value="184">COOK ISLANDS</option><option value="188">COSTA RICA</option><option value="384">COTE D IVOIRE</option><option value="191">CROATIA</option><option value="192">CUBA</option><option value="196">CYPRUS</option><option value="203">CZECH REPUBLIC</option><option value="208">DENMARK</option><option value="262">DJIBOUTI</option><option value="212">DOMINICA</option><option value="214">DOMINICAN REPUBLIC</option><option value="626">EAST TIMOR</option><option value="218">ECUADOR</option><option value="818">EGYPT</option><option value="222">EL SALVADOR</option><option value="226">EQUATORIAL GUINEA</option><option value="232">ERITREA</option><option value="233">ESTONIA</option><option value="231">ETHIOPIA</option><option value="238">FALKLAND ISLANDS</option><option value="234">FAROE ISLANDS</option><option value="242">FIJI</option><option value="246">FINLAND</option><option value="250">FRANCE</option><option value="254">FRENCH GUIANA</option><option value="258">FRENCH POLYNESIA</option><option value="266">GABON</option><option value="270">GAMBIA</option><option value="268">GEORGIA, REPUBLIC OF</option><option value="276">GERMANY</option><option value="288">GHANA</option><option value="292">GIBRALTAR</option><option value="826">GREAT BRITAIN AND NORTHERN IRELAND</option><option value="300">GREECE</option><option value="304">GREENLAND</option><option value="308">GRENADA</option><option value="312">GUADELOUPE</option><option value="320">GUATEMALA</option><option value="831">GUERNSEY</option><option value="324">GUINEA</option><option value="624">GUINEA-BISSAU</option><option value="328">GUYANA</option><option value="332">HAITI</option><option value="340">HONDURAS</option><option value="344">HONG KONG</option><option value="348">HUNGARY</option><option value="352">ICELAND</option><option value="356">INDIA</option><option value="360">INDONESIA</option><option value="364">IRAN</option><option value="368">IRAQ</option><option value="372">IRELAND</option><option value="833">ISLE OF MAN</option><option value="376">ISRAEL</option><option value="380">ITALY</option><option value="388">JAMAICA</option><option value="392">JAPAN</option><option value="832">JERSEY</option><option value="400">JORDAN</option><option value="398">KAZAKHSTAN</option><option value="404">KENYA</option><option value="296">KIRIBATI</option><option value="408">KOREA, DEMOCRATIC PEOPLES REPUBLIC OF(NORTH KOREA)</option><option value="410">KOREA, REPUBLIC OF (SOUTH KOREA)</option><option value="25">KOSOVO, REPUBLIC OF</option><option value="414">KUWAIT</option><option value="417">KYRGYZSTAN</option><option value="418">LAOS</option><option value="428">LATVIA</option><option value="422">LEBANON</option><option value="426">LESOTHO</option><option value="430">LIBERIA</option><option value="434">LIBYA</option><option value="438">LIECHTENSTEIN</option><option value="440">LITHUANIA</option><option value="442">LUXEMBOURG</option><option value="446">MACAO</option><option value="807">MACEDONIA, REPUBLIC OF</option><option value="450">MADAGASCAR</option><option value="454">MALAWI</option><option value="458">MALAYSIA</option><option value="462">MALDIVES</option><option value="466">MALI</option><option value="470">MALTA</option><option value="474">MARTINIQUE</option><option value="478">MAURITANIA</option><option value="480">MAURITIUS</option><option value="175">MAYOTTE</option><option value="484">MEXICO</option><option value="498">MOLDOVA</option><option value="492">MONACO</option><option value="496">MONGOLIA</option><option value="499">MONTENEGRO, REPUBLIC OF</option><option value="500">MONTSERRAT</option><option value="504">MOROCCO</option><option value="508">MOZAMBIQUE</option><option value="516">NAMIBIA</option><option value="520">NAURU</option><option value="524">NEPAL</option><option value="528">NETHERLANDS</option><option value="530">NETHERLANDS ANTILLES</option><option value="540">NEW CALEDONIA</option><option value="554">NEW ZEALAND</option><option value="558">NICARAGUA</option><option value="562">NIGER</option><option value="566">NIGERIA</option><option value="570">NIUE</option><option value="574">NORFOLK ISLAND</option><option value="578">NORWAY</option><option value="512">OMAN</option><option value="586">PAKISTAN</option><option value="591">PANAMA</option><option value="598">PAPUA NEW GUINEA</option><option value="600">PARAGUAY</option><option value="604">PERU</option><option value="608">PHILIPPINES</option><option value="612">PITCAIRN ISLAND</option><option value="616">POLAND</option><option value="620">PORTUGAL</option><option value="634">QATAR</option><option value="638">REUNION</option><option value="642">ROMANIA</option><option value="643">RUSSIA</option><option value="646">RWANDA</option><option value="652">SAINT BARTHELEMY</option><option value="659">SAINT CHRISTOPHER (ST. KITTS) AND NEVIS</option><option value="654">SAINT HELENA</option><option value="662">SAINT LUCIA</option><option value="663">SAINT MARTIN</option><option value="666">SAINT PIERRE AND MIQUELON</option><option value="670">SAINT VINCENT AND THE GRENADINES</option><option value="674">SAN MARINO</option><option value="678">SAO TOME AND PRINCIPE</option><option value="682">SAUDI ARABIA</option><option value="686">SENEGAL</option><option value="688">SERBIA, REPUBLIC OF</option><option value="690">SEYCHELLES</option><option value="694">SIERRA LEONE</option><option value="702">SINGAPORE</option><option value="703">SLOVAK REPUBLIC</option><option value="705">SLOVENIA</option><option value="90">SOLOMON ISLANDS</option><option value="706">SOMALIA</option><option value="710">SOUTH AFRICA</option><option value="239">SOUTH GEORGIA AND THE SOUTH SANDWICH ISLANDS</option><option value="724">SPAIN</option><option value="144">SRI LANKA</option><option value="736">SUDAN</option><option value="740">SURINAME</option><option value="748">SWAZILAND</option><option value="752">SWEDEN</option><option value="756">SWITZERLAND</option><option value="760">SYRIAN ARAB REPUBLIC</option><option value="158">TAIWAN</option><option value="762">TAJIKISTAN</option><option value="834">TANZANIA</option><option value="764">THAILAND</option><option value="768">TOGO</option><option value="772">TOKELAU GROUP</option><option value="776">TONGA</option><option value="780">TRINIDAD AND TOBAGO</option><option value="788">TUNISIA</option><option value="792">TURKEY</option><option value="795">TURKMENISTAN</option><option value="796">TURKS AND CAICOS ISLANDS</option><option value="798">TUVALU</option><option value="800">UGANDA</option><option value="804">UKRAINE</option><option value="784">UNITED ARAB EMIRATES</option><option value="858">URUGUAY</option><option value="860">UZBEKISTAN</option><option value="548">VANUATU</option><option value="336">VATICAN CITY</option><option value="862">VENEZUELA</option><option value="704">VIETNAM</option><option value="876">WALLIS AND FUTUNA ISLANDS</option><option value="882">WESTERN SAMOA</option><option value="887">YEMEN</option><option value="894">ZAMBIA</option><option value="716">ZIMBABWE</option>');

	$ParseTest = explode("</option><option ", $AllCountries);
	for ($c=0;$c<=count($ParseTest);$c++) {
	  $ParseTest2[$c] = explode("value=\"", $ParseTest[$c]);
	}
	for ($f=0;$f<=count($ParseTest);$f++) {
	  $ParseTest3[$f] = explode("\">", $ParseTest2[$f][1]);
	  $cn_abbrv[$f] = $ParseTest3[$f][0];
       $ParseTest4[$f] = explode(" )", $ParseTest3[$f][1]);
	  $CountryName[$f] = $ParseTest4[$f][0];
	}
	$ExactMatch = 0;
// print_r($CountryName);

	// Check if country exists (Exact match)
	for ($i=0;$i<count($ParseTest);$i++) {
	  $UpperCountry = strtoupper($CountryName[$i]);
	  if ($UpperCountry == $UpperPHPCountry) {
	    $ExactMatch = 1;
	    $sCountry = $CountryName[$i];
	    $CountryNum = $i;
	    $sCountryNum = $cn_abbrv[$CountryNum];
	    break;
	  }
	}
	// Check strpos for any partial country matches.
	// This is for countries like "United Kingdom" but USPS has "United Kingdom (Great Britain)"
	if ($ExactMatch < 1) {
	  //echo "\nExact ascii country match not found, searching for partial match. Verify Country is correct\n";
	  if (!$UpperPHPCountry == Null) {
	    for ($i=0;$i<count($ParseTest);$i++) {
	      $UpperCountry = strtoupper($CountryName[$i]);
	      $FoundCountry = strpos($UpperCountry, $UpperPHPCountry);
	      if ($FoundCountry > -1) {
	        $sCountry = $CountryName[$i];
	        $CountryNum = $i;
	        $sCountryNum = $cn_abbrv[$CountryNum];
	        break;
	      }
	    }
	  }
	}
// echo "<br>sCountry = " . $sCountry . "<br>CountryNum = " . $CountryNum . "<br>sCountryNum = " . $sCountryNum . "<br>";


	// Weight Calculations
	if (USPS_WEIGHT_OVERRIDE != '') {
	  $shipping_weight = USPS_WEIGHT_OVERRIDE;
	  } else {
	  $weight_query = tep_db_query("select sum(op.products_quantity * p.products_weight) as weight from " . TABLE_PRODUCTS . " p, " . TABLE_ORDERS_PRODUCTS . " op where op.products_id = p.products_id AND op.orders_id = '" . (int)$oID . "'");
	  $total_weight = tep_db_fetch_array($weight_query);
	  $shipping_weight =  $total_weight['weight'] + SHIPPING_BOX_WEIGHT;  // adds the "Package Tare weight" configuration value to the package value
	  $shipping_weight = ($shipping_weight < 0.0625 ? 0.0625 : $shipping_weight); // if shipping weight is less than one ounce then make it one ounce
	  }
	$shipping_weight = ceil($shipping_weight*16)/16;  // rounds up to the next ounce, 4.6 oz becomes 5 oz, 15.7 oz becomes 1 lb
	$shipping_pounds = floor ($shipping_weight);
	$shipping_ounces = (16 * ($shipping_weight - floor($shipping_weight)));

	$contents_value = ceil(substr(strip_tags($order->totals[0]['text']),1));
	$send_value = (USPS_SEND_VALUE_OVER > $contents_value ? '' : $contents_value);

	?>
	<script type="text/javascript">
	        var StateName = new Array()
	        var StateNum
	        var abbrv = new Array()
	        var sState
	        var CountryName = new Array()
	        var CountryNum
	        var cn_abbrv = new Array()
	        var sCountry
	        var sCountryNum

	function ParseIt() {
	  document.getElementById('LabelInformationAction').submit();
	}
	</script>

	<form name="USPS_labels" target="_self" method="post" action="https://cns.usps.com/go/Secure/LabelInformationAction!input.action" id="LabelInformationAction">
	  <input type="hidden" name="form.submitControl" value="NewLabel">



	<input type="hidden" name="form.returnFirstName" value="MY FIRST NAME" id="form.returnFirstName">
	<input type="hidden" name="form.returnLastName" value="MY LAST NAME" id="form.returnLastName">
	<input type="hidden" name="form.returnCompanyName" value="MY COMPANY NAME" id="tCompanyReturn">
	<input type="hidden" name="form.returnAddressOne" value="MY ADDRESS ONE" id="tAddress1Return">
	<input type="hidden" name="form.returnAddressTwo" value="MY ADDRESS TWO" id="tAddress2Return">
	<input type="hidden" name="form.returnCity" value="MY CITY" id="tCityReturn">
	<input type="hidden" name="form.returnState" value="ST" id="stateReturn">
	<input type="hidden" name="form.returnZipcode" value="5DIGITZIP" id="tZipReturn">
	<input type="hidden" name="form.returnPhoneNumber" value="MYPHONE" id="tPhoneReturn">
	<input type="hidden" name="form.trkConfirmEmail" value="MYEMAIL@EMAIL.COM" id="tEmailReturn">
	<input type="hidden" name="form.trkConfirmNotification"  value="<?php echo USPS_RETURN_TRACKING_EMAIL_NOTIFY; ?>" id="cTrackingNotificationReturnEntered">
	<input type="hidden" name="form.deliveryCountry" value="<?php echo $sCountryNum; ?>" id="deliveryCountry">

	<? //wes removed
	$arr = explode(" ", $order->customer['name'], 2);
	                                                               $myFirstName = $arr[0];
	                                                               $myLastName = $arr[1];
	                                                               $myFirstName = ucfirst(strtolower($myFirstName));
	                                                               if(strlen($myFirstName) < 3 ){
	                                                                  $myFirstName = substr($order->customer['name'], 0, strrpos( $order->customer['name'], ' ') );
	                                                               }


	?>
	<input type="hidden" name="form.deliveryFirstName" value="<?php echo $myFirstName; ?>" id="tNameFirstTo">
	<input type="hidden" name="form.deliveryLastName" value="<?php echo $myLastName; ?>" id="tNameLastTo">
	<input type="hidden" name="form.deliveryCompanyName" value="<?php echo $order->delivery['company']; ?>" id="tCompanyTo">

	<input type="hidden" name="form.deliveryAddressOne" value="<?php echo $order->delivery['street_address']; ?>" id="tAddress1To">
	<input type="hidden" name="form.deliveryAddressTwo" value="<?php echo $order->delivery['suburb']; ?>" id="tAddress2To">
	<input type="hidden" name="form.deliveryCity" value="<?php echo $order->delivery['city']; ?>" id="tCityTo">
	<!-- State -->
	<input type="hidden" name="form.deliveryState" value= "<?php echo $shipping_zone_code;?>" id="stateTo">

<?php if ($sCountry == 'UNITED STATES' ) { ?>
	<input type="hidden" name="form.deliveryZipcode" value="<?php echo $order->delivery['postcode']; ?>" id="tZipTo">
<?php } else { ?>
	<input type="hidden" name="form.deliveryPostalCode" value="<?php echo $order->delivery['postcode']; ?>" id="tPostalCodeTo">
	<input type="hidden" name="form.province" value="<?php echo $order->delivery['state']; ?>" id="tProvinceTo">
	<input type="hidden" name="form.deliveryPhoneNumber" value="<?php echo $order->customer['telephone']; ?>" id="tPhoneTo">
	<input type="hidden" name="form.contentsValue" value="<?php echo $send_value; ?>" id="tValue">
<?php } ?>
	<input type="hidden" name="form.emailNotification"  value="<?php echo $email_notification; ?>" id="cTrackingNotificationTo">
	<input type="hidden" name="form.deliveryEmail" value="<?php echo $order->customer['email_address']; ?>" id="deliveryEmail">
	<input type="hidden" name="form.emailNotification" value="<?php echo USPS_DELIVERY_DEFAULT_EMAIL_NOTIFY; ?>" id="cTrackingNotificationTo">
	<input type="hidden" name="form.deliveryRefNbr" value="<?php echo (int)$oID; ?>" id="tReferenceTo">
	<input type="hidden" name="form.shippingWeightInPounds" value="<?php echo $shipping_pounds; ?>" id="pkg-tlbs">
	<input type="hidden" name="form.shippingWeightInOunces" value="<?php echo $shipping_ounces; ?>" id="pkg-tozs">
	<input type="submit" value="Ship a USPS Package" />

	</form>

	<script>
	document.USPS_labels.submit();
	</script>
