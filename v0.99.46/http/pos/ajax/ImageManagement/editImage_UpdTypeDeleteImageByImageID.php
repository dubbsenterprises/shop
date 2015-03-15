<?php
include('../../../../includes/general_functions.php');
if ( $_GET['image_id']) {
    $image_id       = $_GET['image_id'];
    $column_name    = $_GET['column_name'];
    $image_db_id    = $_GET['image_db_id'];


    $delete_image_sql       = "update item_image_mappings set deleted=convert_tz(now(), \"".date_default_timezone_get()."\", \"UTC\") where image_id = $image_id  and image_db_id = $image_db_id";
    $dal        = new InsertUpdateDelete_DAL();
    $update_id  = $dal->insert_query($delete_image_sql);

    $image_type_function_dataDAL    = new GENERAL_DAL();
    $image_type_function_data       = $image_type_function_dataDAL->get_ImageTypeFunctionData($column_name);
    include('../../../../includes/'.$image_type_function_data[0]->php_reload_include);
    $function_name = $image_type_function_data[0]->php_reload_function;
    $function_name();
}
?>