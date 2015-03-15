<?php
session_start();
include('../../../includes/general_functions.php');
include('../../../includes/inventory_management_functions.php');
include('../../../includes/appointment_functions.php');
include('../'.$_SESSION['settings']['templateType'].'_functions.php');

$general_dal = new GENERAL_DAL();
$inventory_dal = new INVENTORY_DAL();

if ($_POST['service_id']) {
    $service_id = $_POST['service_id'];
    if (!isset($_SESSION['appointment_book']['total_time']))            { $_SESSION['appointment_book']['total_time']           = 0; }
    if (!isset($_SESSION['appointment_book']['total_services_price']))  { $_SESSION['appointment_book']['total_services_price'] = 0; }
    $Inventory_DAL = new INVENTORY_DAL;
    $service_data = $Inventory_DAL->ServiceManagement_ServicesProperties($service_id);
    if (isset($_SESSION['appointment_book']['services_selected'][$service_id])) {
        ### turn OFF OFF OFF
        unset($_SESSION['appointment_book']['services_selected'][$service_id]);
              $_SESSION['appointment_book']['total_time']                               -= $service_data[0]->est_time_mins;
              $_SESSION['appointment_book']['total_services_price']                     -= intval($service_data[0]->price);
        
        $response_array['returnCode']           = 0;
        $response_array['service_id']           = intval($service_id);
        $response_array['est_time_mins']        = intval($service_data[0]->est_time_mins);
        $response_array['total_services_times'] = $_SESSION['appointment_book']['total_time'];
        $response_array['total_services_price'] = $_SESSION['appointment_book']['total_services_price'];

    } else {
        ### turn ON ON ON ON
        $_SESSION['appointment_book']['services_selected'][$service_id]['service_time']  = array($service_data[0]->est_time_mins);
        $_SESSION['appointment_book']['services_selected'][$service_id]                  = intval($service_id);
        $_SESSION['appointment_book']['total_time']                                     += $service_data[0]->est_time_mins;
        $_SESSION['appointment_book']['total_services_price']                           += intval($service_data[0]->price);
        
        $response_array['returnCode']           = 1;
        $response_array['service_id']           = $_SESSION['appointment_book']['services_selected'][$service_id];
        $response_array['est_time_mins']        = intval($service_data[0]->est_time_mins);
        $response_array['total_services_times'] = $_SESSION['appointment_book']['total_time'];
        $response_array['total_services_price'] = $_SESSION['appointment_book']['total_services_price'];
    }
}
    ob_start();
    make_appointment_choost_staff($general_dal,'step_1');
    $response_array['html'] = ob_get_clean();

echo json_encode($response_array);
?>
