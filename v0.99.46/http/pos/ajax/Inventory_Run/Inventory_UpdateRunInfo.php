<?php
include('../../../../includes/general_functions.php');
include('../../../../includes/inventory_management_functions.php');

if ( $_GET['Inventory_RunInfo'] == "1") {
    $_SESSION['inventory_run']['done']  = 0;
    $_SESSION['inventory_run']['RunInfoComplete'] = 1;
    $_SESSION['inventory_run']['inventory_run_login_id']    = $_GET['inventory_run_login_id'];
    $_SESSION['inventory_run']['inventory_run_notes']       = $_GET['inventory_run_notes'];
    //Inventory();
}
?>
