<?php
include('../../../../includes/general_functions.php');
include('../../../../includes/mailer_functions.php');
 
if ( $_GET['Mailer_Show_Completed_Mailer_Run'] == "0" || $_GET['Mailer_Show_Completed_Mailer_Run'] == "1" ) {
    $_SESSION['mailer_run']['Mailer_Show_Completed_Mailer_Run'] = $_GET['Mailer_Show_Completed_Mailer_Run'];
    mailer();
}
?>
