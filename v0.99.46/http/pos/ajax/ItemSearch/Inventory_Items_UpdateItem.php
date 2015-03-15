<?php
include('../../../../includes/general_functions.php');
include('../../../../includes/inventory_management_functions.php');

function process_Attribute($attribute){
    $_SESSION['Items_edit_item'][$attribute]      = urldecode($_POST[$attribute]);
    return urldecode($_POST[$attribute]);
}
process_Attribute('attribute1');
process_Attribute('attribute2');
process_Attribute('brand');
process_Attribute('category');
process_Attribute('supplier');
process_Attribute('department');
process_Attribute('description');
process_Attribute('discount');
process_Attribute('item_name');
process_Attribute('last_buy_price');
process_Attribute('location');
process_Attribute('price');
process_Attribute('quantity');
process_Attribute('reorder_limit_1');
process_Attribute('reorder_limit_2');
process_Attribute('archived');
process_Attribute('style_number');
process_Attribute('taxgroup');
process_Attribute('archived');


if ($_POST['EditItem'] == 1) {
    //$dal = new INVENTORY_DAL();
    //$ItemInfo= $dal->deliveries_ItemInfoByItemID($item_id);
    //    $new_category_id    = $ItemInfo[0]->category_id;
    $item_id            = $_POST['item_id'] = $_SESSION['edit_item']['item_id'];
    $new_attribute1     = process_Attribute('attribute1');
    $new_attribute2     = process_Attribute('attribute2');
    $new_brand_id       = process_Attribute('brand');
    $new_category_id    = process_Attribute('category');
    $new_supplier_id    = process_Attribute('supplier');
    $new_department_id  = process_Attribute('department');
    $new_description    = process_Attribute('description');
    $new_discount       = process_Attribute('discount');
    $new_name           = process_Attribute('item_name');
    $new_last_buy_price = process_Attribute('last_buy_price');
    $new_location       = process_Attribute('location');
    $new_price          = process_Attribute('price');
    $new_quantity       = process_Attribute('quantity');
    $new_reorder_limit1 = empty($_POST['reorder_limit_1']) ? $_SESSION['preferences']['default_reorder_limit1'] : process_Attribute('reorder_limit_1');;
    $new_reorder_limit2 = empty($_POST['reorder_limit_2']) ? $_SESSION['preferences']['default_reorder_limit2'] : process_Attribute('reorder_limit_2');;
    $new_archived       = empty($_POST['archived']) ? 0 : process_Attribute('archived');;
    $new_style_number   = process_Attribute('style_number');;
    $new_tax_group_id   = process_Attribute('taxgroup');;
    $login_id           = $_SESSION['settings']['login_id'];

    $sql = "update items set
            supplier_id     =$new_supplier_id,
            brand_id        =$new_brand_id,
            department_id   =$new_department_id,
            category_id     =$new_category_id,
            tax_group_id    =$new_tax_group_id,
            name            =".quoteSmart($new_name).",
            number          =".quoteSmart($new_style_number).",
            style           =".quoteSmart($new_description).",
            attribute1      =".quoteSmart($new_attribute1).",
            attribute2      =".quoteSmart($new_attribute2).",
            buy_price       =".$new_last_buy_price.",
            price           =".$new_price.",
            discount        =".$new_discount.",
            location        ='".$new_location."',
            reorder_limit1  =".$new_reorder_limit1.",
            reorder_limit2  =".$new_reorder_limit2.",
            added           = now(),
            login_id        =".$login_id.",
            archived        =".$new_archived."
            where id        = $item_id";
            $mySQL      = new InsertUpdateDelete_DAL();
            $item_id    = $mySQL->insert_query($sql);

    $_SESSION['edit_item']['ActiveTab']     = "editItemAttribute";
    ob_start();
        ItemManagement_EditItemStanza($_SESSION['edit_item']['Edit_or_Add']);
    $response_array['message']      = ob_get_clean();
    $response_array['sql']          = $sql;
    $response_array['returnCode']   = 1;
    echo json_encode($response_array);
}
?>