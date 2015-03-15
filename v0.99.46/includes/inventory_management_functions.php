<?php
require_once('general_functions.php');
require_once('profiles_functions.php');
class INVENTORY_DAL {
  public function __construct(){}
  public function Deliveries_LatestDeliveryList($company_id,$totals=0){
    if ($totals == 0) {
        $sql    ="SELECT    
                    distinct(d.id),
                    convert_tz(d.added, 'utc', ".quoteSmart($_SESSION['preferences']['timezone']).") as added,
                    d.ordered,
                    d.shipped,
                    d.received,
                    s.name,
                    shipping_costs,           
                    l.username as receiver ";
    } ELSE { 
        $sql    ="SELECT    
                    count(d.id) as count ";
    }
        
        $sql   .="FROM deliveries d
                    join suppliers s on d.supplier_id = s.id    
                    join logins l on d.receiver_id = l.id
                  WHERE 
                    s.company_id = $company_id";
    #if ( isset($_SESSION['search_data']['dynamic_pannel_start_date']) &&  $_SESSION['search_data']['dynamic_pannel_start_date']  != 'null' && $_SESSION['search_data']['dynamic_pannel_start_date'] != '' && $_SESSION['search_data']['dynamic_pannel_start_date'] != 'undefined' ) { $sql .= " and s.added >= '" .$_SESSION['search_data']['dynamic_pannel_start_date']."' "; }
    #if ( isset($_SESSION['search_data']['dynamic_pannel_end_date']) &&  $_SESSION['search_data']['dynamic_pannel_end_date']  != 'null' && $_SESSION['search_data']['dynamic_pannel_end_date'] != '' && $_SESSION['search_data']['dynamic_pannel_end_date'] != 'undefined' ) { $sql .= " and s.added <= '" .$_SESSION['search_data']['dynamic_pannel_end_date']."' "; }
    if ($totals == 1) {
        $sql .= " group by d.id ";
        if (!(isset($_SESSION['search_data']['column']))) { $sql.= " order by d.id desc"; }
        else { $sql.= " order by " . $_SESSION['search_data']['column'] . " " . $_SESSION['search_data']['asc_desc'] . " "; }

        if ( isset($_SESSION['search_data']['paging_page']) && $_SESSION['search_data']['paging_page'] != 0) {
            if ( $_SESSION['search_data']['paging_page'] == 1) { $limit_offset = 0; }
            else { $limit_offset = ( ($_SESSION['search_data']['paging_page'] - 1) * 10 ) ; }
            $sql .= " limit $limit_offset,10";
        }
    }
    if ($totals == 0) {
        print $sql;
    }
    return $this->query($sql);
  }
  public function Deliveries_DeliveryList_Per_Item($company_id,$item_id){
        $sql    ="SELECT    
                    distinct(d.id),
                    convert_tz(d.added, 'utc', ".quoteSmart($_SESSION['preferences']['timezone']).") as added,
                    d.ordered,
                    d.shipped,
                    d.received,
                    s.name,
                    shipping_costs,           
                    l.username as receiver ";
        $sql   .="FROM 
                    delivery_items as di
                    join deliveries d on d.id = di.delivery_id
                    join suppliers s on d.supplier_id = s.id    
                    join logins l on d.receiver_id = l.id
                  WHERE 
                    s.company_id = $company_id";
        $sql   .="  and di.item_id = $item_id ";
    return $this->query($sql);
  }

  
  public function deliveries_GetSuppliers($company_id){
    $sql ="SELECT id,name from suppliers where company_id = $company_id and deleted is null order by name";
    #print "$sql";
    return $this->query($sql);
  }
  public function deliveries_GetDeliveryInfo($delivery_id){
    $sql ="SELECT convert_tz(d.added, 'utc', ".quoteSmart($_SESSION['preferences']['timezone']).") as added,d.shipped,d.received,d.invoice_no,d.delivered_via,d.shipping_costs,d.receiver_id,d.purchase_order_no,
            sum(di.buy_price*di.quantity) as total_buy_price ,
            sum(di.sell_price*di.quantity) as total_sell_price ,
            sum(di.quantity) as quantity
            from deliveries as d
            join delivery_items di on d.id =di.delivery_id
            where d.id = $delivery_id 
            group by d.id";
    #print "$sql";
    return $this->query($sql);
  }
  public function deliveries_GetDeliveryDetailsItems($delivery_id){
    $sql ="SELECT i.id, i.number, i.barcode, b.name as brand_name,i.name item_name,i.attribute1,attribute2,di.buy_price, di.sell_price, di.quantity
            from delivery_items as di
            join items as i on i.id = di.item_id
            left join brands as b on b.id = i.brand_id
            where di.delivery_id = $delivery_id ";
    #print "$sql";
    return $this->query($sql);
  }
  public function deliveries_TotalsSummary($delivery_id){
    $sql ="SELECT   sum(di.buy_price * di.quantity) as buy_total, 
                    sum(di.sell_price * di.quantity) as sell_total, 
                    sum(di.quantity) as quantity_total,
                    s.name as supplier_name
                    from delivery_items as di 
                    join items as i on i.id = di.item_id 
                    left join brands as b on b.id = i.brand_id
                    left join suppliers as s on s.id = i.supplier_id
                    where di.delivery_id = $delivery_id
                    group by s.id";
    #print "$sql";
    return $this->query($sql);
  }
  public function deliveries_ItemsPerStyleNumber($style_number){
    $sql ="SELECT i.id,
            number as style_number,
            barcode,
            brand_id,
            department_id,
            category_id,
            tax_group_id,
            i.name,
            attribute1,
            attribute2,
            quantity,
            price,
            buy_price,
            iim.image_id as imageid,
            iim.image_db_id as image_db_id
            from items i
            left join item_image_mappings iim on i.id = iim.id
            where i.number = '$style_number' and i.deleted is NULL
            group by i.id
            order by i.name,i.attribute1,i.attribute1;
      ";
    #print "$sql";
    return $this->query($sql);    
  }
  public function deliveries_ItemsPerCategory($category_id){
    $sql ="SELECT i.id,
            number as style_number,
            barcode,
            brand_id,
            department_id,
            category_id,
            tax_group_id,
            i.name,
            attribute1,
            attribute2,
            quantity,
            price,
            buy_price,
            iim.image_id as imageid,
            iim.image_db_id as image_db_id
            from items i
            left join item_image_mappings iim on i.id = iim.id
            where i.category_id = $category_id and 
            i.deleted is NULL and i.quantity > 0
            group by i.id
            order by i.quantity desc, i.name, i.attribute1,i.attribute1
      ";
    #print "$sql";
    return $this->query($sql);
  }

