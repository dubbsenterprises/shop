<?php

function unknownPage() {

?>
            <div class='mt20'>
              <div class='s15 bold'>ERROR</div>
              <div class='s09 mt15'>There is something wrong with the url address.<br/></div>
              <div class='s09 mt5'><a href="javascript: none();" onclick="document.getElementById('post_values').value = 'page_management=1|new_site=main'; document.page_form.submit();">CLICK HERE</a> to go back to the main page.</div>
            </div>
<?

}
function profilesPage() {

?>
            <div class='s15 bold mb20 mt10'>PROFILE<? if ($_SESSION['settings']['admin'] == 1) { ?>S<? } ?></div>
<?

	if ($_SESSION['settings']['admin'] == 1) {
		if ($_SESSION['profiles']['user_id'] > 0 && $result_array = mysql_fetch_array(select_db('firstname, lastname, username, level', 'logins', sprintf('id = %s and company_id = %s and deleted is null', quote_smart($_SESSION['profiles']['user_id']), quote_smart($_SESSION['settings']['company_id']))))) {

?>
            <div class='s1 bold mb20'>EDIT PROFILE</div>
            <div class='s09 mb20'>In the following you can edit the profile you selected before.<br/>If you do not want to update the password, then leave the password box empty.</div>
            <form method='post'>
              <table class='mb30'>
                <tr class='vmiddle'>
                  <td class='left s08 bold pr20 pb5<?=isset($_SESSION['bad']['firstname']) ? ' red' : ''?>'>FIRST NAME:</td>
                  <td class='left pb5'><input type='text' class='text w100' name='new_firstname' value='<?=replace_ticks($result_array['firstname'])?>'/></td>
                </tr>
                <tr class='vmiddle'>
                  <td class='left s08 bold pr20 pb5<?=isset($_SESSION['bad']['lastname']) ? ' red' : ''?>'>LAST NAME:</td>
                  <td class='left pb5'><input type='text' class='text w100' name='new_lastname' value='<?=replace_ticks($result_array['lastname'])?>'/></td>
                </tr>
                <tr class='vmiddle'>
                  <td class='left s08 bold pr20 pb5<?=isset($_SESSION['bad']['username']) ? ' red' : ''?>'>USERNAME:</td>
                  <td class='left pb5'><input type='text' class='text w100' name='new_username' value='<?=replace_ticks($result_array['username'])?>'/></td>
                </tr>
                <tr class='vmiddle'>
                  <td class='left s08 bold pr20 pb5<?=isset($_SESSION['bad']['password']) ? ' red' : ''?>'>PASSWORD:</td>
                  <td class='left pb5'><input type='password' class='text w100' name='new_password' value=''/></td>
                </tr>
                <tr class='vmiddle'>
                  <td class='left s08 bold pr20 pb20<?=isset($_SESSION['bad']['level']) ? ' red' : ''?>'>LEVEL:</td>
                  <td class='left pb20'>
                    <select name='new_level'>
<?

			foreach (array_keys($GLOBALS['userlevels']) as $userlevel) {

?>
                      <option value=<?=$userlevel?><?=$userlevel == $result_array['level'] ? ' selected' : ''?>><?=$GLOBALS['userlevels'][$userlevel]?></option>
<?

			}

?>
                    </select>
                  </td>
                </tr>
                <tr>
                  <td class='center' colspan='2'><input type='submit' class='button' value='UPDATE'/></td>
                </tr>
              </table>
              <input type='hidden' name='update_user_id' value='<?=replace_ticks($_SESSION['profiles']['user_id'])?>'/>
              <input type='hidden' name='update_user' value='1'/>
              <input type='hidden' name='user_management' value='1'/>
            </form>
            <form method='post' name='cancel_edit_user_form'>
              <input type='hidden' name='cancel_edit_user' value='1'/>
              <input type='hidden' name='user_management' value='1'/>
            </form>
            <a href='javascript: none();' class='s08 bold' onclick='document.cancel_edit_user_form.submit();'>GO BACK TO PREVIOUS PAGE</a>
<?

		} else {

?>
            <form method='post' name='edit_user_form'>
              <input type='hidden' name='edit_user_id' id='edit_user_id' value='0'/>
              <input type='hidden' name='edit_user' value='1'/>
              <input type='hidden' name='user_management' value='1'/>
            </form>
            <form method='post' name='delete_user_form'>
              <input type='hidden' name='delete_user_id' id='delete_user_id' value='0'/>
              <input type='hidden' name='delete_user' value='1'/>
              <input type='hidden' name='user_management' value='1'/>
            </form>
            <div class='s1 bold mb10'>PROFILES</div>
            <div class='mb10 s09'>Following profiles exist:</div>
            <table class='mb30 b1st b1sl bcwhite'>
              <tr class='bctrt'>
                <td class='s08 bold p5 b1sb b1sr'>#</td>
                <td class='s08 bold p5 b1sb b1sr'>LAST NAME</td>
                <td class='s08 bold p5 b1sb b1sr'>FIRST NAME</td>
                <td class='s08 bold p5 b1sb b1sr'>USERNAME</td>
                <td class='s08 bold p5 b1sb b1sr'>LEVEL</td>
                <td class='s08 bold p5 b1sb b1sr'>EDIT?</td>
                <td class='s08 bold p5 b1sb b1sr'>DELETE?</td>
              </tr>
<?

			$result = select_db('id, firstname, lastname, level, username', 'logins', sprintf('company_id = %s and deleted is null order by lastname, firstname, username', quote_smart($_SESSION['settings']['company_id'])));

			$rownum = 1;

			while ($result_array = mysql_fetch_array($result)) {

?>
              <tr class='bctr1<?=$rownum % 2 == 1 ? 'a' : 'b'?>'>
                <td class='b1sb b1sr s08 p5'><?=$rownum++?></td>
                <td class='b1sb b1sr s08 p5'><?=$result_array['lastname']?></td>
                <td class='b1sb b1sr s08 p5'><?=$result_array['firstname']?></td>
                <td class='b1sb b1sr s08 p5'><?=$result_array['username']?></td>
                <td class='b1sb b1sr s08 p5'><?=$GLOBALS['userlevels'][$result_array['level']]?></td>
                <td class='b1sb b1sr'><input class='button' type='submit' value='EDIT' onclick="Inventory_Items_SubmitNewItem(<?=$result_array['id']?>)"></td>
                <td class='b1sb b1sr'><? if ($_SESSION['settings']['login_id'] == $result_array['id']) { ?>-<? } else { ?><input class='button' type='button' value='DELETE' onclick='if (confirm("Do you really want to delete the user with the username &#39;<?=$result_array['username']?>&#39;?")) { document.getElementById("delete_user_id").value = <?=$result_array['id']?>; document.delete_user_form.submit(); }'/><? } ?></td>
              </tr>
<?

			}

?>
            </table>
            <div class='s1 bold mb10'>ADD PROFILE</div>
            <table class='mb20 b1st b1sl bcwhite'>
              <tr class='bctrt'>
                <td class='s08 bold p5 b1sb b1sr<?=isset($_SESSION['bad']['lastname']) ? ' red' : ''?>'>LAST NAME</td>
                <td class='s08 bold p5 b1sb b1sr<?=isset($_SESSION['bad']['firstname']) ? ' red' : ''?>'>FIRST NAME</td>
                <td class='s08 bold p5 b1sb b1sr<?=isset($_SESSION['bad']['username']) ? ' red' : ''?>'>USERNAME</td>
                <td class='s08 bold p5 b1sb b1sr<?=isset($_SESSION['bad']['level']) ? ' red' : ''?>'>LEVEL</td>
                <td class='s08 bold p5 b1sb b1sr<?=isset($_SESSION['bad']['password']) ? ' red' : ''?>'>PASSWORD</td>
                <td class='s08 bold p5 b1sb b1sr'>ADD?</td>
              </tr>
              <form method='post'>
                <tr class='bctr1a'>
                  <td class='b1sb b1sr'><input class='w100 text' type='text' name='new_lastname' value='<?=isset($_SESSION['edit']['user']['new']) ? $_SESSION['edit']['user']['new']['lastname'] : ''?>'/></td>
                  <td class='b1sb b1sr'><input class='w100 text' type='text' name='new_firstname' value='<?=isset($_SESSION['edit']['user']['new']) ? $_SESSION['edit']['user']['new']['firstname'] : ''?>'/></td>
                  <td class='b1sb b1sr'><input class='w100 text' type='text' name='new_username' value='<?=isset($_SESSION['edit']['user']['new']) ? $_SESSION['edit']['user']['new']['username'] : ''?>'/></td>
                  <td class='b1sb b1sr'>
                    <select class='w120' name='new_level'>
                      <option value='0'<?=isset($_SESSION['edit']['user']['new']) && $_SESSION['edit']['user']['new']['userlevel'] == 0 ? ' selected' : ''?>><?=$GLOBALS['userlevels'][0]?></option>
                      <option value='1'<?=isset($_SESSION['edit']['user']['new']) && $_SESSION['edit']['user']['new']['userlevel'] == 1 ? ' selected' : ''?>><?=$GLOBALS['userlevels'][1]?></option>
                      <option value='2'<?=isset($_SESSION['edit']['user']['new']) && $_SESSION['edit']['user']['new']['userlevel'] == 2 ? ' selected' : ''?>><?=$GLOBALS['userlevels'][2]?></option>
                    </select>
                  </td>
                  <td class='b1sb b1sr'><input class='w100 text' type='password' name='new_password' value='<?=isset($_SESSION['edit']['user']['new']) ? $_SESSION['edit']['user']['new']['password'] : ''?>'/></td>
                  <td class='b1sb b1sr'><input class='button' type='submit' value='ADD'/></td>
                </tr>
                <input type='hidden' name='add_user' value='1'/>
                <input type='hidden' name='user_management' value='1'/>
              </form>
            </table>
<?

		}
	} else {
			$result = select_db('firstname, lastname, level, username', 'logins', sprintf('company_id = %s and id = %s and deleted is null', quote_smart($_SESSION['settings']['company_id']), quote_smart($_SESSION['settings']['login_id'])));

		if ($result_array = mysql_fetch_array($result)) {

?>
            <div class='s09 mb30'>On this page you can review your profile information and change your password.<br/>First name, last name, user name and level can only be edited by an admin.</div>
            <div class='s1 bold mb15'>PROFILE INFORMATION</div>
            <table class='mb30'>
              <tr class='vmiddle'>
                <td class='s08 bold pr20 pb10 left'>FIRST NAME:</td><td class='s08 pb10 left'><?=$result_array['firstname']?></td>
              </tr>
              <tr class='vmiddle'>
                <td class='s08 bold pr20 pb10 left'>LAST NAME:</td><td class='s08 pb10 left'><?=$result_array['lastname']?></td>
              </tr>
              <tr class='vmiddle'>
                <td class='s08 bold pr20 pb10 left'>USERNAME:</td><td class='s08 pb10 left'><?=$result_array['username']?></td>
              </tr>
              <tr class='vmiddle'>
                <td class='s08 bold pr20 pb10 left'>USERLEVEL:</td><td class='s08 pb10 left'><?=$GLOBALS['userlevels'][$result_array['level']]?></td>
              </tr>
            </table>
            <form method='post'>
              <div class='s1 bold mb15'>UPDATE PASSWORD</div>
              <table class='mb30'>
                <tr class='vmiddle'>
                  <td class='left s08 bold pr10 pb10'>PASSWORD:</td>
                  <td class='left pb10'><input type='password' class='text w100' name='new_password' id='new_password'/></td>
                </tr><tr>
                  <td class='center' colspan='2'><input class='ml2 button' type='submit' value='UPDATE' onclick='if (!confirm("Do you really want to update your password?")) { return false; }'/></td>
                </tr>
              </table>
              <input type='hidden' name='update_my_password' value='1'/>
            </form>

<?

		}
	}
}
function preferencesPage() {

?>
            <form class='mb30' method='post'>
              <div class='s15 bold mb20 mt10'>PREFERENCES</div>
              <table class='p5 mb30'>

<?

	$result = select_db('name, value, options, type', 'preferences', sprintf('company_id = %s order by name', quote_smart($_SESSION['settings']['company_id'])));

	while ($result_array = mysql_fetch_array($result)) {
		$name = "preference_" . preg_replace('/ /', '_', $result_array['name']);

?>
                <tr class='vtop'>
                  <td class='detaillabel'><?=strtoupper($result_array['name'])?>:</td>
                  <td class='left'>
<?

		if (substr($result_array['type'], 0, 4) == "text") {
			$class = "w" . substr($result_array['type'], 4);

?>
                    <input class='text <?=$class?>' type='text' name='<?=$name?>' value='<?=replace_ticks($result_array['value'])?>'/>
<?

		}

		if ($result_array['type'] == "boolean") {

?>
                    <select class='s08 w80' name='<?=$name?>'>
<?

			if (trim($result_array['options']) == '') {

?>
                      <option value='0'>false</option>
                      <option value='1'<? if ($result_array['value'] == 1) { ?> selected<? } ?>>true</option>
<?

			} else {
				foreach (explode(',', $result_array['options']) as $option) {
					$values = explode('=', $option);
?>
                      <option value='<?=$values[1]?>'<?=$values[1] == $result_array['value'] ? ' selected' : ''?>><?=$values[0]?></option>
<?

				}
			}

?>
                    </select>
<?

		}

		if (substr($result_array['type'], 0, 4) == 'mbox') {
			$rows = substr($result_array['type'], 4);

?>
                    <textarea class='w300' name='<?=$name?>' rows='<?=$rows?>'><?=$result_array['value']?></textarea>
<?

		}

?>
                  </td>
                </tr>
<?

	}

?>
              </table>
              <input type='hidden' name='update_preferences' value='1'/>
              <input type='hidden' name='preferences_management' value='1'/>
              <input class='button' type='submit' value='UPDATE PREFERENCES'/>
            </form>
<?

}

