<?php
include('../../../../includes/general_functions.php');
include('../../../../includes/inventory_management_functions.php');

if ( $_GET['createInventory_Run'] == "1") {
    unset($_SESSION['inventory_run']);
    $_SESSION['inventory_run']['created_by_login_id'] = $_SESSION['settings']['login_id'];
    $_SESSION['inventory_run']['created_datetime'] = date("Y-m-d H:i:s", time());
    $_SESSION['inventory_run']['done'] = 0;
    inventory();
}
?>