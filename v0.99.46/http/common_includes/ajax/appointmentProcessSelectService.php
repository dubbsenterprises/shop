<?php
session_start();
include_once('../../../includes/general_functions.php');
include_once('../../../includes/inventory_management_functions.php');
include_once('../../../includes/appointment_functions.php');
include_once('../../../includes/customers_functions.php');
include_once('../../'.$_SESSION['settings']['templateType'].'/'.$_SESSION['settings']['templateType'].'_functions.php');
$general_dal = new GENERAL_DAL();

##  total time
if ($_POST['service_id']) {
    $service_id = $_POST['service_id'];
    if (!isset($_SESSION['appointment_book']['total_time']))            { $_SESSION['appointment_book']['total_time']           = 0; }
    if (!isset($_SESSION['appointment_book']['total_services_price']))  { $_SESSION['appointment_book']['total_services_price'] = 0; }
    $Inventory_DAL              = new INVENTORY_DAL;
    $Appointments_dal           = new Appointments_DAL();
        $response_array['employeePricesSet_or_not'] = $employeePricesSet_or_not;
    $service_data               = $Inventory_DAL->ServiceManagement_ServicesProperties($service_id);
    $employeePricesSet_or_not   = $Appointments_dal->Appointments_CountActiveServicesWithEmployeePrice_by_company_id($_SESSION['settings']['company_id']);

    if (isset($_SESSION['appointment_book']['services_selected'][$service_id])) {
        ### turn OFF OFF OFF
        unset($_SESSION['appointment_book']['services_selected'][$service_id]);
            $_SESSION['appointment_book']['total_time']                                     -= $service_data[0]->est_time_mins;
            $_SESSION['appointment_book']['total_services_price']                           -= intval($service_data[0]->price); 
        $response_array['returnCode']           = 0;
        $response_array['service_id']           = intval($service_id);
        $response_array['est_time_mins']        = intval($service_data[0]->est_time_mins);
        $response_array['total_services_times'] = $_SESSION['appointment_book']['total_time'];
        $response_array['total_services_price'] = $_SESSION['appointment_book']['total_services_price'];
        $response_array['total_services_selected']= count($_SESSION['appointment_book']['services_selected']);
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
        $response_array['total_services_selected']= count($_SESSION['appointment_book']['services_selected']);
    }
    
    #  clear out staff_id if they deselected all services
    if (isset($_SESSION['appointment']['staff_id']) && count($_SESSION['appointment_book']['services_selected']) == 0 ) {
        unset($_SESSION['appointment']['staff_id']);
        $_SESSION['appointment_book']['total_services_price'] = 0;
        $response_array['total_services_price'] = $_SESSION['appointment_book']['total_services_price'];        
    }
    
    ##  total cost
    if (isset($_SESSION['appointment']['staff_id'])){
        $response_array['staff_id']             = $_SESSION['appointment']['staff_id'];
        $response_array['total_services_price'] = appointments_calculate_total_service_price_by_login_id();;
    } else {
        $response_array['staff_id']             = 0;    
    }  
    
}


ob_start();
make_appointment_choost_staff($general_dal);
$response_array['html'] = ob_get_clean();
$response_array['employeePricesSet_or_not'] = count($employeePricesSet_or_not);
echo json_encode($response_array);
?>