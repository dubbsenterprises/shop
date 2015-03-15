<?php
include('../../../../includes/general_functions.php');
include('../../../../includes/inventory_management_functions.php');

if ( $_GET['inventory_run_id']) {
    if ( $_GET['inventory_run_id'] != $_SESSION['inventory_run_backup']['inventory_run_id'] ) {
        unset($_SESSION['inventory_run']['items']);
    }
    $_SESSION['inventory_run']['inventory_run_id'] = $_GET['inventory_run_id'];
    $_SESSION['inventory_run_backup']['inventory_run_id'] = $_GET['inventory_run_id'];

    $_SESSION['inventory_run']['done'] = 1;
    inventory();
}
?>
