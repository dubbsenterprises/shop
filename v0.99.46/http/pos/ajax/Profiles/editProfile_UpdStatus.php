<?php
include('../../../../includes/general_functions.php');
include('../../../../includes/profiles_functions.php');
session_start();

if ( $_GET['editProfile']) {
    $login_id   = $_GET['profiles_login_id'];
    $action     = $_GET['action'];
    $sql        = "update logins set status = $action where id = $login_id";
    $dal        = new InsertUpdateDelete_DAL();
    $update_id  = $dal->insert_query($sql);
    profiles();
}
?>