  public function deliveries_ItemsInfoByItemID($item_id){
    $sql = "SELECT number as style_number,
        barcode,
        price,
        buy_price,
        i.name,
        attribute1,
        attribute2,
        quantity,
        iim.image_id as imageid,
        iim.image_db_id as image_db_id,
        location
        from items i
        left join item_image_mappings iim on i.id = iim.id
        where i.id = $item_id";
    #print "$sql";
    return $this->query($sql);
  }

  public function ItemManagement_GetSuppliers($company_id){
    $sql ="SELECT id,name from suppliers where company_id = $company_id and deleted is null order by name";
    #print "$sql";
    return $this->query($sql);
  }
  public function ItemManagement_GetCategories($company_id){
    $sql ="SELECT id,name from categories where company_id = $company_id and deleted is null order by name";
    #print "$sql";
    return $this->query($sql);
  }
  public function ItemManagement_GetBrands($company_id){
    $sql ="SELECT id,name from brands where company_id = $company_id and deleted is null order by name";
    #print "$sql";
    return $this->query($sql);
  }
  public function ItemManagement_GetDepartments($company_id){
    $sql ="SELECT id,name from departments where company_id = $company_id and deleted is null order by name";
    #print "$sql"; 
    return $this->query($sql);
  }
  public function ItemManagement_GetTaxGroups($company_id){
    $sql ="SELECT id,concat(name,' (',tax,'%)') as name ,tax from tax_groups where company_id = $company_id and deleted is null order by name";
    #print "$sql";
    return $this->query($sql);
  }
  public function ItemManagement_GetCategoryAttributeNames($category_id){
    $sql ="SELECT attribute1,attribute2 from categories where id = $category_id";
    #print "$sql";
    return $this->query($sql);
  }
  public function ItemManagement_ItemLookup_by_Barcode($company_id, $barcode){
    $sql = "SELECT i.id
            from items i join categories c on i.category_id = c.id
            where i.barcode = '$barcode' and c.company_id = $company_id and i.deleted is NULL ;";
    #print "$sql";
    return $this->query($sql);
  }
  public function ItemManagement_get_default_Item_ImageID($item_id,$all=0){
      $sql = "SELECT image_id, image_db_id
                from item_image_mappings
                where id = $item_id and ";
      if ($all == 0 ) { $sql .= " ( `default` = 1 or `default_item_image` = 1 ) and "; }
      $sql .= " deleted is null
                order by default_item_image desc, default_group_image desc, added asc ";
      if ($all == 0 ) { $sql .= " limit 1"; }

                
    #print "$sql";
    return $this->query($sql);
  }
  public function ItemManagement_get_default_Service_ImageID($image_id){
      $sql = "SELECT image_id, image_db_id
                from item_image_mappings
                where id = $item_id and
                ( `default` = 1 or `default_item_image` = 1 ) and
                deleted is null
                order by default_item_image desc, default_group_image desc, added asc
                limit 1";
    #print "$sql";
    return $this->query($sql);
  }

