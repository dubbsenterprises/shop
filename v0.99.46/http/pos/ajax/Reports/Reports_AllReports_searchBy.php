<?php
session_start();
require_once('../../../../includes/general_functions.php');
require_once('../../../../includes/reports_functions.php');

if ( isset($_GET['reportType']) ) {
    $_SESSION['search_data']['paging_page'] = 1;
    evaluate_GET_for_SEARCHES($_GET['reportType'],'start_date');
    evaluate_GET_for_SEARCHES($_GET['reportType'],'end_date');
    evaluate_GET_for_SEARCHES($_GET['reportType'],'staff_id');
}
?>