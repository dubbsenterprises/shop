<?php
include('../../../../includes/general_functions.php');
include('../../../../includes/companies_functions.php');
#session_start();

if ( $_GET['CompanyAdd']) {
    $_SESSION['edit_companies']['CompanyAdd']= 1;
    #unset($_SESSION['edit_companies']['customer_id']);
    companies();
}
?>