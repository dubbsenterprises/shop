<?php
include('../../../includes/general_functions.php');
include('../../../includes/customers_functions.php');
include('../'.$_SESSION['settings']['templateType'].'_functions.php');

    ob_start();
    make_appointment_step3_RIGHT_NEWUSER();
    $response_array['html'] = ob_get_clean();
    echo json_encode($response_array);
?>