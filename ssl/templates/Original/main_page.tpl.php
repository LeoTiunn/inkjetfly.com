<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<?php
if ( file_exists(DIR_WS_INCLUDES . 'header_tags.php') ) {
  require(DIR_WS_INCLUDES . 'header_tags.php');
} else {
?>
  <title><?php echo TITLE ?></title>
<?php
}
?>
<base href="<?php echo (($request_type == 'SSL' || stristr($_SERVER['REQUEST_URI'],'ssl/')) ? HTTPS_SERVER : HTTP_SERVER) . DIR_WS_CATALOG; ?>">
<link rel="stylesheet" type="text/css" href="<? echo TEMPLATE_STYLE;?>">
<?php if (isset($javascript) && file_exists(DIR_WS_JAVASCRIPT . basename($javascript))) { require(DIR_WS_JAVASCRIPT . basename($javascript)); } ?>
<link rel="shortcut icon" href="<?php echo (($request_type == 'SSL' || stristr($_SERVER['REQUEST_URI'],'ssl/')) ? HTTPS_SERVER : HTTP_SERVER) . DIR_WS_CATALOG.'favicon.ico'; ?>" type="image/x-icon" />
<link rel="bookmark" href="<?php echo (($request_type == 'SSL' || stristr($_SERVER['REQUEST_URI'],'ssl/')) ? HTTPS_SERVER : HTTP_SERVER) . DIR_WS_CATALOG.'favicon.ico'; ?>" type="image/x-icon" />
</head>
<body>
<!-- warnings //-->
<?php require(DIR_WS_INCLUDES . 'warnings.php'); ?>
<!-- warning_eof //-->

<!-- header //-->
<?php require(DIR_WS_TEMPLATES . TEMPLATE_NAME .'/header.php'); ?>
<!-- header_eof //-->
<!-- body //-->

<table width="960" border="0" align="center" cellpadding="<?php echo CELLPADDING_MAIN; ?>" cellspacing="3">
  <tr>
<?php
if (DOWN_FOR_MAINTENANCE == 'true') {
  $maintenance_on_at_time_raw = tep_db_query("select last_modified from " . TABLE_CONFIGURATION . " WHERE configuration_key = 'DOWN_FOR_MAINTENANCE'");
  $maintenance_on_at_time= tep_db_fetch_array($maintenance_on_at_time_raw);
  define('TEXT_DATE_TIME', $maintenance_on_at_time['last_modified']);
}
?>
<?php
if (DISPLAY_COLUMN_LEFT == 'yes')  {
// WebMakers.com Added: Down for Maintenance
// Hide column_left.php if not to show
if (DOWN_FOR_MAINTENANCE =='false' || DOWN_FOR_MAINTENANCE_COLUMN_LEFT_OFF =='false') {
?>
    <td width="<?php echo BOX_WIDTH_LEFT; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH_LEFT; ?>" cellspacing="0" cellpadding="<?php echo CELLPADDING_LEFT; ?>">
<!-- left_navigation //-->
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- left_navigation_eof //-->
    </table></td>
<?php
}
}
?>
<!-- content //-->
    <td width="100%" valign="top">
<?php
if (isset($content_template) && file_exists(DIR_WS_CONTENT . basename($content_template))) {
    require(DIR_WS_CONTENT . basename($content_template));
  } else {
    require(DIR_WS_CONTENT . $content . '.tpl.php');
  }
?>
    </td>
<!-- content_eof //-->
<?php
// WebMakers.com Added: Down for Maintenance
// Hide column_right.php if not to show


if (DISPLAY_COLUMN_RIGHT == 'yes')  {
if (DOWN_FOR_MAINTENANCE =='false' || DOWN_FOR_MAINTENANCE_COLUMN_RIGHT_OFF =='false') {
?>
    <td width="<?php echo BOX_WIDTH_RIGHT; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH_RIGHT; ?>" cellspacing="0" cellpadding="<?php echo CELLPADDING_RIGHT; ?>">
<!-- right_navigation //-->
<?php require(DIR_WS_INCLUDES . 'column_right.php'); ?>
<!-- right_navigation_eof //-->
    </table></td>
<?php
}
}
?>
  </tr>
</table>


<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_TEMPLATES . TEMPLATE_NAME .'/footer.php'); ?>
<!-- footer_eof //-->


</body>
</html>