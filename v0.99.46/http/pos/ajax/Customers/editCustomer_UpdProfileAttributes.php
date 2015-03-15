<?php
include('../../../../includes/general_functions.php');
include('../../../../includes/customers_functions.php');
session_start();
$customer_id   = $_GET['customers_customer_id'];
$sql        = "update customers set ";

if      ( $_GET['editCustomer'] == "CustomerAttributes") {
    $sql       .= "firstname = '".$_GET['firstname']."', ";
    $sql       .= "surname  = '".$_GET['lastname']."' ";

}
 elseif ($_GET['editCustomer'] == "CustomerXXXXXXXXXX") {
    $sql       .= "email_address = '".$_GET['email_address']."', ";
    $sql       .= "gmail_username = '".$_GET['gmail_username']."', ";
    $sql       .= "gmail_password = '".$_GET['gmail_password']."' ";
}
 elseif ($_GET['editCustomer'] == "CustomerYYYYYYYYYYYYY") {
    $sql       .= "password     = '".md5($_GET['password'])."' ";
}
    $sql       .= " where id = $customer_id";
    #echo $sql;
    $dal        = new InsertUpdateDelete_DAL();
    $update_id  = $dal->insert_query($sql);
    customers();
?>