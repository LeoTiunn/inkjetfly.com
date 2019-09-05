<?php
/*
  $Id: header.php,v 1.42 2003/06/10 18:20:38 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

// check if the 'install' directory exists, and warn of its existence
  if (WARN_INSTALL_EXISTENCE == 'true') {
    if (file_exists(dirname($HTTP_SERVER_VARS['SCRIPT_FILENAME']) . '/install')) {
      $messageStack->add('header', WARNING_INSTALL_DIRECTORY_EXISTS, 'warning');
    }
  }

// check if the configure.php file is writeable
  if (WARN_CONFIG_WRITEABLE == 'true') {
    if ( (file_exists(dirname($HTTP_SERVER_VARS['SCRIPT_FILENAME']) . '/includes/configure.php')) && (is_writeable(dirname($HTTP_SERVER_VARS['SCRIPT_FILENAME']) . '/includes/configure.php')) ) {
      $messageStack->add('header', WARNING_CONFIG_FILE_WRITEABLE, 'warning');
    }
  }

// check if the session folder is writeable
  if (WARN_SESSION_DIRECTORY_NOT_WRITEABLE == 'true') {
    if (STORE_SESSIONS == '') {
      if (!is_dir(tep_session_save_path())) {
        $messageStack->add('header', WARNING_SESSION_DIRECTORY_NON_EXISTENT, 'warning');
      } elseif (!is_writeable(tep_session_save_path())) {
        $messageStack->add('header', WARNING_SESSION_DIRECTORY_NOT_WRITEABLE, 'warning');
      }
    }
  }

// check session.auto_start is disabled
  if ( (function_exists('ini_get')) && (WARN_SESSION_AUTO_START == 'true') ) {
    if (ini_get('session.auto_start') == '1') {
      $messageStack->add('header', WARNING_SESSION_AUTO_START, 'warning');
    }
  }

  if ( (WARN_DOWNLOAD_DIRECTORY_NOT_READABLE == 'true') && (DOWNLOAD_ENABLED == 'true') ) {
    if (!is_dir(DIR_FS_DOWNLOAD)) {
      $messageStack->add('header', WARNING_DOWNLOAD_DIRECTORY_NON_EXISTENT, 'warning');
    }
  }

  if ($messageStack->size('header') > 0) {
    echo $messageStack->output('header');
  }
?>


  <table cellspacing=0 cellpadding=0 width=960 align=center>
   <tr><td>
        <table cellpadding=0 cellspacing=0>
         <tr><td height=13 colspan=6></td></tr>
         <tr><td style="padding-left:13px;"><a href=<?=tep_href_link('index.php')?>><img src=images/m01.gif width=251 height=70 border=0></a></td>
             <td width=75></td>
             <td><img src=images/m02.gif width=125 height=70></td>
             <td width=405 height=70>
               <table cellspacing=0 cellpadding=0 width=86 align=center>
               <tr><td class=cy></td></tr> 
               <tr><td height=8></td></tr>
               <tr><td>

<? // LANGUAGES
/*
  if (!isset($lng) || (isset($lng) && !is_object($lng))) {
    include(DIR_WS_CLASSES . 'language.php');
    $lng = new language;
  }

  $languages_string = '';
  reset($lng->catalog_languages);
  while (list($key, $value) = each($lng->catalog_languages)) {
    $languages_string .= ' <a href="' . tep_href_link(basename($PHP_SELF), tep_get_all_get_params(array('language', 'currency')) . 'language=' . $key, $request_type) . '">' . tep_image(DIR_WS_LANGUAGES .  $value['directory'] . '/images/' . $value['image'], $value['name']) . '</a> ';
  }

  echo $languages_string;
*/
?>                           

               </td></tr>
              </table>                     
             </td>
             <td><img src=images/m17.gif width=1 height=70></td>
             <td width=159 height=70 >
              <table cellspacing=0 cellpadding=0 align=center width=133>
               <tr><td height=10 colspan=2></td></tr>
               <tr><td align=center><a href=<?=tep_href_link('shopping_cart.php','','SSL')?>><img src=images/m03.gif width=33 height=33 border=0></a></td><td class=cy>Shoping<br>Cart</td></tr>
               <tr><td height=3 colspan=2></td></tr>               
               <tr><td colspan=2>now in your cart<b> &nbsp;<a href=<?=tep_href_link('shopping_cart.php','','SSL')?> class=ml><?=$cart->count_contents()?> items</a></b></td></tr>               
               <tr><td height=8 colspan=2></td></tr>
              </table>                                                            
         </td></tr>
     </table>
        <table align="center" cellpadding=0 cellspacing=0>
         <tr><td width=199 valign=top>
              <table cellspacing=0 cellpadding=0>
               <tr><td><img src=images/m07.gif width=199 height=13></td></tr>               
               <tr><td><a href=<?=tep_href_link('index.php')?>><img src=images/m18.gif width=199 height=29 border=0></a></td></tr>
               <tr><td><a href=<?=tep_href_link('products_new.php')?>><img src=images/m19.gif width=199 height=25 border=0></a></td></tr>
               <tr><td><a href=<?=tep_href_link('account.php','','SSL')?>><img src=images/m20.gif width=199 height=24 border=0></a></td></tr>
               <tr><td><a href=<?=tep_href_link('shopping_cart.php','','SSL')?>><img src=images/m21.gif width=199 height=24 border=0></a></td></tr>
               <tr><td><a href=<?=tep_href_link('checkout_shipping.php','','SSL')?>><img src=images/m22.gif width=199 height=31 border=0></a></td></tr>                              
               <tr><td><img src=images/m15.gif width=199 height=18></td></tr>
              </table>
             </td>
             <td valign=top><img src=images/m08.gif width=419 height=164><img src=images/m09.gif width=174 height=164></td>
             <td>
              <table cellspacing=0 cellpadding=0 height=164 border=0>
               <tr><td><img src=images/m10.gif width=160 height=16></td></tr>
               <tr><td class=bg>
                    <table cellspacing=0 cellpadding=0 width=127 align=center>
                     <tr><td><img src=images/m12.gif width=3 height=3 align=absmiddle> &nbsp; <a href=<?=tep_href_link('specials.php')?> class=ml1>Specials</a></td></tr>
                     <tr><td height=2></td></tr>                          
                     <tr><td><img src=images/m12.gif width=3 height=3 align=absmiddle> &nbsp; <a href=<?=tep_href_link('advanced_search.php')?> class=ml1>Search</a></td></tr>
                     <tr><td height=2></td></tr>                          
                     <tr><td><img src=images/m12.gif width=3 height=3 align=absmiddle> &nbsp; <a href="mailto:support@inkjetfly.com">Contact Us</a>
