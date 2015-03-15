<?php
include('../../../../includes/general_functions.php');
include('../../../../includes/mailer_functions.php');

if ( $_GET['createmailer_Run'] == "1") {
    unset($_SESSION['mailer_run']);
    $_SESSION['mailer_run']['created_by_login_id'] = $_SESSION['settings']['login_id'];
    $_SESSION['mailer_run']['done'] = 0;
    mailer();
}
?>