<?php
include('../../../../includes/general_functions.php');
include('../../../../includes/profiles_functions.php');
include('../../../../includes/calendar_functions.php');

if ( $_POST['editProfile_DaysOff_Set_UnSet']) {
    $login_id                   = $_POST['login_id'];
    $action                     = $_POST['action'];
    $appointment_slot_interval  = $_POST['appointment_slot_interval'];
    $appt_book_count            = $_POST['appt_book_count'];
    $selected_date              = $_POST['selected_date'];

    if ($action == 1) {
        $sql = "INSERT INTO logins_days_off (login_id , type , status , date , added_by_login_id , added)
                VALUES ($login_id, 1, $action, '$selected_date', {$_SESSION['settings']['login_id']}, now() )
                ON DUPLICATE KEY UPDATE status = $action";
        $status = 1;
    } 
    elseif ($action == 0) {
        $sql = "INSERT INTO logins_days_off (login_id , type , status , date , added_by_login_id , added)
                VALUES ($login_id, 1, $action, '$selected_date', {$_SESSION['settings']['login_id']}, now() )
                ON DUPLICATE KEY UPDATE status = $action";
        $status = 0;
    }
    $dal        = new InsertUpdateDelete_DAL();
    $update_id  = $dal->query($sql);

    ob_start();
    Calendar_Show_Employees_Hours($login_id,$selected_date,$appointment_slot_interval,$appt_book_count);
    $html      = ob_get_clean();

    header('Content-Type: application/json');
    $Response = array(  'SQL' => $sql,
                        'html' => $html,
                        'status' => $status
                     );
    echo json_encode($Response);
}
?>