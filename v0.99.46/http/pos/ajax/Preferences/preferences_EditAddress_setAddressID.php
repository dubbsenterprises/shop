<?php
include('../../../../includes/general_functions.php');
include('../../../../includes/preferences_functions.php');

if ( $_GET['edit_address_address_id']) {
    $_SESSION['edit_preferences']['edit_address_address_id'] = $_GET['edit_address_address_id'];
    preferences();
}
?>