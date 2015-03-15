<?php
include('../../../../includes/general_functions.php');
include('../../../../includes/profiles_functions.php');
#session_start();

if ( $_GET['UserAdd']) {
    $_SESSION['edit_profiles']['UserAdd']= 1;
    unset($_SESSION['edit_profiles']['login_id']);
    profiles();
}
?>