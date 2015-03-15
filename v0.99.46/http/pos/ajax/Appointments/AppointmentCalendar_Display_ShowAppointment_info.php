<?php
include('../../../../includes/calendar_functions.php');
if ( $_GET['appointment_id']) {
    $Appointment_data_id    = $_GET['appointment_id'];
    $Appointments_DAL       = new Appointments_DAL();
    $appointment_data       = $Appointments_DAL->Appointments_displayAppointmentInfo($Appointment_data_id);
    ?>
    <div class='d_InlineBlock wp95 hp08 t_align_center main_bc_color2 main_color2_text s09 bold'>Services Info</div>
    <div class='d_InlineBlock wp95 hp08 left  main_bc_color1_light main_color1_light_text no-overflow'>
        <div class='f_left wp90 hp100 mp left  mp no-overflow' onclick='Customer_editProfile(<?=$appointment_data[0]->customer_id?>)'><div class="f_left s06">Customer:</div><?=$appointment_data[0]->first_name?>&nbsp;<?=$appointment_data[0]->last_name?></div>
        <div class='f_left wp10 hp100 mp right mp no-overflow' onclick='Sales_Add_Appointment_Services(<?=$Appointment_data_id?>)'><img class="hp100 wp100"alt="" src="/common_includes/includes/images/checkout.png" id="checkout_img" /></div>
    </div>
    <div class='f_left wp100 hp80 s07' onmouseover="" onmouseout='Show_Upcoming_Appts_Calendar_View("Show_Upcoming_Appts_Calendar_View")' id="tmp">
        <?services_selected_for_appointment($Appointment_data_id)?>
    </div>
<?}?>