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
if ($company_id == 0 ) { 
            ###############
            $general_dal            =  new GENERAL_DAL();
            $companies_list         = $general_dal->appointment_GetCompanysForReminderEmails();
                if (count($companies_list) > 0 ) {
                    foreach($companies_list as $company_data){
                            $company_customer_emails_count = 0 ;
                            $appointment_list  = $general_dal->appointment_GetAppointmentsToReceiveReminderemails($company_data->id,$company_data->timezone);
                            foreach ($appointment_list as $appointment_data ) {
                                #print $appointment_data->id . "-" . $appointment_data->startDate . "-" . $appointment_data->endDate  . "<br>\n";
                                email_Customer_ApptReminder($appointment_data->id,0); 
                                $company_customer_emails_count++ ;
                            }
                        //print $company_data->id . " " . $company_data->name . "<br><br>\n" ;
                        $companies_data_array['companies_data']['companies'][$company_data->id]['company_customer_emails_count']   = $company_customer_emails_count;
                    $count++;
                    }
                    $companies_data_array['companies_data']['count']    =   $count;
                } else {
                    $companies_data_array['companies_data']['count']    =   $count;
                }
            echo json_encode($companies_data_array);
}
?>