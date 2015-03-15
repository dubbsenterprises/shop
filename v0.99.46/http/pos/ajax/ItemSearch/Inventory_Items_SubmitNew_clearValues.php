<?php
include('../../../../includes/general_functions.php');
include('../../../../includes/inventory_management_functions.php');

function process_thing($attribute){
  $keep_attribute = "keep_" . $attribute ;
  unset($_SESSION['Items_CreateNew'][$keep_attribute]);
  unset($_SESSION['Items_CreateNew'][$attribute]);
}
process_thing('attribute1');
process_thing('attribute2');
process_thing('barcode');
process_thing('brand');
process_thing('category');
process_thing('department');
process_thing('description');  
process_thing('discount');
process_thing('item_name');
process_thing('last_buy_price');
process_thing('location');
process_thing('price');
process_thing('quantity');
process_thing('online_active');
process_thing('reorder_limit_1');
process_thing('reorder_limit_2');
process_thing('style_number');
process_thing('supplier');
process_thing('taxgroup');

    ItemManagement_AddItemStanza();
?>