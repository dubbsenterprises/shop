<?php
include_once('general_functions.php');
class Preferences_DAL {
  public function __construct(){}
  public function get_company_preferences($company_id,$column){
    $sql = "SELECT 
            name, value, options, preferences_tab, input_type, type, updated
            from preferences 
            where company_id = $company_id and name = '$column'";
    #echo $sql;
    return $this->query($sql);
  }
  public function get_company_preferences_defaults($column){
    $sql = "SELECT
            name,default_value,type,attributes as options
            from preferences_defaults
            where name = '$column'";
    #echo $sql;
    return $this->query($sql);
  }

  public function get_addresses_per_company_id($company_id){
    $sql = "SELECT address_id,address_line1,address_line2,city,state,zipcode,google_map_url,country,default_address
      from addresses
      where company_id = $company_id ;";
    #echo $sql;
    return $this->query($sql);
  }
  public function get_address_data_by_address_id($address_id){
    $sql = "SELECT address_line1,address_line2,city,state,zipcode,google_map_url
        from addresses
        where address_id = $address_id ";
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
 
function preferences() {
?>
<div class="ReportsTopRow main_bc_color2 main_color2_text">Preferences</div>
    <div style="max-height: 1000px;" class="f_left wp100 hp94">
        <div class="leftSpace main_bc_color2 main_color2_text">&nbsp;</div>
        <div class="middleSpace wp96">
            <div class="d_InlineBlock hp10 wp100">
                <div class="f_left hp100 wp35 left vtop no-overflow">
                    <img alt="" height="45" src="/common_includes/includes/images/preferences_48_48.jpg">
                    Company Preferences
                </div>
                <div class="f_right hp100 wp50 right">
                    &nbsp;
                </div>
            </div>
            <div class="d_InlineBlock wp95 hp90">
                <div class="profileBodyCenter" id="profileBodyCenter">
                <?preferencesStanza();?>
                </div>
            </div>
        </div>
        <div class="rightSpace main_bc_color2 main_color2_text">&nbsp;</div>
    </div>
<div class="ReportsBottomRow main_bc_color2 main_color2_text">&nbsp;</div>
<?
}
    function preferencesStanza() {
    $preferences_dal = new Preferences_DAL();
        preferencesTabs($preferences_dal);
        if (ISSET($_SESSION['preferences']['ActiveTab']) && $_SESSION['preferences']['ActiveTab'] == "GeneralPreferences" || !isset($_SESSION['preferences']['ActiveTab'])){
            GeneralPreferencesTab($preferences_dal,$_SESSION['settings']['company_id']);
        }
        if (isset($_SESSION['preferences']['ActiveTab']) && $_SESSION['preferences']['ActiveTab'] == "ReceiptPreferences" ){
            ReceiptPreferencesTab($preferences_dal,$_SESSION['settings']['company_id']);
        }
        if (ISSET($_SESSION['preferences']['ActiveTab'])&& $_SESSION['preferences']['ActiveTab'] == "ImagesPreferences" ){
            ImagePreferencesTab($preferences_dal,$_SESSION['settings']['company_id']);
        }
        if (ISSET($_SESSION['preferences']['ActiveTab']) && $_SESSION['preferences']['ActiveTab'] == "PreferencesPhysicalAddress" ){
            PreferencesPhysicalAddress($preferences_dal,$_SESSION['settings']['company_id']);
        }
    }
      function preferencesTop($preferences_dal) {
    ?>
        <div class="bctrt wp100" style="display: inline-block;">
            <div style="float: left; width: 150px; height:100px;">
                <img src="/common_includes/includes/images/profile_pic.gif">
            </div>
            <div style="float: right; width: 150px; height:100px;">
                <img src="/common_includes/includes/images/profile_pic.gif">
            </div>
        </div>
    <?
    }
      function preferencesTabs($preferences_dal){
    $activeTabBackground = "bctrt";

    if (isset($_SESSION['preferences']['ActiveTab']) && $_SESSION['preferences']['ActiveTab'] == "GeneralPreferences" || !isset($_SESSION['preferences']['ActiveTab'])){
        $PreferencesGeneralBackground = 'bctrt';
    }
    else { $PreferencesGeneralBackground = ''; }

    if (isset($_SESSION['preferences']['ActiveTab']) && $_SESSION['preferences']['ActiveTab'] == "ReceiptPreferences"){
        $PreferencesReceiptBackground = 'bctrt';
    }
    else { $PreferencesReceiptBackground = ''; }

    if (isset($_SESSION['preferences']['ActiveTab']) && $_SESSION['preferences']['ActiveTab'] == "PreferencesPhysicalAddress"){
        $PreferencesPhysicalAddressBackground = 'bctrt';
    }
    else { $PreferencesPhysicalAddressBackground = ''; }

    if (isset($_SESSION['preferences']['ActiveTab']) && $_SESSION['preferences']['ActiveTab'] == "ImagesPreferences"){
        $PreferencesImageBackground = 'bctrt';
    }
    else { $PreferencesImageBackground = ''; }

    ?>
    <div class="wp99 f_left">
        <div onclick="editPreferencesTabs('GeneralPreferences');"           class="f_left s08 wp20 <?=$PreferencesGeneralBackground?>" >General Preferences</div>
        <div onclick="editPreferencesTabs('ReceiptPreferences');"           class="f_left s08 wp20 <?=$PreferencesReceiptBackground?>" >Receipt Preferences</div>
        <div onclick="editPreferencesTabs('PreferencesPhysicalAddress');"   class="f_left s08 wp20 <?=$PreferencesPhysicalAddressBackground?>" >Locations</div>
        <div onclick="editPreferencesTabs('ImagesPreferences');"            class="f_left s08 wp20 <?=$PreferencesImageBackground?>" >Images</div>
    </div>
    <?
    }

      function GeneralPreferencesTab($preferences_dal,$company_id){
    ?>
    <div class="f_left left wp100 scrolling hp95">
        <div class="d_InlineBlock wp99">
            <div class="wp100 d_InlineBlock ">
                <div class="f_left wp100 bctrt center">General Company Information</div>
            </div>

            <div class="wp99 d_InlineBlock box5">
                <? edit_preference($preferences_dal,'company name',$company_id); ?>
                <? edit_preference($preferences_dal,'master_email',$company_id); ?>
                <? edit_preference($preferences_dal,'timezone',$company_id,'get_All_TimeZones'); ?>
                <? edit_preference($preferences_dal,'currency',$company_id); ?>
                <? edit_preference($preferences_dal,'currency position',$company_id); ?>
                <? edit_preference($preferences_dal,'money string contains space',$company_id); ?>
            </div>

            <div class="wp99 d_InlineBlock box3 mt10">
                <? edit_preference($preferences_dal,'meta_description',$company_id); ?>
                <? edit_preference($preferences_dal,'meta_keywords',$company_id); ?>
            </div>

            <div class="wp99 d_InlineBlock box3 mt10">
                <? edit_preference($preferences_dal,'default_reorder_limit1',$company_id); ?>
                <? edit_preference($preferences_dal,'default_reorder_limit2',$company_id); ?>
            </div>
            
            <? 
            $array = array(1,2,3);
            foreach ($array as $numeration) {?>
            <div class="wp99 d_InlineBlock box3 mt10">
                <? edit_preference_color($preferences_dal,"main_bc_color{$numeration}",$company_id,$numeration); ?>
                <? edit_preference_color($preferences_dal,"main_color{$numeration}_text",$company_id,$numeration); ?>
                
                <? edit_preference_color($preferences_dal,"main_bc_color{$numeration}_light",$company_id,$numeration); ?>
                <? edit_preference_color($preferences_dal,"main_color{$numeration}_light_text",$company_id,$numeration); ?>
            </div>
            <? } ?>

            <div class="wp99 d_InlineBlock box3 mt10">
                <? edit_preference($preferences_dal,'appointments_allow_mult_customer',$company_id); ?>
                <? edit_preference($preferences_dal,'appointment_slot_interval',$company_id); ?>
                <? edit_preference($preferences_dal,'bookings_copy_masterEmail',$company_id); ?>
                <? edit_preference($preferences_dal,'bookings_cancellation_policy',$company_id); ?>
            </div>
        </div>
    </div>
    <?
    }
      function ReceiptPreferencesTab($preferences_dal,$company_id){
?>
    <div class="f_left left wp100 ">
        <div class="f_left wp100 hp05 ">
            <div class="f_left wp100 bctrt center">Receipt Options</div>
        </div>

        <div class="f_left wp100 hp95 mt10 scrolling">
            <div class="box5">
            <? edit_preference($preferences_dal,'receipt title',$company_id,'text'); ?>
            <? edit_preference($preferences_dal,'receipt header',$company_id,'text'); ?>
            <? edit_preference($preferences_dal,'receipt footer',$company_id,'text'); ?>
            <? edit_preference($preferences_dal,'receipt width',$company_id,'text'); ?>
            <? edit_preference($preferences_dal,'brand name shown on receipts',$company_id,'boolean'); ?>
            </div>
        </div>
    </div>
    <?
    }
      function ImagePreferencesTab  ($preferences_dal,$company_id){
    $general_dal = new GENERAL_DAL();
    $preferences_data_receipt_footer = $preferences_dal->get_company_preferences($company_id,'receipt footer');
    $preferences_data_receipt_header = $preferences_dal->get_company_preferences($company_id,'receipt header');
    $preferences_data_receipt_title = $preferences_dal->get_company_preferences($company_id,'receipt title');
    $preferences_data_receipt_width = $preferences_dal->get_company_preferences($company_id,'receipt width');
    $preferences_data_receipt_brand_on_receipt = $preferences_dal->get_company_preferences($company_id,'brand name shown on receipts');
?>
    <div class="f_left left wp100 hp95">
        <div class="wp100 f_left hp05">
            <div class="f_left wp100 bctrt center">Image Options</div>
        </div>
        <div class="wp100 f_left hp95 scrolling">
            <?upload_file_stanza('main_company_logo',$general_dal,$_SESSION['settings']['company_id'],'mainBody');?>
            <?upload_file_stanza('main_page_background',$general_dal,$_SESSION['settings']['company_id'],'mainBody');?>
            <?upload_file_stanza('main_page_img_1',$general_dal,$_SESSION['settings']['company_id'],'mainBody');?>
            <?upload_file_stanza('main_page_img_2',$general_dal,$_SESSION['settings']['company_id'],'mainBody');?>
            <?upload_file_stanza('main_page_img_3',$general_dal,$_SESSION['settings']['company_id'],'mainBody');?>
            <?upload_file_stanza('link_1_img_1',$general_dal,$_SESSION['settings']['company_id'],'mainBody');?>
            <?upload_file_stanza('link_1_img_2',$general_dal,$_SESSION['settings']['company_id'],'mainBody');?>
            <?upload_file_stanza('link_2_img_1',$general_dal,$_SESSION['settings']['company_id'],'mainBody');?>
            <?upload_file_stanza('link_2_img_2',$general_dal,$_SESSION['settings']['company_id'],'mainBody');?>
            <?upload_file_stanza('link_3_img_1',$general_dal,$_SESSION['settings']['company_id'],'mainBody');?>
            <?upload_file_stanza('link_3_img_2',$general_dal,$_SESSION['settings']['company_id'],'mainBody');?>
            <?upload_file_stanza('link_4_img_1',$general_dal,$_SESSION['settings']['company_id'],'mainBody');?>
            <?upload_file_stanza('link_4_img_2',$general_dal,$_SESSION['settings']['company_id'],'mainBody');?>
        </div>
    </div>
    <?
    }
      function PreferencesPhysicalAddress($preferences_dal,$company_id){
                $address_data[0] = array();
                $style = " style=\"text-align: right;\"";
                $bg_color = "#FFFFFF";
    ?>
            <div>
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
            $rows = $preferences_dal->get_addresses_per_company_id($company_id);
            if (count($rows) >0 ) {
            foreach($rows as $row) { ?>
                <div class="f_left wp100 bclightgray ">
                    <div class="f_left wp05">&nbsp;<?=$row->address_id?></div>
                    <div class="f_left wp25">&nbsp;<?=$row->address_line1?></div>
                    <div class="f_left wp15">&nbsp;<?=$row->address_line2?></div>
                    <div class="f_left wp20">&nbsp;<?=$row->city?></div>
                    <div class="f_left wp10">&nbsp;<?=$row->state?></div>
                    <div class="f_left wp10">&nbsp;<?=$row->zipcode?></div>
                    <? if ($_SESSION['settings'][$_SESSION['settings']['login_id']]['level'] >=1 ) { ?>
                    <div class="f_right wp10">
                        <? if (  isset($_SESSION['edit_preferences']['edit_address_address_id']) && $_SESSION['edit_preferences']['edit_address_address_id'] == $row->address_id ){ ?>
                            &nbsp;
                        <? } else {?>
                            <input type="submit" value="Edit" class="button" onclick="preferences_EditAddress_setAddressID(<?=$row->address_id?>)">
                        <? } ?>
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
            if (isset($_SESSION['edit_preferences']['edit_address_address_id']) ){
                $address_data = $preferences_dal->get_address_data_by_address_id($_SESSION['edit_preferences']['edit_address_address_id']);
            } else { $address_data = array(); }
                if (count($address_data) > 0 )  { 
                    $address_line1    = $address_data[0]->address_line1 ;
                    $address_line2    = $address_data[0]->address_line2 ;
                    $city             = $address_data[0]->city ;
                    $state            = $address_data[0]->state ;
                    $zipcode          = $address_data[0]->zipcode ;
                    $google_map_url   = $address_data[0]->google_map_url;
                } else {
                    $address_line1 = ''; $address_line2 = ''; $city = ''; $state = ''; $zipcode = ''; $google_map_url = '';
                }
            ?>
                <? if ($_SESSION['settings'][$_SESSION['settings']['login_id']]['level'] >=1 ) { ?>
                <div class="wp100 d_InlineBlock ">
                    <div class="f_left wp100" >&nbsp;</div>
                </div>
                <div class="wp100 d_InlineBlock ">
                    <div class="f_left wp100 bctrt center">Add/Update Address</div>
                </div>

                
                <div class="wp100 h250px f_left">

                <? if (isset($_SESSION['edit_preferences']['edit_address_address_id']) ){?>
                <div class="wp60 hp100 f_left">
                <? } else { ?>
                <div class="wp100 hp100 f_left">
                <? } ?>
                <div class="wp100 h25px d_InlineBlock bclightgray">
                    <div class="f_left  hp100 wp10 bctrt right">Address</div>
                    <div class="f_left left bclightgray wp40 hp100 textIndent15" id="Summary_preferences_address1"  >
                        <input type="text" class="wp90" value="<?=$address_line1?>" id="preferences_address1">
                    </div>
                    <div id="failed_register_message_NU_Address1" class="f_left wp30 hp100 bclightgray">&nbsp</div>

                    <? if (isset($_SESSION['edit_preferences']['edit_address_address_id']) ){?>
                        <div class="f_right wp15" onclick="preferences_UpdateAddress(<?=$_SESSION['edit_preferences']['edit_address_address_id']?>)">
                            <input type="submit" class="red button " value="Update">
                        </div>
                    <? } else {?>
                        <div class="f_right wp15" onclick="preferences_AddAddressExistingCompany(<?=$company_id?>)">
                            <input type="submit" class="green button " value="Add Address">
                        </div>
                    <? }?>
                </div>

                <div class="wp100 h25px d_InlineBlock bclightgray">
                    <div class="f_left hp100 wp10 bctrt right">Address 2</div>
                    <div id="Summary_preferences_address2"  class="f_left left bclightgray wp50 hp100 textIndent15">
                        <input type="text" class="w150" value="<?=$address_line2?>" id="preferences_address2">
                    </div>
                    <div class="f_left wp40 bclightgray">&nbsp</div>
                </div>

                <div class="wp100 h25px d_InlineBlock bclightgray">
                    <div class="f_left bctrt hp100 wp10 right">City</div>
                    <div id="Summary_preferences_city"      class="f_left left bclightgray wp50 hp100 textIndent15">
                        <input type="text" class="w80" value="<?=$city?>" id="preferences_city">
                    </div>
                    <div id="failed_register_message_NU_City" class="f_left hp100 wp40 bclightgray">&nbsp</div>
                </div>

                <div class="wp100 h25px d_InlineBlock bclightgray">
                    <div class="f_left bctrt hp100 wp10 right">State</div>
                    <div id="Summary_preferences_state"     class="f_left left bclightgray wp50 hp100 textIndent15">
                        <input type="text" class="w20" value="<?=$state?>" id="preferences_state">
                    </div>
                    <div class="f_left wp40 bclightgray">&nbsp</div>
                </div>

                <div class="wp100 h25px d_InlineBlock bclightgray">
                    <div class="f_left bctrt hp100 wp10 right">Zip</div>
                    <div id="Summary_preferences_zipcode"   class="f_left left bclightgray wp50 hp100 textIndent15">
                        <input type="text" class="w70" value="<?=$zipcode?>" id="preferences_zipcode">
                    </div>
                    <div id="failed_register_message_NU_zipcode" class="f_left hp100 wp40 bclightgray">&nbsp</div>
                </div>

                <div class="wp100 h75px d_InlineBlock bclightgray">
                    <div class="f_left bctrt hp100 wp10 right">Google Map</div>
                    <div id="Summary_preferences_google_map_url" class="f_left left bclightgray wp50 hp100 textIndent15">
                        <textarea id="preferences_google_map_url" class="wp90 hp90 text f_right mr3" placeholder="Put the Google Map in this box."><?=$google_map_url?></textarea>
                    </div>
                    <div id="failed_register_message_NU_google_map_url" class="f_left hp100 wp40 bclightgray">&nbsp</div>
                </div>

                </div>
                    
                    <? if (isset($_SESSION['edit_preferences']['edit_address_address_id']) ){?>
                    <div class="wp40 hp100 f_right">
                        <div class="d_InlineBlock f_left hp20 wp100 bctrt">hours crap for address id:<?=$_SESSION['edit_preferences']['edit_address_address_id']?></div>
                        
                        <? $height_percent = 'hp10'; ?>
                        <? print_day_of_week_edit_hours('Monday',$height_percent) ?>
                        <? print_day_of_week_edit_hours('Tuesday',$height_percent) ?>
                        <? print_day_of_week_edit_hours('Wednesday',$height_percent) ?>
                        <? print_day_of_week_edit_hours('Thurday',$height_percent) ?>
                        <? print_day_of_week_edit_hours('Friday',$height_percent) ?>
                        <? print_day_of_week_edit_hours('Saturday',$height_percent) ?>
                        <? print_day_of_week_edit_hours('Sunday',$height_percent) ?>
                    </div>
                    <? } ?>
                    
                </div>
                <? } ?>
            </div>
            <?
            unset($_SESSION['edit_preferences']['edit_address_address_id']);
}
            function print_day_of_week_edit_hours($day,$height_percent){?>
            <div class="d_InlineBlock f_left wp100 <?=$height_percent?> " id="div_preferences_update_address_monday">
                <div class="f_left wp25 hp100 bctrt"><?=$day?></div>
                <div class="f_right left bclightgray wp75 hp100 textIndent15" id="Summary_hours_data_monday"  >
                    <input type="text" class="wp90" value="<?=$hours_data_monday?>" id="hours_data_monday">
                </div>
            </div> 
                
            <?}

          function edit_preference($preferences_dal,$column_name,$company_id,$dal_function=''){
    $preferences_data   = $preferences_dal->get_company_preferences($company_id,$column_name);
    $preferences_defaults_data = $preferences_dal->get_company_preferences_defaults($column_name);

    $textarea_dimensions= $selected = $message = '';
    $insert_dal         = new InsertUpdateDelete_DAL();
    $general_dal        = new GENERAL_DAL();
    if (count($preferences_data) == 0) {
        if (count($preferences_defaults_data) > 0) {
            $sql="insert into preferences (company_id,name,value,type,options,added)
            values (
             ". $company_id . ",
            '" .  $column_name . "',
            "  .  quoteSmart($preferences_defaults_data[0]->default_value) . ",
            '" .  $preferences_defaults_data[0]->type . "',
            '" .  $preferences_defaults_data[0]->options . "',";
            $sql .= " now() )";
            #print $sql; 
            $preference_id = $insert_dal->insert_query($sql);
        } else { $message = "Row $column_name also missing from preferences_defaults table."; }
        $preferences_data   = $preferences_dal->get_company_preferences($company_id,$column_name);
    }
    if ($preferences_defaults_data[0]->type == "textarea") {
        $div_height = 'h150px';
    } else {
        $div_height = 'h25px';
    }
    ?>
    <div class="wp100 <?=$div_height?> d_InlineBlock bctrt ">
        <div class="f_left  wp25 hp100 right no-overflow" title="<?=$column_name?>">
            &nbsp;<?=$column_name?>
        </div>
        <div class="f_left  wp45 hp100 left bclightgray textIndent15">&nbsp;
            <? if ($preferences_defaults_data[0]->type == "boolean") {
                $selected =  ($preferences_data[0]->value == 0 || !$preferences_data[0]->value ) ? "selected" : "" ;
            ?> 
                <select id="<?=$column_name?>" class="<?=$preferences_data[0]->options?>">
                    <option <?=$selected?> value="1">Yes</option>
                    <option <?=$selected?> value="0">No</option>
                </select>
            <? } elseif ($preferences_defaults_data[0]->type == "text") { ?>
                <? if ( ($preferences_defaults_data[0]->default_value == $preferences_data[0]->value) && $preferences_data[0]->updated === NULL ) {?>
                <input placeholder="<?=$preferences_defaults_data[0]->default_value?>" id="<?=$column_name?>" class="<?=$preferences_data[0]->options?>" type="<?=$preferences_data[0]->input_type?>">
                <?} else {?>
                <input value="<?=$preferences_data[0]->value?>" id="<?=$column_name?>" class="<?=$preferences_data[0]->options?>" type="<?=$preferences_data[0]->input_type?>">
                <? } ?>
            <? } elseif ($preferences_defaults_data[0]->type == "dropdown") {
                    if ( $preferences_data[0]->name == "appointment_slot_interval") { ?>
                        <select class="<?=$textarea_dimensions?>" id="<?=$column_name?>">
                                    <option value="5"  <?= $selected =($preferences_data[0]->value == 5  ) ? "selected" : "" ; ?>  >5</option>
                                    <option value="10" <?= $selected =($preferences_data[0]->value == 10 ) ? "selected" : "" ; ?>  >10</option>
                                    <option value="15" <?= $selected =($preferences_data[0]->value == 15 ) ? "selected" : "" ; ?>  >15</option>
                                    <option value="20" <?= $selected =($preferences_data[0]->value == 20 ) ? "selected" : "" ; ?>  >20</option>
                                    <option value="30" <?= $selected =($preferences_data[0]->value == 30 ) ? "selected" : "" ; ?>  >30</option>
                                    <option value="60" <?= $selected =($preferences_data[0]->value == 60 ) ? "selected" : "" ; ?>  >60</option>
                        </select>
                    <? } elseif ( $preferences_data[0]->name == "timezone") {
                        $loop_data   = $general_dal->$dal_function();
                        ?>
                        <select class="<?=$textarea_dimensions?>" id="<?=$column_name?>">
                            <?foreach ($loop_data as $data){
                                if ($preferences_data[0]->value == $data->Name) { $selected = 'selected' ; }
                                ?>
                                <option value="<?=$data->Name?>"  <?=$selected?>  > <?=$data->Name?> </option>
                            <? $selected = ''; } ?>
                        </select>
                    <? } ?>
            <? } elseif ($preferences_defaults_data[0]->type == "textarea") { ?>
                    <? if ( $preferences_defaults_data[0]->default_value == $preferences_data[0]->value && $preferences_data[0]->updated === NULL ) {?>
                        <textarea id="<?=$column_name?>" class="<?=$preferences_data[0]->options?>" placeholder="<?=$preferences_defaults_data[0]->default_value?>"></textarea>
                    <?} else {?>
                        <textarea id="<?=$column_name?>" class="<?=$preferences_data[0]->options?>" ><?=$preferences_data[0]->value?></textarea>
                    <? } ?>
                <? } else { ?>
                Data type '<?=$preferences_defaults_data[0]->type?>' is not yet configured in preferences_functions.php ( edit_preferences).  <?=$message?>
            <? } ?>
        </div>
    <?if (preg_match('/bc_color[0-9]$/', $column_name) || preg_match('/bc_color[0-9]_light$/', $column_name) ) {?>
        <? $count =  $column_name[14] + 0; ?>
        <div class="f_left wp20 hp100 left <?=$column_name?> ">
             <?= $column_name ?>
        </div>
    <?} else {?>
        <div class="f_left wp20 hp100 left textIndent15">
            &nbsp;
        </div>
    <?}?>
        <div class="f_left wp10 hp100">
            <input type="submit" class="button" value="Update" onclick="preferences_UpdAttribute(<?=$company_id?>,'<?=$column_name?>')">
        </div>
    </div>
    <?
}
          function edit_preference_color($preferences_dal,$column_name,$company_id,$numeration,$dal_function=''){
    $preferences_data   = $preferences_dal->get_company_preferences($company_id,$column_name);
    $preferences_defaults_data = $preferences_dal->get_company_preferences_defaults($column_name);

    $textarea_dimensions= $selected = $message = '';
    $insert_dal         = new InsertUpdateDelete_DAL();
    $general_dal        = new GENERAL_DAL();
    if (count($preferences_data) == 0) {
        if (count($preferences_defaults_data) > 0) {
            $sql="insert into preferences (company_id,name,value,type,options,added)
            values (
             ". $company_id . ",
            '" .  $column_name . "',
            "  .  quoteSmart($preferences_defaults_data[0]->default_value) . ",
            '" .  $preferences_defaults_data[0]->type . "',
            '" .  $preferences_defaults_data[0]->options . "',";
            $sql .= " now() )";
            #print $sql; 
            $preference_id = $insert_dal->insert_query($sql);
        } else { $message = "Row $column_name also missing from preferences_defaults table."; }
        $preferences_data   = $preferences_dal->get_company_preferences($company_id,$column_name);
    }
    if ($preferences_defaults_data[0]->type == "textarea") {
        $div_height = 'h150px';
    } else {
        $div_height = 'h25px';
    }
    ?>
    <div class="wp100 <?=$div_height?> d_InlineBlock bctrt ">
        <script type='text/javascript'>
        $(function() {$('#<?=$column_name?>').colorpicker();});
        </script>
        <div class="f_left  wp25 hp100 right no-overflow" title="<?=$column_name?>">
            &nbsp;<?=$column_name?>
        </div>
        <div class="f_left  wp45 hp100 left bclightgray textIndent15">&nbsp;
            <? if ($preferences_defaults_data[0]->type == "boolean") {
                $selected =  ($preferences_data[0]->value == 0 || !$preferences_data[0]->value ) ? "selected" : "" ;
            ?> 
                <select id="<?=$column_name?>" class="<?=$preferences_data[0]->options?>">
                    <option <?=$selected?> value="1">Yes</option>
                    <option <?=$selected?> value="0">No</option>
                </select>
            <? } elseif ($preferences_defaults_data[0]->type == "text") { ?>
                <? if ( ($preferences_defaults_data[0]->default_value == $preferences_data[0]->value) && $preferences_data[0]->updated === NULL ) {?>
                <input placeholder="<?=$preferences_defaults_data[0]->default_value?>" id="<?=$column_name?>" class="<?=$preferences_data[0]->options?>" type="<?=$preferences_data[0]->input_type?>">
                <?} else {?>
                <input value="<?=$preferences_data[0]->value?>" id="<?=$column_name?>" class="<?=$preferences_data[0]->options?>" type="<?=$preferences_data[0]->input_type?>">
                <? } ?>
            <? } elseif ($preferences_defaults_data[0]->type == "dropdown") {
                    if ( $preferences_data[0]->name == "appointment_slot_interval") { ?>
                        <select class="<?=$textarea_dimensions?>" id="<?=$column_name?>">
                                    <option value="5"  <?= $selected =($preferences_data[0]->value == 5  ) ? "selected" : "" ; ?>  >5</option>
                                    <option value="10" <?= $selected =($preferences_data[0]->value == 10 ) ? "selected" : "" ; ?>  >10</option>
                                    <option value="15" <?= $selected =($preferences_data[0]->value == 15 ) ? "selected" : "" ; ?>  >15</option>
                                    <option value="20" <?= $selected =($preferences_data[0]->value == 20 ) ? "selected" : "" ; ?>  >20</option>
                                    <option value="30" <?= $selected =($preferences_data[0]->value == 30 ) ? "selected" : "" ; ?>  >30</option>
                                    <option value="60" <?= $selected =($preferences_data[0]->value == 60 ) ? "selected" : "" ; ?>  >60</option>
                        </select>
                    <? } elseif ( $preferences_data[0]->name == "timezone") {
                        $loop_data   = $general_dal->$dal_function();
                        ?>
                        <select class="<?=$textarea_dimensions?>" id="<?=$column_name?>">
                            <?foreach ($loop_data as $data){
                                if ($preferences_data[0]->value == $data->Name) { $selected = 'selected' ; }
                                ?>
                                <option value="<?=$data->Name?>"  <?=$selected?>  > <?=$data->Name?> </option>
                            <? $selected = ''; } ?>
                        </select>
                    <? } ?>
            <? } elseif ($preferences_defaults_data[0]->type == "textarea") { ?>
                    <? if ( $preferences_defaults_data[0]->default_value == $preferences_data[0]->value && $preferences_data[0]->updated === NULL ) {?>
                        <textarea id="<?=$column_name?>" class="<?=$preferences_data[0]->options?>" placeholder="<?=$preferences_defaults_data[0]->default_value?>"></textarea>
                    <?} else {?>
                        <textarea id="<?=$column_name?>" class="<?=$preferences_data[0]->options?>" ><?=$preferences_data[0]->value?></textarea>
                    <? } ?>
            <? } else { ?>
                Data type '<?=$preferences_defaults_data[0]->type?>' is not yet configured in preferences_functions.php ( edit_preferences).  <?=$message?>
            <? } ?>
        </div>
    <?      if ( preg_match('/bc_color[0-9]$/', $column_name) ) {?>
                <? $count =  $column_name[14] + 0; ?>
                <div class="f_left wp20 hp100 left main_bc_color<?=$numeration?> main_color<?=$numeration?>_text">
                     <?= $column_name ?>
                </div>
    <?} elseif ( preg_match('/bc_color[0-9]_light$/', $column_name)  ) {?>
                <? $count =  $column_name[14] + 0; ?>
                <div class="f_left wp20 hp100 left main_bc_color<?=$numeration?>_light main_color<?=$numeration?>_light_text">
                     <?= $column_name ?>
                </div>
    <?} else {?>
        <div class="f_left wp20 hp100 left textIndent15">
            &nbsp;
        </div>
    <?}?>
        <div class="f_left wp10 hp100">
            <input type="submit" class="button" value="Update" onclick="preferences_UpdAttribute(<?=$company_id?>,'<?=$column_name?>')">
        </div>
    </div>
    <?
}