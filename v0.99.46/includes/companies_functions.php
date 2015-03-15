<?php
include_once('general_functions.php');
class Companies_DAL { 
  public function __construct(){}
  public function get_AllCompanies($null,$totals){
    if ($totals == 0) {
        $sql = "SELECT  c.id, c.name, c.domain, c.subdomain, c.db_version, c.defaultPOS, c.status, c.added ";
    }
    ELSE {
        $sql = "SELECT count(distinct(c.id)) as count ";
    }

    $sql.= " from companies c
            left join preferences p on p.company_id = c.id
            where c.id is not NULL
            ";
    if ( isset($_SESSION['search_data']['Companies_AllCompanies']['company_search_name'])                       && $_SESSION['search_data']['Companies_AllCompanies']['company_search_name'] != -1 )                {$sql .= " and c.name           like '%" . $_SESSION['search_data']['Companies_AllCompanies']['company_search_name'] . "%' "; }
    if ( isset($_SESSION['search_data']['Companies_AllCompanies']['company_search_domain'])                     && $_SESSION['search_data']['Companies_AllCompanies']['company_search_domain'] != -1 )              {$sql .= " and c.domain         like '%" . $_SESSION['search_data']['Companies_AllCompanies']['company_search_domain'] . "%' "; }
    if ( isset($_SESSION['search_data']['Companies_AllCompanies']['company_search_subdomain'])                  && $_SESSION['search_data']['Companies_AllCompanies']['company_search_subdomain'] != -1 )           {$sql .= " and c.subdomain      like '%" . $_SESSION['search_data']['Companies_AllCompanies']['company_search_subdomain'] . "%' "; }
    if ( isset($_SESSION['search_data']['Companies_AllCompanies']['company_search_master_email'])               && $_SESSION['search_data']['Companies_AllCompanies']['company_search_master_email'] != -1 )        {$sql .= " and ( p.value        like '%" . $_SESSION['search_data']['Companies_AllCompanies']['company_search_master_email'] . "%' and p.name = 'master_email' ) "; }

    if ($totals == 0) {
        $sql .= " group by c.id " ;
        if (!(isset($_SESSION['search_data']['column']))) { $sql.= " order by c.id desc"; }
        else { $sql.= " order by " . $_SESSION['search_data']['column'] . " " . $_SESSION['search_data']['asc_desc'] . " "; }

        if ( isset($_SESSION['search_data']['paging_page']) && $_SESSION['search_data']['paging_page'] != 0) {
            if ( $_SESSION['search_data']['paging_page'] == 1) { $limit_offset = 0; }
            else { $limit_offset = ( ($_SESSION['search_data']['paging_page'] - 1) * 10 ) ; }
            $sql .= " limit $limit_offset,10";
        }
    }
    #print $sql . "<br>\n";
    return $this->query($sql);
  }
  public function get_default_Company_ImageID($company_id){
      $sql = "SELECT image_id, image_db_id
                from item_image_mappings
                where id = $company_id and image_type_id = 7 and 
                `default` = 1 and
                deleted is null
                order by default_item_image desc, default_group_image desc, added asc
                limit 1";
    #print "$sql";
    return $this->query($sql);
  }
  public function get_company_Settings($company_id,$setting_name){
    $sql = "SELECT
            $setting_name as value
            from companies
            where id = $company_id ";
    #echo $sql;
    return $this->query($sql);
  }
  public function get_company_appointmentsCount($company_id){
      $sql = "SELECT count(id) as count from appointments where company_id = $company_id and status =0 ;";
    #echo $sql;
    return $this->query($sql);
  }
  public function get_company_salesCount($company_id){
      $sql = "SELECT count(id) as count from sales where company_id = $company_id and deleted is NULL;";
    #echo $sql;
    return $this->query($sql);
  }
  public function get_company_LastAppointment($company_id){
      $sql = "SELECT convert_tz(insert_date, 'utc', ".quoteSmart($_SESSION['preferences']['timezone']).") as last_appointment
            from appointments where company_id = $company_id and status =0 order by insert_date desc limit 1 ;";
    return $this->query($sql);
  }
  public function get_company_LastSale($company_id){
      $sql = "SELECT convert_tz(added, 'utc', ".quoteSmart($_SESSION['preferences']['timezone']).") as last_sale
            from sales where company_id = $company_id and deleted is NULL order by added desc limit 1 ;";
    #echo $sql;
    return $this->query($sql);
  }


  public function Companies_Check_existingCompany($company_id,$companyName){
    $sql = "SELECT id
            from companies
            where name = '$companyName'";
    #echo $sql;
    return $this->query($sql);
  }
  public function Companies_Check_existingDomainName($company_id,$DomainName){
    $sql = "SELECT id
            from companies
            where domain = '$DomainName'";
    #echo $sql;
    return $this->query($sql);
  }
  public function Companies_Check_existingSubDomain($company_id,$SubDomain){
    $sql = "SELECT id
            from companies
            where subdomain = '$SubDomain'";
    #echo $sql;
    return $this->query($sql);
  }
  public function Companies_get_templateTabData_byCompany_ID_n_templateTabId($templateTabId,$company_id){
    $sql = "SELECT ttd.id,ttd.value
            from templateTabsData ttd 
            where ttd.templateTabID =$templateTabId and ttd.company_id=$company_id";
    #echo $sql . "<br>\n";
    return $this->query($sql);
  }
  public function Companies_get_templateTabDefaultData_by_templateTabId($templateTabId){
    $sql = "SELECT default_value from templateTabs where Id = $templateTabId";
    #echo $sql . "<br>\n";
    return $this->query($sql);
  }
  public function Companies_GetHTTPtemplateTypes(){
    $sql = "SELECT id,templateName from HTTPtemplateTypes";
    #echo $sql;
    return $this->query($sql);
  }

