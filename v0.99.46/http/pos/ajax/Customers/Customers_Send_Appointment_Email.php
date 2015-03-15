<?php
include('../../../../includes/general_functions.php');
include('../../../../includes/customers_functions.php');

if (isset($_POST['action']) && $_POST['action'] == 'Send_Email') {
    $Customers_DAL      = new Customers_DAL();
    $general_DAL        = new GENERAL_DAL();

    $appointment_id     = urldecode($_POST['appointment_id']);
    $type               = urldecode($_POST['type']);

    if (        $type == 'Cancelation' ) {
        email_Customer_Cancel_ApptNotification($appointment_id,0);
        $response_array['returnCode']       = 1;
        $response_array['type']             = $type;
        $response_array['appointment_id']   = $appointment_id;
    } else if ( $type == 'Reminder' ) {
        email_Customer_ApptReminder($appointment_id,0);
        $response_array['returnCode']       = 1;
        $response_array['type']             = $type;
        $response_array['appointment_id']   = $appointment_id;
    }
    else {
        $response_array['returnCode']   = 0;
    }
    
echo json_encode($response_array);
}
?>