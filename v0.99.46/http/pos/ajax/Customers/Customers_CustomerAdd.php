<?php
include('../../../../includes/general_functions.php');
include('../../../../includes/customers_functions.php');
#session_start();

if ( $_GET['CustomerAdd']) {
    $_SESSION['edit_customers']['CustomerAdd']= 1;
    unset($_SESSION['edit_customers']['customer_id']);
    customers();
}
?>