<?php
require_once('../../../../includes/general_functions.php');
require_once('../../../../includes/mailer_functions.php');

if ( isset($_GET['ReportDataRefreshQuery']) && $_GET['ReportDataRefreshQuery'] == 1) {
    unset($_SESSION['search_data']);
    $_SESSION['search_data']['paging_page'] = 1;
}

if ($_GET['ReportDataPagingIteration'] == 1 && isset($_SESSION['reportType'])) {
        $function_name = $_SESSION['reportType'] . "Stanza";
        $function_name($_SESSION['reportType']);
}
?>
