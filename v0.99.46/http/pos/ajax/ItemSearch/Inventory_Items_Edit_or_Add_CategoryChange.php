<?php
include('../../../../includes/general_functions.php');
include('../../../../includes/inventory_management_functions.php');
#    $keep_attribute = "keep_" . $attribute ;
     if ($_POST['changeCategory'] == 1 && $_POST['category_id'] != -1 ) {
        $category_id   = $_POST['category_id'] ;
        $Inventory_DAL = new INVENTORY_DAL();
        $CategoryNames = $Inventory_DAL->ItemManagement_GetCategoryAttributeNames($category_id);
        $attributeName1 = $CategoryNames[0]->attribute1;
        $attributeName2 = $CategoryNames[0]->attribute2;
     }
     else {
         $attributeName1 = "Attribute1";
         $attributeName2 = "Attribute2";
     }
    $response_array['attribute1'] = $attributeName1;
    $response_array['attribute2'] = $attributeName2;
    echo json_encode($response_array);
?>