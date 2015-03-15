<?php
include('../../../../includes/general_functions.php');
include('../../../../includes/inventory_management_functions.php');

if ( $_GET['ActiveTab']) {
    $_SESSION['edit_service']['ActiveTab'] = $_GET['ActiveTab'];
    ItemManagement_EditServiceStanza('Edit');
}
?>