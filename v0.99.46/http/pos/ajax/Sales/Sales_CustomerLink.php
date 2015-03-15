<?php
include('../../../../includes/general_functions.php');
include('../../../../includes/sales_functions.php');
#session_start();

if ( $_GET['LinkCustomer']) {
    $_SESSION['sale']['customer_id'] = $_GET['customer_id'];
    sales_choose_customer();
}
?>