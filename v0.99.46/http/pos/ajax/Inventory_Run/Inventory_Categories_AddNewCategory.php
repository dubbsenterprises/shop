<?php
require_once('../../../../includes/inventory_management_functions.php');
$inventory_dal = new INVENTORY_DAL();

    $_SESSION['category']['addNewCategory'] = array();
    $_SESSION['category']['addNewCategory'] == 1;
    AddNewCategoryStanza($inventory_dal);
?>