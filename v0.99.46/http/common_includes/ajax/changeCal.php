<?php
session_start();
include_once('../../../includes/general_functions.php');
include_once('../../../includes/inventory_management_functions.php');
include_once('../../../includes/appointment_functions.php');
include_once('../../../includes/customers_functions.php');
include_once('../../'.$_SESSION['settings']['templateType'].'/'.$_SESSION['settings']['templateType'].'_functions.php');

$_SESSION['appointment']['cal_year']   = $_GET['year'];
$_SESSION['appointment']['cal_month']  = $_GET['month'];
    show_calendar($_SESSION['appointment']['cal_year'], $_SESSION['appointment']['cal_month']) ;
?>