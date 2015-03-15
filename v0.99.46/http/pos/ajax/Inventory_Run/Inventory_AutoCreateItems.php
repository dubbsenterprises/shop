<?php
include('../../../../includes/general_functions.php');
include('../../../../includes/inventory_management_functions.php');

if ($_GET['item_count']) {
    $item_count = $_GET['item_count'];
    $dal  = new INVENTORY_DAL();
    unset($_SESSION['inventory_run']['items']);
    $items = $dal->Inventory_AutoCreateItems($_SESSION['settings']['company_id'],$item_count);
    foreach($items as $item)
        {
            $_SESSION['inventory_run']['items'][$item->item_id]['item_id']  = $item->item_id;
            $_SESSION['inventory_run']['items'][$item->item_id]['quantity'] = 0;
            $_SESSION['inventory_run']['items'][$item->item_id]['pos_quantity'] = $item->quantity;
        }
    Inventory_ShowItems();
}
?>