  public function ItemManagement_ItemSaleHistory($item_id){
      $sql = "SELECT
        si.price, si.tax, si.discount, si.additional_discount, si.quantity,
        convert_tz(s.added, 'utc', ".quoteSmart($_SESSION['preferences']['timezone']).") as added
        from sale_items si
        join sales s on si.sale_id = s.id
        where si.item_id = $item_id";
    #print "$sql";
    return $this->query($sql);
  }

  public function ServiceManagement_AllActiveServices($company_id){
      $sql = "  SELECT i.id, i.est_time_mins, i.name as name, i.price, i.attribute1, i.attribute2, i.style, i.category_id, c.name as category_name
                from items i
                left join categories c on c.id = i.category_id
                where i.company_id = $company_id and
                i.type = 2 and
                i.deleted is null
                order by i.category_id";
    #print "$sql";
    return $this->query($sql);
  }
  public function ServiceManagement_ServicesProperties($service_id){
      $sql = "SELECT id,price,discount,est_time_mins,name,attribute1,attribute2 from items
              where id = $service_id";
    #print "$sql";
    return $this->query($sql);
  }

  public function Inventory_StyleNumber_by_Barcode($barcode,$company_id){
    $sql ="SELECT number as style_number from items i
        join categories as c on c.id = i.category_id
        where i.barcode = '$barcode' and c.company_id = $company_id and i.deleted is NULL ;
      ";
    #print "$sql";
    return $this->query($sql);
  }
  public function Inventory_Category_by_Category_ID($barcode,$company_id){
    $sql ="SELECT number as style_number from items i
        join categories as c on c.id = i.category_id
        where i.barcode = '$barcode' and c.company_id = $company_id and i.deleted is NULL ;
      ";
    #print "$sql";
    return $this->query($sql);
  }
  public function Inventory_get_latest_Inventory_Runs($company_id,$totals=1){
      if ($totals == 0) {
        $sql ="SELECT
        ir.id,
        ir.start_date,
        convert_tz(ir.added, 'utc', ".quoteSmart($_SESSION['preferences']['timezone']).") as added,
        l1.firstname as l1_fn,l1.lastname as l1_ln,
        l2.firstname as l2_fn,l2.lastname as l2_ln";
    } ELSE {
        $sql ="SELECT count(ir.id) as count ";
    }
        $sql .= " FROM inventory_run ir
        join logins l1 on ir.assigned_login_id   = l1.id
        join logins l2 on ir.created_by_login_id = l2.id
        where ir.company_id = $company_id";

        if ($totals == 0) {
            #$sql .= " group by d.id ";
            if (!(isset($_SESSION['search_data']['column']))) { $sql.= " order by ir.id desc"; }
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
  public function Inventory_Inventory_RunDetails($inventory_run_id){
        $sql ="SELECT
        count(iri.id) as items_count,
        sum(iri.pos_quantity) as pos_quantity, 
        sum(iri.quantity) as quantity, 
        sum(CASE WHEN iri.updated is NULL THEN 1 ELSE 0 END) AS remaining_items
        FROM inventory_run_items iri
        where inventory_run_id = $inventory_run_id
        group by iri.inventory_run_id";
        #print $sql;
    return $this->query($sql);
  }
  public function Inventory_InventoryRunInfo($inventory_run_id){
    $sql = "SELECT   ir.id as run_number,
                    convert_tz(ir.added, 'utc', ".quoteSmart($_SESSION['preferences']['timezone']).") as added,
                    ir.assigned_login_id as login_id,
                    l.firstname as firstname, l.lastname as lastname,
                    ir.notes as notes,
                    i.price as price,
                    i.buy_price as buy_price,
                    count(iri.id) as inventory_run_item_total,
                    sum(iri.pos_quantity) as total_pos_quantity,
                    sum(iri.quantity) as total_quantity

            from inventory_run ir
            join logins l1 on ir.assigned_login_id   = l1.id
            join logins l2 on ir.created_by_login_id = l2.id
            join inventory_run_items iri on ir.id = iri.inventory_run_id
            join items i on iri.item_id = i.id
            join logins l on l.id = ir.assigned_login_id
            where ir.id = $inventory_run_id";
            $sql .= " group by ir.id";
    #print "$sql";
    return $this->query($sql);
  }
  public function Inventory_GetInventoryDetailsItems($inventory_run_id){
      $sql = "SELECT 
                    i.id as item_id,
                    ir.id as run_number,
                    i.price as price,
                    i.number as style_number,
                    i.barcode as barcode,
                    i.name as name,
                    i.attribute1 as attribute1,
                    i.attribute2 as attribute2,
                    i.buy_price as buy_price,
                        sum(iri.pos_quantity) as total_pos_quantity,
                        sum(iri.quantity) as total_quantity,
                        iri.id as inventory_run_items_id,
                        iri.updated as updated
            from inventory_run_items iri
            join inventory_run ir on iri.inventory_run_id = ir.id
            join logins l1 on ir.assigned_login_id   = l1.id
            join logins l2 on ir.created_by_login_id = l2.id
            join items i on iri.item_id = i.id
            join logins l on l.id = ir.assigned_login_id
            where ir.id = $inventory_run_id
            group by iri.id
            order by style_number";
    #print "$sql";
    return $this->query($sql);
  }
  public function Inventory_ItemInfoByItemID($item_id){
      $sql = "SELECT
                items.company_id, supplier_id, brand_id, department_id, category_id, tax_group_id, est_time_mins,
                items.name, b.name as brand_name, number, style, attribute1,attribute2, size, color, barcode,
                buy_price, price, tax, discount, location, quantity,
                reorder_limit1, reorder_limit2, convert_tz(items.added, 'utc', ".quoteSmart($_SESSION['preferences']['timezone']).") as added, 
                items.deleted, archived, online_active, old_tax
              FROM items
              left join brands b on b.company_id = items.company_id
              where items.id = $item_id";
    #print "$sql" . "\n";
    return $this->query($sql);
  }
  public function Inventory_ItemIdByBarcode($barcode){
      $sql = "SELECT max(id) as item_id
              from items
              where barcode = $barcode";
    //print "$sql" . "\n";
    return $this->query($sql);
  }
  public function Inventory_AutoCreateItems($company_id,$item_count){
    $sql ="SELECT   i.id as item_id,
                    i.number,
                    i.quantity,
                    i.name,
                    i.barcode,
                    i.attribute1,
                    i.attribute2
        from items i
        join categories c on i.category_id = c.id
        where i.deleted is NULL and
        c.company_id = $company_id and
        i.quantity > 0

        order by rand(),i.added
        limit $item_count;
      ";
    #print "$sql";
    return $this->query($sql);
  }
  public function Inventory_GetCategoriesByCompanyId($company_id,$totals){
        if ($totals == 1) {
            $sql ="SELECT count(distinct(c.id)) as count ";
        }
        ELSE {
            $sql ="SELECT   c.id as category_id,
                    c.type,
                    c.parent_id,
                    c.name,
                    c.attribute1,
                    c.attribute2 ";
        }
    $sql .="
        from categories c
        join items i on category_id = i.category_id
        where c.deleted is NULL and
        c.company_id = $company_id and
        i.quantity > 0
      ";
       if ( isset($_SESSION['item_management_categories']['category_id']) && $_SESSION['item_management_categories']['category_id']  != 'null' ) { $sql .= " and parent_id = '" .$_SESSION['item_management_categories']['category_id']."' "; }
       if ($totals == 0) {
            $sql .= " group by c.id ";
            if (!(isset($_SESSION['search_data']['column']))) { $sql.= " order by c.name desc"; }
            else { $sql.= " order by " . $_SESSION['search_data']['column'] . " " . $_SESSION['search_data']['asc_desc'] . " "; }

            if ( isset($_SESSION['search_data']['paging_page']) && $_SESSION['search_data']['paging_page'] != 0) {
                if ( $_SESSION['search_data']['paging_page'] == 1) { $limit_offset = 0; }
                else { $limit_offset = ( ($_SESSION['search_data']['paging_page'] - 1) * 10 ) ; }
                $sql .= " limit $limit_offset,10";
            }
        }
    #print "$sql";
    return $this->query($sql);     
  }
  public function Inventory_GetSubCategories($category_id,$company_id){
    $sql ="SELECT count(id) as count from categories where parent_id = $category_id company_id = $company_id";
    #print "$sql";
    return $this->query($sql);
  }
  public function Inventory_GetSubActiveItems_PerCategory($category_id,$company_id){
    $sql ="SELECT count(c.id) as count from items i join categories c on i.category_id = c.id where c.id = $category_id and c.company_id = $company_id and i.deleted is NULL";
    #print "$sql";
    return $this->query($sql);
  }


  public function getCategoryName_byID($category_id){
    $sql ="SELECT name as category_name from categories where id = $category_id";
    #print "$sql";
    return $this->query($sql);
  }
  public function labels_GetLabelData($item_id){
    $sql ="SELECT   b.name as brand, 
                    i.name,
                    i.number as style_number,
                    i.price, 
                    coalesce(i.discount, 0) as discount, 
                    i.attribute1,
                    i.attribute2,
                    i.barcode, 
                    c.attribute1 as catt1
                    from items as i 
                    join categories as c on c.id = i.category_id 
                    left join brands as b on b.id = i.brand_id 
                    where i.id = $item_id";
    #print "$sql";
    return $this->query($sql);
  }
  public function deliveries_ItemInfoByStyleNumber($style_number){
      $sql = "SELECT category_id,supplier_id,brand_id,department_id,tax_group_id,name,number,style,location
      from items
      where number = '$style_number' and deleted is NULL order by id desc limit 1;";
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

function   ItemManagement() {?>
<div class="ReportsTopRow main_bc_color2 main_color2_text"><a onclick="mainDiv('ItemManagement')" href="javascript: none();">Item Management</a></div>
    <div style="max-height: 1000px;" class="f_left wp100 hp94">
        <div class="leftSpace main_bc_color2 main_color2_text">&nbsp;</div>
            <div class="leftContent">
                <div class="reportHeader ml10">Item Attributes &nbsp;</div>
                    <? ItemManagementAttributes(); ?>
            </div>
        <div class="middleSpace wp02">&nbsp;</div>
            <div class="rightContent">
                <div class="reportHeader ml10">Manage Items &nbsp;&&nbsp; Services</div>
                    <? ItemManagementItemsStanza(); ?>
                <div class="reportHeader">&nbsp;</div>
                <div class="reportHeader ml10">Inventory Management &nbsp;</div>
                    <? ItemManagementInventory(); ?>
            </div>
        <div class="rightSpace main_bc_color2 main_color2_text">&nbsp;</div>
    </div>
<div class="ReportsBottomRow main_bc_color2 main_color2_text">&nbsp;</div>
<?
}
  function ItemManagementAttributes() {
?>
<div class="reportBodyDataContainer wp100">
    <div class="reportBodyCenter">
        <div class="action_group">
            <h4 class="HEADER main_bc_color1 main_color1_text">Item Properties</h4>
            <ul class="actions">
                <li class="action"><a href='javascript: none();' onclick="mainDiv('Inventory_Categories')">Categories</a>
                    <div class="explanation"> - Manage the different categories available for all goods and services.</div>
                </li>
                <li class="action"><a onclick="document.getElementById('post_values').value = 'item_management=1|show_type=suppliers'; document.page_form.submit(); return false;">Suppliers</a>
                    <div class="explanation"> - Manage all companies who deliver goods and services.</div>
                </li>
                <li class="action"><a onclick="document.getElementById('post_values').value = 'item_management=1|show_type=brands'; document.page_form.submit(); return false;">Brands</a>
                    <div class="explanation"> - Brand manufacturer that identifies a product or service.</div>
                </li>
                <li class="action"><a onclick="document.getElementById('post_values').value = 'item_management=1|show_type=departments'; document.page_form.submit(); return false;">Departments</a>
                    <div class="explanation"> - Smaller subsets of the organization for items/services to belong to.</div>
                </li>
                <li class="action"><a onclick="document.getElementById('post_values').value = 'item_management=1|show_type=taxgroups'; document.page_form.submit(); return false;">Tax Groups</a>
                    <div class="explanation"> - Manage all the different tax percentages applicable to items.</div>
                </li>
            </ul>
        </div>
    </div>
</div>
<?
}
  function ItemManagementItemsStanza() {
?>
<div class="reportBodyDataContainer wp100">
    <div class="reportBodyCenter ">
        <div class="action_group ">
            <h4 class="HEADER main_bc_color1 main_color1_text">Items - </h4>
            <ul class="actions">
                <li class="action"><a onclick="mainDiv('item_search')"                      href='javascript: none();' title="Items">Items</a>
                    <div class="explanation"> - A list of all items.</div>
                </li>
                <li class="action"><a onclick="mainDiv('ItemManagement_CreateNewItem')"     href="javascript: none();" title="Create New Item">Create New Item</a>
                    <div class="explanation"> - Create a new item in the inventory database.</div>
                </li>
                <li class="action"><a onclick="mainDiv('ItemManagement_CreateNewService')"  href="javascript: none();" title="Create New Service">Create New Service</a>
                    <div class="explanation"> - Create a service.</div>
                </li>
            </ul>

        </div>
    </div>
</div>
<?
}
  function ItemManagementInventory() {
?>
<div class="reportBodyDataContainer wp100">
    <div class="reportBodyCenter">
        <div class="action_group">
            <h4 class="HEADER main_bc_color1 main_color1_text">Items - </h4>
            <ul class="actions">
                <li class="action"><a href='javascript: none();' onclick="mainDiv('Deliveries_AllDeliveries')">Deliveries</a>
                    <div class="explanation"> - A list of all deliveries, shows each items and item counts.</div>
                </li>
                <li class="action"><a href='javascript: none();' onclick="mainDiv('SupplierReturns')">Returns to Suppliers</a>
                    <div class="explanation"> - Return items to Supplier,  use the supplier and then the style number.</div>
                </li>
                <li class="action"><a href='javascript: none();' onclick="mainDiv('Inventory_AllInventoryRuns')">Inventory</a>
                    <div class="explanation"> - Run an inventory audit and see results of past inventory runs.</div>
                </li>
                <li class="action"><a href='javascript: none();' onclick="mainDiv('Mailer_AllMailerRuns')">Mailer</a>
                    <div class="explanation"> - Run an email blast and see results of past email blasts.</div>
                </li>
            </ul>
        </div>
    </div>
</div>
<?
}

require_once('inventory_management_functions_Deliveries.php');
require_once('inventory_management_functions_Inventory.php');
require_once('inventory_management_functions_Categories.php');
require_once('inventory_management_functions_ItemManagement.php');
?>