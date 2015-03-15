<?php
include('../../../../includes/general_functions.php');
include('../../../../includes/inventory_management_functions.php');

if ( $_GET['item_id']) {
    $item_id = $_GET['item_id'];
    $quantity = $_GET['quantity'] ;
    $iri_id   = $_GET['iri_id'];

    $_SESSION['inventory_run']['items'][$item_id]['updated'] = 1;
    $InsertUpdateDelete_DAL = new InsertUpdateDelete_DAL();

    $update_quantity_sql=  "update items 
                            set quantity =".$quantity.", updated = now(), login_id = ".$_SESSION['settings']['login_id']."
                            where id = $item_id";
                            $InsertUpdateDelete_DAL->insert_query($update_quantity_sql);

    $update_inventory_sql ="update inventory_run_items 
                            set quantity=$quantity,updated=now() 
                            where id = $iri_id";
                            $InsertUpdateDelete_DAL->insert_query($update_inventory_sql);
}
Inventory_ShowItems();
?>