<?php
include('../../../../includes/general_functions.php');
include('../../../../includes/inventory_management_functions.php');

if ($_GET['item_id']) {
    $item_id = $_GET['item_id'];
    $_SESSION['delivery']['items'][$item_id]['item_id']       = $item_id;
    $_SESSION['delivery']['items'][$item_id]['buy_price']     = $_GET['buy_price'];
    $_SESSION['delivery']['items'][$item_id]['sell_price']    = $_GET['sell_price'];
    $_SESSION['delivery']['items'][$item_id]['quantity']      = $_GET['quantity'];
    Deliveries_ShowItems();
}
?>