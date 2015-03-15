<?php
include_once('../../../../includes/calendar_functions.php');
$general_dal                = new GENERAL_DAL();
$PreferenceData             = $general_dal->get_CompanyPreference_by_Company_ID($_SESSION['settings']['company_id'],'timezone');
date_default_timezone_set($PreferenceData[0]->value);
$current_date = date("Y-m-d",mktime());
if (isset($_SESSION['calendar']['display_date'])) {
    $selected_date = $_SESSION['calendar']['display_date'];
} else {
    $selected_date = $current_date;
}
Show_Upcoming_Appts_Calendar_View($selected_date,$current_date)
?>