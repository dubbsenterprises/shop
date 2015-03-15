<?php
include_once('general_functions.php');
class DAL {
  public function __construct(){}
  public function get_all_sales($company_id,$totals){
    if ($totals == 1) {
        $sql ="SELECT count(s.id) as count ";
    }
    ELSE {
        $sql ="SELECT s.id, s.login_id, s.paid, l.username as username, l2.username as register_username, s.customer_id, s.receipt_id,  convert_tz(s.added, 'utc', ".quoteSmart($_SESSION['preferences']['timezone']).")  as added";
    }
    $sql .= " FROM sales s
        join logins l  on l.id  = s.sales_person_id
        join logins l2 on l2.id = s.login_id
        where s.company_id = $company_id";
    if (  ( isset($_SESSION['search_data']['SalesReport']['start_date']) &&  $_SESSION['search_data']['SalesReport']['start_date']  != 'null' && $_SESSION['search_data']['SalesReport']['start_date'] != '' && $_SESSION['search_data']['SalesReport']['start_date'] != 'undefined' )
                    &&
          ( isset($_SESSION['search_data']['SalesReport']['start_date']) &&  $_SESSION['search_data']['SalesReport']['end_date']  != 'null' && $_SESSION['search_data']['SalesReport']['end_date'] != '' && $_SESSION['search_data']['SalesReport']['end_date'] != 'undefined' )
        ) {
        $sql .= " and (convert_tz(s.added, 'utc', ".quoteSmart($_SESSION['preferences']['timezone']).")) BETWEEN '" .$_SESSION['search_data']['SalesReport']['start_date']."' and DATE_ADD('" .$_SESSION['search_data']['SalesReport']['end_date']."',INTERVAL +1 DAY) ";
    }
    if ( isset($_SESSION['search_data']['SalesReport']['staff_id']) && $_SESSION['search_data']['SalesReport']['staff_id']  != '' && $_SESSION['search_data']['SalesReport']['staff_id']  != '-1') { $sql .= " and s.sales_person_id in (".$_SESSION['search_data']['SalesReport']['staff_id'].") "; }

    if ( isset($_SESSION['employee_recent_sales']['employee_id'])       && $_SESSION['employee_recent_sales']['employee_id']  != '' && $_SESSION['employee_recent_sales']['employee_id'] != '-1') { $sql .= " and s.sales_person_id = '" .$_SESSION['employee_recent_sales']['employee_id']."' "; }
    if ( isset($_SESSION['employee_recent_sales']['recent_sales_date']) ) { $sql .= " and s.added like '%" .$_SESSION['employee_recent_sales']['recent_sales_date']."%' "; }
    $sql .= " order by s.added desc";
    if ($totals == 0) {
        if ( isset($_SESSION['search_data']['paging_page']) && $_SESSION['search_data']['paging_page'] != 0) {
            if ( $_SESSION['search_data']['paging_page'] == 1) { $limit_offset = 0; }
            else { $limit_offset = ( ($_SESSION['search_data']['paging_page'] - 1) * 20 ) ; }
            $sql .= " limit $limit_offset,20";
        }
    }
    #if ($totals == 0 ) { print "$totals <br> $sql"; }
    return $this->query($sql);
    ;
  }
  public function get_last_X_sales($company_id,$employee_id,$recent_sales_date,$totals=0){
        if ($totals == 1) {
            $sql ="SELECT count(s.id) as count ";
        }
        ELSE {
            $sql ="SELECT s.id, s.login_id, s.paid, l.username as username, s.customer_id, s.receipt_id, convert_tz(s.added, 'utc', ".quoteSmart($_SESSION['preferences']['timezone']).")  as added ";
        }
        $sql .= " FROM sales s
                  left join logins l on s.sales_person_id = l.id
                  where s.company_id = $company_id";
        $sql .= " and s.login_id = ".$employee_id." ";
        $sql .= " and date(convert_tz(s.added, 'utc', ".quoteSmart($_SESSION['preferences']['timezone']).")) = '" .$recent_sales_date."' ";
        $sql .= " order by s.added desc";
        $sql .= " limit 0,20";
        #if ($totals == 0 ) { print "$totals <br> $sql"; }
    return $this->query($sql);
  }
  public function get_top_sales($company_id,$login_id){
    $sql ="SELECT  s.id,s.login_id,s.paid,l.username as username,s.customer_id,s.receipt_id,s.added,
            (si.price*si.quantity) as sub_total,
            format( (( (si.price - (si.discount/100)*si.price - (si.additional_discount/100)*si.price) * (si.tax/100)) * si.quantity),2) as tax_total,
            format( ((            si.discount/100)*si.price * si.quantity),2) as discount_total,
            format( (( si.additional_discount/100)*si.price * si.quantity),2) as additional_discount_total,
            format(
                    (si.price*si.quantity) +
                    sum( ( (si.price - (si.discount/100)*si.price - (si.additional_discount/100)*si.price) * (si.tax/100)) * si.quantity) -
                    sum( ( si.discount/100) *si.price * si.quantity) -
                    sum( ( si.additional_discount/100)*si.price * si.quantity),2)
                    as total
            FROM sales s
            inner join sale_items si on si.sale_id = s.id
            inner join logins l on s.sales_person_id = l.id
            where s.company_id = ".$company_id." and s.sales_person_id = ".$login_id." and s.deleted is NULL
            group by s.id
            order by sub_total desc
            limit 10";
        #if ($totals == 0 ) { print "$totals <br> $sql"; }
    return $this->query($sql);
  }
  public function get_SalesPerHourStats($company_id) {
    $sql = "SELECT distinct(count(hour(convert_tz(s.added, 'utc', ".quoteSmart($_SESSION['preferences']['timezone']).")))) cnt, hour(convert_tz(s.added, 'utc', ".quoteSmart($_SESSION['preferences']['timezone']).")) as hour
        from sales s
        where s.company_id = $company_id ";
    if ( ( isset($_SESSION['search_data']['SalesPerHourReport']['start_date']) &&  $_SESSION['search_data']['SalesPerHourReport']['start_date']  != 'null' && $_SESSION['search_data']['SalesPerHourReport']['start_date'] != '' && $_SESSION['search_data']['SalesPerHourReport']['start_date'] != 'undefined' )
            &&
         ( isset($_SESSION['search_data']['SalesPerHourReport']['start_date']) &&  $_SESSION['search_data']['SalesPerHourReport']['end_date']  != 'null' && $_SESSION['search_data']['SalesPerHourReport']['end_date'] != '' && $_SESSION['search_data']['SalesPerHourReport']['end_date'] != 'undefined' )
    ) {
        $sql .= " and (convert_tz(s.added, 'utc', ".quoteSmart($_SESSION['preferences']['timezone']).")) BETWEEN '" .$_SESSION['search_data']['SalesPerHourReport']['start_date']."' and DATE_ADD('" .$_SESSION['search_data']['SalesPerHourReport']['end_date']."',INTERVAL +1 DAY) ";
    }
    if ( isset($_SESSION['search_data']['SalesPerHourReport']['staff_id']) && $_SESSION['search_data']['SalesPerHourReport']['staff_id']  != '' && $_SESSION['search_data']['SalesPerHourReport']['staff_id']  != '-1') { $sql .= " and s.sales_person_id in (".$_SESSION['search_data']['SalesPerHourReport']['staff_id'].") "; }

    $sql .= " group by hour(convert_tz(s.added, 'utc', ".quoteSmart($_SESSION['preferences']['timezone'])."))
            order by cnt asc;";
    #if ($totals == 0 ) { print "$totals <br> $sql"; }
    return $this->query($sql);
}
  public function get_SalesPerMonthStats($company_id,$totals) {
    $sql = "SELECT distinct(count(CONCAT(rtrim(MONTH(s.added)),'-',YEAR(s.added)))) cnt, CONCAT(rtrim(MONTH(s.added)),'-',YEAR(s.added)) as monthYear
        from sales s
        where s.company_id = $company_id ";
    if ( ( isset($_SESSION['search_data']['SalesPerMonthReport']['start_date']) &&  $_SESSION['search_data']['SalesPerMonthReport']['start_date']  != 'null' && $_SESSION['search_data']['SalesPerMonthReport']['start_date'] != '' && $_SESSION['search_data']['SalesPerMonthReport']['start_date'] != 'undefined' )
            &&
         ( isset($_SESSION['search_data']['SalesPerMonthReport']['start_date']) &&  $_SESSION['search_data']['SalesPerMonthReport']['end_date']  != 'null' && $_SESSION['search_data']['SalesPerMonthReport']['end_date'] != '' && $_SESSION['search_data']['SalesPerMonthReport']['end_date'] != 'undefined' )
    ) {
        $sql .= " and (convert_tz(s.added, 'utc', ".quoteSmart($_SESSION['preferences']['timezone']).")) BETWEEN '" .$_SESSION['search_data']['SalesPerMonthReport']['start_date']."' and DATE_ADD('" .$_SESSION['search_data']['SalesPerMonthReport']['end_date']."',INTERVAL +1 DAY) ";
    }
    if ( isset($_SESSION['search_data']['SalesPerMonthReport']['staff_id']) && $_SESSION['search_data']['SalesPerMonthReport']['staff_id']  != '' && $_SESSION['search_data']['SalesPerMonthReport']['staff_id']  != '-1') { $sql .= " and s.sales_person_id in (".$_SESSION['search_data']['SalesPerMonthReport']['staff_id'].") "; }

    $sql  .= " group by monthYear
            order by s.added asc;";
         #if ($totals == 0 ) { print "$totals <br> $sql"; }
    return $this->query($sql);
}
      public function get_Item_Stats($company_id){
    $sql = "SELECT count(distinct(i.barcode)) unique_items, sum(i.quantity) as TotalItems, sum(i.quantity*i.price) as Total_Store_Value, sum(i.quantity*i.buy_price) as Total_Buy_Price
            from items i 
            where deleted is NULL and quantity > 0 and company_id = $company_id";
        #if ($totals == 0 ) { print "$totals <br> $sql"; }
    return $this->query($sql);
  }

  public function get_DailyInventoryStats($company_id,$totals) {
        if ($totals == 0) {
            $sql = "SELECT distinct(DATE_FORMAT(s.added,'%Y-%m-%d')) as date ";
        } else {
            $sql = "SELECT count(distinct(DATE_FORMAT(s.added,'%m-%d-%Y'))) as count ";
        }
        $sql .= " FROM sales as s";
        if (  ( isset($_SESSION['search_data']['DailyInventoryReport']['start_date']) &&  $_SESSION['search_data']['DailyInventoryReport']['start_date']  != 'null' && $_SESSION['search_data']['DailyInventoryReport']['start_date'] != '' && $_SESSION['search_data']['DailyInventoryReport']['start_date'] != 'undefined' )
                        &&
              ( isset($_SESSION['search_data']['DailyInventoryReport']['start_date']) &&  $_SESSION['search_data']['DailyInventoryReport']['end_date']  != 'null' && $_SESSION['search_data']['DailyInventoryReport']['end_date'] != '' && $_SESSION['search_data']['DailyInventoryReport']['end_date'] != 'undefined' )
            ) {
            $sql .= " where (convert_tz(s.added, 'utc', ".quoteSmart($_SESSION['preferences']['timezone']).")) BETWEEN '" .$_SESSION['search_data']['DailyInventoryReport']['start_date']."' and DATE_ADD('" .$_SESSION['search_data']['DailyInventoryReport']['end_date']."',INTERVAL +1 DAY) ";
            }

        if ($totals == 0) {
            if (!(isset($_SESSION['search_data']['column']))) { $sql.= " order by added desc"; }
            else { $sql.= " order by " . $_SESSION['search_data']['column'] . " " . $_SESSION['search_data']['asc_desc'] . " "; }
            if ( isset($_SESSION['search_data']['paging_page']) && $_SESSION['search_data']['paging_page'] != 0) {
                if ( $_SESSION['search_data']['paging_page'] == 1) { $limit_offset = 0; }
                else { $limit_offset = ( ($_SESSION['search_data']['paging_page'] - 1) * 20 ) ; }
                $sql .= " limit $limit_offset,20";
            }
        }
        #if ($totals == 0 ) { print "$totals <br> $sql"; }
        return $this->query($sql);
}
    public function get_DailyInventoryStats_sale($company_id,$date) {
        $sql = "    SELECT ifnull(sum(si.quantity),0) as count from sales as s
                    join sale_items as si on si.sale_id = s.id
                    where
                    date(convert_tz(s.added, 'utc', ".quoteSmart($_SESSION['preferences']['timezone']).")) = '$date'
                    and s.company_id = $company_id
                    and s.deleted is NULL;";
        #print "$sql<br>";
        return $this->query($sql);
}
    public function get_DailyInventoryStats_delivered($company_id,$date) {
        $sql = "    SELECT ifnull(sum(di.quantity),0) as count from deliveries as d
                    join delivery_items as di on di.delivery_id = d.id
                    join suppliers as s on s.id = d.supplier_id
                    where 
                    date(convert_tz(d.added, 'utc', ".quoteSmart($_SESSION['preferences']['timezone']).")) = '$date'
                    and s.company_id = $company_id
                    and d.deleted is NULL;";
        #print "$sql<br>";
        return $this->query($sql);
}
    public function get_DailyInventoryStats_returned($company_id,$date) {
        $sql = "    SELECT ifnull(sum(ri.quantity),0) as count from returns as r
                    join return_items as ri on ri.return_id = r.id
                    where
                    date(convert_tz(r.added, 'utc', ".quoteSmart($_SESSION['preferences']['timezone']).")) = '$date'
                    and r.company_id = $company_id
                    and r.deleted is NULL;";
        #print "$sql<br>";
        return $this->query($sql);
}

