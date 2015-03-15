<?php
include('../../../../includes/general_functions.php');
include('../../../../includes/inventory_management_functions.php');

if ($_GET['action'] == 'increase') {
    $item_id = $_GET['item_id'];
    $_SESSION['inventory_run']['items'][$item_id]['quantity']++;
}
elseif ($_GET['action'] == 'decrease') {
    $item_id = $_GET['item_id'];
    if ( $_SESSION['inventory_run']['items'][$item_id]['quantity'] == 1 || $_SESSION['inventory_run']['items'][$item_id]['quantity'] <= 0) {
        unset($_SESSION['inventory_run']['items'][$item_id]);
    }
    else {
        $_SESSION['inventory_run']['items'][$item_id]['quantity']--;
    }
}
Inventory_ShowItems();
?>