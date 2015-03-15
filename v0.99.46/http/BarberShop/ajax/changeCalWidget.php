<?php
session_start();
include('../../'.$_SESSION['settings']['templateType'].'/'.$_SESSION['settings']['templateType'].'_functions.php');

$new_year   = $_GET['year'];
    make_appointment_step2_choose_month($new_year) ;
?>