<?php
require_once('../../../../includes/general_functions.php');
require_once('../../../../includes/inventory_management_functions.php');
session_start();
?>

<?php
if ( $_GET['updCategory'] == "1") {
        $category_id             = $_GET['category_id'] ;
        $upd_itemcategory_name  = $_GET['upd_itemcategory_name'] ;
        $upd_attribute1         = $_GET['upd_attribute1'] ;
        $upd_attribute2         = $_GET['upd_attribute2'] ;
        $updCategorySql = "update  categories
            set name=".quoteSmart($upd_itemcategory_name).",
                attribute1=".quoteSmart($upd_attribute1).",
                attribute2=".quoteSmart($upd_attribute2).",
                updated=convert_tz(now(), 'utc', 'america/chicago')
                where id = $category_id";
        //echo $updCategorySql;
        $dal = new InsertUpdateDelete_DAL();
        $updateCategory = $dal->insert_query($updCategorySql);
    }
    categoriesStanza($category_id);
?>
