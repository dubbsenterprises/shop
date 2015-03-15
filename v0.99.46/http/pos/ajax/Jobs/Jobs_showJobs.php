<?php
require_once('../../../../includes/general_functions.php');
require_once('../../../../includes/jobs_functions.php');

if ( ( isset($_GET['ReportDataRefreshQuery']) && $_GET['ReportDataRefreshQuery'] == 1 ) || $_GET['ReportDataPagingIteration'] == 1) {
    ###  Show the report
      $_SESSION['reportType'] = "Jobs_AllJobs";
      JobsStanza();
}
?>