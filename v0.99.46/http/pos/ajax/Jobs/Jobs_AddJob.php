<?php
include('../../../../includes/general_functions.php');
include('../../../../includes/jobs_functions.php');
#session_start();

if ( $_GET['JobAdd']) {
    $_SESSION['edit_jobs']['JobAdd']= 1;
    unset($_SESSION['edit_jobs']['job_id']);
    jobs();
}
?>