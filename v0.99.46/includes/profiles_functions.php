<?php
include_once('general_functions.php');

class Profiles_DAL  {
  public function __construct(){}
  public function get_AllEmployeesPerCompanyId($company_id,$totals,$active=0,$appt_active=0){
    if ($totals == 0) {
        $sql = "SELECT id,
                    username,
                    firstname,
                    lastname,
                    level,
                    status,
                    employee_quote,
                    added,
                    email_address,
                    gmail_username,
                    gmail_password";
    }
    ELSE {
        $sql ="SELECT count(distinct(id)) as count ";
    }

  $sql.= " from logins l
  where deleted is NULL and
  company_id = $company_id
  and username not in ('admin','employee','manager') ";
    if ( (isset($_SESSION['search_data']['Profiles_AllProfiles']['profiles_search_inactive_profiles'])   && $_SESSION['search_data']['Profiles_AllProfiles']['profiles_search_inactive_profiles'] == 1) ){
        $sql .= " and ( l.status in (0,1) ) ";
    } else {
        $sql .= " and ( l.status = 1 ) ";
    }
    if ( isset($_SESSION['search_data']['Profiles_AllProfiles']['profiles_search_first_name'])          && $_SESSION['search_data']['Profiles_AllProfiles']['profiles_search_first_name'] != -1 )                    {$sql .= " and l.firstname        like '%" . $_SESSION['search_data']['Profiles_AllProfiles']['profiles_search_first_name'] . "%' "; }
    if ( isset($_SESSION['search_data']['Profiles_AllProfiles']['profiles_search_last_name'])           && $_SESSION['search_data']['Profiles_AllProfiles']['profiles_search_last_name'] != -1 )                     {$sql .= " and l.lastname         like '%" . $_SESSION['search_data']['Profiles_AllProfiles']['profiles_search_last_name'] . "%' "; }
    if ( isset($_SESSION['search_data']['Profiles_AllProfiles']['profiles_search_email'])               && $_SESSION['search_data']['Profiles_AllProfiles']['profiles_search_email'] != -1 )                         {$sql .= " and l.email_address    like '%" . $_SESSION['search_data']['Profiles_AllProfiles']['profiles_search_email'] . "%' "; }
    if ( isset($_SESSION['search_data']['Profiles_AllProfiles']['profiles_search_phone_number'])        && $_SESSION['search_data']['Profiles_AllProfiles']['profiles_search_phone_number'] != -1 )                  {$sql .= " and l.phone_num        like '%" . $_SESSION['search_data']['Profiles_AllProfiles']['profiles_search_phone_number'] . "%' "; }

    if ($appt_active == 1 ) {
      $sql.= " and appt_active = 1 ";
    }
    if ($totals == 0) {
        if (!(isset($_SESSION['search_data']['column']))) { $sql.= " order by id desc"; }
        else { $sql.= " order by " . $_SESSION['search_data']['column'] . " " . $_SESSION['search_data']['asc_desc'] . " "; }
        
        if ( isset($_SESSION['search_data']['paging_page']) && $_SESSION['search_data']['paging_page'] != 0) {
            if ( $_SESSION['search_data']['paging_page'] == 1) { $limit_offset = 0; }
            else { $limit_offset = ( ($_SESSION['search_data']['paging_page'] - 1) * 10 ) ; }
            $sql .= " limit $limit_offset,10";
        }
    }
    #if ($totals != 1 ) { print $sql . "\n"; }
    return $this->query($sql);
  }
  public function get_last_X_clock_in_out($login_id,$number_of_results){
    $sql = "SELECT date_time, login_id, action 
        from timeManagement
        where login_id = $login_id
        order by date_time desc";
        if ($number_of_results != 0 ){
            $sql .= " limit $number_of_results";
        }
    return $this->query($sql);
  }
  public function get_addresses_per_login_id($login_id){
    $sql = "SELECT address_id,address_line1,address_line2,city,state,zipcode,country,default_address
      from addresses
      where login_id = $login_id ;";
    #echo $sql;
    return $this->query($sql);
  }
  public function get_address_data_by_address_id($address_id){
    $sql = "SELECT address_line1,address_line2,city,state,zipcode
        from addresses
        where address_id = $address_id ";
    return $this->query($sql);
  }
  public function get_last_clock_in_out($login_id,$action,$clock_out_time='NULL'){
      if ( $action == 'PUNCHIN' && $clock_out_time != 'NULL') {
          $sql = "SELECT date_time as clock_in_time
                    from timeManagement
                    where date_time < '$clock_out_time' and login_id = $login_id
                    order by date_time desc
                    limit 1;";

        }
        else {
        $sql = "SELECT date_time 
        from timeManagement 
        where login_id = $login_id and action = '$action' 
        order by date_time desc 
        limit 1";
        }
        return $this->query($sql);
  }
  public function get_apptTimeStatus($login_id,$day_of_week,$time,$appointment_slot_interval){
      $sql = "SELECT status from logins_def_appt_times
          where login_id            = $login_id and
          day_of_week               = $day_of_week and
          time                      = '$time' and
          appointment_slot_interval = '$appointment_slot_interval' ";
        return $this->query($sql);
  }
  public function get_ServiceStatus_byLoginId($login_id,$service_id){
      $sql = "SELECT login_id,service_id,employee_price,status,updated
                from logins_services
                where service_id = $service_id and
                login_id = $login_id;";
      #print $sql;
      return $this->query($sql);
  }

  public function get_EmployeeDataPerLoginId($login_id){
    $sql = "SELECT id,username,password,firstname,lastname,level,status,appt_active,employee_quote,added,email_address,gmail_username,gmail_password
      from logins
      where id = $login_id";
    return $this->query($sql);
    #print $sql;
  }
  public function get_EmployeeDataPerEmployeeRemoteKey($ipAddress,$EmployeeRemoteKey){
    $sql = "SELECT id,username,company_id
      from logins
      where EmployeeRemoteIP = '$ipAddress' and EmployeeRemoteKey = '$EmployeeRemoteKey' and status = 1 and deleted is NULL";
    #print $sql . "<br>";
    return $this->query($sql);
  }

  public function get_apptsByDayAndLoginID($login_id,$day_of_week,$appointment_slot_interval){
      $sql = "SELECT replace(time, ':', '') as time
                from logins_def_appt_times
                where login_id = $login_id and
                status = 1 and
                appointment_slot_interval = $appointment_slot_interval and
                day_of_week = $day_of_week";
      //print $sql;
        return $this->query($sql);
  }
  public function get_ImageID_byProfileID($login_id){
      $sql = "SELECT image_id
                from item_image_mappings
                where profile_id = $login_id and
                deleted is null
                order by default_item_image desc, default_group_image desc, added asc
                limit 1";
    #print "$sql";
    return $this->query($sql);
  }
  public function get_default_Profile_ImageID($login_id){
      $sql = "SELECT image_id, image_db_id
                from item_image_mappings
                where id = $login_id and
                `default` = 1 and
                deleted is null
                order by default_item_image desc, default_group_image desc, added asc
                limit 1";
    #print "$sql";
    return $this->query($sql);
  }

