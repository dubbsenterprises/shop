<?php
include('../../../../includes/general_functions.php');
include('../../../../includes/inventory_management_functions.php');
$inventory_dal  = new INVENTORY_DAL();

if ($_POST['action'] == 'Sales_Add_Item') {
    $item_id        = urldecode($_POST['item_id']);
    $Item_info      = $inventory_dal->Inventory_ItemInfoByItemID($item_id);

    $response_array['returnCode']               = 1;
    $response_array['itemsArray']               = $_SESSION['sale']['basket']['items'];
    $response_array['item_id']                  = $item_id;

    $sale           = &$_SESSION['sale'];
    $item           = &$sale['basket']['items'][$item_id];
    if( isset($item['quantity']) ) {$item['quantity']++;} else {$item['quantity'] = 1; }
    $item['real_discount']      = $item['discount'] = $Item_info[0]->discount;
    $item['real_price']         = $item['price'] = $Item_info[0]->price;
    $item['rtax']               = $Item_info[0]->tax === null ? $_SESSION['preferences']['tax'] : $Item_info[0]->tax;
    $item['tax']                = isset($sale['no_tax']) ? 0 : $item['rtax'];
    $item['brand']              = $Item_info[0]->brand_name;
    $item['name']               = $Item_info[0]->name;
    $item['attribute1']         = $Item_info[0]->attribute1;
    $item['attributename1']     = $Item_info[0]->attribute1;
    $item['attribute2']         = $Item_info[0]->attribute2;
    $item['attributename2']     = $Item_info[0]->attribute2;
    $item['barcode']            = $Item_info[0]->barcode;
    $item['number']             = $Item_info[0]->number;
    $sale['basket']['lastitemid'] = $item['id'] = $item_id;
    echo json_encode($response_array);
}
?>