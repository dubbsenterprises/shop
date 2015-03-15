<?php
include('../../../../includes/general_functions.php');
include('../../../../includes/customers_functions.php');

if (isset($_POST['action']) && $_POST['action'] == 'Send_Email') {
    $Customers_DAL      = new Customers_DAL();
    $general_DAL        = new GENERAL_DAL();

    $customer_id        = urldecode($_POST['customer_id']);
    $type               = urldecode($_POST['type']);

    if (        $type == 'Registration' ) {
        email_Customer_Registration($customer_id,0);
        $response_array['returnCode']   = 1;
        $response_array['type']         = $type;
        $response_array['customer_id']  = $customer_id;
    } else {
        $response_array['returnCode']   = 0;
    }
    
echo json_encode($response_array);
}
?>