  public function get_all_ItemsReport_BestSellers($company_id,$totals){
        if ($totals == 0) {
            $sql ="SELECT distinct(i.number) as style_number,
                    si.item_id as item_id,
                    i.name as item_name,
                    sum(si.quantity) as style_sold_quantity_total,
                    sum(i.quantity) as style_item_quantity,
                    ( 100 - ((i.buy_price / i.price)*100) ) as margin,
                    sum(si.quantity * ( si.price  - ( (si.price * (si.discount/100)) - (si.price * (si.additional_discount/100)) ) )  ) as total_revenue_in_for_item";
        }
        ELSE {
            $sql ="SELECT count(distinct(i.number)) as count ";
        }
        $sql .= " FROM items i
            join categories c on i.category_id = c.id
            join sale_items si on i.id = si.item_id
            join sales s on si.sale_id = s.id

            where c.company_id = $company_id";
            if ( isset($_SESSION['search_data']['dynamic_pannel_start_date']) &&  $_SESSION['search_data']['dynamic_pannel_start_date']  != 'null' && $_SESSION['search_data']['dynamic_pannel_start_date'] != '' && $_SESSION['search_data']['dynamic_pannel_start_date'] != 'undefined' ) { $sql .= " and s.added >= '" .$_SESSION['search_data']['dynamic_pannel_start_date']."' "; }
            if ( isset($_SESSION['search_data']['dynamic_pannel_end_date']) &&  $_SESSION['search_data']['dynamic_pannel_end_date']  != 'null' && $_SESSION['search_data']['dynamic_pannel_end_date'] != '' && $_SESSION['search_data']['dynamic_pannel_end_date'] != 'undefined' ) { $sql .= " and s.added <= '" .$_SESSION['search_data']['dynamic_pannel_end_date']."' "; }
            if ( isset($_SESSION['search_data']['dynamic_pannel_id_search']) && $_SESSION['search_data']['dynamic_pannel_id_search']  != '') { $sql .= " and i.id = " .$_SESSION['search_data']['dynamic_pannel_id_search']." "; }
            if ( isset($_SESSION['search_data']['dynamic_pannel_style_number_search']) && $_SESSION['search_data']['dynamic_pannel_style_number_search']  != '') { $sql .= " and i.number like '%" .$_SESSION['search_data']['dynamic_pannel_style_number_search']."%' "; }
            if ( isset($_SESSION['search_data']['dynamic_pannel_barcode_search']) && $_SESSION['search_data']['dynamic_pannel_barcode_search']  != '') { $sql .= " and i.barcode like '%" .$_SESSION['search_data']['dynamic_pannel_barcode_search']."%' "; }

        if ($totals == 0) {
            $sql .= " group by i.barcode ";
            
            if (!(isset($_SESSION['search_data']['column']))) { $sql.= " order by style_item_quantity desc"; }
            else { $sql.= " order by " . $_SESSION['search_data']['column'] . " " . $_SESSION['search_data']['asc_desc'] . " "; }
            if ( isset($_SESSION['search_data']['paging_page']) && $_SESSION['search_data']['paging_page'] != 0) {
                if ( $_SESSION['search_data']['paging_page'] == 1) { $limit_offset = 0; }
                else { $limit_offset = ( ($_SESSION['search_data']['paging_page'] - 1) * 20 ) ; }
                $sql .= " limit $limit_offset,20";
            }
        }
        #if ($totals == 0 ) { print "$totals <br> $sql"; }
    return $this->query($sql);
  }
  public function get_all_ItemsReport_Category($company_id,$totals){
        if ($totals == 0) {
            $sql ="SELECT distinct(c.id) as category_id,
                  c.name as category_name ,
                  COALESCE(sum(si.quantity),0) as total_quantity_sold_from_this_CATEGORY,
                  sum(si.quantity * ( si.price - ( (si.price * (si.discount/100)) - (si.price * (si.additional_discount/100)) ) ) ) as total_revenue_in_for_CATEGORY ";
        }
        ELSE {
            $sql ="SELECT count(distinct(c.id)) as count ";
        }
        $sql .= " FROM items i
                join categories c on i.category_id = c.id
                join sale_items si on i.id = si.item_id
                join sales s on si.sale_id = s.id
                join brands b on i.brand_id = b.id
                join suppliers supp on i.supplier_id = supp.id
                join departments d on i.department_id = d.id


            where c.company_id = $company_id";
            if ( isset($_SESSION['search_data']['dynamic_pannel_start_date']) &&  $_SESSION['search_data']['dynamic_pannel_start_date']  != 'null' && $_SESSION['search_data']['dynamic_pannel_start_date'] != '' && $_SESSION['search_data']['dynamic_pannel_start_date'] != 'undefined' ) { $sql .= " and s.added >= '" .$_SESSION['search_data']['dynamic_pannel_start_date']."' "; }
            if ( isset($_SESSION['search_data']['dynamic_pannel_end_date']) &&  $_SESSION['search_data']['dynamic_pannel_end_date']  != 'null' && $_SESSION['search_data']['dynamic_pannel_end_date'] != '' && $_SESSION['search_data']['dynamic_pannel_end_date'] != 'undefined' ) { $sql .= " and s.added <= '" .$_SESSION['search_data']['dynamic_pannel_end_date']."' "; }
            if ( isset($_SESSION['search_data']['dynamic_pannel_id_search']) && $_SESSION['search_data']['dynamic_pannel_id_search']  != '') { $sql .= " and c.id = " .$_SESSION['search_data']['dynamic_pannel_id_search']." "; }
            if ( isset($_SESSION['search_data']['dynamic_pannel_style_number_search']) && $_SESSION['search_data']['dynamic_pannel_style_number_search']  != '') { $sql .= " and i.number like '%" .$_SESSION['search_data']['dynamic_pannel_style_number_search']."%' "; }
            if ( isset($_SESSION['search_data']['dynamic_pannel_barcode_search']) && $_SESSION['search_data']['dynamic_pannel_barcode_search']  != '') { $sql .= " and i.barcode like '%" .$_SESSION['search_data']['dynamic_pannel_barcode_search']."%' "; }
            # 2nd Row of Search Options
            if ( isset($_SESSION['search_data']['dynamic_pannel_supplier']) && $_SESSION['search_data']['dynamic_pannel_supplier']  != '-1') { $sql .= " and supp.id = " .$_SESSION['search_data']['dynamic_pannel_supplier']." "; }
            if ( isset($_SESSION['search_data']['dynamic_pannel_brand']) && $_SESSION['search_data']['dynamic_pannel_brand']  != '-1') { $sql .= " and b.id = " .$_SESSION['search_data']['dynamic_pannel_brand']." "; }
            if ( isset($_SESSION['search_data']['dynamic_pannel_department']) && $_SESSION['search_data']['dynamic_pannel_department']  != '-1') { $sql .= " and d.id = " .$_SESSION['search_data']['dynamic_pannel_department']." "; }

        if ($totals == 0) {
            $sql .= " group by i.category_id ";
            
            if (!(isset($_SESSION['search_data']['column']))) { $sql.= " order by total_quantity_sold_from_this_CATEGORY desc"; }
            else { $sql.= " order by " . $_SESSION['search_data']['column'] . " " . $_SESSION['search_data']['asc_desc'] . " "; }
            
            if ( isset($_SESSION['search_data']['paging_page']) && $_SESSION['search_data']['paging_page'] != 0) {
                if ( $_SESSION['search_data']['paging_page'] == 1) { $limit_offset = 0; }
                else { $limit_offset = ( ($_SESSION['search_data']['paging_page'] - 1) * 20 ) ; }
                $sql .= " limit $limit_offset,20";
            }
        }
        #if ($totals == 0 ) { print "$totals <br> $sql"; }
    return $this->query($sql);

    ;
  }
  public function get_all_ItemsReport_AllItems($company_id,$totals){
        if ($totals == 0) {
            $sql ="SELECT   i.id as item_id,
                            i.name as item_name,
                            i.number as style_number,
                            i.barcode,
                            i.attribute1,
                            i.attribute2,
                            i.price,
                            i.quantity,
                            i.added,
                            (i.quantity * i.price) as total_value";
        }
        ELSE {
            $sql ="SELECT count(i.id) as count ";
        }
        $sql .= " from items i
                join categories c on i.category_id = c.id
                join brands b on i.brand_id = b.id
                join suppliers supp on i.supplier_id = supp.id
                join departments d on i.department_id = d.id
        
            where b.company_id = $company_id and i.deleted is null and i.quantity >0";
            #if ( isset($_SESSION['search_data']['dynamic_pannel_start_date']) &&  $_SESSION['search_data']['dynamic_pannel_start_date']  != 'null' && $_SESSION['search_data']['dynamic_pannel_start_date'] != '' && $_SESSION['search_data']['dynamic_pannel_start_date'] != 'undefined' ) { $sql .= " and s.added >= '" .$_SESSION['search_data']['dynamic_pannel_start_date']."' "; }
            #if ( isset($_SESSION['search_data']['dynamic_pannel_end_date']) &&  $_SESSION['search_data']['dynamic_pannel_end_date']  != 'null' && $_SESSION['search_data']['dynamic_pannel_end_date'] != '' && $_SESSION['search_data']['dynamic_pannel_end_date'] != 'undefined' ) { $sql .= " and s.added <= '" .$_SESSION['search_data']['dynamic_pannel_end_date']."' "; }
            if ( isset($_SESSION['search_data']['dynamic_pannel_id_search']) && $_SESSION['search_data']['dynamic_pannel_id_search']  != '') { $sql .= " and i.barcode = " .$_SESSION['search_data']['dynamic_pannel_id_search']." "; }
            if ( isset($_SESSION['search_data']['dynamic_pannel_style_number_search']) && $_SESSION['search_data']['dynamic_pannel_style_number_search']  != '') { $sql .= " and i.number like '%" .$_SESSION['search_data']['dynamic_pannel_style_number_search']."%' "; }
            if ( isset($_SESSION['search_data']['dynamic_pannel_barcode_search']) && $_SESSION['search_data']['dynamic_pannel_barcode_search']  != '') { $sql .= " and i.barcode like '%" .$_SESSION['search_data']['dynamic_pannel_barcode_search']."%' "; }
           

            # 2nd Row of Search Options
            if ( isset($_SESSION['search_data']['dynamic_pannel_supplier']) && $_SESSION['search_data']['dynamic_pannel_supplier']  != '-1') { $sql .= " and supp.id = " .$_SESSION['search_data']['dynamic_pannel_supplier']." "; }
            if ( isset($_SESSION['search_data']['dynamic_pannel_brand']) && $_SESSION['search_data']['dynamic_pannel_brand']  != '-1') { $sql .= " and b.id = " .$_SESSION['search_data']['dynamic_pannel_brand']." "; }
            if ( isset($_SESSION['search_data']['dynamic_pannel_department']) && $_SESSION['search_data']['dynamic_pannel_department']  != '-1') { $sql .= " and d.id = " .$_SESSION['search_data']['dynamic_pannel_department']." "; }
            if ( isset($_SESSION['search_data']['dynamic_pannel_category']) && $_SESSION['search_data']['dynamic_pannel_category']  != '-1') { $sql .= " and c.id = " .$_SESSION['search_data']['dynamic_pannel_category']." "; }

        if ($totals == 0) {
            #$sql .= " group by i.number ";

            if (!(isset($_SESSION['search_data']['column']))) { $sql.= " order by quantity desc"; }
            else { $sql.= " order by " . $_SESSION['search_data']['column'] . " " . $_SESSION['search_data']['asc_desc'] . " "; }

            if ( isset($_SESSION['search_data']['paging_page']) && $_SESSION['search_data']['paging_page'] != 0) {
                if ( $_SESSION['search_data']['paging_page'] == 1) { $limit_offset = 0; }
                else { $limit_offset = ( ($_SESSION['search_data']['paging_page'] - 1) * 20 ) ; }
                $sql .= " limit $limit_offset,20";
            }
        }
        #if ($totals == 0 ) { print "<br> $sql"; }
    return $this->query($sql);

    ;
  }
  public function get_all_ItemsReport_Department($company_id,$totals){
        if ($totals == 0) {
            $sql ="SELECT distinct(d.id) as department_id,
        d.name as department_name ,
        COALESCE(sum(si.quantity),0) as total_quantity_sold_from_this_DEPARTMENT,
        sum(si.quantity * ( si.price - ( (si.price * (si.discount/100)) - (si.price * (si.additional_discount/100)) ) ) ) as total_revenue_in_for_DEPARTMENT ";
        }
        ELSE {
            $sql ="SELECT count(distinct(c.id)) as count ";
        }
        $sql .= " FROM items i
                join categories c on i.category_id = c.id
                join sale_items si on i.id = si.item_id
                join sales s on si.sale_id = s.id
                join brands b on i.brand_id = b.id
                join suppliers supp on i.supplier_id = supp.id
                join departments d on i.department_id = d.id

            where c.company_id = $company_id";
            if ( isset($_SESSION['search_data']['dynamic_pannel_start_date']) &&  $_SESSION['search_data']['dynamic_pannel_start_date']  != 'null' && $_SESSION['search_data']['dynamic_pannel_start_date'] != '' && $_SESSION['search_data']['dynamic_pannel_start_date'] != 'undefined' ) { $sql .= " and s.added >= '" .$_SESSION['search_data']['dynamic_pannel_start_date']."' "; }
            if ( isset($_SESSION['search_data']['dynamic_pannel_end_date']) &&  $_SESSION['search_data']['dynamic_pannel_end_date']  != 'null' && $_SESSION['search_data']['dynamic_pannel_end_date'] != '' && $_SESSION['search_data']['dynamic_pannel_end_date'] != 'undefined' ) { $sql .= " and s.added <= '" .$_SESSION['search_data']['dynamic_pannel_end_date']."' "; }
            if ( isset($_SESSION['search_data']['dynamic_pannel_id_search']) && $_SESSION['search_data']['dynamic_pannel_id_search']  != '') { $sql .= " and d.id = " .$_SESSION['search_data']['dynamic_pannel_id_search']." "; }
            if ( isset($_SESSION['search_data']['dynamic_pannel_style_number_search']) && $_SESSION['search_data']['dynamic_pannel_style_number_search']  != '') { $sql .= " and i.number like '%" .$_SESSION['search_data']['dynamic_pannel_style_number_search']."%' "; }
            if ( isset($_SESSION['search_data']['dynamic_pannel_barcode_search']) && $_SESSION['search_data']['dynamic_pannel_barcode_search']  != '') { $sql .= " and i.barcode like '%" .$_SESSION['search_data']['dynamic_pannel_barcode_search']."%' "; }

            # 2nd Row of Search Options
            if ( isset($_SESSION['search_data']['dynamic_pannel_supplier']) && $_SESSION['search_data']['dynamic_pannel_supplier']  != '-1') { $sql .= " and supp.id = " .$_SESSION['search_data']['dynamic_pannel_supplier']." "; }
            if ( isset($_SESSION['search_data']['dynamic_pannel_brand']) && $_SESSION['search_data']['dynamic_pannel_brand']  != '-1') { $sql .= " and b.id = " .$_SESSION['search_data']['dynamic_pannel_brand']." "; }
            if ( isset($_SESSION['search_data']['dynamic_pannel_category']) && $_SESSION['search_data']['dynamic_pannel_category']  != '-1') { $sql .= " and c.id = " .$_SESSION['search_data']['dynamic_pannel_category']." "; }

        if ($totals == 0) {
            $sql .= " group by i.department_id ";

            if (!(isset($_SESSION['search_data']['column']))) { $sql.= " order by total_quantity_sold_from_this_DEPARTMENT desc"; }
            else { $sql.= " order by " . $_SESSION['search_data']['column'] . " " . $_SESSION['search_data']['asc_desc'] . " "; }
            if ( isset($_SESSION['search_data']['paging_page']) && $_SESSION['search_data']['paging_page'] != 0) {
                if ( $_SESSION['search_data']['paging_page'] == 1) { $limit_offset = 0; }
                else { $limit_offset = ( ($_SESSION['search_data']['paging_page'] - 1) * 20 ) ; }
                $sql .= " limit $limit_offset,20";
            }
        }
        #if ($totals == 0 ) { print "$totals <br> $sql"; }
    return $this->query($sql);
  }
  public function get_all_ItemsReport_Vendor($company_id,$totals){
  if ($totals == 0) {
            $sql ="
                SELECT distinct(supp.id) as supplier_id,
                supp.name as supplier_name ,
                COALESCE(sum(si.quantity),0) as total_quantity_sold_from_this_supplier,
                sum(si.quantity * ( si.price - ( (si.price * (si.discount/100)) - (si.price * (si.additional_discount/100)) ) ) ) as total_revenue_in_for_supplier";
        } ELSE {
            $sql ="SELECT count(distinct(supp.id)) as count ";
        }
        $sql .= "
                FROM items i
                join categories c on i.category_id = c.id
                join sale_items si on i.id = si.item_id
                join sales s on si.sale_id = s.id
                join brands b on i.brand_id = b.id
                join suppliers supp on i.supplier_id = supp.id
                join departments d on i.department_id = d.id
                where c.company_id = $company_id";
            if ( isset($_SESSION['search_data']['dynamic_pannel_start_date']) &&  $_SESSION['search_data']['dynamic_pannel_start_date']  != 'null' && $_SESSION['search_data']['dynamic_pannel_start_date'] != '' && $_SESSION['search_data']['dynamic_pannel_start_date'] != 'undefined' ) { $sql .= " and s.added >= '" .$_SESSION['search_data']['dynamic_pannel_start_date']."' "; }
            if ( isset($_SESSION['search_data']['dynamic_pannel_end_date']) &&  $_SESSION['search_data']['dynamic_pannel_end_date']  != 'null' && $_SESSION['search_data']['dynamic_pannel_end_date'] != '' && $_SESSION['search_data']['dynamic_pannel_end_date'] != 'undefined' ) { $sql .= " and s.added <= '" .$_SESSION['search_data']['dynamic_pannel_end_date']."' "; }
            if ( isset($_SESSION['search_data']['dynamic_pannel_id_search']) && $_SESSION['search_data']['dynamic_pannel_id_search']  != '') { $sql .= " and supplier_id = " .$_SESSION['search_data']['dynamic_pannel_id_search']." "; }

            # 2nd Row of Search Options
            if ( isset($_SESSION['search_data']['dynamic_pannel_supplier']) && $_SESSION['search_data']['dynamic_pannel_supplier']  != '-1') { $sql .= " and supp.id = " .$_SESSION['search_data']['dynamic_pannel_supplier']." "; }
            if ( isset($_SESSION['search_data']['dynamic_pannel_brand']) && $_SESSION['search_data']['dynamic_pannel_brand']  != '-1') { $sql .= " and b.id = " .$_SESSION['search_data']['dynamic_pannel_brand']." "; }
            if ( isset($_SESSION['search_data']['dynamic_pannel_category']) && $_SESSION['search_data']['dynamic_pannel_category']  != '-1') { $sql .= " and c.id = " .$_SESSION['search_data']['dynamic_pannel_category']." "; }
        if ($totals == 0) {
            $sql .= " group by supplier_id ";

            if (!(isset($_SESSION['search_data']['column']))) { $sql.= " order by total_quantity_sold_from_this_supplier desc"; }
            else { $sql.= " order by " . $_SESSION['search_data']['column'] . " " . $_SESSION['search_data']['asc_desc'] . " "; }
            if ( isset($_SESSION['search_data']['paging_page']) && $_SESSION['search_data']['paging_page'] != 0) {
                if ( $_SESSION['search_data']['paging_page'] == 1) { $limit_offset = 0; }
                else { $limit_offset = ( ($_SESSION['search_data']['paging_page'] - 1) * 20 ) ; }
                $sql .= " limit $limit_offset,20";
            }
        }
        #if ($totals == 0 ) { print "$totals <br> $sql"; }
    return $this->query($sql);
  }
  public function get_all_ItemsReport_SoldOut($company_id,$totals){
        if ($totals == 0) {
            $sql ="SELECT i.number as style_number,
            i.barcode,
            i.buy_price,
            i.price,
            si.item_id as item_id,
            i.name as item_name,
            sum(si.quantity) as ITEM_ID_sold_total,
            i.quantity,
            ( 100 - ((i.buy_price / i.price)*100) ) as margin,
            sum(si.quantity * ( si.price - ( (si.price * (si.discount/100)) - (si.price * (si.additional_discount/100)) ) ) ) as total_revenue_in_for_item
            ";
        }
        ELSE {
            $sql ="SELECT count(i.number) as count ";
        }
        $sql .= " FROM items i
                join categories c on i.category_id = c.id
                join sale_items si on i.id = si.item_id
                join sales s on si.sale_id = s.id
                join brands b on i.brand_id = b.id
                join suppliers supp on i.supplier_id = supp.id
                join departments d on i.department_id = d.id

            where c.company_id = $company_id and i.quantity = 0 ";
            if ( isset($_SESSION['search_data']['dynamic_pannel_start_date']) &&  $_SESSION['search_data']['dynamic_pannel_start_date']  != 'null' && $_SESSION['search_data']['dynamic_pannel_start_date'] != '' && $_SESSION['search_data']['dynamic_pannel_start_date'] != 'undefined' ) { $sql .= " and s.added >= '" .$_SESSION['search_data']['dynamic_pannel_start_date']."' "; }
            if ( isset($_SESSION['search_data']['dynamic_pannel_end_date']) &&  $_SESSION['search_data']['dynamic_pannel_end_date']  != 'null' && $_SESSION['search_data']['dynamic_pannel_end_date'] != '' && $_SESSION['search_data']['dynamic_pannel_end_date'] != 'undefined' ) { $sql .= " and s.added <= '" .$_SESSION['search_data']['dynamic_pannel_end_date']."' "; }
            if ( isset($_SESSION['search_data']['dynamic_pannel_id_search']) && $_SESSION['search_data']['dynamic_pannel_id_search']  != '') { $sql .= " and i.id = " .$_SESSION['search_data']['dynamic_pannel_id_search']." "; }
            if ( isset($_SESSION['search_data']['dynamic_pannel_style_number_search']) && $_SESSION['search_data']['dynamic_pannel_style_number_search']  != '') { $sql .= " and i.number like '%" .$_SESSION['search_data']['dynamic_pannel_style_number_search']."%' "; }
            if ( isset($_SESSION['search_data']['dynamic_pannel_barcode_search']) && $_SESSION['search_data']['dynamic_pannel_barcode_search']  != '') { $sql .= " and i.barcode like '%" .$_SESSION['search_data']['dynamic_pannel_barcode_search']."%' "; }
            
            # 2nd Row of Search Options
            if ( isset($_SESSION['search_data']['dynamic_pannel_supplier']) && $_SESSION['search_data']['dynamic_pannel_supplier']  != '-1') { $sql .= " and supp.id = " .$_SESSION['search_data']['dynamic_pannel_supplier']." "; }
            if ( isset($_SESSION['search_data']['dynamic_pannel_brand']) && $_SESSION['search_data']['dynamic_pannel_brand']  != '-1') { $sql .= " and b.id = " .$_SESSION['search_data']['dynamic_pannel_brand']." "; }
            if ( isset($_SESSION['search_data']['dynamic_pannel_category']) && $_SESSION['search_data']['dynamic_pannel_category']  != '-1') { $sql .= " and c.id = " .$_SESSION['search_data']['dynamic_pannel_category']." "; }

        if ($totals == 0) {
            $sql .= " group by i.barcode ";

            if (!(isset($_SESSION['search_data']['column']))) { $sql.= " order by margin desc"; }
            else { $sql.= " order by " . $_SESSION['search_data']['column'] . " " . $_SESSION['search_data']['asc_desc'] . " "; }
            if ( isset($_SESSION['search_data']['paging_page']) && $_SESSION['search_data']['paging_page'] != 0) {
                if ( $_SESSION['search_data']['paging_page'] == 1) { $limit_offset = 0; }
                else { $limit_offset = ( ($_SESSION['search_data']['paging_page'] - 1) * 20 ) ; }
                $sql .= " limit $limit_offset,20";
            }
        }
        #if ($totals == 0 ) { print "$totals <br> $sql"; }
    return $this->query($sql);
  }

