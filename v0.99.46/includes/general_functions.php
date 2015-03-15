<?php
require_once('permissions.php');
require_once('dbConfig.php');
require_once('shop.php');

$_SESSION['userlevels'] = array(0 => "Employee",
                                1 => "Asst. Mgr",
                                2 => "Manager",
                                3 => "Administrator");
class DALQueryResult{
  private $_results = array();
  public function __construct(){}
  public function __set($var,$val){
    $this->_results[$var] = $val;
  }
  public function __get($var){
    if (isset($this->_results[$var])){
	  return $this->_results[$var];
    }
    else {
	  return null;
	}
  }
}
class GENERAL_DAL{
var $main_bc_color1;
var $main_bc_color1_light;
var $main_bc_color2;
var $main_bc_color2_light;
var $main_bc_color3;
var $main_bc_color3_light;
var $main_color1_light_text;
var $main_color1_text;
var $main_color2_light_text;
var $main_color2_text;
var $main_color3_light_text;
var $main_color3_text;
 
  public function __construct(){}

  public function get_company_info_by_hostNdomain($host,$domain){
    #      print $host . "\n";
    $sql = "SELECT c.id, c.domain, c.subdomain, c.templateNumber, HTTP_tt.templateName, c.defaultPOS
    from  companies c
    join HTTPtemplateTypes HTTP_tt on HTTP_tt.id = c.templateType
    join
    where c.domain = '$domain' and c.subdomain = '$host'";
    #print $sql . "<br>\n";
    return $this->query($sql);
  }
  public function get_company_info_by_host($host,$domain){
    #      print $host . "\n";
    $sql = "SELECT c.id, c.domain, c.subdomain, c.templateNumber, HTTP_tt.templateName, c.defaultPOS
    from  companies c
    join HTTPtemplateTypes HTTP_tt on HTTP_tt.id = c.templateType
    where c.subdomain = '$host'";
    #print $sql . "<br>\n";
    return $this->query($sql);
  }
  public function get_company_info_by_domain($host,$domain){
    $sql = "SELECT c.id, c.domain, c.subdomain, c.templateNumber, HTTP_tt.templateName, c.defaultPOS
    from  companies c
    join HTTPtemplateTypes HTTP_tt on HTTP_tt.id = c.templateType
    where c.domain = '$domain'";
    #print $sql . "<br>\n";
    return $this->query($sql);
  }
  public function get_company_info_by_Host_or_Domain($subdomain,$domain){
        $company_id_sql = "SELECT id, db_version from companies ";
        if ($subdomain == 'www') {
            $company_id_sql .= "where domain       = '$domain'    and deleted is null order by id desc";            
        } else {
            $company_id_sql .= "where subdomain    = '$subdomain' and deleted is null order by id desc";            
        }
    #print $company_id_sql . "<br>\n";
    return $this->query($company_id_sql);
  }
  
  public function get_Company_TemplateData_by_Company_ID($company_id) {
    $sql = "SELECT tt.name,ttd.value
        from templateTabsData ttd
        join templateTabs tt on tt.id=ttd.templateTabId
        where ttd.status = 1 and company_id = $company_id";
    #echo $sql;
    return $this->query($sql);
  }
  public function get_Company_Preference($company_id,$name){
    $sql = "SELECT name,value
            from preferences where name='$name' and company_id = $company_id";
    #echo $sql;
    return $this->query($sql);      
  }
  public function get_CompanyPreference_by_Company_ID($company_id,$preference_name=0){
      $sql = "SELECT name,value from preferences where company_id = $company_id";
      if ($preference_name !== 0 ) {
          $sql.=" and name = '$preference_name'";
      }
      #print $sql." - ".$preference_name;
    return $this->query($sql);
  }
  public function get_all_sales($company_id,$totals){
        if ($totals == 1) {
            $sql ="SELECT count(s.id) as count ";
        }
        ELSE {
            $sql ="SELECT s.id, s.login_id, s.paid, l.username as username, s.customer_id, s.receipt_id,s.added ";
        }
        $sql .= " FROM sales s
        left join logins l on s.sales_person_id = l.id
        where s.company_id = $company_id";
        if ( isset($_SESSION['search_data']['dynamic_pannel_start_date']) &&  $_SESSION['search_data']['dynamic_pannel_start_date']  != 'null' && $_SESSION['search_data']['dynamic_pannel_start_date'] != '' && $_SESSION['search_data']['dynamic_pannel_start_date'] != 'undefined' ) { $sql .= " and s.added >= '" .$_SESSION['search_data']['dynamic_pannel_start_date']."' "; }
        if ( isset($_SESSION['search_data']['dynamic_pannel_end_date']) &&  $_SESSION['search_data']['dynamic_pannel_end_date']  != 'null' && $_SESSION['search_data']['dynamic_pannel_end_date'] != '' && $_SESSION['search_data']['dynamic_pannel_end_date'] != 'undefined' ) { $sql .= " and s.added <= '" .$_SESSION['search_data']['dynamic_pannel_end_date']."' "; }

        if ( isset($_SESSION['search_data']['dynamic_pannel_id_search']) && $_SESSION['search_data']['dynamic_pannel_id_search']  != '') { $sql .= " and s.id = " .$_SESSION['search_data']['dynamic_pannel_id_search']." "; }
        if ( isset($_SESSION['search_data']['dynamic_pannel_customer_search'])) { }
        if ( isset($_SESSION['search_data']['dynamic_pannel_taxcat_name']) && ( $_SESSION['search_data']['dynamic_pannel_taxcat_name'] == 0 ) ) { $sql .= " and s.taxed = 0" . " "; }
        if ( isset($_SESSION['search_data']['dynamic_pannel_employee_username']) && $_SESSION['search_data']['dynamic_pannel_employee_username']  != '' && $_SESSION['search_data']['dynamic_pannel_employee_username']  != '-1') { $sql .= " and s.sales_person_id in (select id from logins where username = '" .$_SESSION['search_data']['dynamic_pannel_employee_username']."' ) "; }
        $sql .= " order by s.added desc";
        if ($totals == 0) {
            if ( $_SESSION['search_data']['paging_page'] == 1) { $limit_offset = 0; }
            else { $limit_offset = ( ($_SESSION['search_data']['paging_page'] - 1) * 20 ) + 1 ; }
            $sql .= " limit $limit_offset,20";
        }
        #if ($totals == 0 ) { print "$totals <br> $sql"; }
    return $this->query($sql);
  }
  public function get_AllEmployeesPerCompanyId($company_id,$active=0,$appt_active=0){
    $sql = "SELECT l.id,username,firstname,lastname,level,l.status,employee_quote,added,email_address,gmail_username,gmail_password,sum(ls.status) as count
        from logins l
        left join logins_services ls on l.id = ls.login_id
        where deleted is NULL and
        company_id = $company_id and
        username not in ('admin','employee','manager')";
        if ($active == 1 ) {
            $sql.= " and l.status = 1 ";
        }
        if ($appt_active == 1 ) {
            $sql.= " and appt_active = 1 ";
        }
        $sql .= "   group by l.id ";
        $sql .= "   order by l.id desc ";
    #print $sql;
    return $this->query($sql);
  }

  public function get_All_TimeZones(){
      $sql = "SELECT * FROM mysql.time_zone_name where Name like 'America/%'";
      return $this->query($sql);
  }

  public function get_SupplierInfoPerSupplierId($supplier_id){
    $sql = "SELECT name as supplier_name from suppliers where id = $supplier_id";
    return $this->query($sql);
    print $sql;
  }
  public function get_StyleNumbersByCompanyID($company_id){
    $sql = "SELECT 
        i.number,
        sum(i.quantity) as quantity
        from items as i
        join suppliers as s on s.id = i.supplier_id
        join brands as b on i.brand_id = b.id
        where s.company_id = b.company_id 
        and s.company_id = $company_id
        and coalesce(s.deleted, b.deleted, i.deleted) is null
        group by i.number
        order by i.number asc, b.name asc, i.name asc, i.attribute1 asc, i.attribute2 asc";
    return $this->query($sql);
    #print $sql;
  }
  public function get_CategoriesByCompanyID($company_id){
    $sql = "SELECT
        c.id,
        c.name,
        sum(i.quantity) as quantity
        from items as i
        join suppliers as s on s.id = i.supplier_id
        join categories as c on c.id = i.category_id
        join brands as b on i.brand_id = b.id
        where s.company_id = b.company_id
        and s.company_id = $company_id
        and coalesce(c.deleted, s.deleted, b.deleted, i.deleted) is null
        group by c.id
        order by c.name asc";
    return $this->query($sql);
    #print $sql;
  }

  public function get_StyleNumbersBySupplierIDandCompanyID($supplier_id,$company_id){
    $sql = "SELECT distinct i.number
        from items as i
        join suppliers as s on s.id = i.supplier_id
        join brands as b on i.brand_id = b.id
        where s.id = $supplier_id and
        s.company_id = b.company_id
        and s.company_id = $company_id
        and coalesce(s.deleted, b.deleted, i.deleted) is null
        order by i.number asc, b.name asc, i.name asc, i.attribute1 asc, i.attribute2 asc";
    return $this->query($sql);
    #print $sql;
  }
  public function get_last_date_sold($item_id){
    $sql = "SELECT max(s.added) as last_date_sold from sales s inner join sale_items si on s.id = si.sale_id where si.item_id = $item_id";
    #print "$sql";
    return $this->query($sql);
  }
  public function get_AppointmentHistory($customer_id,$limit_count=10){
    $sql = "SELECT appts.id, appts.startDate, appts.endDate, appts.status, appts.deleted_by_type, appts.deleted_by_id,
            l.firstname, l.lastname, 
            count(appt_s.id) as services_count
      from appointments appts
      join logins l on appts.login_id = l.id
      join appointments_services appt_s on appts.id = appt_s.appointment_id
      where appts.customer_id = $customer_id
      group by appts.id
      order by appts.startDate desc";
      if ($limit_count != 0 ) {
          $sql .= " limit $limit_count;";
      } 
    #print "$sql";
    return $this->query($sql);
  }
  public function get_TotalCompletedAppointments($customer_id){
      $sql = "SELECT count(appts.id) as count
      from appointments appts
      where appts.customer_id = $customer_id and status in (0) and convert_tz(appts.startDate, 'utc', ".quoteSmart($_SESSION['preferences']['timezone']).") < convert_tz(now(), 'utc', 'america/chicago')
      ";
    #print "$sql";
    return $this->query($sql);
  }

  public function get_EventData_byID($event_id){
    $sql = "SELECT login_id,ThirdPartyApptEventId,ThirdPartyApptVendor from appointments where id = $event_id";
    #print "$sql";
    return $this->query($sql);
  }
  public function get_Active_Future_Appointments_byCustomerID($customer_id){
      $sql="SELECT count(id) as count from appointments where startDate > convert_tz(now(), 'utc', ".quoteSmart($_SESSION['preferences']['timezone']).") and status = 0 and customer_id = $customer_id";
      #print $sql;
      # convert_tz(s.startDate, 'utc', ".quoteSmart($_SESSION['preferences']['timezone'])."
      return $this->query($sql);
  }
  public function check_loginExists($login_id,$company_id){
      $sql = "SELECT count(id) as count from logins where username = '$login_id' and company_id = $company_id";
      return $this->query($sql);
  }
  public function check_preferenceExist($company_id,$column){
      $sql = "SELECT count(company_id) as count from preferences where company_id = $company_id and name='$column'";
      #echo $sql;
      return $this->query($sql);
  }

  public function get_ImageTypeFunctionData($image_type){
      $sql = "SELECT php_reload_function,php_reload_include from image_types where type_name = '$image_type'";
      #echo $sql;
      return $this->query($sql);
  }
  public function get_ImageDatabasesInfo(){
      $sql = "SELECT id,hostname, databasename, username, password from image_dbs ";
      #echo $sql;
      return $this->query($sql);
  }
  public function get_ImageCount_Per_ImageDB($image_db_id){
      $sql = "SELECT count(id) as count from item_image_mappings where image_db_id = $image_db_id ";
      #echo $sql;
      return $this->query($sql);
  }
  public function get_ImageGroupImage_by_StyleNumber_N_CompanyID($company_id,$style_number){
      $sql = "SELECT iim.image_id,iim.image_db_id from items i left join item_image_mappings iim on i.id = iim.id where i.deleted is NULL and iim.default_group_image = 1 and i.number = '$style_number' and i.company_id = $company_id;";
      #echo $sql;
      return $this->query($sql);
  }

  public function get_ImageDatabaseInfo_by_ImageDatabaseID($image_db_id){
      $sql = "SELECT hostname, databasename, username, password from image_dbs where id = $image_db_id ";
      #echo $sql;
      return $this->query($sql);
  }

  public function get_ImageInfo_by_ImageID_and_image_db_id($image_id,$image_db_id){
      $sql = "  SELECT iim.width, iim.height,
                        image_dbs.hostname, image_dbs.databasename, image_dbs.username, image_dbs.password
                FROM item_image_mappings iim
                join image_dbs on image_dbs.id = iim.image_db_id
                where image_id = $image_id and image_db_id = $image_db_id";
      #echo $sql;
      return $this->query($sql);
  }
  public function get_all_images_BY_ImageTypeId_and_column_name($ImageTypeId,$column_name){
      $sql = "SELECT iim.image_id, iim.image_db_id, iim.image_name, iim.size, iim.mime, iim.width, iim.height, iim.default, iim.default_group_image, iim.default_item_image, it.type_name, i.company_id, i.number as style_number
                from item_image_mappings iim
                join image_types it on it.id = iim.image_type_id
                left join items i on i.id = iim.id
                where iim.id        =  $ImageTypeId and
                      it.type_name  = '$column_name' and
                iim.deleted is null
                order by iim.added asc
                ";
    #print "$sql";
    return $this->query($sql);
  }
  public function item_typeId_by_type($column_name){
        $sql = "SELECT id from image_types where type_name = '$column_name'";
    #print "$sql";
    return $this->query($sql);
  }
  public function item_ImageCount_by_ID_and_TypeId($source_record_id,$type_id){
      $sql = "SELECT id from item_image_mappings where id = $source_record_id and image_type_id = $type_id and deleted is NULL";
    #print "$sql";
    return $this->query($sql);
    }

