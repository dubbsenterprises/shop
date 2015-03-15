<?php
include('../../../../includes/general_functions.php');
include('../../../../includes/profiles_functions.php');
//session_start();

if ( $_POST['updateServiceActivateDeActivate']) {
    $login_id=$_POST['login_id'];
    $service_id=$_POST['service_id'];

    $Profiles_DAL = new Profiles_DAL();
    $apptStatusInfo = $Profiles_DAL->get_ServiceStatus_byLoginId($login_id,$service_id);
    
    if   ( count($apptStatusInfo) == 0 || $apptStatusInfo[0]->status == 0) {$status = 1; }
    else {                                                                  $status = 0; }

    if (count($apptStatusInfo) == 0) {
        $sql = "insert into logins_services
                        (status,login_id,service_id)
                        values ($status,$login_id,$service_id)";
    }
    else {
        $sql = "update logins_services
                        set status = $status, updated=now()
                        where login_id = $login_id and
                        service_id = $service_id";
    }
    //echo $sql ;
    $dal        = new InsertUpdateDelete_DAL();
    $update_id  = $dal->insert_query($sql);
    header('Content-Type: application/json');
    $Response = array(  'SQL' => $sql,
                        'status' => $status,
                        'Count' => count($apptStatusInfo));
    echo json_encode($Response);
}
?>