  public function parseArrayToObject($array) {
    $object = new stdClass();
    if (is_array($array) && count($array) > 0) {
        foreach ($array as $name=>$value) {
            $name = strtolower(trim($name));
            if (!empty($name)) {
                $object->$name = $value;
            }
        }
    }
    return $object;
}

  public function get_AppointmentsPerHourStats($company_id,$totals) {
    $sql = "SELECT count(hour(a.startDate)) cnt, hour(a.startDate) as hour
    from appointments a
    where a.company_id = $company_id ";
    if (  ( isset($_SESSION['search_data']['AppointmentsPerHourReport']['start_date']) &&  $_SESSION['search_data']['AppointmentsPerHourReport']['start_date']  != 'null' && $_SESSION['search_data']['AppointmentsPerHourReport']['start_date'] != '' && $_SESSION['search_data']['AppointmentsPerHourReport']['start_date'] != 'undefined' )
                    &&
          ( isset($_SESSION['search_data']['AppointmentsPerHourReport']['start_date']) &&  $_SESSION['search_data']['AppointmentsPerHourReport']['end_date']  != 'null' && $_SESSION['search_data']['AppointmentsPerHourReport']['end_date'] != '' && $_SESSION['search_data']['AppointmentsPerHourReport']['end_date'] != 'undefined' )
    ) {
        $sql .= " and a.startDate BETWEEN '" .$_SESSION['search_data']['AppointmentsPerHourReport']['start_date']."' and DATE_ADD('" .$_SESSION['search_data']['AppointmentsPerHourReport']['end_date']."',INTERVAL +1 DAY) ";
    }
    if ( isset($_SESSION['search_data']['AppointmentsPerHourReport']['staff_id']) && $_SESSION['search_data']['AppointmentsPerHourReport']['staff_id']  != '' && $_SESSION['search_data']['AppointmentsPerHourReport']['staff_id']  != '-1') { $sql .= " and a.login_id in (".$_SESSION['search_data']['AppointmentsPerHourReport']['staff_id'].") "; }

    $sql  .= " group by hour(a.startDate) order by hour";
    #if ($totals == 0 ) { print "$totals <br> $sql"; }
    return $this->query($sql);
}
  public function get_AppointmentsPerMonthStats($company_id,$totals) {
    $sql = "SELECT  distinct(count(CONCAT(rtrim(MONTH(a.startDate)),'-',YEAR(a.startDate)))) cnt,
                                 CONCAT(rtrim(MONTH(a.startDate)),'-',YEAR(a.startDate)) as monthYear
    from appointments a
    where a.company_id = $company_id ";
    if (  ( isset($_SESSION['search_data']['AppointmentsPerMonthReport']['start_date']) &&  $_SESSION['search_data']['AppointmentsPerMonthReport']['start_date']  != 'null' && $_SESSION['search_data']['AppointmentsPerMonthReport']['start_date'] != '' && $_SESSION['search_data']['AppointmentsPerHourReport']['start_date'] != 'undefined' )
                    &&
          ( isset($_SESSION['search_data']['AppointmentsPerMonthReport']['start_date']) &&  $_SESSION['search_data']['AppointmentsPerMonthReport']['end_date']  != 'null' && $_SESSION['search_data']['AppointmentsPerMonthReport']['end_date'] != '' && $_SESSION['search_data']['AppointmentsPerHourReport']['end_date'] != 'undefined' )
    ) {
        $sql .= " and a.startDate BETWEEN '" .$_SESSION['search_data']['AppointmentsPerMonthReport']['start_date']."' and DATE_ADD('" .$_SESSION['search_data']['AppointmentsPerMonthReport']['end_date']."',INTERVAL +1 DAY) ";
    }
    if ( isset($_SESSION['search_data']['AppointmentsPerMonthReport']['staff_id']) && $_SESSION['search_data']['AppointmentsPerMonthReport']['staff_id']  != '' && $_SESSION['search_data']['AppointmentsPerMonthReport']['staff_id']  != '-1') { $sql .= " and a.login_id in (".$_SESSION['search_data']['AppointmentsPerMonthReport']['staff_id'].") "; }

    $sql .= " group by CONCAT(rtrim(MONTH(a.startDate)),'-',YEAR(a.startDate))  order by a.startDate";
    #if ($totals == 0 ) { print "$totals <br> $sql"; }
    return $this->query($sql);
}

  public function get_sales_by_date_range($company_id,$to,$from){
            $sql = "
            SELECT s.id,
            s.paid as CashPaid,
            sum(si.quantity) as quant_total,
            sum(si.price * si.quantity) as price_total,
            @Discount_total         := sum(round((si.discount/100) * si.price *si.quantity,2))            as discount_total,
            @Additional_discount    := sum(round((si.additional_discount/100) * si.price *si.quantity,2))    as additional_discount,
            sum( (si.tax/100) * (si.price*si.quantity) - @Discount_total - @Additional_discount )    as tax_total,
            ifnull((select sum(c.amount) from card_payments c where c.sale_id = s.id),0)  as card_payment_total,
            ifnull((select sum(v.value) from vouchers v where v.sale_id = s.id),0)  as voucher_payment_total
            
            from sales as s
            left join logins as l on l.id = s.login_id
            left join logins as l2 on l2.id = s.sales_person_id
            left join sale_items si on si.sale_id = s.id
            join items as i on i.id = si.item_id

            where s.deleted is null and
            s.company_id = $company_id and
            date(convert_tz(s.added, 'utc', ".quoteSmart($_SESSION['preferences']['timezone']).")) >= '$to' and
            date(convert_tz(s.added, 'utc', ".quoteSmart($_SESSION['preferences']['timezone']).")) <= '$from'
            group by s.id;
            ";
            #print "$totals <br> $sql";
    return $this->query($sql);
  }
  public function get_sales_details_by_sale_id($sale_id){
      $sql = "
            SELECT
            @Price_total            := si.price * si.quantity                                           as price_total,
            @Discount_total         := round((si.discount/100)              * si.price *si.quantity,2)  as discount_total,
            @Additional_discount    := round((si.additional_discount/100)   * si.price *si.quantity,2)  as additional_discount,
            round((si.tax/100) * (@Price_total - @Discount_total - @Additional_discount),2)             as tax_total
            from sale_items as si
            where si.sale_id = $sale_id
            ;";
            #print "$totals <br> $sql";
    return $this->query($sql);
  }
  public function get_card_sales_by_sale_id($sale_id){
      $sql = "SELECT coalesce(ct.name, 'other') as cardname,
                    cp.amount
                    from card_payments as cp
                    left join card_types as ct on ct.id = cp.card_type_id
                    where cp.sale_id = $sale_id";
         #print "$totals <br> $sql";
    return $this->query($sql);
  }
  public function get_vouchers_by_company_id_n_date($company_id,$to,$from){
      $sql = "SELECT value, type
      from vouchers v
      where type = 'gift_certificate' and
      company_id = $company_id and
      date(convert_tz(v.added, 'utc', ".quoteSmart($_SESSION['preferences']['timezone']).")) >= '$from' and
      date(convert_tz(v.added, 'utc', ".quoteSmart($_SESSION['preferences']['timezone']).")) <= '$to'";
      #print "$totals <br> $sql";
    return $this->query($sql);
  }
  public function get_redeemed_vouchers_by_sale_id($sale_id){
      $sql = "SELECT id, type, value from vouchers where sale_id = $sale_id";
         #print "$totals <br> $sql";
    return $this->query($sql);
  }

  public function get_sale_figures($sale_id){
    $sql = "SELECT
            format(   sum(      si.price * si.quantity)
                    - sum( ( (            si.discount/100)  *si.price                                )* si.quantity) 
                    - sum( ( (  si.additional_discount/100)*(si.price - (si.discount/100)*si.price)  )* si.quantity)
                    + (
                      sum( ( (  si.price - (si.discount/100)*si.price - (si.additional_discount/100)*(si.price - (si.discount/100)*si.price) ) * (si.tax/100)) * si.quantity)
                     )
                ,2) as total,
                
            format(   sum(      si.price * si.quantity) 
                    - sum( ( (            si.discount/100)  *si.price                                )* si.quantity) 
                    - sum( ( (  si.additional_discount/100)*(si.price - (si.discount/100)*si.price)  )* si.quantity) 
                ,2) as sub_total,
                
            format(   sum( ( (  si.price - (si.discount/100)*si.price - (si.additional_discount/100)*(si.price - (si.discount/100)*si.price) ) * (si.tax/100)) * si.quantity)
                ,2) as tax_total,
            
            format( sum( (            si.discount/100)*si.price                                 * si.quantity)
                ,2) as discount_total,
                
            format( sum( ( si.additional_discount/100)*(si.price - (si.discount/100)*si.price)  * si.quantity)
                ,2) as additional_discount_total,

            COALESCE((select amount from card_payments where sale_id = $sale_id),0.00) as card_total,
            COALESCE((select value from vouchers where sale_id = $sale_id),0.00) as voucher_total

            from sale_items si
            left join card_payments cp on si.sale_id = cp.sale_id
            where si.sale_id = $sale_id";
    #print $sql;
    return $this->query($sql);
  }

