<?php
include('../../../../includes/general_functions.php');
include('../../../../includes/profiles_functions.php');
//session_start();

if ( $_GET['updateApptTime']) {
    $login_id=$_GET['login_id'];
    $time=$_GET['time'];
    $day_of_week=$_GET['day_of_week'];
    $appointment_slot_interval = $_GET['appointment_slot_interval'];

    $dal = new Profiles_DAL();
    $apptStatusInfo = $dal->get_apptTimeStatus($login_id,$day_of_week,$time,$appointment_slot_interval);
    $count = count($apptStatusInfo);
    if ($count == 0 || $apptStatusInfo[0]->status == 0) {
        $status = 1;
    } else { $status = 0;}    

    if ($count == 0) {
        $sql = "insert into logins_def_appt_times
                        (status,login_id,day_of_week,time,appointment_slot_interval)
                        values ($status,$login_id,$day_of_week,'$time',$appointment_slot_interval)";
    }
    else {
        $sql = "update logins_def_appt_times
                        set status = $status
                        where login_id = $login_id and
                        day_of_week = $day_of_week and
                        time = '$time' and
                        appointment_slot_interval = $appointment_slot_interval";
    }
    //echo $sql ;
    $dal        = new InsertUpdateDelete_DAL();
    $update_id  = $dal->insert_query($sql);
    header('Content-Type: application/json');
    $Response = array(  'SQL' => $sql,
                        'status' => $status,
                        'Count' => $count);
    echo json_encode($Response);
}
?>