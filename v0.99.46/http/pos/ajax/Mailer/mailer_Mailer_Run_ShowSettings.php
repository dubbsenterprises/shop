<?php
include('../../../../includes/general_functions.php');
include('../../../../includes/mailer_functions.php');

if ( $_GET['mailer_Mailer_Run_ShowSettings'] == 1 ) {
    mailer_Mailer_Run_ShowSettings($_GET['company_id']);
}
?>