  public function get_AllSuppliersPerCompanyId($company_id){
        $sql = "SELECT  distinct(s.id) as id,
                        s.name as name 
                from suppliers  s 
                right join items i on i.supplier_id = s.id 
                where s.deleted is NULL and 
                s.company_id = $company_id 
                "; 
    if ( isset($_SESSION['search_data']['item_search']['item_search_exclude_qty_zero'] ) && $_SESSION['search_data']['item_search']['item_search_exclude_qty_zero'] != 1 || !isset($_SESSION['search_data']['item_search']['item_search_exclude_qty_zero'] ) )
        { $sql .= " and ( (i.quantity > 0 || i.type = 2) ) "; }
    $sql .= " order by name desc;";
    return $this->query($sql);
  }
  public function get_AllBrandsPerCompanyId($company_id){
        $sql = "SELECT  distinct(b.id) as id,
                        b.name as name 
                from brands b 
                right join items i on i.brand_id = b.id 
                where b.deleted is NULL and 
                b.company_id = $company_id 
                "; 
    if ( isset($_SESSION['search_data']['item_search']['item_search_exclude_qty_zero'] ) && $_SESSION['search_data']['item_search']['item_search_exclude_qty_zero'] != 1 || !isset($_SESSION['search_data']['item_search']['item_search_exclude_qty_zero'] ) )
        { $sql .= " and ( (i.quantity > 0 || i.type = 2) ) "; }
    $sql .= " order by name desc;";
    return $this->query($sql);
  }
  public function get_AllDepartmentsPerCompanyId($company_id){
    $sql = "SELECT  distinct(d.id) as id,
                    d.name as name 
            from departments d 
            right join items i on i.department_id = d.id 
            where d.deleted is NULL and 
            d.company_id = $company_id 
            "; 
    if ( isset($_SESSION['search_data']['item_search']['item_search_exclude_qty_zero'] ) && $_SESSION['search_data']['item_search']['item_search_exclude_qty_zero'] != 1 || !isset($_SESSION['search_data']['item_search']['item_search_exclude_qty_zero'] ) )
        { $sql .= " and ( (i.quantity > 0 || i.type = 2) ) "; }
    $sql .= " order by name desc;";
    return $this->query($sql);
  }
  public function get_AllCategoriesPerCompanyId($company_id){
    $sql = "SELECT  distinct(c.id) as id,
                    c.name as name 
            from categories c 
            right join items i on i.category_id = c.id 
            where c.deleted is NULL and 
            c.company_id = $company_id 
            ";
    if ( isset($_SESSION['search_data']['item_search']['item_search_exclude_qty_zero'] ) && $_SESSION['search_data']['item_search']['item_search_exclude_qty_zero'] != 1 || !isset($_SESSION['search_data']['item_search']['item_search_exclude_qty_zero'] ) )
        { $sql .= " and ( (i.quantity > 0 || i.type = 2) ) "; }
    $sql .= " order by name desc;";
    return $this->query($sql);
  }
  public function get_AllStyleNumbersPerCompanyId($company_id){
        $sql = "SELECT distinct(number) 
                from items i
                where i.company_id = $company_id and 
                i.deleted is NULL and 
                i.archived = 0 
                "; 
    if ( isset($_SESSION['search_data']['item_search']['item_search_exclude_qty_zero'] ) && $_SESSION['search_data']['item_search']['item_search_exclude_qty_zero'] != 1 || !isset($_SESSION['search_data']['item_search']['item_search_exclude_qty_zero'] ) )
        { $sql .= " and ( (i.quantity > 0 || i.type = 2) ) "; }
    $sql .= " order by i.name desc;";
    return $this->query($sql);
  }

