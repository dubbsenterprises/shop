<?php
include('../../../../includes/general_functions.php');
include('../../../../includes/profiles_functions.php');

if ( $_GET['ActiveTab']) {
    $_SESSION['edit_profiles']['ActiveTab'] = $_GET['ActiveTab'];
    profiles();
}
?>