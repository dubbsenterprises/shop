<?php
require_once('../../../../includes/general_functions.php');
require_once('../../../../includes/shop_db_functions.php');
require_once('../../../../includes/shop_sale_functions.php');
require_once('../../../../includes/shop_processVariables.php');
require_once('../../../../includes/sales_functions.php');
    dbconnect();
    processVariables();
    ob_start();
    sales();
    $html      = ob_get_clean(); 
    
    $Response = array( 'html' => $html,
                       'message' => $_SESSION['message']
                     );
    unset($_SESSION['message']);
    echo json_encode($Response);
?>