</td></tr>
                     <tr><td height=2></td></tr>                          
                     <tr><td><img src=images/m12.gif width=3 height=3 align=absmiddle> &nbsp; <a href=<?=tep_href_link('login.php','','SSL')?> class=ml1>Customer Login</a></td></tr>
                     <tr><td height=2></td></tr>                          
                     <tr><td><img src=images/m12.gif width=3 height=3 align=absmiddle> &nbsp; <a href=<?=tep_href_link('logoff.php')?> class=ml1>Logoff</a></td></tr>
                     <tr><td height=12></td></tr>                          
                     <tr><td><img src=images/m14.gif width=127 height=1></td></tr>                          
                     <tr><td height=4></td></tr>                          
                     <tr><td><b><font color=#000000>currency</font></b></td></tr>
                     <tr><td height=4></td></tr>                          
                     <tr><td>
<? // CURRENCIES

    echo tep_draw_form('currencies', tep_href_link(basename($PHP_SELF), '', $request_type, false), 'get');

    reset($currencies->currencies);
    $currencies_array = array();
    while (list($key, $value) = each($currencies->currencies)) {
      $currencies_array[] = array('id' => $key, 'text' => $value['title']);
    }

    $hidden_get_variables = '';
    reset($HTTP_GET_VARS);
    while (list($key, $value) = each($HTTP_GET_VARS)) {
      if ( ($key != 'currency') && ($key != tep_session_name()) && ($key != 'x') && ($key != 'y') ) {
        $hidden_get_variables .= tep_draw_hidden_field($key, $value);
      }
    }

    echo tep_draw_pull_down_menu('currency', $currencies_array, $currency, 'onChange="this.form.submit();" style="width: 100%"') . $hidden_get_variables . tep_hide_session_id();
    echo '</form>';
    
?>               
                     
                     </td></tr>
                    </table>
               </td></tr>
               <tr><td><img src=images/m16.gif width=160 height=18></td></tr>
              </table>
         </td></tr>
     </table>
