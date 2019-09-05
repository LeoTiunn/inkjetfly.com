<?php
/*
  $Id: footer.php,v 1.26 2003/02/10 22:30:54 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

  require(DIR_WS_INCLUDES . 'counter.php');
?>


         </td></tr>
        </table>
        <table align="center" cellpadding=0 cellspacing=0>
         <tr><td height=5 colspan=4></td></tr>
         <tr><td width=960 height=6 bgcolor=#d9d9d9 colspan=4></td></tr>
         <tr><td height=14 colspan=4></td></tr>
         <tr><td width=207 align=center valign=center><img src=images/m33.gif width=140 height=66></td>
             <td><img src=images/m31.gif width=1 height=63></td>
             <td width=21></td>
             <td width=506>
              <table cellspacing=0 cellpadding=0>
               <tr><td height=15></td></tr>
               <tr><td><a href=<?=tep_href_link('index.php')?> class=ml>Home</a> &nbsp;<img src=images/m32.gif width=1 height=11 align=absmiddle> &nbsp;<a href=<?=tep_href_link('account.php','','SSL')?> class=ml>My account</a> &nbsp;<img src=images/m32.gif width=1 height=11 align=absmiddle> &nbsp;<a href=<?=tep_href_link('products_new.php')?> class=ml>New products</a> &nbsp;<img src=images/m32.gif width=1 height=11 align=absmiddle> &nbsp;<a href=<?=tep_href_link('shopping_cart.php','','SSL')?> class=ml>Shopping cart</a> &nbsp;<img src=images/m32.gif width=1 height=11 align=absmiddle> &nbsp;<a href=<?=tep_href_link('checkout_shipping.php','','SSL')?> class=ml>Checkout</a> &nbsp;<img src=images/m32.gif width=1 height=11 align=absmiddle> &nbsp;<a href=information.php?info_id=16 class=ml>Privacy policy</a></td></tr>
               <tr><td height=10></td></tr>
               <tr><td>Copyright &copy; 2006 InkjetFly.com. All rights reserved.  Powered by <a href="http://www.oscommerce.com"
 target="_blank">osCommerce</a>
<br><br>All trademarks are the sole property of their respective companies. All prices and specifications are subject to change without notice. Inkjetfly.com is not responsible for typographical errors. All typographical errors are subject to correction.

<b><a href=http://oscommerce.com target=_blank class=ml1></a></b></td></tr>
               <tr><td height=30></td></tr>
              </table>      
             </td></tr>
         <tr><td height=20 colspan=4></td></tr>
        </table>
   </td></tr>
  </table>
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td align="center" class="smallText">
<?php

 if (!(getenv('HTTPS')=='on')){
//google banner ad
if (tep_banner_exists('dynamic','googlefoot')  ) {
?>
<br>
<table border="0" width="100%" cellspacing="0" cellpadding="0">
  <tr>
    <td align="center"><?php echo tep_display_banner('dynamic', 'googlefoot'); ?></td>
  </tr>
</table>
<br>
<?php
  } }
?>

<?php 
if ( tep_banner_exists('dynamic','468x50') ) { ?>
<table border="0" width="100%" cellspacing="0" cellpadding="0">
  <tr>
    <td align="center"><?php echo tep_display_banner('dynamic', '468x50'); ?></td>
  </tr>
</table>

<?php
  }
?>

<?php
/*
  The following copyright announcement can only be
  appropriately modified or removed if the layout of
  the site theme has been modified to distinguish
  itself from the default osCommerce-copyrighted
  theme.

  For more information please read the following
  Frequently Asked Questions entry on the osCommerce
  support site:

  http://www.oscommerce.com/community.php/faq,26/q,50

  Please leave this comment intact together with the
  following copyright announcement.
*/

  echo FOOTER_TEXT_BODY
?>
    </td>
  </tr>
</table>
         </td></tr></table>
       </td></tr>
     </table>
   </td>
  </tr>
</table>