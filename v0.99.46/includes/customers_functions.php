<?php
include_once('general_functions.php');
include_once('reports_functions.php');
include_once('profiles_functions.php');
include_once('appointment_functions.php');

class Customers_DAL {
  public function __construct(){}
  public function get_AllCustomersPerCompanyId($company_id,$totals,$active=1){
    if ($totals == 0) {
        $sql = "SELECT  c.id as id,
                        c.status as status,
                        c.email as email,
                        c.firstname as firstname ,
                        c.surname as lastname,
                        c.phone_num as phone_num,
                        c.email_promotions as email_promotions";
    }
    ELSE if ($totals == 2) {
        $sql ="SELECT  c.id as id,
                        c.status as status,
                        c.email as email,
                        c.firstname as firstname ,
                        c.surname as lastname,
                        c.phone_num as phone_num,
                        c.email_promotions as email_promotions ";
    }
    ELSE {
        $sql ="SELECT count(c.id) as count ";
    }

    $sql.= " from customers c
           where c.company_id = $company_id ";
    if ( isset($_SESSION['search_data']['Customers_AllCustomers']['customer_search_inactive_customers'])                   && $_SESSION['search_data']['Customers_AllCustomers']['customer_search_inactive_customers'] == 1 ){
        $sql .= " and ( c.status in (0,1) ) ";
    } else {
        $sql .= " and ( c.status = 1 ) ";
    }
    if ( isset($_SESSION['search_data']['Customers_AllCustomers']['customer_search_first_name'])                           && $_SESSION['search_data']['Customers_AllCustomers']['customer_search_first_name'] != -1 )                    {$sql .= " and c.firstname  like '%" . $_SESSION['search_data']['Customers_AllCustomers']['customer_search_first_name'] . "%' "; }
    if ( isset($_SESSION['search_data']['Customers_AllCustomers']['customer_search_last_name'])                            && $_SESSION['search_data']['Customers_AllCustomers']['customer_search_last_name'] != -1 )                     {$sql .= " and c.surname    like '%" . $_SESSION['search_data']['Customers_AllCustomers']['customer_search_last_name'] . "%' "; }
    if ( isset($_SESSION['search_data']['Customers_AllCustomers']['customer_search_email'])                                && $_SESSION['search_data']['Customers_AllCustomers']['customer_search_email'] != -1 )                         {$sql .= " and c.email      like '%" . $_SESSION['search_data']['Customers_AllCustomers']['customer_search_email'] . "%' "; }
    if ( isset($_SESSION['search_data']['Customers_AllCustomers']['customer_search_phone_number'])                         && $_SESSION['search_data']['Customers_AllCustomers']['customer_search_phone_number'] != -1 )                  {$sql .= " and c.phone_num  like '%" . $_SESSION['search_data']['Customers_AllCustomers']['customer_search_phone_number'] . "%' "; }

    if ($totals == 0) {
        if (!(isset($_SESSION['search_data']['column']))) { $sql.= " order by c.id desc"; }
        else { $sql.= " order by " . $_SESSION['search_data']['column'] . " " . $_SESSION['search_data']['asc_desc'] . " "; }

        if ( isset($_SESSION['search_data']['paging_page']) && $_SESSION['search_data']['paging_page'] != 0) {
            if ( $_SESSION['search_data']['paging_page'] == 1) { $limit_offset = 0; }
            else { $limit_offset = ( ($_SESSION['search_data']['paging_page'] - 1) * 10 ) ; }
            $sql .= " limit $limit_offset, 10 ";
        }
    }
    #if ($totals != 1 ) { print $sql . "\n"; }
    return $this->query($sql);
  }
  public function get_addresses_per_customer_id($customer_id){
    $sql = "SELECT address_id,address_line1,address_line2,city,state,zipcode,country,default_address
      from addresses
      where customer_id = $customer_id ;";
    #echo $sql;
    return $this->query($sql);
  }
  public function get_address_data_by_address_id($address_id){
    $sql = "SELECT address_line1,address_line2,city,state,zipcode
        from addresses
        where address_id = $address_id ";
    return $this->query($sql);
  }
  public function get_CustomerDataPerId($customer_id){
    $sql = "SELECT id, company_id, firstname, surname as lastname, email, email as email_address, phone_num, convert_tz(added, 'utc', ".quoteSmart($_SESSION['preferences']['timezone']).") as added, email_promotions
      from customers
      where id = $customer_id";
    #print $sql;
    return $this->query($sql);
  }
  public function get_CustomerDataPerEmail($email_address,$company_id){
    $sql = "SELECT id,firstname,surname,phone_num,email from customers where email = '$email_address' and company_id = $company_id;";
    return $this->query($sql);
    #print $sql;
  }
  public function get_CustomerRandomCustomer_id($company_id) {
    $sql = "SELECT id from customers where company_id = $company_id order by rand() limit 1";
    return $this->query($sql);
    #print $sql;      
  }

  public function get_Customer_Sale_Count($company_id, $customer_id){
      $sql = "SELECT count(id) as count from sales where customer_id = $customer_id and company_id = $company_id and deleted is NULL;";
    #print "$sql";
    return $this->query($sql);
  }
  public function get_Customer_Return_Count($company_id, $customer_id){
      $sql = "SELECT count(id) as count from returns where customer_id = $customer_id and company_id = $company_id and deleted is NULL;";
    #print "$sql";
    return $this->query($sql);
  }
  public function get_Customer_Appt_Count($company_id, $customer_id){
      $sql = "SELECT count(id)as count from appointments where customer_id = $customer_id and company_id = $company_id and status != 1;";
    #print "$sql";
    return $this->query($sql);
  }
  public function get_Customer_Canceled_Appt_Count($company_id, $customer_id){
      $sql = "SELECT count(id) as count from appointments where customer_id = $customer_id and company_id = $company_id and status = 1;";
    #print "$sql";
    return $this->query($sql);
  }

