<?php
function   deliveries(){
?>
<div class="ReportsTopRow main_bc_color2 main_color2_text"><a href="#" title="Deliveries" onclick="mainDiv('Deliveries_AllDeliveries')">Deliveries</a></div>
    <div style="max-height: 1000px;" class="f_left wp100 hp94">
        <div class="leftSpace main_bc_color2 main_color2_text">&nbsp;</div>
        <div class="middleSpace wp96">
            <div class="profileHeader hp10">
                <div class="f_left left wp65 hp100">
                    <img alt="" height="45" src="/common_includes/includes/images/icon_profiles_50.jpg">
                    <? if (isset($_SESSION['delivery']['supplier_id']) ) { ?>Add New Delivery<?}
                    else { ?> Deliveries History <?}?>
                </div>
                <div class="f_right right wp35 hp100">
                    <? if (!isset($_SESSION['delivery']['supplier_id']) && $_SESSION['settings'][$_SESSION['settings']['login_id']]['level'] >= 1) { ?>
                        <select class='s08' id='delivery_supplier_id'>
                            <option value='0'>- SELECT A SUPPLIER -</option>
                            <?
                            $dal = new INVENTORY_DAL();
                            $rows = $dal->deliveries_GetSuppliers($_SESSION['settings']['company_id']);
                            foreach($rows as $row) { ?>
                                <option value='<?=$row->id?>'><?=$row->name?></option>
                            <?}?>
                        </select>
                        <input class='button' type='submit' value='CREATE DELIVERY' onclick="Deliveries_createDelivery()"/>
                    <? } ?>
                        &nbsp;
                </div>
            </div>
            <? if (!isset($_SESSION['delivery']['supplier_id']) && !isset($_SESSION['delivery']['delivery_id'])) {?>
            <div class="d_InlineBlock hp90 wp100">
                <div class="wp100 hp100" >
                    <div class="f_left wp15 hp100">
                        <div class="d_InlineBlock wp100 hp100" >
                                Search
                        </div>
                    </div>
                    <div class="f_right wp85 hp100">
                        <div class="d_InlineBlock wp100 hp100" id="Deliveries_AllDeliveriesBodyCenter">
                            <?deliveriesStanza()?>
                        </div>
                    </div>
                </div>
            </div>
            <?}
            if (isset($_SESSION['delivery']['supplier_id'])) { ?>
                    <div id="Deliveries_DeliveryDetails_master" class="Deliveries_DeliveryDetailsMaster">
                        <div id="Deliveries_Details" class="Deliveries_Details b1s f_left">
                            <? Deliveries_Details();?>
                        </div>
                        <div id="Deliveries_TotalsSummary" class="Deliveries_TotalsSummary b1s f_right">
                            <? Deliveries_TotalsSummary();?>
                        </div>
                    </div>
                <? if (isset($_SESSION['delivery']['DeliveryInfoComplete']) && $_SESSION['delivery']['DeliveryInfoComplete'] ==1 ) { ?>
                    <div id="showItems_for_Delivery" class="pb5 Deliveries_ShowDeliveryItems d_InlineBlock wp100 b1s">
                        <? Deliveries_ShowItems();?>
                    </div>
                    <div style="display:inline-block; width:756px; height:5px;"></div>
                    <? Deliveries_AddStyleNumber(); ?>
                <?}?>
            <?}?>
            <?if (isset($_SESSION['delivery']['delivery_id'])) { ?>
                <div id="Deliveries_DeliveryDetails_master" class="Deliveries_DeliveryDetailsMaster bclightgray ">
                    <div id="Deliveries_Details" class="Deliveries_Details b1s f_left">
                        <? Deliveries_Details();?>
                    </div>
                    <div id="Deliveries_TotalsSummary" class="Deliveries_TotalsSummary b1s f_right">
                        <? Deliveries_TotalsSummary();?>
                    </div>
                </div>
                <?
                Deliveries_DeliveryDetailsItems();
                unset($_SESSION['delivery']['delivery_id']);
            }?>
        </div>
        <div class="rightSpace main_bc_color2 main_color2_text">&nbsp;</div>
    </div>
<?
}
    function deliveriesStanza(){
    ?>
            <div class="wp100 hp07" id="listing_search_paging_top">
                <? showPaging(); ?>
            </div>
            <div class="d_InlineBlock wp100 hp85">
                <? Deliveries_deliveryList();?>
            </div>
            <div class="wp100 hp07" id="listing_search_paging_bottom">
                <? showPaging(); ?>
            </div>
    <?
    }

  function Deliveries_deliveryList() {?>
        <?= Deliveries_deliveryListHeader()?>
        <?= Deliveries_deliveriesRows()?>
<?}
    function Deliveries_deliveryListHeader() { ?>
                    <div class="profiles_profile_header HEADER main_bc_color1 main_color1_text wp100">
                        <div class="f_left report_header_cell_wp03">#</div>
                        <div class="f_left report_header_cell_wp15">ADDED</div>
                        <div class="f_left report_header_cell_wp08">RECEIVED</div>
                        <div class="f_left report_header_cell_wp15">SUPPLIER</div>
                        <div class="f_left report_header_cell_wp05">ITEMS</div>
                        <div class="f_left report_header_cell_wp07">SHIPPING</div>
                        <div class="f_left report_header_cell_wp07">BUY PRICE</div>
                        <div class="f_left report_header_cell_wp07">SELL REVENUE</div>
                        <div class="f_left report_header_cell_wp07">PROFIT</div>
                        <div class="f_left report_header_cell_wp10">RECEIVER</div>
                        <div class="f_left report_header_cell_wp12">DETAILS?</div>
                    </div>
<?
}
    function Deliveries_deliveriesrows(){
        $dal = new INVENTORY_DAL();
        $rows = $dal->Deliveries_LatestDeliveryList($_SESSION['settings']['company_id']);
        $altClass = "bctr1a";
        if (count($rows) >0 ) {
           $rownum = 1;
                foreach($rows as $row) {
                    $Inventory_dal = new INVENTORY_DAL();
                    $Delivery_data = $Inventory_dal->deliveries_GetDeliveryInfo($row->id);
                    ?>
                    <div class="profileRow wp100 lh20 <?=$altClass?>">
                        <div class="report_data_cell_wp03">&nbsp;<?=$rownum++?></div>
                        <div class="report_data_cell_wp15">&nbsp;<?=$row->added?></div>
                        <div class="report_data_cell_wp08">&nbsp;<?=$row->received?></div>
                        <div class="report_data_cell_wp15">&nbsp;<?=$row->name?></div>
                        <div class="report_data_cell_wp05">&nbsp;<?=$Delivery_data[0]->quantity?></div>
                        <div class="report_data_cell_wp07">&nbsp;<?=money2($row->shipping_costs)?></div>
                        <div class="report_data_cell_wp07">&nbsp;<?=money2($Delivery_data[0]->total_buy_price + $row->shipping_costs)?></div>
                        <div class="report_data_cell_wp07">&nbsp;<?=money2($Delivery_data[0]->total_sell_price )?></div>
                        <div class="report_data_cell_wp07">&nbsp;<?=money2($Delivery_data[0]->total_sell_price - $Delivery_data[0]->total_buy_price)?></div>
                        <div class="report_data_cell_wp10">&nbsp;<?=$row->receiver?></div>
                        <div class="report_data_cell_wp12">&nbsp;<input type='button' class='button' value='DETAILS' onclick=Deliveries_deliveryDetails(<?=$row->id?>)></div>
                    </div>
                    <? if ($altClass == "bctr1a") { $altClass = "bctr1b"; } else {$altClass = "bctr1a";} ?>
                <?}?>
            <?} else { ?>
                    <div class=" wp100 center">There are no delivery records currently in the database.</div>
            <? }
    }

  function Deliveries_Details(){
$dal            = new GENERAL_DAL();
$Inventory_DAL  = new INVENTORY_DAL();
if ($_SESSION['delivery']['done'] == 1){
    $deliveryInfo   = $Inventory_DAL->deliveries_GetDeliveryInfo($_SESSION['delivery']['delivery_id']);
    $_SESSION['delivery']['shipping_costs'] = $deliveryInfo[0]->shipping_costs;
}
?>
<div class='mb15 bold s14 blue'>
    <a class='' href='javascript: none();' onclick='Deliveries_UpdateDeliveryInfo()'>Delivery Details</a>
</div>
<table class='mb20'>
    <tr>
        <td id='failed_delivery_ordered' class='left bold s08 pl10'>
          ORDERED (DATE):
        </td>
        <td class='left pr10
            <? if ($_SESSION['delivery']['done'] == 1) { ?>
                s08 '><?=$deliveryInfo[0]->ordered?>
            <? } else { ?>
                '><input type='text' class='w75' id='delivery_ordered' value='<?=replace_ticks2(empty($_SESSION['delivery']['ordered']) ? date('Y-m-d') : $_SESSION['delivery']['ordered'])?>'/>
            <? } ?>
        </td>
        <td id='failed_delivery_invoice_no' class='left bold s08'>
          INVOICE NO:
        </td>
        <td class='
            <? if ($_SESSION['delivery']['done'] == 1) { ?>
                s08 '><?=$deliveryInfo[0]->invoice_no?>
            <? } else { ?>
                '><input type='text' class='w75' id='delivery_invoice_no' value='<?=replace_ticks2($_SESSION['delivery']['invoice_no'])?>'/>
            <? } ?>
        </td>
    </tr>

    <tr>
        <td style="height: 2px">
        </td>
    </tr


    <tr>
        <td id="failed_delivery_shipped" class='left bold s08 pl10'>
          SHIPPED (DATE):
        </td>
        <td class='left s08 pb5'>
            <? if ($_SESSION['delivery']['done'] == 1) { ?>
                <?=$deliveryInfo[0]->shipped?>
            <? } else { ?>
                <input type='text' class='w75' id='delivery_shipped' value='<?=replace_ticks2(empty($_SESSION['delivery']['shipped']) ? date('Y-m-d') : $_SESSION['delivery']['shipped'])?>'/>
            <? } ?>
        </td>
        <td id="failed_delivery_delivered_via" class='left bold s08 '>
            DELIVERED VIA:
        </td>
        <td class='left pt10 s08 pb5'>
        <? if ($_SESSION['delivery']['done'] == 1) { ?>
            <?=$deliveryInfo[0]->delivered_via?>
        <? } else { ?>
            <input type='text' class='w75' id='delivery_delivered_via' value='<?=replace_ticks2($_SESSION['delivery']['delivered_via'])?>'/>
        <? } ?>
        </td>
    </tr>

    <tr>
        <td style="height: 2px">
        </td>
    </tr>
    <tr>
        <td colspan='2'>
          &nbsp;
        </td>
        <td id="failed_delivery_shipping_costs" class='left bold s08'>
          SHIPPING COSTS<?=isset($_SESSION['delivery']['done']) ? '' : ' (' . $_SESSION['preferences']['currency'] . ')'?>:
        </td>
        <td class='left s08 pb5'>
        <? if ($_SESSION['delivery']['done'] == 1) { ?>
            <?=money2($deliveryInfo[0]->shipping_costs)?>
        <? } else { ?>
            <input type='text' class='w40' id='delivery_shipping_costs' value='<?=replace_ticks2($_SESSION['delivery']['shipping_costs'])?>'/>
        <? } ?>
        </td>
    </tr>

    <tr>
        <td style="height: 2px">
        </td>
    </tr>
    <tr>
        <td id="failed_delivery_received" class='left bold s08 pl10 '>
          RECEIVED (DATE):
        </td>
        <td class='left s08'>
            <? if ($_SESSION['delivery']['done'] == 1) { ?>
                <?=$deliveryInfo[0]->received?>
            <? } else { ?>
                <input type='text' class='w75' id='delivery_received' value='<?=replace_ticks2(empty($_SESSION['delivery']['received']) ? date('Y-m-d') : $_SESSION['delivery']['received'])?>'/>
            <? } ?>
        </td>
        <td id="failed_delivery_receiver_id" class='left bold s08 '>
          RECEIVED BY:
        </td>
        <td class='left s08'>
            <? if ($_SESSION['delivery']['done'] == 1) { ?>
                <? echo $deliveryInfo[0]->receiver_id; ?>
            <? } else { ?>
                <select id='delivery_receiver_id'>
                  <option value='0'>-please select-</option>
                    <?
                    $rows = $dal->get_AllEmployeesPerCompanyId($_SESSION['settings']['company_id'],1);
                    foreach ($rows as $row) { ?>
                        <option value='<?=$row->id?>'<?=$row->id == $_SESSION['delivery']['receiver_id'] ? ' selected' : ''?>><?=$row->username?></option>
                    <? } ?>
                </select>
            <? } ?>
        </td>
    </tr>

    <tr>
        <td style="height: 2px">
        </td>
    </tr>
    <tr>
        <td colspan='2' class='left bold s07 orange pl10 '>
          <? if ($_SESSION['delivery']['done'] != 1) { ?>
            NOTICE: date format is 'YYYY-MM-DD'
          <? } ?>
        </td>
        <td id="failed_delivery_purchase_order_no" class='left bold s08 '>
          PURCHASE ORDER NO:
        </td>
        <td class='left s08'>
          <? if ($_SESSION['delivery']['done'] == 1) { ?>
            <?=$deliveryInfo[0]->purchase_order_no?>
          <? } else { ?>
            <input type='text' class='w75' id='delivery_purchase_order_no' value='<?=replace_ticks2($_SESSION['delivery']['purchase_order_no'])?>'/>
          <? } ?>
        </td>
    </tr>

    <? if ($_SESSION['delivery']['done'] != 1) { ?>
    <tr>
        <td style="height: 2px">
        </td>
    </tr>
    <tr>
        <td colspan='4' class='center left bold s11 pl10 '>
          <input type="button" onclick='Deliveries_UpdateDeliveryInfo()' value="UPDATE DELIVERY INFO" class="button">
        </td>
    </tr>
    <? } ?>
</table>
<?
}
  function Deliveries_TotalsSummary(){
if (isset($_SESSION['delivery']['delivery_id'])) {
    $Inventory_DAL  = new INVENTORY_DAL();
    $TotalsSummary  = $Inventory_DAL->deliveries_TotalsSummary($_SESSION['delivery']['delivery_id']);

    $supplier_name  = $TotalsSummary[0]->supplier_name;
    $quantity_total = $TotalsSummary[0]->quantity_total;
    $buy_total      = $TotalsSummary[0]->buy_total;
    $sell_total     = $TotalsSummary[0]->sell_total;
    $shipping_costs = $_SESSION['delivery']['shipping_costs'];
    $calculated_profit = $TotalsSummary[0]->sell_total - ($TotalsSummary[0]->buy_total + $_SESSION['delivery']['shipping_costs']);
}
else {
    $General_DAL  = new GENERAL_DAL();
    $SupplierInfo = $General_DAL->get_SupplierInfoPerSupplierId($_SESSION['delivery']['supplier_id']);
    $supplier_name  = $SupplierInfo[0]->supplier_name;
    $quantity_total = 0;
    if(isset($_SESSION['delivery']['items'])) {
        foreach (array_keys($_SESSION['delivery']['items']) as $item_id) {
            $item = $_SESSION['delivery']['items'][$item_id];
            $quantity_total =  $quantity_total   +   $_SESSION['delivery']['items'][$item_id]['quantity'] ;
            $buy_total      =  $buy_total        + ( $_SESSION['delivery']['items'][$item_id]['quantity'] * $_SESSION['delivery']['items'][$item_id]['buy_price']);
            $sell_total     =  $sell_total       + ( $_SESSION['delivery']['items'][$item_id]['quantity'] * $_SESSION['delivery']['items'][$item_id]['sell_price']);
        }
    }
    $shipping_costs = $_SESSION['delivery']['shipping_costs'];
    $calculated_profit = ($sell_total - $buy_total);
}
?>
            <div class='mb15 bold s14 blue'>
                <a class='' href='javascript: none();'>Delivery Totals Summary</a>
            </div>
            <table>
                <tr>
                  <td class='s08 left bold  pb5'>Supplier Name:</td>
                  <td class='s08 right pb5'><?=$supplier_name?></td>
                </tr>
                <tr>
                  <td class='s08 left bold  pb5'>TOTAL ITEM COUNT OF THIS DELIVERY:</td>
                  <td class='s08 right pb5'><?=$quantity_total?></td>
                </tr>
                <tr>
                  <td class='s08 left bold  pb5'>TOTAL PRICE OF ALL ITEMS:</td>
                  <td class='s08 right pb5'><?=money2($buy_total)?></td>
                </tr>
                <tr>
                  <td class='s08 left bold  pb5'>CALCULATED SELL REVENUE:</td>
                  <td class='s08 right pb5'><?=money2($sell_total)?></td>
                </tr>
                <tr>
                  <td class='s08 left bold '>Shipping Costs:</td>
                  <td class='s08 right'><?=money2($shipping_costs)?></td>
                </tr>

                <tr>
                  <td class='s08 left bold  pb5'>CALCULATED PROFIT:</td>
                  <td class='s08 right pb5'><?=money2($calculated_profit)?></td>
                </tr>
                <tr>
                  <td class='s08 left  pb5'>
                      <? if ( isset($_SESSION['delivery']['supplier_id']) ) { ?>
                        <input type="button" class="button" value="Cancel Delivery" onclick="Deliveries_Cancel_Delivery()">
                      <? } ?>
                      &nbsp;
                  </td>
                  <td class='s08 right pb5'>
                      <? if (isset($_SESSION['delivery']['DeliveryInfoComplete']) && $_SESSION['delivery']['DeliveryInfoComplete'] ==1 ) { ?>
                      <input type="button" class="button" value="Complete Delivery" onclick="Deliveries_AddNewDelivery()">
                      <? } ?>
                      &nbsp;
                  </td>
                </tr>
            </table>
<?
}

  function Deliveries_ShowItems() {
?>
        <div class="d_InlineBlock wp100">
            <div class="f_left left wp100 pl10 s13 bold">Items Included in this delivery.</div>
        </div>
        <div class="HEADER main_bc_color1 main_color1_text d_InlineBlock wp100">
            <div class="f_left wp10">Style #</div>
            <div class="f_left wp15">Name</div>
            <div class="f_left wp15">Attribute1</div>
            <div class="f_left wp15">Attribute2</div>
            <div class="f_left wp04">POS Qty</div>
            <div class="f_left wp15">Barcode</div>
            <div class="f_left wp08">Buy Price</div>
            <div class="f_left wp08">Sell Price</div>
            <div class="f_left wp02">&nbsp;</div>
            <div class="f_left wp04">Delivery Qty</div>
            <div class="f_left wp02">&nbsp;</div>
        </div>
<?
    if (count($_SESSION['delivery']['items']) >0 ){
        $dal = new INVENTORY_DAL();
        foreach($_SESSION['delivery']['items'] as $item_id => $value)
        {
        $item = $dal->deliveries_ItemsInfoByItemID($item_id);
        ?>
        <div class="d_InlineBlock wp100">
            <div class="f_left wp10" style="background-color: lightgrey;">&nbsp;<?=$item[0]->style_number?></div>
            <div class="f_left wp15" style="background-color: lightcyan;">&nbsp;<?=$item[0]->name?></div>
            <div class="f_left wp15" style="background-color: lightgrey;">&nbsp;<?=$item[0]->attribute1?></div>
            <div class="f_left wp15" style="background-color: lightblue;">&nbsp;<?=$item[0]->attribute2?></div>
            <div class="f_left wp04" style="background-color: lightcyan;">&nbsp;<?=$item[0]->quantity?></div>
            <div class="f_left wp15" style="background-color: lightblue;">&nbsp;<?=$item[0]->barcode?></div>
            <div class="f_left wp08" style="background-color: lightgrey;"><?=$_SESSION['delivery']['items'][$item_id]['buy_price']?></div>
            <div class="f_left wp08" style="background-color: lightgrey;"><?=$_SESSION['delivery']['items'][$item_id]['sell_price']?></div>
            <div class="f_left wp02">
                <a class="menu" onclick="Deliveries_PendingItem_increase_decrease('decrease','<?=$item_id?>')">
                    <img height="11" width="11" title="Decrease" src="/common_includes/includes/images/minus_sign.jpg">
                </a>
            </div>
            <div class="f_left wp04" style="background-color: lightgrey;"><?=$_SESSION['delivery']['items'][$item_id]['quantity']?></div>
            <div class="f_left wp02">
                <a class="menu" onclick="Deliveries_PendingItem_increase_decrease('increase','<?=$item_id?>')">
                    <img height="11" width="11" title="Increase" src="/common_includes/includes/images/plus_sign.jpg">
                </a>
            </div>
        </div>
        <?
        }
    } else {
    ?>
        <div style="display:inline-block; width:100%;">
            <div class="left pl10">
            There aren't any items added to this "Delivery" yet.
            </div>
        </div>
    <?}
}
  function Deliveries_DeliveryDetailsItems() {
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
        <td width='30'  class='bctrt b1sr b1st b1sb'><input class='button' type='button' value='LABELS' onclick='label(<?=$_SESSION['delivery']['delivery_id']?>, <?=$_SESSION['preferences']['label_width']?>, 0, 1);'/></td>
<?}?>
      </tr>
