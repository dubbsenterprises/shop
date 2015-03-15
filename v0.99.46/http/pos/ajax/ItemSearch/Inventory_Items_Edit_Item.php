<?php
include('../../../../includes/general_functions.php');
include('../../../../includes/inventory_management_functions.php');

if ( $_GET['item_id']) {
    $_SESSION['edit_item']['item_id'] = $_GET['item_id'];
    $_SESSION['edit_item']['Edit_or_Add'] = "Edit";
    $_SESSION['settings']['site'] = "itemmgnt";
    ItemManagement_Add_Edit_Item();
}
?>