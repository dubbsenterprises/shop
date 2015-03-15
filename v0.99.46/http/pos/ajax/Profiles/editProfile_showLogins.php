<?php
#include    ('../../../includes/functions.php');
require_once('../../../../includes/general_functions.php');
require_once('../../../../includes/profiles_functions.php');

if (  ( isset($_GET['ReportDataRefreshQuery']) && $_GET['ReportDataRefreshQuery'] == 1 || $_GET['ReportDataPagingIteration'] == 1) )  {
    ###  Show the report
      $_SESSION['reportType'] = "Profiles_AllProfiles";
      profilesStanza();
}
?>
