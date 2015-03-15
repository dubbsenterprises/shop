<?php
include('../../../../includes/general_functions.php');
include('../../../../includes/inventory_management_functions.php');

if ($_GET['item_id']) {
    $item_id = $_GET['item_id'];
    $dal  = new INVENTORY_DAL();
    $itemInfo = $dal->deliveries_ItemsInfoByItemID($item_id);

    $_SESSION['inventory_run']['items'][$item_id]['item_id']  = $item_id;
    $_SESSION['inventory_run']['items'][$item_id]['quantity'] = $_GET['quantity'];
    $_SESSION['inventory_run']['items'][$item_id]['pos_quantity'] = $itemInfo[0]->quantity;
    Inventory_ShowItems();
}
?>