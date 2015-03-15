<?php
session_start();
include('../../BarberShop/BarberShop_functions.php');
?>

<?php
$_SESSION['appointment']['selected_date']   = $_GET['selected_date'];
    make_appointment_step2_choose_apt();
?>