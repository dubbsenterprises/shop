<?php
include_once('../../../includes/general_functions.php');
include_once('../../../includes/inventory_management_functions.php');
include_once('../../../includes/appointment_functions.php');
include_once('../../../includes/customers_functions.php');
include_once('../../'.$_SESSION['settings']['templateType'].'/'.$_SESSION['settings']['templateType'].'_functions.php');
    ob_start();
    login_horizontal_data();
    $response_array['html'] = ob_get_clean();
    echo json_encode($response_array);
?>