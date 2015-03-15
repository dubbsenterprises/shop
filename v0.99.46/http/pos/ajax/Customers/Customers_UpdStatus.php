<?php
include('../../../../includes/general_functions.php');
include('../../../../includes/customers_functions.php');

if ( $_GET['editCustomer']) {
    $login_id   = $_GET['customer_id'];
    $action     = $_GET['action'];
    $sql        = "update customers set status = $action where id = $login_id";
    $dal        = new InsertUpdateDelete_DAL();
    $update_id  = $dal->insert_query($sql);
    //unset($_SESSION['search_data']['Customers_AllCustomers']['customer_search_inactive_customers']);
    customers();
}
?>