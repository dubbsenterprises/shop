<?php
session_start();
require_once('../../../../includes/general_functions.php');
require_once('../../../../includes/reports_functions.php');

if ( isset($_GET['reportType']) ) {
    $_SESSION['search_data']['paging_page'] = 1;
    evaluate_GET_for_SEARCHES($_GET['reportType'],'profiles_search_inactive_profiles');
    evaluate_GET_for_SEARCHES($_GET['reportType'],'profiles_search_first_name');
    evaluate_GET_for_SEARCHES($_GET['reportType'],'profiles_search_last_name');
    evaluate_GET_for_SEARCHES($_GET['reportType'],'profiles_search_email');
    evaluate_GET_for_SEARCHES($_GET['reportType'],'profiles_search_phone_number');
}
?>