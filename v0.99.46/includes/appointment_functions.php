<?php 
include_once('customers_functions.php');
include_once('companies_functions.php');
include_once('profiles_functions.php');
include_once('inventory_management_functions.php');
class Appointments_DAL {
  public function __construct(){}
  public function Appointments_displayAppointmentInfo($appointment_id){
        $sql ="SELECT 
        c.firstname     as first_name,
        c.surname       as last_name,
        c.phone_num,
        c.email, 
        l.id            as employee_login_id,
        l.firstname     as staff_first_name,
        l.lastname      as staff_last_name,
        l.email_address as staff_email_address,
        a.customer_id,
        a.company_id,
        a.status,
        a.ThirdPartyApptVendor,
        a.ThirdPartyApptEventId,
        a.startDate, 
        a.endDate

        from appointments a
        join customers c        on a.customer_id= c.id
        join logins l           on a.login_id   = l.id
        where a.id = $appointment_id";
    #print "$sql\n";
    return $this->query($sql);
  }
  public function Appointments_displayAppointmentServices_by_appointment_ID($appointment_id){
    $sql = "SELECT  apps_s.service_id, apps_s.employee_id, apps_s.service_price, apps_s.price_paid,
                    i.name as item_name, i.price, i.est_time_mins 
            from appointments_services apps_s left
            join items i on apps_s.service_id = i.id
            where appointment_id = $appointment_id";
    #print "$sql\n";
    return $this->query($sql);
  }
  public function Company_hours_by_ID_and_dayID($company_id,$address_id,$day_id){
    $sql = "SELECT hours_shown from company_hours 
            where company_id = $company_id and 
                  address_id = $address_id and 
                  day_id     = $day_id ;";
    #print "$sql\n";
    return $this->query($sql);
  }
  public function Appointments_CountActiveServicesWithEmployeePrice_by_company_id($company_id){
      $sql = "SELECT i.id,i.name,ls.employee_price,ls.login_id,ls.status 
                from items i 
                left join logins_services ls on ls.service_id = i.id 
                where i.type        = 2 and 
                ls.status           = 1 and 
                ls.employee_price  != 0 and 
                company_id          = $company_id 
                order by i.name";
    #print "$sql\n";
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

function appointments() { ?>
<div class="d_InlineBlock wp98 hp100">
    <div class="d_InlineBlock wp100 hp08 s09">
            <div class="wp30 hp100 f_left">
                <img src="/common_includes/includes/images/appointment.gif"  alt="Schedule an Appointment" >
            </div>
            <? if ( isset($_SESSION['appointment']['step1']) && $_SESSION['appointment']['step1'] == 1 ) {
                $Step1Class = "AppointmentHeaderActive main_color1_text";
                $content = "<a onclick=\"appointmentProcessStepOne();\" >Select Staff</a>";
            }
            elseif ( isset($_SESSION['appointment']['step1']) && $_SESSION['appointment']['step1'] == 2 ) {
                $Step1Class = "AppointmentHeaderVisited main_color3_text";
                $content = "<a onclick=\"appointmentProcessStepOne();\" >Select Staff</a>";
            }
            elseif ( !(isset($_SESSION['appointment']['step1'])) ) {
                $Step1Class = "AppointmentHeaderFuture main_color2_text";
                $content = "<a onclick=\"return false\" >Select Barber</a>";
            }?>
            <div class="wp15 hp100 f_left <?=$Step1Class?>">
                    <?=$content?>
            </div>

            <? if (isset($_SESSION['appointment']['step2']) && $_SESSION['appointment']['step2'] == 1 ) {
                $Step2Class = "AppointmentHeaderActive main_color1_text";
                $content = "<a onclick=\"appointmentProcessStepTwo('{$_SESSION['appointment']['staff_id']}');\" >Appointment Time</a>";
            }
            elseif ( isset($_SESSION['appointment']['step2']) && $_SESSION['appointment']['step2'] == 2 ) {
                $Step2Class = "AppointmentHeaderVisited main_color3_text";
                $content = "<a onclick=\"appointmentProcessStepTwo('{$_SESSION['appointment']['staff_id']}');\" >Appointment Time</a>";
            }
            elseif ( !(isset($_SESSION['appointment']['step2'])) ) {
                $Step2Class = "AppointmentHeaderFuture main_color2_text";
                $content = "<a onclick=\"return false\" >Appointment Time</a>";
            }?>
            <div class="wp20 hp100 f_left <?=$Step2Class?>">
                    <?=$content?>
            </div>

            <? if (isset($_SESSION['appointment']['step3']) && $_SESSION['appointment']['step3'] == 1 ) {
                $Step3Class = "AppointmentHeaderActive main_color1_text";
                $content = "<a onclick=\"appointmentProcessStepThree();\" >Login/Register</a>";
            }
            elseif ( isset($_SESSION['appointment']['step3']) && $_SESSION['appointment']['step3'] == 2 ) {
                $Step3Class = "AppointmentHeaderVisited main_color3_text";
                $content = "<a onclick=\"appointmentProcessStepThree();\" >Login/Register</a>";
            }
            elseif ( !(isset($_SESSION['appointment']['step3'])) ) {
                $Step3Class = "AppointmentHeaderFuture main_color2_text";
                $content = "<a onclick=\"return false\" >Login/Register</a>";
            }?>
            <div class="wp15 hp100 f_left <?=$Step3Class?>">
                    <?=$content?>
            </div>
            <? if (isset($_SESSION['appointment']['step4']) && $_SESSION['appointment']['step4'] == 1 ) {
                $Step4Class = "AppointmentHeaderActive main_color1_text";
                $content = "<a onclick=\"appointmentProcessStepFour();\" >Confirmation</a>";
            }
            elseif ( isset($_SESSION['appointment']['step4']) && $_SESSION['appointment']['step4'] == 2 ) {
                $Step4Class = "AppointmentHeaderVisited main_color3_text";
                $content = "<a onclick=\"appointmentProcessStepFour();\" >Confirmation</a>";
            }
            elseif ( !(isset($_SESSION['appointment']['step4'])) ) {
                $Step4Class = "AppointmentHeaderFuture main_color2_text";
                $content = "<a onclick=\"return false\" >Confirmation</a>";
            }?>
            <div class="wp15 hp100 f_left <?=$Step4Class?>">
                    <?=$content?>
            </div>
    </div>
        <?
        if ($_SESSION['appointment']['step'] == 1) {
            make_appointment_step1();
        }
        if ($_SESSION['appointment']['step'] == 2) {
            make_appointment_step2();
        }
        if ($_SESSION['appointment']['step'] == 3) {
            make_appointment_step3();
        }
        if ($_SESSION['appointment']['step'] == 4) {
            make_appointment_step4();
        }
        ?>
</div>
<?}
    function make_appointment_step1() {
    $general_dal        = new GENERAL_DAL();
    $inventory_dal      = new INVENTORY_DAL();
    $Appointments_dal   = new Appointments_DAL();

    if (isset($_SESSION['appointment_book']['total_time'])) { $total_service_time = $_SESSION['appointment_book']['total_time']; } else { $total_service_time = 0 ; }
    if (isset($_SESSION['appointment_book']['total_services_price'])) { $total_service_price = $_SESSION['appointment_book']['total_services_price']; }else { $total_service_price = 0.00 ; }
    $company_id                     = $_SESSION['settings']['company_id'];
    $employeePricesSet_or_not       = $Appointments_dal->Appointments_CountActiveServicesWithEmployeePrice_by_company_id($_SESSION['settings']['company_id']);

    if ( isset($_SESSION['appointment_book']['services_selected']) && count($_SESSION['appointment_book']['services_selected']) > 0 ) {
        $make_appointment_step1_choose_service          = 'hp50';
        $make_appointment_step1_choost_staff            = 'hp40';
    } else {
        $make_appointment_step1_choose_service          = 'hp90';
        $make_appointment_step1_choost_staff            = 'd_None'; 
    }
    
    if (   isset($_SESSION['appointment']['staff_id']) && count($_SESSION['appointment_book']['services_selected']) > 0  ||
           count($employeePricesSet_or_not) == 0 
       ) {
        $ChooseServices_total_services_price    = '';
    } else {
        $ChooseServices_total_services_price    = 'd_None';
    }
    ?>
    <div id="make_appointment_step1" class="d_InlineBlock wp100 hp90 main_bc_color2_light">
        <div class="d_InlineBlock wp98 hp98 mt5">
            <div class="d_InlineBlock wp100 hp10 main_bc_color2 main_color2_text">
                
                <div class="f_left left hp100 s11 vtop">
                    <img src="/common_includes/includes/images/number1.png" title="" width="33" height="33">
                </div>
                <div class="f_left left wp60 hp100 s13 vtop">
                    &nbsp;Choose Services:
                </div>  
                
                <div class="f_right wp15 hp100 no-overflow s11 pt10 <?=$ChooseServices_total_services_price?>" id="ChooseServices_total_services_price">
                    Value: $<?=$total_service_price?>
                </div>
                <div class="f_right wp15 hp100 no-overflow s11 pt10" id="ChooseServices_est_time_total">
                    <?=$total_service_time?> mins.
                </div>
            </div>
             
            <div id="make_appointment_step1_choose_service" class="f_left wp100 <?=$make_appointment_step1_choose_service?> scrolling ">
                <? make_appointment_step1_choose_service($inventory_dal);?>
            </div>
            
            <div id="make_appointment_step1_choost_staff"   class="f_left wp100 <?=$make_appointment_step1_choost_staff?> <?=$make_appointment_step1_choost_staff_display ?>" >
                <div class="d_InlineBlock wp100 hp02 main_bc_color2">
                    &nbsp;
                </div>
                <div class="d_InlineBlock wp100 hp98 main_bc_color2">
                    <div class="f_left wp70 hp100">
                        <div class="f_left left wp100 hp25 main_bc_color2 main_color2_text s13 vtop">
                            <div class="d_InlineBlock f_left left  hp100">
                                <img src="/common_includes/includes/images/number2.png" title="" width="33" height="33">
                            </div>
                            <div class="d_InlineBlock f_left left wp65 hp100">
                                &nbsp;Select Staff:
                            </div>
                            <div class="d_InlineBlock f_right center wp20 hp100 red mp">
                                &nbsp;
                                <? if (isset($_SESSION['appointment']['staff_id'])) {?>
                                <a onclick="appointmentProcessStepTwo(<?=$_SESSION['appointment']['staff_id']?>);">Continue</a>
                                <? } ?>
                            </div>
                        </div>
                        <div class="f_left bcgrey scrolling hp75 wp100" id="make_appointment_step1_profile_descriptions" >
                            <? make_appointment_choost_staff($general_dal,'step_1');?>
                        </div>
                    </div>
                    <div class="f_left wp30 hp100">
                        <?php make_appointment_step1_business_hours($company_id);?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <? }
        function make_appointment_step1_choose_service($inventory_dal)      {
            $Companies_dal                  = new Companies_DAL();
            $Appointments_dal               = new Appointments_DAL();
            $count                          = $total_service_count = $current_category_id = 0;
            $NewAppointment_Company_Message = $Companies_dal->get_TemplateTabData_by_Name('NewAppointment_Company_Message',$_SESSION['settings']['company_id']);
            $available_services             = $inventory_dal->ServiceManagement_AllActiveServices($_SESSION['settings']['company_id']);
            $chooseService_Class_static     =' wp100 hp90 s07 no-overflow';
            $chooseService_Class_ON         = 'black bclightgreen';
            $chooseService_Class_OFF        = 'black bcgrey';          
            if (count($available_services) > 0 ) {
            $employeePricesSet_or_not       = $Appointments_dal->Appointments_CountActiveServicesWithEmployeePrice_by_company_id($_SESSION['settings']['company_id']);
            foreach ($available_services as $service) {
                $chooseService_Class_static     =' wp90 hp90 s07 no-overflow';
                $chooseService_Class_ON = 'black bclightgreen';
                $chooseService_Class_OFF= 'black bcgrey';
                $service_price          = '';
                if (isset($_SESSION['appointment']['staff_id'])){
                    $Profiles_DAL   = new Profiles_DAL();
                    $employeeServiceStatusInfo = $Profiles_DAL->get_ServiceStatus_byLoginId($_SESSION['appointment']['staff_id'],$service->id);
                    if ( isset($employeeServiceStatusInfo) && $employeeServiceStatusInfo[0]->employee_price != 0 ) {
                        $service_price = money2($employeeServiceStatusInfo[0]->employee_price,0);
                    } else {
                        $service_price = money2($service->price,0);
                    }
                } else {
                        if ( count($employeePricesSet_or_not) > 0 ){
                            $service_price = '';
                        } else {
                            $service_price = money2($service->price,0);
                        }
                }
                if ( $current_category_id != $service->category_id) {
                        $current_category_id = $service->category_id;
                        $count =0;
                    ?>
                    <div class="f_left wp100 h25px mt10 main_color1_text main_bc_color1">
                        <div class='f_left wp30 hp100 left s08 bold no-overflow'><?=$service->category_name?>&nbsp;<font class="s06">Category.</font></div>
                        <div class='f_left wp40 hp100 left s06'>(click on the button of the service you want)<img src="/common_includes/includes/images/selectIcon.png" height="15" width="15" title="Click the icon that looks like this to make that service your selection."></div>
                        <div class='f_left wp30 hp100 s06'><?=$NewAppointment_Company_Message[0]->value?></div>
                    </div>
                    <? $service_row_div_class="f_left center wp100 h80px"; ?>
                    <div class="<?=$service_row_div_class?>">
                    <?
                }
                if (isset($_SESSION['appointment_book']['services_selected'][$service->id])) {$chooseService_Class  =$chooseService_Class_static . ' ' . $chooseService_Class_ON; } else { $chooseService_Class = $chooseService_Class_static . ' ' . $chooseService_Class_OFF;} ?>
                    <div class="d_InlineBlock f_left wp31 hp100">
                        <div class="d_InlineBlock box3-black wp100 hp100">
                            <div class="f_left wp100 hp35 main_color1_light_text">
                                <div class="f_left wp20 hp100 s09 left no-overflow"><?=$service_price?></div>
                                <div class="f_left wp15 hp100 s07 left no-overflow">&nbsp;</div>
                                <div class="f_left wp55 hp100 center no-overflow p2" onclick="appointmentProcessSelectService(<?=$service->id?>,'<?=$chooseService_Class_static?>','<?=$chooseService_Class_ON?>','<?=$chooseService_Class_OFF?>');">
                                        <input value="<?=$service->name?>" type="submit" id="ChooseService_<?=$service->id?>" class="<?=$chooseService_Class?>" title="Click to choose this service.">
                                </div>
                                <div class="f_right wp10 hp100 center no-overflow mp" onclick="appointmentProcessSelectService(<?=$service->id?>,'<?=$chooseService_Class_static?>','<?=$chooseService_Class_ON?>','<?=$chooseService_Class_OFF?>');">
                                    <img src="/common_includes/includes/images/selectIcon.png" height="22" width="22" title="Click to choose this service.">
                                </div>
                            </div>
                            <div class="f_left wp100 hp65 s08 bclightgray text_OverFlow_ellipsis scrolling main_color1_light_text"  title="<?=$service->style?>">
                                ~ <?=$service->est_time_mins?> minutes. - <?=$service->style?>
                            </div>
                        </div>
                    </div>
                    <div class="d_InlineBlock f_left wp02 hp100">
                        &nbsp;
                    </div>
                    <?
                    $count++; 
                    if ($count == 3 ) { $count =0; ?></div><div class="<?=$service_row_div_class?>">&nbsp;<?}?>
                <?
                $total_service_count++;
                } ?>
                    </div>
            </div>
            <?} else { ?>
                    <div class="f_left left wp90 hp100 no-overflow">
                        There are not any active services to choose from at this time.
                    </div>
            <?} ?>
        <?php }
        function make_appointment_choost_staff($general_dal)          {
            $count      = 0;
            $open_div   = "<div class='d_Table wp100 h50px mt5'>";
            $available_profiles = $general_dal->get_AllEmployeesPerCompanyId($_SESSION['settings']['company_id'],1,1);
            if (count($available_profiles)> 0 ) {
                ?> <?=$open_div?> <?
                foreach($available_profiles as $profile_data) {
                    if (isset($_SESSION['appointment']['step2']) && $_SESSION['appointment']['step2'] == 1 && $profile_data->id == $_SESSION['appointment']['staff_id']) { continue; }
                    make_appointment_profile_description($profile_data->id,$count);
                    $count++;
                    if ( ($count % 3) === 0) { ?> </div> <?=$open_div ?> <? }
                }
                ?></div><?                
            } else {?>
                <div class="f_left wp90 hp70 no-overflow">
                    <div>There are not any active staff members at this time.</div>
                </div>
            <? }
        }
            function make_appointment_profile_description($login_id,$count)  {
            $profiles_dal   = new Profiles_DAL();
            $title          = "";
            $profile_data   = $profiles_dal->get_EmployeeDataPerId($login_id);
            $Staff_name     = $profile_data[0]->firstname . " " . $profile_data[0]->lastname ;
            if (strlen($profile_data[0]->employee_quote) ==0 )  { $Staff_desc     = "Select a service above then choose me to book an appointment."; }
            else                                                { $Staff_desc     = $profile_data[0]->employee_quote; }
            $disabled_or_not= ""; $bgcolor = "bclightgray"; $strikethrough = "";
            if (isset($_SESSION['appointment_book']) && isset($_SESSION['appointment_book']['total_time']) && $_SESSION['appointment_book']['total_time'] > 0) {
                foreach($_SESSION['appointment_book']['services_selected'] as $service_id) {
                    $serviceAvailablebyLogin_id = $profiles_dal->get_ServiceIDStatus_byLogin_id($login_id,$service_id);
                    if (count($serviceAvailablebyLogin_id) == 0 ) {
                        $disabled_or_not = "disabled"; 
                        $bgcolor = 'bcdarkgray'; 
                        $strikethrough = "strikethrough"; 
                        $title="This barber/stylist($Staff_name) does not perform one of the services you have selected.";
                        if (isset($_SESSION['appointment']['staff_id']) && $_SESSION['appointment']['staff_id'] == $login_id) { unset($_SESSION['appointment']['staff_id']);}
                    }
                }
            } else {
                    $disabled_or_not = "disabled"; $bgcolor = 'bcgray'; $strikethrough = "strikethrough" ; $title="Until a serivce is choosen above, you cannot select a barber/stylist.";
            }
            if ( $_SESSION['appointment']['step1'] == 1 || ($_SESSION['appointment']['step2'] == 1 && $disabled_or_not != "disabled") ) {
                make_appointment_profile_description_html($bgcolor,$title,$login_id,$disabled_or_not,$strikethrough,$Staff_name,$Staff_desc,$count);
            }
            }
                function make_appointment_profile_description_html($bgcolor,$title,$login_id,$disabled_or_not,$strikethrough,$Staff_name,$Staff_desc){
                    $profiles_dal = new Profiles_DAL();
                    $image_id_data = $profiles_dal->get_default_Profile_ImageID($login_id);
                    ?>
                    <? if (isset($_SESSION['appointment']['staff_id']) && $_SESSION['appointment']['staff_id'] == $login_id) { $checked = 'checked'; $bold = 'bold s07'; $bgcolor = 'bcgray'; } else { $checked = ''; $bold = '';} ?>
                        <?if ($_SESSION['appointment']['step1'] == 1) {?>
                            <? if (isset($image_id_data) && count($image_id_data) > 0) { ?>
                                <div class="d_InlineBlock wp30 hp100 <?=$bgcolor?>">
                                    <div class="f_left wp10 hp100 " title="<?=$title?>">
                                        <input type="radio" name="appointBook_Staff_Selection" value="<?=$login_id?>" <?=$disabled_or_not?> <?=$checked?> onclick="appointmentProcessStepOne(<?=$login_id?>);">
                                        <? if (isset($_SESSION['appointment']['staff_id']) && $_SESSION['appointment']['staff_id'] == $login_id) {?>                                
                                            <a onclick="appointmentProcessStepTwo(<?=$_SESSION['appointment']['staff_id']?>);" class='mp red'> GO </a>
                                        <? } ?>                            
                                    </div>
                                    <div class="f_left left wp50 hp100 center s08 no-overflow main_color1_light_text <?=$strikethrough?> <?=$bold?>" title="<?=$title?>">
                                        &nbsp; <?=$Staff_name?> 
                                    </div>
                                    <div class="f_left wp40 hp100 f_left">
                                        &nbsp;<img src="/pos/showimage.php?id=<?=$image_id_data[0]->image_id?>&image_db_id=<?=$image_id_data[0]->image_db_id?>&w=60&h=50">
                                    </div>
                                </div>
                            <? } else { ?>
                                <div class="d_InlineBlock wp30 hp100 <?=$bgcolor?>">
                                    <div class="f_left wp10 hp100 " title="<?=$title?>">
                                        <input type="radio" name="appointBook_Staff_Selection" value="<?=$login_id?>" <?=$disabled_or_not?> <?=$checked?> onclick="appointmentProcessStepOne(<?=$login_id?>);">
                                        <? if (isset($_SESSION['appointment']['staff_id']) && $_SESSION['appointment']['staff_id'] == $login_id) {?>                                
                                            <a onclick="appointmentProcessStepTwo(<?=$_SESSION['appointment']['staff_id']?>);" class='mp red'> GO </a>
                                        <? } ?>                            
                                    </div>
                                    <div class="f_left center wp90 hp100  s08 no-overflow main_color1_light_text <?=$strikethrough?> <?=$bold?>" title="<?=$title?>">
                                        &nbsp; <?=$Staff_name?> 
                                    </div>
                                </div>
                            <? } ?>
                        <? } else { ?> <!-- Step2 Quick Switch -->
                                <div class="d_InlineBlock wp30 hp100 <?=$bgcolor?>">
                                    <div class="f_left wp100 hp85 f_left scrolling">
                                        <div class="f_left wp20 hp100 f_left center no-overflow " title="<?=$title?>">
                                            <input type="radio" name="appointBook_Staff_Selection" value="<?=$login_id?>" <?=$disabled_or_not?> onclick="appointmentProcessStepTwo(<?=$login_id?>);">
                                        </div>
                                        <div class="f_left wp80 hp100 f_left center s07 main_color1_light_text no-overflow <?=$strikethrough?>" title="<?=$title?>">
                                            <?=$Staff_name?>
                                        </div>
                                    </div>
                                </div>
                        <? } ?>
                <?}
        function make_appointment_step1_business_hours($company_id)         {
            $Appointments_DAL   = new Appointments_DAL();
            $Day_hours_data_0   = $Appointments_DAL->Company_hours_by_ID_and_dayID($company_id,101,0);
            $Day_hours_data_1   = $Appointments_DAL->Company_hours_by_ID_and_dayID($company_id,101,1);
            $Day_hours_data_2   = $Appointments_DAL->Company_hours_by_ID_and_dayID($company_id,101,2);
            $Day_hours_data_3   = $Appointments_DAL->Company_hours_by_ID_and_dayID($company_id,101,3);
            $Day_hours_data_4   = $Appointments_DAL->Company_hours_by_ID_and_dayID($company_id,101,4);
            $Day_hours_data_5   = $Appointments_DAL->Company_hours_by_ID_and_dayID($company_id,101,5);
            $Day_hours_data_6   = $Appointments_DAL->Company_hours_by_ID_and_dayID($company_id,101,6);
            if (count($Day_hours_data_0)>0) { $Day_hours_0 = $Day_hours_data_0[0]->hours_shown ; } else { $Day_hours_0 = "* CLOSED *" ;         }
            if (count($Day_hours_data_1)>0) { $Day_hours_1 = $Day_hours_data_1[0]->hours_shown ; } else { $Day_hours_1 = "8:00AM - 8:00PM" ;}
            if (count($Day_hours_data_2)>0) { $Day_hours_2 = $Day_hours_data_2[0]->hours_shown ; } else { $Day_hours_2 = "8:00AM - 8:00PM" ;}
            if (count($Day_hours_data_3)>0) { $Day_hours_3 = $Day_hours_data_3[0]->hours_shown ; } else { $Day_hours_3 = "8:00AM - 8:00PM" ;}
            if (count($Day_hours_data_4)>0) { $Day_hours_4 = $Day_hours_data_4[0]->hours_shown ; } else { $Day_hours_4 = "8:00AM - 8:00PM" ;}
            if (count($Day_hours_data_5)>0) { $Day_hours_5 = $Day_hours_data_5[0]->hours_shown ; } else { $Day_hours_5 = "8:00AM - 8:00PM" ;}
            if (count($Day_hours_data_6)>0) { $Day_hours_6 = $Day_hours_data_6[0]->hours_shown ; } else { $Day_hours_6 = "8:00AM - 4:00PM" ;}
            ?>
        <div class="wp100 hp100 d_InlineBlock s07">
            <div class="f_left wp100 hp10 ">
                <div CLASS="s15 wp100 hp100 f_left main_bc_color1 main_color1_text center bold">Business Hours</div>
            </div>
            <div class="f_left wp100 hp10">
                <div CLASS="wp35 hp100 f_left center main_bc_color1_light main_color1_light_text">Monday</div>
                <div CLASS="wp65 hp100 f_right main_bc_color2 main_color2_text f_left center no-overflow"><?=$Day_hours_1?></div>
            </div>
            <div class="f_left wp100 hp11">
                <div CLASS="wp35 hp100 f_left center main_bc_color1_light main_color1_light_text">Tuesday</div>
                <div CLASS="wp65 hp100 f_right main_bc_color2 main_color2_text f_left center no-overflow"><?=$Day_hours_2?></div>
            </div>
            <div class="f_left wp100 hp11">
                <div CLASS="wp35 hp100 f_left center main_bc_color1_light main_color1_light_text">Wednesday</div>
                <div CLASS="wp65 hp100 f_right main_bc_color2 main_color2_text f_left center no-overflow"><?=$Day_hours_3?></div>
            </div>
            <div class="f_left wp100 hp11">
                <div CLASS="wp35 hp100 f_left center main_bc_color1_light main_color1_light_text">Thursday</div>
                <div CLASS="wp65 hp100 f_right main_bc_color2 main_color2_text f_left center no-overflow"><?=$Day_hours_4?></div>
            </div>
            <div class="f_left wp100 hp11">
                <div CLASS="wp35 hp100 f_left center main_bc_color1_light main_color1_light_text">Friday</div>
                <div CLASS="wp65 hp100 f_right main_bc_color2 main_color2_text f_left center no-overflow"><?=$Day_hours_5?></div>
            </div>
            <div class="f_left wp100 hp11">
                <div CLASS="wp35 hp100 f_left center main_bc_color1_light main_color1_light_text">Saturday</div>
                <div CLASS="wp65 hp100 f_right main_bc_color2 main_color2_text f_left center no-overflow"><?=$Day_hours_6?></div>
            </div>
            <div class="f_left wp100 hp10">
                <div CLASS="wp35 hp100 f_left center main_bc_color1_light main_color1_light_text">Sunday</div>
                <div CLASS="wp65 hp100 f_right main_bc_color2 main_color2_text f_left center no-overflow"><?=$Day_hours_0?></div>
            </div>           
        </div>
        <? }
        function appointments_calculate_total_service_price_by_login_id(){
            $profiles_dal               = new Profiles_DAL(); 
            $Inventory_DAL              = new INVENTORY_DAL;

            $_SESSION['appointment_book']['total_services_price']   = 0; 
            foreach ($_SESSION['appointment_book']['services_selected'] as $service_id) {
                $get_ServiceStatus_byLoginId    = $profiles_dal->get_ServiceStatus_byLoginId($_SESSION['appointment']['staff_id'],$service_id);
                $service_data                   = $Inventory_DAL->ServiceManagement_ServicesProperties($service_id);
                if ($get_ServiceStatus_byLoginId[0]->employee_price != 0) {
                    $_SESSION['appointment_book']['total_services_price'] += intval($get_ServiceStatus_byLoginId[0]->employee_price);
                } else {
                    $_SESSION['appointment_book']['total_services_price'] += intval($service_data[0]->price);;       
                }
            }
            return $_SESSION['appointment_book']['total_services_price'];
        }
                
    function make_appointment_step2() {
        $general_dal                = new GENERAL_DAL();
        $PreferenceData             = $general_dal->get_CompanyPreference_by_Company_ID($_SESSION['settings']['company_id'],'timezone');
        date_default_timezone_set($PreferenceData[0]->value);
        $intNow = mktime();
        if( !isset($year) ) { $date=date("Y-m-d H:i:s",$intNow); $year = date('Y',$intNow); }
    ?>
    <div id="make_appointment_step2" class="d_InlineBlock wp100 hp90 main_bc_color2_light">
        <div class="d_InlineBlock main_bc_color2_light main_color2_light_text wp100 hp100">
            <div class="wp60 hp98 m5 f_left  main_bc_color1 main_color1_text">
                <div class="mt5 hp98 wp100">
                    <?php make_appointment_step2_select_date()?>
                </div>
            </div>
            <div class="wp01 f_left">
                <div class="hp100 wp100">
                    &nbsp;
                </div>
            </div>
            <div class="wp35 hp98 m5 f_right main_bc_color1 main_color1_text">
                <div class="mt5 hp98 wp100" id="make_appointment_step2_choose_apt" >
                    <?php make_appointment_step2_choose_apt();?>
                </div>
            </div> 
       </div>
    </div> 
<?}
        function appt_show_appt_info($appointment_id)            {
            $Appointments_DAL = new Appointments_DAL();
            $appointment_data           = $Appointments_DAL->Appointments_displayAppointmentInfo($appointment_id);
            $Appointment_Service_Data   = $Appointments_DAL->Appointments_displayAppointmentServices_by_appointment_ID($appointment_id);
            $service_ids = array();
            foreach($Appointment_Service_Data as $Appointment_Service) { array_push($service_ids, $Appointment_Service->service_id); }
            $html = "<div   class='f_left wp100 hp30 mp right no-overflow' 
                            onclick='AppointmentCalendar_Display_ShowAppointment_info(" . $appointment_id . ",\"Show_Upcoming_Appts_Calendar_View\")'>";
            $html .= ucfirst($appointment_data[0]->first_name) . " " . ucfirst($appointment_data[0]->last_name);
            $html .= "</div>";
            $html .= "<div class='f_left wp100 hp70 right scrolling'>" ;
            $count = 1 ;
            foreach($Appointment_Service_Data as $Appointment_Service) {
                $html  .=  "<div class='f_left wp100 s07 right no-overflow'>" . $count . ". " . $Appointment_Service->item_name . "</div>";
                $count++;
            }
            $html .= "</div>";
            
            return  $html;
        } 
        function make_appointment_step2_select_date()           {
        if (!(isset($arg_month))) { $arg_month = 0; }
        if (!(isset($arg_year)))  { $arg_year  = 0; }
        ?>
        <div CLASS="wp98 hp100 d_InlineBlock main_bc_color1_light main_color1_light_text">
            <div class="f_left wp100 hp10 left vtop">
                <img src="/common_includes/includes/images/number1.png" title="" width="25" height="25">Staff Selected: <font class='bold'><?=$_SESSION['appointment']['staff_firstname'] . ' ' . $_SESSION['appointment']['staff_surname']?></font>
            </div>
            <div class="f_left wp100 hp90" id="calendar" >
                <? show_calendar($arg_year,$arg_month)?>
            </div>
        </div>
        <?php }
        function make_appointment_step2_choose_month($new_year) {
        $general_dal                = new GENERAL_DAL();
        $PreferenceData             = $general_dal->get_CompanyPreference_by_Company_ID($_SESSION['settings']['company_id'],'timezone');
        date_default_timezone_set($PreferenceData[0]->value);
        $intNow = mktime();

        if( !($new_year) ) {
            $date=mktime();
            $new_year = date('Y', $intNow);
        }
        $last_year = $new_year - 1;
        $next_year = $new_year + 1;
        $month_class_default    ="f_left wp33 hp100 mp CENTER LABEL";
        $month_class_mouseover  ="f_left wp33 hp100 mp CENTER INPUT";
        ?>
        <div class="wp95 hp100 d_InlineBlock main_bc_color1 main_color1_light_text">
            <div class="main_bc_color2 main_color2_text s13 wp100 hp20">
                <? if ( $last_year < date("Y",$intNow) ) {?>
                    <?=$last_year?>
                <?} else {?>
                    <a class="mp" onclick='changeCalendarYearWidget(<?=$last_year?>);'><?=$last_year?></a>
                <?}?>
                    -
                    <a class="mp" onclick='changeCalendarYearWidget(<?=$next_year?>);'><?=$next_year?></a>
            </div>
            <div class="wp100 hp20 d_InlineBlock">
                <div class="<?=$month_class_default?> " onmouseover="this.className='<?=$month_class_mouseover?>';" onmouseout="this.className='<?=$month_class_default?>';" onclick="show_calendar('<?=$new_year?>','01');">Jan</div>
                <div class="<?=$month_class_default?> " onmouseover="this.className='<?=$month_class_mouseover?>';" onmouseout="this.className='<?=$month_class_default?>';" onclick="show_calendar('<?=$new_year?>','02');">Feb</div>
                <div class="<?=$month_class_default?> " onmouseover="this.className='<?=$month_class_mouseover?>';" onmouseout="this.className='<?=$month_class_default?>';" onclick="show_calendar('<?=$new_year?>','03');">Mar</div>
            </div>
            <div class="wp100 hp20 d_InlineBlock">
                <div class="<?=$month_class_default?> " onmouseover="this.className='<?=$month_class_mouseover?>';" onmouseout="this.className='<?=$month_class_default?>';" onclick="show_calendar('<?=$new_year?>','04');">Apr</div>
                <div class="<?=$month_class_default?> " onmouseover="this.className='<?=$month_class_mouseover?>';" onmouseout="this.className='<?=$month_class_default?>';" onclick="show_calendar('<?=$new_year?>','05');">May</div>
                <div class="<?=$month_class_default?> " onmouseover="this.className='<?=$month_class_mouseover?>';" onmouseout="this.className='<?=$month_class_default?>';" onclick="show_calendar('<?=$new_year?>','06');">June</div>
            </div>
            <div class="wp100 hp20 d_InlineBlock">
                <div class="<?=$month_class_default?> " onmouseover="this.className='<?=$month_class_mouseover?>';" onmouseout="this.className='<?=$month_class_default?>';" onclick="show_calendar('<?=$new_year?>','07');">July</div>
                <div class="<?=$month_class_default?> " onmouseover="this.className='<?=$month_class_mouseover?>';" onmouseout="this.className='<?=$month_class_default?>';" onclick="show_calendar('<?=$new_year?>','08');">Aug</div>
                <div class="<?=$month_class_default?> " onmouseover="this.className='<?=$month_class_mouseover?>';" onmouseout="this.className='<?=$month_class_default?>';" onclick="show_calendar('<?=$new_year?>','09');">Sep</div>
            </div>
            <div class="wp100 hp20 d_InlineBlock">
                <div class="<?=$month_class_default?> " onmouseover="this.className='<?=$month_class_mouseover?>';" onmouseout="this.className='<?=$month_class_default?>';" onclick="show_calendar('<?=$new_year?>','10');">Oct</div>
                <div class="<?=$month_class_default?> " onmouseover="this.className='<?=$month_class_mouseover?>';" onmouseout="this.className='<?=$month_class_default?>';" onclick="show_calendar('<?=$new_year?>','11');">Nov</div>
                <div class="<?=$month_class_default?> " onmouseover="this.className='<?=$month_class_mouseover?>';" onmouseout="this.className='<?=$month_class_default?>';" onclick="show_calendar('<?=$new_year?>','12');">Dec</div>
            </div>
        </div>
        <? }
        function make_appointment_step2_choose_apt()            {
            $general_dal                = new GENERAL_DAL();
            $staff_id                   = $_SESSION['appointment']['staff_id'];
            $total_time_of_services     = $_SESSION['appointment_book']['total_time'] ;
            if ( !(isset($_SESSION['appointment']['selected_date']))) {
                $_SESSION['appointment']['selected_date']   = date("Y-m-d");
                $selected_date                              = date("Y-m-d");
            }
            else {
                $selected_date = $_SESSION['appointment']['selected_date'] ;
            }
        $appointment_Get_LoginAvailibility_based_on_DaysOff_Table_Info = $general_dal->appointment_Get_LoginAvailibility_based_on_DaysOff_Table($staff_id,$selected_date);
        if ( count($appointment_Get_LoginAvailibility_based_on_DaysOff_Table_Info) == 1) { $day_off = 1 ; } else { $day_off = 0 ;}

        $appointment_slot_interval_data = $general_dal->get_CompanyPreference_by_Company_ID($_SESSION['settings']['company_id'],'appointment_slot_interval');
        $appointment_slot_interval      = ( count($appointment_slot_interval_data)==0 ) ? 30 : $appointment_slot_interval_data[0]->value;
        $width_percent                  = sprintf('%02d',floor($appointment_slot_interval/60 *80));

        $Available_Appointments_array   = SetSessionsOfAvailable_APPTS($staff_id,$selected_date,$appointment_slot_interval);
        $AvailableApptsRowClasses       = "main_bc_color2 wp100 h15px d_InlineBlock s06 main_color1_light_text center";
        $left_column_class              = "f_left right wp14 hp100 s09 main_color2_text no-overflow";
        ##############
        $b4_appt_buffer_time            = 90 ;
        $compact                        = 0 ;
        ############## 
        ?>
        <div class="wp95 hp75 d_InlineBlock main_bc_color2 main_color2_text">
            <div class="wp98 h25px d_InlineBlock mt2">
                <div class="s10 wp100 bold left d_InlineBlock center hp100 main_bc_color1 main_color1_text">
                    <div class="f_left wp10 hp100"><img src="/common_includes/includes/images/number2.png" title="" width="25" height="25"></div>
                    <div class="f_left wp90 hp100"><?=date("D M j Y",  strtotime($selected_date))?></div>
                </div>
            </div>
            <? if ($compact == 1) { ?>
                <div class="wp100 h15px d_InlineBlock s06 main_color1_text main_bc_color1 center">
                    <div CLASS="f_left hp100 wp14" ><?=$total_time_of_services?></div>
                    <?$current_time = $hour = 0 ;
                    while ($current_time < ($hour+60)) {?>
                        <div CLASS="d_InlineBlock hp100 main_color1_text wp<?=$width_percent?>" >:<?=sprintf('%02d',$current_time)?></div>
                    <? $current_time += $appointment_slot_interval; } ?>
                </div>
            <?}?>
            <? $hour = 700; while ($hour <= 2100) { ?>
                <div class="<?=$AvailableApptsRowClasses?>">
                    <div CLASS="<?=$left_column_class?>" ><?=date('g A', strtotime(substr_replace(sprintf('%04d',$hour ), ':', -2, 0)))?></div>
                    <?
                    $current_time = $hour ;
                    while ($current_time < ($hour+60)) {
                    list($result,$conflicting_start_time,$appointment_ids) = checkEmployeeAvailibilityByTimeSlot($staff_id,$Available_Appointments_array,$selected_date,$current_time,$appointment_slot_interval,$total_time_of_services,$b4_appt_buffer_time,$day_off);
                    printAppointmentTimeSlot($result,$appointment_ids,$staff_id,$selected_date,$current_time,$appointment_slot_interval,$total_time_of_services,$conflicting_start_time);
                    $current_time += $appointment_slot_interval ;
                    }
                    ?>
                </div>
            <? $hour += 100; } ?>
        </div>
        <div class="wp95 hp25 d_InlineBlock main_bc_color2 main_color2_text">
            <div class="wp98 hp15 d_InlineBlock ">
                <div class="s10 wp100  f_left hp100 main_bc_color1 main_color1_text s06">
                    <div class="wp30 f_left left  no-overflow">
                        Change Staff?
                    </div>
                    <div class="wp70 f_left right no-overflow">
                        <?=$_SESSION['appointment_book']['total_time'] ?> mins of services with <?=$_SESSION['appointment']['staff_firstname'] . ' ' . $_SESSION['appointment']['staff_surname']?>
                    </div>
                </div>
            </div>
            <div class="wp98  hp85 d_InlineBlock scrolling-y">
                <? make_appointment_choost_staff($general_dal);?>
            </div>
        </div>
        <? }
            function checkEmployeeAvailibilityByTimeSlot($staff_id,$Available_Appointments_array,$selected_date,$selected_time,$appointment_slot_interval,$total_time_of_services,$min_b4_appt_cutoff,$day_off,$app_book_count=-1) {
    if (!isset($Available_Appointments_array[$selected_time])) { $Available_Appointments_array[$selected_time] = NULL; }
    $general_dal    = new GENERAL_DAL();

    $PreferenceData = $general_dal->get_CompanyPreference_by_Company_ID($_SESSION['settings']['company_id'],'timezone');
    date_default_timezone_set($PreferenceData[0]->value);
    $day = date('D', strtotime($selected_date));
    $appointment_ids = array();
    $appointment_slot_interval--;
    $total_time_of_services--;
    $inTwoHours             = date("H:i", strtotime("+".$min_b4_appt_cutoff." mins"));
    $dateTimeStart_massaged = substr_replace(sprintf('%04d',$selected_time ), ':', -2, 0);

    $appointment_check_StartDate= $selected_date . " " . substr_replace($selected_time,    ':', -2, 0);
    $appointment_check_EndDate  = $selected_date . " " . substr_replace($selected_time+$appointment_slot_interval, ':', -2, 0);
    $when_appt_would_end        = date('H:i', strtotime("+$total_time_of_services minutes", strtotime(sprintf('%04d',$selected_time ))));
    $timeslot_appt_would_end    = appt_end_timeslot($appointment_slot_interval,$when_appt_would_end);
    #########################################
        $EnoughTimeResults          = $general_dal->appointment_EnoughTimeResults($staff_id,         $appointment_check_StartDate,$selected_date,$when_appt_would_end);
            $when_appt_would_end_check  = count($EnoughTimeResults);
        
        if(is_array($EnoughTimeResults) && count($EnoughTimeResults) >= 1 ) { $conflicting_start_time = $EnoughTimeResults[0]->startDate; } else { $conflicting_start_time = 0 ; }
        
        $ApptResults                = $general_dal->appointment_checkApptAvailable($staff_id,        $appointment_check_StartDate,$appointment_check_EndDate);
            $appt_count_from_check      = count($ApptResults);

        $appointment_CIAstartB      = $general_dal->appointment_Check_if_Appt_Start_Booked($staff_id,$appointment_check_StartDate,$appointment_check_EndDate);
            $appointment_CIAstartB_check= count($appointment_CIAstartB);
        $appointment_CIAspanB       = $general_dal->appointment_Check_if_Appt_Span_Booked($staff_id, $appointment_check_StartDate,$appointment_check_EndDate);
            $appointment_CIAspanB_check = count($appointment_CIAspanB);
        $appointment_CIAendB        = $general_dal->appointment_Check_if_Appt_End_Booked($staff_id,  $appointment_check_StartDate,$appointment_check_EndDate);
            $appointment_CIAendB_check  = count($appointment_CIAendB);

        $get_LoginAvailibility_based_on_Open_Slot_TableInfo  = $general_dal->appointment_GetLoginAvailibility_based_on_Open_Slot_Table($staff_id,$selected_date,$selected_time,                         $appointment_slot_interval+1);
        $get_LoginAvailibility_based_on_Off_table_and_EndTime= $general_dal->get_LoginAvailibility_based_on_Off_table_and_EndTime(     $staff_id,$selected_date,$selected_time,$timeslot_appt_would_end,$appointment_slot_interval+1);
    #########################################
        if (isset($appointment_CIAstartB) && is_array($appointment_CIAstartB)) { 
            foreach ($appointment_CIAstartB as $appointment) { array_push($appointment_ids, $appointment->id); } }
        if (isset($appointment_CIAspanB) && is_array($appointment_CIAspanB)) {
            foreach ($appointment_CIAspanB  as $appointment) { array_push($appointment_ids, $appointment->id); } }
        if (isset($appointment_CIAendB) && is_array($appointment_CIAendB)) {
            foreach ($appointment_CIAendB   as $appointment) { array_push($appointment_ids, $appointment->id); } }
        $appointment_ids = array_unique($appointment_ids);
        if ( !(in_array($selected_time, $_SESSION[$staff_id][$day])) 
           )     { $result = 1;} ###  Not Available
        elseif ( $day_off == 1 ) 
                 { $result = 2; } ###  Day Off
        elseif ( (isset($get_LoginAvailibility_based_on_Open_Slot_TableInfo) && 
                  count($get_LoginAvailibility_based_on_Open_Slot_TableInfo) == 1) 
               ) { $result = 2.5; } ###  Slot Marked Off
        elseif ( (isset($get_LoginAvailibility_based_on_Off_table_and_EndTime) && 
                  count($get_LoginAvailibility_based_on_Off_table_and_EndTime) == 1) 
               ) { $result = 2.6; } ###  When appt would end slot is Marked Off
        elseif (
                        ( isset($Available_Appointments_array) && $Available_Appointments_array[$selected_time] != 1 ) ||
                        ( $appt_count_from_check > 0 ) ||
                        ( $appointment_CIAstartB_check > 0 ) ||
                        ( $appointment_CIAspanB_check > 0 ) ||
                        ( $appointment_CIAendB_check > 0 )
               ) { $result = 3; } ### Booked  
        elseif ( 
                ((date("Y-m-d") == $selected_date) && ($inTwoHours  > $dateTimeStart_massaged)) || $selected_date < date("Y-m-d")
               ) { $result = 4; } ###  Expired 
        elseif ( (isset($Available_Appointments_array) && $Available_Appointments_array[$selected_time] != 1) || 
                        $when_appt_would_end_check > 0 
               ) { $result = 5; } ###  Your Appt Would end at
        elseif ( !(in_array($timeslot_appt_would_end, $_SESSION[$staff_id][$day])) 
           )     { $result = 5.1; } ###  Your Appt would end in an unavailable timeslot(not in the available appts table, or it is marked off)
        elseif (1) 
                 { $result = 6; } ###  Open Appointment
        return array($result,$conflicting_start_time,$appointment_ids);
    }
                function printAppointmentTimeSlot($result,$appointment_ids,$staff_id,$selected_date,$selected_time,$appointment_slot_interval,$total_time_of_services,$conflicting_start_time){
                $dateTimeStart_massaged     = substr_replace(sprintf('%04d',$selected_time ), ':', -2, 0);
                $width_percent              = sprintf('%02d',floor($appointment_slot_interval/60 *80));
                $when_appt_would_end        = date('g:i a', strtotime("+$total_time_of_services minutes", strtotime($dateTimeStart_massaged)));
                $appt_class_defs            = " d_InlineBlock f_left no-overflow hp100 box1-black wp" . $width_percent;
                $appt_class_defs_mouseover  = " d_InlineBlock f_left no-overflow hp100 box1-black greenyellow wp" . $width_percent;
                $data_div_css               = " hp90 box1-black no-overflow ";
            if ( $result == 1) { ?>
                <div id=ChooseApttime_<?=$selected_time?> CLASS="<?=$appt_class_defs?> ChooseApt_NotAvailable"              title="Not Available"><div class="<?=$data_div_css?>"></div></div>
            <? } elseif ( $result == 2 || $result == 2.5 ) {  ?>
                <div id=ChooseApttime_<?=$selected_time?> CLASS="<?=$appt_class_defs?> main_bc_color1 main_color1_text"     title="Off"><div class="<?=$data_div_css?>">Off</div></div>
            <? } elseif ( $result == 2.6 ) {  ?>
                <div id=ChooseApttime_<?=$selected_time?> CLASS="<?=$appt_class_defs?> main_bc_color1 main_color1_text"     title="Booking in this timeslot will conflict with the employee's break."><div class="<?=$data_div_css?>">Off</div></div>
            <? } elseif ( $result == 3 ) {  ?>
                <div id=ChooseApttime_<?=$selected_time?> CLASS="<?=$appt_class_defs?> main_bc_color1 main_color1_text"     title="Booked"><div class="<?=$data_div_css?>">Booked</div></div>
            <? } elseif ( $result == 4 ) {  ?>
                <div id=ChooseApttime_<?=$selected_time?> CLASS="<?=$appt_class_defs?> ChooseApt_EXPIRED"                   title="Expired"><div class="<?=$data_div_css?>">Expired</div></div>
            <? } elseif ( $result == 5 ) {  ?>
                <div id=ChooseApttime_<?=$selected_time?> CLASS="<?=$appt_class_defs?> ChooseApt_NotAvailable"              title="Your appointment would end at <?=$when_appt_would_end?>, this is not enough time. Conflicts with an Appt starting at <?=date('g:i a', strtotime($conflicting_start_time))?>."><div class="<?=$data_div_css?>">&nbsp;</div></div>
            <? } elseif ( $result == 5.1 ) {  ?>
                <div id=ChooseApttime_<?=$selected_time?> CLASS="<?=$appt_class_defs?> ChooseApt_NotAvailable"              title="This slot is open and available, however your Appointment would end at <?=$when_appt_would_end?>, this is not enough time. Conflicts with an unavailable time slot.">  <div class="<?=$data_div_css?>">&nbsp;</div></div>
            <? } elseif ( $result == 6 ) {  ?>
                <div id=ChooseApttime_<?=$selected_time?> CLASS="<?=$appt_class_defs?> ChooseApt_MouseOut"                  title="Open appointment slot, your appoinment will end at <?=$when_appt_would_end?>."
                                                                                                                            onclick="appointmentProcessStepThree(<?=$selected_time;?>);"
                                                                                                                            onmouseover="this.className='<?=$appt_class_defs_mouseover?>    ChooseApt_MouseOver';"
                                                                                                                            onmouseout ="this.className='<?=$appt_class_defs?>              ChooseApt_MouseOut';">
                    <div class="<?=$data_div_css?>" ><?=date('g:i a', strtotime($dateTimeStart_massaged)) ?></div>
                </div>
            <?}
        }
                function printPOSTimeSlot        ($result,$appointment_ids,$staff_id,$selected_date,$selected_time,$appointment_slot_interval,$total_time_of_services,$conflicting_start_time){
                $dateTimeStart_massaged     = substr_replace(sprintf('%04d',$selected_time ), ':', -2, 0);
                $width_percent              = sprintf('%02d',floor($appointment_slot_interval/60 *100));
                $when_appt_would_end        = date('g:i a', strtotime("+$total_time_of_services minutes", strtotime($dateTimeStart_massaged)));
                $appt_class_defs            = " f_left f_left hp100 wp" . $width_percent;
                $appt_class_defs_mouseover  = " f_left f_left no-overflow hp100 greenyellow wp" . $width_percent;
                $data_div_css               = " s08 hp95 box1-black no-overflow";
                $title = '';?>
            <? if ( $result == 1) { ?>
                <div id=ChooseApttime_<?=$selected_date?>_<?=$selected_time?>_<?=$staff_id?> CLASS="<?=$appt_class_defs?> ChooseApt_NotAvailable"              title="Not Available">
                    <div id=ChooseApttime_<?=$selected_date?>_<?=$selected_time?>_<?=$staff_id?>_slot class="<?=$data_div_css?> right">
                        <? if (count($appointment_ids) > 0 ) {
                            foreach($appointment_ids as $appointment_id){?> <?=appt_show_appt_info($appointment_id)?><br><?}
                        } else { ?>
                            Not Available<br>
                        <?}?>
                    </div>
                </div>
            <? } elseif ( $result == 2 ) {  ?>
                <div id=ChooseApttime_<?=$selected_date?>_<?=$selected_time?>_<?=$staff_id?> CLASS="<?=$appt_class_defs?> mp ChooseApt_EXPIRED"  title="Day Off">
                    <div id=ChooseApttime_<?=$selected_date?>_<?=$selected_time?>_<?=$staff_id?>_slot class="<?=$data_div_css?>">Day Off
                        <? if (count($appointment_ids) > 0 ) {
                            foreach($appointment_ids as $appointment_id){?> <?=appt_show_appt_info($appointment_id)?><br><?}
                        }?>
                    </div>        
                </div>
            <? } elseif ( $result == 2.5 || $result == 2.6) {  ?>
                <div id=ChooseApttime_<?=$selected_date?>_<?=$selected_time?>_<?=$staff_id?> CLASS="<?=$appt_class_defs?> ChooseApt_EXPIRED"     title="Appt Slot Off"
                                                                                                                            onclick="AppointmentCalendar_Open_Slot_Book(<?=$staff_id?>,'<?=$selected_date?>','<?=$selected_time;?>',<?=$appointment_slot_interval?>,'Show_Upcoming_Appts_Calendar_View');">
                    <div id=ChooseApttime_<?=$selected_date?>_<?=$selected_time?>_<?=$staff_id?>_slot class="<?=$data_div_css?>">Appt Slot<br>Off</div>
                </div>
            <? } elseif ( $result == 3 ) {  ?>
                <div id=ChooseApttime_<?=$selected_date?>_<?=$selected_time?>_<?=$staff_id?> CLASS="<?=$appt_class_defs?> main_bc_color1 main_color1_text"     title="Booked">
                    <div id=ChooseApttime_<?=$selected_date?>_<?=$selected_time?>_<?=$staff_id?>_slot class="<?=$data_div_css?> right scrolling">
                            <?foreach($appointment_ids as $appointment_id){?> <?=appt_show_appt_info($appointment_id)?><br><?}?>
                    </div>
                </div>
            <? } elseif ( $result == 4 ) {  ?>
                <div id=ChooseApttime_<?=$selected_date?>_<?=$selected_time?>_<?=$staff_id?> CLASS="<?=$appt_class_defs?> ChooseApt_EXPIRED"                   title="Expired">
                    <div id=ChooseApttime_<?=$selected_date?>_<?=$selected_time?>_<?=$staff_id?>_slot class="<?=$data_div_css?>">Expired</div>
                </div>
            <? } elseif ( $result == 5 || $result == 5.1 || $result == 6 ) {  ?>
                <div id=ChooseApttime_<?=$selected_date?>_<?=$selected_time?>_<?=$staff_id?> CLASS="<?=$appt_class_defs?> mp ChooseApt_MouseOut"               title="Open Appointment Slot"
                                                                                                                            onclick="AppointmentCalendar_Open_Slot_Book(<?=$staff_id?>,'<?=$selected_date?>','<?=$selected_time;?>',<?=$appointment_slot_interval?>,'Show_Upcoming_Appts_Calendar_View');"
                                                                                                                            onmouseover="this.className='<?=$appt_class_defs_mouseover?> ChooseApt_MouseOver';"
                                                                                                                            onmouseout ="this.className='<?=$appt_class_defs?> ChooseApt_MouseOut';">
                    <div id=ChooseApttime_<?=$selected_date?>_<?=$selected_time?>_<?=$staff_id?>_slot class="<?=$data_div_css?>" ><?=date('g:i a', strtotime($dateTimeStart_massaged)) ?></div>
                </div>
            <?}
        }
                    function appt_end_timeslot($appointment_slot_interval,$when_appt_would_end){
                        $check_min = 0; $switch = 1;
                        $appointment_slot_interval++;
                        $ts = strtotime($when_appt_would_end);
                        $hour           = date("G",$ts);
                        $ending_min     = date("i",$ts);
                        while ($check_min<60 && $switch == 1) {
                            if ( ($ending_min >= $check_min) && ($ending_min <= $appointment_slot_interval)) { 
                                $time_slot = $hour . sprintf("%02d", $check_min) ;
                                $switch = 0;
                            } else {
                                $check_min = $check_min + $appointment_slot_interval;
                                $appointment_slot_interval =  $appointment_slot_interval + $appointment_slot_interval;
                            }
                        }
                        return $time_slot;
                    }
    function make_appointment_step3() { ?>
    <div id="make_appointment_step3" class="d_InlineBlock wp100 hp90 main_bc_color2_light">
        <div class="wp48 hp98 m5 f_left main_bc_color1 main_color1_text">
            <?make_appointment_step3_LEFT_LOGIN();?>
        </div>
        <div class="wp48 hp98 m5 f_right main_bc_color1 main_color1_text">
            <?make_appointment_step3_RIGHT_REGISTER();?>
        </div>
    </div>
    <? }
        function make_appointment_step3_LEFT_LOGIN() {
        ?>
        <div class="wp100 hp100" id="make_appointment_step3_LEFT_LOGIN">
                <!--Line Space-->
                <div class="f_left wp100 hp02">
                    &nbsp;
                </div>

                <!--Appointment Info-->
                <div class="f_left wp100 hp35">
                    <?appointment_info();?>
                </div>
            
                <!--Line Space-->
                <div class="f_left wp100 hp02">
                    &nbsp;
                </div>
            
                <!--Services selected for appointment-->
                <div class="f_left wp100 hp30">
                    <?services_selected_for_appointment();?>
                </div>
            
                <!--Line Space-->    
                <div class="f_left wp100 hp02">
                    &nbsp;
                </div>
            
            <? if (!isset($_SESSION['appointment']['customer_id']) && !isset($_SESSION['appointment']['appt_set']) ) {?>
                <div class="f_left wp100 hp20">
                    <div class="f_left wp100 hp40">
                      <div class="f_left wp40 right">Email Address:&nbsp;</div>
                      <div class="f_left wp60 left"><input class="wp80" type="text" placeholder="email address" id="user_email"></div>
                    </div>
                    <div class="f_left wp100 hp30">
                      <input onclick="ValidateUser('make_appointment_step3_LEFT_LOGIN')" type="submit" value="Login and Continue" class="button" >
                    </div>
                    <div class="f_left wp100 hp30 red s07 no-overflow" id="failed_login_message">&nbsp;</div>
                </div>
            <? } else { ?>
                <div class="d_InlineBlock wp95 hp20 main_bc_color1_light main_color1_light_text" >
                    <div class="f_left wp100 hp50">
                       <? appointment_link_div_by_status() ?>
                    </div>
                    <div class="f_left mt05 wp100 hp50 s08 ">
                        You're logged in as: &nbsp <font class="s13"> <?=$_SESSION['appointment']['first_name'] . " " . $_SESSION['appointment']['last_name']?> </font> &nbsp; (<a href="" onclick="Logout('appointment','body_div')">logout</a>)
                    </div>
                </div>
            <? } ?>            
        </div>
        <?php
        }
            function appointment_info(){
            $_SESSION['appointment']['selected_apt_time'] = sprintf('%04d',$_SESSION['appointment']['selected_apt_time'] );
            $startTime = substr($_SESSION['appointment']['selected_apt_time'], 0, 2) . ':' . substr($_SESSION['appointment']['selected_apt_time'], 2);

            $startDateTime = $_SESSION['appointment']['selected_date']." ".$startTime.":00";
            $total_time = $_SESSION["appointment_book"]['total_time'] ;
            $etime = strtotime("+$total_time minutes", strtotime($startDateTime));
            $when_appt_would_end = date('g:i a', $etime);
            ?>
            <div id="STEP3_display_appt_info" class="d_InlineBlock wp95 hp100 main_bc_color1_light main_color1_light_text" >
                <div class="wp100 hp30 f_left">
                    <? appointment_link_div_by_status() ?>
                </div>
                <div class="wp100 hp15 f_left s08">
                    <div class="f_left left wp30 hp100 ml5 ">Staff Name</div>
                    <div class="f_left right wp65 hp100 "><?=$_SESSION['appointment']['staff_firstname'] . " " . $_SESSION['appointment']['staff_surname'] ?></div>
                </div>
                <div class="wp100 hp15 f_left s08">
                    <div class="f_left left wp30 hp100 ml5 ">Date</div>
                    <div class="f_left right wp65 hp100 "><?=date('l jS \of F Y ', strtotime($_SESSION['appointment']['selected_date']))?></div>
                </div>
                <div class="wp100 hp15 f_left s08">
                    <div class="f_left left wp30 hp100 ml5 ">Start Time</div>
                    <div class="f_left right wp65 hp100 "><?=date('g:i a', strtotime($startTime))?></div>
                </div>
                <div class="wp100 hp15 f_left s08">
                    <div class="f_left left wp30 hp100 ml5 ">End Time</div>
                    <div class="f_left right wp65 hp100 "><?=$when_appt_would_end?></div>
                </div>
            </div>
            <?}
            function appointment_link_div_by_status() {
                $general_dal    = new GENERAL_DAL();
                $AAMC           = $general_dal->get_Company_Preference($_SESSION['settings']['company_id'],'appointments_allow_mult_customer');
                if ( isset($_SESSION['appointment']['customer_id'])) {
                    $appts  = $general_dal->get_Active_Future_Appointments_byCustomerID($_SESSION['appointment']['customer_id']);
                    $appts_count = $appts[0]->count;
                }
                else {
                    $appts = array();
                    $appts_count = 0;
                    $pending = "<font color=\"red\">Pending </font>"; $td = "Please Login to proceed.";
                 }
                if ( (isset($_SESSION['appointment']['customer_id']) && ($appts_count == 0 || $AAMC[0]->value == 1 )) && (!(isset($_SESSION['appointment']['appt_set'])) ) ) {
                    $td = "<input onclick=\"appointmentProcessStepFour()\" type=\"submit\" value=\"Agree and Book Appointment?\" class=\"button buttonMargin mp\" title=\"Agree and Book Appointment?\" />";
                    $pending = "<font color=\"red\">Pending </font>";
                 }
                 elseif (  ($appts_count > 0 && !isset($_SESSION['appointment']['appt_set']) ) && ($AAMC[0]->value == 0 ) ){
                    $td = "<input onclick=\"AppointmentAlreadyBooked()\" type=\"submit\" value=\"*** Confirm details and Book ***\" class=\"button buttonMargin mp\" />";
                    $pending = "<font color=\"red\">Pending </font>";
                 }
                 elseif (isset($_SESSION['appointment']['appt_set']) && $_SESSION['appointment']['appt_set'] == 1 ){
                    $td = "<input onclick=\"appointmentProcessStepFour()\" type=\"submit\" value=\"View your Confirmed Appointment Info?\" class=\"button buttonMargin mp\" title=\"View your Confirmed Appointment Info?\" />";
                    $pending = "<font color=\"green\">Booked and Confirmed </font>";
                 }
                ?>
                <div class="wp100 hp100 f_left t_align_center main_bc_color2 main_color2_text">
                    <div class="f_left center wp100 hp40 hp100 s08 bold">Appointment is currently: <font class='s10'><?=$pending?></font></div>
                    <div class="f_left center wp100 hp60 hp100 s08"><?=$td ?></div>
                </div>
                <?
            }
            function services_selected_for_appointment($appointment_id=0){
            $total_mins = $total_price= 0;
            if ($appointment_id!=0) {
                $Appointment_dal = new Appointments_DAL();
                $Appointment_Service_Data = $Appointment_dal->Appointments_displayAppointmentServices_by_appointment_ID($appointment_id);
                $service_ids = array();
                foreach($Appointment_Service_Data as $Appointment_Service) { array_push($service_ids, $Appointment_Service->service_id); }
                $employee_id = $Appointment_Service->employee_id;
            }
            else {
                $service_ids= $_SESSION['appointment_book']['services_selected'];
                $employee_id= $_SESSION['appointment']['staff_id'];
            }
            ?>
            <div class="d_InlineBlock wp95 hp100 main_bc_color1_light main_color1_light_text scrolling" style="display:inline-block; width: 95%;" id="STEP3_display_appt_info">
                    <div class="f_left wp100 t_align_center main_bc_color2 main_color2_text s10 bold" style="display:inline-block; width: 100%;">
                        <div class="f_left wp03" style="float:left; width:03%">#</div>
                        <div class="f_left wp12" style="float:left; width:12%">&nbsp;</div>
                        <div class="f_left wp40 no-overflow right" style="float:left; width:40%; overflow:hidden;">Service Name</div>
                        <div class="f_left wp20" style="float:left; width:20%">Time</div>
                        <div class="f_left wp20 right" style="float:left; width:20%; text-align: right;">Price</div>
                    </div>
                <?
                $count = 1;
                include_once('inventory_management_functions.php');
                $INVENTORY_DAL = new INVENTORY_DAL();
                foreach ($service_ids as $service_id) {
                    $apptInfo = $INVENTORY_DAL->ServiceManagement_ServicesProperties($service_id);
                    $Profiles_DAL   = new Profiles_DAL();
                    $employeeServiceStatusInfo = $Profiles_DAL->get_ServiceStatus_byLoginId($employee_id,$service_id);
                    if ( $employeeServiceStatusInfo[0]->employee_price != 0 ) {
                        $apptInfo[0]->price = $employeeServiceStatusInfo[0]->employee_price;
                    }
                    ?>
                    <div class="wp100 f_left center s07"    style="display:inline-block; width: 100%;">
                        <div class="f_left wp03"                    style="float:left; width:03%"><?=$count?>.</div>
                        <div class="f_left wp12"                    style="float:left; width:12%">&nbsp;</div>
                        <div class="f_left wp40 no-overflow right"  style="float:left; width:40%; overflow:hidden;"><?=$apptInfo[0]->name?></div>
                        <div class="f_left wp20"                    style="float:left; width:20%"><?=$apptInfo[0]->est_time_mins?> min</div>
                        <div class="f_left wp20 right"              style="float:left; width:20%; text-align: right;"><?=money2($apptInfo[0]->price)?></div>
                    </div>
                    <? $count++; $total_mins += $apptInfo[0]->est_time_mins;$total_price += $apptInfo[0]->price;
                } ?>
                    <div class="wp100 f_left s08"           style="display:inline-block; width: 100%;">
                        <div class="f_left wp03"                    style="float:left; width:03%">&nbsp;</div>
                        <div class="f_left wp12"                    style="float:left; width:12%">&nbsp;</div>
                        <div class="f_left wp40 no-overflow right"  style="float:left; width:40%;">Total</div>
                        <div class="f_left wp20"                    style="float:left; width:20%; color:green; font-weight:bold;"><?=$total_mins?> mins</div>
                        <div class="f_left wp20 right"              style="float:left; width:20%; text-align: right; color:green; font-weight:bold;"><?=money2($total_price)?></div>
                    </div>
            </div>
            <?
            }
        function make_appointment_step3_RIGHT_REGISTER() {?>
        <?if (!isset($_SESSION['appointment']['customer_id'])){ ?>
            <div id="registerPanel">
                <div class="s10 bold">? Do you need to register in our System ?</div>
                <div class="m5 center box">
                  <p>Registration is <b>free</b> and quick.<br>Registration allows you to:</p>
                  <ul>
                    <li class="s08">Easily Book Appointments for any of our services.</li>
                    <li class="s08">Receive alerts reminding you of your scheduled appointment. </li>
                    <li class="s08">Receive email alerts of special deals and offers.</li>
                    <li class="s08">... and no need to call us anymore!</li>
                  </ul>
                  <p>
                      <label>&nbsp;</label>
                      <input onclick="LoadNewUserDiv()" type="submit" value="Click here to Register as a new user." class="button"/>
                  </p>
                </div>
            </div>
        <?  } else { ?>
            <div class="f_left wp100 hp100" id="appointmentHistoryPanel">
                <? show_appt_history($_SESSION['appointment']['customer_id']);?>
            </div>
            <?}
        }
        function make_appointment_step3_RIGHT_NEWUSER($register_only='register_and_book') {
        if ($register_only == 'register_only') {
            $onclick_function = "onclick=NewUser('register_only')";
        } else {
            $onclick_function = "onclick=NewUser('register_and_book')";
        }
        if (isset($_SESSION['appointment']['user_email'])) { $user_email = $_SESSION['appointment']['user_email'] ; } else { $user_email =''; }
        ?><div id="register_pannel" class="h350px">
            <div class="d_inlineBlock wp95 ">
                <div class="s12 bold">We're glad to have you register! </div><div class="s09">Just give us the basic info.</div>
            </div>
            <div class="d_InlineBlock wp95  mb5 main_bc_color1_light main_color1_light_text">
                <div class="wp100 d_InlineBlock left main_bc_color2 main_color2_text s11">
                    Personal Information
                </div>
                <div class="d_InlineBlock wp100 s08">
                    <div class="wp20 f_left right">First Name</div>
                    <div class="wp40 f_left left">
                            <input type="text" id="NU_first_name" name="NU_first_name" value="" size="20" maxlength="50">
                    </div>
                    <div class="wp40 f_left left no-overflow" id="failed_register_message_NU_first_name">&nbsp;</div>
                </div>
                <div class="d_InlineBlock wp100 s08">
                    <div class="wp20 f_left right">Last Name</div>
                    <div class="wp40 f_left left">
                            <input type="text" id="NU_last_name" name="NU_last_name" value="" size="20" maxlength="50">
                    </div>
                    <div class="wp40 f_left left no-overflow" id="failed_register_message_NU_last_name">&nbsp;</div>
                </div>
                <div class="d_InlineBlock wp100 s08">
                    <div class="wp20 f_left right">E-mail</div>
                    <div class="wp45 f_left left">
                            <input type="text" id="NU_user_email" name="NU_user_email" class="wp90" value="<?=$user_email?>" maxlength="64">
                    </div>
                    <div class="wp35 f_left left no-overflow" id="failed_register_message_NU_user_email">&nbsp;</div>
                </div>
                <div class="d_InlineBlock wp100 s08">
                    <div class="wp20 f_left right">Phone #</div>
                    <div class="wp40 f_left left">
                            <input type="text" id="NU_phone_num" name="NU_phone_num" value="" size="16" maxlength="16">
                    </div>
                    <div class="wp40 f_left left no-overflow" id="failed_register_message_NU_phone_num">&nbsp;</div>
                </div>
            </div>
            <div class="d_InlineBlock wp100 mb5">
                <input <?=$onclick_function?> type="submit" value="Complete Registration" class="button"/>
            </div>

            <div class="d_InlineBlock wp95 mb5 main_bc_color1_light main_color1_light_text">
                <div class="wp100 d_InlineBlock left main_bc_color2 main_color2_text s11">
                    Optional Information
                </div>
                <div class="d_InlineBlock wp100 s08">
                    <div class="wp20 f_left right">Address</div>
                    <div class="wp50 f_left left">
                        <input type="text" maxlength="70" size="20" id="NewCustomer_Street1">
                    </div>
                    <div class="wp25 f_left left no-overflow" id="failed_register_message_NewCustomer_Street1">&nbsp;</div>
                </div>
                <div class="d_InlineBlock wp100 s08">
                    <div class="wp20 f_left right">City</div>
                    <div class="wp50 f_left left">
                            <input name="NewCustomer:City" type="text" maxlength="40" size="20" id="NewCustomer_City" />
                    </div>
                    <div class="wp25 f_left left no-overflow" id="failed_register_message_NewCustomer_City">&nbsp;</div>
                </div>
                <div class="d_InlineBlock wp100 s08">
                    <div class="wp20 f_left right">State</div>
                    <div class="wp55 f_left left">
                        <select name="NewCustomer:State" id="NewCustomer_State">
                        <option value="">- Select -</option>
                        <option value="AL">Alabama</option>
                        <option value="AK">Alaska</option>
                        <option value="AB">Alberta</option>
                        <option value="AZ">Arizona</option>

                        <option value="AR">Arkansas</option>
                        <option value="BC">British Columbia</option>
                        <option value="CA">California</option>
                        <option value="CO">Colorado</option>
                        <option value="CT">Connecticut</option>
                        <option value="DE">Delaware</option>

                        <option value="DC">District of Columbia</option>
                        <option value="FL">Florida</option>
                        <option value="GA">Georgia</option>
                        <option value="HI">Hawaii</option>
                        <option value="ID">Idaho</option>
                        <option value="IL" selected>Illinois</option>

                        <option value="IN">Indiana</option>
                        <option value="IA">Iowa</option>
                        <option value="KS">Kansas</option>
                        <option value="KY">Kentucky</option>
                        <option value="LA">Louisiana</option>
                        <option value="ME">Maine</option>

                        <option value="MB">Manitoba</option>
                        <option value="MD">Maryland</option>
                        <option value="MA">Massachusetts</option>
                        <option value="MI">Michigan</option>
                        <option value="MN">Minnesota</option>
                        <option value="MS">Mississippi</option>

                        <option value="MO">Missouri</option>
                        <option value="MT">Montana</option>
                        <option value="NE">Nebraska</option>
                        <option value="NV">Nevada</option>
                        <option value="NB">New Brunswick</option>
                        <option value="NF">Newfoundland</option>

                        <option value="NH">New Hampshire</option>
                        <option value="NJ">New Jersey</option>
                        <option value="NM">New Mexico</option>
                        <option value="NY">New York</option>
                        <option value="NC">North Carolina</option>
                        <option value="ND">North Dakota</option>

                        <option value="NT">Northwest Territories</option>
                        <option value="NS">Nova Scotia</option>
                        <option value="OH">Ohio</option>
                        <option value="OK">Oklahoma</option>
                        <option value="ON">Ontario</option>
                        <option value="OR">Oregon</option>

                        <option value="PA">Pennsylvania</option>
                        <option value="PE">Prince Edward Island</option>
                        <option value="PR">Puerto Rico</option>
                        <option value="QC">Quebec</option>
                        <option value="RI">Rhode Island</option>
                        <option value="SK">Saskatchewan</option>

                        <option value="SC">South Carolina</option>
                        <option value="SD">South Dakota</option>
                        <option value="TN">Tennessee</option>
                        <option value="TX">Texas</option>
                        <option value="UT">Utah</option>
                        <option value="VT">Vermont</option>

                        <option value="VA">Virginia</option>
                        <option value="WA">Washington</option>
                        <option value="WV">West Virginia</option>
                        <option value="WI">Wisconsin</option>
                        <option value="WY">Wyoming</option>
                        <option value="YK">Yukon Territory</option>
                        </select>
                    </div>
                    <div class="wp25 f_left left no-overflow" id="failed_register_message_NewCustomer_State">&nbsp;</div>
                </div>
                <div class="d_InlineBlock wp100 s08">
                    <div class="wp20 f_left right">Zip Code</div>
                    <div class="wp55 f_left left">
                            <input type="text" maxlength="50" size="10" id="NewCustomer_PostalCode" />
                    </div>
                    <div class="wp25 f_left left no-overflow" id="failed_register_message_NewCustomer_PostalCode">&nbsp;</div>
                </div>
                <div class="d_InlineBlock wp100 s08">
                    <div class="wp20 f_left right">Country</div>
                    <div class="wp50 f_left left">
                        <select name="NewCustomer:Country" id="NewCustomer_Country" onChange="ToggleState();">
                        <option value="">- Select -</option>
                        <option value="USA" selected>United States</option>
                        <option value="ZWE">Zimbabwe</option>
                        </select>
                    </div>
                    <div class="wp25 f_left left no-overflow" id="failed_register_message_NewCustomer_Country">&nbsp;</div>
                </div>
            </div>
            <div class="d_InlineBlock wp100 mb5">
                <input <?=$onclick_function?> type="submit" value="Complete Registration with Optional Info" class="button">
            </div>

            <div id="failed_NewUser_message">
                &nbsp;
            </div>
        </div>
    <?php }

    function make_appointment_step4() { ?>
    <div id="make_appointment_step4" class="d_InlineBlock wp100 hp90 main_bc_color2_light">
<!--        <div id="confirmation-details" style="height:100%; width:100%; float:left; background: white; border: 1px solid #AAA;">
            <div id="main" style="border: 0; background: 0; float:left; width: 75%; height:100%;">
                <div id="confirmation-message" style="margin: 30px;background: url(common_includes/includes/images/icon-confirm-checkmark.png) no-repeat top left; line-height: 35px;font-size: 15px;color: #555;padding: 5px 0 5px 100px;">
                  <span id="thank-you">Thank you for booking your appointment.</span>
                    <strong style="display: block;font-size: 16px;color: #333;font-weight: bold;">Tuesday, August 21 2012 at 1:00 PM</strong>
                </div>
                <div id="confirmation-actions" style="margin: 0 30px;padding: 30px 0;border-top: 1px solid #DDD;">
                  <div id="add-calendar" style="margin-bottom: 10px; background-image: url(common_includes/includes/images/icon-add-calendar.png);font-size: 16px;font-weight: bold;line-height: 40px;padding-left: 40px;background-repeat: no-repeat;background-position: center left;">
                    <div class="calendars1"  style="float: left; width: 30%;">Add to Calendar</div>
                    <div class="calendars2"  style="float: right; width: 60%; font-size: 16px;font-weight: bold;line-height: 40px;">
                      <a target="_blank" href=""                                    style="background-image: url(common_includes/includes/images/icon-outlook.png); background-repeat: no-repeat;background-position: center left;padding-left: 25px;float: left;font-size: 13px;line-height: 20px;margin-top: 10px;margin-left: 20px;font-weight: normal;text-decoration: none;color: #369;color: -webkit-link;text-decoration: underline;cursor: auto;">Outlook</a>
                      <a target="_blank" href="/e/confirm/calendars/google?_e="     style="background-image: url(common_includes/includes/images/icon-yahoo.png);   background-repeat: no-repeat;background-position: center left;padding-left: 25px;float: left;font-size: 13px;line-height: 20px;margin-top: 10px;margin-left: 20px;font-weight: normal;text-decoration: none;color: #369;color: -webkit-link;text-decoration: underline;cursor: auto;">Google</a>
                      <a target="_blank" href="/e/confirm/calendars/yahoo?_e="      style="background-image: url(common_includes/includes/images/icon-google.png);  background-repeat: no-repeat;background-position: center left;padding-left: 25px;float: left;font-size: 13px;line-height: 20px;margin-top: 10px;margin-left: 20px;font-weight: normal;text-decoration: none;color: #369;color: -webkit-link;text-decoration: underline;cursor: auto;">Yahoo</a>
                      <a target="_blank" href="/e/confirm/calendars/ical.ics?_e="   style="background-image: url(common_includes/includes/images/icon-ical.png);    background-repeat: no-repeat;background-position: center left;padding-left: 25px;float: left;font-size: 13px;line-height: 20px;margin-top: 10px;margin-left: 20px;font-weight: normal;text-decoration: none;color: #369;color: -webkit-link;text-decoration: underline;cursor: auto;">iCal</a>
                    </div>
                  </div>
                  <div id="get-directions" style="background-image: url(common_includes/includes/images/icon-get-directions.png); font-size: 16px;font-weight: bold;line-height: 40px;padding-left: 40px;background-repeat: no-repeat;background-position: center left;">
                    <a target="_blank" href="http://maps.google.com/?daddr=25%20East%20Washington%20Street%2C%20Chicago%2C%20IL%2060602" >Get Directions</a>
                  </div>
                </div>
            </div>
            <div id="sidebar" style="border: 0; background: 0; float:right; width: 25%; height:100%;">
                <div class="business-card">
                  <div class="summary">
                    <a target="_blank" href="http://www.demandforce.com/b/strobeldentistry">
                      <div class="logo">
                        <img src="//d2nlmfejzmb6of.cloudfront.net/images/logos/59779294-primary.jpg?68c334edead5b358a932379f38d376aaf21ec600" alt="Strobel Dentistry">
                      </div>
                      <h1>Strobel Dentistry</h1>
                      <h2>Dentists</h2>
                    </a>
                    <a target="_blank" class="reviews" href="http://www.demandforce.com/b/strobeldentistry/reviews">
                      <div class="stars-wrap">
                        <div class="stars" style="width: 80px;"></div>
                      </div>
                      <span>411 reviews</span>
                    </a>
                  </div>
                  <div class="details">
                    <div class="address">
                      <a target="_blank" class="street" href="http://maps.google.com/?daddr=25%20East%20Washington%20Street%2C%20Chicago%2C%20IL%2060602">25 East Washington Street<br>Chicago, IL 60602</a>
                      <span class="phone">(312) 726-3135</span>
                      <a class="email" title="strobeldentistry@live.com" href="mailto:strobeldentistry@live.com">Email</a>
                      <a target="_blank" class="website" href="http://www.strobeldentistry.com/">Website</a>
                    </div>
                    <div class="hours"><span>Sun: Closed</span><span>Mon - Thu: 8:00 AM - 5:00 PM</span><span>Fri: 8:00 AM - 3:00 PM</span><span>Sat: 9:00 AM - 3:00 PM</span></div>
                  </div>
                </div>
            </div>
        </div>        -->
        <div class="wp98 hp97 m5 f_left main_bc_color1 main_color1_text">
            <?make_appointment_step4_confirm();?>
        </div>
    </div>
    <? }
        function make_appointment_step4_confirm() {
        $InsertUpdateDelete_DAL = new InsertUpdateDelete_DAL();
        $General_DAL            = new GENERAL_DAL();
        $profiles_dal           = new Profiles_DAL();
        $Inventory_DAL          = new INVENTORY_DAL;        
        $just_set_appt= 0 ;
        ?>
        <div id="confirm_panel" class="f_left wp100 hp100">
            <? if (!isset($_SESSION['appointment']['appt_set']) || $_SESSION['appointment']['appt_set'] != 1 ){
                $_SESSION['appointment']['selected_apt_time'] = sprintf('%04d',$_SESSION['appointment']['selected_apt_time'] );
                $startDate      = $_SESSION['appointment']['selected_date'];
                $startTime      = $_SESSION['appointment']['startTime'] = substr($_SESSION['appointment']['selected_apt_time'], 0, 2) . ':' . substr($_SESSION['appointment']['selected_apt_time'], 2) . ':00';

                if( ceil($_SESSION['appointment_book']['total_time']/10) == $_SESSION['appointment_book']['total_time']/10) { $_SESSION['appointment_book']['total_time']--; }
                $total_time     = $_SESSION['appointment_book']['total_time'] ;
                $_SESSION['appointment']['endDateTime'] =date('Y-m-d', strtotime("+$total_time minutes", strtotime($startDate." ".$startTime))). " ".date('H:i:s', strtotime("+$total_time minutes", strtotime($startDate." ".$startTime))) ;

                $endDateTime   = date('Y-m-d', strtotime("+$total_time minutes", strtotime($startDate." ".$startTime))).":".date('H:i:s', strtotime("+$total_time minutes", strtotime($startDate." ".$startTime)));
                $startDateTime = $startDate.":".$startTime;

                ##############################################
                #  Insert Appt Data
                $insertApptSql  = "insert into appointments (company_id,login_id,customer_id,startDate,endDate,status,insert_date)";
                $insertApptSql .= " values(";
                $insertApptSql .= "'" .$_SESSION['settings']['company_id'].  "'," ;
                $insertApptSql .= $_SESSION['appointment']['staff_id'].  " , " ;
                $insertApptSql .= $_SESSION['appointment']['customer_id']. " , " ;
                $insertApptSql .= "'" .$startDateTime.  "'," ;
                $insertApptSql .= "'" .$endDateTime.  "'," ;
                $insertApptSql .= "0," ;
                $insertApptSql .= "now()" ;
                $insertApptSql .= ")";

                $insertApptID   = $InsertUpdateDelete_DAL->insert_query($insertApptSql);
                $_SESSION['appointment']['appt_id']     = $insertApptID;
                $_SESSION['appointment']['appt_set']    = 1;
                $just_set_appt = 1;
 
                ###############################################################
                #  Insert Appointment Services
                foreach ($_SESSION['appointment_book']['services_selected'] as $service_id) {
                    $get_ServiceStatus_byLoginId = $profiles_dal->get_ServiceStatus_byLoginId($_SESSION['appointment']['staff_id'],$service_id);
                    $service_data       = $Inventory_DAL->ServiceManagement_ServicesProperties($service_id);
                    $service_price      = intval($service_data[0]->price);                    
                    if ($get_ServiceStatus_byLoginId[0]->employee_price != 0) {
                        $price_paid     = intval($get_ServiceStatus_byLoginId[0]->employee_price);
                    }  else {
                        $price_paid     = intval($service_data[0]->price);
                    }
                        
                    $insertAppointment_ServicesSql = "
                        insert into appointments_services (employee_id, appointment_id, service_id,  service_price,  price_paid, added) 
                        values ({$_SESSION['appointment']['staff_id']}, $insertApptID, $service_id, $service_price, $price_paid, now())";
                    #echo "$insertAppointment_ServicesSql";
                    $Appointment_ServicesID = $InsertUpdateDelete_DAL->insert_query($insertAppointment_ServicesSql);
                }

                ###############################################################
                #  Email the Customer about the new Appointment.
                email_Customer_NewApptNotification($insertApptID,0);
                #  Email the Employee.
                email_Employee_NewApptNotification($insertApptID,0);
                #  Email the Master email address if applicable
                $bookings_copy_masterEmail_data = $General_DAL->get_Company_Preference($_SESSION['settings']['company_id'],'bookings_copy_masterEmail');
                if ( isset($bookings_copy_masterEmail_data) && $bookings_copy_masterEmail_data[0]->value == 1 ) {
                    email_MasterEmail_NewApptNotification($insertApptID,0);
                }

                ###############################################################
                ###   Set the appointment in the employee's Calendar.
                $createdEvent                                       = createEvent($insertApptID);
                if (is_object($createdEvent)) { $eventID  = $createdEvent->id->text; } else { $eventID  = $createdEvent; }
                $_SESSION['appointment']['remote_appointment_set']  = 1;

                ###############################################################
                #  Update Appoints and set the ThirdPartyApptVendor and the ThirdPartyApptEventId
                $updateAppointment_ServicesSql = "update appointments set ThirdPartyApptEventId='$eventID' where id = '$insertApptID'";
                $updateAppointment_ServicesSql = $InsertUpdateDelete_DAL->insert_query($updateAppointment_ServicesSql);
            } else {
                $insertApptID = $_SESSION['appointment']['appt_id'];
            } ?>
            <div id="STEP4_display_appt_info" class="d_InlineBlock wp70 hp100" >
                <div class="f_left wp100 hp55 mt5">
                    <?displayAppointmentInfo($just_set_appt,$insertApptID);?>
                </div>
                <div class="f_left wp100 hp40 mt10">
                    <?displayCancellationPolicy($_SESSION['settings']['company_id']);?>
                </div>
            </div>
        </div>
        <?
        }
        function displayAppointmentInfo($just_set_appt,$appointment_id) {
            require_once('appointment_functions.php');
            $Appointments_dal   = new Appointments_DAL;
            $AppointmentInfo    = $Appointments_dal->Appointments_displayAppointmentInfo($appointment_id);

            require_once('companies_functions.php');
            $Companies_dal      = new Companies_DAL();
                $SERVER_ADDRESS     = $Companies_dal->get_TemplateTabData_by_Name('SERVER_ADDRESS',$AppointmentInfo[0]->company_id);
                $PHYSICAL_ADDRESS   = $Companies_dal->get_TemplateTabData_by_Name('PHYSICAL_ADDRESS', $AppointmentInfo[0]->company_id);
            if       ( $just_set_appt == 0 && $AppointmentInfo[0]->status != 1) {
                $message = "Your appointment was already set!";
            } elseif ( $just_set_appt == 1 && $AppointmentInfo[0]->status != 1) {
                $message = "Your appointment was just set!";
            } elseif ( $just_set_appt == 2 || $AppointmentInfo[0]->status == 1) {
                $message = "Your appointment was Canceled! Please book another time slot soon.";
            } else {
                $message = "Your appointment was already set!(".$just_set_appt.")";
            }?>
            <div class="d_InlineBlock f_left wp100 main_bc_color1_light main_color1_light_text" style="display:inline-block; width: 100%;">
                <div class="wp100 d_InlineBlock t_align_center main_bc_color2 main_color2_text" style="display:inline-block; width: 100%;">
                    <div class="f_left left wp100 s10 bold"  style="float:left; text-align: center; width:100%">Appointment Information</div>
                </div>
                <div class="wp100 d_InlineBlock s08" style="display:inline-block; width: 100%;">
                    <div class="f_left left wp30 ml5 "  style="float:left; text-align: left; width:30%">Appointment for:</div>
                    <div class="f_left right wp65"  style="float:left; text-align: right; width:65%"><?=$AppointmentInfo[0]->first_name?> <?=$AppointmentInfo[0]->last_name?></div>
                </div>
                <div class="wp100 d_InlineBlock s08" style="display:inline-block; width: 100%;">
                    <div class="f_left left wp50 ml5 "  style="float:left; text-align: left; width:50%">Customer's phone number:</div>
                    <div class="f_left right wp45"  style="float:left; text-align: right; width:45%"><?=$AppointmentInfo[0]->phone_num?></div>
                </div>
                <div class="wp100 d_InlineBlock s08" style="display:inline-block; width: 100%;">
                    <div class="f_left left wp45 ml5 "  style="float:left; text-align: left; width:45%"><?=$AppointmentInfo[0]->first_name?>'s E-mail:</div>
                    <div class="f_left right wp50"  style="float:left; text-align: right; width:50%"><?=$AppointmentInfo[0]->email?></div>
                </div>

                <div class="wp100 d_InlineBlock s08" style="display:inline-block; width: 100%;">
                    <div class="f_left left wp100 ml5 "  style="float:left; text-align: right; width:100%">&nbsp;</div>
                </div>

                <div class="wp100 d_InlineBlock s08" style="display:inline-block; width: 100%;">
                    <div class="f_left left  wp50 ml5 "  style="float:left; text-align: left; width:30%">Appointment Date</div>
                    <div class="f_left right wp45" style="float:left; text-align: right; width:65%"><?=date('l jS \of F Y', strtotime($AppointmentInfo[0]->startDate))?></div>
                </div>

                <div class="wp100 d_InlineBlock s08" style="display:inline-block; width: 100%;">
                    <div class="f_left left  wp50 ml5 " style="float:left; text-align: left; width:50%">Appointment Start Time</div>
                    <div class="f_left right wp45"  style="float:left; text-align: right; width:45%"><?=date('g:i a', strtotime($AppointmentInfo[0]->startDate))?></div>
                </div>
                <div class="wp100 d_InlineBlock s08" style="display:inline-block; width: 100%;">
                    <div class="f_left left  wp50 ml5 " style="float:left; text-align: left; width:50%">Appointment End Time</div>
                    <div class="f_left right wp45"  style="float:left; text-align: right; width:45%"><?=date('g:i a', strtotime($AppointmentInfo[0]->endDate))?></div>
                </div>
                <div class="wp100 d_InlineBlock s08" style="display:inline-block; width: 100%;">
                    <div class="f_left left wp30 ml5 "  style="float:left; text-align: left; width:30%">Staff Name</div>
                    <div class="f_left right wp65" style="float:left; text-align: right; width:65%"><?=$AppointmentInfo[0]->staff_first_name;?> <?=$AppointmentInfo[0]->staff_last_name?></div>
                </div>
                <div class="wp100 d_InlineBlock s08" style="display:inline-block; width: 100%;">
                    <div class="f_left left wp30 ml5 "  style="float:left; text-align: left; width:30%">Location</div>
                    <div class="f_left right wp65"  style="float:left; text-align: right; width:65%"><?=$PHYSICAL_ADDRESS[0]->value?></div>
                </div>

                <div class="wp100 d_InlineBlock s08" style="display:inline-block; width: 100%;">
                    <div class="f_left left wp100 ml5"  style="float:left; text-align: left; width:100%">&nbsp;</div>
                </div>

                <? if ( isset($_SESSION['appointment']['appt_set']) && $_SESSION['appointment']['appt_set'] == 1 ) {?>
                <div class="wp100 d_InlineBlock s08" style="display:inline-block; width: 100%;">
                    <div class="f_left wp100 ml5 " style="float:left; width:100%"><?=$message?></div>
                </div>
                <?}?>
            </div>
<?}
        function displayCancellationPolicy($company_id) {
        require_once('preferences_functions.php');
        $preferences_dal        = new Preferences_DAL();
        $preferences_data       = $preferences_dal->get_company_preferences($company_id,'bookings_cancellation_policy');
        ?>
        <? if ( isset($preferences_data[0]->value) && strlen($preferences_data[0]->value) > 10 ) {?>
        <div class="d_InlineBlock f_left wp100 hp100 main_bc_color1_light main_color1_light_text" style="display:inline-block; width: 100%;">
            <div class="wp100 hp15 d_InlineBlock t_align_center main_bc_color2 main_color2_text" style="display:inline-block; width: 100%;">
                <div class="f_left wp100 hp100 s10 bold"  style="float:left; text-align: center; width:100%">Cancellation Policy</div>
            </div>
            <div class="wp100 hp85 d_InlineBlock s08" style="display:inline-block; width: 100%;">
                <div class="f_left left wp100 hp100 ml5 scrolling"  style="float:left; text-align: left; width:100%">
                    <?=$preferences_data[0]->value?>
                </div>
            </div>
        </div>
       <? } ?>
<?}

function show_calendar($arg_year,$arg_month) {
    $general_dal                    = new GENERAL_DAL();
    $appointment_slot_interval_data = $general_dal->get_CompanyPreference_by_Company_ID($_SESSION['settings']['company_id'],'appointment_slot_interval');
    $appointment_slot_interval      = ( count($appointment_slot_interval_data)==0 ) ? 30 : $appointment_slot_interval_data[0]->value;

    $PreferenceData                 = $general_dal->get_CompanyPreference_by_Company_ID($_SESSION['settings']['company_id'],'timezone');
    date_default_timezone_set($PreferenceData[0]->value);
    

    appointments_StaffAvailableSlots_per_DaySlotInterval($_SESSION['appointment']['staff_id'],0,$appointment_slot_interval);
    appointments_StaffAvailableSlots_per_DaySlotInterval($_SESSION['appointment']['staff_id'],1,$appointment_slot_interval);
    appointments_StaffAvailableSlots_per_DaySlotInterval($_SESSION['appointment']['staff_id'],2,$appointment_slot_interval);
    appointments_StaffAvailableSlots_per_DaySlotInterval($_SESSION['appointment']['staff_id'],3,$appointment_slot_interval);
    appointments_StaffAvailableSlots_per_DaySlotInterval($_SESSION['appointment']['staff_id'],4,$appointment_slot_interval);
    appointments_StaffAvailableSlots_per_DaySlotInterval($_SESSION['appointment']['staff_id'],5,$appointment_slot_interval);
    appointments_StaffAvailableSlots_per_DaySlotInterval($_SESSION['appointment']['staff_id'],6,$appointment_slot_interval);

if($arg_year && $arg_month) {
    $_SESSION['appointment']['selected_date']= $arg_year."-".$arg_month."-01";
    $date=mktime(0, 0, 0, $arg_month, 01, $arg_year);

}
elseif($arg_year && !(isset($arg_month)) ) {
    $_SESSION['appointment']['selected_date']= $arg_year."-01-01";
    $date=mktime(0, 0, 0, date("m"), 01, $arg_year);
}
elseif( $arg_year == 0 || $arg_month == 0 ) {
    $date=time();
}
$day=date('d', $date);$month=date('m', $date);$year=date('Y', $date);$Hour=date('H', $date);$minuite=date('i', $date);$second= date('s', $date);

if (!isset($_SESSION['appointment']['selected_date']) ){
    $current_selected_date = date('Y-m-d');
}
else {
    $current_selected_date = $_SESSION['appointment']['selected_date'] ;
}

$first_day = mktime(0,0,0,$month,1,$year);
$title = date('F', $first_day);
$day_of_week = date('w', $first_day);
$YearMonth	= $year . "-" . $month . "-" ;

$next_month_date_YEAR  = date("Y", mktime(0, 0, 0, date("m",$date)+1, date("d",$date), date("y",$date)) );
$next_month_date_MONTH = date("m", mktime(0, 0, 0, date("m",$date)+1, date("d",$date), date("y",$date)) );
$last_month_date_YEAR  = date("Y", mktime(0, 0, 0, date("m",$date)-1, date("d",$date), date("y",$date)) );
$last_month_date_MONTH = date("m", mktime(0, 0, 0, date("m",$date)-1, date("d",$date), date("y",$date)) );

$next_month = date("Y-M", mktime(0, 0, 0, date("m",$date)+1, date("d",$date), date("y",$date)) );
$last_month = date("M-Y", mktime(0, 0, 0, date("m",$date)-1, date("d",$date), date("y",$date)));

$days_in_month = cal_days_in_month(0, $month, $year);

$monthly_total = 0;
$lastyearmonthday = $year . "-" . $month . "-01";
if ( strtotime(date("Y-m-d")) < strtotime($lastyearmonthday) ) {
    $last_year = "<a onclick=show_calendar('$last_month_date_YEAR','$last_month_date_MONTH');>$last_month</a>";
}
else {
    $last_year = "$last_month";
}
?>
<?
$week_div_css       = 'wp100 d_InlineBlock center f_left hp17';
$date_cell_css      = 'f_left hp100 no-overflow ';
$date_cell_div_css  = 'box1-black hp100 left ';
$date_cell_space    = 'f_left wp01 hp100 no-overflow ';
?>
<input type="hidden" id="current_selected_date" value="<?=$current_selected_date?>">
        <div class="d_InlineBlock wp100 hp07 main_bc_color1 main_color1_text">
            <div class="f_left wp20 hp100 mp"><?=$last_year?> </div>
            <div class="f_left wp60 hp100 main_bc_color2 main_color2_text s1"  > <?=$title?>&nbsp;<?=$year?> </div>
            <div class="f_left wp20 hp100 mp"><a onclick=show_calendar('<?=$next_month_date_YEAR?>','<?=$next_month_date_MONTH?>');><?=$next_month?></a></div>
        </div>
        <div class="d_InlineBlock wp100 hp05 s07">
            <div class="f_left wp15 hp100" ><div class="box1-black hp90 wp100">Sunday</div></div>
            <div class="f_left wp14 hp100" ><div class="box1-black hp90 wp100">Monday</div></div>
            <div class="f_left wp14 hp100" ><div class="box1-black hp90 wp100">Tuesday</div></div>
            <div class="f_left wp14 hp100" ><div class="box1-black hp90 wp100">Wednesday</div></div>
            <div class="f_left wp14 hp100" ><div class="box1-black hp90 wp100">Thursday</div></div>
            <div class="f_left wp14 hp100" ><div class="box1-black hp90 wp100">Friday</div></div>
            <div class="f_left wp15 hp100" ><div class="box1-black hp90 wp100">Saturday</div></div>
        </div>
        <div class="d_InlineBlock f_left bcgray hp86 wp100">
            <div class="d_inlineBlock s07 wp100 hp100">
                <div class="<?=$week_div_css?>">
                <?
                $day_count = 1;
                while ( $day_of_week >= 1 ) {?>
                    <? if ( $day_of_week == 0 || $day_of_week == 7 ) { $date_cell_css_width = "wp14";} else { $date_cell_css_width = "wp13"; } ?>
                    <div class="<?=$date_cell_space?>">&nbsp;</div>
                    <div class="<?=$date_cell_css?> <?=$date_cell_css_width?>">
                        &nbsp;
                    </div>
                    <?
                    $day_of_week--;
                    $day_count++;
                }
            $day_num = 1;
            $cal_bg_switch = 0;
            $date_cell_css_width = '';
            if ( date('w', $first_day) != 0 ){ ?> <div class="<?=$date_cell_space?>">&nbsp;</div><? }
            while ($day_num <= $days_in_month ) {
                    $YearMonthDay = $YearMonth . sprintf("%02.0f", $day_num ) ;
                    $day = mktime(0,0,0,$month,$day_num,$year);
                    $day = date("D", $day);
                    $todays_date  = date("Y-m-d");
                    $ordinalize_day_num = ordinalize($day_num);
                    if ( $day_count == 1 || $day_count == 7 ) { $date_cell_css_width = "wp14";} else { $date_cell_css_width = "wp13"; }
                    if ($_SESSION['appointment']['selected_date'] == $YearMonthDay) { $dateSelectedClass = "dateSelected" ; } else { $dateSelectedClass = "dateNotSelected"; }

                    if ( ( $YearMonthDay >= $todays_date || $cal_bg_switch == 1 ) &&  count($_SESSION[$_SESSION['appointment']['staff_id']][$day]) != 0) {?>
                        <div class="<?=$date_cell_space?>">&nbsp;</div>
                        <div class="<?=$date_cell_css?> <?=$date_cell_css_width?>"     onclick="selectAppointmentDate('<?=$YearMonthDay?>','<?=$date_cell_div_css?>')">
                            <div id="DateTD_<?=$YearMonthDay?>" class="<?=$date_cell_div_css?> <?=$dateSelectedClass?>"><?=$ordinalize_day_num?><br>Choose this day!</div>
                        </div>
                        <? $cal_bg_switch = 1;
                    }
                    elseif ($YearMonthDay < $todays_date ) {?>
                        <div class="<?=$date_cell_space?>">&nbsp;</div>
                        <div class="<?=$date_cell_css?> <?=$date_cell_css_width?>">
                            <div id="DateTD_<?=$YearMonthDay?>" class="<?=$date_cell_div_css?> dateExpired"><?=$ordinalize_day_num?><br>In the Past...<br>..</div>
                        </div>
                    <? }
                    elseif ( count($_SESSION[$_SESSION['appointment']['staff_id']][$day]) == 0 ) {?>
                        <div class="<?=$date_cell_space?>">&nbsp;</div>
                        <div class="<?=$date_cell_css?> <?=$date_cell_css_width?>"    onclick="selectAppointmentDate('<?=$YearMonthDay?>','<?=$date_cell_div_css?>')">
                            <div id="DateTD_<?=$YearMonthDay?>" class="<?=$date_cell_div_css?> dateExpired"><?=$ordinalize_day_num?><br>None available today.</div>
                        </div>
                    <? }
                    if ( $day_count == 7 ) {?>
                        </div>
                        <? if ($day_num <= $days_in_month) { ?>
                            <div class="<?=$week_div_css?>">
                            <? $day_count = 0; ?>
                        <?}
                     }
                    $day_num++;
                    $day_count++;
                }
                while ( $day_count >1 && $day_count < 7) {?>
                    <div class="<?=$date_cell_css?>">&nbsp;</div>
                <? $day_count++; } ?>
            </div>
        </div>
        </div>
<?
}
    function ordinalize($number) {
    if (in_array(($number % 100),range(11,13))){
        return $number.'th';
    }
    else{
        switch (($number % 10)) {
            case 1:
            return $number.'st';
            break;
            case 2:
            return $number.'nd';
            break;
            case 3:
            return $number.'rd';
            default:
            return $number.'th';
            break;
        }
    }
}
function show_appt_history($customer_id){
    $customers_dal  = new Customers_DAL();
    $general_dal    = new GENERAL_DAL();
    $customer_data  = $customers_dal->get_CustomerDataPerId($customer_id);
    ?>
    <div class="f_left m5 wp95 hp05">
        " Welcome Back <?=$customer_data[0]->firstname?>&nbsp;<?=$customer_data[0]->lastname?> "
    </div>
    <div class="d_InlineBlock m5 s08 wp100 hp05 left">
        Your appointment history.
    </div>
    <div class="d_InlineBlock wp95 hp60 main_bc_color1_light main_color1_light_text scrolling">
        <div class="wp100 h20px f_left t_align_center main_bc_color2 main_color2_text">
            <div class="f_left left s10 bold wp17 hp100 ml5">Date</div>
            <div class="f_left left s10 bold wp17 hp100 ml5">Time</div>
            <div class="f_left left s10 bold wp30 hp100 ml5">Staff</div>
            <div class="f_left left s10 bold wp15 hp100 ml5">Status</div>
            <div class="f_left left s10 bold wp10 hp100 ml5">Edit</div>
        </div>
            <?
            $PreferenceData             = $general_dal->get_CompanyPreference_by_Company_ID($customer_data[0]->company_id,'timezone');
            date_default_timezone_set($PreferenceData[0]->value);
            $intNow = mktime();
            $current_time = date("Y-m-d H:i:s",$intNow);

            $AppointmentHistory_rows    = $general_dal->get_AppointmentHistory($customer_id,0);
            if (count($AppointmentHistory_rows)!=0) {
                foreach($AppointmentHistory_rows as $Appointment_data) {
                    $date = date('m/d/y', strtotime($Appointment_data->startDate));
                    $time = date('h:i A', strtotime($Appointment_data->startDate));
                    if ( $Appointment_data->startDate < $current_time) {
                        $status = "Past";
                        $editORnot = "---";
                    }
                    else {
                        $status = "Pending";
                        $editORnot = "<a onclick=\"deleteAppointment($Appointment_data->id,$customer_id,1,".$customer_id.",'appointmentHistoryPanel');\" >
                            <img src=\"/common_includes/includes/images/cancel.jpg\" title=\"Cancel the Appointment\" width=\"11\" height=\"11\">
                                </a>";
                    }
                    if ( $Appointment_data->status==1) {
                        $status = "<font color=red size=1 title=\"Canceled\">X</font>";
                        $editORnot = "---";
                    }
                    ?>
                  <div class="wp100 h15px f_left s07">
                    <div class="f_left left wp17 hp100 ml5 "><?=$date?></div>
                    <div class="f_left left wp17 hp100 ml5 "><?=$time?></div>
                    <div class="f_left left wp30 hp100 ml5 "><?=$Appointment_data->firstname?>  <?=$Appointment_data->lastname?></div>
                    <div class="f_left left wp15 hp100 ml5 center no-overflow"><?=$status?></div>
                    <div class="f_left left wp10 hp100 ml5 center"><?=$editORnot?></div>
                  </div>
                <? }
            } else {?>
                  <div class="wp100 d_InlineBlock s08">
                    <div class="wp100 f_left center red">We do not have any appointment history for you as of today.</div>
                  </div>
            <?} ?>
    </div>
    <?
}
?>