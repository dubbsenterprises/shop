<?php
include('../../../includes/general_functions.php');
include('../../../includes/inventory_management_functions.php');
include('../'.$_SESSION['settings']['templateType'].'_functions.php');

$inventory_dal  = new INVENTORY_DAL();
if ($_POST['appointment_id']) {
    $appointment_id = $_POST['appointment_id'];
    #  Delete from local database
    deleteDubbsDBAppointment($appointment_id);
    #  Delete from remote Calendar
    deleteRemoteAppointment($appointment_id);
}

ob_start();
customer_id_show_appt_history($appointment_id);
$response_array['html'] = ob_get_clean();
$response_array['returnCode'] = 1;
echo json_encode($response_array);
?>