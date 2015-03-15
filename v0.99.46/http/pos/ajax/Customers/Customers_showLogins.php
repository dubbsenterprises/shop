<?php
require_once('../../../../includes/general_functions.php');
require_once('../../../../includes/customers_functions.php');

if ( ( isset($_GET['ReportDataRefreshQuery']) && $_GET['ReportDataRefreshQuery'] == 1 ) || $_GET['ReportDataPagingIteration'] == 1) {
    ###  Show the report
      $_SESSION['reportType'] = "Customers_AllCustomers";
      CustomersStanza();
}
?>
