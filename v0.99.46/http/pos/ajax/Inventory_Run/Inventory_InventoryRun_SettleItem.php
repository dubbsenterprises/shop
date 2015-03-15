<?php
include('../../../../includes/general_functions.php');
include('../../../../includes/inventory_management_functions.php');

$item_id = $_GET['item_id'];
if ( $item_id) {
    $quantity = $_GET['quantity'] ;
    $iri_id   = $_GET['iri_id'];

    $InsertUpdateDelete_DAL = new InsertUpdateDelete_DAL();
    $sql = "update inventory_run_items 
            set quantity=$quantity,updated=now()
            where id = $iri_id";
    //echo $sql;
    $InsertUpdateDelete_DAL->insert_query($sql);
}
Inventory_ShowItems();
?>