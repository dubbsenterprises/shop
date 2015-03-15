<?php
session_start();
include('../../'.$_SESSION['settings']['templateType'].'/'.$_SESSION['settings']['templateType'].'_functions.php');
?>

<?php
$_SESSION['appointment']['selected_date']   = $_GET['selected_date'];
    make_appointment_step2_choose_apt();
?>