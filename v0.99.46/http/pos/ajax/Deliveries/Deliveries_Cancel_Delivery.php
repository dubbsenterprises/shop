<?php
include('../../../../includes/general_functions.php');
include('../../../../includes/inventory_management_functions.php');

if ( $_GET['cancelDelivery'] == "1") {
    unset($_SESSION['delivery']);
    deliveries();
}
?>
