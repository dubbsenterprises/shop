<?php
include('../../../../includes/general_functions.php');
include('../../../../includes/profiles_functions.php');
//session_start();

if ( $_POST['editProfile_Appt_ActivateDeActivate']) {
    $login_id   =$_POST['login_id'];
    $action     =$_POST['action'];

    if ($action == 1) {
        $sql = "update logins
                set appt_active = 1
                where id = $login_id";
        $status = 1;
    }
    elseif ($action == 0) {
        $sql = "update logins
                set appt_active = 0
                where id = $login_id";
        $status = 0;
    }
    //echo $sql ;
    $dal        = new InsertUpdateDelete_DAL();
    $update_id  = $dal->query($sql);

    header('Content-Type: application/json');
    $Response = array(  'SQL' => $sql,
                        'status' => $status
                     );
    echo json_encode($Response);
}
?>