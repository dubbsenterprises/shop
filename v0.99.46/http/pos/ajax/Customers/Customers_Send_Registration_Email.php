<?php
include('../../../../includes/general_functions.php');
include('../../../../includes/customers_functions.php');

if (isset($_POST['action']) && $_POST['action'] == 'Send_Registration_Email') {
    $customer_id    = urldecode($_POST['customer_id']);

    $Customers_DAL  = new Customers_DAL();
    $general_DAL    = new GENERAL_DAL();

    email_Customer_Registration($customer_id,0);
    $response_array['returnCode']       = 1;
    $response_array['function_called']  = "email_Customer_Registration($customer_id,0)";

    echo json_encode($response_array);
}
?>