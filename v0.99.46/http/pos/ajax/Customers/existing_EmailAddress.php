<?php
include('../../../../includes/general_functions.php');
include('../../../../includes/customers_functions.php');


if ($_GET['email_address']) {
    $email_address  = $_GET['email_address'];
    $dal            = new Customers_DAL();
    $ID             = $dal->get_CustomerDataPerEmail($email_address,$_SESSION['settings']['company_id']);

    if (count($ID) > 0){ $switch = 0; }
    else { $switch = 1; }
    $Response = array(  'ExistResponse' => $switch,
                        'status' => "XXX"
                     );
echo json_encode($Response);
}
?>
