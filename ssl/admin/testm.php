<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"> 
<html dir="LTR" lang="en"> 
<head> 
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"> 
  <title>Catalog Tree</title> 
  <script type="text/javascript" src="includes/prototype.js"></script> 
  <link rel="StyleSheet" href="includes/stylesheet.css" type="text/css" /> 
  <script  language="javascript" src="includes/javascript/dhtmlxTree/js/dhtmlXCommon.js"></script> 
  <script  language="javascript" src="includes/javascript/dhtmlxTree/js/dhtmlXTree.js"></script>    
  <script  language="javascript" src="includes/javascript/dhtmlxTree/js/dhtmlXTree_start.js"></script>    
  <link rel="STYLESHEET" type="text/css" href="includes/javascript/dhtmlxTree/css/dhtmlXTree.css"> 
  <script type='text/javascript'><!--
    function cycleCheckboxes() {
      prod_value = "";
      cat_value = "";
      if (checked_value = tree.getAllChecked()) {
        value_array = checked_value.split(",");
        for (i = 0; i < value_array.length; i++) {
          pair = value_array[i].split("_");
          if (pair[0] == "p") {
            prod_value += pair[1] + ",";
          } else {
            cat_value += pair[1] + ",";
          }
        }
      }
window.opener.document.coupon.coupon_products.value = prod_value;window.opener.document.coupon.coupon_categories.value = cat_value;
      window.close();
    }
//--></script> 
</head> 
<body> 
<div class="dtree"> 
  <p><a href="javascript:void(0);" onclick="tree.openAllItems(0);">open all</a> | <a href="javascript:void(0);" onclick="tree.closeAllItems(0);">close all</a></p> 
  <br> 
  <div id="cat_tree" style="width:100%;height:400"></div> 
  <script> 
    tree = new dhtmlXTreeObject("cat_tree", "100%", "100%", 0); 
    tree.enableCheckBoxes(1);
    tree.enableThreeStateCheckboxes(true);
    tree.setImagePath("includes/javascript/dhtmlxTree/imgs/"); 
    tree.loadXML("https://p7.secure.hostingprod.com/@www.inkjetfly.com/ssl/admin/get_categories.php?osCAdminID=d4206aeb1a7a2c82643ac2e5f856960d"); 
  </script> 
  <br><br> 
  <input type="button" onClick="cycleCheckboxes()" value="OK" /> 
</div> 
</body> 
</html> 