  public function get_EmployeeDataPerId($login_id){
    $sql = "SELECT id,username,password,firstname,lastname,level,status,employee_quote,employee_bio,added,email_address,gmail_username,gmail_password
      from logins
      where id = $login_id";
    #print $sql;
    return $this->query($sql);

  }
  public function get_ServiceIDStatus_byLogin_id($login_id,$service_id){
      $sql = "SELECT service_id
          from logins_services
          where login_id = $login_id and
          status = 1 and
          service_id = $service_id";
     #print $sql."<br>";
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

function profiles_clock_in_out() {?>
<div class="ReportsTopRow main_bc_color2 main_color2_text">Clock In / Out</div>
    <div style="max-height: 1000px;" class="f_left wp100 hp94">
        <div class="leftSpace main_bc_color2 main_color2_text">&nbsp;</div>
        <div class="middleSpace wp96">
            <?profiles_clock_in_out_TOP();?>
            <?profiles_clock_in_outStanza();?>
        </div>
        <div class="rightSpace main_bc_color2 main_color2_text">&nbsp;</div>
    </div>
<div class="ReportsBottomRow main_bc_color2 main_color2_text">&nbsp;</div>
<?
}
    function profiles_clock_in_out_TOP(){?>
            <div class="profileHeader hp10">
                <div class="f_left">
                    <img alt="" height="45" src="/common_includes/includes/images/time_clock_button.gif">
                    Clock your hours here. Time is recorded down to the second.
                </div>
                <div class="f_right">
                    <? if ($_SESSION['settings'][$_SESSION['settings']['login_id']]['level'] >= 2) { ?>
                    <a onclick="editProfile_AddUser()" href="javascript: none();">
                        <img alt="" height="45" src="/common_includes/includes/images/group-user-add.png" style="border-style: none">
                    </a>
                    <? } ?>
                </div>
            </div>        
    <?}
    function profiles_clock_in_outStanza(){
    ?>
        <div class="d_InlineBlock hp90 wp100">
            <div class="d_InlineBlock hp100 wp100" id="profileBodyCenter">
                <div class="f_left  hp20 wp100">
                    <? profiles_clock_in_out_buttons($_SESSION['settings']['login_id']);?>
                </div>
                <div class="f_left  hp80 wp100 scrolling">
                    <? profiles_clock_in_out_report($_SESSION['settings']['login_id'],10); ?>
                </div>
            </div>
        </div>
    <?
    }
      function profiles_clock_in_out_buttons(){
    $dal = new Profiles_DAL();
    $punchin  = $dal->get_last_clock_in_out($_SESSION['settings']['login_id'],'PUNCHIN');
    $punchout = $dal->get_last_clock_in_out($_SESSION['settings']['login_id'],'PUNCHOUT');
      ?>
        <div class="wp45 hp100 f_left center">
            <div
                <? if ( $punchout >= $punchin ) {?>
                    onclick="timeManagement_ClockInOrOut(<?=$_SESSION['settings']['login_id']?>,'PUNCHIN');" class="mp cssbox wp90 hp90"
                <?} else {?>
                    style="opacity:.3;" class="cssbox wp90 hp90"
                <?}?>
                >
                <div class="cssbox_head">
                    <h2 class="green pl20 pt10">Punch IN</h2>
                </div>
                <div class="cssbox_body">
                    <p>Last Action: <?=$punchin[0]->date_time?></p>
                </div>
            </div>
        </div>
        <div class="wp10 hp100 f_left">&nbsp;</div>
        <div class="wp45 hp100 f_left center">
            <div
                <? if ($punchin > $punchout ) {?>
                    onclick="timeManagement_ClockInOrOut(<?=$_SESSION['settings']['login_id']?>,'PUNCHOUT');" class="mp cssbox wp90 hp90"
                <?} else {?>
                    style="opacity:.3;" class="cssbox wp90 hp90"
                <?}?>
                >
                <div class="cssbox_head">
                    <h2 class="red pl20 pt10">Punch Out</h2>
                </div>
                <div class="cssbox_body">
                    <p>Last Action:<?=$punchout[0]->date_time?></p>
                </div>
            </div>
        </div>
      <?
      }
      function profiles_clock_in_out_report($login_id,$number_of_results) {
    $profiles_dal   = new Profiles_DAL();
    $profile_data   = $profiles_dal->get_EmployeeDataPerId($login_id);
    $rows           = $profiles_dal->get_last_X_clock_in_out($login_id,$number_of_results);
    ?>
        <div class="box5">
            <div class="wp100 d_InlineBlock">
                <div class="d_InlineBlock wp100 left">
                    <div class="f_left wp100 bctrt center">Your recent history of time recorded.</div>
                </div>
                <div class="d_InlineBlock wp100 center">
                    <div class="HEADER main_bc_color1 main_color1_text report_header_cell_wp19">Action</div>
                    <div class="HEADER main_bc_color1 main_color1_text report_header_cell_wp30">Staff Name</div>
                    <div class="HEADER main_bc_color1 main_color1_text report_header_cell_wp15">Time In</div>
                    <div class="HEADER main_bc_color1 main_color1_text report_header_cell_wp15">Time Out</div>
                    <div class="HEADER main_bc_color1 main_color1_text report_header_cell_wp10">Time</div>
                    <div class="HEADER main_bc_color1 main_color1_text report_header_cell_wp09">Edit</div>
                </div>
                <? if (count($rows) >=1 ) {
                       foreach($rows as $row) {
                            $backgroundColor = 'bclightgreen';
                            if ($row->action == 'PUNCHOUT') {
                              $clock_in_time = $profiles_dal->get_last_clock_in_out($login_id,'PUNCHIN',$row->date_time);
                              $difference =  strtotime($row->date_time) - strtotime($clock_in_time[0]->clock_in_time);
                              $hours = $difference / 3600; // 3600 seconds in an hour
                              $minutes = ($hours - floor($hours)) * 60;
                              $final_hours = round($hours,0);
                              $final_minutes = sprintf("%02d",$minutes);
                              $time_diff = $final_hours . ":" . $final_minutes ;
                              $time_WholeNumber = $final_hours . "." . sprintf("%02u",( ($final_minutes / 60) * 100) ) ;
                              $backgroundColor = 'bclightpink';
                            }
                            ?>
                    <div class="d_InlineBlock wp100 center s07 <?=$backgroundColor?>">
                        <div class="report_data_cell_wp19">&nbsp;<? if ($row->action == 'PUNCHIN')  { echo '--> IN&nbsp;'; } else { echo '<-- OUT'; } ?></div>
                        <div class="report_data_cell_wp30 no-overflow">&nbsp;<?=$profile_data[0]->firstname?> <?=$profile_data[0]->lastname?></div>
                        <div class="report_data_cell_wp15">&nbsp;<? if ($row->action == 'PUNCHIN')  { echo date("m/d/y g:i a", strtotime($row->date_time));}?></div>
                        <div class="report_data_cell_wp15">&nbsp;<? if ($row->action == 'PUNCHOUT') { echo date("m/d/y g:i a", strtotime($row->date_time));}?></div>
                        <div class="report_data_cell_wp10">&nbsp;<? if ($row->action == 'PUNCHOUT') { echo $time_diff . "&nbsp;&nbsp;-&nbsp;&nbsp;" . $time_WholeNumber;}?> </div>
                        <div class="report_data_cell_wp09"><a class="menu" onclick=""><img width="11" height="9" src="/common_includes/includes/images/edit_icon_20_19.jpg" title="Edit"></a></div>
                    </div>
                        <? if ($row->action == 'PUNCHIN') { ?>
                    <div class="wp100 h08px">&nbsp;</div>
                        <? } ?>
                    <? }
                } else { ?>
                    <div class="wp100">You don't have any recorded time actions logged(<?=count($rows);?>).</div>
                <? } ?>
            </div>
        </div>
    <?
    }

function profiles() {
?>
<div class="ReportsTopRow main_bc_color2 main_color2_text"><a class="mp" onclick="onclick=mainDiv('Profiles'); return false;"> Employee Profiles Main</a></div>
    <div style="max-height: 1000px;" class="f_left wp100 hp94">
    <div class="leftSpace main_bc_color2 main_color2_text">
        &nbsp;
    </div>
    <div class="middleSpace wp96">
            <div class="f_left wp100 hp10">
                <div class="f_left hp100 wp35 left vtop no-overflow">
                        <img alt="" class="hp90" src="/common_includes/includes/images/employee_button.gif">
                        Employee Profiles
                </div>
                <div class="f_right hp100 wp50 right">&nbsp;
                    <? if (!isset($_SESSION['edit_profiles']['UserAdd']) && $_SESSION['settings'][$_SESSION['settings']['login_id']]['level'] >= 2) { ?>
                    <a onclick="editProfile_AddUser()" href="javascript: none();">
                        <img alt="" class="hp90" src="/common_includes/includes/images/group-user-add.png" style="border-style: none">
                    </a>
                    <? } ?>
                </div>
            </div>
        <?
        if (!isset($_SESSION['edit_profiles']['login_id']) && !isset($_SESSION['edit_profiles']['UserAdd'])) {
            if ( $_SESSION['settings'][$_SESSION['settings']['login_id']]['level'] >= 2) {?>
                <div class="d_InlineBlock wp100 hp90" >
                    <div class="f_left wp15 hp100">
                        <div class="d_InlineBlock wp100 hp100" >
                            <?=ProfilesSearchStanza() ?>
                        </div>
                    </div>
                    <div class="f_right wp85 hp100">
                        <div class="d_InlineBlock wp100 hp100" id="Profiles_AllProfilesBodyCenter" >
                            <?=profilesStanza()?>
                        </div>
                    </div>
                </div>
            <?} else {
                editLoginStanza($_SESSION['settings']['login_id']);
            }
        }
        elseif (isset($_SESSION['edit_profiles']['UserAdd'])) {
            userAddStanza();
        }
        else{
            editLoginStanza($_SESSION['edit_profiles']['login_id']);
        }
        ?>
    </div>
    <div class="rightSpace main_bc_color2 main_color2_text">
        &nbsp;
    </div>
</div>
<div class="ReportsBottomRow main_bc_color2 main_color2_text">
    &nbsp;
</div>
<?
}
    function profilesStanza() {
    ?>
            <div class="wp100 hp07" id="listing_search_paging_top">
                <? showPaging(); ?>
            </div>
            <div class="wp100 hp85 scrolling">
                <? profilesHeader(); ?>
                <? profilesAllProfiles(); ?>
            </div>
            <div class="wp100 hp07" id="listing_search_paging_bottom">
                <? showPaging(); ?>
            </div>
    <?
    }
    function profilesHeader() { ?>
        <div class="f_left wp100 h25px s08 HEADER main_bc_color1 main_color1_text">
            <div class="report_header_cell_wp04"><a onclick="orderBy('id','Profiles_AllProfiles'); return false;">ID#</a></div>
            <div class="report_header_cell_wp25"><a onclick="orderBy('lastname','Profiles_AllProfiles'); return false;">Last Name</a></div>
            <div class="report_header_cell_wp15"><a onclick="orderBy('firstname','Profiles_AllProfiles'); return false;">First Name</a></div>
            <div class="report_header_cell_wp25"><a onclick="orderBy('username','Profiles_AllProfiles'); return false;">Username</a></div>
            <div class="report_header_cell_wp10"><a onclick="orderBy('level','Profiles_AllProfiles'); return false;">Level</a></div>
            <div class="report_header_cell_wp10"><a onclick="orderBy('status','Profiles_AllProfiles'); return false;">Status</a></div>
            <div class="report_header_cell_wp07">Edit</div>
        </div>
    <?
    }
    function profilesAllProfiles() {
    $Profiles_DAL = new Profiles_DAL();
    $employees = $Profiles_DAL->get_AllEmployeesPerCompanyId($_SESSION['settings']['company_id'],0,0);

    if (count($employees) > 0 ) {
    $altClass = "bctr1a";
    foreach($employees as $employee){
    if     ($employee->status == 0) {   $status_action = "INactive"; $status_class = "red"    ;
                                        $action = 1; $alt="Activate?";}
    elseif ($employee->status == 1) {   $status_action = "Active"  ; $status_class = "green"  ;
                                        $action = 0; $alt="DeActivate Login?";}
    ?>
        <div class="f_left wp100 lh20 s07 <?=$altClass?>">
            <div class="report_data_cell_wp04"><?=$employee->id?></div>
            <div class="report_data_cell_wp25"><?=$employee->lastname?></div>
            <div class="report_data_cell_wp15"><?=$employee->firstname?></div>
            <div class="report_data_cell_wp25"><?=$employee->username?></div>
            <div class="report_data_cell_wp10"><?=$_SESSION['userlevels'][$employee->level]?></div>
            <div class="report_data_cell_wp10" title="<?=$alt?>">
                <input alt="<?=$alt?>" onclick="editProfile_UpdStatus(<?=$employee->id?>,<?=$action?>)" type="submit" value="<?=$status_action?>" class="button s07 <?=$status_class?>">
            </div>
            <div class="report_data_cell_wp07">
                <input onclick="editProfile_login(<?=$employee->id?>)" type="submit" value="EDIT" class="button s07">
            </div>
        </div>
    <?
    if ($altClass == "bctr1a") { $altClass = "bctr1b"; } else {$altClass = "bctr1a";}
    }
    }else {?>
    <div class="report_data_cell_wp100">There are't any employees added to the system yet.  Add an employee using the icon in the upper right of this window.</div>
    <?}
    }

    function editLoginStanza($login_id) {
    $profiles_dal = new Profiles_DAL();
    ?>
    <div class="d_InlineBlock wp100 hp90">
        <div class="d_InlineBlock wp95 hp100">
            <?=profileTop($profiles_dal,$login_id); ?>
            <?=profileLoginTabs($profiles_dal,$login_id); ?>
    <?
    if (isset($_SESSION['edit_profiles']['ActiveTab']) && $_SESSION['edit_profiles']['ActiveTab'] == "Hours_Report"){
        profileHours_Report($profiles_dal,$login_id);
    }

    if (isset($_SESSION['edit_profiles']['ActiveTab']) && $_SESSION['edit_profiles']['ActiveTab'] == "ElectronicInfo"){
        profileElectronicAddress($profiles_dal,$login_id);
    }

    if (isset($_SESSION['edit_profiles']['ActiveTab']) && $_SESSION['edit_profiles']['ActiveTab'] == "PhysicalAddress"){
        profilePhysicalAddress($profiles_dal,$login_id);
    }

    if (isset($_SESSION['edit_profiles']['ActiveTab']) && $_SESSION['edit_profiles']['ActiveTab'] == "ApptTimes"){
        profileApptTimes($profiles_dal,$login_id);
    }
    
    if (isset($_SESSION['edit_profiles']['ActiveTab']) && $_SESSION['edit_profiles']['ActiveTab'] == "Services"){
        profileServices($profiles_dal,$login_id);
    }

    if (isset($_SESSION['edit_profiles']['ActiveTab']) && $_SESSION['edit_profiles']['ActiveTab'] == "ProfileAttributes" || !isset($_SESSION['edit_profiles']['ActiveTab']) ) {
        profileLoginSummary($profiles_dal,$login_id);
    }
    ?>
        </div>
    </div>
    <?
    }
      function profileTop($profiles_dal,$login_id) {
       $image_id_data = array ();
       $image_id_data = $profiles_dal->get_default_Profile_ImageID($login_id);
       $profile_data  = $profiles_dal->get_EmployeeDataPerId($login_id);
    ?>
    <div class="bctrt wp100 hp20 d_InlineBlock">
        <div class="f_left wp25 hp100 no-overflow" >
            <? if (isset($image_id_data)) {?>
            <img src="showimage.php?id=<?=$image_id_data[0]->image_id?>&image_db_id=<?=$image_id_data[0]->image_db_id?>&w=150&h=100">
            <? } else { ?>
            <img src="showimage.php?id=0&image_db_id=0&w=150&h=100">
            <? } ?>
        </div>
        <div class="f_left wp50 hp100 s19">
            &nbsp;<?=$profile_data[0]->firstname?> <?=$profile_data[0]->lastname?>
        </div>
        <div class="f_right wp25 hp100 " >
            &nbsp;
        </div>
    </div>
    <?
    }
      function profileLoginTabs($profiles_dal,$login_id){
    $activeTabBackground = "bctrt";
    if (isset($_SESSION['edit_profiles']['ActiveTab']) && $_SESSION['edit_profiles']['ActiveTab'] == "Hours_Report"){
        $Hours_ReportBackground = 'bctrt';
    }    else { $Hours_ReportBackground = ''; }

    if (isset($_SESSION['edit_profiles']['ActiveTab']) && $_SESSION['edit_profiles']['ActiveTab'] == "ElectronicInfo"){
        $ElectronicInfoBackground = 'bctrt';
    }    else { $ElectronicInfoBackground = ''; }

    if (isset($_SESSION['edit_profiles']['ActiveTab']) && $_SESSION['edit_profiles']['ActiveTab'] == "PhysicalAddress"){
        $PhysicalAddressBackground = 'bctrt';
    }    else { $PhysicalAddressBackground = ''; }

    if (isset($_SESSION['edit_profiles']['ActiveTab']) && $_SESSION['edit_profiles']['ActiveTab'] == "ApptTimes"){
        $ApptTimesBackground = 'bctrt';
    }    else { $ApptTimesBackground = ''; }

    if (isset($_SESSION['edit_profiles']['ActiveTab']) && $_SESSION['edit_profiles']['ActiveTab'] == "Services"){
        $ServicesBackground = 'bctrt';
    }    else { $ServicesBackground = ''; }

    if (isset($_SESSION['edit_profiles']['ActiveTab']) && $_SESSION['edit_profiles']['ActiveTab'] == "ProfileAttributes" ||  !isset($_SESSION['edit_profiles']['ActiveTab']) ) {
        $ProfileAttributesBackground = 'bctrt';
    }    else { $ProfileAttributesBackground = ''; }

    ?>
        <div class="wp100 hp05 f_left">
            <div onclick="editProfileLoginTabs('ProfileAttributes');" class="f_left s08 wp16 hp100 mp <?=$ProfileAttributesBackground?>" >Employee Info</div>
            <div onclick="editProfileLoginTabs('ElectronicInfo');" class="f_left s08 wp16 hp100 mp <?=$ElectronicInfoBackground?>" >Electronic Info</div>
            <div onclick="editProfileLoginTabs('PhysicalAddress');" class="f_left s08 wp16 hp100 mp <?=$PhysicalAddressBackground?>" >Physical Address</div>
            <div onclick="editProfileLoginTabs('ApptTimes');" class="f_left s08 wp16 hp100 mp <?=$ApptTimesBackground?>" >Appointment Times</div>
            <div onclick="editProfileLoginTabs('Services');" class="f_left s08 wp16 hp100 mp <?=$ServicesBackground?>" >Services</div>
            <div onclick="editProfileLoginTabs('Hours_Report');" class="f_left s08 hp100 wp16 mp <?=$Hours_ReportBackground?>" >Punch In/Out Data</div>
        </div>
    <?
    }
          function profileLoginSummary($profiles_dal,$login_id){
        $profile_data = $profiles_dal->get_EmployeeDataPerId($login_id)
        ?>
        <div class="wp100 hp75 f_left d_InlineBlock scrolling">
            <div class="wp100 d_InlineBlock ">
                <div class="f_left wp100 bctrt center">Employee Basic Information</div>
            </div>

            <div class="box5">
                <div class="wp100 d_InlineBlock bctrt">
                    <div class="f_left wp20 ">
                        First Name
                    </div>
                    <div class="f_left wp20 ">
                        Last Name
                    </div>
                    <div class="f_left wp20 " >
                        Username
                    </div>
                    <div class="f_left wp20 " >
                        Level
                    </div>
                    <div class="f_left wp20 " >
                        &nbsp;
                    </div>
                </div>
                
                <div class="wp100 h25px d_InlineBlock bclightgray">
                    <div id="profileLoginSummary_firstname" class="bclightgray f_left wp20 hp100">
                        <input type="text" class="w90 " value="<?=$profile_data[0]->firstname?>" id="editProfile_login_firstname">
                    </div>
                    <div id="profileLoginSummary_lastname" class="bclightgray f_left wp20 hp100">
                        <input type="text" class="w90 " value="<?=$profile_data[0]->lastname?>" id="editProfile_login_lastname">
                    </div>
                    <div class="bclightgray f_left wp20 hp100" >
                        <?=$profile_data[0]->username?>
                    </div>
                    <div id="profileLoginSummary_login_level" class="bclightgray f_left wp20 hp100">
                        <? if ( $_SESSION['settings'][$_SESSION['settings']['login_id']]['level'] < 2 ) { $disabled = "disabled";} else { $disabled = ""; } ?>
                        <select <?=$disabled?> class=" dynamic_pannel_search data_control" id="editProfile_login_level">
                                <?
                                foreach ($_SESSION['userlevels'] as $Level => $name){
                                if ($Level == $profile_data[0]->level) { $selected = 'selected';} else {$selected='';}
                                ?>
                                <option value="<?=$Level?>" <?=$selected?>><?=$name?></option>
                                <? unset($selected); } ?>
                        </select>
                    </div>
                    <div class="bclightgray f_left wp05 hp100" >
                        &nbsp;
                    </div>
                    <div class="bclightgray f_left wp15 hp100">
                        <input type="submit" class="button" value="Update" onclick="editProfile_UpdProfileAttributes(<?=$login_id?>)">
                    </div>
                </div>
                
                <div class="wp100 h25px d_InlineBlock">
                    <div class="f_left wp20 hp100 bctrt right s07 ">
                        Employee Quote
                    </div>
                    <div id="profileLoginSummary_employee_quote" class="f_left left bclightgray wp80 hp100 textIndent15">
                        <input type="text" class="w500" value="<?=$profile_data[0]->employee_quote?>" id="editProfile_employee_quote">
                    </div>
                </div>


                <div class="wp100 h70px d_InlineBlock">
                    <div class="f_left wp20 hp100 bctrt right s07 ">
                        Employee Bio
                    </div>
                    <div id="profileLoginSummary_employee_bio" class="f_left left bclightgray wp80 hp100 textIndent15">
                        <textarea class="w500 h65px" id="editProfile_employee_bio"><?=$profile_data[0]->employee_bio?></textarea>
                    </div>

                </div>
            </div>

            <div class="wp100 d_InlineBlock ">
                <div class="f_left wp100" >&nbsp;</div>
            </div>
            <div class="box5">
                <div class="wp100 d_InlineBlock ">
                    <div class="f_left wp100 bctrt center">Security Information</div>
                </div>
                <div class="wp100 d_InlineBlock bclightgray">
                    <div class="f_left wp20 bctrt right s08 lh20">
                        New Password
                    </div>
                    <div class="f_left left  wp60 textIndent15">
                        <div id="profileLoginSummary_password" class="f_left left bclightgray w215 h20px textIndent15">
                            <input type="password" class="w70" id="editProfile_login_password">
                        </div>
                    </div>
                    <div class="bclightgray f_left wp20">
                        <input type="submit" class="button" value="Update" onclick="editProfile_UpdProfilePassword(<?=$login_id?>)">
                    </div>
                </div>
                <div class="wp100 d_InlineBlock ">
                    <div class="f_left wp20 bctrt right lh20">Confirm</div>
                    <div class="f_left left bclightgray wp80 textIndent15">
                    <div id="profileLoginSummary_password2" class="f_left left bclightgray w215 h20px textIndent15">
                        <input type="password" class="w70" id="editProfile_login_password2">
                    </div>
                    </div>
                </div>
            </div>


            <div class="wp100 d_InlineBlock h02px">
                <div class="f_left wp100 " > &nbsp;</div>
            </div>
        <?
            $general_dal = new GENERAL_DAL();
            upload_file_stanza('profile',$general_dal,$login_id,'mainBody');
        ?>
        </div>
<? }
          function profilePhysicalAddress($profiles_dal,$login_id){
            $image_id_data = array ();
            $style = " style=\"text-align: right;\"";
            $bg_color = "#FFFFFF";
            ?>
            <div class="d_InlineBlock wp99 hp75 f_left scrolling">
                <div class="wp100 f_left ">
                    <div class="f_left wp100 bctrt center">Physical Address Information</div>
                </div>
                    <div class="wp100 f_left bctrt">
                        <div class="f_left box0 wp05 ">ID</div>
                        <div class="f_left box0 wp30 ">Address 1</div>
                        <div class="f_left box0 wp15  ">Address 2</div>
                        <div class="f_left box0 wp20  ">City</div>
                        <div class="f_left box0 wp05  ">State</div>
                        <div class="f_left box0 wp10  ">Zip Code</div>
                    </div>
                    <?
                $rows = $profiles_dal->get_addresses_per_login_id($login_id);
                if (count($rows) >0 ) {
                foreach($rows as $row) { ?>
                    <div class="d_InlineBlock wp100 f_left bclightgray">
                        <div class="f_left box0 wp05  ">&nbsp;<?=$row->address_id?></div>
                        <div class="f_left box0 wp30  ">&nbsp;<?=$row->address_line1?></div>
                        <div class="f_left box0 wp15  ">&nbsp;<?=$row->address_line2?></div>
                        <div class="f_left box0 wp20  ">&nbsp;<?=$row->city?></div>
                        <div class="f_left box0 wp05  ">&nbsp;<?=$row->state?></div>
                        <div class="f_left box0 wp10  ">&nbsp;<?=$row->zipcode?></div>
                        <? if ($_SESSION['settings'][$_SESSION['settings']['login_id']]['level'] >=1 ) { ?>
                        <div class="f_left box0 wp10 ">
                            <input type="submit" value="Edit" class="button" onclick="editProfile_EditAddress_setAddressID(<?=$row->address_id?>)">
                        </div>
                        <? } ?>
                    </div>
                    <?
                    if      ( $bg_color == '#DDEEFF')   { $bg_color = "#FFFFFF"; }
                    elseif  ( $bg_color == '#FFFFFF')   { $bg_color = "#DDEEFF"; }
                    }
                }
                else { ?>
                    <div class="wp100 d_InlineBlock ">
                        <div class="f_left box0 wp100 bcwhite">There aren't any addresses added yet.</div>
                    </div>
                <?}?>
                <?
                if (isset($_SESSION['edit_profiles']['edit_address_address_id']) ){
                    $address_data = $profiles_dal->get_address_data_by_address_id($_SESSION['edit_profiles']['edit_address_address_id']);
                } else { $address_data = array(); }
                    if (count($address_data) > 0 )  { $address_line1= $address_data[0]->address_line1 ; } else { $address_line1 = ''; }
                    if (count($address_data) > 0)   { $address_line2= $address_data[0]->address_line2 ; } else { $address_line2 = ''; }
                    if (count($address_data) > 0)   { $city         = $address_data[0]->city ;          } else { $city = ''; }
                    if (count($address_data) > 0)   { $state        = $address_data[0]->state ;         } else { $state = ''; }
                    if (count($address_data) > 0)   { $zipcode      = $address_data[0]->zipcode ;       } else { $zipcode = ''; }
                ?>
                <? if ($_SESSION['settings'][$_SESSION['settings']['login_id']]['level'] >=1 ) { ?>
                <div class="wp100 d_InlineBlock ">
                    <div class="f_left wp100" >&nbsp;</div>
                </div>
                
                    <div class="wp100 d_InlineBlock ">
                        <div class="f_left wp100 bctrt center">Add/Update Address</div>
                    </div>
                <div class="box5">
                    <div class="wp100 h25px d_InlineBlock bclightgray">
                        <div class="f_left  w90 hp100 bctrt right">Address</div>
                        <div id="profileLoginSummary_employee_address1" class="f_left left bclightgray w170 hp100 textIndent15">
                            <input type="text" class="w150" value="<?=$address_line1?>" id="editProfile_employee_address1">
                        </div>
                        <div id="failed_register_message_NU_Address1" class="f_left w300 hp100 bclightgray">&nbsp</div>

                        <? if (isset($_SESSION['edit_profiles']['edit_address_address_id']) ){?>
                        <div class="f_left w80" onclick="editProfile_UpdateAddress(<?=$_SESSION['edit_profiles']['edit_address_address_id']?>)">
                            <input type="submit" class="button red" value="Update">
                        </div>
                        <? } else {?>
                        <div class="f_left w80" onclick="editProfile_AddAddressExistingUser(<?=$login_id?>)">
                            <input type="submit" class="button" value="Add Address">
                        </div>
                        <? }?>
                    </div>

                    <div class="wp100 h25px d_InlineBlock bclightgray">
                        <div class="f_left  w90 bctrt hp100 right">Address 2</div>
                        <div id="profileLoginSummary_employee_address2" class="f_left left bclightgray w170 hp100 textIndent15">
                            <input type="text" class="w150" value="<?=$address_line2?>" id="editProfile_employee_address2">
                        </div>
                        <div class="f_left w310 bclightgray">&nbsp</div>
                    </div>

                    <div class="wp100 h25px d_InlineBlock bclightgray">
                        <div class="f_left bctrt w90 hp100 right">City</div>
                        <div id="profileLoginSummary_employee_city" class="f_left left bclightgray w170 hp100 textIndent15">
                            <input type="text" class="w80" value="<?=$city?>" id="editProfile_employee_city">
                        </div>
                        <div id="failed_register_message_NU_City" class="f_left w310 hp100 bclightgray">&nbsp</div>
                    </div>

                    <div class="wp100 h25px d_InlineBlock bclightgray">
                        <div class="f_left bctrt w90 hp100 right">State</div>
                        <div id="profileLoginSummary_employee_state" class="f_left left bclightgray w170 hp100 textIndent15">
                            <input type="text" class="w20" value="<?=$state?>" id="editProfile_employee_state">
                        </div>
                        <div class="f_left w310 bclightgray">&nbsp</div>
                    </div>
                    <div class="wp100 h25px d_InlineBlock bclightgray">
                        <div class="f_left bctrt w90 hp100 right">Zip</div>
                        <div id="profileLoginSummary_employee_zip" class="f_left left bclightgray w170 hp100 textIndent15">
                            <input type="text" class="w70" value="<?=$zipcode?>" id="editProfile_employee_zipcode">
                        </div>
                        <div id="failed_register_message_NU_ZipCode" class="f_left w310 hp100 bclightgray">&nbsp</div>
                    </div>
                </div>
            </div>
            <? } ?>
        <?
        unset($_SESSION['edit_profiles']['edit_address_address_id']);
        }
          function profileElectronicAddress($profiles_dal,$login_id){
        $profile_data = $profiles_dal->get_EmployeeDataPerLoginId($login_id)
        ?>
        <div class="wp100 hp75 f_left d_InlineBlock scrolling">
            <div class="wp100 d_InlineBlock ">
                <div class="f_left wp100 bctrt center">Electronic Contact Information</div>
            </div>
            <div class="box5">
                <div class="wp100 d_InlineBlock bclightgray">
                    <div class="f_left wp20 bctrt right s07 h20px">
                        Personal Email
                    </div>
                    <div id="profileLoginSummary_email_address" class="f_left left  wp60 h20px textIndent15">
                        <input type="text" class="w200 " value="<?=$profile_data[0]->email_address?>" id="editProfile_login_email_address">
                    </div>
                    <div class="f_left wp20">
                        <input type="submit" class="button" value="Update" onclick="editProfile_UpdElectronicInfo(<?=$login_id?>)">
                    </div>
                </div>
                <div class="wp100 d_InlineBlock ">
                    <div class="f_left bctrt wp20 right">Phone 1</div>
                    <div class="f_left left bclightgray wp80 textIndent15">555-555-1212</div>
                </div>
                <div class="wp100 d_InlineBlock ">
                    <div class="f_left bctrt wp20 right">Phone 1</div>
                    <div class="f_left left bclightgray wp80 textIndent15">555-555-1212</div>
                </div>
                <div class="wp100 d_InlineBlock ">
                    <div class="f_left bctrt wp20 right s07 h20px">Calendar Client</div>
                    <div class="f_left left bclightgray wp80 h20px textIndent15">GMAIL</div>
                </div>
                <div class="wp100 d_InlineBlock ">
                    <div class="f_left bctrt wp20 right s06 h20px">Calendar Username</div>
                    <div id="profileLoginSummary_gmail_username" class="f_left left bclightgray wp80 h20px textIndent15">
                        <input type="text" class="w130 " value="<?=$profile_data[0]->gmail_username?>" id="editProfile_login_gmail_username">
                    </div>
                </div>
                <div class="wp100 d_InlineBlock ">
                    <div class="f_left bctrt wp20 right s06 h20px">Calendar Password</div>
                    <div id="profileLoginSummary_gmail_password" class="f_left left bclightgray wp80 h20px textIndent15">
                        <input type="password" class="w70 " value="<?=$profile_data[0]->gmail_password?>" id="editProfile_login_gmail_password">
                    </div>
                </div>
            </div>
        </div>
        <?
        }
          function profileApptTimes($profiles_dal,$login_id){
            $profile_data   = $profiles_dal->get_EmployeeDataPerLoginId($login_id);
            if ($profile_data[0]->appt_active == 1 ) { $action = 0 ; $activate_deactivate_class = 'bcgreen white'; } else { $action = 1 ; $activate_deactivate_class = 'bcred black'; }
            $style = " style=\"text-align: right;\"";
            $bg_color = "#FFFFFF";
?>          <div class="wp100 hp75 f_left d_InlineBlock scrolling">
                <div class="wp100 d_InlineBlock bctrt">
                    <div class="f_left wp60 right">Appointment Availability</div>
                    <div class="f_left wp35 center ml10 <?=$activate_deactivate_class?>" id="Appt_ActivateDeActivate" onclick="editProfile_Appt_ActivateDeActivate(<?=$login_id?>,<?=$action?>)">OFF/ON</div>
                </div>
                <div class="box5">
                    <?appointment_day($profiles_dal,$login_id,0);?>
                    <?appointment_day($profiles_dal,$login_id,1);?>
                    <?appointment_day($profiles_dal,$login_id,2);?>
                    <?appointment_day($profiles_dal,$login_id,3);?>
                    <?appointment_day($profiles_dal,$login_id,4);?>
                    <?appointment_day($profiles_dal,$login_id,5);?>
                    <?appointment_day($profiles_dal,$login_id,6);?>
                </div>
            </div>
    <?
}
            function appointment_day($profiles_dal,$login_id,$day){
                $general_dal                    = new GENERAL_DAL();
                $appointment_slot_interval_data = $general_dal->get_CompanyPreference_by_Company_ID($_SESSION['settings']['company_id'],'appointment_slot_interval');
                $appointment_slot_interval      = ( count($appointment_slot_interval_data)==0 ) ? 30 : $appointment_slot_interval_data[0]->value;
                
                $width_percent                  = sprintf('%02d',floor($appointment_slot_interval/60 *100));
                $day_of_week[0] = 'Sunday';
                $day_of_week[1] = 'Monday';
                $day_of_week[2] = 'Tuesday';
                $day_of_week[3] = 'Wednesday';
                $day_of_week[4] = 'Thursday';
                $day_of_week[5] = 'Friday';
                $day_of_week[6] = 'Saturday';
            ?>
    <div class="wp100 hp100 d_InlineBlock center">
        <div class="f_left box0 wp07 bclightgray h35px s06">
            &nbsp;<?=$day_of_week[$day]?>
        </div>
        <div class="f_left box0 wp93  bclightgray h35px center">
            <div class="d_InlineBlock f_left wp05 hp100">
                <div class="wp100 hp50">
                    <div class="wp100 hp100 bcwhite">AM</div>
                </div>
                <div class="wp100 hp50">
                    <div class="wp100 hp100 bcwhite">PM</div>
                </div>
            </div>
            <div class="d_InlineBlock f_left wp95 center">
                <div class="d_InlineBlock hp50 wp100 center">
                    <? $hour = 0; while ($hour <= 1100) { ?>
                    <div class="d_InineBlock f_left wp08 hp100 center">
                        <div class=" f_left hp100 wp100 center">
                            <div class="d_InlineBlock f_left hp100 wp100 center">
                                <? $hour = $current_time = $hour ; while ($current_time < ($hour+60)) {?>
                                    <div class="f_left d_InlineBlock center no-overflow wp<?=$width_percent?>"><?appt_time($profiles_dal,$login_id,$day,$current_time,$appointment_slot_interval)?></div>
                                <? $current_time += $appointment_slot_interval ;} ?>
                            </div>
                        </div>
                    </div>
                    <? $hour += 100; } ?>
                </div>

                <div class="d_InlineBlock hp50 wp100 center">
                    <? $hour = 1200; while ($hour <= 2300) { ?>
                    <div class="d_InlineBlock f_left wp08 hp100 center">
                        <div class=" f_left hp100 wp100 center">
                            <div class="d_InlineBlock f_left hp100 wp100 center">
                                <? $hour = $current_time = $hour ; while ($current_time < ($hour+60)) {?>
                                    <div class="f_left center no-overflow wp<?=$width_percent?>"><?appt_time($profiles_dal,$login_id,$day,$current_time,$appointment_slot_interval)?></div>
                                <? $current_time += $appointment_slot_interval ;} ?>
                            </div>
                        </div>
                    </div>
                    <? $hour += 100; } ?>
                </div>
            </div>
        </div>
    </div>
            <?}
            function appt_time($profiles_dal,$login_id,$day_of_week,$time,$appointment_slot_interval){
            $css    = 'wp95 hp100 mp s08 no-overflow ';
            $time   = sprintf("%04d", $time);
            $time   = substr_replace(sprintf('%04d',$time ), ':', -2, 0);
            $display_time = date('g:i a', strtotime($time));
            $action = 1;
            $apptStatusInfo = $profiles_dal->get_apptTimeStatus($login_id,$day_of_week,$time,$appointment_slot_interval);
            $count = count($apptStatusInfo);
            if ($count == 0 || $apptStatusInfo[0]->status == 0) {
                $apptBGcolor = 'bcred';
            } else { $apptBGcolor ='bcgreen';}
            ?>
                <div id="appointmentTime_<?=$day_of_week?>_<?=$time?>" title="<?=$display_time;?>" class="<?=$css?> <?=$apptBGcolor?>" onclick="editProfile_UpdDefaultAppt(<?=$login_id?>,'<?=$time?>', <?=$day_of_week?>, <?=$appointment_slot_interval?>,'<?=$css?>')"><?=date(':i', strtotime($time))?></div>
            <?}
          function profileServices($profiles_dal,$login_id){
            include_once('inventory_management_functions.php');
            $inventory_dal  = new INVENTORY_DAL();
            $Profiles_DAL   = new Profiles_DAL();
            $style = " style=\"text-align: right;\"";
            $bg_color = "#FFFFFF";
            ?>
            <div class="wp100 hp75 f_left d_InlineBlock">
                <div class="wp100 hp05 d_InlineBlock">
                    <div class="f_left wp100 bctrt center">Services Available for booking</div>
                </div>
                <?
                $available_services  = $inventory_dal->ServiceManagement_AllActiveServices($_SESSION['settings']['company_id']);
                $count = $total_service_count = $current_category_id = 0;
                ?>
                <div class="wp100 hp100 d_InlineBlock">
                    <div class="wp100 hp90 box3 scrolling">
                        <div class="f_left hp100 wp100">
                            <?
                                if (count($available_services) > 0 ) {
                                    foreach ($available_services as $service) {
                                        if ( $current_category_id != $service->category_id) {
                                            $current_category_id = $service->category_id;
                                            $count =0;
                                                if ($total_service_count >0 ) {
                                                    ?></div><?
                                                }
                                            ?>
                                            <div class="f_left left s2 wp100 mt10">
                                                <?=$service->category_name?> Category
                                            </div>
                                            <div class="d_InlineBlock center wp100 ">
                                            <?
                                        }
                                        $apptStatusInfo = $Profiles_DAL->get_ServiceStatus_byLoginId($login_id,$service->id);
                                        if ( count($apptStatusInfo) == 0  || $apptStatusInfo[0]->status == 0 ) { $chooseService_Class = 'bcgrey'; } else { $chooseService_Class ='bcgreen white'; }
                                        if ( $apptStatusInfo[0]->employee_price != 0 ) {
                                            $strikethrough  = 'strikethrough'; 
                                            $employee_price = money2($apptStatusInfo[0]->employee_price);
                                        } else { 
                                            $strikethrough = ''; 
                                            $employee_price = '&nbsp;';
                                        }
                                        ?>
                                        <div class="d_InlineBlock f_left wp31 box3 bctrt">
                                            <div class="f_left wp100">
                                                <div class="f_left wp20 left no-overflow <?=$strikethrough?> s08" id="login_service_default_price_<?=$service->id?>"><?=money2($service->price)?></div>
                                                <div class="f_left wp15 left no-overflow s07"                     id="login_service_employee_price_<?=$service->id?>"><?=$employee_price?></div>
                                                <div class="f_left wp55 center no-overflow"><input type="submit" id="ChooseService_<?=$service->id?>" onclick="editProfile_ServiceActivateDeActivate(<?=$service->id?>,<?=$login_id?>);" value="<?=$service->name?>" class="<?=$chooseService_Class?> CENTER" title="Click to choose this service."></div>
                                                <div class="f_left wp10 center"><a href="" onclick="Inventory_Items_Edit_Service(<?=$service->id?>)">Edit<a></div>
                                            </div>
                                            <div class="f_left wp100 h60px bcwhite text_OverFlow_ellipsis scrolling" title="<?=$service->style?>">
                                                <div class="f_left wp20 center no-overflow">
                                                    <div class="d_InlineBlock center hp25 s07">
                                                        Your Price
                                                    </div>
                                                    <div class="d_InlineBlock center hp35">
                                                    <input type="text" 
                                                           class="center wp75 "
                                                           id="employee_price_<?=$service->id?>" 
                                                           value="<?=$employee_service_data->employee_price?>"
                                                    >
                                                    </div>
                                                    
                                                    <div class="d_InlineBlock center hp35">
                                                    <input type="submit" 
                                                           onclick="editProfile_employee_price(<?=$service->id?>,<?=$login_id?>);" 
                                                           value="Set"
                                                    >
                                                    </div>                                                                                                   
                                                </div>
                                                <div class="f_left wp80 left s07 no-overflow center">
                                                    ~ <?=$service->est_time_mins?> minutes. - <?=$service->style?>                                                    
                                                </div>
                                            </div>
                                        </div>
                                        <div class="f_left wp01">&nbsp;</div>
                                        <?
                                    $count++;
                                if ($count == 3) { $count =0; ?></div> <div class="d_InlineBlock wp100">
                            <? }
                            $total_service_count++;
                            } ?>
                            </div>
                    </div>
                        <? } else { ?>
                            <div class="f_left wp90 no-overflow">
                                <div>Unfortunately there are not any active services to choose from at this time.</div>
                            </div>
                        <?} ?>
                        </div>
                    </div>
            </div>
    <?
}
          function profileHours_Report($profiles_dal,$login_id){?>
            <div class="wp100 hp75 f_left d_InlineBlock scrolling" style="min-height: 100px; height: 300px;">
              <? profiles_clock_in_out_report($login_id,0); ?>
            </div>
          <?}
    function userAddStanza($profiles_dal) {
    ?>
    <div class="profileBodyDataContainer wp95">
        <div class="profileBodyCenter wp100">
            <?= userAdd($profiles_dal);?>
        </div>
    </div>
    <?
    }
        function userAdd($profiles_dal) {
    ?>
    <div class="wp100 d_InlineBlock">
        <div id="registerPanel" class="d_InlineBlock wp95">
                <div class="d_InlineBlock wp100 bctrt">
                    <div class="wp100 f_left left ml10 bctrt">
                        New Employee Basic Information
                    </div>
                </div>
                <div class="d_InlineBlock wp100 box5">
                    <div class="wp47 f_left bctrt box5">
                        <div class="d_InlineBlock wp100">
                            <div class="f_left left wp20">First Name</div>
                            <div class="f_left wp30">
                                <input class="wp90" type="text" id="NU_first_name" value="">
                            </div>
                            <div class="d_InlineBlock wp50 f_left red s07 no-overflow" id="failed_register_message_NU_first_name">
                                &nbsp;
                            </div>
                        </div>
                        <div class="d_InlineBlock wp100">
                            <div class="f_left left wp20">Last Name</div>
                            <div class="f_left wp30">
                                <input class="wp90" type="text" id="NU_last_name" value="">
                            </div>
                            <div class="d_InlineBlock wp50 f_left red s07 no-overflow" id="failed_register_message_NU_last_name">
                                &nbsp;
                            </div>
                        </div>
                        <div class="d_InlineBlock wp100">
                            <div class="f_left left wp20">Email</div>
                            <div class="f_left wp30">
                                <input class="wp90" type="text" id="NU_user_email" value="">
                            </div>
                            <div class="d_InlineBlock wp50 f_left red s07 no-overflow" id="failed_register_message_NU_user_email">
                                &nbsp;
                            </div>
                        </div>
                        <div class="d_InlineBlock wp100">
                            <div class="f_left left wp20">Phone Number</div>
                            <div class="f_left wp30">
                                <input class="wp90" type="text" id="NU_phone_num" value="">
                            </div>
                            <div class="d_InlineBlock wp50 f_left red s07 no-overflow" id="failed_register_message_NU_phone_num">
                                &nbsp;
                            </div>
                        </div>
                        <div class="d_InlineBlock wp100">
                            <div class="f_left left wp20">Login Name</div>
                            <div class="f_left wp30">
                                <input class="wp90" type="text" id="NU_login_name" value="">
                            </div>
                            <div class="d_InlineBlock wp50 f_left red s07 no-overflow" id="failed_register_message_NU_login_name">
                                &nbsp;
                            </div>
                        </div>
                        <div class="d_InlineBlock wp100">
                            <div class="f_left left wp20" id="div_NU_password">Password</div>
                            <div class="f_left wp30">
                                <input class="wp90" type="password" id="NU_password" value="">
                            </div>
                            <div class="d_InlineBlock wp50 f_left red s07 no-overflow" id="failed_register_message_NU_password">
                                &nbsp;
                            </div>
                        </div>
                        <div class="d_InlineBlock wp100">
                            <div class="f_left left wp20 s07" id="div_NU_password_confirm">Confirm Password</div>
                            <div class="f_left wp30">
                                <input class="wp90" type="password" id="NU_password_confirm" value="">
                            </div>
                            <div class="d_InlineBlock wp50 f_left red s07 no-overflow" id="failed_register_message_NU_password_confirm">
                                &nbsp;
                            </div>
                        </div>
                    </div>
                    <div class="wp47 f_right bctrt box5">
                        <div class="d_InlineBlock wp100">
                            <div class="f_left left wp20">Address</div>
                            <div class="f_left wp30">
                                <input class="wp90" type="text" id="NU_Address" value="">
                            </div>
                            <div class="d_InlineBlock wp50 f_left red s07 no-overflow" id="failed_register_message_NU_Address">
                                &nbsp;
                            </div>
                        </div>
                        <div class="d_InlineBlock wp100">
                            <div class="f_left left wp20">City</div>
                            <div class="f_left wp30">
                                <input class="wp90" type="text" id="NU_City" value="">
                            </div>
                            <div class="d_InlineBlock wp50 f_left red s07 no-overflow" id="failed_register_message_NU_City">
                                &nbsp;
                            </div>
                        </div>
                        <div class="d_InlineBlock wp100">
                            <div class="f_left left wp20 s07">State/Province</div>
                            <div class="f_left wp30" >
                                <select style="max-width:120px;" name="NU_State" id="NU_State">
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
                            <div class="d_InlineBlock wp50 f_left red s07 no-overflow" id="failed_register_message_NU_State">
                                &nbsp;
                            </div>
                        </div>
                        <div class="d_InlineBlock wp100">
                            <div class="f_left left wp20">Country</div>
                            <div class="f_left wp30">
                                <select name="NU_Country" id="NU_Country">
                                <option value="">- Select -</option>
                                <option value="USA" selected>United States</option>
                                <option value="ZWE">Zimbabwe</option>
                                </select>
                            </div>
                            <div class="d_InlineBlock wp50 f_left red s07 no-overflow" id="failed_register_message_NU_Country">
                                &nbsp;
                            </div>
                        </div>
                        <div class="d_InlineBlock wp100">
                            <div class="f_left left wp20 s07">Zip/Postal Code</div>
                            <div class="f_left wp30">
                                <input class="wp90" type="text" id="NU_PostalCode" value="">
                            </div>
                            <div class="d_InlineBlock wp50 f_left red s07 no-overflow" id="failed_register_message_NU_PostalCode">
                                &nbsp;
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d_InlineBlock wp100">
                    <div class="center">
                            <input onclick="editProfile_AddNewUser()" type="submit" value="Add New Employee" class="button buttonMargin" tabindex="3"/>
                    </div>
                </div>
                <div class="d_InlineBlock wp100" id="failed_NewUser_message">
                    &nbsp;
                </div>
        </div>
    </div>
    <?
    }

function ProfilesSearchStanza() {
$reportType = 'Profiles_AllProfiles';
?>
    <div id="item_SearchStanza" class="d_InlineBlock hp100 wp100">
        <div class="wp95 hp100 d_InlineBlock">
            <?=profiles_search_div('first_name','text',$reportType,09)?>
            <?=profiles_search_div('last_name','text',$reportType,09)?>
            <?=profiles_search_div('email','text',$reportType,09)?>
            <?=profiles_search_div('phone_number','text',$reportType,09)?>
            <?=profiles_search_div('miscellaneous','checkbox',$reportType,11)?>
            <?=profiles_search_div('submit','checkbox',$reportType,08)?>
        </div>
    </div>
<?
}
    function profiles_search_div($search_by_field,$data_type,$reportType,$height_percent){
    if (isset($_SESSION['search_data']['profile_search']['profile_search_inactive_profile']) && $_SESSION['search_data']['profile_search']['profile_search_inactive_profile'] == 1)   { $inactive_profiles_checked = "checked"; } else {$inactive_profiles_checked = "";}
    ?>
            <div class="d_InlineBlock mb5 bctrt wp100 hp<?=$height_percent?>" >
                <?       if ($search_by_field == 'first_name' || $search_by_field == 'last_name' || $search_by_field == 'email' || $search_by_field == 'phone_number' ) { ?>
                    <div class="f_left wp100 hp40">
                        &nbsp;<?=ucfirst($search_by_field)?>
                    </div>
                    <div class="f_left wp100 hp60">
                        <input
                            class="wp90"
                            type="text"
                            maxlength="50"
                            size="15"
                            id="dynamic_pannel_<?=$search_by_field?>"
                            placeholder="<?=$search_by_field?>"
                            x-webkit-speech>
                    </div>
                <? } elseif  ( ($search_by_field == 'miscellaneous' ) ) { ?>
                    <div class="f_left wp100 hp50">
                        &nbsp;<?=ucfirst($search_by_field)?>
                    </div>
                    <div class="d_InlineBlock f_left wp100 hp50">
                        <div class="f_left wp100 hp100">
                            <div class="f_left right wp85 hp100 s06">InActive Employees</div>
                            <div class="f_left wp15 hp100"><input type='checkbox' id="dynamic_pannel_inactive_profiles" value='1' onclick="Profiles_Search_searchBy('<?=$reportType?>');" <?=$inactive_profiles_checked?> ></div>
                        </div>
                    </div>
                <? } elseif  ( ($search_by_field == 'submit' ) ) { ?>
                    <div class="f_left hp100 wp100">
                        <input class="button s08 wp90" type="submit" value="Search" onclick="Profiles_Search_searchBy('<?=$reportType?>');">
                    </div>
                <? } ?>
            </div>
    <?}