<?php
require_once('general_functions.php');
require_once('shop_general_functions.php');
require_once('shop_sale_functions.php');

class SALES_DAL {
    public function __construct(){}
    public function get_AllCustomersPerCompanyId($company_id,$totals){
    if ($totals == 1) {
        $sql = "SELECT id, status, email, firstname, surname as lastname";
    }
    ELSE {
        $sql ="SELECT count(distinct(id)) as count ";
    }

  $sql.= " from customers
            where company_id = $company_id";
    if ($totals == 0) {
        if ( isset($_SESSION['search_data']['paging_page']) && $_SESSION['search_data']['paging_page'] != 0) {
            if ( $_SESSION['search_data']['paging_page'] == 1) { $limit_offset = 0; }
            else { $limit_offset = ( ($_SESSION['search_data']['paging_page'] - 1) * 10 ) + 1 ; }
            $sql .= " limit $limit_offset,10";
        }
    }
    $sql.= " order by lastname, firstname";
    #print $sql . "\n";
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

function set_payinfo_values($sale) {
    $paidbycard = $paidbyvoucher = 0;
    if (isset($sale['card_payments'])   && is_array($sale['card_payments'])) {
            foreach (array_keys($sale['card_payments']) as $id) {
                    $paidbycard += $sale['card_payments'][$id]['amount'];
            }
    }
    if (isset($sale['voucher_payments'])&& (is_array($sale['voucher_payments']))) {
            foreach (array_keys($sale['voucher_payments']) as $id) {
                    $paidbyvoucher += $sale['voucher_payments'][$id]['value'];
            }
    }

    $payinfo = pay($sale['totals']['price'] - $sale['totals']['discount'] + $sale['totals']['tax'], $sale['cash_payment'], $paidbycard, $paidbyvoucher);
    if (isset($sale['check_finish'])    && $sale['check_finish']    == 1) {
        unset($sale['check_finish']);
        if ($payinfo['open'] == 0) {
                $sale['finish'] = 1;
        }
    }
    return $payinfo;
}

function returns() {
        if ($_SESSION['settings']['site'] == 'returns' || $_SESSION['settings']['site'] == 'exchanges') { $return = &$_SESSION['return']; } else { $return = &$_SESSION['return2']; }
        calc_return_totals($return);
        if ($_SESSION['settings']['site'] == 'returns') {?>
            <div class='s15 bold mb20 mt10'>RETURNS</div>
        <? }

        if (isset($return['receipt_id'])) {?>
            <div class='s08 bold mb20'>RETURN RECEIPT BARCODE: <?=$return['receipt_id']?></div>
        <?}
        if (isset($_SESSION['settings']['returns']['add_items'])) {?>
            <form method='post'>
              <div class='s09'>Select a sale to accept return items from it:</div>
              <div class='mt10'><span class='s09'>Sale receipt barcode:</span><input class='ml10 w120 text' type='text' name='receipt_id' id='focusitem' value=''/><input class='ml2 button' type='submit' value='SHOW SALE DETAILS'/></div>
              <input type='hidden' name='show_sale_details' value='1'/>
              <input type='hidden' name='return_management' value='1'/>
            </form>
        <?}

        if ($_SESSION['settings'][$_SESSION['settings']['site']]['sale_receipt_id'] != "") {
            $result = select_db(sprintf('s.id, convert_tz(s.added, "utc", %s) as added, l.username as clerk, coalesce(l2.username, l.username) as sales_person', quote_smart($_SESSION['preferences']['timezone'])), 'sales as s left join logins as l on l.id = s.login_id left join logins as l2 on l.id = s.sales_person_id', sprintf('s.company_id = %s and s.receipt_id = %s and s.deleted is null', quote_smart($_SESSION['settings']['company_id']), quote_smart($_SESSION['settings'][$_SESSION['settings']['site']]['sale_receipt_id'])));
            $total = 0;
            if ($result_array = mysql_fetch_array($result)) {?>
            <div class='<?=$_SESSION['settings']['site'] == 'returns' ? 'mt20' : ''?> s09'><?=isset($return['receipt_id']) ? 'Here are the details of the sale of the selected return item' : 'Please choose the return items from the this sale' ?>:</div>
            <table class='mt15'>
              <tr class='vtop'>
                <td class='center'>
                  <table class='b1sl b1st'>
                    <tr class='vtop bctrt'>
                      <td class='s09 bold p5 center b1sr b1sb' colspan='2'>SALE DETAILS</td>
                    </tr>
                    <tr class='vtop bctr1a'>
                      <td class='bold s08 left p5 b1sr b1sb'>RECEIPT BARCODE:</td><td class='s08 pl10 left p5 b1sr b1sb'><?=$_SESSION['settings'][$_SESSION['settings']['site']]['sale_receipt_id']?></td>
                    </tr>
                    <tr class='vtop bctr1b'>
                      <td class='bold s08 left p5 b1sr b1sb'>SALE DATE:</td><td class='s08 pl10 left p5 b1sr b1sb'><?=$result_array['added']?></td>
                    <?if (!empty($result_array['clerk'])) {?>
                    </tr>
                    <tr class='vtop bctr1a'>
                      <td class='bold s08 left p5 b1sr b1sb'>CLERK:</td><td class='s08 pl10 left p5 b1sr b1sb'><?=$result_array['clerk']?></td>
                    <?}
                    if (!empty($result_array['sales_person'])) {?>
                    </tr>
                    <tr class='vtop bctr1b'>
                      <td class='bold s08 left p5 b1sr b1sb'>SALES PERSON:</td><td class='s08 pl10 left p5 b1sr b1sb'><?=$result_array['sales_person']?></td>
                    <?}?>
                    </tr>
                  </table>
                </td>
                <td class='center pl10'>
                <?
                $result2 = select_db('si.id, b.name as brandname, i.id as itemid, i.barcode as itembarcode, i.name as itemname, i.number as itemnumber, i.style as itemstyle, i.attribute1 as itemattribute1, i.attribute2 as itemattribute2, si.price as itemprice, si.discount as itemdiscount, si.additional_discount, si.quantity, si.tax, sum(coalesce(ri.quantity, 0)) as returns, c.attribute1 as catt1, c.attribute2 as catt2', 'sales as s join sale_items as si on s.id = si.sale_id join items as i on i.id = si.item_id join categories as c on c.id = i.category_id left join brands as b on b.id = i.brand_id left join return_items as ri on ri.sale_item_id = si.id', sprintf('s.company_id = %s and s.receipt_id = %s group by si.id', quote_smart($_SESSION['settings']['company_id']), quote_smart($_SESSION['settings'][$_SESSION['settings']['site']]['sale_receipt_id'])));
                $result3 = select_db('v.value, count(v.id) as quantity', 'vouchers as v join sales as s on s.id = v.origin_id', sprintf('v.company_id = %s and v.type = "gift_certificate" and s.receipt_id = %s group by v.value', quote_smart($_SESSION['settings']['company_id']), quote_smart($_SESSION['settings'][$_SESSION['settings']['site']]['sale_receipt_id'])));

                $totalprice = $totaltax = $totaldiscount = 0;
                while ($result_array2 = mysql_fetch_array($result2)) {
                        $vals = calc($result_array2['itemprice'], $result_array2['itemdiscount'], $result_array2['additional_discount'], $result_array2['tax'], $result_array2['quantity']);
                        $totalprice += $vals['price'];
                        $totaltax += $vals['tax'];
                        $totaldiscount += $vals['discount'];
                }
                if (mysql_num_rows($result2) > 0) {
                        mysql_data_seek($result2, 0);
                }
                while ($result_array3 = mysql_fetch_array($result3)) {
                        $totalprice += $result_array3['value'] * $result_array3['quantity'];
                }
                if (mysql_num_rows($result3) > 0) {
                        mysql_data_seek($result3, 0);
                }
                ?>
                <table class='b1sl b1st'>
                    <tr class='vtop bctrt'>
                        <td class='center s09 bold p5 b1sr b1sb' colspan='2'>SALE TOTALS</td>
                    </tr>
                    <tr class='vtop bctr1a'>
                        <td  class='left s08 bold p5 b1sr b1sb'>PRICE:</td><td class='pl10 left s08 bold p5 b1sr b1sb'><?=money2($totalprice)?></td>
                    </tr>
                    <tr class='vtop bctr1b'>
                        <td class='left s08 bold p5 b1sr b1sb'>DISCOUNT:</td><td class='pl10 left s08 bold p5 b1sr b1sb'><?=money2(-$totaldiscount)?></td>
                    </tr>
                    <tr class='vtop bctr1a'>
                        <td class='left s08 bold p5 b1sr b1sb'>TAX:</td><td class='pl10 left s08 bold p5 b1sr b1sb'><?=money2($totaltax)?></td>
                    </tr>
                    <tr class='vtop bctr1b'>
                        <td class='left s08 bold p5 b1sr b1sb'>NET:</td><td class='pl10 left s08 bold p5 b1sr b1sb'><?=money2($totalprice + $totaltax - $totaldiscount)?></td>
                    </tr>
                </table>
              </td>
            </tr>
          </table>
          <form method='post'>
            <table class='mt15 b1st b1sl bcwhite'>
                <tr class='bctrt'>
                  <td class='s09 bold p5 b1sb b1sr' colspan='13'>SALE ITEMS</td>
                </tr>
                <tr class='bctrt'>
                  <td class='s08 bold p5 b1sb b1sr'>#</td>
                  <td class='s08 bold p5 b1sb b1sr'>BRAND<br/>NAME</td>
                  <td class='s08 bold p5 b1sb b1sr'>ITEM<br/>NAME</td>
                  <td class='s08 bold p5 b1sb b1sr'>STYLE<br/> NR.</td>
                  <td class='s08 bold p5 b1sb b1sr'>DESCRIPTION</td>
                  <td class='s08 bold p5 b1sb b1sr'>PRICE</td>
                  <td class='s08 bold p5 b1sb b1sr'>ITEM<br/>DISCOUNT</td>
                  <td class='s08 bold p5 b1sb b1sr'>ADDITIONAL<br/>DISCOUNT</td>
                  <td class='s08 bold p5 b1sb b1sr'>TAX</td>
                  <td class='s08 bold p5 b1sb b1sr'>QTY</td>
                  <td class='s08 bold p5 b1sb b1sr'>SUBTOTAL</td>
                  <td class='s08 bold p5 b1sb b1sr'>RETURNS<br/>YET</td>
                  <td class='s08 bold p5 b1sb b1sr'>RETURNS</td>
                </tr>
            <?
            $rownum = 1;
            while ($result_array2 = mysql_fetch_array($result2)) {
            $vals = calc($result_array2['itemprice'], $result_array2['itemdiscount'], $result_array2['additional_discount'], $result_array2['tax']);?>
                <tr class='bctr1<?=$rownum % 2 == 1 ? 'a' : 'b'?>'>
                <td class='s08 b1sb b1sr p5'><?=$rownum++?></td>
                <td class='s08 b1sb b1sr p5'><?=$result_array2['brandname']?></td>
                <td class='s08 b1sb b1sr p5'><?=$result_array2['itemname']?></td>
                <td class='s08 b1sb b1sr p5'><?=$result_array2['itemnumber']?></td>
                <td class='s08 b1sb b1sr p5'><?=$result_array2['itemstyle']?></td>
                <td class='s08 b1sb b1sr p5'><?=money2($vals['price'])?></td>
                <td class='s08 b1sb b1sr p5'><?=$result_array2['itemdiscount']?>%</td>
                <td class='s08 b1sb b1sr p5'><?=$result_array2['additional_discount']?>%</td>
                <td class='s08 b1sb b1sr p5'><?=$result_array2['tax']?>%</td>
                <td class='s08 b1sb b1sr p5'><?=$result_array2['quantity']?></td>
                <td class='s08 b1sb b1sr p5'><?=money2($result_array2['quantity'] * ($vals['total']))?></td>
                <td class='s08 b1sb b1sr p5'><?=$result_array2['returns']?></td>
                <td class='b1sb b1sr'>
                <?if ($result_array2['quantity'] - $result_array2['returns'] < 1 && !isset($return['receipt_id'])) {?>
                  <div class='s08'>-</div>
                <?} else {
                    if (isset($return['receipt_id'])) {?>
                        <div class='s08 p5'><?=$return['items'][$result_array2['id']]['quantity'] > 0 ? $return['items'][$result_array2['id']]['quantity'] : '-'?></div>
                    <?} else {?>
                        <input name='sale_item_<?=$result_array2['id']?>' class='w50' type='text' value='<?=replace_ticks($return['items'][$result_array2['id']]['quantity'])?>'/>
                    <? }
                }?>
                </td>
              </tr>
            <?}
              while ($result_array3 = mysql_fetch_array($result3)) {?>
                <tr class='<?=$bcclass?>'>
                  <td class='s08 b1sb b1sr p5'>-</td>
                  <td class='s08 b1sb b1sr p5'>gift certificate</td>
                  <td class='s08 b1sb b1sr p5'>-</td>
                  <td class='s08 b1sb b1sr p5'>-</td>
                  <td class='s08 b1sb b1sr p5'><?=money2($result_array3['value'])?></td>
                  <td class='s08 b1sb b1sr p5'>-</td>
                  <td class='s08 b1sb b1sr p5'>-</td>
                  <td class='s08 b1sb b1sr p5'>-</td>
                  <td class='s08 b1sb b1sr p5'><?=$result_array3['quantity']?></td>
                  <td class='s08 b1sb b1sr p5'><?=money2($result_array3['value'] * $result_array3['quantity'])?></td>
                  <td class='s08 b1sb b1sr p5'>-</td>
                  <td class='s08 b1sb b1sr p5'>-</td>
                </tr>
              <? } ?>
            </table>
<?
                }
                if (!isset($return['receipt_id'])) {?>
                    <input class='mt20 button' type='submit' value='UPDATE RETURN DETAILS'/>
                    <input type='hidden' name='update_return_items' value='1'/>
                    <input type='hidden' name='return_management' value='1'/>
                <? } ?>
          </form>
          <form class='mt20' method='post' name='go_back_form'>
            <a class='s08 bold' href='javascript: none();' onclick='document.go_back_form.submit();'>GO BACK TO RETURN DETAILS</a>
            <input type='hidden' name='no_sale_details' value='1'/>
          </form>
        <?} else {?>
          <table>
            <tr class='vtop'>
              <td class='pl5 pr5'>
                <table class='b1sl b1st w200'>
                  <tr class='vtop bctrt'>
                    <td class='center s09 bold p5 b1sr b1sb' colspan='2'>RETURN TOTALS</td>
                  </tr><tr class='vtop bctr1a'>
                    <td class='left s08 bold p5 b1sr b1sb'>PRICE:</td><td class='right s08 bold p5 b1sr b1sb'><?=money2($return['totals']['price'])?></td>
                  </tr><tr class='vtop bctr1b'>
                    <td class='left s08 bold p5 b1sr b1sb'>DISCOUNT:</td><td class='right s08 bold p5 b1sr b1sb'><?=money2(-$return['totals']['discount'])?></td>
                  </tr><tr class='vtop bctr1a'>
                    <td class='left s08 bold p5 b1sr b1sb'>TAX:</td><td class='right s08 bold p5 b1sr b1sb'><?=money2($return['totals']['tax'])?></td>
                  </tr><tr class='vtop bctr1b'>
                    <td class='left s08 bold p5 b1sr b1sb'>NET:</td><td class='right s08 bold p5 b1sr b1sb'><?=money2($return['totals']['price'] - $return['totals']['discount'] + $return['totals']['tax'])?></td>
                  </tr>
                </table>
              </td>
              <td class='pl5 pr5'>
                <table class='b1sl b1st w200'>
                  <tr class='vtop bctrt'>
                    <td class='center s09 bold p5 b1sr b1sb'>RETURN ACTIONS</td>
                  </tr><tr class='vtop bctr1a'>
                    <td class='left p5 bold b1sr b1sb'>
                      <form method='post'><input class='w180 button<?=($bool = !(is_array($return['items']) && count($return['items']) > 0 && !isset($return['receipt_id']) && $_SESSION['settings']['site'] == 'returns')) ? 'disabled' : ''?>' id='focusitem"?>' type='submit' value='FINISH RETURN'<?=$bool ? ' disabled' : ''?>/><input type='hidden' name='encash_return' value='1'/><input type='hidden' name='return_management' value='1'/></form>
                      <form class='mt5' method='post'><input type='submit' class='w180 button<?=($bool = !(is_array($return['items']) && count($return['items']) > 0 && isset($return['receipt_id']) && ($_SESSION['settings']['site'] == 'returns' || $_SESSION['settings']['site'] == 'reports'))) ? 'disabled' : ''?>' value='SHOW RETURN RECEIPT' onclick=''<?=$bool ? ' disabled' : ''?>/><input type='hidden' name='encash_return' value='1'/><input type='hidden' name='return_management' value='1'/></form>
                      <form class='mt5' method='post'><input type='submit' class='w180 button<?=($bool = !(is_array($return['items']) && count($return['items']) > 0 && $_SESSION['settings']['site'] != 'reports')) ? 'disabled' : ''?>' value='START NEW RETURN'<? if (is_array($return['items']) && count($return['items']) > 0 && !isset($return['receipt_id'])) { ?> onclick='if (!confirm("Do you really want to cancel this return?")) { return false; }'<? } ?><?=$bool ? ' disabled' : ''?>/><input type='hidden' name='return_action' value='cancel'/><input type='hidden' name='return_management' value='1'/></form>
                    </td>
                  </tr>
                </table>
              </td>
            </tr>
          </table>
                <? if (($_SESSION['settings']['site'] == 'returns' || $_SESSION['settings']['site'] == 'exchanges') && !isset($return['receipt_id'])){?>
                    <table class='mt15 b1sl b1st'>
                      <tr class='bctrt'>
                        <td class='p5 b1sr b1sb s09 bold'>MANAGE RETURN ITEMS</td>
                      </tr><tr class='bctr1a'>
                        <td class='p5 b1sr b1sb'><form method='post'><span class='s08 bold'>SALE RECEIPT BARCODE:</span><input class='ml10 w120 text' type='text' name='receipt_id' id='focusitem' value=''/><input class='ml2 button' type='submit' value='SHOW DETAILS'/><input type='hidden' name='show_sale_details' value='1'/><input type='hidden' name='return_management' value='1'/></form></td>
                      </tr>
                    </table>
                <?}
                show_return_items($return, $_SESSION['settings']['site'] == 'exchanges' ? 'exchange' : '');
                if ($_SESSION['settings']['site'] != 'returns') {?>
                    <form class='mt30 s08 bold' method='post' name='no_return_details_form'><a href='javascript: none();' onclick='document.no_return_details_form.submit();'>GO BACK TO <?=$_SESSION['settings']['site'] == 'exchanges' ? 'EXCHANGE DETAILS' : 'PREVIOUS PAGE'?></a><input type='hidden' name='no_return_details' value='1'/><input type='hidden' name='return_management' value='1'/></form>
                <? }
        }
}
function exchanges() {
        $id = $_SESSION['settings']['site'] == 'exchanges' ? '' : '2';
        $sale = &$_SESSION['sale' . $id];
        $return = &$_SESSION['return' . $id];
        $exchange = &$_SESSION['exchange' . $id];

        calc_sale_totals($sale);
        calc_return_totals($return);
        calc_exchange_totals($exchange, $sale, $return);
?>
        <div class='s15 bold mb20 mt10'>EXCHANGES</div>
<?
        if ($_SESSION['settings']['exchanges']['show_item_id'] > 0) {
                itemDetails();
        } else {
                if (isset($_SESSION['settings']['exchanges']['manage_type'])) {
                    if ($_SESSION['settings']['exchanges']['manage_type'] == 'returns') {
                            returnsPage();
                    }
                    if ($_SESSION['settings']['exchanges']['manage_type'] == 'sales') {
                            salesPage();
                    }
                    return;
                }

?>
        <table<?=$_SESSION['settings']['site'] == 'returns' ? " class='mt20'" : ''?>>
          <tr class='vtop'>
            <td class='pl5 pr5'>
              <table class='b1sl b1st w200'>
                <tr class='vtop bctrt'>
                  <td class='center s09 bold p5 b1sr b1sb' colspan='2'>EXCHANGE TOTALS</td>
                </tr><tr class='vtop bctr1a'>
                  <td class='left s08 bold p5 b1sr b1sb'>PRICE:</td><td class='right s08 bold p5 b1sr b1sb'><?=money2($exchange['totals']['price'])?></td>
                </tr><tr class='vtop bctr1b'>
                  <td class='left s08 bold p5 b1sr b1sb'>DISCOUNT:</td><td class='right s08 bold p5 b1sr b1sb'><?=money2(-$exchange['totals']['discount'])?></td>
                </tr><tr class='vtop bctr1a'>
                  <td class='left s08 bold p5 b1sr b1sb'>TAX:</td><td class='right s08 bold p5 b1sr b1sb'><?=money2($exchange['totals']['tax'])?></td>
                </tr><tr class='vtop bctr1b'>
                  <td class='left s08 bold p5 b1sr b1sb'>NET:</td><td class='right s08 bold p5 b1sr b1sb'><?=money2($exchange['totals']['price'] - $exchange['totals']['discount'] + $exchange['totals']['tax'])?></td>
                </tr>
              </table>
            </td><td class='pr5'>
<?

                if ($_SESSION['exchange']['encash'] == 1) {
                        $paidbycard = $paidbyvoucher = 0;
                        if (is_array($exchange['card_payments'])) {
                                foreach (array_keys($exchange['card_payments']) as $id) {
                                        $paidbycard += $exchange['card_payments'][$id]['amount'];
                                }
                        }
                        if (is_array($exchange['voucher_payments'])) {
                                foreach (array_keys($exchange['voucher_payments']) as $id) {
                                        $paidbyvoucher += $exchange['voucher_payments'][$id]['value'];
                                }
                        }
                        $payinfo = pay($exchange['totals']['price'] - $exchange['totals']['discount'] + $exchange['totals']['tax'], $exchange['cash_payment'], $paidbycard, $paidbyvoucher);
                        if ($exchange['check_finish'] == 1) {
                                if ($payinfo['open'] == 0) {
                                        $exchange['finish'] = 1;
                                }
                                unset($exchange['check_finish']);
                        }

                        if ($exchange['finish'] == 1) {
                                $exchange['change']['cash'] = $payinfo['cash'];
                                $exchange['change']['voucher'] = $payinfo['voucher'];
                        }

?>
              <table class='b1sl b1st'>
                <tr class='vmiddle bctrt'>
                  <td class='center bold s09 p5 b1sr b1sb' colspan='2'>PAYMENT DETAILS</td>
                </tr>
                <tr class='vmiddle bctr1a'>
                  <td class='left s08 bold p5 b1sr b1sb'>OPEN AMOUNT:</td><td class='right s08 bold p5 b1sr b1sb'><?=money2($payinfo['open'])?></td>
                </tr><tr class='vmiddle bctr1b'>
                  <td class='left s08 bold p5 b1sr b1sb'>PAID CASH:</td><td class='right s08 bold p5 b1sr b1sb'><?=money2($exchange['cash_payment'])?></td>
                </tr><tr class='vmiddle bctr1a'>
                  <td class='left s08 bold p5 b1sr b1sb'>PAID BY CARD(S):</td><td class='right s08 bold p5 b1sr b1sb'><?=money2($paidbycard)?></td>
                </tr>
                <tr class='vmiddle bctr1b'>
                  <td class='left s08 bold p5 b1sr b1db'>PAID BY VOUCHER(S):</td><td class='right s08 bold p5 b1sr b1db'><?=money2($paidbyvoucher)?></td>
                </tr>
                <tr class='vmiddle bctr1a'>
                  <td class='left s08 bold p5 b1sr b1sb'>CHANGE:</td><td class='right s08 bold p5 b1sr b1sb'><?=money2($payinfo['cash'])?></td>
                </tr>
                <tr class='vmiddle bctr1b'>
                  <td class='left s08 bold p5 b1sr b1sb'>NEW VOUCHER VALUE:</td><td class='right s08 bold p5 b1sr b1sb'><?=money2($payinfo['voucher'])?></td>
                </tr>
              </table>
            </td><td class='pr5'>
                <? } ?>
              <table class='b1sl b1st w200'>
                <tr class='bctrt'>
                  <td class='center bold s09 p5 b1sr b1sb'>EXCHANGE ACTIONS</td>
                </tr><tr class='vmiddle bctr1b'>
                  <td class='left p5 b1sr b1sb'>
                    <form method='post'><input class='w180 button<?=($bool = !(is_array($sale['basket']['items']) && !isset($sale['receipt_id']) && count($sale['basket']['items']) > 0 && is_array($return['items']) && count($return['items']) > 0 && !isset($return['receipt_id']) && $_SESSION['settings']['site'] == 'exchanges' && !isset($exchange['encash']))) ? 'disabled' : ''?>' type='submit' value='FINISH EXCHANGE'<?=$bool ? ' disabled' : ''?>/><input type='hidden' name='encash' value='1'/><input type='hidden' name='exchange_management' value='1'/></form>
                    <form class='mt5' method='post'><input class='w180 button<?=($bool = ($exchange['encash'] == 1 && !isset($exchange['receipt_id']))) ? '' : 'disabled'?>' type='submit' value='MODIFY EXCHANGE'<?=$bool ? '' : ' disabled'?>/><input type='hidden' name='no_encash' value='1'/><input type='hidden' name='exchange_management' value='1'/></form>
                    <form class='mt5' method='post'><input class='w180 button<?=($bool = !isset($exchange['receipt_id'])) ? 'disabled' : "' id='focusitem"?>' type='submit' value='SHOW RECEIPT'<?=$bool ? ' disabled' : ''?>/><input type='hidden' name='save_exchange' value='1'/><input type='hidden' name='exchange_management' value='1'/></form>
                    <form class='mt5' method='post'><input class='w180 button<?=($bool = ((!is_array($sale['basket']['items']) || count($sale['basket']['items']) == 0) && (!is_array($sale['basket']['gift_certificates']) || count($sale['basket']['gift_certificates']) == 0) && (!is_array($return['items']) || count($return['items']) == 0))) ? 'disabled' : ''?>' type='submit' <?=isset($sale['receipt_id']) ? "id='focusitem' " : ''?>value='START NEW EXCHANGE' <?=isset($exchange['receipt_id']) ? '' : " onclick='if (!confirm(\"Do you really want to cancel the current exchange (sale and return)?\")) { return false; }'"?><?=$bool ? ' disabled' : ''?>/><input type='hidden' name='cancel' value='1'/><input type='hidden' name='exchange_management' value='1'/></form>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
        <table>
          <tr>
            <td class='center'>
<?
                if (isset($_SESSION['exchange']['encash'])) {
                        if (!isset($_SESSION['exchange']['receipt_id'])) {
                                if ($_SESSION['exchange']['finish'] == 1) {

?>
              <table class='mt15 b1sl b1st'>
                <tr class='vmiddle bctrt'>
                  <td class='center bold s09 p5 b1sr b1sb' colspan='2'>SAVE EXCHANGE</td>
                </tr><tr class='vmiddle bctr1a'>
                  <td class='left p5 b1sr b1sb'>
                    <form method='post'>
                      <span class='s08 bold<?=$_SESSION['bad']['username'] == 1 ? ' red': ''?>'>SALES PERSON:&nbsp;</span><select name='sales_person_id'>
                        <option value='0'>- none -</option>
<?

                                        $result = select_db('id, username', 'logins', sprintf('company_id = %s and deleted is null order by username', $_SESSION['settings']['company_id']));

                                        while ($result_array = mysql_fetch_array($result)) {

?>
                        <option value='<?=$result_array['id']?>'><?=$result_array['username']?></option>
<?

                                        }

?>
                      </select>
                    <form>
                    <div class='mt10 s12'><input class='button' type='submit' value='SAVE EXCHANGE' id='focusitem'/></div>
                    <form method='post'><input type='hidden' name='save_exchange' value='1'/><input type='hidden' name='new_exchange' value='1'/><input type='hidden' name='exchange_management' value='1'/></form>
                  </td>
                </tr>
              </table>
                <? } else {?>
              <table class='b1sl b1st mt15 wp100'>
                <tr class='vmiddle bctrt'>
                  <td class='center bold s09 p5 b1sr b1sb' colspan='3'>PAYMENT OPTIONS</td>
                </tr>
                <tr class='vmiddle'>
                  <td class='left p5 b1sr b1sb bctr1a'><form method='post'><span class='s08'>Cash:&nbsp;<?=$_SESSION['preferences']['currency_position'] == 1 ? $_SESSION['preferences']['currency'] . '&nbsp;' : ''?></span><input class='w50 text' type='text' name='paidamount'<?=$payinfo['open'] > 0 ? " id='focusitem'" : ''?>/><?=$_SESSION['preferences']['currency_position'] == 0 ? "<span class='s08'>&nbsp;" . $_SESSION['preferences']['currency'] . '&nbsp;</span>' : ''?><input class='ml2 button' type='submit' value='PAY'/><input type='hidden' name='paid_cash' value='1'/><input type='hidden' name='new_exchange' value='1'/><input type='hidden' name='exchange_management' value='1'/></form></td>
                  <td class='center p5 b1sr b1sb bctr1b'><form method='post'><span class='s08'>Card:&nbsp;<?=$_SESSION['preferences']['currency_position'] == 1 ? $_SESSION['preferences']['currency'] . '&nbsp;' : ''?></span><input class='w50 text' type='text' name='paidamount' value='<?=number($payinfo['open'])?>'/><?=$_SESSION['preferences']['currency_position'] == 0 ? "<span class='s08'>&nbsp;" . $_SESSION['preferences']['currency'] . '&nbsp;</span>' : ''?><select class='ml2' name='cardtype'>
                    <?
                    $result = select_db('id, name', 'card_types', sprintf('company_id = 0 or company_id = %s', $_SESSION['settings']['company_id']));
                    while ($result_array = mysql_fetch_array($result)) {?>
                        <option value='<?=$result_array['id']?>'><?=$result_array['name']?></option>
                    <?}?>
                    </select><input class='button ml2' type='submit' value='PAY'/><input type='hidden' name='add_card_payment' value='1'/><input type='hidden' name='card_payment_management' value='1'/><input type='hidden' name='new_exchange' value='1'/><input type='hidden' name='exchange_management' value='1'/></form></td>
                  <td class='center p5 b1sr b1sb bctr1a'><form method='post'><span class='s08'>Voucher:&nbsp;</span><input class='w100 text' type='text' name='voucherbarcode'/><input class='ml2 button' type='submit' value='PAY'/><input type='hidden' name='add_voucher_payment' value='1'/><input type='hidden' name='new_exchange' value='1'/><input type='hidden' name='voucher_payment_management' value='1'/><input type='hidden' name='exchange_management' value='1'/></form>
                </tr>
              </table>
              <form method='post' name='payment_details_form'>
                <input type='hidden' name='show_card_payments' id='show_card_payments' value='0'/>
                <input type='hidden' name='show_voucher_payments' id='show_voucher_payments' value='0'/>
              </form>
<?
                                }
                        }
                } else {?>
              <table class='mt15 b1sl b1st'>
                <tr class='bctrt'>
                  <td class='p5 b1sr b1sb s09 bold'>MANAGE ITEMS</td>
                </tr><tr class='bctr1a'>
                  <td class='p5 b1sr b1sb'><form method='post' name='manage_items_form'><input type='button' class='w180 button' onclick='document.getElementById("manage_type").value = "sales"; document.manage_items_form.submit();' value='MANAGE SALE ITEMS'/><input type='button' class='ml10 w180 button' onclick='document.manage_items_form.submit();' value='MANAGE RETURN ITEMS'/><input type='hidden' name='manage_type' id='manage_type' value='returns'/><input type='hidden' name='choose_manage_type' value='1'/><input type='hidden' name='exchange_management' value='1'/></form></td>
                </tr>
              </table>
            <? }?>
<?
                show_sale_items($_SESSION['sale'], 'exchange', 'wp100');
                show_return_items($_SESSION['return'], 'exchange', 'wp100');
?>
            </td>
          </tr>
        </table>
<?

        }
}
function sales() {?>
<head>
<script src="includes/<?=__FUNCTION__?>_functions.js?unique=<?=substr(number_format(time() * rand(),0,'',''),0,10)?>" type="text/javascript"></script>
</head>
<div class="ReportsTopRow main_bc_color2 main_color2_text"><a href="#" title="Item Search" onclick="mainDiv('new_sale'); return false;">Item Sale</a></div>
    <div style="max-height: 1000px;" class="f_left wp100 hp94">
        <div class="leftSpace main_bc_color2 main_color2_text">&nbsp;</div>
        <div class="f_left wp96 hp100">
            <? 
            if ($_SESSION['settings']['site'] == 'sales' || $_SESSION['settings']['site'] == 'exchanges') {
                $sale = &$_SESSION['sale']; 
            } else { 
                if (isset($_SESSION['sale3'])) { $sale = &$_SESSION['sale3']; } 
                else { $sale = &$_SESSION['sale2']; }
            }
            calc_sale_totals($sale);
            $payinfo = set_payinfo_values($sale);
            if ((isset($_SESSION['sale3']) && $_SESSION['settings']['sales']['show_item_id2'] > 0) || (!isset($_SESSION['sale']) && $_SESSION['settings']['sales']['show_item_id'] > 0)) {
                itemDetails();
            } else {
                if (isset($sale['encash']) && $sale['encash'] != 1 && isset($sale['receipt_id'])) {
                    $sale['encash'] = 1;
                }
                if (isset($sale['encash']) && $sale['encash'] == 1 && !is_array($sale['basket'])) {
                    unset($sale['encash']);
                }
            }?>              
            <div class='d_InlineBlock f_left wp100 hp100'>
                <div class='d_InlineBlock f_left center wp20 hp100' >
                    <? div_sale_left_column($sale); ?>
                </div>
                <div class='d_InlineBlock f_left center wp60 hp100 bclightblue ' style='margin: 0 auto;'>                    
                    <? div_sale_middle_column($sale,$payinfo); ?>
                </div>
                <div class='d_InlineBlock f_left center wp20 hp100' >
                    <?=div_sale_right_column($sale,$payinfo);?>
                </div>                
            </div>
        </div>
        <div class="rightSpace main_bc_color2 main_color2_text">&nbsp;</div>
    </div>
<div class="ReportsBottomRow main_bc_color2 main_color2_text">&nbsp;</div>    
<?
}
    function div_sale_left_column($sale){?>
        <? div_sale_totals($sale);   ?>
        <div class='d_InlineBlock f_left wp100 h10px'></div>               
        <? div_tax_on_or_off($sale); ?>
        <div class='d_InlineBlock f_left wp100 h10px'></div>               
        <? div_sale_actions($sale);  ?>
        <div class='d_InlineBlock f_left wp100 h10px'></div>               
        <? div_sale_global_discount($sale); ?>    
    <?}
        function div_sale_totals($sale){?>                    
            <div class='d_InlineBlock f_left wp100'>
                <div class='d_InlineBlock wp99 hp10 vmiddle bctrt'>
                    <div class="bold s08">SALE TOTALS</div>
                </div>   
                <div class='d_InlineBlock wp100 hp90 vmiddle'>
                    <div class='d_InlineBlock wp90 left s07'>
                        <div class='f_left wp100 vmiddle bctr1a'>
                          <div class='f_left left wp50 bold b1sb'>FULL PRICE:</div>
                          <div class='f_right right wp50 b1sb'><?=money2($sale['totals']['price'])?></div>
                        </div>
                        <div class='f_left wp100 vmiddle bctr1b'>
                          <div class='f_left left wp50 bold b1sb'>DISCOUNT:</div>
                          <div class='f_right right wp50 b1sb'><?=money2(-$sale['totals']['discount'])?></div>
                        </div>
                        <div class='f_left wp100 vmiddle bctr1a'>
                          <div class='f_left left wp50 bold b1sb'>Sub Total:</div>
                          <div class='f_right right wp50 b1sb'><?=money2($sale['totals']['price'] - $sale['totals']['discount'])?></div>
                        </div>
                        <div class='f_left wp100 vmiddle bctr1b'>
                          <div class='f_left left wp50 bold b1sb'>TAX:</div>
                          <div class='f_right right wp50 b1sb'><?=money2($sale['totals']['tax'])?></div>
                        </div>
                        <div class='f_left wp100 vmiddle bctr1a'>
                            <div class='f_left left wp50 bold b1sb'>NET Price:</div>
                            <div class='f_right right wp50 b1sb'><?=money2($sale['totals']['price'] - $sale['totals']['discount'] + $sale['totals']['tax'])?></div>
                        </div>
                        <div class='f_left wp100 vmiddle bctr1b'>
                        <?if (isset($sale['receipt_id'])){?>
                            <div class='f_left left wp50 bold b1sb'>SALE RECEIPT #</div>
                            <div class='f_right right wp50 b1sb'><?=$sale['receipt_id']?></div>
                        <?}?>
                        </div>
                    </div>
                </div>               
            </div>  
        <?}   
        function div_tax_on_or_off($sale){
            if ( !isset($sale['no_tax']) )  { 
                $tax_on_checked  = 'checked=checked';   $tax_off_checked ='';   } 
            else { 
                $tax_off_checked  = 'checked=checked';  $tax_on_checked =''; }
            if ( ($_SESSION['settings']['site'] != 'sales' || $sale['encash'] == 1)          && 
                 ($_SESSION['settings']['site'] != 'exchanges' || $exchange['encash'] == 1) &&
                 (isset($_SESSION['settings']['manager']) && $_SESSION['settings']['manager'] == 1)
               )                            { $disabled = 'disabled';   } else { $disabled  = ''; }
            ?>  
            <div class='d_InlineBlock f_left wp100 hp07'>
                <div class='d_InlineBlock wp99 hp40 vmiddle bctrt'>
                    <div class="bold s08 wp100 hp100">TAX</div>
                </div>   
                <div class='d_InlineBlock wp99 hp60 vmiddle'>
                      <div class='d_InlineBlock wp90 hp100 s07'>
                        <form method='post' name='tax_form'>
                            <input 
                                type='radio' 
                                name='sale_tax' 
                                value='1' 
                                onclick='Sales_ProcessVariables(this.form.name);'
                                <?=$tax_on_checked?>
                                <?=$disabled?>
                            > Tax ON&nbsp;&nbsp;&nbsp;
                            <input 
                                type='radio' 
                                name='sale_tax' 
                                value='0' 
                                onclick='Sales_ProcessVariables(this.form.name);;'
                                <?=$tax_off_checked?>
                                <?=$disabled?>
                            > Tax OFF&nbsp;&nbsp;&nbsp;
                            <input type='hidden' 
                                   name='sale_management' 
                                   value='1'>
                        </form>                              
                      </div>
                </div>               
            </div>  
        <?} 
        function div_sale_actions($sale){
            $woptions = "width=" . (!isset($_SESSION['preferences']['receipt_width']) || $_SESSION['preferences']['receipt_width'] > 380 ? 400 : $_SESSION['preferences']['receipt_width'] + 50).",height=400,screenX=100,screenY=100,scrollbars=yes,resizeable=yes";
            ?>
            <div class='d_InlineBlock f_left wp100'>
                <div class='d_InlineBlock wp99 hp10 vmiddle bctrt'>
                  <div class='bold s08'>SALE ACTIONS</div>
                </div>
                <div class='d_InlineBlock f_left wp100 hp90 vmiddle'>
                    <div class='d_InlineBlock wp90 left b1sb'>
                        <form class='mt5' method='post'><input class='w180 button<?=($bool = ((!is_array($sale['basket']['items']) || count($sale['basket']['items']) == 0) && (!is_array($sale['basket']['gift_certificates']) || count($sale['basket']['gift_certificates']) == 0)) || $_SESSION['settings']['site'] != 'sales' || $sale['encash'] == 1) ? 'disabled' : ''?>'                          type='submit' value='PAY!'                     <?=$bool ? ' disabled' : ''?>/>   <input type='hidden' name='encash' value='1'/>                  <input type='hidden' name='new_sale' value='1'/></form>
                        <form class='mt5' method='post'><input class='w180 button<?=($bool = $sale['encash'] == 1 && !isset($sale['receipt_id']) && $_SESSION['settings']['site'] == 'sales') ? '' : 'disabled'?>'                                                                                                                                                                                       type='submit' value='MODIFY SALE'              <?=$bool ? '' : ' disabled'?>/>   <input type='hidden' name='no_encash' value='1'/>               <input type='hidden' name='new_sale' value='1'/></form>
                        <form class='mt5' method='post'><input class='w180 button<?=($bool = !(isset($sale['receipt_id']) && ($_SESSION['settings']['site'] == 'sales' || $_SESSION['settings']['site'] == 'reports' || $_SESSION['settings']['site'] == 'itemmgnt'))) ? 'disabled' : ''?>'                                                                                                              type='submit' value='SHOW RECEIPT'             <?=$bool ? ' disabled' : ''?>/>   <input type='hidden' name='save_sale' value='1'/>               <input type='hidden' name='new_sale' value='1'/></form>
                        <form class='mt5'              ><input class='w180 button<?=($bool = !(isset($sale['receipt_id']) && ($_SESSION['settings']['site'] == 'sales' || $_SESSION['settings']['site'] == 'reports' || $_SESSION['settings']['site'] == 'itemmgnt'))) ? 'disabled' : ''?>'                                                                                                              type='submit' value='GIFT RECEIPT'             <?=$bool ? " disabled" : " onclick=sale_print_receipt('".$sale['receipt_id']."','".$woptions."','gift_receipt') " ?> ></form>
                        <form class='mt5' method='post'><input class='w180 button<?=($bool = !(isset($sale['receipt_id']) && is_array($sale['basket']['gift_certificates']) && count($sale['basket']['gift_certificates']) > 0 && $_SESSION['settings']['site'] == 'sales')) ? 'disabled' : ''?>'                                                                                                        type='submit' value='SHOW GIFT CERTIF'         <?=$bool ? ' disabled' : ''?>/>   <input type='hidden' name='show_gift_certificates' value='1'/>  <input type='hidden' name='new_sale' value='1'/></form>
                        <form class='mt5' method='post'><input class='w180 button<?=($bool = ((!is_array($sale['basket']['items']) || count($sale['basket']['items']) == 0) && (!is_array($sale['basket']['gift_certificates']) || count($sale['basket']['gift_certificates']) == 0)) || $_SESSION['settings']['site'] == 'reports' || $_SESSION['settings']['site'] == 'itemmgnt') ? 'disabled' : ''?>' type='submit' value='START NEW SALE'           <?=isset($sale['receipt_id']) ? '' : " onclick='if (!confirm(\"Do you really want to cancel the current sale?\")) { return false; }'"?><?=$bool ? ' disabled' : ''?>/><input type='hidden' name='cancel_sale' value='1'/><input type='hidden' name='new_sale' value='1'/></form>
                    </div>
                </div>
            </div>
        <?}
        function div_sale_global_discount($sale){
            $woptions = "width=" . (!isset($_SESSION['preferences']['receipt_width']) || $_SESSION['preferences']['receipt_width'] > 380 ? 400 : $_SESSION['preferences']['receipt_width'] + 50).",height=400,screenX=100,screenY=100,scrollbars=yes,resizeable=yes";
            if ( !isset($_SESSION['sale']['finish']) || $_SESSION['sale']['finish'] != 1 ) {?>
            <div class='d_InlineBlock f_left wp100'>
                <div class='d_InlineBlock wp99 hp10 vmiddle bctrt'>
                  <div class='bold s08'>SALE Discount %</div>
                </div>
                <div class='d_InlineBlock wp90 hp90 vmiddle bctr1a'>
                    <div class='d_InlineBlock f_left wp100 vmiddle bctrt center s06 mp' title="Click to apply a percentage discount to all items?">
                        <div class='f_left wp11 b1sb' onclick="Sales_AllItems_Discount(00)" id="Sales_AllItems_Discount_00"> 0</div>
                        <div class='f_left wp11 b1sb' onclick="Sales_AllItems_Discount(05)" id="Sales_AllItems_Discount_05"> 5</div>
                        <div class='f_left wp11 b1sb' onclick="Sales_AllItems_Discount(10)" id="Sales_AllItems_Discount_10">10</div>
                        <div class='f_left wp11 b1sb' onclick="Sales_AllItems_Discount(15)" id="Sales_AllItems_Discount_15">15</div>
                        <div class='f_left wp11 b1sb' onclick="Sales_AllItems_Discount(20)" id="Sales_AllItems_Discount_20">20</div>
                        <div class='f_left wp11 b1sb' onclick="Sales_AllItems_Discount(25)" id="Sales_AllItems_Discount_25">25</div>
                        <div class='f_left wp11 b1sb' onclick="Sales_AllItems_Discount(30)" id="Sales_AllItems_Discount_30">30</div>
                        <div class='f_left wp11 b1sb' onclick="Sales_AllItems_Discount(40)" id="Sales_AllItems_Discount_40">40</div>
                        <div class='f_left wp11 b1sb' onclick="Sales_AllItems_Discount(50)" id="Sales_AllItems_Discount_50">50</div>
                    </div>
                </div>
            </div>
            <? }
        }

    function div_sale_middle_column($sale,$payinfo){
        if (isset($sale['finish']) && $sale['finish'] == 1) {
            $sale['change']['cash'] = $payinfo['cash'];
            $sale['change']['voucher'] = $payinfo['voucher'];
        }?>
        <div class='d_InlineBlock f_left wp100 hp30'>
            <? 
            if ( isset($sale['encash']) && $sale['encash'] == 1 ) {
                div_sale_post_functions($sale,$payinfo);
            } else {
                div_add_to_sale($sale);
            }
            ?>
        </div> 
        <div class='d_InlineBlock f_left wp100 hp70'>
                <? show_sale_items($sale, '', 'wp100', isset($sale['encash']) ? 0 : 1); ?>
        </div>    
    <?}    
        function div_add_to_sale($sale){?>
            <script type="text/javascript">
            $(document).ready(function(){
                document.getElementById('focusitem').focus();
            });
            </script>
            <div class='wp75 hp100' style='margin: 0 auto;'>
                <div class='d_InlineBlock f_left wp100 hp10 bctrt'>
                    <div class='center s09 bold  wp100 hp100 b1sr b1sb'>ADD TO SALE</div>
                </div>
                <div class='d_InlineBlock f_left wp100 hp30 bctr1a'>
                    <div class='f_left center wp100 hp100 b1sr b1sb'>
                        <?=sales_add_barcode_to_sale(); ?>
                    </div>
                </div>

                <div class='d_InlineBlock f_left wp100 hp30 bctr1a'>
                    <div class='f_left center wp100 hp100 b1sr b1sb' id="sales_choose_customer">
                        <?=sales_choose_customer(); ?>
                    </div>
                </div>

                <div class='d_InlineBlock f_left wp100 hp30 bctr1b'>
                    <div class='f_left center wp100 hp100 b1sr b1sb'>
                        <?=sales_add_gift_certificate(); ?>
                    </div>
                </div>
            </div>
        <?}
            function sales_add_barcode_to_sale() {?>
                <div class='d_Block f_left wp30 hp100'>
                    Barcode #
                </div>
                <div class='d_Block f_right wp70 hp100'>
                    <form class="wp100 hp100" name='add_item_form' method='post' onSubmit='return Sales_ProcessVariables(this.name)'>
                      <input class='w120 text' type='text' name='saleitemid' id='focusitem' value='' 
                             onSubmit='Sales_ProcessVariables(this.form.name);'
                             onkeyup='if (this.value == "+" || this.value == "-" || this.value == "=") { Sales_ProcessVariables(this.form.name); }'/>
                      <input class='ml2 button' type='button' value='ADD' onclick="this.disabled=true; this.value='Sending'; Sales_ProcessVariables(this.form.name);"/>
                      <input type='hidden' name='modify_basket' value='1'/>
                      <input type='hidden' name='new_sale' value='1'/>
                    </form>
                </div>
            <?}
            function sales_choose_customer() {?>
            <div class='f_left center wp100 hp100' id="sales_choose_customer">
                <div class="d_Block f_left wp70">
                    <select id='customer_id' class="wp90" onchange='Sale_CustomerLink();'>
                        <option value=''>- select customer -</option>
                        <?
                        $dal = new SALES_DAL();
                        $customers = $dal->get_AllCustomersPerCompanyId($_SESSION['settings']['company_id'],1);

                        foreach ( $customers as $customer) { ?>
                            <option value='<?=$customer->id?>' <?=isset($_SESSION['sale']['customer_id']) && $_SESSION['sale']['customer_id'] == $customer->id ? ' selected' : ''?>>
                                <?=$customer->firstname?> <?=$customer->lastname?>
                            </option>
                        <? } ?>
                    </select>
                </div>
                <div class="d_Block f_right wp30">
                    <? if (isset($_SESSION['sale']['customer_id'])) {?>
                    <input onclick='Sale_CancelNewCustomer();'  type="submit" value="Reset" class="ml2 button">
                    <? } else { ?>
                    <input onclick='Sale_AddNewCustomer();'     type="submit" value="New"   class="ml2 button">
                    <? } ?>
                </div>
            </div>
            <?}
            function sales_add_gift_certificate(){?>
                <div class='d_InlineBlock f_left wp100 hp100 bctr1b'>
                    <div class='f_left center wp100 hp100'>
                        <form class='' method='post' name='add_gift_certificate_form' onSubmit='return Sales_ProcessVariables(this.name)'>
                            <span class='s08 bold'>      <?=$_SESSION['preferences']['currency']?>&nbsp;</span>
                            <input class='w60 text' type='text' name='gift_certificate_value' value=''/>
                            <input class='ml2 button' type='button' value='ADD GIFT CERTIFICATE' onclick="this.disabled=true; this.value='Sending'; Sales_ProcessVariables(this.form.name);"/>          
                            <input type='hidden' name='modify_basket' value='1'/>
                            <input type='hidden' name='new_sale' value='1'/>
                        </form>
                    </div>
                </div>
            <?}
            function Sale_AddNewCustomer() {
            ?>
            <div class="d_InlineBlock wp100 bctrt ">
                <div class="pt5 pb5">
                    <div class="d_Inline ">
                        <div id="failed_register_message_NC_first_name" class="f_left  center ">First Name</div>
                        <div id="failed_register_message_NC_last_name" class="f_right center ">Last Name</div>
                    </div>
                    <br>
                    <div class="d_Inline">
                        <div class="f_left  center ">
                            <input type="text" tabindex="1" maxlength="50" size="15" value="" name="NC_first_name" id="NC_first_name">
                        </div>
                        <div class="f_right center ">
                            <input type="text" tabindex="1" maxlength="50" size="15" value="" name="NC_last_name" id="NC_last_name">
                        </div>
                    </div>
                    <br>
                    <div class="d_Inline">
                        <div id="failed_register_message_NC_user_email" class="center">Email</div>
                    </div>
                    <div class="d_Inline">
                        <div class="center ">
                            <input type="text" tabindex="1" maxlength="50" size="32" value="" name="NC_user_email" id="NC_user_email">
                        </div>
                    </div>

                    <div class="d_Inline">
                        <div class="center ">
                            <input onclick="Sales_QuickAddNewCustomer();" class="ml2 button" type="submit" value="Add New Customer">
                        </div>
                    </div>
                </div>
            </div>

            <?}    
        function div_sale_post_functions ($sale,$payinfo){
            if ( !isset($sale['receipt_id'])                         &&  
                ( isset($payinfo['open']) && $payinfo['open'] <= 0 ) &&
                $sale['encash']  == 1
            ) {    
                div_save_sale($sale); 
            } else if (
                !isset($sale['receipt_id'])                         &&  
               ( isset($payinfo['open']) && $payinfo['open'] >= 0 ) &&
               $sale['encash']  == 1
            ) {
                echo "you still owe " . $payinfo['open'];
            } else if (1) {
                echo    "you ARE FINISHED <br>" . 
                        "Balance:"  . $payinfo['open'] . "<br>" .
                        "Total:"    . ($sale['totals']['total']);
            }
            div_show_card_payment_details($sale);
            div_show_voucher_payment_details($sale);
        }  
            function div_save_sale($sale){?>
                <table class='mt15 b1sl  w300'>
                    <tr class='vmiddle bctrt'>
                        <td class='center bold s09 b1sr b1sb' colspan='2'>SAVE SALE</td>
                    </tr>
                    <tr class='vmiddle bctr1a'>
                        <td class='left b1sb b1sr'>
                            <form method='post'>
                                <span class='s08 bold<?=$_SESSION['bad']['username'] == 1 ? ' red': ''?>'>SALES PERSON:&nbsp;</span>
                                <select name='sales_person_id'>
                                <?
                                $result = select_db('id, username', 'logins', sprintf('company_id = %s and deleted is null order by username', $_SESSION['settings']['company_id']));
                                while ($result_array = mysql_fetch_array($result)) {?>
                                    <option value='<?=$result_array['id']?>'<?=$result_array['id'] == $_SESSION['settings']['login_id'] ? ' selected' : ''?>><?=$result_array['username']?></option>
                                <?}?>
                                </select>
                            <form>
                                <table class='mt5'>
                                    <tr class='vtop'>
                                        <td class='s08 bold'>
                                            NOTE:&nbsp
                                        </td>
                                        <td>
                                            <textarea name='sale_note' class='s08 w200' rows='2'></textarea>
                                        </td>
                                    </tr>
                                </table>
                                <div class='mt10 s12'>
                                    <input class='button' type='submit' value='SAVE SALE' id='focusitem'/>
                                </div>
                            <form method='post'>
                                <input type='hidden' name='save_sale' value='1'/>
                                <input type='hidden' name='new_sale' value='1'/>
                            </form>
                        </td>
                    </tr>
                </table>
            <?}
            function div_display_or_ask_sale_note($sale){
                if (!empty($sale['note'])) {?>
                  <table class='mt15 b1sl  w300'>
                    <tr class='vmiddle bctrt'>
                      <td class='center bold s09 b1sr b1sb' colspan='2'>SALE NOTE</td>
                    </tr>
                    <tr class='bctr1a'>
                      <td class='center b1sb b1sr s08'><?=$sale['note']?></td>
                    </tr>
                  </table>
                <?}
            }        
            function div_show_card_payment_details($sale){
                show_card_payments_input_box($sale);
                if (    
                            (   (isset($sale['card_payments'])          && is_array($sale['card_payments']) ) && 
                                count($sale['card_payments']) > 0       && 
                                (isset($_POST['show_card_payments'])    && $_POST['show_card_payments']     == 1 ))
                                || 
                            (   (isset($sale['finish'])  && $sale['finish'] == 1)    &&
                                (isset($sale['card_payments'])          && is_array($sale['card_payments']) ) && 
                                count($sale['card_payments']) > 0 ) 
                    ) {
                    if (!isset($sale['finish'])) {?>
                        <form method='post' name='delete_card_payment_form'>
                            <input type='hidden' name='delete_card_payment_id' id='delete_card_payment_id' value='0'/>
                            <input type='hidden' name='delete_card_payment' value='1'/>
                            <input type='hidden' name='new_sale' value='1'/>
                            <input type='hidden' name='card_payment_management' value='1'/>
                            <input type='hidden' name='show_card_payments' value='1'/>
                        </form>
                    <? } ?>
                    <table class='mt15 b1sl '>
                        <tr class='vmiddle bctrt'>
                          <td class='center bold s09 b1sr b1sb' colspan='<?=isset($sale['finish']) ? 3 : 4?>'>CARD PAYMENT DETAILS</td>
                        </tr>
                        <tr class='vmiddle bctrt'>
                          <td class='center bold s08 b1sr b1sb'>#</td>
                          <td class='w200 center bold s08 b1sr b1sb'>CARD TYPE</td>
                          <td class='w100 center bold s08 b1sr b1sb'>AMOUNT</td>
                          <?=isset($sale['finish']) ? '' : "<td class='center bold s08 b1sr b1sb'>DELETE?</td>"?>
                        </tr>
                        <?
                        $rownum = 1;
                        foreach (array_keys($sale['card_payments']) as $id) {?>
                            <tr class='vmiddle bctr1<?=$rownum % 2 == 1 ? 'a' : 'b'?>'>
                            <td class='center s08 b1sr b1sb'><?=$rownum++?></td>
                            <td class='center s08 b1sr b1sb'><?=$sale['card_payments'][$id]['name']?></td>
                            <td class='center s08 b1sr b1sb'><?=money2($sale['card_payments'][$id]['amount'])?></td>
                            <?=isset($sale['finish']) ? '' : "<td class='center b1sr b1sb'><input type='button' class='button' value='DELETE' onclick='if (confirm(\"Do you really want to delete this card payment?\")) { document.getElementById(\"delete_card_payment_id\").value = " . $id . "; document.delete_card_payment_form.submit(); }'/></td>"?>
                        </tr>
                        <?}?>
                    </table>
                <?}
            }
                function show_card_payments_input_box($sale){
                    if (    (isset($sale['card_payments'])           && is_array($sale['card_payments']) )   && 
                            count($sale['card_payments']) > 0       && 
                            (isset($_POST['show_card_payments'])    && $_POST['show_card_payments'] != 1) && 
                            !isset($sale['finish']) && 
                            $_SESSION['settings']['site'] == 'sales') {?>
                      <input class='button mt5 ml10 mr10' type='button' value='SHOW CARD PAYMENT DETAILS' onclick='document.getElementById("show_card_payments").value = 1; document.payment_details_form.submit();'/>
                    <?} 
                }
            function div_show_voucher_payment_details($sale){
                show_voucher_payments_input_box($sale);
                if ( 
                        (   (isset($sale['voucher_payments'])       && is_array($sale['voucher_payments'])) && 
                            count($sale['voucher_payments']) > 0    && 
                            (isset($_POST['show_voucher_payments']) && $_POST['show_voucher_payments']  == 1 ))
                            || 
                        (   (isset($sale['finish']) && $sale['finish'] == 1 )) &&
                            (isset($sale['voucher_payments'])       && is_array($sale['voucher_payments'])) && 
                            count($sale['voucher_payments']) > 0
                ) {    
                    if (!isset($sale['finish'])) {?>
                        <form method='post' name='delete_voucher_payment_form'><input type='hidden' name='delete_voucher_payment_id' id='delete_voucher_payment_id' value='0'/>
                            <input type='hidden' name='delete_voucher_payment' value='1'/>
                            <input type='hidden' name='voucher_payment_management' value='1'/>
                            <input type='hidden' name='new_sale' value='1'/>
                            <input type='hidden' name='show_voucher_payments' value='1'/>
                        </form>
                    <?}?>
                    <table class='mt10 b1sl '>
                        <tr class='vmiddle bctrt'>
                          <td class='center bold s09 b1sr b1sb' colspan='<?=isset($sale['finish']) ? 4 : 5?>'>VOUCHER PAYMENT DETAILS</td>
                        </tr>
                        <tr class='vmiddle bctrt'>
                          <td class='center bold s08 b1sr b1sb'>#</td>
                          <td class='w200 center bold s08 b1sr b1sb'>DATE ISSUED</td>
                          <td class='w150 center bold s08 b1sr b1sb'>BARCODE</td>
                          <td class='w100 center bold s08 b1sr b1sb'>VALUE</td>
                          <?=isset($sale['finish']) ? '' : "<td class='center bold s08 b1sr b1sb'>DELETE?</td>"?>
                        </tr>
                    <?
                    $rownum = 1;
                    foreach (array_keys($sale['voucher_payments']) as $id) {?>
                        <tr class='vmiddle bctr1<?=$rownum % 2 == 1 ? 'a' : 'b'?>'>
                          <td class='center s08 b1sr b1sb'><?=$rownum++?></td>
                          <td class='center s08 b1sr b1sb'><?=$sale['voucher_payments'][$id]['issued']?></td>
                          <td class='center s08 b1sr b1sb'><?=$sale['voucher_payments'][$id]['barcode']?></td>
                          <td class='center s08 b1sr b1sb'><?=$sale['voucher_payments'][$id]['value']?></td>
                          <?=isset($sale['finish']) ? '' : "<td class='center b1sr b1sb'><input type='button' class='button' value='DELETE' onclick='if (confirm(\"Do you really want to delete this voucher payment?\")) { document.getElementById(\"delete_voucher_payment_id\").value = \"" . $id . "\"; document.delete_voucher_payment_form.submit(); }'/></td>"?>
                        </tr>
                    <?}?>
                    </table>
                <?}
            }
                function show_voucher_payments_input_box($sale){
                if (    (isset($sale['voucher_payments'])        && is_array($sale['voucher_payments'])) && 
                        count($sale['voucher_payments']) > 0    && 
                        (isset($_POST['show_voucher_payments']) && $_POST['show_voucher_payments'] != 1 ) && 
                        !isset($sale['finish']) && 
                        $_SESSION['settings']['site'] == 'sales') {?>
                    <input class='button mt5 ml10 mr10' type='button' value='SHOW VOUCHER PAYMENT DETAILS' onclick='document.getElementById("show_voucher_payments").value = 1; document.payment_details_form.submit();'/>
                <?}            
            }
        function show_sale_items($sale, $word = '', $classes = '', $change_price = 0) {
            if ($word == "") { $word = "sale"; };
            if (((is_array($sale['basket']['items']) && count($sale['basket']['items']) > 0) || (is_array($sale['basket']['gift_certificates']) && count($sale['basket']['gift_certificates']) > 0)) && (!isset($sale['receipt_id']) || ($_SESSION['settings']['site'] == 'sales' || $_SESSION['settings']['site'] == 'reports' || $_SESSION['settings']['site'] == 'itemmgnt' || (isset($_SESSION['exchange']['receipt_id']) && $sale['receipt_id'] == $_SESSION['exchange']['receipt_id'])))) 
                {
                if ($bool = ((!isset($sale['encash']) && $_SESSION['settings']['site'] == 'sales') || ($_SESSION['settings']['site'] == 'exchanges' && !isset($_SESSION['exchange']['encash']) && $_SESSION['settings']['exchanges']['manage_type'] == 'sales'))) {?>
                <form method='post' name='update_item_price_form'>
                    <input type='hidden' name='new_price' id='new_item_price' value='0'/>
                    <input type='hidden' name='item_id' id='item_id_price' value='0'/>
                    <input type='hidden' name='update_item_price' value='1'/>
                    <input type='hidden' name='sale_management' value='1'/>
                </form>
                <form method='post' name='changeitemquantityform'>
                    <input type='hidden' name='new_lastitemid' id='changequantityitemid' value='0'/>
                    <input type='hidden' name='saleitemid' id='plusorminus' value='+'/>
                    <input type='hidden' name='modify_basket' value='1'/>
                    <input type='hidden' name='new_sale' value='1'/>
                </form>
                <form method='post' name='removeitemform'>
                    <input type='hidden' name='removeitemid' id='removeitemid' value='0'/>
                    <input type='hidden' name='modify_basket' value='1'/>
                    <input type='hidden' name='new_sale' value='1'/>
                </form>
                <form method='post' name='change_gift_certificate_quantity_form'>
                    <input type='hidden' name='change_gift_certificate_value' id='change_gift_certificate_value' value='0'/>
                    <input type='hidden' name='change_how_gift_certificate' id='change_how_gift_certificate' value='+'/>
                    <input type='hidden' name='modify_basket' value='1'/>
                    <input type='hidden' name='new_sale' value='1'/>
                </form>
                <form method='post' name='remove_gift_certificate_form'>
                    <input type='hidden' name='remove_gift_certificate_value' id='remove_gift_certificate_value' value='0'/>
                    <input type='hidden' name='remove_gift_certificate_value_form' value='1'/>
                    <input type='hidden' name='modify_basket' value='1'/>
                    <input type='hidden' name='new_sale' value='1'/>
                </form>
                <?}?>
                    <div class='d_InlineBlock hp100 f_left mt5 bcwhite b1st<?=$classes == '' ? '' : " $classes"?>'>
                        <div class='d_InlineBlock f_left wp100 hp05 bctrt'>
                              <div class="b1sl b1sr b1sb">SALE ITEMS</div>
                        </div>
                        <div class='d_InlineBlock f_left wp100 hp05 bctrt'>
                              <div class=' f_left wp03 hp100 s08'>#</div>
                              <div class=' f_left wp10 hp100 s08'>PIC</div>
                              <div class=' f_left wp30 hp100 s06'></div>
                              <div class=' f_left wp10 hp100 s06'>PRICE</div>
                              <div class=' f_left wp12 hp100 s06'>ADDITIONAL DISCOUNT</div>
                              <div class=' f_left wp10 hp100 s08'>QTY</div>
                              <div class=' f_left wp10 hp100 s06'>SUBTOTAL</div>
                              <div class=' f_left wp05 hp100 s08'>TAX</div>
                              <div class=' f_left wp10 hp100 s08'>&nbsp;</div>
                        </div> 
                        <div class='d_InlineBlock f_left wp100 hp85 scrolling'>
                          <? if (is_array($sale['basket']['items'])) {
                              $rownum = 1;
                              foreach (array_keys($sale['basket']['items']) as $key) {
                                  $item = &$sale['basket']['items'][$key];
                                  if ($item['quantity'] > 0) {
                                          $result = select_db('iim.image_id,iim.image_db_id', 'item_image_mappings as iim join items as i on i.id = iim.id join categories as c on c.id = i.category_id join items as i2 on i2.number = i.number and i2.category_id = i.category_id', sprintf('i2.id = %s and c.company_id = %s and coalesce(iim.deleted, i.deleted, c.deleted, i2.deleted) is null order by iim.default_group_image desc, i.id asc, iim.default_item_image desc limit 1', quote_smart($key), quote_smart($_SESSION['settings']['company_id'])));
                                          $imageid = 0;
                                          if ($result_array = mysql_fetch_array($result)) { $imageid = $result_array['image_id'];  $image_db_id = $result_array['image_db_id'];}
                                          $vals = calc($item['price'], $item['discount'], $item['additional_discount'], $item['tax'], $item['quantity']);
                                          $vals2 = calc($item['real_price'], $item['real_discount'], 0, 1);
                                          $extra_discount = number(100 / $vals2['price'] * ($vals2['price'] + ($vals['odiscount'] / $item['quantity']) - $vals2['odiscount'] - $item['price']));
                                   ?>
                                  <div class='d_InlineBlock f_left wp99 h40px bctr1<?=$rownum % 2 == 1 ? 'a' : 'b'?>'>
                                      <div class='d_InlineBlock f_left wp03 hp100 s07'>
                                          <div class='f_left wp100 hp100 b1sl b1sr b1sb'>
                                              <?=$rownum++?>
                                          </div>
                                      </div>
                                      <div class='d_InlineBlock f_left wp10 hp100 center lh0'>
                                          <div class='f_left wp100 hp100 b1sr b1sb'>      
                                                <img   class="m0 <? if ($imageid > 0) { print ' mp '; } else { print ' hp100 wp100';} ?> "
                                                    alt=""
                                                    src="showimage.php?id=<?=$imageid?>&image_db_id=<?=$image_db_id?>&w=50&h=40"
                                                    <? if ($imageid > 0) { ?> 
                                                    onclick='window.open("showimage.php?id=<?=$imageid?>&image_db_id=<?=$image_db_id?>&w=600&h=600", "_new", "innerHeight=620,innerWidth=620,scrollbars=no,toolbar=no,resizable=no,menubar=0");'
                                                    <? } ?>                                                               
                                                />
                                          </div>
                                      </div>
                                      <div class='d_InlineBlock f_left wp30 hp100 s07'>
                                          <div class='f_left wp100 hp100 b1sr b1sb'>
                                              <div class='d_InlineBlock wp95 hp30 left'>
                                              <?=$item['name']?>
                                          </div>
                                              <div class='d_InlineBlock wp95 h30 left'>
                                                  <?=$item['attribute1']?>
                                              </div>
                                              <div class='d_InlineBlock wp95 hp40'>
                                                  <div class='f_left left wp80 hp100'>
                                                      <?=$item['attribute2']?><br>
                                                  </div>
                                                  <div class='f_left right s08 wp20 hp100 mp' onclick="Inventory_Items_Edit_Item(<?=$key?>)">
                                                      Details
                                                  </div>
                                              </div>
                                          </div>
                                      </div>
                                      <div class='d_InlineBlock f_left wp10 hp100 s08'<?=$change_price == 1 ? " onclick='document.getElementById(\"div_$key\").style.display = \"none\"; document.getElementById(\"set_div_$key\").style.display = \"\"; document.getElementById(\"price_$key\").focus();'" : ''?>>
                                          <div class='f_left wp100 hp100 b1sr b1sb'>
                                              <div class='s08' id='div_<?=$key?>' class='s08'>
                                                  <?=money($item['finalprice'])?>
                                                  <? if ($item['real_discount'] > 0 || $extra_discount != 0) { ?>
                                                    <br/>(-<?=abs($item['real_discount'])?>%<? if ($extra_discount != 0) { ?> + extra <?=($extra_discount > 0 ? '-' : '+') . abs($extra_discount)?>%<? } ?>)
                                                  <? } ?>
                                              </div>
                                              <div id='set_div_<?=$key?>' style='display: none;'>
                                                  <div class='f_left wp100 hp100 b1sr b1sb'>
                                                      <input type='text' class='s08 w50' id='price_<?=$key?>' value='<?=number($item['finalprice'])?>' onchange='this.blur()' onblur='document.getElementById("item_id_price").value = <?=$key?>; document.getElementById("new_item_price").value = this.value; document.update_item_price_form.submit();'/>
                                                  </div>    
                                              </div>
                                          </div>
                                      </div>
                                      <div class='d_InlineBlock f_left wp12 hp100 s08'>
                                          <div class='f_left wp100 hp100 b1sr b1sb'>
                                              <form class='wp100 hp100' name='additional_discount_input' method='post' onSubmit='return Sales_ProcessVariables(this.name)'>
                                              <div class='d_InlineBlock f_left wp100 hp40'>
                                                  <input class='w20 center s06 text' name='itemdiscount' value='<?=$item['additional_discount'] == '' ? 0 : $item['additional_discount'] ?>'/>
                                              </div>
                                              <? if ($bool) { ?>
                                              <div class='d_InlineBlock f_left wp100 hp60'>
                                                  <div class='f_left wp100 hp100'>
                                                      <input class='ml2 button' type='button' value='Update' onclick="this.disabled=true; this.value='Sending'; Sales_ProcessVariables(this.form.name);"/>
                                                  </div>
                                              </div>
                                              <input type='hidden' name='itemid' value='<?=$key?>'/>
                                              <input type='hidden' name='set_item_discount' value='1'/>
                                              <input type='hidden' name='new_sale' value='1'/>
                                              </form>
                                              <? } ?>
                                          </div>              
                                      </div>
                                      <div class='d_InlineBlock f_left wp10 hp100 s08'>
                                          <div class='f_left wp100 hp100 b1sr b1sb'>
                                              <div class='d_InlineBlock f_left wp100 hp40'>
                                                    <?=$item['quantity']?>
                                              </div>
                                              <? if ($bool) { ?>
                                              <form name='changeitemquantityform'>
                                              <div class='d_InlineBlock f_left wp100 hp60'>
                                                  <div class='f_left wp50 hp100'>
                                                      <input class='ml5 w20 button' 
                                                             type='button' 
                                                             value='+' 
                                                             onclick='  document.getElementById("changequantityitemid").value   = <?=$key?>; 
                                                                        Sales_ProcessVariables(this.form.name);'/>
                                                  </div>
                                                  <div class='f_left wp50 hp100'>
                                                      <input class='ml1 w20 button' 
                                                             type='button' 
                                                             value='-' 
                                                             onclick='  document.getElementById("changequantityitemid").value   = <?=$key?>; 
                                                                        document.getElementById("plusorminus").value            = "-"; 
                                                                        Sales_ProcessVariables(this.form.name);'/>
                                                  </div>
                                              </div>
                                              </form>
                                              <? } ?>
                                          </div>
                                      </div>
                                      <div class='d_InlineBlock f_left wp10 hp100 s07'>
                                          <div class='f_left wp100 hp100 b1sr b1sb'>
                                              <?=money2($vals['total'] - $vals['tax'])?>
                                          </div>
                                      </div>
                                      <div class='d_InlineBlock f_left wp05 hp100 s07'>
                                          <div class='f_left wp100 hp100 b1sr b1sb'>
                                              <?=$item['tax']?>%
                                          </div>
                                      </div>
                                      <?if ($bool) {?>
                                      <div class='d_InlineBlock f_left wp10 hp100 s08'>
                                          <div class='f_left wp100 hp100 b1sr b1sb'>
                                              <form name='removeitemform'>
                                                <input type='hidden' name='remove_item_from_basket' value='1'/>
                                                <input type='hidden' name='new_sale'                value='1'/>
                                                <input class='button' 
                                                     type='button' 
                                                     value='DEL'
                                                     onclick='if (!confirm("Do you really want to remove this item from this sale?")) { return false; }; 
                                                            document.getElementById("removeitemid").value       = <?=$key?>; 
                                                            Sales_ProcessVariables(this.form.name);'/>
                                              </form>
                                          </div>
                                      </div>
                                      <?}?>
                                  </div>
                                  <?}
                              }
                          }
                              if (isset($sale['basket']['gift_certificates']) && count($sale['basket']['gift_certificates']) > 0) {
                                  $rownum = 1;
                                  foreach (array_keys($sale['basket']['gift_certificates']) as $value) {?>
                                      <div class='d_InlineBlock f_left wp100 h40px bctr1<?=$rownum % 2 == 1 ? 'a' : 'b'?>'>
                                          <div class='d_InlineBlock f_left wp03 hp100 center s08'>
                                              <div class='f_left wp100 hp100 b1sl b1sr b1sb'>
                                                  <?=$rownum++?>
                                              </div>                            
                                          </div>
                                          <div class='d_InlineBlock f_left wp10 hp100 center s08 lh0'>
                                              <div class='f_left wp100 hp100 b1sr b1sb'>
                                                  -
                                              </div>                            
                                          </div>
                                          <div class='d_InlineBlock f_left wp10 hp100 center s08'>
                                              <div class='f_left wp100 hp100 b1sr b1sb'>
                                                  -
                                              </div>                            
                                          </div>
                                          <div class='d_InlineBlock f_left wp10 hp100 center s08'>
                                              <div class='f_left wp100 hp100 b1sr b1sb'>
                                                  -
                                              </div>                            
                                          </div>
                                          <div class='d_InlineBlock f_left wp10 hp100 center s08'>
                                              <div class='f_left wp100 hp100 b1sr b1sb'>
                                                  gift certificate
                                              </div>                            
                                          </div>
                                          <div class='d_InlineBlock f_left wp10 hp100 center s08'>
                                              <div class='f_left wp100 hp100 b1sr b1sb'>
                                                  <?=money($value)?>
                                              </div>
                                          </div>
                                          <div class='d_InlineBlock f_left wp12 hp100 center'>
                                              <div class='f_left wp100 hp100 b1sr b1sb'>
                                                  -
                                              </div>                             
                                          </div>
                                          <div class='d_InlineBlock f_left wp10 hp100 s08'>
                                              <div class='f_left wp100 hp100 b1sr b1sb'>
                                                  <div class='f_left wp30 hp100'>
                                                      <?=count($sale['basket']['gift_certificates'][$value])?>
                                                  </div>
                                                  <? if ($sale['encash'] != 1) { ?>
                                                  <div class='d_InlineBlock f_left wp70 hp100'>
                                                      <div class='f_left wp50 hp100'>
                                                          <input class='ml5 w20 button' type='button' value='+' onclick='document.getElementById("change_gift_certificate_value").value = <?=$value?>; document.change_gift_certificate_quantity_form.submit();'/>
                                                      </div>
                                                      <div class='f_left wp50 hp100'>
                                                          <input class='ml1 w20 button' type='button' value='-' onclick='document.getElementById("change_gift_certificate_value").value = <?=$value?>; document.getElementById("change_how_gift_certificate").value = "-"; document.change_gift_certificate_quantity_form.submit();'/>
                                                      </div>
                                                  </div><? } ?>
                                              </div>
                                          </div>                                    
                                          <div class='d_InlineBlock f_left wp10 hp100 center s08'>
                                              <div class='f_left wp100 hp100 b1sr b1sb'>
                                                  <?=money($value * count($sale['basket']['gift_certificates'][$value]))?>
                                              </div> 
                                          </div>
                                          <div class='d_InlineBlock f_left wp05 hp100 s08'>
                                              <div class='f_left wp100 hp100 b1sr b1sb'>
                                                  -
                                              </div> 
                                          </div>
                                          <?if ($bool) {?>
                                          <div class='d_InlineBlock f_left wp10 hp100 left'>
                                              <input class='button' type='button' value='DEL' onclick='if (!confirm("Do you really want to remove this gift certificate from this sale?")) { return false; }; document.getElementById("remove_gift_certificate_value").value = <?=$value?>; document.remove_gift_certificate_form.submit();'/>
                                          </div>
                                          <?}?>
                                    </div>
                                  <?}
                              }?>
                          </div>
                    </div>
                <? } else {?>
                    <div class='d_InlineBlock f_left wp100 mt15 s09'>
                        The <?=$word?> does not include any sale item.
                    </div>
                <?}
        }    

    function div_sale_right_column($sale,$payinfo){
        if (!isset($sale['finish'])) {
            ?>
            <? div_payment_details($sale,$payinfo); ?>
            <div class='d_InlineBlock f_left wp100 h10px'></div>               
            <? div_payment_options($sale,$payinfo); ?>
        <?}           
    }
        function div_payment_details($sale,$payinfo){
            $key_wpercent = 'wp75';
            $val_wpercent = 'wp25';
            ?>
            <div class='d_InlineBlock f_left wp100 h200px'>
                <div class='d_InlineBlock wp99 hp10 vmiddle bctrt'>
                  <div class='bold s08 wp100 hp100'>Payment Details</div>
                </div>
                <div class='d_InlineBlock wp100 hp90 vmiddle '>
                    <div class='d_InlineBlock wp90 hp100 left s06'>
                        <div class='d_InlineBlock f_left hp15 wp100 vmiddle bctr1a'>
                          <div class='f_left left hp100 <?=$key_wpercent?> bold b1sb'>OPEN AMOUNT:</div>
                          <div class='f_right right hp100 <?=$val_wpercent?>  b1sb'><?=money2($payinfo['open'])?></div>
                        </div>
                        <div class='d_InlineBlock f_left hp15 wp100 vmiddle bctr1b'>
                          <div class='f_left left hp100 <?=$key_wpercent?> bold b1sb'>PAID CASH:</div>
                          <div class='f_right right hp100 <?=$val_wpercent?> b1sb'><?=money2($payinfo['paidbycash'])?></div>
                        </div>
                        <div class='d_InlineBlock f_left hp15 wp100 vmiddle bctr1a'>
                          <div class='f_left left hp100 <?=$key_wpercent?> bold b1sb'>PAID BY CARD(S):</div>
                          <div class='f_right right hp100 <?=$val_wpercent?> b1sb'><?=money2($payinfo['paidbycard'])?></div>
                        </div>
                        <div class='d_InlineBlock f_left hp15 wp100 vmiddle bctr1b'>
                          <div class='f_left left hp100 <?=$key_wpercent?> bold b1sb'>PAID BY VOUCHER(S):</div>
                          <div class='f_right right hp100 <?=$val_wpercent?>  b1sb'><?=money2($payinfo['paidbyvoucher'])?></div>
                        </div>
                        <div class='d_InlineBlock f_left hp15 wp100 vmiddle bctr1b'>
                          <div class='f_left left hp100 <?=$key_wpercent?> bold b1sb'>CHANGE:</div>
                          <div class='f_right right hp100 <?=$val_wpercent?> b1sb'><?=money2($payinfo['cash'])?></div>
                        </div>
                        <div class='d_InlineBlock f_left hp15 wp100 vmiddle bctr1b'>
                          <div class='f_left left hp100 <?=$key_wpercent?> bold b1sb'>NEW VOUCHER VALUE:</div>
                          <div class='f_right right hp100 <?=$val_wpercent?> b1sb'><?=money2($payinfo['voucher'])?></div>
                        </div>
                    </div>
                </div>
            </div>
        <?}
        function div_payment_options($sale,$payinfo){
            $key_wpercent = 'wp50';
            $val_wpercent = 'wp25';
            $sub_wpercent = 'wp25';
            if ( (isset($payinfo['open'])    && $payinfo['open'] > 0 ) &&
                 (isset($sale['encash'])     && $sale['encash']  == 1) &&
                 !isset($sale['receipt_id'])
            ) {?>
            <form></form>
            <div class='d_InlineBlock f_left wp100'>
                <div class='d_InlineBlock wp99 hp10 vmiddle bctrt'>
                  <div class='bold s08'>Payment Options</div>
                </div>
                <div class='d_InlineBlock wp100 hp90 vmiddle'>
                    <div class='d_InlineBlock wp90 left b1sb'>                        
                        <div class='f_left wp100 vmiddle bctr1a'>
                            <form method='post' name='paid_cash_submit'>
                                <div class='f_left left     <?=$key_wpercent?> s07 bold b1sb'>&nbsp;
                                  <?=$_SESSION['preferences']['currency_position'] == 1 ? $_SESSION['preferences']['currency'] . '&nbsp;' : ''?>
                                    Cash:
                                  <?=$_SESSION['preferences']['currency_position'] == 0 ? "<span class='s08'>&nbsp;" . $_SESSION['preferences']['currency'] . '&nbsp;</span>' : ''?>
                                </div>
                                <div class='f_left left     <?=$val_wpercent?> s07 bold b1sb'>
                                    <input class='w50 text' type='text' name='paidamount'<?=$payinfo['open'] > 0 ? " id='focusitem'" : ''?>/>
                                </div>
                                <div class='f_right right   <?=$sub_wpercent?> s08 bold b1sb'>
                                    <input class='button ml2' type='submit' onClick="this.disabled=true; this.value='Sending'; Sales_ProcessVariables(this.form.name);" value='PAY'/>
                                    <input type='hidden' name='paid_cash' value='1'/>
                                    <input type='hidden' name='new_sale' value='1'/>                                
                                </div>
                            </form>
                        </div>

                        <div class='f_left wp100 vmiddle bctr1a'>
                            <form method='post' name='paid_card_submit'>
                                <div class='f_left left     <?=$key_wpercent?> s07 bold b1sb'>&nbsp;
                                  <?=$_SESSION['preferences']['currency_position'] == 1 ? $_SESSION['preferences']['currency'] . '&nbsp;' : ''?>
                                    Card:
                                  <?=$_SESSION['preferences']['currency_position'] == 0 ? "<span class='s08'>&nbsp;" . $_SESSION['preferences']['currency'] . '&nbsp;</span>' : ''?>
                                </div>
                                <div class='f_left left     <?=$val_wpercent?> s07 bold b1sb'>
                                   <input class='w50 text' type='text' name='paidamount' value='<?=number($payinfo['open'])?>'/>
                                      <?=$_SESSION['preferences']['currency_position'] == 0 ? "<span class='s08'>&nbsp;" . $_SESSION['preferences']['currency'] . '&nbsp;</span>' : ''?>
                                    <select class='ml2' name='cardtype'>
                                        <? $result = select_db('id, name', 'card_types', sprintf('company_id = 0 or company_id = %s', $_SESSION['settings']['company_id']));
                                        while ($result_array = mysql_fetch_array($result)) { ?>
                                            <option value='<?=$result_array['id']?>'><?=$result_array['name']?></option>
                                        <?}?>
                                    </select>
                                </div>
                                <div class='f_right right   <?=$sub_wpercent?> s08 bold b1sb'>
                                    <input class='button ml2' type='submit' onClick="this.disabled=true; this.value='Sending'; Sales_ProcessVariables(this.form.name);" value='PAY'/>
                                    <input type='hidden' name='add_card_payment' value='1'/>
                                    <input type='hidden' name='card_payment_management' value='1'/>
                                    <input type='hidden' name='new_sale' value='1'/>
                                </div>
                            </form>
                        </div>

                        <div class='f_left wp100 vmiddle bctr1a'>
                            <form method='post' name='paid_voucher_submit'>
                                <div class='f_left left     <?=$key_wpercent?> s07 bold b1sb'>&nbsp;
                                  <?=$_SESSION['preferences']['currency_position'] == 1 ? $_SESSION['preferences']['currency'] . '&nbsp;' : ''?>
                                    Voucher:
                                  <?=$_SESSION['preferences']['currency_position'] == 0 ? "<span class='s08'>&nbsp;" . $_SESSION['preferences']['currency'] . '&nbsp;</span>' : ''?>
                                </div>
                                <div class='f_left left     <?=$val_wpercent?> s07 bold b1sb'>
                                    <input type='text'   name='voucherbarcode' class='w100 text' />
                                </div>
                                <div class='f_right right   <?=$sub_wpercent?> s08 bold b1sb'>
                                    <input class='button ml2' type='submit' onClick="this.disabled=true; this.value='Sending'; Sales_ProcessVariables(this.form.name);" value='PAY'/>
                                    <input type='hidden' name='add_voucher_payment' value='1'/>
                                    <input type='hidden' name='new_sale' value='1'/>
                                    <input type='hidden' name='voucher_payment_management' value='1'/>                                
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <? } ?>       
            <form method='post' name='payment_details_form'>
              <input type='hidden' name='show_card_payments'    id='show_card_payments'     value='0'/>
              <input type='hidden' name='show_voucher_payments' id='show_voucher_payments'  value='0'/>
            </form>
            <? 
        }
?>