<?php
include_once('general_functions.php');
include_once('profiles_functions.php');
include_once('customers_functions.php'); 
include_once('companies_functions.php');

function setup_path_mailer(){
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

set_include_path($shop_path.$version.'/:'.$Zend_path);
return array ($host,$domain);
}
class Mailer_DAL {
  public function __construct(){}
  public function mailer_GetCustomerPossibleCount($company_id){
      $sql = "SELECT count(id) as count
                from customers where status in (1,0) and deleted is NULL and company_id = $company_id";
    #print "$sql" . "\n";
    return $this->query($sql);
  }
  public function mailer_GetCustomerActiveCount($company_id){
      $sql = "SELECT count(id) as count
                from customers where status = 1 and deleted is NULL and company_id = $company_id";
    #print "$sql" . "\n";
    return $this->query($sql);
  }
  public function mailer_GetCustomerOPTEDINCount($company_id){
      $sql = "SELECT count(id) as count
                from customers
                where
                status = 1 and
                email_promotions = 1 and
                deleted is NULL and company_id = $company_id";
    #print "$sql" . "\n";
    return $this->query($sql);
  }
  public function mailer_GetCustomerInActiveCount($company_id){
      $sql = "SELECT count(id) as count
                from customers where status = 0 and deleted is NULL and company_id = $company_id";
    #print "$sql" . "\n";
    return $this->query($sql);
    }

