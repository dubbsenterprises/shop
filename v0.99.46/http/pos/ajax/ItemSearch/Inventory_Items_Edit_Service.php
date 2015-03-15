<?php
include('../../../../includes/general_functions.php');
include('../../../../includes/inventory_management_functions.php');

if ( $_GET['service_id']) {
    $_SESSION['edit_service']['service_id'] = $_GET['service_id'];
    $_SESSION['edit_service']['Edit_or_Add'] = "Edit";
    $_SESSION['settings']['site'] = "itemmgnt";
    ItemManagement_Add_Edit_Service();
}
?>