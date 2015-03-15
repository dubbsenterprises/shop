<?php
include('../../../../includes/general_functions.php');
include('../../../../includes/profiles_functions.php');

if ( $_GET['edit_address_address_id']) {
    $_SESSION['edit_profiles']['edit_address_address_id'] = $_GET['edit_address_address_id'];
    profiles();
}
?>