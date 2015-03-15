<?php
include_once('general_functions.php');
include_once('reports_functions.php');

class Jobs_DAL {
  public function __construct(){}
  public function get_AllJobsPerCompanyId($company_id,$totals,$active=1){
    if ($totals == 0 || $totals == 2) {
        $sql = "SELECT  j.id, j.company_id, j.name, j.sub_desc, j.company_name,
                        j.location_state, j.location_city, j.industries,
                        j.job_type, j.experience_years, j.education, j.salary,
                        j.status, j.added ";
    }
    ELSE {
        $sql ="SELECT count(j.id) as count ";
    }

        $sql.= " from jobs_master j
        where j.company_id = $company_id ";
    if ( isset($_SESSION['search_data']['Jobs_AllJobs']['jobs_search_inactive_jobs'])                   && $_SESSION['search_data']['Jobs_AllJobs']['jobs_search_inactive_jobs'] == 1 ){
        $sql .= " and ( j.status in (0,1) ) ";
    } else {
        $sql .= " and ( j.status = 1 ) ";
    }

    if ( isset($_SESSION['search_data']['Jobs_AllJobs']['jobs_search_name'])                            && $_SESSION['search_data']['Jobs_AllJobs']['jobs_search_name'] != -1 )                    {$sql .= " and j.name            like '%" . $_SESSION['search_data']['Jobs_AllJobs']['jobs_search_name'] . "%' "; }
    if ( isset($_SESSION['search_data']['Jobs_AllJobs']['jobs_search_company_name'])                    && $_SESSION['search_data']['Jobs_AllJobs']['jobs_search_company_name'] != -1 )            {$sql .= " and j.company_name    like '%" . $_SESSION['search_data']['Jobs_AllJobs']['jobs_search_company_name'] . "%' "; }
    if ( isset($_SESSION['search_data']['Jobs_AllJobs']['jobs_search_location_city'])                   && $_SESSION['search_data']['Jobs_AllJobs']['jobs_search_location_city'] != -1 )           {$sql .= " and j.location_city   like '%" . $_SESSION['search_data']['Jobs_AllJobs']['jobs_search_location_city'] . "%' "; }
    if ( isset($_SESSION['search_data']['Jobs_AllJobs']['jobs_search_location_state'])                  && $_SESSION['search_data']['Jobs_AllJobs']['jobs_search_location_state'] != -1 )          {$sql .= " and j.location_state  like '%" . $_SESSION['search_data']['Jobs_AllJobs']['jobs_search_location_state'] . "%' "; }


    if ($totals == 0) {
        if (!(isset($_SESSION['search_data']['column']))) { $sql.= " order by j.added desc"; }
        else { $sql.= " order by " . $_SESSION['search_data']['column'] . " " . $_SESSION['search_data']['asc_desc'] . " "; }

        if ( isset($_SESSION['search_data']['paging_page']) && $_SESSION['search_data']['paging_page'] != 0) {
            if ( $_SESSION['search_data']['paging_page'] == 1) { $limit_offset = 0; }
            else { $limit_offset = ( ($_SESSION['search_data']['paging_page'] - 1) * 10 ) ; }
            $sql .= " limit $limit_offset, 10 ";
        }
    }
    #if ($totals == 0 ) { print $sql . "\n"; }
    return $this->query($sql);
  }
  public function get_JobDataPerId($job_id){
    $sql = "SELECT
                id, name, sub_desc, company_name,
                location_city, location_state, industries, job_type,
                experience_years, education, salary,
                status,
                convert_tz(added, 'utc', ".quoteSmart($_SESSION['preferences']['timezone']).") as added
      from jobs_master
      where id = $job_id";
    return $this->query($sql);
    #print $sql;
  }


