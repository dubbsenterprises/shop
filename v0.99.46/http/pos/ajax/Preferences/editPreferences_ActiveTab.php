<?php
include('../../../../includes/general_functions.php');
include('../../../../includes/preferences_functions.php');

if ( $_GET['ActiveTab']) {
    $_SESSION['preferences']['ActiveTab'] = $_GET['ActiveTab'];
    preferences();
}
?>