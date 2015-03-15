<?php
include('../../../../includes/general_functions.php');
include('../../../../includes/inventory_management_functions.php');

if ($_GET['style_number']) {
    $new_company_id = $_SESSION['settings']['company_id'];
    $style_number   = $_GET['style_number'];
    $new_attribute1 = $_GET['attribute1'];
    $new_attribute2 = $_GET['attribute2'];
    $new_quantity   = $_GET['quantity'];
    $new_buy_price  = $_GET['buy_price'];
    $new_price      = $_GET['sell_price'];
    $new_barcode    = empty($_GET['new_barcode']) ? date('dymsHi') : $_GET['new_barcode'];
    $new_reorder_limit1 = empty($_GET['new_reorder_limit1']) ? $_SESSION['preferences']['default_reorder_limit1'] : $_GET['new_reorder_limit1'];
    $new_reorder_limit2 = empty($_GET['new_reorder_limit2']) ? $_SESSION['preferences']['default_reorder_limit2'] : $_GET['new_reorder_limit2'];


    $dal = new INVENTORY_DAL();
    $ItemInfo= $dal->deliveries_ItemInfoByStyleNumber($style_number);

    $new_category_id    = $ItemInfo[0]->category_id;
    $new_supplier_id    = $ItemInfo[0]->supplier_id ;
    $new_brand_id       = $ItemInfo[0]->brand_id ;
    $new_department_id  = $ItemInfo[0]->department_id ;
    $new_tax_group_id   = $ItemInfo[0]->tax_group_id ;
    $new_name           = $ItemInfo[0]->name ;
    $new_style          = $ItemInfo[0]->style ;
    $new_location       = $ItemInfo[0]->location ;

    $sql = "insert into items
        (company_id,supplier_id,brand_id,department_id,category_id,tax_group_id,name,
        number,style,attribute1,attribute2,barcode,buy_price,price,
        location,quantity,reorder_limit1,reorder_limit2,added)
        values (
        ".$new_company_id.",
        ".$new_supplier_id.",
        ".$new_brand_id.",
        ".$new_department_id.",
        ".$new_category_id.",
        ".$new_tax_group_id.",
        '".$new_name."',
        '".$style_number."',
        ".quoteSmart($new_style).",
        '".$new_attribute1."',
        '".$new_attribute2."',
        '".$new_barcode."',
        ".$new_buy_price.",
        ".$new_price.",
        1,
        0,
        ".$new_reorder_limit1.",
        ".$new_reorder_limit2.",
        now() )";
    #print $sql;
    $mySQL      = new InsertUpdateDelete_DAL();
    $item_id    = $mySQL->insert_query($sql);

    $_SESSION['delivery']['items'][$item_id]['item_id']       = $item_id;
    $_SESSION['delivery']['items'][$item_id]['buy_price']     = $new_buy_price;
    $_SESSION['delivery']['items'][$item_id]['sell_price']    = $new_price;
    $_SESSION['delivery']['items'][$item_id]['quantity']      = $new_quantity;
    Deliveries_ShowItems();
}
?>