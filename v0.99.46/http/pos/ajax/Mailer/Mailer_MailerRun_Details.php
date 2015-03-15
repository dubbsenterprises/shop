<?php
include('../../../../includes/general_functions.php');
include('../../../../includes/mailer_functions.php');

if ( $_GET['mailer_run_id']) {
    $_SESSION['mailer_run']['mailer_run_id'] = $_GET['mailer_run_id'];
    mailer();
}
?>
