<?php
include('../../../../includes/general_functions.php');
include('../../../../includes/inventory_management_functions.php');

if ($_GET['action'] == 'increase') {
    $item_id = $_GET['item_id'];
    $_SESSION['delivery']['items'][$item_id]['quantity']++;
}
elseif ($_GET['action'] == 'decrease') {
    $item_id = $_GET['item_id'];
    if ( $_SESSION['delivery']['items'][$item_id]['quantity'] == 1) {
        unset($_SESSION['delivery']['items'][$item_id]);
    }
    else {
        $_SESSION['delivery']['items'][$item_id]['quantity']--;
    }
}
Deliveries_ShowItems();
?>