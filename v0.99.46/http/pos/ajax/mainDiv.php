<?php
require_once('../../../includes/general_functions.php');

$general_dal                = new GENERAL_DAL();
$posurl = 'http://' . $_SERVER['HTTP_HOST'] . "/pos";
if (!(isset($_SESSION['settings']['company_id']))) { 
    header("Location: $posurl"); 
}
$PreferenceData = $general_dal->get_CompanyPreference_by_Company_ID($_SESSION['settings']['company_id'],'timezone');
date_default_timezone_set($PreferenceData[0]->value);

if ( $_GET['page'] == "mainPage") {
    require_once('../../../includes/mainPage_functions.php');
    $_SESSION['page'] = $_GET['page'];
    mainPage();
}

if ( $_GET['page'] == "reports") {
    require_once('../../../includes/reports_functions.php');
    if (isset($_SESSION['search_data'])) { unset($_SESSION['search_data']);}
    $_SESSION['page'] = $_GET['page'];
    $_SESSION['settings']['site'] = "reports";
    reports();
}

elseif ( $_GET['page'] == "item_search") {
    require_once('../../../includes/item_search_functions.php');
    if (isset($_SESSION['search_data'])) { unset($_SESSION['search_data']);}
    $_SESSION['page'] = $_GET['page'];
    $_SESSION['reportType'] = $_GET['page'];
    $_SESSION['search_data']['item_search']['item_search_exclude_qty_zero'] = 1;
    $_SESSION['search_data']['item_search']['item_search_exclude_services'] = 0;
    $_SESSION['search_data']['item_search']['item_search_exclude_items'] = 0;
    item_search();
}
elseif ( $_GET['page'] == "ItemManagement") {
    require_once('../../../includes/inventory_management_functions.php');
    if (isset($_SESSION['search_data'])) { unset($_SESSION['search_data']);}
    $_SESSION['page'] = $_GET['page'];
    $_SESSION['settings']['site'] = "itemmgnt";
    ItemManagement();
}
elseif ( $_GET['page'] == "Deliveries_AllDeliveries") {
    require_once('../../../includes/inventory_management_functions.php');
    if (isset($_SESSION['search_data'])) { unset($_SESSION['search_data']);}
    $_SESSION['reportType'] = $_GET['page'];
    $_SESSION['search_data']['paging_page'] = 1;
    $_SESSION['page'] = $_GET['page'];
    deliveries();
}
elseif ( $_GET['page'] == "Inventory_AllInventoryRuns") {
    require_once('../../../includes/inventory_management_functions.php');
    if (isset($_SESSION['search_data']))    { unset($_SESSION['search_data']);}
    $_SESSION['reportType'] = $_GET['page'];
    $_SESSION['search_data']['paging_page'] = 1;
    $_SESSION['page'] = $_GET['page'];
    inventory();
}
elseif ( $_GET['page'] == "Mailer_AllMailerRuns") {
    require_once('../../../includes/mailer_functions.php');
    if (isset($_SESSION['search_data']))    { unset($_SESSION['search_data']);}
    if (isset($_SESSION['mailer_run']))     { unset($_SESSION['mailer_run']);}
    $_SESSION['reportType'] = $_GET['page'];
    $_SESSION['search_data']['paging_page'] = 1;
    $_SESSION['page'] = $_GET['page'];
    mailer();
}
elseif ( $_GET['page'] == "Inventory_Categories") {
    require_once('../../../includes/inventory_management_functions.php');
    unset($_SESSION['item_management_categories']['category_id']);
    if (isset($_SESSION['search_data'])) { unset($_SESSION['search_data']);}
    $_SESSION['page'] = $_GET['page'];
    $_SESSION['reportType'] = $_GET['page'];
    $_SESSION['dynamic_pannel_advanced_search'] = 0;
    Categories();
}
elseif ( $_GET['page'] == "ItemManagement_CreateNewItem") {
    require_once('../../../includes/inventory_management_functions.php');
    if (isset($_SESSION['search_data'])) { unset($_SESSION['search_data']);}
    $_SESSION['page'] = $_GET['page'];
    $_SESSION['edit_item']['Edit_or_Add'] = "Add";
    $_SESSION['settings']['site'] = "itemmgnt";
    ItemManagement_Add_Edit_Item();
}
elseif ( $_GET['page'] == "ItemManagement_EditItem") {
    require_once('../../../includes/inventory_management_functions.php');
    if (isset($_SESSION['search_data'])) { unset($_SESSION['search_data']);}
    $_SESSION['page'] = $_GET['page'];
    $_SESSION['edit_item']['Edit_or_Add'] = "Edit";
    $_SESSION['settings']['site'] = "itemmgnt";
    ItemManagement_Add_Edit_Item();
}
elseif ( $_GET['page'] == "ItemManagement_CreateNewService") {
    require_once('../../../includes/inventory_management_functions.php');
    if (isset($_SESSION['search_data'])) { unset($_SESSION['search_data']);}
    $_SESSION['page'] = $_GET['page'];
    $_SESSION['edit_service']['Edit_or_Add'] = "Add";
    $_SESSION['settings']['site'] = "itemmgnt";
    ItemManagement_Add_Edit_Service();
}
elseif ( $_GET['page'] == "ItemManagement_EditService") {
    require_once('../../../includes/inventory_management_functions.php');
    if (isset($_SESSION['search_data'])) { unset($_SESSION['search_data']);}
    $_SESSION['page'] = $_GET['page'];
    $_SESSION['edit_service']['Edit_or_Add'] = "Edit";
    $_SESSION['settings']['site'] = "itemmgnt";
    ItemManagement_Add_Edit_Service();
}
elseif ( $_GET['page'] == "Profiles") {
    require_once('../../../includes/profiles_functions.php');
    unset($_SESSION['edit_profiles']['login_id']);
    unset($_SESSION['edit_profiles']['UserAdd']);
    if (isset($_SESSION['search_data'])) { unset($_SESSION['search_data']);}
    $_SESSION['reportType'] = "Profiles_AllProfiles";
    $_SESSION['search_data']['paging_page'] = 1;
    $_SESSION['page'] = $_GET['page'];
    profiles();
}
elseif ( $_GET['page'] == "profiles_clock_in_out") {
    require_once('../../../includes/profiles_functions.php');
    if (isset($_SESSION['search_data'])) { unset($_SESSION['search_data']);}
    $_SESSION['page'] = $_GET['page'];
    profiles_clock_in_out();
}
elseif ( $_GET['page'] == "Customers") {
    require_once('../../../includes/customers_functions.php');
    unset($_SESSION['edit_customers']['customer_id']);
    unset($_SESSION['edit_customers']['CustomerAdd']);
    if (isset($_SESSION['search_data'])) { unset($_SESSION['search_data']);}
    $_SESSION['reportType'] = "Customers_AllCustomers";
    $_SESSION['search_data']['paging_page'] = 1;
    $_SESSION['page'] = $_GET['page'];
    customers();
}
elseif ( $_GET['page'] == "Preferences_Company") {
    require_once('../../../includes/preferences_functions.php');
    $_SESSION['page'] = $_GET['page'];
    preferences();
}
elseif ( $_GET['page'] == "companies") {
    require_once('../../../includes/companies_functions.php');
    unset($_SESSION['edit_companies']);
    if (isset($_SESSION['search_data'])) { unset($_SESSION['search_data']);}
    $_SESSION['reportType'] = "Companies_AllCompanies";
    $_SESSION['page'] = $_GET['page'];
    companies();
}
elseif ( $_GET['page'] == "calendar_appointments") {
    require_once('../../../includes/calendar_functions.php');
    if (isset($_SESSION['search_data'])) { unset($_SESSION['search_data']);}
    //$_SESSION['reportType'] = "Companies_AllCompanies";
    $_SESSION['page'] = $_GET['page'];
    calendar();
}
elseif ( $_GET['page'] == "jobs") {
    require_once('../../../includes/jobs_functions.php');
    if (isset($_SESSION['search_data'])) { unset($_SESSION['search_data']);}
    $_SESSION['reportType'] = "Jobs_AllJobs";
    unset($_SESSION['edit_jobs']['JobAdd']);
    unset($_SESSION['edit_jobs']['job_id']);
    $_SESSION['page'] = $_GET['page'];
    jobs();
}
elseif ( $_GET['page'] == "new_sale") {
    require_once('../../../includes/sales_functions.php');
    if (isset($_SESSION['search_data'])) { unset($_SESSION['search_data']);}
    $_SESSION['page'] = $_GET['page'];
    $_SESSION['settings']['site'] = 'sales';
    sales();
}
elseif ( $_GET['page'] == "returns") {
    require_once('../../../includes/sales_functions.php');
    if (isset($_SESSION['search_data'])) { unset($_SESSION['search_data']);}
    $_SESSION['page'] = $_GET['page'];
    returns();
}
elseif ( $_GET['page'] == "exchanges") {
    require_once('../../../includes/sales_functions.php');
    if (isset($_SESSION['search_data'])) { unset($_SESSION['search_data']);}
    $_SESSION['page'] = $_GET['page'];
    exchanges();
}

else {
    print "The Page: <font color=red>\"{$_GET['page']}\"</font> hasn't been configured in ajax/mainDiv.php.<br>";
}
?>