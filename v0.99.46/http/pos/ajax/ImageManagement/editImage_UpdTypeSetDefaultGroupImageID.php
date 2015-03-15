<?php
include('../../../../includes/general_functions.php');
$General_DAL            = new General_DAL();
$InsertUpdateDelete_dal = new InsertUpdateDelete_DAL();

if ( $_GET['image_id']) {
    $image_id       = $_GET['image_id'];
    $column_name    = $_GET['column_name'];
    $image_db_id    = $_GET['image_db_id'];
    $style_number   = $_GET['style_number'];
    $company_id     = $_GET['company_id'];

    $image_type_id  = $General_DAL->item_typeId_by_type($column_name);
    $type_id        = $image_type_id[0]->id;

    $default_status_sql       = "update item_image_mappings as iim join items i on iim.id = i.id set iim.default_group_image = 0  where i.number = '$style_number'  and i.company_id = $company_id";
    $update_status_sql        = "update item_image_mappings set `default_group_image`=1  where image_id = $image_id and image_db_id = $image_db_id";

    $update_id  = $InsertUpdateDelete_dal->insert_query($default_status_sql);
    $update_id  = $InsertUpdateDelete_dal->insert_query($update_status_sql);

    $image_type_function_data       = $General_DAL->get_ImageTypeFunctionData($column_name);
    include('../../../../includes/'.$image_type_function_data[0]->php_reload_include);
    $function_name = $image_type_function_data[0]->php_reload_function;
    $function_name();
}
?>