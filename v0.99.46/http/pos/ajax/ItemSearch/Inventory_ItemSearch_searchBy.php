<?php
session_start();
require_once('../../../../includes/general_functions.php');
require_once('../../../../includes/reports_functions.php');
require_once('../../../../includes/item_search_functions.php');

if ( isset($_GET['reportType']) ) {
    $_SESSION['search_data']['paging_page'] = 1;
    ##  Category
    if (isset($_GET['item_search_category']) ) {
        if ( $_GET['item_search_category'] == -1 or !isset($_GET['item_search_category']) ) { unset($_SESSION['search_data']['item_search']['item_search_category']);}
        else { $_SESSION['search_data']['item_search']['item_search_category'] = $_GET['item_search_category'] ;}
    }
    ##  Brand
    if (isset($_GET['item_search_brand']) ) {
        if ( $_GET['item_search_brand'] == -1 or !isset($_GET['item_search_brand']) ) { unset($_SESSION['search_data']['item_search']['item_search_brand']);}
        else { $_SESSION['search_data']['item_search']['item_search_brand'] = $_GET['item_search_brand'] ;}
    }
    ##  Supplier
    if (isset($_GET['item_search_supplier']) ) {
        if ( $_GET['item_search_supplier'] == -1 or !isset($_GET['item_search_supplier']) ) { unset($_SESSION['search_data']['item_search']['item_search_supplier']);}
        else { $_SESSION['search_data']['item_search']['item_search_supplier'] = $_GET['item_search_supplier'] ;}
    }
    ##  Department
    if (isset($_GET['item_search_department']) ) {
        if ( $_GET['item_search_department'] == -1 or !isset($_GET['item_search_department']) ) { unset($_SESSION['search_data']['item_search']['item_search_department']);}
        else { $_SESSION['search_data']['item_search']['item_search_department'] = $_GET['item_search_department'] ;}
    }
    ##  styleNumber
    if (isset($_GET['item_search_styleNumber']) ) {
        if ( $_GET['item_search_styleNumber'] == -1 or !isset($_GET['item_search_styleNumber']) ) { unset($_SESSION['search_data']['item_search']['item_search_styleNumber']);}
        else { $_SESSION['search_data']['item_search']['item_search_styleNumber'] = $_GET['item_search_styleNumber'] ;}
    }


    ##  Keyword
    if (isset($_GET['item_search_item_keyword']) ) {
        if ( $_GET['item_search_item_keyword'] == -1 )              { unset($_SESSION['search_data']['item_search']['item_search_item_keyword']);}
        else { $_SESSION['search_data']['item_search']['item_search_item_keyword'] = $_GET['item_search_item_keyword'] ; }
    }
    ############################################################################
    ##  Barcode
    if (isset($_GET['item_search_item_barcode']) ) {
        if ( $_GET['item_search_item_barcode'] == -1 )              { unset($_SESSION['search_data']['item_search']['item_search_item_barcode']);}
        else { $_SESSION['search_data']['item_search']['item_search_item_barcode'] = $_GET['item_search_item_barcode'] ; }
    }
    ##  Name
    if (isset($_GET['item_search_item_name']) ) {
        if ( $_GET['item_search_item_name'] == -1 )                 { unset($_SESSION['search_data']['item_search']['item_search_item_name']);}
        else { $_SESSION['search_data']['item_search']['item_search_item_name'] = $_GET['item_search_item_name'] ; }
    }
    ############################################################################
    ##  Exclude Qty Zero
    if (isset($_GET['item_search_exclude_qty_zero']) ) {
        if ( $_GET['item_search_exclude_qty_zero'] == -1 )           { unset($_SESSION['search_data']['item_search']['item_search_exclude_qty_zero']);}
        else { 
            $_SESSION['search_data']['item_search']['item_search_exclude_qty_zero'] = $_GET['item_search_exclude_qty_zero'] ; }
            
            $reportType = 'item_search';
            ob_start();
            dynamic_pannel_advanced_search_Categories('Inventory_ItemSearch_searchBy("item_search")');
            $response_array['html_item_search_item_category']      = ob_get_clean();

            ob_start();            
            dynamic_pannel_advanced_search_styleNumber('Inventory_ItemSearch_searchBy("item_search")');
            $response_array['html_item_search_item_styleNumber']      = ob_get_clean();
            
            ob_start();            
            dynamic_pannel_advanced_search_Brands('Inventory_ItemSearch_searchBy("item_search")');
            $response_array['html_item_search_item_brand']      = ob_get_clean();
            
            ob_start();            
            dynamic_pannel_advanced_search_Suppliers('Inventory_ItemSearch_searchBy("item_search")');
            $response_array['html_item_search_item_supplier']      = ob_get_clean();
            
            ob_start();            
            dynamic_pannel_advanced_search_Departments('Inventory_ItemSearch_searchBy("item_search")');
            $response_array['html_item_search_item_department']      = ob_get_clean();
        }
    ##  Exclude Services
    if (isset($_GET['item_search_exclude_services']) ) {
        if ( $_GET['item_search_exclude_services'] == -1 )           { unset($_SESSION['search_data']['item_search']['item_search_exclude_services']);}
        else { $_SESSION['search_data']['item_search']['item_search_exclude_services'] = $_GET['item_search_exclude_services'] ; }
    }
    ##  Exclude Items
    if (isset($_GET['item_search_exclude_items']) ) {
        if ( $_GET['item_search_exclude_items'] == -1 )               { unset($_SESSION['search_data']['item_search']['item_search_exclude_items']);}
        else { $_SESSION['search_data']['item_search']['item_search_exclude_items'] = $_GET['item_search_exclude_items'] ; }
    }
    
    echo json_encode($response_array);
}
?>
