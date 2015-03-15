<?php
session_start();
require_once('../../../includes/general_functions.php');
require_once('../../../includes/inventory_management_functions.php');
require_once('../../../includes/profiles_functions.php');
require_once('../../../includes/item_search_functions.php');
$count = 0;
unset($_SESSION['search_data']);

$ipAddress = $_SERVER['REMOTE_ADDR']; 
if (isset($_GET['EmployeeRemoteKey'])) {
        $company_id =   set_Company_id_PerEmployeeRemoteKey_and_RemoteIP($ipAddress,$_GET['EmployeeRemoteKey']);}
else { 
                        set_Company_id_PerEmployeeRemoteKey_and_RemoteIP($ipAddress); } // its gonna fail and exit since no Key is passed.

if ( isset($_GET['action'])&& ($_GET['action'] == 'all_pages') ){ paging_first_next_last('all_pages',0); }  else { $_SESSION['search_data']['paging_page'] = 0; }
if ( isset($_GET['divisior']) )                                 { $divisor = $_GET['divisior']; }           else { $divisor = 12;}

#####  ITEM SEARCH  ITEM SEARCH   ITEM SEARCH #####
if ( ( isset($_GET['page'])&& $_GET['page'] == "item_search") && ($company_id != -1) ) {
    $_SESSION['search_data']['item_search']['item_search_exclude_qty_zero'] = 1;  #  Also can be supplied via URL. See below
    $_SESSION['search_data']['item_search']['item_search_exclude_services'] = 0;
    $_SESSION['search_data']['item_search']['item_search_exclude_items'] = 0;
    ##  Keyword
    if (isset($_GET['item_search_item_keyword']) ) {
        if ( $_GET['item_search_item_keyword']  == -1 )              { unset($_SESSION['search_data']['item_search']['item_search_item_keyword']);}
        else { $_SESSION['search_data']['item_search']['item_search_item_keyword'] = $_GET['item_search_item_keyword'] ; }
    } else { if ( isset($_SESSION['search_data']['item_search']['item_search_item_keyword']) ) { unset($_SESSION['search_data']['item_search']['item_search_item_keyword']); } }
    ##  Category
    if (isset($_GET['item_search_category']) ) {
        if ( $_GET['item_search_category']      == -1 )              { unset($_SESSION['search_data']['item_search']['item_search_category']);}
        else { $_SESSION['search_data']['item_search']['item_search_category'] = $_GET['item_search_category'] ; }
    } else { if ( isset($_SESSION['search_data']['item_search']['item_search_category']) ) { unset($_SESSION['search_data']['item_search']['item_search_category']); } }
    ##  Brand
    if (isset($_GET['item_search_brand']) ) {
        if ( $_GET['item_search_brand']         == -1 )              { unset($_SESSION['search_data']['item_search']['item_search_brand']);}
        else { $_SESSION['search_data']['item_search']['item_search_brand'] = $_GET['item_search_brand'] ; }
    } else { if ( isset($_SESSION['search_data']['item_search']['item_search_brand']) ) { unset($_SESSION['search_data']['item_search']['item_search_brand']); } }
    ##  Department
    if (isset($_GET['item_search_department']) ) {
        if ( $_GET['item_search_department']    == -1 )              { unset($_SESSION['search_data']['item_search']['item_search_department']);}
        else { $_SESSION['search_data']['item_search']['item_search_department'] = $_GET['item_search_department'] ; }
    } else { if ( isset($_SESSION['search_data']['item_search']['item_search_department']) ) { unset($_SESSION['search_data']['item_search']['item_search_department']); } }
    ##  StyleNumber 
    if (isset($_GET['item_search_styleNumber']) ) {
        if ( $_GET['item_search_styleNumber']   == -1 )              { unset($_SESSION['search_data']['item_search']['item_search_styleNumber']);}
        else { $_SESSION['search_data']['item_search']['item_search_styleNumber'] = $_GET['item_search_styleNumber'] ; }
    } else { if ( isset($_SESSION['search_data']['item_search']['item_search_styleNumber']) ) { unset($_SESSION['search_data']['item_search']['item_search_styleNumber']); } }
    ############################################################################
    ##  Exclude Qty Zero
    if (isset($_GET['item_search_exclude_qty_zero']) ) {
        if ( $_GET['item_search_exclude_qty_zero'] == -1 )           { unset($_SESSION['search_data']['item_search']['item_search_exclude_qty_zero']);}
        else { $_SESSION['search_data']['item_search']['item_search_exclude_qty_zero'] = $_GET['item_search_exclude_qty_zero'] ; }
    }
    
    $items_dal                          = new ITEM_SEARCH_DAL();
    $itemsANDservicesCount              = $items_dal->get_AllItemsANDServices_by_CompanyId($company_id,1,$divisor);
    $_SESSION['search_data']['pages']   = ceil($itemsANDservicesCount[0]->count / $divisor) ;

    $itemsANDservices                   = $items_dal->get_AllItemsANDServices_by_CompanyId($company_id,0,$divisor);


    $item_data_array['results_count']   = ($itemsANDservicesCount[0]->count);
    if (count($itemsANDservices) > 0 ) {
        foreach($itemsANDservices as $itemORserviceData){
            if ($count == 50){ break; }
            $item_data_array['items'][$itemORserviceData->id]['item_id']            = $itemORserviceData->id;
            $item_data_array['items'][$itemORserviceData->id]['item_name']          = $itemORserviceData->name;
            $item_data_array['items'][$itemORserviceData->id]['price']              = $itemORserviceData->price;
            $item_data_array['items'][$itemORserviceData->id]['quantity']           = $itemORserviceData->quantity;
            $item_data_array['items'][$itemORserviceData->id]['description']        = $itemORserviceData->style;
            $item_data_array['items'][$itemORserviceData->id]['number']             = $itemORserviceData->number;
            $item_data_array['items'][$itemORserviceData->id]['barcode']            = $itemORserviceData->barcode;
            $item_data_array['items'][$itemORserviceData->id]['attribute1']         = $itemORserviceData->attribute1;
            $item_data_array['items'][$itemORserviceData->id]['attribute2']         = $itemORserviceData->attribute2;
            $item_data_array['items'][$itemORserviceData->id]['added']              = $itemORserviceData->added;
            $item_data_array['items'][$itemORserviceData->id]['updated']            = $itemORserviceData->updated;
            $item_data_array['items'][$itemORserviceData->id]['online_active']      = $itemORserviceData->online_active;

            $image_count = 1;
            $imagesData  = $items_dal->get_AllImagesPerItemId($itemORserviceData->id);
            foreach($imagesData as $imageDATA){
               $item_data_array['items'][$itemORserviceData->id]['images'][$image_count]['image_id']            = $imageDATA->image_id;
               $item_data_array['items'][$itemORserviceData->id]['images'][$image_count]['image_name']          = $imageDATA->image_name;
               $item_data_array['items'][$itemORserviceData->id]['images'][$image_count]['size']                = $imageDATA->size;
               $item_data_array['items'][$itemORserviceData->id]['images'][$image_count]['mime']                = $imageDATA->mime;
               $item_data_array['items'][$itemORserviceData->id]['images'][$image_count]['width']               = $imageDATA->width;
               $item_data_array['items'][$itemORserviceData->id]['images'][$image_count]['height']              = $imageDATA->height;
               $item_data_array['items'][$itemORserviceData->id]['images'][$image_count]['image_db_id']         = $imageDATA->image_db_id;
               $item_data_array['items'][$itemORserviceData->id]['images'][$image_count]['default_group_image'] = $imageDATA->default_group_image;
               $item_data_array['items'][$itemORserviceData->id]['images'][$image_count]['default_item_image']  = $imageDATA->default_item_image;
               $item_data_array['items'][$itemORserviceData->id]['images'][$image_count]['default']             = $imageDATA->default;
               $item_data_array['items'][$itemORserviceData->id]['images'][$image_count]['added']               = $imageDATA->added;
               $item_data_array['items'][$itemORserviceData->id]['images'][$image_count]['default_group_image'] = $imageDATA->default_group_image;
               list ($IMG_HTML_data, $raw_img_location) = show_ItemOrServiceIMG($company_id,$itemORserviceData->id,$itemORserviceData->number,$imageDATA->image_id,$imageDATA->image_db_id,100,80);
               $item_data_array['items'][$itemORserviceData->id]['images'][$image_count]['img']                 = $raw_img_location;
               $image_count++;
            }
        $count++;
        }
    }
echo json_encode($item_data_array);
}




