<?php
include('../../../../includes/general_functions.php');
include('../../../../includes/customers_functions.php');

if ( $_GET['ActiveTab']) {
    $_SESSION['edit_customers']['ActiveTab'] = $_GET['ActiveTab'];
    customers();
}
?>