  public function get_ImageID_byJobID($job_id){
      $sql = "SELECT image_id
                from item_image_mappings
                where id = $job_id and
                deleted is null
                order by default_item_image desc, default_group_image desc, added asc
                limit 1";
    #print "$sql";
    return $this->query($sql);
  }
  public function get_default_Job_ImageID($job_id){
      $sql = "SELECT image_id, image_db_id 
                from item_image_mappings iim
                join image_types it on iim.image_type_id = it.id
                where iim.id = $job_id and
                it.type_name = 'jobs' and
                iim.default = 1 and
                iim.deleted is null
                order by iim.default_item_image desc, iim.default_group_image desc, iim.added asc
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

function jobs() {
    $jobs_dal = new Jobs_DAL();
?>
<head>
<script src="includes/<?=__FUNCTION__?>_functions.js" type="text/javascript"></script>
</head>
<div class="ReportsTopRow main_bc_color2 main_color2_text"><a href="#" title="Jobs" onclick="mainDiv('jobs'); return false;">Jobs</a></div>
    <div style="max-height: 1000px;" class="f_left wp100 hp94">
        <div class="leftSpace main_bc_color2 main_color2_text">&nbsp;</div>
        <div class="middleSpace wp96">
            <div class="f_left wp100 hp10">
                <div class="f_left hp100 wp35 left vtop no-overflow">
                    <img alt="" class="hp90"  src="/common_includes/includes/images/job_openings.png">
                    Job Profiles
                </div>
                <div class="f_right hp100 wp50 right">&nbsp;
                    <? if ($_SESSION['settings'][$_SESSION['settings']['login_id']]['level'] >= 1) { ?>
                    <a onclick="Jobs_AddJob()" href="javascript: none();">
                        <img alt="" class="hp90"  src="/common_includes/includes/images/add_icon.png" style="border-style: none">
                    </a>
                    <? } ?>
                </div>
            </div>
            <? 
            if (!isset($_SESSION['edit_jobs']['job_id']) && !isset($_SESSION['edit_jobs']['JobAdd'])) { ?>
            <div class="f_left wp100 hp90">
                <div class="f_left wp100 hp100" >
                    <div class="f_left wp15 hp100">
                        <div class="d_InlineBlock wp100 hp100" >
                            <?=JobSearchStanza() ?>
                        </div>
                    </div>
                    <div class="f_right wp85 hp100">
                        <div class="d_InlineBlock wp100 hp100" id="Jobs_AllJobsBodyCenter">
                            <?=JobsStanza()?>
                        </div>
                    </div>
                </div>
            </div>
            <?}
            elseif (isset($_SESSION['edit_jobs']['JobAdd'])) {
                JobAddStanza($jobs_dal);
            }
            else{?>
                <? editJobStanza($_SESSION['edit_jobs']['job_id']);?>
            <? } ?>
        </div>
        <div class="rightSpace main_bc_color2 main_color2_text">&nbsp;</div>
    </div>
<div class="ReportsBottomRow main_bc_color2 main_color2_text">&nbsp;</div>
<?
}

function JobsStanza() {
?>
        <div class="wp100 hp07" id="listing_search_paging_top">
            <? showPaging(); ?>
        </div>
        <div class="wp100 hp85 scrolling">
            <? jobsHeader(); ?>
            <? jobsAllJobs(); ?>
        </div>
        <div class="wp100 hp07" id="listing_search_paging_bottom">
            <? showPaging(); ?>
        </div>
<?
}
  function jobsHeader() {
?>
                <div class="f_left wp100 h25px s07 HEADER main_bc_color1 main_color1_text">
                    <div class="hp100 report_header_cell_wp03"><a onclick="orderBy('id','Jobs_AllJobs'); return false;">ID#</a></div>
                    <div class="hp100 report_header_cell_wp17"><a onclick="orderBy('name','Jobs_AllJobs'); return false;">Job Title</a></div>
                    <div class="hp100 report_header_cell_wp23"><a onclick="orderBy('sub_desc','Jobs_AllJobs'); return false;">Sub Description</a></div>
                    <div class="hp100 report_header_cell_wp11"><a onclick="orderBy('company_name','Jobs_AllJobs'); return false;">Company Name</a></div>
                    <div class="hp100 report_header_cell_wp15"><a onclick="orderBy('location_city','Jobs_AllJobs'); return false;">City</a></div>
                    <div class="hp100 report_header_cell_wp05"><a onclick="orderBy('location_state','Jobs_AllJobs'); return false;">State</a></div>
                    <div class="hp100 report_header_cell_wp10"><a onclick="orderBy('salary','Jobs_AllJobs'); return false;">Salary</a></div>
                    <div class="hp100 report_header_cell_wp07"><a onclick="orderBy('status','Jobs_AllJobs'); return false;">Status</a></div>
                    <div class="hp100 report_header_cell_wp06">Edit</div>
                </div>
<?
}
  function jobsAllJobs() {
    $jobs_dal = new Jobs_DAL();
    $jobs = $jobs_dal->get_AllJobsPerCompanyId($_SESSION['settings']['company_id'],0);
    $altClass = "bctr1a";
    if (count($jobs) >0 ) {
        foreach($jobs as $job){
            if     ($job->status == 0) {   $status_action = "INactive"; $status_class = "red"    ;
                                                $action = 1; $alt="Activate?";}
            elseif ($job->status == 1) {   $status_action = "Active"  ; $status_class = "green"  ;
                                                $action = 0; $alt="DeActivate?";}
            ?>
                <div class="d_InlineBlock f_left wp100 h35px s07 <?=$altClass?>">
                    <div class="hp100 report_data_cell_wp03"><?=$job->id?></div>
                    <div class="hp100 report_data_cell_wp17 s10"><?=$job->name?></div>
                    <div class="hp100 report_data_cell_wp23 s09 left no-overflow" title="<?=$job->sub_desc?>"><?=$job->sub_desc?>&nbsp;</div>
                    <div class="hp100 report_data_cell_wp11"><?=$job->company_name?>&nbsp;</div>
                    <div class="hp100 report_data_cell_wp15 no-overflow"><?=$job->location_city?>&nbsp;</div>
                    <div class="hp100 report_data_cell_wp05"><?=$job->location_state?>&nbsp;</div>
                    <div class="hp100 report_data_cell_wp10 right"><?=money2($job->salary)?>&nbsp;</div>
                    <div class="hp100 report_data_cell_wp07" title="<?=$alt?>">
                        <input alt="<?=$alt?>" onclick="Jobs_UpdStatus(<?=$job->id?>,<?=$action?>)" type="submit" value="<?=$status_action?>" class="button s07 <?=$status_class?>">
                    </div>
                    <div class="hp100 report_data_cell_wp06">
                        <input onclick="Jobs_editJob(<?=$job->id?>)" type="submit" value="EDIT" class="button s07">
                    </div>
                </div>
            <?
            if ($altClass == "bctr1a") { $altClass = "bctr1b"; } else {$altClass = "bctr1a";}
        }
     } else { ?>
                <div class="d_InlineBlock f_left wp100 h30px s07 <?=$altClass?>">
                    <div class="hp100 center wp100">There are no jobs currently in the database meeting your search criteria.</div>
                </div>
     <? }
}

function editJobStanza($job_id) {
$jobs_dal = new Jobs_DAL();
?>
<div class="d_InlineBlock wp100 hp90">
    <div class="d_InlineBlock wp95 hp100">
        <?=jobs_editJobTop($jobs_dal,$job_id); ?>
        <?=jobs_editJobTabs($jobs_dal,$job_id); ?>
        <?
        if (isset($_SESSION['edit_jobs']['ActiveTab']) && $_SESSION['edit_jobs']['ActiveTab'] == "JobAttributes" || !isset($_SESSION['edit_jobs']['ActiveTab']) ) {
            jobSummary($jobs_dal,$job_id);
        } elseif (isset($_SESSION['edit_jobs']['ActiveTab']) && $_SESSION['edit_jobs']['ActiveTab'] == "JobImages" || !isset($_SESSION['edit_jobs']['ActiveTab']) ) {
            jobImages($jobs_dal,$job_id);
        }
        ?>
    </div>
</div>
<?
}
  function jobs_editJobTop($jobs_dal,$job_id) {
       $image_id_data   = $jobs_dal->get_default_Job_ImageID($job_id);
       $job_data        = $jobs_dal->get_JobDataPerId($job_id);
?>
    <div class="bctrt wp100 hp20 d_InlineBlock">
        <div class="f_left wp25 hp100">
            <? show_Image($image_id_data)?>
        </div>
        <div class="f_left  wp50 hp100">
            <div class="f_left hp50 wp100 s19">&nbsp;<?=$job_data[0]->name?></div><br>
            <div class="f_left hp50 wp100 s10">Added<br><?=$job_data[0]->added?></div>
        </div>
        <div class="f_right wp25 hp100" >
            <div id="update_add_job_status_results" >&nbsp;</div>
        </div>
    </div>
<?
}
  function jobs_editJobTabs($jobs_dal,$job_id){
$activeTabBackground = "bctrt";
if (isset($_SESSION['edit_jobs']['ActiveTab']) && $_SESSION['edit_jobs']['ActiveTab'] == "JobAttributes"){
    $JobAttributesBackground = 'bctrt';
} else { $JobAttributesBackground = ''; }

if (isset($_SESSION['edit_jobs']['ActiveTab']) && $_SESSION['edit_jobs']['ActiveTab'] == "JobImages"){
    $JobImagesBackground = 'bctrt';
} else { $JobImagesBackground = ''; }
?>
    <div class="wp100 hp05 f_left">
        <div onclick="Jobs_ActiveJobsTabs('JobAttributes');"        class="f_left s08 wp20 hp100 mp <?=$JobAttributesBackground?>" >Job Info</div>
        <div onclick="Jobs_ActiveJobsTabs('JobImages');"            class="f_left s08 wp20 hp100 mp <?=$JobImagesBackground?>" >Job Images</div>
    </div>

<?
}
      function jobSummary($jobs_dal,$job_id){
        $job_data = $jobs_dal->get_JobDataPerId($job_id);
    ?>
        <div class="wp100 d_InlineBlock ">
            <div class="f_left wp100 bctrt center">Job Basic Information</div>
        </div>
        <div class="box5 wp100">
            <div class="wp100 d_InlineBlock bctrt">
                <div class="f_left wp35 bctrt">Job Name</div>
                <div class="f_left wp25 bctrt">Sub Description</div>
                <div class="f_left wp40 bctrt" >&nbsp;</div>
            </div>
            <div class="wp100 d_InlineBlock bclightgray">
                <div id="jobSummary_name" class="f_left wp35">
                    <input type="text" class="wp90 " value="<?=$job_data[0]->name?>" id="dynamic_pannel_name">
                </div>
                <div id="jobSummary_company_name" class="f_left wp25" >
                    <input type="text" class="wp90 s07 " value="<?=$job_data[0]->company_name?>" id="dynamic_pannel_company_name">
                </div>
            </div>

            <div class="wp100 d_InlineBlock bctrt">
                <div class="f_left wp35 bctrt">Location City</div>
                <div class="f_left wp25 bctrt">Location State</div>
                <div class="f_left wp25 bctrt">Salary</div>
                <div class="f_left wp15 bctrt">&nbsp;</div>
            </div>
            <div class="wp100 d_InlineBlock bclightgray">
                <div id="jobSummary_location_city" class="f_left wp35">
                    <input type="text" class="wp90 " value="<?=$job_data[0]->location_city?>" id="dynamic_pannel_location_city">
                </div>
                <div id="jobSummary_company_location_state" class="f_left wp25" >
                    <input type="text" class="wp90 s07 " value="<?=$job_data[0]->location_state?>" id="dynamic_pannel_location_state">
                </div>
                <div id="jobSummary_company_salary" class="f_left wp25" >
                    <input type="text" class="wp25 center s07 " value="<?=$job_data[0]->salary?>" id="dynamic_pannel_salary">
                </div>
            </div>

            <div class="wp100 d_InlineBlock bctrt">
                <div class="f_left wp80 bctrt">Job Description - Full Details</div>
            </div>
            <div class="wp100 d_InlineBlock bclightgray">
                <div id="jobSummary_sub_desc" class="f_left wp80">
                    <textarea class="wp90 h100px s08" id="dynamic_pannel_sub_desc" ><?=$job_data[0]->sub_desc?></textarea>
                </div>
            </div>
            <div class="wp100 d_InlineBlock bclightgray">
                <div class="f_right wp25">
                    <input type="submit" class="button" value="Update" onclick="Job_UpdJobAttributes(<?=$job_id?>)">
                </div>
            </div>
        </div>

        <div class="wp100 d_InlineBlock h02px">
            <div class="f_left wp100 " >&nbsp;</div>
        </div>
        <?
    }
      function jobImages($jobs_dal,$job_id){
        $job_data = $jobs_dal->get_JobDataPerId($job_id);
        $general_dal = new GENERAL_DAL();
        upload_file_stanza('jobs',$general_dal,$job_id);
    }
 
  function JobAddStanza($jobs_dal) {
    ?>
    <div class="profileBodyDataContainer wp100 hp90">
        <div class="profileBodyCenter wp100 hp100">
            <?= JobAdd($jobs_dal);?>
        </div>
    </div>
    <?
    }
      function JobAdd($jobs_dal) {
        $company_id = $_SESSION['settings']['company_id'];
    ?>
    <div class="d_InlineBlock wp100 hp100">
        <div id="registerPanel" class="d_InlineBlock wp95 hp100">
            <div class="f_left left wp100 hp05 textIndent15 bctrt">
               New Job Post Basic Information
            </div>
            <div class="d_InlineBlock wp100 hp75 box5">
                <div class="d_InlineBlock wp90 bctrt box5">
                    <div class="d_InlineBlock wp100">
                        <div class="f_left right wp15">Job Name</div>
                        <div class="f_left wp40">
                            <input class="wp90" type="text" id="dynamic_pannel_name" value="Senior Boss Man">
                            <input type="hidden" id="dynamic_pannel_css_name" value="wp90">
                        </div>
                        <div class="d_InlineBlock wp45 f_left red s07 no-overflow" id="dynamic_pannel_name_error">
                            &nbsp;
                        </div>
                    </div>
                    <div class="d_InlineBlock wp100">
                        <div class="f_left right wp15 s08">Company Name</div>
                        <div class="f_left wp40">
                            <input class="wp90" type="text" id="dynamic_pannel_company_name" value="company1">
                            <input type="hidden" id="dynamic_pannel_css_company_name" value="wp90">
                        </div>
                        <div class="d_InlineBlock wp45 f_left red s07 no-overflow" id="dynamic_pannel_company_name_error">
                            &nbsp;
                        </div>
                    </div>
                    <div class="d_InlineBlock wp100">
                        <div class="f_left right wp15">Description</div>
                        <div class="f_left wp40">
                            <textarea class="wp90 h100px text " id="dynamic_pannel_sub_desc" >This job is awesome.  Would you like to be running the top cool thing involved in design of something worth designing?</textarea>
                            <input type="hidden" id="dynamic_pannel_css_sub_desc" value="wp90 h100px text">
                        </div>
                        <div class="d_InlineBlock wp45 f_left red s07 no-overflow" id="dynamic_pannel_sub_desc_error">
                            &nbsp;
                        </div>
                    </div>
                    <div class="d_InlineBlock wp100">
                        <div class="f_left right wp15">City</div>
                        <div class="f_left wp40">
                            <input class="wp90" type="text" id="dynamic_pannel_location_city" value="Compton">
                            <input type="hidden" id="dynamic_pannel_css_location_city" value="wp90">
                        </div>
                        <div class="d_InlineBlock wp45 f_left red s07 no-overflow" id="dynamic_pannel_location_city_error">
                            &nbsp;
                        </div>
                    </div>
                    <div class="d_InlineBlock wp100">
                        <div class="f_left right wp15">State</div>
                        <div class="f_left left pl10 wp35" >
                            <select style="max-width:120px;" id="dynamic_pannel_location_state">
                            <option value="">- Select -</option>
                            <option value="AL" selected>Alabama</option>
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
                            <input type="hidden" id="dynamic_pannel_css_location_state" value="max-width:120px;">
                        </div>
                        <div class="d_InlineBlock wp45 f_left red s07 no-overflow" id="dynamic_pannel_location_state_error">
                            &nbsp;
                        </div>
                    </div>
                    <div class="d_InlineBlock wp100">
                        <div class="f_left right wp15">Salary</div>
                        <div class="f_left left pl10 wp35 ">
                            <input class="wp20 money" type="text" id="dynamic_pannel_salary" value="100000">
                            <input type="hidden" id="dynamic_pannel_css_salary" value="wp20">
                        </div>
                        <div class="d_InlineBlock wp45 f_left red s07 no-overflow" id="dynamic_pannel_salary_error">
                            &nbsp;
                        </div>
                    </div>                    
                </div>
            </div>
            <div class="d_InlineBlock wp100 hp05">
                <div class="center">
                        <input onclick="Jobs_AddNewJob(<?=$company_id?>)" type="submit" value="Add New Job Listing" class="button buttonMargin"/>
                </div>
            </div>
            <div class="d_InlineBlock wp100 hp10" id="failed_NewJob_message">
                &nbsp;
            </div>
        </div>
    </div>
    <?
    }

function JobSearchStanza() {
$reportType = 'Jobs_AllJobs';
?>
    <div id="item_SearchStanza" class="d_InlineBlock hp100 wp100">
        <div class="wp95 hp100 d_InlineBlock">
            <?=job_search_div('name','text',$reportType,09)?>
            <?=job_search_div('sub_desc','text',$reportType,09)?>
            <?=job_search_div('company_name','text',$reportType,09)?>
            <?=job_search_div('location_city','text',$reportType,09)?>
            <?=job_search_div('location_state','text',$reportType,09)?>
            <?=job_search_div('miscellaneous','checkbox',$reportType,11)?>
            <?=job_search_div('submit','checkbox',$reportType,08)?>
        </div>
    </div>
<?
}
    function job_search_div($search_by_field,$data_type,$reportType,$height_percent){
    if (isset($_SESSION['search_data']['Jobs_AllJobs']['job_search_inactive_jobs']) && $_SESSION['search_data']['Jobs_AllJobs']['job_search_inactive_jobs'] == 1)   { $inactive_jobs_checked = "checked"; } else {$inactive_jobs_checked = "";}
    ?>
            <div class="d_InlineBlock mb5 bctrt wp100 hp<?=$height_percent?>" >
                <?       if ($search_by_field == 'name' || $search_by_field == 'sub_desc' || $search_by_field == 'company_name' || $search_by_field == 'location_city' || $search_by_field == 'location_state' ) { ?>
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
                            <div class="f_left right wp85 hp100 s06">InActive Jobs</div>
                            <div class="f_left wp15 hp100"><input type='checkbox' id="dynamic_pannel_inactive_jobs" value='1' onclick="Jobs_Search_searchBy('<?=$reportType?>');" <?=$inactive_jobs_checked?> ></div>
                        </div>
                    </div>
                <? } elseif  ( ($search_by_field == 'submit' ) ) { ?>
                    <div class="f_left hp100 wp100">
                        <input class="button s08 wp90" type="submit" value="Search" onclick="Jobs_Search_searchBy('<?=$reportType?>');">
                    </div>
                <? } ?>
            </div>
    <?}