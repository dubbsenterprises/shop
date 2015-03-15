<?php
require_once('../../../../includes/general_functions.php');
require_once('../../../../includes/inventory_management_functions.php');
session_start();
?>

<?php
if ( $_GET['addNewCategory'] == "1") {
        $company_id             = $_GET['company_id'] ;
        $new_itemcategory_name  = $_GET['new_itemcategory_name'] ;
        $new_attribute1         = $_GET['new_attribute1'] ;
        $new_attribute2         = $_GET['new_attribute2'] ;
        $addNewCategorySql = "insert into categories
            (company_id,type,name,attribute1,attribute2,added)
            values ($company_id,'shop',".quoteSmart($new_itemcategory_name).",".quoteSmart($new_attribute1).",".quoteSmart($new_attribute2).",convert_tz(now(), 'utc', 'america/chicago') );
            ";
        #echo $sql;
        $dal = new InsertUpdateDelete_DAL();
        $insertCategory = $dal->insert_query($addNewCategorySql);
    }
    categoriesStanza($category_id);
?>
