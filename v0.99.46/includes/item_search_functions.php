<?php 
include_once('general_functions.php');
include_once('reports_functions.php');
include_once('profiles_functions.php');

class ITEM_SEARCH_DAL {
  public function __construct(){}
  public function get_AllItemsANDServices_by_CompanyId($company_id,$totals,$divisor){
    $barcode_id='';
    if ($totals == 0) {
        $sql = "SELECT i.*,
                count(di.delivery_id) as count,
                c.name as category_name,
                c.attribute1 as cat_attribute1_name,
                c.attribute2 as cat_attribute2_name,
                iim.image_id, iim.image_db_id";
    } 
    ELSE {
        $sql ="SELECT count(distinct(i.barcode)) as count ";
    }
  $sql.= " from items i
            left join item_image_mappings iim on i.id=iim.id and iim.deleted is NULL and iim.default_item_image = 1
            left join categories c on i.category_id=c.id
            left join delivery_items di on i.id = di.item_id

            where i.company_id = $company_id and i.deleted is NULL";
    if (
        ( isset($_SESSION['search_data']['item_search']['item_search_exclude_qty_zero'])        && $_SESSION['search_data']['item_search']['item_search_exclude_qty_zero'] == 1 && !isset($_SESSION['search_data']['item_search']['item_search_item_barcode']) && ( isset($_SESSION['search_data']['item_search']['item_search_exclude_services']) && $_SESSION['search_data']['item_search']['item_search_exclude_services'] != 1 ) ) or 
        (!isset($_SESSION['search_data']['item_search']['item_search_exclude_qty_zero']) ) 
       )          {$sql .= " and ( (i.quantity > 0 || i.type = 2) ) "; }
    if ( isset($_SESSION['search_data']['item_search']['item_search_exclude_services'])         && $_SESSION['search_data']['item_search']['item_search_exclude_services'] == 1 )           {$sql .= " and ( i.type != 2) "; }
    if ( isset($_SESSION['search_data']['item_search']['item_search_exclude_items'])            && $_SESSION['search_data']['item_search']['item_search_exclude_items'] == 1 )              {$sql .= " and ( i.type != 1) "; }

    if ( isset($_SESSION['search_data']['item_search']['item_search_category'])                 && $_SESSION['search_data']['item_search']['item_search_category'] != -1 )                  {$sql .= " and ( i.category_id      in (" . $_SESSION['search_data']['item_search']['item_search_category'] . ")) "; }
    if ( isset($_SESSION['search_data']['item_search']['item_search_brand'])                    && $_SESSION['search_data']['item_search']['item_search_brand'] != -1 )                     {$sql .= " and ( i.brand_id         in (" . $_SESSION['search_data']['item_search']['item_search_brand'] . ")) "; }
    if ( isset($_SESSION['search_data']['item_search']['item_search_supplier'])                 && $_SESSION['search_data']['item_search']['item_search_supplier'] != -1 )                  {$sql .= " and ( i.supplier_id      in (" . $_SESSION['search_data']['item_search']['item_search_supplier'] . ")) "; }
    if ( isset($_SESSION['search_data']['item_search']['item_search_department'])               && $_SESSION['search_data']['item_search']['item_search_department'] != -1 )                {$sql .= " and ( i.department_id    in (" . $_SESSION['search_data']['item_search']['item_search_department'] . ")) "; }
    if ( isset($_SESSION['search_data']['item_search']['item_search_styleNumber'])              && $_SESSION['search_data']['item_search']['item_search_styleNumber'] != -1 )               {$sql .= " and ( i.number           =  '" . $_SESSION['search_data']['item_search']['item_search_styleNumber'] . "') "; }
    ### Keyword Section  ### Keyword Section  ### Keyword Section  ### Keyword Section  ### Keyword Section
    if ( isset  (
                    $_SESSION['search_data']['item_search']['item_search_item_keyword']) &&
                    $_SESSION['search_data']['item_search']['item_search_item_keyword'] != -1
                ){
                    $sql .= " and ( i.name      like '%" . $_SESSION['search_data']['item_search']['item_search_item_keyword'] . "%' or
                                    i.style     like '%" . $_SESSION['search_data']['item_search']['item_search_item_keyword'] . "%' or
                                    i.barcode   like '%" . $_SESSION['search_data']['item_search']['item_search_item_keyword'] . "%' or
                                    i.number    like '%" . $_SESSION['search_data']['item_search']['item_search_item_keyword'] . "%'
                                   )";
        }
    ### Bar-code Section  ### Bar-code Section  ### Bar-code Section  ### Bar-code Section  ### Bar-code Section
    if ( isset($_SESSION['search_data']['item_search']['item_search_item_barcode']) && $_SESSION['search_data']['item_search']['item_search_item_barcode'] != -1 )  {
            $barcode_id         = "=".$_SESSION['search_data']['item_search']['item_search_item_barcode'];
            $styleNumber_data   = $this->get_styleNumber_by_barcode($company_id,$_SESSION['search_data']['item_search']['item_search_item_barcode']);
            if ( count($styleNumber_data) > 0 ) {
                $sql .= " and (i.barcode = '" . $_SESSION['search_data']['item_search']['item_search_item_barcode'] . "' or i.number = '" . $styleNumber_data[0]->style_number . "' ) ";
            } else {
                $sql .= " and (i.barcode = '" . $_SESSION['search_data']['item_search']['item_search_item_barcode'] . "'                                                            ) ";
            }
    }
    ### Item Name Section  ### Item Name Section  ### Item Name Section  ### Item Name Section  ### Item Name Section
    if ( isset($_SESSION['search_data']['item_search']['item_search_item_name'])    && $_SESSION['search_data']['item_search']['item_search_item_name'] != -1 )     {$sql .= " and (i.name like '%" . $_SESSION['search_data']['item_search']['item_search_item_name'] . "%' ) "; }
    #  The paging part.
    if ($totals == 0) {
        $sql.= " group by i.id ";
        if (!(isset($_SESSION['search_data']['column']))) { $sql.= " order by i.barcode".$barcode_id." desc"; }
        else { $sql.= " order by " . $_SESSION['search_data']['column'] . " " . $_SESSION['search_data']['asc_desc'] . " "; }
        
        if ( isset($_SESSION['search_data']['paging_page']) && $_SESSION['search_data']['paging_page'] != 0) {
            if ( $_SESSION['search_data']['paging_page'] == 1) { $limit_offset = 0; }
            else { $limit_offset = ( ($_SESSION['search_data']['paging_page'] - 1) * $divisor ) ; }
            $sql .= " limit $limit_offset,$divisor";
        }
    }
    #if ($totals == 0){ print $sql . "\n"; }
    return $this->query($sql);
  }
  public function get_AllImagesPerItemId($item_id){
        $sql = "SELECT * from item_image_mappings where id = $item_id and image_type_id in (3,8) and deleted is NULL";
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
      $sql = "SELECT image_id
                from item_image_mappings
                where customer_id = $customer_id and
                `default` = 1 and
                deleted is null
                order by default_item_image desc, default_group_image desc, added asc
                limit 1";
    #print "$sql";
    return $this->query($sql);
  }
  public function get_styleNumber_by_barcode($company_id,$barcode_id){
      $sql = "SELECT number as style_number,style,company_id from items where barcode = '$barcode_id' and company_id = $company_id and Deleted is NULL ;";
    #print $sql ;
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

function item_search() {
?>

<script src="includes/sales_functions.js" type="text/javascript"></script>

<div class="ReportsTopRow main_bc_color2 main_color2_text"><a href="#" title="Item Search" onclick="mainDiv('item_search'); return false;">Item Search</a></div>
    <div style="max-height: 1000px;" class="f_left wp100 hp94">
        <div class="leftSpace main_bc_color2 main_color2_text">&nbsp;</div>
        <div class="middleSpace wp96">
            <div class="d_InlineBlock ItemSearchHeader hp10 bcwhite">
                <div class="f_left wp100 hp100">
                    <?item_search_topSearch()?>
                </div>
            </div>
            <div class="d_InlineBlock wp100 hp90" >
                <div class="f_left wp15 hp100">
                    <div class="d_InlineBlock wp100 hp100" >
                        <?=ItemSearchStanza() ?>
                    </div>
                </div>
                <div class="f_right wp85 hp100">
                    <div class="d_InlineBlock wp100 hp100" >
                        <div id="item_searchBodyCenter" class="InventoryMgmtBodyCenter wp100 hp100">
                        <?=ItemSearchResultsStanza()?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="rightSpace main_bc_color2 main_color2_text">&nbsp;</div>
    </div>
<div class="ReportsBottomRow main_bc_color2 main_color2_text">&nbsp;</div>
<?
}
function item_search_topSearch(){
?>
<div class="d_InlineBlock wp100 hp100">
    <div class="f_left wp50 hp80 vmiddle mt10">
        Search by Keyword
        <input type="text" maxlength="300" size="30" id="dynamic_pannel_item_keyword" placeholder="Enter an item's keyword here." x-webkit-speech>
    </div>
    <div class="f_left wp10 hp80 vmiddle mt10">
        <input class="button s08 wp90" type="submit" value="Search" onclick="Inventory_ItemSearch_searchBy('item_search');">
    </div>
    <div class="f_left wp40 hp80 vmiddle mt10" id="item_search_messages">
        &nbsp;
    </div>
</div>
<?
}

function ItemSearchResultsStanza() {
?>
        <div class="wp95 hp100 d_InlineBlock">
            <div class="wp100 hp07" id="listing_search_paging_top">
                <? showPaging(); ?>
            </div>
            <div class="wp100 hp85 scrolling">
                <? ItemSearchHeader(); ?>
                <? ItemSearchAllItems(); ?>
            </div>
            <div class="wp100 hp07" id="listing_search_paging_bottom">
                <? showPaging(); ?>
            </div>
        </div>
<?
}
  function ItemSearchHeader() {
?>
<div class="box5">
    <div class="d_InlineBlock lh20 s07 wp100 HEADER main_bc_color1 main_color1_text center">
        <div class="report_header_cell_wp15"><a onclick="orderBy('name','item_search'); return false;">Name</a></div>
        <div class="report_header_cell_wp15"><a onclick="orderBy('name','attribute1'); return false;">Attributes</a></div>
        <div class="report_header_cell_wp20"><a onclick="orderBy('name','style'); return false;">Description</a></div>
        <div class="report_header_cell_wp07"><a onclick="orderBy('price','item_search'); return false;">Price</a></div>
        <div class="report_header_cell_wp08"><a onclick="orderBy('quantity','item_search'); return false;">Quantity</a></div>
        <div class="report_header_cell_wp15"><a onclick="orderBy('iim.image_id','item_search'); return false;">Img</a></div>
        <div class="report_header_cell_wp08"><a onclick="orderBy('status','item_search'); return false;">Status</a></div>
        <div class="report_header_cell_wp08">&nbsp;</div>
    </div>
</div> 
<? 
}
  function ItemSearchAllItems() {
$items_dal = new ITEM_SEARCH_DAL();
$Profiles_DAL   = new Profiles_DAL();

$itemsANDservices = $items_dal->get_AllItemsANDServices_by_CompanyId($_SESSION['settings']['company_id'],0,3);
    $altClass = "bctr1a";  
    if (count($itemsANDservices) >0 ) {
        foreach($itemsANDservices as $itemORserviceData){
        $serviceInfo    = $Profiles_DAL->get_ServiceStatus_byLoginId($_SESSION['settings']['login_id'],$itemORserviceData->id);
        $employee_price = money2($serviceInfo[0]->employee_price);
        if (count($serviceInfo) > 0 && $serviceInfo[0]->employee_price != 0 ) {
            $default_price_css = "strikethrough";
            $employee_price = "Your Price<br>" . money2($serviceInfo[0]->employee_price);
        } else {
            $employee_price = '';
            $default_price_css = '';
        }
        

        if     ($itemORserviceData->archived == 1) {   $status_action = "INactive"; $status_class = "red"    ;
                                            $action = 1; $alt="Activate?";}
        elseif ($itemORserviceData->archived == 0) {   $status_action = "Active"  ; $status_class = "green"  ;
                                            $action = 0; $alt="DeActivate Login?";}
        if ( isset($_SESSION['search_data']['item_search']['item_search_item_barcode']) && $_SESSION['search_data']['item_search']['item_search_item_barcode'] == $itemORserviceData->barcode )  {
            $altClass = "bclightpink";
        }
        ?>
        <div class="box5">
            <div class="<?=$altClass?> center h20 d_InlineBlock s07 wp100 h85px">
                <div class="d_InlineBlock hp100 report_data_cell_wp15">
                    <div class="f_left wp100 hp20 ml2 no-overflow">BC#:&nbsp;<?=$itemORserviceData->barcode?></div>
                    <div class="f_left wp100 hp20 ml2 no-overflow">Style  #:&nbsp;<?=$itemORserviceData->number?></div>
                    <div class="f_left wp100 hp40 ml2 no-overflow">&nbsp;<input type="button" onclick="label(<?=$itemORserviceData->id?>, 180, 1);" value="LABEL" class="button w70"></div>
                </div>
                <div class="f_left hp100 report_data_cell_wp15 left">
                    <div class="f_left wp100 hp20 left ml2 no-overflow">Category:&nbsp;<?=$itemORserviceData->category_name?></div>
                    <div class="f_left wp100 hp20 left ml2 no-overflow">Name:    &nbsp;<?=$itemORserviceData->name?></div>
                    <div class="f_left wp100 hp20 left ml2 no-overflow"><?=$itemORserviceData->cat_attribute1_name?>:&nbsp;<?=$itemORserviceData->attribute1?></div>
                    <div class="f_left wp100 hp20 left ml2 no-overflow"><?=$itemORserviceData->cat_attribute2_name?>:&nbsp;<?=$itemORserviceData->attribute2?></div>
                    <div class="f_left wp100 hp20 left ml2 no-overflow">Deliveries:&nbsp;<?=$itemORserviceData->count?></div>
                </div> 
                <div class="f_left hp100 report_data_cell_wp20 scrolling">
                    &nbsp;<?=$itemORserviceData->style?>
                </div>
                <div class="f_left hp100 report_data_cell_wp07">
                    <div class="d_inlineBlock wp100 hp50 <?=$default_price_css?>"><?=money2($itemORserviceData->price)?></div>
                    <div class="d_inlineBlock wp100 hp50 s07"><?=$employee_price?></div>
                </div>
                <div class="f_left hp100 report_data_cell_wp08">
                <? if ( $itemORserviceData->type == 1 ) { ?>
                    <div class="hp50 wp100 s17">&nbsp;<?=$itemORserviceData->quantity?></div>
                <? } else { ?>
                    <div class="hp50 wp100 s08">Unlimited<br>Service</div>
                <? } ?>
                    <div class="hp50 wp100 s08">&nbsp;
                        <? if ( !(isset($_SESSION['sale']['finish']) && $_SESSION['sale']['finish'] != 1) && !isset($_SESSION['sale']['receipt_id']) ) {?>
                            <? if (!isset($_SESSION['sale']['basket']['items'][$itemORserviceData->id])) {?>
                            <img onclick="Sales_Add_Item(<?=$itemORserviceData->id?>)" 
                                 id="Item_Search_Add_Item_<?=$itemORserviceData->id?>" 
                                 src="/common_includes/includes/images/add_to_cart.png" 
                                 title="Add this item to Sale" >
                            <? } else { ?>
                            <img  
                                 id="Item_Search_Add_Item_<?=$itemORserviceData->id?>" 
                                 src="/common_includes/includes/images/added_to_cart.png" 
                                 title="This item is alredy added to the Sale" >
                            <? } ?>
                        <? } ?>
                    </div>
                </div>
                <div class="f_left hp100 report_data_cell_wp15">
                    <?list ($IMG_HTML_data, $raw_img_location) = show_ItemOrServiceIMG($_SESSION['settings']['company_id'],$itemORserviceData->id,$itemORserviceData->number,$itemORserviceData->image_id,$itemORserviceData->image_db_id,100,80);?>
                    <?=$IMG_HTML_data?>
                </div>
                <div class="f_left hp100 report_data_cell_wp08">
                    <input alt="<?=$alt?>"  type="submit" value="<?=$status_action?>" class="button s09 <?=$status_class?>">
                </div>
                <div class="f_left hp100 report_data_cell_wp08">
                    <? if ($itemORserviceData->type == 1) { ?>
                        <input onclick="Inventory_Items_Edit_Item(<?=$itemORserviceData->id?>)"     type="submit" value="EDIT" class="button">
                    <? } elseif ($itemORserviceData->type == 2) { ?>
                        <input onclick="Inventory_Items_Edit_Service(<?=$itemORserviceData->id?>)"  type="submit" value="EDIT" class="button">
                    <? } ?>
                </div> 
            </div> 
        </div>
        <?
        if ($altClass == "bctr1a") { $altClass = "bctr1b"; } else {$altClass = "bctr1a";}
        }
    } else { ?>
            <div class="center h20 f_left s07 wp100">
                <div class="f_left hp100 report_data_cell_wp99">Your query returned zero results. <a href="#" title="Item Search" onclick="mainDiv('item_search'); return false;" class="mp">Clear Search Data and Retry?</a>.</div>
            </div>
     <? }
}

function ItemSearchStanza() {
$reportType = 'item_search';
?>
    <div id="item_SearchStanza" class="hp100 wp100 InventoryMgmtBodyCenter">
        <div class="wp95 hp100 d_InlineBlock">
            <?=item_search_div('item_barcode','text',$reportType,09)?>
            <?=item_search_div('item_name','text',$reportType,09)?>
            <?=item_search_div("item_category","text",$reportType,09,"Inventory_ItemSearch_searchBy('item_search')")?>
            <?=item_search_div("item_styleNumber","text",$reportType,09,"Inventory_ItemSearch_searchBy('item_search')")?>
            <?=item_search_div("item_brand","text",$reportType,09,"Inventory_ItemSearch_searchBy('item_search')")?>
            <?=item_search_div("item_supplier","text",$reportType,09,"Inventory_ItemSearch_searchBy('item_search')")?>
            <?=item_search_div("item_department","text",$reportType,09,"Inventory_ItemSearch_searchBy('item_search')")?>
            <?=item_search_div('miscellaneous','checkbox',$reportType,11)?>
            <?=item_search_div('submit','checkbox',$reportType,08)?>
        </div>
    </div>
<? 
}
    function item_search_div($search_by_field,$data_type,$reportType,$height_percent,$OnClickAction=0){
    if (isset($_SESSION['search_data']['item_search']['item_search_exclude_qty_zero']) && $_SESSION['search_data']['item_search']['item_search_exclude_qty_zero'] != 1)   { $exclude_qty_zero_checked = "checked"; } else {$exclude_qty_zero_checked = "";}
    if (isset($_SESSION['search_data']['item_search']['item_search_exclude_services']) && $_SESSION['search_data']['item_search']['item_search_exclude_services'] == 0)   { $exclude_services_checked = "checked"; } else {$exclude_services_checked = "";}
    if (isset($_SESSION['search_data']['item_search']['item_search_exclude_items'])    && $_SESSION['search_data']['item_search']['item_search_exclude_items'] == 0)      { $exclude_items_checked    = "checked"; } else {$exclude_items_checked = "";}
    ?>
    <div class="d_InlineBlock mb5 bctrt wp100 hp<?=$height_percent?>" >
        <?if ($search_by_field == 'item_barcode' || $search_by_field == 'item_name' ) { ?>
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
                    onkeyup="if (this.value.length == 12) { Inventory_ItemSearch_searchBy('item_search'); }"
                    x-webkit-speech>
            </div>
        <? } elseif  ( ($search_by_field == 'item_category' ) ) { ?>
            <div id="item_search_<?=$search_by_field?>" class="d_InlineBlock wp100 hp60 s07 center">
               <? dynamic_pannel_advanced_search_Categories($OnClickAction);?>
            </div>
        <? } elseif  ( ($search_by_field == 'item_brand' ) ) { ?>
            <div id="item_search_<?=$search_by_field?>" class="d_InlineBlock wp100 hp60 s07 center">
               <? dynamic_pannel_advanced_search_Brands($OnClickAction);?>
            </div>
        <? } elseif  ( ($search_by_field == 'item_supplier' )  ) { ?>
            <div id="item_search_<?=$search_by_field?>" class="d_InlineBlock wp100 hp60 s07 center">
               <? dynamic_pannel_advanced_search_Suppliers($OnClickAction);?>
            </div>
        <? } elseif  ( ($search_by_field == 'item_department' ) ) { ?>
            <div id="item_search_<?=$search_by_field?>" class="d_InlineBlock wp100 hp60 s07 center">
               <? dynamic_pannel_advanced_search_Departments($OnClickAction);?>
            </div>
        <? } elseif  ( ($search_by_field == 'item_styleNumber' ) ) { ?>
            <div id="item_search_<?=$search_by_field?>" class="d_InlineBlock wp100 hp60 s07 center">
               <? dynamic_pannel_advanced_search_styleNumber($OnClickAction);?>
            </div>
        <? } elseif  ( ($search_by_field == 'miscellaneous' ) ) { ?>
            <div class="f_left wp100 hp30">
                &nbsp;<?=ucfirst($search_by_field)?>
            </div>                
            <div class="d_InlineBlock f_left wp100 hp70">
                <div class="f_left wp33 hp100">
                    <div class="hp40 s06">Qty 0</div>
                    <div class="hp50"><input type='checkbox' id="dynamic_pannel_exclude_qty_zero" value='1' onclick="Inventory_ItemSearch_searchBy('item_search');" <?=$exclude_qty_zero_checked?> ></div>
                </div>
                <div class="f_left wp33 hp100">
                    <div class="hp40 s06">Services</div>
                    <div class="hp50"><input type='checkbox' id="dynamic_pannel_exclude_services" value='1' onclick="Inventory_ItemSearch_searchBy('item_search');" <?=$exclude_services_checked?> ></div>
                </div>
                <div class="f_left wp33 hp100">
                    <div class="hp40 s06">Items</div>
                    <div class="hp50"><input type='checkbox' id="dynamic_pannel_exclude_items"    value='1' onclick="Inventory_ItemSearch_searchBy('item_search');" <?=$exclude_items_checked?> ></div>
                </div>
            </div>
        <? } elseif  ( ($search_by_field == 'submit' ) ) { ?>
            <div class="f_left hp100 wp100">
                <input class="button s08 wp90" type="submit" value="Search" onclick="Inventory_ItemSearch_searchBy('<?=$reportType?>');">
            </div>
        <? } ?>
    </div>
    <?}