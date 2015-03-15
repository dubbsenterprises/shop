<?php
include('../../../../includes/general_functions.php');
$General_DAL        = new General_DAL();   #$ImageDatabaseID[0]->image_db_id

if ( $_GET['image_id']) {
    $image_id       = $_GET['image_id'];
    $profile_id     = $_GET['profile_id'];
    $column_name    = $_GET['column_name'];
    $image_db_id    = $_GET['image_db_id'];

    $image_type_id  = $General_DAL->item_typeId_by_type($column_name);
    $type_id        = $image_type_id[0]->id;

    $default_status_sql       = "update item_image_mappings set `default_item_image`=0,`default`=0 where image_type_id = $type_id and id = $profile_id";
    $update_status_sql        = "update item_image_mappings set `default_item_image`=1,`default`=1 where image_id = $image_id and image_db_id = $image_db_id";

    
    $dal        = new InsertUpdateDelete_DAL();
    $update_id  = $dal->insert_query($default_status_sql);
    $update_id  = $dal->insert_query($update_status_sql);

    $image_type_function_dataDAL    = new GENERAL_DAL();
    $image_type_function_data       = $image_type_function_dataDAL->get_ImageTypeFunctionData($column_name);
    include('../../../../includes/'.$image_type_function_data[0]->php_reload_include);
    $function_name = $image_type_function_data[0]->php_reload_function;
    $function_name();
}
?>