<?php
session_start();
include('../../'.$_SESSION['settings']['templateType'].'/'.$_SESSION['settings']['templateType'].'_functions.php');
?>

<?php
$_SESSION['appointment']['cal_year']   = $_GET['year'];
$_SESSION['appointment']['cal_month']  = $_GET['month'];
    show_calendar($_SESSION['appointment']['cal_year'], $_SESSION['appointment']['cal_month']) ;
?>