<?php
require_once('../../../../includes/general_functions.php');
require_once('../../../../includes/shop_db_functions.php');
require_once('../../../../includes/shop_sale_functions.php');
require_once('../../../../includes/shop_processVariables.php');
require_once('../../../../includes/sales_functions.php');

$InsertUpdateDelete_Dal = new InsertUpdateDelete_DAL();
$companies_dal          = new Sales_DAL();

if ($_POST['action'] == 'Sales_AllItems_Discount') {
    $discount_percentage    = urldecode($_POST['discount_percentage']);

    $response_array['returnCode']               = 1;
    $response_array['itemsArray']               = $_SESSION['sale']['basket']['items'];
    $response_array['itemCount']                = count($_SESSION['sale']['basket']['items']);
    foreach ($_SESSION['sale']['basket']['items'] as $item) {
        $response_array['items'][$item['id']]['additional_discount']            = $discount_percentage;
        $_SESSION['sale']['basket']['items'][$item['id']]['additional_discount']= $discount_percentage;
    }
    
    dbconnect();
    processVariables();
    ob_start();
    sales();
    $html      = ob_get_clean(); 
    $response_array['html']   = $html;             
    echo json_encode($response_array);
}
?>