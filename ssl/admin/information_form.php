<?PHP
  /*
  Module: Information Pages Unlimited
  		  File date: 2003/03/02
		  Based on the FAQ script of adgrafics
  		  Adjusted by Joeri Stegeman (joeri210 at yahoo.com), The Netherlands

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Released under the GNU General Public License
  */
?>
<tr class=pageHeading><td><?php echo $title ?></td></tr>
<tr class="dataTableRow"><td><font color=red>
<?
echo QUEUE_INFORMATION_LIST;
$data=browse_information();
$no=1;
if (sizeof($data) > 0) {
  while (list($key, $val)=each($data)) {
		echo "$val[v_order], ";
		$no++;
		}
}
?>
</font>
</td></tr>
	<tr><td>
<table border="0" cellpadding=0 cellspacing=2">
<tr><td><?php echo QUEUE_INFORMATION;?> </td>
<td>
<?php if ($edit[v_order]) {$no=$edit[v_order];}; echo tep_draw_input_field('v_order', "$no", 'size=3 maxlength=4'); ?>
<?php
echo VISIBLE_INFORMATION;
if ($edit[visible]==1) {
echo tep_image(DIR_WS_ICONS . 'icon_status_green.gif', INFORMATION_ID_ACTIVE);
}else{
echo tep_image(DIR_WS_ICONS . 'icon_status_red.gif', INFORMATION_ID_DEACTIVE);
}
?>
<?php if ($edit[visible]) {$checked= "checked";}; echo tep_draw_checkbox_field('visible', '1', "$checked") . VISIBLE_INFORMATION_DO; ?>
</td>
</tr>

<tr><td><?php echo TITLE_INFORMATION;?><br></td>
	<td>


<?php echo tep_draw_input_field('info_title', "$edit[info_title]", 'maxlength=255'); ?></td>
</tr>

<tr><td><?php echo DESCRIPTION_INFORMATION;?><br>
</td>
<td>


<?php echo tep_draw_textarea_field('description', '', '60', '10', "$edit[description]"); ?></td>
  <?php if (HTML_AREA_WYSIWYG_DISABLE == 'Disable') {} else { ?>
          <script language="JavaScript1.2" defer>
// MaxiDVD Added WYSIWYG HTML Area Box + Admin Function v1.7 - 2.2 MS2 HTML Email HTML - <body>
           var config = new Object();  // create new config object
           config.width = "<?php echo EMAIL_AREA_WYSIWYG_WIDTH; ?>px";
           config.height = "<?php echo EMAIL_AREA_WYSIWYG_HEIGHT; ?>px";
           config.bodyStyle = 'background-color: <?php echo HTML_AREA_WYSIWYG_BG_COLOUR; ?>; font-family: "<?php echo HTML_AREA_WYSIWYG_FONT_TYPE; ?>"; color: <?php echo HTML_AREA_WYSIWYG_FONT_COLOUR; ?>; font-size: <?php echo HTML_AREA_WYSIWYG_FONT_SIZE; ?>pt;';
           config.debug = <?php echo HTML_AREA_WYSIWYG_DEBUG; ?>;
           editor_generate('description',config);
<?php }
// MaxiDVD Added WYSIWYG HTML Area Box + Admin Function v1.7 - 2.2 MS2 HTML Email HTML - <body>
   ?>
          </script>
</tr>
<tr><td></td>
<td align=right>
<?php
echo tep_image_submit('button_insert.gif', IMAGE_INSERT);
echo '<a href="' . tep_href_link(FILENAME_INFORMATION_MANAGER, '', 'NONSSL') . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>';
 ?>
</td>
</tr>
</table>
</form>
	</td></tr>
