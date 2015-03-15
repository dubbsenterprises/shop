<?php
include('../../../../includes/general_functions.php');
include('../../../../includes/inventory_management_functions.php');
$InsertUpdateDelete_Dal = new InsertUpdateDelete_DAL();

if (isset($_POST['action']) && $_POST['action'] == 'AddItem') {
    $new_company_id     = $_SESSION['settings']['company_id'];

    $new_category_id    = empty($_POST['category_id']) ? '0': process_Attribute('Items_CreateNewItem','category_id');
    $new_name           = process_Attribute('Items_CreateNewItem','name');
    $new_buy_price      = process_Attribute('Items_CreateNewItem','buy_price');
    $new_price          = process_Attribute('Items_CreateNewItem','price');
    $new_barcode        = empty($_POST['barcode']) ? date('dymsHi') : process_Attribute('Items_CreateNewItem','barcode');
    $new_number         = process_Attribute('Items_CreateNewItem','number');
    $new_style          = process_Attribute('Items_CreateNewItem','style');

    $new_supplier_id    = empty($_POST['supplier_id']) ? '0': process_Attribute('Items_CreateNewItem','supplier_id');
    $new_brand_id       = empty($_POST['brand_id']) ? '0': process_Attribute('Items_CreateNewItem','brand_id');
    $new_department_id  = empty($_POST['department_id']) ? '0': process_Attribute('Items_CreateNewItem','department_id');
    $new_tax_group_id   = empty($_POST['tax_group_id']) ? '1': process_Attribute('Items_CreateNewItem','tax_group_id');
    $new_attribute1     = empty($_POST['attribute1']) ? '': process_Attribute('Items_CreateNewItem','attribute1');
    $new_attribute2     = empty($_POST['attribute2']) ? '': process_Attribute('Items_CreateNewItem','attribute2');
    $new_discount       = empty($_POST['discount']) ? 0 : process_Attribute('Items_CreateNewItem','discount');
    $new_location       = empty($_POST['location']) ? 1 : process_Attribute('Items_CreateNewItem','location');
    $new_reorder_limit1 = empty($_POST['reorder_limit1']) ? $_SESSION['preferences']['default_reorder_limit1'] : process_Attribute('Items_CreateNewItem','reorder_limit1');
    $new_reorder_limit2 = empty($_POST['reorder_limit2']) ? $_SESSION['preferences']['default_reorder_limit2'] : process_Attribute('Items_CreateNewItem','reorder_limit2');
    $new_online_active  = empty($_POST['online_active']) ? 1 : process_Attribute('Items_CreateNewItem','online_active');

    $login_id           = $_SESSION['settings']['login_id'];
    ######################################
    $sql                = "insert into items
                        (
                            company_id,type,
                            category_id,name,buy_price,price,barcode,number,style,
                            supplier_id,brand_id,department_id,tax_group_id,attribute1,attribute2,discount,location,reorder_limit1,reorder_limit2,online_active,
                            added,login_id
                        )

                        values ("
                            .$new_company_id.","
                            ."1,"

                            .$new_category_id.","
                            .quoteSmart($new_name).","
                            .$new_buy_price.","
                            .$new_price.","
                            .quoteSmart($new_barcode).","
                            .quoteSmart($new_number).","
                            .quoteSmart($new_style).","

                            .quoteSmart($new_supplier_id).","
                            .quoteSmart($new_brand_id).","
                            .quoteSmart($new_department_id).","
                            .quoteSmart($new_tax_group_id).","
                            .quoteSmart($new_attribute1).","
                            .quoteSmart($new_attribute2).","
                            .quoteSmart($new_discount).","
                            .quoteSmart($new_location).","
                            .$new_reorder_limit1.","
                            .$new_reorder_limit2.","
                            .$new_online_active.
                            ",now(),"
                            .$login_id.
                        ")";
$item_id                                = $InsertUpdateDelete_Dal->insert_query($sql);
$response_array['item_id']              = $item_id;
$response_array['itemType']             = 'Item';
$response_array['itemAction']           = 'Added';

$_SESSION['edit_item']['Edit_or_Add']   = "Edit";
$_SESSION['edit_item']['item_id']       = $item_id;
$_SESSION['edit_item']['ActiveTab']     = "editItemImages";
}
elseif (isset($_POST['action']) && $_POST['action'] == 'AddService') {
    $new_company_id     = $_SESSION['settings']['company_id'];

    $new_category_id    = empty($_POST['category_id']) ? '0': process_Attribute('Items_CreateNewService','category_id');
    $new_name           = process_Attribute('Items_CreateNewService','name');
    $new_buy_price      = process_Attribute('Items_CreateNewService','buy_price');
    $new_price          = process_Attribute('Items_CreateNewService','price');
    $new_est_time_mins  = process_Attribute('Items_CreateNewService','est_time_mins');
    $new_barcode        = empty($_POST['barcode']) ? date('dymsHi') : process_Attribute('Items_CreateNewService','barcode');
    $new_style          = process_Attribute('Items_CreateNewService','style');

    $new_department_id  = empty($_POST['department_id']) ? '0': process_Attribute('Items_CreateNewService','department_id');
    $new_tax_group_id   = empty($_POST['tax_group_id']) ? '1': process_Attribute('Items_CreateNewService','tax_group_id');
    $new_attribute1     = empty($_POST['attribute1']) ? '': process_Attribute('Items_CreateNewService','attribute1');
    $new_attribute2     = empty($_POST['attribute2']) ? '': process_Attribute('Items_CreateNewService','attribute2');
    $new_discount       = empty($_POST['discount']) ? 0 : process_Attribute('Items_CreateNewService','discount');
    $new_location       = empty($_POST['location']) ? 1 : process_Attribute('Items_CreateNewService','location');

    $login_id           = $_SESSION['settings']['login_id'];
    ############################################################################
    $sql                = "insert into items
                        (
                            company_id,type,
                            category_id,name,buy_price,price,est_time_mins,barcode,style,
                            department_id,tax_group_id,attribute1,attribute2,discount,location,
                            added,login_id
                        )

                        values ("
                            .$new_company_id.","
                            ."2,"

                            .$new_category_id.","
                            .quoteSmart($new_name).","
                            .$new_buy_price.","
                            .$new_price.","
                            .$new_est_time_mins.","
                            .quoteSmart($new_barcode).","
                            .quoteSmart($new_style).","

                            .quoteSmart($new_department_id).","
                            .quoteSmart($new_tax_group_id).","
                            .quoteSmart($new_attribute1).","
                            .quoteSmart($new_attribute2).","
                            .quoteSmart($new_discount).","
                            .quoteSmart($new_location).

                            ",now(),"
                            .$login_id.
                        ")";
$item_id                                = $InsertUpdateDelete_Dal->insert_query($sql);
$response_array['item_id']              = $item_id;
$response_array['itemType']             = 'Service';
$response_array['itemAction']           = 'Added';

$_SESSION['edit_service']['Edit_or_Add']= "Edit";
$_SESSION['edit_service']['service_id'] = $item_id;
$_SESSION['edit_service']['ActiveTab']  = "editItemAttribute";
}





elseif (isset($_POST['action']) && $_POST['action'] == 'EditItem') {
    $item_id            = $_POST['item_id'];
    $new_category_id    = process_Attribute('Items_UpdateItem','category_id');
    $new_name           = process_Attribute('Items_UpdateItem','name');
    $new_buy_price      = process_Attribute('Items_UpdateItem','buy_price');
    $new_price          = process_Attribute('Items_UpdateItem','price');
    $new_quantity       = process_Attribute('Items_UpdateItem','quantity');
    //Barcode not updated
    $new_number         = process_Attribute('Items_UpdateItem','number');
    $new_style          = process_Attribute('Items_UpdateItem','style');

    $new_supplier_id    = process_Attribute('Items_UpdateItem','supplier_id');
    $new_brand_id       = process_Attribute('Items_UpdateItem','brand_id');
    $new_department_id  = process_Attribute('Items_UpdateItem','department_id');
    $new_tax_group_id   = process_Attribute('Items_UpdateItem','tax_group_id');
    $new_attribute1     = process_Attribute('Items_UpdateItem','attribute1');
    $new_attribute2     = process_Attribute('Items_UpdateItem','attribute2');
    $new_discount       = process_Attribute('Items_UpdateItem','discount');
    $new_location       = process_Attribute('Items_UpdateItem','location');
    $new_reorder_limit1 = empty($_POST['reorder_limit1']) ? $_SESSION['preferences']['default_reorder_limit1'] : process_Attribute('Items_UpdateItem','reorder_limit1');
    $new_reorder_limit2 = empty($_POST['reorder_limit2']) ? $_SESSION['preferences']['default_reorder_limit2'] : process_Attribute('Items_UpdateItem','reorder_limit2');
    $new_online_active  = empty($_POST['online_active']) ? 0 : process_Attribute('Items_UpdateItem','online_active');

    $new_archived       = empty($_POST['archived']) ? 0 : process_Attribute('Items_UpdateItem','archived');

    $login_id           = $_SESSION['settings']['login_id'];
    ############################################################################
    $sql                = "update items set
                        category_id     = $new_category_id,
                        name            =".quoteSmart($new_name).",
                        buy_price       =".$new_buy_price.",
                        price           =".$new_price.",

                        number          =".quoteSmart($new_number).",
                        style           =".quoteSmart($new_style).",

                        supplier_id     =$new_supplier_id,
                        brand_id        =$new_brand_id,
                        department_id   =$new_department_id,
                        tax_group_id    =$new_tax_group_id,
                        attribute1      =".quoteSmart($new_attribute1).",
                        attribute2      =".quoteSmart($new_attribute2).",
                        discount        =".$new_discount.",
                        location        ='".$new_location."',
                        reorder_limit1  =".$new_reorder_limit1.",
                        reorder_limit2  =".$new_reorder_limit2.",
                        archived        =".$new_archived.",
                        online_active   =".$new_online_active.",

                        updated         = now(),
                        login_id        =".$login_id."
                        where id        = $item_id";
$update_id                              = $InsertUpdateDelete_Dal->query($sql);
#print $sql;
$response_array['item_id']              = $item_id;
$response_array['itemType']             = 'Item';
$response_array['itemAction']           = 'Edited';

//$_SESSION['edit_item']['Edit_or_Add'] = "Edit";    // Already set due to this being an edit.  not needed
//$_SESSION['edit_item']['item_id']     = $item_id;  // Already set due to this being an edit.  not needed
$_SESSION['edit_item']['ActiveTab']     = "editItemAttribute";
}
elseif (isset($_POST['action']) && $_POST['action'] == 'EditService') {
    $item_id            = $_POST['item_id'];

    $new_category_id    = process_Attribute('Items_UpdateService','category_id');
    $new_name           = process_Attribute('Items_UpdateService','name');
    $new_buy_price      = process_Attribute('Items_UpdateService','buy_price');
    $new_price          = process_Attribute('Items_UpdateService','price');
    $new_est_time_mins  = process_Attribute('Items_UpdateService','est_time_mins');
    //Barcode not updated
    $new_style          = process_Attribute('Items_UpdateService','style');

    $new_department_id  = process_Attribute('Items_UpdateService','department_id');
    $new_tax_group_id   = process_Attribute('Items_UpdateService','tax_group_id');
    $new_attribute1     = process_Attribute('Items_UpdateService','attribute1');
    $new_attribute2     = process_Attribute('Items_UpdateService','attribute2');
    $new_discount       = process_Attribute('Items_UpdateService','discount');
    $new_location       = process_Attribute('Items_UpdateService','location');
    $new_archived       = empty($_POST['archived']) ? 0 : process_Attribute('Items_UpdateService','archived');
    $new_online_active  = empty($_POST['online_active']) ? 0 : process_Attribute('Items_UpdateService','online_active');

    $login_id           = $_SESSION['settings']['login_id'];
    ############################################################################
    $sql                = "update items set
                        category_id     = $new_category_id,
                        name            =".quoteSmart($new_name).",
                        buy_price       =".$new_buy_price.",
                        price           =".$new_price.",
                        est_time_mins   =".$new_est_time_mins.",

                        number          =".quoteSmart($new_number).",
                        style           =".quoteSmart($new_style).",

                        department_id   =$new_department_id,
                        tax_group_id    =$new_tax_group_id,
                        attribute1      =".quoteSmart($new_attribute1).",
                        attribute2      =".quoteSmart($new_attribute2).",
                        discount        =".$new_discount.",
                        location        ='".$new_location."',
                        archived        =".$new_archived.",
                        online_active   =".$new_online_active.",

                        updated         = now(),
                        login_id        =".$login_id."
                        where id        = $item_id";
$update_id                              = $InsertUpdateDelete_Dal->query($sql);
$response_array['item_id']              = $item_id;
$response_array['itemType']             = 'Service';
$response_array['itemAction']           = 'Edited';

//$_SESSION['edit_service']['Edit_or_Add']= "Edit";    // Already set due to this being an edit.  not needed
//$_SESSION['edit_service']['service_id'] = $item_id;  // Already set due to this being an edit.  not needed
$_SESSION['edit_service']['ActiveTab']  = "editItemAttribute";
}






if       ($_POST['action'] == 'AddItem'      or $_POST['action'] == 'EditItem')   {
    ob_start();
    ItemManagement_EditItemStanza();
} elseif ($_POST['action'] == 'AddService'   or $_POST['action'] == 'EditService'){
    ob_start();
    ItemManagement_EditServiceStanza();
}
$response_array['message']      = ob_get_clean();
$response_array['sql']          = $sql;
$response_array['returnCode']   = 1;
echo json_encode($response_array);

function process_Attribute($serviceORitem_session,$attribute){
    if ($serviceORitem_session = 'Items_CreateNewItem' or $serviceORitem_session = 'Items_CreateNewService')
    $keep_attribute = "keep_" . $attribute ;
    if (isset($_POST[$keep_attribute]) && $_POST[$keep_attribute] == 1) {   $_SESSION[$serviceORitem_session][$keep_attribute] = 1;
                                           $_SESSION[$serviceORitem_session][$attribute]      = urldecode($_POST[$attribute]);}
    else {        $_SESSION[$serviceORitem_session][$keep_attribute] = 0;
            unset($_SESSION[$serviceORitem_session][$attribute]);}
    return urldecode($_POST[$attribute]);

}
?>