<?php 
require_once('general_functions.php');
include_once('reports_functions.php'); 
class mainPage_DAL {
  public function __construct(){}
  public function get_last_logins($company_id,$login_id){
        $sql ="SELECT 1";
        #if ($totals == 0 ) { print "$totals <br> $sql"; }
    return $this->query($sql);
  }
  public function get_last_X_open_Inventory_Runs($company_id,$login_id){
        $sql ="SELECT ir.id as id,
                ir.start_date as start_date,
                sum(CASE WHEN iri.updated is NULL THEN 1 ELSE 0 END) AS remaining_items,
                ir.added as added,
                count(iri.id) as items_count ,
                sum(iri.pos_quantity) as pos_quantity,
                sum(iri.quantity) as quantity
                from inventory_run ir
                join inventory_run_items iri on ir.id = iri.inventory_run_id
                where company_id = $company_id and assigned_login_id = $login_id
                group by ir.id
                limit 5";
        #print "$totals <br> $sql";
    return $this->query($sql);
  }
  public function get_last_X_open_Appointments($company_id,$login_id,$security_level=0){
        $sql ="SELECT c.id as customer_id, a.startDate as startDate, a.endDate as endDate, c.firstname as customer_first_name, c.surname as customer_last_name, l.firstname as staff_firstname, l.lastname as staff_lastname, l.id as login_id,
                c.email as customer_email, c.phone_num as customer_phone_number
                from appointments a
                join customers c on c.id = a.customer_id
                join logins l on l.id = a.login_id
                where a.company_id = $company_id and
                a.status != 1 and ";
        if ($security_level != 0 && $security_level < 2){
        $sql.= " a.login_id = $login_id and ";
        }
        $sql.= " a.startDate > convert_tz(now(), 'utc', ".quoteSmart($_SESSION['preferences']['timezone']).")
                order by a.startdate asc
                limit 50";
        #print "$security_level:$totals: <br> $sql";
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
function update_DeliveryInfo() { ?>
    <div class=''>
        <a class='bold s08' href='javascript: none();' onclick='updateDeliveryInfo()'>Update Delivery Info</a>
    </div>
<?}
function mainPage() {
?>
<head>
<script src="includes/customers_functions.js" type="text/javascript"></script>
</head>
<div class="ReportsTopRow main_bc_color2 main_color2_text">POS Start PAGE</div>
    <div style="max-height: 1000px;" class="f_left wp100 hp94">
        <div class="leftSpace main_bc_color2 main_color2_text">&nbsp;</div>
            <div class="leftContent">
                <? mainPageEmployeeLatestSales(); ?>
            </div>
        <div class="middleSpace wp02">&nbsp;</div>
            <div class="rightContent">
                <? mainPageEmployeeUpcomingAppointments(); ?>
                <? mainPageEmployeeOpenInventoryRuns(); ?>
            </div>
        <div class="rightSpace main_bc_color2 main_color2_text">&nbsp;</div>
    </div>
<div class="ReportsBottomRow main_bc_color2 main_color2_text">&nbsp;</div>
<?
}
  function mainPageEmployeeLatestSales() {
    $reports_dal = new DAL();
    $general_dal                = new GENERAL_DAL();
    $PreferenceData             = $general_dal->get_CompanyPreference_by_Company_ID($_SESSION['settings']['company_id'],'timezone');
    date_default_timezone_set($PreferenceData[0]->value);
    $current_date = date("Y-m-d",mktime());
    $rows = $reports_dal->get_last_X_sales($_SESSION['settings']['company_id'],$_SESSION['settings']['login_id'],$current_date);
?>
<div class="f_left wp100 hp33 mt10">
    <div class="f_right right wp95 hp95 box5">
        <div class="f_left wp100 hp100">
            <div class="f_left wp100 hp15 bold left">
                Showing a maximum of 20 sales on <?=$current_date?>.
            </div>
            <div class="f_left wp100 hp15 report_header center">
                <div class="HEADER main_bc_color2 main_color2_text report_header_cell_wp15">Date</div>
                <div class="HEADER main_bc_color2 main_color2_text report_header_cell_wp20">Time</div>
                <div class="HEADER main_bc_color2 main_color2_text report_header_cell_wp15">Additional<br>Discount</div>
                <div class="HEADER main_bc_color2 main_color2_text report_header_cell_wp15">Store<br>Discount</div>
                <div class="HEADER main_bc_color2 main_color2_text report_header_cell_wp10">Tax</div>
                <div class="HEADER main_bc_color2 main_color2_text report_header_cell_wp10">Sub Total</div>
                <div class="HEADER main_bc_color2 main_color2_text report_header_cell_wp10">Total</div>
            </div>
            <div class="f_left wp100 hp65 scrolling">
            <? if (count($rows) >0 ) {
                foreach($rows as $row) {
                    $get_sale_figures = $reports_dal->get_sale_figures($row->id);?>
                    <div class="f_left wp100 h15px report_rows">
                        <div class="f_left hp100 report_data_cell_wp15"><?=date("m.d.y",strtotime($row->added))?></div>
                        <div class="f_left hp100 report_data_cell_wp20"><?=date("h:i:s A",strtotime($row->added))?></div>
                        <div class="f_left hp100 report_data_cell_wp15">&nbsp;<?=$get_sale_figures[0]->additional_discount_total?></div>
                        <div class="f_left hp100 report_data_cell_wp15">&nbsp;<?=$get_sale_figures[0]->discount_total?></div>
                        <div class="f_left hp100 report_data_cell_wp10">&nbsp;<?=$get_sale_figures[0]->tax_total?></div>
                        <div class="f_left hp100 report_data_cell_wp10">&nbsp;<?=$get_sale_figures[0]->sub_total?></div>
                        <div class="f_left hp100 report_data_cell_wp10">&nbsp;<?=$get_sale_figures[0]->total?></div>
                    </div>
                <? }
                } else { ?>
                    <div class="f_left wp100 hp80">
                        <div class="f_left wp100 h20px">
                            <div class="f_left hp100 report_data_cell_wp96 textIndent15">There aren't any sales for you today.</div>
                        </div>
                    </div>
                <? } ?>
            </div>
        </div>
    </div>
</div>
<?
}
  function mainPageEmployeeTopSales   () {
$dal = new DAL();
$rows = $dal->get_top_sales($_SESSION['settings']['company_id'],$_SESSION['settings']['login_id']);
?>
<div class="d_InlineBlock wp100 mt10">
    <div class="d_InlineBlock wp95 hp100 action_group">
            <div class="d_InlineBlock wp100 bold left">Showing your top 10 sales by revenue.</div>
            <div class="d_InlineBlock wp100 report_header center">
                <div class="HEADER main_bc_color2 main_color2_text report_header_cell_7">Date</div>
                <div class="HEADER main_bc_color2 main_color2_text report_header_cell_7">Time</div>
                <div class="HEADER main_bc_color2 main_color2_text report_header_cell_9">&nbsp;Register Clerk</div>
                <div class="HEADER main_bc_color2 main_color2_text report_header_cell_9">Additional Discount</div>
                <div class="HEADER main_bc_color2 main_color2_text report_header_cell_9">Store Discount</div>
                <div class="HEADER main_bc_color2 main_color2_text report_header_cell_8">Tax</div>
                <div class="HEADER main_bc_color2 main_color2_text report_header_cell_8">Total</div>
            </div>
            <? if (count($rows) >0 ) {
                foreach($rows as $row) {
                    $get_sale_figures = $dal->get_sale_figures($row->id);?>
            <div class="d_InlineBlock wp100 report_rows ">
                <div class="report_data_cell_7"><?=date("m.d.y",strtotime($row->added))?></div>
                <div class="report_data_cell_7"><?=date("h:m:s",strtotime($row->added))?></div>
                <div class="report_data_cell_9"><?=$row->username?></div>
                <div class="report_data_cell_9">&nbsp;<?=$get_sale_figures[0]->additional_discount_total?></div>
                <div class="report_data_cell_9">&nbsp;<?=$get_sale_figures[0]->discount_total?></div>
                <div class="report_data_cell_8">&nbsp;<?=$get_sale_figures[0]->tax_total?></div>
                <div class="report_data_cell_8">&nbsp;<?=$get_sale_figures[0]->total?></div>
            </div>
                <? }
                } else { ?>
                <div class="d_InlineBlock wp100 left textIndent15">You have no recorded sales at this time.</div>
                <? } ?>
    </div>
</div>
<?
}
  function mainPageEmployeeLastLogins () {
$dal = new mainPage_DAL();
$rows = $dal->get_last_logins($_SESSION['settings']['company_id'],$_SESSION['settings']['login_id']);
?>
<div class="d_InlineBlock wp100 mt10">
    <div class="d_InlineBlock wp95 hp100 action_group">
            <div class="d_InlineBlock wp100">Showing last 10 logins and hours per login.</div>
            <div class="d_InlineBlock wp100 report_header center ">
                <div class="HEADER main_bc_color2 main_color2_text report_header_cell_9">Date</div>
                <div class="HEADER main_bc_color2 main_color2_text report_header_cell_11">Time In</div>
                <div class="HEADER main_bc_color2 main_color2_text report_header_cell_11">Time Out</div>
                <div class="HEADER main_bc_color2 main_color2_text report_header_cell_12">Time Total</div>
                <div class="HEADER main_bc_color2 main_color2_text report_header_cell_9">&nbsp;Register</div>
                <div class="HEADER main_bc_color2 main_color2_text report_header_cell_7">&nbsp;Location</div>
            </div>
            <? if (count($rows) >1 ) {
                foreach($rows as $row) {?>
            <div class="d_InlineBlock wp100 report_rows ">
                <div class="report_data_cell_9"><?=date("m.d.y",strtotime($row->added))?></div>
                <div class="report_data_cell_11"><?=date("h:m:s",strtotime($row->added))?></div>
                <div class="report_data_cell_11"><?=date("h:m:s",strtotime($row->added))?></div>
                <div class="report_data_cell_12">&nbsp;</div>
                <div class="report_data_cell_9">&nbsp;</div>
                <div class="report_data_cell_7">&nbsp;</div>
            </div>
                <? }
                } else { ?>
                        <div class="f_left wp100 hp80">
                            <div class="f_left wp100 h20px">
                                <div class="f_left hp100 report_data_cell_wp99 textIndent15">No time recorded as of now.</div>
                            </div>
                        </div>
                <? } ?>
    </div>
</div>
<?
}
  function mainPageEmployeeUpcomingAppointments () {
$dal = new mainPage_DAL();
$rows = $dal->get_last_X_open_Appointments($_SESSION['settings']['company_id'],$_SESSION['settings']['login_id'],$_SESSION['settings'][$_SESSION['settings']['login_id']]['level']);
?>
<div class="f_left wp100 hp33 mt10">
    <div class="f_left wp95 hp95 box5">
        <div class="f_left wp100 hp100">
            <div class="f_left wp100 hp15 bold left">
                Upcoming Appointments
            </div>
            <div class="f_left wp100 hp15 report_header center">
                <div class="HEADER main_bc_color2 main_color2_text report_header_cell_wp40">Appointment Time</div>
                <div class="HEADER main_bc_color2 main_color2_text report_header_cell_wp35">Full Name</div>
                <div class="HEADER main_bc_color2 main_color2_text report_header_cell_wp22">Ph. Num</div>
            </div>
            <div class="f_left wp100 hp65 scrolling">
            <? if (count($rows) >=1 ) {
                foreach($rows as $row) {
                $General_dal = new GENERAL_DAL();
                $TotalCompletedAppointments = $General_dal->get_TotalCompletedAppointments($row->customer_id);
                if ($row->login_id == $_SESSION['settings']['login_id']) { $bg_color = 'bclightgreen'; } else { $bg_color = ''; } ?>
                <div class="f_left wp100 h15px report_rows mp <?=$bg_color?>" title="View <?=ucfirst($row->customer_first_name)?> <?=ucfirst($row->customer_last_name)?>'s profile?" onclick="Customer_editProfile(<?=$row->customer_id?>)">
                    <div class="hp100 report_data_cell_wp40"><?=date("m/d h:i a", strtotime($row->startDate))?> - <?=date("h:i a", strtotime($row->endDate))?></div>
                    <div class="hp100 report_data_cell_wp35 no-overflow left">&nbsp;(<?=ucfirst($row->staff_firstname[0])?>)&nbsp;<?=ucfirst($row->customer_first_name)?> <?=ucfirst($row->customer_last_name)?> (<?=$TotalCompletedAppointments[0]->count?>)</div>
                    <div class="hp100 report_data_cell_wp22 no-overflow "><?=formatPhone($row->customer_phone_number)?></div>
                </div>
                <? }
            } else { ?>
                        <div class="f_left wp100 hp80">
                            <div class="f_left wp100 h20px">
                                <div class="f_left hp100 report_data_cell_wp97 textIndent15">You don't have any upcoming appointments.</div>
                            </div>
                        </div>
            <? } ?>
            </div>
        </div>
    </div>
</div>
<?
}
  function mainPageEmployeeOpenInventoryRuns    () {
$dal = new mainPage_DAL();
$rows = $dal->get_last_X_open_Inventory_Runs($_SESSION['settings']['company_id'],$_SESSION['settings']['login_id']);
?>
<div class="f_left wp100 hp33 mt10">
    <div class="f_left wp95 hp95 box5">
        <div class="f_left wp100 hp100">
            <div class="f_left wp100 hp15 bold left">
                Open Inventory Runs
            </div>
            <div class="f_left wp100 hp15 report_header center">
                <div class="HEADER main_bc_color2 main_color2_text report_header_cell_wp30">Date</div>
                <div class="HEADER main_bc_color2 main_color2_text report_header_cell_wp10">Items</div>
                <div class="HEADER main_bc_color2 main_color2_text report_header_cell_wp10">Balance</div>
                <div class="HEADER main_bc_color2 main_color2_text report_header_cell_wp20">POS Quantity</div>
                <div class="HEADER main_bc_color2 main_color2_text report_header_cell_wp13">Counted</div>
                <div class="HEADER main_bc_color2 main_color2_text report_header_cell_wp12">Edit</div>
            </div>
            <div class="f_left wp100 hp65">
            <? if (count($rows) >=1 ) {
                foreach($rows as $row) {
                    if ($row->remaining_items >0) { $remaining_items_bg_color = 'bcyellow'; } else { $remaining_items_bg_color=''; } ?>
                        <div class="f_left wp100 h15px report_rows">
                            <div class="hp100 report_data_cell_wp30"><?=date("m/d g:i a", strtotime($row->start_date))?></div>
                            <div class="hp100 report_data_cell_wp10"><?=$row->items_count?></div>
                            <div class="hp100 report_data_cell_wp10 <?=$remaining_items_bg_color?> "><?=$row->remaining_items?></div>
                            <div class="hp100 report_data_cell_wp20"><?=$row->pos_quantity?></div>
                            <div class="hp100 report_data_cell_wp13"><?=$row->quantity?></div>
                            <div class="hp100 report_data_cell_wp12 mp">
                                <a class="menu" onclick="Inventory_InventoryRun_Details(<?=$row->id?>)">
                                    <img alt="" width="11" height="9" src="/common_includes/includes/images/edit_icon_20_19.jpg" title="Edit">
                                </a>
                            </div>
                        </div>
                    <? }
                    } else { ?>
                        <div class="f_left wp100 hp80">
                            <div class="f_left wp100 h20px">
                                <div class="f_left hp100 report_data_cell_wp97 textIndent15">You don't have any OPEN Inventory Runs.</div>
                            </div>
                        </div>
                 <? } ?>
            </div>
        </div>
    </div>
</div>
<?
}
?>