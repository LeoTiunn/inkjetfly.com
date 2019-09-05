<?php
/*
  $Id: column_right.php,v 1.1.1.1 2004/03/04 23:40:37 ccwjr Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 osCommerce

  Released under the GNU General Public License
*/

  $column_query = tep_db_query('select infobox_id, display_in_column as cfgcol, infobox_file_name as cfgtitle, infobox_display as cfgvalue, infobox_define as cfgkey, box_template, box_heading_font_color from ' . TABLE_INFOBOX_CONFIGURATION . ' where template_id = ' . TEMPLATE_ID . ' and infobox_display = "yes" and display_in_column = "right" order by location');
    while ($column = tep_db_fetch_array($column_query)) {
      if ( file_exists(DIR_FS_TEMPLATE_BOXES . '/boxes/' . $column['cfgtitle'])) {
        $box_heading = tep_get_box_heading($column['infobox_id'], $languages_id);
        define($column['cfgkey'], $box_heading);
        //$infobox_define = $box_heading;
        $infobox_template = $column['box_template'];
        $infobox_template_heading = $infobox_template . 'Heading';
        $infobox_template_footer = $infobox_template . 'Footer';
        $font_color = $column['box_heading_font_color'];
        $infobox_class = $column['box_template'];
        //cache control side box detect
        if ((USE_CACHE == 'true') && empty($SID) && ($column['cfgtitle'] == 'categories1.php') ) {
          echo tep_cache_categories_box();
        } else if ((USE_CACHE == 'true') && empty($SID) && ($column['cfgtitle'] == 'categories.php') ) {
          echo tep_cache_categories_box();
        } else if ((USE_CACHE == 'true') && empty($SID) && ($column['cfgtitle'] == 'categories2.php') ) {
          echo tep_cache_categories_box1();
        } else if ((USE_CACHE == 'true') && empty($SID) && ($column['cfgtitle'] == 'categories3.php') ) {
          echo tep_cache_categories_box3();
        } else if ((USE_CACHE == 'true') && empty($SID) && ($column['cfgtitle'] == 'categories4.php') ) {
          echo tep_cache_categories_box4();
        } else if ((USE_CACHE == 'true') && empty($SID) && ($column['cfgtitle'] == 'categories5.php') ) {
          echo tep_cache_categories_box5();
        } else if ((USE_CACHE == 'true') && empty($SID) && ($column['cfgtitle'] == 'coolmenu.php') ) {
          echo tep_cache_coolmenu();
        } else if ((USE_CACHE == 'true') && empty($SID) && ($column['cfgtitle'] == 'manufacturers.php') ) {
          echo tep_cache_manufacturers_box();
        } else {
          require(DIR_FS_TEMPLATE_BOXES . '/' . $column['cfgtitle']);
        }
        // end cache control code   
      } else {
        $box_heading = tep_get_box_heading($column['infobox_id'], $languages_id);
        //echo 'side ' . $column['infobox_id'] . $box_heading . $languages_id;
        if (!defined($column['cfgkey'])) {
          define($column['cfgkey'],$box_heading);
        }        
        //$infobox_define = $box_heading;
        $infobox_template = $column['box_template'];
        $infobox_template_heading = $infobox_template . 'Heading';
        $infobox_template_footer = $infobox_template . 'Footer';
        $font_color = $column['box_heading_font_color'];   
        $infobox_class = $column['box_template'];
        if(file_exists(DIR_FS_TEMPLATE_BOXES . '/' . $column['cfgtitle'])){
        require_once(DIR_FS_TEMPLATE_BOXES . '/' . $column['cfgtitle']);
        } else if(file_exists(DIR_WS_TEMPLATES . 'default/' . $column['cfgtitle'])){
        require_once(DIR_WS_TEMPLATES . 'default/' . $column['cfgtitle']);
        } else {
        echo "\n" . '<!-- missing infobox ' . $column['cfgtitle'] . ' -->' . "\n";
        }
      }
    }
?>