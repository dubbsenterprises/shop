<?php
include('../../../../includes/general_functions.php');
include('../../../../includes/profiles_functions.php');
if ( $_GET['editProfile']) {
    unset($_SESSION['edit_profiles']['UserAdd']);
    unset($_SESSION['edit_profiles']['edit_address_address_id']);
    $_SESSION['edit_profiles']['login_id'] = $_GET['profiles_login_id'];
    profiles();
}
?>