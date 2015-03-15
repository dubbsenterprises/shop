<?php
include('../../../../includes/general_functions.php');
include('../../../../includes/companies_functions.php');

if ( $_GET['edit_company']) {
    unset($_SESSION['edit_companies']['CompanyAdd']);
    unset($_SESSION['edit_companies']['company_id']);
    $_SESSION['edit_companies']['company_id'] = $_GET['company_id'];
    companies();
}
?>