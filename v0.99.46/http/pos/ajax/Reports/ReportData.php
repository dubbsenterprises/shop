<?php
#include    ('../../../includes/functions.php');
require_once('../../../../includes/general_functions.php');
require_once('../../../../includes/reports_functions.php');
require_once('../../../../includes/inventory_management_functions.php');

if ( $_GET['ReportDataRefreshQuery'] == 1) {
    unset($_SESSION['search_data']);
    $_SESSION['search_data']['paging_page'] = 1;
    $_SESSION['search_data']['dynamic_pannel_start_date']       = $_GET['dynamic_pannel_start_date'];
    $_SESSION['search_data']['dynamic_pannel_end_date']         = $_GET['dynamic_pannel_end_date'];    
    #    SALES FIELDS
    if ( $_SESSION['reportType'] == "SalesReport" ) {
    $_SESSION['search_data']['dynamic_pannel_id_search']        = $_GET['dynamic_pannel_id_search'];
    $_SESSION['search_data']['dynamic_pannel_barcode_search']   = $_GET['dynamic_pannel_barcode_search'];
    $_SESSION['search_data']['dynamic_pannel_style_number_search'] = $_GET['dynamic_pannel_style_number_search'];

    $_SESSION['search_data']['dynamic_pannel_customer_search']  = $_GET['dynamic_pannel_customer_search'];
    $_SESSION['search_data']['dynamic_pannel_employee_username']= $_GET['dynamic_pannel_employee_username'];
    $_SESSION['search_data']['dynamic_pannel_register_id']      = $_GET['dynamic_pannel_register_id'];
    $_SESSION['search_data']['dynamic_pannel_taxcat_name']      = $_GET['dynamic_pannel_taxcat_name'];
    $_SESSION['search_data']['dynamic_pannel_customer_type_id'] = $_GET['dynamic_pannel_customer_type_id'];
    }
    #    ITEMS FIELDS
    if ( $_SESSION['reportType'] == "ItemsReport_BestSellers" ) {
    $_SESSION['search_data']['dynamic_pannel_id_search']        = $_GET['dynamic_pannel_id_search'];
    $_SESSION['search_data']['dynamic_pannel_barcode_search']   = $_GET['dynamic_pannel_barcode_search'];
    $_SESSION['search_data']['dynamic_pannel_style_number_search'] = $_GET['dynamic_pannel_style_number_search'];
    }
    #    Category FIELDS
    if ( $_SESSION['reportType'] == "ItemsReport_Category" ) {
    $_SESSION['search_data']['dynamic_pannel_id_search']        = $_GET['dynamic_pannel_id_search'];
    $_SESSION['search_data']['dynamic_pannel_barcode_search']   = $_GET['dynamic_pannel_barcode_search'];
    $_SESSION['search_data']['dynamic_pannel_style_number_search'] = $_GET['dynamic_pannel_style_number_search'];
    $_SESSION['search_data']['dynamic_pannel_supplier']         = $_GET['dynamic_pannel_supplier'];
    $_SESSION['search_data']['dynamic_pannel_brand']            = $_GET['dynamic_pannel_brand'];
    $_SESSION['search_data']['dynamic_pannel_department']       = $_GET['dynamic_pannel_department'];
    }
    #    Department FIELDS
    if ( $_SESSION['reportType'] == "ItemsReport_Department" ) {
    $_SESSION['search_data']['dynamic_pannel_id_search']        = $_GET['dynamic_pannel_id_search'];
    $_SESSION['search_data']['dynamic_pannel_barcode_search']   = $_GET['dynamic_pannel_barcode_search'];
    $_SESSION['search_data']['dynamic_pannel_style_number_search'] = $_GET['dynamic_pannel_style_number_search'];
    $_SESSION['search_data']['dynamic_pannel_supplier']         = $_GET['dynamic_pannel_supplier'];
    $_SESSION['search_data']['dynamic_pannel_brand']            = $_GET['dynamic_pannel_brand'];
    $_SESSION['search_data']['dynamic_pannel_category']         = $_GET['dynamic_pannel_category'];
    }
    #    Vendor FIELDS
    if ( $_SESSION['reportType'] == "ItemsReport_Vendor" ) {
    $_SESSION['search_data']['dynamic_pannel_id_search']        = $_GET['dynamic_pannel_id_search'];
    $_SESSION['search_data']['dynamic_pannel_supplier']         = $_GET['dynamic_pannel_supplier'];
    $_SESSION['search_data']['dynamic_pannel_brand']            = $_GET['dynamic_pannel_brand'];
    $_SESSION['search_data']['dynamic_pannel_category']         = $_GET['dynamic_pannel_category'];
    }
    if ( $_SESSION['reportType'] == "ItemsReport_SoldOut" ) {
    $_SESSION['search_data']['dynamic_pannel_id_search']        = $_GET['dynamic_pannel_id_search'];
    $_SESSION['search_data']['dynamic_pannel_barcode_search']   = $_GET['dynamic_pannel_barcode_search'];
    $_SESSION['search_data']['dynamic_pannel_style_number_search'] = $_GET['dynamic_pannel_style_number_search'];
    $_SESSION['search_data']['dynamic_pannel_supplier']         = $_GET['dynamic_pannel_supplier'];
    $_SESSION['search_data']['dynamic_pannel_brand']            = $_GET['dynamic_pannel_brand'];
    $_SESSION['search_data']['dynamic_pannel_category']         = $_GET['dynamic_pannel_category'];
    $_SESSION['search_data']['dynamic_pannel_department']       = $_GET['dynamic_pannel_department'];
    }
    if ( $_SESSION['reportType'] == "ItemsReport_AllItems" ) {
    $_SESSION['search_data']['dynamic_pannel_id_search']        = $_GET['dynamic_pannel_id_search'];
    $_SESSION['search_data']['dynamic_pannel_barcode_search']   = $_GET['dynamic_pannel_barcode_search'];
    $_SESSION['search_data']['dynamic_pannel_style_number_search'] = $_GET['dynamic_pannel_style_number_search'];
    $_SESSION['search_data']['dynamic_pannel_supplier']         = $_GET['dynamic_pannel_supplier'];
    $_SESSION['search_data']['dynamic_pannel_brand']            = $_GET['dynamic_pannel_brand'];
    $_SESSION['search_data']['dynamic_pannel_category']         = $_GET['dynamic_pannel_category'];
    $_SESSION['search_data']['dynamic_pannel_department']       = $_GET['dynamic_pannel_department'];
    }
    if ( $_SESSION['reportType'] == "Profiles_AllProfiles" ) {
    $_SESSION['search_data']['dynamic_pannel_id_search']        = $_GET['dynamic_pannel_id_search'];
    }
    ###  Show the report
    ReportRows($_SESSION['reportType']);
}
elseif ($_GET['ReportDataPagingIteration'] == 1) {
    ReportRows($_SESSION['reportType']);
}
?>
