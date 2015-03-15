<?php
require_once('../../../includes/general_functions.php');
require_once('../../../includes/inventory_management_functions.php');
require_once('../../../includes/profiles_functions.php');
require_once('../../../includes/item_search_functions.php');
$options = getopt("k:");

$count = 0;
$ipAddress = $_SERVER['REMOTE_ADDR'];
if      ( isset($_GET['EmployeeRemoteKey']) ) {
        $company_id =   set_Company_id_PerEmployeeRemoteKey_and_RemoteIP($ipAddress,$_GET['EmployeeRemoteKey']);}
elseif  ( isset($options['k']) ) {
        $EmployeeRemoteKey = $options['k'];
        $company_id =   set_Company_id_PerEmployeeRemoteKey_and_RemoteIP($ipAddress,$EmployeeRemoteKey);}
else {
                        set_Company_id_PerEmployeeRemoteKey_and_RemoteIP($ipAddress); } // its gonna fail and exit since no Key is passed.
if (is_numeric($company_id) ) {
            ###############
            $general_dal            =  new GENERAL_DAL();
            $companies_data_array['companies_data']['company_id']    =   $company_id;

}

echo json_encode($companies_data_array);
?>