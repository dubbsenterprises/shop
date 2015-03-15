<?php
include_once('../../../includes/general_functions.php');
include_once('../../../includes/inventory_management_functions.php');
include_once('../../../includes/appointment_functions.php');
include_once('../../../includes/customers_functions.php');
include_once('../../'.$_SESSION['settings']['templateType'].'/'.$_SESSION['settings']['templateType'].'_functions.php');
if ( $_GET['step'] == 1) {
    if ( isset($_GET['staff_id']) ) { 
        load_staff_info($_GET['staff_id']); 
        appointments_calculate_total_service_price_by_login_id();
    }
    if ( !(isset($_SESSION['appointment']['remote_appointment_set']))   ) {
        unset($_SESSION['appointment']['step4']);
        unset($_SESSION['appointment']['step3']);
        unset($_SESSION['appointment']['step2']);
    }
    $_SESSION['appointment']['step']        = 1;
    $_SESSION['appointment']['step1']       = 1;
    if (isset($_SESSION['appointment']['step2'])) { $_SESSION['appointment']['step2'] = 2; }
    if (isset($_SESSION['appointment']['step3'])) { $_SESSION['appointment']['step3'] = 2; }
    if (isset($_SESSION['appointment']['step4'])) { $_SESSION['appointment']['step4'] = 2; }

    }

if ( $_GET['step'] == 2) {
    if ( !(isset($_SESSION['appointment']['remote_appointment_set'])) ) {
        unset($_SESSION['appointment']['step4']);
        unset($_SESSION['appointment']['step3']);
    }
    if ( !(isset($_SESSION['appointment']['selected_date'])) ) { $_SESSION['appointment']['selected_date']  = date("Y-m-d");}
    $_SESSION['appointment']['step']        = 2;
    $_SESSION['appointment']['step2']       = 1;
    $_SESSION['appointment']['step1']       = 2;
    if (isset($_SESSION['appointment']['step3'])) { $_SESSION['appointment']['step3'] = 2; }
    if (isset($_SESSION['appointment']['step4'])) { $_SESSION['appointment']['step4'] = 2; }

    if ( isset($_GET['staff_id']) ) { 
        load_staff_info($_GET['staff_id']); }
    }

if ( $_GET['step'] == 3) {
    if ( !(isset($_SESSION['appointment']['remote_appointment_set'])) ) {
        unset($_SESSION['appointment']['step4']);
    }
    $_SESSION['appointment']['step']       = 3;
    $_SESSION['appointment']['step3']      = 1;
    $_SESSION['appointment']['step1']= $_SESSION['appointment']['step2'] = 2;
    if (isset($_SESSION['appointment']['step4'])) { $_SESSION['appointment']['step4'] = 2; }

    if (isset($_GET['selected_apt_time']) && $_GET['selected_apt_time'] != '' ) { $_SESSION['appointment']['selected_apt_time']   = $_GET['selected_apt_time'];   }
    }

if ( $_GET['step'] == 4) {
    $_SESSION['appointment']['step']       = 4;
    $_SESSION['appointment']['step4']      = 1;
    $_SESSION['appointment']['step1']= $_SESSION['appointment']['step2'] = $_SESSION['appointment']['step3'] = 2;
    }
    
    appointments();
    
    function load_staff_info($staff_id){
            $_SESSION['appointment']['staff_id']   = $staff_id;
            require_once('../../../includes/profiles_functions.php');
            $profiles_dal           = new Profiles_DAL();
            $load_staff_info        = $profiles_dal->get_EmployeeDataPerLoginId($staff_id);
            $_SESSION['appointment'][$_SESSION['appointment']['staff_id']]['gmail_username']    = $load_staff_info[0]->gmail_username;
            $_SESSION['appointment'][$_SESSION['appointment']['staff_id']]['gmail_password']    = $load_staff_info[0]->gmail_password;
            $_SESSION['appointment']['staff_firstname']                                         = $load_staff_info[0]->firstname;
            $_SESSION['appointment']['staff_surname']                                           = $load_staff_info[0]->lastname;
            $_SESSION['appointment']['staff_email_address']                                     = $load_staff_info[0]->email_address;        
    }
?>