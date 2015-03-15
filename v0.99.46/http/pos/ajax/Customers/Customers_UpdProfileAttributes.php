<?php
include('../../../../includes/general_functions.php');
include('../../../../includes/profiles_functions.php');
session_start();
$login_id   = $_GET['profiles_login_id'];
$sql        = "update logins set ";

if      ( $_GET['editProfile'] == "ProfileAttributes") {
    $sql       .= "firstname = '".$_GET['firstname']."', ";
    $sql       .= "lastname  = '".$_GET['lastname']."', ";
    $sql       .= "level     = '".$_GET['level']."', ";
    if (strlen($_GET['password']) >=8 ) {
    $sql       .= "password     = '".md5($_GET['password'])."', ";
    }
    $sql       .= "employee_quote = ".quoteSmart($_GET['employee_quote'])." ";

}
 elseif ($_GET['editProfile'] == "ElectronicInfo") {
    $sql       .= "email_address = '".$_GET['email_address']."', ";
    $sql       .= "gmail_username = '".$_GET['gmail_username']."', ";
    $sql       .= "gmail_password = '".$_GET['gmail_password']."' ";
}
    $sql       .= " where id = $login_id";
    #echo $sql;
    $dal        = new InsertUpdateDelete_DAL();
    $update_id  = $dal->insert_query($sql);
    profiles();
?>