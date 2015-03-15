<?php
include('../../../../includes/general_functions.php');
include('../../../../includes/sales_functions.php');
#session_start();
    unset($_SESSION['sale']['customer_id']);
    sales_choose_customer();
?>