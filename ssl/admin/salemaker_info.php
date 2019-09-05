<?php
/*
  $Id: salemaker_info.php,v 1.1.1.1 2004/03/04 23:38:57 ccwjr Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/
define('DISPLAYTTT', 'True');

  require("includes/application_top.php");

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<script type="text/javascript" src="includes/prototype.js"></script>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<!--[if IE]>
<link rel="stylesheet" type="text/css" href="includes/stylesheet-ie.css">
<![endif]-->
</head>
<body>
<p class="main"><center><h1><?php echo HEADING_TITLE; ?><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></h1></center></p>
<table width="90%" align="center">
<p class="main"><h3><?php echo SUBHEADING_TITLE; ?></h3></p>
<div class="main">
<?php echo INFO_TEXT; ?>
</div>
<p align="center" class="main"><a href="javascript:window.close();"><?php echo TEXT_CLOSE_WINDOW; ?></a></p>
<?php
if ( (defined('DISPLAYTTT')) && (DISPLAYTTT == 'True') ) {
?>
<center><?php echo tep_draw_separator('pixel_trans.gif', '4', '1') . tep_image(DIR_WS_IMAGES . 'thinktank.jpg'); ?></center>
<?php
} else {
  echo '<br>';
}
?>
</body>
</html>
<?php
  require(DIR_WS_INCLUDES . 'application_bottom.php');
?>
