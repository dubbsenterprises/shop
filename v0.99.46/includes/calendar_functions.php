<?php
include_once('general_functions.php');
include_once('profiles_functions.php');
include_once('appointment_functions.php');

class Calendar_DAL {
  public function __construct(){}
  public function get_Distinct_startDateTimes_from_Appointemnts($company_id,$date,$current_date){
        $sql = "SELECT  distinct(startDate)
                    from appointments
                    where startDate like '$date%' and company_id = $company_id and status = 0 ";
    if ($date == $current_date) {
        $sql.= " and startDate > convert_tz(now(), 'utc', ".quoteSmart($_SESSION['preferences']['timezone']).") ";
    }
        $sql.=               " order by startDate;";
    #print $sql;
    return $this->query($sql);
  }
  public function get_Appointments_Per_Start_Time($company_id,$startDate){
    $sql = "SELECT  l.firstname as login_firstname,
                    c.id as customer_id,
                    c.firstname as customer_firstname,
                    c.surname as customer_lasttname

                    from appointments a
                    join logins l on a.login_id = l.id
                    left join customers c on  a.customer_id = c.id
      
                    where a.startDate = '$startDate' and a.company_id = $company_id and a.status = 0";
    #print $sql;
    return $this->query($sql);
  }
  public function get_Get_DayOff_count_based_on_profileID_and_Date($staff_id,$selected_date){
    $sql = "SELECT id from logins_days_off where date(date) = '$selected_date' and login_id = $staff_id and status = 1";
    #print $sql;
    return $this->query($sql);
  }
  public function get_Get_Employee_Slots_Available_by_Day($staff_id,$selected_date,$appointment_slot_interval){
    $day_of_week = date('w', strtotime($selected_date));
    $sql = "SELECT count(time) as slots
            from logins_def_appt_times 
            where 
            status                      = 1 and 
            appointment_slot_interval   = $appointment_slot_interval and 
            login_id                    = $staff_id and 
            day_of_week                 = $day_of_week";
    #print $sql;
    return $this->query($sql);
  }
  
  public function get_ImageID_byCustomerID($customer_id){
      $sql = "SELECT image_id
                from item_image_mappings
                where customer_id = $customer_id and
                deleted is null
                order by default_item_image desc, default_group_image desc, added asc
                limit 1";
    #print "$sql";
    return $this->query($sql);
  }
  public function get_default_Customer_ImageID($customer_id){
      $sql = "SELECT image_id
                from item_image_mappings
                where customer_id = $customer_id and
                `default` = 1 and
                deleted is null
                order by default_item_image desc, default_group_image desc, added asc
                limit 1";
    #print "$sql";
    return $this->query($sql);
  }

  private function dbconnect(){
    $conn = mysql_connect($_SESSION['MYSQL_HOST'], $_SESSION['MYSQL_USER'], $_SESSION['MYSQL_PASS']) or die ("<br/>Cουld not connect tο MySQL server");
    mysql_select_db($_SESSION['MYSQL_DATABASE'],$conn) or die ("<br/>Cουld nοt select the indicated database");
	return $conn;
  }
  private function query($sql){
    $this->dbconnect();
    $res = mysql_query($sql);
    if ($res){
        if (strpos($sql,'SELECT') === false){
            return true;
        }
    }
    else{
        if (strpos($sql,'SELECT') === false){
            return false;
        }
        else{
            return null;
        }
    }
    $consequences = array();
    while ($row = mysql_fetch_array($res)){
      $result = new DALQueryResult();
      foreach ($row as $k=>$v){
        $result->$k = $v;
      }
      $consequences[] = $result;
    }
    return $consequences;
  }
}

