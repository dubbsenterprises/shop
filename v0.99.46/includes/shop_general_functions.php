<?php
function show_calendar22($name, $text = '', $date = 0, $max_year = 0, $submit = 0) {
	if ($date != 0) {
		$date_array = explode('-', $date);
		if (count($date_array) == 3) {
			$cur = array('year' => abs($date_array[0]), 'month' => abs($date_array[1]), 'day' => abs($date_array[2]));
		} else {
			$date = 0;
		}
	}

	if ($date == 0) {
		$time = time();
		$cur = array('year' => date('Y', $time), 'month' => date('n', $time), 'day' => date('j', $time));
	}

	$max_year = $max_year == 0 ? $cur['year'] : $max_year;

?>
        <table>
          <td class='bold s08 pr5'><?=$text?></td>
	  <td>
            <select id='<?=$name?>_month' onchange='update_calendar(document.getElementById("<?=$name?>_year"), this, document.getElementById("<?=$name?>_day"), document.getElementById("<?=$name?>_date"), 1);<?=$submit == 1 ? ' this.form.submit();' : ''?>'>
<?

	$months = array(1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December');

	for ($i = 1; $i <= 12; $i++) {

?>
              <option value='<?=($i < 10 ? '0' : '') . $i?>'<?=$cur['month'] == $i ? ' selected' : ''?>><?=$months[$i]?></option>
<?

	}

?>
            </select>
          </td>
          <td class='pl5'>
            <select id='<?=$name?>_day' onchange='update_calendar(document.getElementById("<?=$name?>_year"), document.getElementById("<?=$name?>_month"), this, document.getElementById("<?=$name?>_date"));<?=$submit == 1 ? ' this.form.submit();' : ''?>'>
<?

	$max = 28;
	for ($i = 29; $i <= 31; $i++) {
		if (checkdate($cur['month'], $i, $cur['year'])) {
			$max = $i;
		}
	}

	for ($i = 1; $i <= $max; $i++) {

?>
              <option value='<?=($i < 10 ? '0' : '') . $i?>'<?=$cur['day'] == $i ? ' selected' : ''?>><?=$i?>,</option>
<?

	}

?>
            </select>
          </td>
          <td class='pl5'>
            <select id='<?=$name?>_year' onchange='update_calendar(this, document.getElementById("<?=$name?>_month"), document.getElementById("<?=$name?>_day"), document.getElementById("<?=$name?>_date"), 1);<?=$submit == 1 ? ' this.form.submit();' : ''?>'>
<?

	for ($i = $cur['year'] < 2010 ? $cur['year'] : 2010; $i <= $max_year; $i++) {

?>
              <option value='<?=$i?>'<?=$cur['year'] == $i ? ' selected' : ''?>><?=$i?></option>
<?

	}

?>
            </select>
          </td>
        </table>
        <input type='hidden' id='<?=$name?>_date' name='<?=$name?>_date' value='<?=$cur['year'] . '-' .  ($cur['month'] < 10 ? '0' : '') . $cur['month'] . '-' . ($cur['day'] < 10 ? '0' : '') . $cur['day']?>'/>
<?

}
function mainLogin() {
        session_start();
	$badlogin           = isset($_SESSION['settings']['badlogin']);
        $badlogin_message   =       $_SESSION['settings']['badlogin_message'];
	session_unset();
?>
    <table class='wp100 hp100'>
        <tr class='vmiddle'>
            <td class='center'>
            <form method='post'>
                <table class='mauto'>
                  <tr>
                    <td class='p10 b1s bctrt bold s15'>MAIN LOGIN</td>
                  </tr>
                  <tr>
                    <td class='p10 b1s bctrl' style='border-top: 0px;'>
                    <?if ($badlogin) {?>
                      <div class='wp100 mb10 red s07 bold'><?=$badlogin_message?></div>
                    <? }?>
                      <table>
                        <tr class='vmiddle'>
                            <td class='left pb3 pr10 s07 bold'>
                                NAME:
                            </td>
                            <td class='left pb3'>
                                <input type='text' class='text w200' name='login_name' id='focusitem'/>
                            </td>
                        </tr>
                        <tr class='vmiddle'>
                            <td class='left pr10 s07 bold'>
                                PASSWORD:
                            </td>
                            <td class='left'>
                                <input type='password' class='text w200' name='login_password'/>
                            </td>
                        </tr>
                      </table>
                      <div class='wp100 mt20'><input class='button' type='submit' value='LOGIN' onclick='if (getElementById("focusitem").value == "") { return false; };'/></div>
                      <input type='hidden' name='mainlogin' value='1'/>
                    </td>
                  </tr>
              </table>
            </form>
            </td>
        </tr>
    </table>
<?

}
function left_menu() { ?>
    <div class="f_left wp90 hp100 center">
       <div class="f_left wp100 hp90 center">
            <div class='f_left wp100 s09 mb10'><input type="button" class="button wp90" value="HOME"            onclick="mainDiv('mainPage')""></div>
            <div class='f_left wp100 s09 mb10'><input type="button" class="button wp90" value="NEW&nbsp;SALE"   onclick="mainDiv('new_sale')"></div>
            <div class='f_left wp100 s09 mb10'><input type="button" class="button wp90" value="RETURNS"         onclick="document.getElementById('post_values').value = 'page_management=1|new_site=returns'; document.page_form.submit();"></div>
            <div class='f_left wp100 s09 mb10'><input type="button" class="button wp90" value="EXCHANGES"       onclick="document.getElementById('post_values').value = 'page_management=1|new_site=exchanges'; document.page_form.submit();"></div>
            <div class='f_left wp100 s09 mb10'><input type="button" class="button wp90" value="ITEM SEARCH"     onclick="mainDiv('item_search')"></div>
    <? if ($_SESSION['settings']['manager'] == 1) {?>
            <div class='f_left wp100 s09 mb10'><input type="button" class="button wp90" value="ITEM&nbsp;MGMT." onclick="mainDiv('ItemManagement')"></div>
            <div class='f_left wp100 s09 mb10'><input type="button" class="button wp90" value="REPORTS"         onclick="mainDiv('reports')"></div>
            <? if ($_SESSION['settings']['company_id'] == 0) { ?>
            <div class='f_left wp100 s09 mb10'><input type="button" class="button wp90" value="COMPANIES"       onclick="mainDiv('companies')"></div>
            <? } ?>
    <? } ?>
       </div>
       <div class='f_left wp100 hp10'>
            <div class='f_left left    wp100 hp80 s07'>
              <? if (isset($_SESSION['settings']['user'])) { ?>
                  <div class='d_InlineBlock wp100 f_left center'>Welcome <?=$_SESSION['settings']['user']; ?> (<a href='ajax/user_logout.php'>LOGOUT</a>)</div>
                  <div class='d_InlineBlock wp100 f_left center'>Level: <?=$_SESSION['userlevels'][ $_SESSION['settings'][ $_SESSION['settings']['login_id'] ]['level'] ]?>(<?=$_SESSION['settings'][$_SESSION['settings']['login_id']]['level']?>)</div>
              <? }?>
            </div>
            <div class='f_right center wp100 hp20 s07'>
                  <? if ($_SESSION['settings']['admin'] == 99) {?>
                  <div class='pr10'><a href='javascript: none();' onclick="document.getElementById('post_values').value = 'page_management=1|new_site=preferences'; document.page_form.submit();">PREFERENCES</a></div>
                  <?}?>
                  TIM<span onclick="window.open('ssv.php', '_blank', 'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=800,height=400,left=430,top=23');">E</span>: <span id='localtime'>-</span>
            </div>
        </div>
    </div>
<?}
function showpage($page) {?>
    <div id="mainBody" class='f_left wp100 hp100' >
        <form name='settings_form' method='post'>
          <input type='hidden' name='new_settings' id='new_settings' value=''/>
	  <input type='hidden' name='drop_settings' id='drop_settings' value=''/>
        </form>
    <? if (isset($_SESSION['message'])) {?>
        <div class='messagebox<? if (strpos(strtoupper($_SESSION['message']), 'ERROR:') !== false ) { ?>_red<? 
        } else if (strpos(strtoupper($_SESSION['message']), 'WARNING:') !== false) { 
            ?>_orange<? 
        } else { 
            ?>_green<? } ?> mauto s08 bold pt5 pl5 pr5 mb5'>INFO MESSAGE:<div class='mt5 mb10'><?=$_SESSION['message']?></div>
        </div>
    <?}
    
    if ($page) {
        $site = $page;
    } else {
        $site = $_SESSION['settings']['site'] ;
    }

    switch ($site) {
        case 'main':
            require_once('mainPage_functions.php');
            mainPage(); break;
        case 'calendar':
            require_once('calendar_functions.php');
            calendar(); break;            
        case 'profile':
            profilesPage(); break;
        case 'preferences':
            preferencesPage(); break;
        case 'sales':
            require_once('sales_functions.php');
            sales(); break;
        case 'returns':
            returns(); break;
        case 'exchanges':
            exchanges(); break;
        case 'itemmgnt':
            itemMgntPage(); break;
        case 'reports':
            reportsPage(); break;
        default:
            unknownPage();
    }
    ?>
    </div>
    <?
    if (isset($_SESSION['message'])) { unset($_SESSION['message']); }
    if (isset($_SESSION['bad'])) { unset($_SESSION['bad']); }
    if (isset($_SESSION['results'])) { unset($_SESSION['results']); }
}
function quote_smart($value, $force = 0) {
        if ($force == 0 && ((is_numeric($value) && (substr($value, 0, 1) != '0' || substr($value, 1) == '')) || strtolower($value) == "null")) {
                return $value;
        }

	if ($force == 0 && $value === null) {
		return "null";
	}

        if (get_magic_quotes_gpc()) {
                $value = stripslashes($value);
        }

        return "'" . mysql_escape_string($value) . "'";
}
function replace_ticks($string) {
	return str_replace("'", '&#39;', $string);
}
function number($number, $decimals = 2, $dec_point = '.', $thousands_sep = '') {
	return number_format($number, $decimals, $dec_point, $thousands_sep);
}
function money($amount) {
	return $_SESSION['preferences']['currency_position'] == 1 ? $_SESSION['preferences']['currency'] . ($_SESSION['preferences']['money_string_contains_space'] ? '&nbsp;' : '') . number($amount) : number($amount) . ($_SESSION['preferences']['money_string_contains_space'] ? '&nbsp;' : '') . $_SESSION['preferences']['currency'];
}
function calc($price, $discount, $additional_discount = 0, $tax = 0, $quantity = 1) {
	if (!is_numeric($price)) { $price = 0; }
	if (!is_numeric($tax)) { $tax = $_SESSION['preferences']['tax']; }
	if (!is_numeric($discount)) { $discount = 0; }
	if (!is_numeric($additional_discount)) { $additional_discount = 0; }
	if (!is_numeric($quantity)) { $quantity = 1; }

	$odiscount = number($quantity * $discount * $price / 100);
	$xdiscount = number($additional_discount * ( ($quantity * $price) - $odiscount) / 100);
	
        $discount = $odiscount + $xdiscount;
	$tax = number( ($tax * (($quantity * $price) - $odiscount - $xdiscount) ) / 100);

	$price = number($quantity * $price);
	$total = $price - $discount + $tax;

	return array('total' => $total, 'price' => $price, 'tax' => $tax, 'discount' => $discount, 'odiscount' => $odiscount, 'xdiscount' => $xdiscount);
}
function pay($price, $cash, $card, $voucher) {
        $paidbyvoucher  = $voucher;
        $paidbycard     = $card;
        $paidbycash     = $cash;
        
	if (!is_numeric($price))   {$price = 0;   } else { $price   = round($price * 100); }
	if (!is_numeric($cash))    {$cash = 0;    } else { $cash    = round($cash * 100); }
	if (!is_numeric($card))    {$card = 0;    } else { $card    = round($card * 100); }
	if (!is_numeric($voucher)) {$voucher = 0; } else { $voucher = round($voucher * 100); }
	if ($voucher >= $price) {
            $voucher -= $price;
            $price = 0;
	} else {
            $price -= $voucher;
            $voucher = 0;
            if ($card >= $price) {
                $card -= $price;
                $price = 0;
            } else {
                $price -= $card;
                $card = 0;
                if ($cash >= $price) {
                    $cash -= $price;
                    $price = 0;
                } else {
                    $price -= $cash;
                    $cash = 0;
                }
            }
	}

	if ($card > 0) {
		$voucher += $card;
		$card = 0;
	}

	return array(   'open'          => $price / 100, 
                        'cash'          => $cash / 100, 
                        'card'          => $card / 100, 
                        'voucher'       => $voucher / 100, 
                        'paidbyvoucher' => $paidbyvoucher,
                        'paidbycash'    => $paidbycash,
                        'paidbycard'    => $paidbycard);
}
function currentTimestamp() {
	$result = select_db(sprintf('convert_tz(utc_timestamp(), "utc", %s) as now', quote_smart($_SESSION['preferences']['timezone'])));
	$result_array = mysql_fetch_array($result);
	return $result_array['now'];
}
function currentMilliseconds() {
	$result = select_db(sprintf('unix_timestamp(now()) * 1000 as current', quote_smart($_SESSION['preferences']['timezone'])));
	#$result = select_db(sprintf('unix_timestamp(convert_tz(utc_timestamp(), "utc", %s)) * 1000 as current', quote_smart($_SESSION['preferences']['timezone'])));
	$result_array = mysql_fetch_array($result);

	return $result_array['current'];
}
function format_date($date) {
	$date_array = explode('-', $date);
	if (count($date_array) != 3) {
            return '';
	}

	switch(abs($date_array[1])) {
            case 1: $date = 'January '; break;
            case 2: $date = 'February '; break;
            case 3: $date = 'March '; break;
            case 4: $date = 'April '; break;
            case 5: $date = 'May '; break;
            case 6: $date = 'June '; break;
            case 7: $date = 'July '; break;
            case 8: $date = 'August '; break;
            case 9: $date = 'September '; break;
            case 10: $date = 'October '; break;
            case 11: $date = 'November '; break;
            case 12: $date = 'December '; break;
	}
	$date .= abs($date_array[2]) . ', ' . $date_array[0];
	return $date;
}
function log_sql($sql) {
	$f = fopen('/tmp/shopsql.txt', 'a');
	fwrite($f, strftime('%D %T: ') . "$sql\n");
	fclose($f);
}
?>