<?php
include('../../../../includes/general_functions.php');
include('../../../../includes/customers_functions.php');
session_start();

$dal        = new GENERAL_DAL();
$insert_dal = new InsertUpdateDelete_DAL();

if ( isset($_GET['newCustomer']) && $_GET['newCustomer'] == 1 ) {
    $sql="insert into customers (company_id,firstname,surname,email,address,town,country,state,zipcode,phone_num,added)
        values (
         ".$_SESSION['settings']['company_id'] . ",
        '" . $_GET['NC_first_name']  . "',
        '" . $_GET['NC_last_name']   . "',
        '" . $_GET['NC_user_email']  . "',
        '" . $_GET['NC_Address']   . "',
        '" . $_GET['NC_City']   . "',
        '" . $_GET['NC_Country']   . "',
        '" . $_GET['NC_State']  . "',
        '" . $_GET['NC_PostalCode']  . "',
        '" . $_GET['NC_phone_num']   . "'," ;
        $sql .= "now() )";
$login_id = $insert_dal->insert_query($sql);
}
?>