#####  CATEGORIES #####
if ( ( isset($_GET['page'])&& $_GET['page'] == "categories") && ($company_id != -1) ) {
    require_once('../../../includes/reports_functions.php');
    $reports_dal =  new DAL();
    $categories  = $reports_dal->get_AllCategoriesPerCompanyId($company_id);
    $categories_data_array['results_count']   = (count($categories));
    if (count($categories) > 0 ) {
        foreach($categories as $category){
            $categories_data_array['categories'][$category->id]['category_name']          = $category->name;
        $count++;
        }
    }
echo json_encode($categories_data_array);
}

#####  BRANDS #####
if ( ( isset($_GET['page'])&& $_GET['page'] == "brands") && ($company_id != -1) ) {
    require_once('../../../includes/reports_functions.php');
    $reports_dal =  new DAL();
    $brands  = $reports_dal->get_AllBrandsPerCompanyId($company_id);
    $brands_data_array['results_count']   = (count($brands));
    if (count($brands) > 0 ) {
        foreach($brands as $brand){
            $brands_data_array['brands'][$brand->id]['brand_name']          = $brand->name;
        $count++;
        }
    }
echo json_encode($brands_data_array);
}

#####  Departments #####
if ( ( isset($_GET['page'])&& $_GET['page'] == "departments") && ($company_id != -1) ) {
    require_once('../../../includes/reports_functions.php');
    $reports_dal =  new DAL();
    $departments  = $reports_dal->get_AllDepartmentsPerCompanyId($company_id);
    $departments_data_array['results_count']   = (count($departments));
    if (count($departments) > 0 ) {
        foreach($departments as $department){
            $departments_data_array['departments'][$department->id]['department_name']          = $department->name;
        $count++;
        }
    }
echo json_encode($departments_data_array);
}

?>