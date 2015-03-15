<?php
require_once('../../../../includes/general_functions.php');
require_once('../../../../includes/companies_functions.php');

if ( isset($_GET['ReportDataRefreshQuery']) && $_GET['ReportDataRefreshQuery'] == 1) {
    unset($_SESSION['search_data']);
    $_SESSION['search_data']['paging_page'] = 1;
}

if ( $_SESSION['reportType'] == "Companies_AllCompanies" ) {
companiesStanza();
}
?>
