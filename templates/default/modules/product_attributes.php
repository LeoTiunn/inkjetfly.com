<?php
    $products_attributes_query = tep_db_query("select count(*) as total from " . TABLE_PRODUCTS_ATTRIBUTES . " where products_id='" . (int)$product_info['products_id'] . "' ");
    $products_attributes = tep_db_fetch_array($products_attributes_query);
    if ($products_attributes['total'] > 0) {
      // the tax rate will be needed, so get it once
      $tax_rate = tep_get_tax_rate($product_info['tax_class_id']);
?>
            <table width="100%" border="0" cellspacing="0" cellpadding="2">
              <tr>
                <td class="main" colspan="2"><strong><?php echo TEXT_PRODUCT_OPTIONS; ?></strong></td>
              </tr>
              <?php
      $products_options_query = tep_db_query("select pa.options_id, pa.options_values_id, pa.options_values_price, pa.price_prefix, po.options_type, po.options_length, pot.products_options_name, pot.products_options_instruct from
           " . TABLE_PRODUCTS_ATTRIBUTES  . " AS pa, 
           " . TABLE_PRODUCTS_OPTIONS  . " AS po,
           " . TABLE_PRODUCTS_OPTIONS_TEXT  . " AS pot
           where pa.products_id = '" . (int)$_GET['products_id'] . "'
             and pa.options_id = po.products_options_id
             and po.products_options_id = pot.products_options_text_id and pot.language_id = '" . (int)$languages_id . "'
           order by pa.products_options_sort_order, po.products_options_sort_order
           ");
      
      // Store the information from the tables in arrays for easy of processing
      $options = array();
      $options_values = array();
      while ($po = tep_db_fetch_array($products_options_query)) {
        //  we need to find the values name 
        if ( $po['options_type'] != 1  && $po['options_type'] != 4 ) {
          $options_values_query = tep_db_query("select products_options_values_name from " . TABLE_PRODUCTS_OPTIONS_VALUES . " where products_options_values_id ='". $po['options_values_id'] . "' and language_id = '" . (int)$languages_id . "'");
          $ov = tep_db_fetch_array($options_values_query);
        } else {
          $ov['products_options_values_name'] = '';
        }
        $options[$po['options_id']] = array('name' => $po['products_options_name'],
                                            'type' => $po['options_type'],
                                            'length' => $po['options_length'],
                                            'instructions' => $po['products_options_instruct'],
                                            'price' => $po['options_values_price'],
                                            'prefix' => $po['price_prefix'],
                                            );

        $options_values[$po['options_id']][$po['options_values_id']] =  array('name' => stripslashes($ov['products_options_values_name']),
                                                                              'price' => $po['options_values_price'],
                                                                              'prefix' => $po['price_prefix']);
      }

      foreach ($options as $oID => $op_data) {
        switch ($op_data['type']) {
            
          case 1:
            $maxlength = ( $op_data['length'] > 0 ? ' maxlength="' . $op_data['length'] . '"' : '' );
            $attribute_price = $currencies->display_price($op_data['price'], $tax_rate);
            $tmp_html = '<input type="text" name="id[' . $oID . '][t]"' . $maxlength . '>';
?>
              <tr>
                <td class="main"><?php
                echo $op_data['name'] . ':' . ($op_data['instructions'] != '' ? '<br><span class="smallText">' . $op_data['instructions'] . '</span>' : '' );
                echo ($attribute_price >= 0 ? '<br><span class="smallText">' . $op_data['prefix'] . ' ' . $attribute_price . '</span>' : '' );
                 ;?>
                </td>
                <td class="main"><?php echo $tmp_html;  ?></td>
              </tr>
              <?php
            break;

          case 4:
            $text_area_array = explode(';',$op_data['length']);
            $cols = $text_area_array[0];
            if ( $cols == '' ) $cols = '100%';
            $rows = $text_area_array[1];
            $attribute_price = $currencies->display_price($op_data['price'], $tax_rate);

            $tmp_html = '<textarea name="id[' . $oID . '][t]" rows="'.$rows.'" cols="'.$cols.'" wrap="virtual"></textarea>';
?>
              <tr>
                <td class="main"><?php
                echo $op_data['name'] . ':' . ($op_data['instructions'] != '' ? '<br><span class="smallText">' . $op_data['instructions'] . '</span>' : '' );
                echo ($attribute_price >= 0 ? '<br><span class="smallText">' . $op_data['prefix'] . ' ' . $attribute_price . '</span>' : '' );
                 ;?>
                </td>
                <td class="main" align="center"><?php echo $tmp_html;  ?></td>
              </tr>
              <?php
            break;
      
          case 2:
            $tmp_html = '';
            foreach ( $options_values[$oID] as $vID => $ov_data ) {
              if ( (float)$ov_data['price'] == 0 ) {
                  $price = '&nbsp;';
              } else {
                  $price = '(&nbsp;' . $ov_data['prefix'] . '&nbsp;' . $currencies->display_price($ov_data['price'], $tax_rate) . '&nbsp;)';
              }
              $tmp_html .= '<input type="radio" name="id[' . $oID . ']" value="' . $vID . '">' . $ov_data['name'] . '&nbsp;' . $price . '<br>';
            } // End of the for loop on the option value
?>
              <tr>
                <td class="main"><?php echo $op_data['name'] . ':' . ($op_data['instructions'] != '' ? '<br><span class="smallText">' . $op_data['instructions'] . '</span>' : '' ); ?></td>
                <td class="main"><?php echo $tmp_html;  ?></td>
              </tr>
              <?php
            break;
          
          case 3:
            $tmp_html = '';
            $i = 0;
            foreach ( $options_values[$oID] as $vID => $ov_data ) {
              if ( (float)$ov_data['price'] == 0 ) {
                $price = '&nbsp;';
              } else {
                $price = '(&nbsp;'.$ov_data['prefix'] . '&nbsp;' . $currencies->display_price($ov_data['price'], $tax_rate).'&nbsp;)';
              }
              $tmp_html .= '<input type="checkbox" name="id[' . $oID . '][c][' . $i . ']" value="' . $vID . '">' . $ov_data['name'] . '&nbsp;' . $price . '<br>';
              $i++;
            }
?>
              <tr>
                <td class="main"><?php echo $op_data['name'] . ':' . ($op_data['instructions'] != '' ? '<br><span class="smallText">' . $op_data['instructions'] . '</span>' : '' ); ?></td>
                <td class="main"><?php echo $tmp_html;  ?></td>
              </tr>
              <?php
            break;
            
          case 0:
            $tmp_html = '<select name="id[' . $oID . ']">';
            foreach ( $options_values[$oID] as $vID => $ov_data ) {
              if ( (float)$ov_data['price'] == 0 ) {
                $price = '&nbsp;';
              } else {
                $price = '(&nbsp; '.$ov_data['prefix'] . '&nbsp;' . $currencies->display_price($ov_data['price'], $tax_rate).'&nbsp;)';
              }
              $tmp_html .= '<option value="' . $vID . '">' . $ov_data['name'] . '&nbsp;' . $price .'</option>';
            } // End of the for loop on the option values
            $tmp_html .= '</select>';
?>
              <tr>
                <td class="main"><?php echo $op_data['name'] . ':' . ($op_data['instructions'] != '' ? '<br><span class="smallText">' . $op_data['instructions'] . '</span>' : '' ); ?></td>
                <td class="main"><?php echo $tmp_html;  ?></td>
              </tr>
              <?php
            break;
        }  //end of switch
      } //end of while
?>
            </table>
            <?php
    } // end of ($products_attributes['total'] > 0)
?>