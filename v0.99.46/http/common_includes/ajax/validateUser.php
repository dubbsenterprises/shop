<?php
session_start();
include_once('../../../includes/general_functions.php');
include_once('../../../includes/inventory_management_functions.php');
include_once('../../../includes/appointment_functions.php');
include_once('../../../includes/customers_functions.php');
include_once('../../'.$_SESSION['settings']['templateType'].'/'.$_SESSION['settings']['templateType'].'_functions.php');

$general_dal    = new GENERAL_DAL();
$inventory_dal  = new INVENTORY_DAL();

if ( isset($_POST['email']) ) {

    $Customers_DAL = new Customers_DAL();
    $CustomersData = $Customers_DAL->get_CustomerDataPerEmail($_POST['email'],$_SESSION['settings']['company_id']);
    if (count($CustomersData) == 1) {
        $_SESSION['appointment']['customer_id'] = $CustomersData[0]->id;
        $_SESSION['appointment']['first_name']  = $CustomersData[0]->firstname;
        $_SESSION['appointment']['last_name']   = $CustomersData[0]->surname;
        $_SESSION['appointment']['phone_num']   = $CustomersData[0]->phone_num;
        $_SESSION['appointment']['user_email']  = $CustomersData[0]->email;
        $Response =   array('count' => count($CustomersData),
                            'email' => $_POST['email']);
    echo json_encode($Response);
    } else {
        $_SESSION['appointment']['user_email'] = $_POST['email'];

        $Response =   array('count' => 0,
                            'email' => "");
        echo json_encode($Response);
    }
}
?>