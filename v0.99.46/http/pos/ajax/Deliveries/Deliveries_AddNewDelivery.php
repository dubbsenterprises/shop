<?php
include('../../../../includes/general_functions.php');
include('../../../../includes/inventory_management_functions.php');

if ( $_GET['createDelivery'] == "1") {
$dal = new InsertUpdateDelete_DAL();
    if (isset($_SESSION['bad'])) {
            $_SESSION['message'] = "ERROR: Delivery could not be added since at least one mandatory detail is missing or wrong! Whatever is missing or wrong is marked in red.";
    } else {
            $supplier_id    = $_SESSION['delivery']['supplier_id'];
            $ordered        = $_SESSION['delivery']['ordered'];
            $invoice_no     = $_SESSION['delivery']['invoice_no'];
            $shipped        = $_SESSION['delivery']['shipped'];
            $delivered_via  = $_SESSION['delivery']['delivered_via'];
            $shipping_costs = $_SESSION['delivery']['shipping_costs'];
            $received       = $_SESSION['delivery']['received'];
            $receiver_id    = $_SESSION['delivery']['receiver_id'];
            $purchase_order_no = $_SESSION['delivery']['purchase_order_no'];
            $added          = 0;
            $sql = "insert into deliveries
                (supplier_id,login_id,ordered,invoice_no,shipped,delivered_via,
                    shipping_costs,received,receiver_id,purchase_order_no,added)
                values ($supplier_id,$receiver_id,'$ordered','$invoice_no','$shipped','$delivered_via',
                    $shipping_costs,'$received',$receiver_id,'$purchase_order_no',now()
                    )" ;
            $delivery_id    = $dal->insert_query($sql);
            foreach (array_keys($_SESSION['delivery']['items']) as $item_id) {
                    $item = $_SESSION['delivery']['items'][$item_id];
                    $Inventory_DAL = new INVENTORY_DAL();
                    $itemInfo = $Inventory_DAL->deliveries_ItemsInfoByItemID($_SESSION['delivery']['items'][$item_id]['item_id']);
                    $new_quantity = $itemInfo[0]->quantity + $_SESSION['delivery']['items'][$item_id]['quantity'] ;
                    $delivery_items_sql = "insert into delivery_items
                        (delivery_id,item_id,buy_price,sell_price,quantity)
                        values(".$delivery_id.","
                                .$_SESSION['delivery']['items'][$item_id]['item_id'].","
                                ."'".$_SESSION['delivery']['items'][$item_id]['buy_price']."',"
                                ."'".$_SESSION['delivery']['items'][$item_id]['sell_price']."',"
                                .$_SESSION['delivery']['items'][$item_id]['quantity'].
                    ")";
                    $addDeliveryItemsToDeliveriesTable    = $dal->insert_query($delivery_items_sql);

                    $delivery_update_item_sql = "update items set
                        quantity=".$new_quantity.",
                        buy_price ='".$_SESSION['delivery']['items'][$item_id]['buy_price'] ."',
                        price='".$_SESSION['delivery']['items'][$item_id]['sell_price']."'
                        where id = ".$item_id;
                    $updateDeliveryItemsToDeliveryItemsTable    = $dal->insert_query($delivery_update_item_sql);
                    #echo $delivery_update_item_sql."<br>".$updateDeliveryItemsToDeliveryItemsTable;
            }

            unset($_SESSION['delivery'],$_SESSION['delivery']['items']);
            #$_SESSION['settings']['itemmgnt']['delivery_id'] = $delivery_id;
            #$_SESSION['message'] = 'You successfully added a delivery.';
    }
    deliveries();
    #echo $sql;
}
?>