  public function get_last_date_sold($item_id){
    $sql = "SELECT max(s.added) as last_date_sold from sales s inner join sale_items si on s.id = si.sale_id where si.item_id = $item_id";
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
function reports() {
?>
    <div class="ReportsTopRow main_bc_color2 main_color2_text">
        <a onclick="mainDiv('reports')" href="javascript: none();">Reports</a>
    </div>
    <div style="max-height: 1000px;" class="f_left wp100 hp94">
        <div class="leftSpace main_bc_color2 main_color2_text">&nbsp;</div>
            <div class="leftContent">
                <div class="reportHeader"><img src="/common_includes/includes/images/big_money.gif">Point of Sale Reports&nbsp;</div>
                    <? reportSalesStanza(); ?>
                    <? reportAppointmentsStanza(); ?>
                    <? reportSummaryStanza(); ?>
            </div>
        <div class="middleSpace wp02">&nbsp;</div>
            <div class="rightContent">
                <div class="reportHeader"><img src="/common_includes/includes/images/big_money.gif">Reports of Items&nbsp;</div>
                    <? reportItemsStanza(); ?>
                    <? reportGroupedSalesStanza(); ?>
            </div>
        <div class="rightSpace main_bc_color2 main_color2_text">&nbsp;</div>
    </div>
<div class="ReportsBottomRow main_bc_color2 main_color2_text">&nbsp;</div>
<?
}
    function reportSalesStanza() {
    ?>
    <div class="reportBodyDataContainer wp100">
        <div class="reportBodyCenter">
            <div class="action_group">
                <h4 class="HEADER main_bc_color1 main_color1_text">Sales &amp; Refunds</h4>
                <ul class="actions h80px wp100 scrolling">
                    <li class="action"><a onclick="generateReport('SalesReport'); return false;">Totals</a>
                        <div class="explanation"> - Your basic sales report, lists all sales and totals them.</div>
                    </li>
                    <li class="action"><a onclick="generateReport('SalesPerHourReport'); return false;">Sales per hour</a>
                        <div class="explanation"> - Report of sales per hour.</div>
                    </li>
                    <li class="action"><a onclick="generateReport('SalesPerMonthReport'); return false;">Sales per month</a>
                        <div class="explanation"> - Report of sales per month.</div>
                    </li>
                    <li class="action"><a onclick="generateReport('DailyInventoryReport'); return false;">Daily Inventory Report</a>
                        <div class="explanation"> - Inventory Levels 'day-by-day'.</div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <?
    }
    function reportAppointmentsStanza() {
    ?>
    <div class="reportBodyDataContainer wp100">
        <div class="reportBodyCenter">
            <div class="action_group">
                <h4 class="HEADER main_bc_color1 main_color1_text">Appointments</h4>
                <ul class="actions h80px wp100 scrolling">
                    <li class="action"><a onclick="generateReport('AppointmentsPerHourReport'); return false;">Per hour</a>
                        <div class="explanation"> - Appointments booked on a per hour basis.</div>
                    </li>
                    <li class="action"><a onclick="generateReport('AppointmentsPerMonthReport'); return false;">Per month</a>
                        <div class="explanation"> - Appointments booked on a per MONTH basis.</div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <?
    }
    function reportSummaryStanza() {
    ?>
    <div class="reportBodyDataContainer wp100">
        <div class="reportBodyCenter">
            <div class="action_group">
                <h4 class="HEADER main_bc_color1 main_color1_text">General Reports</h4>
                <ul class="actions h80px wp100 scrolling">
                    <li class="action"><a class='bold' href='javascript: none();' onclick="document.getElementById('post_values').value = 'report_management=1|show_type=summary'; document.page_form.submit();">SUMMARY</a>
                        <div class="explanation"> - Show summary of totals by day, month, year or date range.</div>
                    </li>
                    <li class="action"><a class='bold' href='javascript: none();' onclick="document.getElementById('post_values').value = 'report_management=1|show_type=sales'; document.page_form.submit();">OLD SALES</a>
                        <div class="explanation"> - Shows a summary of all the sales. (old format).</div>
                    </li>
                    <li class="action"><a class='bold' href='javascript: none();' onclick="document.getElementById('post_values').value = 'report_management=1|show_type=returns'; document.page_form.submit();">RETURNS</a>
                        <div class="explanation"> - Reports showing all returns, keep a watchful eye on this.</div>
                    </li>
                    <li class="action"><a class='bold' href='javascript: none();' onclick="document.getElementById('post_values').value = 'report_management=1|show_type=vouchers'; document.page_form.submit();">VOUCHERS</a>
                        <div class="explanation"> - Reports showing all open and closed Vouchers.</div>
                    </li>
                    <li class="action"><a class='bold' href='javascript: none();' onclick="document.getElementById('post_values').value = 'report_management=1|show_type=inventory'; document.page_form.submit();">INVENTORY</a>
                        <div class="explanation"> - Report of inventory irregularities.</div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <?
    }
    function reportItemsStanza() {
    ?>
    <div class="reportBodyDataContainer wp100">
        <div class="reportBodyCenter">
            <div class="action_group">
                <h4 class="HEADER main_bc_color1 main_color1_text">Items - whats selling and whats not.</h4>
                <ul class="actions h80px wp100 scrolling">
                    <li class="action"><a onclick="generateReport('ItemsReport_BestSellers'); return false;">Best Sellers</a>
                        <div class="explanation"> - Item Sales report by quantity, lists top item sales by style number.</div>
                    </li>
                    <li class="action"><a onclick="generateReport('ItemsReport_SoldOut'); return false;">Sold Out Items</a>
                        <div class="explanation"> - Item Sales report by items with an inventory of zero.</div>
                    </li>
                    <li class="action"><a onclick="generateReport('ItemsReport_AllItems'); return false;">All Items</a>
                        <div class="explanation"> - Items report, all the items. <br>Breakdown by Store, Department, Category, Brand, Shelf Location, etc.</div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <?
    }
    function reportGroupedSalesStanza() {
    ?>
    <div class="reportBodyDataContainer wp100">
        <div class="reportBodyCenter">
            <div class="action_group">
                <h4 class="HEADER main_bc_color1 main_color1_text">Grouped Sales Totals - </h4>
                <ul class="actions h80px wp100 scrolling">
                    <li class="action"><a onclick="generateReport('ItemsReport_Category'); return false;">Category</a>
                        <div class="explanation"> - Item Sales report by quantity, lists top item sales by style number. </div>
                    </li>
                    <li class="action"><a onclick="generateReport('ItemsReport_Department'); return false;">Department</a>
                        <div class="explanation"> - Which departments sold the most, made the most, etc.</div>
                    </li>
                    <li class="action"><a onclick="generateReport('ItemsReport_Vendor'); return false;">Vendor</a>
                        <div class="explanation"> - How much revenue was taken in by each vendor/supplier. From which suppliers are you selling the most in revenue.</div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <?
    }

function SalesReports() {
    $general_dal    = new GENERAL_DAL();
    $PreferenceData = $general_dal->get_CompanyPreference_by_Company_ID($_SESSION['settings']['company_id'],'timezone');
    $timezone = $PreferenceData[0]->value;
    date_default_timezone_set($PreferenceData[0]->value);?>
    <div class="ReportsTopRow main_bc_color2 main_color2_text"><a onclick="mainDiv('reports')" href="javascript: none();">Reports</a> -> <?=$_SESSION['reportType']; ?></div>
        <div style="max-height: 1000px;" class="f_left wp100 hp94">
            <div class="leftSpace main_bc_color2 main_color2_text">&nbsp;</div>
            <div class="middleSpace wp96">
                    <div class="d_InlineBlock hp10 wp100">
                        <div class="f_left hp100 wp35 left vtop no-overflow">
                            <img alt="" height="45" src="/common_includes/includes/images/reports_icon.jpeg">
                            <?=$_SESSION['reportType']; ?>
                        </div>
                        <div class="f_right hp100 wp50 right">
                            &nbsp;
                        </div>
                    </div>
                    <div class="d_InlineBlock hp90 wp100">
                        <div class="f_left wp15 hp100">
                            <div class="d_InlineBlock wp100 hp100" >
                                <?=ReportsSearchStanza($_SESSION['reportType']) ?>
                            </div>
                        </div>
                        <div class="f_left wp85 hp100">
                            <div class="d_InlineBlock wp100 hp100" id="<?=$_SESSION['reportType']; ?>BodyCenter">
                            <?
                            ########  SalesReport  SalesReport  SalesReport
                            if ( $_SESSION['reportType'] == 'SalesReport' )                 { SalesReportStanza($_SESSION['reportType']); }
                            ########  SalesPerHourReport  SalesPerHourReport  SalesPerHourReport
                            if ( $_SESSION['reportType'] == 'SalesPerHourReport' )          { SalesPerHourReportStanza($_SESSION['reportType']); }
                            ########  SalesPerMonthReport  SalesPerMonthReport  SalesPerMonthReport
                            if ( $_SESSION['reportType'] == 'SalesPerMonthReport' )         { SalesPerMonthReportStanza($_SESSION['reportType']); }
                            ########  DailyInventoryReport  DailyInventoryReport  DailyInventoryReport
                            if ( $_SESSION['reportType'] == 'DailyInventoryReport' )        { DailyInventoryReportStanza($_SESSION['reportType']); }

                            ########  AppointmentsPerHourReport  AppointmentsPerHourReport  AppointmentsPerHourReport
                            if ( $_SESSION['reportType'] == 'AppointmentsPerHourReport' )   { AppointmentsPerHourReportStanza($_SESSION['reportType']);  }
                            ########  AppointmentsPerMonthReport  AppointmentsPerMonthReport  AppointmentsPerMonthReport
                            if ( $_SESSION['reportType'] == 'AppointmentsPerMonthReport' )  { AppointmentsPerMonthReportStanza($_SESSION['reportType']);}
                            ?>
                            </div>
                        </div>
                    </div>
            </div>
            <div class="rightSpace main_bc_color2 main_color2_text">&nbsp;</div>
        </div>
    <div class="ReportsBottomRow main_bc_color2 main_color2_text">&nbsp;</div>
<?
}
    function        SalesReportStanza(){?>
        <div class="wp100 hp07" id="listing_search_paging_top">
            <? showPaging(); ?>
        </div>
        <div class="d_InlineBlock wp100 hp85 scrolling">
            <div class="f_left wp100 hp100" id="report_data" >
                <? reportHeader($_SESSION['reportType']); ?>
                <? ReportRows($_SESSION['reportType']); ?>
            </div>
        </div>
        <div class="wp100 hp07" id="listing_search_paging_bottom">
            <? showPaging(); ?>
        </div>
<?}
    function        SalesPerHourReportStanza(){?>
        <div class="wp100 hp07" id="listing_search_paging_top">
            <? showPaging(); ?>
        </div>
        <div class="d_InlineBlock wp100 hp85 scrolling">
            <div class="f_left wp100 hp100" id="report_data" >
                <? ReportRows($_SESSION['reportType']); ?>
            </div>
        </div>
        <div class="wp100 hp07" id="listing_search_paging_bottom">
            <? showPaging(); ?>
        </div>
    <?}
    function        SalesPerMonthReportStanza(){?>
        <div class="wp100 hp07" id="listing_search_paging_top">
            <? showPaging(); ?>
        </div>
        <div class="d_InlineBlock wp100 hp85 scrolling">
            <div class="f_left wp100 hp100" id="report_data" >
                <? ReportRows($_SESSION['reportType']); ?>
            </div>
        </div>
        <div class="wp100 hp07" id="listing_search_paging_bottom">
            <? showPaging(); ?>
        </div>
    <?}
    function        DailyInventoryReportStanza(){?>
        <div class="wp100 hp07" id="listing_search_paging_top">
            <? showPaging(); ?>
        </div>
        <div class="d_InlineBlock wp100 hp85 scrolling">
            <div class="f_left wp100 hp100" id="report_data" >
                <? reportHeader($_SESSION['reportType']); ?>
                <? ReportRows($_SESSION['reportType']); ?>
            </div>
        </div>
        <div class="wp100 hp07" id="listing_search_paging_bottom">
            <? showPaging(); ?>
        </div>
    <?}
    function        AppointmentsPerHourReportStanza(){?>
        <div class="wp100 hp07" id="listing_search_paging_top">
            <? showPaging(); ?>
        </div>
        <div class="d_InlineBlock wp100 hp85 scrolling">
            <div class="f_left wp100 hp100" id="report_data" >
                <? ReportRows($_SESSION['reportType']); ?>
            </div>
        </div>
        <div class="wp100 hp07" id="listing_search_paging_bottom">
            <? showPaging(); ?>
        </div>
    <?}
    function        AppointmentsPerMonthReportStanza(){?>
        <div class="wp100 hp07" id="listing_search_paging_top">
            <? showPaging(); ?>
        </div>
        <div class="d_InlineBlock wp100 hp85 scrolling">
            <div class="f_left wp100 hp100" id="report_data" >
                <? ReportRows($_SESSION['reportType']); ?>
            </div>
        </div>
        <div class="wp100 hp07" id="listing_search_paging_bottom">
            <? showPaging(); ?>
        </div>
    <?}

function ItemReports() {
    $general_dal    = new GENERAL_DAL();
    $PreferenceData = $general_dal->get_CompanyPreference_by_Company_ID($_SESSION['settings']['company_id'],'timezone');
    $timezone = $PreferenceData[0]->value;
    date_default_timezone_set($PreferenceData[0]->value);
?>
    <div class="ReportsTopRow main_bc_color2 main_color2_text"><a onclick="mainDiv('reports')" href="javascript: none();">Reports</a> -> <?=$_SESSION['reportType']; ?></div>
        <div style="height: 75%; max-height: 1000px;" class="f_left wp100">
            <div class="leftSpace main_bc_color2 main_color2_text">&nbsp;</div>
            <div class="middleSpace wp96">
                    <div class="d_InlineBlock hp10 wp100">
                        <div class="f_left hp100 wp35 left vtop no-overflow">
                            <img alt="" height="45" src="/common_includes/includes/images/reports_icon.jpeg">
                            <?=$_SESSION['reportType']; ?>
                        </div>
                        <div class="f_right hp100 wp50 right">
                            &nbsp;
                        </div>
                    </div>
                    <div class="d_InlineBlock hp90 wp100">
                        <div class="f_left wp15 hp100">
                            <div class="d_InlineBlock wp100 hp100" >
                                <?=ReportsSearchStanza($_SESSION['reportType']) ?>
                            </div>
                        </div>
                        <div class="f_left wp85 hp100">
                            <div class="d_InlineBlock wp100 hp100" id="<?=$_SESSION['reportType']; ?>BodyCenter">
                            <?
                            ########  ItemsReport_BestSellers  ItemsReport_BestSellers  ItemsReport_BestSellers
                            if ( $_SESSION['reportType'] == 'ItemsReport_BestSellers' )                 { ItemsReport_BestSellersStanza($_SESSION['reportType']); }
                            ?>
                            </div>
                        </div>
                    </div>
            </div>
            <div class="rightSpace main_bc_color2 main_color2_text">&nbsp;</div>
        </div>
    <div class="ReportsBottomRow main_bc_color2 main_color2_text">&nbsp;</div>
<?
}
    function        ItemsReport_BestSellersStanza(){?>
        <div class="wp100 hp07" id="listing_search_paging_top">
            <? showPaging(); ?>
        </div>
        <div class="d_InlineBlock wp100 hp85 scrolling">
            <div class="f_left wp100 hp100" id="report_data" >
                <? reportHeader($_SESSION['reportType']); ?>
                <? ReportRows($_SESSION['reportType']); ?>
            </div>
        </div>
        <div class="wp100 hp07" id="listing_search_paging_bottom">
            <? showPaging(); ?>
        </div>
<?}

function ItemReports2() {
    $general_dal    = new GENERAL_DAL();
    $PreferenceData = $general_dal->get_CompanyPreference_by_Company_ID($_SESSION['settings']['company_id'],'timezone');
    $timezone = $PreferenceData[0]->value;
    date_default_timezone_set($PreferenceData[0]->value);
?>
<div class="ReportsTopRow main_bc_color2 main_color2_text"><a onclick="mainDiv('reports')" href="javascript: none();">Reports</a> -> <?=$_SESSION['reportType']; ?></div>
    <div style="max-height: 1000px;" class="f_left wp100 hp94">
        <div class="leftSpace main_bc_color2 main_color2_text">&nbsp;</div>
        <div class="middleSpace wp96">
                <div class="reports_container">
                        <div id="dynamic_pannel_search" class="listing_search_controls set_auto_focus">
                            <div class="search_controls_start"></div>
                                <? if (     $_SESSION['reportType'] == 'SalesReport' ){
                                                dynamic_pannel_search_date('start',$timezone);
                                                dynamic_pannel_search_date('end',$timezone);
                                                dynamic_pannel_search_ID();
                                    }
                                    elseif (
                                            $_SESSION['reportType'] == 'ItemsReport_BestSellers' ||
                                            $_SESSION['reportType'] == 'ItemsReport_SoldOut' ||
                                            $_SESSION['reportType'] == 'ItemsReport_AllItems'
                                            ) {
                                                dynamic_pannel_search_date('start',$timezone);
                                                dynamic_pannel_search_date('end',$timezone);
                                                dynamic_pannel_search_ID();
                                                dynamic_pannel_search_style_number();
                                                dynamic_pannel_search_barcode();
                                    }
                                    elseif (
                                            $_SESSION['reportType'] == 'ItemsReport_Category' ||
                                            $_SESSION['reportType'] == 'ItemsReport_Department' ||
                                            $_SESSION['reportType'] == 'ItemsReport_Vendor'
                                           ) {
                                                dynamic_pannel_search_date('start',$timezone);
                                                dynamic_pannel_search_date('end',$timezone);
                                                dynamic_pannel_search_ID();
                                    }
                                    dynamic_pannel_search_button();
                                ?>
                            <div class="search_controls_end"></div>

                            <? if ( $_SESSION['dynamic_pannel_advanced_search'] == 1 ) { ?>
                            <div style="display: none;" id="dynamic_pannel_advanced_search" class="advanced_search">
                                <div class="search_controls_start"></div>
                                <h1>More Search Options<button onclick="listingAdvancedSearch('dynamic_pannel');" title="Advanced">Hide</button></h1>
                                <div class="search_controls_end"></div>
                                <!--  First row-->
                                <div class="search_controls_start"></div>
                                <? if ( $_SESSION['reportType'] == 'SalesReport' ||
                                        $_SESSION['reportType'] == 'ItemsReport_BestSellers' ||
                                        $_SESSION['reportType'] == 'ItemsReport_Category' ||
                                        $_SESSION['reportType'] == 'ItemsReport_Department'
                                       ) {
                                     dynamic_pannel_advanced_search_Customer();
                                     dynamic_pannel_advanced_search_Employee();
                                     dynamic_pannel_advanced_search_Register();
                                     //dynamic_pannel_advanced_search_SalesTax();
                                }

                                ?>
                                <div class="search_controls_end"></div>




                                <!--  Second row  -->
                                <div class="search_controls_start"></div>
                                <? if ( $_SESSION['reportType'] == 'ItemsReport_Category' ) {
                                        dynamic_pannel_advanced_search_Suppliers();
                                        dynamic_pannel_advanced_search_Brands();
                                        dynamic_pannel_advanced_search_Departments();
                                }
                                ?>
                                <? if ( $_SESSION['reportType'] == 'ItemsReport_Department' ) {
                                        dynamic_pannel_advanced_search_Suppliers();
                                        dynamic_pannel_advanced_search_Brands();
                                        dynamic_pannel_advanced_search_Categories();
                                }
                                ?>
                                <? if ( $_SESSION['reportType'] == 'ItemsReport_Vendor' ) {
                                        dynamic_pannel_advanced_search_Suppliers();
                                        dynamic_pannel_advanced_search_Brands();
                                        dynamic_pannel_advanced_search_Categories();
                                }
                                ?>
                                <? if ( $_SESSION['reportType'] == 'ItemsReport_SoldOut' ) {
                                        dynamic_pannel_advanced_search_Suppliers();
                                        dynamic_pannel_advanced_search_Brands();
                                        dynamic_pannel_advanced_search_Categories();
                                        dynamic_pannel_advanced_search_Departments();
                                }
                                ?>
                                <? if ( $_SESSION['reportType'] == 'ItemsReport_AllItems' ) {
                                        dynamic_pannel_advanced_search_Suppliers();
                                        dynamic_pannel_advanced_search_Brands();
                                        dynamic_pannel_advanced_search_Categories();
                                        dynamic_pannel_advanced_search_Departments();
                                }
                                ?>

                                <div class="search_controls_end"></div>
                            </div>
                            <? } ?>

                            <div class="d_InlineBlock wp90" id="listing_search_paging_top">
                                <? showPaging(); ?>
                            </div>
                            <div class="d_InlineBlock wp90 report_results m1">
                                    <? reportHeader($_SESSION['reportType']); ?><?=$_SESSION['reportType']?>
                                <div id="report_data" class="d_InlineBlock wp100">
                                    <? ReportRows($_SESSION['reportType']); ?>
                                </div>
                            </div>
                            <div class="d_InlineBlock wp90" id="listing_search_paging_bottom">
                                    <? showPaging(); ?>
                            </div>
                        </div>
                </div>
        </div>
        <div class="rightSpace main_bc_color2 main_color2_text">&nbsp;</div>
    </div>
<div class="ReportsBottomRow main_bc_color2 main_color2_text">&nbsp;</div>
<?
}

function reportHeader($reportType) {
    if          ($reportType == 'SalesReport') { ?>
    <div class="report_header HEADER main_bc_color1 main_color1_text wp100">
        <div class="report_header_cell_wp10">Id</div>
        <div class="report_header_cell_wp12">Date/Time</div>
        <div class="report_header_cell_wp10">Register<br>Staff</div>
        <div class="report_header_cell_wp10">Sales<br>Staff</div>
        <div class="report_header_cell_wp05">Sub<br>Total</div>
        <div class="report_header_cell_wp05">Discount</div>
        <div class="report_header_cell_wp05">Register<br>Discount</div>
        <div class="report_header_cell_wp05">Tax</div>
        <div class="report_header_cell_wp08">Total</div>
        <div class="report_header_cell_wp05">Cash</div>
        <div class="report_header_cell_wp05">Card</div>
        <div class="report_header_cell_wp05">Voucher</div>
        <div class="report_header_cell_wp05">Details</div>
        <div class="report_header_cell_wp04">Units</div>
    </div>
    <? } elseif ($reportType == 'SalesPerHourReport') { ?>
    <div class="report_header HEADER main_bc_color1 main_color1_text wp100">
        <div class="report_header_cell_wp45"><a onclick="orderBy('cnt','<?=$reportType; ?>'); return false;">Cnt</a></div>
        <div class="report_header_cell_wp50"><a onclick="orderBy('hour','<?=$reportType; ?>'); return false;">Hour</a></div>
    </div>
    <? } elseif ($reportType == 'DailyInventoryReport') { ?>
    <div class="report_header HEADER main_bc_color1 main_color1_text wp100">
        <div class="report_header_cell_wp15"><a onclick="orderBy('added','<?=$reportType; ?>'); return false;">Date</a></div>
        <div class="report_header_cell_wp20">Sold</div>
        <div class="report_header_cell_wp20">Delivered</div>
        <div class="report_header_cell_wp20">Returned</div>
        <div class="report_header_cell_wp20">Delta &Delta;</div>
    </div>
    <? } elseif ($reportType == 'ItemsReport_BestSellers') { ?>
    <div class="report_header HEADER main_bc_color1 main_color1_text wp100">
        <div class="report_header_cell_wp05"><a onclick="orderBy('item_id','<?=$reportType; ?>'); return false;">Item ID</a></div>
        <div class="report_header_cell_wp15"><a onclick="orderBy('style_number','<?=$reportType; ?>'); return false;">Style Number</a></div>
        <div class="report_header_cell_wp30"><a onclick="orderBy('item_name','<?=$reportType; ?>'); return false;">Item Name</a></div>
        <div class="report_header_cell_wp10"><a onclick="orderBy('style_item_quantity','<?=$reportType; ?>'); return false;">Inventory</a></div>
        <div class="report_header_cell_wp10"><a onclick="orderBy('style_sold_quantity_total','<?=$reportType; ?>'); return false;">Total Sold</a></div>
        <div class="report_header_cell_wp07"><a onclick="orderBy('margin','<?=$reportType; ?>'); return false;">Margin</a></div>
        <div class="report_header_cell_wp10"><a onclick="orderBy('total_revenue_in_for_item','<?=$reportType; ?>'); return false;">Revenue In</a></div>
        <div class="report_header_cell_wp10">Last Date</div>
    </div>
    <? } elseif ($reportType == 'ItemsReport_Category') { ?>
    <div class="d_InlineBlock report_header HEADER main_bc_color1 main_color1_text wp100">
        <div class="report_header_cell_wp25"><a onclick="orderBy('category_id','<?=$reportType; ?>'); return false;">Category ID</a></div>
        <div class="report_header_cell_wp25"><a onclick="orderBy('category_name','<?=$reportType; ?>'); return false;">Category Name</a></div>
        <div class="report_header_cell_wp25"><a onclick="orderBy('total_quantity_sold_from_this_CATEGORY','<?=$reportType; ?>'); return false;">Total Sold</a></div>
        <div class="report_header_cell_wp24"><a onclick="orderBy('total_revenue_in_for_CATEGORY','<?=$reportType; ?>'); return false;">Revenue In</a></div>
    </div>
    <? } elseif ($reportType == 'ItemsReport_Department') { ?>
    <div class="report_header HEADER main_bc_color1 main_color1_text wp100">
        <div class="report_header_cell_wp25"><a onclick="orderBy('department_id','<?=$reportType; ?>'); return false;">Department ID</a></div>
        <div class="report_header_cell_wp25"><a onclick="orderBy('department_name','<?=$reportType; ?>'); return false;">Department Name</a></div>
        <div class="report_header_cell_wp25"><a onclick="orderBy('total_quantity_sold_from_this_DEPARTMENT','<?=$reportType; ?>'); return false;">Total Sold</a></div>
        <div class="report_header_cell_wp24"><a onclick="orderBy('total_revenue_in_for_DEPARTMENT','<?=$reportType; ?>'); return false;">Revenue In</a></div>
    </div>
    <? } elseif ($reportType == 'ItemsReport_Vendor') { ?>
    <div class="report_header HEADER main_bc_color1 main_color1_text wp100">
        <div class="report_header_cell_wp25"><a onclick="orderBy('supplier_id','<?=$reportType; ?>'); return false;">Vendor ID</a></div>
        <div class="report_header_cell_wp25"><a onclick="orderBy('supplier_name','<?=$reportType; ?>'); return false;">Vendor Name</a></div>
        <div class="report_header_cell_wp25"><a onclick="orderBy('total_quantity_sold_from_this_supplier','<?=$reportType; ?>'); return false;">Total Sold</a></div>
        <div class="report_header_cell_wp24"><a onclick="orderBy('total_revenue_in_for_supplier','<?=$reportType; ?>'); return false;">Revenue In</a></div>
    </div>
    <? } elseif ($reportType == 'ItemsReport_SoldOut') { ?>
    <div class="report_header HEADER main_bc_color1 main_color1_text wp100">
        <div class="report_header_cell_5"> <a onclick="orderBy('item_id','<?=$reportType; ?>'); return false;">Item ID</a></div>
        <div class="report_header_cell_15"> <a onclick="orderBy('style_number','<?=$reportType; ?>'); return false;">Style Number</a></div>
        <div class="report_header_cell_12"><a onclick="orderBy('barcode','<?=$reportType; ?>'); return false;">Barcode ID</a></div>
        <div class="report_header_cell_32"><a onclick="orderBy('item_name','<?=$reportType; ?>'); return false;">Name</a></div>
        <div class="report_header_cell_5"><a onclick="orderBy('ITEM_ID_sold_total','<?=$reportType; ?>'); return false;"># Sold</a></div>
        <div class="report_header_cell_7"><a onclick="orderBy('quantity','<?=$reportType; ?>'); return false;">Item Quantity</a></div>
        <div class="report_header_cell_9"><a onclick="orderBy('buy_price','<?=$reportType; ?>'); return false;">Buy Price</a></div>
        <div class="report_header_cell_7"><a onclick="orderBy('price','<?=$reportType; ?>'); return false;">Price</a></div>
        <div class="report_header_cell_7"><a onclick="orderBy('margin','<?=$reportType; ?>'); return false;">Margin</a></div>
        <div class="report_header_cell_9"><a onclick="orderBy('total_revenue_in_for_item','<?=$reportType; ?>'); return false;">Revenue In</a></div>
    </div>
    <? } elseif ($reportType == 'ItemsReport_AllItems') { ?>
    <div class="report_header HEADER main_bc_color1 main_color1_text wp100">
        <div class="report_header_cell_wp04"> <a onclick="orderBy('item_id','<?=$reportType; ?>'); return false;">Item ID</a></div>
        <div class="report_header_cell_wp07"> <a onclick="orderBy('added','<?=$reportType; ?>'); return false;">Added</a></div>
        <div class="report_header_cell_wp10"> <a onclick="orderBy('style_number','<?=$reportType; ?>'); return false;">Style Number</a></div>
        <div class="report_header_cell_wp08"><a onclick="orderBy('barcode','<?=$reportType; ?>'); return false;">Barcode</a></div>
        <div class="report_header_cell_wp17"><a onclick="orderBy('item_name','<?=$reportType; ?>'); return false;">Name</a></div>
        <div class="report_header_cell_wp14"><a onclick="orderBy('attribute1','<?=$reportType; ?>'); return false;">Attribute 1</a></div>
        <div class="report_header_cell_wp14"><a onclick="orderBy('attribute2','<?=$reportType; ?>'); return false;">Attribute 2</a></div>
        <div class="report_header_cell_wp05"><a onclick="orderBy('quantity','<?=$reportType; ?>'); return false;">Qty</a></div>
        <div class="report_header_cell_wp07"><a onclick="orderBy('price','<?=$reportType; ?>'); return false;">Price</a></div>
        <div class="report_header_cell_wp10"><a onclick="orderBy('total_value','<?=$reportType; ?>'); return false;">Total Value</a></div>
    </div>
    <? } else { ?>
    <div class="report_header HEADER main_bc_color1 main_color1_text wp100">
        <div class="wp100">Row Not setup for <?=$reportType?></div>
        <div>function  reportHeader(<?=$reportType?>) . page includes/report_functions.php line ~715</div>
    </div>
    <? }
}
function ReportRows($reportType) {
$style = " style=\"text-align: right;\"";
$bg_color = "#FFFFFF";
$dal = new DAL();
    if ( $reportType == 'SalesReport') {
        $rows = $dal->get_all_sales($_SESSION['settings']['company_id'],0);
        if (count($rows) >0 ) {
        $colors = array("#DDEEFF","#FFFFFF");
        foreach($rows as $row)
            {
            $get_sale_figures = $dal->get_sale_figures($row->id);
            if (!isset($last_date)) {$last_date = $row->added;}
            if  ( date("m.d.y",strtotime($last_date)) != date("m.d.y",strtotime($row->added)) )   {
                $bg_color = array_shift($colors);
                array_push($colors,$bg_color);
            }
            ?>
            <div class="report_rows wp100 " style="background-color:<?=$bg_color?>;">
                <div class="report_data_cell_wp10"><?=$row->receipt_id?></div>
                <div class="report_data_cell_wp12 no-overflow"><?=date("d/m/y h:i a",strtotime($row->added))?></div>
                <div class="report_data_cell_wp10">&nbsp;<?=$row->register_username?></div>
                <div class="report_data_cell_wp10">&nbsp;<?=$row->username?></div>
                <div class="report_data_cell_wp05" <?=$style?> >&nbsp;<?=$get_sale_figures[0]->sub_total?></div>
                <div class="report_data_cell_wp05" <?=$style?> >&nbsp;<?=$get_sale_figures[0]->discount_total?></div>
                <div class="report_data_cell_wp05" <?=$style?> >&nbsp;<?=$get_sale_figures[0]->additional_discount_total?></div>
                <div class="report_data_cell_wp05" <?=$style?> >&nbsp;<?=$get_sale_figures[0]->tax_total?></div>
                <div class="report_data_cell_wp08" <?=$style?> ><img class="f_left" src="/common_includes/includes/images/SignDollar-Green16x16.png" height="12" widht="12">&nbsp;<?=$get_sale_figures[0]->total?></div>
                <div class="report_data_cell_wp05" <?=$style?> >&nbsp;<?=$row->paid?></div>
                <div class="report_data_cell_wp05" <?=$style?> >&nbsp;<?=$get_sale_figures[0]->card_total?></div>
                <div class="report_data_cell_wp05" <?=$style?> >&nbsp;<?=$get_sale_figures[0]->voucher_total?></div>
                <div class="report_data_cell_wp05"><?=$row->id?></div>
                <div class="report_data_cell_wp04">&nbsp;<?=$get_sale_figures[0]->total_units?></div>
            </div>
            <?
            $last_date = $row->added ;
            }
        }  else { ?>
            <div class="report_rows wp100 " style="background-color:<?=$bg_color;?>;">
                <div class="report_data_cell_NoData wp99">NO results returned in your query.</div>
            </div>
        <?}
    }  elseif ( $reportType == 'SalesPerHourReport') {
        include_once('libchart/classes/libchart.php');
        $rows = $dal->get_SalesPerHourStats($_SESSION['settings']['company_id'],0);
            $current_hours = 0;
            $chart      = new HorizontalBarChart(600,300);
            $dataSet    = new XYDataSet();
            while ($current_hours < 24 ) {
                $hour = date("g:i a",strtotime($current_hours.":00:00"));
                $switch = 0;
                foreach($rows as $row) {
                    if ($row->hour == $current_hours) {   
                        $dataSet->addPoint(new Point($hour, $row->cnt))  ;
                        $switch = 1;
                    }
                }
                if ($switch == 0 ) {$dataSet->addPoint(new Point($hour, 0))  ;}
                $current_hours++;
                reset($rows);
            }
            $chart->setDataSet($dataSet);
            $chart->setTitle($_SESSION['preferences']['company_name'] . " hourly sales report.");

            ob_start(); $chart->render(); $b64 = base64_encode(ob_get_contents()); ob_end_clean();
            ?>
            <img src="data:image/png;base64,<?=$b64?>">
            <?
    }  elseif ( $reportType == 'SalesPerMonthReport') {
        include_once('libchart/classes/libchart.php');
        $rows = $dal->get_SalesPerMonthStats($_SESSION['settings']['company_id'],0);
            $chart      = new VerticalBarChart(600,300);
            $dataSet    = new XYDataSet();
            foreach($rows as $row) {
                    $dataSet->addPoint(new Point($row->monthYear, $row->cnt))  ;
            }
            $chart->setDataSet($dataSet);
            $chart->setTitle($_SESSION['preferences']['company_name'] . " sales Per month report.");
            ob_start(); $chart->render(); $b64 = base64_encode(ob_get_contents()); ob_end_clean();
            ?>
            <img src="data:image/png;base64,<?=$b64?>">
            <?
    } elseif ( $reportType == 'DailyInventoryReport') {
        $rows = $dal->get_DailyInventoryStats($_SESSION['settings']['company_id'],0);
        if (count($rows) >0 ) {
        $colors = array("#DDEEFF","#FFFFFF");
        foreach($rows as $row)
            {
                if (!isset($last_date)) {$last_date = $row->date;}
                if  ( $last_date != $row->date )   {
                    $bg_color = array_shift($colors);
                    array_push($colors,$bg_color);
                }
                $get_DailyInventoryStats_sale       = $dal->get_DailyInventoryStats_sale($_SESSION['settings']['company_id'],$row->date);
                $get_DailyInventoryStats_delivered  = $dal->get_DailyInventoryStats_delivered($_SESSION['settings']['company_id'],$row->date);
                $get_DailyInventoryStats_returned   = $dal->get_DailyInventoryStats_returned($_SESSION['settings']['company_id'],$row->date);
                ?>
                <div class="report_rows wp100 " style="background-color:<?=$bg_color?>;">
                    <div class="report_data_cell_wp15"><?=$row->date?></div>
                    <div class="report_data_cell_wp20">&nbsp;<?=$get_DailyInventoryStats_sale[0]->count?></div>
                    <div class="report_data_cell_wp20">&nbsp;<?=$get_DailyInventoryStats_delivered[0]->count?></div>
                    <div class="report_data_cell_wp20">&nbsp;<?=$get_DailyInventoryStats_returned[0]->count?></div>
                    <div class="report_data_cell_wp20">&nbsp;<?=$get_DailyInventoryStats_returned[0]->count + $get_DailyInventoryStats_delivered[0]->count - $get_DailyInventoryStats_sale[0]->count?></div>
                </div>
                <?
                $last_date = $row->date ;
            }
        }  else { ?>
            <div class="report_rows wp100" style="background-color:<?=$bg_color;?>;">
                <div class="report_data_cell_NoData wp99">NO results returned in your query.</div>
            </div>
        <?}
    }


    elseif ( $reportType == 'AppointmentsPerHourReport') {
        include_once('libchart/classes/libchart.php');
        $rows = $dal->get_AppointmentsPerHourStats($_SESSION['settings']['company_id'],0);
            $current_hours = 0;
            $chart      = new HorizontalBarChart(600,300);
            $dataSet    = new XYDataSet();
            while ($current_hours < 24 ) {
                $hour = date("g:i a",strtotime($current_hours.":00:00"));
                $switch = 0;
                foreach($rows as $row) {
                    if ($row->hour == $current_hours) {
                        $dataSet->addPoint(new Point($hour, $row->cnt))  ;
                        $switch = 1;
                    }
                }
                if ($switch == 0 ) {$dataSet->addPoint(new Point($hour, 0))  ;}
                $current_hours++;
                reset($rows);
            }
            $chart->setDataSet($dataSet);
            $chart->setTitle($_SESSION['preferences']['company_name'] . " appointmnets per hour report.");

            ob_start(); $chart->render(); $b64 = base64_encode(ob_get_contents()); ob_end_clean();
            ?>
            <img src="data:image/png;base64,<?=$b64?>">
            <?
    }
    elseif ( $reportType == 'AppointmentsPerMonthReport') {
        include_once('libchart/classes/libchart.php');
        $rows = $dal->get_AppointmentsPerMonthStats($_SESSION['settings']['company_id'],0);
            $chart      = new VerticalBarChart(600,300);
            $dataSet    = new XYDataSet();
            foreach($rows as $row) {
                    $dataSet->addPoint(new Point($row->monthYear, $row->cnt))  ;
            }
            $chart->setDataSet($dataSet);
            $chart->setTitle($_SESSION['preferences']['company_name'] . " appointments per month report.");
            ob_start(); $chart->render(); $b64 = base64_encode(ob_get_contents()); ob_end_clean();
            ?>
            <img src="data:image/png;base64,<?=$b64?>">
            <?
    }

    #######################################
    #######################################
    elseif ( $reportType == 'ItemsReport_BestSellers') {
    $rows = $dal->get_all_ItemsReport_BestSellers($_SESSION['settings']['company_id'],0);
    if (count($rows) >0 ) {
    foreach($rows as $row)
        {
        $get_last_date_sold = $dal->get_last_date_sold($row->item_id);
        ?>
        <div class="report_rows wp100 " style="background-color:<?=$bg_color;?>;">
            <div class="report_data_cell_wp05"><?=$row->item_id; ?></div>
            <div class="report_data_cell_wp15"><?=$row->style_number; ?></div>
            <div class="report_data_cell_wp30"><?=$row->item_name; ?></div>
            <div class="report_data_cell_wp10"><?=$row->style_item_quantity; ?></div>
            <div class="report_data_cell_wp10"><?=$row->style_sold_quantity_total; ?></div>
            <div class="report_data_cell_wp07"><?=sprintf("%.02f",$row->margin); ?></div>
            <div class="report_data_cell_wp10"><?=money_format('%i', $row->total_revenue_in_for_item); ?></div>
            <div class="report_data_cell_wp10"><?=date("m.d.y",strtotime($get_last_date_sold[0]->last_date_sold)); ?></div>
        </div>
        <?
        if      ( $bg_color == '#DDEEFF')   { $bg_color = "#FFFFFF"; }
        elseif  ( $bg_color == '#FFFFFF')   { $bg_color = "#DDEEFF"; }
        }
    }
    else { ?>
        <div class="report_rows wp100 " style="background-color:<?=$bg_color;?>;">
            <div class="report_data_cell_NoData wp99">NO results returned in your query.</div>
        </div>
    <?}
    }
    #######################################
    #######################################
    elseif ( $reportType == 'ItemsReport_Category') {
    $rows = $dal->get_all_ItemsReport_Category($_SESSION['settings']['company_id'],0);
    if (count($rows) >0 ) {
    foreach($rows as $row)
        {
        //$get_last_date_sold = $dal->get_last_date_sold($row->item_id);
        ?>
        <div class="report_rows wp100 " style="background-color:<?=$bg_color;?>;">
            <div class="report_data_cell_wp25"><?=$row->category_id; ?></div>
            <div class="report_data_cell_wp25"><?=$row->category_name; ?></div>
            <div class="report_data_cell_wp25"><?=$row->total_quantity_sold_from_this_CATEGORY; ?></div>
            <div class="report_data_cell_wp24"><?=money_format('%i', $row->total_revenue_in_for_CATEGORY); ?></div>
        </div>
        <?
        if      ( $bg_color == '#DDEEFF')          { $bg_color = "#FFFFFF"; }
        elseif  ( $bg_color == '#FFFFFF')   { $bg_color = "#DDEEFF"; }
        }
    }
    else { ?>
        <div class="report_rows  wp100" style="background-color:<?=$bg_color;?>;">
            <div class="report_data_cell_NoData wp99">NO results returned in your query.</div>
        </div>
    <?}
    }
    #######################################
    #######################################
    elseif ( $reportType == 'ItemsReport_Department') {
    $rows = $dal->get_all_ItemsReport_Department($_SESSION['settings']['company_id'],0);
    if (count($rows) >0 ) {
    foreach($rows as $row)
        {
        //$get_last_date_sold = $dal->get_last_date_sold($row->item_id);
        ?>
        <div class="report_rows  wp100" style="background-color:<?=$bg_color;?>;">
            <div class="report_data_cell_wp25"><?=$row->department_id; ?></div>
            <div class="report_data_cell_wp25"><?=$row->department_name; ?></div>
            <div class="report_data_cell_wp25"><?=$row->total_quantity_sold_from_this_DEPARTMENT; ?></div>
            <div class="report_data_cell_wp24"><?=money_format('%i', $row->total_revenue_in_for_DEPARTMENT); ?></div>
        </div>
        <?
        if      ( $bg_color == '#DDEEFF')          { $bg_color = "#FFFFFF"; }
        elseif  ( $bg_color == '#FFFFFF')   { $bg_color = "#DDEEFF"; }
        }
    }
    else { ?>
        <div class="report_rows  wp100" style="background-color:<?=$bg_color;?>;">
            <div class="report_data_cell_NoData wp99">NO results returned in your query.</div>
        </div>
    <?}
    }
    #######################################
    #######################################
    elseif ( $reportType == 'ItemsReport_Vendor') {
    $rows = $dal->get_all_ItemsReport_Vendor($_SESSION['settings']['company_id'],0);
    if (count($rows) >0 ) {
    foreach($rows as $row)
        {
        //$get_last_date_sold = $dal->get_last_date_sold($row->item_id);
        ?>
        <div class="report_rows  wp100" style="background-color:<?=$bg_color;?>;">
            <div class="report_data_cell_wp25"><?=$row->supplier_id; ?></div>
            <div class="report_data_cell_wp25"><?=$row->supplier_name; ?></div>
            <div class="report_data_cell_wp25"><?=$row->total_quantity_sold_from_this_supplier; ?></div>
            <div class="report_data_cell_wp24"><?=money_format('%i', $row->total_revenue_in_for_supplier); ?></div>
        </div>
        <?
        if      ( $bg_color == '#DDEEFF')          { $bg_color = "#FFFFFF"; }
        elseif  ( $bg_color == '#FFFFFF')   { $bg_color = "#DDEEFF"; }
        }
    }
    else { ?>
        <div class="report_rows  wp100" style="background-color:<?=$bg_color;?>;">
            <div class="report_data_cell_NoData wp99">NO results returned in your query.</div>
        </div>
    <?}
    }
    #######################################
    #######################################
    elseif ( $reportType == 'ItemsReport_SoldOut') {
    $rows = $dal->get_all_ItemsReport_SoldOut($_SESSION['settings']['company_id'],0);
    if (count($rows) >0 ) {
    foreach($rows as $row)
        {
        //$get_last_date_sold = $dal->get_last_date_sold($row->item_id);
        ?>
        <div class="report_rows  wp100" style="background-color:<?=$bg_color?>;">
            <div class="report_data_cell_5">&nbsp;<?=$row->item_id?></div>
            <div class="report_data_cell_15">&nbsp;<?=$row->style_number?></div>
            <div class="report_data_cell_12">&nbsp;<?=$row->barcode?></div>
            <div class="report_data_cell_32">&nbsp;<?=$row->item_name?></div>
            <div class="report_data_cell_5">&nbsp;<?=$row->ITEM_ID_sold_total?></div>
            <div class="report_data_cell_7">&nbsp;<?=$row->quantity?></div>
            <div class="report_data_cell_9">&nbsp;<?=money_format('%i', $row->buy_price)?></div>
            <div class="report_data_cell_7">&nbsp;<?=money_format('%i', $row->price)?></div>
            <div class="report_data_cell_7">&nbsp;<?=sprintf("%.02f",$row->margin)?></div>
            <div class="report_data_cell_9">&nbsp;<?=money_format('%i', $row->total_revenue_in_for_item)?></div>
        </div>
        <?
        if      ( $bg_color == '#DDEEFF')          { $bg_color = "#FFFFFF"; }
        elseif  ( $bg_color == '#FFFFFF')   { $bg_color = "#DDEEFF"; }
        }
    }
    else { ?>
        <div class="report_rows wp100" style="background-color:<?=$bg_color?>;">
            <div class="report_data_cell_NoData wp99">NO results returned in your query.</div>
        </div>
    <?}
    }
    #######################################
    #######################################
    elseif ( $reportType == 'ItemsReport_AllItems') {
    $rows = $dal->get_all_ItemsReport_AllItems($_SESSION['settings']['company_id'],0);
    if (count($rows) >0 ) {
    foreach($rows as $row)
        {
        //$get_last_date_sold = $dal->get_last_date_sold($row->item_id);
        ?>
        <div class="report_rows wp100 " style="background-color:<?=$bg_color;?>;">
            <div class="report_data_cell_wp04"> <?=$row->item_id; ?></div>
            <div class="report_data_cell_wp07"> <?=date("m.d.y",strtotime($row->added)); ?></div>
            <div class="report_data_cell_wp10"><?=$row->style_number; ?></div>
            <div class="report_data_cell_wp08"><?=$row->barcode; ?></div>
            <div class="report_data_cell_wp17 no-overflow"> <?=$row->item_name; ?></div>
            <div class="report_data_cell_wp14 no-overflow"> <?=$row->attribute1; ?></div>
            <div class="report_data_cell_wp14 no-overflow"> <?=$row->attribute2; ?></div>
            <div class="report_data_cell_wp05"> <?=$row->quantity; ?></div>
            <div class="report_data_cell_wp07"><img class="f_left" src="/common_includes/includes/images/SignDollar-Green16x16.png" height="12" widht="12"><?=$row->price; ?></div>
            <div class="report_data_cell_wp10"><img class="f_left" src="/common_includes/includes/images/SignDollar-Green16x16.png" height="12" widht="12">&nbsp;<?=$row->total_value; ?></div>
        </div>
        <?
        if      ( $bg_color == '#DDEEFF')          { $bg_color = "#FFFFFF"; }
        elseif  ( $bg_color == '#FFFFFF')   { $bg_color = "#DDEEFF"; }
        }
    }
    else { ?>
        <div class="report_rows wp100" style="background-color:<?=$bg_color;?>;">
            <div class="report_data_cell_NoData wp99">NO results returned in your query.</div>
        </div>
    <?}
    }

}

function dynamic_pannel_search_date($search_by_field,$timezone) {
    date_default_timezone_set($timezone);
    if ($search_by_field == "start_date"){
        $display_date = date("Y/m",time()) . "/01";
    } else {
        $display_date = date("Y/m/d",time());
    }

    ?>
    <script type="text/javascript">
            $("#dynamic_pannel_<?=$search_by_field?>").datepicker({dateFormat: 'yy/mm/dd'});
    </script>
    <div class="d_InlineBlock f_left wp100">
            <div class="d_InlineBlock wp100"><?=ucfirst($search_by_field)?></div>
            <div class="d_InlineBlock wp100">
                <input type="text" maxlength="10" id="dynamic_pannel_<?=$search_by_field?>" value="<?=$display_date?>" class="wp90 dynamic_pannel_search date" >
            </div>
    </div>
<?
}
function dynamic_pannel_search_ID                () {
?>
    <div class="f_left wp10 hp100 box5">
            <div class="f_left wp20 hp100">ID</div>
            <div class="f_left wp80 hp100">
                <input type="text" class="wp90 dynamic_pannel_search" name="id_search" id="dynamic_pannel_id_search" value="" >
            </div>
    </div>
<?
}
function dynamic_pannel_search_style_number      () {
?>
    <div class="f_left wp15 hp100 box5">
            <div class="f_left wp35 hp100">Style #</div>
            <div class="f_left wp65 hp100">
                <input type="text" class="wp90 dynamic_pannel_search" name="style_number_search" id="dynamic_pannel_style_number_search" maxlength="20" size="10" value="" >
            </div>
    </div>
<?
}
function dynamic_pannel_search_barcode           () {
?>
    <div class="f_left wp15 hp100 box5">
            <div class="f_left wp35 hp100">Bar-code#</div>
            <div class="f_left wp65 hp100">
                <input type="text" class="wp90 dynamic_pannel_search" name="barcode_search" id="dynamic_pannel_barcode_search" maxlength="15" size="11" value="" >
            </div>
    </div>
<?
}
function dynamic_pannel_search_button            () {
?>
    <div class="f_left wp20 hp100 box5">
            <div class="f_left wp40 hp100">
                <button onclick="ReportData(0,'<?=$_SESSION['reportType']; ?>');" title="Search">Search</button>
            </div>
            <div class="f_left wp60 hp100">
                <? if ( $_SESSION['dynamic_pannel_advanced_search'] == 1 ) { ?>
                    <button onclick="listingAdvancedSearch('dynamic_pannel');" title="Advanced" id="dynamic_pannel_advanced_toggle">More Options</button>
                <? } ?>
                &nbsp;
            </div>
    </div>
<?
}

function dynamic_pannel_advanced_search_Customer () {
?>
<div class="inline_block">
    <div class="">Customer</div>
    <div class="">
        <input type="text" tabindex="auto_tabindex" onkeypress="if (onEnterKey(event,function () {  listingSearch('dynamic_pannel','dynamic_pannel_search'); })) return false; return true;" class=" dynamic_pannel_search string data_control" name="customer_search" id="dynamic_pannel_customer_search" maxlength="255" size="10" value="" autocomplete="off">
    </div>
</div>
<?
}
function dynamic_pannel_advanced_search_Employee ($search_by_field,$timezone) {
?>
<div class="d_InlineBlock f_left wp100">
    <div class="d_InlineBlock wp100">Employee</div>
    <div class="d_InlineBlock wp100">
        <select class="wp90 dynamic_pannel_search data_control" id="dynamic_pannel_<?=$search_by_field?>">
            <option value="-1">All</option>
                <?
                $dal = new GENERAL_DAL();
                $employees = $dal->get_AllEmployeesPerCompanyId($_SESSION['settings']['company_id'],1,0);
                foreach($employees as $employee){ ?><option value="<?=$employee->id; ?>"><?=$employee->username;?></option><? } ?>
        </select>
    </div>
</div>
<?
}
function dynamic_pannel_advanced_search_Register () {
?>
<div class="inline_block">
    <div class="">Register</div>
    <div class="">
        <select tabindex="auto_tabindex" onkeypress="if (onEnterKey(event,function () {  listingSearch('dynamic_pannel','dynamic_pannel_search'); })) return false; return true;" class=" dynamic_pannel_search data_control" id="dynamic_pannel_register_id" name="register_id">
            <option value="-1">All</option>
            <option value="2">Front Register</option>
            <option value="3">WEBSITE</option>
        </select>
    </div>
</div>
<?
}
function dynamic_pannel_advanced_search_SalesTax () {
?>
<div class="inline_block">
    <div class="">Sales Tax</div>
    <div class="">
        <select tabindex="auto_tabindex" onkeypress="if (onEnterKey(event,function () {  listingSearch('dynamic_pannel','dynamic_pannel_search'); })) return false; return true;" class=" dynamic_pannel_search data_control" id="dynamic_pannel_taxcat_name" name="taxcat_name">
            <option value="-1">All</option>
            <option value="0">None</option>
            <option value="2">Sales Tax</option>
        </select>
    </div>
</div>
<?
}

function dynamic_pannel_advanced_search_Suppliers   ($onclick=0) {
    if ($onclick !== 0) { $OnClickAction = "onchange=". $onclick . ";"; } else { $OnClickAction = $onclick; }
    $dal = new DAL();
    $suppliers = $dal->get_AllSuppliersPerCompanyId($_SESSION['settings']['company_id']);    
?>
<div class="d_InlineBlock f_left wp100">
    <div class="d_InlineBlock wp100">Suppliers: (<?=count($suppliers)?>)</div>
    <div class="d_InlineBlock wp100">
        <select class=" dynamic_pannel_search data_control" id="dynamic_pannel_supplier" name="supplier"  <?=$OnClickAction?> >
            <option value="-1">All</option>
                <?
                foreach($suppliers as $supplier) { 
                    if ( isset($_SESSION['search_data']['item_search']['item_search_supplier']) && 
                               $_SESSION['search_data']['item_search']['item_search_supplier']  == $supplier->id 
                       )
                       { $selected = 'selected'; } else {$selected = ''; }
                ?>
                    <option value="<?=$supplier->id; ?>" <?=$selected?> > <?=$supplier->name; ?></option>
                <? } 
                ?>
        </select>
    </div>
</div>
<?
}
function dynamic_pannel_advanced_search_Brands      ($onclick=0) {
    if ($onclick !== 0) { $OnClickAction = "onchange=". $onclick . ";"; } else { $OnClickAction = $onclick; }
    $dal = new DAL();
    $brands = $dal->get_AllBrandsPerCompanyId($_SESSION['settings']['company_id']);
    ?>
<div class="d_InlineBlock f_left wp100">
    <div class="d_InlineBlock wp100">Brands: (<?=count($brands)?>)</div>
    <div class="d_InlineBlock wp100">
        <select class=" dynamic_pannel_search data_control" id="dynamic_pannel_brand" name="brand"  <?=$OnClickAction?> >
            <option value="-1">All</option>
                <?
                foreach($brands as $brand) {
                    if ( isset($_SESSION['search_data']['item_search']['item_search_brand']) && 
                               $_SESSION['search_data']['item_search']['item_search_brand']  == $brand->id 
                       )
                       { $selected = 'selected';} else {$selected = ''; }
                ?>
                    <option value="<?=$brand->id; ?>" <?=$selected?> ><?=$brand->name; ?></option>
                <? } ?>
        </select>
    </div>
</div>
<?
}
function dynamic_pannel_advanced_search_Departments ($onclick=0) {
    if ($onclick !== 0) { $OnClickAction = "onchange=". $onclick . ";"; } else { $OnClickAction = $onclick; }
    $dal = new DAL();
    $departments = $dal->get_AllDepartmentsPerCompanyId($_SESSION['settings']['company_id']);
    ?>
<div class="d_InlineBlock f_left wp100">
    <div class="d_InlineBlock wp100">Departments: (<?=count($departments)?>)</div>
    <div class="d_InlineBlock wp100">
        <select class=" dynamic_pannel_search data_control" id="dynamic_pannel_department" name="department"  <?=$OnClickAction?> >
            <option value="-1">All</option>
                <?
                foreach($departments as $department) { 
                    if ( isset($_SESSION['search_data']['item_search']['item_search_department']) && 
                               $_SESSION['search_data']['item_search']['item_search_department']  == $department->id 
                       )
                       { $selected = 'selected';} else {$selected = ''; }                    
                ?>
                <option value="<?=$department->id; ?>" <?=$selected?> ><?=$department->name; ?></option>
                <? } 
                ?>
        </select>
    </div>
</div>
<?
}
function dynamic_pannel_advanced_search_Categories  ($onclick=0) {
    if ($onclick !== 0) { $OnClickAction = "onchange=". $onclick . ";"; } else { $OnClickAction = $onclick; }
    $dal = new DAL();
    $categories = $dal->get_AllCategoriesPerCompanyId($_SESSION['settings']['company_id']);
    ?>
<div class="d_InlineBlock f_left wp100">
    <div class="d_InlineBlock wp100">Categories: (<?=count($categories)?>)</div>
    <div class="d_InlineBlock wp100">
        <select class=" dynamic_pannel_search data_control" id="dynamic_pannel_category" name="category" <?=$OnClickAction?> >
            <option value="-1">All</option>
                <?
                foreach($categories as $category) { 
                    if ( isset($_SESSION['search_data']['item_search']['item_search_category']) && 
                               $_SESSION['search_data']['item_search']['item_search_category']  == $category->id 
                       )
                       { $selected = 'selected'; print "yeah";} else {$selected = ''; }                    
                ?>
                <option value="<?=$category->id; ?>" <?=$selected?> > <?=$category->name; ?></option>
                <? } 
                ?>
        </select>
    </div>
</div>
<?
}
function dynamic_pannel_advanced_search_styleNumber ($onclick=0) {
    if ($onclick !== 0) { $OnClickAction = "onchange=". $onclick . ";"; } else { $OnClickAction = $onclick; }
    $dal = new DAL();
    $styleNumbers = $dal->get_AllStyleNumbersPerCompanyId($_SESSION['settings']['company_id']);
    ?>
<div class="d_InlineBlock f_left wp100">
    <div class="d_InlineBlock wp100">Style Number: (<?=count($styleNumbers)?>)</div>
    <div class="d_InlineBlock wp100">
        <select class=" dynamic_pannel_search data_control" id="dynamic_pannel_styleNumber" name="styleNumber" <?=$OnClickAction?> >
            <option value="-1">All</option>
                <?
                foreach($styleNumbers as $styleNumber){ 
                    if ( isset($_SESSION['search_data']['item_search']['item_search_styleNumber']) && 
                               $_SESSION['search_data']['item_search']['item_search_styleNumber']  == $styleNumber->number 
                       )
                       { $selected = 'selected';} else {$selected = ''; }                
                ?>
                <option value="<?=$styleNumber->number; ?>" <?=$selected?> > <?=$styleNumber->number; ?></option>
                <? } 
                ?>
        </select>
    </div>
</div>
<?
}

function daily_lender_sales_summary($reportDAL,$to,$from) {
    $SaleRows = $reportDAL->get_sales_by_date_range($_SESSION['settings']['company_id'],$to,$from);

    $salesMade = count($SaleRows);
    foreach($SaleRows as $Sale) {
        $sale_detail_total_price = $sale_detail_total_discount = $sale_detail_total_tax = 0;
        $SaleDetailsRows = $reportDAL->get_sales_details_by_sale_id($Sale->id);
        foreach($SaleDetailsRows as $SaleDetails) {
            $sale_detail_total_price += $SaleDetails->price_total ;
            $sale_detail_total_discount += $SaleDetails->discount_total + $SaleDetails->additional_discount ;
            $sale_detail_total_tax += $SaleDetails->tax_total ;
        }
    $total_price    += $sale_detail_total_price ;
    $total_discount += $sale_detail_total_discount ;
    $total_tax      += $sale_detail_total_tax ;
    }
    $total_net = $total_price - $total_discount + $total_tax;
?>
    <table border="1">
            <tr>
                <td class="HEADER main_bc_color1 main_color1_text center" style='width: 200px; font-size: 0.7em; line-height: 110%; font-family: Arial;  margin-bottom: 5px;' colspan="2">SALES SUMMARY</td>
            </tr>
            <tr>
                <td style='width: 75%; text-align: left; font-size: 0.7em; line-height: 110%; font-family: Arial;  margin-bottom: 5px;'>SALES MADE:</td>
                <td style='width: 15%; margin-right: 5px; text-align: right; font-size: 0.7em; line-height: 110%; font-family: Arial;  margin-bottom: 5px;'><?=floatval(count($SaleRows));?></td>
            </tr>
            <tr style="background-color:#DDEEFF;">
                <td style='text-align: left; font-size: 0.7em; line-height: 110%; font-family: Arial;  margin-bottom: 5px;'>TOTAL PRICE:</td>
                <td style='text-align: right; font-size: 0.7em; line-height: 110%; font-family: Arial;  margin-bottom: 5px; '>&nbsp;<?=money2($total_price)?></td>
            </tr>

            <tr style="background-color:#DDEEFF;">
                <td style='text-align: left; font-size: 0.7em; line-height: 110%; font-family: Arial;  margin-bottom: 5px;'>TOTAL DISCOUNT:</td>
                <td style='text-align: right; font-size: 0.7em; line-height: 110%; font-family: Arial;  margin-bottom: 5px;'>-&nbsp;<?=money2($total_discount)?></td>
            </tr>
            <tr style="background-color:#DDEEFF;">
                <td style='text-align: left; font-size: 0.7em; line-height: 110%; font-family: Arial;  margin-bottom: 5px;'>TOTAL TAX:</td>
                <td style='text-align: right; font-size: 0.7em; line-height: 110%; font-family: Arial;  margin-bottom: 5px;'>&nbsp;<?=money2($total_tax)?></td>
            </tr>
            <tr>
                <td style='text-align: left; font-size: 0.7em; line-height: 110%; font-family: Arial;  margin-bottom: 5px;'>TOTAL NET:</td>
                <td style='text-align: right; font-size: 0.7em; line-height: 110%; font-family: Arial;  margin-bottom: 5px;'>&nbsp;<?=money2($total_net)?></td>
            </tr>
    </table>

<?

}
function daily_lender_payment_summary($reportDAL,$to,$from) {
    $SalesRows = $reportDAL->get_sales_by_date_range($_SESSION['settings']['company_id'],$to,$from);
    $cashPayments = 0;

    $voucherPaymentsRows = $reportDAL->get_vouchers_by_company_id_n_date($_SESSION['settings']['company_id'],$to,$from);
    foreach($voucherPaymentsRows as $voucherPayment) {
        $voucherPayments++;
        $total_voucherPayment += $voucherPayment->value ;
    }

    foreach($SalesRows as $Sale) {
        if ($Sale->CashPaid != "0") {
            $cashPayments++;
            $total_cashPayment += $Sale->CashPaid ;
        }
        $cardPaymentsRows = $reportDAL->get_card_sales_by_sale_id($Sale->id);
        foreach($cardPaymentsRows as $cardPayment) {
            $cardPayments++;
            $total_cardPayment += $cardPayment->amount ;
        }
        $redeemedVoucherPaymentsRows = $reportDAL->get_redeemed_vouchers_by_sale_id($Sale->id);
        foreach($redeemedVoucherPaymentsRows as $redeemedVoucherPayment) {
            $redeemedVoucherPayments++;
            $total_redeemedVoucherPayment += $redeemedVoucherPayment->value ;
        }
        $payinfo = pay($Sale->price_total - ($Sale->discount_total + $Sale->additional_discount) + $Sale->tax_total, 0, $total_cardPayment, $total_redeemedVoucherPayment);
        $cash_returned += $payinfo['cash'];
    }
?>
<table border="1">
        <tr>
            <td class="HEADER main_bc_color1 main_color1_text center" style='width: 200px; font-size: 0.7em; line-height: 110%; font-family: Arial;  margin-bottom: 5px;' colspan="2">PAYMENTS SUMMARY</td>
        </tr>
        <tr style="background-color:#DDEEFF;">
            <td style='width: 75%; text-align: left; font-size: 0.7em; line-height: 110%; font-family: Arial;  margin-bottom: 5px;'>CASH PAYMENTS:</td>
            <td style='width: 15%; margin-right: 5px; text-align: right; font-size: 0.7em; line-height: 110%; font-family: Arial;  margin-bottom: 5px;'><?=floatval($cashPayments);?></td>
        </tr>
        <tr style="background-color:#DDEEFF;">
            <td style='text-align: left; font-size: 0.7em; line-height: 110%; font-family: Arial;  margin-bottom: 5px;'>TOTAL VALUE:</td>
            <td style='text-align: right; font-size: 0.7em; line-height: 110%; font-family: Arial;  margin-bottom: 5px; '>&nbsp;<?=money2($total_cashPayment)?></td>
        </tr>
        
        <tr>
            <td style='width: 75%; text-align: left; font-size: 0.7em; line-height: 110%; font-family: Arial;  margin-bottom: 5px;'>CARD PAYMENTS:</td>
            <td style='width: 15%; margin-right: 5px; text-align: right; font-size: 0.7em; line-height: 110%; font-family: Arial;  margin-bottom: 5px;'><?=floatval($cardPayments);?></td>
        </tr>
        <tr>
            <td style='text-align: left; font-size: 0.7em; line-height: 110%; font-family: Arial;  margin-bottom: 5px;'>TOTAL VALUE:</td>
            <td style='text-align: right; font-size: 0.7em; line-height: 110%; font-family: Arial;  margin-bottom: 5px; '>&nbsp;<?=money2($total_cardPayment)?></td>
        </tr>

        
        <tr style="background-color:#DDEEFF;">
            <td style='width: 75%; text-align: left; font-size: 0.7em; line-height: 110%; font-family: Arial;  margin-bottom: 5px;'>VOUCHERS CREATED:</td>
            <td style='width: 15%; margin-right: 5px; text-align: right; font-size: 0.7em; line-height: 110%; font-family: Arial;  margin-bottom: 5px;'><?=floatval($voucherPayments)?></td>
        </tr>
        <tr style="background-color:#DDEEFF;">
            <td style='text-align: left; font-size: 0.7em; line-height: 110%; font-family: Arial;  margin-bottom: 5px;'>TOTAL VOUCHER AMOUNT:</td>
            <td style='text-align: right; font-size: 0.7em; line-height: 110%; font-family: Arial;  margin-bottom: 5px; '>&nbsp;<?=money2($total_voucherPayment)?></td>
        </tr>
        <tr>
            <td style='width: 75%; text-align: left; font-size: 0.7em; line-height: 110%; font-family: Arial;  margin-bottom: 5px;'>REDEEMED VOUCHERS:</td>
            <td style='width: 15%; margin-right: 5px; text-align: right; font-size: 0.7em; line-height: 110%; font-family: Arial;  margin-bottom: 5px;'><?=floatval($redeemedVoucherPayments)?></td>
        </tr>
        <tr>
            <td style='text-align: left; font-size: 0.7em; line-height: 110%; font-family: Arial;  margin-bottom: 5px;'>REDEEMED VALUE TOTAL</td>
            <td style='text-align: right; font-size: 0.7em; line-height: 110%; font-family: Arial;  margin-bottom: 5px; '>&nbsp;<?=money2($total_redeemedVoucherPayment)?></td>
        </tr>
        <tr>
            <td style='text-align: left; font-size: 0.7em; line-height: 110%; font-family: Arial;  margin-bottom: 5px;'>TOTAL NET:</td>
            <td style='text-align: right; font-size: 0.7em; line-height: 110%; font-family: Arial;  margin-bottom: 5px;'>&nbsp;<?=money2(0)?></td>
        </tr>
</table>
<?
}
function daily_lender_card_payment_summary($reportDAL,$to,$from) {
    $PaymentRows = $reportDAL->get_sales_by_date_range($_SESSION['settings']['company_id'],$to,$from);
    foreach($PaymentRows as $Payment) {
        $cardPaymentsRows = $reportDAL->get_card_sales_by_sale_id($Payment->id);
        foreach($cardPaymentsRows as $cardPayment) {
            $Ledger{$cardPayment->cardname}{'count'}++;
            $Ledger{$cardPayment->cardname}{'total'} += $cardPayment->amount;
            $cardPayments++;
            $total_cardPayment += $cardPayment->amount ;
        }

    }
?>
<table border="1">
        <tr>
            <td class="HEADER main_bc_color1 main_color1_text center" style='width: 200px; font-size: 0.7em; line-height: 110%; font-family: Arial;  margin-bottom: 5px;' colspan="2">CARD PAYMENTS</td>
        </tr>
        <? 
        if (is_array($Ledger) ) {
            foreach (array_keys($Ledger) as $cardname) {?>
            <tr style="background-color:#DDEEFF;">
                <td style='width: 75%; text-align: left; font-size: 0.7em; line-height: 110%; font-family: Arial;  margin-bottom: 5px;'><?=ucfirst($cardname)?> PAYMENTS:</td>
                <td style='width: 15%; margin-right: 5px; text-align: right; font-size: 0.7em; line-height: 110%; font-family: Arial;  margin-bottom: 5px;'><?=floatval($Ledger{$cardname}{'count'})?></td>
            </tr>
            <tr style="background-color:#DDEEFF;">
                <td style='text-align: left; font-size: 0.7em; line-height: 110%; font-family: Arial;  margin-bottom: 5px;'>TOTAL VALUE:</td>
                <td style='text-align: right; font-size: 0.7em; line-height: 110%; font-family: Arial;  margin-bottom: 5px; '>&nbsp;<?=money2($Ledger{$cardname}{'total'})?></td>
            </tr>
            <?
            }?>
            <tr>
                <td style='text-align: left; font-size: 0.7em; line-height: 110%; font-family: Arial;  margin-bottom: 5px;'>TOTAL CARD CHARGES</td>
                <td style='text-align: right; font-size: 0.7em; line-height: 110%; font-family: Arial;  margin-bottom: 5px; '>&nbsp;<?=money2($total_cardPayment)?></td>
            </tr>
        <?} else {?>
        <tr>
            <td style='width: 200px; font-size: 0.7em; line-height: 110%; font-family: Arial;  margin-bottom: 5px;' colspan="2">There weren't any Cards Processes</td>
        </tr>
        <? } ?>
</table>
<?
}
function daily_lender_voucher_payment_summary($reportDAL,$to,$from) {
        $voucherPaymentsRows = $reportDAL->get_vouchers_by_company_id_n_date($Payment->id,$to,$from);
        if (is_array($voucherPaymentsRows) ) {
            foreach($voucherPaymentsRows as $voucherPayment) {
                $Ledger{$voucherPayment->type}{'count'}++;
                $Ledger{$voucherPayment->type}{'total'} += $voucherPayment->value;
                $cardPayments++;
                $total_voucherPayment += $voucherPayment->value ;
            }
        }
?>
<table border="1">
        <tr>
            <td class="HEADER main_bc_color1 main_color1_text center" style='width: 200px; font-size: 0.7em; line-height: 110%; font-family: Arial;  margin-bottom: 5px;' colspan="2">VOUCHER PAYMENTS</td>
        </tr>
        <? 
        if (is_array($Ledger) ) {
            foreach (array_keys($Ledger) as $vouchertype) {?>
            <tr style="background-color:#DDEEFF;">
                <td style='width: 75%; text-align: left; font-size: 0.7em; line-height: 110%; font-family: Arial;  margin-bottom: 5px;'><?=ucfirst($vouchertype)?>&nbsp;VOUCHERS:</td>
                <td style='width: 15%; margin-right: 5px; text-align: right; font-size: 0.7em; line-height: 110%; font-family: Arial;  margin-bottom: 5px;'><?=floatval($Ledger{$vouchertype}{'count'})?></td>
            </tr>
            <tr style="background-color:#DDEEFF;">
                <td style='text-align: left; font-size: 0.7em; line-height: 110%; font-family: Arial;  margin-bottom: 5px;'>TOTAL VALUE:</td>
                <td style='text-align: right; font-size: 0.7em; line-height: 110%; font-family: Arial;  margin-bottom: 5px; '>$&nbsp;<?=floatval($Ledger{$vouchertype}{'total'})?></td>
            </tr>
            <?
            }
        } else {?>
        <tr>
            <td style='width: 200px; font-size: 0.7em; line-height: 110%; font-family: Arial;  margin-bottom: 5px;' colspan="2">There weren't any Vouchers Processes.</td>
        </tr>
        <? } ?>
</table>
<?
}

function ReportsSearchStanza($reportType) {
    if          ($reportType == 'SalesReport')   {?>
    <div id="item_SearchStanza" class="hp100 wp100 InventoryMgmtBodyCenter">
        <div class="wp95 hp100 d_InlineBlock">
            <?=reports_search_div('start_date','text',$reportType,09,0,0)?>
            <?=reports_search_div('end_date','text',$reportType,09,0,0)?>
            <?=reports_search_div('staff_id','text',$reportType,09,0,0)?>
            <?=reports_search_div('submit','checkbox',$reportType,08,0,'Reports_'.$reportType.'_searchBy')?>
        </div>
    </div>
    <? } elseif ($reportType == 'SalesPerHourReport') { ?>
    <div id="item_SearchStanza" class="hp100 wp100 InventoryMgmtBodyCenter">
        <div class="wp95 hp100 d_InlineBlock">
            <?=reports_search_div('start_date','text',$reportType,09,0,0)?>
            <?=reports_search_div('end_date','text',$reportType,09,0,0)?>
            <?=reports_search_div('staff_id','text',$reportType,09,0,0)?>
            <?=reports_search_div('submit','checkbox',$reportType,08,0,'Reports_'.$reportType.'_searchBy')?>
        </div>
    </div>
    <? } elseif ($reportType == 'SalesPerMonthReport') { ?>
    <div id="item_SearchStanza" class="hp100 wp100 InventoryMgmtBodyCenter">
        <div class="wp95 hp100 d_InlineBlock">
            <?=reports_search_div('start_date','text',$reportType,09,0,0)?>
            <?=reports_search_div('end_date','text',$reportType,09,0,0)?>
            <?=reports_search_div('staff_id','text',$reportType,09,0,0)?>
            <?=reports_search_div('submit','checkbox',$reportType,08,0,'Reports_'.$reportType.'_searchBy')?>
        </div>
    </div>
    <? } elseif ($reportType == 'DailyInventoryReport') { ?>
    <div id="item_SearchStanza" class="hp100 wp100 InventoryMgmtBodyCenter">
        <div class="wp95 hp100 d_InlineBlock">
            <?=reports_search_div('start_date','text',$reportType,09,0,0)?>
            <?=reports_search_div('end_date','text',$reportType,09,0,0)?>
            <?=reports_search_div('submit','checkbox',$reportType,08,0,'Reports_'.$reportType.'_searchBy')?>
        </div>
    </div>
    <? } elseif ($reportType == 'AppointmentsPerHourReport') { ?>
    <div id="item_SearchStanza" class="hp100 wp100 InventoryMgmtBodyCenter">
        <div class="wp95 hp100 d_InlineBlock">
            <?=reports_search_div('start_date','text',$reportType,09,0,0)?>
            <?=reports_search_div('end_date','text',$reportType,09,0,0)?>
            <?=reports_search_div('staff_id','text',$reportType,09,0,0)?>
            <?=reports_search_div('submit','checkbox',$reportType,08,0,'Reports_'.$reportType.'_searchBy')?>
        </div>
    </div>
    <? } elseif ($reportType == 'AppointmentsPerMonthReport') { ?>
    <div id="item_SearchStanza" class="hp100 wp100 InventoryMgmtBodyCenter">
        <div class="wp95 hp100 d_InlineBlock">
            <?=reports_search_div('start_date','text',$reportType,09,0,0)?>
            <?=reports_search_div('end_date','text',$reportType,09,0,0)?>
            <?=reports_search_div('staff_id','text',$reportType,09,0,0)?>
            <?=reports_search_div('submit','checkbox',$reportType,08,0,'Reports_'.$reportType.'_searchBy')?>
        </div>
    </div>
    <? } elseif ($reportType == 'ItemsReport_BestSellers') { ?>
    <div id="item_SearchStanza" class="hp100 wp100 InventoryMgmtBodyCenter">
        <div class="wp95 hp100 d_InlineBlock">
            <?=reports_search_div('start_date','text',$reportType,09,0,0)?>
            <?=reports_search_div('end_date','text',$reportType,09,0,0)?>
            <?=reports_search_div('submit','checkbox',$reportType,08,0,'Reports_'.$reportType.'_searchBy')?>
        </div>
    </div>
    <? } ?>
<?
}
    function reports_search_div($search_by_field,$data_type,$reportType,$height_percent,$OnClickAction=0,$SubmitFunction=0){
    $general_dal    = new GENERAL_DAL();
    $PreferenceData = $general_dal->get_CompanyPreference_by_Company_ID($_SESSION['settings']['company_id'],'timezone');
    $timezone = $PreferenceData[0]->value;
    ?>
        <div class="d_InlineBlock mb5 bctrt wp100 hp<?=$height_percent?>" >
            <?       if ($search_by_field == 'item_barcode' || $search_by_field == 'item_name' ) { ?>
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
                        onkeyup="if (this.value.length == 12) { Reports_SalesReport_searchBy('<?=$reportType?>'); }"
                        x-webkit-speech>
                </div>
            <? } elseif  ( ($search_by_field == 'start_date' ) ) { ?>
                <div class="d_InlineBlock wp100 hp60 s07 center">
                   <? dynamic_pannel_search_date($search_by_field,$timezone);?>
                </div>
            <? } elseif  ( ($search_by_field == 'end_date' ) ) { ?>
                <div class="d_InlineBlock wp100 hp60 s07 center">
                   <? dynamic_pannel_search_date($search_by_field,$timezone);?>
                </div>
            <? } elseif  ( ($search_by_field == 'staff_id' ) ) { ?>
                <div class="d_InlineBlock wp100 hp60 s07 center">
                   <? dynamic_pannel_advanced_search_Employee($search_by_field,$timezone);?>
                </div>
            <? } elseif  ( ($search_by_field == 'submit' ) ) { ?>
                <div class="f_left hp100 wp100">
                    <input class="button s08 wp90" type="submit" value="Search" onclick="<?=$SubmitFunction?>('<?=$reportType?>');">
                </div>
            <? } ?>
        </div>
    <?}?>