  public function Companies_existing_templateTabGroupName($templateTabGroupName){
    $sql = "SELECT id from templateTabs where name = '$templateTabGroupName';";
    #print "$sql";
    return $this->query($sql);
  }

  public function get_TemplatesFromTemplateMaster($company_id){
    $sql = "SELECT id, default_name from templateMaster";
    #echo $sql;
    return $this->query($sql);
  }
  public function get_TemplateTabIDs_by_GroupID($TemplateTabGroupID){
    $sql = "SELECT id,name,dataType,dataGroup from templateTabs where dataGroup = $TemplateTabGroupID ";
    #echo $sql;
    return $this->query($sql);
  }
  public function get_TemplateTabGroups($templateId){
    $sql = "SELECT ttg.id as TemplateTabGroupID, ttg.templateTabGroupName,tm.default_name
            from templateTabsGroups ttg
            join templateMaster tm on tm.id = ttg.templateId
            where ttg.templateId = $templateId";
    #echo $sql;
    return $this->query($sql);
  }
  public function get_TemplateTabGroupInfo_by_GroupID($TemplateTabGroupID){
    $sql = "SELECT templateTabGroupName from templateTabsGroups where id = $TemplateTabGroupID";
    #echo $sql;
    return $this->query($sql);
  }
  public function get_TemplateTabData_by_Name($template_name,$company_id){
      $sql = "SELECT ttd.value
                from templateTabs tt
                join templateTabsData ttd on ttd.templateTabId = tt.id
                where tt.name = '$template_name' and
                ttd.company_id = $company_id;";
    #echo $sql."<br>";
    return $this->query($sql);
  }
  public function get_ImageType_nameNid_by_TemplateMasterID($templateMaster_ID){
      $sql = "SELECT id,type_name from image_types where templateMaster_ID = $templateMaster_ID order by type_name asc";
    #echo $sql;
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

function companies() {
?>
<div class="ReportsTopRow main_bc_color2 main_color2_text"><a href="#" title="Item Search" onclick="mainDiv('companies'); return false;">Companies Main</a></div>
    <div style="max-height: 1000px;" class="f_left wp100 hp94">
        <div class="leftSpace main_bc_color2 main_color2_text">&nbsp;</div>
        <div class="middleSpace wp96">
            <div class="f_left hp10 wp100">
                <div class="f_left hp100 wp35 left vtop no-overflow">
                        <img alt="" class="hp90" src="/common_includes/includes/images/icon_profiles_50.jpg">
                    Companies Profiles
                </div>
                <div class="f_right hp100 wp50 right">&nbsp;
                    <? if (!isset($_SESSION['edit_companies']['UserAdd']) && $_SESSION['settings'][$_SESSION['settings']['login_id']]['level'] >= 1) { ?>
                    <a onclick="Company_AddCompany()" href="javascript: none();">
                        <img alt="" class="hp90" src="/common_includes/includes/images/group-user-add.png" style="border-style: none">
                    </a>
                    <? } ?>
                </div>
            </div>
            <?
            if (!isset($_SESSION['edit_companies']['company_id']) && !isset($_SESSION['edit_companies']['CompanyAdd'])) { ?>
            <div class="d_InlineBlock wp100 hp90">
                <div class="wp100 hp100" >
                    <div class="f_left wp15 hp100">
                        <div class="d_InlineBlock wp100 hp100" >
                            <?CompanySearchStanza();?>
                        </div>
                    </div>
                    <div class="f_right wp85 hp100">
                        <div class="d_InlineBlock wp100 hp100" id="Companies_AllCompaniesBodyCenter">
                            <?CompaniesStanza();?>
                        </div>
                    </div>
                </div>
            </div>
            <?}
            elseif (isset($_SESSION['edit_companies']['CompanyAdd'])) {
                CompanyAddStanza();
            }
            else{
                editCompaniesStanza($_SESSION['edit_companies']['company_id']);
            }
            ?> 
        </div>
        <div class="rightSpace main_bc_color2 main_color2_text">&nbsp;</div>
    </div>
<div class="ReportsBottomRow main_bc_color2 main_color2_text">&nbsp;</div>
<?
} 

function CompaniesStanza() {
?>
        <div class="wp100 hp07" id="listing_search_paging_top">
            <? showPaging(); ?>
        </div>
        <div class="d_InlineBlock wp100 hp85">
            <? companiesHeader(); ?>
            <? companiesAllCompanies(); ?>
        </div>
        <div class="wp100 hp07" id="listing_search_paging_bottom">
            <? showPaging(); ?>
        </div>
<?
}
  function companiesHeader() {
?>
    <div class="f_left wp100 h25px s08 HEADER main_bc_color1 main_color1_text">
        <div class="f_left report_header_cell_wp04"><a onclick="orderBy('id','Companies_AllCompanies'); return false;">ID#</a></div>
        <div class="f_left report_header_cell_wp25"><a onclick="orderBy('name','Companies_AllCompanies'); return false;">Company Name</a></div>
        <div class="f_left report_header_cell_wp12"><a onclick="orderBy('subdomain','Companies_AllCompanies'); return false;">Sub-Domain</a></div>
        <div class="f_left report_header_cell_wp21"><a onclick="orderBy('defaultPOS','Companies_AllCompanies'); return false;">Website ?</a></div>
        <div class="f_left report_header_cell_wp06">Appts</a></div>
        <div class="f_left report_header_cell_wp06">Sales</a></div>
        <div class="f_left report_header_cell_wp10"><a onclick="orderBy('added','Companies_AllCompanies'); return false;">Added</a></div>
        <div class="f_left report_header_cell_wp07"><a onclick="orderBy('status','Companies_AllCompanies'); return false;">Status</a></div>
        <div class="f_left report_header_cell_wp06">Edit</div>
    </div>
<?
}
  function companiesAllCompanies() {
$companies_dal = new Companies_DAL();
$companies = $companies_dal->get_AllCompanies(0,0);
    $altClass = "bctr1a";
    if (count($companies) >0 ) {
        foreach($companies as $company){
            $appointments_data  = $companies_dal->get_company_appointmentsCount($company->id);
            $sales_data         = $companies_dal->get_company_salesCount($company->id);
            $last_appointment   = $companies_dal->get_company_LastAppointment($company->id);
            $last_sale          = $companies_dal->get_company_LastSale($company->id);

            if (count($last_appointment) > 0) {
                    $last_appointment_date  = date('Y-m-d',  strtotime($last_appointment[0]->last_appointment));
                    $appointments_count     = $appointments_data[0]->count; }
                else {
                    $last_appointment_date  = '';
                    $appointments_count     = 0; }
            if (count($last_sale) > 0) {
                    $last_sale_date         = date('Y-m-d',  strtotime($last_sale[0]->last_sale));
                    $sales_count            = $sales_data[0]->count; }
                else {
                    $last_sale_date         = '';
                    $sales_count            = 0; }

            if     ($company->status == 0) {   $status_action = "INactive"; $status_class = "red";   $action = 1; $alt="Activate?";}
            elseif ($company->status == 1) {   $status_action = "Active"  ; $status_class = "green"; $action = 0; $alt="DeActivate Login?";}

            if     ($company->defaultPOS == 0) {   $defaultPOS_msg = "<a href=\"http://".$company->subdomain.".".$_SESSION['settings']['domain']."\" target=\"_blank\">".$company->domain."</a>"  ; $defaultPOS_class = "green";}
            elseif ($company->defaultPOS == 1) {   $defaultPOS_msg = "<a href=\"http://".$company->subdomain.".".$_SESSION['settings']['domain']."/pos\" target=\"_blank\">POS ONLY!</a>"  ; $defaultPOS_class = "";}
            ?>
                <div class="f_left wp100 s07 lh20 <?=$altClass?>">
                    <div class="report_data_cell_wp04">&nbsp;<?=$company->id?></div>
                    <div class="report_data_cell_wp25">&nbsp;<?=$company->name?></div>
                    <div class="report_data_cell_wp12">&nbsp;<?=$company->subdomain?></div>
                    <div class="report_data_cell_wp21 no-overflow <?=$defaultPOS_class?>">&nbsp;<?=$defaultPOS_msg?></div>
                    <div class="report_data_cell_wp06" title="Last Appt:<?=$last_appointment_date?>">   &nbsp;<?=$appointments_count?></div>
                    <div class="report_data_cell_wp06" title="Last Sale:<?=$last_sale_date?>">          &nbsp;<?=$sales_count?></div>
                    <div class="report_data_cell_wp10 no-overflow" title="<?=$company->added?>">&nbsp;<?=date('Y-m-d',  strtotime($company->added));?></div>
                    <div class="report_data_cell_wp07" title="<?=$alt?>">
                        <input alt="<?=$alt?>" onclick="editCompany_UpdStatus(<?=$company->id?>,<?=$action?>)" type="submit" value="<?=$status_action?>" class="button s07 <?=$status_class?>">
                    </div>
                    <div class="report_data_cell_wp06">
                        <input onclick="Company_editCompany(<?=$company->id?>)" type="submit" value="EDIT" class="button s07 ">
                    </div>
                </div>
            <?
            if ($altClass == "bctr1a") { $altClass = "bctr1b"; } else {$altClass = "bctr1a";}
        }
     }
     else { ?>
            <div class=" wp100 center">There are no companies currently in the database.</div>
     <? }
} 

function editCompaniesStanza($company_id) {
$companies_dal = new Companies_DAL();
?>
<div class="d_InlineBlock wp95 hp90">
    <?=companies_editCompaniesTop($companies_dal,$company_id); ?>
    <?=companies_editCompaniesTabs($companies_dal,$company_id); ?>
    <?
    if (isset($_SESSION['edit_companies']['ActiveTab']) && $_SESSION['edit_companies']['ActiveTab'] == "CustomerElectronicInfo"){
        companiesElectronicInfo($companies_dal,$company_id);
    }

    if (isset($_SESSION['edit_companies']['ActiveTab']) && $_SESSION['edit_companies']['ActiveTab'] == "CustomerPhysicalAddress"){
        companiesPhysicalAddress($companies_dal,$company_id);
    }

    if (isset($_SESSION['edit_companies']['ActiveTab']) && $_SESSION['edit_companies']['ActiveTab'] == "CompaniesTemplateTabs") {
        CompaniesTemplateTabs($companies_dal,$company_id);
    }

    if (isset($_SESSION['edit_companies']['ActiveTab']) && $_SESSION['edit_companies']['ActiveTab'] == "CompaniesSettings" || !isset($_SESSION['edit_companies']['ActiveTab']) ) {
        CompaniesSettings($companies_dal,$company_id);
    }
    ?>
</div>
<?
}
  function companies_editCompaniesTop($companies_dal,$company_id) {
        $image_id_data      = array ();
        $image_id_data      = $companies_dal->get_default_Company_ImageID($company_id);
        $companies_data     = $companies_dal->get_company_Settings($company_id,'name');
        $IMAGE_DAL          = new IMAGE_DATA_DAL();
        $main_company_logo  = $IMAGE_DAL->get_Imagedata_byPreferenceName_AND_CompanyID($company_id,'main_company_logo');
?>
    <div class="bctrt wp100 d_InlineBlock">
        <div class="f_left wp25 h100">
            <img class='wp100 m0 b0 <? if ($main_company_logo[0]->image_id > 0) { print ' mp'; } ?>'  height="50"  width="85" src='/pos/showimage.php?id=<?=$main_company_logo[0]->image_id?>&image_db_id=<?=$main_company_logo[0]->image_db_id?>'  <? if ($main_company_logo[0]->image_id > 0) { ?> <? } ?>  />
        </div>
        <div class="f_left wp50 h100 s19">
            &nbsp;<?=$companies_data[0]->value?>
        </div>
        <div class="f_right wp25 h100" >
            &nbsp;
        </div>
    </div>
<?
}
  function companies_editCompaniesTabs($companies_dal,$company_id){
$activeTabBackground = "bctrt";

if (isset($_SESSION['edit_companies']['ActiveTab']) && $_SESSION['edit_companies']['ActiveTab'] == "CustomerElectronicInfo"){
    $CustomerElectronicInfoBackground = 'bctrt';
} else { $CustomerElectronicInfoBackground = ''; }

if (isset($_SESSION['edit_companies']['ActiveTab']) && $_SESSION['edit_companies']['ActiveTab'] == "CustomerPhysicalAddress"){
    $CustomerPhysicalAddressBackground = 'bctrt';
} else { $CustomerPhysicalAddressBackground = ''; }

if (isset($_SESSION['edit_companies']['ActiveTab']) &&  $_SESSION['edit_companies']['ActiveTab'] == "CompaniesTemplateTabs" ) {
    $CompaniesTemplateTabsBackground = 'bctrt';
} else { $CompaniesTemplateTabsBackground = ''; }

if (isset($_SESSION['edit_companies']['ActiveTab']) &&  $_SESSION['edit_companies']['ActiveTab'] == "CompaniesSettings" ||  !isset($_SESSION['edit_companies']['ActiveTab']) ) {
    $CompaniesSettingsBackground = 'bctrt';
} else { $CompaniesSettingsBackground = ''; }

?>
    <div class="wp100 f_left ">
        <div onclick="Companies_ActiveLoginTabs('CompaniesSettings');"          class="f_left s08 wp20 <?=$CompaniesSettingsBackground?>" >High Level Settings</div>
        <div onclick="Companies_ActiveLoginTabs('CompaniesElectronicInfo');"    class="f_left s08 wp20 <?=$CustomerElectronicInfoBackground?>" >Electronic Info</div>
        <div onclick="Companies_ActiveLoginTabs('CustomerPhysicalAddress');"    class="f_left s08 wp20 <?=$CustomerPhysicalAddressBackground?>" >Physical Address</div>
        <div onclick="Companies_ActiveLoginTabs('CompaniesTemplateTabs');"      class="f_left s08 wp20 <?=$CompaniesTemplateTabsBackground?>" >Template Tabs</div>
    </div>
<?
}

  function CompaniesSettings($companies_dal,$company_id){
    #$company_data = $companies_dal->get_CustomerDataPerId($company_id);
?>
    <div class="wp100 d_InlineBlock ">
        <div class="f_left wp100 bctrt center">Company Settings</div>
    </div>
    <div class="box5">
        <? edit_Settings($companies_dal,'name',$company_id); ?>
        <? edit_Settings($companies_dal,'domain',$company_id); ?>
        <? edit_Settings($companies_dal,'subdomain',$company_id); ?>
        <? edit_Settings($companies_dal,'templateType',$company_id); ?>
        <? edit_Settings($companies_dal,'templateNumber',$company_id); ?>
        <? edit_Settings($companies_dal,'status',$company_id); ?>
        <? edit_Settings($companies_dal,'defaultPOS',$company_id); ?>
    </div>
    <div class="wp100 d_InlineBlock h02px">
        <div class="f_left wp100 " >&nbsp;</div>
    </div>
    <?
        //$general_dal = new GENERAL_DAL();
        //upload_file_stanza('company',$general_dal,$company_id);
    ?>

<?}
    function edit_Settings($companies_dal,$setting_name,$company_id){
    $Settings_data = $companies_dal->get_company_Settings($company_id,$setting_name);
    ?>
    <div class="wp100 d_InlineBlock bctrt ">
        <div class="f_left  wp25 right no-overflow">&nbsp;<?=$setting_name?></div>
        <div class="f_left  wp65 left bclightgray textIndent15">&nbsp;
            <?   if ($setting_name == "defaultPOS" ) {
                $selected =  ($Settings_data[0]->value == 0 || !$Settings_data[0]->value ) ? "selected" : "" ;
                ?>
                <select id="<?=$setting_name?>" class="w50">
                    <option <?=$selected?> value="1">Yes</option>
                    <option <?=$selected?> value="0">No</option>
                </select>

            <?   } elseif ($setting_name == "status") {
                $selected =  ($Settings_data[0]->value == 0 || !$Settings_data[0]->value ) ? "selected" : "" ;
                ?>
                <select id="<?=$setting_name?>" class="w75">
                    <option <?=$selected?> value="1">Active</option>
                    <option <?=$selected?> value="0">Inactive</option>
                </select>

            <?   } elseif ($setting_name == "templateType") {
                ?>
                <select id="<?=$setting_name?>" class="w125">
                    <?
                    $templateTypes = $companies_dal->Companies_GetHTTPtemplateTypes($_SESSION['settings']['company_id']);
                    foreach ($templateTypes as $templateType) {
                    $selected =  ($Settings_data[0]->value == $templateType->id || !$Settings_data[0]->value ) ? "selected" : "" ;
                    ?>
                    <option <?=$selected?> value="<?=$templateType->id?>"><?=$templateType->templateName?></option>
                    <? } ?>
                </select>

            <?   } elseif ($setting_name == "templateNumber") {
                $selected =  ($Settings_data[0]->value == 0 || !$Settings_data[0]->value ) ? "selected" : "" ;
                ?>
                <select id="<?=$setting_name?>" class="w125">
                    <?
                    $templateNumbers = array(1,2,3,4,5,6,7);
                    foreach ($templateNumbers as $templateNumber) {
                    $selected =  ($Settings_data[0]->value == $templateNumber || !$Settings_data[0]->value ) ? "selected" : "" ;
                    ?>
                    <option <?=$selected?> value="<?=$templateNumber?>"><?=$templateNumber?></option>
                    <? } ?>
                </select>

            <? } elseif (
                    $setting_name == "name" ||
                    $setting_name == "domain" ||
                    $setting_name == "subdomain"
            ) { ?>
                <input value="<?=$Settings_data[0]->value?>" id="<?=$setting_name?>" class="w200" type="<text">
            <? } else { ?>
                Data type <?=$Settings_data[0]->type?> is not yet configured.
            <? } ?>

        </div>
        <div class="f_left wp10">
            <input type="submit" class="button" value="Update" onclick="companies_UpdSettings(<?=$company_id?>,'<?=$setting_name?>')">
        </div>
    </div>
    <?
    }

  function CompaniesTemplateTabs($companies_dal,$company_id){
$general_dal = new GENERAL_DAL();
?>
    <div class="wp100 f_left">
        <!-- ######################### LEFT  LEFT  LEFT ######################### -->
        <div class="wp15 f_left">
                <div class="wp100 f_left">
                    <div class="wp98 bctrt d_InlineBlock">Tabs</div>
                </div>
            <?
            $templateTabs = $companies_dal->get_TemplatesFromTemplateMaster($company_id);
            foreach ($templateTabs as $templateTab) {?>
                <div class="wp100 f_left ">
                    <div class="wp100 mp">
                        <div class=box2>
                        <input type="button" onclick="Companies_showTemplateTab_byID(<?=$templateTab->id?>,<?=$company_id?>)" value="<?=$templateTab->default_name?>" class="s06 button s06 ">
                        </div>
                    </div>
                </div>
            <? } ?>
        </div>
        <!-- ######################### RIGHT  RIGHT  RIGHT ######################### -->
        <div id="Companies_showTemplateTab_byID" class="wp85 f_right">
            <?
            if   ( !isset($_SESSION['CompanyTemplates']['templateId'])) { $templateId = 1; }
            else { $templateId = $_SESSION['CompanyTemplates']['templateId']; }
                Companies_showTemplateTab_byID($companies_dal,$company_id,$templateId);
            ?>
        </div>
    </div>
    <?}
  function Companies_showTemplateTab_byID($companies_dal,$company_id,$templateId){
        $general_dal    = new GENERAL_DAL();
        $TemplateGroups = $companies_dal->get_TemplateTabGroups($templateId);
        ?>
            <div class="wp100 d_InlineBlock ">
                <div class="f_left wp100 bctrt center"><?=$TemplateGroups[0]->default_name; ?> Settings</div>
            </div>
            <div class="wp100 h320px scrolling" id ="TemplateTabsData">
                <?
                foreach ($TemplateGroups as $TemplateGroup) {?>
                    <div id="edit_TemplateTabGroupData_<?=$TemplateGroup->TemplateTabGroupID?>" class="mb20 wp98">
                    <? edit_TemplateTabGroupData($companies_dal,$company_id,$TemplateGroup->TemplateTabGroupID); ?>
                    </div>
                <?}?>

                    <div class="wp98 d_InlineBlock "><div class="f_left wp100 center">&nbsp;</div></div>
                    <div class="wp98 d_InlineBlock "><div class="f_left wp100 center">&nbsp;</div></div>
                    <?
                    $type_names = $companies_dal->get_ImageType_nameNid_by_TemplateMasterID($templateId);
                    foreach ($type_names as $type_name) {?>
                    <div class="wp98 d_InlineBlock "><div class="f_left wp100 center"><?upload_file_stanza($type_name->type_name,$general_dal,$company_id,'Companies_showTemplateTab_byID');?></div></div>
                    <div class="wp98 d_InlineBlock "><div class="f_left wp100 center">&nbsp;</div></div>
                    <? } ?>
            </div>
    <?}
    function edit_TemplateTabGroupData($companies_dal,$company_id,$TemplateTabGroupID){
        $TemplateGroupInfo = $companies_dal->get_TemplateTabGroupInfo_by_GroupID($TemplateTabGroupID);
        ?>
            <div class="wp100 d_InlineBlock bctrt ">
                <div class="f_left bold pl10 left wp100 no-overflow">&nbsp;<?=$TemplateGroupInfo[0]->templateTabGroupName?> Group</div>
            </div>
            <div class="d_InlineBlock box5">
                <?$Settings_data = $companies_dal->get_TemplateTabIDs_by_GroupID($TemplateTabGroupID);
                foreach ($Settings_data as $Setting) {
                    edit_TemplateTabData($companies_dal,$Setting->id,$Setting->name,$Setting->dataType,$Setting->dataGroup,$company_id,'wp90','no_keep');
                }
                ##  Only allow adding stuff if its company_id = 0
                if ( $company_id == 0) { Companies_AddTemplateData($TemplateTabGroupID,$company_id); }
                ?>
            </div>
        <?}
    function edit_TemplateTabData($companies_dal,$TemplateTabId,$templateTabName,$templateTabType,$TemplateTabGroupID,$company_id,$textarea_dimensions,$keep_or_not){
        $textarea_dimensions    = 'wp90';
        $data                   = $companies_dal->Companies_get_templateTabData_byCompany_ID_n_templateTabId($TemplateTabId,$company_id);
        if  (  count($data) > 0 ) { $TemplateDataID = $data[0]->id; $value = $data[0]->value; $placeholder = '';}
        else { $TemplateDataID = 0; $value = '';
                $DefaultsData = $companies_dal->Companies_get_templateTabDefaultData_by_templateTabId($TemplateTabId);
                $placeholder = $DefaultsData[0]->default_value;
        }
        ?>
        <div class="wp100 d_InlineBlock bctrt ">
            <div class="f_left  s07 wp35 right no-overflow" title="<?=$templateTabName?>">&nbsp;<?=$templateTabName?></div>
            <div class="f_left  wp35 left bclightgray textIndent15">&nbsp;
            <?   if         ( $templateTabType == "text" ) { ?>
                    <input type="hidden" id="dynamic_pannel_css_<?=$TemplateTabId?>" value="<?=$textarea_dimensions?>">
                    <input type="hidden" id="dynamic_pannel_keep_<?=$keep_or_not?>" value="<?=$textarea_dimensions?>">
                    <input value="<?=$value?>" id="dynamic_pannel_<?=$TemplateTabId?>" class="<?=$textarea_dimensions?>" type="<?=$templateTabType?>" placeholder="<?=$placeholder?>" >
                <? } elseif ( $templateTabType == "textarea" ) { ?>
                    <input type="hidden" id="dynamic_pannel_css_<?=$TemplateTabId?>" value="<?=$textarea_dimensions?>">
                    <input type="hidden" id="dynamic_pannel_keep_<?=$keep_or_not?>" value="<?=$textarea_dimensions?>">
                    <textarea id="dynamic_pannel_<?=$TemplateTabId?>" class="wp89 h150px text f_right mr3" placeholder="<?=$placeholder?>" ><?=$value?></textarea>
                <? } elseif ( $templateTabType == "boolean") {
                    $selected =  ($value == 0 || !$value ) ? "selected" : "" ;
                ?>
                    <input type="hidden" id="dynamic_pannel_css_<?=$TemplateTabId?>" value="<?=$textarea_dimensions?>">
                    <input type="hidden" id="dynamic_pannel_keep_<?=$keep_or_not?>" value="<?=$textarea_dimensions?>">
                    <select id="dynamic_pannel_<?=$TemplateTabId?>" class="<?=$preferences_data[0]->options?>">
                        <option <?=$selected?> value="1">Yes</option>
                        <option <?=$selected?> value="0">No</option>
                    </select>
            <? } else { ?>
                    Template Tab Type '<?=$data[0]->type?>' is not yet configured.
                <? } ?>
            </div>
          <div class="f_left wp20 s06 center red no-overflow" id="dynamic_pannel_<?=$TemplateTabId?>_error">&nbsp;</div>
            <div class="f_left wp10">
                <input type="submit" class="button" value="Update" onclick="Companies_UpdTemplateData_byID(<?=$TemplateDataID?>,<?=$TemplateTabId?>,<?=$TemplateTabGroupID?>,<?=$company_id?>)">
            </div>
        </div>
<?
}
    function Companies_AddTemplateData($TemplateTabGroupID,$company_id){
        $textarea_dimensions    = '';
        $keep_or_not            = "no_keep";
        ?>
        <div class="wp100 d_InlineBlock bctrt ">
            <div class="f_left  s07 wp35 right no-overflow green bold">&nbsp;Add a new Dynamic Variable to the Group</div>
            <div class="f_left wp35 d_InlineBlock">
                <div class="f_left wp100 left bclightgray textIndent15">Name;
                        <input type="hidden"    id="dynamic_pannel_css_name_<?=$TemplateTabGroupID?>"           value="<?=$textarea_dimensions?>">
                        <input type="hidden"    id="dynamic_pannel_keep_name_<?=$TemplateTabGroupID?>"          value="<?=$keep_or_not?>">
                        <input type="text"      id="dynamic_pannel_name_<?=$TemplateTabGroupID?>"               class="<?=$textarea_dimensions?>" >
                </div>
                <div class="f_left wp100 left bclightgray textIndent15">DataType;
                        <input type="hidden"    id="dynamic_pannel_css_dataType_<?=$TemplateTabGroupID?>"       value="<?=$textarea_dimensions?>">
                        <input type="hidden"    id="dynamic_pannel_keep_dataType_<?=$TemplateTabGroupID?>"      value="<?=$keep_or_not?>">
                        <select id="dynamic_pannel_dataType_<?=$TemplateTabGroupID?>" class="<?=$textarea_dimensions?>">
                            <option value="boolean">boolean</option>
                            <option value="text">text</option>
                            <option value="textarea">textarea</option>
                        </select>
                </div>
                <div class="f_left wp100 left bclightgray textIndent15">Default Value
                        <input type="hidden"    id="dynamic_pannel_css_DefaultValue_<?=$TemplateTabGroupID?>"   value="<?=$textarea_dimensions?>">
                        <input type="hidden"    id="dynamic_pannel_keep_DefaultValue_<?=$TemplateTabGroupID?>"  value="<?=$keep_or_not?>">
                        <input type="text"      id="dynamic_pannel_DefaultValue_<?=$TemplateTabGroupID?>"       class="<?=$textarea_dimensions?>" >
                </div>
            </div>
            <div class="f_left wp20 s06 center red no-overflow" id="dynamic_pannel_Companies_AddTemplateData_error_<?=$TemplateTabGroupID?>">&nbsp;</div>
            <div class="f_left wp10">
                <input type="submit" class="button red" value="Add" onclick="Companies_CreateNewTemplateTab_by_GroupID(<?=$TemplateTabGroupID?>,<?=$company_id?>)">
            </div>
        </div>
<? }


function CompanyAddStanza() {
    $companies_dal = new Companies_DAL();
?>
<div class="CompaniesBodyDataContainer wp95">
    <div class="wp100 CompaniesBodyCenter">
        <?= CompanyAdd($companies_dal);?>
    </div>
</div>
<?
}
  function CompanyAdd($companies_dal) {
?>
<div class="d_InlineBlock bctrt wp100 h445px">
    <div id="NewCompanyPanel">
        <H2>New Company Information</H2>
        <div class="d_InlineBlock wp100">
            <div class="d_InlineBlock wp100 f_left center">
                <? form_fields($companies_dal,'Items_CreateNewItem','text',    'wp50','wp90 text data_control',$Edit_or_Add,'name',               'Company Name',$itemInfo,'nokeep','no_onchange');?>
                <? form_fields($companies_dal,'Items_CreateNewItem','text',    'wp50','wp90 text data_control',$Edit_or_Add,'domain',             'Domain Name',$itemInfo,'nokeep','no_onchange');?>
                <? form_fields($companies_dal,'Items_CreateNewItem','text',    'wp50','wp90 text data_control',$Edit_or_Add,'subdomain',          'Host Name',$itemInfo,'nokeep','no_onchange');?>
                <? form_fields($companies_dal,'Items_CreateNewItem','dropdown','wp50','wp90 text data_control',$Edit_or_Add,'templateType',       'Template Type',$itemInfo,'nokeep','no_onchange');?>
                <? form_fields($companies_dal,'Items_CreateNewItem','bolean',  'wp50','wp20 text data_control',$Edit_or_Add,'defaultPOS',         'P.O.S. Only?',$itemInfo,'nokeep','no_onchange');?>
            </div>
        </div>
        
        <div class="wp100 f_left">
            <div class="center">
                    <input onclick="Companies_SubmitNewCompany()" type="submit" value="Add New Company" class="button buttonMargin">
            </div>
        </div>


    </div>
</div>
<?
}
function form_fields($dal,$RememberFieldSessionType,$FormDataTypetype,$dataInputWidth,$textarea_dimensions,$Edit_or_Add,$attribute,$attribute_display_name,$itemInfo='null',$keep_or_not='keep',$javaScriptOnchange='no_change') {
    # $serviceORitem_session = Items_CreateNewItem or Items_CreateNewService
    if ($itemInfo != 'null') {
        $value = $itemInfo[0]->$attribute ;
    } else {
        if ( isset($_SESSION[$serviceORitem_session]['keep_'.$attribute]) && $_SESSION[$serviceORitem_session]['keep_'.$attribute] == 1 ) { $value= $_SESSION[$serviceORitem_session][$attribute]; }
        else { $value = ''; }
    }
    ?>
            <div class=" f_left wp100 h20px m2">
              <div class="f_left s08 right bold wp20 h20px mr10 no-overflow" id ="Inventory_Items_<?=$attribute?>"><?=ucwords($attribute_display_name);?>:</div>
              <div class="f_left s08 left  bold wp50 h20px no-overflow">
                <div class="f_left <?=$dataInputWidth?>" id="new_item_<?=$attribute?>_div">
                    <?
                        if ($FormDataTypetype == 'dropdown') {
                            switch($attribute){
                               case 'templateType':  $results = $dal->Companies_GetHTTPtemplateTypes($_SESSION['settings']['company_id']);   break;
                            }
                            if ($attribute == 'templateType') { ?>
                                <select id="dynamic_pannel_<?=$attribute?>" class="<?=$textarea_dimensions?>">
                                <? foreach ($results as $result) { $selected =  ($Settings_data[0]->value == $templateType || !$Settings_data[0]->value ) ? "selected" : "" ; ?>
                                    <option <?=$selected?> value="<?=$result->id?>"><?=$result->templateName?></option>
                                <? }?>
                                </select>
                            <?} else {
                            $javaScriptOnchangeValue = '';
                            if ($javaScriptOnchange != "no_onchange") { $javaScriptOnchangeValue = "onchange=\"Inventory_Items_Edit_or_Add_CategoryChange()\""; }
                            ?>
                            <select class="<?=$textarea_dimensions?>" id="dynamic_pannel_<?=$attribute?>" name="<?=$attribute?>" <?=$javaScriptOnchangeValue?>>
                                <option value="-1">-Select-</option>
                                <?
                                foreach($results as $result)
                                {
                                    if ($Edit_or_Add == "Add") {
                                        if ( isset($_SESSION[$serviceORitem_session]['keep_'.$attribute]) &&
                                                   $_SESSION[$serviceORitem_session]['keep_'.$attribute] == 1 &&
                                                   $result->id == $_SESSION[$serviceORitem_session][$attribute]
                                            ) {$selected = "selected" ;  }
                                        else {$selected = "" ;}
                                    }
                                    elseif ($Edit_or_Add == "Edit"){
                                        if ( $itemInfo[0]->$attribute == $result->id ) {$selected = "selected" ;  } else {$selected = "" ;}
                                    }?>
                                <option value="<?=$result->id?>" <?=$selected?> ><?=$result->name?></option>
                                <? } ?>
                        </select>
                        <?}?>
                    <? } elseif ($FormDataTypetype == 'text') {
                        ?>
                            <input class="<?=$textarea_dimensions?>" type="text" value="<?=$value?>" id="dynamic_pannel_<?=$attribute?>">
                    <? } elseif ($FormDataTypetype == 'textarea') {
                        list($rows,$columns) = split('x', $textarea_dimensions)
                        ?>
                            <textarea class="<?=$textarea_dimensions?>" id="dynamic_pannel_<?=$attribute?>" ><?=$value?></textarea>
                    <? } elseif ($FormDataTypetype == 'bolean') {
                                    $archive_selected = $active_selected = "" ;
                                    if ($Edit_or_Add     == "Add") {
                                    }
                                    elseif ($Edit_or_Add == "Edit"){
                                        if ( $result->$attribute      == 0 ) { $active_selected = "selected" ;  } else { $archive_selected = "selected" ; }
                                    }?>
                        <select class="<?=$textarea_dimensions?>" id="dynamic_pannel_<?=$attribute?>" name="<?=$attribute?>">
                            <option <?=$archive_selected?>  value="1">YES</option>
                            <option <?=$active_selected ?>  value="0">No</option>
                        </select>
                    <? } else{?>
                              Edit_or_Add_item_attribute_choice(); <?=$attribute?> is not configured yet.
                    <? } ?>
                            <input type="hidden" id="dynamic_pannel_css_<?=$attribute?>" value="<?=$textarea_dimensions?>">
                            <input type="hidden" id="dynamic_pannel_keep_<?=$keep_or_not?>" value="<?=$textarea_dimensions?>">
                </div>
                <? if ($Edit_or_Add == "Add" && $keep_or_not == 'keep') { ?>
                <div class="f_right wp15">
                        <? if (isset($_SESSION[$serviceORitem_session]['keep_'.$attribute]) && ($_SESSION[$serviceORitem_session]['keep_'.$attribute] == 1 ) ) { $checked = "checked" ; } else {$checked = "" ;} ?>
                        <input type="checkbox" value="1" id="dynamic_pannel_<?=$attribute?>_keep" class="ml5" <?=$checked?> >
                </div>
                <?} else {?>
                <div class="f_right wp15">
                        <input type="hidden" value="0" id="dynamic_pannel_<?=$attribute?>_keep" class="ml5" >
                </div>
                <? } ?>
              </div>
              <div class="f_left wp25 s06 h20px center red no-overflow" id="dynamic_pannel_<?=$attribute?>_error">&nbsp;</div>
            </div>
            <?}


function CompanySearchStanza() {
$reportType = 'Companies_AllCompanies';
?>
<div id="Companies_SearchStanza" class="d_InlineBlock hp100 wp100">
    <div class="wp95 hp100 d_InlineBlock">
            <?=search_div('name','text',$reportType,09)?>
            <?=search_div('domain','text',$reportType,09)?>
            <?=search_div('subdomain','text',$reportType,09)?>
            <?=search_div('master_email','text',$reportType,09)?>
            <?=search_div('submit','checkbox',$reportType,08)?>
    </div>
</div>
<?
}
    function search_div($search_by_field,$data_type,$reportType,$height_percent){
if (isset($_SESSION['search_data']['customer_search']['customer_search_inactive_customers']) && $_SESSION['search_data']['customer_search']['customer_search_inactive_customers'] == 1)   { $inactive_customers_checked = "checked"; } else {$inactive_customers_checked = "";}
?>
        <div class="d_InlineBlock mb5 bctrt wp100 hp<?=$height_percent?>" >
            <?       if ($search_by_field == 'name' || $search_by_field == 'domain' || $search_by_field == 'subdomain' || $search_by_field == 'master_email' ) { ?>
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
                        <div class="f_left right wp75 hp100 s06">InActive Companies</div>
                        <div class="f_left wp25 hp100"><input type='checkbox' id="dynamic_pannel_inactive_customers" value='1' onclick="Companies_CompaniesSearch_searchBy('<?=$reportType?>');" <?=$inactive_customers_checked?> ></div>
                    </div>
                </div>
            <? } elseif  ( ($search_by_field == 'submit' ) ) { ?>
                <div class="f_left hp100 wp100">
                    <input class="button s08 wp90" type="submit" value="Search" onclick="Companies_CompaniesSearch_searchBy('<?=$reportType?>');">
                </div>
            <? } ?>
        </div>
<?}