<?
$total = $count = $totalcount = 0;
    foreach (array_keys($_SESSION['delivery']['items']) as $id) {
        $bcclass = 'bctr1' . ($count++ % 2 == 1 ? 'a' : 'b');
        $total += $_SESSION['delivery']['items'][$id]['buy_price'] * $_SESSION['delivery']['items'][$id]['quantity'];
        $totalcount += $_SESSION['delivery']['items'][$id]['quantity'];
        if ( isset($_SESSION['edit_item']['item_id']) && $_SESSION['edit_item']['item_id'] == $_SESSION['delivery']['items'][$id]['id'] ){
            $bcclass = 'bcTextBoxErr';
        }
        ?>
        <tr>
            <td class='<?=$bcclass?> s08 p5 b1sl b1sr b1sb'><?=$_SESSION['delivery']['items'][$id]['style_number']?></td>
            <td class='<?=$bcclass?> s08 p5 b1sl b1sr b1sb'><?=$_SESSION['delivery']['items'][$id]['barcode']?></td>
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
  function Deliveries_AddStyleNumber(){
$Inventory_DAL  = new INVENTORY_DAL();
$TotalsSummary  = $Inventory_DAL->deliveries_TotalsSummary($_SESSION['delivery']['delivery_id']);
if ($_SESSION['delivery']['done'] != 1) {
    $General_DAL  = new GENERAL_DAL();
    $SupplierInfo = $General_DAL->get_SupplierInfoPerSupplierId($_SESSION['delivery']['supplier_id'],$_SESSION['settings']['company_id']);
}
?>
<div id="Deliveries_AddStyleNumber" class="Deliveries_AddStyleNumber b1s">
    <div class='pr5 s1 blue inline f_left'>Change Style</div>
    <div id="Deliveries_StyleNumberDropDown" class='bold s1 blue inline f_left'>
        <? Deliveries_select_StyleNumber(); ?>
    </div>
    <div class='HEADER main_bc_color1 main_color1_text inline mr200'>DELIVERY Add Style Number</div>
</div>

<div id="style_number" class="Deliveries_AddItem b1s">
    <div id="Deliveries_ItemsPerCurrentStyleNumber" class="left ">
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Please Select from the choices of Styles to add to the current delivery.
    </div>
</div>
<?}
  function Deliveries_select_StyleNumber($method='SupplierIDandCompanyID') { ?>
<div class='left'>
    <select id='selectStyleNumber' onchange='Deliveries_selectStyleNumber();'>
        <option value=''>- all style numbers -</option>
        <?
        $dal = new GENERAL_DAL();
        $StyleNumbers = $dal->get_StyleNumbersBySupplierIDandCompanyID($_SESSION['delivery']['supplier_id'],$_SESSION['settings']['company_id']);

        foreach ( $StyleNumbers as $StyleNumber) { ?>
            <option value='<?=$StyleNumber->number?>'<?=isset($_SESSION['delivery']['item_number']) && $_SESSION['delivery']['item_number'] == $StyleNumber->number ? ' selected' : ''?>><?=$StyleNumber->number?></option>
        <? } ?>
    </select>
</div>
<?
}
  function Deliveries_showStyleNumber_for_Additions() {
    $dal = new INVENTORY_DAL();
    $items = $dal->deliveries_ItemsPerStyleNumber($_SESSION['delivery']['active_style_number']);
?>
    <div style="display:inline-block; width:100%;" class="HEADER main_bc_color1 main_color1_text">
        <div class="f_left wp25">Name</div>
        <div class="f_left wp15">Attribute1</div>
        <div class="f_left wp15">Attribute2</div>
        <div class="f_left wp04">Qty</div>
        <div class="f_left wp08">P. Buy</div>
        <div class="f_left wp08">P. Sell</div>
        <div class="f_left wp09">Buy Price</div>
        <div class="f_left wp09">Sell Price</div>
        <div class="f_left wp04">Qty</div>
        <div class="f_left wp02">&nbsp;+</div>
    </div>
<?
foreach ( $items as $item) { ?>
    <div style="display:inline-block; width:100%;">
        <div class="f_left wp25" style="background-color: lightcyan;"><?=$item->name?></div>
        <div class="f_left wp15" style="background-color: lightgrey;">&nbsp;<?=$item->attribute1?></div>
        <div class="f_left wp15" style="background-color: lightblue;">&nbsp;<?=$item->attribute2?></div>
        <div class="f_left wp04" style="background-color: lightcyan;"><?=$item->quantity?></div>
        <div class="f_left wp08" style="background-color: lightblue;"><?=$item->buy_price?></div>
        <div class="f_left wp08" style="background-color: lightgrey;"><?=$item->price?></div>
        <div class="f_left wp09" style="background-color: lightgrey;"><input id="delivery_NewItem_buy_price_<?=$item->id?>" value="<?=$item->buy_price?>" type="text" class="w40 "></div>
        <div class="f_left wp09" style=" background-color: lightgrey;"><input id="delivery_NewItem_sell_price_<?=$item->id?>" value="<?=$item->price?>" type="text" class="w40 "></div>
        <div class="f_left wp04" style="background-color: lightgrey;"><input id="delivery_NewItem_quantity_<?=$item->id?>" value="1" type="text" class="w20 "></div>
        <div class="f_left wp02">
            <? if(!isset($_SESSION['delivery']['items'][$item->id])) { ?>
            <a class="menu" onclick="add_deleteDeliveryItem('<?=$item->id?>')">
                <img id="delivery_NewItem_action_img_<?=$item->id?>" height="11" width="11" title="Add this item to delivery" src="/common_includes/includes/images/plus_sign.jpg">
            </a>
            <? } else { ?>
                <img id="delivery_NewItem_action_img_<?=$item->id?>" height="11" width="11" title="Item Already Added, Edit in Box above." src="/common_includes/includes/images/checkbox_red_med.jpg">
            <? } ?>
        </div>
    </div>
    <? } ?>
    <div style="display:inline-block; width:100%;">
        <div class="pl10" style="float:left; text-align: left; width:auto;">
            <img height="20" width="20" title="" src="/common_includes/includes/images/arrow_down.jpg">
            Add New Item For Style Number <font color="red">"<?=$_SESSION['delivery']['active_style_number']?>"</font>
            <img height="20" width="20" title="" src="/common_includes/includes/images/arrow_down.jpg">
        </div>
    </div>
    <div style="display:inline-block; width:100%;">
        <div class="f_left wp25" style="background-color: lightcyan;"><?=$item->name?></div>
        <div class="f_left wp15" style="background-color: lightgrey;"><input id="delivery_CreateItem_attribute1" type="text" style="width:60px;"></div>
        <div class="f_left wp15" style="background-color: lightblue;"><input id="delivery_CreateItem_attribute2" type="text" style="width:60px;"></div>
        <div class="f_left wp04" style="background-color: lightcyan;">----</div>
        <div class="f_left wp08" style="background-color: lightblue;">----</div>
        <div class="f_left wp08" style="background-color: lightgrey;">----</div>
        <div class="f_left wp09" style="background-color: lightgrey;"><input id="delivery_CreateItem_buy_price" type="text" style="width:40px;"></div>
        <div class="f_left wp09" style="background-color: lightgrey;"><input id="delivery_CreateItem_sell_price" type="text" style="width:40px;"></div>
        <div class="f_left wp04" style="background-color: lightgrey;"><input id="delivery_CreateItem_quantity" type="text" class="w20 "></div>
        <div class="f_left wp02">
            <a class="menu" onclick="this.onclick=function(){return false};  createNewItemByStyleNumber('<?=$_SESSION['delivery']['active_style_number']?>')">
                <img height="15" width="15" title="Create this New Item for <?=$_SESSION['delivery']['active_style_number']?>" src="/common_includes/includes/images/save.png">
            </a>
        </div>
    </div>
<?
}
?>