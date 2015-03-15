<?php
include('../../../../includes/general_functions.php');
include('../../../../includes/jobs_functions.php');

if ( $_GET['ActiveTab']) {
    $_SESSION['edit_jobs']['ActiveTab'] = $_GET['ActiveTab'];
    jobs();
}
?>