  public function get_Customer_Appointment_info($appointment_id){
      $sql = " SELECT i.name,i.price,i.est_time_mins
                from appointments_services as appt_s
                join items i on appt_s.service_id = i.id
                where appt_s.appointment_id = $appointment_id";
    #print "$sql";
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
      $sql = "SELECT image_id, image_db_id
                from item_image_mappings
                where id = $customer_id and
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

function customers() {
?>
<head>
<script src="includes/<?=__FUNCTION__?>_functions.js" type="text/javascript"></script>
</head>
<div class="ReportsTopRow main_bc_color2 main_color2_text"><a title="Customers" onclick="mainDiv('Customers'); return false;">Customers</a></div>
    <div style="max-height: 1000px;" class="f_left wp100 hp94">
        <div class="leftSpace main_bc_color2 main_color2_text">&nbsp;</div>
        <div class="middleSpace wp96">
            <div class="f_left wp100 hp10">
                <div class="f_left hp100 wp35 left vtop no-overflow">
                    <img alt="" class="hp90"  src="/common_includes/includes/images/icon_profiles_50.jpg">
                    Customer Profiles
                </div>
                <div class="f_right hp100 wp50 right">&nbsp;
                    <? if (!isset($_SESSION['edit_customers']['UserAdd']) && $_SESSION['settings'][$_SESSION['settings']['login_id']]['level'] >= 1) { ?>
                    <a onclick="Customer_AddCustomer()" href="javascript: none();">
                        <img alt="" class="hp90"  src="/common_includes/includes/images/group-user-add.png" style="border-style: none">
                    </a>
                    <? } ?>
                </div>
            </div>
            <? 
            if (!isset($_SESSION['edit_customers']['customer_id']) && !isset($_SESSION['edit_customers']['CustomerAdd'])) { ?>
            <div class="f_left wp100 hp90">
                <div class="f_left wp100 hp100" >
                    <div class="f_left wp15 hp100">
                        <div class="d_InlineBlock wp100 hp100" >
                            <?=CustomerSearchStanza() ?>
                        </div>
                    </div>
                    <div class="f_right wp85 hp100">
                        <div class="d_InlineBlock wp100 hp100" id="Customers_AllCustomersBodyCenter">
                            <?=CustomersStanza()?>
                        </div>
                    </div>
                </div>
            </div>
            <?}
            elseif (isset($_SESSION['edit_customers']['CustomerAdd'])) {
                CustomerAddStanza();
            }
            else{?>
                <? editCustomerStanza($_SESSION['edit_customers']['customer_id']);?>
            <? } ?>
        </div>
        <div class="rightSpace main_bc_color2 main_color2_text">&nbsp;</div>
    </div>
<div class="ReportsBottomRow main_bc_color2 main_color2_text">&nbsp;</div>
<?
}

function CustomersStanza() {
?>
        <div class="wp100 hp07" id="listing_search_paging_top">
            <? showPaging(); ?>
        </div>
        <div class="wp100 hp85 scrolling">
            <? customersHeader(); ?>
            <? customersAllCustomers(); ?>
        </div>
        <div class="wp100 hp07" id="listing_search_paging_bottom">
            <? showPaging(); ?>
        </div>
<?
}
  function customersHeader() {
?>
    <div class="f_left wp100 h25px s08 HEADER main_bc_color1 main_color1_text">
        <div class="report_header_cell_wp04 mp"><a onclick="orderBy('id','Customers_AllCustomers'); return false;">ID#</a></div>
        <div class="report_header_cell_wp30 mp"><a onclick="orderBy('lastname','Customers_AllCustomers'); return false;">Last Name</a>,&nbsp;<a onclick="orderBy('firstname','Customers_AllCustomers'); return false;">First Name</a></div>
        <div class="report_header_cell_wp25 mp"><a onclick="orderBy('email','Customers_AllCustomers'); return false;">E-mail</a></div>
        <div class="report_header_cell_wp05 ">Call</div>
        <div class="report_header_cell_wp05 ">Sales</div>
        <div class="report_header_cell_wp05 ">Appts</div>
        <div class="report_header_cell_wp05 s06">Canceled Appts</div>
        <div class="report_header_cell_wp09 mp"><a onclick="orderBy('status','Customers_AllCustomers'); return false;">Status</a></div>
        <div class="report_header_cell_wp09">Edit</div>
    </div>
<?
}
  function customersAllCustomers() {
    $customers_dal = new Customers_DAL();
    $customers = $customers_dal->get_AllCustomersPerCompanyId($_SESSION['settings']['company_id'],0);
    $altClass = "bctr1a";
    if (count($customers) >0 ) {
        foreach($customers as $customer){
        $Customer_Sale_Count            = $customers_dal->get_Customer_Sale_Count($_SESSION['settings']['company_id'],$customer->id);
        $Customer_Return_Count          = $customers_dal->get_Customer_Return_Count($_SESSION['settings']['company_id'],$customer->id);
        $Customer_Appt_Count            = $customers_dal->get_Customer_Appt_Count($_SESSION['settings']['company_id'],$customer->id);
        $Customer_Canceled_Appt_Count   = $customers_dal->get_Customer_Canceled_Appt_Count($_SESSION['settings']['company_id'],$customer->id);

        if     ($customer->status == 0) {   $status_action = "INactive"; $status_class = "red"    ;
                                            $action = 1; $alt="Activate?";}
        elseif ($customer->status == 1) {   $status_action = "Active"  ; $status_class = "green"  ;
                                            $action = 0; $alt="DeActivate Login?";}
        ?>
            <div class="f_left wp100 lh20 s07 <?=$altClass?>">
                <div class="report_data_cell_wp04"><?=$customer->id?></div>
                <div class="report_data_cell_wp30 ">
                    <div class="f_left left  hp100 pl5 no-overflow "><?=$customer->lastname?>,&nbsp;<?=$customer->firstname?></div>
                    <div class="f_right right hp100" id="Customers_Send_Registration_Email_<?=$customer->id?>">
                        <a onclick="Customers_Send_Email(<?=$customer->id?>,'Registration','Customers_Send_Registration_Email_<?=$customer->id?>')">
                            <img alt="" src="/common_includes/includes/images/email_icon_small.png" class="mp" title="Send Registration Email?">
                        </a>
                    </div>
                </div>
                <div class="report_data_cell_wp25"><?=$customer->email?></div>
                <div class="report_data_cell_wp05" title="<?=$customer->phone_num?>" id="Customer_Employee_Call_Customer_<?=$customer->id?>" onclick="Customer_Employee_Call_Customer(<?=$customer->id?>,'+17734564205','+13123714779')">&nbsp;<img alt="" height="12" src="/common_includes/includes/images/phone_call.png" style="border-style: none"></div>
                <div class="report_data_cell_wp05">&nbsp;<?=$Customer_Sale_Count[0]->count?></div>
                <div class="report_data_cell_wp05">&nbsp;<?=$Customer_Appt_Count[0]->count?></div>
                <div class="report_data_cell_wp05">&nbsp;<?=$Customer_Canceled_Appt_Count[0]->count?></div>
                <div class="report_data_cell_wp09" title="<?=$alt?>">
                    <input alt="<?=$alt?>" onclick="Customer_UpdStatus(<?=$customer->id?>,<?=$action?>)" type="submit" value="<?=$status_action?>" class="button s07 <?=$status_class?>">
                </div>
                <div class="report_data_cell_wp09">
                    <input onclick="Customer_editProfile(<?=$customer->id?>)" type="submit" value="EDIT" class="button s07">
                </div>
            </div>
        <?
        if ($altClass == "bctr1a") { $altClass = "bctr1b"; } else {$altClass = "bctr1a";}
        }
     }
     else { ?>
                <div class="center wp100">There are no customers currently in the database meeting your search criteria.</div>
     <? }
}

function editCustomerStanza($customer_id) {
$customers_dal = new Customers_DAL();
?>
<div class="d_InlineBlock wp100 hp90">
    <div class="d_InlineBlock wp95 hp100">
        <?=customers_editCustomerTop($customers_dal,$customer_id); ?>
        <?=customers_editCustomerTabs($customers_dal,$customer_id); ?>
        <?
        if (isset($_SESSION['edit_customers']['ActiveTab']) && $_SESSION['edit_customers']['ActiveTab'] == "CustomerElectronicInfo"){
            customerElectronicAddress($customers_dal,$customer_id);
        }

        if (isset($_SESSION['edit_customers']['ActiveTab']) && $_SESSION['edit_customers']['ActiveTab'] == "CustomerPhysicalAddress"){
            customerPhysicalAddress($customers_dal,$customer_id);
        }

        if (isset($_SESSION['edit_customers']['ActiveTab']) && $_SESSION['edit_customers']['ActiveTab'] == "CustomerAppointmentHistory" ) {
            CustomerAppointmentHistory($customers_dal,$customer_id);
        }

        if (isset($_SESSION['edit_customers']['ActiveTab']) && $_SESSION['edit_customers']['ActiveTab'] == "CustomerAttributes" || !isset($_SESSION['edit_customers']['ActiveTab']) ) {
            customerLoginSummary($customers_dal,$customer_id);
        }
        ?>
    </div>
</div>
<?
}
  function customers_editCustomerTop($customers_dal,$customer_id) {
       $image_id_data = $customers_dal->get_default_Customer_ImageID($customer_id);
       $customer_data = $customers_dal->get_CustomerDataPerId($customer_id);
?>
    <div class="bctrt wp100 hp20 d_InlineBlock">
        <div class="f_left wp25 hp100">
            <? show_Image($image_id_data)?>
        </div>
        <div class="f_left  wp50 hp100">
            <div class="f_left hp50 wp100 s19">&nbsp;<?=$customer_data[0]->firstname?> <?=$customer_data[0]->lastname?></div><br>
            <div class="f_left hp50 wp100 s10">Added<br><?=$customer_data[0]->added?></div>
        </div>
        <div class="f_right wp25 hp100" >
            &nbsp;
        </div>
    </div>
<?
}
  function customers_editCustomerTabs($customers_dal,$customer_id){
$activeTabBackground = "bctrt";

if (isset($_SESSION['edit_customers']['ActiveTab']) && $_SESSION['edit_customers']['ActiveTab'] == "CustomerElectronicInfo"){
    $CustomerElectronicInfoBackground = 'bctrt';
} else { $CustomerElectronicInfoBackground = ''; }

if (isset($_SESSION['edit_customers']['ActiveTab']) && $_SESSION['edit_customers']['ActiveTab'] == "CustomerPhysicalAddress"){
    $CustomerPhysicalAddressBackground = 'bctrt';
} else { $CustomerPhysicalAddressBackground = ''; }

if (isset($_SESSION['edit_customers']['ActiveTab']) && $_SESSION['edit_customers']['ActiveTab'] == "CustomerAppointmentHistory"){
    $CustomerAppointmentHistoryBackground = 'bctrt';
} else { $CustomerAppointmentHistoryBackground = ''; }

if (isset($_SESSION['edit_customers']['ActiveTab']) && $_SESSION['edit_customers']['ActiveTab'] == "CustomerAttributes" ||  !isset($_SESSION['edit_customers']['ActiveTab']) ) {
    $CustomerAttributesBackground = 'bctrt';
} else { $CustomerAttributesBackground = ''; }

?>
    <div class="wp100 hp05 f_left">
        <div onclick="Customer_ActiveLoginTabs('CustomerAttributes');"          class="f_left s08 wp20 hp100 mp <?=$CustomerAttributesBackground?>" >Customer Info</div>
        <div onclick="Customer_ActiveLoginTabs('CustomerElectronicInfo');"      class="f_left s08 wp20 hp100 mp <?=$CustomerElectronicInfoBackground?>" >Electronic Info</div>
        <div onclick="Customer_ActiveLoginTabs('CustomerPhysicalAddress');"     class="f_left s08 wp20 hp100 mp <?=$CustomerPhysicalAddressBackground?>" >Physical Address</div>
        <div onclick="Customer_ActiveLoginTabs('CustomerAppointmentHistory');"  class="f_left s08 wp20 hp100 mp <?=$CustomerAppointmentHistoryBackground?>" >Appointment History</div>
    </div>
<?
}
      function customerLoginSummary($customers_dal,$customer_id){
        $customer_data = $customers_dal->get_CustomerDataPerId($customer_id);
    ?>
        <div class="wp100 d_InlineBlock ">
            <div class="f_left wp100 bctrt center">Customer Basic Information</div>
        </div>
        <div class="box5 wp100">
            <div class="wp100 d_InlineBlock bctrt">
                <div class="f_left wp25 bctrt">First Name</div>
                <div class="f_left wp25 bctrt">Last Name</div>
                <div class="f_left wp25 bctrt" >Username</div>
                <div class="f_left wp10 bctrt" >&nbsp;</div>
                <div class="f_left wp15 bctrt" >&nbsp;</div>
            </div>

            <div class="wp100 d_InlineBlock bclightgray">
                <div id="customerLoginSummary_firstname" class="f_left wp25">
                    <input type="text" class="w90 " value="<?=$customer_data[0]->firstname?>" id="editCustomer_firstname">
                </div>
                <div id="customerLoginSummary_lastname" class="f_left wp25">
                    <input type="text" class="w90 " value="<?=$customer_data[0]->lastname?>" id="editCustomer_lastname">
                </div>
                <div class="f_left wp25" >
                    &nbsp;<?=$customer_data[0]->username?>
                </div>
                <div class="f_left wp10">
                   &nbsp;
                </div>
                <div class="f_left wp15">
                    <input type="submit" class="button" value="Update" onclick="Customer_UpdProfileAttributes(<?=$customer_id?>)">
                </div>
            </div>
            <div class="wp100 d_InlineBlock bclightgray">
                <div class="f_left wp20 bctrt right s08 lh20">New Password</div>
                <div class="f_left left wp80 textIndent15">
                <div id="profileLoginSummary_password" class="f_left left w215 h20px textIndent15">
                    <input type="password" class="w70" id="editProfile_login_password">
                </div>
                </div>
            </div>
            <div class="wp100 d_InlineBlock bclightgray">
                <div class="f_left wp20 bctrt right s08 lh20">Confirm</div>
                <div class="f_left left wp80 textIndent15">
                <div id="profileLoginSummary_password2" class="f_left left w215 h20px textIndent15">
                    <input type="password" class="w70" id="editProfile_login_password2">
                </div>
                </div>
            </div>
        </div>

        <div class="wp100 d_InlineBlock h02px">
            <div class="f_left wp100 " >&nbsp;</div>
        </div>
        <?
            $general_dal = new GENERAL_DAL();
            upload_file_stanza('customer',$general_dal,$customer_id);
    }
      function customerElectronicAddress($customers_dal,$customer_id){
    $textarea_dimensions = 'wp100';
    $keep_or_not         = '0';
    $address_data = $customers_dal->get_CustomerDataPerId($customer_id)
    ?>
        <div class="wp100 d_InlineBlock ">
            <div class="f_left wp100 bctrt center">Electronic Contact Information</div>
        </div>
        <div class="box5">
            <div class="wp100 h20px d_InlineBlock bclightgray">
                <div class="f_left wp20 hp100 bctrt right">Customer Email</div>
                <div id="CustomerSummary_email_address" class="f_left left  wp40 hp100 textIndent15">
                    <input type="hidden" id="dynamic_pannel_css_email" value="wp90 bclightgreen">
                    <input type="hidden" id="dynamic_pannel_email_keep" value="0" class="ml5" >
                    <input type="text"   id="dynamic_pannel_email"           value="<?=$address_data[0]->email_address?>" class="wp90">
                </div>
                <div class="f_left wp30 hp100 s06 center red no-overflow" id="dynamic_pannel_email_error">&nbsp;</div>
                <div class="f_left wp05 hp100">
                    <input type="submit" class="button" value="Update" onclick="Customer_editCustomer_UpdElectronicInfo(<?=$customer_id?>,'email')">
                </div>
            </div>

            <div class="wp100 h20px d_InlineBlock bclightgray">
                <div class="f_left wp20 hp100 bctrt right">Customer Phone Number</div>
                <div id="CustomerSummary_phone_num" class="f_left left  wp40 hp100 textIndent15">
                    <input type="hidden" id="dynamic_pannel_css_phone_num" value="wp40 bclightgreen">
                    <input type="hidden" id="dynamic_pannel_phone_num_keep" value="0" class="ml5" >
                    <input type="text"   id="dynamic_pannel_phone_num"           value="<?=$address_data[0]->phone_num?>" class="wp40 ">
                </div>
                <div class="f_left wp30 hp100 s06 center red no-overflow" id="dynamic_pannel_phone_num_error">&nbsp;</div>
                <div class="f_left wp05 hp100">
                    <input type="submit" class="button" value="Update" onclick="Customer_editCustomer_UpdElectronicInfo(<?=$customer_id?>,'phone_num')">
                </div>
            </div>
            <div class="wp100 h20px d_InlineBlock bclightgray">
                <div class="f_left wp20 hp100 bctrt right">Email Promotions</div>
                <div id="CustomerSummary_email_promotions" class="f_left left  wp40 hp100 textIndent15">
                    <input type="hidden" id="dynamic_pannel_css_email_promotions" value="wp10 bclightgreen">
                    <input type="hidden" id="dynamic_pannel_email_promotions_keep" value="0" class="ml5" >
                    <? $selected =  ($address_data[0]->email_promotions == 0 || !$address_data[0]->email_promotions ) ? "selected" : "" ;?>
                    <select id="dynamic_pannel_email_promotions" class="wp20">
                        <option <?=$selected?> value="1">Yes</option>
                        <option <?=$selected?> value="0">No</option>
                    </select>
                </div>
                <div class="f_left wp30 hp100 s06 center red no-overflow" id="dynamic_pannel_email_promotions_error">&nbsp;</div>
                <div class="f_left wp05 hp100">
                    <input type="submit" class="button" value="Update" onclick="Customer_editCustomer_UpdElectronicInfo(<?=$customer_id?>,'email_promotions')">
                </div>
            </div>
        </div>
    <?
    }
      function customerPhysicalAddress($customers_dal,$customer_id){
                $address_data[0] = array();
                $style = " style=\"text-align: right;\"";
                $bg_color = "#FFFFFF";
    ?>
                <div class="wp100 d_InlineBlock ">
                    <div class="f_left wp100 bctrt center">Physical Address Information</div>
                </div>
                <div class="wp100 d_InlineBlock bctrt">
                    <div class="f_left box0 wp05 ">ID</div>
                    <div class="f_left box0 wp25 ">Address 1</div>
                    <div class="f_left box0 wp15  ">Address 2</div>
                    <div class="f_left box0 wp20  ">City</div>
                    <div class="f_left box0 wp10  ">State</div>
                    <div class="f_left box0 wp10  ">Zip Code</div>
                </div>
    <?
            $rows = $customers_dal->get_addresses_per_customer_id($customer_id);
            if (count($rows) >0 ) {
            foreach($rows as $row) { ?>
                <div class="wp100 d_InlineBlock ">
                    <div class="f_left box0 wp05  bclightgray">&nbsp;<?=$row->address_id?></div>
                    <div class="f_left box0 wp25  bclightgray">&nbsp;<?=$row->address_line1?></div>
                    <div class="f_left box0 wp15  bclightgray">&nbsp;<?=$row->address_line2?></div>
                    <div class="f_left box0 wp20  bclightgray">&nbsp;<?=$row->city?></div>
                    <div class="f_left box0 wp10  bclightgray">&nbsp;<?=$row->state?></div>
                    <div class="f_left box0 wp10  bclightgray">&nbsp;<?=$row->zipcode?></div>
                    <? if ($_SESSION['settings'][$_SESSION['settings']['login_id']]['level'] >=1 ) { ?>
                    <div class="f_left box0 wp10 bclightgray">
                        <input type="submit" value="Edit" class="button" onclick="Customer_EditAddress_setAddressID(<?=$row->address_id?>)">
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
                    <div class="f_left box0 wp100 bcwhite">There are not any addresses added as of yet.</div>
                </div>
            <?}?>
            <?
            if (isset($_SESSION['edit_customers']['edit_address_address_id']) ){
                $address_data = $customers_dal->get_address_data_by_address_id($_SESSION['edit_customers']['edit_address_address_id']);
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

                <div class="wp100 h25px d_InlineBlock bclightgray">
                    <div class="f_left hp100 w90 bctrt right">Address</div>
                    <div id="CustomerLoginSummary_employee_address1" class="f_left left bclightgray w170 hp100  textIndent15">
                        <input type="text" class="w150" value="<?=$address_line1?>" id="Customer_address1">
                    </div>
                    <div id="failed_register_message_NU_Address1" class="f_left w300 hp100 bclightgray">&nbsp</div>

                    <? if (isset($_SESSION['edit_customers']['edit_address_address_id']) ){?>
                    <div class="f_left w80" onclick="Customer_UpdateAddress(<?=$_SESSION['edit_customers']['edit_address_address_id']?>)">
                        <input type="submit" class="button red" value="Update">
                    </div>
                    <? } else {?>
                    <div class="f_left w80" onclick="Customer_AddAddressExistingUser(<?=$customer_id?>)">
                        <input type="submit" class="button" value="Add Address">
                    </div>
                    <? }?>
                </div>

                <div class="wp100 h25px d_InlineBlock bclightgray">
                    <div class="f_left hp100 w90 bctrt right">Address 2</div>
                    <div id="CustomerLoginSummary_address2" class="f_left left bclightgray w170 hp100 textIndent15">
                        <input type="text" class="w150" value="<?=$address_line2?>" id="Customer_address2">
                    </div>
                    <div class="f_left w310 bclightgray">&nbsp</div>
                </div>

                <div class="wp100 h25px d_InlineBlock bclightgray">
                    <div class="f_left bctrt hp100 w90 right">City</div>
                    <div id="CustomerLoginSummary_city" class="f_left left bclightgray w170 hp100 textIndent15">
                        <input type="text" class="w80" value="<?=$city?>" id="Customer_city">
                    </div>
                    <div id="failed_register_message_NU_City" class="f_left w310 hp100 bclightgray">&nbsp</div>
                </div>

                <div class="wp100 h25px d_InlineBlock bclightgray">
                    <div class="f_left bctrt hp100 w90 right">State</div>
                    <div id="CustomerLoginSummary_state" class="f_left left bclightgray w170 hp100 textIndent15">
                        <input type="text" class="w20" value="<?=$state?>" id="Customer_state">
                    </div>
                    <div class="f_left w310 bclightgray">&nbsp</div>
                </div>

                <div class="wp100 h25px d_InlineBlock bclightgray">
                    <div class="f_left bctrt hp100 w90 right">Zip</div>
                    <div id="CustomerLoginSummary_employee_zip" class="f_left left bclightgray w170 hp100 textIndent15">
                        <input type="text" class="w70" value="<?=$zipcode?>" id="Customer_zipcode">
                    </div>
                    <div id="failed_register_message_NU_ZipCode" class="f_left w310 hp100 bclightgray">&nbsp</div>
                </div>
                <? } ?>
            <?
            unset($_SESSION['edit_customers']['edit_address_address_id']);
            }
      function CustomerAppointmentHistory($customers_dal,$customer_id){
        $general_dal                = new GENERAL_DAL();
        $profiles_dal               = new Profiles_DAL();
        $customers_dal              = new Customers_DAL();

        $AppointmentHistory_rows    = $general_dal->get_AppointmentHistory($customer_id,0);
        $PreferenceData             = $general_dal->get_CompanyPreference_by_Company_ID($_SESSION['settings']['company_id'],'timezone');
        date_default_timezone_set($PreferenceData[0]->value);
        $intNow = mktime();
        $current_time = date("Y-m-d H:i:s",$intNow);
        ?>
        <div class="f_left hp75 wp100" id="CustomerAppointmentHistory">
            <div class="wp100 hp10 f_left ">
                <div class="f_left wp100 bctrt left "><div class="pl20">Appointment History</div></div>
            </div>
            <div class="f_left wp100 hp90">
                <div class="wp100 hp08 f_left t_align_center bctrt">
                    <div class="f_left center s10 bold wp15 ml5">Start Time</div>
                    <div class="f_left center s10 bold wp15">End Time</div>
                    <div class="f_left center s10 bold wp25">Staff booked with</div>
                    <div class="f_left center s10 bold wp15">Services Booked</div>
                    <div class="f_left center s10 bold wp20">Status</div>
                    <div class="f_left center s10 bold wp05">Cancel?</div>
                    <div class="f_left center s10 bold wp02">&nbsp;</div>
                </div>
                <div class="f_left wp100 hp90 scrolling">
                <? if (count($AppointmentHistory_rows)!=0) {
                    foreach($AppointmentHistory_rows as $Appointment_data) {
                        $startdate              = date('m/d/y h:i A', strtotime($Appointment_data->startDate));
                        $enddate                = date('m/d/y h:i A', strtotime($Appointment_data->endDate));
                        $send_email_html        = '';
                        $Appointment_data_id    = $Appointment_data->id;
                        if ($Appointment_data->status==1) {
                            if          ($Appointment_data->deleted_by_type == 0) {
                                $deleted_by_data      = $profiles_dal->get_EmployeeDataPerLoginId($Appointment_data->deleted_by_id);
                                if ( isset($deleted_by_data) && count($deleted_by_data) > 0 ) {
                                   $deleted_by_firstname    = $deleted_by_data[0]->firstname;
                                   $deleted_by_lastname     = $deleted_by_data[0]->lastname;
                                } else {
                                    $deleted_by_firstname   = 'Employee1';
                                    $deleted_by_lastname    = 'Employee2';
                                }
                                $deleted_by_person_type     = 'Employee';
                            } else if   ($Appointment_data->deleted_by_type == 1) {
                                $deleted_by_data      = $customers_dal->get_CustomerDataPerId($Appointment_data->deleted_by_id);
                                if ( isset($deleted_by_data) && count($deleted_by_data) > 0 ) {
                                   $deleted_by_firstname    = $deleted_by_data[0]->firstname;
                                   $deleted_by_lastname     = $deleted_by_data[0]->lastname;
                                } else {
                                    $deleted_by_firstname   = 'Customer1';
                                    $deleted_by_lastname    = 'Customer2';
                                }
                                $deleted_by_person_type     = 'Customer';
                            }
                            $deleted_by_data      = $profiles_dal->get_EmployeeDataPerLoginId($Appointment_data->deleted_by_id);

                            $status = "
                                <div class=\"f_left wp100\">
                                    <div class=\"f_left right wp35\">
                                        <font color=red size=1 title=\"Canceled\">Canceled by:</font>
                                    </div>
                                    <div class=\"f_left left wp65 no-overflow\">
                                        <font size=1>".$deleted_by_firstname."&nbsp;".$deleted_by_lastname."(".$deleted_by_person_type.")</font>
                                    </div>
                                </div>
                            ";
                            if ( $Appointment_data->startDate < $current_time) {
                                $send_email_html = "";
                            } else {
                                $send_email_html = "
                                    <div class=\"f_right right hp100\" id=\"Customers_Send_Cancelation_Email_$Appointment_data_id\">
                                        <a onclick=\"Customers_Send_Appointment_Email($Appointment_data_id,'Cancelation','Customers_Send_Cancelation_Email_$Appointment_data_id')\">
                                            <img alt=\"\" src=\"/common_includes/includes/images/email_icon_small.png\" class=\"mp\" title=\"Re-Send Cancelation Email?\">
                                        </a>
                                    </div>
                                ";
                            }
                            $editORnot = "---";
                            $background = 'bclightpink';
                        } else {
                            $employee_firstname = 'Unknown2';
                            $employee_lastname  = 'Unknown2';
                            if ( $Appointment_data->startDate < $current_time) {
                                $background     = 'bclightcoral';
                                $status         = "Past";
                                $editORnot      = "---";
                                $send_email_html= "";
                            } else {
                                $background     = 'bclightgreen';
                                $status         = "Pending";
                                $editORnot      = "
                                    <a onclick=\"deleteAppointment($Appointment_data_id,$customer_id,0,{$_SESSION['settings']['login_id']},'CustomerAppointmentHistory');\" class=\"menu\">
                                        <img src=\"/common_includes/includes/images/cancel.jpg\" title=\"Cancel the Appointment\" width=\"11\" height=\"11\">
                                    </a>
                                 ";
                                $send_email_html= "
                                    <div class=\"f_right right hp100\" id=\"Customers_Send_Reminder_Email_$Appointment_data_id\">
                                        <a onclick=\"Customers_Send_Appointment_Email($Appointment_data_id,'Reminder','Customers_Send_Reminder_Email_$Appointment_data_id')\">
                                        <img alt=\"\" src=\"/common_includes/includes/images/email_icon_small.png\" class=\"mp\" title=\"Send Reminder Email?\">
                                        </a>
                                    </div>
                                 ";
                            }         
                        }
                        ?>
                      <div class="wp100 f_left s07 <?=$background?>">
                        <div class="f_left left wp15 ml5 center"><?=$startdate?></div>
                        <div class="f_left left wp15 ml5 center"><?=$enddate?></div>
                        <div class="f_left left wp25 ml5 center">
                            <div class="f_left wp90"><?=$Appointment_data->firstname?>  <?=$Appointment_data->lastname?></div>
                            <div class="f_left wp10">
                                <?=$send_email_html?>
                            </div>
                        </div>
                        <div class="f_left left wp15 ml5 center">
                                <?=$Appointment_data->services_count?> service<?= ($Appointment_data->services_count > 1 ? 's' : '') ?>
                        </div>
                        <div class="f_left left wp20 ml5 center">
                                <?=$status?>
                        </div>
                        <div class="f_left left wp05 ml5 center">
                                <?=$editORnot?>
                        </div>
                      </div>
                            <div class="wp60 f_right s07 right">
                                <?services_selected_for_past_appointment($Appointment_data_id);?>
                            </div>
                    <? }
                } else {?>
                      <div class="wp100 d_InlineBlock s08">
                        <div class="wp100 f_left center red">We do not have any appointment history for this customer.</div>
                      </div>
                <?} ?>
                </div>
            </div>
        </div>
    <?
}
        function services_selected_for_past_appointment($appointment_id){
            $total_mins = $total_price_paid = $total_service_price = 0;
            $count = 1;
            $Appointment_dal = new Appointments_DAL();
            $Appointments_Services_Data = $Appointment_dal->Appointments_displayAppointmentServices_by_appointment_ID($appointment_id);
            ?>
            <div class="d_InlineBlock wp95 hp100 main_bc_color1_light main_color1_light_text scrolling" style="display:inline-block; width: 95%;" id="STEP3_display_appt_info">
                    <div class="f_left wp100 t_align_center main_bc_color2 main_color2_text s10 bold" style="display:inline-block; width: 100%;">
                        <div class="f_left wp03" style="float:left; width:03%">#</div>
                        <div class="f_left wp12" style="float:left; width:12%">&nbsp;</div>
                        <div class="f_left wp30 no-overflow right" style="float:left; width:30%; overflow:hidden;">Service Name</div>
                        <div class="f_left wp10" style="float:left; width:10%">Time</div>
                        <div class="f_left wp20 right" style="float:left; width:20%; text-align: right;">POS Price</div>
                        <div class="f_left wp20 right" style="float:left; width:20%; text-align: right;">Paid</div>                        
                    </div>
                    <?
                    foreach($Appointments_Services_Data as $Appointment_Service_Info) { ?>
                        <div class="wp100 f_left center s07"    style="display:inline-block; width: 100%;">
                            <div class="f_left wp03"                    style="float:left; width:03%"><?=$count?>.</div>
                            <div class="f_left wp12"                    style="float:left; width:12%">&nbsp;</div>
                            <div class="f_left wp30 no-overflow right"  style="float:left; width:30%; overflow:hidden;">&nbsp;<?=$Appointment_Service_Info->item_name?></div>
                            <div class="f_left wp10"                    style="float:left; width:10%"><?=$Appointment_Service_Info->est_time_mins?> min</div>
                            <div class="f_left wp20 right"              style="float:left; width:20%; text-align: right;"><?=money2($Appointment_Service_Info->service_price)?></div>
                            <div class="f_left wp20 right"              style="float:left; width:20%; text-align: right;"><?=money2($Appointment_Service_Info->price_paid)?></div>                        
                        </div>
                        <?  
                        $count++; 
                        $total_mins             += $Appointment_Service_Info->est_time_mins;
                        $total_service_price    += $Appointment_Service_Info->service_price; 
                        $total_price_paid       += $Appointment_Service_Info->price_paid;
                        ?>
                    <? } ?>
                    <div class="wp100 f_left s08"           style="display:inline-block; width: 100%;">
                        <div class="f_left wp03"                    style="float:left; width:03%">&nbsp;</div>
                        <div class="f_left wp12"                    style="float:left; width:12%">&nbsp;</div>
                        <div class="f_left wp30 no-overflow right"  style="float:left; width:30%;">Total</div>
                        <div class="f_left wp10"                    style="float:left; width:10%; color:green; font-weight:bold;"><?=$total_mins?> mins</div>
                        <div class="f_left wp20 right"              style="float:left; width:20%; text-align: right; color:green; font-weight:bold;"><?=money2($total_service_price)?></div>
                        <div class="f_left wp20 right"              style="float:left; width:20%; text-align: right; color:green; font-weight:bold;"><?=money2($total_price_paid)?></div>
                    </div>
            </div>
            <?            
}
function CustomerAddStanza() {
    $customers_dal = new Customers_DAL();
?>
<div class="profileBodyDataContainer wp95">
    <div class="d_InlineBlock wp75 hp100">
        <?= CustomerAdd($customers_dal);?>
    </div>
</div>
<?
}
  function CustomerAdd($customers_dal) {
?>
<div class="f_left wp100 wp100 bctrt">
    <div class="f_left wp100 wp100" id="registerPanel">
        <H2>New Customer Information</H2>
        <div class="d_InlineBlock">
            <div class="wp50 f_left">
                <div>
                    <div class="left w120">First Name</div>
                    <div class="left">
                        <input type="text" id="NC_first_name" name="NC_first_name" value="" size="20" maxlength="50" tabindex="1">
                    </div>
                </div>
                <div>
                    <div>
                        <font size="1" color="red"><div class="left" id="failed_register_message_NC_first_name"></div></font>
                    </div>
                </div>

                <div>
                    <div class="left w120">Last Name</div>
                    <div class="left">
                        <input type="text" id="NC_last_name" name="NC_last_name" value="" size="20" maxlength="50" tabindex="1">
                    </div>
                </div>
                <div>
                    <div>
                        <font size="1" color="red"><div class="left" id="failed_register_message_NC_last_name"></div></font>
                    </div>
                </div>

                <div>
                    <div class="left w120">Email</div>
                    <div class="left">
                        <input type="text" id="NC_user_email" name="NC_user_email" value="<? echo $_SESSION['appointment']['user_email']; ?>" size="20" maxlength="64" tabindex="1">
                    </div>
                </div>
                <div>
                    <div>
                        <font size="1" color="red"><div class="left" id="failed_register_message_NC_user_email"></div></font>
                    </div>
                </div>
                
                <div>
                    <div class="left w120">Phone Number</div>
                    <div class="left">
                        <input type="text" id="NC_phone_num" name="NC_phone_num" value="" size="16" maxlength="16" tabindex="1">
                    </div>
                </div>
                <div>
                    <div>
                        <font size="1" color="red"><div class="left" id="failed_register_message_NC_phone_num"></div></font>
                    </div>
                </div>

                
            </div>

            <div class="wp50 f_right">
                <div>
                    <div class="left w120px">Address</div>
                    <div class="left">
                        <input id="NC_Address" name="NC_Address" type="text" maxlength="70" size="20"/>&nbsp;<SPAN class="s06">&nbsp;</SPAN>
                    </div>
                </div>
                <div>
                    <div>
                        <font size="1" color="red"><div class="left" id="failed_register_message_NC_Address"></div></font>
                    </div>
                </div>

                <div>
                    <div class="left w120px">City</div>
                    <div class="left">
                        <input id="NC_City" name="NC_City" type="text" maxlength="40" size="20"/>&nbsp;<SPAN class="s06">&nbsp;</SPAN>
                    </div>
                </div>
                <div>
                    <div>
                        <font size="1" color="red"><div class="left" id="failed_register_message_NC_City"></div></font>
                    </div>
                </div>

                <div>
                    <div class="left w120px">Zip/Postal Code</div>
                    <div class="left">
                        <input id="NC_PostalCode" name="NC_PostalCode" type="text" maxlength="50" size="10"/><SPAN class="s06">&nbsp;</SPAN>
                    </div>
                </div>
                <div>
                    <div>
                        <font size="1" color="red"><div class="left" id="failed_register_message_NC_PostalCode"></div></font>
                    </div>
                </div>

                <div>
                    <div class="left w120px">State/Province</div>
                    <div class="left">
                        <select name="NC_State" id="NC_State">
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
                        <SPAN class="s06">&nbsp;</SPAN>
                    </div>
                </div>
                <div>
                    <div>
                        <font size="1" color="red"><div class="left" id="failed_register_message_NC_State"></div></font>
                    </div>
                </div>

                <div>
                    <div class="left w120px">Country</div>
                    <div class="left">
                        <select name="NC_Country" id="NC_Country">
                        <option value="">- Select -</option>
                        <option value="USA" selected>United States</option>
                        <option value="ZWE">Zimbabwe</option>
                        </select>
                        <SPAN class="s06">&nbsp;</SPAN>
                    </div>
                </div>
                <div>
                    <div>
                        <font size="1" color="red"><div class="left" id="failed_register_message_NC_Country"></div></font>
                    </div>
                </div>
            </div>

            <div class="wp100 f_left">
                <div class="center">
                        <input onclick="Customer_AddNewCustomer()" type="submit" value="Add Customer" class="button buttonMargin" tabindex="3"/>
                </div>
            </div>
        </div>
        <div id="failed_NewUser_message">&nbsp;</div>
    </div>
</div>
<?
}

function CustomerSearchStanza() {
$reportType = 'Customers_AllCustomers';
?>
    <div id="item_SearchStanza" class="d_InlineBlock hp100 wp100">
        <div class="wp95 hp100 d_InlineBlock">
            <?=customer_search_div('first_name','text',$reportType,09)?>
            <?=customer_search_div('last_name','text',$reportType,09)?>
            <?=customer_search_div('email','text',$reportType,09)?>
            <?=customer_search_div('phone_number','text',$reportType,09)?>
            <?=customer_search_div('miscellaneous','checkbox',$reportType,11)?>
            <?=customer_search_div('submit','checkbox',$reportType,08)?>
        </div>
    </div>
<?
}
    function customer_search_div($search_by_field,$data_type,$reportType,$height_percent){
    if (isset($_SESSION['search_data']['customer_search']['customer_search_inactive_customers']) && $_SESSION['search_data']['customer_search']['customer_search_inactive_customers'] == 1)   { $inactive_customers_checked = "checked"; } else {$inactive_customers_checked = "";}
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
                <? } elseif  ( ($search_by_field == 'item_styleNumber' ) ) { ?>
                    <div class="d_InlineBlock wp100 hp60 s07 center">
                       <? dynamic_pannel_advanced_search_styleNumber();?>
                    </div>
                <? } elseif  ( ($search_by_field == 'miscellaneous' ) ) { ?>
                    <div class="f_left wp100 hp50">
                        &nbsp;<?=ucfirst($search_by_field)?>
                    </div>
                    <div class="d_InlineBlock f_left wp100 hp50">
                        <div class="f_left wp100 hp100">
                            <div class="f_left right wp85 hp100 s06">InActive Customers</div>
                            <div class="f_left wp15 hp100"><input type='checkbox' id="dynamic_pannel_inactive_customers" value='1' onclick="Customer_Search_searchBy('<?=$reportType?>');" <?=$inactive_customers_checked?> ></div>
                        </div>
                    </div>
                <? } elseif  ( ($search_by_field == 'submit' ) ) { ?>
                    <div class="f_left hp100 wp100">
                        <input class="button s08 wp90" type="submit" value="Search" onclick="Customer_Search_searchBy('<?=$reportType?>');">
                    </div>
                <? } ?>
            </div>
    <?}