<?php
include('../../../../includes/calendar_functions.php');

if ( $_POST['staff_id']) {
    $staff_id                   = $_POST['staff_id'];
    $selected_date              = $_POST['selected_date'];
    $selected_time              = $_POST['selected_time'];
    $appointment_slot_interval  = $_POST['appointment_slot_interval'];

    ob_start();
    AppointmentCalendar_Open_Slot_Book($staff_id,$selected_date,$selected_time,$appointment_slot_interval);
    $html   = ob_get_clean();

    $response_array['returnCode']   = 1;
    $response_array['html']         = $html;
    echo json_encode($response_array);
}
?>