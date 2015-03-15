<?php
session_start();
include_once('../../../includes/general_functions.php');
include_once('../../../includes/inventory_management_functions.php');
include_once('../../../includes/appointment_functions.php');
include_once('../../../includes/customers_functions.php');
include_once('../../'.$_SESSION['settings']['templateType'].'/'.$_SESSION['settings']['templateType'].'_functions.php');

$InsertUpdateDelete_DAL = new InsertUpdateDelete_DAL();
$Customers_DAL          = new Customers_DAL();

if ( isset($_POST['newuser']) && $_POST['newuser'] == 1 ) {
    $CustomersData = $Customers_DAL->get_CustomerDataPerEmail($_POST['NU_user_email'],$_SESSION['settings']['company_id']);
        if ( count($CustomersData) == 0 ) {
            $query="insert into customers (company_id,firstname,surname,phone_num,email,added) values (".
                     $_SESSION['settings']['company_id'] . ",
                '" . $_POST['NU_first_name']  . "',
                '" . $_POST['NU_last_name']   . "',
                '" . $_POST['NU_phone_num']   . "',
                '" . $_POST['NU_user_email']  . "',
                    now() )";
            $customer_id = $InsertUpdateDelete_DAL->insert_query($query);
            if ($customer_id) {
                $_SESSION['appointment']['first_name']  = $_POST['NU_first_name'];
                $_SESSION['appointment']['last_name']   = $_POST['NU_last_name'];
                $_SESSION['appointment']['user_email']  = $_POST['NU_user_email'];
                $_SESSION['appointment']['phone_num']   = $_POST['NU_phone_num'];
                $_SESSION['appointment']['customer_id'] = $customer_id;
                $success = 1;
                $message = "New Registration worked!";
                email_Customer_Registration($customer_id,0);
            }
            else {
                $success = 0 ;
                $message = "The email address doesn't exist, but something went wrong.";
            }
        }
        elseif ( count($CustomersData) >= 1 ) {
            $success = 0;
            $message = "Email Address Already Exists!";
        }
    $response_array['returnCode']   = $success;
    $response_array['status']       = $success;
    $response_array['message']      = $message;
}
echo json_encode($response_array);
?>