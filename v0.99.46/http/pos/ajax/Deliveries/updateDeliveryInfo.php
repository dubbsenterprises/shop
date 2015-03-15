<?php
include('../../../../includes/general_functions.php');
include('../../../../includes/inventory_management_functions.php');

if ( $_GET['updateDeliveryInfo'] == "1") {
    $_SESSION['delivery']['done'] = 0;
    $_SESSION['delivery']['DeliveryInfoComplete'] = 1 ;

    $_SESSION['delivery']['ordered'] = $_GET['ordered'];
    $_SESSION['delivery']['invoice_no'] = $_GET['invoice_no'];

    $_SESSION['delivery']['shipped'] = $_GET['shipped'];
    $_SESSION['delivery']['delivered_via'] = $_GET['delivered_via'];

    $_SESSION['delivery']['shipping_costs'] = $_GET['shipping_costs'];

    $_SESSION['delivery']['received'] = $_GET['received'];
    $_SESSION['delivery']['receiver_id'] = $_GET['receiver_id'];

    $_SESSION['delivery']['purchase_order_no'] = $_GET['purchase_order_no'];
}
?>
