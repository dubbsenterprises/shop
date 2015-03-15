<?php
include('../../../../includes/general_functions.php');
include('../../../../includes/profiles_functions.php');
//session_start();

if ( $_POST['updateServiceEmployeePrice']) {
    $login_id       =$_POST['login_id'];
    $service_id     =$_POST['service_id'];
    $employee_price =$_POST['employee_price'];

    $Profiles_DAL = new Profiles_DAL();
    $apptStatusInfo = $Profiles_DAL->get_ServiceStatus_byLoginId($login_id,$service_id);
    $status = 1; 

    if (count($apptStatusInfo) == 0) {
        $sql = "insert into logins_services
                (status,login_id,service_id,employee_price)
                values ($status,$login_id,$service_id,$employee_price)";
    }
    else {
        $sql = "update logins_services
                set status = $status, updated=now(), employee_price=$employee_price
                where login_id = $login_id and
                service_id = $service_id";
    }

    $dal        = new InsertUpdateDelete_DAL();
    $update_id  = $dal->query($sql);

    header('Content-Type: application/json');
    $Response = array(  'SQL' => $sql,
                        'status' => $status,
                        'employee_price' => $employee_price,
                        'update_id' => $update_id
                     );
    echo json_encode($Response);
}
?>