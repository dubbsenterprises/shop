<?php
include('../../../../includes/general_functions.php');
include('../../../../includes/customers_functions.php');

if ( $_GET['edit_customers']) {
    unset($_SESSION['edit_customers']['CustomerAdd']);
    unset($_SESSION['edit_customers']['edit_address_address_id']);
    $_SESSION['edit_customers']['customer_id'] = $_GET['customer_id'];
    customers();
}
?>