<?php
require_once('../../../../includes/general_functions.php');

if ( isset($_GET['ReportDataRefreshQuery']) && $_GET['ReportDataRefreshQuery'] == 1) {
    unset($_SESSION['search_data']);
    $_SESSION['search_data']['paging_page'] = 1;
}

if ( $_SESSION['reportType'] == "Inventory_Categories" ) {
    require_once('../../../../includes/inventory_management_functions.php');
    categoriesStanza();
}
elseif ( $_SESSION['reportType'] == "Deliveries_AllDeliveries" ) {
    require_once('../../../../includes/inventory_management_functions.php');
    deliveriesStanza();
}
elseif ( $_SESSION['reportType'] == "item_search" ) {
    require_once('../../../../includes/item_search_functions.php');
    ItemSearchResultsStanza();
}
elseif ( $_SESSION['reportType'] == "Inventory_AllInventoryRuns") {
    require_once('../../../../includes/inventory_management_functions.php');
    Inventory_InventoryRunStanza();
}

?>
