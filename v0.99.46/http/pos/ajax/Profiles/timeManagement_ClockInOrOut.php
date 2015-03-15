<?php
include('../../../../includes/general_functions.php');
include('../../../../includes/profiles_functions.php');

if ( $_GET['login_id']) {
    $login_id   = $_GET['login_id'];
    $action     = $_GET['action'];
    $sql        = "insert into timeManagement 
                    (date_time,login_id,action)
                    values (convert_tz(now(), 'utc', 'america/chicago'), $login_id, '$action')";
    $dal        = new InsertUpdateDelete_DAL();
    $insert_id  = $dal->insert_query($sql);
    profiles_clock_in_out();
}
?>