  public function mailer_GetMailerCustomers_per_Mailer_Run_ID($mailer_run_id,$status,$limit){
      $sql  ="  SELECT 
                    mri.id,
                    convert_tz(mri.added,           'utc', ".quoteSmart($_SESSION['preferences']['timezone']).") as added,
                    convert_tz(mri.completed_date,  'utc', ".quoteSmart($_SESSION['preferences']['timezone']).") as completed_date,
                    mri.status,
                    c.id as customer_id,
                    c.firstname, 
                    c.surname as lastname,
                    c.email
                from mailer_run_items mri
                join customers c on c.id = mri.customer_id
                where 
                mri.status in ($status) and
                mri.mailer_run_id = $mailer_run_id";
        if ($limit <> 0 ) {
      $sql .= " limit $limit ";
        }
    #print "$sql" . "\n";
    return $this->query($sql);
    }
  public function mailer_MailerRunDetails($mailer_run_id){
        $sql ="SELECT
        count(mri.id) as total_count, mt.template_name as template_name,
        sum(CASE WHEN mri.status is NULL THEN 0 ELSE mri.status END) AS completed_mails,
        mr.id,
        l_assigned.firstname    as l_assigned_firstname,
        l_assigned.lastname     as l_assigned_lastname,
        l_created.firstname     as l_created_firstname,
        l_created.lastname      as l_created_lastname,
        mr.created_by_login_id
      
        FROM mailer_run_items mri
        join mailer_run mr on mr.id = mri.mailer_run_id
        join logins l_assigned      on l_assigned.id        = mr.assigned_login_id
        join logins l_created       on l_created.id         = mr.created_by_login_id
        join mailer_templates mt    on mt.id                = mr.mailer_templates_id

        where mri.mailer_run_id = $mailer_run_id
        group by mr.id";
        #print $sql."<br>";
    return $this->query($sql);
  }
  public function mailer_get_latest_mailer_Runs($company_id,$totals=1){
      if ($totals == 0) {
        $sql ="SELECT
        mr.id,
        convert_tz(mr.start_date,   'utc', ".quoteSmart($_SESSION['preferences']['timezone'])."),
        convert_tz(mr.added,        'utc', ".quoteSmart($_SESSION['preferences']['timezone']).") as added,
        l1.firstname as l1_fn,l1.lastname as l1_ln,
        l2.firstname as l2_fn,l2.lastname as l2_ln,
        mt.template_name as template_name";
    } ELSE {
        $sql ="SELECT count(mr.id) as count ";
    }
        $sql .= " FROM mailer_run mr
        join logins l1              on mr.assigned_login_id     = l1.id
        join logins l2              on mr.created_by_login_id   = l2.id
        join mailer_templates mt    on mt.id                    = mr.mailer_templates_id

        where mr.company_id = $company_id";

        if ($totals == 0) {
            #$sql .= " group by d.id ";
            if (!(isset($_SESSION['search_data']['column']))) { $sql.= " order by mr.id desc"; }
            else { $sql.= " order by " . $_SESSION['search_data']['column'] . " " . $_SESSION['search_data']['asc_desc'] . " "; }

            if ( isset($_SESSION['search_data']['paging_page']) && $_SESSION['search_data']['paging_page'] != 0) {
                if ( $_SESSION['search_data']['paging_page'] == 1) { $limit_offset = 0; }
                else { $limit_offset = ( ($_SESSION['search_data']['paging_page'] - 1) * 10 ) ; }
                $sql .= " limit $limit_offset,10";
            }
        }
        #if ($totals == 0) { print $sql;}
    return $this->query($sql);
  }
  public function mailer_Customer_ID_from_mailer_run_item_id($mailer_run_item_id){
      $sql = "  SELECT customer_id, mri.status as status, mt.template_name as  template_name
                from mailer_run_items mri  
                join mailer_run mr on mr.id = mri.mailer_run_id
                join mailer_templates mt on mt.id = mr.mailer_templates_id
                where mri.id = $mailer_run_item_id";
    #print "$sql" . "\n";
    return $this->query($sql);
  }
  public function mailer_get_MailerTemplateInfo_by_TemplateID($mailer_template_id){
      $sql = "SELECT id,template_name from mailer_templates where id = $mailer_template_id ";
    #print "$sql" . "\n";
    return $this->query($sql);
  }
  public function get_AllMailerTemplates($company_id){
      $sql = "SELECT id, template_name
                from mailer_templates where status = 1";
    #print "$sql" . "\n";
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

function   mailer(){
    $companies_dal = new Companies_DAL();
?>
<head>
<script src="includes/<?=__FUNCTION__?>_functions.js" type="text/javascript"></script>
</head>
<div class="ReportsTopRow main_bc_color2 main_color2_text"><a href="#" title="Mailer" onclick="mainDiv('Mailer_AllMailerRuns')">Mailer Run</a></div>
    <div style="max-height: 1000px;" class="f_left wp100 hp94">
        <div class="leftSpace main_bc_color2 main_color2_text">&nbsp;</div>
        <div class="middleSpace wp96">
                <div class="f_left wp100 hp10">
                    <div class="f_left hp100 wp35 left vtop no-overflow">
                            <img alt="" class="hp90" src="/common_includes/includes/images/icon_mailer_50.png">
                            Mailer Runs
                    </div>
                    <div class="f_right hp100 wp50 right">&nbsp;
                        <? if ($_SESSION['settings'][$_SESSION['settings']['login_id']]['level'] >= 1 && !isset($_SESSION['mailer_run']['created_by_login_id']) && !isset($_SESSION['mailer_run']['mailer_run_id']) ) { ?>
                        <a onclick="mailer_Create_Run()">
                            <img alt="" class="hp90" src="/common_includes/includes/images/plus_sign.jpg" class="mp">
                        </a>
                        <? } ?>
                    </div>
            </div>
            <?
            if (!isset($_SESSION['mailer_run']['created_by_login_id']) && !isset($_SESSION['mailer_run']['mailer_run_id'])) {?>
                <div class="d_InlineBlock hp90 wp100">
                        <div class="f_left wp100 hp100" >
                            <div class="f_left wp15 hp100">
                                <div class="d_InlineBlock wp100 hp100" >
                                    <? MailerSearchStanza();?>
                                </div>
                            </div>
                            <div class="f_right wp85 hp100">
                                <div class="d_InlineBlock wp100 hp100" id="Mailer_AllMailerRunsBodyCenter">
                                    <? Mailer_AllMailerRunsStanza();?>
                                </div>
                            </div>
                        </div>
                </div>
            <?} else if (isset($_SESSION['mailer_run']['created_by_login_id']) || isset($_SESSION['mailer_run']['mailer_run_id']) ) { ?>
                <div class="f_left wp15 hp90"></div>
                <div class="d_InlineBlock f_left wp70 hp90">
                    <div id="Mailers_Details_master"    class="wp100 hp30 f_left"  style="max-height: 200px;">
                        <? mailer_Mailer_Run_TotalsSummary($_SESSION['settings']['company_id']);?>
                    </div>

                    <? if ( isset($_SESSION['mailer_run']['mailer_run_template_id']) && $_SESSION['mailer_run']['mailer_run_template_id'] != 0 && !(isset($_SESSION['mailer_run']['mailer_run_id'])) ){?>
                    <div id="showItems_for_mailer"      class="f_left wp100 hp70">
                            <? Mailer_ShowTemplatePreview($_SESSION['mailer_run']['mailer_run_template_id']) ?>
                    </div>
                    <? } else if ( isset($_SESSION['mailer_run']['mailer_run_id']) ) { ?>
                    <div id="showItems_for_mailer"      class="wp100 hp70 f_left">
                            <? mailer_Mailer_Run_ShowCustomers($_SESSION['settings']['company_id']);?>
                    </div>
                    <? } else { ?>
                    <div id="showItems_for_mailer"      class="f_left wp100 hp70">
                            &nbsp;
                    </div>
                    <? } ?>
                </div>
                <div class="f_left wp15 hp90"></div>
            <?}?>
        </div>
        <div class="rightSpace main_bc_color2 main_color2_text">&nbsp;</div>
    </div>
<div class="ReportsBottomRow main_bc_color2 main_color2_text">&nbsp;</div>
<?
}
  function Mailer_AllMailerRunsStanza(){?>
        <div class="wp100 hp07" id="listing_search_paging_top">
            <? showPaging(); ?>
        </div>
        <div class="d_InlineBlock wp100 hp85 scrolling">
            <?mailer_mailer_RunList();?>
        </div>
        <div class="wp100 hp07" id="listing_search_paging_bottom">
            <? showPaging(); ?>
        </div>
<?}
    function mailer_mailer_RunList(){?>
                    <div class="f_left wp100 h30px s08 HEADER main_bc_color1 main_color1_text">
                        <div class="hp100 report_header_cell_wp03">#</div>
                        <div class="hp100 report_header_cell_wp15 no-overflow">ADDED</div>
                        <div class="hp100 report_header_cell_wp15 no-overflow">Added by</div>
                        <div class="hp100 report_header_cell_wp15 no-overflow">Assigned to</div>
                        <div class="hp100 report_header_cell_wp15 no-overflow">Template Name</div>
                        <div class="hp100 report_header_cell_wp10">Remaining</div>
                        <div class="hp100 report_header_cell_wp07">Total</div>
                        <div class="hp100 report_header_cell_wp15">View/Edit</div>
                    </div>
        <?
        $dal = new Mailer_DAL();
        $rows = $dal->mailer_get_latest_mailer_Runs($_SESSION['settings']['company_id'],0);
        $altClass = "bctr1a";
        if (count($rows) >0 ) {
           $rownum = 1;
                foreach($rows as $row) {
                    $Mailer_DAL = new Mailer_DAL();
                    $mailer_run_data = $Mailer_DAL->mailer_MailerRunDetails($row->id);
                    if ( ( $mailer_run_data[0]->total_count - $mailer_run_data[0]->completed_mails ) >0) { $remaining_items_bg_color = 'bcyellow'; } else { $remaining_items_bg_color = ''; }
                    ?>
                    <div class="f_left wp100 s07 lh25 <?=$altClass?>">
                        <div class="report_data_cell_wp03">&nbsp;<?=$rownum++?></div>
                        <div class="report_data_cell_wp15 no-overflow">&nbsp;<?=date("d/m/y h:i a",strtotime($row->added))?></div>
                        <div class="report_data_cell_wp15 no-overflow">&nbsp;<?=$row->l2_fn?>&nbsp;<?=$row->l2_ln?></div>
                        <div class="report_data_cell_wp15 no-overflow">&nbsp;<?=$row->l1_fn?>&nbsp;<?=$row->l1_ln?></div>
                        <div class="report_data_cell_wp15 no-overflow bold">&nbsp;<?=$row->template_name?></div>
                        <div class="report_data_cell_wp10 no-overflow <?= $remaining_items_bg_color ?>">&nbsp;<?= ( $mailer_run_data[0]->total_count - $mailer_run_data[0]->completed_mails ) ?></div>
                        <div class="report_data_cell_wp07 no-overflow">&nbsp;<?=$mailer_run_data[0]->total_count?></div>
                        <div class="report_data_cell_wp15">&nbsp;
                            <? if ( $mailer_run_data[0]->total_count - $mailer_run_data[0]->completed_mails != 0 ) {?>
                                <input type='button' class='button' value='Continue'    onclick=Mailer_MailerRun_Details(<?=$row->id?>)>
                            <? } else {?>
                                <input type='button' class='button' value='VIEW'        onclick=Mailer_MailerRun_Details(<?=$row->id?>)>
                            <? } ?>
                        </div>
                    </div>
                    <? if ($altClass == "bctr1a") { $altClass = "bctr1b"; } else {$altClass = "bctr1a";} ?>
                <?}?>
            <?} else { ?>
                    <div class=" wp100 center">No mailer Run was found matching the criteria.</div>
            <? }
    }
    function Mailer_ShowTemplatePreview(){
        $Customers_Dal          = new Customers_DAL();
        $Mailer_DAL             = new Mailer_DAL();
        $customer_info          = $Customers_Dal->get_CustomerRandomCustomer_id($_SESSION['settings']['company_id']);
        $mailer_template_info   = $Mailer_DAL->mailer_get_MailerTemplateInfo_by_TemplateID($_SESSION['mailer_run']['mailer_run_template_id']);
        $template_name          = $mailer_template_info[0]->template_name;
        ?>
            <div id="mailer_Mailer_Run_TotalsSummary"  class="wp100 hp100 f_left center bclightgray scrolling s08">
                <?
                print $mailer_template_info[0]->template_name."(".$customer_info[0]->id.",1)<br>";
                $template_name($customer_info[0]->id,1);
                ?>
            </div>
<?
}
    function MailerSearchStanza() {
    $reportType = 'Mailer_AllMailerRuns';
    ?>
    <div id="item_SearchStanza" class="hp100 wp100 InventoryMgmtBodyCenter">
        <div class="wp95 hp100 d_InlineBlock">
            <?=MailerSearch_div('start_date','text',$reportType,09,0,0)?>
            <?=MailerSearch_div('end_date','text',$reportType,09,0,0)?>
            <?=MailerSearch_div('submit','checkbox',$reportType,08,0,'Reports_'.$reportType.'_searchBy')?>
        </div>
    </div>

<?
}
        function MailerSearch_div($search_by_field,$data_type,$reportType,$height_percent,$OnClickAction=0,$SubmitFunction=0){
    $general_dal    = new GENERAL_DAL();
    $PreferenceData = $general_dal->get_CompanyPreference_by_Company_ID($_SESSION['settings']['company_id'],'timezone');
    $timezone = $PreferenceData[0]->value;
        ?>
        <div class="d_InlineBlock mb5 bctrt wp100 hp<?=$height_percent?>" >
            <?       if  ( ($search_by_field == 'start_date' ) ) { ?>
                <div class="d_InlineBlock wp100 hp60 s07 center">
                   <? dynamic_pannel_search_date('start_date',$timezone);?>
                </div>
            <? } elseif  ( ($search_by_field == 'end_date' ) ) { ?>
                <div class="d_InlineBlock wp100 hp60 s07 center">
                   <? dynamic_pannel_search_date('end_date',$timezone);?>
                </div>
            <? } elseif  ( ($search_by_field == 'submit' ) ) { ?>
                <div class="f_left hp100 wp100">
                    <input class="button s08 wp90" type="submit" value="Search" onclick="<?=$SubmitFunction?>('<?=$reportType?>');">
                </div>
            <? } ?>
        </div>
    <?}
  function mailer_Mailer_Run_TotalsSummary($company_id){
    $Mailer_DAL         = new Mailer_DAL();
    $General_DAL        = new GENERAL_DAL();
    $Profiles_DAL       = new Profiles_DAL();
    if ( (isset($_SESSION['mailer_run']['mailer_run_id']) && !isset($_SESSION['mailer_run']['created_by_login_id'])) ) {
            $mailer_run_data = $Mailer_DAL->mailer_MailerRunDetails($_SESSION['mailer_run']['mailer_run_id']);
            if ( ( $mailer_run_data[0]->total_count - $mailer_run_data[0]->completed_mails ) >0) { $status_message = 'In Process'; } else { $status_message = 'Completed'; }
            ?>
            <div id="mailer_Mailer_Run_TotalsSummary"  class="wp80 hp100 f_left left center bclightgray-2 vbottom s08">
                    <div class="f_left wp100 hp20 HEADER main_bc_color1 main_color1_text s15">
                        <div class='wp20 hp100 f_left s15'>&nbsp;</div>
                        <div class='wp46 hp100 f_left'><?= $status_message ?> Mail Run (<?=$_SESSION['mailer_run']['mailer_run_id']?>)</div>
                        <div class='wp33 hp100 f_right right mp' onclick="">&nbsp;</div>
                    </div>
                    <div class="f_left wp100 hp10">
                      <div class='wp70 hp100 f_left left '>Mailers Customers:</div>
                      <div class='wp29 hp100 f_left right bold'><?=$mailer_run_data[0]->total_count?></div>
                    </div>
                    <div class="f_left wp100 hp10">
                      <? if ( ( isset($_SESSION['mailer_run']['Mailer_Show_Completed_Mailer_Run']) && $_SESSION['mailer_run']['Mailer_Show_Completed_Mailer_Run'] ==1 ) ) { ?>
                      <div class='wp70 hp100 f_left left '>Completed Mailers:</div>
                      <div class='wp29 hp100 f_left right mp bold green' id="completed_mailers"><?=$mailer_run_data[0]->completed_mails?></div>
                      <? } else { ?>
                      <div class='wp70 hp100 f_left left  mp' onclick="Mailer_Show_Completed_Mailer_Run(1)">Completed Mailers:</div>
                      <div class='wp29 hp100 f_left right mp bold' id="completed_mailers" onclick="Mailer_Show_Completed_Mailer_Run(1)"><?=$mailer_run_data[0]->completed_mails?></div>
                      <? } ?>
                    </div>
                    <div class="f_left wp100 hp10">
                      <? if ( !(isset($_SESSION['mailer_run']['Mailer_Show_Completed_Mailer_Run'])) || (isset($_SESSION['mailer_run']['Mailer_Show_Completed_Mailer_Run']) && $_SESSION['mailer_run']['Mailer_Show_Completed_Mailer_Run'] ==0 ) ) { ?>
                      <div class='wp50 hp100 f_left left '>Remaining Mailers:</div>
                      <div class='wp49 hp100 f_left right bold yellow' id="remaining_mailers"><?=$mailer_run_data[0]->total_count - $mailer_run_data[0]->completed_mails?></div>
                      <? } else { ?>
                      <div class='wp50 hp100 f_left left  mp' onclick="Mailer_Show_Completed_Mailer_Run(0)">Remaining Mailers:</div>
                      <div class='wp49 hp100 f_left right mp bold yellow' id="remaining_mailers" onclick="Mailer_Show_Completed_Mailer_Run(0)"><?=$mailer_run_data[0]->total_count - $mailer_run_data[0]->completed_mails?></div>
                      <? } ?>
                    </div>
                    <div class="f_left wp100 hp15">
                      <div class='wp50 hp100 f_left left '>Assigned to:</div>
                      <div class='wp49 hp100 f_left right'>
                            <?= $mailer_run_data[0]->l_assigned_firstname ?> <?= $mailer_run_data[0]->l_assigned_lastname ?>
                      </div>
                    </div>
                    <div class="f_left wp100 hp15">
                      <div class='wp50 hp100 f_left left '>Created by:</div>
                      <div class='wp49 hp100 f_left right'>
                            <?= $mailer_run_data[0]->l_created_firstname ?> <?= $mailer_run_data[0]->l_created_lastname ?>
                      </div>
                    </div>
                    <div class="f_left wp100 hp15">
                      <div class='wp50 hp100 f_left left '>Template Type:</div>
                      <div class='wp49 hp100 f_left right bold red'>
                            <?= $mailer_run_data[0]->template_name ?>
                      </div>
                    </div>
            </div>
            <div class="wp20 hp100 f_left center">
                <div class="f_left wp100 hp100 mt20">&nbsp;
                    <input type="button" onclick="Mailer_MailerRun_Details(<?=$_SESSION['mailer_run']['mailer_run_id']?>)" value="Refresh?" class="button mp">
                </div>
            </div>
        <?
        } else {
            $possible_count     = $Mailer_DAL->mailer_GetCustomerPossibleCount($company_id);
            $Active_count       = $Mailer_DAL->mailer_GetCustomerActiveCount($company_id);
            $InActive_count     = $Mailer_DAL->mailer_GetCustomerInactiveCount($company_id);
            $OPTEDIN_count      = $Mailer_DAL->mailer_GetCustomerOPTEDINCount($company_id);
            ?>
            <div id="mailer_Mailer_Run_TotalsSummary"  class="wp100 hp100 f_left left center bclightgray-2 vbottom s08">
                    <div class="f_left wp100 hp17 HEADER main_bc_color1 main_color1_text s15">
                      <div class='wp20 hp100 f_left'>&nbsp;</div>
                      <div class='wp46 hp100 f_left'>Pending Mail Run</div>
                      <div class='wp33 hp100 f_right right mp' onclick="mailer_Mailer_Run_ShowSettings(<?=$company_id?>)">&nbsp; x</div>
                    </div>
                    <div class="f_left wp100 hp10">
                      <div class='wp70 hp100 f_left left '>Total Customers:</div>
                      <div class='wp29 hp100 f_left right bold'><?=$possible_count[0]->count?></div>
                    </div>
                    <div class="f_left wp100 hp10">
                      <div class='wp70 hp100 f_left left '>Total Active Customers:</div>
                      <div class='wp29 hp100 f_left right bold'><?=$Active_count[0]->count?></div>
                    </div>
                    <div class="f_left wp100 hp10">
                      <div class='wp50 hp100 f_left left '>Total Inactive Customers:</div>
                      <div class='wp49 hp100 f_left right bold'><?=$InActive_count[0]->count?></div>
                    </div>
                    <div class="f_left wp100 hp10">
                      <div class='wp50 hp100 f_left left '>Total OPTED-in Customers:</div>
                      <div class='wp49 hp100 f_left right bold'><?=$OPTEDIN_count[0]->count?></div>
                    </div>
                    <div class="f_left wp100 hp10">
                      <div class='wp50 hp100 f_left left '>Total MAILED customers:</div>
                      <div class='wp49 hp100 f_left right bold'>0</div>
                    </div>
                    <div class="f_left wp100 hp15">
                        <div class="f_left wp50 hp100">
                          <div class='wp35 hp100 f_left right '><img height="15" width=15" onclick="Mailer_UpdateRunInfo()" title="Refresh" src="/common_includes/includes/images/Refresh-icon.png">&nbsp;Template #:</div>
                          <div class='wp65 hp100 f_left left'>
                            <select id='mailer_run_template_id' onchange="Mailer_UpdateRunInfo()">
                              <option value='0'>-please select-</option>
                                <?
                                $rows = $Mailer_DAL->get_AllMailerTemplates($company_id);
                                foreach ($rows as $row) { ?>
                                    <option value='<?=$row->id?>'  <?= isset($_SESSION['mailer_run']['mailer_run_template_id']) && $_SESSION['mailer_run']['mailer_run_template_id'] == $row->id  ? ' selected' : ''?> ><?=$row->template_name ?></option>
                                <? } ?>
                            </select>
                          </div>
                        </div>
                        <div class="f_left wp50 hp100">
                          <div class='wp35 hp100 f_left right'>Assigned to:</div>
                          <div class='wp65 hp100 f_left left'>
                            <select id='mailer_run_login_id' onchange="Mailer_UpdateRunInfo()">
                              <option value='0'>-please select-</option>
                                <?
                                $rows = $General_DAL->get_AllEmployeesPerCompanyId($company_id,1);
                                foreach ($rows as $row) { ?>
                                    <option value='<?=$row->id?>'  <?= isset($_SESSION['mailer_run']['mailer_run_login_id']) && $_SESSION['mailer_run']['mailer_run_login_id'] == $row->id  ? ' selected' : ''?>><?=$row->firstname ?> <?=$row->lastname ?></option>
                                <? } ?>
                            </select>
                          </div>
                        </div>
                    </div>
                    <? if ($_SESSION['mailer_run']['done'] != 1 ) { ?>
                    <div class="f_left wp100 hp25">
                      <div class='wp50 hp100 f_left left'>&nbsp;
                            <? if ( !(isset($_SESSION['mailer_run']['mailer_run_id'])) ) { ?>
                            <input type="button" class="red button"     value="Cancel Mailer Run"       onclick="Mailer_Cancel_Mailer_Run()">
                            <? } ?>
                      </div>
                      <div class='wp50 hp100 f_right right'>&nbsp;
                            <? if ( !(isset($_SESSION['mailer_run']['mailer_run_id'])) && isset($_SESSION['mailer_run']['mailer_run_login_id']) && ( isset($_SESSION['mailer_run']['mailer_run_template_id']) && $_SESSION['mailer_run']['mailer_run_template_id'] != 0 ) ) { ?>
                            <input type="button" class="green button "   value="Create Mailer Run"      onclick="Mailer_CreateMailer_Run(<?=$_SESSION['settings']['company_id']?>,<?=$_SESSION['mailer_run']['mailer_run_login_id']?>,<?=$_SESSION['settings']['login_id']?>,<?=$_SESSION['mailer_run']['mailer_run_template_id']?>)">
                            <? } ?>
                      </div>
                    </div>
                    <? } ?>


            </div>
    <? } ?>
<?
}
    function mailer_Mailer_Run_ShowCustomers($company_id) {
        $customers_to_show = 250;
        ?>
        <div class="wp100 hp100 f_left">
            <div class='f_left left wp100 hp10'>
                <div class='f_left left wp50 hp100'>
                    <div class="f_left left wp100 hp100 s07 bold ml5">Customers in the current "Mailer Run".</div>
                </div>
                <div class='f_left left wp35 hp100'>
                    <div class="f_left hp100 red s06">Showing a maximum of <?=$customers_to_show?> customers.</div>
                </div>
                <div class='f_left right wp15 hp100 mp'>&nbsp;
                    <?if ( isset($_SESSION['mailer_run']['mailer_run_id']) ) {?>
                    <input type="button" onclick="Mailer_SendMassMail(<?=$_SESSION['mailer_run']['mailer_run_id']?>)" value="Mail All" class="button mp">
                    <? } ?>
                </div>
            </div>
            <div class="f_left wp100 hp10 s08 HEADER main_bc_color1 main_color1_text">
                <div class="f_left report_header_cell_wp15 hp100">Name</div>
                <div class="f_left report_header_cell_wp30 hp100">Email Address</div>
                <div class="f_left report_header_cell_wp07 hp100">Mailed?</div>
                <div class="f_left report_header_cell_wp13 hp100">APPTs</div>
                <div class="f_left report_header_cell_wp07 hp100">Sales</div>
                <div class="f_left report_header_cell_wp13 hp100">Added</div>
                <div class="f_left report_header_cell_wp12 hp100">Completed</div>
            </div>
        <?if ( isset($_SESSION['mailer_run']['mailer_run_login_id']) || isset($_SESSION['mailer_run']['mailer_run_id']) ) {
                $Mailer_DAL         = new Mailer_DAL();
                $Customers_dal      = new Customers_DAL();
                $altClass           = "bctr1a";
                $mailer_run_item_ids= array();
                $MailerCustomers    = $Mailer_DAL->mailer_GetMailerCustomers_per_Mailer_Run_ID($_SESSION['mailer_run']['mailer_run_id'],0,$customers_to_show);
                if ( count($MailerCustomers) == 0 || ( isset($_SESSION['mailer_run']['Mailer_Show_Completed_Mailer_Run']) && $_SESSION['mailer_run']['Mailer_Show_Completed_Mailer_Run'] ==1 ) ) {
                    $MailerCustomers    = $Mailer_DAL->mailer_GetMailerCustomers_per_Mailer_Run_ID($_SESSION['mailer_run']['mailer_run_id'],1,0);
                    $altClass           = 'bclightgreen';
                }?>
                <div class="f_left wp100 hp78 scrolling">
                <?
                    foreach($MailerCustomers as $customer_mailer_info) {
                        $Customer_Sale_Count            = $Customers_dal->get_Customer_Sale_Count($company_id,$customer_mailer_info->customer_id);
                        $Customer_Appt_Count            = $Customers_dal->get_Customer_Appt_Count($company_id,$customer_mailer_info->customer_id);
                        if ($customer_mailer_info->status == 0) { array_push($mailer_run_item_ids, $customer_mailer_info->id); }
                        ?>
                    <div class="f_left wp100 h20px s08 <?=$altClass?>" id="Mailer_customer_mailer_info_row_<?=$customer_mailer_info->id?>">
                        <div class="report_data_cell_wp15 hp100 f_left left no-overflow">&nbsp;
                            <?=$customer_mailer_info->firstname?> <?=$customer_mailer_info->lastname?>
                        </div>
                        <div class="report_data_cell_wp30 hp100 f_left left no-overflow s07 pl3" title="<?=$customer_mailer_info->email?>">
                            <?=$customer_mailer_info->email?>
                        </div>
                        <div class="report_data_cell_wp07 hp100 f_left no-overflow" id="Mailer_customer_mailer_info_row_status_<?=$customer_mailer_info->id?>">
                            <?=$customer_mailer_info->status == 0 ? '<font class=red>NO</font>' : 'Yes'?>
                            <? if ($customer_mailer_info->status == 0 ) { ?><a class="mp" onclick="Mailer_SendMail(<?=$customer_mailer_info->id?>)"><img alt="" src="/common_includes/includes/images/email_icon_small.png"></a><? } ?>
                        </div>
                        <div class="report_data_cell_wp13 hp100 f_left no-overflow">&nbsp;
                            <?=$Customer_Appt_Count[0]->count?>
                        </div>
                        <div class="report_data_cell_wp07 hp100 f_left no-overflow">&nbsp;
                            <?=$Customer_Sale_Count[0]->count?>
                        </div>
                        <div class="report_data_cell_wp13 hp100 f_left no-overflow s07">&nbsp;
                            <?=date("d/m/y h:i a",strtotime($customer_mailer_info->added))?>
                        </div>
                        <div class="report_data_cell_wp12 hp100 f_left no-overflow s07" id="Mailer_customer_mailer_info_row_complete_date_<?=$customer_mailer_info->id?>">&nbsp;
                            <?=$customer_mailer_info->completed_date === NULL ? 'Not Yet' : date("d/m/y h:i a",strtotime($customer_mailer_info->completed_date))?>
                        </div>
                    </div>
                    <?
                    if($altClass != 'bclightgreen') {
                        if ($altClass == "bctr1a") { $altClass = "bctr1b"; } else {$altClass = "bctr1a";}
                    }
                    }?>
                </div>
                <? if (count($mailer_run_item_ids) > 0 ) {?>
                <input type="hidden" id="mailer_run_item_ids" value="<?=implode(',',$mailer_run_item_ids)?>">
                <? } else { ?>
                <input type="hidden" id="mailer_run_item_ids" value='-1'>
                <? } ?>
        <?} else {?>
            <div class="f_left wp100 h25px">
                <div class="report_data_cell_wp99 h25px left f_left">
                    All Customers have been mailed from this "mailer Run".
                </div>
            </div>
        <?}?>
        </div>
  <?}
    function mailer_Mailer_Run_ShowSettings($company_id){
   $companies_dal = new Companies_DAL();
?>
    <div class="d_InlineBlock f_right wp100 hp100 ">
        <? Companies_showTemplateTab_byID($companies_dal,$company_id,6) ?>
    </div>
<?}

function email_Template_1($customer_id,$debug){
    include_once('companies_functions.php');
    include_once('customers_functions.php');
    $BGcolor                                = "#C1C1C1";
    $Customer_dal                           = new Customers_DAL();
    $Companies_dal                          = new Companies_DAL();
    $general_dal                            = new GENERAL_DAL();
    $IMAGE_DAL                              = new IMAGE_DATA_DAL();

    $Customer_Info                          = $Customer_dal->get_CustomerDataPerId($customer_id);
    $COMPANY_NAME                           = $Companies_dal->get_TemplateTabData_by_Name('COMPANY_NAME',   $Customer_Info[0]->company_id);
    $company_id                             = $Customer_Info[0]->company_id;
        if (count($Customer_Info))                  { $email_send_address             = $Customer_Info[0]->email_address; }
        else                                        { $email_send_address             = "Not yet Defined \$Customer_Info[0]->email_address"; }

    $email_Template_1_SUBJECT                   = $Companies_dal->get_TemplateTabData_by_Name('email_Template_1_SUBJECT',$company_id);
        if (count($email_Template_1_SUBJECT))       { $email_Template_SUBJECT             = $email_Template_1_SUBJECT[0]->value; }
        else                                        { $email_Template_SUBJECT             = "Not yet Defined \$email_Template_1_SUBJECT[0]->value"; }

    $email_Template_1_reply_address         = $Companies_dal->get_TemplateTabData_by_Name('email_Template_1_reply_address',$company_id);
        if (count($email_Template_1_reply_address)) { $reply_address             = $email_Template_1_reply_address[0]->value; }
        else                                        { $reply_address             = "Not yet Defined \$email_Template_1_reply_address[0]->value"; }

    $message                                = email_Template_1_Master($company_id,$customer_id,$general_dal,$IMAGE_DAL,$Companies_dal,$Customer_dal,$BGcolor);
    if ($debug != 1) {
        #print "Mailing to $email_send_address";
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=iso-8859-1" . "\r\n";
        $headers .= "From: ".$COMPANY_NAME[0]->value." <".$reply_address.">\r\n";
        mail($email_send_address,$email_Template_SUBJECT,$message,$headers);
    }
    else {
        print "REPLY TO: ".$COMPANY_NAME[0]->value." <$reply_address> <br>";
        print "Mailing to: $email_send_address<br>";
        print "Subject: $email_Template_SUBJECT<br>";
        echo $message;
    }

}
    function email_Template_1_Master($company_id,$customer_id,$general_dal,$IMAGE_DAL,$Companies_dal,$Customer_dal,$BGcolor){
    include_once('mailer/mailer_functions_1.php');

    ob_start();
    email_TemplateHeader($company_id,$customer_id,$general_dal,$IMAGE_DAL,$Companies_dal,$Customer_dal,$BGcolor);
    $Header     = ob_get_clean();

    ob_start();
    email_TemplateTopRow($company_id,$customer_id,$general_dal,$IMAGE_DAL,$Companies_dal,$Customer_dal,$BGcolor);
    $TopRow     = ob_get_clean();

    ob_start();
    email_TemplateMailBody($company_id,$customer_id,$general_dal,$IMAGE_DAL,$Companies_dal,$Customer_dal,$BGcolor);
    $MailBody       = ob_get_clean();

    ob_start();
    email_TemplateBottomRow($company_id,$customer_id,$general_dal,$IMAGE_DAL,$Companies_dal,$Customer_dal,$BGcolor);
    $BottomRow  = ob_get_clean();

    ob_start();
    email_TemplateFooter($company_id,$customer_id,$general_dal,$IMAGE_DAL,$Companies_dal,$Customer_dal,$BGcolor);
    $Footer     = ob_get_clean();

    $html   = $Header;
    $html  .="<div style=\"margin: 0px auto 0px auto; width:600px;\">";
        $html  .=$TopRow;
        $html  .=$MailBody;
        $html  .=$BottomRow;
        $html  .=$Footer;
    $html  .="</div>";
return $html;
}

function email_Template_2($customer_id,$debug){
    include_once('companies_functions.php');
    include_once('customers_functions.php');
    $BGcolor                                = "#C1C1C1";
    $Customer_dal                           = new Customers_DAL();
    $Companies_dal                          = new Companies_DAL();
    $general_dal                            = new GENERAL_DAL();
    $IMAGE_DAL                              = new IMAGE_DATA_DAL();

    $Customer_Info                          = $Customer_dal->get_CustomerDataPerId($customer_id);
    $COMPANY_NAME                           = $Companies_dal->get_TemplateTabData_by_Name('COMPANY_NAME',   $Customer_Info[0]->company_id);
    $company_id                             = $Customer_Info[0]->company_id;
        if (count($Customer_Info))                  { $email_send_address             = $Customer_Info[0]->email_address; }
        else                                        { $email_send_address             = "Not yet Defined \$Customer_Info[0]->email_address"; }

    $email_Template_1_SUBJECT                   = $Companies_dal->get_TemplateTabData_by_Name('email_Template_1_SUBJECT',$company_id);
        if (count($email_Template_1_SUBJECT))       { $email_Template_SUBJECT             = $email_Template_1_SUBJECT[0]->value; }
        else                                        { $email_Template_SUBJECT             = "Not yet Defined \$email_Template_1_SUBJECT[0]->value"; }

    $email_Template_1_reply_address         = $Companies_dal->get_TemplateTabData_by_Name('email_Template_1_reply_address',$company_id);
        if (count($email_Template_1_reply_address)) { $reply_address             = $email_Template_1_reply_address[0]->value; }
        else                                        { $reply_address             = "Not yet Defined \$email_Template_1_reply_address[0]->value"; }

    $message                                = email_Template_2_Master($company_id,$customer_id,$general_dal,$IMAGE_DAL,$Companies_dal,$Customer_dal,$BGcolor);
    if ($debug != 1) {
        #print "Mailing to $email_send_address";
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=iso-8859-1" . "\r\n";
        $headers .= "From: ".$COMPANY_NAME[0]->value." <".$reply_address.">\r\n";
        mail($email_send_address,$email_Template_1_SUBJECT[0]->value,$message,$headers);
    }
    else {
        print "REPLY TO: ".$COMPANY_NAME[0]->value." <$reply_address> <br>";
        print "Mailing to: $email_send_address<br>";
        print "Subject: $email_Template_SUBJECT<br>";
        echo $message;
    }
}
    function email_Template_2_Master($company_id,$customer_id,$general_dal,$IMAGE_DAL,$Companies_dal,$Customer_dal,$BGcolor){
    include_once('mailer/mailer_functions_2.php');

    ob_start();
    email_TemplateHeader($company_id,$customer_id,$general_dal,$IMAGE_DAL,$Companies_dal,$Customer_dal,$BGcolor);
    $Header     = ob_get_clean();

    ob_start();
    email_Template_2_MailBody($company_id,$customer_id,$general_dal,$IMAGE_DAL,$Companies_dal,$Customer_dal,$BGcolor);
    $MailBody   = ob_get_clean();
  
    ob_start();
    email_Template_2_Footer($company_id,$customer_id,$general_dal,$IMAGE_DAL,$Companies_dal,$Customer_dal,$BGcolor);
    $Footer     = ob_get_clean();

    $html   = $Header;
    $html  .="<div style=\"margin: 0px auto 0px auto; width:600px;\">";
        $html  .=$MailBody;
        $html  .=$Footer;
    $html  .="</div>";
return $html;
}
        function email_Template_2_MailBody ($company_id,$customer_id,$general_dal,$IMAGE_DAL,$Companies_dal,$Customer_dal,$BGcolor){
list($host,$domain)                     = setup_path_mailer();
$COMPANY_URL                            = $Companies_dal->get_TemplateTabData_by_Name('COMPANY_URL',$company_id);

$email_Template_1_1                     = $IMAGE_DAL->get_Imagedata_byPreferenceName_AND_CompanyID($company_id,'email_Template_1_1');
$email_Template_1_2                     = $IMAGE_DAL->get_Imagedata_byPreferenceName_AND_CompanyID($company_id,'email_Template_1_2');
?>
    <div class="main_bc_color1 main_color1_text" style="width:100%; float:left; font-family: Arial; ">
        <div style="text-align:center; width:100%; height:100%;">
            <table width="100%" height="100%" border="0" cellspacing="0">
                <tr >
                    <td width="600" colspan="3" style="border: 1px solid #cac8c1;">
                        <?email_Template_2_TopRow($company_id,$customer_id,$general_dal,$IMAGE_DAL,$Companies_dal,$Customer_dal,$BGcolor)?>
                    </td>
                </tr>

                <?email_Template_TR_Spacer(20,3)?>

                <tr>
                    <td width="380" height="200" style="border: 1px solid #cac8c1;">
                        <?email_Template_DIV_TEXT($company_id,$customer_id,$general_dal,$IMAGE_DAL,$Companies_dal,$Customer_dal,$BGcolor)?>
                    </td>
                    <td width="20">
                        &nbsp;
                    </td>
                    <td width="200" rowspan="5" valign="top" style="border: 1px solid #cac8c1;">
                        <?email_Template_DIV_SocialMedia($company_id,$customer_id,$general_dal,$IMAGE_DAL,$Companies_dal,$Customer_dal,$BGcolor)?>
                    </td>
                </tr>

                <?email_Template_TR_Spacer(20,3)?>

                <tr>
                    <td width="380" height="200" style="border: 1px solid #cac8c1;">
                        <?email_Template_DIV_PICTURE($company_id,$customer_id,$general_dal,$IMAGE_DAL,$Companies_dal,$Customer_dal,$BGcolor)?>
                    </td>
                </tr>

                <?email_Template_TR_Spacer(20,3)?>

                <tr>
                    <td width="380" height="200" style="border: 1px solid #cac8c1;">
                        <?email_Template_DIV_TEXT_n_PICTURE($company_id,$customer_id,$general_dal,$IMAGE_DAL,$Companies_dal,$Customer_dal,$BGcolor)?>
                    </td>
                </tr>

                <?email_Template_TR_Spacer(20,3)?>
            </table>
        </div>
    </div>
<?}

function email_Template_3($customer_id,$debug){
    include_once('companies_functions.php');
    include_once('customers_functions.php');
    $BGcolor                                = "#C1C1C1";
    $Customer_dal                           = new Customers_DAL();
    $Companies_dal                          = new Companies_DAL();
    $general_dal                            = new GENERAL_DAL();
    $IMAGE_DAL                              = new IMAGE_DATA_DAL();

    $Customer_Info                          = $Customer_dal->get_CustomerDataPerId($customer_id);
    $COMPANY_NAME                           = $Companies_dal->get_TemplateTabData_by_Name('COMPANY_NAME',   $Customer_Info[0]->company_id);
    $company_id                             = $Customer_Info[0]->company_id;
        if (count($Customer_Info))                  { $email_send_address             = $Customer_Info[0]->email_address; }
        else                                        { $email_send_address             = "Not yet Defined \$Customer_Info[0]->email_address"; }

    $email_Template_1_SUBJECT                   = $Companies_dal->get_TemplateTabData_by_Name('email_Template_1_SUBJECT',$company_id);
        if (count($email_Template_1_SUBJECT))       { $email_Template_SUBJECT             = $email_Template_1_SUBJECT[0]->value; }
        else                                        { $email_Template_SUBJECT             = "Not yet Defined \$email_Template_1_SUBJECT[0]->value"; }

    $email_Template_1_reply_address         = $Companies_dal->get_TemplateTabData_by_Name('email_Template_1_reply_address',$company_id);
        if (count($email_Template_1_reply_address)) { $reply_address             = $email_Template_1_reply_address[0]->value; }
        else                                        { $reply_address             = "Not yet Defined \$email_Template_1_reply_address[0]->value"; }

    $message                                = email_Template_3_Master($company_id,$customer_id,$general_dal,$IMAGE_DAL,$Companies_dal,$Customer_dal,$BGcolor);
    if ($debug != 1) {
        #print "Mailing to $email_send_address";
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=iso-8859-1" . "\r\n";
        $headers .= "From: ".$COMPANY_NAME[0]->value." <".$reply_address.">\r\n";
        mail($email_send_address,$email_Template_1_SUBJECT[0]->value,$message,$headers);
    }
    else {
        print "REPLY TO: ".$COMPANY_NAME[0]->value." <$reply_address> <br>";
        print "Mailing to: $email_send_address<br>";
        print "Subject: $email_Template_SUBJECT<br>";
        echo $message;
    }

}
    function email_Template_3_Master($company_id,$customer_id,$general_dal,$IMAGE_DAL,$Companies_dal,$Customer_dal,$BGcolor){
    include_once('mailer/mailer_functions_3.php');

    ob_start();
    email_TemplateHeader($company_id,$customer_id,$general_dal,$IMAGE_DAL,$Companies_dal,$Customer_dal,$BGcolor);
    $Header     = ob_get_clean();

    ob_start();
    email_Template_3_MailBody($company_id,$customer_id,$general_dal,$IMAGE_DAL,$Companies_dal,$Customer_dal,$BGcolor);
    $MailBody   = ob_get_clean();

    ob_start();
    email_TemplateFooter_3($company_id,$customer_id,$general_dal,$IMAGE_DAL,$Companies_dal,$Customer_dal,$BGcolor);
    $Footer     = ob_get_clean();

    $html   = $Header;
    $html  .="<div style=\"margin: 0px auto 0px auto; width:600px;\">";
        $html  .=$MailBody;
        $html  .=$Footer;
    $html  .="</div>";


return $html;
}
        function email_Template_3_MailBody ($company_id,$customer_id,$general_dal,$IMAGE_DAL,$Companies_dal,$Customer_dal,$BGcolor){
        list($host,$domain)                     = setup_path_mailer();
        $COMPANY_URL                            = $Companies_dal->get_TemplateTabData_by_Name('COMPANY_URL',$company_id);

        $email_Template_1_1                     = $IMAGE_DAL->get_Imagedata_byPreferenceName_AND_CompanyID($company_id,'email_Template_1_1');
        $email_Template_1_2                     = $IMAGE_DAL->get_Imagedata_byPreferenceName_AND_CompanyID($company_id,'email_Template_1_2');
?>
    <div class="main_bc_color1 main_color1_text" style="width:100%; float:left; font-family: Arial; ">
        <div style="text-align:center; width:100%;">
            <table width="100%" height="100%" border="0" cellspacing="0">
                <tr >
                    <td width="600" colspan="3" style="border: 1px solid #cac8c1;">
                        <?email_Template_3_TopRow($company_id,$customer_id,$general_dal,$IMAGE_DAL,$Companies_dal,$Customer_dal,$BGcolor)?>
                    </td>
                </tr>
                    <?email_Template_TR_Spacer(20,3)?>
                <tr>
                    <td width="380" height="200" style="border: 1px solid #cac8c1;">
                        <?email_Template_3_DIV_TEXT_n_PICTURE($company_id,$customer_id,$general_dal,$IMAGE_DAL,$Companies_dal,$Customer_dal,$BGcolor)?>
                    </td>
                    <td width="20">
                        &nbsp;
                    </td>
                    <td width="200" rowspan="5" valign="top" style="border: 1px solid #cac8c1;">
                        <?email_Template_3_DIV_SocialMedia($company_id,$customer_id,$general_dal,$IMAGE_DAL,$Companies_dal,$Customer_dal,$BGcolor)?>
                    </td>
                </tr>
            </table>
        </div>
    </div>
<?}

function email_Template_4($customer_id,$debug){
    include_once('companies_functions.php');
    include_once('customers_functions.php');
    $BGcolor                                = "#C1C1C1";
    $Customer_dal                           = new Customers_DAL();
    $Companies_dal                          = new Companies_DAL();
    $general_dal                            = new GENERAL_DAL();
    $IMAGE_DAL                              = new IMAGE_DATA_DAL();

    $Customer_Info                          = $Customer_dal->get_CustomerDataPerId($customer_id);
    $COMPANY_NAME                           = $Companies_dal->get_TemplateTabData_by_Name('COMPANY_NAME',   $Customer_Info[0]->company_id);
    $company_id                             = $Customer_Info[0]->company_id;
        if (count($Customer_Info))                  { $email_send_address             = $Customer_Info[0]->email_address; }
        else                                        { $email_send_address             = "Not yet Defined \$Customer_Info[0]->email_address"; }

    $email_Template_1_SUBJECT                   = $Companies_dal->get_TemplateTabData_by_Name('email_Template_1_SUBJECT',$company_id);
        if (count($email_Template_1_SUBJECT))       { $email_Template_SUBJECT             = $email_Template_1_SUBJECT[0]->value; }
        else                                        { $email_Template_SUBJECT             = "Not yet Defined \$email_Template_1_SUBJECT[0]->value"; }

    $email_Template_1_reply_address         = $Companies_dal->get_TemplateTabData_by_Name('email_Template_1_reply_address',$company_id);
        if (count($email_Template_1_reply_address)) { $reply_address             = $email_Template_1_reply_address[0]->value; }
        else                                        { $reply_address             = "Not yet Defined \$email_Template_1_reply_address[0]->value"; }

    $message                                = email_Template_4_Master($company_id,$customer_id,$general_dal,$IMAGE_DAL,$Companies_dal,$Customer_dal,$BGcolor);
    if ($debug != 1) {
        #print "Mailing to $email_send_address";
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=iso-8859-1" . "\r\n";
        $headers .= "From: ".$COMPANY_NAME[0]->value." <".$reply_address.">\r\n";
        mail($email_send_address,$email_Template_1_SUBJECT[0]->value,$message,$headers);
    }
    else {
        print "REPLY TO: ".$COMPANY_NAME[0]->value." <$reply_address> <br>";
        print "Mailing to: $email_send_address<br>";
        print "Subject: $email_Template_SUBJECT<br>";
        echo $message;
    }

}
    function email_Template_4_Master($company_id,$customer_id,$general_dal,$IMAGE_DAL,$Companies_dal,$Customer_dal,$BGcolor){
    include_once('mailer/mailer_functions_4.php');

    ob_start();
    email_TemplateHeader($company_id,$customer_id,$general_dal,$IMAGE_DAL,$Companies_dal,$Customer_dal,$BGcolor);
    $Header     = ob_get_clean();

    ob_start();
    email_Template_4_MailBody($company_id,$customer_id,$general_dal,$IMAGE_DAL,$Companies_dal,$Customer_dal,$BGcolor);
    $MailBody   = ob_get_clean();

    $html   = $Header;
    $html  .="<div style=\"margin: 0px auto 0px auto; width:600px;\">";
        $html  .=$MailBody;
    $html  .="</div>";

return $html;
}
        function email_Template_4_MailBody ($company_id,$customer_id,$general_dal,$IMAGE_DAL,$Companies_dal,$Customer_dal,$BGcolor){
        list($host,$domain)                     = setup_path_mailer();
        $COMPANY_URL                            = $Companies_dal->get_TemplateTabData_by_Name('COMPANY_URL',$company_id);

        $email_Template_1_1                     = $IMAGE_DAL->get_Imagedata_byPreferenceName_AND_CompanyID($company_id,'email_Template_1_1');
        $email_Template_1_2                     = $IMAGE_DAL->get_Imagedata_byPreferenceName_AND_CompanyID($company_id,'email_Template_1_2');
?>
<div class="main_bc_color1 main_color1_text" style="width:100%; float:left; font-family: Arial; ">
    <div style="text-align:center; width:100%; height:100%;">
        <table style="border:1px solid rgb(0,0,0);background-color:rgb(51,51,102);" cellspacing="0" width="600" >
            <tbody>
                <tr >
                    <td height="580" valign="top" width="99%" align="center" >
                        <table style="" cellspacing="0" cellpadding="0" width="98%" bgcolor="#333366" >
                            <tbody>
                                <?email_Template_4_Row_1($company_id,$customer_id,$general_dal,$IMAGE_DAL,$Companies_dal,$Customer_dal,$BGcolor)?>
                                <?email_Template_4_Row_2($company_id,$customer_id,$general_dal,$IMAGE_DAL,$Companies_dal,$Customer_dal,$BGcolor)?>
                                <?email_Template_4_Row_3($company_id,$customer_id,$general_dal,$IMAGE_DAL,$Companies_dal,$Customer_dal,$BGcolor)?>
                                <?email_Template_4_Row_4($company_id,$customer_id,$general_dal,$IMAGE_DAL,$Companies_dal,$Customer_dal,$BGcolor)?>
                                <?email_TemplateFooter_2($company_id,$customer_id,$general_dal,$IMAGE_DAL,$Companies_dal,$Customer_dal,$BGcolor)?>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<?}

function email_Template_5($customer_id,$debug){
    include_once('companies_functions.php');
    include_once('customers_functions.php');
    $BGcolor                                = "#C1C1C1";
    $Customer_dal                           = new Customers_DAL();
    $Companies_dal                          = new Companies_DAL();
    $general_dal                            = new GENERAL_DAL();
    $IMAGE_DAL                              = new IMAGE_DATA_DAL();

    $Customer_Info                          = $Customer_dal->get_CustomerDataPerId($customer_id);
    $COMPANY_NAME                           = $Companies_dal->get_TemplateTabData_by_Name('COMPANY_NAME',   $Customer_Info[0]->company_id);
    $company_id                             = $Customer_Info[0]->company_id;
        if (count($Customer_Info))                  { $email_send_address             = $Customer_Info[0]->email_address; }
        else                                        { $email_send_address             = "Not yet Defined \$Customer_Info[0]->email_address"; }

    $email_Template_1_SUBJECT                   = $Companies_dal->get_TemplateTabData_by_Name('email_Template_1_SUBJECT',$company_id);
        if (count($email_Template_1_SUBJECT))       { $email_Template_SUBJECT             = $email_Template_1_SUBJECT[0]->value; }
        else                                        { $email_Template_SUBJECT             = "Not yet Defined \$email_Template_1_SUBJECT[0]->value"; }

    $email_Template_1_reply_address         = $Companies_dal->get_TemplateTabData_by_Name('email_Template_1_reply_address',$company_id);
        if (count($email_Template_1_reply_address)) { $reply_address             = $email_Template_1_reply_address[0]->value; }
        else                                        { $reply_address             = "Not yet Defined \$email_Template_1_reply_address[0]->value"; }

    $message                                = email_Template_5_Master($company_id,$customer_id,$general_dal,$IMAGE_DAL,$Companies_dal,$Customer_dal,$BGcolor);
    if ($debug != 1) {
        #print "Mailing to $email_send_address";
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=iso-8859-1" . "\r\n";
        $headers .= "From: ".$COMPANY_NAME[0]->value." <".$reply_address.">\r\n";
        mail($email_send_address,$email_Template_SUBJECT,$message,$headers);
    }
    else {
        print "REPLY TO: ".$COMPANY_NAME[0]->value." <$reply_address> <br>";
        print "Mailing to: $email_send_address<br>";
        print "Subject: $email_Template_SUBJECT<br>";
        echo $message;
    }

}
    function email_Template_5_Master($company_id,$customer_id,$general_dal,$IMAGE_DAL,$Companies_dal,$Customer_dal,$BGcolor){
    include_once('mailer/mailer_functions_5.php');
    ob_start();
    email_TemplateHeader($company_id,$customer_id,$general_dal,$IMAGE_DAL,$Companies_dal,$Customer_dal,$BGcolor);
    $Header     = ob_get_clean();

    ob_start();
    email_TemplateTopRow($company_id,$customer_id,$general_dal,$IMAGE_DAL,$Companies_dal,$Customer_dal,$BGcolor);
    $TopRow     = ob_get_clean();

    ob_start();
    email_TemplateMailBody($company_id,$customer_id,$general_dal,$IMAGE_DAL,$Companies_dal,$Customer_dal,$BGcolor);
    $MailBody       = ob_get_clean();

    ob_start();
    email_TemplateBottomRow($company_id,$customer_id,$general_dal,$IMAGE_DAL,$Companies_dal,$Customer_dal,$BGcolor);
    $BottomRow  = ob_get_clean();

    ob_start();
    email_TemplateFooter($company_id,$customer_id,$general_dal,$IMAGE_DAL,$Companies_dal,$Customer_dal,$BGcolor);
    $Footer     = ob_get_clean();

    $html   = $Header;
    $html  .="<div style=\"margin: 0px auto 0px auto; width:600px;\">";
        $html  .=$TopRow;
        $html  .=$MailBody;
        $html  .=$BottomRow;
        $html  .=$Footer;
    $html  .="</div>";
return $html;
}

function email_TemplateHeader       ($company_id,$customer_id,$general_dal,$IMAGE_DAL,$Companies_dal,$Customer_dal,$BGcolor){
list($host,$domain)                     = setup_path_mailer();
ob_start();
include 'http/common_includes/colors_styles.php';
$css_styles = ob_get_clean();?>
<html>
    <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Subject</title>
    <?=$css_styles ?>
    </head>
<body>
<?
}
function email_TemplateFooter       ($company_id,$customer_id,$general_dal,$IMAGE_DAL,$Companies_dal,$Customer_dal,$BGcolor){
list($host,$domain)                     = setup_path_mailer();
$SERVER_ADDRESS                         = $Companies_dal->get_TemplateTabData_by_Name('SERVER_ADDRESS',$company_id);
$COMPANY_URL                            = $Companies_dal->get_TemplateTabData_by_Name('COMPANY_URL',$company_id);
$COMPANY_NAME                           = $Companies_dal->get_TemplateTabData_by_Name('COMPANY_NAME',$company_id);
$COMPANY_Facebook_Link                  = $Companies_dal->get_TemplateTabData_by_Name('COMPANY_Facebook_Link',$company_id);
$email_Template_Link_1                  = $Companies_dal->get_TemplateTabData_by_Name('email_Template_Link_1',$company_id);

$PHYSICAL_ADDRESS                       = $Companies_dal->get_TemplateTabData_by_Name('PHYSICAL_ADDRESS',$_SESSION['settings']['company_id']);
$Phone_Number_Main                      = $Companies_dal->get_TemplateTabData_by_Name('Phone_Number_Main',$_SESSION['settings']['company_id']);
$Phone_Number_2                         = $Companies_dal->get_TemplateTabData_by_Name('Phone_Number_2',$_SESSION['settings']['company_id']);

$email_Template_1_1                     = $IMAGE_DAL->get_Imagedata_byPreferenceName_AND_CompanyID($company_id,'email_Template_1_1');
$email_Template_1_2                     = $IMAGE_DAL->get_Imagedata_byPreferenceName_AND_CompanyID($company_id,'email_Template_1_2');

$customer_info                          = $Customer_dal->get_CustomerDataPerId($customer_id);
$customer_added                         = date("y/m/d",strtotime($customer_info[0]->added));
?>
<div class="main_bc_color1 main_color1_text" style="width:100%; float:left;">
    <div style="float:left; text-align:center; width:100%; height:100%; padding: 0; border-top: 1px solid #cac8c1; border-left: 1px solid #cac8c1; border-right: 1px solid #cac8c1;">
        <table width="100%" cellspacing="0" cellpadding="0" border="0" align="center" class="main_bc_color1 main_color1_text" style="font-size:10px;text-align:left;padding:05px;" >
            <tbody>
                <tr>
                    <td style="margin-top:05px;" >
                        <table cellspacing="0" cellpadding="0" border="0" align="center" style="max-width:600px;" >
                            <tbody >
                                <tr >
                                    <td style="font-family:'Lucida Sans Unicode', 'Lucida Grande', sans-serif;;font-size:12px;" >
                                        <table cellspacing="0" cellpadding="0" border="0" style="margin-bottom:5px;margin-top:5px;" >
                                            <tbody >
                                                <tr >
                                                    <td style="padding-right:10px;font-size:13px;font-family:'Lucida Sans Unicode', 'Lucida Grande', sans-serif;;" >
                                                        <img width="22" height="23" alt="You're receiving this email from our secure server at <?=$COMPANY_NAME[0]->value?>" src="http://<?=$host?>.<?=$domain?>/common_includes/includes/images/privacy_small.gif" >
                                                    </td>
                                                    <td valign="top" style="font-size:11px;font-family:'Lucida Sans Unicode', 'Lucida Grande', sans-serif;;" >
                                                        You are receiving this email from our secure server at
                                                        <a style="text-decoration:none;" href="http://<?=$SERVER_ADDRESS[0]->value?>" target="_blank" rel="nofollow" >
                                                            <span  class=""><?=$COMPANY_NAME[0]->value?></span>
                                                        </a>
                                                        because you signed up on <?=$customer_added?> from
                                                        <a style="text-decoration:none;" href="mailto:<?=$customer_info[0]->email_address?>" target="_blank" rel="nofollow" >
                                                            <span class=""><?=$customer_info[0]->email_address?></span>
                                                        </a> with the phone number <?=$customer_info[0]->phone_num?>.<br>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>

                                        <p style="font-size:11px;font-family:'Lucida Sans Unicode', 'Lucida Grande', sans-serif;;margin-bottom:5px;" >
                                            To stop receiving <?=$COMPANY_NAME[0]->value?> emails,
                                            <a style="text-decoration:none;" href="mailto:gloria@gtmassageandskincare.com?subject=REMOVEME" target="_blank" rel="nofollow" >
                                                <span  class="">unsubscribe</span>
                                            </a>.
                                        </p>

                                        <p style="font-size:11px;font-family:'Lucida Sans Unicode', 'Lucida Grande', sans-serif;;margin-bottom:5px;" >
                                            Find this email in your spam or junk folder? Click the "Not Spam" button at the top of this email. <br>
                                            To make sure this email is not sent to your "junk/bulk" folder, select "Add/save to Address Book" in your email browser and follow the appropriate instructions.
                                        </p>

                                        <table width="100%" cellspacing="0" cellpadding="0" border="0" >
                                            <tbody >
                                                <tr >
                                                    <td valign="bottom" >
                                                      <p style="font-size:11px;font-family:'Lucida Sans Unicode', 'Lucida Grande', sans-serif;;margin:0;margin-bottom:5px;" >&copy; <?=date("Y")?>
                                                            <a style="text-decoration:none;" href="http://<?=$COMPANY_URL[0]->value?>" target="_blank" rel="nofollow">
                                                                <span class=""><?=$COMPANY_NAME[0]->value?></span></a> |
                                                            <a style="text-decoration:none;" href="http://<?=$COMPANY_URL[0]->value?>" target="_blank" rel="nofollow" >
                                                                <span class="">Privacy</span></a> |
                                                            <a style="text-decoration:none;" href="http://<?=$COMPANY_URL[0]->value?>" target="_blank" rel="nofollow" >
                                                                <span class="">Terms of Use</span></a> |
                                                            <a style="text-decoration:none;" href="http://<?=$COMPANY_URL[0]->value?>" target="_blank" rel="nofollow" >
                                                                <span class="">Affiliates</span></a> |
                                                            <a style="text-decoration:none;" href="http://<?=$COMPANY_URL[0]->value?>" target="_blank" rel="nofollow" >
                                                                <span class="">Contact</span></a> |
                                                            <a style="text-decoration:none;" href="http://<?=$COMPANY_URL[0]->value?>" target="_blank" rel="nofollow" >
                                                                <span class="">Employers</span></a> |
                                                            <a style="text-decoration:none;" href="http://<?=$COMPANY_URL[0]->value?>" target="_blank" rel="nofollow" >
                                                                <span class="">Site Map</span></a>
                                                            <br>
                                                            <a style="text-decoration:none;" href="http://<?=$COMPANY_URL[0]->value?>" target="_blank" rel="nofollow">
                                                                <?=$COMPANY_NAME[0]->value?></a>, Inc. | <?=$PHYSICAL_ADDRESS[0]->value?>
                                                      </p>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
</body>
<?}
function email_TemplateFooter_2     ($company_id,$customer_id,$general_dal,$IMAGE_DAL,$Companies_dal,$Customer_dal,$BGcolor){
list($host,$domain)                     = setup_path_mailer();
$SERVER_ADDRESS                         = $Companies_dal->get_TemplateTabData_by_Name('SERVER_ADDRESS',$company_id);
$COMPANY_URL                            = $Companies_dal->get_TemplateTabData_by_Name('COMPANY_URL',$company_id);
$COMPANY_NAME                           = $Companies_dal->get_TemplateTabData_by_Name('COMPANY_NAME',$company_id);
$COMPANY_Facebook_Link                  = $Companies_dal->get_TemplateTabData_by_Name('COMPANY_Facebook_Link',$company_id);
$email_Template_Link_1                  = $Companies_dal->get_TemplateTabData_by_Name('email_Template_Link_1',$company_id);
$PHYSICAL_ADDRESS                       = $Companies_dal->get_TemplateTabData_by_Name('PHYSICAL_ADDRESS',$_SESSION['settings']['company_id']);
$Phone_Number_Main                      = $Companies_dal->get_TemplateTabData_by_Name('Phone_Number_Main',$_SESSION['settings']['company_id']);
$Phone_Number_2                         = $Companies_dal->get_TemplateTabData_by_Name('Phone_Number_2',$_SESSION['settings']['company_id']);
?>
<div class="main_bc_color1 main_color1_text" style="width:100%; float:left; font-family: Arial; ">
    <tr>
        <td style="text-align:left;border-top-color:rgb(51,51,102);border-top-width:10px;border-top-style:solid;background-color:rgb(51,51,102);" valign="top" colspan="3">
            <span style="text-align:left;color:rgb(153,102,0);line-height:100%;font-family:verdana;font-size:10px;">
                <span>
                    If you do not want to receive future e-mails from us, click <a href="mailto:gloria@gtmassageandskincare.com?subject=REMOVEME" target="_blank" rel="nofollow">Unsubscribe</a> Thank You.
                </span>
                <br>
                <span>
                    <a style="text-decoration:none; color:white;" href="http://<?=$COMPANY_URL[0]->value?>" target="_blank" rel="nofollow"><?=$COMPANY_NAME[0]->value?>, Inc.</a> | <?=$PHYSICAL_ADDRESS[0]->value?>
                </span>
            </span>
        </td>
    </tr>
</div>
<?}
function email_TemplateFooter_3     ($company_id,$customer_id,$general_dal,$IMAGE_DAL,$Companies_dal,$Customer_dal,$BGcolor){
list($host,$domain)                     = setup_path_mailer();
$SERVER_ADDRESS                         = $Companies_dal->get_TemplateTabData_by_Name('SERVER_ADDRESS',$company_id);
$COMPANY_URL                            = $Companies_dal->get_TemplateTabData_by_Name('COMPANY_URL',$company_id);
$COMPANY_NAME                           = $Companies_dal->get_TemplateTabData_by_Name('COMPANY_NAME',$company_id);
$COMPANY_Facebook_Link                  = $Companies_dal->get_TemplateTabData_by_Name('COMPANY_Facebook_Link',$company_id);
$email_Template_Link_1                  = $Companies_dal->get_TemplateTabData_by_Name('email_Template_Link_1',$company_id);
?>
<div class="main_bc_color1 main_color1_text" style="width:100%; height: 35px; float:left; font-family: Arial;  ">
<div style="text-align:center; width:100%; height:100%;">
    <table width="100%" height="100%" border="0" cellspacing="0"  background="">
        <tr>
            <td colspan="3" style="height:32px; padding: 0; border-top: 1px solid #cac8c1; border-left: 1px solid #cac8c1; border-right: 1px solid #cac8c1;">
                <div style="height:32px; padding: 0; border-top: 1px solid #fff; border-left: 1px solid #fff; border-right: 1px solid #fff; overflow: hidden;">
                <p style="float: right; font-size: 8pt; margin:0; padding: 10px; color:#fff;">Copyright &copy; <?=date("Y")?> <a style="" href="http://<?=$COMPANY_URL[0]->value?>"><?=$COMPANY_NAME[0]->value?></a>. All rights reserved.</p>
                </div>
            </td>
        </tr>
    </table>
</div>
</div>
<?}
function email_Template_TR_Spacer   ($height,$columns){
            ?>
            <tr>
                <td colspan="<?=$columns?>" height="<?=$height?>">
                    &nbsp;
                </td>
            </tr>
            <?}
?>