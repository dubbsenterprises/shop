<?php
include('../../../../includes/general_functions.php');
include('../../../../includes/customers_functions.php');

if ( $_GET['edit_address_address_id']) {
    $_SESSION['edit_customers']['edit_address_address_id'] = $_GET['edit_address_address_id'];
    customers();
}
?>