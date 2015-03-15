<?php
require_once('shop_db_functions.php');

function show_return_items($return, $word = '', $classes = '') {
	if ($word == '') { $word = 'return'; }

	if (is_array($return['items']) && count($return['items']) > 0) {

?>
            <form method='post' name='return_edit_form'>
              <table class='mt15 b1st b1sl<?=$classes == '' ? '' : " $classes"?>'>
                <tr class='bctrt'>
                  <td class='s09 bold p5 b1sb b1sr' colspan='20'>RETURN ITEMS</td>
                </tr><tr class='bctrt'>
                  <td class='s08 bold p5 b1sb b1sr'>#</td>
<?

		if ($bool = ($_SESSION['settings']['site'] == 'returns' && !isset($return['encash']) && !isset($return['receipt_id'])) || ($_SESSION['settings']['site'] == 'exchanges' && !isset($_SESSION['exchange']['encash']) && !isset($_SESSION['receipt_id']) && $_SESSION['settings']['exchanges']['manage_type'] == 'returns')) {

?>
                  <td class='s08 bold p5 b1sb b1sr'>SALE DATE<br/>AND TIME</td>
<?

		}

?>
                  <td class='s08 bold p5 b1sb b1sr'>PIC</td>
                  <td class='s08 bold p5 b1sb b1sr'>BRAND<br/>NAME</td>
                  <td class='s08 bold p5 b1sb b1sr'>ITEM<br/>NAME</td>
                  <td class='s08 bold p5 b1sb b1sr'>STYLE<br/> NR.</td>
                  <td class='s08 bold p5 b1sb b1sr'>PRICE</td>
                  <td class='s08 bold p5 b1sb b1sr'>ADDITIONAL<br/>DISCOUNT</td>
                  <td class='s08 bold p5 b1sb b1sr'>TAX</td>
                  <td class='s08 bold p5 b1sb b1sr'>QTY</td>
                  <td class='s08 bold p5 b1sb b1sr'>SUBTOTAL</td>
                  <td class='s08 bold p5 b1sb b1sr'>DETAILS?</td>
<?

		if ($bool) {

?>
                  <td class='s08 bold p5 b1sb b1sr'>DELETE?</td>
<?

		}

?>
                </tr>
<?

		$rownum = 1;

		foreach (array_keys($return['items']) as $sale_item_id) {
			$item = $return['items'][$sale_item_id];
			$result = select_db('iim.image_id', 'item_image_mappings as iim join items as i on i.id = iim.id join categories as c on c.id = i.category_id join items as i2 on i2.number = i.number and i2.category_id = i.category_id', sprintf('i2.id = %s and c.company_id = %s and coalesce(iim.deleted, i.deleted, c.deleted, i2.deleted) is null order by iim.default_group_image desc, i.id asc, iim.default_item_image desc limit 1', quote_smart($item['item_id']), quote_smart($_SESSION['settings']['company_id'])));
			$imageid = 0;
			if ($result_array = mysql_fetch_array($result)) { $imageid = $result_array['image_id']; }
			$vals = calc($item['price'], $item['discount'], $item['additional_discount'], $item['tax'], $item['quantity']);
			$item['finalprice'] = ($vals['price'] - $vals['odiscount']) / $item['quantity'];

?>
                <tr class='bctr1<?=$rownum % 2 == 1 ? 'a' : 'b'?>'>
                  <td class='s08 p5 b1sb b1sr'><?=$rownum++?></td>
<?

			if ($bool) {

?>
                  <td class='s08 p5 b1sb b1sr'><a href='javascript: none();' onclick='document.getElementById("return_edit_var").value = "<?=$item['sale_receipt_id']?>"; document.getElementById("return_edit_action").value = "show_sale"; document.return_edit_form.submit();'><?=$item['sale_date']?></a></td>
<?

			}

?>
                  <td class='center s08 p1 b1sr b1sb lh0'><img class='m0<? if ($imageid > 0) { print ' mp'; } ?>' src='showimage.php?id=<?=$imageid?>&w=50&h=40'<? if ($imageid > 0) { ?> onclick='window.open("showimage.php?id=<?=$imageid?>&w=600&h=600", "_new", "innerHeight=620,innerWidth=620,scrollbars=no,toolbar=no,resizable=no,menubar=0");'<? } ?>/></td>
                  <td class='s08 p5 b1sb b1sr'><?=$item['brand']?></td>
                  <td class='s08 p5 b1sb b1sr'><?=$item['name']?></td>
                  <td class='s08 p5 b1sb b1sr'><?=$item['number']?></td>
                  <td class='s08 p5 b1sb b1sr'><?=money($item['finalprice'])?><? if ($item['discount'] > 0) { ?><br/>(-<?=$item['discount']?>%)<? } ?></td>
                  <td class='s08 p5 b1sb b1sr'><?=$item['additional_discount']?>%</td>
                  <td class='s08 p5 b1sb b1sr'><?=$item['tax']?>%</td>
                  <td class='s08 p5 b1sb b1sr'><?=$item['quantity']?></td>
                  <td class='s08 p5 b1sb b1sr'><?=money($vals['total'])?></td>
                  <td class='p1 b1sr b1sb'><form method='post'><input class='button' type='submit' value='DETAILS'/><input type='hidden' name='itemid' value='<?=$item['item_id']?>'/><input type='hidden' name='edititem' value='1'/></form></td>
<?

			if ($bool) {

?>
                  <td class='b1sb b1sr'><input class='button' type='button' value='DELETE' onclick='if (confirm("Do you really want to delete all return items of this sale item?")) { document.getElementById("return_edit_var").value = <?=$sale_item_id?>; document.return_edit_form.submit(); }'></td>
<?

			}

?>
                </tr>
<?

		}

?>
              </table>
              <input type='hidden' name='action' id='return_edit_action' value='delete_item'/>
              <input type='hidden' name='var' id='return_edit_var' value='0'/>
              <input type='hidden' name='return_management' value='1'/>
            </form>
<?

	} else {

?>
            <div class='mt15 s09'>The <?=$word?> does not include any return items.</div>
<?

	}
}
function calc_sale_totals(&$sale) {
	unset($sale['totals']);
        $sale['totals']['price'] = $sale['totals']['tax'] = $sale['totals']['discount'] = 0;
	if (is_array($sale['basket']['items'])) {
		foreach (array_keys($sale['basket']['items']) as $key) {
			$item = &$sale['basket']['items'][$key];
			if ($item['quantity'] > 0) {
                                $additional_discount = isset($item['additional_discount']) ? $item['additional_discount'] : 0;
				$vals = calc($item['price'], $item['discount'], $additional_discount, !isset($sale['no_tax']) ? $item['tax'] : 0, $item['quantity']);
				$item['finalprice'] = ($vals['price'] - $vals['odiscount']) / $item['quantity'];
				$sale['totals']['price'] += $vals['price'];
				$sale['totals']['tax'] += $vals['tax'];
				$sale['totals']['discount'] += $vals['discount'];
			}
		}
	}
        if ( isset($sale['basket']['gift_certificates']) && is_array($sale['basket']['gift_certificates']) ) {
                foreach (array_keys($sale['basket']['gift_certificates']) as $value) {
                        $sale['totals']['price'] += $value * count($sale['basket']['gift_certificates'][$value]);
                }
        }

	$sale['totals']['total'] = $sale['totals']['price'] - $sale['totals']['discount'] + $sale['totals']['tax'];
}
function calc_return_totals(&$return) {
	$totals = array();
	unset($return['totals']);
	if (is_array($return['items'])) {
		foreach (array_keys($return['items']) as $sale_item_id) {
			$item = $return['items'][$sale_item_id];
			$vals = calc($item['price'], $item['discount'], $item['additional_discount'], $item['tax'], $item['quantity']);
			$return['totals']['price'] += $vals['price'];
			$return['totals']['tax'] += $vals['tax'];
			$return['totals']['discount'] += $vals['discount'];
		}
	}

	$return['totals']['total'] = $return['totals']['price'] - $return['totals']['discount'] + $return['totals']['tax'];
}
function calc_exchange_totals(&$exchange, &$sale, &$return) {
	foreach (array('price', 'discount', 'tax') as $key) {
		$exchange['totals'][$key] = $sale['totals'][$key] - $return['totals'][$key];
	}

	$exchange['totals']['total'] = $exchange['totals']['price'] - $exchange['totals']['discount'] + $exchange['totals']['tax'];
}
function save_sale(&$sale, $pay = 1, $receipt_id = '') {
	if ($receipt_id == '') {
		$receipt_id = date('dymsHi');
	}

	if (!isset($sale['cash_payment'])) {
		$sale['cash_payment'] = 0;
	}

	$sale_id = insert_db('sale', array('company_id' => $_SESSION['settings']['company_id'], 'login_id' => $_SESSION['settings']['login_id'], 'sales_person_id' => $_POST['sales_person_id'], 'receipt_id' => $receipt_id, 'saletype' => $_SESSION['settings']['pagetype'], 'taxed' => isset($sale['no_tax']) ? 0 : 1, 'paid' => $sale['cash_payment'], 'customer_id' => $sale['customer_id'], 'added' => 0));

	if (!empty($_POST['sale_note'])) {
		insert_db('sale_note', array('sale_id' => $sale_id, 'text' => $_POST['sale_note']));
		$sale['note'] = $_POST['sale_note'];
	}

	if ($_POST['sales_person_id'] > 0) {
		$result = select_db('username', 'logins', sprintf('id = %s', quote_smart($_POST['sales_person_id'])));
		if ($result_array = mysql_fetch_array($result)) {
			$sale['clerk'] = $result_array['username'];
		}
	}

	if (is_array($sale['basket']['items'])) {
		foreach (array_keys($sale['basket']['items']) as $key) {
			$item = &$sale['basket']['items'][$key];

			if (!isset($item['additional_discount'])) {
				$item['additional_discount'] = 0;
			}

			if ($item['quantity'] > 0) {
				insert_db('sale_item', array('sale_id' => $sale_id, 'item_id' => $key, 'price' => $item['price'], 'discount' => $item['discount'], 'additional_discount' => $item['additional_discount'], 'tax' => $item['tax'], 'quantity' => $item['quantity']));
			}
		}
	}

	update_db('sale_items as si join items as i on i.id = si.item_id join categories as c on c.id = i.category_id', array('i.quantity' => '[i.quantity - si.quantity]'), sprintf('c.company_id = %s and c.type = %s and c.deleted is null and i.deleted is null and si.sale_id = %s', quote_smart($_SESSION['settings']['company_id']), quote_smart($_SESSION['settings']['pagetype']), quote_smart($sale_id)));

	if (is_array($sale['basket']['gift_certificates'])) {
		foreach (array_keys($sale['basket']['gift_certificates']) as $value) {
			if (is_array($sale['basket']['gift_certificates'][$value])) {
				foreach ($sale['basket']['gift_certificates'][$value] as $barcode) {
					insert_db('vouchers', array('company_id' => $_SESSION['settings']['company_id'], 'origin_id' => $sale_id, 'barcode' => $barcode, 'type' => 'gift_certificate', 'value' => $value, 'added' => 0));
				}
			}
		}
	}

	if ($pay == 1) {
		if (is_array($sale['card_payments'])) {
			foreach (array_keys($sale['card_payments']) as $id) {
				insert_db('card_payment', array('sale_id' => $sale_id, 'card_type_id' => $sale['card_payments'][$id]['type'], 'amount' => $sale['card_payments'][$id]['amount']));
			}
		}

		if (is_array($sale['voucher_payments']) && count(array_keys($sale['voucher_payments'])) > 0) {
			update_db('voucher', array('deleted' => '[utc_timestamp()]', 'sale_id' => $sale_id), sprintf('company_id = %s and id in (%s)', quote_smart($_SESSION['settings']['company_id']), implode(array_keys($sale['voucher_payments']), ',')));
		}

		if ($sale['change']['voucher'] > 0) {
			insert_db('voucher', array('company_id' => $_SESSION['settings']['company_id'], 'origin_id' => $sale_id, 'barcode' => $receipt_id, 'type' => 'overpayment', 'value' => $sale['change']['voucher'], 'added' => 0));
		}
	}

	$sale['receipt_id'] = $receipt_id;

	return $sale_id;
}
function save_return(&$return, $pay = 1, $receipt_id = '') {
	$receipt_id = date('dymsHi');

	$return_id = insert_db('return', array('company_id' => $_SESSION['settings']['company_id'], 'login_id' => $_SESSION['settings']['login_id'], 'barcode' => $receipt_id, 'added' => 0));

	insert_db('voucher', array('company_id' => $_SESSION['settings']['company_id'], 'origin_id' => $return_id, 'barcode' => $receipt_id, 'type' => 'return', 'value' => $return['totals']['total'], 'added' => 0));

	return_items(0, $return['items'], $return_id, $receipt_id);

	$return['receipt_id'] = $receipt_id;

	return $return_id;
}
function return_items($sale_id, &$items = array('all' => 1), $return_id = 0, $receipt_id = 0) {
	if (!is_array($items)) { return; }
	$return_items = array();

	if ($items['all'] != 1) {
		foreach (array_keys($items) as $id) {
			if ($id > 0 && $items[$id] > 0) {
				$result = select_db(sprintf('least(si.quantity - coalesce(ri.quantity, 0), %s) as quantity', quote_smart($items[$id]['quantity'])), 'sales as s join sale_items as si on si.sale_id = s.id left join return_items as ri on ri.sale_item_id = si.id', sprintf('si.id = %s and s.company_id = %s and s.deleted is null', quote_smart($id), quote_smart($_SESSION['settings']['company_id'])));
				if ($result_array = mysql_fetch_array($result)) {
					if ($result_array['quantity'] > 0) { $return_items[$id] = $result_array['quantity']; }
				} else {
					unset($item[$id]);
				}
			}
		}
	} else {
		$result = select_db('si.id, si.quantity - coalesce(sum(ri.quantity), 0) as quantity', 'sale_items as si left join return_items as ri on ri.sale_item_id = si.id', sprintf('si.sale_id = %s group by si.id', quote_smart($sale_id)));
		while ($result_array = mysql_fetch_array($result)) {
			if ($result_array['quantity'] > 0) { $return_items[$result_array['id']] = $result_array['quantity']; }
		}
	}

	if (count($return_items) < 1) { return; }

	$item_ids = implode(',', array_keys($return_items));
	$result = select_db('si.id, i.id as item_id, i.deleted as item_deleted, i.supplier_id, s.deleted as supplier_deleted, i.brand_id, b.deleted as brand_deleted, i.category_id, c.deleted as category_deleted', 'sale_items as si join items as i on i.id = si.item_id left join suppliers as s on s.id = i.supplier_id left join brands as b on b.id = i.brand_id left join categories as c on c.id = i.category_id', sprintf('si.id in (%s)', $item_ids));

	while ($result_array = mysql_fetch_array($result)) {
		$sale_item_id = $result_array['id'];
		$item_id = $result_array['item_id'];
		$supplier_id = $result_array['supplier_id'];
		$brand_id = $result_array['brand_id'];
		$category_id = $result_array['category_id'];

		if ($result_array['supplier_deleted'] !== null) {
			$supplier_id = insert_db('supplier', array('old_id' => $result_array['supplier_id']));
		}

		if ($result_array['brand_deleted'] !== null) {
			$brand_id = insert_db('brand', array('old_id' => $result_array['brand_id']));
		}

		if ($result_array['category_deleted'] !== null) {
			$category_id = insert_db('category', array('old_id' => $result_array['category_id']));
		}

		if ($result_array['item_deleted'] != null) {
			$id = insert_db('items', array('old_id' => $item_id, 'supplier_id' => $supplier_id, 'brand_id' => $brand_id, 'category_id' => $category_id, 'quantity' => $return_items[$sale_item_id]));
			update_db('sale_item', array('item_id' => $id), sprintf('id = %s', quote_smart($sale_item_id)));
		} else {
			update_db('items as i join sale_items as si on si.item_id = i.id', array('i.quantity' => sprintf('[i.quantity + %s]', quote_smart($return_items[$sale_item_id]))), sprintf('si.id = %s', quote_smart($sale_item_id)));
		}

		if ($return_id > 0) {
			insert_db('return_item', array('return_id' => $return_id, 'sale_item_id' => $result_array['id'], 'quantity' => $return_items[$result_array['id']]));
		}
	}
}
?>