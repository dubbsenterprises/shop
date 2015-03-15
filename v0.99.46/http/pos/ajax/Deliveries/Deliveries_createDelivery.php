<?php
include('../../../../includes/general_functions.php');
include('../../../../includes/inventory_management_functions.php');

if ( $_GET['createDelivery'] == "1") {
    unset($_SESSION['delivery']);
    $_SESSION['delivery']['supplier_id'] = $_GET['supplier_id'];
    $_SESSION['delivery']['done'] = 0;
    deliveries();
}
?>
