<?php
include('../../../../includes/general_functions.php');
include('../../../../includes/profiles_functions.php');
include('../../../../includes/class.googlevoice.php');

if ( $_POST['Customers_Employee_Call_Customer']) {
    $response_array['count']        = 1;
    $response_array['success']      = 1;
    $response_array['customer_id']  = urldecode($_POST['customer_id']);
    $response_array['to']           = $_POST['to'];
    $response_array['from']         = $_POST['from'];
try {
    $gv = new GoogleVoice('vertical222@gmail.com', 'Nos1eb1tch');
    $gv->call($response_array['from'],$response_array['to']);
    //$gv->sms(17734564205, 'Test');
    $response_array['error'] = 0;
} catch (Exception $e) {
    $response_array['error'] = $e->getMessage();
}

echo json_encode($response_array);
}
?>