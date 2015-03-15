<?php
include('../../../../includes/general_functions.php');
include('../../../../includes/profiles_functions.php');
include('../../../../includes/calendar_functions.php');

if ( $_POST['AppointmentCalendar_Open_Slot_Book_Set_UnSet']) {
    $staff_id                   = $_POST['staff_id'];
    $action                     = $_POST['action'];
    $appointment_slot_interval  = $_POST['appointment_slot_interval'];
    $selected_date              = $_POST['selected_date'];
    $selected_time              = $_POST['selected_time'];

    if ($action == 1) {
        $sql = "INSERT INTO logins_appt_slots_off (login_id , type , status , date , appt_slot, appointment_slot_interval, added_by_login_id , added)
                VALUES ($staff_id, 1, $action, '$selected_date', '$selected_time', '$appointment_slot_interval', {$_SESSION['settings']['login_id']}, now() )
                ON DUPLICATE KEY UPDATE status = $action";
        $status = 1;
    }
    elseif ($action == 0) {
        $sql = "INSERT INTO logins_appt_slots_off (login_id , type , status , date, appt_slot, appointment_slot_interval, added_by_login_id , added)
                VALUES ($staff_id, 1, $action, '$selected_date', '$selected_time', '$appointment_slot_interval', {$_SESSION['settings']['login_id']}, now() )
                ON DUPLICATE KEY UPDATE status = $action";
        $status = 0;
    }
    $dal        = new InsertUpdateDelete_DAL();
    $update_id  = $dal->query($sql);
    $dateTimeStart_massaged     = substr_replace(sprintf('%04d',$selected_time ), ':', -2, 0);

    ob_start();
    AppointmentCalendar_Open_Slot_Book($staff_id,$selected_date,$selected_time,$appointment_slot_interval);
    $html      = ob_get_clean();

    header('Content-Type: application/json');
    $Response = array(  'SQL' => $sql,
                        'dateTimeStart_massaged' => date('g:i a', strtotime($dateTimeStart_massaged)),
                        'html' => $html,
                        'status' => $status
                     );
    echo json_encode($Response);
}
?>