<?php
/*
  $Id: 2_ordertotals_index_blockright.php,v 1.0.0.0 2007/11/15 01:41:11 wa4u Exp $

  CRE Loaded, Open Source E-Commerce Solutions
  http://www.creloaded.com

  Copyright (c) 2008 CRE Loaded
  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/
if(defined('ADMIN_BLOCKS_OT_STATUS') && ADMIN_BLOCKS_OT_STATUS == 'true'){
  require_once(DIR_WS_CLASSES . 'currencies.php');
   
  $currencies = new currencies();
  global $languages_id, $key;

  $today = date('Y-m-d');
  $yesterday = strtotime( '-1 days' ); // or, strtotime( 'yesterday' );
  $yesterday = date( 'Y-m-d', $yesterday );
  
  //check Highest Order Number
  $highest_order_num = tep_db_fetch_array(tep_db_query("SELECT MAX(orders_id) AS highest_order_num FROM " . TABLE_ORDERS . ""));
  
   // today's
  
  $ot_today_query = tep_db_query("SELECT SUM(ot.value) AS total, COUNT(*) AS count FROM " . TABLE_ORDERS_TOTAL . " ot, " . TABLE_ORDERS . " o, " . TABLE_ORDERS_STATUS . " os WHERE  o.date_purchased LIKE '" . $today . "%' AND ot.orders_id = o.orders_id AND ot.class = 'ot_total' AND os.orders_status_id = o.orders_status AND os.language_id = '" . $_SESSION['languages_id'] . "' AND os.orders_status_id in ('" . str_replace(', ', "', '", ADMIN_BLOCKS_OT_RAW_ORDER_STATUS_MAP  ) . "')");
  $ot_today = tep_db_fetch_array($ot_today_query);
  $todays_total = $ot_today['total'];
  $count = $ot_today['count'];
  
  if ($count > 0) {
    $todays_avarage = $todays_total / $count;
  } else {
    $todays_avarage = 0;
  }
  $num_of_orders_today = $count;
  
  $ot_today_query = tep_db_query("SELECT SUM(ot.value) AS total, COUNT(*) AS count FROM " . TABLE_ORDERS_TOTAL . " ot, " . TABLE_ORDERS . " o, " . TABLE_ORDERS_STATUS . " os WHERE  o.date_purchased LIKE '" . $today . "%' AND ot.orders_id = o.orders_id AND ot.class = 'ot_total' AND os.orders_status_id = o.orders_status AND os.language_id = '" . $_SESSION['languages_id'] . "' AND os.orders_status_id in ('" . str_replace(', ', "', '", ADMIN_BLOCKS_OT_APPROVED_ORDER_STATUS_MAP ) . "')");
  if (tep_db_num_rows($ot_today_query) > 0) {
    $ot_today = tep_db_fetch_array($ot_today_query);
    $todays_total_approved = $ot_today['total'];
    
  } else {
    $todays_total_approved = 0;
  }
  if ($count > 0) {
    $num_orders_apptoday = $ot_today['count'] / $count * 100;
    $todays_avarage_app = $todays_total_approved / $count;
  } else {
    $num_orders_apptoday = 0;
    $todays_avarage_app = 0;
  }
  
  //yesterday's total
  $yesterdays_total = '';
  $ot_today_query = tep_db_query("SELECT SUM(ot.value) AS total, COUNT(*) AS count FROM " . TABLE_ORDERS_TOTAL . " ot, " . TABLE_ORDERS . " o, " . TABLE_ORDERS_STATUS . " os WHERE  o.date_purchased LIKE '" . $yesterday . "%' AND ot.orders_id = o.orders_id AND ot.class = 'ot_total' AND os.orders_status_id = o.orders_status AND os.language_id = '" . $_SESSION['languages_id'] . "' AND os.orders_status_id in ('" . str_replace(', ', "', '", ADMIN_BLOCKS_OT_RAW_ORDER_STATUS_MAP  ) . "')");
  $ot_today = tep_db_fetch_array($ot_today_query);
  $yesterdays_total = $ot_today['total'];
  
  $ot_yesterday_query = tep_db_query("SELECT SUM(ot.value) AS total, COUNT(*) AS count FROM " . TABLE_ORDERS_TOTAL . " ot, " . TABLE_ORDERS . " o, " . TABLE_ORDERS_STATUS . " os WHERE  o.date_purchased LIKE '" . $yesterday . "%' AND ot.orders_id = o.orders_id AND ot.class = 'ot_total' AND os.orders_status_id = o.orders_status AND os.language_id = '" . $_SESSION['languages_id'] . "' AND os.orders_status_id in ('" . str_replace(', ', "', '", ADMIN_BLOCKS_OT_APPROVED_ORDER_STATUS_MAP ) . "')");
  if (tep_db_num_rows($ot_yesterday_query) > 0) {
    $ot_yesterday = tep_db_fetch_array($ot_yesterday_query);
    $yesterdays_total_approved = $ot_yesterday['total'];
  } else {
    $yesterdays_total_approved = 0;
  }
  
  //month's total
  $this_month = date('Y-m-');
  
  $ot_months_query = tep_db_query("SELECT SUM(ot.value) AS total, COUNT(*) AS count FROM " . TABLE_ORDERS_TOTAL . " ot, " . TABLE_ORDERS . " o, " . TABLE_ORDERS_STATUS . " os WHERE o.date_purchased LIKE '" . $this_month . "%' AND ot.orders_id = o.orders_id AND ot.class = 'ot_total' AND os.orders_status_id = o.orders_status AND os.language_id = '" . $_SESSION['languages_id'] . "' AND os.orders_status_id in ('" . str_replace(', ', "', '", ADMIN_BLOCKS_OT_RAW_ORDER_STATUS_MAP  ) . "')");
  
  $ot_month = tep_db_fetch_array($ot_months_query);
  $months_total = $ot_month['total'];
  $mcount = $ot_month['count'];
  
  $ot_month_query = tep_db_query("SELECT SUM(ot.value) AS total, COUNT(*) AS count FROM " . TABLE_ORDERS_TOTAL . " ot, " . TABLE_ORDERS . " o, " . TABLE_ORDERS_STATUS . " os WHERE  o.date_purchased LIKE '" . $this_month . "%' AND ot.orders_id = o.orders_id AND ot.class = 'ot_total' AND os.orders_status_id = o.orders_status AND os.language_id = '" . $_SESSION['languages_id'] . "' AND os.orders_status_id in ('" . str_replace(', ', "', '", ADMIN_BLOCKS_OT_APPROVED_ORDER_STATUS_MAP ) . "')");
  if (tep_db_num_rows($ot_month_query) > 0) {
    $ot_month = tep_db_fetch_array($ot_month_query);
    $month_total_approved = $ot_month['total'];
  } else {
    $month_total_approved = 0;
  }
  
  //last month's total
  if (strtolower(ADMIN_BLOCKS_OT_SHOW_LAST_MONTH) == 'true') {
    $last_month = strtotime( '-1 months' );
    $last_month = date( 'Y-m', $last_month );
    
    $ot_months_query = tep_db_query("SELECT SUM(ot.value) AS total, COUNT(*) AS count FROM " . TABLE_ORDERS_TOTAL . " ot, " . TABLE_ORDERS . " o, " . TABLE_ORDERS_STATUS . " os WHERE o.date_purchased LIKE '" . $last_month . "%' AND ot.orders_id = o.orders_id AND ot.class = 'ot_total' AND os.orders_status_id = o.orders_status AND os.language_id = '" . $_SESSION['languages_id'] . "' AND os.orders_status_id in ('" . str_replace(', ', "', '", ADMIN_BLOCKS_OT_RAW_ORDER_STATUS_MAP  ) . "')");
    
    $ot_month = tep_db_fetch_array($ot_months_query);
    $last_months_total = $ot_month['total'];
    $last_mcount = $ot_month['count'];
    
    $ot_month_query = tep_db_query("SELECT SUM(ot.value) AS total, COUNT(*) AS count FROM " . TABLE_ORDERS_TOTAL . " ot, " . TABLE_ORDERS . " o, " . TABLE_ORDERS_STATUS . " os WHERE  o.date_purchased LIKE '" . $last_month . "%' AND ot.orders_id = o.orders_id AND ot.class = 'ot_total' AND os.orders_status_id = o.orders_status AND os.language_id = '" . $_SESSION['languages_id'] . "' AND os.orders_status_id in ('" . str_replace(', ', "', '", ADMIN_BLOCKS_OT_APPROVED_ORDER_STATUS_MAP ) . "')");
    if (tep_db_num_rows($ot_month_query) > 0) {
      $ot_month = tep_db_fetch_array($ot_month_query);
      $last_month_total_approved = $ot_month['total'];
    } else {
      $last_month_total_approved = 0;
    }
  }
  
  //year's total
  if (strtolower(ADMIN_BLOCKS_OT_SHOW_YTD) == 'true') {
    $this_year = date('Y');
    
    $ot_years_query = tep_db_query("SELECT SUM(ot.value) AS total, COUNT(*) AS count FROM " . TABLE_ORDERS_TOTAL . " ot, " . TABLE_ORDERS . " o, " . TABLE_ORDERS_STATUS . " os WHERE o.date_purchased LIKE '" . $this_year . "%' AND ot.orders_id = o.orders_id AND ot.class = 'ot_total' AND os.orders_status_id = o.orders_status AND os.language_id = '" . $_SESSION['languages_id'] . "' AND os.orders_status_id in ('" . str_replace(', ', "', '", ADMIN_BLOCKS_OT_RAW_ORDER_STATUS_MAP  ) . "')");
    
    $ot_year = tep_db_fetch_array($ot_years_query);
    $years_total = $ot_year['total'];
    $year = $ot_year['count'];
    
    $ot_year_query = tep_db_query("SELECT SUM(ot.value) AS total, COUNT(*) AS count FROM " . TABLE_ORDERS_TOTAL . " ot, " . TABLE_ORDERS . " o, " . TABLE_ORDERS_STATUS . " os WHERE  o.date_purchased LIKE '" . $this_year . "%' AND ot.orders_id = o.orders_id AND ot.class = 'ot_total' AND os.orders_status_id = o.orders_status AND os.language_id = '" . $_SESSION['languages_id'] . "' AND os.orders_status_id in ('" . str_replace(', ', "', '", ADMIN_BLOCKS_OT_APPROVED_ORDER_STATUS_MAP ) . "')");
    if (tep_db_num_rows($ot_year_query) > 0) {
      $ot_year = tep_db_fetch_array($ot_year_query);
      $year_total_approved = $ot_year['total'];
    } else {
      $year_total_approved = 0;
    }
  }
  
  $num_of_orders_month = $mcount;
  if ($mcount > 0) {
    $num_orders_approved = $ot_month['count'] / $mcount * 100;
  } else {
    $num_orders_approved = 0;
  }
?>
  <table width="100%"  border="0" cellspacing="0" cellpadding="0" summary="Order Information">
    <tr valign="top">
      <td width="100%" style="padding-right: 12px;"><div class="form-head-light"><?php cre_index_block_title(BLOCK_TITLE_OT, tep_href_link(FILENAME_ORDERS),BLOCK_HELP_OT);?></div>
      <div class="form-body form-body-fade">
        <ul class="ul_index">
          <li><?php echo BLOCK_CONTENT_OT_HIGHEST_ORDER_NUM . $highest_order_num['highest_order_num'];?></li>
        </ul>
        <ul class="ul_index">
          <li><?php echo BLOCK_CONTENT_OT_TODAY_SO_FAR . $currencies->format($todays_total) . ' (' . $currencies->format($todays_total_approved) . ')';?></li>
          <!--li>Todays Average Order Amount: <?php //echo $currencies->format($todays_avarage);?></li-->
          <li><?php echo BLOCK_CONTENT_OT_MONTH_AVG_AMOUNT . $currencies->format($todays_avarage) . ' (' . $currencies->format($todays_avarage_app) . ')';?></li>
        </ul>
        <ul class="ul_index">
          <li><?php echo BLOCK_CONTENT_OT_YESTERDAYS_ORDERS . $currencies->format($yesterdays_total) . ' (' . $currencies->format($yesterdays_total_approved) . ')';?></li>
          <li><?php echo BLOCK_CONTENT_OT_MONTH_DATE_TOTAL . $currencies->format($months_total) . ' (' . $currencies->format($month_total_approved) . ')';?></li>
<?php
        if (strtolower(ADMIN_BLOCKS_OT_SHOW_LAST_MONTH) == 'true') {
?>
          <li><?php echo BLOCK_CONTENT_OT_LAST_MONTH_TOTAL . $currencies->format($last_months_total) . ' (' . $currencies->format($last_month_total_approved) . ')';?></li>
<?php
        }
        if (strtolower(ADMIN_BLOCKS_OT_SHOW_YTD) == 'true') {
?>
          <li><?php echo BLOCK_CONTENT_OT_YEAR_DATE_TOTAL . $currencies->format($years_total) . ' (' . $currencies->format($year_total_approved) . ')';?></li>
<?php
        }
?>
        </ul>
        <ul class="ul_index">
          <li><!-- <?php //echo BLOCK_CONTENT_TODAYS_ORDERS_PERCENTAGE_APPROVED;?>(<?php echo ADMIN_BLOCKS_OT_APPROVED_ORDER_STATUS_MAP ;?>)<br> -->
            <?php echo BLOCK_CONTENT_OT_THIS_MONTH . $num_orders_approved;?>% | <?php echo BLOCK_CONTENT_OT_TODAY . $num_orders_apptoday;?>% </li>
        </ul>
        <ul class="ul_index">
          <b><a href="#" onclick="window.open('<?php echo tep_href_link('index_block_preference.php', '', 'SSL');?>', 'popupWindow', 'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=yes,copyhistory=no,width=400,height=300,top=200,left=300');"><?php echo BLOCK_CONTENT_OT_PREFERENCE; ?></a></b>
        </ul>
        </div><td>
    </tr>
  </table>
<?php
  }
?>