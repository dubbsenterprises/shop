<?php
function   inventory(){
?>
<div class="ReportsTopRow main_bc_color2 main_color2_text"><a href="#" title="Deliveries" onclick="mainDiv('Inventory_AllInventoryRuns')">Inventory Run</a></div>
    <div style="max-height: 1000px;" class="f_left wp100 hp94">
        <div class="leftSpace main_bc_color2 main_color2_text">&nbsp;</div>
        <div class="middleSpace wp96">
            <div class="profileHeader hp10">
                <div class="f_left">
                    <img alt="" height="45" src="/common_includes/includes/images/icon_profiles_50.jpg">
                    Inventory Runs
                </div>
                <div class="f_right">
                    <? if ($_SESSION['settings'][$_SESSION['settings']['login_id']]['level'] >= 1) { ?>
                    <a onclick="Inventory_Create_Run()">
                        <img alt="" height="45" src="/common_includes/includes/images/group-user-add.png" class="mp">
                    </a>
                    <? } ?>
                </div>
            </div>
                <?
                if (!isset($_SESSION['inventory_run']['created_by_login_id']) && !isset($_SESSION['inventory_run']['inventory_run_id'])) {?>
                    <div class="d_InlineBlock hp90 wp100">
                        <div class="wp100 hp100" >
                            <div class="f_left wp15 hp100">
                                <div class="d_InlineBlock wp100 hp100" >
                                        Search
                                </div>
                            </div>
                            <div class="f_right wp85 hp100">
                                <div class="d_InlineBlock wp100 hp100" id="Inventory_AllInventoryRunsBodyCenter">
                                <?Inventory_InventoryRunStanza();?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?} else if (isset($_SESSION['inventory_run']['created_by_login_id'])) { ?>
                    <div id="Deliveries_DeliveryDetails_master" class="Deliveries_DeliveryDetailsMaster">
                        <div id="Inventory_Run_Details" class="Inventory_Run_Details b1s f_left">
                            <? Inventory_Details();?>
                        </div>
                        <div id="Inventory_TotalsSummary" class="Inventory_TotalsSummary b1s f_right">
                            <? Inventory_TotalsSummary();?>
                        </div>
                    </div>
                <? if (isset($_SESSION['inventory_run']['RunInfoComplete']) && $_SESSION['inventory_run']['RunInfoComplete'] ==1  ) { ?>
                    <div class="wp100 b1s bctrt left">
                        <div class='inline wp100 bold'>
                            Items in the Current Inventory Run.
                        </div>
                    </div>
                    <div id="showItems_for_Inventory" class="pb5 Inventory_ShowDeliveryItems">
                        <? Inventory_ShowItems();?>
                    </div>
                    <div class="d_InlineBlock wp100 hp05">
                        <?=Inventory_AddStyleNumber();?>
                    </div>
                <? } ?>
                <?}
                ##################################################
                ##################################################
                else if (isset($_SESSION['inventory_run']['inventory_run_id'])) { ?>
                    <div id="Deliveries_DeliveryDetails_master" class="Deliveries_DeliveryDetailsMaster ">
                        <div id="Inventory_Run_Details" class="Inventory_Run_Details b1s f_left bclightgray">
                            <? Inventory_Details();?>
                        </div>
                        <div id="Inventory_TotalsSummary" class="Inventory_TotalsSummary b1s f_right bclightgray">
                            <? Inventory_TotalsSummary();?>
                        </div>
                    </div>
                    <div id="showItems_for_Inventory" class=" d_InlineBlock bs2 wp100 pb5 Inventory_ShowDeliveryItems">
                        <? Inventory_ShowItems();?>
                    </div>
                    <? unset($_SESSION['inventory_run']['inventory_run_id']);
                }?>
            </div>
        <div class="rightSpace main_bc_color2 main_color2_text">&nbsp;</div>
    </div>
<?
}
  function Inventory_InventoryRunStanza(){
?>
            <div class="wp100 hp07" id="listing_search_paging_top">
                <? showPaging(); ?>
            </div>
            <div class="d_InlineBlock wp100 hp85">
                <?Inventory_Inventory_RunList();?>
            </div>
            <div class="wp100 hp07" id="listing_search_paging_bottom">
                <? showPaging(); ?>
            </div>
<?}

  function Inventory_Inventory_RunList() {?>
        <?= Inventory_Inventory_RunHeader()?>
        <?= Inventory_Inventory_RunRows()?>
<?}
    function Inventory_Inventory_RunHeader() { ?>
                    <div class="profiles_profile_header HEADER main_bc_color1 main_color1_text wp100">
                        <div class="f_left report_header_cell_wp03">#</div>
                        <div class="f_left report_header_cell_wp15">ADDED</div>
                        <div class="f_left report_header_cell_wp17">Added by</div>
                        <div class="f_left report_header_cell_wp17">Assigned to</div>
                        <div class="f_left report_header_cell_wp05">ITEMS</div>
                        <div class="f_left report_header_cell_wp07">Balance</div>
                        <div class="f_left report_header_cell_wp07">POS Qty</div>
                        <div class="f_left report_header_cell_wp07">Inv Run Qty</div>
                        <div class="f_left report_header_cell_wp07">Issues</div>
                        <div class="f_left report_header_cell_wp12">DETAILS?</div>
                    </div>
<?
}
    function Inventory_Inventory_RunRows(){
        $dal = new INVENTORY_DAL();
        $rows = $dal->Inventory_get_latest_Inventory_Runs($_SESSION['settings']['company_id'],0);
        $altClass = "bctr1a";
        if (count($rows) >0 ) {
           $rownum = 1;
                foreach($rows as $row) {
                    $INVENTORY_DAL = new INVENTORY_DAL();
                    $inventory_run_data = $INVENTORY_DAL->Inventory_Inventory_RunDetails($row->id);
                    if ($inventory_run_data[0]->remaining_items >0) { $remaining_items_bg_color = 'bcyellow'; } else { $remaining_items_bg_color = ''; }
                    ?>
                    <div class="profileRow wp100 lh20 <?=$altClass?>">
                        <div class="report_data_cell_wp03">&nbsp;<?=$rownum++?></div>
                        <div class="report_data_cell_wp15 no-overflow">&nbsp;<?=$row->added?></div>
                        <div class="report_data_cell_wp17 no-overflow">&nbsp;<?=$row->l2_fn?>&nbsp;<?=$row->l2_ln?></div>
                        <div class="report_data_cell_wp17 no-overflow">&nbsp;<?=$row->l1_fn?>&nbsp;<?=$row->l1_ln?></div>
                        <div class="report_data_cell_wp05">&nbsp;<?=$inventory_run_data[0]->items_count?></div>
                        <div class="report_data_cell_wp07 <?=$remaining_items_bg_color?>"><?=$inventory_run_data[0]->remaining_items?></div>
                        <div class="report_data_cell_wp07">&nbsp;<?=$inventory_run_data[0]->pos_quantity?></div>
                        <div class="report_data_cell_wp07">&nbsp;<?=$inventory_run_data[0]->quantity?></div>
                        <div class="report_data_cell_wp07">&nbsp;<?=abs($inventory_run_data[0]->pos_quantity - $inventory_run_data[0]->quantity);?></div>
                        <div class="report_data_cell_wp12">&nbsp;
                            <? if ( $row->remaining_items != 0 ) {?>
                                <input type='button' class='button' value='EDIT'      onclick=Inventory_InventoryRun_Details(<?=$row->id?>)>
                            <? } else {?>
                                <input type='button' class='button' value='VIEW' onclick=Inventory_InventoryRun_Details(<?=$row->id?>)>
                            <? } ?>
                        </div>
                    </div>
                    <? if ($altClass == "bctr1a") { $altClass = "bctr1b"; } else {$altClass = "bctr1a";} ?>
                <?}?>
            <?} else { ?>
                    <div class=" wp100 center">No Inventory Run was found matching the criteria.</div>
            <? }
    }

  function Inventory_DetailsItems() {

$Inventory_DAL  = new INVENTORY_DAL();
$rows   = $Inventory_DAL->deliveries_GetDeliveryDetailsItems($_SESSION['delivery']['delivery_id']);
unset($_SESSION['delivery']['items']);
    foreach($rows as $row) {
        $_SESSION['delivery']['items'][$row->id]['id'] = $row->id;
        $_SESSION['delivery']['items'][$row->id]['style_number'] = $row->number;
        $_SESSION['delivery']['items'][$row->id]['item_name'] = $row->item_name;
        $_SESSION['delivery']['items'][$row->id]['brand_name'] = $row->brand_name;
        $_SESSION['delivery']['items'][$row->id]['attribute1'] = $row->attribute1;
        $_SESSION['delivery']['items'][$row->id]['attribute2'] = $row->attribute2;

        $_SESSION['delivery']['items'][$row->id]['buy_price'] = $row->buy_price;
        $_SESSION['delivery']['items'][$row->id]['sell_price'] = $row->sell_price;
        $_SESSION['delivery']['items'][$row->id]['quantity'] = $row->quantity;
        $_SESSION['delivery']['items'][$row->id]['barcode'] = $row->barcode;

    }
?>
    <div class='mb10 mt10 bold s1'>DELIVERY ITEMS</div>
<?
$itemno = 0;
    if (is_array($_SESSION['delivery']['items'])) {
            foreach (array_keys($_SESSION['delivery']['items']) as $item) {
                    $itemno++;
            }
    }

    if ($itemno == 0) { ?>
            <div class='mb30 s09'>There was no item added <?=$_SESSION['delivery']['done'] == 1 ? '' : 'yet '?>for this delivery.</div>
<? } else { ?>
    <table class='mb30'?>
    <tr>
        <td width='50' class='bctrt bold s08 p5 b1sl b1st b1sr b1sb'>Style Number</td>
        <td width='100' class='bctrt bold s08 p5 b1sl b1st b1sr b1sb'>Barcode</td>
        <td width='155' class='bctrt bold s08 p5 b1sl b1st b1sr b1sb'>Item Name</td>
        <td width='100' class='bctrt bold s08 p5 b1sl b1st b1sr b1sb'>Brand Name</td>
        <td width='100' class='bctrt bold s08 p5 b1sl b1st b1sr b1sb'>Attribute #1</td>
        <td width='100' class='bctrt bold s08 p5 b1sl b1st b1sr b1sb'>Attribute #2</td>

        <td width='30'  class='bctrt bold s08 p5 b1st b1sr b1sb'>QTY</td>
        <td width='50'  class='bctrt bold s08 p5 b1st b1sr b1sb'>BUY<br/>PRICE</td>
        <td width='50'  class='bctrt bold s08 p5 b1st b1sr b1sb'>TOTAL<br/>PRICE</td>
        <td width='80'  class='bctrt bold s08 p5 b1st b1sr b1sb'>NEW SELL<br/>PRICE</td>
<? if ($_SESSION['delivery']['done'] != 1) { ?>
        <td width='30'  class='bctrt bold s08 p5 b1st b1sr b1sb'>EDIT?</td>
        <td width='30'  class='bctrt bold s08 p5 b1st b1sr b1sb'>DELETE?</td>
<?} else {?>
        <td width='30'  class='bctrt b1sr b1st b1sb'><input class='button' type='button' value='LABsELSS' onclick='label(<?=$_SESSION['delivery']['delivery_id']?>, <?=$_SESSION['preferences']['label_width']?>, 0, 1);'/></td>
<?}?>
      </tr>
<?
$total = $count = $totalcount = 0;
foreach (array_keys($_SESSION['delivery']['items']) as $id) {
    $bcclass = 'bctr1' . ($count++ % 2 == 1 ? 'a' : 'b');
    $total += $_SESSION['delivery']['items'][$id]['buy_price'] * $_SESSION['delivery']['items'][$id]['quantity'];
    $totalcount += $_SESSION['delivery']['items'][$id]['quantity'];
?>
    <tr>
        <td class='<?=$bcclass?> s08 p5 b1sl b1sr b1sb'><?=$_SESSION['delivery']['items'][$id]['style_number']?></td>
        <td class='<?=$bcclass?> s08 p5 b1sl b1sr b1sb'><img src="../../common_includes/includes/images/icon_profiles_50.jpg"></td>
        <td class='<?=$bcclass?> s08 p5 b1sl b1sr b1sb'><?=$_SESSION['delivery']['items'][$id]['item_name']?></td>
        <td class='<?=$bcclass?> s08 p5 b1sl b1sr b1sb'><?=$_SESSION['delivery']['items'][$id]['brand_name']?></td>
        <td class='<?=$bcclass?> s08 p5 b1sl b1sr b1sb'><?=$_SESSION['delivery']['items'][$id]['attribute1']?></td>
        <td class='<?=$bcclass?> s08 p5 b1sl b1sr b1sb'><?=$_SESSION['delivery']['items'][$id]['attribute2']?></td>

        <td class='<?=$bcclass?> s08 p5 b1sr b1sb'><?=$_SESSION['delivery']['items'][$id]['quantity']?></td>
        <td class='<?=$bcclass?> s08 p5 b1sr b1sb'><?=money2($_SESSION['delivery']['items'][$id]['buy_price'])?></td>
        <td class='<?=$bcclass?> s08 p5 b1sr b1sb'><?=money2($_SESSION['delivery']['items'][$id]['buy_price'] * $_SESSION['delivery']['items'][$id]['quantity'])?></td>
        <td class='<?=$bcclass?> s08 p5 b1sr b1sb'><?=money2($_SESSION['delivery']['items'][$id]['sell_price'])?></td>
    <? if ($_SESSION['delivery']['done'] != 1) { ?>
        <td class='<?=$bcclass?> b1sr b1sb'><input class='button' type='button' value='EDIT' onclick='document.getElementById("delivery_item_id").value = <?=$id?>; document.getElementById("form_action").value = "edit_delivery_item"; document.delivery_form.submit();'/></td>
        <td class='<?=$bcclass?> b1sr b1sb'><input class='button' type='button' value='DELETE' onclick='if (confirm("Do you really want to delete this delivery item?")) { document.getElementById("delivery_item_id").value = <?=$id?>; document.getElementById("form_action").value = "delete_delivery_item"; document.delivery_form.submit(); }'/></td>
    <? } else { ?>
        <td class='<?=$bcclass?> b1sr b1sb'><input class='button w70 left' type='button' value='LABEL<?=$_SESSION['delivery']['items'][$id]['quantity'] > 1 ? 'S' : ''?>' onclick='label(<?=$id?>, <?=$_SESSION['preferences']['label_width']?>, <?=$_SESSION['delivery']['items'][$id]['quantity']?>);'/></td>
    <? } ?>
    </tr>
<? } ?>
</table>
<? }
}
  function Inventory_Details() {
$GENERAL_DAL    = new GENERAL_DAL();
$Inventory_DAL  = new INVENTORY_DAL();
if ($_SESSION['inventory_run']['done'] == 1){
    $Inventory_RunInfo   = $Inventory_DAL->Inventory_InventoryRunInfo($_SESSION['inventory_run']['inventory_run_id']);
}
?>
<div class='mb15 bold s14 blue '>Inventory Run Details</div>
<table class='mb20'>
    <tr>
        <td id='failed_inventory_run_date' class='left s11 pl10'>
          DATE:
        </td>
        <td class='s11 left pr10'>
            <? if ($_SESSION['inventory_run']['done'] == 1) { ?>
                <?=$Inventory_RunInfo[0]->added?>
            <? } else { ?>
                <?=date('Y-m-d')?>
            <? } ?>
        </td>
        <td id='failed_inventory_run_number' class='right s11 pl10'>
          Run Number:
        </td>
        <td class='s11'>
            <? if ($_SESSION['inventory_run']['done'] == 1) { ?>
                <?=$Inventory_RunInfo[0]->run_number?>
            <? } else { ?>
                <i>not yet set</i>
            <? } ?>
        </td>
    </tr>

    <tr>
        <td colspan='2'>
        </td>
        <td id="failed_inventory_run_login_id" class='right s11 pl10 '>
          Run by:
        </td>
        <td class='left s11 pl10'>
        <? if ($_SESSION['inventory_run']['done'] == 1) { ?>
            <?=$Inventory_RunInfo[0]->firstname;?>&nbsp;<?=$Inventory_RunInfo[0]->lastname;?>
        <? } else { ?>
        <select id='inventory_run_login_id' onchange="Inventory_UpdateRunInfo()">
          <option value='0'>-please select-</option>
            <?
            $rows = $GENERAL_DAL->get_AllEmployeesPerCompanyId($_SESSION['settings']['company_id'],1);
            foreach ($rows as $row) { ?>
                <option value='<?=$row->id?>'  <?= isset($_SESSION['inventory_run']['inventory_run_login_id']) && $_SESSION['inventory_run']['inventory_run_login_id'] == $row->id  ? ' selected' : ''?>><?=$row->username?></option>
            <? } ?>
        </select>
        <? } ?>
        </td>
    </tr>

    <tr>
        <td style="height: 5px">
        </td>
    </tr>
    <tr>
      <td  colspan='1' id="failed_inventory_run_notes" class='s11'>
          Rack Name:
      </td>
      <td  colspan='3' class='left s11'>
          <? if ($_SESSION['inventory_run']['done'] == 1) { ?>
            <?=$Inventory_RunInfo[0]->notes?>
          <? } else { ?>
            <input type='textarea' class="wp75" id='inventory_run_notes' value='<?=$_SESSION['inventory_run']['inventory_run_notes']?>'>
          <? } ?>
      </td>
    </tr>

    <? if ($_SESSION['inventory_run']['done'] != 1) { ?>
    <tr>
        <td style="height: 2px">
        </td>
    </tr>
    <tr>
      <td colspan='4' class='center left bold s11 pl10 '>
          <input type="button" onclick='Inventory_UpdateRunInfo()' value="UPDATE RUN INFO" class="button">
      </td>
    </tr>
    <? } ?>

</table>
<?
}

  function Inventory_TotalsSummary(){
  $Inventory_DAL    = new INVENTORY_DAL();
  $General_DAL      = new GENERAL_DAL();
  $Profiles_DAL     = new Profiles_DAL();

    if (isset($_SESSION['inventory_run']['inventory_run_id'])) {
        $Inventory_RunInfo   = $Inventory_DAL->Inventory_InventoryRunInfo($_SESSION['inventory_run']['inventory_run_id']);
        $inventory_run_login_name     = $Inventory_RunInfo[0]->firstname . " " . $Inventory_RunInfo[0]->lastname ;
        $inventory_run_item_total     = $Inventory_RunInfo[0]->inventory_run_item_total;
        $inventory_run_database_total = $Inventory_RunInfo[0]->total_quantity;
        $inventory_run_recorded_total = $Inventory_RunInfo[0]->total_pos_quantity;
        $buy_total = $Inventory_RunInfo[0]->total_pos_quantity * $Inventory_RunInfo[0]->buy_price;
        $sell_total = $Inventory_RunInfo[0]->total_pos_quantity * $Inventory_RunInfo[0]->price;;
        $inventory_run_calculated_profit = ($sell_total - $buy_total);
    }
    else {
        $EmployeeData = $Profiles_DAL->get_EmployeeDataPerLoginId($_SESSION['inventory_run']['inventory_run_login_id']);
        $_SESSION['inventory_run']['RunLoginID_Firstname'] = $EmployeeData[0]->firstname;
        $_SESSION['inventory_run']['RunLoginID_Lastname'] = $EmployeeData[0]->lastname;

        $inventory_run_recorded_total = $inventory_run_database_total = $inventory_run_item_total = $buy_total =$sell_total =0;
        if(isset($_SESSION['inventory_run']['items'])) {
            foreach (array_keys($_SESSION['inventory_run']['items']) as $item_id) {
                $ItemData = $Inventory_DAL->deliveries_ItemsInfoByItemID($item_id);
                $inventory_run_database_total = $inventory_run_database_total + $ItemData[0]->quantity;
                $inventory_run_recorded_total = $inventory_run_recorded_total +   $_SESSION['inventory_run']['items'][$item_id]['quantity'] ;
                $buy_total      =  $buy_total        + ( $_SESSION['inventory_run']['items'][$item_id]['quantity'] * $ItemData[0]->buy_price);
                $sell_total     =  $sell_total       + ( $_SESSION['inventory_run']['items'][$item_id]['quantity'] * $ItemData[0]->price);
                $inventory_run_item_total++;
            }
        }
        if (!isset($_SESSION['inventory_run']['inventory_run_login_id'])){
            $inventory_run_login_name = "<font color=\"red\">Staff not yet set.</font>";
        }
        else {
            $inventory_run_login_name =$_SESSION['inventory_run']['RunLoginID_Firstname'] ." ".$_SESSION['inventory_run']['RunLoginID_Lastname'];
        }
        $inventory_run_calculated_profit= ($sell_total - $buy_total);
    }
    ?>
<div class='mb15 bold s14 blue'>Inventory Run Summary</div>
<div>
    <div class="inline">
      <div class='wp30 f_left s10 left bold pb5'>Staff Name:</div>
      <div class='wp69 f_left s10 right pb5'><?=$inventory_run_login_name?></div>
    </div>
    <div class="inline">
      <div class='wp70 f_left s10 left bold  pb5'>UNIQUE ITEMS IN Inventory Run:</div>
      <div class='wp29 f_left s10 right pb5'><?=$inventory_run_item_total?></div>
    </div>
    <div class="inline">
      <div class='wp70 f_left s10 left bold  pb5'>Recorded ITEM COUNT from Inventory Run:</div>
      <div class='wp29 f_left s10 right pb5'><?=$inventory_run_database_total?></div>
    </div>
    <div class="inline">
      <div class='wp70 f_left s10 left bold  pb5'>Expected ITEM COUNT from Inventory Run:</div>
      <div class='wp29 f_left s10 right pb5'><?=$inventory_run_recorded_total?></div>
    </div>
    <div class="inline">
      <div class='wp50 f_left s10 left bold  pb5'>TOTAL BUY COST:</div>
      <div class='wp49 f_left s10 right pb5'><?=money2($buy_total)?></div>
    </div>
    <div class="inline">
      <div class='wp50 f_left s10 left bold  pb5'>TOTAL SELL REVENUE:</div>
      <div class='wp49 f_left s10 right pb5'><?=money2($sell_total)?></div>
    </div>
    <div class="inline">
      <div class='wp50 f_left s10 left bold  pb5'>CALCULATED PROFIT:</div>
      <div class='wp49 f_left s10 right pb5'><?=money2($inventory_run_calculated_profit)?></div>
    </div>
    <? if ($_SESSION['inventory_run']['done'] != 1) { ?>
    <div class="inline">
      <div class='wp50 f_left s10 left bold  pb5'>&nbsp;
            <input type="button" class="button" value="Cancel Inventory Run" onclick="Inventory_Cancel_Inventory_Run()">
      </div>
      <div class='wp49 f_left s10 right pb5'>&nbsp;
            <? if ( count($_SESSION['inventory_run']['items']) > 0 ) { ?>
            <input type="button" class="button" value="Complete Inventory Run" onclick="Inventory_AddNewInventory_Run()">
            <? } ?>
      </div>
    </div>
    <? } ?>
</div>
<?
}
  function Inventory_ShowItems() {
    Inventory_HeaderDiv();
    $rowcount = '0';
    if ($_SESSION['inventory_run']['done'] == 1) {
        $dal = new INVENTORY_DAL();
        if (isset($_SESSION['inventory_run']['inventory_run_id'])) {
            $Items = $dal->Inventory_GetInventoryDetailsItems($_SESSION['inventory_run']['inventory_run_id']);
        }
        else {
            $Items = $dal->Inventory_GetInventoryDetailsItems($_SESSION['inventory_run_backup']['inventory_run_id']);
        }
        foreach ( $Items as $Item) {
            $_SESSION['inventory_run']['items'][$Item->item_id]['updated'] = $Item->updated;
            $_SESSION['inventory_run']['items'][$Item->item_id]['inventory_run_items_id'] = $Item->inventory_run_items_id;
            $_SESSION['inventory_run']['items'][$Item->item_id]['pos_quantity'] = $Item->total_pos_quantity;
            if (!isset($_SESSION['inventory_run']['items'][$Item->item_id]['quantity'])) {
                $_SESSION['inventory_run']['items'][$Item->item_id]['quantity']     = $Item->total_quantity;
                }
            }
        }

        if (count($_SESSION['inventory_run']['items']) >0 ){
            foreach($_SESSION['inventory_run']['items'] as $item_id => $value)
            {
                $bcclass = 'bctrt' . ($rowcount++ % 2 == 0 ? 'a ' : 'b ');
                $dal = new INVENTORY_DAL();
                $item = $dal->deliveries_ItemsInfoByItemID($item_id);
                if ($item[0]->quantity == $_SESSION['inventory_run']['items'][$item_id]['quantity']){
                     $_SESSION['inventory_run']['items'][$item_id]['matched'] = 1;
                     $qty_bgcolor="bclightgray";
                }
                elseif($item[0]->quantity < $_SESSION['inventory_run']['items'][$item_id]['quantity'] ){
                     $_SESSION['inventory_run']['items'][$item_id]['matched'] = '';
                     $qty_bgcolor="bcyellow";
                }
                elseif($_SESSION['inventory_run']['items'][$item_id]['updated'] != NULL) {
                    $qty_bgcolor="bclightgray";
                    // here i decided to show the quantity the POS had
                    // at time of Inventory Run creation , likely by the manager and assigned to an employee
                    #$_SESSION['inventory_run']['items'][$item_id]['pos_quantity'] = $_SESSION['inventory_run']['items'][$item_id]['quantity'] ;
                }
                else {
                    $qty_bgcolor="bcyellow";
                }
           ?>
            <div class="wp100 d_InlineBlock <?=$bcclass?>">
                <div class="wp10 f_left no-overflow" title="<?=$item[0]->style_number?>">&nbsp;<?=$item[0]->style_number?></div>
                <div class="wp15 f_left">&nbsp;<img class='m0<? if ($item[0]->imageid > 0) { print ' mp'; } ?>' src='showimage.php?id=<?=$item[0]->imageid?>&image_db_id=<?=$item[0]->image_db_id?>&w=100&h=80'<? if ($item[0]->imageid > 0) { ?> height="80" width="80" onclick='window.open("showimage.php?id=<?=$item[0]->imageid?>&image_db_id=<?=$item[0]->image_db_id?>&w=600&h=600", "_new", "innerHeight=620,innerWidth=620,scrollbars=no,toolbar=no,resizable=no,menubar=0");'<? } ?>/></div>
                <div class="wp20 f_left no-overflow" title="<?=$item[0]->name?>" onclick="Inventory_Items_Edit_Item(<?=$item_id?>)">&nbsp;<?=$item[0]->name?><br><?=$item[0]->barcode?></div>
                <div class="wp10 f_left no-overflow" title="<?=$item[0]->attribute1?>">&nbsp;<?=$item[0]->attribute1?></div>
                <div class="wp10 f_left no-overflow" title="<?=$item[0]->attribute2?>">&nbsp;<?=$item[0]->attribute2?></div>
                <div class="wp10 f_left">&nbsp;<?=$item[0]->price?></div>
                <div class="wp06 f_left no-overflow">
                    <?  if (  $_SESSION['inventory_run']['done'] == 1 &&
                            ($_SESSION['inventory_run']['items'][$item_id]['pos_quantity'] != $_SESSION['inventory_run']['items'][$item_id]['quantity']) &&
                            ($_SESSION['inventory_run']['items'][$item_id]['updated'] == NULL)
                          ) {?>
                            <a class="menu" onclick="Inventory_ItemUpdateQuantity(<?=$item_id?>,<?=$_SESSION['inventory_run']['items'][$item_id]['inventory_run_items_id']?>)">
                            <!--<img title="Repair Discrepancy" height="20" src="/common_includes/includes/images/tool-repair-icon.jpg">-->
                            <font class="red">ISSUE</font>
                            </a>
                    <?  }
                        elseif (isset($_SESSION['inventory_run']['items'][$item_id]['updated']) && $_SESSION['inventory_run']['items'][$item_id]['updated'] != NULL) {?>
                            <font class="yellowgreen">FIXED</font>
                    <?}
                        elseif ( $_SESSION['inventory_run']['items'][$item_id]['updated'] != NULL &&
                                 ($_SESSION['inventory_run']['items'][$item_id]['matched'] == 1  )
                                ) {?>
                            <font class="green">MATCH</font>
                    <?}
                        elseif( ($_SESSION['inventory_run']['items'][$item_id]['pos_quantity'] == $_SESSION['inventory_run']['items'][$item_id]['quantity']) &&
                                ($_SESSION['inventory_run']['items'][$item_id]['updated'] == NULL) &&
                                ($_SESSION['inventory_run']['done'] == 1)
                              ) {?>
                            <a class="menu" onclick="Inventory_InventoryRun_SettleItem(<?=$item_id?>,<?=$_SESSION['inventory_run']['items'][$item_id]['inventory_run_items_id']?>)">
                            <font class="black">SETTLE</font>
                            </a>
                    <?}
                        else {?>
                            <input type="button" onclick="label(<?=$item_id?>, 180);" value="LABEL" class=" s07">
                    <?}?>
                </div>
                <div class="wp08 f_left">&nbsp;<?=$_SESSION['inventory_run']['items'][$item_id]['pos_quantity']?></div>
                <div class="wp03 f_left">
                    <? if( !isset($_SESSION['inventory_run']['items'][$item_id]['updated']) ) {?>
                        <a class="menu" onclick="Inventory_PendingItem_increase_decrease('decrease','<?=$item_id?>')">
                            <img height="11" width="11" title="Decrease" src="/common_includes/includes/images/minus_sign.jpg">
                        </a>
                    <? } else { ?>
                        &nbsp;
                    <? } ?>
                </div>
                <div id="Inventory_item_quantity_<?=$item_id?>" class="f_left wp05 <?=$qty_bgcolor?>"><?=$_SESSION['inventory_run']['items'][$item_id]['quantity']?></div>
                <div class="wp03 f_left no-overflow">
                    <? if( !isset($_SESSION['inventory_run']['items'][$item_id]['updated']) ){?>
                        <a class="menu" onclick="Inventory_PendingItem_increase_decrease('increase','<?=$item_id?>')">
                            <img height="11" width="11" title="Increase" src="/common_includes/includes/images/plus_sign.jpg">
                        </a>
                    <? } else { ?>
                        &nbsp;
                    <? } ?>
                </div>
            </div>
            <?
            }
        }
        else {?>
            <div class="wp100 d_InlineBlock ">
                <div class="left f_left pl10">
                    There aren't any items added to this "Inventory Run" yet.
                </div>
                <div class="center f_left pl10 wp50">
                    <div class="ttl">Auto Generate Items:
                    <a onclick="Inventory_AutoCreateItems(25)">25</a> |
                    <a onclick="Inventory_AutoCreateItems(50)">50</a> |
                    <a onclick="Inventory_AutoCreateItems(100)">100</a> |
                    <a onclick="Inventory_AutoCreateItems(200)">200</a>
                    </div>
                </div>

            </div>
        <?}
}
  function Inventory_AddStyleNumber(){

$Inventory_DAL  = new INVENTORY_DAL();
$TotalsSummary  = $Inventory_DAL->deliveries_TotalsSummary($_SESSION['delivery']['delivery_id']);
if ($_SESSION['delivery']['done'] != 1) {
    $General_DAL  = new GENERAL_DAL();
    $SupplierInfo = $General_DAL->get_SupplierInfoPerSupplierId($_SESSION['delivery']['supplier_id'],$_SESSION['settings']['company_id']);
}
?>
<div class="wp100 b1s bctrt">
    <div class='inline wp100 bold'>
        Choose from items below to add to this Inventory Run.
    </div>
</div>
<div id="Inventory_AddStyleNumber" class="Inventory_AddStyleNumber b1s">
    <div id="Inventory_StyleNumberDropDown" class='bold s1 blue inline f_left'>
        <? Inventory_select_StyleNumber('CompanyID'); ?>
    </div>
    <div id="Inventory_StyleNumberDropDown" class='w50 s08 blue inline f_left'>
        - or -
    </div>
    <div id="Inventory_CategoryDropDown" class='bold s1 blue inline f_left'>
        <? Inventory_select_Category('CompanyID'); ?>
    </div>
    <div id="Inventory_StyleNumberDropDown" class='w50 s08 blue inline f_left'>
        - or -
    </div>
    <div class='pr5 s09 inline f_left'>
        Barcode Num.
    </div>
    <div id="Inventory_BarcodeSpecify" class='bold s1 blue inline f_left'>
        <? Inventory_BarcodeSpecify(); ?>
    </div>
    <div class='inline bold'>
        Inventory Add Item
    </div>
</div>

<div id="style_number" class="Deliveries_AddItem b1s">
    <div id="Inventory_ItemsPerCurrentStyleNumber" class="left ">
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Please Select from the choices of Styles, Categories or the Barcode ID to add to the current "Inventory Run".
    </div>
</div>

<?}
  function Inventory_HeaderDiv(){
  ?>
    <div style="display:inline-block; width:100%;" class="HEADER main_bc_color1 main_color1_text">
        <div class="f_left wp10">Style #</div>
        <div class="f_left wp15">Barcode</div>
        <div class="f_left wp20">Name</div>
        <div class="f_left wp10">Attribute1</div>
        <div class="f_left wp10">Attribute2</div>
        <div class="f_left wp10">Sell Price</div>
        <div class="f_left wp06">&nbsp;</div>
        <div class="f_left wp08">POS Qty</div>
        <div class="f_left wp03">&nbsp;</div>
        <div class="f_left wp05">INV Qty</div>
        <div class="f_left wp03">&nbsp;</div>
    </div>
  <?}

  function Inventory_select_StyleNumber() { ?>
<div class='left'>
    <select id='selectStyleNumber' onchange='Inventory_selectStyleNumber();'>
        <option value=''>-Select Style Number-</option>
        <?
        $dal = new GENERAL_DAL();
        $StyleNumbers = $dal->get_StyleNumbersByCompanyID($_SESSION['settings']['company_id']);

        foreach ( $StyleNumbers as $StyleNumber) { ?>
            <option style="width:120px;" value='<?=$StyleNumber->number?>' <?=isset($_SESSION['delivery']['item_number']) && $_SESSION['delivery']['item_number'] == $StyleNumber->number ? ' selected' : ''?>>
                <?=$StyleNumber->number?> (<?=$StyleNumber->quantity?>)
            </option>
        <? } ?>
    </select>
</div>
<?
}
  function Inventory_select_Category() { ?>
<div class='left'>
    <select id='selectCategory' onchange='Inventory_selectCategory();'>
        <option value=''>-Select Category-</option>
        <?
        $dal = new GENERAL_DAL();
        $Categories = $dal->get_CategoriesByCompanyID($_SESSION['settings']['company_id']);

        foreach ( $Categories as $Category) { ?>
            <option style="width:120px;" value='<?=$Category->id?>' <?=isset($_SESSION['delivery']['item_number']) && $_SESSION['delivery']['item_number'] == $Category->id ? ' selected' : ''?>>
                <?=$Category->name?> (<?=$Category->quantity?>)
            </option>
        <? } ?>
    </select>
</div>
<?
}  
  function Inventory_BarcodeSpecify(){
?>
    <div class='left'>
        <input type="textarea" value="" id="Input_Inventory_BarcodeSpecify" class="w90" onkeyup="if (this.value.length == 12) { Inventory_BarcodeSpecify('item_search'); }">
        <a onclick="Inventory_BarcodeSpecify()" class="menu"><img width="11" height="11" title="" src="/common_includes/includes/images/plus_sign.jpg" title="Add item by barcode"></a>
    </div>
    <?
}

  function Inventory_showStyleNumber_for_Additions() {
    $rowcount   = 1;
    $items      = array();
    $INVENTORY_DAL = new INVENTORY_DAL();
    if ( !isset($_SESSION['inventory_run']['active_style_number']) ) {
        $StyleNumber_by_Barcode_Info    = $INVENTORY_DAL->Inventory_StyleNumber_by_Barcode($_SESSION['inventory_run']['active_barcode'],$_SESSION['settings']['company_id']) ;
        $active_style_number            = $StyleNumber_by_Barcode_Info[0]->style_number;
    }
    else {
        $active_style_number = $_SESSION['inventory_run']['active_style_number'] ;
    }
    #  if the $StyleNumber_by_Barcode_Info array has a count higher than zero then get all items of that style number.
    $items = $INVENTORY_DAL->deliveries_ItemsPerStyleNumber($active_style_number);
    Inventory_HeaderDiv();
    if ( count($items) > 0 ) {
        foreach ( $items as $item) {
            $bcclass = 'bctrt' . ($rowcount++ % 2 == 0 ? 'a ' : 'b ');
            if (isset($_SESSION['inventory_run']['items'][$item->id]))          { $bcclass="bclightpink";}
            if ($_SESSION['inventory_run']['active_barcode'] == $item->barcode) { $bcclass="bclightcoral";}
                Inventory_AddToInventoryItemsRows($item,$bcclass);
        }
    }
    else { ?>
        <div class="d_InlineBlock wp100">
            <div class="f_left">&nbsp;Sorry, no items match that barcode.</div>
        </div>
    <?}
}
  function Inventory_showCategory_for_Additions() {
    $active_category_id = $_SESSION['inventory_run']['active_category_id'] ;
    $rowcount = 0;
    $INVENTORY_DAL = new INVENTORY_DAL();
    $items = $INVENTORY_DAL->deliveries_ItemsPerCategory($active_category_id);
    Inventory_HeaderDiv();
    if ( count($items) > 0 && $active_category_id !="") {
            foreach ( $items as $item) {
            $bcclass = 'bctrt' . ($rowcount++ % 2 == 0 ? 'a ' : 'b ');
            if (isset($_SESSION['inventory_run']['items'][$item->id]))          { $bcclass="bclightpink";}
            Inventory_AddToInventoryItemsRows($item,$bcclass);
        }
    }
    else { ?>
        <div style="display:inline-block; width:100%;">
            <div class="f_left">&nbsp;Sorry, no items match that Category.</div>
        </div>
    <?}
}
    function Inventory_AddToInventoryItemsRows($item,$bcclass){
        ?>
        <div id="div_Inventory_NewItem_row_<?=$item->id?>" class="wp100 d_InlineBlock  <?=$bcclass?>">
                <div class="wp10 f_left no-overflow" title="<?=$item->style_number?>">&nbsp;<?=$item->style_number?></div>
                <div class="wp15 f_left">&nbsp;<img class='m0<? if ($item->imageid > 0) { print ' mp'; } ?>' src='showimage.php?id=<?=$item->imageid?>&image_db_id=<?=$item->image_db_id?>&w=100&h=80'<? if ($item->imageid > 0) { ?> height="80" width="80" onclick='window.open("showimage.php?id=<?=$item->imageid?>&image_db_id=<?=$item->image_db_id?>&w=600&h=600", "_new", "innerHeight=620,innerWidth=620,scrollbars=no,toolbar=no,resizable=no,menubar=0");'<? } ?> title="<?=$item->barcode?>"/></div>
                <div class="wp20 f_left no-overflow" title="<?=$item->name?>" onclick="Inventory_Items_Edit_Item(<?=$item->id?>)">&nbsp;<?=$item->name?><br><?=$item->barcode?></div>
                <div class="wp10 f_left no-overflow" title="<?=$item->attribute1?>">&nbsp;<?=$item->attribute1?></div>
                <div class="wp10 f_left no-overflow" title="<?=$item->attribute2?>">&nbsp;<?=$item->attribute2?></div>
                <div class="wp10 f_left">&nbsp;<?=$item->price?></div>
                <div class="wp06 f_left no-overflow"><input type="button" onclick="label(<?=$item->id?>, 180);" value="LABEL" class=" s07"></div>
                <div class="wp08 f_left s2">&nbsp;<?=$item->quantity?></div>
                <div class="wp03 f_left">&nbsp;</div>
                <div id="div_Inventory_NewItem_quantity_<?=$item->id?>" class="f_left wp05">
                    <input id="Inventory_NewItem_quantity_<?=$item->id?>" type="text" value="0" class="w20 center ">
                </div>
                <div class="wp03 f_left no-overflow">
                    <? if(!isset($_SESSION['inventory_run']['items'][$item->id])) { ?>
                    <a class="menu" onclick="Inventory_add_delete_Item('<?=$item->id?>')">
                        <img id="Inventory_NewItem_action_img_<?=$item->id?>" height="11" width="11" title="Add this item to Inventory Run" src="/common_includes/includes/images/plus_sign.jpg">
                    </a>
                    <? } else { ?>
                        <img id="Inventory_NewItem_action_img_<?=$item->id?>" height="11" width="11" title="Item Already Added, Edit in Box above." src="/common_includes/includes/images/checkbox_red_med.jpg">
                    <? } ?>
                </div>
        </div>
    <?}
?>