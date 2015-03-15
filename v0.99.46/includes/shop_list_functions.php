<?php
function itemList($where_statement = "", $dynamic = 0) {
	if ($_SESSION['settings']['manager'] == 1) {

?>
                    <form method='post' name='deleteitemform'>
                      <input type='hidden' name='delete_item_id' id='delete_item_id' value='0'/>
                      <input type='hidden' name='deleteitem' value='1'/>
                    </form>
                    <form method='post' name='templateadditemform'>
                      <input type='hidden' name='template_add_item_id' id='template_add_item_id' value='0'/>
                      <input type='hidden' name='gotoadditem' value='1'/>
                    </form>
                    <div class='mb30'>
<?

	}

	if (!isset($_SESSION['it_list_start'])) { $_SESSION['it_list_start'] = 1; }
	if (isset($_SESSION['settings']['itemmgnt']['style_edit_item_id'])) { $style_type = isset($_SESSION['edit']['style']['old']) ? 'old' : 'new'; }

	$result = select_db('c.id as cid, c.name as categoryname, c.attribute1 as catt1, c.attribute2 as catt2, d.id as did, d.name as departmentname, i.id, b.id as bid, b.name as brand, i.name, i.number, i.style, i.attribute1, i.attribute2, i.barcode, i.buy_price, i.price, i.discount, i.quantity', 'items as i join categories as c on (i.category_id = c.id and c.deleted is null) left join departments as d on i.department_id = d.id left join brands as b on (b.id = i.brand_id and b.deleted is null)', sprintf('c.company_id = %s and c.type = %s and i.deleted is null%s%s%s%s%s%s%s order by c.name asc, c.id asc, i.number asc, b.name asc, i.name asc, i.style asc, i.attribute1 asc, i.attribute2 asc', quote_smart($_SESSION['settings']['company_id']), quote_smart($_SESSION['settings']['pagetype']), $_SESSION['settings']['itemmgnt']['category_id'] > 0 && !isset($_SESSION['settings']['ka']) ? sprintf(' and c.id = %s', quote_smart($_SESSION['settings']['itemmgnt']['category_id'])) : '', $_SESSION['settings']['itemmgnt']['department_id'] > 0 && !isset($_SESSION['settings']['ka']) ? sprintf(' and d.id = %s', quote_smart($_SESSION['settings']['itemmgnt']['department_id'])) : '', $_SESSION['settings']['itemmgnt']['brand_id'] > 0 && !isset($_SESSION['settings']['ka']) ? sprintf(' and b.id = %s', quote_smart($_SESSION['settings']['itemmgnt']['brand_id'])) : '', $_SESSION['settings']['itemmgnt']['number_id'] != '' && !isset($_SESSION['settings']['ka']) ? sprintf(' and i.number = %s', quote_smart($_SESSION['settings']['itemmgnt']['number_id'])) : '', $_SESSION['settings']['itemmgnt']['style_edit_item_id'] > 0 ? sprintf(' and i.category_id = %s and i.number = %s and i.name = %s and i.brand_id = %s and i.department_id = %s and i.style = %s', quote_smart($_SESSION['edit']['style'][$style_type]['category_id']), quote_smart($_SESSION['edit']['style'][$style_type]['number']), quote_smart($_SESSION['edit']['style'][$style_type]['name']), quote_smart($_SESSION['edit']['style'][$style_type]['brand_id']), quote_smart($_SESSION['edit']['style'][$style_type]['department_id']), quote_smart($_SESSION['edit']['style'][$style_type]['style'])) : '',!empty($where_statement) ? " and $where_statement" : '', isset($_SESSION['settings']['itemmgnt']['highlight_item_number']) ? sprintf(' and i.number = %s', quote_smart($_SESSION['settings']['itemmgnt']['highlight_item_number'])) : ''));

	if (isset($_SESSION['settings']['itemmgnt']['highlight_item_id'])) {
		$highlight_item = mysql_fetch_array(select_db('category_id, number, name, brand_id, department_id, style', 'items', sprintf('id = %s', quote_smart($_SESSION['settings']['itemmgnt']['highlight_item_id']))));
	}

	if (mysql_num_rows($result) == 0) {
		unset($_SESSION['totalbuy']);
		unset($_SESSION['totalprice']);
		unset($_SESSION['totalquantity']);

?>
                      - NONE -
                    </div>
<?

	} else {

?>
                      <form name='style_edit_form' method='post'>
                        <input type='hidden' name='item_id' id='style_edit_item_id' value='0'/>
                        <input type='hidden' name='style_edit' value='1'/>
                        <input type='hidden' name='item_group_management' value='1'/>
                      </form>
<?

		$cid = 0;
		$totalbuy = $totalprice = $totalquantity = 0;

		$start = 1;
		if ($_SESSION['settings']['itemmgnt']['page'] > 0 && !isset($_SESSION['settings']['itemmgnt']['style_edit_item_id'])) { $start = 1 + ($_SESSION['settings']['itemmgnt']['page'] - 1) * $_SESSION['preferences']['results_per_page']; }
		$stop = $start - 1 + $_SESSION['preferences']['results_per_page'];
		$growid = 0;
		if (isset($_SESSION['settings']['itemmgnt']['style_edit_item_id'])) {
			unset($_SESSION['edit']['style']['ids']);
		}

		while ($result_array = mysql_fetch_array($result)) {
			if (isset($_SESSION['settings']['itemmgnt']['style_edit_item_id'])) {
				$_SESSION['edit']['style']['ids'][$result_array['id']] = 1;
			}
			$sub = 0;
			if (isset($last) && $last['cid'] == $result_array['cid'] && $last['number'] == $result_array['number'] && $last['name'] == $result_array['name'] && $last['bid'] == $result_array['bid'] && $last['itemmgnt']['department_id'] == $result_array['department_id'] && $last['style'] == $result_array['style'] ) {
				$sub = 1;
			} else {
				$growid++;
			}

			if ($cid != $result_array['cid']) {
				$rowid = $rowid2 = 0;

				if ($growid >= $start && $growid <= $stop) {
					if ($cid > 0) {


?>
                      </table>
<?

					}

?>
                      <table width='800' class='<?=$cid > 0 ? 'mt30' : ''?>'>
                        <tr><td></td><td class='bctrt2 bold s08 p5 b1sl b1st b1sr b1sb' colspan='<?=$_SESSION['settings'][$_SESSION['settings']['login_id']]['level'] >=0 ? 9 : 7?>'><?=$result_array['categoryname']?></td></tr>
<?

					$cid = $result_array['cid'];
				}
			}

			$vals = calc($result_array['price'], $result_array['discount'], 0, 0, $result_array['quantity']);
			$totalbuy += $result_array['quantity'] * $result_array['buy_price'];
			$totalprice += $vals['price'];
			$totalquantity += $result_array['quantity'];
			$vals = calc($result_array['price'], $result_array['discount'], 0, 0);

			if ($sub == 0) {
				$bcclass = 'bctrt' . ($rowid++ % 2 == 0 ? 'a ' : 'b ');

				if ($growid >= $start && $growid <= $stop) {
					$rowid2 = 0;
					$result2 = select_db('iim.image_id,iim.image_db_id', 'item_image_mappings as iim join items as i on i.id = iim.id join categories as c on c.id = i.category_id join items as i2 on i2.number = i.number and i2.category_id = i.category_id', sprintf('i2.id = %s and c.company_id = %s and coalesce(iim.deleted, i.deleted, c.deleted, i2.deleted) is null order by iim.default_group_image desc, i.id asc, iim.default_item_image desc limit 1', quote_smart($result_array['id']), quote_smart($_SESSION['settings']['company_id'])));
					$imageid = 0;
					if ($result_array2 = mysql_fetch_array($result2)) { $imageid = $result_array2['image_id']; $image_db_id = $result_array2['image_db_id']; }
					$result3 = select_db('sum(quantity) as sumqty', 'items as i join categories as c on c.id = i.category_id', sprintf('i.number = %s and c.company_id = %s and i.category_id = %s and i.deleted is null and c.deleted is null', quote_smart($result_array['number']), quote_smart($_SESSION['settings']['company_id']), quote_smart($result_array['cid'])));
					$result_array3 = mysql_fetch_array($result3);
					$openthis = isset($highlight_item) && $result_array['cid'] == $highlight_item['category_id'] && $result_array['number'] == $highlight_item['number'] && $result_array['name'] == $highlight_item['name'] && $result_array['bid'] == $highlight_item['brand_id'] && $result_array['did'] == $highlight_item['department_id'] && $result_array['style'] == $highlight_item['style'] ? 1 : 0;
					if (isset($_SESSION['settings']['itemmgnt']['open_highlight']) && $_SESSION['settings']['itemmgnt']['open_highlight'] > 0) {
						$openthis = 1;
						unset($_SESSION['settings']['itemmgnt']['open_highlight']);
					}

?>
                        <tr>
                          <td width='20'><input class='b0 bcnone' type='button' value='<?=$openthis == 1 ? '-' : '+'?>' onclick='if (this.value == "+") { this.value = "-"; i = 0; while (document.getElementById("row_<?=$cid?>_<?=$rowid?>_" + i)) { document.getElementById("row_<?=$cid?>_<?=$rowid?>_" + i).style.visibility = "visible"; i++; }} else { this.value = "+"; i = 0; while (document.getElementById("row_<?=$cid?>_<?=$rowid?>_" + i)) { document.getElementById("row_<?=$cid?>_<?=$rowid?>_" + i).style.visibility = "collapse"; i++; }};'/></td>
                          <td width='20' class='bctrt2 s08 p5 b1sl b1sb'>#<?=$rowid?></td>
                          <td width='100' class='<?=$bcclass?> center s08 p1 b1sl b1sr b1sb lh0'><img class='m0<? if ($imageid > 0) { print ' mp'; } ?>' src='showimage.php?id=<?=$imageid?>&image_db_id=<?=$image_db_id?>&w=100&h=80'<? if ($imageid > 0) { ?> onclick='window.open("showimage.php?id=<?=$imageid?>&image_db_id=<?=$image_db_id?>&w=600&h=600", "_new", "innerHeight=620,innerWidth=620,scrollbars=no,toolbar=no,resizable=no,menubar=0");'<? } ?>/></td>
                          <td class='<?=$bcclass?> left vtop p5 b1sr b1sb' colspan='<?=$_SESSION['settings'][$_SESSION['settings']['login_id']]['level'] >=0 ? 7 : 5?>'>
                            <table class='wp100'>
                              <tr>
                                <td class='left p2 wp30 s08'><span class='bold'>STYLE:&nbsp;</span><?=$result_array['number']?></td>
                                <td class='left p2 wp40 s08'><span class='bold'>NAME:&nbsp;</span><?=$result_array['name']?></td>
                                <td class='left p2 wp30 s08'><span class='bold'>BRAND:&nbsp;</span><?=$result_array['brand']?></td>
                              </tr>
                              <tr>
                                <td class='left p2 s08' colspan='2'><span class='bold'>DEPARTMENT:&nbsp;</span><?=$result_array['departmentname']?></td>
                                <td class='left p2 s08'><span class='bold'>TOTAL QUANTITY:&nbsp;</span><?=$result_array3['sumqty']?></td>
                              </tr>
                              <tr>
                                <td class='left p2' colspan='3'>
                                  <table class='wp100'>
                                    <tr class='vtop'>
                                      <td class='left s08 bold p0 m0'>DESCRIPTION:&nbsp;</td>
                                      <td class='left s08 wp100 p0 m0'><?=$result_array['style']?></td>
                                      <td class='right m0 p0'><input type='button' class='button<?=isset($_SESSION['settings']['itemmgnt']['style_edit_item_id']) || isset($_SESSION['settings']['ka']) || isset($_SESSION['settings'][$_SESSION['settings']['site']]['show_item_id']) || isset($_SESSION['settings']['itemmgnt']['highlight_item_id']) || $_SESSION['settings'][$_SESSION['settings']['login_id']]['level'] < 2 ? 'disabled' : ''?>' value='EDIT' onclick='document.getElementById("style_edit_item_id").value = "<?=$result_array['id']?>"; document.style_edit_form.submit();'<?=isset($_SESSION['settings']['itemmgnt']['style_edit_item_id']) || isset($_SESSION['settings']['ka']) || isset($_SESSION['settings'][$_SESSION['settings']['site']]['show_item_id']) || isset($_SESSION['settings']['itemmgnt']['highlight_item_id']) ? ' disabled' : ''?>/></td>
                                    </tr>
                                  </table>
                                </td>
                              </tr>
                            </table>
                          </td>
                        </tr>
                        <tr id='row_<?=$cid?>_<?=$rowid . '_' . $rowid2?>' class='vmiddle'<?=$openthis == 0 ? " style='visibility: collapse;'" : ''?>>
                          <td></td>
                          <td></td>
                          <td class='bctrt2 bold s08 p5 b1sl b1sr b1sb'>PIC</td>
                          <td class='bctrt2 bold s08 p5 b1sr b1sb'><?=strtoupper($result_array['catt1'])?></td>
                          <td class='bctrt2 bold s08 p5 b1sr b1sb'><?=strtoupper($result_array['catt2'])?></td>
                          <td class='bctrt2 bold s08 p5 b1sr b1sb'>BARCODE</td>
                          <td width='60' class='bctrt2 bold s08 p5 b1sr b1sb'>PRICE</td>
                          <td width='40' class='bctrt2 bold s08 p5 b1sr b1sb'>QTY</td>
<?

					if ($_SESSION['settings'][$_SESSION['settings']['login_id']]['level'] >= 0) {

?>
                          <td width='80' class='bctrt2 bold s08 p5 b1sr b1sb'>EDIT?</td>
                          <td width='80' class='bctrt2 bold s08 p5 b1sr b1sb'>DELETE?<br/>LABEL?</td>
<?

					}

?>
                        </tr>
<?

				}
			}

			$last['cid'] = $result_array['cid'];
			$last['number'] = $result_array['number'];
			$last['name'] = $result_array['name'];
			$last['bid'] = $result_array['bid'];
			$last['itemmgnt']['department_id'] = $result_array['itemmgnt']['department_id'];
			$last['style'] = $result_array['style'];

			if ($growid >= $start && $growid <= $stop) {
				$bcclass = 'bctr1' . ($rowid2++ % 2 == 0 ? 'a ' : 'b ');
				if ($_SESSION['settings']['itemmgnt']['highlight_item_id'] > 0 && $_SESSION['settings']['itemmgnt']['highlight_item_id'] == $result_array['id']) { $bcclass = 'bctrhl '; }
				$result2 = select_db('image_id,image_db_id', 'item_image_mappings', sprintf('id = %s and deleted is null order by default_item_image desc, default_group_image desc, added asc limit 1', quote_smart($result_array['id'])));
				$imageid = 0;
				if ($result_array2 = mysql_fetch_array($result2)) { $imageid = $result_array2['image_id']; $image_db_id = $result_array2['image_db_id']; }
				$description = $result_array['style'];
				if (strlen($description) > 50) { $description = substr($description, 0, 50) . '...'; }

?>

                  <tr id='row_<?=$cid?>_<?=$rowid . '_' . $rowid2?>' class='vmiddle'<?=$openthis == 0 ? " style='visibility: collapse;'" : ''?>>
                    <td></td>
                    <td></td>
                    <td class='<?=$bcclass?>center s08 p1 b1sl b1sr b1sb lh0'><img class='m0<? if ($imageid > 0) { ?> mp<? } ?>' src='showimage.php?id=<?=$imageid?>&image_db_id=<?=$image_db_id?>&w=100&h=80'<? if ($imageid > 0) { ?> onclick='window.open("showimage.php?id=<?=$imageid?>&image_db_id=<?=$image_db_id?>&w=600&h=600", "_new", "innerHeight=620,innerWidth=620,scrollbars=no,toolbar=no,resizable=no,menubar=0");<? } ?>'/></td>
                    <td class='<?=$bcclass?>left s08 p5 b1sr b1sb'><?=$result_array['attribute1']?></td>
                    <td class='<?=$bcclass?>left s08 p5 b1sr b1sb'><?=$result_array['attribute2']?></td>
                    <td class='<?=$bcclass?>left s08 p5 b1sr b1sb'><?=$result_array['barcode']?></td>
                    <td class='<?=$bcclass?>right s08 p5 b1sr b1sb'><? if ($result_array['discount'] > 0) { ?>(-<?=$result_array['discount']?>%) <? } ?><?=money($vals['price'])?></td>
                    <td class='<?=$bcclass?>right s08 p5 b1sr b1sb'><?=$result_array['quantity']?></td>
<?

				if ($_SESSION['settings'][$_SESSION['settings']['login_id']]['level'] >=0) {

?>
                    <td class='<?=$bcclass?>p1 b1sr b1sb'><input class='button' type='submit' value='EDIT' onclick="Inventory_Items_Edit_Item(<?=$result_array['id']?>)"></td>
<?

					if ($result_array['quantity'] == 0) {

?>
                    <td class='<?=$bcclass?>p1 b1sr b1sb'><input class='button<?=isset($_SESSION['settings']['itemmgnt']['style_edit_item_id']) || isset($_SESSION['settings']['itemmgnt']['highlight_item_id']) ? 'disabled' : ''?>' type='button' onclick='if (!confirm("Do you really want to delete this item?")) { return false; }; document.getElementById("delete_item_id").value = <?=$result_array['id']?>; document.deleteitemform.submit();' value='DELETE'<?=isset($_SESSION['settings']['itemmgnt']['style_edit_item_id']) ? ' disabled' : ''?>/></td>
<?

					} else {

?>
                    <td class='<?=$bcclass?>p1 b1sr b1sb'><input class='button<?=isset($_SESSION['settings']['itemmgnt']['style_edit_item_id']) ? 'disabled' : ''?>' type='button' value='LABEL' onclick='label(<?=$result_array['id']?>, <?=$_SESSION['preferences']['label_width']?>);'<?=isset($_SESSION['settings']['itemmgnt']['style_edit_item_id']) ? ' disabled' : ''?>/></td>

<?

					}
				}

?>
                  </tr>
                  <input type='hidden' name='itemid' value='<?=$result_array['id']?>'/>
                  <input type='hidden' name='edititem' value='1'/>

<?

			}
		}

		$_SESSION['totalbuy'] = $totalbuy;
		$_SESSION['totalprice'] = $totalprice;
		$_SESSION['totalquantity'] = $totalquantity;

?>
              </table>
<?

		if ($growid > $_SESSION['preferences']['results_per_page']) {
?>
                        <script>document.getElementById('resultinfo').innerHTML = 'Showing results <?=$start . '-' . ($stop > $growid ? $growid : $stop)?>:'; document.getElementById('resultinfo').style.display = '';</script>
<?

			$cstart = $start;
			$start = 1;

?>
                        <form method='post' name='choose_result_page_form'><input type='hidden' name='result_page' id='result_page' value='1'><input type='hidden' name='choose_result_page' value='1'/></form>
                        <? $p = 0; while ($start <= $growid) { $p++; $stop = $start - 1 + $_SESSION['preferences']['results_per_page']; if ($stop > $growid) { $stop = $growid; }; if ($start == $cstart) { print "<span class='ml3 mr3 s08'>[$start-$stop]</span>"; } else { print "<a href='javascript: none();' class='ml3 mr3 s08 bold' onclick='document.getElementById(\"result_page\").value = $p; document.choose_result_page_form.submit();'>[$start-$stop]</a>"; }; $start += $_SESSION['preferences']['results_per_page']; } ?>
<?

		}

?>
            </div>
<?

	}

	return mysql_num_rows($result);
}
function itemDetails($add = 0, $edit = 0, $template_id = 0) {
	$show = 0;
	if ($add + $edit == 0) { $show = 1; }

?>
            <div class='s1<? if ($add + $edit > 0) { ?>3<? } ?> bold mb20 mt10'><?=$add > 0 ? "ADD ITEMS" : ($edit > 0 ? "EDIT ITEM" : "ITEM DETAILS")?></div>
<?

	if ($add > 0) {
		if (isset($_SESSION['additembarcode'])) {

?>
            <div class='red mb20'>This barcode does not yet exist. You can add a new item with this barcode now.</div>
<?

		}

		if (!isset($_SESSION['settings']['ka'])) {
			unset($_SESSION['added']);
		}

		if (is_array($_SESSION['added'])) {

?>
            <div class='mb10'>Recently added items:</div>
<?

			itemList("i.id in (" . implode(array_keys($_SESSION['added']), ',') . ")");
		}

?>
            <div class='s09 mb20 wp80 mauto'>For each input you can set to use the same info for the next add by having the checkbox checked sitting to the right of the input field.</div>
<?

	}

	$result = select_db('id, name, attribute1, attribute2', 'categories', sprintf('company_id = %s and type = %s and deleted is null order by name', quote_smart($_SESSION['settings']['company_id']), quote_smart($_SESSION['settings']['pagetype'])));

	if (mysql_num_rows($result) == 0) {

?>
              <div class='mb30'>No item can be added since no item category does exist.</div>
<?

	} else {
		foreach (array_keys($_POST) as $key) {
			if (substr($key, 0, 5) == 'keep_' && $_POST[$key] == 1) {
				$key2 = substr($key, 5);
				$_SESSION['edit']['item']['new'][$key2] = $_SESSION['edit']['item']['old'][$key2];
			}
		}

?>
              <form method='post'>
                <table class='mb30'>
                  <tr class='vtop'>
                    <td class='pr30'>
                      <table>
                        <tr class='vtop'>
                          <td class='p5 s08 left bold<?=isset($_SESSION['bad']['category']) ? ' red' : ''?>'>CATEGORY:</td>
                          <td class='left pr30'>
                            <select type='s08' name='new_category_id'<? if ($show > 0) { ?> class='bcwhite black' disabled<? } else { ?> onchange="str = this.value > 0 ? '- loading -' : '-';document.getElementById('ajax_attribute1').innerHTML = document.getElementById('ajax_attribute2').innerHTML = str; if (this.value > 0) { sendRequest('getinfo.php?category_id=' + this.value + '&mode=add_item'); }"<? } ?>>
                              <option value='0'>- please select -</option>
<?

		while ($result_array = mysql_fetch_array($result)) {

?>
                              <option value='<?=$result_array['id']?>'<? if ($_SESSION['edit']['item']['new']['category_id'] == $result_array['id'] || (empty($cid) && $_SESSION['settings']['itemmgnt']['category_id'] == $result_array['id'])) { ?> selected<?; $attribute1 = $result_array['attribute1']; $attribute2 = $result_array['attribute2']; } ?>><?=$result_array['name']?></option>
<?

		}

?>
                            </select>
<?

		if ($add > 0) {

?>
                            <input class='ml5' type='checkbox' name='keep_category_id' value='1'<? if ($template_id > 0 || $_POST['keep_category_id'] == 1) { ?> checked<? } ?>/>
<?

		}

?>
                          </td>
                        </tr>
                        <tr class='vtop'>
                          <td class='p5 s08 left bold<?=isset($_SESSION['bad']['supplier']) ? ' red' : ''?>'>SUPPLIER:</td>
                          <td class='left pr30'>
                            <select type='s08' name='new_supplier_id'<?=$show > 0 ? " class='bcwhite black' disabled" : ''?>>
<?

		if ($_SESSION['edit']['item']['new']['supplier_id'] < 1) {

?>
                              <option value='0'>- please choose -</option>
<?

		}

		$result2 = select_db('id, name', 'suppliers', sprintf('company_id = %s and deleted is null order by name', quote_smart($_SESSION['settings']['company_id'])));

		while ($result_array2 = mysql_fetch_array($result2)) {

?>
                              <option value='<?=$result_array2['id']?>'<? if ($_SESSION['edit']['item']['new']['supplier_id'] == $result_array2['id']) { ?> selected<? } ?>><?=$result_array2['name']?></option>
<?

		}

?>
                            </select>
<?

		if ($add > 0) {

?>
                            <input class='ml5' type='checkbox' name='keep_supplier_id' value='1'<? if ($template_id > 0 || $_POST['keep_supplier_id'] == 1) { ?> checked<? } ?>/>
<?

		}

?>
                          </td>
                        </tr>
                        <tr class='vtop'>
                          <td class='p5 s08 left bold<?=isset($_SESSION['bad']['brand']) ? ' red' : ''?>'>BRAND:</td>
                          <td class='left pr30'>
                            <select type='s08' name='new_brand_id'<?=$show > 0 ? " class='bcwhite black' disabled" : ''?>>
<?

		if ($_SESSION['edit']['item']['new']['brand_id'] < 1) {

?>
                              <option value='0'>- please choose -</option>
<?

		}

		$result2 = select_db('id, name', 'brands', sprintf('company_id = %s and deleted is null order by name', quote_smart($_SESSION['settings']['company_id'])));

		while ($result_array2 = mysql_fetch_array($result2)) {

?>
                              <option value='<?=$result_array2['id']?>'<? if ($_SESSION['edit']['item']['new']['brand_id'] == $result_array2['id']) { ?> selected<? } ?>><?=$result_array2['name']?></option>
<?

		}

?>
                            </select>
<?

		if ($add > 0) {

?>
                            <input class='ml5' type='checkbox' name='keep_brand_id' value='1'<? if ($template_id > 0 || $_POST['keep_brand_id'] == 1) { ?> checked<? } ?>/>
<?

		}

?>
                          </td>
                        </tr>
                        <tr class='vtop'>
                          <td class='p5 s08 left bold<?=isset($_SESSION['bad']['department']) ? ' red' : ''?>'>DEPARTMENT:</td>
                          <td class='left pr30'>
                            <select type='s08' name='new_department_id'<?=$show > 0 ? " class='bcwhite black' disabled" : ''?>>
<?

		if ($_SESSION['edit']['item']['new']['department_id'] < 1) {

?>
                              <option value='0'>- please choose -</option>
<?

		}

		$result2 = select_db('id, name', 'departments', sprintf('company_id = %s and deleted is null order by name', quote_smart($_SESSION['settings']['company_id'])));

		while ($result_array2 = mysql_fetch_array($result2)) {

?>
                              <option value='<?=$result_array2['id']?>'<? if ($_SESSION['edit']['item']['new']['department_id'] == $result_array2['id']) { ?> selected<? } ?>><?=$result_array2['name']?></option>
<?

		}

?>
                            </select>
<?

		if ($add > 0) {

?>
                            <input class='ml5' type='checkbox' name='keep_department_id' value='1'<? if ($template_id > 0 || $_POST['keep_department_id'] == 1) { ?> checked<? } ?>/>
<?

		}

?>
                          </td>
                        </tr>
                        <tr class='vtop'>
                          <td class='p5 s08 left bold<?=isset($_SESSION['bad']['taxgroup']) ? ' red' : ''?>'>TAX GROUP:</td>
                          <td class='left pr30'>
                            <select type='s08' name='new_taxgroup_id'<?=$show > 0 ? " class='bcwhite black' disabled" : ''?>>
<?

		if ($_SESSION['edit']['item']['new']['taxgroup_id'] < 1) {

?>
                              <option value='0'>- please choose -</option>
<?

		}

		$result2 = select_db('id, name, tax', 'tax_groups', sprintf('company_id = %s and deleted is null order by tax asc', quote_smart($_SESSION['settings']['company_id'])));

		while ($result_array2 = mysql_fetch_array($result2)) {

?>
                              <option value='<?=$result_array2['id']?>'<? if ($_SESSION['edit']['item']['new']['taxgroup_id'] == $result_array2['id']) { ?> selected<? } ?>><?=$result_array2['name'] . " ($result_array2[tax])"?></option>
<?

		}

?>
                            </select>
<?

		if ($add > 0) {

?>
                            <input class='ml5' type='checkbox' name='keep_taxgroup_id' value='1'<? if ($template_id > 0 || $_POST['keep_taxgroup_id'] == 1) { ?> checked<? } ?>/>
<?

		}

?>
                          </td>
                        </tr>
                        <tr class='vtop'>
                          <td class='p5 s08 left bold<?=isset($_SESSION['bad']['name']) ? ' red' : ''?>'>ITEM NAME:</td>
                          <td class='left'><input class='w150 text<?=$show > 0 ? " bcwhite black' disabled" : "'"?> type='text' name='new_name' value='<?=replace_ticks($_SESSION['edit']['item']['new']['name'])?>'/><? if ($add > 0) { ?><input class='ml5' type='checkbox' name='keep_name' value='1'<? if (0 || $_POST['keep_name'] == 1) { ?> checked<? } ?>/><? } ?></td>
                        </tr>
                        <tr class='vtop'>
                          <td class='p5 s08 left bold<?=isset($_SESSION['bad']['number']) ? ' red' : ''?>'>STYLE NUMBER:</td>
                          <td class='left'><input class='w100 text<?=$show > 0 ? " bcwhite black' disabled" : "'"?> type='text' name='new_number' value='<?=replace_ticks($_SESSION['edit']['item']['new']['number'])?>'/><? if ($add > 0) { ?><input class='ml5' type='checkbox' name='keep_number' value='1'<? if (0 || $_POST['keep_number'] == 1) { ?> checked<? } ?>/><? } ?></td>
                        </tr>
                        <tr class='vtop'>
                          <td class='p5 s08 left bold'>DESCRIPTION:</td>
                          <td class='left'><textarea class='w250<?=$show > 0 ? " bcwhite black' disabled" : "'"?> rows='6' name='new_style'/><?=$_SESSION['edit']['item']['new']['style']?></textarea><? if ($add > 0) { ?><input class='ml5' type='checkbox' name='keep_style' value='1'<? if (0 || $_POST['keep_style'] == 1) { ?> checked<? } ?>/><? } ?></td>
                        </tr>
                      </table>
                    </td>
                    <td>
                      <table>
                        <tr style='vtop'>
                          <td class='p5 s08 left bold'><span id='ajax_attribute1'><?=$attribute1 == '' ? 'ATTRIBUTE1' : strtoupper($attribute1)?></span>:</td>
                          <td class='left'><input class='w150 text<?=$show > 0 ? " bcwhite black' disabled" : "'"?> type='text' name='new_attribute1' value='<?=replace_ticks($_SESSION['edit']['item']['new']['attribute1'])?>'/><? if ($add > 0) { ?><input class='ml5' type='checkbox' name='keep_attribute1' value='1'<? if (0 || $_POST['keep_attribute1'] == 1) { ?> checked<? } ?>/><? } ?></td>
                        </tr>
                        <tr style='vtop'>
                          <td class='p5 s08 left bold'><span id='ajax_attribute2'><?=$attribute2 == '' ? 'ATTRIBUTE2' : strtoupper($attribute2)?></span>:</td>
                          <td class='left'><input class='w100 text<?=$show > 0 ? " bcwhite black' disabled" : "'"?> type='text' name='new_attribute2' value='<?=replace_ticks($_SESSION['edit']['item']['new']['attribute2'])?>'/><? if ($add > 0) { ?><input class='ml5' type='checkbox' name='keep_attribute2' value='1'<? if (0 || $_POST['keep_attribute2'] == 1) { ?> checked<? } ?>/><? } ?></td>
                        </tr>
                        <tr style='vtop'>
                          <td class='p5 s08 left bold'>PRICE (<?=$_SESSION['preferences']['currency']?>):</td>
                          <td class='left'><input class='w80 text<?=$show > 0 ? " bcwhite black' disabled" : "'"?> type='text' name='new_price' value='<?=replace_ticks($_SESSION['edit']['item']['new']['price'])?>'/><? if ($add > 0) { ?><input class='ml5' type='checkbox' name='keep_price' value='1'<? if (0 || $_POST['keep_price'] == 1) { ?> checked<? } ?>/><? } ?></td>
                        </tr>
<?

		if ($_SESSION['settings'][$_SESSION['settings']['login_id']]['level'] >=1) {

?>
                        <tr class='vtop'>
                          <td class='p5 s08 left bold'>LAST BUY PRICE (<?=$_SESSION['preferences']['currency']?>):</td>
                          <td class='left'><input class='w80 text<?=$show > 0 ? " bcwhite black' disabled" : "'"?> type='text' name='new_buy_price' value='<?=replace_ticks($_SESSION['edit']['item']['new']['buy_price'])?>'/><? if ($add > 0) { ?><input class='ml5' type='checkbox' name='keep_buy_price' value='1'<? if (0 || $_POST['keep_buy_price'] == 1) { ?> checked<? } ?>/><? } ?></td>
                        </tr>
<?

		}

		if ($show > 0) {

?>
                        <tr class='vtop'>
                          <td class='p5 s08 left bold'>TAX (%):</td>
                          <td class='left'><input class='w80 text bcwhite black' disabled type='text' value='<?=replace_ticks($_SESSION['edit']['item']['new']['tax'])?>'/></td>
                        </tr>
                        <tr class='vtop'>
                          <td class='p5 s08 left bold'>FINAL PRICE (<?=$_SESSION['preferences']['currency']?>):</td>
                          <td class='left'><input class='w80 text bcwhite black' disabled type='text' value='<?=replace_ticks($_SESSION['edit']['item']['new']['finalprice'])?>'/></td>
                        </tr>
<?

		}

?>
                        <tr class='vtop'>
                          <td class='p5 s08 left bold'>DISCOUNT (%):</td>
                          <td class='left'><input class='w80 text<?=$show > 0 ? " bcwhite black' disabled" : "'"?> type='text' name='new_discount' value='<?=replace_ticks($_SESSION['edit']['item']['new']['discount'])?>'/><? if ($add > 0) { ?><input class='ml5' type='checkbox' name='keep_discount' value='1'<? if (0 || $_POST['keep_discount'] == 1) { ?> checked<? } ?>/><? } ?></td>
                        </tr>
                        <tr class='vtop'>
                          <td class='p5 s08 left bold'>LOCATION:</td>
                          <td class='left'><input class='w80 text<?=$show > 0 ? " bcwhite black' disabled" : "'"?> type='text' name='new_location' value='<?=replace_ticks($_SESSION['edit']['item']['new']['location'])?>'/><? if ($add > 0) { ?><input class='ml5' type='checkbox' name='keep_location' value='1'<? if (0 || $_POST['keep_location'] == 1) { ?> checked<? } ?>/><? } ?></td>
                        </tr>
                        <tr class='vtop'>
                          <td class='p5 s08 left bold'>QUANTITY:</td>
                          <td class='left'><input class='w80 text<?=$show > 0 ? " bcwhite black' disabled" : "'"?> type='text' name='new_quantity' value='<?=replace_ticks($_SESSION['edit']['item']['new']['quantity'])?>'/><? if ($add > 0) { ?><input class='ml5' type='checkbox' name='keep_quantity' value='1'<? if (0 || $_POST['keep_quantity'] == 1) { ?> checked<? } ?>/><? } ?></td>
                        </tr>
                        <tr class='vtop'>
                          <td class='p5 s08 left bold'>BARCODE:</td>
                          <td class='left'><input class='w150 text<?=$show > 0 ? " bcwhite black' disabled" : "'"?> type='text' name='new_barcode' value='<?=replace_ticks($add > 0 ? $_SESSION['additembarcode'] : $_SESSION['edit']['item']['new']['barcode'])?>'/></td>
                        </tr>
                        <tr class='vtop'>
                          <td class='p5 s08 left bold'>LIMIT REORDER LEVEL 1:</td>
                          <td class='left'><input class='w80 text<?=$show > 0 ? " bcwhite black' disabled" : "'"?> type='text' name='new_reorder_limit1' value='<?=replace_ticks($_SESSION['edit']['item']['new']['reorder_limit1'])?>'/></font><? if ($add > 0) { ?><input class='ml5' type='checkbox' name='keep_reorder_limit1' value='1'<? if ($template_id > 0 || $_POST['keep_reorder_limit1'] == 1) { ?> checked<? } ?>/><? } ?></td>
                        </tr>
                        <tr class='vtop'>
                          <td class='p5 s08 left bold'>LIMIT REORDER LEVEL 2:</td>
                          <td class='left'><input class='w80 text<?=$show > 0 ? " bcwhite black' disabled" : "'"?> type='text' name='new_reorder_limit2' value='<?=replace_ticks($_SESSION['edit']['item']['new']['reorder_limit2'])?>'/><? if ($add > 0) { ?><input class='ml5' type='checkbox' name='keep_reorder_limit2' value='1'<? if ($template_id > 0 || $_POST['keep_reorder_limit2'] == 1) { ?> checked<? } ?>/><? } ?></td>
                        </tr>
                        <tr class='vtop'>
                          <td class='p5 s08 left bold'>ARCHIVED:</td>
                          <td class='left'>
                            <select type='s08' name='new_archived'<?=$show > 0 ? " class='bcwhite black' disabled" : ''?>>
                              <option value='0'>no</option>
                              <option value='1'<? if ($_SESSION['edit']['item']['new']['archived'] > 0) { ?> selected<? } ?>>yes</option>
                            </select>
<?

		if ($add > 0) {

?>
                            <input class='ml5' type='checkbox' name='keep_archived' value='1'<? if ($template_id > 0 || $_POST['keep_archived'] == 1) { ?> checked<? } ?>/>
<?

		}

?>
			  </td>
                        </tr>
                      </table>
                    </td>
                  </tr>
<?

		if ($add > 0 || $edit > 0) {

?>
                  <tr class='vtop'>
                    <td colspan='2' class='center pt20'>
            <? if ($_SESSION['settings'][$_SESSION['settings']['login_id']]['level'] >= 2 ){?>
                        <input class='button' type='submit' <? if ( !$_SESSION['settings'][$_SESSION['settings']['login_id']]['level'] >=2) {?>disabled<?}?> value='<?=$add > 0 ? "ADD" : "UPDATE"?> ITEM'/>
            <?}?>
                    </td>
                  </tr>
                  <input type='hidden' name='<?=$add > 0 ? "add" : "update"?>item' value='1'/>
<?

			if ($edit > 0) {

?>
                  <input type='hidden' name='update_item_id' value='<?=$_SESSION['edit']['item']['new']['id']?>'/>
<?

			}
		}

?>
                </table>
              </form>
              <div class='s1 mb10 bold'>PICTURES</div>
              <div class='mb30'>
<?

		if ($add == 0) {
                        $result = select_db('iim.image_id, iim.default_item_image, iim.default_group_image', 'item_image_mappings as iim join items as i on i.id = iim.id', sprintf('iim.id = %s and iim.deleted is null order by iim.default_group_image desc, iim.default_item_image desc, i.added asc', quote_smart($_SESSION['edit']['item']['new']['id'])));

			if (mysql_num_rows($result) == 0) {

?>
                <div class='s09 mb30'>No picture does exist for this item yet.</div>
<?

			} else {

?>
                <table>
                  <tr class='vmiddle'>
<?

				while ($result_array = mysql_fetch_array($result)) {

?>
                    <td class='center'>
                      <table height='120px'>
                        <tr class='vbottom'><td class='center'><img class='mp' src='showimage.php?id=<?=$result_array['image_id']?>&w=200&h=150' onclick='window.open("showimage.php?id=<?=$result_array['image_id']?>&w=600&h=600", "_new", "innerHeight=620,innerWidth=620,scrollbars=no,toolbar=no,resizable=no,menubar=0");'/></td></tr>
<?

					if ($_SESSION['settings'][$_SESSION['settings']['login_id']]['level'] >=0 && $_SESSION['settings']['itemmgnt']['type'] == 'items') {

?>
                        <tr class='vbottom' height='10px'><td class='center s07 bold'><? if ($result_array['default_group_image'] != 1) { ?><a href='javascript: none();' onclick='if (confirm("Do you really want to make this picture to be the default group picture?")) { document.getElementById("defaultpicid").value = <?=$result_array['image_id']?>; document.getElementById("defgrouppic").value = 1; document.defaultpicform.submit(); }'>MAKE DEF GROUP PIC</a><? } else { ?>IS DEF GROUP PIC<? } ?></td></tr>
                        <tr class='vbottom' height='10px'><td class='center s07 bold'><? if ($result_array['default_item_image'] != 1) { ?><a href='javascript: none();' onclick='if (confirm("Do you really want to make this picture to be the default item picture?")) { document.getElementById("defaultpicid").value = <?=$result_array['image_id']?>; document.defaultpicform.submit(); }'>MAKE DEF ITEM PIC</a><? } else { ?>IS DEF ITEM PIC<? } ?></td></tr>
                        <tr class='vbottom' height='10px'><td class='center s07 bold'><a href='javascript: none();' onclick='if (confirm("Do you really want to delete the selected picture?")) { document.getElementById("deletepicid").value = <?=$result_array['image_id']?>; document.deletepicform.submit(); }'>DELETE</a></td></tr>
<?

					}

?>
                      </table>
                    </td>
<?

				}

?>
                  </tr>
                </table>
<?

				if ($_SESSION['settings'][$_SESSION['settings']['login_id']]['level'] >= 0 && $_SESSION['settings']['itemmgnt']['type'] == 'items') {

?>
                <form method='post' name='defaultpicform'>
                  <input type='hidden' name='image_id' id='defaultpicid' value='0'/>
                  <input type='hidden' name='defaultgrouppicture' id='defgrouppic' value='0'/>
                  <input type='hidden' name='item_id' value='<?=$_SESSION['edit']['item']['new']['id']?>'/>
                  <input type='hidden' name='defaultitempicture' value='1'/>
                </form>
                <form method='post' name='deletepicform'>
                  <input type='hidden' name='image_id' id='deletepicid' value='0'/>
                  <input type='hidden' name='item_id' value='<?=$_SESSION['edit']['item']['new']['id']?>'/>
                  <input type='hidden' name='deleteitempicture' value='1'/>
                </form>
<?

				}
			}

			if ($_SESSION['settings'][$_SESSION['settings']['login_id']]['level'] >= 0 && $_SESSION['settings']['itemmgnt']['type'] == 'items') {

?>
                <div class='mb30 mt20'>
                  <div class='bold s09 mb10'>ADD PICTURE</div>
<?

				if (mysql_num_rows($result) > 1) {

?>
                  <div class='s09'>No picture can be added since only two pictures are allowed for each item.</div>
<?

				} else {

?>
                  <form method='post' enctype='multipart/form-data'>
                    <input class='text' type='file' name='new_picture'/><input class='ml2 button' type='submit' value='UPLOAD'/>
                    <input type='hidden' name='item_id' value='<?=$_SESSION['edit']['item']['new']['id']?>'/>
                    <input type='hidden' name='newitempicture' value='1'/>
                  </form>
<?

				}

?>
                </div>
<?

			}

			if (1) {

?>
                <div class='mb30 mt20'>
                  <div class='bold s1 mb10'>SALES INCLUDING THIS ITEM</div>
<?

				$result = select_db(sprintf('s.id as sale_id, date_format(convert_tz(s.added, "utc", %s), %s) as added, si.quantity', quote_smart($_SESSION['preferences']['timezone']), "'%b %D, %Y @ %H:%i:%S'"), 'sales as s join sale_items as si on s.id = si.sale_id', sprintf('s.company_id = %s and si.item_id = %s and s.deleted is null order by s.added desc', quote_smart($_SESSION['settings']['company_id']), quote_smart($_SESSION['edit']['item']['new']['id'])));
				if (mysql_num_rows($result) > 0) {

?>
                  <form name='showsaledetailsform' method='post'>
                    <input type='hidden' name='saleid' id='showsaledetails_rid' value='0'/>
                    <input type='hidden' name='show_details' value='1'/>
		    <input type='hidden' name='third' value='1'/>
                    <input type='hidden' name='sale_management' value='1'/>
                  </form>
                  <table class='mb30 b1sl b1st'>
                    <tr>
                      <td class='bctrt bold s08 p5 b1sr b1sb'>#</td>
                      <td class='bctrt bold s08 p5 b1sr b1sb'>SALE DATE AND TIME</td>
                      <td class='bctrt bold s08 p5 b1sr b1sb'>SALE TOTAL</td>
                      <td class='bctrt bold s08 p5 b1sr b1sb'>ITEM QUANTITY</td>
                      <td class='bctrt bold s08 p5 b1sr b1sb'>SHOW SALE DETAILS?</td>
                    </tr>
<?

					$rownum = 1;

					while ($result_array = mysql_fetch_array($result)) {
						$total = 0;
						$result2 = select_db('price, tax, discount, additional_discount, quantity', 'sale_items', sprintf('sale_id = %s', quote_smart($result_array['sale_id'])));
						while ($result_array2 = mysql_fetch_array($result2)) {
							$calc = calc($result_array2['price'], $result_array2['discount'], $result_array2['additional_discount'], $result_array2['tax'], $result_array2['quantity']);
							$total += $calc['total'];
						}

						$result2 = select_db('value', 'vouchers', sprintf('origin_id = %s and deleted is null', quote_smart($result_array['sale_id'])));

						while ($result_array2 = mysql_fetch_array($result2)) {
							$total += $result_array2['value'];
						}

?>
                    <tr class='bctr1<?=$rownum % 2 == 0 ? 'a' : 'b'?>'>
                      <td class='s08 p5 b1sr b1sb'><?=$rownum++?></td>
                      <td class='s08 p5 b1sr b1sb'><?=$result_array['added']?></td>
                      <td class='s08 p5 b1sr b1sb'><?=money($total)?></td>
                      <td class='s08 p5 b1sr b1sb'><?=$result_array['quantity']?></td>
                      <td class='b1sr b1sb'><input class='button' type='button' value='SHOW SALE DETAILS' onclick='document.getElementById("showsaledetails_rid").value = "<?=$result_array['sale_id']?>"; document.showsaledetailsform.submit();'/></td>
                    </tr>
<?

					}

?>
                  </table>
<?

				} else {
?>
                  <div class='s09'>This item was not sold yet.</div>
<?

				}
			}
		} else {

?>
                  <div class='s09'>Pictures can be uploaded when you edit the item after adding it.</div>
<?

		}

?>
                </div>
              </div>
              <form class='mb30 mt30' method='post' name='go_back_form'>
                <a href='javascript: none();' class='s08 bold' onclick='document.go_back_form.submit();'>GO BACK TO PREVIOUS PAGE</a>
                <input type='hidden' name='noitemdetails' value='1'/>
              </form>
            </div>
<?

	}
}
function saleList($where, $orderby = '', $groupby = '') {
	if (!empty($orderby)) { $orderby = ' order by ' . $orderby; }
	if (!empty($groupby)) { $groupby = ' group by ' . $groupby; } else { $groupby = ' group by s.id'; }
	if (!empty($where)) { $where = "and $where"; }

	$result = select_db(sprintf('s.id, date_format(convert_tz(s.added, "utc", %s), %s) as datestring, date_format(convert_tz(s.added, "utc", %s), %s) as timestring, coalesce(l2.username, l.username, "-") as sales_person, s.paid, coalesce(sum(v.value), "-") as vouchers', quote_smart($_SESSION['preferences']['timezone']), "'%b %D, %Y'", quote_smart($_SESSION['preferences']['timezone']), "'%H:%i:%s'"), 'sales as s left join logins as l on l.id = s.login_id left join logins as l2 on l2.id = s.sales_person_id left join vouchers as v on v.sale_id = s.id', sprintf('s.company_id = %s and s.deleted is null %s%s%s', quote_smart($_SESSION['settings']['company_id']), $where, $groupby, $orderby));
	$rows = mysql_num_rows($result);

	if ($rows == 0) {

?>
        <div class='mb30'>No sale was found matching the criteria.</div>
<?

	} else {

?>
        <table class='mb20 b1sl b1st'>
          <tr>
            <td class='bctrt bold s08 p5 b1sr b1sb'>#</td>
            <td class='bctrt bold s08 p5 b1sr b1sb'>DATE</td>
            <td class='bctrt bold s08 p5 b1sr b1sb'>TIME</td>
            <td class='bctrt bold s08 p5 b1sr b1sb'>SALES<br/>PERSON</td>
            <td class='bctrt bold s08 p5 b1sr b1sb'>PRICE</td>
            <td class='bctrt bold s08 p5 b1sr b1sb'>INCLUDED<br/>DISCOUNT</td>
            <td class='bctrt bold s08 p5 b1sr b1sb'>INCLUDED<br/>EXTRA<br/>DISCOUNT</td>
            <td class='bctrt bold s08 p5 b1sr b1sb'>TAX</td>
            <td class='bctrt bold s08 p5 b1sr b1sb'>TOTAL</td>
            <td class='bctrt bold s08 p5 b1sr b1sb'>CASH<br/>PAYMENT</td>
            <td class='bctrt bold s08 p5 b1sr b1sb'>CARD<br/>PAYMENT</td>
            <td class='bctrt bold s08 p5 b1sr b1sb'>VOUCHER<br/>PAYMENT</td>
            <td class='bctrt bold s08 p5 b1sr b1sb'>DETAILS?</td>
            <td class='bctrt bold s08 p5 b1sr b1sb'>SHOW RECEIPT?</td>
            <td class='bctrt bold s08 p5 b1sr b1sb'>DELETE?</td>
          </tr>
<?

		$rownum = 1;

		while ($result_array = mysql_fetch_array($result)) {
			$paid = array('cash' => $result_array['paid']);
			$result2 = select_db('si.price, si.discount, si.additional_discount, si.tax, si.quantity, i.price as real_price, i.discount as real_discount', 'sale_items as si join items as i on i.id = si.item_id', sprintf('si.sale_id = %s', quote_smart($result_array['id'])));

			$totals = array();
			while ($result_array2 = mysql_fetch_array($result2)) {
				$vals = calc($result_array2['price'], $result_array2['discount'], $result_array2['additional_discount'], $result_array2['tax'], $result_array2['quantity']);
				$vals2 = calc($result_array2['real_price'], $result_array2['real_discount'], 0, $result_array2['tax'], $result_array2['quantity']);

				foreach (array('price', 'tax', 'total') as $key) {
					$totals[$key] += $vals[$key];
				}

				$totals['odiscount'] = $vals2['odiscount'];
				$totals['xdiscount'] = $vals['xdiscount'] + $vals2['price'] - $vals['price'] + $vals['odiscount'] - $vals2['odiscount'];
			}

			$result2 = select_db('v.value', 'vouchers as v join sales as s on s.id = v.origin_id', sprintf('v.type = "gift_certificate" and s.id = %s', quote_smart($result_array['id'])));

			while ($result_array2 = mysql_fetch_array($result2)) {
				$totals['price'] += $result_array2['value'];
				$totals['total'] += $result_array2['value'];
			}

			$result2 = select_db('cp.amount', 'card_payments as cp join sales as s on s.id = cp.sale_id', sprintf('s.id = %s', quote_smart($result_array['id'])));

			while ($result_array2 = mysql_fetch_array($result2)) {
				$paid['card'] += $result_array2['amount'];
			}

			$result2 = select_db('value', 'vouchers', sprintf('sale_id = %s', quote_smart($result_array['id'])));

			while ($result_array2 = mysql_fetch_array($result2)) {
				$paid['voucher'] += $result_array2['value'];
			}

?>
          <tr class='bctr1<?=$rownum % 2 == 1 ? 'a' : 'b'?>'>
            <td class='s08 p5 b1sr b1sb'><?=$rownum++?></td>
            <td class='s08 p5 b1sr b1sb'><nobr><?=$result_array['datestring']?></nobr></td>
            <td class='s08 p5 b1sr b1sb'><?=$result_array['timestring']?></td>
            <td class='s08 p5 b1sr b1sb'><?=$result_array['sales_person']?></td>
            <td class='s08 p5 b1sr b1sb'><?=money($totals['price'] - $totals['discount'])?></td>
            <td class='s08 p5 b1sr b1sb'><?=$totals['odiscount'] == 0 ? '-' : money($totals['odiscount'])?></td>
            <td class='s08 p5 b1sr b1sb'><?=$totals['xdiscount'] == 0 ? '-' : money($totals['xdiscount'])?></td>
            <td class='s08 p5 b1sr b1sb'><?=$totals['tax'] > 0 ? money($totals['tax']) : '-'?></td>
            <td class='s08 p5 b1sr b1sb'><?=money($totals['total'])?></td>
            <td class='s08 p5 b1sr b1sb'><?=$paid['cash'] > 0 ? money($paid['cash']) : '-'?></td>
            <td class='s08 p5 b1sr b1sb'><?=$paid['card'] > 0 ? money($paid['card']) : '-'?></td>
            <td class='s08 p5 b1sr b1sb'><?=$paid['voucher'] > 0 ? money($paid['voucher']) : '-'?></td>
            <td class='b1sr b1sb'><input type='button' class='button' value='DETAILS' onclick='document.getElementById("details_saleid").value = <?=$result_array['id']?>; document.saledetailsform.submit();'/></td>
            <td class='b1sr b1sb'><input type='button' class='button' value='RECEIPT' onclick='document.getElementById("receipt_saleid").value = <?=$result_array['id']?>; document.salereceiptform.submit();'/></td>
            <td class='b1sr b1sb'><input type='button' class='button' value='DELETE' onclick='if (confirm("Deleting this sale will add the sale items back to the shop items.\nIt will also delete all returns of this sale (if any).\nDo you really want to delete this sale?")) { document.getElementById("deletesaleid").value = <?=$result_array['id']?>; document.deletesaleform.submit(); }'/></td>
          </tr>
<?

		}

?>
        </table>
        <form method='post' name='saledetailsform'>
          <input type='hidden' name='saleid' id='details_saleid' value='0'/>
          <input type='hidden' name='show_details' value='1'/>
          <input type='hidden' name='sale_management' value='1'/>
        </form>
        <form method='post' name='salereceiptform'>
          <input type='hidden' name='saleid' id='receipt_saleid' value='0'/>
          <input type='hidden' name='receiptshow' value='1'/>
          <input type='hidden' name='sale_management' value='1'/>
        </form>
        <form method='post' name='deletesaleform'>
          <input type='hidden' name='delete_sale_id' id='deletesaleid' value='0'/>
          <input type='hidden' name='sale_management' value='1'/>
        </form>
<?

	}
}
function returnList($where, $additional = '', $sub_where = '', $sub_additional = '') {
	if (!empty($orderby)) { $orderby = ' order by ' . $orderby; }
	if (!empty($groupby)) { $groupby = ' group by ' . $groupby; } else { $groupby = ' group by r.id'; }
	$where = sprintf('r.company_id = %s and r.deleted is null', quote_smart($_SESSION['settings']['company_id'])) . (empty($where) ? '' : " and $where");

	get('returns', sprintf('r.id, date_format(convert_tz(r.added, "utc", %s), %s) as date, date_format(convert_tz(r.added, "utc", %s), %s) as time, coalesce(l.username, "-") as receiver', quote_smart($_SESSION['preferences']['timezone']), "'%b %D, %Y'", quote_smart($_SESSION['preferences']['timezone']), "'%H:%i:%s'"), 'returns as r join logins as l on l.id = r.login_id', "$where $additional", 'items', 'r.id as main_id, i.id, ri.quantity, si.price, si.tax, si.discount, si.additional_discount', 'returns as r join return_items as ri on ri.return_id = r.id join sale_items as si on si.id = ri.sale_item_id join items as i on i.id = si.item_id', 'r.id in ([main_ids])' . ($sub_where == '' ? '' : 'and ' . $sub_where) . ($sub_additional == '' ? '' : " $sub_additional"));

	if (!isset($_SESSION['results']['returns'])) {

?>
        <div class='mb30'>No return was found matching the criteria.</div>
<?

	} else {

?>
        <table class='mb20 b1sl b1st'>
          <tr>
            <td class='bctrt bold s08 p5 b1sr b1sb'>#</td>
            <td class='bctrt bold s08 p5 b1sr b1sb'>DATE</td>
            <td class='bctrt bold s08 p5 b1sr b1sb'>TIME</td>
            <td class='bctrt bold s08 p5 b1sr b1sb'>RECEIVER</td>
            <td class='bctrt bold s08 p5 b1sr b1sb'>ITEMS</td>
            <td class='bctrt bold s08 p5 b1sr b1sb'>TOTAL<br/>PRICE</td>
            <td class='bctrt bold s08 p5 b1sr b1sb'>TOTAL<br/>TAX</td>
            <td class='bctrt bold s08 p5 b1sr b1sb'>TOTAL<br/>DISCOUNT</td>
            <td class='bctrt bold s08 p5 b1sr b1sb'>TOTAL<br/>NET</td>
            <td class='bctrt bold s08 p5 b1sr b1sb'>DETAILS?</td>
            <td class='bctrt bold s08 p5 b1sr b1sb'>SHOW RECEIPT?</td>
          </tr>
<?

		$rownum = 1;

		foreach (array_keys($_SESSION['results']['returns']) as $return_id) {
			$return = $_SESSION['results']['returns'][$return_id];
			$total = $tax = $discount = $count = 0;

			if (is_array($return['items'])) {
				foreach (array_keys($return['items']) as $return_item_id) {
					$item = $return['items'][$return_item_id];
					$vals = calc($item['price'], $item['discount'], $item['additional_discount'], $item['tax'], $item['quantity']);
					$total += $vals['price'];
					$tax += $vals['tax'];
					$discount += $vals['discount'];
				}
			}

?>
          <tr class='bctr1<?=$rownum % 2 == 1 ? 'a' : 'b'?>'>
            <td class='s08 p5 b1sr b1sb'><?=$rownum++?></td>
            <td class='s08 p5 b1sr b1sb'><?=$return['date']?></td>
            <td class='s08 p5 b1sr b1sb'><?=$return['time']?></td>
            <td class='s08 p5 b1sr b1sb'><?=$return['receiver']?></td>
            <td class='s08 p5 b1sr b1sb'><?=$item['quantity']?></td>
            <td class='s08 p5 b1sr b1sb'><?=money($total)?></td>
            <td class='s08 p5 b1sr b1sb'><?=money($tax)?></td>
            <td class='s08 p5 b1sr b1sb'><?=money($discount)?></td>
            <td class='s08 p5 b1sr b1sb'><?=money($total - $discount + $tax)?></td>
            <td class='b1sr b1sb'><input type='button' class='button' value='DETAILS' onclick='document.getElementById("details_return_id").value = <?=$return_id?>; document.returndetailsform.submit();'/></td>
            <td class='b1sr b1sb'><input type='button' class='button' value='SHOW RECEIPT' onclick='document.getElementById("receipt_return_id").value = <?=$return_id?>; document.returnreceiptform.submit();'/></td>
          </tr>
<?

		}

?>
        </table>
        <form method='post' name='returndetailsform'>
          <input type='hidden' name='return_id' id='details_return_id' value='0'/>
          <input type='hidden' name='show_return_details' value='1'/>
          <input type='hidden' name='return_management' value='1'/>
        </form>
        <form method='post' name='returnreceiptform'>
          <input type='hidden' name='return_id' id='receipt_return_id' value='0'/>
          <input type='hidden' name='return_receipt_show' value='1'/>
          <input type='hidden' name='return_management' value='1'/>
        </form>
<?

	}
}
function deliveryList($where, $orderby = '', $groupby = '') {
	if (!empty($orderby)) { $orderby = ' order by ' . $orderby; }
	if (!empty($groupby)) { $groupby = ' group by ' . $groupby; } else { $groupby = ' group by d.id'; }
	if (!empty($where)) { $where = "and $where"; }

	$result = select_db(sprintf('d.id, date_format(convert_tz(d.added, "utc", %s), %s) as date_added, date_format(convert_tz(d.ordered, "utc", %s), %s) as date_ordered, date_format(convert_tz(d.shipped, "utc", %s), %s) as date_sent, date_format(convert_tz(d.received, "utc", %s), %s) as date_received, coalesce(l.username, "-") as receiver, s.name, sum(di.quantity) as itemno, sum(di.buy_price * di.quantity) as total, d.shipping_costs', quote_smart($_SESSION['preferences']['timezone']), "'%b %D, %Y'", quote_smart($_SESSION['preferences']['timezone']), "'%b %D, %Y'", quote_smart($_SESSION['preferences']['timezone']), "'%b %D, %Y'", quote_smart($_SESSION['preferences']['timezone']), "'%b %D, %Y'"), 'deliveries as d join delivery_items as di on di.delivery_id = d.id join suppliers as s on s.id = d.supplier_id left join logins as l on l.id = d.receiver_id', sprintf('l.company_id = %s %s%s%s', quote_smart($_SESSION['settings']['company_id']), $where, $groupby, $orderby));
	$rows = mysql_num_rows($result);

	if ($rows == 0) {

?>
        <div class='mb30'>No delivery was found matching the criteria.</div>
<?

	} else {

?>
        <form name='delivery_details_form' method='post'>
          <input type='hidden' name='delivery_id' id='show_delivery_id' value='0'/>
          <input type='hidden' name='form_action' value='show_delivery'/>
          <input type='hidden' name='delivery_management' value='1'/>
        </form>
        <table class='mb20 b1sl b1st'>
          <tr>
            <td class='bctrt bold s08 p5 b1sr b1sb'>#</td>
            <td class='bctrt bold s08 p5 b1sr b1sb'>ADDED</td>
            <td class='bctrt bold s08 p5 b1sr b1sb'>ORDERED</td>
            <td class='bctrt bold s08 p5 b1sr b1sb'>SENT</td>
            <td class='bctrt bold s08 p5 b1sr b1sb'>RECEIVED</td>
            <td class='bctrt bold s08 p5 b1sr b1sb'>SUPPLIER</td>
            <td class='bctrt bold s08 p5 b1sr b1sb'>ITEMS</td>
            <td class='bctrt bold s08 p5 b1sr b1sb'>PRICE</td>
            <td class='bctrt bold s08 p5 b1sr b1sb'>RECEIVER</td>
            <td class='bctrt bold s08 p5 b1sr b1sb'>DETAILS?</td>
          </tr>
<?

		$rownum = 1;

		while ($result_array = mysql_fetch_array($result)) {

?>
          <tr class='bctr1<?=$rownum % 2 == 1 ? 'a' : 'b'?>'>
            <td class='s08 p5 b1sr b1sb'><?=$rownum++?></td>
            <td class='s08 p5 b1sr b1sb'><?=$result_array['date_added']?></td>
            <td class='s08 p5 b1sr b1sb'><?=$result_array['date_ordered']?></td>
            <td class='s08 p5 b1sr b1sb'><?=$result_array['date_sent']?></td>
            <td class='s08 p5 b1sr b1sb'><?=$result_array['date_received']?></td>
            <td class='s08 p5 b1sr b1sb'><?=$result_array['name']?></td>
            <td class='s08 p5 b1sr b1sb'><?=$result_array['itemno']?></td>
            <td class='s08 p5 b1sr b1sb'><?=money($result_array['total'] + $result_array['shipping_costs'])?></td>
            <td class='s08 p5 b1sr b1sb'><?=$result_array['receiver']?></td>
            <td class='b1sr b1sb'><input type='button' class='button' value='DETAILS' onclick='document.getElementById("show_delivery_id").value = <?=$result_array['id']?>; document.delivery_details_form.submit();'/></td>
          </tr>
<?

		}

?>
        </table>
        <form method='post' name='deliverydetailsform'>
          <input type='hidden' name='details_delivery_id' id='details_deliveryid' value='0'/>
          <input type='hidden' name='delivery_management' value='1'/>
        </form>
<?

	}
}
function voucherList($where = '') {
	if (!empty($where)) { $where = " and $where "; }

	if ($_SESSION['settings']['reports']['frame'] == 'r') {
		$where .= sprintf(' and date(convert_tz(added, "utc", %s)) >= %s and date(convert_tz(added, "utc", %s)) <= %s', quote_smart($_SESSION['preferences']['timezone']), quote_smart($_SESSION['settings']['reports']['from_date']), quote_smart($_SESSION['preferences']['timezone']), quote_smart($_SESSION['settings']['reports']['till_date']));
	}

	$result = select_db(sprintf('origin_id, sale_id, barcode, type, value, date_format(convert_tz(added, "utc", %s), %s) as date_added, date_format(convert_tz(deleted, "utc", %s), %s) as date_deleted', quote_smart($_SESSION['preferences']['timezone']), "'%b %D, %Y'", quote_smart($_SESSION['preferences']['timezone']), "'%b %D, %Y'"), 'vouchers', sprintf('company_id = %s %s order by added asc', quote_smart($_SESSION['settings']['company_id']), $where));
	$rows = mysql_num_rows($result);

	if ($rows == 0) {

?>
        <div class='mb30'>No voucher was found matching the criteria.</div>
<?

	} else {

?>
        <form method='post' name='sale_details_form'>
          <input type='hidden' name='saleid' value='0'/>
          <input type='hidden' name='show_details' value='1'/>
          <input type='hidden' name='sale_management' value='1'/>
        </form>
        <table class='mb20 b1sl b1st'>
          <tr>
            <td class='bctrt bold s08 p5 b1sr b1sb'>#</td>
            <td class='bctrt bold s08 p5 b1sr b1sb'>ADDED</td>
            <td class='bctrt bold s08 p5 b1sr b1sb'>BARCODE</td>
            <td class='bctrt bold s08 p5 b1sr b1sb'>TYPE</td>
            <td class='bctrt bold s08 p5 b1sr b1sb'>VALUE</td>
            <td class='bctrt bold s08 p5 b1sr b1sb'>ENCASHED</td>
          </tr>
<?

		$rownum = 1;

		while ($result_array = mysql_fetch_array($result)) {

?>
          <tr class='bctr1<?=$rownum % 2 == 1 ? 'a' : 'b'?>'>
            <td class='s08 p5 b1sr b1sb'><?=$rownum++?></td>
            <td class='s08 p5 b1sr b1sb'><? if ($result_array['type'] != 'return') { ?><a href='javascript: none();' onclick='document.sale_details_form.saleid.value = <?=$result_array['origin_id']?>; document.sale_details_form.submit();'><?=$result_array['date_added']?></a><? } else { ?><?=$result_array['date_added']?><? } ?></td>
            <td class='s08 p5 b1sr b1sb'><?=$result_array['barcode']?></td>
            <td class='s08 p5 b1sr b1sb'><?=$result_array['type']?></td>
            <td class='s08 p5 b1sr b1sb'><?=money($result_array['value'])?></td>
            <td class='s08 p5 b1sr b1sb'><?=$result_array['date_deleted']?></td>
          </tr>
<?

		}

?>
        </table>
<?

	}
}
?>