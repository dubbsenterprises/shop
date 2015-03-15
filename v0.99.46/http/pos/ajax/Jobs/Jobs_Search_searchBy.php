<?php
session_start();
require_once('../../../../includes/general_functions.php');
require_once('../../../../includes/reports_functions.php');

if ( isset($_GET['reportType']) ) {
    $_SESSION['search_data']['paging_page'] = 1;
    evaluate_GET_for_SEARCHES($_GET['reportType'],'jobs_search_inactive_jobs');
    evaluate_GET_for_SEARCHES($_GET['reportType'],'jobs_search_name');
    evaluate_GET_for_SEARCHES($_GET['reportType'],'jobs_search_sub_desc');
    evaluate_GET_for_SEARCHES($_GET['reportType'],'jobs_search_company_name');
    evaluate_GET_for_SEARCHES($_GET['reportType'],'jobs_search_location_state');
    evaluate_GET_for_SEARCHES($_GET['reportType'],'jobs_search_location_city');
}
?>