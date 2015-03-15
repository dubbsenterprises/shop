<?php
include('../../../../includes/general_functions.php');
include('../../../../includes/sales_functions.php');
session_start();

$dal        = new GENERAL_DAL();
$insert_dal = new InsertUpdateDelete_DAL();

if ( isset($_GET['newCustomer']) && $_GET['newCustomer'] == 1 ) {
//    $login_check_result = $dal->check_loginExists($_GET['NC_login_name']);
//    if ( $login_check_result[0]->count == 0 ) {
    $sql="insert into customers (company_id,firstname,surname,email,address,town,state,zipcode,phone_num,country,added)
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
#echo $sql;
$login_id = $insert_dal->insert_query($sql);
$_SESSION['sale']['customer_id'] = $login_id ;
sales_choose_customer();
}
?>