function deliveriesPage() {
	if ($_SESSION['settings']['itemmgnt']['add_item'] == 1) {
		itemDetails(1);
		return;
	}
	if ($_SESSION['delivery']['done'] == 1 && !isset($_SESSION['settings']['itemmgnt']['delivery_id'])) {
		unset($_SESSION['delivery']);
	}
	if ($_SESSION['settings']['itemmgnt']['delivery_id'] > 1) {
		unset($_SESSION['delivery']);
		if ($result_array = mysql_fetch_array(select_db(sprintf('date_format(convert_tz(d.added, "utc", %s), %s) as added, coalesce(l2.username, l.username) as username, d.supplier_id, d.ordered, d.invoice_no, d.shipped, d.delivered_via, d.shipping_costs, d.received, d.receiver_id, d.purchase_order_no', quote_smart($_SESSION['preferences']['timezone']), "'%b %D, %Y @ %H:%i:%S'"), 'deliveries as d join logins as l on l.id = d.receiver_id left join logins as l2 on l2.id = d.login_id', sprintf('l.company_id = %s and d.id = %s and d.deleted is null', quote_smart($_SESSION['settings']['company_id']), quote_smart($_SESSION['settings']['itemmgnt']['delivery_id']))))) {
			$_SESSION['delivery']['added'] = $result_array['added'];
			$_SESSION['delivery']['username'] = $result_array['username'];
			$_SESSION['delivery']['supplier_id'] = $result_array['supplier_id'];
			$_SESSION['delivery']['ordered'] = $result_array['ordered'];
			$_SESSION['delivery']['invoice_no'] = $result_array['invoice_no'];
			$_SESSION['delivery']['sent'] = $result_array['shipped'];
			$_SESSION['delivery']['delivered_via'] = $result_array['delivered_via'];
			$_SESSION['delivery']['shipping_costs'] = $result_array['shipping_costs'];
			$_SESSION['delivery']['received'] = $result_array['received'];
			$_SESSION['delivery']['receiver_id'] = $result_array['receiver_id'];
			$_SESSION['delivery']['purchase_order_no'] = $result_array['purchase_order_no'];

			$result = select_db('i.id, concat(i.number, " - ", b.name, " - ", i.name, " - ", i.attribute1, " - ", i.attribute2) as info, di.buy_price, di.sell_price, di.quantity', 'delivery_items as di join items as i on i.id = di.item_id left join brands as b on b.id = i.brand_id', sprintf('di.delivery_id = %s', quote_smart($_SESSION['settings']['itemmgnt']['delivery_id'])));

			while ($result_array2 = mysql_fetch_array($result)) {
				$_SESSION['delivery']['items'][$result_array2['id']]['info'] = $result_array2['info'];
				$_SESSION['delivery']['items'][$result_array2['id']]['buy_price'] = $result_array2['buy_price'];
				$_SESSION['delivery']['items'][$result_array2['id']]['sell_price'] = $result_array2['sell_price'];
				$_SESSION['delivery']['items'][$result_array2['id']]['quantity'] = $result_array2['quantity'];
			}

			$_SESSION['delivery']['done'] = 1;
		}
	}

	if (isset($_SESSION['delivery']['supplier_id']) && !($result_array = mysql_fetch_array(select_db('id, name', 'suppliers', sprintf('company_id = %s and id = %s and deleted is null', quote_smart($_SESSION['settings']['company_id']), quote_smart($_SESSION['delivery']['supplier_id'])))))) {
		unset($_SESSION['delivery']);
	}

	if (!isset($_SESSION['delivery']['supplier_id'])) {

?>
          <div class='s1 bold mb10 mt10'>ADD NEW DELIVERY</div>
          <form class='mb30' method='post'>
            <select class='s08' name='delivery_supplier_id'>
              <option value='0'>- SELECT A SUPPLIER -</option>
<?

		$result = select_db('id, name', 'suppliers', sprintf('company_id = %s and deleted is null order by name', quote_smart($_SESSION['settings']['company_id'])));
		while ($result_array = mysql_fetch_array($result)) {
?>
              <option value='<?=$result_array['id']?>'><?=$result_array['name']?></option>
<?
		}

?>
            </select>
            <input class='button' type='submit' value='CREATE DELIVERY'/>
            <input type='hidden' name='delivery_management' value='1'/>
          </form>
          <div class='s1 mb10 bold'>RECENT DELIVERIES</div>
<?

		deliveryList('', 'd.added desc limit 10');

?>
          <form method='post' class='mb30' name='go_back_form'>
            <a href='javascript: none();' class='s08 bold' onclick='document.go_back_form.submit();'>GO BACK TO PREVIOUS PAGE</a>
            <input type='hidden' name='no_management_type' value='1'/>
          </form>
<?

	} else {
		if ($_SESSION['settings']['itemmgnt']['deliveries_item_id'] > 0 && isset($_SESSION['delivery']['items'][$_SESSION['settings']['itemmgnt']['deliveries_item_id']])) {

?>
            <div class='mb10 bold'>EDIT DELIVERY ITEM</div>
            <div class='mb20'>You are editing an item of a new delivery of the supplier '<?=$result_array['name']?>'.</div>
            <form name='delivery_form' method='post'>
              <table class='mb30'>
                <tr class='vmiddle'>
                  <td class='left s08 bold pr10<?=isset($_SESSION['bad']['item_id']) ? ' red' : ''?>'>ITEM:</td>
                  <td class='pt2 pb2'>
                    <select name='delivery_update_item_id' onchange="str = this.value > 0 ? '- loading -' : '-'; document.getElementById('ajax_quantity').innerHTML = document.getElementById('ajax_buy_price').innerHTML = document.getElementById('ajax_sell_price').innerHTML = str; if (this.value > 0) { sendRequest('getinfo.php?item_id=' + this.value + '&mode=delivery') };">
<?

			$result = select_db('i.id, concat(i.number, " - ", b.name, " - ", i.name, " - ", i.attribute1, " - ", i.attribute2) as info', 'items as i join suppliers as s on s.id = i.supplier_id join brands as b on i.brand_id = b.id', sprintf('s.id = %s and s.company_id = b.company_id and s.company_id = %s and coalesce(s.deleted, b.deleted, i.deleted) is null order by b.name asc, i.name asc, i.attribute1 asc, i.attribute2 asc', quote_smart($_SESSION['delivery']['supplier_id']), quote_smart($_SESSION['settings']['company_id'])));

			while ($result_array = mysql_fetch_array($result)) {
				if (!isset($_SESSION['delivery']['items'][$result_array['id']]) || $result_array['id'] == $_SESSION['settings']['itemmgnt']['deliveries_item_id']) {

?>
                      <option value='<?=$result_array['id']?>'<?=$result_array['id'] == $_SESSION['delivery']['update']['item_id'] ? ' selected' : ''?>><?=$result_array['info']?></option>
<?

				}
			}

			$result_array = mysql_fetch_array(select_db('quantity, buy_price, price as sell_price', 'items', sprintf('id = %s', $_SESSION['delivery']['update']['item_id'])));

?>
                    </select>
                  </td>
                </tr><tr class='vmiddle'>
                  <td class='left s08 bold pr10<?=isset($_SESSION['bad']['quantity']) ? ' red' : ''?>'>QUANTITY:</td>
                  <td class='left pt2 pb2'><input type='text' class='w50' name='delivery_update_quantity' value='<?=replace_ticks($_SESSION['delivery']['update']['quantity'])?>'/></td>
                </tr><tr class='vmiddle'>
                  <td class='left s08 bold pr10'?>CURRENT QUANTITY:</td>
                  <td class='left s08 pt2 pb2'><span id='ajax_quantity'><?=$result_array['quantity']?></span></td>
                </tr><tr class='vmiddle'>
                  <td class='left s08 bold pr10<?=isset($_SESSION['bad']['buy_price']) ? ' red' : ''?>'>BUY PRICE (<?=$_SESSION['preferences']['currency']?>):</td>
                  <td class='left pt2 pb2'><input type='text' class='w80' name='delivery_update_buy_price' value='<?=replace_ticks($_SESSION['delivery']['update']['buy_price'])?>'/></td>
                </tr><tr class='vmiddle'>
                  <td class='left s08 bold pr10'?>LAST BUY PRICE (<?=$_SESSION['preferences']['currency']?>):</td>
                  <td class='left s08 pt2 pb2'><span id='ajax_buy_price'><?=$result_array['buy_price']?></span></td>
                </tr><tr class='vmiddle'>
                  <td class='left s08 bold pr10<?=isset($_SESSION['bad']['sell_price']) ? ' red' : ''?>'>SELL PRICE (<?=$_SESSION['preferences']['currency']?>):</td>
                  <td class='left pt2 pb2'><input type='text' class='w80' name='delivery_update_sell_price' value='<?=replace_ticks($_SESSION['delivery']['update']['sell_price'])?>'/></td>
                </tr><tr class='vmiddle'>
                  <td class='left s08 bold pr10'?>CURRENT SELL PRICE (<?=$_SESSION['preferences']['currency']?>):</td>
                  <td class='left s08 pt2 pb2'><span id='ajax_sell_price'><?=$result_array['sell_price']?></span></td>
                </tr><tr>
                  <td colspan='2' class='center pt15'><input class='button' type='submit' value='UPDATE DELIVERY ITEM'/></td>
                </tr>
              </table>
              <input type='hidden' name='form_action' id='form_action' value='update_delivery_item'/>
              <input type='hidden' name='delivery_management' value='1'/>
              <a class='bold s08' href='javascript: none();' onclick='if (confirm("Do you really want to cancel the editing of this delivery item?")) { document.getElementById("form_action").value = "cancel_edit_delivery_item"; document.delivery_form.submit(); }'>CANCEL EDITING DELIVERY ITEM</a></form>
            </form>
<?

		} else {

?>
            <div class='mb20 s09'><?=isset($_SESSION['delivery']['done']) ? 'This is' : 'You are about to add'?> a delivery of the supplier '<?=$result_array['name']?>'<?=isset($_SESSION['delivery']['done']) ? ' added on ' . $_SESSION['delivery']['added'] . " by user '" . $_SESSION['delivery']['username'] . "'" : ''?>.</div>
            <div class='mb15 bold s1'>DELIVERY DETAILS</div>
<?

			if ($_SESSION['delivery']['done'] != 1) {

?>
            <form class='mb30' name='delivery_form' method='post'>
<?

			}

?>
              <table class='mb20'>
                <tr>
                  <td class='left bold s08 pr10 pb5<?=isset($_SESSION['bad']['ordered']) ? ' red' : ''?>'>ORDERED (DATE):</td>
                  <td class='left pr30<? if ($_SESSION['delivery']['done'] == 1) { ?> s08 pb5'><?=$_SESSION['delivery']['ordered']?><? } else { ?>'><input type='text' class='w150' name='delivery_ordered' value='<?=replace_ticks($_SESSION['delivery']['ordered'])?>'/><? } ?></td>
                  <td class='left bold s08 pr10 pb5<?=isset($_SESSION['bad']['invoice_no']) ? ' red' : ''?>'>INVOICE NO:</td>
                  <td class='left<? if ($_SESSION['delivery']['done'] == 1) { ?> s08 pb5'><?=$_SESSION['delivery']['invoice_no']?><? } else { ?>'><input type='text' class='w150' name='delivery_invoice_no' value='<?=replace_ticks($_SESSION['delivery']['invoice_no'])?>'/><? } ?></td>
                </tr>
                <tr>
                  <td class='left bold s08 pt10 pr10 pb5<?=isset($_SESSION['bad']['sent']) ? ' red' : ''?>'>SHIPPED (DATE):</td>
                  <td class='left pt10 pr30<? if ($_SESSION['delivery']['done'] == 1) { ?> s08 pb5'><?=$_SESSION['delivery']['sent']?><? } else { ?>'><input type='text' class='w150' name='delivery_sent' value='<?=replace_ticks($_SESSION['delivery']['sent'])?>'/><? } ?></td>
                  <td class='left bold s08 pt10 pr10 pb5<?=isset($_SESSION['bad']['delivered_via']) ? ' red' : ''?>'>DELIVERED VIA:</td>
                  <td class='left pt10 <? if ($_SESSION['delivery']['done'] == 1) { ?> s08 pb5'><?=$_SESSION['delivery']['delivered_via']?><? } else { ?>'><input type='text' class='w150' name='delivery_delivered_via' value='<?=replace_ticks($_SESSION['delivery']['delivered_via'])?>'/><? } ?></td>
                </tr>
                <tr>
                  <td colspan='2'></td>
                  <td class='left bold s08 pr10 pb5<?=isset($_SESSION['bad']['shipping_costs']) ? ' red' : ''?>'>SHIPPING COSTS<?=isset($_SESSION['delivery']['done']) ? '' : ' (' . $_SESSION['preferences']['currency'] . ')'?>:</td>
                  <td class='left<? if ($_SESSION['delivery']['done'] == 1) { ?> s08 pb5'><?=money($_SESSION['delivery']['shipping_costs'])?><? } else { ?>'><input type='text' class='w150' name='delivery_shipping_costs' value='<?=replace_ticks($_SESSION['delivery']['shipping_costs'])?>'/><? } ?></td>
                </tr>
                <tr>
                  <td class='left bold s08 pt10 pr10 pb5<?=isset($_SESSION['bad']['received']) ? ' red' : ''?>'>RECEIVED (DATE):</td>
                  <td class='left pt10 pr30<? if ($_SESSION['delivery']['done'] == 1) { ?> s08 pb5'><?=$_SESSION['delivery']['received']?><? } else { ?>'><input type='text' class='w150' name='delivery_received' value='<?=replace_ticks($_SESSION['delivery']['received'])?>'/><? } ?></td>
                  <td class='left bold s08 pt10 pr10 pb5<?=isset($_SESSION['bad']['receiver_id']) ? ' red' : ''?>'>RECEIVED BY:</td>
                  <td class='left pt10 <? if ($_SESSION['delivery']['done'] == 1) { ?> s08 pb5'><?=($result_array = mysql_fetch_array(select_db('username', 'logins', sprintf('id = %s and company_id = %s and deleted is null', quote_smart($_SESSION['delivery']['receiver_id']), quote_smart($_SESSION['settings']['company_id']))))) ? $result_array['username'] : 'abc'?><? } else { ?>'>
                    <select name='delivery_receiver_id'>
                      <option value=''>- please select -</option>
<?

			$result = select_db('id, username', 'logins', sprintf('company_id = %s and deleted is null', quote_smart($_SESSION['settings']['company_id'])));
			while ($result_array = mysql_fetch_array($result)) {

?>
                      <option value='<?=$result_array['id']?>'<?=$result_array['id'] == $_SESSION['delivery']['receiver_id'] ? ' selected' : ''?>><?=$result_array['username']?></option>
<?

			}

?>
                    </select>
                  <? } ?></td>
                </tr>
                <tr>
                  <td colspan='2' class='left bold s07 orange'><? if ($_SESSION['delivery']['done'] != 1) { ?>NOTICE: date format is 'YYYY-MM-DD'<? } ?></td>
                  <td class='left bold s08 pr10<?=isset($_SESSION['bad']['purchase_order_no']) ? ' red' : ''?>'>PURCHASE ORDER NO:</td>
                  <td class='left<? if ($_SESSION['delivery']['done'] == 1) { ?> s08 pb5'><?=$_SESSION['delivery']['purchase_order_no']?><? } else { ?><?=isset($_SESSION['bad']['purchase_order_no']) ? ' red' : ''?>'><input type='text' class='w150' name='delivery_purchase_order_no' value='<?=replace_ticks($_SESSION['delivery']['purchase_order_no'])?>'/><? } ?></td>
                </tr>
              </table>
            <div class='mb10 bold s1'>DELIVERY ITEMS</div>
<?

			$itemno = 0;
			if (is_array($_SESSION['delivery']['items'])) {
				foreach (array_keys($_SESSION['delivery']['items']) as $item) {
					$itemno++;
				}
			}

			if ($itemno == 0) {

?>
            <div class='mb30 s09'>There was no item added <?=$_SESSION['delivery']['done'] == 1 ? '' : 'yet '?>for this delivery.</div>
<?

			} else {

?>
            <table class='mb30'?>
              <tr>
                <td width='400' class='bctrt bold s08 p5 b1sl b1st b1sr b1sb'>ITEM</td>
                <td width='30' class='bctrt bold s08 p5 b1st b1sr b1sb'>QTY</td>
                <td width='50' class='bctrt bold s08 p5 b1st b1sr b1sb'>BUY<br/>PRICE</td>
                <td width='50' class='bctrt bold s08 p5 b1st b1sr b1sb'>TOTAL<br/>PRICE</td>
                <td width='80' class='bctrt bold s08 p5 b1st b1sr b1sb'>NEW SELL<br/>PRICE</td>
<?

				if ($_SESSION['delivery']['done'] != 1) {

?>
                <td width='30' class='bctrt bold s08 p5 b1st b1sr b1sb'>EDIT?</td>
                <td width='30' class='bctrt bold s08 p5 b1st b1sr b1sb'>DELETE?</td>
<?

				} else {

?>
                <td width='30' class='bctrt bold s08 p5 b1st b1sr b1sb'>LABEL?</td>
<?

				}

?>
              </tr>
<?

				$total = $count = $totalcount = 0;

				foreach (array_keys($_SESSION['delivery']['items']) as $id) {
					$bcclass = 'bctr1' . ($count++ % 2 == 1 ? 'a' : 'b');
					$total += $_SESSION['delivery']['items'][$id]['buy_price'] * $_SESSION['delivery']['items'][$id]['quantity'];
					$totalcount += $_SESSION['delivery']['items'][$id]['quantity'];

?>
              <tr>
                <td class='<?=$bcclass?> s08 p5 b1sl b1sr b1sb'><?=$_SESSION['delivery']['items'][$id]['info']?></td>
                <td class='<?=$bcclass?> s08 p5 b1sr b1sb'><?=$_SESSION['delivery']['items'][$id]['quantity']?></td>
                <td class='<?=$bcclass?> s08 p5 b1sr b1sb'><?=money($_SESSION['delivery']['items'][$id]['buy_price'])?></td>
                <td class='<?=$bcclass?> s08 p5 b1sr b1sb'><?=money($_SESSION['delivery']['items'][$id]['buy_price'] * $_SESSION['delivery']['items'][$id]['quantity'])?></td>
                <td class='<?=$bcclass?> s08 p5 b1sr b1sb'><?=money($_SESSION['delivery']['items'][$id]['sell_price'])?></td>
<?

					if ($_SESSION['delivery']['done'] != 1) {

?>
                <td class='<?=$bcclass?> b1sr b1sb'><input class='button' type='button' value='EDIT' onclick='document.getElementById("delivery_item_id").value = <?=$id?>; document.getElementById("form_action").value = "edit_delivery_item"; document.delivery_form.submit();'/></td>
                <td class='<?=$bcclass?> b1sr b1sb'><input class='button' type='button' value='DELETE' onclick='if (confirm("Do you really want to delete this delivery item?")) { document.getElementById("delivery_item_id").value = <?=$id?>; document.getElementById("form_action").value = "delete_delivery_item"; document.delivery_form.submit(); }'/></td>
<?

					} else {

?>
                <td class='<?=$bcclass?> b1sr b1sb'><input class='button' type='button' value='LABEL<?=$_SESSION['delivery']['items'][$id]['quantity'] > 1 ? 'S' : ''?>' onclick='label(<?=$id?>, <?=$_SESSION['preferences']['label_width']?>, <?=$_SESSION['delivery']['items'][$id]['quantity']?>);'/></td>
<?

					}

?>
              </tr>
<?

				}

?>
              <tr>
                <td colspan='6'>
                  <table class='pt20'>
                    <tr>
                      <td class='s08 left bold pr10 pb5'>TOTAL ITEM COUNT OF THIS DELIVERY:</td><td class='s08 right pb5'><?=$totalcount?></td>
                    </tr><tr>
                      <td class='s08 left bold pr10 pb5'>TOTAL PRICE OF ALL ITEMS:</td><td class='s08 right pb5'><?=money($total)?></td>
                    </tr><tr>
                      <td class='s08 left bold pr10'>TOTAL PRICE OF THIS DELIVERY:</td><td class='s08 right'><?=money($total + $_SESSION['delivery']['shipping_costs'])?></td>
                    </tr>
                  </table>
                </td>
              </tr>
            </table>
<?

			}

			if ($_SESSION['delivery']['done'] != 1) {

?>
            <div class='mb10 bold s1'>ADD DELIVERY ITEM</div>
<?

				if (isset($result2)) {
					unset($result2);
				}

				if (isset($_SESSION['delivery']['item_number'])) {
					$result2 = select_db('i.id, concat(i.number, " - ", b.name, " - ", i.name, " - ", i.attribute1, " - ", i.attribute2) as info', 'items as i join suppliers as s on s.id = i.supplier_id join brands as b on i.brand_id = b.id', sprintf('s.id = %s and s.company_id = b.company_id and s.company_id = %s and coalesce(s.deleted, b.deleted, i.deleted) is null and i.number = %s order by i.number asc, b.name asc, i.name asc, i.attribute1 asc, i.attribute2 asc', quote_smart($_SESSION['delivery']['supplier_id']), quote_smart($_SESSION['settings']['company_id']), quote_smart($_SESSION['delivery']['item_number'])));
				}

				$result = select_db('i.id, concat(i.number, " - ", b.name, " - ", i.name, " - ", i.attribute1, " - ", i.attribute2) as info', 'items as i join suppliers as s on s.id = i.supplier_id join brands as b on i.brand_id = b.id', sprintf('s.id = %s and s.company_id = b.company_id and s.company_id = %s and coalesce(s.deleted, b.deleted, i.deleted) is null order by i.number asc, b.name asc, i.name asc, i.attribute1 asc, i.attribute2 asc', quote_smart($_SESSION['delivery']['supplier_id']), quote_smart($_SESSION['settings']['company_id'])));

				if (mysql_num_rows($result) == 0) {

?>
            <div class='mb20'>There is no item in the database yet for this supplier.</div>
<?

				} else {
					$count = 0;
					$select_array = array();

					while ($count == 0) {
						while ($result_array = mysql_fetch_array(isset($result2) ? $result2 : $result)) {
							if (!isset($_SESSION['delivery']['items'][$result_array['id']])) {
								$count++;
								$select_array[$result_array['id']]['info'] = $result_array['info'];
							}
						}

						if ($count == 0) {
							if (isset($result2)) {
								unset($result2);
								unset($_SESSION['delivery']['item_number']);
							} else {
								$count = -1;

?>
            <div class='s09 mb20'>All items of this supplier were added to the delivery already.</div>
<?

							}
						}
					}

					if ($count > 0) {

?>
              <table class='mb20'>
                <tr class='vtop'>
                  <td class='s08 bold left p5 pb10'>STYLE NUMBER:</td>
                  <td class='left pb10'>
                    <select id='show_number' onchange='update_delivery_items(this);'>
                      <option value=''>- all style numbers -</option>
<?

						$result = select_db('i.number, i.id', 'items as i join categories as c on c.id = i.category_id', sprintf('c.company_id = %s and coalesce(i.deleted, c.deleted) is null and i.supplier_id = %s group by i.number order by i.number asc', quote_smart($_SESSION['settings']['company_id']), quote_smart($_SESSION['delivery']['supplier_id'])));

						while ($result_array = mysql_fetch_array($result)) {

?>
                      <option value='<?=$result_array['id']?>'<?=isset($_SESSION['delivery']['item_number']) && $_SESSION['delivery']['item_number'] == $result_array['number'] ? ' selected' : ''?>><?=$result_array['number']?></option>
<?

						}

?>
                    </select>
                  </td>
                </tr><tr class='vtop'>
                  <td class='s08 bold left p5<?=isset($_SESSION['bad']['item_id']) ? ' red' : ''?>'>ITEM:</td>
                  <td class='left'>
                    <select id='ajax_delivery_item_select' name='delivery_add_item_id' onchange="str = this.value > 0 ? '- loading -' : '-'; document.getElementById('ajax_quantity').innerHTML = document.getElementById('ajax_buy_price').innerHTML = document.getElementById('ajax_sell_price').innerHTML = str; if (this.value > 0) { sendRequest('getinfo.php?item_id=' + this.value + '&mode=delivery') };">
                      <option value='0'>- please select -</option>
<?

						foreach (array_keys($select_array) as $id) {

?>
                      <option value='<?=$id?>'<? if (isset($_SESSION['delivery']['add']['item_id']) && $_SESSION['delivery']['add']['item_id'] == $id) { print ' selected'; $item_info = mysql_fetch_array(select_db('quantity, price, buy_price', 'items', sprintf('id = %s', quote_smart($id)))); } ?>><?=$select_array[$id]['info']?></option>
<?

						}

?>
                    </select>
                  </td>
                </tr>
                <tr class='vtop'>
                  <td class='s08 bold left p5<?=isset($_SESSION['bad']['quantity']) ? ' red' : ''?>'>DELIVERY QUANTITY:</td>
                  <td class='left'><input class='text w80' type='text' name='delivery_add_quantity' value='<?=isset($_SESSION['delivery']['add']['quantity']) ? $_SESSION['delivery']['add']['quantity'] : ''?>'/></td>
                </tr>
                <tr class='vtop'>
                  <td class='s08 bold left p5'>CURRENT QUANTITY:</td>
                  <td class='s08 left p5'><span id='ajax_quantity'><?=isset($item_info) ? $item_info['quantity'] : '-'?></span></td>
                </tr>
                <tr class='vtop'>
                  <td class='s08 bold left p5<?=isset($_SESSION['bad']['buy_price']) ? ' red' : ''?>'>DELIVERY BUY PRICE (<?=$_SESSION['preferences']['currency']?>):</td>
                  <td class='left'><input class='text w80' type='text' name='delivery_add_buy_price' value='<?=isset($_SESSION['delivery']['add']['buy_price']) ? $_SESSION['delivery']['add']['buy_price'] : ''?>'/></td>
                </tr>
                <tr class='vtop'>
                  <td class='s08 bold left p5'>LAST BUY PRICE (<?=$_SESSION['preferences']['currency']?>):</td>
                  <td class='s08 left p5'><span id='ajax_buy_price'><?=isset($item_info) ? $item_info['buy_price'] : '-'?></span><td>
                </tr>
                <tr class='vtop'>
                  <td class='s08 bold left p5<?=isset($_SESSION['bad']['sell_price']) ? ' red' : ''?>'>NEW SELL PRICE (<?=$_SESSION['preferences']['currency']?>):</td>
                  <td class='left'><input class='text w80' type='text' name='delivery_add_sell_price' value='<?=isset($_SESSION['delivery']['add']['sell_price']) ? $_SESSION['delivery']['add']['price'] : ''?>'/></td>
                </tr>
                <tr class='vtop'>
                  <td class='s08 bold left p5'>CURRENT SELL PRICE (<?=$_SESSION['preferences']['currency']?>):</td>
                  <td class='s08 left p5'><span id='ajax_sell_price'><?=isset($item_info) ? $item_info['sell_price'] : '-'?></span></td>
                </tr>
              </table>
              <div class='mb30' colspan='2'><input class='button' type='button' onclick='document.getElementById("form_action").value = "add_delivery_item"; document.delivery_form.submit();' value='ADD DELIVERY ITEM'/></div>
<?

					}
				}

?>
              <div class='mb10 bold s1'>ADD ITEM</div>
              <div class='mb10 s09'>If you need to add a new item to the inventory, then click the following button:</div>
              <div class='mb30'><input type='button' class='button' onclick='document.getElementById("form_action").value = "add_item"; document.delivery_form.submit();' value='ADD ITEM'></div>
              <div class='mb10 bold s1'>FINISH DELIVERY</div>
              <div class='mb30'><input type='button' class='button<?=$itemno == 0 ? 'disabled' : ''?>' onclick='document.getElementById("form_action").value = "add_delivery"; document.delivery_form.submit();' value='ADD DELIVERY'<?=$itemno == 0 ? ' disabled' : ''?>></div>
              <input type='hidden' name='form_action' id='form_action' value=''/>
              <input type='hidden' name='delivery_item_id' id='delivery_item_id' value='0'/>
              <input type='hidden' name='delivery_management' value='1'/>
            </form>
<?

			}

?>
            <form class='mb30' method='post' name='deliverycancelform'><a class='bold s08' href='javascript: none();' onclick='<?=$_SESSION['delivery']['done'] == 1 ? '' : 'if (confirm("Do you really want to cancel this delivery?")) { '?>document.deliverycancelform.submit();<?=$_SESSION['delivery']['done'] == 1 ? '' : ' }' ?>'><?=$_SESSION['delivery']['done'] == 1 ? 'GO BACK TO PREVIOUS PAGE' : 'CANCEL DELIVERY'?></a><input type='hidden' name='cancel_delivery' value='1'/><input type='hidden' name='delivery_management' value='1'/></form>
<?

		}
	}
}
function itemMgntPage() {

?>
          <div class='s15 bold mb20 mt10'>ITEM MANAGEMENT</div>
<?

	switch ($_SESSION['settings']['itemmgnt']['type']) {
		case 'suppliers' : suppliersPage(); break;
		case 'brands' : brandsPage(); break;
		case 'departments' : departmentsPage(); break;
		case 'categories' : itemcategoriesPage(); break;
		case 'taxgroups' : taxgroupsPage(); break;
		case 'items' : itemsPage(); break;
		case 'deliveries' : deliveriesPage(); break;
		default :

?>
          <div class='s09 mb20'>Click on the type you want to manage:</div>
          <div class='mb10'><a class='bold' href='javascript: none();' onclick="document.getElementById('post_values').value = 'item_management=1|show_type=suppliers'; document.page_form.submit();">SUPPLIERS</a></div>
          <div class='mb10'><a class='bold' href='javascript: none();' onclick="document.getElementById('post_values').value = 'item_management=1|show_type=brands'; document.page_form.submit();">BRANDS</a></div>
          <div class='mb10'><a class='bold' href='javascript: none();' onclick="document.getElementById('post_values').value = 'item_management=1|show_type=departments'; document.page_form.submit();">DEPARTMENTS</a></div>
          <div class='mb10'><a class='bold' href='javascript: none();' onclick="document.getElementById('post_values').value = 'item_management=1|show_type=categories'; document.page_form.submit();">ITEM CATEGORIES</a></div>
          <div class='mb10'><a class='bold' href='javascript: none();' onclick="document.getElementById('post_values').value = 'item_management=1|show_type=taxgroups'; document.page_form.submit();">TAX GROUPS</a></div>
          <div class='mb10'><a class='bold' href='javascript: none();' onclick="document.getElementById('post_values').value = 'item_management=1|show_type=items'; document.page_form.submit();">ITEMS</a></div>
          <div class='mb10'><a class='bold' href='javascript: none();' onclick="mainDiv('Deliveries')">DELIVERIES</a>&nbsp;<a href='javascript: none();' onclick="mainDiv('Deliveries')">2</a></div>
<?

	}
}
function suppliersPage() {
	if ($_SESSION['settings']['itemmgnt']['sid'] > 0) {
		$result = select_db('id, name, address, contact, phone, email', 'suppliers', sprintf('id = %s and deleted is null', quote_smart($_SESSION['settings']['itemmgnt']['sid'])));
		if ($result_array = mysql_fetch_array($result)) {

?>
            <div class='bold mb20 s08'>NOTE: All items that belong to this supplier will continue to belong to it.<br/>So only use the edit option for updating supplier details,<br/>not for changing it to a different supplier.</div>
            <form method='post'>
              <table class='mb30'>
                <tr class='vtop'>
                  <td class='p5 left s08 bold'>SUPPLIER NAME:</td>
                  <td class='left pl10'><input class='w200 text' type='text' id='addinput' name='new_supplier_name' value='<?=replace_ticks(isset($_SESSION['wronginput']['supplier']) ? $_SESSION['wronginput']['supplier']['new_supplier_name'] : $result_array['name'])?>'/></td>
                  <td class='p5 left pl30 s08 bold'>CONTACT PERSON:</td>
                  <td class='left pl10'><input class='w200 text' type='text' name='new_contact' value='<?=replace_ticks(isset($_SESSION['wronginput']['supplier']) ? $_SESSION['wronginput']['supplier']['new_contact'] : $result_array['contact'])?>'/></td>
                </tr><tr class='vtop'>
                  <td class='p5 left s08 bold' rowspan='2'>ADDRESS:</td>
                  <td class='left pl10' rowspan='2'><textarea class='w200' cols='4' name='new_address'><?=isset($_SESSION['wronginput']['supplier']) ? $_SESSION['wronginput']['supplier']['new_address'] : $result_array['address']?></textarea></td>
                  <td class='p5 left pl30 s08 bold'>PHONE:</td>
                  <td class='left pl10'><input class='w200 text' type='text' name='new_phone' value='<?=replace_ticks(isset($_SESSION['wronginput']['supplier']) ? $_SESSION['wronginput']['supplier']['new_phone'] : $result_array['phone'])?>'/></td>
                </tr><tr class='vtop'>
                  <td class='p5 left pl30 s08 bold'>EMAIL:</td>
                  <td class='left pl10'><input class='w200 text' type='text' name='new_email' value='<?=replace_ticks(isset($_SESSION['wronginput']['supplier']) ? $_SESSION['wronginput']['supplier']['new_email'] : $result_array['email'])?>'/></td>
                </tr><tr class='vtop'>
                  <td class='center pt10' colspan='4'><input class='button' type='submit' value='UPDATE SUPPLIER'/></td>
                </tr>
              </table>
              <input type='hidden' name='update_supplier_id' value='<?=$result_array['id']?>'/>
              <input type='hidden' name='supplier_management' value='1'/>
            </form>
<?

	} else {

?>
            <div class='mb30 s08'>Supplier was not found.</div>
<?

	}

?>
            <form method='post' class='mb30' name='go_back_form'>
              <a href='javascript: none();' class='s08 bold' onclick='document.go_back_form.submit();'>GO BACK TO PREVIOUS PAGE</a>
              <input type='hidden' name='edit_supplier_id' value='0'/>
              <input type='hidden' name='supplier_management' value='1'/>
            </form>
<?

	} else {

?>
            <div class='mb20 s09'><a href='#addnew' onclick='document.getElementById("addinput").focus();'>CLICK HERE</a> to scroll down to the bottom of this page for adding new suppliers.</div>
            <div class='bold mb10'>SUPPLIERS</div>
<?

	if (!isset($_SESSION['ic_list_start'])) { $_SESSION['ic_list_start'] = 1; }

	$result = select_db('s.id, s.name, s.phone, s.contact, s.email, count(i.id) as items', 'suppliers as s left join items as i on (i.supplier_id = s.id and i.deleted is null)', sprintf('s.company_id = %s and s.deleted is null group by s.id order by s.name', quote_smart($_SESSION['settings']['company_id'])));

	if (mysql_num_rows($result) == 0) {

?>
            <div class='mb30'>No suppliers exists currently.</div>
<?

	} else {

?>
            <table class='mb30 bcwhite b1sl b1st'>
              <tr class='vtop bctrt'>
                <td class='bold s08 p5 b1sr b1sb'>#</td>
                <td class='bold s08 p5 b1sr b1sb'>SUPPLIER NAME</td>
                <td class='bold s08 p5 b1sr b1sb'>CONTACT PERSON</td>
                <td class='bold s08 p5 b1sr b1sb'>PHONE NUMBER</td>
                <td class='bold s08 p5 b1sr b1sb'>EMAIL ADDRESS</td>
                <td class='bold s08 p5 b1sr b1sb'>EDIT?</td>
                <td class='bold s08 p5 b1sr b1sb'>DELETE?</td>
              </tr>
<?

		$rowid = 0;

		while ($result_array = mysql_fetch_array($result)) {

?>
              <tr class='bctr1<?=$rowid++ % 2 == 0 ? 'a' : 'b'?>'>
                <td class='s08 p1 b1sr b1sb pl5 pr5'><?=$rowid?></td>
                <td class='s08 b1sr b1sb'><?=$result_array['name']?></td>
                <td class='s08 b1sr b1sb'><?=$result_array['contact']?></td>
                <td class='s08 b1sr b1sb'><?=$result_array['phone']?></td>
                <td class='s08 b1sr b1sb'><?=$result_array['email']?></td>
                <td class='b1sr b1sb'><input class='button' type='button' value='EDIT' onclick='document.getElementById("edit_supplier_id").value = <?=$result_array['id']?>; document.editsupplierform.submit();'/></td>
                <td class='b1sr b1sb'><input class='button' type='button' onclick='if (<?=$result_array['items']?> > 0) { alert("This supplier cannot be deleted since there are existing items that belong to it!"); } else { if (!confirm("Do you really want to delete this supplier?")) { return false; }; document.getElementById("delete_supplier_id").value = <?=$result_array['id']?>; document.deletesupplierform.submit(); }' value='DELETE'/></td>
              </tr>
<?

		}

?>
            </table>
            <form method='post' name='editsupplierform'>
              <input type='hidden' name='edit_supplier_id' id='edit_supplier_id' value='0'/>
              <input type='hidden' name='supplier_management' value='1'/>
            </form>
            <form method='post' name='deletesupplierform'>
              <input type='hidden' name='delete_supplier_id' id='delete_supplier_id' value='0'/>
              <input type='hidden' name='supplier_management' value='1'/>
            </form>
<?

	}

?>
            <a name='addnew'></a><div class='bold mb10'>ADD SUPPLIER</div>
            <form method='post'>
              <table class='mb30'>
                <tr class='vtop'>
                  <td class='p5 left s08 bold'>SUPPLIER NAME:</td>
                  <td class='left pl10'><input class='w200 text' type='text' id='addinput' name='new_supplier_name' value='<?=replace_ticks($_SESSION['wronginput']['supplier']['new_supplier_name'])?>'/></td>
                  <td class='p5 left pl30 s08 bold'>CONTACT PERSON:</td>
                  <td class='left pl10'><input class='w200 text' type='text' name='new_contact' value='<?=replace_ticks($_SESSION['wronginput']['supplier']['new_contact'])?>'/></td>
                </tr><tr class='vtop'>
                  <td class='p5 left s08 bold' rowspan='2'>ADDRESS:</td>
                  <td class='left pl10' rowspan='2'><textarea class='w200' cols='4' name='new_address'><?=$_SESSION['wronginput']['supplier']['new_address']?></textarea></td>
                  <td class='p5 left pl30 s08 bold'>PHONE:</td>
                  <td class='left pl10'><input class='w200 text' type='text' name='new_phone' value='<?=replace_ticks($_SESSION['wronginput']['supplier']['new_phone'])?>'/></td>
                </tr><tr class='vtop'>
                  <td class='p5 left pl30 s08 bold'>EMAIL:</td>
                  <td class='left pl10'><input class='w200 text' type='text' name='new_email' value='<?=replace_ticks($_SESSION['wronginput']['supplier']['new_email'])?>'/></td>
                </tr><tr class='vtop'>
                  <td class='center pt10' colspan='4'><input class='button' type='submit' value='ADD SUPPLIER'/></td>
                </tr>
              </table>
              <input type='hidden' name='supplier_management' value='1'/>
            </form>
            <form method='post' class='mb30' name='go_back_form'>
              <a href='javascript: none();' class='s08 bold' onclick='document.go_back_form.submit();'>GO BACK TO PREVIOUS PAGE</a>
              <input type='hidden' name='no_management_type' value='1'/>
            </form>
<?

	}
}
function brandsPage() {

?>
            <div class='mb20 s09'><a href='#addnew' onclick='document.getElementById("addinput").focus();'>CLICK HERE</a> to scroll down to the bottom of this page for adding new brands.</div>
            <div class='bold mb10'>BRANDS</div>
<?

	if (!isset($_SESSION['ib_list_start'])) { $_SESSION['ib_list_start'] = 1; }

	$result = select_db('b.id, b.name, count(i.id) as items' , 'brands as b left join items as i on (i.brand_id = b.id and i.deleted is null)', sprintf('b.company_id = %s and b.deleted is null group by b.id order by b.name', quote_smart($_SESSION['settings']['company_id'])));

	if (mysql_num_rows($result) == 0) {

?>
            <div class='mb30'>No brands exists currently.</div>
<?

	} else {

?>
            <table class='mb30 bcwhite b1sl b1st'>
              <tr class='vtop bctrt'>
                <td class='bold s08 p5 b1sr b1sb'>#</td>
                <td class='bold s08 p5 b1sr b1sb'>BRAND NAME</td>
<?

			if ($_SESSION['settings']['manager'] == 1) {

?>
                <td class='bold s08 p5 b1sr b1sb'>UPDATE?</td>
                <td class='bold s08 p5 b1sr b1sb'>DELETE?</td>
<?

			}

?>
              </tr>
<?

		$rowid = 0;

		while ($result_array = mysql_fetch_array($result)) {

?>
              <tr class='bctr1<?=$rowid++ % 2 == 0 ? 'a' : 'b'?>'>
                <form method='post'>
                  <td class='s08 p1 b1sr b1sb pl5 pr5'><?=$rowid?></td>
                  <td class='p1 b1sr b1sb'><input class='w200 text' type='text' name='new_brand_name' value='<?=replace_ticks($result_array['name'])?>'/>
<?

			if ($_SESSION['settings']['manager'] == 1) {

?>
                  <td class='p1 b1sr b1sb'><input class='button' type='submit' value='UPDATE'/></td>
                  <td class='p1 b1sr b1sb'><input class='button' type='button' onclick='if (<?=$result_array['items']?> > 0) { alert("This brand cannot be deleted since there are existing items that belong to it!"); } else { if (!confirm("Do you really want to delete this brand?")) { return false; }; document.getElementById("delete_brand_id").value = <?=$result_array['id']?>; document.deletebrandform.submit(); }' value='DELETE'/></td>
<?

			}

?>
                  <input type='hidden' name='update_brand_id' value='<?=$result_array['id']?>'/>
                  <input type='hidden' name='brand_management' value='1'/>
                </form>
              </tr>
<?

		}

?>
            </table>
            <form method='post' name='deletebrandform'>
              <input type='hidden' name='delete_brand_id' id='delete_brand_id' value='0'/>
              <input type='hidden' name='brand_management' value='1'/>
            </form>
<?

	}

?>
            <a name='addnew'></a><div class='bold mb10'>ADD BRAND</div>
            <form method='post'>
              <table class='mb30 bcwhite b1sl b1st'>
                <tr class='vtop'>
                  <td class='left b1sr b1sb'><input class='w200 text cleardefault' type='text' id='addinput' name='new_brand_name' value='brand name'/></td>
                  <td class='left b1sr b1sb'><input class='button' type='submit' value='ADD'/></td>
                </tr>
              </table>
              <input type='hidden' name='brand_management' value='1'/>
            </form>
            <form method='post' class='mb30' name='go_back_form'>
              <a href='javascript: none();' class='s08 bold' onclick='document.go_back_form.submit();'>GO BACK TO PREVIOUS PAGE</a>
              <input type='hidden' name='no_management_type' value='1'/>
            </form>
<?

}
function departmentsPage() {

?>
          <div class='mb20 s09'><a href='#addnew' onclick='document.getElementById("addinput").focus();'>CLICK HERE</a> to scroll down to the bottom of this page for adding new departments.</div>
          <div class='bold mb10'>DEPARTMENTS</div>
<?

	if (!isset($_SESSION['ib_list_start'])) { $_SESSION['ib_list_start'] = 1; }

	$result = select_db('d.id, d.name, d.location, d.contact, d.phone, count(i.id) as items' , 'departments as d left join items as i on (i.department_id = d.id and i.deleted is null)', sprintf('d.company_id = %s and d.deleted is null group by d.id order by d.name', quote_smart($_SESSION['settings']['company_id'])));

	if (mysql_num_rows($result) == 0) {

?>
          <div class='mb30'>No department exists currently.</div>
<?

	} else {

?>
          <table class='mb30 bcwhite b1sl b1st'>
            <tr class='vtop bctrt'>
              <td class='bold s08 p5 b1sr b1sb'>#</td>
              <td class='bold s08 p5 b1sr b1sb'>DEPARTMENT NAME</td>
              <td class='bold s08 p5 b1sr b1sb'>LOCATION</td>
              <td class='bold s08 p5 b1sr b1sb'>CONTACT PERSON</td>
              <td class='bold s08 p5 b1sr b1sb'>PHONE NO</td>
<?

			if ($_SESSION['settings']['manager'] == 1) {

?>
              <td class='bold s08 p5 b1sr b1sb'>UPDATE?</td>
              <td class='bold s08 p5 b1sr b1sb'>DELETE?</td>
<?

			}

?>
            </tr>
<?

		$rowid = 0;

		while ($result_array = mysql_fetch_array($result)) {

?>
            <tr class='bctr1<?=$rowid++ % 2 == 0 ? 'a' : 'b'?>'>
              <form method='post'>
                <td class='s08 p1 b1sr b1sb pl5 pr5'><?=$rowid?></td>
                <td class='p1 b1sr b1sb'><input class='w150 text' type='text' name='new_name' value='<?=replace_ticks($result_array['name'])?>'/>
                <td class='p1 b1sr b1sb'><input class='w100 text' type='text' name='new_location' value='<?=replace_ticks($result_array['location'])?>'/>
                <td class='p1 b1sr b1sb'><input class='w150 text' type='text' name='new_contact' value='<?=replace_ticks($result_array['contact'])?>'/>
                <td class='p1 b1sr b1sb'><input class='w120 text' type='text' name='new_phone' value='<?=replace_ticks($result_array['phone'])?>'/>
<?

			if ($_SESSION['settings']['manager'] == 1) {

?>
                <td class='p1 b1sr b1sb'><input class='button' type='submit' value='UPDATE'/></td>
                <td class='p1 b1sr b1sb'><input class='button' type='button' onclick='if (<?=$result_array['items']?> > 0) { alert("This department cannot be deleted since there are existing items that belong to it!"); } else { if (!confirm("Do you really want to delete this department?")) { return false; }; document.getElementById("delete_department_id").value = <?=$result_array['id']?>; document.delete_department_form.submit(); }' value='DELETE'/></td>
<?

			}

?>
                <input type='hidden' name='update_department_id' value='<?=$result_array['id']?>'/>
                <input type='hidden' name='department_management' value='1'/>
              </form>
            </tr>
<?

		}

?>
          </table>
          <form method='post' name='delete_department_form'>
            <input type='hidden' name='delete_department_id' id='delete_department_id' value='0'/>
            <input type='hidden' name='department_management' value='1'/>
          </form>
<?

	}

?>
          <a name='addnew'></a><div class='bold mb10'>ADD DEPARTMENT</div>
          <form method='post'>
            <table class='mb30 bcwhite b1sl b1st'>
              <tr class='vtop'>
                <td class='left b1sr b1sb'><input class='w150 text cleardefault' type='text' id='addinput' name='new_name' value='department name'/></td>
                <td class='left b1sr b1sb'><input class='w100 text cleardefault' type='text' name='new_location' value='location'/></td>
                <td class='left b1sr b1sb'><input class='w150 text cleardefault' type='text' name='new_contact' value='contact person'/></td>
                <td class='left b1sr b1sb'><input class='w120 text cleardefault' type='text' name='new_phone' value='phone no'/></td>
                <td class='left b1sr b1sb'><input class='button' type='submit' value='ADD'/></td>
              </tr>
            </table>
            <input type='hidden' name='department_management' value='1'/>
          </form>
          <form method='post' class='mb30' name='go_back_form'>
            <a href='javascript: none();' class='s08 bold' onclick='document.go_back_form.submit();'>GO BACK TO PREVIOUS PAGE</a>
            <input type='hidden' name='no_management_type' value='1'/>
          </form>
<?

}
function itemcategoriesPage() {

?>
            <div class='mb20 s09'><a href='#addnew' onclick='document.getElementById("addinput").focus();'>CLICK HERE</a> to scroll down to the bottom of this page for adding new categories.</div>
            <div class='bold mb10'>CATEGORIES</div>
<?

	if (!isset($_SESSION['sc_list_start'])) { $_SESSION['sc_list_start'] = 1; }

	$result = select_db('c.id, c.name, c.attribute1, c.attribute2, count(i.id) as items', 'categories as c left join items as i on (i.category_id = c.id and i.deleted is null)', sprintf('c.company_id = %s and c.deleted is null and c.type = %s group by c.id order by c.name', quote_smart($_SESSION['settings']['company_id']), quote_smart($_SESSION['settings']['pagetype'])));

	if (mysql_num_rows($result) == 0) {

?>
            <div class='mb30 s09'>No item category exists currently.</div>
<?

	} else {

?>
            <table class='mb30 bcwhite b1sl b1st'>
              <tr class='vtop bctrt'>
                <td class='bold s08 p5 b1sr b1sb'>#</td>
                <td class='bold s08 p5 b1sr b1sb'>CATEGORY NAME</td>
                <td class='bold s08 p5 b1sr b1sb'>ATTRIBUTE 1</td>
                <td class='bold s08 p5 b1sr b1sb'>ATTRIBUTE 2</td>
<?

			if ($_SESSION['settings']['manager'] == 1) {

?>
                <td class='bold s08 p5 b1sr b1sb'>UPDATE?</td>
                <td class='bold s08 p5 b1sr b1sb'>DELETE?</td>
<?

			}

?>
              </tr>
<?

		$rowid = 0;

		while ($result_array = mysql_fetch_array($result)) {

?>
              <tr class='bctr1<?=$rowid++ % 2 == 0 ? 'a' : 'b'?>'>
                <form method='post'>
                  <td class='s08 p1 b1sr b1sb pl5 pr5'><?=$rowid?></td>
                  <td class='p1 b1sr b1sb'><input class='w200 text' type='text' name='new_itemcategory_name' value='<?=replace_ticks($result_array['name'])?>'/>
                  <td class='p1 b1sr b1sb'><input class='w100 text' type='text' name='new_attribute1' value='<?=replace_ticks($result_array['attribute1'])?>'/>
                  <td class='p1 b1sr b1sb'><input class='w100 text' type='text' name='new_attribute2' value='<?=replace_ticks($result_array['attribute2'])?>'/>
<?

			if ($_SESSION['settings']['manager'] == 1) {

?>
                  <td class='p1 b1sr b1sb'><input class='button' type='submit' value='UPDATE'/></td>
                  <td class='p1 b1sr b1sb'><input class='button' type='button' onclick='if (<?=$result_array['items']?> > 0) { alert("This item category cannot be deleted since there are existing items that belong to it!"); } else { if (!confirm("Do you really want to delete this item category?")) { return false; }; document.getElementById("delete_category_id").value = <?=$result_array['id']?>; document.deleteitemcategoryform.submit(); }' value='DELETE'/></td>
<?

			}

?>
                  <input type='hidden' name='update_category_id' value='<?=$result_array['id']?>'/>
                  <input type='hidden' name='category_management' value='1'/>
                </form>
              </tr>
<?

		}

?>
            </table>
            <form method='post' name='deleteitemcategoryform'>
              <input type='hidden' name='delete_category_id' id='delete_category_id' value='0'/>
              <input type='hidden' name='category_management' value='1'/>
            </form>
<?

	}

?>
            <a name='addnew'></a><div class='bold mb10'>ADD CATEGORY</div>
            <form method='post'>
              <table class='mb30 bcwhite b1sl b1st'>
                <tr class='vtop'>
                  <td class='left b1sr b1sb'><input class='w200 text cleardefault' type='text' id='addinput' name='new_itemcategory_name' value='category name'/></td>
                  <td class='left b1sr b1sb'><input class='w100 text cleardefault' type='text' name='new_attribute1' value='attribute 1'/></td>
                  <td class='left b1sr b1sb'><input class='w100 text cleardefault' type='text' name='new_attribute2' value='attrubute 2'/></td>
                  <td class='left b1sr b1sb'><input class='button' type='submit' value='ADD'/></td>
                </tr>
              </table>
              <input type='hidden' name='category_management' value='1'/>
            </form>
            <form method='post' class='mb30' name='go_back_form'>
              <a href='javascript: none();' class='s08 bold' onclick='document.go_back_form.submit();'>GO BACK TO PREVIOUS PAGE</a>
              <input type='hidden' name='no_management_type' value='1'/>
            </form>
<?

}
function taxgroupsPage() {

?>
            <div class='mb20 s09'><a href='#addnew' onclick='document.getElementById("addinput").focus();'>CLICK HERE</a> to scroll down to the bottom of this page for adding new tax groups.</div>
            <div class='bold mb10'>TAX GROUPS</div>
<?

	$result = select_db('tg.id, tg.name, tg.tax, count(i.id) as items', 'tax_groups as tg left join items as i on (i.tax_group_id = tg.id and i.deleted is null)', sprintf('tg.company_id = %s and tg.deleted is null group by tg.id order by tg.tax', quote_smart($_SESSION['settings']['company_id'])));

	if (mysql_num_rows($result) == 0) {

?>
            <div class='mb30 s09'>No tax group exists currently.</div>
<?

	} else {

?>
            <table class='mb30 bcwhite b1sl b1st'>
              <tr class='vtop bctrt'>
                <td class='bold s08 p5 b1sr b1sb'>#</td>
                <td class='bold s08 p5 b1sr b1sb'>TAX GROUP NAME</td>
                <td class='bold s08 p5 b1sr b1sb'>TAX</td>
                <td class='bold s08 p5 b1sr b1sb'>ITEMS</td>
<?

			if ($_SESSION['settings']['manager'] == 1) {

?>
                <td class='bold s08 p5 b1sr b1sb'>UPDATE?</td>
                <td class='bold s08 p5 b1sr b1sb'>DELETE?</td>
<?

			}

?>
              </tr>
<?

		$rowid = 0;

		while ($result_array = mysql_fetch_array($result)) {

?>
              <tr class='bctr1<?=$rowid++ % 2 == 0 ? 'a' : 'b'?>'>
                <form method='post'>
                  <td class='s08 p1 b1sr b1sb pl5 pr5'><?=$rowid?></td>
                  <td class='p1 b1sr b1sb'><input class='w200 text' type='text' name='new_taxgroup_name' value='<?=replace_ticks($result_array['name'])?>'/>
                  <td class='p1 b1sr b1sb'><input class='w100 text' type='text' name='new_tax' value='<?=replace_ticks($result_array['tax'])?>'/>
                  <td class='p1 b1sr b1sb s08'><?=$result_array['items']?></td>
<?

			if ($_SESSION['settings']['manager'] == 1) {

?>
                  <td class='p1 b1sr b1sb'><input class='button' type='submit' value='UPDATE'/></td>
                  <td class='p1 b1sr b1sb'><input class='button' type='button' onclick='if (<?=$result_array['items']?> > 0) { alert("This tax group cannot be deleted since there are existing items that belong to it!"); } else { if (!confirm("Do you really want to delete this tax group?")) { return false; }; document.getElementById("delete_taxgroup_id").value = <?=$result_array['id']?>; document.delete_taxgroup_form.submit(); }' value='DELETE'/></td>
<?

			}

?>
                  <input type='hidden' name='update_taxgroup_id' value='<?=$result_array['id']?>'/>
                  <input type='hidden' name='taxgroup_management' value='1'/>
                </form>
              </tr>
<?

		}

?>
            </table>
            <form method='post' name='delete_taxgroup_form'>
              <input type='hidden' name='delete_taxgroup_id' id='delete_taxgroup_id' value='0'/>
              <input type='hidden' name='taxgroup_management' value='1'/>
            </form>
<?

	}

?>
            <a name='addnew'></a><div class='bold mb10'>ADD TAX GROUP</div>
            <form method='post'>
              <table class='mb30 bcwhite b1sl b1st'>
                <tr class='vtop bctrt'>
                  <td class='p5 center s08 bold b1sr b1sb'>TEXT GROUP NAME</td>
                  <td class='p5 center s08 bold b1sr b1sb'>TAX (%)</td>
                  <td class='p5 center s08 bold b1sr b1sb'>ADD?</td>
                </tr><tr class='vtop'>
                  <td class='left b1sr b1sb'><input class='w200 text' type='text' id='addinput' name='new_taxgroup_name' value=''/></td>
                  <td class='left b1sr b1sb'><input class='w100 text' type='text' name='new_tax' value=''/></td>
                  <td class='left b1sr b1sb'><input class='button' type='submit' value='ADD'/></td>
                </tr>
              </table>
              <input type='hidden' name='taxgroup_management' value='1'/>
            </form>
            <form method='post' class='mb30' name='go_back_form'>
              <a href='javascript: none();' class='s08 bold' onclick='document.go_back_form.submit();'>GO BACK TO PREVIOUS PAGE</a>
              <input type='hidden' name='no_management_type' value='1'/>
            </form>
<?

}
function itemsPage() {
	if (isset($_SESSION['settings'][$_SESSION['settings']['site']]['show_item_id'])) {
		itemDetails(0, 1);
	} else if (isset($_SESSION['settings']['itemmgnt']['sale_receipt_id']) || isset($_SESSION['settings']['itemmgnt']['sale_receipt_id2'])) {
		salesPage();
	} else if (isset($_SESSION['settings']['itemmgnt']['highlight_item_id']) && !isset($_SESSION['settings']['itemmgnt']['style_edit_item_id'])) {
		if ($_SESSION['settings']['itemmgnt']['highlight_item_id'] == 0) {

?>
          <div class='mb20'>No item found with matching barcode.</div>
<?

		} else {

?>
	  <div class='s09 mt10 mb5 center'>For searching and listing items the following options are used:</div>
	  <table class='mb15'><tr><td><form method='post' class='s09 left'><input type='checkbox' id='global_search_option_zero' name='search_option_zero' value='1'<?=isset($_SESSION['settings']['itemmgnt']['category_id']) ? " onclick='this.form.submit();'" : ''?><?=$_SESSION['settings']['itemmgnt']['search_option_zero'] == '1' ? ' checked' : ''?>/> include items with quantity < 1<br/><input type='checkbox' id='global_search_option_archived' name='search_option_archived' value='1'<?=isset($_SESSION['settings']['itemmgnt']['category_id']) ? " onclick='this.form.submit();'" : ''?><?=$_SESSION['settings']['itemmgnt']['search_option_archived'] == '1' ? ' checked' : ''?>/> include archived items<input type='hidden' name='search_options_update' value='1'/><input type='hidden' name='item_management' value='1'/></form></td></tr></table>
          <div class='bold mb10'>FOUND ITEM</div>
<?

			$where = sprintf('i.number = %s', quote_smart($_SESSION['settings']['itemmgnt']['highlight_item_number']));

			if (isset($_SESSION['settings']['itemmgnt']['search_option_zero']) && $_SESSION['settings']['itemmgnt']['search_option_zero'] == 0) {
				$where .= ($where == '' ? '' : ' and ') . 'i.quantity > 0';
			}

			if (isset($_SESSION['settings']['itemmgnt']['search_option_archived']) && $_SESSION['settings']['itemmgnt']['search_option_archived'] == 0) {
				$where .= ($where == '' ? '' : ' and ') . 'i.archived = 0';
			}

			if (itemList(sprintf($where, quote_smart($_SESSION['settings']['itemmgnt']['highlight_item_number']))) > 0) {;
                                //itemDetails(0, 1);
			}
		}
	} else if ($_SESSION['settings']['itemmgnt']['add_item'] == 1) {
		if ($_POST['template_add_item_id'] > 0) {
			itemDetails(1, 0, $_POST['template_add_item_id']);
		} else {
			itemDetails(1);
		}
	} else {
		$resultc = select_db('id, name', 'categories', sprintf('company_id = %s and type = %s and deleted is null order by name', quote_smart($_SESSION['settings']['company_id']), quote_smart($_SESSION['settings']['pagetype'])));
		$resultd = select_db('id, name', 'departments', sprintf('company_id = %s and deleted is null order by name', quote_smart($_SESSION['settings']['company_id'])));
		$resultb = select_db('id, name', 'brands', sprintf('company_id = %s and deleted is null order by name', quote_smart($_SESSION['settings']['company_id'])));
		$resultn = select_db('distinct i.number', 'items as i join categories as c on c.id = i.category_id', sprintf('c.company_id = %s and coalesce(i.deleted, c.deleted) is null order by number asc', quote_smart($_SESSION['settings']['company_id'])));


		if (mysql_num_rows($resultc) == 0) {

?>
          <div class='mt20 mb30'>No categories do exist yet. You need to add categories first.</div>
<?

		} else if (mysql_num_rows($resultd) == 0) {

?>
          <div class='mt20 mb30'>No departments do exist yet. You need to add departments first.</div>
<?

		} else {

?>
	  <div class='s09 mt10 mb5 center'>For searching and listing items the following options are used:</div>
	  <table class='mb15'><tr><td><form method='post' class='s09 left'><input type='checkbox' id='global_search_option_zero' name='search_option_zero' value='1'<?=isset($_SESSION['settings']['itemmgnt']['category_id']) ? " onclick='this.form.submit();'" : ''?><?=$_SESSION['settings']['itemmgnt']['search_option_zero'] == '1' ? ' checked' : ''?>/> include items with quantity < 1<br/><input type='checkbox' id='global_search_option_archived' name='search_option_archived' value='1'<?=isset($_SESSION['settings']['itemmgnt']['category_id']) ? " onclick='this.form.submit();'" : ''?><?=$_SESSION['settings']['itemmgnt']['search_option_archived'] == '1' ? ' checked' : ''?>/> include archived items<input type='hidden' name='search_options_update' value='1'/><input type='hidden' name='item_management' value='1'/></form></td></tr></table>
<?

			if (!isset($_SESSION['settings']['itemmgnt']['category_id'])) {

?>
          <div class='bold mb15 mt10'>SHOW ITEM BY BARCODE</div>
          <div class='s09 mb10'>Enter the item barcode:</div>
          <form class='mb20' method='post' name='iteminfoform'>
            <input class='w150 text' type='text' name='infoitemid' id='focusitem' value=''/>
            <input class='button' type='button' value='SHOW ITEM INFO' onclick='if (document.getElementById("global_search_option_zero").checked) { this.form.search_option_zero.value = 1; }; if (document.getElementById("global_search_option_archived").checked) { this.form.search_option_archived.value = 1; }; this.form.submit();'/>
            <input type='hidden' name='search_option_zero' value='0'/>
            <input type='hidden' name='search_option_archived' value='0'/>
            <input type='hidden' name='showiteminfo' value='1'/>
          </form>
          <div class='bold mb15 mt10'>SHOW ITEMS BY<br/>CATEGORY/DEPARTMENT/BRAND</div>
<?

			}

?>
          <table class='mb30'>
            <tr class='vtop'>
              <td class='pt5 s08 bold pr10 left'>CATEGORY:</td>
              <td class='left'>
                <form class='mb2 left' method='post' name='categoryform'>
                  <select name='category_id' onchange='if (document.getElementById("global_search_option_zero").checked) { this.form.search_option_zero.value = 1; }; if (document.getElementById("global_search_option_archived").checked) { this.form.search_option_archived.value = 1; }; document.categoryform.submit();'>
<?

			if (!isset($_SESSION['settings']['itemmgnt']['category_id'])) {

?>
                    <option value='n'>- please select -</option>
<?

			}

?>
                    <option value='0'<? if (isset($_SESSION['settings']['itemmgnt']['category_id']) && $_SESSION['settings']['itemmgnt']['category_id'] == 0) { ?> selected<? } ?>>all categories</option>
<?

			while ($result_array = mysql_fetch_array($resultc)) {

?>
                    <option value='<?=$result_array['id']?>'<? if ($_SESSION['settings']['itemmgnt']['category_id'] == $result_array['id']) { ?> selected<? } ?>><?=$result_array['name']?></option>
<?

			}

?>

                  </select>
                  <input type='hidden' name='search_option_zero' value='0'/>
                  <input type='hidden' name='search_option_archived' value='0'/>
                  <input type='hidden' name='choose_category' value='1'/>
                  <input type='hidden' name='item_management' value='1'/>
                </form>
              </td>
            </tr><tr class='vtop'>
              <td class='pt5 s08 bold pr10 left'>DEPARTMENT:</td>
              <td class='left'>
                <form class='mb2 left' method='post' name='select_department_form'>
                  <select name='department_id' onchange='if (document.getElementById("global_search_option_zero").checked) { this.form.search_option_zero.value = 1; }; if (document.getElementById("global_search_option_archived").checked) { this.form.search_option_archived.value = 1; }; document.select_department_form.submit();'>
<?

			if (!isset($_SESSION['settings']['itemmgnt']['department_id'])) {

?>
                    <option value='n'>- please select -</option>
<?

			}

?>
                    <option value='0'<? if (isset($_SESSION['settings']['itemmgnt']['department_id']) && $_SESSION['settings']['itemmgnt']['department_id'] == 0) { ?> selected<? } ?>>all departments</option>
<?

			while ($result_array = mysql_fetch_array($resultd)) {

?>
                    <option value='<?=$result_array['id']?>'<? if ($_SESSION['settings']['itemmgnt']['department_id'] == $result_array['id']) { ?> selected<? } ?>><?=$result_array['name']?></option>
<?

			}

?>

                  </select>
                  <input type='hidden' name='search_option_zero' value='0'/>
                  <input type='hidden' name='search_option_archived' value='0'/>
                  <input type='hidden' name='choose_department' value='1'/>
                  <input type='hidden' name='item_management' value='1'/>
                </form>
              </td>
            </tr>
            <tr class='vtop'>
              <td class='pt5 s08 bold pr10 left'>BRAND:</td>
              <td class='left'>
                <form class='mb2 left' method='post' name='select_brand_form'>
                  <select name='brand_id' onchange='if (document.getElementById("global_search_option_zero").checked) { this.form.search_option_zero.value = 1; }; if (document.getElementById("global_search_option_archived").checked) { this.form.search_option_archived.value = 1; }; document.select_brand_form.submit();'>
<?
			if (!isset($_SESSION['settings']['itemmgnt']['brand_id'])) {
?>
                    <option value='n'>- please select -</option>
<?
			}
?>
                    <option value='0'<? if (isset($_SESSION['settings']['itemmgnt']['brand_id']) && $_SESSION['settings']['itemmgnt']['brand_id'] == 0) { ?> selected<? } ?>>all brands</option>
<?
			while ($result_array = mysql_fetch_array($resultb)) {
?>
                    <option value='<?=$result_array['id']?>'<? if ($_SESSION['settings']['itemmgnt']['brand_id'] == $result_array['id']) { ?> selected<? } ?>><?=$result_array['name']?></option>
<?
			}
?>
                  </select>
                  <input type='hidden' name='search_option_zero' value='0'/>
                  <input type='hidden' name='search_option_archived' value='0'/>
                  <input type='hidden' name='choose_brand' value='1'/>
                  <input type='hidden' name='item_management' value='1'/>
                </form>
              </td>
            </tr>
            <tr class='vtop'>
              <td class='pt5 s08 bold pr10 left'>STYLE:</td>
              <td class='left'>
                <form class='mb2 left' method='post' name='select_style_form'>
                  <select name='number_id' onchange='if (document.getElementById("global_search_option_zero").checked) { this.form.search_option_zero.value = 1; }; if (document.getElementById("global_search_option_archived").checked) { this.form.search_option_archived.value = 1; }; document.select_style_form.submit();'>
<?
			if (!isset($_SESSION['settings']['itemmgnt']['number_id'])) {
?>
                    <option value='n'>- please select -</option>
<?
			}
?>
                    <option value='0'<? if (isset($_SESSION['settings']['itemmgnt']['number_id']) && $_SESSION['settings']['itemmgnt']['number_id'] == '0') { ?> selected<? } ?>>all styles</option>
<?
			while ($result_array = mysql_fetch_array($resultn)) {
?>
                    <option value='<?=$result_array['number']?>'<? if (isset($_SESSION['settings']['itemmgnt']['number_id']) && $_SESSION['settings']['itemmgnt']['number_id'] == $result_array['number']) { ?> selected<? } ?>><?=$result_array['number']?></option>
<?
			}
?>

                  </select>
                  <input type='hidden' name='search_option_zero' value='0'/>
                  <input type='hidden' name='search_option_archived' value='0'/>
                  <input type='hidden' name='choose_number' value='1'/>
                  <input type='hidden' name='item_management' value='1'/>
                </form>
              </td>
            </tr>
          </table>
<?
			if (isset($_SESSION['settings']['itemmgnt']['category_id'])) {
?>
          <div class='mb10 bold'><?=$_SESSION['settings']['itemmgnt']['highlight_item_id'] > 0 ? "FOUND ITEM" : "ITEMS" ?></div>
          <div class='mb10 s08' id='resultinfo' style='display: none;'></div>
<?
				$where = isset($_SESSION['settings']['itemmgnt']['search_option_zero']) && $_SESSION['settings']['itemmgnt']['search_option_zero'] == 0 ? 'i.quantity > 0' : '';
				if (isset($_SESSION['settings']['itemmgnt']['search_option_archived']) && $_SESSION['settings']['itemmgnt']['search_option_archived'] == 0) {
					$where .= ($where == '' ? '' : ' and ') . 'i.archived = 0';
				}
				$rows = itemList($where, 1);
				if ($_SESSION['settings']['manager'] == 1 && $rows > 0) {
					if (isset($_SESSION['settings']['itemmgnt']['style_edit_item_id'])) {
?>
          <div class='mb10 bold'>MODIFY COMMON DETAILS</div>
          <div class='mb20 s09'>In the following you can edit the common details of the item(s) shown above:</div>
          <form class='mb30' method='post'>
            <table class='mb20'>
              <tr>
                <td class='s08 bold pr10 left<?=isset($_SESSION['bad']['category']) ? ' red' : ''?>'>CATEGORY:</td>
                <td class='left'>
                  <select class='s08' name='new_category_id'>
<?
						$result = select_db('id, name', 'categories', sprintf('company_id = %s and type = %s and deleted is null order by name asc', quote_smart($_SESSION['settings']['company_id']), quote_smart($_SESSION['settings']['pagetype'])));
						while ($result_array = mysql_fetch_array($result)) {
?>
                    <option value='<?=$result_array['id']?>'<?=$result_array['id'] == $_SESSION['edit']['style']['new']['category_id'] ? ' selected' : ''?>><?=$result_array['name']?></option>
<?
						}
?>
                  </select>
                </td>
              </tr>
              <tr>
                <td class='s08 bold pr10 left<?=isset($_SESSION['bad']['number']) ? ' red' : ''?>'>STYLE:</td>
                <td class='left'><input type='text' class='text w100' name='new_number' value='<?=replace_ticks($_SESSION['edit']['style']['new']['number'])?>'/></td>
              </tr>
              <tr>
                <td class='s08 bold pr10 left<?=isset($_SESSION['bad']['name']) ? ' red' : ''?>'>NAME:</td>
                <td class='left'><input type='text' class='text w300' name='new_name' value='<?=replace_ticks($_SESSION['edit']['style']['new']['name'])?>'/></td>
              </tr>
              <tr>
                <td class='s08 bold pr10 left<?=isset($_SESSION['bad']['brand']) ? ' red' : ''?>'>BRAND:</td>
                <td class='left'>
                  <select class='s08' name='new_brand_id'>
<?
						if (!($_SESSION['style']['new']['brand_id'] > 0)) {
?>
                    <option value='0'>- please choose -</option>
<?
						}
						$result = select_db('id, name', 'brands', sprintf('company_id = %s and deleted is null order by name asc', quote_smart($_SESSION['settings']['company_id'])));
						while ($result_array = mysql_fetch_array($result)) {
?>
                    <option value='<?=$result_array['id']?>'<?=$result_array['id'] == $_SESSION['edit']['style']['new']['brand_id'] ? ' selected' : ''?>><?=$result_array['name']?></option>
<?
						}
?>
                  </select>
                </td>
              </tr>
              <tr>
                <td class='s08 bold pr10 left<?=isset($_SESSION['bad']['department']) ? ' red' : ''?>'>DEPARTMENT:</td>
                <td class='left'>
                  <select class='s08' name='new_department_id'>
<?
						if (!($_SESSION['edit']['style']['new']['department_id'] > 0)) {
?>
                    <option value='0'>- please select -</option>
<?
						}
						$result = select_db('id, name', 'departments', sprintf('company_id = %s and deleted is null order by name asc', quote_smart($_SESSION['settings']['company_id'])));
						while ($result_array = mysql_fetch_array($result)) {
?>
                    <option value='<?=$result_array['id']?>'<?=$result_array['id'] == $_SESSION['edit']['style']['new']['department_id'] ? ' selected' : ''?>><?=$result_array['name']?></option>
<?
						}
?>
                  </select>
                </td>
              </tr>
              <tr class='vtop'>
                <td class='s08 bold pr10 left pt4'>DESCRIPTION:</td>
                <td class='left'><textarea class='w300' rows='5' name='new_style'><?=$_SESSION['edit']['style']['new']['style']?></textarea></td>
              </tr>
            </table>
            <input type='submit' class='button' value='UPDATE ALL SHOWN ITEMS'/>
            <input type='hidden' name='update_style' value='1'/>
            <input type='hidden' name='item_group_management' value='1'/>
          </form>
          <form class='mb30' method='post' name='exit_style_edit_form'>
            <a href='javascript: none();' class='s08 bold' onclick='document.exit_style_edit_form.submit();'>GO BACK TO PREVIOUS PAGE</a>
            <input type='hidden' name='exit_style_edit' value='1'/>
            <input type='hidden' name='item_group_management' value='1'/>
          </form>
<?
					} else {
?>
          <div class='mb10 bold'>TOTALS</div>
          <table class='b1st b1sl mb30'>
            <tr class='bctrt'>
              <td class='bold s08 p5 b1sr b1sb'>BUY PRICE</td>
              <td class='bold s08 p5 b1sr b1sb'>PRICE</td>
              <td class='bold s08 p5 b1sr b1sb'>QTY</td>
            </tr>
            <tr class='bcwhite'>
              <td class='s08 p5 b1sr b1sb'><?=money($_SESSION['totalbuy'])?></td>
              <td class='s08 p5 b1sr b1sb'><?=money($_SESSION['totalprice'])?></td>
              <td class='s08 p5 b1sr b1sb'><?=$_SESSION['totalquantity']?></td>
            </tr>
          </table>
<?
					}
				}
			}
		}
		if (!isset($_SESSION['settings']['itemmgnt']['style_edit_item_id'])) {
?>
          <form method='post' class='mb30' name='go_back_form'>
            <a href='javascript: none();' class='s08 bold' onclick='document.go_back_form.submit();'>GO BACK TO PREVIOUS PAGE</a>
            <input type='hidden' name='no_management_type' value='1'/>
          </form>
<?
		}
	}
}
function reportsPage() {
?>
        <div class='s15 bold mt10 mb20'>REPORTS</div>
<?
	if ((isset($_SESSION['sale3']) && $_SESSION['settings']['reports']['show_item_id2'] > 0) || (!isset($_SESSION['sale3']) && $_SESSION['settings']['reports']['show_item_id'] > 0)) {
		itemDetails();
	} else if (isset($_SESSION['settings']['reports']['show_return_details'])) {
		returnsPage();
	} else if (isset($_SESSION['settings']['reports']['sale_receipt_id'])) {
		salesPage();
	} else {
		switch ($_SESSION['settings']['reports']['type']) {
			case 'summary' : showReport('summary'); break;
			case 'sales'   : showReport('sales'); break;
			case 'returns' : showReport('returns'); break;
			case 'vouchers' : showReport('vouchers'); break;
			case 'inventory' : showReport('inventory'); break;
			default :

?>
        <div class='s09 mb20'>Click on the report type you want to be shown:</div>
        <div class='s1 mb10'><a class='bold' href='javascript: none();' onclick="document.getElementById('post_values').value = 'report_management=1|show_type=summary'; document.page_form.submit();">SUMMARY REPORT</a></div>
        <div class='s1 mb10'><a class='bold' href='javascript: none();' onclick="document.getElementById('post_values').value = 'report_management=1|show_type=sales'; document.page_form.submit();">SALES REPORT</a></div>
        <div class='s1 mb10'><a class='bold' href='javascript: none();' onclick="document.getElementById('post_values').value = 'report_management=1|show_type=returns'; document.page_form.submit();">RETURNS REPORT</a></div>
        <div class='s1 mb10'><a class='bold' href='javascript: none();' onclick="document.getElementById('post_values').value = 'report_management=1|show_type=vouchers'; document.page_form.submit();">VOUCHERS REPORT</a></div>
        <div class='s1 mb10'><a class='bold' href='javascript: none();' onclick="document.getElementById('post_values').value = 'report_management=1|show_type=inventory'; document.page_form.submit();">INVENTORY REPORT</a></div>
        <div class='s1 mb10'><a class='bold' href='javascript: none();' onclick="document.getElementById('post_values').value = 'report_management=1|show_type=deliveries'; document.page_form.submit();">DELIVERY REPORT (- coming soon -)</a></div>
        <div class='s1 mb10'><a class='bold' href='javascript: none();' onclick="document.getElementById('post_values').value = 'report_management=1|show_type=items'; document.page_form.submit();">ITEM REPORT (- coming soon -)</a></div>
<?

		}
	}
}
function showReport($reporttype) {
	if ($reporttype == 'inventory') {
		if (isset($_SESSION['settings']['reports']['itype'])) {
			$expression = str_replace('eq', ' = ', $_SESSION['settings']['reports']['itype']);
			$expression = str_replace('lt', ' < ', $expression);
			$expression = str_replace('le', ' <= ', $expression);
			$expression = str_replace('ge', ' >= ', $expression);
			$expression = str_replace('gt', ' > ', $expression);

?>
            <div class='mb20 mt20 bold s1'>Inventory Report (Quantity<?=$expression?>)</div>
<?

			if (($rows = itemList("quantity$expression", 1)) == 0) {

?>
            <div class='mb20 s09'>There is no item that is matching the expression.</div>
<?

			}

?>
            <div class='mt30 mb30'><a class='s08 bold' href='javascript: none();' onclick="document.getElementById('drop_settings').value='reports:itype'; document.settings_form.submit();">GO BACK TO PREVIOUS PAGE</a></form>
<?

		} else {

?>
            <div class='mb20 mt20 s1'>Click on the inventory report type you want to be shown:</div>
            <div class='mb10 s1'><a class='bold' href='javascript: none();' onclick="document.getElementById('new_settings').value='reports:itype=eq0'; document.settings_form.submit();">INVENTORY REPORT (QUANTITY = 0)</a></div>
            <div class='mb10 s1'><a class='bold' href='javascript: none();' onclick="document.getElementById('new_settings').value='reports:itype=ge20'; document.settings_form.submit();">INVENTORT REPORT (QUANTITY >= 20)</a></div>
            <div class='mt30 mb30'><a class='s08 bold' href='javascript: none();' onclick="document.getElementById('drop_settings').value='reports:type'; document.settings_form.submit();">GO BACK TO REPORTS MAIN PAGE</a></form>
<?

		}

		return;
	}

	if ($reporttype == 'vouchers') {
		if (isset($_SESSION['settings']['reports']['vtype'])) {

?>
            <div class='mb20 mt20 bold s1'><?=strtoupper($_SESSION['settings']['reports']['vtype'])?> VOUCHER REPORT<?=$expression?></div>
            <div class='s08 mb5 bold'>SELECT DATE RANGE</div>
            <form method='post' class='mb30'>
              <table>
                <tr>
                  <td>
<?

			show_calendar22('from', 'FROM:', $_SESSION['settings']['reports']['frame'] == 'r' ? $_SESSION['settings']['reports']['from_date'] : '2010-01-01', 0, 1);

?>
                  </td><td class='pl30'>
<?

			show_calendar22('till', 'TILL:', $_SESSION['settings']['reports']['till_date'], 0, 1);

?>
                  </td>
                </tr>
                <tr>
                  <td colspan='2' class='center pt10'>
                    <input type='hidden' name='date_range_report' value='1'/>
                    <input type='hidden' name='report_management' value='1'/>
                  </td>
                </tr>
              </table>
            </form>
<?

			voucherList($_SESSION['settings']['reports']['vtype'] == 'open' ? 'deleted is null' : 'deleted is not null');

?>
            <div class='mt30 mb30'><a class='s08 bold' href='javascript: none();' onclick="document.getElementById('drop_settings').value='reports:vtype'; document.settings_form.submit();">GO BACK TO PREVIOUS PAGE</a></form>
<?

		} else {

?>
            <div class='mb20 mt20 s1'>Please select the voucher report type you want to be shown:</div>
            <div class='mb10 s1'><a class='bold' href='javascript: none();' onclick="document.getElementById('new_settings').value='reports:vtype=open'; document.settings_form.submit();">OPEN VOUCHERS REPORT</a></div>
            <div class='mb10 s1'><a class='bold' href='javascript: none();' onclick="document.getElementById('new_settings').value='reports:vtype=used'; document.settings_form.submit();">USED VOUCHERS REPORT</a></div>
            <div class='mt30 mb30'><a class='s08 bold' href='javascript: none();' onclick="document.getElementById('drop_settings').value='reports:type'; document.settings_form.submit();">GO BACK TO REPORTS MAIN PAGE</a></form>
<?

		}

		return;
	}

	$rf = $_SESSION['settings']['reports']['frame'];

	switch($rf) {
		case 'y' :
		case 'm' :
		case 'd' :
		case 'r' :
			$type = $rf == 'y' ? 'YEAR' : ($rf == 'm' ? 'MONTH' : ($rf == 'd' ? 'DAY' : 'DATE RANGE'));

?>
            <div class='mb20 s1 bold'><?=$type == 'DAY' ? 'DAI' : $type?><?=$rf == 'r' ? '' : 'LY'?> <?=strtoupper($reporttype)?> REPORT</div>
            <form method='post' name='dateselectform'>
              <input type='hidden' name='reportyear' id='reportyear' value='<?=isset($_SESSION['dateinfo']['year']) ? $_SESSION['dateinfo']['year'] : 0?>'/>
              <input type='hidden' name='reportmonth' id='reportmonth' value='<?=isset($_SESSION['dateinfo']['month']) ? $_SESSION['dateinfo']['month'] : 0?>'/>
              <input type='hidden' name='reportday' id='reportday' value='<?=isset($_SESSION['dateinfo']['day']) ? $_SESSION['dateinfo']['day'] : 0?>'/>
              <input type='hidden' name='reportstring' id='reportstring' value=''/>
              <input type='hidden' name='reportdatechange' value='1'/>
            </form>
<?

			if (!isset($_SESSION['dateinfo']['year']) && $rf != 'r') {

?>
            <div class='mb20'>Please select the year:</div>
            <table>
              <tr class='vtop'>
                <td class='left'>
<?

				$result = select_db('year, sum(sales) as sales, sum(returns) as returns', sprintf('(select year, sales, returns, company_id from (select year(convert_tz(added, "utc", %s)) as year, 1 as sales, 0 as returns, company_id from sales where deleted is null) as info union all (select year(convert_tz(added, "utc", %s)) as year, 0 as sales, 1 as returns, company_id from returns)) as info2', quote_smart($_SESSION['preferences']['timezone']), quote_smart($_SESSION['preferences']['timezone'])), sprintf('company_id = %s group by year order by year desc', quote_smart($_SESSION['settings']['company_id'])));
				$rows = mysql_num_rows($result);
				$rowid = 0;

				while ($result_array = mysql_fetch_array($result)) {
					if (($reporttype == 'sales' && $result_array['sales'] > 0) || ($reporttype == 'returns' && $result_array['returns'] > 0) || $reporttype == 'summary') {
						$rowid++;
						if (($rows > 20 && ($rowid == ceil($rows / 3) + 1 || $rowid == 2 * ceil($rows / 3) + 1)) || ($rows > 10 && $rows <= 20 && $rowid == ceil($rows / 2) + 1)) {

?>
                </td><td class='left pl100'>
<?

						}

?>
                  <div class='s1 mb10'><a class='bold' href='javascript: none();' onclick='document.getElementById("reportyear").value = <?=$result_array['year']?>; document.getElementById("reportstring").value = "<?=$result_array['year']?>"; document.dateselectform.submit();'><?=$result_array['year']?> (<? if ($reporttype == 'sales' || $reporttype == 'summary') { ?><?=$result_array['sales']?> sale<?=$result_array['sales'] != 1 ? 's' : ''?><? }; if ($reporttype == 'summary') { ?>, <? }; if ($reporttype == 'summary' || $reporttype == 'returns') { ?><?=$result_array['returns']?> return<?=$result_array['returns'] != 1 ? 's' : ''?><? } ?>)</a></div>
<?

					}
				}

?>
                </td>
              </tr>
            </table>
            <div class='mt30 mb30'><a class='s08 bold' href='javascript: none();' onclick="document.getElementById('drop_settings').value='reports:frame'; document.settings_form.submit();">GO BACK TO PREVIOUS PAGE</a></form>
<?

			} else if (!isset($_SESSION['dateinfo']['month']) && $rf != 'y' && $rf != 'r') {

?>
            <div class='mb20'>Please select the month:</div>
<?

				$result = select_db('month, monthname, sum(sales) as sales, sum(returns) as returns', sprintf('(select month, monthname, year, sales, returns, company_id from (select month(convert_tz(added, "utc", %s)) as month, monthname(convert_tz(added, "utc", %s)) as monthname, year(convert_tz(added, "utc", %s)) as year, 1 as sales, 0 as returns, company_id from sales where deleted is null) as info union all (select month(convert_tz(added, "utc", %s)) as month, monthname(convert_tz(added, "utc", %s)) as monthname, year(convert_tz(added, "utc", %s)) as year, 0 as sales, 1 as returns, company_id from returns)) as info2', quote_smart($_SESSION['preferences']['timezone']), quote_smart($_SESSION['preferences']['timezone']), quote_smart($_SESSION['preferences']['timezone']), quote_smart($_SESSION['preferences']['timezone']), quote_smart($_SESSION['preferences']['timezone']), quote_smart($_SESSION['preferences']['timezone'])), sprintf('company_id = %s and year = %s group by month order by month desc', quote_smart($_SESSION['settings']['company_id']), quote_smart($_SESSION['dateinfo']['year'])));

				while ($result_array = mysql_fetch_array($result)) {
					if (($reporttype == 'sales' && $result_array['sales'] > 0) || ($reporttype == 'returns' && $result_array['returns'] > 0) || $reporttype == 'summary') {

?>
            <div class='s1 mb10'><a class='bold' href='javascript: none();' onclick='document.getElementById("reportmonth").value = <?=$result_array['month']?>; document.getElementById("reportstring").value = "<?=$result_array['monthname']?> <?=$_SESSION['dateinfo']['year']?>"; document.dateselectform.submit();'><?=$result_array['monthname']?> <?=$_SESSION['dateinfo']['year']?> (<? if ($reporttype == 'sales' || $reporttype == 'summary') { ?><?=$result_array['sales']?> sale<?=$result_array['sales'] != 1 ? 's' : ''?><? }; if ($reporttype == 'summary') { ?>, <? }; if ($reporttype == 'summary' || $reporttype == 'returns') { ?><?=$result_array['returns']?> return<?=$result_array['returns'] != 1 ? 's' : ''?><? } ?>)</a></div>
<?

					}
				}

?>
            <div class='mt30 mb30'><a class='bold s08' href='javascript: none();' onclick='document.getElementById("reportyear").value = -1; document.dateselectform.submit();'>GO BACK TO YEAR SELECTION</a></div>
<?

			} else if (!isset($_SESSION['dateinfo']['day']) && $rf == 'd') {

?>
            <div class='mb20'>Please select the day:</div>
            <table>
              <tr class='vtop'>
                <td class='left'>
<?

				$result = select_db('day, daystring, monthname, sum(sales) as sales, sum(returns) as returns', sprintf('(select day, daystring, month, monthname, year, sales, returns, company_id from (select day(convert_tz(added, "utc", %s)) as day, date_format(convert_tz(added, "utc", %s), %s) as daystring, month(convert_tz(added, "utc", %s)) as month, monthname(convert_tz(added, "utc", %s)) as monthname, year(convert_tz(added, "utc", %s)) as year, 1 as sales, 0 as returns, company_id from sales where deleted is null) as info union all (select day(convert_tz(added, "utc", %s)) as day, date_format(convert_tz(added, "utc", %s), %s) as daystring, month(convert_tz(added, "utc", %s)) as month, monthname(convert_tz(added, "utc", %s)) as monthname, year(convert_tz(added, "utc", %s)) as year, 0 as sales, 1 as returns, company_id from returns)) as info2', quote_smart($_SESSION['preferences']['timezone']), quote_smart($_SESSION['preferences']['timezone']), "'%D'", quote_smart($_SESSION['preferences']['timezone']), quote_smart($_SESSION['preferences']['timezone']), quote_smart($_SESSION['preferences']['timezone']), quote_smart($_SESSION['preferences']['timezone']), quote_smart($_SESSION['preferences']['timezone']), "'%D'", quote_smart($_SESSION['preferences']['timezone']), quote_smart($_SESSION['preferences']['timezone']), quote_smart($_SESSION['preferences']['timezone'])), sprintf('company_id = %s and year = %s and month = %s group by day order by day desc', quote_smart($_SESSION['settings']['company_id']), quote_smart($_SESSION['dateinfo']['year']), quote_smart($_SESSION['dateinfo']['month'])));
				$rows = mysql_num_rows($result);
				$rowid = 0;

				while ($result_array = mysql_fetch_array($result)) {
					if (($reporttype == 'sales' && $result_array['sales'] > 0) || ($reporttype == 'returns' && $result_array['returns'] > 0) || $reporttype == 'summary') {
						$rowid++;
						if ($rowid == ceil($rows / 2) + 1 && $rows > 10) {

?>
                </td><td class='left pl100'>
<?

						}

?>
                  <div class='s1 mb10'><a class='bold' href='javascript: none();' onclick='document.getElementById("reportday").value = <?=$result_array['day']?>; document.getElementById("reportstring").value = "<?=substr($result_array['monthname'], 0, 3)?> <?=$result_array['daystring']?>, <?=$_SESSION['dateinfo']['year']?>"; document.dateselectform.submit();'><?=substr($result_array['monthname'], 0, 3)?> <?=$result_array['daystring']?>, <?=$_SESSION['dateinfo']['year']?> (<? if ($reporttype == 'sales' || $reporttype == 'summary') { ?><?=$result_array['sales']?> sale<?=$result_array['sales'] != 1 ? 's' : ''?><? }; if ($reporttype == 'summary') { ?>, <? }; if ($reporttype == 'summary' || $reporttype == 'returns') { ?><?=$result_array['returns']?> return<?=$result_array['returns'] != 1 ? 's' : ''?><? } ?>)</a></div>
<?

					}
				}

?>
                </td>
              </tr>
            </table>
            <div class='mt30 mb30'><a class='bold s08' href='javascript: none();' onclick='document.getElementById("reportmonth").value = -1; document.dateselectform.submit();'>GO BACK TO MONTH SELECTION</a></div>
<?

			} else {
				unset($statistics);

				if ($rf == 'r') {
					$where = sprintf('s.company_id = %s and date(convert_tz(s.added, "utc", %s)) >= %s and date(convert_tz(s.added, "utc", %s)) <= %s', quote_smart($_SESSION['settings']['company_id']), quote_smart($_SESSION['preferences']['timezone']), quote_smart($_SESSION['settings']['reports']['from_date']), quote_smart($_SESSION['preferences']['timezone']), quote_smart($_SESSION['settings']['reports']['till_date']));

?>
            <form method='post' class='pb<?=$reporttype == 'sales' ? '5' : '20'?>'>
              <table>
                <tr>
                  <td>
<?

					show_calendar22('from', 'FROM:', $_SESSION['settings']['reports']['from_date'], 0, 1);

?>
                  </td><td class='pl30'>
<?

					show_calendar22('till', 'TILL:', $_SESSION['settings']['reports']['till_date'], 0, 1);

?>
                  </td>
                </tr>
                <tr>
                  <td colspan='2' class='center'>
                    <input type='hidden' name='date_range_report' value='1'/>
                    <input type='hidden' name='report_management' value='1'/>
                  </td>
                </tr>
              </table>
            </form>
<?

				} else {
					$where = sprintf('s.company_id = %s and year(convert_tz(s.added, "utc", %s)) = %s', quote_smart($_SESSION['settings']['company_id']), quote_smart($_SESSION['preferences']['timezone']), quote_smart($_SESSION['dateinfo']['year']));
					if ($_SESSION['dateinfo']['month'] > 0) { $where .= sprintf(' and month(convert_tz(s.added, "utc", %s)) = %s', quote_smart($_SESSION['preferences']['timezone']), quote_smart($_SESSION['dateinfo']['month'])); }
					if ($_SESSION['dateinfo']['day'] > 0) { $where .= sprintf(' and day(convert_tz(s.added, "utc", %s)) = %s', quote_smart($_SESSION['preferences']['timezone']), quote_smart($_SESSION['dateinfo']['day'])); }
				}

				$spwhere = $_SESSION['settings']['reports']['sales_person_id'] > 0 ? sprintf(' and coalesce(l2.id, l.id) = %s', quote_smart($_SESSION['settings']['reports']['sales_person_id'])) : '';

				if ($reporttype == 'summary' || $reporttype == 'sales') {
					$result = select_db('s.id, s.paid', 'sales as s join logins as l on l.id = s.login_id left join logins as l2 on l2.id = s.sales_person_id', 's.deleted is null and ' . $where . $spwhere);
					$statistics['sales']['quantity'] = mysql_num_rows($result);

					while ($result_array = mysql_fetch_array($result)) {
						$result2 = select_db('si.price, si.discount, si.additional_discount, si.tax, si.quantity', 'sale_items as si join items as i on i.id = si.item_id', sprintf('si.sale_id = %s', quote_smart($result_array['id'])));
						$totalprice = $totaldiscount = $totaltax = 0;

						while ($result_array2 = mysql_fetch_array($result2)) {
							$vals = calc($result_array2['price'], $result_array2['discount'], $result_array2['additional_discount'], $result_array2['tax'], $result_array2['quantity']);
							$totalprice += $vals['price'];
							$totaldiscount += $vals['discount'];
							$totaltax += $vals['tax'];
						}

						$statistics['sales']['totalprice'] += $totalprice;
						$statistics['sales']['totaldiscount'] += $totaldiscount;
						$statistics['sales']['totaltax'] += $totaltax;
						$statistics['sales']['totalnet'] += $totalprice - $totaldiscount + $totaltax;

						$cash = $card = $voucher = 0;

						if ($result_array['paid'] > 0) {
							$cash = $result_array['paid'];
						}

						$result2 = select_db('value', 'vouchers', sprintf('type = "gift_certificate" and origin_id = %s', quote_smart($result_array['id'])));

						while ($result_array2 = mysql_fetch_array($result2)) {
							$statistics['sales']['totalprice'] += $result_array2['value'];
						}

						$result2 = select_db('id, type, value', 'vouchers', sprintf('sale_id = %s', quote_smart($result_array['id'])));
						while ($result_array2 = mysql_fetch_array($result2)) {
							$statistics['payments']['vouchers'][$result_array2['type']]['quantity']++;
							$statistics['payments']['vouchers']['total']['quantity']++;
							$statistics['payments']['vouchers'][$result_array2['type']]['value'] += $result_array2['value'];
							$statistics['payments']['vouchers']['total']['value'] += $result_array2['value'];
							$voucher += $result_array2['value'];
						}

						$result2 = select_db('coalesce(ct.name, "other") as cardname, cp.amount', 'card_payments as cp left join card_types as ct on ct.id = cp.card_type_id', sprintf('cp.sale_id = %s', quote_smart($result_array['id'])));

						while ($result_array2 = mysql_fetch_array($result2)) {
							$statistics['payments']['cards']['quantity']++;
							$statistics['payments']['cards']['value'] += $result_array2['amount'];
							$statistics['cards'][$result_array2['cardname']]['quantity']++;
							$statistics['cards'][$result_array2['cardname']]['value'] += $result_array2['amount'];
							$card += $result_array2['amount'];
						}

						$vals = pay($totalprice - $totaldiscount + $totaltax, $cash, $card, $voucher);

						if ($cash > 0) {
							$statistics['payments']['cash']['quantity']++;
							$statistics['payments']['cash']['value'] += $cash - $vals['cash'];
							$statistics['cash']['received'] += $cash;
							$statistics['cash']['returned'] += $vals['cash'];
						}

						if ($vals['voucher'] > 0) {
							$statistics['overpayments']['quantity']++;
							$statistics['overpayments']['value'] += $vals['voucher'];
						}
					}

					$result = select_db('id, type, value, if(sale_id is null, 0, 1) as encashed', 'vouchers as s', $where);

					while ($result_array = mysql_fetch_array($result)) {
						$statistics['vouchers'][$result_array['type']]['quantity']++;
						$statistics['vouchers']['total']['quantity'] += 1;
						$statistics['vouchers'][$result_array['type']]['value'] += $result_array['value'];
						$statistics['vouchers']['total']['value'] += $result_array['value'];
						if ($result_array['encashed'] == 1) {
							$statistics['vouchers'][$result_array['type']]['encashed']['quantity']++;
							$statistics['vouchers']['total']['encashed']['quantity']++;
							$statistics['vouchers'][$result_array['type']]['encashed']['value'] += $result_array['value'];
							$statistics['vouchers']['total']['encashed']['value'] += $result_array['value'];
						}
					}
				}

				$rwhere = str_replace('s.', 'r.', $where);
				get('returns', 'r.id, r.added', 'returns as r', $rwhere, 'items', 'r.id as main_id, ri.id, ri.quantity, si.price, si.tax, si.discount, si.additional_discount', 'returns as r join return_items as ri on ri.return_id = r.id join sale_items as si on ri.sale_item_id = si.id join items as i on i.id = si.item_id', 'r.id in ([main_ids])');
				$statistics['returns']['quantity'] = $totalprice = $totaldiscount = $totaltax = 0;

				if (is_array($_SESSION['results']['returns'])) {
					foreach (array_keys($_SESSION['results']['returns']) as $return_id) {
						$return = $_SESSION['results']['returns'][$return_id];
						if (is_array($return['items'])) {
							foreach (array_keys($return['items']) as $item_id) {
								$item = $return['items'][$item_id];
								$vals = calc($item['price'], $item['discount'], $item['additional_discount'], $item['tax'], $item['quantity']);
								$totalprice += $vals['price'];
								$totaldiscount += $vals['discount'];
								$totaltax += $vals['tax'];
							}
						}
						$statistics['returns']['quantity']++;
					}
				}

				$statistics['returns']['totalprice'] = $totalprice;
				$statistics['returns']['totaldiscount'] = $totaldiscount;
				$statistics['returns']['totaltax'] = $totaltax;
				$statistics['returns']['totalnet'] = $totalprice - $totaldiscount + $totaltax;

				if ($rf != 'r') {

?>
            <div class='s08 mb20 bold'>REPORT TIME FRAME: <?=$_SESSION['dateinfo']['string']?></div>
<?

				}

				if ($reporttype == 'sales') {

?>
            <form class='s1 mb20' method='post'>
              <font class='s08 bold'>SALES PERSON:</font>
              <select name='sales_person_id' onchange='this.form.submit()'>
                <option value='0'>-- all sales persons --</option>
<?

				$result = select_db('distinct l.id, l.username', 'logins as l join sales as s on s.sales_person_id = l.id', sprintf('s.company_id = %s', quote_smart($_SESSION['settings']['company_id'])) . ' order by l.username');

				while ($result_array = mysql_fetch_array($result)) {

?>
                <option value='<?=$result_array['id']?>'<?=$_SESSION['settings']['reports']['sales_person_id'] == $result_array['id'] ? ' selected' : ''?>><?=$result_array['username']?></option>
<?

				}

?>
              </select>
              <input type='hidden' name='clerk_select' value='1'/>
              <input type='hidden' name='report_management' value='1'/>
            </form>
<?

				}

?>
            <table class='mb30 mauto bs2'>
              <tr class='vtop'>
<?

				if ($reporttype == 'sales' || $reporttype == 'summary') {

?>
                <td class='box1'>
                  <table>
                    <tr>
                      <td colspan='2' class='pb10 bold center'>SALES SUMMARY</td>
                    </tr><tr>
                      <td class='left bold s08 p5'>SALES MADE:</td><td class='left'><?=$statistics['sales']['quantity'] > 0 ? $statistics['sales']['quantity'] : 0?></td>
<?

					if ($statistics['sales']['quantity'] > 0) {

?>
                    </tr><tr>
                      <td class='left bold s08 p5'>TOTAL PRICE:</td><td class='left'><?=money($statistics['sales']['totalprice'])?></td>
                    </tr><tr>
                      <td class='left bold s08 p5'>TOTAL DISCOUNT:</td><td class='left'><?=money(-$statistics['sales']['totaldiscount'])?></td>
                    </tr><tr>
                      <td class='left bold s08 p5'>TOTAL TAX:</td><td class='left'><?=money($statistics['sales']['totaltax'])?></td>
                    </tr><tr>
                      <td class='left bold s08 p5'>TOTAL NET:</td><td class='left bold'><?=money($statistics['sales']['totalnet'])?></td>
<?

					}

?>
                    </tr>
                  </table>
                </td>
<?

				}

				if ($reporttype == 'returns' || $reporttype == 'summary') {

?>
                <td class='box2'>
                  <table>
                    <tr>
                      <td colspan='2' class='pb10 bold center'>RETURNS SUMMARY</td>
                    </tr><tr>
                      <td class='left bold s08 p5'>RETURNS RECEIVED:</td><td class='left'><?=$statistics['returns']['quantity'] > 0 ? $statistics['returns']['quantity'] : 0?></td>
<?

					if ($statistics['returns']['quantity'] > 0) {

?>
                    </tr><tr>
                      <td class='left bold s08 p5'>TOTAL PRICE:</td><td class='left'><?=money($statistics['returns']['totalprice'])?></td>
                    </tr><tr>
                      <td class='left bold s08 p5'>TOTAL DISCOUNT:</td><td class='left'><?=money(-$statistics['returns']['totaldiscount'])?></td>
                    </tr><tr>
                      <td class='left bold s08 p5'>TOTAL TAX:</td><td class='left'><?=money($statistics['returns']['totaltax'])?></td>
                    </tr><tr>
                      <td class='left bold s08 p5'>TOTAL NET:</td><td class='left bold'><?=money($statistics['returns']['totalnet'])?></td>
<?

					}

?>
                    </tr>
                  </table>
                </td>
<?

				}

				if ($reporttype == 'summary') {

?>
              </tr><tr class='vtop'>
                <td class='box2'>
                  <table>
                    <tr>
                      <td colspan='2' class='pb10 bold'>PAYMENTS SUMMARY</td>
                    </tr><tr>
                      <td class='left bold s08 p5'>CASH PAYMENTS:</td><td class='left'><?=$statistics['payments']['cash']['quantity'] > 0 ? $statistics['payments']['cash']['quantity'] : 0?></td>
<?

					if ($statistics['payments']['cash']['quantity'] > 0) {

?>
                    </tr><tr>
                      <td class='left bold s08 p5 pl20'>TOTAL VALUE:</td><td class='left bold'><?=money($statistics['payments']['cash']['value'])?></td>
<?

						$totalpayment += $statistics['payments']['cash']['value'];
					}

?>
                    </tr><tr>
                      <td class='left s08 p5 bold'>CARD PAYMENTS:</td><td class='left'><?=$statistics['payments']['cards']['quantity'] > 0 ? $statistics['payments']['cards']['quantity'] : 0?></td>
<?

					if ($statistics['payments']['cards']['quantity'] > 0) {

?>
                    </tr><tr>
                      <td class='left bold s08 p5 pl20'>TOTAL VALUE:</td><td class='left bold'><?=money($statistics['payments']['cards']['value'])?></td>
<?

						$totalpayment += $statistics['payments']['cards']['value'];
					}

?>
                    </tr><tr>
                      <td class='left bold s08 p5'>VOUCHER PAYMENTS:</td><td class='left'><?=$statistics['payments']['vouchers']['total']['quantity'] > 0 ? $statistics['payments']['vouchers']['total']['quantity'] : 0?></td>
<?

					if ($statistics['payments']['vouchers']['total']['quantity'] > 0) {

?>
                    </tr><tr>
                      <td class='left bold s08 p5 pl20'>RETURN VOUCHERS:</td><td class='left'><?=$statistics['payments']['vouchers']['return']['quantity'] > 0 ? $statistics['payments']['vouchers']['return']['quantity'] : 0?></td>
<?

						if ($statistics['payments']['vouchers']['return']['quantity'] > 0) {

?>
                    </tr><tr>
                      <td class='left bold s08 p5 pl40'>TOTAL VALUE:</td><td class='left bold'><?=money($statistics['payments']['vouchers']['return']['value'])?></td>
<?

							$totalpayment += $statistics['payments']['vouchers']['return']['value'];
						}

?>
                    </tr><tr>
                      <td class='left bold s08 p5 pl20'>OVERPAYMENT VOUCHERS:</td><td class='left'><?=$statistics['payments']['vouchers']['overpayment']['quantity'] > 0 ? $statistics['payments']['vouchers']['overpayment']['quantity'] : 0?></td>
<?

						if ($statistics['payments']['vouchers']['overpayment']['quantity'] > 0) {

?>
                    </tr><tr>
                      <td class='left bold s08 p5 pl40'>TOTAL VALUE:</td><td class='left bold'><?=money($statistics['payments']['vouchers']['overpayment']['value'])?></td>
<?

							$totalpayment += $statistics['payments']['vouchers']['overpayment']['value'];
						}
					}

?>
                    </tr><tr>
                      <td class='left bold s08 p5'>NEW OVERPAYMENT VOUCHERS:</td><td class='left'><?=$statistics['overpayments']['quantity'] > 0 ? $statistics['overpayments']['quantity'] : 0?></td>
<?

					if ($statistics['overpayments']['quantity'] > 0) {

?>
                    </tr><tr>
                      <td class='left bold s08 p5 pl20'>TOTAL VALUE:</td><td class='left bold'><?=money($statistics['overpayments']['value'])?></td>
<?

						$totalpayment -= $statistics['overpayments']['value'];
					}

?>
                    </tr><tr>
                      <td class='left bold s08 p5'>PAYMENT NET:</td><td class='left bold'><?=money($totalpayment)?></td>
                    </tr>
                  </table>
                </td>
                <td class='box1'>
                  <table>
                    <tr>
                      <td colspan='2' class='pb10 bold'>VOUCHERS SUMMARY</td>
                    </tr><tr>
                      <td class='left bold s08 p5'>VOUCHERS CREATED:</td><td class='left'><?=$statistics['vouchers']['total']['quantity'] > 0 ? $statistics['vouchers']['total']['quantity'] : 0?></td>
<?

					if ($statistics['vouchers']['total']['quantity'] > 0) {

?>
                    </tr><tr>
                      <td class='left bold s08 p5 pl20'>RETURN VOUCHERS:</td><td class='left'><?=$statistics['vouchers']['return']['quantity'] > 0 ? $statistics['vouchers']['return']['quantity'] : 0?></td>
<?

						if ($statistics['vouchers']['return']['quantity'] > 0) {

?>
                    </tr><tr>
                      <td class='left bold s08 p5 pl40'>TOTAL VALUE:</td><td class='left'><?=money($statistics['vouchers']['return']['value'])?></td>
                    </tr><tr>
                      <td class='left bold s08 p5 pl40'>ENCASHED:</td><td class='left'><?=$statistics['vouchers']['return']['encashed']['quantity'] > 0 ? $statistics['vouchers']['return']['encashed']['quantity'] : 0?></td>
<?

							if ($statistics['vouchers']['return']['encashed']['quantity'] > 0) {

?>
                    </tr><tr>
                      <td class='left bold s08 p5 pl40'>TOTAL ENCASHED VALUE:</td><td class='left'><?=money($statistics['vouchers']['return']['encashed']['value'])?></td>
<?

							}
						}

?>
                    </tr><tr>
                      <td class='left bold s08 p5 pl20'>OVERPAYMENT VOUCHERS:</td><td class='left'><?=$statistics['vouchers']['overpayment']['quantity'] > 0 ? $statistics['vouchers']['overpayment']['quantity'] : 0?></td>
<?

						if ($statistics['vouchers']['overpayment']['quantity'] > 0) {

?>
                    </tr><tr>
                      <td class='left bold s08 p5 pl40'>TOTAL VALUE:</td><td class='left'><?=money($statistics['vouchers']['overpayment']['value'])?></td>
                    </tr><tr>
                      <td class='left bold s08 p5 pl40'>ENCASHED:</td><td class='left'><?=$statistics['vouchers']['overpayment']['encashed']['quantity'] > 0 ? $statistics['vouchers']['overpayment']['encashed']['quantity'] : 0?></td>
<?

							if ($statistics['vouchers']['overpayment']['encashed']['quantity'] > 0) {

?>
                    </tr><tr>
                      <td class='left bold s08 p5 pl40'>TOTAL ENCASHED VALUE:</td><td class='left'><?=money($statistics['vouchers']['overpayment']['encashed']['value'])?></td>
<?

							}
						}
					}

					$totalpayment = 0;

?>
                    </tr>
                  </table>
                </td>
              </tr><tr class='vtop'>
                <td class='box1'>
                  <table>
                    <tr>
                      <td colspan='2' class='pb10 bold'>CASH SUMMARY</td>
                    </tr><tr>
                      <td class='left bold s08 p5'>CASH RECEIVED:</td><td class='left'><?=money($statistics['cash']['received'] > 0 ? $statistics['cash']['received'] : 0)?></td>
                    </tr><tr>
                      <td class='left bold s08 p5'>CASH RETURNED:</td><td class='left'><?=money($statistics['cash']['returned'] > 0 ? $statistics['cash']['returned'] : 0)?></td>
                    </tr><tr>
                      <td class='left bold s08 p5'>CASH NET:</td><td class='left bold'><?=money($statistics['cash']['received'] - $statistics['cash']['returned'] > 0 ? $statistics['cash']['received'] - $statistics['cash']['returned'] : 0)?></td>
                    </tr>
                  </table>
                </td>
                <td class='box2'>
                  <table>
                    <tr>
                      <td colspan='2' class='pb10 bold'>CARDS SUMMARY</td>
<?

					$cardtotal = 0;

					if (is_array($statistics['cards'])) {
						ksort($statistics['cards']);
						foreach (array_keys($statistics['cards']) as $cardname) {

?>
                    </tr><tr>
                      <td class='left bold s08 p5'><?=strtoupper($cardname)?> PAYMENTS:</td><td class='left'><?=$statistics['cards'][$cardname]['quantity']?></td>
                    </tr><tr>
                      <td class='left bold s08 p5 pl20'>TOTAL VALUE:</td><td class='left bold'><?=money($statistics['cards'][$cardname]['value'])?></td>
<?

							$cardtotal += $statistics['cards'][$cardname]['value'];
						}
					}

?>
                    </tr><tr>
                      <td class='left bold s08 p5'>CARD PAYMENTS SUM:</td><td class='left bold'><?=money($cardtotal)?></td>
                    </tr>
                  </table>
                </td>
<?

				}

?>
              </tr>
            </table>
<?

				if ($reporttype == 'sales') {

?>
            <div class='mb15 bold'>SALES LIST</div>
<?

					saleList($where . $spwhere, $orderby);
				}

				if ($reporttype == 'returns') {
?>
            <div class='mb15 bold'>RETURNS LIST</div>
<?

					returnList($rwhere, $orderby);
				}

?>
            <div class='mt30 mb30'><a class='s08 bold' href='javascript: none();' onclick="document.getElementById('drop_settings').value='reports:frame'; document.settings_form.submit();">GO BACK TO PREVIOUS PAGE</a></form>
<?

			}

			break;
		default:
			$_SESSION['report_page'] = 0;
			unset($_SESSION['dateinfo']);

?>
            <div class='s09 mb20'>Select the kind of <?=strtolower($reporttype)?> report you want to be shown:</div>
            <div class='s1 mb15 bold'>DATE RANGE REPORT</div>
            <form method='post' class='mb30'>
              <table>
                <tr>
                  <td>
<?

			show_calendar22('from', 'FROM:');

?>
                  </td><td class='pl30'>
<?

			show_calendar22('till', 'TILL:');

?>
                  </td>
                </tr>
                <tr>
                  <td colspan='2' class='center pt10'>
                    <input type='submit' class='button' value='SHOW REPORT'/>
                    <input type='hidden' name='date_range_report' value='1'/>
                    <input type='hidden' name='report_management' value='1'/>
                  </td>
                </tr>
              </table>
            </form>
            <div class='s1 mb15 bold'>OTHER REPORTS</div>
            <div class='s09 mb10'><a class='bold' href='javascript: none();' onclick="document.getElementById('new_settings').value = 'reports:frame=d'; document.settings_form.submit();">DAILY <?=strtoupper($reporttype)?> REPORT</a></div>
            <div class='s09 mb10'><a class='bold' href='javascript: none();' onclick="document.getElementById('new_settings').value = 'reports:frame=m'; document.settings_form.submit();">MONTHLY <?=strtoupper($reporttype)?> REPORT</a></div>
            <div class='s09 mb10'><a class='bold' href='javascript: none();' onclick="document.getElementById('new_settings').value = 'reports:frame=y'; document.settings_form.submit();">YEARLY <?=strtoupper($reporttype)?> REPORT</a></div>
<?

			if ($reporttype == 'sales') {

?>
            <div class='mt30 mb15 bold'>RECENT SALES</div>
<?

				saleList('', 's.added desc limit 10');
			}

			if ($reporttype == 'returns') {

?>
            <div class='mt30 mb15 bold'>RECENT RETURNS</div>
<?

				returnList('', 'order by r.added desc limit 10');
			}

?>
            <div class='mt30 mb30'><a class='s08 bold' href='javascript: none();' onclick="document.getElementById('drop_settings').value='reports:type'; document.settings_form.submit();">GO BACK TO REPORTS MAIN PAGE</a></form>
<?

	}
}
?>