function calendar() {
    $general_dal                = new GENERAL_DAL();
    $PreferenceData             = $general_dal->get_CompanyPreference_by_Company_ID($_SESSION['settings']['company_id'],'timezone');
    date_default_timezone_set($PreferenceData[0]->value);
    $current_date = date("Y-m-d",mktime());
    if (isset($_SESSION['calendar']['display_date'])) {
        $selected_date = $_SESSION['calendar']['display_date'];
    } else {
        $selected_date = $current_date;
    }
?>
<head>
<script src="includes/sales_functions.js"       type="text/javascript"></script>
<script src="includes/customers_functions.js"   type="text/javascript"></script>
</head>
<div class="ReportsTopRow main_bc_color2 main_color2_text "><a title="Calendar" onclick="mainDiv('calendar_appointments'); return false;">Calendar</a></div>
    <div style="max-height: 1000px;" class="f_left wp100 hp94">
        <div class="leftSpace main_bc_color2 main_color2_text">&nbsp;</div>
            <div class="f_left d_InlineBlock wp96 hp100">
            <div class="wp100 f_left bcwhite">
                        <?calendar_top_display($current_date)?>
            </div>
            <div class="wp100 hp90 f_left" >
                <div class="f_left  wp20 hp100 left scrolling-y">
                            <?=Show_Upcoming_Appts_Brief($selected_date,$current_date)?>
                </div>
                <div class="f_right wp80 hp100">
                            <?=Calendar_Show_Employees($general_dal,$selected_date)?>
                </div>
            </div>
        </div>
        <div class="rightSpace main_bc_color2 main_color2_text">&nbsp;</div>
    </div>
<div class="ReportsBottomRow main_bc_color2 main_color2_text">&nbsp;</div>
<?
}
    function calendar_top_display($current_date){
        $current_date = strtotime(date("Y-m-d", strtotime($current_date)));
        ?>
        <div class="f_left wp20 hp100">
            <div class="d_InlineBlock wp100">
                <div class="bold">Today is:</div>
                <div class="mp s08" onclick="AppointmentCalendar_Display_ChangeDate('<?=date('Y-m-d', $current_date)?>')">&nbsp;<?=date('D\, M jS\, o',$current_date)?></div>
            </div>
        </div>
        <div class="f_left wp80 hp100"><?=Calendar_DateSelect()?></div>
    <?
    }
        function Calendar_DateSelect(){
        $general_dal                = new GENERAL_DAL();
        $PreferenceData             = $general_dal->get_CompanyPreference_by_Company_ID($_SESSION['settings']['company_id'],'timezone');
        date_default_timezone_set($PreferenceData[0]->value);

        if (isset($_SESSION['calendar']['display_date'])) {
           $currentDate =  $_SESSION['calendar']['display_date'];
        } else {
           $currentDate = date("Y-m-d");// current date
        }
        $title_non_selected = "View Schedule for this day?";
        $day0  = strtotime(date("Y-m-d", strtotime($currentDate)) . "-1 day");
        $day1  = strtotime(date("Y-m-d", strtotime($currentDate)));
        $day2  = strtotime(date("Y-m-d", strtotime($currentDate)) . "+1 day");
        $day3  = strtotime(date("Y-m-d", strtotime($currentDate)) . "+2 day");
        $day4  = strtotime(date("Y-m-d", strtotime($currentDate)) . "+3 day");
        $day5  = strtotime(date("Y-m-d", strtotime($currentDate)) . "+4 day");
            ?>
            <div class="d_InlineBlock f_left wp100 mp">
                <div class="f_left bclightgray wp15 s07" onclick="AppointmentCalendar_Display_ChangeDate('<?=date('Y-m-d', $day0)?>')" title="<?=$title_non_selected?>"><?=date('D', $day0)?><br><?=date('Y-m-d', $day0)?></div>
                <div class="f_left bcgray wp15 bold"     onclick="AppointmentCalendar_Display_ChangeDate('<?=date('Y-m-d', $day1)?>')" title="You are viewing this day now."><?=date('l', $day1)?><br><?=date('Y-m-d', $day1)?></div>
                <div class="f_left bclightgray wp15 s07" onclick="AppointmentCalendar_Display_ChangeDate('<?=date('Y-m-d', $day2)?>')" title="<?=$title_non_selected?>"><?=date('D', $day2)?><br><?=date('Y-m-d', $day2)?></div>
                <div class="f_left bclightgray wp15 s07" onclick="AppointmentCalendar_Display_ChangeDate('<?=date('Y-m-d', $day3)?>')" title="<?=$title_non_selected?>"><?=date('D', $day3)?><br><?=date('Y-m-d', $day3)?></div>
                <div class="f_left bclightgray wp15 s07" onclick="AppointmentCalendar_Display_ChangeDate('<?=date('Y-m-d', $day4)?>')" title="<?=$title_non_selected?>"><?=date('D', $day4)?><br><?=date('Y-m-d', $day4)?></div>
                <div class="f_left bclightgray wp15 s07" onclick="AppointmentCalendar_Display_ChangeDate('<?=date('Y-m-d', $day5)?>')" title="<?=$title_non_selected?>"><?=date('D', $day5)?><br><?=date('Y-m-d', $day5)?></div>
            </div>
    <?    }
    function Show_Upcoming_Appts_Brief($query_date,$current_date){
        if        ($query_date == $current_date)    {$query_date_title='Coming up TODAY';}
        elseif    ($query_date <  $current_date)    {$query_date_title='<font color=red>PAST</font> on '.$query_date;}
        elseif    ($query_date >  $current_date)    {$query_date_title='Coming up on '.$query_date;}
        ?>
<head>
    <style>
    div.ui-datepicker{
     font-size:8px;
     width:98%;
     height:100%;
    }
    </style>
    <script type="text/javascript">
        $(function() {
            $("#calendar").datepicker({
                numberOfMonths: 1,
                autoSize: true,
                dateFormat: 'yy-mm-dd',
                gotoCurrent: true,
                defaultDate: '<?=$query_date?>',
                onSelect: function(dateText,inst){
                    AppointmentCalendar_Display_ChangeDate(dateText)
                }
            });
        });
    </script>
</head>
        <div class="f_left left wp100 hp40 bcgray" id="calendar">
        </div>
        <div class="f_left wp100 hp01 no-overflow">
            &nbsp;
        </div>
        <div class="f_left wp100  hp05 bcgray s08 bold">
          <?=$query_date_title?>
        </div>
        <div class="f_left wp100 vtop hp50" id="Show_Upcoming_Appts_Calendar_View">
            <? Show_Upcoming_Appts_Calendar_View($query_date,$current_date) ?>
        </div>
<script type="text/javascript">

</script>
<?}
    function Show_Upcoming_Appts_Calendar_View($query_date,$current_date){
            $Calendar_dal     = new Calendar_DAL();
            $startDateTimes   = $Calendar_dal->get_Distinct_startDateTimes_from_Appointemnts($_SESSION['settings']['company_id'],$query_date,$current_date);
            if ( count($startDateTimes) > 0 ) {?>
                <?foreach ($startDateTimes as $startDateTime) {?>
                        <div class="wp100 left bclightgray s08">
                          <?=date('g:i a', strtotime($startDateTime->startDate))?>
                        </div>
                    <?
                    $Appointments_Per_Start_Time        = $Calendar_dal->get_Appointments_Per_Start_Time($_SESSION['settings']['company_id'],$startDateTime->startDate);
                    foreach ($Appointments_Per_Start_Time as $Appointment) {
                        if ($Appointment->customer_firstname === NULL) {$Appointment->customer_firstname = "Ms./Mr. Unknown"; }
                        if ($Appointment->customer_lasttname === NULL) {$Appointment->customer_lasttname = "Unknown"; }
                        ?>
                        <div class=" wp98 right mb5 s07 no-overflow" onclick="Customer_editProfile(<?=$Appointment->customer_id?>)">
                          (<?=$Appointment->login_firstname[0]?>), <?=$Appointment->customer_firstname?> <?=$Appointment->customer_lasttname?>
                        </div>
                    <?}?>
                <? }?>
            <?} else { ?>
            <div class="wp100 bclightgray s08">
              There aren't any more appointments scheduled today.
            </div>
            <? }
    }
    function Calendar_Show_Employees($general_dal,$selected_date) {
        setup_path_general();
        $Profiles_DAL   = new Profiles_DAL();
        $Calendar_dal   = new Calendar_DAL();
        $employees      = $Profiles_DAL->get_AllEmployeesPerCompanyId($_SESSION['settings']['company_id'],0,1,1);

        $appointment_slot_interval_data = $general_dal->get_CompanyPreference_by_Company_ID($_SESSION['settings']['company_id'],'appointment_slot_interval');
        $appointment_slot_interval      = ( count($appointment_slot_interval_data)==0 ) ? 30 : $appointment_slot_interval_data[0]->value;
    ?>
        <? if (count($employees) > 0 ) {
            $altClass   = "bctr1a";
            $count      = 0;?>
                <div id="calendar_appointmentsBodyCenter" 
                     class="wp99 hp100 f_left left d_InlineBlock scrolling" 
                     style="white-space: nowrap; max-width: 900px;">
                    <div class="d_InlineBlock w40 hp100 vtop">
                        <div class="hp05 wp100">
                            <div class="">&nbsp;</div>
                        </div>
                        <div class="hp95 wp100">
                        <? $hour = 600; while ($hour <= 2300) { ?>
                            <?=Calendar_Show_DaysHoursMargin($hour,$appointment_slot_interval)?>
                            <? $hour += 100; } ?>
                        </div>
                    </div>
                    <?foreach($employees as $employee) { 
                    $appt_book_count                  = count($general_dal->appointment_Get_AppointmentCount_based_on_profileID_and_Date($employee->id,$selected_date));
                    $Employee_DayOff_count            = count($Calendar_dal->get_Get_DayOff_count_based_on_profileID_and_Date($employee->id,$selected_date));
                    $Employee_Slots_Available_by_Day  = $Calendar_dal->get_Get_Employee_Slots_Available_by_Day($employee->id,$selected_date,$appointment_slot_interval);
                      
                      if ($Employee_DayOff_count == 1) { 
                            $image_html_data =  '<img src="../common_includes/includes/images/Drink_BeerBottle.png"    height="12" width="12" alt="Day Off" title="Day Off">';
                            $action = 0;
                      } else {
                            $image_html_data =  '<img src="../common_includes/includes/images/work.png"                height="12" width="12" alt="Work Day" title="Work Day">';
                            $action = 1;
                      }
                    ?>
                    <?if ($Employee_Slots_Available_by_Day[0]->slots > 0) {?>
                        <div class="d_InlineBlock  w150 hp100">
                            <div class="wp100 hp05">
                                 <div class="d_InlineBlock wp100 hp100 bcgray mauto" >
                                     <div class="f_left wp85 hp100 s08 pt2 no-overflow" >
                                        <? if ( $_SESSION['settings'][$_SESSION['settings']['login_id']]['level'] >= 2)   { ?>
                                            <a onclick="editProfile_login(<?=$employee->id?>)" href="javascript: none();" class="mp"><?=$employee->firstname?> </a>
                                        <? } else { ?>
                                            <?=$employee->firstname?>
                                        <? } ?>
                                        - <?=$appt_book_count?>/<?=$Employee_Slots_Available_by_Day[0]->slots?>
                                     </div>
                                     <div class="f_left wp15 hp100 mp" id="Calendar_Employees_Day_Available_Icon_<?=$employee->id?>" onclick="editProfile_DaysOff_Set_UnSet(<?=$employee->id?>,<?=$action?>,'<?=$selected_date?>',<?=$appointment_slot_interval?>,<?=$appt_book_count?>)" href="javascript: none();">
                                            <?=$image_html_data?>
                                     </div>
                                 </div>
                            </div>
                            <div class="hp95" id="Calendar_Show_Employees_Hours_<?=$employee->id?>">
                                <?
                                $hour = 600; 
                                while ($hour <= 2300) {
                                Calendar_Show_DaysHoursVertical($employee->id,$hour,$selected_date,$appointment_slot_interval,$appt_book_count);
                                $hour += 100; } ?>
                            </div>
                        </div>
                    <? $count++;
                    }
                 } ?>
                </div>
                <script type="text/javascript">
                        divName = 'calendar_hour_'+<?=date('G')?>;
                        document.getElementById(divName).scrollIntoView();
                </script>
            <?} ?>
    
    <?
}
        function Calendar_Show_DaysHoursMargin($hour,$appointment_slot_interval){
        $DivHeight          = "h10px";
        $hour_title         = date('g A', strtotime(substr_replace(sprintf('%04d',$hour ), ':', -2, 0)));
        $hour_single_digit  = date('G',   strtotime(substr_replace(sprintf('%04d',$hour ), ':', -2, 0)));
        ?>
            <div class="wp100 b0 s07 bcgray" id="calendar_hour_<?=$hour_single_digit?>">
                <div style="border-top:1px solid #000; border-bottom:1px solid #000;padding:.1em;">
                    <div class="b1 btgt <?=$DivHeight?>">&nbsp;<?=$hour_title?></div>
                    <div class="b1 <?=$DivHeight?>">&nbsp;</div>
                    <div class="b1 <?=$DivHeight?>">&nbsp;</div>
                    <div class="b1 <?=$DivHeight?>">&nbsp;</div>
                </div>
            </div>
        <?}
        function Calendar_Show_Employees_Hours($employee_id,$selected_date,$appointment_slot_interval,$appt_book_count){
                $hour = 600; while ($hour <= 2300) {
                    Calendar_Show_DaysHoursVertical($employee_id,$hour,$selected_date,$appointment_slot_interval,$appt_book_count);
                $hour += 100; }
        }        
        function Calendar_Show_DaysHoursVertical($staff_id,$hour,$selected_date,$appointment_slot_interval,$appt_book_count){
            $general_dal = new GENERAL_DAL();
            $Available_Appointments_array   = SetSessionsOfAvailable_APPTS($staff_id,$selected_date,$appointment_slot_interval);
            $appointment_Get_LoginAvailibility_based_on_DaysOff_Table_Info  = $general_dal->appointment_Get_LoginAvailibility_based_on_DaysOff_Table($staff_id,$selected_date);
            if ( count($appointment_Get_LoginAvailibility_based_on_DaysOff_Table_Info) == 1)        { $day_off = 1 ; } else { $day_off = 0 ;}
    ?>
            <div class="wp100 s07 bclightgray">
                <div class="h40px" style="border:1px solid #000; padding:.1em;">
                    <?
                    $current_time = $hour ;
                    while ($current_time < ($hour+60)) {
                    list($result,$conflicting_start_time,$appointment_ids) = checkEmployeeAvailibilityByTimeSlot($staff_id,$Available_Appointments_array,$selected_date,$current_time,$appointment_slot_interval,0,0,$day_off,$appt_book_count);
                    printPOSTimeSlot($result,$appointment_ids,$staff_id,$selected_date,$current_time,$appointment_slot_interval,0,$conflicting_start_time);
                    $current_time += $appointment_slot_interval ;
                    }
                    ?>
                </div>
            </div>
<?}

