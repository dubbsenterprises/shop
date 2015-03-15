<?php
include('../../../../includes/general_functions.php');
include('../../../../includes/inventory_management_functions.php');

if ( $_GET['delivery_id']) {
    $_SESSION['delivery']['delivery_id'] = $_GET['delivery_id'];
    $_SESSION['delivery']['done'] = 1;
    deliveries();
}
?>
