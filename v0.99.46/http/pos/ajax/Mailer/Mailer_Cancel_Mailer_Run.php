<?php
include('../../../../includes/general_functions.php');
include('../../../../includes/mailer_functions.php');
 
if ( $_GET['cancelMailer_Run'] == "1") {
    unset($_SESSION['mailer_run']);
    mailer();
}
?>
