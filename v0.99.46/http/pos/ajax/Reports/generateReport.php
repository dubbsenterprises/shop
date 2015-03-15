<?php
require_once('../../../../includes/general_functions.php');
require_once('../../../../includes/reports_functions.php');
$general_dal                = new GENERAL_DAL();
$PreferenceData = $general_dal->get_CompanyPreference_by_Company_ID($_SESSION['settings']['company_id'],'timezone');
date_default_timezone_set($PreferenceData[0]->value);

$reportType = $_GET['reportType'];

if ( $reportType == "SalesReport") {
    unset($_SESSION['search_data']);
    $_SESSION['reportType'] = $reportType;
    $_SESSION['dynamic_pannel_advanced_search'] = 1;
    $_SESSION['search_data'][$reportType]['start_date'] = date("Y/m",time()) . "/01";
    $_SESSION['search_data'][$reportType]['end_date']   = date("Y/m/d",time());
    SalesReports();
} elseif ( $reportType == "SalesPerHourReport") {
    unset($_SESSION['search_data']);
    $_SESSION['reportType'] = $reportType;
    $_SESSION['dynamic_pannel_advanced_search'] = 1;
    $_SESSION['search_data'][$reportType]['start_date'] = date("Y/m",time()) . "/01";
    $_SESSION['search_data'][$reportType]['end_date']   = date("Y/m/d",time());
    SalesReports();
} elseif ( $reportType == "SalesPerMonthReport") {
    unset($_SESSION['search_data']);
    $_SESSION['reportType'] = $reportType;
    $_SESSION['dynamic_pannel_advanced_search'] = 1;
    $_SESSION['search_data'][$reportType]['start_date'] = date("Y/m",time()) . "/01";
    $_SESSION['search_data'][$reportType]['end_date']   = date("Y/m/d",time());
    SalesReports();
} elseif ( $reportType == "DailyInventoryReport") {
    unset($_SESSION['search_data']);
    $_SESSION['reportType'] = $reportType;
    $_SESSION['dynamic_pannel_advanced_search'] = 1;
    SalesReports();
}


elseif ( $reportType == "AppointmentsPerHourReport") {
    unset($_SESSION['search_data']);
    $_SESSION['reportType'] = $reportType;
    $_SESSION['dynamic_pannel_advanced_search'] = 1;
    $_SESSION['search_data'][$reportType]['start_date'] = date("Y/m",time()) . "/01";
    $_SESSION['search_data'][$reportType]['end_date']   = date("Y/m/d",time());
    SalesReports();
} elseif ( $reportType == "AppointmentsPerMonthReport") {
    unset($_SESSION['search_data']);
    $_SESSION['reportType'] = $reportType;
    $_SESSION['dynamic_pannel_advanced_search'] = 1;
    $_SESSION['search_data'][$reportType]['start_date'] = date("Y/m",time()) . "/01";
    $_SESSION['search_data'][$reportType]['end_date']   = date("Y/m/d",time());
    SalesReports();
}



elseif ( $reportType == "LinesReport") {
    unset($_SESSION['search_data']);
    $_SESSION['reportType'] = $reportType;
    $_SESSION['dynamic_pannel_advanced_search'] = 0;
    SalesReports();
}
elseif ( $reportType == "MarginLineReport") {
    unset($_SESSION['search_data']);
    $_SESSION['reportType'] = $reportType;
    $_SESSION['dynamic_pannel_advanced_search'] = 0;
    SalesReports();
}
elseif ( $reportType == "UnitsPerSaleReport") {
    unset($_SESSION['search_data']);
    $_SESSION['reportType'] = $reportType;
    $_SESSION['dynamic_pannel_advanced_search'] = 0;
    SalesReports();
}
elseif ( $reportType == "VoidsReport") {
    unset($_SESSION['search_data']);
    $_SESSION['reportType'] = $reportType;
    $_SESSION['dynamic_pannel_advanced_search'] = 0;
    SalesReports();
}


####  ITEM REPORT types
elseif ( $reportType == "ItemsReport_BestSellers") {
    unset($_SESSION['search_data']);
    $_SESSION['reportType'] = $reportType;
    $_SESSION['dynamic_pannel_advanced_search'] = 0;
    ItemReports();
}
elseif ( $reportType == "ItemsReport_Category") {
    unset($_SESSION['search_data']);
    $_SESSION['reportType'] = $reportType;
    $_SESSION['dynamic_pannel_advanced_search'] = 1;
    ItemReports();
}
elseif ( $reportType == "ItemsReport_Department") {
    unset($_SESSION['search_data']);
    $_SESSION['reportType'] = $reportType;
    $_SESSION['dynamic_pannel_advanced_search'] = 1;
    ItemReports();
}
elseif ( $reportType == "ItemsReport_Vendor") {
    unset($_SESSION['search_data']);
    $_SESSION['reportType'] = $reportType;
    $_SESSION['dynamic_pannel_advanced_search'] = 1;
    ItemReports();
}




elseif ( $reportType == "ItemsReport_SoldOut") {
    unset($_SESSION['search_data']);
    $_SESSION['reportType'] = $reportType;
    $_SESSION['dynamic_pannel_advanced_search'] = 1;
    ItemReports();
}
elseif ( $reportType == "ItemsReport_AllItems") {
    unset($_SESSION['search_data']);
    $_SESSION['reportType'] = $reportType;
    $_SESSION['dynamic_pannel_advanced_search'] = 1;
    ItemReports();
}
else {
    echo "this type of report <font color=red> \"". $reportType . "\"</font> is not yet configured. Reports/generateReport.php";
}
?>
