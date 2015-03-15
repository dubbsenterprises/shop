<?php
session_start();
require_once('../../../../includes/general_functions.php');
require_once('../../../../includes/reports_functions.php');

if ( isset($_GET['reportType']) ) {
    $_SESSION['search_data']['paging_page'] = 1;
    evaluate_GET_for_SEARCHES($_GET['reportType'],'company_search_name');
    evaluate_GET_for_SEARCHES($_GET['reportType'],'company_search_domain');
    evaluate_GET_for_SEARCHES($_GET['reportType'],'company_search_subdomain');
    evaluate_GET_for_SEARCHES($_GET['reportType'],'company_search_master_email');
}
?>