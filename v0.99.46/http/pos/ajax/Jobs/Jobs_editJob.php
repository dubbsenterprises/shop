<?php
include('../../../../includes/general_functions.php');
include('../../../../includes/jobs_functions.php');

if ( $_GET['edit_Jobs']) {
    unset($_SESSION['edit_jobs']['JobAdd']);
    $_SESSION['edit_jobs']['job_id'] = $_GET['job_id'];
    jobs();
}
?>