  public function appointment_checkApptAvailable($login_id, $startDate,$endDate){
      $sql = "SELECT id from appointments where login_id = $login_id and startDate ='$startDate' and startDate <= '$endDate' and status != 1";
    #print "$sql";
    return $this->query($sql);
  }
  public function appointment_EnoughTimeResults($login_id, $appointment_check_StartDate,$selected_date,$when_appt_would_end){
      $sql = "SELECT id,startDate 
                from appointments 
                where login_id = " . $login_id . 
              "  and startDate > '" . $appointment_check_StartDate . 
              "' and startDate < '" . $selected_date . " " . $when_appt_would_end . 
              "' and status != 1";
    #print "$sql";
    return $this->query($sql);
  }
  public function appointment_getAppointmentInfo_BY_ID($appointment_id){
      $sql = "SELECT company_id,login_id,add_staff_id,customer_id,status,insert_date,ThirdPartyApptEventId,deleted_by_type,deleted_by_id  from appointments where id = $appointment_id";
    #print "$sql";
    return $this->query($sql);
  }
  public function appointment_Check_if_Appt_Start_Booked($login_id,$appointment_check_StartDate,$appointment_check_EndDate){
      $sql = "SELECT id,startDate
          from appointments
          where login_id = " . $login_id . " and
              ( startDate >= '" . $appointment_check_StartDate . "' and startDate <= '" . $appointment_check_EndDate . "' ) and
              status != 1";
    #print "$sql";
    return $this->query($sql);
  }  
  public function appointment_Check_if_Appt_Span_Booked($login_id,$appointment_check_StartDate,$appointment_check_EndDate){
      $sql = "SELECT id,startDate
          from appointments
          where login_id = " . $login_id . " and
              ( startDate <= '" . $appointment_check_StartDate . "' and endDate >= '" . $appointment_check_EndDate . "' ) and
              status != 1";
    #print "$sql";
    return $this->query($sql);
  }
  public function appointment_Check_if_Appt_End_Booked($login_id,$appointment_check_StartDate,$appointment_check_EndDate){
      $sql = "SELECT id,startDate
          from appointments
          where login_id = " . $login_id . " and
              (endDate >= '" . $appointment_check_StartDate . "' and endDate <= '" . $appointment_check_EndDate . "' ) and
              status != 1";
    #print "$sql";
    return $this->query($sql);
  }
  public function appointment_Get_LoginAvailibility_based_on_DaysOff_Table($login_id,$date){
      $sql = "SELECT id from logins_days_off where date = '$date' and login_id = $login_id and status = 1";
      #print $sql;
      return $this->query($sql);
  }
  public function appointment_Get_AppointmentCount_based_on_profileID_and_Date($login_id,$date){
      $sql = "SELECT id from appointments where startDate like '$date%' and login_id = $login_id and status = 0 ";
      #print $sql;
      return $this->query($sql);      
  }
  public function appointment_GetCompanysForReminderEmails(){ 
      $sql = "SELECT c.id, c.name, p.value as timezone
                from companies c
                join preferences p on c.id = p.company_id
                where p.name    = 'timezone' and
                time(convert_tz(now(), 'utc', p.value)) like '00:00%'";
      #print $sql;
      return $this->query($sql);        
  }
  public function appointment_GetAppointmentsToReceiveReminderemails($company_id,$timezone){
    $sql = "SELECT a.status, a.id, a.company_id, a.customer_id, a.login_id, a.startDate, a.endDate
      from appointments a
      right join customers c on a.customer_id = c.id
      where
            date(a.startDate) = date(DATE_ADD(convert_tz(now(), 'utc', '$timezone'),INTERVAL 1 DAY)) and
            a.company_id      = $company_id and
            a.status          = 0;";
    #print $sql;
    return $this->query($sql);
    }
  public function appointment_GetLoginAvailibility_based_on_Open_Slot_Table($staff_id,$selected_date,$selected_time,$appointment_slot_interval){
    $sql = "SELECT id 
            from logins_appt_slots_off 
            where date(date) = '$selected_date' and 
                appt_slot = $selected_time 
                and appointment_slot_interval = $appointment_slot_interval 
                and login_id = $staff_id 
                and status = 1";
    #print $sql;
    return $this->query($sql);
  }
  public function get_LoginAvailibility_based_on_Off_table_and_EndTime($staff_id,$selected_date,$selected_time,$timeslot_appt_would_end,$appointment_slot_interval){
    $sql = "SELECT id 
            from logins_appt_slots_off 
            where date(date) = '$selected_date' and 
                appt_slot > $selected_time and
                appt_slot <= $timeslot_appt_would_end 
                and appointment_slot_interval = $appointment_slot_interval 
                and login_id = $staff_id 
                and status = 1";
    #print $sql;
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
class IMAGE_DATA_DAL{
  public function __construct($hostname='',$username='',$password='',$databasename=''){
            if ($hostname == ''      ) { $this->hostname    = $_SESSION['MYSQL_HOST'];}         else {$this->hostname       = $hostname;}
            if ($username == ''      ) { $this->username    = $_SESSION['MYSQL_USER'];}         else {$this->username       = $username;}
            if ($password == ''      ) { $this->password    = $_SESSION['MYSQL_PASS'];}         else {$this->password       = $password;}
            if ($databasename == ''  ) { $this->databasename= $_SESSION['MYSQL_DATABASE'];}     else {$this->databasename   = $databasename;}
  }
  public function get_ImageData_byID($image_id){
      $sql = "SELECT image, width, height from images where id = $image_id";
      #echo $sql;
      return $this->query($sql);
  }
  public function get_Imagedata_byPreferenceName_AND_CompanyID($company_id,$preference_name){
    $sql = "SELECT iim.image_id,iim.image_db_id 
            from item_image_mappings iim 
            join image_types it on it.id = iim.image_type_id 
            where iim.default = 1 and it.type_name = '$preference_name' and iim.id = $company_id and iim.deleted is null";
    #echo $sql;
    return $this->query($sql);
}
  public function get_Imagedata_byPreferenceName_AND_CompanyID_rotating($company_id,$preference_name){
    $sql = "SELECT iim.image_id,iim.image_db_id 
            from item_image_mappings iim 
            join image_types it on it.id = iim.image_type_id 
            where iim.default = 0 and it.type_name = '$preference_name' and iim.id = $company_id and iim.deleted is null";
    #echo $sql;
    return $this->query($sql);
}
  function dbconnect(){
    $conn = mysql_connect( $this->hostname,$this->username, $this->password) or die ("<br/>Host: $this->hostname, User: $this->username, Pass: $this->password - Cουld not connect tο MySQL server sir. IMAGE_DATA_DAL/private function dbconnect");
    mysql_select_db($this->databasename,$conn) or die ("<br/>Cουld nοt select the indicated database Database:{$this->databasename} @{$this->$hostname}");
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
class InsertUpdateDelete_DAL{
	protected $mysql;
	function __construct($hostname='',$username='',$password='',$databasename='') {
            if ($hostname == ''      ) { $this->hostname = $_SESSION['MYSQL_HOST'];}            else {$this->hostname  = $hostname;}
            if ($username == ''      ) { $this->username = $_SESSION['MYSQL_USER'];}            else {$this->username  = $username;}
            if ($password == ''      ) { $this->password = $_SESSION['MYSQL_PASS'];}            else {$this->password  = $password;}
            if ($databasename == ''  ) { $this->databasename = $_SESSION['MYSQL_DATABASE'];}    else {$this->databasename  = $databasename;}
            if ( !is_resource($this->mysql) ) {
                    $this->mysql = mysql_connect($this->hostname, $this->username, $this->password )or $this->error();
                    mysql_select_db( $this->databasename, $this->mysql ) or $this->error();
            }
	}

        private function error() {
		return printf( '<b>MySQL ERROR:</b> %s %s %s %s %s (%d)', $this->hostname, $this->username, $this->password, $this->databasename, mysql_error(), mysql_errno() );
        }
	public function fetch_array( $query ) {
		$mysql_query = mysql_query( $query, $this->mysql );
		while( $result = mysql_fetch_array( $mysql_query, MYSQL_ASSOC ) ) {
			$return[] = $result;
		}
		return $return;
	}
	public function query( $query ) {
		return mysql_query( $query, $this->mysql );
	}
        public function insert_query( $query ) {
		mysql_query( $query, $this->mysql ) or $this->error();
                $id = mysql_insert_id($this->mysql);
                return $id;
	}
	public function debug( $log ) {
		mysql_query( "INSERT INTO `debug` (`debug_id`, `debug_text`, `debug_timestamp`) VALUES ( '', '{$log}', '". date("m.d.y, h:i a") ."' )", $this->mysql );
	}
}

function Company_SetTemplateData_by_Company_ID($Company_ID) {
$general_dal  = new GENERAL_DAL();
$TemplateData = $general_dal->get_Company_TemplateData_by_Company_ID($Company_ID);
    foreach ($TemplateData as $TemplateDataRow) {
        $_SESSION['company_info'][$_SESSION['settings']['company_id']][$TemplateDataRow->name] = $TemplateDataRow->value;
    }
}
function set_preference_session($company_id){
    unset($_SESSION['preferences']);
    $general_dal = new GENERAL_DAL();
    $company_preferences = $general_dal->get_CompanyPreference_by_Company_ID($company_id);
    foreach ($company_preferences as $company_preference) {
            $_SESSION['preferences'][preg_replace('/ /', '_', $company_preference->name)] = $company_preference->value;
    }
}
function Company_Setup_Company($general_dal,$host,$domain,$http_host){
if (    $domain == "is-a-chef.com" || $domain == "system101.com" || $domain == "dubbsenterprises.com") {
    $companies=     $general_dal->get_company_info_by_host($host,$domain);
        if (count($companies) != 0 ) {
            list($defaultPOS,$posurl,$template_function,$include_file) = Company_Setup_Company_Variables($general_dal,$host,$domain,$http_host,'get_company_info_by_host');
        } elseif($host == 'www'){
            list($defaultPOS,$posurl,$template_function,$include_file) = Company_Setup_Company_Variables($general_dal,$host,$domain,$http_host,'get_company_info_by_domain');
        } else { Dubbs_Error_pages('url_not_configured',$host,$domain,$http_host); }
} else {
    $companies      = $general_dal->get_company_info_by_hostNdomain($host,$domain);
        if(count($companies) > 0){
            list($defaultPOS,$posurl,$template_function,$include_file) = Company_Setup_Company_Variables($general_dal,$host,$domain,$http_host,'get_company_info_by_hostNdomain');
        }
        elseif($host != 'www'){
            list($defaultPOS,$posurl,$template_function,$include_file) = Company_Setup_Company_Variables($general_dal,$host,$domain,$http_host,'get_company_info_by_host');
        }
        else{
            list($defaultPOS,$posurl,$template_function,$include_file) = Company_Setup_Company_Variables($general_dal,$host,$domain,$http_host,'get_company_info_by_domain');
        }
}
return array ($defaultPOS,$posurl,$template_function,$include_file);
}
function Company_Setup_Company_Variables($general_dal,$host,$domain,$http_host,$dal_function){
    $posurl = 'http://' . $http_host . "/pos";
    $company_data       = $general_dal->$dal_function($host,$domain);
    $include_file       = $company_data[0]->templateName.'/'.$company_data[0]->templateName . '_functions.php';
    $template_function  = $company_data[0]->templateName.'_'.$company_data[0]->templateNumber.'_template';
    $_SESSION['settings']['company_id']     = $company_data[0]->id;
        Company_SetTemplateData_by_Company_ID($company_data[0]->id);
    $_SESSION['settings']['templateNumber'] = $company_data[0]->templateNumber;
    $_SESSION['settings']['templateType']   = $company_data[0]->templateName;
    #print "1. $host.$domain - $include_file - $template_function - {$company_data[0]->defaultPOS}: CompanyID: {$_SESSION['settings']['company_id']} <br>\n";
    return array ($company_data[0]->defaultPOS,$posurl,$template_function,$include_file);
}
function setup_path_general(){
if (substr_count($_SERVER['SERVER_NAME'],".") == 1){
    $domref = "www." . $_SERVER['SERVER_NAME'] ; }
else {
    $domref =          $_SERVER['SERVER_NAME'] ; }
list($host,$domain,$ext) = split("\.",$domref);
$domain .= "." . $ext;
$_SESSION['settings']['domain'] = $domain;
#################
if (isset($_SERVER['SUBDOMAIN_DOCUMENT_ROOT'])){
    $orig_path_info = realpath($_SERVER['SUBDOMAIN_DOCUMENT_ROOT']);
    $pos = strpos($orig_path_info, '/shop/');
}
else {
    $orig_path_info = realpath($_SERVER['DOCUMENT_ROOT']);
    $pos = strpos($orig_path_info, '/shop/');
}
$shop_path  = substr($orig_path_info, 0, $pos)."/shop/";
$Zend_path  = $shop_path . 'Zend/library/';
$version    = 'v' . substr($orig_path_info, $pos + 7, strpos($orig_path_info, '/', $pos + 7) - ($pos + 7));

set_include_path($shop_path.$version.'/:'.$shop_path.$version.'/includes:'.$Zend_path);
return array ($host,$domain);
}
function Dubbs_Error_pages($type,$host,$domain,$http_host){
    if ($type == 'url_not_configured' ){
        print "The URL $host.{$domain} has not been configured yet.<br>\n";
    }
}
function paging_first_next_last($action,$debug=0){
if ( isset($action) && $action != '' ) {
    if ($debug) { print "paging_first_next_last($action,$debug)<br>"; }
    if ( $action == "first_page" ) {
            $_SESSION['search_data']['paging_page'] = 1;
    }
    elseif ( $action == "prev_page" ) {
        if ($_SESSION['search_data']['paging_page'] >=2 ) {
            $_SESSION['search_data']['paging_page']--;
        }
    }
    elseif ( $action == "next_page" ) {
        if ($_SESSION['search_data']['paging_page'] < $_SESSION['search_data']['pages'] ) {
            $_SESSION['search_data']['paging_page']++;
        }
    }
    elseif ( $action == "last_page" ) {
            $_SESSION['search_data']['paging_page'] = $_SESSION['search_data']['pages'] ;
    }
    elseif ( $action == "all_pages" ) {
            $_SESSION['search_data']['paging_page'] = 0 ;
    }
}
}
function showPaging(){
$divisor           = 20;
$failed_result     = 0 ;
if (!isset($_SESSION['search_data']['paging_page']) ) {
    $_SESSION['search_data']['paging_page'] = 1;
    }

if ( $_SESSION['reportType'] == "SalesReport" ) {
    require_once('reports_functions.php');
    $dal        = new DAL();
    $results    = $dal->get_all_sales($_SESSION['settings']['company_id'],1);
}

elseif ( $_SESSION['reportType'] == "SalesPerHourReport" ) {
    require_once('reports_functions.php');
    $dal        = new DAL();
    $results    = $dal->get_SalesPerHourStats($_SESSION['settings']['company_id'],1);
}
elseif ( $_SESSION['reportType'] == "SalesPerMonthReport" ) {
    require_once('reports_functions.php');
    $dal        = new DAL();
    $results    = $dal->get_SalesPerMonthStats($_SESSION['settings']['company_id'],1);
}
elseif ( $_SESSION['reportType'] == "DailyInventoryReport" ) {
    require_once('reports_functions.php');
    $dal        = new DAL();
    $results    = $dal->get_DailyInventoryStats($_SESSION['settings']['company_id'],1);
}


elseif ( $_SESSION['reportType'] == "AppointmentsPerHourReport" ) {
    require_once('reports_functions.php');
    $dal        = new DAL();
    $results    = $dal->get_AppointmentsPerHourStats($_SESSION['settings']['company_id'],1);
}
elseif ( $_SESSION['reportType'] == "AppointmentsPerMonthReport" ) {
    require_once('reports_functions.php');
    $dal        = new DAL();
    $results    = $dal->get_AppointmentsPerMonthStats($_SESSION['settings']['company_id'],1);
}


elseif ( $_SESSION['reportType'] == "ItemsReport_Category" ) {
    require_once('reports_functions.php');
    $dal        = new DAL();
    $results    = $dal->get_all_ItemsReport_Category($_SESSION['settings']['company_id'],1);
}
elseif ( $_SESSION['reportType'] == "ItemsReport_Department" ) {
    require_once('reports_functions.php');
    $dal        = new DAL();
    $results    = $dal->get_all_ItemsReport_Department($_SESSION['settings']['company_id'],1);
}
elseif ( $_SESSION['reportType'] == "ItemsReport_Vendor" ) {
    require_once('reports_functions.php');
    $dal        = new DAL();
    $results    = $dal->get_all_ItemsReport_Vendor($_SESSION['settings']['company_id'],1);
}


elseif ( $_SESSION['reportType'] == "ItemsReport_BestSellers" ) {
    require_once('reports_functions.php');
    $dal        = new DAL();
    $results    = $dal->get_all_ItemsReport_BestSellers($_SESSION['settings']['company_id'],1);
}
elseif ( $_SESSION['reportType'] == "ItemsReport_SoldOut" ) {
    require_once('reports_functions.php');
    $dal        = new DAL();
    $results    = $dal->get_all_ItemsReport_SoldOut($_SESSION['settings']['company_id'],1);
}
elseif ( $_SESSION['reportType'] == "ItemsReport_AllItems" ) {
    require_once('reports_functions.php');
    $dal        = new DAL();
    $results    = $dal->get_all_ItemsReport_AllItems($_SESSION['settings']['company_id'],1);
}


elseif ( $_SESSION['reportType'] == "Profiles_AllProfiles" ) {
    $divisor = 10;
    require_once('profiles_functions.php');
    $dal        = new Profiles_DAL();
    $results    = $dal->get_AllEmployeesPerCompanyId($_SESSION['settings']['company_id'],1,0);
}
elseif ( $_SESSION['reportType'] == "Customers_AllCustomers" ) {
    $divisor = 10;
    require_once('customers_functions.php');
    $dal        = new Customers_DAL();
    $results    = $dal->get_AllCustomersPerCompanyId($_SESSION['settings']['company_id'],1);
}
elseif ( $_SESSION['reportType'] == "Companies_AllCompanies" ) {
    $divisor = 10;
    require_once('companies_functions.php');
    $dal        = new Companies_DAL();
    $results    = $dal->get_AllCompanies($_SESSION['settings']['company_id'],1);
}
elseif ( $_SESSION['reportType'] == "Inventory_Categories" ) {
    $divisor = 10;
    require_once('inventory_management_functions.php');
    $dal        = new Inventory_DAL();
    $results    = $dal->Inventory_GetCategoriesByCompanyId($_SESSION['settings']['company_id'],1);
}

elseif ( $_SESSION['reportType'] == "item_search" ) {
    $divisor = 3;
    require_once('item_search_functions.php');
    $dal        = new ITEM_SEARCH_DAL();
    $results    = $dal->get_AllItemsANDServices_by_CompanyId($_SESSION['settings']['company_id'],1,$divisor);
}

elseif ( $_SESSION['reportType'] == "Mailer_AllMailerRuns" ) {
    $divisor = 10;
    require_once('mailer_functions.php');
    $dal        = new Mailer_DAL();
    $results    = $dal->Mailer_get_latest_Mailer_Runs($_SESSION['settings']['company_id'],1);
}

elseif ( $_SESSION['reportType'] == "Inventory_AllInventoryRuns" ) {
    $divisor = 10;
    require_once('inventory_management_functions.php');
    $dal        = new Inventory_DAL();
    $results    = $dal->Inventory_get_latest_Inventory_Runs($_SESSION['settings']['company_id'],1);
}


elseif ( $_SESSION['reportType'] == "Deliveries_AllDeliveries" ) {
    $divisor = 10;
    require_once('inventory_management_functions.php');
    $dal        = new Inventory_DAL();
    $results    = $dal->Deliveries_LatestDeliveryList($_SESSION['settings']['company_id'],1);
}

elseif ( $_SESSION['reportType'] == "Jobs_AllJobs" ) {
    $divisor = 10;
    require_once('jobs_functions.php');
    $dal        = new Jobs_DAL();
    $results    = $dal->get_AllJobsPerCompanyId($_SESSION['settings']['company_id'],1);
}

else {
    $failed_result = 1;
}

if (isset($results)) { $results_count = $results[0]->count ; } else { $results_count = 0; }

$_SESSION['search_data']['pages'] = ceil($results_count / $divisor) ;
?>

<? if ($failed_result != 1) { ?>
    <? if ($_SESSION['search_data']['paging_page'] == 0 ) {?>
        <div class="f_left wp30 hp100">
            Showing All Results
        </div>
    <?} else { ?>
        <div class="f_left wp30 hp100">
            <? if ( $_SESSION['search_data']['pages'] == 0 ) { ?>
                Page <?=$_SESSION['search_data']['paging_page'] - 1; ?> of <?=$_SESSION['search_data']['pages']; ?>
            <? } else { ?>
                Page <?=$_SESSION['search_data']['paging_page']; ?>     of <?=$_SESSION['search_data']['pages']; ?>
            <? } ?>
        </div>
    <?}?>
        <div class="f_left wp40  hp100">
            <a onclick="paging('first_page','<?=$_SESSION['reportType'];?>')"  href="javascript: none();"><img border="0" title="First Page" alt="First" src="/common_includes/includes/images/first.gif"></a>
            <a onclick="paging('prev_page', '<?=$_SESSION['reportType'];?>')"  href="javascript: none();"><img border="0" title="Previous Page"alt="Previous" src="/common_includes/includes/images/prev.gif"></a>
            <a onclick="paging('next_page', '<?=$_SESSION['reportType'];?>')"  href="javascript: none();"><img border="0" title="Next Page"alt="Next" src="/common_includes/includes/images/next.gif"></a>
            <a onclick="paging('last_page', '<?=$_SESSION['reportType'];?>')"  href="javascript: none();"><img border="0" title="Last Page" alt="Last" src="/common_includes/includes/images/last.gif"></a>
            <a onclick="paging('all_pages', '<?=$_SESSION['reportType'];?>')"  href="javascript: none();"><img border="0" title="All Pages" alt="Last" src="/common_includes/includes/images/all.gif"></a>
        </div>
        <div class="f_left wp30 hp100">
            <?=$results_count?> Total Results
        </div>
<? } else {?>
        <div class="wp100 hp100 mb10">
            $_SESSION['reportType'](<?=$_SESSION['reportType']?>) is not configured for this report type yet.<br>
            includes/general_functions.php, called from ajax/showpaging.php via  (printed at <?=__LINE__?>)
        </div>
<? }
}
function pos_header(){
$IMAGE_DAL         = new IMAGE_DATA_DAL($_SESSION['MYSQL_HOST'],$_SESSION['MYSQL_USER'],$_SESSION['MYSQL_PASS'],$_SESSION['MYSQL_DATABASE']);
$main_company_logo = $IMAGE_DAL->get_Imagedata_byPreferenceName_AND_CompanyID($_SESSION['settings']['company_id'],'main_company_logo');

$woptions = "width=" . (!isset($_SESSION['preferences']['receipt_width']) || $_SESSION['preferences']['receipt_width'] > 380 ? 400 : $_SESSION['preferences']['receipt_width'] + 50) . ",height=600, screenX=100, screenY=100, scrollbars=yes, resizeable=yes";
?>
    <div class="d_InlineBlock wp100 hp100" id="tab_nav">
            <div class="f_left  d_inlineBlock wp05 hp100"><a onclick="mainDiv('Customers');" title="Customers" href="#" class="icon f_left customers">&nbsp;</a></div>
        <? if ($_SESSION['settings'][$_SESSION['settings']['login_id']]['level'] >=1 ) { ?>
            <div class="f_left  d_inlineBlock wp05 hp100"><a onclick="Daily_Ledger('<?=$woptions;?>','2011-06-15','2011-06-20');" title="Daily Ledger" href="#" class="icon f_left daily_ledger">&nbsp;</a></div>
        <? } ?>

        <? if ($_SESSION['settings'][$_SESSION['settings']['login_id']]['level'] >=2 ) { ?>
            <div class="f_left  d_inlineBlock wp05 hp100"><a onclick="mainDiv('Preferences_Company');" title="Preferences" href="#" class="icon f_left preferences">&nbsp;</a></div>
        <? } ?>

        <? if ($_SESSION['settings'][$_SESSION['settings']['login_id']]['level'] >=0 ) { ?>
            <div class="f_left  d_inlineBlock wp05 hp100"><a onclick="mainDiv('calendar_appointments');" title="Calendar" href="#" class="icon f_left calendar">&nbsp;</a></div>
        <? } ?>

        <? if ($_SESSION['settings'][$_SESSION['settings']['login_id']]['level'] >=2 ) { ?>
            <div class="f_left  d_inlineBlock wp05 hp100"><a onclick="mainDiv('Preferences_CashRegister');" title="Preferences" href="#" class="icon f_left cash_register">&nbsp;</a></div>
        <? } ?>

        <? if ($_SESSION['settings'][$_SESSION['settings']['login_id']]['level'] >=1 ) { ?>
            <div class="f_left  d_inlineBlock wp05 hp100"><a onclick="mainDiv('jobs');" title="Jobs Listings" href="#" class="icon f_left jobs_listings">&nbsp;</a></div>
        <? } ?>
            <div class="f_left  d_inlineBlock wp40 hp100" >
                <div class="f_left  d_InlineBlock wp100 hp75 s15 bold" >
                    <div class='mt5'><?=isset($_SESSION['preferences']['company_name']) ? $_SESSION['preferences']['company_name'] : "Not yet set(Preferences)." ?></div>
                </div>
                <div class="f_left  d_inlineBlock wp100 hp25" >
                    <div class='d_InlineBlock wp100 f_left center s07'>VERSION: <?=$_SESSION['settings']['version']?></div>
                </div>
            </div>

            <div class="f_left  d_inlineBlock wp20 hp100 mauto">
                <img class='wp100 m0 b0 <? if ($main_company_logo[0]->image_id > 0) { print ' mp'; } ?>'  height="50"  width="85" src='/pos/showimage.php?id=<?=$main_company_logo[0]->image_id?>&image_db_id=<?=$main_company_logo[0]->image_db_id?>'  <? if ($main_company_logo[0]->image_id > 0) { ?> <? } ?>  />
            </div>

            <div class="f_right d_inlineBlock wp05 hp100"><a onclick="mainDiv('profiles_clock_in_out');"   title="Clock In / Out (F11)" href="#" class="icon f_right time_clock">&nbsp;</a></div>

            <div class="f_right d_inlineBlock wp05 hp100"><a onclick="mainDiv('Profiles');" title="Switch Employees / Lock System" href="#" class="icon f_right employee">&nbsp;</a></div>
    </div>
<?
}
function default_header(){
	$general_dal      = new GENERAL_DAL();
	$IMAGE_DAL        = new IMAGE_DATA_DAL();
	$company_name     = $general_dal->get_Company_Preference($_SESSION['settings']['company_id'],'company name');
	$meta_description = $general_dal->get_Company_Preference($_SESSION['settings']['company_id'],'meta_description');
	$meta_keywords    = $general_dal->get_Company_Preference($_SESSION['settings']['company_id'],'meta_keywords');
	?>
	<head>
		<title>
			<?=$company_name[0]->value?>
		</title>
		<META name="description"    content="<?=$meta_description[0]->value?>">
		<META name="keywords"       content="<?=$meta_keywords[0]->value?>">

		<script type="text/javascript"              src="/pos/includes/jQueryJS/jquery-1.4.4.min.js">
		</script>
		<script type="text/javascript"              src="/pos/includes/jQueryJS/jquery-ui-1.8.9.custom.min.js">
		</script>
		<script type="text/javascript"              src= "/common_includes/common.js">
		</script>

		<link   rel="stylesheet" type="text/css"    href="/massage/includes/reset.css"/> 
		<link   rel="stylesheet" type="text/css"    href="/massage/includes/master.css"/>

		<!--<link   rel="stylesheet" type="text/css"    href="/pos/includes/pos.css"/>-->
		 <!-- <link   rel="stylesheet" type="text/css"    href="/<?=$_SESSION['settings']['templateType']?>/includes/<?=$_SESSION['settings']['templateType']?>.css"/>-->
		<link   rel="stylesheet" type="text/css"    href="/common_includes/colors_styles.php?style=Include" media="screen"/>
	</head>
	<?
}

function replace_ticks2($string){
	return str_replace("'", '&#39;', $string);
}
function number2($number, $decimals = 2, $dec_point = '.', $thousands_sep = ''){
	return number_format($number, $decimals, $dec_point, $thousands_sep);
}
function money2($amount,$showDecimals=2){
	return $_SESSION['preferences']['currency_position'] == 1 ? $_SESSION['preferences']['currency'] . ($_SESSION['preferences']['money_string_contains_space'] ? '&nbsp;' : '') . number2($amount,$showDecimals) : number2($amount,$showDecimals) . ($_SESSION['preferences']['money_string_contains_space'] ? '&nbsp;' : '') . $_SESSION['preferences']['currency'];
}
function quoteSmart($value, $force = 0) {
        if ($force == 0 && ((is_numeric($value) && (substr($value, 0, 1) != '0' || substr($value, 1) == '')) || strtolower($value) == "null")) {
                return $value;
        }

	if ($force == 0 && $value === null) {
		return "null";
	}

        if (get_magic_quotes_gpc()) {
                $value = stripslashes($value);
        }

        return "'" . mysql_escape_string($value) . "'";
}
function search(){
    echo $_SESSION['page'];
}
function    evaluate_GET_for_SEARCHES($search_type,$search_data_name){
    if (isset($_GET[$search_data_name]) ) {
        if ( $_GET[$search_data_name] == -1 or !isset($_GET[$search_data_name]) ) { unset($_SESSION['search_data'][$search_type][$search_data_name]);}
        else { $_SESSION['search_data'][$search_type][$search_data_name] = $_GET[$search_data_name] ;}
}
}
function formatPhone($number) {
    $number = preg_replace('/[^\d]/', '', $number); //Remove anything that is not a number
    if(strlen($number) < 10)
     {
    	return false;
     }
    return substr($number, 0, 3) . '-' . substr($number, 3, 3) . '-' . substr($number, 6);
 }
function set_Company_id_PerEmployeeRemoteKey_and_RemoteIP($ipAddress,$EmployeeRemoteKey = '-1'){
        if ( $EmployeeRemoteKey == -1 or $EmployeeRemoteKey === NULL  ) {
            $posurl = 'http://' . $_SERVER['HTTP_HOST'] . "/pos";
            print "No EmployeeRemoteKey set in URL. EmployeeRemoteIP='.$ipAddress.'<br>\n";
            $company_id = -1;
        } else {
            require_once('profiles_functions.php');
            $profiles_dal       = new Profiles_DAL();
            $profile_data       = $profiles_dal->get_EmployeeDataPerEmployeeRemoteKey($ipAddress,$EmployeeRemoteKey);
            if (count($profile_data) == 0) { die('Invalid EmployeeRemoteKey='.$EmployeeRemoteKey.' or EmployeeRemoteIP='.$ipAddress.' or INACTIVE ID'); }
            $company_id         = $profile_data[0]->company_id;
    }
return $company_id;
}

function upload_file_stanza($column_name,$dal,$ImageTypeId){
    $profile_images = $dal->get_all_images_BY_ImageTypeId_and_column_name($ImageTypeId,$column_name);?>
    <div class="f_left wp100">
        <div class="f_left wp100 bctrt left ">
                <font class="bold ml5"><?=$column_name?> (<?=$ImageTypeId?>)</font>&nbsp;Images
            </div>
    </div>
    <div class="f_left wp99 box3 mb50 ">
         <div class="f_left wp100 h20px bcgray">
            <div class="f_left wp03 hp100 no-overflow">#</div>
            <div class="f_left wp20 hp100 no-overflow">&nbsp;File Name</div>
            <div class="f_left wp08 hp100 ">&nbsp;Size</div>
            <div class="f_left wp14 hp100 " >&nbsp;Mime</div>
            <div class="f_left wp07 hp100 ">&nbsp;Width</div>
            <div class="f_left wp07 hp100 " >&nbsp;Height</div>
            <div class="f_left wp02 hp100 ">&nbsp;</div>
            <div class="f_left wp09 hp100 ">&nbsp;Default?</div>
            <div class="f_left wp02 hp100 ">&nbsp;</div>
            <div class="f_left wp15 hp100 ">&nbsp;<? if ( in_array($column_name, array('item','service')) ){?>Group Default<?}?></div>
            <div class="f_left wp13 hp100 ">&nbsp;</div>
        </div>
        <?
        if (count($profile_images) >0 ) {
            $count =1;
            foreach($profile_images as $image_data)
            {
            if (strlen($image_data->image_name) < 1) { $image_data->image_name = "Unknown"; }
            if (strlen($image_data->mime) < 1)       { $image_data->mime       = "Unknown"; }
            ?>
            <div class="f_left wp100 h20px s07 bclightgray">
                <div class="bclightgray f_left wp03 hp100 no-overflow"><?=$count?></div>
                <div id="profileLoginImageName_<?=$image_data->image_id?>" class="bclightgray f_left wp20 hp100 no-overflow">&nbsp;<?=$image_data->image_name?></div>
                <div id="profileLoginImageSize_<?=$image_data->image_id?>" class="bclightgray f_left hp100 wp08">&nbsp;<?=formatbytes($image_data->size,"MB")?></div>
                <div id="profileLoginImageMime_<?=$image_data->image_id?>" class="bclightgray f_left hp100 wp14" >&nbsp;<?=$image_data->mime?></div>
                <div id="profileLoginImageWidth_<?=$image_data->image_id?>" class="bclightgray f_left wp07 hp100">&nbsp;<?=$image_data->width?></div>
                <div id="profileLoginImageHeight_<?=$image_data->image_id?>" class="bclightgray f_left wp07 hp100" >&nbsp;<?=$image_data->height?></div>
                <div class="bclightgray f_left wp02 hp100">&nbsp;</div>
                <div class="bclightgray f_left wp09 hp100">
                    <? if ($image_data->default == 0 ) { ?>
                            <input type="submit" class="button wp90" value="Set Default" onclick="editImage_UpdTypeSetDefaultImageID(<?=$image_data->image_id?>,<?=$ImageTypeId?>,'<?=$column_name?>','<?=$image_data->image_db_id?>','mainBody')">
                            <? } else { ?>
                            <input type="submit" class="button wp90 green" value="Default" >
                    <? } ?>
                </div>
                <div class="bclightgray f_left wp02 hp100">&nbsp;</div>
                <div class="bclightgray f_left wp15 hp100">
                    <? if ( in_array($image_data->type_name, array('item','service')) && $image_data->style_number !== NULL  ) { ?>
                        <? if ($image_data->default_group_image == 0  ) { ?>
                            <input type="submit" class="button wp90" value="Set Group IMG" onclick="editImage_UpdTypeSetDefaultGroupImageID(<?=$image_data->image_id?>,<?=$ImageTypeId?>,'<?=$column_name?>','<?=$image_data->image_db_id?>','mainBody','<?=$image_data->style_number?>','<?=$image_data->company_id?>')">
                            <? } else { ?>
                            <input type="submit" class="button wp90 green" value="Group Default IMG!" >
                        <? } ?>
                    <? } else { ?>
                            &nbsp;Style Num='<?=$image_data->style_number?>'
                    <? } ?>
                </div>
                <div class="bclightgray f_left wp02 hp100">&nbsp;</div>
                <div class="bclightgray f_left wp08 hp100">
                    <? if ($image_data->default == 0 && $image_data->default_group_image == 0 ) { ?>
                            <input type="submit" class="button wp90" value="Delete" onclick="editImage_UpdTypeDeleteImageByImageID(<?=$image_data->image_id?>,<?=$ImageTypeId?>,'<?=$column_name?>','<?=$image_data->image_db_id?>','mainBody')">
                    <? } ?>
                    &nbsp;
                </div>
            </div>
            <?
            $count++;
            }
        } else {?>
            <div class="f_left wp100 h25px bclightgray">
                    <div class="bclightgray f_left  left wp100 hp100 no-overflow">
                        There aren't any images uploaded for '<?=$column_name?>' yet.
                    </div>
                </div>
        <?}?>
            <div class="f_left wp100 h30px ">
                <div class="f_left wp20 hp100 right bctrt s08">Select An Image<br><font color="red" class="s08">2Mb file size limit</font></div>
                <div class="f_left wp80 hp100 left bclightgray textIndent15">
                    <div id="ImageFileInputtDiv" class="f_left left wp50 hp100 textIndent">
                        <form action="ajax/uploadFile.php" method="post" enctype="multipart/form-data">
                                <input class="bcwhite" id="uploader" name="<?=$column_name?>" type="file" />
                                <input type="hidden" name="file_hash_name"  value="<?=$column_name?>"/>
                                <input type="hidden" name="column_name"      value="<?=$column_name?>"/>
                                <input type="hidden" name="source_record_id" value="<?=$ImageTypeId?>">
                                <input type="submit" value="Upload" id="pxUpload"/>
                                <input type="reset"  value="Clear"  id="pxClear" />
                        </form>
                    </div>
                    <div id="main_container" class="f_left left wp50 hp100 textIndent">
                        <head>
                        <link href="includes/jQueryCSS/fileUploader.css" rel="stylesheet" type="text/css" />
                        <script src="includes/jQueryJS/jquery.fileUploader.js" type="text/javascript"></script>
                        </head>
                        <script type="text/javascript">
                            $(function(){
                                $('#uploader').fileUploader({
                                    limit: false,
                                    imageLoader: '',
                                    formName: '<?=$column_name?>',
                                    buttonUpload: '#pxUpload',
                                    buttonClear: '#pxClear',
                                    successOutput: 'File Uploaded',
                                    errorOutput: 'Failed',
                                    allowedExtension: 'jpg|jpeg|gif|png',
                                    //Callbacks
                                    onFileChange: function(e, form) {
                                    },
                                    onFileRemove: function(e) {
                                    },
                                    beforeUpload: function(e) {
                                    },
                                    beforeEachUpload: function(form) {
                                    },
                                    afterEachUpload: function(data, status, idOfText) {
                                        //alert(data);
                                    },
                                    afterUpload: function(e) {
                                    }
                                });
                            });
                            </script>
                    </div>
                </div>
            </div>
   </div>
<?
}
function upload_file($userfile,$column_name,$source_record_id) {
    $General_DAL        = new General_DAL();
    $ImageDatabases  = $General_DAL->get_ImageDatabasesInfo();
    $loop_count = $previous_count = 1;
    foreach ($ImageDatabases as $ImageDatabase) {
            //$Image_DAL          = new IMAGE_DATA_DAL($ImageDatabase->hostname,$ImageDatabase->username,$ImageDatabase->password,$ImageDatabase->databasename);
            $image_count        = $General_DAL->get_ImageCount_Per_ImageDB($ImageDatabase->id);
            $ImageDBCount[$ImageDatabase->id] = $image_count[0]->count;
            if (($ImageDBCount[$ImageDatabase->id] < $previous_count) || ($loop_count == 1) ) { $database_ID_NUMBER = $ImageDatabase->id; }
            $previous_count = $ImageDBCount[$ImageDatabase->id]; $loop_count++;
    }
    //print "Lowest count db ID: $database_ID_NUMBER";
    if(isset($_FILES[$userfile])) {
            if ($source_record_id >= 0 && isset($_FILES[$userfile]['tmp_name']) && file_exists($_FILES[$userfile]['tmp_name'])) {
                $file_size = $_FILES[$userfile]["size"];
                $info = getimagesize($_FILES[$userfile]['tmp_name']);
            unset($image);
            switch ($info['mime']) {
                    case 'image/gif' :
                            if (!isset($image)) { $image = imagecreatefromgif($_FILES[$userfile]['tmp_name']); }
                    case 'image/jpeg' :
                            if (!isset($image)) { $image = imagecreatefromjpeg($_FILES[$userfile]['tmp_name']); }
                    case 'image/png' :
                            if (!isset($image)) { $image = imagecreatefrompng($_FILES[$userfile]['tmp_name']); }

                        $m = $info[0] > $info[1] ? 600 / $info[0] : 600 / $info[1];
                        $x = floor($info[0] * $m); $y = floor($info[1] * $m);

                        $image2 = imagecreatetruecolor($x, $y);
                        imagecopyresized($image2, $image, 0, 0, 0, 0, $x, $y, $info[0], $info[1]);
                        imagedestroy($image);
                        $fname = '/tmp/shopimport.png';
                        imagepng($image2, $fname);
                        imagedestroy($image2);
                        $f = fopen($fname, 'r');
                        $c = fread($f, filesize($fname));
                        fclose($f);
                        $t_image = chunk_split(base64_encode($c));

                        $General_DAL        = new General_DAL();   #$ImageDatabaseID[0]->image_db_id
                        $ImageDatabaseInfo  = $General_DAL->get_ImageDatabaseInfo_by_ImageDatabaseID($database_ID_NUMBER);

                        $insert_image_dal   = new InsertUpdateDelete_DAL($ImageDatabaseInfo[0]->hostname,$ImageDatabaseInfo[0]->username,$ImageDatabaseInfo[0]->password,$ImageDatabaseInfo[0]->databasename);
                        
                        $images_table_insert_query = "INSERT INTO `images` (`image`,`added`) VALUES ('$t_image',now())";
                        $image_id = $insert_image_dal->insert_query($images_table_insert_query);

                        $image_type_id  = $General_DAL->item_typeId_by_type($column_name);
                        $type_id        = $image_type_id[0]->id;

                        $image_count_data = $General_DAL->item_ImageCount_by_ID_and_TypeId($source_record_id,$type_id);
                        if (count($image_count_data) > 0) { $default_image = 0 ; } else { $default_image = 1 ; }

                        $insert_dal         = new InsertUpdateDelete_DAL();
                        $item_image_mappings_table_insert_query = "
                            INSERT INTO `item_image_mappings`
                                    (`id`,                  `image_type_id`,    `image_id`,     `image_db_id`,          `image_name`,                   `size`,         `mime`,                 `width`,    `height`,   `default_item_image`,   `default`,          `added`)
                            VALUES  ('$source_record_id',   $type_id,           $image_id,      $database_ID_NUMBER,    '{$_FILES[$userfile]['name']}', '$file_size',   '{$info['mime']}',      '$x',       '$y',       $default_image,         $default_image ,    convert_tz( now(),'america/chicago','utc') )";
                        $insert_id= $insert_dal->insert_query($item_image_mappings_table_insert_query);
                        unlink($fname);
                        $result = 'success';
                        ?>
                        <div id="output"><?=$result?></div>
                        <?
                        break;
                    default:
                            $result = 'failure';
                            ?>
                            <div id="output"><?=$result?></div>
                            <?
                    }
            }
    }
    else {
        echo 'An error accured while the file was being uploaded. ' . 'Error code: '. intval($_FILES[$userfile]['error']);
        $result = 'failure';
        $_SESSION['message'] = sprintf('ERROR: Unsupported image type: %s', $info['mime']);
        ?>
        <div id="output"><?=$result?></div>
        <div id="yep"><?=$error_message = file_upload_error_message($_FILES[$userfile]['error'])?></div>
        <?
    }
    return array ($result, $_FILES[$userfile]['tmp_name'], $info['mime'], "image binary data", $file_size);
}
function show_ItemOrServiceIMG($company_id,$item_or_serviceID,$style_number,$image_id,$image_db_id,$width,$height){
        $php_file = "showimage.php";
        if ( ($image_id !== NULL || $image_db_id !== NULL) && ($image_id != '' || $image_db_id != '') ) {
                $IMG_HTML_data = "<img  onclick=\"window.open('".$php_file."?id=".$image_id."&image_db_id=".$image_db_id."&w=600&h=600', '_new', 'innerHeight=620,innerWidth=620,scrollbars=no,toolbar=no,resizable=no,menubar=0' );\"
                                    src='".$php_file."?id=".$image_id."&image_db_id=".$image_db_id."&w=".$width."&h=".$height."'
                                    class='m0 mp mauto'
                              >";
                $raw_img_location = "http://demo.dubbsenterprises.com/pos/".$php_file."?id=".$image_id."&image_db_id=".$image_db_id."&w=".$width."&h=".$height;
        } else {
            $general_dal        = new GENERAL_DAL();
            $image_group_data   = $general_dal->get_ImageGroupImage_by_StyleNumber_N_CompanyID($company_id,$style_number);
            if (count($image_group_data) > 0) {
                $image_id           = $image_group_data[0]->image_id;
                $image_db_id        = $image_group_data[0]->image_db_id;
                $IMG_HTML_data = "<img  onclick=\"window.open('".$php_file."?id=".$image_id."&image_db_id=".$image_db_id."&w=600&h=600', '_new', 'innerHeight=620,innerWidth=620,scrollbars=no,toolbar=no,resizable=no,menubar=0' );\"
                                    src='".$php_file."?id=".$image_id."&image_db_id=".$image_db_id."&w=".$width."&h=".$height."'
                                    class='m0 mp mauto'
                              >g";
                $raw_img_location = "http://demo.dubbsenterprises.com/pos/".$php_file."?id=".$image_id."&image_db_id=".$image_db_id."&w=".$width."&h=".$height;
            } else {
                $image_id = $image_db_id = 0;
                $IMG_HTML_data = "<img  onclick=\"window.open('".$php_file."?id=".$image_id."&image_db_id=".$image_db_id."&w=600&h=600', '_new', 'innerHeight=620,innerWidth=620,scrollbars=no,toolbar=no,resizable=no,menubar=0' );\"
                                    src='".$php_file."?id=".$image_id."&image_db_id=".$image_db_id."&w=".$width."&h=".$height."'
                                    class='m0 mp'
                              >";
                $raw_img_location = "http://demo.dubbsenterprises.com/pos/".$php_file."?id=".$image_id."&image_db_id=".$image_db_id."&w=".$width."&h=".$height;
            }
        }
        return array($IMG_HTML_data, $raw_img_location);
}
function show_Image($image_id_data){?>
    <? if (count($image_id_data) > 0) { ?>
        <img class="hp95 mt2" alt="" src="showimage.php?id=<?=$image_id_data[0]->image_id?>&image_db_id=<?=$image_id_data[0]->image_db_id?>&w=150&h=80">
    <? } else { ?>
        <img class="hp95 mt2" alt="" src="showimage.php?id=0&image_db_id=0&w=150&h=80">
    <? } ?>
<?} 
function file_upload_error_message($error_code) {
    switch ($error_code) {
        case UPLOAD_ERR_INI_SIZE:
            return 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
        case UPLOAD_ERR_FORM_SIZE:
            return 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
        case UPLOAD_ERR_PARTIAL:
            return 'The uploaded file was only partially uploaded';
        case UPLOAD_ERR_NO_FILE:
            return 'No file was uploaded';
        case UPLOAD_ERR_NO_TMP_DIR:
            return 'Missing a temporary folder';
        case UPLOAD_ERR_CANT_WRITE:
            return 'Failed to write file to disk';
        case UPLOAD_ERR_EXTENSION:
            return 'File upload stopped by extension';
        default:
            return 'Unknown upload error';
    }
}
function formatbytes($kilobytes, $type){
switch($type){
        case "KB":
            $filesize = $kilobytes * .0009765625; // bytes to KB
        break;
        case "MB":
            $filesize = ($kilobytes * .0009765625) * .0009765625; // bytes to MB
        break;
        case "GB":
            $filesize = (($kilobytes * .0009765625) * .0009765625) * .0009765625; // bytes to GB
        break;
}
if($filesize <= 0){
    return $filesize = 'Unknown Size';}
    else{return round($filesize, 2).' '.$type;}
}

function SetGoogleClient($staff_id=0){
$result =1;
if ( $staff_id==0 ){ $staff_id = $_SESSION['appointment']['staff_id']; }
require_once('profiles_functions.php');
$profiles_dal           = new Profiles_DAL();
$load_staff_info        = $profiles_dal->get_EmployeeDataPerLoginId($staff_id);
$user                   = $load_staff_info[0]->gmail_username."@gmail.com";
$pass                   = $load_staff_info[0]->gmail_password;
$staff_firstname        = $load_staff_info[0]->firstname;
$staff_surname          = $load_staff_info[0]->lasttname;
$staff_email_address    = $load_staff_info[0]->email_address;

require_once 'Zend/Loader.php';
Zend_Loader::loadClass('Zend_Gdata');
Zend_Loader::loadClass('Zend_Gdata_ClientLogin');
Zend_Loader::loadClass('Zend_Gdata_Calendar');
Zend_Loader::loadClass('Zend_Uri_Http');
Zend_Loader::loadClass('Zend_Http_Client');
// create authenticated HTTP client for Calendar service
$service    = Zend_Gdata_Calendar::AUTH_SERVICE_NAME;
    if (                  isset($_SESSION['appointment']['client'][$staff_id]) ) {
        $session  = unserialize($_SESSION['appointment']['client'][$staff_id])  ;
        if (              isset($_SESSION['appointment']['gcal'][$staff_id]) ) {
            $gcal = unserialize($_SESSION['appointment']['gcal'][$staff_id]);
        }
        else {
            $gcal = new Zend_Gdata_Calendar($client);
            $_SESSION['appointment']['gcal'][$staff_id]   = serialize($gcal);
        }
    }
    else {
        try
            { $client = Zend_Gdata_ClientLogin::getHttpClient($user,$pass,$service); }
        catch(Exception $e)
            {
                $result=0;
            }

            if ($result != 0) {
                $gcal       = new Zend_Gdata_Calendar($client);
                $_SESSION['appointment']['gcal'][$staff_id]    = serialize($gcal);
            }
            else {
                $client = 0;
            }
    }
return $client;
}
function SetSessionsOfAvailable_APPTS($staff_id,$date,$appointment_slot_interval){
    $startMinDate  = date ( 'Y-m-d' ,   StrToTime($date) );
    $weekDayNumber = date ( 'w' ,       StrToTime($date) );
    $followingDate = date ( 'Y-m-d' ,   StrToTime('+1 days', StrToTime($date)) );
    unset($Available_Appointments_array, $start, $end, $openAppts, $eventFeed, $appInstance, $gdataCal, $event);
    $Available_Appointments_array = array();
    appointments_StaffAvailableSlots_per_DaySlotInterval($staff_id,$weekDayNumber,$appointment_slot_interval);

//    $client = SetGoogleClient($staff_id);
//    if (is_object($client))  {
//        $gdataCal = new Zend_Gdata_Calendar($client);
//        $query = $gdataCal->newEventQuery();
//            $query->setVisibility('private');
//            $query->setProjection('full');
//            $query->setOrderby('starttime');
//            $query->setsortOrder('ascending');
//            $query->setStartMin($startMinDate."T00:00:00.000");
//            $query->setStartMax($followingDate."T23:59:59.000");
//        $eventFeed = $gdataCal->getCalendarEventFeed($query);
//    }
        $startTimeArray = $_SESSION[$staff_id][date('D', strtotime($date))] ;
        $openAppts = $eventFeed = array();
        $apptInstance =0;
 #=$startMinDate." - ".$followingDate."<br><hr>";

    foreach ($eventFeed as $event) {
        foreach ($event->when as $when) {
            $start = split("T", $when->startTime );
            $end   = split("T", $when->endTime );

            if ($start[0] != $date ) { break; }

            $start = split("\.", $start[1] ); $start = $start[0];
            $end   = split("\.", $end[1]   ); $end   = $end[0];

            $start = str_replace (":", "", $start);
            $end   = str_replace (":", "", $end);

            $start = substr($start,0,-2);
            $end   = substr($end,  0,-2);

            while( $apptInstance < count($startTimeArray)) {
                if ( $startTimeArray[$apptInstance] < $start ) {
                    #echo "if,    yes, $startTimeArray[$apptInstance] is less than Start:$start. $apptInstance<br>\n";
                    array_push($openAppts, $startTimeArray[$apptInstance]);
                    $apptInstance++;
                    }
                elseif ( $startTimeArray[$apptInstance] > $end ) {
                    #echo "elsif, no, $startTimeArray[$apptInstance] is greater then End:$end. $apptInstance<br>\n";
                    #array_push($openAppts, $startTimeArray[$apptInstance]);
                    $break = 1; if ($break) { $break=0; break;}
                    $apptInstance++;
                }
                else {
                    $apptInstance++;
                }
            }
        }
    }  //  end of events,  lets loop the rest of the $apptInstance less than count($startTimeArray) .

    while( $apptInstance < count($startTimeArray)) {
            #echo "End of da loop, $startTimeArray[$apptInstance] is greater than $end. $apptInstance<br>\n";
            array_push($openAppts, $startTimeArray[$apptInstance]);
            $apptInstance++;
    }
    foreach ($openAppts as $appt) {
        $Available_Appointments_array[$appt] = 1;
    }
    return $Available_Appointments_array;
}
    function appointments_StaffAvailableSlots_per_DaySlotInterval($login_id,$day,$appointment_slot_interval){
    $dal = new Profiles_DAL();
    $dayhash[0] = 'Sun'; $dayhash[1] = 'Mon'; $dayhash[2] = 'Tue'; $dayhash[3] = 'Wed'; $dayhash[4] = 'Thu'; $dayhash[5] = 'Fri'; $dayhash[6] = 'Sat';
    $_SESSION[$login_id][$dayhash[$day]] = array();
    $get_apptsByDayAndLoginID = $dal->get_apptsByDayAndLoginID($login_id,$day,$appointment_slot_interval);
    if (isset($get_apptsByDayAndLoginID) && is_array($get_apptsByDayAndLoginID)) {
        foreach ( $get_apptsByDayAndLoginID as $time) {
            array_push($_SESSION[$login_id][$dayhash[$day]], (int)$time->time);
        }
    }
}

function createEvent($appointment_id) {
        require_once('appointment_functions.php');
        $Appointments_dal   = new Appointments_DAL;
        $AppointmentInfo    = $Appointments_dal->Appointments_displayAppointmentInfo($appointment_id);

        require_once('customers_functions.php');
        $Customer_dal       = new Customers_DAL();
        $Customer_Info      = $Customer_dal->get_CustomerDataPerId($AppointmentInfo[0]->customer_id);

        require_once('companies_functions.php');
        $Companies_dal      = new Companies_DAL();
            $COMPANY_NAME = $Companies_dal->get_TemplateTabData_by_Name('COMPANY_NAME',$AppointmentInfo[0]->company_id);
            $PHYSICAL_ADDRESS                       = $Companies_dal->get_TemplateTabData_by_Name('PHYSICAL_ADDRESS', $AppointmentInfo[0]->company_id);

        $startDate      = date('Y-m-d', strtotime($AppointmentInfo[0]->startDate));
        $startTime      = date('H:i:s', strtotime($AppointmentInfo[0]->startDate));
        $endDate        = date('Y-m-d', strtotime($AppointmentInfo[0]->endDate));
        $endTime        = date('H:i:s', strtotime($AppointmentInfo[0]->endDate));

        #######################################################################
        ##  get the number of services associated tot his appointment.
        $Appointment_Service_Data = $Appointments_dal->Appointments_displayAppointmentServices_by_appointment_ID($appointment_id);
        $service_ids = array();
        foreach($Appointment_Service_Data as $Appointment_Service) { array_push($service_ids, $Appointment_Service->service_id); }

        $client = SetGoogleClient($AppointmentInfo[0]->employee_login_id);
        if (is_object($client)) {
            $gdataCal = new Zend_Gdata_Calendar($client);
            $newEvent = $gdataCal->newEventEntry();

            $title = $Customer_Info[0]->firstname." ".$Customer_Info[0]->lastname ." - " .$COMPANY_NAME[0]->value ;
            $desc  = $Customer_Info[0]->firstname." ".$Customer_Info[0]->lastname. " - " . $Customer_Info[0]->phone_num. " - " . $Customer_Info[0]->email. "
Number of services: " . count($service_ids);
            $count = 1;
            foreach($Appointment_Service_Data as $Appointment_Service) {
                $desc  .= "
" . $count . ". " . $Appointment_Service->item_name ;
                $count++;
            }
            $where = $PHYSICAL_ADDRESS[0]->value;
            $newEvent->title      = $gdataCal->newTitle($title);
            $newEvent->where      = array($gdataCal->newWhere($where));
            $newEvent->content    = $gdataCal->newContent("$desc");

            $when = $gdataCal->newWhen();
                $when->startTime      = "{$startDate}T{$startTime}.000";
                $when->endTime        = "{$endDate}T{$endTime}.000";
            $newEvent->when       = array($when);

            #echo "Start:{$when->startTime}<br>";
            #echo "Stop: {$when->endTime}  <br>";
            try {
              $response = $gdataCal->insertEvent($newEvent);
            } catch (Zend_Gdata_App_Exception $e) {
              $response = "Error: " . $e->getResponse();
            }
        } else {
            $response = "Error: $client";
        }
    return $response ;
}
function deleteDubbsDBAppointment($appointment_id,$deleted_by_type,$deleted_by_id){
    $event = 1;
    $cancel_event_sql = "update appointments set status = 1, deleted_by_type=$deleted_by_type, deleted_by_id=$deleted_by_id where id = $appointment_id";
    try {
        $InsertUpdateDelete_DAL = new InsertUpdateDelete_DAL();
        $InsertUpdateDelete_DAL->query($cancel_event_sql);
    } catch (exception $e) {
      $event = "Error: " . $e;
    }
    return $event ;
}
function deleteRemoteAppointment($appointment_id){
    require_once('appointment_functions.php');
    $Appointments_dal   = new Appointments_DAL;
    $General_DAL        = new GENERAL_DAL();
    $AppointmentInfo    = $Appointments_dal->Appointments_displayAppointmentInfo($appointment_id);
    $staff_id           = $AppointmentInfo[0]->login_id;
    $eventURL           = $AppointmentInfo[0]->ThirdPartyApptEventId;
    $client             = SetGoogleClient($AppointmentInfo[0]->employee_login_id);

    email_Customer_Cancel_ApptNotification($appointment_id);
    email_Employee_Cancel_ApptNotification($appointment_id);
    #  Email the Master email address if applicable
    $bookings_copy_masterEmail_data = $General_DAL->get_Company_Preference($_SESSION['settings']['company_id'],'bookings_copy_masterEmail');
    if ( isset($bookings_copy_masterEmail_data) && $bookings_copy_masterEmail_data[0]->value == 1 ) {
        email_MasterEmail_Cancel_ApptNotification($appointment_id,0);
    }

    try {
        $service = new Zend_Gdata_Calendar($client);
        $event = $service->getCalendarEventEntry($eventURL);
        $event->delete();
    } catch (Zend_Gdata_App_Exception $e) {
      $event = "Error: " . $e->getResponse();
    }
    return $event ;
}

function email_Customer_Registration                ($customer_id,$debug=0){
            ### Registration, Customer.  needs customer_id , from the customer_id we can get the company_id..
            require_once('customers_functions.php');
            require_once('companies_functions.php');
            $Customer_dal   = new Customers_DAL();
            $Companies_dal  = new Companies_DAL();

            $Customer_Info  = $Customer_dal->get_CustomerDataPerId($customer_id);
            $SERVER_ADDRESS                         = $Companies_dal->get_TemplateTabData_by_Name('SERVER_ADDRESS',$Customer_Info[0]->company_id);
            $COMPANY_URL                            = $Companies_dal->get_TemplateTabData_by_Name('COMPANY_URL',$Customer_Info[0]->company_id);
            $COMPANY_NAME                           = $Companies_dal->get_TemplateTabData_by_Name('COMPANY_NAME',$Customer_Info[0]->company_id);

            $Registration_EMAIL_FROM_NAME           = $Companies_dal->get_TemplateTabData_by_Name('Registration_EMAIL_FROM_NAME',$Customer_Info[0]->company_id);
            $Registration_EMAIL_SUBJECT             = $Companies_dal->get_TemplateTabData_by_Name('Registration_EMAIL_SUBJECT',$Customer_Info[0]->company_id);
            $Registration_EMAIL_HEADER_MESSAGE      = $Companies_dal->get_TemplateTabData_by_Name('Registration_EMAIL_HEADER_MESSAGE',$Customer_Info[0]->company_id);
            $Registration_EMAIL_REPLY_ADDRESS       = $Companies_dal->get_TemplateTabData_by_Name('Registration_EMAIL_REPLY_ADDRESS',$Customer_Info[0]->company_id);

            $email_subject                          = $Registration_EMAIL_SUBJECT[0]->value;
            $reply_address                          = $Registration_EMAIL_REPLY_ADDRESS[0]->value;

            $BGcolor = "#45a853";
            $TopHeader_Display = emailTopHeader($BGcolor,$email_subject,$Registration_EMAIL_HEADER_MESSAGE[0]->value);

            $message = $TopHeader_Display;
            $message.= "
                <tr>
                    <td colspan=\"3\">
                        <table cellpadding=\"0\">
                            <tr>
                                <td width=\"425\" style=\"padding-right:10px; font-family:Trebuchet MS, Verdana, Arial; font-size:12px;\" valign=\"top\">
                                    <span style=\"font-family:Trebuchet MS, Verdana, Arial; font-size:17px; font-weight:bold;\">Welcome!</span>
                                    <br />
                                    <p>".$Customer_Info[0]->firstname." ".$Customer_Info[0]->lastname.",<br>You are successfully REGISTERED and we look forward to seeing you soon:</p>
                                    <br />
                                    <div style=\"padding-left:20px; padding-bottom:10px;\"><img src=\"http://".$SERVER_ADDRESS[0]->value."/common_includes/includes/images/spade.gif\" alt=\"\"/>&nbsp;&nbsp;&nbsp;Get email reminders of upcoming appointments.</div>
                                    <div style=\"padding-left:20px; padding-bottom:10px;\"><img src=\"http://".$SERVER_ADDRESS[0]->value."/common_includes/includes/images/spade.gif\" alt=\"\"/>&nbsp;&nbsp;&nbsp;Schedule an appointment any time, day or night!</div>
                                    <div style=\"padding-left:20px; padding-bottom:10px;\"><img src=\"http://".$SERVER_ADDRESS[0]->value."/common_includes/includes/images/spade.gif\" alt=\"\"/>&nbsp;&nbsp;&nbsp;Booking online saves us time and stops us from interrupting other clients to take your calls.</div>
                                    <p>In the meantime, you can <a href=\"http://".$COMPANY_URL[0]->value."\">return to our website</a> to continue browsing.</p>

                                    Best Regards,<br/>
                                    ".$Registration_EMAIL_FROM_NAME[0]->value."<br/>
                                    ".$COMPANY_NAME[0]->value."<br/>
                                    <a href=\"http://". $COMPANY_URL[0]->value."\">".$COMPANY_URL[0]->value."</a><br/>
                                    <br/>
                                    This welcome email was sent to ".$Customer_Info[0]->email." because you recently registered at ".$COMPANY_URL[0]->value.".
                                </td>
                                <td style=\"border-left:1px solid #e4e4e4; padding-left:15px;\" valign=\"top\">
                                    ".emailRightColumn($Companies_dal,$Customer_Info[0]->company_id)."
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        <br />
            ".emailBottomTable()."
    </center>
</body>";

// Always set content-type when sending HTML email
$headers  = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=iso-8859-1" . "\r\n";
$headers .= "From: \"".$COMPANY_NAME[0]->value."\" <no-reply@dubbsenterprises.com>\r\n";
$headers .= "Reply-to: \"".$COMPANY_NAME[0]->value."\" <no-reply@dubbsenterprises.com>\r\n";
$headers .= "X-Mailer: PHP/" . phpversion()."\r\n";

if ($debug != 1) {
    mail($Customer_Info[0]->email,$email_subject,$message,$headers);
}
else {
    echo "REPLY TO:" . $reply_address . "<br>";
    echo $message;
}

}

function email_Customer_NewApptNotification         ($appointment_id,$debug=0){
### Customer New Appt Notification  needs appt_id only
        require_once('appointment_functions.php');
        $Appointments_dal   = new Appointments_DAL;
        $AppointmentInfo    = $Appointments_dal->Appointments_displayAppointmentInfo($appointment_id);

        require_once('customers_functions.php');
        $Customer_dal       = new Customers_DAL();
        $Customer_Info      = $Customer_dal->get_CustomerDataPerId($AppointmentInfo[0]->customer_id);

        require_once('companies_functions.php');
            $Companies_dal                          = new Companies_DAL();
            $SERVER_ADDRESS                         = $Companies_dal->get_TemplateTabData_by_Name('SERVER_ADDRESS',$Customer_Info[0]->company_id);
            $COMPANY_URL                            = $Companies_dal->get_TemplateTabData_by_Name('COMPANY_URL',$Customer_Info[0]->company_id);
            $COMPANY_NAME                           = $Companies_dal->get_TemplateTabData_by_Name('COMPANY_NAME',$Customer_Info[0]->company_id);

            $NewAppointment_EMAIL_SUBJECT           = $Companies_dal->get_TemplateTabData_by_Name('NewAppointment_EMAIL_SUBJECT',$Customer_Info[0]->company_id);
            $NewAppointment_EMAIL_HEADER_MESSAGE    = $Companies_dal->get_TemplateTabData_by_Name('NewAppointment_EMAIL_HEADER_MESSAGE',$Customer_Info[0]->company_id);
            $NewAppointment_Company_Message         = $Companies_dal->get_TemplateTabData_by_Name('NewAppointment_Company_Message',$Customer_Info[0]->company_id);
            $NewAppointment_EMAIL_REPLY_ADDRESS     = $Companies_dal->get_TemplateTabData_by_Name('NewAppointment_EMAIL_REPLY_ADDRESS',$Customer_Info[0]->company_id);

            $email_subject                          = $COMPANY_NAME[0]->value." - ".$NewAppointment_EMAIL_SUBJECT[0]->value;
            $reply_address                          = $NewAppointment_EMAIL_REPLY_ADDRESS[0]->value;

            ob_start();
            displayAppointmentInfo(0,$appointment_id);
            $AppointmentInfo_Display = ob_get_clean();

            ob_start();
            services_selected_for_appointment($appointment_id);
            $AppointmentServices_Display = ob_get_clean();

            $BGcolor = "#45a853";
            $TopHeader_Display = emailTopHeader($BGcolor,$email_subject,$NewAppointment_EMAIL_HEADER_MESSAGE[0]->value);

            $message = $TopHeader_Display;
            $message.= "
                <tr>
                    <td colspan=\"3\">
                        <table cellpadding=\"0\">
                            <tr>
                                <td width=\"425\" style=\"padding-right:10px; font-family:Trebuchet MS, Verdana, Arial; font-size:12px;\" valign=\"top\">
                                    ".$AppointmentInfo_Display."
                                    ".$AppointmentServices_Display."
                                    <div>
                                    ".$NewAppointment_Company_Message[0]->value."
                                    </div>
                                    <br>
                                    <div>
                                        Best regards,<br/>
                                        ".$COMPANY_NAME[0]->value."<br/>
                                        <a href=\"http://". $COMPANY_URL[0]->value."\">".$COMPANY_URL[0]->value."</a><br/>
                                    </div>
                                </td>
                                <td style=\"border-left:1px solid #e4e4e4; padding-left:15px;\" valign=\"top\">
                                    ".emailRightColumn($Companies_dal,$Customer_Info[0]->company_id)."
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
        </table>
        <br/>
        ".emailBottomTable()."
    </center>
</body>";
$headers  = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=iso-8859-1" . "\r\n";
$headers .= "From: \"".$COMPANY_NAME[0]->value."\" <no-reply@dubbsenterprises.com>\r\n";
$headers .= "Reply-to: \"".$COMPANY_NAME[0]->value."\" <no-reply@dubbsenterprises.com>\r\n";
$headers .= "X-Mailer: PHP/" . phpversion()."\r\n";

if ($debug != 1) {
    mail($Customer_Info[0]->email,$email_subject,$message,$headers);
}
else {
    echo "REPLY TO:" . $reply_address . "<br>";
    echo $message;
}
}
function email_Customer_Cancel_ApptNotification     ($appointment_id,$debug=0){
### Customer Cancel Appt Notification, needs appt_id only
        require_once('appointment_functions.php');
        $Appointments_dal   = new Appointments_DAL;
        $AppointmentInfo    = $Appointments_dal->Appointments_displayAppointmentInfo($appointment_id);

        require_once('customers_functions.php');
        $Customer_dal       = new Customers_DAL();
        $Customer_Info      = $Customer_dal->get_CustomerDataPerId($AppointmentInfo[0]->customer_id);

        require_once('companies_functions.php');
        $Companies_dal      = new Companies_DAL();
            $SERVER_ADDRESS                         = $Companies_dal->get_TemplateTabData_by_Name('SERVER_ADDRESS', $Customer_Info[0]->company_id);
            $COMPANY_URL                            = $Companies_dal->get_TemplateTabData_by_Name('COMPANY_URL',    $Customer_Info[0]->company_id);
            $COMPANY_NAME                           = $Companies_dal->get_TemplateTabData_by_Name('COMPANY_NAME',   $Customer_Info[0]->company_id);
            
            $NewAppointment_EMAIL_REPLY_ADDRESS     = $Companies_dal->get_TemplateTabData_by_Name('NewAppointment_EMAIL_REPLY_ADDRESS',$Customer_Info[0]->company_id);

            $email_subject                          = "Canceled ".$COMPANY_NAME[0]->value." Appointment Confirmation";
            $reply_address                          = $NewAppointment_EMAIL_REPLY_ADDRESS[0]->value;

            ob_start();
            displayAppointmentInfo(2,$appointment_id);
            $AppointmentInfo_Display = ob_get_clean();

            ob_start();
            services_selected_for_appointment($appointment_id);
            $AppointmentServices_Display = ob_get_clean();

            $BGcolor = "red";
            $TopHeader_Display = emailTopHeader($BGcolor,$email_subject,'This is a notification about a canceled appointment.');

            $message = $TopHeader_Display;
            $message.= "
                <tr>
                    <td colspan=\"3\">
                    <!--CONTENT STARTS HERE-->
                        <table cellpadding=\"0\">
                            <tr>
                                <td style=width:75%; padding-right:10px; font-family:Trebuchet MS, Verdana, Arial; font-size:12px; valign=\"top\">
                                    ".$AppointmentInfo_Display."
                                    ".$AppointmentServices_Display."
                                    Best regards,<br/>
                                    ".$COMPANY_NAME[0]->value."<br/>
                                    <a href=\"http://". $COMPANY_URL[0]->value."\">".$COMPANY_URL[0]->value."</a><br/>
                                </td>
                                <td style=width:25%; border-left:1px solid #e4e4e4; padding-left:15px; valign=\"top\">
                                    ".emailRightColumn($Companies_dal,$Customer_Info[0]->company_id)."
                                </td>
                                <br>
                            </tr>
                        </table>
                    </td>
                </tr>
</table>
<br/>
".emailBottomTable()."
</center>
</body>
";

// Always set content-type when sending HTML email
$headers  = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=iso-8859-1" . "\r\n";
$headers .= "From: \"".$COMPANY_NAME[0]->value."\" <no-reply@dubbsenterprises.com>\r\n";
$headers .= "Reply-to: \"".$COMPANY_NAME[0]->value."\" <no-reply@dubbsenterprises.com>\r\n";
$headers .= "X-Mailer: PHP/" . phpversion()."\r\n";

if ($debug != 1) {
    mail($Customer_Info[0]->email,$email_subject,$message,$headers);
}
else {
    echo "REPLY TO:" . $reply_address . "<br>";
    echo $message;
}
}
function email_Customer_ApptReminder                ($appointment_id,$debug=0){
### Customer Cancel Appt Notification, needs appt_id only
        require_once('appointment_functions.php');
        $Appointments_dal    = new Appointments_DAL;
            $AppointmentInfo    = $Appointments_dal->Appointments_displayAppointmentInfo($appointment_id);

        require_once('customers_functions.php');
        $Customer_dal       = new Customers_DAL();
            $Customer_Info  = $Customer_dal->get_CustomerDataPerId($AppointmentInfo[0]->customer_id);

        require_once('companies_functions.php');
        $Companies_dal      = new Companies_DAL();
            $SERVER_ADDRESS                         = $Companies_dal->get_TemplateTabData_by_Name('SERVER_ADDRESS', $Customer_Info[0]->company_id);
            $COMPANY_NAME                           = $Companies_dal->get_TemplateTabData_by_Name('COMPANY_NAME',   $Customer_Info[0]->company_id);
            $COMPANY_URL                            = $Companies_dal->get_TemplateTabData_by_Name('COMPANY_URL',    $Customer_Info[0]->company_id);

            
            $APPT_Reminder_EMAIL_REPLY_ADDRESS      = $Companies_dal->get_TemplateTabData_by_Name('APPT_Reminder_EMAIL_REPLY_ADDRESS',$Customer_Info[0]->company_id);
            $APPT_Reminder_EMAIL_SUBJECT            = $Companies_dal->get_TemplateTabData_by_Name('APPT_Reminder_EMAIL_SUBJECT',$Customer_Info[0]->company_id);
            $APPT_Reminder_HEADER_MESSAGE           = $Companies_dal->get_TemplateTabData_by_Name('APPT_Reminder_HEADER_MESSAGE',$Customer_Info[0]->company_id);

            $email_subject                          = $APPT_Reminder_EMAIL_SUBJECT[0]->value;
            $reply_address                          = $APPT_Reminder_EMAIL_REPLY_ADDRESS[0]->value;

            ob_start();
            displayAppointmentInfo(2,$appointment_id);
            $AppointmentInfo_Display = ob_get_clean();

            ob_start();
            services_selected_for_appointment($appointment_id);
            $AppointmentServices_Display = ob_get_clean();

            $BGcolor = "#45a853";
            $TopHeader_Display = emailTopHeader($BGcolor,$email_subject,$APPT_Reminder_HEADER_MESSAGE[0]->value);

            $message = $TopHeader_Display;
            $message.= "
                    <tr>
                        <td colspan=\"3\">
                        <!--CONTENT STARTS HERE-->
                            <table>
                                <tr>
                                    <td width=\"425\" style=\"padding-right:10px; font-family:Trebuchet MS, Verdana, Arial; font-size:12px;\" valign=\"top\">
                                        ".$AppointmentInfo_Display."
                                        ".$AppointmentServices_Display."
                                        Best regards,<br/>
                                        ".$COMPANY_NAME[0]->value."<br/>
                                        <a href=\"http://". $COMPANY_URL[0]->value."\">".$COMPANY_URL[0]->value."</a><br/>
                                    </td>
                                    <td style=\"border-left:1px solid #e4e4e4; padding-left:15px;\" valign=\"top\">
                                        ".emailRightColumn($Companies_dal,$Customer_Info[0]->company_id)."
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
    </table>
    <br/>
    ".emailBottomTable()."
    </center>
</body>
";

// Always set content-type when sending HTML email
$headers  = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=iso-8859-1" . "\r\n";
$headers .= "From: \"".$COMPANY_NAME[0]->value."\" <no-reply@dubbsenterprises.com>\r\n";
$headers .= "Reply-to: \"".$COMPANY_NAME[0]->value."\" <no-reply@dubbsenterprises.com>\r\n";
$headers .= "X-Mailer: PHP/" . phpversion()."\r\n";

if ($debug != 1) {
    mail($Customer_Info[0]->email,$email_subject,$message,$headers);
}
else {
    echo "REPLY TO:" . $reply_address . "<br>";
    echo $message;
}
}

function email_Employee_NewApptNotification         ($appointment_id,$debug=0){
    ### Employee New Appt Notification  needs appt_id only
    require_once('appointment_functions.php');
    $Appointments_dal       = new Appointments_DAL;
        $AppointmentInfo    = $Appointments_dal->Appointments_displayAppointmentInfo($appointment_id);

    require_once('customers_functions.php'); 
    $Customer_dal           = new Customers_DAL();
        $Customer_Info      = $Customer_dal->get_CustomerDataPerId($AppointmentInfo[0]->customer_id);

    require_once('companies_functions.php');
        $Companies_dal      = new Companies_DAL();
        $SERVER_ADDRESS                         = $Companies_dal->get_TemplateTabData_by_Name('SERVER_ADDRESS', $Customer_Info[0]->company_id);
        $COMPANY_URL                            = $Companies_dal->get_TemplateTabData_by_Name('COMPANY_URL',    $Customer_Info[0]->company_id);
        $COMPANY_NAME                           = $Companies_dal->get_TemplateTabData_by_Name('COMPANY_NAME',   $Customer_Info[0]->company_id);
        $NewAppointment_EMAIL_REPLY_ADDRESS     = $Companies_dal->get_TemplateTabData_by_Name('NewAppointment_EMAIL_REPLY_ADDRESS',$Customer_Info[0]->company_id);

        $email_subject                          = "STAFF: New Appt - ".$Customer_Info[0]->firstname. " " . $Customer_Info[0]->lastname.".";
        $reply_address                          = $NewAppointment_EMAIL_REPLY_ADDRESS[0]->value;

        ob_start();
        displayAppointmentInfo(0,$appointment_id);
        $AppointmentInfo_Display = ob_get_clean();

        ob_start();
        services_selected_for_appointment($appointment_id);
        $AppointmentServices_Display = ob_get_clean();

        $BGcolor = "#45a853";
        $TopHeader_Display = emailTopHeader($BGcolor,$email_subject, "Appt. Alert:<br>".date('l jS \of F Y', strtotime($AppointmentInfo[0]->startDate))."<br>Time: ".date('h:i a', strtotime($AppointmentInfo[0]->startDate)). "<br>".$Customer_Info[0]->firstname." ".$Customer_Info[0]->lastname);

        $message = $TopHeader_Display;
        $message.= "
            <tr> 
                <td colspan=\"3\">
                <!--CONTENT STARTS HERE-->
                    <table cellpadding=\"0\" width=100%>
                        <tr>
                            <td width=\"100%\" style=\"padding-right:10px; font-family:Trebuchet MS, Verdana, Arial; font-size:12px;\" valign=\"top\">
                                <div class='wp100 d_InlineBlock s08' style='display:inline-block; width: 100%;'>
                                    <div class='f_left left wp50 ml5' style='float:left; text-align: left; width:25%'>Customer:</div>
                                    <div class='f_left right wp45' style='float:left; text-align: right; width:75%'>".$Customer_Info[0]->firstname." ".$Customer_Info[0]->lastname.", ".$Customer_Info[0]->email."</div>
                                </div>
                                <div class='wp100 d_InlineBlock s08' style='display:inline-block; width: 100%;'>
                                    <div class='f_left left wp50 ml5' style='float:left; text-align: left; width:25%'>Cust Ph. Number:</div>
                                    <div class='f_left right wp45' style='float:left; text-align: right; width:75%'>".$Customer_Info[0]->phone_num."</div>
                                </div>
                                <div class='wp100 d_InlineBlock s08' style='display:inline-block; width: 100%;'>
                                    <div class='f_left left wp50 ml5' style='float:left; text-align: left; width:25%'>Staff:</div>
                                    <div class='f_left right wp45' style='float:left; text-align: right; width:75%'>".$AppointmentInfo[0]->staff_first_name." ".$AppointmentInfo[0]->staff_last_name.", ".$AppointmentInfo[0]->staff_email_address."</div>
                                </div>
                                <div class='wp100 d_InlineBlock s08' style='display:inline-block; width: 100%;'>
                                    <div class='f_left left wp50 ml5' style='float:left; text-align: left; width:25%'>Date:</div>
                                    <div class='f_left right wp45' style='float:left; text-align: right; width:75%'>".date('l jS \of F Y', strtotime($AppointmentInfo[0]->startDate))."</div>
                                </div>
                                <div class='wp100 d_InlineBlock s08' style='display:inline-block; width: 100%;'>
                                    <div class='f_left left wp50 ml5' style='float:left; text-align: left; width:25%'>Time:</div>
                                    <div class='f_left right wp45' style='float:left; text-align: right; width:75%'>".date('h:i a', strtotime($AppointmentInfo[0]->startDate))."</div>
                                </div>
                                ".$AppointmentServices_Display."
                                <br>
                                Best Regards,<br/>
                                ".$COMPANY_NAME[0]->value."<br/>
                             </td>
                       </tr>
                    </table>
                </td>
            </tr>
</table>
<br/>
".emailBottomTable()."
</center>
</body>
";

$headers  = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=iso-8859-1" . "\r\n";
$headers .= "From: \"".$COMPANY_NAME[0]->value."\" <no-reply@dubbsenterprises.com>\r\n";
$headers .= "Reply-to: \"".$COMPANY_NAME[0]->value."\" <no-reply@dubbsenterprises.com>\r\n";
$headers .= "X-Mailer: PHP/" . phpversion()."\r\n";
if ($debug != 1) {
    mail($AppointmentInfo[0]->staff_email_address,$email_subject,$message,$headers);
} else {
    echo "REPLY TO:" . $reply_address . "<br>";
    echo $message;
}
}
function email_Employee_Cancel_ApptNotification     ($appointment_id,$debug=0){
        ### Customer Appointment Reminder needs appt_id only
        require_once('appointment_functions.php');
        $Appointments_dal    = new Appointments_DAL;
        $AppointmentInfo    = $Appointments_dal->Appointments_displayAppointmentInfo($appointment_id);

        require_once('customers_functions.php');
        $Customer_dal       = new Customers_DAL();
        $Customer_Info  = $Customer_dal->get_CustomerDataPerId($AppointmentInfo[0]->customer_id);

        require_once('companies_functions.php');
        $Companies_dal      = new Companies_DAL();
        $SERVER_ADDRESS                         = $Companies_dal->get_TemplateTabData_by_Name('SERVER_ADDRESS', $Customer_Info[0]->company_id);
        $COMPANY_NAME                           = $Companies_dal->get_TemplateTabData_by_Name('COMPANY_NAME',   $Customer_Info[0]->company_id);
        $COMPANY_URL                            = $Companies_dal->get_TemplateTabData_by_Name('COMPANY_URL',    $Customer_Info[0]->company_id);
        $NewAppointment_EMAIL_REPLY_ADDRESS     = $Companies_dal->get_TemplateTabData_by_Name('NewAppointment_EMAIL_REPLY_ADDRESS',$Customer_Info[0]->company_id);
        $email_subject                          = "STAFF: CANCELED Appt - ".$Customer_Info[0]->firstname. " " . $Customer_Info[0]->lastname.".";
        $reply_address                          = $NewAppointment_EMAIL_REPLY_ADDRESS[0]->value;

        ob_start();
        displayAppointmentInfo(0,$appointment_id);
        $AppointmentInfo_Display = ob_get_clean();

        ob_start();
        services_selected_for_appointment($appointment_id);
        $AppointmentServices_Display = ob_get_clean();

        $BGcolor = "red";
        $TopHeader_Display = emailTopHeader($BGcolor,$email_subject,"Appt. Canceled:<br>".date('l jS \of F Y', strtotime($AppointmentInfo[0]->startDate))."<br>Time: ".date('h:i a', strtotime($AppointmentInfo[0]->startDate)). "<br>" .$Customer_Info[0]->firstname." ".$Customer_Info[0]->lastname);

        $message = $TopHeader_Display;
        $message.= "
    <tr>
        <td colspan=\"3\">
        <!--CONTENT STARTS HERE-->
            <table cellpadding=\"0\" width=100%>
                <tr>
                    <td width='100%' style='padding-right:10px; font-family:Trebuchet MS, Verdana, Arial; font-size:12px;' valign='top'>
                        <div class='wp100 d_InlineBlock s08' style='display:inline-block; width: 100%;'>
                            <div class='f_left left wp50 ml5' style='float:left; text-align: left; width:25%'>Customer:</div>
                            <div class='f_left right wp45' style='float:left; text-align: right; width:75%'>".$Customer_Info[0]->firstname." ".$Customer_Info[0]->lastname.", ".$Customer_Info[0]->email."</div>
                        </div>
                        <div class='wp100 d_InlineBlock s08' style='display:inline-block; width: 100%;'>
                            <div class='f_left left wp50 ml5' style='float:left; text-align: left; width:25%'>Cust Ph. Number:</div>
                            <div class='f_left right wp45' style='float:left; text-align: right; width:75%'>".$Customer_Info[0]->phone_num."</div>
                        </div>
                        <div class='wp100 d_InlineBlock s08' style='display:inline-block; width: 100%;'>
                            <div class='f_left left wp50 ml5' style='float:left; text-align: left; width:20%'>Staff:</div>
                            <div class='f_left right wp45' style='float:left; text-align: right; width:80%'>".$AppointmentInfo[0]->staff_first_name." ".$AppointmentInfo[0]->staff_last_name.", ".$AppointmentInfo[0]->staff_email_address."</div>
                        </div>
                        <div class='wp100 d_InlineBlock s08' style='display:inline-block; width: 100%;'>
                            <div class='f_left left wp50 ml5' style='float:left; text-align: left; width:25%'>Date:</div>
                            <div class='f_left right wp45' style='float:left; text-align: right; width:75%'>".date('l jS \of F Y', strtotime($AppointmentInfo[0]->startDate))."</div>
                        </div>
                        <div class='wp100 d_InlineBlock s08' style='display:inline-block; width: 100%;'>
                            <div class='f_left left wp50 ml5' style='float:left; text-align: left; width:25%'>Time:</div>
                            <div class='f_left right wp45' style='float:left; text-align: right; width:75%'>".date('h:i a', strtotime($AppointmentInfo[0]->startDate))."</div>
                        </div>                     
                        ".$AppointmentServices_Display."
                        <br>
                        Sorry about the bad news,<br/>
                        ".$COMPANY_NAME[0]->value."<br/>
                        <a href=\"http://". $COMPANY_URL[0]->value."\">".$COMPANY_URL[0]->value."</a><br/>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<br />
".emailBottomTable()."
</center>
</body>
";
$headers  = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=iso-8859-1" . "\r\n";
$headers .= "From: \"".$COMPANY_NAME[0]->value."\" <no-reply@dubbsenterprises.com>\r\n";
$headers .= "Reply-to: \"".$COMPANY_NAME[0]->value."\" <no-reply@dubbsenterprises.com>\r\n";
$headers .= "X-Mailer: PHP/" . phpversion()."\r\n";
if ($debug != 1) {
    mail($AppointmentInfo[0]->staff_email_address,$email_subject,$message,$headers);
}
else {
    echo "REPLY TO:" . $reply_address . "<br>";
    echo $message;
}
}

function email_MasterEmail_NewApptNotification      ($appointment_id,$debug=0){
    ### Employee New Appt Notification  needs appt_id only
    require_once('appointment_functions.php');
    $Appointments_dal       = new Appointments_DAL;
        $AppointmentInfo    = $Appointments_dal->Appointments_displayAppointmentInfo($appointment_id);

    require_once('customers_functions.php');
    $Customer_dal           = new Customers_DAL();
        $Customer_Info      = $Customer_dal->get_CustomerDataPerId($AppointmentInfo[0]->customer_id);

    require_once('general_functions.php');
    $General_DAL            = new GENERAL_DAL();
        $master_email_data  = $General_DAL->get_Company_Preference($Customer_Info[0]->company_id,'master_email');
        $master_email       = $master_email_data[0]->value;

    require_once('companies_functions.php');
        $Companies_dal      = new Companies_DAL();
        $SERVER_ADDRESS                         = $Companies_dal->get_TemplateTabData_by_Name('SERVER_ADDRESS', $Customer_Info[0]->company_id);
        $COMPANY_URL                            = $Companies_dal->get_TemplateTabData_by_Name('COMPANY_URL',    $Customer_Info[0]->company_id);
        $COMPANY_NAME                           = $Companies_dal->get_TemplateTabData_by_Name('COMPANY_NAME',   $Customer_Info[0]->company_id);
        $NewAppointment_EMAIL_REPLY_ADDRESS     = $Companies_dal->get_TemplateTabData_by_Name('NewAppointment_EMAIL_REPLY_ADDRESS',$Customer_Info[0]->company_id);

        $email_subject                          = "MANAGER: New Appt: ".$Customer_Info[0]->firstname. " " . $Customer_Info[0]->lastname.".";

        $reply_address                          = $NewAppointment_EMAIL_REPLY_ADDRESS[0]->value;

        ob_start();
        displayAppointmentInfo(0,$appointment_id);
        $AppointmentInfo_Display = ob_get_clean();

        ob_start();
        services_selected_for_appointment($appointment_id);
        $AppointmentServices_Display = ob_get_clean();

        $BGcolor = "#45a853";
        $TopHeader_Display = emailTopHeader($BGcolor,$email_subject, "Manager Appt. Alert:<br>".date('l jS \of F Y', strtotime($AppointmentInfo[0]->startDate))."<br>Time: ".date('h:i a', strtotime($AppointmentInfo[0]->startDate)). "<br>".$Customer_Info[0]->firstname." ".$Customer_Info[0]->lastname);

        $message = $TopHeader_Display;
        $message.= "
            <tr>
                <td colspan=\"3\">
                <!--CONTENT STARTS HERE-->
                    <table cellpadding=\"0\" width=100%>
                        <tr>
                            <td width=\"100%\" style=\"padding-right:10px; font-family:Trebuchet MS, Verdana, Arial; font-size:12px;\" valign=\"top\">
                                <div class='wp100 d_InlineBlock s08' style='display:inline-block; width: 100%;'>
                                    <div class='f_left left wp50 ml5' style='float:left; text-align: left; width:25%'>Customer:</div>
                                    <div class='f_left right wp45' style='float:left; text-align: right; width:75%'>".$Customer_Info[0]->firstname." ".$Customer_Info[0]->lastname.", ".$Customer_Info[0]->email."</div>
                                </div>
                                <div class='wp100 d_InlineBlock s08' style='display:inline-block; width: 100%;'>
                                    <div class='f_left left wp50 ml5' style='float:left; text-align: left; width:25%'>Cust Ph. Number:</div>
                                    <div class='f_left right wp45' style='float:left; text-align: right; width:75%'>".$Customer_Info[0]->phone_num."</div>
                                </div>
                                <div class='wp100 d_InlineBlock s08' style='display:inline-block; width: 100%;'>
                                    <div class='f_left left wp50 ml5' style='float:left; text-align: left; width:25%'>Staff:</div>
                                    <div class='f_left right wp45' style='float:left; text-align: right; width:75%'>".$AppointmentInfo[0]->staff_first_name." ".$AppointmentInfo[0]->staff_last_name.", ".$AppointmentInfo[0]->staff_email_address."</div>
                                </div>
                                <div class='wp100 d_InlineBlock s08' style='display:inline-block; width: 100%;'>
                                    <div class='f_left left wp50 ml5' style='float:left; text-align: left; width:25%'>Date:</div>
                                    <div class='f_left right wp45' style='float:left; text-align: right; width:75%'>".date('l jS \of F Y', strtotime($AppointmentInfo[0]->startDate))."</div>
                                </div>
                                <div class='wp100 d_InlineBlock s08' style='display:inline-block; width: 100%;'>
                                    <div class='f_left left wp50 ml5' style='float:left; text-align: left; width:25%'>Time:</div>
                                    <div class='f_left right wp45' style='float:left; text-align: right; width:75%'>".date('h:i a', strtotime($AppointmentInfo[0]->startDate))."</div>
                                </div>
                                ".$AppointmentServices_Display."
                                <br>
                                Best Regards,<br/>
                                ".$COMPANY_NAME[0]->value."<br/>
                             </td>
                       </tr>
                    </table>
                </td>
            </tr>
</table>
<br/>
".emailBottomTable()."
</center>
</body>
";

$headers  = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=iso-8859-1" . "\r\n";
$headers .= "From: \"".$COMPANY_NAME[0]->value."\" <no-reply@dubbsenterprises.com>\r\n";
$headers .= "Reply-to: \"".$COMPANY_NAME[0]->value."\" <no-reply@dubbsenterprises.com>\r\n";
$headers .= "X-Mailer: PHP/" . phpversion()."\r\n";
if ($debug != 1) {
    mail($master_email,$email_subject,$message,$headers);
} else {
    echo "REPLY TO:" . $reply_address . "<br>";
    echo $message;
}
}
function email_MasterEmail_Cancel_ApptNotification  ($appointment_id,$debug=0){
        ### Customer Appointment Reminder needs appt_id only
        require_once('appointment_functions.php');
        $Appointments_dal    = new Appointments_DAL;
        $AppointmentInfo    = $Appointments_dal->Appointments_displayAppointmentInfo($appointment_id);

        require_once('customers_functions.php');
        $Customer_dal       = new Customers_DAL();
        $Customer_Info  = $Customer_dal->get_CustomerDataPerId($AppointmentInfo[0]->customer_id);

        require_once('general_functions.php');
        $General_DAL            = new GENERAL_DAL();
        $master_email_data  = $General_DAL->get_Company_Preference($Customer_Info[0]->company_id,'master_email');
        $master_email       = $master_email_data[0]->value;

        require_once('companies_functions.php');
        $Companies_dal      = new Companies_DAL();
        $SERVER_ADDRESS                         = $Companies_dal->get_TemplateTabData_by_Name('SERVER_ADDRESS', $Customer_Info[0]->company_id);
        $COMPANY_NAME                           = $Companies_dal->get_TemplateTabData_by_Name('COMPANY_NAME',   $Customer_Info[0]->company_id);
        $COMPANY_URL                            = $Companies_dal->get_TemplateTabData_by_Name('COMPANY_URL',    $Customer_Info[0]->company_id);
        $NewAppointment_EMAIL_REPLY_ADDRESS     = $Companies_dal->get_TemplateTabData_by_Name('NewAppointment_EMAIL_REPLY_ADDRESS',$Customer_Info[0]->company_id);
        $email_subject                          = "MANAGER: CANCELED Appt - ".$Customer_Info[0]->firstname. " " . $Customer_Info[0]->lastname.".";
        $reply_address                          = $NewAppointment_EMAIL_REPLY_ADDRESS[0]->value;

        ob_start();
        displayAppointmentInfo(0,$appointment_id);
        $AppointmentInfo_Display = ob_get_clean();

        ob_start();
        services_selected_for_appointment($appointment_id);
        $AppointmentServices_Display = ob_get_clean();

        $BGcolor = "red";
        $TopHeader_Display = emailTopHeader($BGcolor,$email_subject,"Appt. Canceled:<br>".date('l jS \of F Y', strtotime($AppointmentInfo[0]->startDate))."<br>Time: ".date('h:i a', strtotime($AppointmentInfo[0]->startDate)). "<br>" .$Customer_Info[0]->firstname." ".$Customer_Info[0]->lastname);

        $message = $TopHeader_Display;
        $message.= "
    <tr>
        <td colspan=\"3\">
        <!--CONTENT STARTS HERE-->
            <table cellpadding=\"0\" width=100%>
                <tr>
                    <td width='100%' style='padding-right:10px; font-family:Trebuchet MS, Verdana, Arial; font-size:12px;' valign='top'>
                        <div class='wp100 d_InlineBlock s08' style='display:inline-block; width: 100%;'>
                            <div class='f_left left wp50 ml5' style='float:left; text-align: left; width:25%'>Customer:</div>
                            <div class='f_left right wp45' style='float:left; text-align: right; width:75%'>".$Customer_Info[0]->firstname." ".$Customer_Info[0]->lastname.", ".$Customer_Info[0]->email."</div>
                        </div>
                        <div class='wp100 d_InlineBlock s08' style='display:inline-block; width: 100%;'>
                            <div class='f_left left wp50 ml5' style='float:left; text-align: left; width:25%'>Cust Ph. Number:</div>
                            <div class='f_left right wp45' style='float:left; text-align: right; width:75%'>".$Customer_Info[0]->phone_num."</div>
                        </div>
                        <div class='wp100 d_InlineBlock s08' style='display:inline-block; width: 100%;'>
                            <div class='f_left left wp50 ml5' style='float:left; text-align: left; width:20%'>Staff:</div>
                            <div class='f_left right wp45' style='float:left; text-align: right; width:80%'>".$AppointmentInfo[0]->staff_first_name." ".$AppointmentInfo[0]->staff_last_name.", ".$AppointmentInfo[0]->staff_email_address."</div>
                        </div>
                        <div class='wp100 d_InlineBlock s08' style='display:inline-block; width: 100%;'>
                            <div class='f_left left wp50 ml5' style='float:left; text-align: left; width:25%'>Date:</div>
                            <div class='f_left right wp45' style='float:left; text-align: right; width:75%'>".date('l jS \of F Y', strtotime($AppointmentInfo[0]->startDate))."</div>
                        </div>
                        <div class='wp100 d_InlineBlock s08' style='display:inline-block; width: 100%;'>
                            <div class='f_left left wp50 ml5' style='float:left; text-align: left; width:25%'>Time:</div>
                            <div class='f_left right wp45' style='float:left; text-align: right; width:75%'>".date('h:i a', strtotime($AppointmentInfo[0]->startDate))."</div>
                        </div>
                        ".$AppointmentServices_Display."
                        <br>
                        Sorry about the bad news,<br/>
                        ".$COMPANY_NAME[0]->value."<br/>
                        <a href=\"http://". $COMPANY_URL[0]->value."\">".$COMPANY_URL[0]->value."</a><br/>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<br />
".emailBottomTable()."
</center>
</body>
";

$headers  = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=iso-8859-1" . "\r\n";
$headers .= "From: \"".$COMPANY_NAME[0]->value."\" <no-reply@dubbsenterprises.com>\r\n";
$headers .= "Reply-to: \"".$COMPANY_NAME[0]->value."\" <no-reply@dubbsenterprises.com>\r\n";
$headers .= "X-Mailer: PHP/" . phpversion()."\r\n";        
      
if ($debug != 1) {
    mail($master_email,$email_subject,$message,$headers);
}
else {
    echo "REPLY TO:" . $reply_address . "<br>";
    echo $message;
}
}

    function emailTopHeader($BGcolor,$email_subject,$message_text){
$html="
<html>
<head>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" />
<title>Appointment Reminder</title>
</head>
<body>
    <center>
    <table width=\"600\" background=\"#FFFFFF\" style=\"text-align:left;\" cellpadding=\"0\" cellspacing=\"0\">
        <!--GREEN STRIPE-->
        <tr>
            <td bgcolor=\"".$BGcolor."\" style=\"border-top:1px solid #FFF; border-bottom:1px solid #FFF;\" width=\"10%\" height=\"113\">
                <div style=\"line-height: 0px; font-size: 1px; position: absolute;\">&nbsp;</div>
            </td>
            <!--WHITE TEXT AREA-->
            <td style=\"border-top:1px solid #FFF; text-align:center;\" height=\"113\" width=\"40%\" bgcolor=\"white\" valign=\"middle\">
                <span style=\"font-size:25px; font-family:Trebuchet MS, Verdana, Arial; color:".$BGcolor.";\">".$email_subject."</span>
            </td>
            <!--GREEN TEXT AREA-->
            <td bgcolor=\"".$BGcolor."\" style=\"border-top:1px solid #FFF; border-bottom:1px solid #FFF; padding-left:15px;\" height=\"113\">
                <span style=\"color:#FFFFFF; font-size:18px; font-family:Trebuchet MS, Verdana, Arial;\">$message_text&nbsp;</span>
            </td>
        </tr>
        ";
return $html;
}
    function emailRightColumn($Companies_dal,$company_id) {
    $SERVER_ADDRESS = $Companies_dal->get_TemplateTabData_by_Name('SERVER_ADDRESS',$company_id);
    $Registration_EMAIL_REPLY_ADDRESS = $Companies_dal->get_TemplateTabData_by_Name('Registration_EMAIL_REPLY_ADDRESS',$company_id);
    $html = "
            <!--RIGHT COLUMN FIRST BOX-->
            <table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" style=\"border-bottom:1px solid #e4e4e4; font-family:Trebuchet MS, Verdana, Arial; font-size:12px;\">
            <tr>
            <td>
            <div style=\"font-family:Trebuchet MS, Verdana, Arial; font-size:17px; font-weight:bold; padding-bottom:10px;\">Add Us To Your Address Book</div>
            <img src=\"http://".$SERVER_ADDRESS[0]->value."/common_includes/includes/images/addressbook.gif\" align=\"right\" style=\"padding-left:10px; padding-top:10px; padding-bottom:10px;\" alt=\"\"/>
            <p>To help ensure that you receive all email messages consistently in your inbox with images displayed, please add this address to your address book or contacts list: <strong>".$Registration_EMAIL_REPLY_ADDRESS[0]->value."</strong>.</p>
            <br />
            </td>
            </tr>
            </table>

            <!--RIGHT COLUMN SECOND BOX-->
            <br />
            <table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" style=\"border-bottom:1px solid #e4e4e4; font-family:Trebuchet MS, Verdana, Arial; font-size:12px;\">
            <tr>
            <td>
            <div style=\"font-family:Trebuchet MS, Verdana, Arial; font-size:17px; font-weight:bold; padding-bottom:10px;\">Have Any Questions?</div>
            <img src=\"http://".$SERVER_ADDRESS[0]->value."/common_includes/includes/images/penpaper.gif\" align=\"right\" style=\"padding-left:10px; padding-top:10px; padding-bottom:10px;\" alt=\"\"/>
            <p>Don't hesitate to hit the reply button to any of the messages you receive.</p>
            <br />
            </td>
            </tr>
            </table>

            <!--RIGHT COLUMN THIRD BOX-->
            <br />
            <table cellpadding=\"0\" width=\"100%\" cellspacing=\"0\" style=\"font-family:Trebuchet MS, Verdana, Arial; font-size:12px;\">
            <tr>
            <td>
            <div style=\"font-family:Trebuchet MS, Verdana, Arial; font-size:17px; font-weight:bold; padding-bottom:10px;\">Have A Topic Idea?</div>
            <img src=\"http://".$SERVER_ADDRESS[0]->value."/common_includes/includes/images/lightbulb.gif\" align=\"right\" style=\"padding-left:10px; padding-top:10px; padding-bottom:10px;\" alt=\"\"/>
            <p>I'd love to hear it! Just reply any time and let me know what topics you'd like to know more about.</p>

            <br />
            </td>
            </tr>
            </table>
    ";
    return $html;
    }
    function emailHeightTDs($height) {
    $html = "
            <td height=\"".$height."\" width=\"31\" style=\"border-bottom:1px solid #e4e4e4;\">
            <div style=\"line-height: 0px; font-size: 1px; position: absolute;\">&nbsp;</div>
            </td>
            <td height=\"".$height."\" width=\"131\">
            <div style=\"line-height: 0px; font-size: 1px; position: absolute;\">&nbsp;</div>
            </td>
            <td height=\"".$height."\" width=\"466\" style=\"border-bottom:1px solid #e4e4e4;\">
            <div style=\"line-height: 0px; font-size: 1px; position: absolute;\">&nbsp;</div>
            </td>
    ";
    }
    function emailBottomTable(){
$html = "
    <table cellpadding=\"0\" style=\"border-top:1px solid #e4e4e4; text-align:center; font-family:Trebuchet MS, Verdana, Arial; font-size:12px;\" cellspacing=\"0\" width=\"600\">
        <tr>
            <td height=\"2\" style=\"border-bottom:1px solid #e4e4e4;\">
                <div style=\"line-height: 0px; font-size: 1px; position: absolute;\">&nbsp;</div>
            </td>
        </tr>
        <tr>
            <td style=\"font-family:Trebuchet MS, Verdana, Arial; font-size:12px;\">
            <br>
            </td>
        </tr>
</table>";
return $html;
}

if(!function_exists('json_encode')){function json_encode($a=false){
        // Some basic debugging to ensure we have something returned
        if (is_null($a)) return 'null';
        if ($a === false) return 'false';
        if ($a === true) return 'true';
        if (is_scalar($a))
        {
                if (is_float($a))
                {
                        // Always use '.' for floats.
                        return floatval(str_replace(',', '.', strval($a)));
                }
                if (is_string($a))
                {
                        static $jsonReplaces = array(array('\\', '/', "\n", "\t", "\r", "\b", "\f", '"'), array('\\\\', '\\/', '\\n', '\\t', '\\r', '\\b', '\\f', '\"'));
                        return '"' . str_replace($jsonReplaces[0], $jsonReplaces[1], $a) . '"';
                }
                else
                        return $a;
        }
        $isList = true;
        for ($i = 0, reset($a); true; $i++) {
                if (key($a) !== $i)
                {
                        $isList = false;
                        break;
                }
        }
        $result = array();
        if ($isList)
        {
                foreach ($a as $v) $result[] = json_encode($v);
                return '[' . join(',', $result) . ']';
        }
        else
        {
                foreach ($a as $k => $v) $result[] = json_encode($k).':'.json_encode($v);
                return '{' . join(',', $result) . '}';
        }
}}
?>