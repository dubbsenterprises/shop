<?php
session_start();
include_once('../../../includes/general_functions.php');
include_once('../../../includes/inventory_management_functions.php');
include_once('../../../includes/appointment_functions.php');
include_once('../../../includes/customers_functions.php');
include_once('../../'.$_SESSION['settings']['templateType'].'/'.$_SESSION['settings']['templateType'].'_functions.php');

$inventory_dal  = new INVENTORY_DAL();
$customers_dal  = new Customers_DAL();
if ($_POST['appointment_id']) {
    $appointment_id     = $_POST['appointment_id'];
    $customer_id        = $_POST['customer_id'];
    $deleted_by_type    = $_POST['deleted_by_type'];
    $deleted_by_id      = $_POST['deleted_by_id'];
    #  Delete from local database
    deleteDubbsDBAppointment($appointment_id,$deleted_by_type,$deleted_by_id);
    #  Delete from remote Calendar
    deleteRemoteAppointment($appointment_id);
}
    # Customer
    if ($deleted_by_type == 1 ) {
        ob_start();
        show_appt_history($customer_id);
        $response_array['html'] = ob_get_clean();
    }
    # Employee
    else if ($deleted_by_type == 0){
        ob_start();
        CustomerAppointmentHistory($customers_dal,$customer_id);
        $response_array['html'] = ob_get_clean();
    }
$response_array['returnCode'] = 1;
echo json_encode($response_array);
?>