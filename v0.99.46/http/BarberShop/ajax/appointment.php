<?php
session_start();
include('../../'.$_SESSION['settings']['templateType'].'/'.$_SESSION['settings']['templateType'].'_functions.php');

if ( $_GET['step'] == "1") {
    $_SESSION['appointment']['step']        = 1;
    $_SESSION['appointment']['step1']       = 1;
    if (isset($_SESSION['appointment']['step2'])) { $_SESSION['appointment']['step2'] = 2; }
    if (isset($_SESSION['appointment']['step3'])) { $_SESSION['appointment']['step3'] = 2; }
    if (isset($_SESSION['appointment']['step4'])) { $_SESSION['appointment']['step4'] = 2; }
    #if (isset($_SESSION['appointment_book']['services_selected'])) { unset($_SESSION['appointment_book']['services_selected']);}
    #if (isset($_SESSION['appointment_book']['total_time'])) { unset($_SESSION['appointment_book']['total_time']);}
    appointments();
    }


if ( $_GET['step'] == "2") {
    $_SESSION['appointment']['selected_date']  = date("Y-m-d");
    $_SESSION['appointment']['step']        = 2;
    $_SESSION['appointment']['step2']       = 1;
    $_SESSION['appointment']['step1']       = 2;
    if (isset($_SESSION['appointment']['step3'])) { $_SESSION['appointment']['step3'] = 2; }
    if (isset($_SESSION['appointment']['step4'])) { $_SESSION['appointment']['step4'] = 2; }

    if (isset($_GET['staff_id'])   ) {
            $_SESSION['appointment']['staff_id']   = $_GET['staff_id'];
            require_once('../../../includes/profiles_functions.php');
            $profiles_dal           = new Profiles_DAL();
            $load_staff_info        = $profiles_dal->get_EmployeeDataPerLoginId($_SESSION['appointment']['staff_id']);

            $_SESSION['appointment'][$_SESSION['appointment']['staff_id']]['gmail_username']    = $load_staff_info[0]->gmail_username;
            $_SESSION['appointment'][$_SESSION['appointment']['staff_id']]['gmail_password']    = $load_staff_info[0]->gmail_password;
            $_SESSION['appointment']['staff_firstname']                                         = $load_staff_info[0]->firstname;
            $_SESSION['appointment']['staff_surname']                                           = $load_staff_info[0]->lastname;
            $_SESSION['appointment']['staff_email_address']                                     = $load_staff_info[0]->email_address;
            
    }
    //if (isset($_GET['service_id']) ) { $_SESSION['appointment']['service_id'] = $_GET['service_id']; }
    appointments();
    }

if ( $_GET['step'] == "3") {
    $_SESSION['appointment']['step']       = 3;
    $_SESSION['appointment']['step3']      = 1;
    $_SESSION['appointment']['step1']= $_SESSION['appointment']['step2'] = 2;
    if (isset($_SESSION['appointment']['step4'])) { $_SESSION['appointment']['step4'] = 2; }

    if (isset($_GET['selected_apt_time'])   ) { $_SESSION['appointment']['selected_apt_time']   = $_GET['selected_apt_time'];   }
    appointments();
    }


if ( $_GET['step'] == "4") {
    $_SESSION['appointment']['step']       = 4;
    $_SESSION['appointment']['step4']      = 1;
    $_SESSION['appointment']['step1']= $_SESSION['appointment']['step2'] = $_SESSION['appointment']['step3'] = 2;
    appointments();
    }
?>