function AppointmentCalendar_Open_Slot_Book($staff_id,$selected_date,$selected_time,$appointment_slot_interval){
$general_dal                = new GENERAL_DAL();
$profiles_dal               = new Profiles_DAL();
$appointment_Get_LoginAvailibility_based_on_Open_Slot_Table_Info  = $general_dal->appointment_GetLoginAvailibility_based_on_Open_Slot_Table($staff_id,$selected_date,$selected_time,$appointment_slot_interval);
$div_onclick                = '';
$EmployeeData               = $profiles_dal->get_EmployeeDataPerLoginId($staff_id);
$count                      = count($appointment_Get_LoginAvailibility_based_on_Open_Slot_Table_Info);
$dateTimeStart_massaged     = substr_replace(sprintf('%04d',$selected_time ), ':', -2, 0);
    if ($count == 0){
            if ( $_SESSION['settings'][$_SESSION['settings']['login_id']]['level'] >= 2 || $_SESSION['settings']['login_id'] == $EmployeeData[0]->id ) {
                $img_html       = "<img src='../common_includes/includes/images/work.png' height='12' width='12' alt='Work' title='Mark Slot Off' class='mp'>";
                $div_onclick    = "onclick=AppointmentCalendar_Open_Slot_Book_Set_UnSet($staff_id,1,'$selected_date',$selected_time,$appointment_slot_interval)";
            }
        } else {
            $img_html       = "<img src='../common_includes/includes/images/Drink_BeerBottle.png' height='12' width='12' alt='Work' title='Mark Slot on' class='mp'>";
            if ( $_SESSION['settings'][$_SESSION['settings']['login_id']]['level'] >= 2 || $_SESSION['settings']['login_id'] == $EmployeeData[0]->id ) {
                $div_onclick    = "onclick=AppointmentCalendar_Open_Slot_Book_Set_UnSet($staff_id,0,'$selected_date',$selected_time,$appointment_slot_interval)";
            }
    }
?>
    <div class='d_InlineBlock wp95 hp08 t_align_center main_bc_color2 main_color2_text s09 bold'>Appointment Slot Edit</div>
    <div class='d_InlineBlock wp95 hp08 left  main_bc_color1_light main_color1_light_text no-overflow'>
            <div class="f_left s06 wp65 no-overflow left" >
                <?=$EmployeeData[0]->firstname?> <?=$EmployeeData[0]->lastname?>
            </div>
            <div class="f_left s06 wp25 no-overflow" >
                <?=date('g:i a', strtotime($dateTimeStart_massaged)) ?>
            </div>
            <div class='f_left wp10 hp100 right no-overflow' <?=$div_onclick?> id="Calendar_Employees_Open_Slot_Available_Icon_<?=$staff_id?>">
                <?=$img_html?>
            </div>
    </div>
<?}