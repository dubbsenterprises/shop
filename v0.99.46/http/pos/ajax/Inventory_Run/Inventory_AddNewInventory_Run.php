<?php
include('../../../../includes/general_functions.php');
include('../../../../includes/inventory_management_functions.php');

if ( $_GET['createInventory_Run'] == "1") {
$dal = new InsertUpdateDelete_DAL();
    if (isset($_SESSION['bad'])) {
            $_SESSION['message'] = "ERROR: Inventory Run could not be added since at least one mandatory detail is missing or wrong! Whatever is missing or wrong is marked in red.";
    } else {
            $company_id     = $_SESSION['settings']['company_id'];
            $start_date     = $_SESSION['inventory_run']['created_datetime'];
            $assigned_login_id      = $_SESSION['inventory_run']['inventory_run_login_id'];
            $created_by_login_id    = $_SESSION['inventory_run']['created_by_login_id'];
            $notes          = $_SESSION['inventory_run']['inventory_run_notes'];
            $added          = "now()";
            $sql = "insert into inventory_run
                    values (NULL,$company_id,
                            '$start_date',
                            $assigned_login_id,
                            $created_by_login_id,
                            ".quoteSmart($notes).",
                            now()
                    )" ;
            $inventory_run_id    = $dal->insert_query($sql);
            foreach (array_keys($_SESSION['inventory_run']['items']) as $item_id) {
                    $item = $_SESSION['inventory_run']['items'][$item_id];
                    $Inventory_DAL = new INVENTORY_DAL();
                    $itemInfo = $Inventory_DAL->deliveries_ItemsInfoByItemID($_SESSION['inventory_run']['items'][$item_id]['item_id']);

                    $Inventory_Run_items_sql = "insert into inventory_run_items
                        (id,inventory_run_id,item_id,pos_quantity,quantity)
                        values(NULL,"
                                .$inventory_run_id.","
                                .$_SESSION['inventory_run']['items'][$item_id]['item_id'].","
                                .$_SESSION['inventory_run']['items'][$item_id]['pos_quantity'].","
                                .$_SESSION['inventory_run']['items'][$item_id]['quantity'].
                    ")";
                    $addInventory_RunItemsToDeliveriesTable    = $dal->insert_query($Inventory_Run_items_sql);
            }

            unset($_SESSION['inventory_run'],$_SESSION['inventory_run']['items']);
    }
    inventory();
    #echo $sql;
}
?>
