<?php
include('../../../../includes/general_functions.php');
include('../../../../includes/mailer_functions.php');

if ( $_GET['mailer_RunInfo'] == "1") {
    $_SESSION['mailer_run']['done']                     = 0;
    if ($_GET['mailer_run_login_id']    != 0) {
        $_SESSION['mailer_run']['mailer_run_login_id']      = $_GET['mailer_run_login_id'];
    }
    if ($_GET['mailer_run_template_id'] != 0) {
        $_SESSION['mailer_run']['mailer_run_template_id']   = $_GET['mailer_run_template_id'];
    }
    mailer();
}
?>
